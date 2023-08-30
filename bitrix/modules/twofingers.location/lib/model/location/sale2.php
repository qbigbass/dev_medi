<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 05.02.2019
 * Time: 12:02
 *
 *
 */

namespace TwoFingers\Location\Model\Location;

use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Sale\Location\LocationTable;
use TwoFingers\Location\Model\Location;
use TwoFingers\Location\Model\Sale\DefaultSite;
use TwoFingers\Location\Model\Sale\Zip;
use TwoFingers\Location\Options;
use TwoFingers\Location\Model\Sale\SiteLocation;
use TwoFingers\Location\Entity\Location as LocationEntity;
use TwoFingers\Location\Model\Sale\Location as LocationSaleModel;

/**
 * Class Sale2
 *
 * @package TwoFingers\Location\Model\Location
 */
class Sale2 extends Location
{
    /**
     * @param int $id
     * @param string $langId
     * @param string|null $siteId
     * @return false|mixed|null
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getById(int $id, string $langId, string $siteId = null): ?array
    {
        if (!$id) {
            throw new Main\ArgumentNullException('id');
        }

        $filter = [
            '=ID' => $id,
        ];

        $filter    = self::addSiteLocationsFilter($filter, $siteId);
        $locations = self::getByFilter(['filter' => $filter], $langId, $siteId);

        return count($locations) ? reset($locations) : null;
    }

    /**
     * @param string $code
     * @param string $langId
     * @param string|null $siteId
     * @return false|mixed|null
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getByCode(string $code, string $langId, string $siteId = null): ?array
    {
        $code = trim($code);
        if (!strlen($code)) {
            throw new Main\ArgumentNullException('code');
        }

        if (!SiteLocation::isCodeAllowsToSite($code, $siteId)) {
            return null;
        }

        $filter = [
            '=CODE' => $code,
        ];

        $locations = self::getByFilter(['filter' => $filter], $langId, $siteId);

        return count($locations) ? reset($locations) : null;
    }


    /**
     * @param string $siteId
     * @param string|mixed $langId
     * @return array|null
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getDefault(string $siteId, string $langId = LANGUAGE_ID): ?array
    {
        $defaultCode = Options::getDefaultLocation($siteId);
        if (!$defaultCode) {
            return null;
        }

        if (!SiteLocation::isCodeAllowsToSite($defaultCode, $siteId)) {
            return null;
        }

        $filter = [
            '=CODE' => $defaultCode,
        ];

        $default = self::getByFilter(['filter' => $filter], $langId, $siteId);
        if ($default) {
            return reset($default);
        }

        return null;
    }

    /**
     * @param array $filter
     * @param string|null $siteId
     * @param string $key
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\SystemException
     */
    protected static function addSiteLocationsFilter(array $filter, string $siteId = null, string $key = '=CODE'): array
    {
        if (isset($siteId)) {
            $siteLocations = SiteLocation::getCodesBySiteId($siteId);
            if (!empty($siteLocations)) {
                $filter[$key] = $siteLocations;
            }
        }

        return $filter;
    }

    /**
     * @param string $name
     * @param string $langId
     * @param array|null $typesCodes
     * @param int|null $parentId
     * @param string|null $siteId
     * @return false|mixed|void|null
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getByName(
        string $name,
        string $langId,
        array  $typesCodes = null,
        int $parentId = null,
        string $siteId = null
    ): ?array {
        if (!Loader::IncludeModule('sale')) {
            return null;
        }

        $name = trim($name);
        if (!strlen($name)) {
            return null;
        }

        $filter = [
            '=NAME.NAME' => $name,
        ];

        if (isset($typesCodes)) {
            $filter['=TYPE.CODE'] = $typesCodes;
        }

        if (isset($parentId)) {
            $filter['=PARENTS.ID'] = $parentId;
        }

        $filter   = self::addSiteLocationsFilter($filter, $siteId);
        $location = self::getByFilter(['filter' => $filter], $langId, $siteId);
        if ($location) {
            return reset($location);
        }

        return null;
    }

    /**
     * @param array $query
     * @param string $langId
     * @param string|null $siteId
     * @return array|bool|mixed|null
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getByFilter(array $query, string $langId, string $siteId = null): ?array
    {
        $query['filter']['=NAME.LANGUAGE_ID'] = $query['filter']['=NAME.LANGUAGE_ID'] ?? $langId;
        $locations                            = LocationSaleModel::getList($query, $siteId);

        if (Options::isCapabilityMode()) {
            $locations = self::addCapabilityData($locations);
        }

        return $locations;
    }

    /**
     * @param string $langId
     * @param string|null $siteId
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getList(string $langId, string $siteId = null): ?array
    {
        $filter = [
            '=TYPE.CODE' => self::getAvailableTypeCodes(),
        ];

        $filter = self::addSiteLocationsFilter($filter, $siteId);

        return self::getByFilter(['filter' => $filter], $langId, $siteId);
    }

    /**
     * @param string $langId
     * @param string|null $siteId
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getCitiesList(string $langId, string $siteId = null): ?array
    {
        $filter = [
            '=TYPE.CODE' => [LocationEntity::TYPE_CITY],
        ];

        $filter = self::addSiteLocationsFilter($filter, $siteId);

        return self::getByFilter(['filter' => $filter], $langId, $siteId);
    }

    /**
     * @return array
     */
    protected static function getAvailableTypeCodes(): array
    {
        $typeCode = [LocationEntity::TYPE_CITY];
        if (Options::isListShowVillages()) {
            $typeCode[] = LocationEntity::TYPE_VILLAGE;
        }

        return $typeCode;
    }

