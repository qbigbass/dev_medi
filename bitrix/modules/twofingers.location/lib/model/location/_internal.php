<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 05.02.2019
 * Time: 12:33
 *
 *
 */

namespace TwoFingers\Location\Model\Location;

use Bitrix\Iblock\SectionTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use TwoFingers\Location\Entity\Location;
use TwoFingers\Location\Model\Iblock\Location\Element;
use TwoFingers\Location\Model\Iblock\Location\Section;
use TwoFingers\Location\Model\Iblock\Location as LocationIblock;
use TwoFingers\Location\Model\Location as LocationModel;
use TwoFingers\Location\Options;

/**
 * Class Internal
 *
 * @package TwoFingers\Location\Location
 *
 */
class Internal extends LocationModel
{

    /**
     * @param int $id
     * @param string $langId
     * @param string|null $siteId
     * @return false|mixed|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getById(int $id, string $langId, string $siteId = null): ?array
    {
        if (!$id) {
            throw new ArgumentNullException('id');
        }

        $filter       = Element::getDefaultFilter($siteId);
        $filter['ID'] = $id;

        $locations = self::getByFilter(['filter' => $filter], $langId, $siteId);

        return count($locations) ? reset($locations) : null;
    }

    /**
     * @param string $code
     * @param string $langId
     * @param string|null $siteId
     * @return false|mixed|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getByCode(string $code, string $langId, string $siteId = null): ?array
    {
        if (!Loader::includeModule('iblock')) {
            return null;
        }

        $code = trim($code);
        if (!strlen($code)) {
            throw new ArgumentNullException('code');
        }

        $filter         = Element::getDefaultFilter($siteId);
        $filter['CODE'] = $code;

        $locations = self::getByFilter(['filter' => $filter], $langId, $siteId);

        return count($locations) ? reset($locations) : null;
    }


    /**
     * @param string $name
     * @param string $langId
     * @param array|null $typesCodes
     * @param string|null $siteId
     * @return array|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     * @throws SystemException|LoaderException
     */
    public static function getByName(
        string $name,
        string $langId,
        array  $typesCodes = null,
        string $siteId = null
    ): ?array {
        if (!Loader::includeModule('iblock')) {
            return null;
        }

        $name = trim($name);
        if (!strlen($name)) {
            return null;
        }

        $filter         = Element::getDefaultFilter($siteId);
        $filter['NAME'] = $name;

        if (isset($typesCodes)) {
            $filter = self::addTypesFilter($filter, $typesCodes);
        }


        $locations = self::getByFilter(['filter' => $filter], $langId, $siteId);

        return $locations ? reset($locations) : null;
    }


    /**
     * @param string $locationCode
     * @param string $parentTypeCode
     * @param string $langId
     * @param string|null $siteId
     * @return array|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getParent(
        string $locationCode,
        string $parentTypeCode,
        string $langId,
        string $siteId = null
    ): ?array {
        $location = self::getByCode($locationCode, $langId, $siteId);

        if (!isset($location[Location::PARENT_ID])) {
            return null;
        }

        do {
            $locationRaw = Section::getById($location[Location::PARENT_ID]);

            if (!$locationRaw) {
                break;
            }

            $location = Section::makeLocationData($locationRaw, $langId, $siteId);

            if ($location[Location::TYPE] == $parentTypeCode) {
                return $location;
            }
        } while (isset($location[Location::PARENT_ID]));

        return null;
    }

    /**
     * @param string $langId
     * @param string|null $siteId
     * @return array
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getCitiesList(string $langId, string $siteId = null): array
    {
        $filter = Element::getDefaultFilter($siteId);
        $filter = self::addTypesFilter($filter, [Location::TYPE_CITY]);

        return self::getByFilter(['filter' => $filter], $langId, $siteId);
    }

    /**
     * @param string $langId
     * @param string|null $siteId
     * @return array|bool|mixed|null
     * @throws LoaderException
     * @throws SystemException
     */
    public static function getList(string $langId, string $siteId = null): array
    {
        $filter = Element::getDefaultFilter($siteId);
        $filter = self::addCityTypesFilter($filter);

        return self::getByFilter(['filter' => $filter], $langId, $siteId);
    }

