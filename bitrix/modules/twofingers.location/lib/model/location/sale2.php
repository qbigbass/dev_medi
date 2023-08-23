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

use Bitrix\Sale\Location\DefaultSiteTable;
use Bitrix\Sale\Location\ExternalTable;
use Bitrix\Sale\Location\LocationTable;
use Bitrix\Sale\Location\SiteLocationTable;
use TwoFingers\Location\Model\Location;
use Bitrix\Main\Loader;
use TwoFingers\Location\Helper\Tools;
use Bitrix\Main\Application;
use TwoFingers\Location\Options;
use TwoFingers\Location\Settings;
use \Bitrix\Main;

/**
 * Class Sale2
 *
 * @package TwoFingers\Location\Model\Location
 */
class Sale2 extends Location
{
    /**
     * @param              $cityName
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return array|false|mixed|null
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getIdByName($cityName, $langId = LANGUAGE_ID, $siteId = SITE_ID)
    {
        if (!Loader::includeModule('sale'))
            return null;

        $cityName = trim($cityName);
        if (!strlen($cityName))
            return null;

        $cacheId    = crc32(__METHOD__ . implode(func_get_args()));
        $cache      = Application::getInstance()->getManagedCache();

        if ($cache->read(self::CACHE_TTL, $cacheId))
            return $cache->get($cacheId);

        $query = array(
            'filter' => array(
                '=NAME.LANGUAGE_ID' => $langId,
                '=NAME.NAME'        => $cityName
            ),
            'select' => array('CODE')
        );

        if (!empty($siteId)/*Options::isSiteLocationsOnly()*/)
        {
            $siteLocations = self::getCodesBySiteId($siteId);
            if (empty($siteLocations))
                return [];

            $query['filter']['=CODE'] = $siteLocations;
        }

        $result = LocationTable::getRow($query);
        if (!isset($result['CODE']))
            return null;

        $cache->set($cacheId, $result['CODE']);

        return $result['CODE'];
    }

    /**
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return mixed|null
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getDefault($langId = LANGUAGE_ID, $siteId = SITE_ID):? array
    {
        $defaultCode = Options::getDefaultLocation($siteId);
        if (!$defaultCode)
            return null;

        if (!empty($siteId)/*Options::isSiteLocationsOnly()*/)
        {
            $siteLocations = self::getCodesBySiteId($siteId);
            if (count($siteLocations) && !in_array($defaultCode, $siteLocations))
                return null;
        }

        $filter = [
            '=NAME.LANGUAGE_ID' => $langId,
            '=CODE'             => $defaultCode,
        ];

        $default = self::getByFilter(['filter' => $filter]);
        if ($default)
            return reset($default);

        return null;
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
     */
    public static function getNameById($cityId, $langId = LANGUAGE_ID, $siteId = SITE_ID): ?string
    {
        return self::getNameByPrimary($cityId, $langId, $siteId);
    }

   /**
     * @param              $primary
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return string|null
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getNameByPrimary($primary, $langId = LANGUAGE_ID, $siteId = SITE_ID): ?string
    {
        if (!Loader::IncludeModule('sale'))
            return null;

        $primary = trim($primary);
        if (!strlen($primary))
            return null;

        $cacheId    = crc32(__METHOD__ . implode(func_get_args()));
        $cache      = Application::getInstance()->getManagedCache();

        if ($cache->read(self::CACHE_TTL, $cacheId))
            return $cache->get($cacheId);

        if (!empty($siteId)/*Options::isSiteLocationsOnly()*/)
        {
            $siteLocations = self::getCodesBySiteId($siteId);
            if (empty($siteLocations) || !in_array($primary, $siteLocations))
                return null;
        }

        $query = array(
            'filter' => array(
                '=NAME.LANGUAGE_ID' => $langId,
                '=CODE'             => $primary
            ),
            'select' => array('LNAME' => 'NAME.NAME')
        );

        $result = LocationTable::getRow($query);

        if (!isset($result['LNAME']))
            return null;

        $cache->set($cacheId, $result['LNAME']);

        return $result['LNAME'];
    }

    /**
     * @param mixed|string      $langId
     * @param bool|mixed|string $siteId
     * @return array|false|mixed|null
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getFavoritesList($langId = LANGUAGE_ID, $siteId = SITE_ID): array
    {
        if (!Loader::IncludeModule('sale'))
            return [];

        $cacheId    = crc32(__METHOD__ . $langId . $siteId);
        $cache      = Application::getInstance()->getManagedCache();

        if ($cache->read(self::CACHE_TTL, $cacheId))
            return $cache->get($cacheId);

        // default
        $res = DefaultSiteTable::getList(array(
            'filter' => array(
                'SITE_ID'                   => $siteId,
                'LOCATION.NAME.LANGUAGE_ID' => $langId
            ),
            'order' => array(
                'SORT' => 'asc'
            ),
            'select' => array(
                'CODE'      => 'LOCATION.CODE',
                'ID'        => 'LOCATION.ID',
                'NAME'      => 'LOCATION.NAME.NAME',
            )
        ));

        $defaults = [];
        while($item = $res->fetch())
        {
             $location = Array(
                'NAME'      => htmlspecialcharsEx($item['NAME']),
                'TRANSLIT'  => Tools::translit($item['NAME'], $langId),
                'ID'        => $item['CODE'], // @deprecated
                'CODE'      => $item['CODE'],
                'SHOW_REGION' => 'N',
            );

            $location = self::addTreeInfo($location);

            $defaults[] = $location;
        }

        $cache->set($cacheId, $defaults);

        return $defaults;
    }

    /**
     * @param array $filter
     * @return array|bool|mixed|null
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     *
     */
    public static function getByFilter(array $filter = [])
    {
        if (!Loader::IncludeModule('sale'))
            return null;

        $cacheId        = crc32(__METHOD__ . serialize($filter));
        $cache          = Application::getInstance()->getManagedCache();

        if ($cache->read(self::CACHE_TTL, $cacheId))
            return $cache->get($cacheId);

        $langId = isset($filter['=NAME.LANGUAGE_ID']) ? $filter['=NAME.LANGUAGE_ID'] : LANGUAGE_ID;

        if (!isset($filter['select']))
            $filter['select'] = ['ID', 'CODE', 'LEFT_MARGIN', 'RIGHT_MARGIN', 'LNAME' => 'NAME.NAME'];

        if (!isset($filter['order']))
            $filter['order'] = ['LNAME' => 'asc'];

        if (!isset($filter['limit']))
            $filter['limit'] = Options::getLocationsLimit();

        $dbResult   = LocationTable::getList($filter);
        $locations  = array();

        while ($item = $dbResult->fetch())
        {
            $location = Array(
                'NAME'          => htmlspecialcharsEx($item['LNAME']),
                'ID'            => $item['CODE'], // @deprecated
                'CODE'          => $item['CODE'],
                'TRANSLIT'      => Tools::translit($item['LNAME'], $langId),
                'SHOW_REGION'   => 'N'
            );

            $location = self::addTreeInfo($location);

            $locations[$location['ID']] = $location;
        }

        $cache->set($cacheId, $locations);

        return $locations;
    }

    /**
     * @param false|mixed|string $siteId
     * @return array|bool|mixed
     * @throws Main\ArgumentException
     * @throws Main\SystemException
     */
    public static function getCodesBySiteId($siteId)
    {
        $siteId = trim($siteId);
        if (!strlen($siteId))
            throw new Main\ArgumentNullException('siteId');

        $cacheId    = crc32(__METHOD__ . serialize(func_get_args()));
        $cache      = Application::getInstance()->getManagedCache();

        if ($cache->read(self::CACHE_TTL, $cacheId))
            return $cache->get($cacheId);

        $locations = SiteLocationTable::getList([
            'filter'    => ['=SITE_ID' => $siteId],
            'select'    => [
                'LOCATION_ID',
                'LOCATION_LEFT_MARGIN' => 'LOCATION.LEFT_MARGIN',
                'LOCATION_RIGHT_MARGIN' => 'LOCATION.RIGHT_MARGIN',
                'LOCATION_CODE' => 'LOCATION.CODE'
            ],
        ]);
        $result = [];

        while ($location = $locations->fetch())
        {
            $result[] = $location['LOCATION_CODE'];

            $res = LocationTable::getList([
                'filter' => [
                    '>LEFT_MARGIN' => $location['LOCATION_LEFT_MARGIN'],
                    '<RIGHT_MARGIN' => $location['LOCATION_RIGHT_MARGIN'],
                ],
                'select' => ['CODE']
            ]);

            while($locParent = $res->fetch())
            {
                $result[] = $locParent['CODE'];
            }
        }

        $cache->set($cacheId, $result);

        return $result;
    }

    /**
     * @param mixed|string       $langId
     * @param false|mixed|string $siteId
     * @return array|bool|mixed|null
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getList($langId = LANGUAGE_ID, $siteId = SITE_ID): array
    {
        $code = ['CITY'];
        if (Settings::get('TF_LOCATION_SHOW_VILLAGES') === 'Y')
            $code[] = 'VILLAGE';

        $filter = [
            '=NAME.LANGUAGE_ID' => $langId,
            '=TYPE.CODE'        => $code,
        ];

        if (!empty($siteId)/*Options::isSiteLocationsOnly()*/)
        {
            $siteLocations = self::getCodesBySiteId($siteId);
            if (empty($siteLocations))
                return [];

            $filter['=CODE'] = $siteLocations;
        }

        return self::getByFilter(['filter' => $filter]);
    }

    /**
     * @param mixed|string       $langId
     * @param false|mixed|string $siteId
     * @return array|bool|mixed|null
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getCitiesList($langId = LANGUAGE_ID, $siteId = SITE_ID): array
    {
        $filter = [
            '=NAME.LANGUAGE_ID' => $langId,
            '=TYPE.CODE'        => ['CITY'],
        ];

        if (!empty($siteId)/*Options::isSiteLocationsOnly()*/)
        {
            $siteLocations = self::getCodesBySiteId($siteId);
            if (empty($siteLocations))
                return [];

            $filter['=CODE'] = $siteLocations;
        }

        return self::getByFilter(['filter' => $filter]);
    }

    /**
     * @param              $q
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function find($q, $langId = LANGUAGE_ID, $siteId = SITE_ID): array
    {
        $code = ['CITY'];
        if (Settings::get('TF_LOCATION_SHOW_VILLAGES') === 'Y')
            $code[] = 'VILLAGE';

        $filter = [
            '=NAME.LANGUAGE_ID' => $langId,
            '=TYPE.CODE'        => $code,
            '>=LNAME'           => $q,
            '%LNAME'            => $q
        ];

        if (!empty($siteId)/*Options::isSiteLocationsOnly()*/)
        {
            $siteLocations = self::getCodesBySiteId($siteId);
            if (empty($siteLocations))
                return [];

            $filter['=CODE'] = $siteLocations;
        }

        return self::getByFilter(['filter' => $filter, 'limit' => Options::getSearchLimit()]);
    }

    /**
     * @param $location
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    protected static function addTreeInfo($location): array
    {
        if (empty($location['CODE'])) return [];

        $tree = self::getTree($location['CODE']);
        foreach ($tree as $branch)
        {
            switch ($branch['I_TYPE']){
                case 'REGION':
                    $location['REGION_NAME']    = $branch['I_NAME'];
                    $location['REGION_ID']      = $branch['I_CODE'];
                    $location['REGION_CODE']    = $branch['I_CODE'];
                    break;
                case 'COUNTRY':
                    $location['COUNTRY_NAME']   = $branch['I_NAME'];
                    $location['COUNTRY_ID']     = $branch['I_CODE'];
                    $location['COUNTRY_CODE']   = $branch['I_CODE'];
                    break;
            }
        }

        return $location;
    }

    /**
     * @param              $locationCode
     * @param mixed|string $langId
     * @return array|false|mixed
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getTree($locationCode, $langId = LANGUAGE_ID)
    {
        $cacheId    = crc32(__METHOD__ . $locationCode . $langId);
        $cache      = Application::getInstance()->getManagedCache();

        if ($cache->read(self::CACHE_TTL, $cacheId))
            return $cache->get($cacheId);

        $query = [
            'filter' => array(
                '=CODE'                             => $locationCode,
                '=PARENTS.NAME.LANGUAGE_ID'         => $langId,
                '=PARENTS.TYPE.NAME.LANGUAGE_ID'    => $langId,
                '=PARENTS.TYPE.CODE'                => ['REGION', 'COUNTRY']
            ),
            'select' => array(
                'I_ID'      => 'PARENTS.ID',
                'I_CODE'    => 'PARENTS.CODE',
                'I_NAME'    => 'PARENTS.NAME.NAME',
                'I_TYPE'    => 'PARENTS.TYPE.CODE',
                //  'I_TYPE_NAME_RU' => 'PARENTS.TYPE.NAME.NAME'
            ),
            'order' => array(
                'PARENTS.DEPTH_LEVEL' => 'asc'
            )
        ];

        $res    = LocationTable::getList($query);
        $tree   = [];

        while($item = $res->fetch())
            $tree[] = $item;

        $cache->set($cacheId, $tree);

        return $tree;
    }

    /**
     * @param $locationCode
     * @return array|mixed|null
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    public static function getZipById($locationCode): ?string
    {
        $result = ExternalTable::getList(array(
            'filter' => array(
                '=SERVICE.CODE' => 'ZIP',
                '=LOCATION_ID'  => $locationCode
            ),
            'select' => array(
                'ID',
                'ZIP' => 'XML_ID'
            ),
            'cache' => ['ttl' => self::CACHE_TTL],
            'limit' => 1,
        ))->fetch();

        return $result['ZIP'] ? : null;
    }

    /**
     * @param              $locationCode
     * @param mixed|string $langId
     * @return false|mixed|null
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    protected static function getIdByCode($locationCode, $langId = LANGUAGE_ID)
    {
        if (!Loader::includeModule('sale'))
            return null;

        $cacheId    = crc32(__METHOD__ . implode(func_get_args()));
        $cache      = Application::getInstance()->getManagedCache();

        if ($cache->read(self::CACHE_TTL, $cacheId))
            return $cache->get($cacheId);

        $query = array(
            'filter' => array(
                '=NAME.LANGUAGE_ID' => $langId,
                '=CODE'             => $locationCode
            ),
            'select' => array('ID')
        );

        $result = LocationTable::getRow($query);

        if (!isset($result['ID']))
            return null;

        $cache->set($cacheId, $result['ID']);

        return $result['ID'];
    }
}