    /**
     * @param              $q
     * @param string $langId
     * @param string|null $siteId
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function find($q, string $langId, string $siteId = null): ?array
    {
        $filter = [
            '=TYPE.CODE' => self::getAvailableTypeCodes(),
            '>=LNAME'    => $q,
            '%LNAME'     => $q
        ];

        $filter = self::addSiteLocationsFilter($filter, $siteId);

        return self::getByFilter(['filter' => $filter, 'limit' => Options::getSearchLimit()], $langId, $siteId);
    }

    /**
     * @param string $locationCode
     * @param string $parentTypeCode
     * @param string $langId
     * @param string|null $siteId
     * @return array|null
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getParent(
        string $locationCode,
        string $parentTypeCode,
        string $langId,
        string $siteId = null
    ): ?array {
        $parent = LocationSaleModel::getParent($locationCode, $parentTypeCode, $langId, $siteId);

        if ($parent && Options::isCapabilityMode()) {
            $parent = [$parent];
            $parent = self::addCapabilityData($parent);
            $parent = reset($parent);
        }

        return $parent;
    }


    /**
     * @param $locationId
     * @return array|mixed|null
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getZipById($locationId): ?string
    {
        if (mb_strlen($locationId) != mb_strlen(intval($locationId))) // locationId == code
        {
            $location = LocationTable::getRow([
                'filter' => ['=CODE' => $locationId],
                'select' => ['ID'],
                'cache'  => ['ttl' => Location::CACHE_TTL],
                'limit'  => 1,
            ]);

            if (!isset($location['ID'])) {
                return null;
            }

            $locationId = $location['ID'];
        }

        return Zip::getByLocationId($locationId);
    }

    /**
     * @param $locationData
     * @param string|null $langId
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @deprecated
     */
    protected static function addTreeInfo($locationData, string $langId = null): array
    {
        if (empty($locationData['CODE'])) {
            return [];
        }

        $tree = self::getTree($locationData['CODE'], $langId);

        foreach ($tree as $branch) {
            switch ($branch['I_TYPE']) {
                case 'CITY':
                    $locationData['CITY_NAME'] = $branch['I_NAME'];
                    $locationData['CITY_ID']   = $branch['I_CODE'];
                    $locationData['CITY_CODE'] = $branch['I_CODE'];
                    break;
                case 'REGION':
                    $locationData['REGION_NAME'] = $branch['I_NAME'];
                    $locationData['REGION_ID']   = $branch['I_CODE'];
                    $locationData['REGION_CODE'] = $branch['I_CODE'];
                    break;
                case 'COUNTRY':
                    $locationData['COUNTRY_NAME'] = $branch['I_NAME'];
                    $locationData['COUNTRY_ID']   = $branch['I_CODE'];
                    $locationData['COUNTRY_CODE'] = $branch['I_CODE'];
                    break;
            }
        }

        return $locationData;
    }

    /**
     * @param              $cityId
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return string|null
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @deprecated
     *      * @delete in 2023
     */
    public static function getNameById($cityId, $langId = LANGUAGE_ID, $siteId = null): ?string
    {
        return self::getNameByPrimary($cityId, $langId, $siteId);
    }

    /**
     * @param      $primary
     * @param bool $byCode
     * @return array|null
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @deprecated
     */
    public static function getByPrimary($primary, bool $byCode = true): ?array
    {
        return $byCode ? self::getByCode($primary, LANGUAGE_ID) : self::getById($primary, LANGUAGE_ID);
    }