    /**
     * @param array $filter
     * @param array $typesCodes
     * @return array
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    protected static function addTypesFilter(array $filter, array $typesCodes): array
    {
        $enums = [];

        foreach ($typesCodes as $typeCode) {
            $enumId = LocationIblock::getEnumIdByTypeCode($typeCode);
            if (isset($enumId)) {
                $enums[] = $enumId;
            }
        }

        if (count($enums)) {
            $filter['PROPERTY_' . LocationIblock::PROPERTY_TYPE] = $enums;
        }

        return $filter;
    }

    /**
     * @param $filter
     * @return array
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    protected static function addCityTypesFilter($filter): array
    {
        $typesToAdd = [Location::TYPE_CITY];

        if (Options::isListShowVillages()) {
            $typesToAdd = [Location::TYPE_VILLAGE];
        }

        return self::addTypesFilter($filter, $typesToAdd);
    }

    /**
     * @param array $filter
     * @param string $langId
     * @param string|null $siteId
     * @return array|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getByFilter(array $filter, string $langId, string $siteId = null): ?array
    {
        $locationsRawData = Element::getList($filter);
        if (!empty($locationsRawData)) {
            $locationsData = Element::makeLocationsData($locationsRawData, $langId, $siteId);
            if (Options::isCapabilityMode()) {
                $locationsData = self::addCapabilityData($locationsData);
            }
        }

        if (!isset($locationsData)) {

            $searchInSection = true;

            if (isset($filter['filter']['PROPERTY_' . LocationIblock::PROPERTY_TYPE])) {
                $enumsToSearch = [];
                foreach ([Location::TYPE_REGION, Location::TYPE_COUNTRY] as $typeCode) {
                    if (null !== $enumId = LocationIblock::getEnumIdByTypeCode($typeCode)) {
                        $enumsToSearch[] = $enumId;
                    }
                }

                if (!$enumsToSearch || !array_intersect($enumsToSearch,
                        $filter['filter']['PROPERTY_' . LocationIblock::PROPERTY_TYPE])) {
                    $searchInSection = false;
                }
            }

            if ($searchInSection) {
                $sectionFilter = Section::convertElementFilter($filter);
                if (isset($sectionFilter)) {
                    $locationsRawData = Section::getList($sectionFilter);
                    if (isset($locationsRawData)) {
                        $locationsData = Section::makeLocationsData($locationsRawData, $langId, $siteId);
                    }
                }
            }
        }

        return $locationsData ?? null;
    }

    /**
     * @param array|null $locationsData
     * @return array|null
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    protected static function addCapabilityData(array $locationsData = null): ?array
    {
        if (!isset($locationsData)) {
            return null;
        }

        foreach ($locationsData as &$locationData) {
            $region = SectionTable::getRow([
                'filter' => [
                    //'=CODE' => $locationData[Location::PARENT_CODE],
                    '=ID' => $locationData[Location::PARENT_ID],

                ],
                'select' => [
                    'ID',
                    'NAME',
                    'PARENT_ID'   => 'PARENT_SECTION.ID',
                    'PARENT_NAME' => 'PARENT_SECTION.NAME'
                ],
                'cache'  => ['ttl' => LocationModel::CACHE_TTL]
            ]);

            $locationData = array_merge($locationData, [
                'NAME'         => $locationData[Location::NAME],
                'ID'           => $locationData[Location::ID],
                'CODE'         => $locationData[Location::ID],
                'REGION_NAME'  => $region['NAME'],
                'REGION_ID'    => $region['ID'],
                'REGION_CODE'  => $region['ID'],
                'COUNTRY_NAME' => $region['PARENT_NAME'],
                'COUNTRY_ID'   => $region['PARENT_ID'],
                'SHOW_REGION'  => 'N'
            ]);
        }

        return $locationsData;
    }

    /**
     * @param              $q
     * @param string $langId
     * @param string|null $siteId
     * @return array|bool|mixed|null
     * @throws LoaderException
     * @throws SystemException
     */
    public static function find($q, string $langId, string $siteId = null): array
    {
        $filter          = Element::getDefaultFilter($siteId);
        $filter          = self::addCityTypesFilter($filter);
        $filter['%NAME'] = $q;

        return self::getByFilter(['filter' => $filter, 'nav' => ['nTopCount' => Options::getSearchLimit()]], $langId,
            $siteId);
    }

