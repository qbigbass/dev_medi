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
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use TwoFingers\Location\Model\Iblock\Location;
use TwoFingers\Location\Model\Location as LocationModel;
use TwoFingers\Location\Helper\Tools;
use \TwoFingers\Location\Model\Iblock\Location as LocationIblock;
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
     * @param mixed|string       $langId
     * @param false|mixed|string $siteId
     * @return array|bool|mixed|null
     * @throws LoaderException
     * @throws SystemException
     */
    public static function getList($langId = LANGUAGE_ID, $siteId = SITE_ID): array
    {
        $filter = [
            'PROPERTY_' . LocationIblock::PROPERTY_SITE_ID => $siteId
        ];

        return self::getByFilter(['filter' => $filter], $langId);
    }

    /**
     * @param array        $filter
     * @param mixed|string $langId
     * @return array|false|mixed|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     */
    public static function getByFilter(array $filter = [], $langId = LANGUAGE_ID)
    {
        if (!Loader::IncludeModule('iblock'))
            return null;

        $cacheId    = md5(__METHOD__ . serialize($filter) . $langId);
        $cache      = Application::getInstance()->getManagedCache();
        if ($cache->read(self::CACHE_TTL, $cacheId))
            return $cache->get($cacheId);

        $filter['filter']['IBLOCK_ID']    = LocationIblock::getId();
        $filter['filter']['ACTIVE']       = 'Y';

        $elements   = \CIBlockElement::GetList(
            isset($filter['order']) ? $filter['order'] : ['NAME' => 'ASC'],
            isset($filter['filter']) ? $filter['filter'] : ['NAME' => 'ASC'],
            false,
            isset($filter['nav']) ? $filter['nav'] : ['nTopCount' => Options::getLocationsLimit()],
            ['*', 'PROPERTY_FEATURES', 'PROPERTY_DEFAULT']
        );
        $locations  = [];

        while ($element = $elements->Fetch())
        {
            $region = SectionTable::getRow([
                'filter' => ['=ID' => $element['IBLOCK_SECTION_ID']],
                'select' => ['ID', 'NAME', 'PARENT_ID' => 'PARENT_SECTION.ID', 'PARENT_NAME' => 'PARENT_SECTION.NAME']
            ]);

            $location = [
                'NAME'          => $element['NAME'],
                'ID'            => $element['ID'],
                'CODE'          => $element['ID'],
                'REGION_NAME'   => $region['NAME'],
                'REGION_ID'     => $region['ID'],
                'REGION_CODE'   => $region['ID'],
                'COUNTRY_NAME'  => $region['PARENT_NAME'],
                'COUNTRY_ID'    => $region['PARENT_ID'],
                'TRANSLIT'      => Tools::translit($element['NAME'], $langId),
                'SHOW_REGION'   => 'N'
            ];

            $locations[$location['ID']] = $location;
        }

        $cache->set($cacheId, $locations);

        return $locations;
    }

    /**
     * @param              $q
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return array|bool|mixed|null
     * @throws LoaderException
     * @throws SystemException
     */
    public static function find($q, $langId = LANGUAGE_ID, $siteId = SITE_ID): array
    {
        $filter = [
            '%NAME' => $q,
            'PROPERTY_' . LocationIblock::PROPERTY_SITE_ID => $siteId
        ];

        return self::getByFilter(['filter' => $filter, 'nav' => ['nTopCount' => Options::getSearchLimit()]], $langId);
    }

    /**
     * @param mixed|string      $langId
     * @param bool|mixed|string $siteId
     * @return array|bool|mixed|null
     * @throws LoaderException
     * @throws SystemException
     */
    public static function getFavoritesList($langId = LANGUAGE_ID, $siteId = SITE_ID): array
    {
        $filter = [
            '!PROPERTY_' . LocationIblock::PROPERTY_FEATURED    => false,
            'PROPERTY_' . LocationIblock::PROPERTY_SITE_ID      => $siteId
        ];

        return self::getByFilter(['filter' => $filter, 'order' => ['SORT' => 'ASC']], $langId);
    }

    /**
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return mixed|null
     * @throws LoaderException
     * @throws SystemException
     */
    public static function getDefault($langId = LANGUAGE_ID, $siteId = SITE_ID): ?array
    {
        $filter = [
            '!PROPERTY_' . Location::PROPERTY_DEFAULT => false,
            'PROPERTY_' . Location::PROPERTY_SITE_ID  => $siteId,
        ];

        $default = self::getByFilter(['filter' => $filter], $langId);

        return $default ? reset($default) : null;
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
     */
    public static function getNameByPrimary($primary, $langId = LANGUAGE_ID, $siteId = SITE_ID): ?string
    {
        $primary = intval($primary);
        if (!$primary)
            return null;

        $filter = [
            'IBLOCK_ID' => LocationIblock::getId(),
            'ACTIVE'    => 'Y',
            'ID'        => $primary,
            'PROPERTY_' . Location::PROPERTY_SITE_ID => $siteId
        ];

        $list = self::getByFilter(['filter' => $filter], $langId);
        if (isset($list[$primary]['NAME']))
            return $list[$primary]['NAME'];

        return null;
    }

    /**
     * @param              $cityName
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return int|null |null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     */
    public static function getIdByName($cityName, $langId = LANGUAGE_ID, $siteId = SITE_ID)
    {
        $cityName = trim($cityName);
        if (!strlen($cityName))
            return null;

        $filter = [
            'IBLOCK_ID' => LocationIblock::getId(),
            'ACTIVE'    => 'Y',
            'NAME'      => $cityName
        ];
        $list = self::getByFilter(['filter' => $filter], $langId);

        if (empty($list))
            return null;

        $element = reset($list);
        return isset($element['ID'])
            ? $element['ID']
            : null;
    }

    /**
     * @param $locationId
     * @return null
     *
     */
    public static function getZipById($locationId): ?string
    {
        return null;
    }
}