    /**
     * @param              $code
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return string|null
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @deprecated
     */
    public static function getNameByPrimary($code, $langId = LANGUAGE_ID, $siteId = null): ?string
    {
        if (!Loader::IncludeModule('sale')) {
            return null;
        }

        $code = trim($code);
        if (!strlen($code)) {
            return null;
        }

        if (!SiteLocation::isCodeAllowsToSite($code, $siteId)) {
            return null;
        }

        $cacheId = crc32(__METHOD__ . implode(func_get_args()));
        $cache   = Application::getInstance()->getManagedCache();

        if ($cache->read(Location::CACHE_TTL, $cacheId)) {
            return $cache->get($cacheId);
        }

        $query = [
            'filter' => [
                '=NAME.LANGUAGE_ID' => $langId,
                '=CODE'             => $code
            ],
            'select' => ['LNAME' => 'NAME.NAME']
        ];

        $result = LocationTable::getRow($query);

        $cache->set($cacheId, $result['LNAME']);

        return $result['LNAME'] ?? null;
    }

    /**
     * @param string $cityName
     * @param string $langId
     * @param string|null $siteId
     * @return array|false|mixed|null
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @deprecated
     */
    public static function getIdByName(string $cityName, string $langId, string $siteId = null)
    {
        if (!Loader::includeModule('sale')) {
            return null;
        }

        $cityName = trim($cityName);
        if (!strlen($cityName)) {
            return null;
        }

        $cacheId = crc32(__METHOD__ . implode(func_get_args()));
        $cache   = Application::getInstance()->getManagedCache();

        if ($cache->read(Location::CACHE_TTL, $cacheId)) {
            return $cache->get($cacheId);
        }

        $query = [
            'filter' => [
                '=NAME.LANGUAGE_ID' => $langId,
                '=NAME.NAME'        => $cityName
            ],
            'select' => ['CODE']
        ];

        $query['filter'] = self::addSiteLocationsFilter($query['filter'], $siteId);

        $result = LocationTable::getRow($query);
        if (!isset($result['CODE'])) {
            return null;
        }

        $cache->set($cacheId, $result['CODE']);

        return $result['CODE'];
    }

    /**
     * @param string $siteId
     * @param string|mixed $langId
     * @return array|false|mixed|null
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @deprecated
     */
    public static function getFavoritesList(string $siteId, string $langId = LANGUAGE_ID): array
    {
        $defaults = DefaultSite::getList($langId, $siteId);
        $defaults = DefaultSite::makeLocationData($defaults, $langId, $siteId);

        if (Options::isCapabilityMode()) {
            $defaults = self::addCapabilityData($defaults);
        }

        return $defaults;
    }

    /**
     * @param array|null $locationsData
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @deprecated
     */
    protected static function addCapabilityData(array $locationsData = null): array
    {
        if (!isset($locationsData)) {
            return $locationsData;
        }

        foreach ($locationsData as &$locationData) {
            $locationData = array_merge($locationData, [
                'NAME'        => $locationData[LocationEntity::NAME],
                'ID'          => $locationData[LocationEntity::CODE],
                'CODE'        => $locationData[LocationEntity::CODE],
                'SHOW_REGION' => 'N',
            ]);

            $locationData = self::addTreeInfo($locationData, $locationData[LocationEntity::LANG_ID]);
        }

        return $locationsData;
    }

    /**
     * @param              $locationCode
     * @param mixed|string $langId
     * @return array|false|mixed
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * @deprecated
     */
    protected static function getTree($locationCode, $langId = LANGUAGE_ID)
    {
        $cacheId = crc32(__METHOD__ . $locationCode . $langId);
        $cache   = Application::getInstance()->getManagedCache();

        if ($cache->read(Location::CACHE_TTL, $cacheId)) {
            return $cache->get($cacheId);
        }

        $query = [
            'filter' => [
                '=CODE'              => $locationCode,
                '=PARENTS.TYPE.CODE' => ['REGION', 'COUNTRY', 'CITY']
            ],
            'select' => [
                'I_ID'   => 'PARENTS.ID',
                'I_CODE' => 'PARENTS.CODE',
                'I_NAME' => 'PARENTS.NAME.NAME',
                'I_TYPE' => 'PARENTS.TYPE.CODE',
                //  'I_TYPE_NAME_RU' => 'PARENTS.TYPE.NAME.NAME'
            ],
            'order'  => [
                'PARENTS.DEPTH_LEVEL' => 'asc'
            ]
        ];

        if ($langId) {
            $query['filter']['=PARENTS.NAME.LANGUAGE_ID']      = $langId;
            $query['filter']['=PARENTS.TYPE.NAME.LANGUAGE_ID'] = $langId;
        }

        $res  = LocationTable::getList($query);
        $tree = [];

        while ($item = $res->fetch()) {
            if ($item['I_CODE'] != $locationCode) {
                $tree[] = $item;
            }
        }

        $cache->set($cacheId, $tree);

        return $tree;
    }
}