    /**
     * @param string $siteId
     * @param string|mixed $langId
     * @return array|bool|mixed|null
     * @throws LoaderException
     * @throws SystemException
     */
    public static function getFavoritesList(string $siteId, string $langId = LANGUAGE_ID): ?array
    {
        $filter = Element::getDefaultFilter($siteId);

        $filter['!PROPERTY_' . LocationIblock::PROPERTY_FEATURED] = false;

        return self::getByFilter(['filter' => $filter, 'order' => ['SORT' => 'ASC']], $langId, $siteId);
    }

    /**
     * @param string $siteId
     * @param string|mixed $langId
     * @return mixed|null
     * @throws LoaderException
     * @throws SystemException
     */
    public static function getDefault(string $siteId, string $langId = LANGUAGE_ID): ?array
    {
        $filter                                                  = Element::getDefaultFilter($siteId);
        $filter['!PROPERTY_' . LocationIblock::PROPERTY_DEFAULT] = false;

        $default = self::getByFilter(['filter' => $filter], $langId, $siteId);

        return $default ? reset($default) : null;
    }

    /**
     * @param $locationId
     * @return string|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getZipById($locationId): ?string
    {
        $location = self::getById($locationId, LANGUAGE_ID);

        return $location['PROPERTY_' . LocationIblock::PROPERTY_ZIP . '_VALUE'] ?? null;
    }

    /**
     * @param              $cityId
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return mixed|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     * @deprecated
     * @delete in 2023
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
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     * @deprecated
     */
    public static function getNameByPrimary($primary, $langId = LANGUAGE_ID, $siteId = SITE_ID): ?string
    {
        $primary = intval($primary);
        if (!$primary) {
            return null;
        }

        $filter = [
            'IBLOCK_ID'                                    => LocationIblock::getId(),
            'ACTIVE'                                       => 'Y',
            'ID'                                           => $primary,
            'PROPERTY_' . LocationIblock::PROPERTY_SITE_ID => [$siteId, null]
        ];

        $list = self::getByFilter(['filter' => $filter], $langId);

        return $list[$primary]['NAME'] ?? null;
    }

    /**
     * @param      $primary
     * @param bool $byCode
     * @return array|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @deprecated
     */
    public static function getByPrimary($primary, bool $byCode = false): ?array
    {
        return $byCode ? self::getByCode($primary, LANGUAGE_ID) : self::getById($primary, LANGUAGE_ID);
    }

    /**
     * @param string $cityName
     * @param string $langId
     * @param string|null $siteId
     * @return int|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @deprecated
     */
    public static function getIdByName(string $cityName, string $langId, string $siteId = null): ?int
    {
        $cityName = trim($cityName);
        if (!strlen($cityName)) {
            return null;
        }

        $filter = [
            'IBLOCK_ID' => LocationIblock::getId(),
            'ACTIVE'    => 'Y',
            'NAME'      => $cityName
        ];
        $list   = self::getByFilter(['filter' => $filter], $langId);

        if (empty($list)) {
            return null;
        }

        $element = reset($list);

        return $element[Location::ID] ?? null;
    }
}