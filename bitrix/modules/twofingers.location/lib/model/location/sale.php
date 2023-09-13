<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 05.02.2019
 * Time: 11:46
 *
 *
 */

namespace TwoFingers\Location\Model\Location;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SystemException;
use CSaleLocation;
use TwoFingers\Location\Model\Location,
    Bitrix\Main\Loader,
    TwoFingers\Location\Helper\Tools,
    Bitrix\Main\Application;
use TwoFingers\Location\Options;

/**
 * Class Sale
 *
 * @package TwoFingers\Location\Location
 *
 */
class Sale extends Location
{
    /**
     * @param      $primary
     * @param bool $byCode
     * @return false|mixed|null
     * @throws ArgumentNullException
     * @throws LoaderException
     */
    public static function getByPrimary($primary, bool $byCode = false): ?array
    {
        $primary = intval($primary);
        if (!$primary)
            throw new ArgumentNullException('primary');

        $locations = self::getByFilter(['filter' => ['ID' => $primary]]);

        return count($locations) ? reset($locations) : null;
    }
    /**
     * @param              $cityName
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return int|null
     * @throws LoaderException
     */
    public static function getIdByName($cityName, $langId = LANGUAGE_ID, $siteId = SITE_ID): ?int
    {
        if (!Loader::IncludeModule('sale'))
            return null;

        $cityName = trim($cityName);
        if (!strlen($cityName)) return null;

        $cacheId    = crc32(__METHOD__ . $cityName . $langId);
        $cache      = Application::getInstance()->getManagedCache();

        if ($cache->read(self::CACHE_TTL, $cacheId))
            return $cache->get($cacheId);

        $saleLocation = CSaleLocation::GetList(
            array("CITY_NAME"=>"ASC"),
            array("LID" => $langId, 'CITY_NAME' => $cityName),
            false,
            false,
            array('ID')
        )->Fetch();

        if (!isset($saleLocation['ID']))
            return null;

        $cache->set($cacheId, $saleLocation['ID']);

        return $saleLocation['ID'];
    }

    /**
     * @param              $cityId
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return false|mixed|null
     * @throws LoaderException
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
     * @return false|mixed|null
     * @throws LoaderException
     */
    public static function getNameByPrimary($primary, $langId = LANGUAGE_ID, $siteId = SITE_ID): ?string
    {
        if (!Loader::IncludeModule('sale'))
            return null;

        $primary = trim($primary);
        if (!strlen($primary))
            return null;

        $cacheId    = crc32(__METHOD__ . implode('', func_get_args()));
        $cache      = Application::getInstance()->getManagedCache();

        if ($cache->read(self::CACHE_TTL, $cacheId))
            return $cache->get($cacheId);

        $saleLocation = CSaleLocation::GetList(
            array("CITY_NAME" => "ASC"),
            array("LID" => $langId, 'ID' => $primary),
            false,
            false,
            array('CITY_NAME')
        )->Fetch();

        if (!isset($saleLocation['CITY_NAME']))
            return null;

        $cache->set($cacheId, $saleLocation['CITY_NAME']);

        return $saleLocation['CITY_NAME'];
    }

    /**
     * @param string      $langId
     * @param bool|string $siteId
     * @return array
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     *
     */
    public static function getFavoritesList($langId = LANGUAGE_ID, $siteId = SITE_ID): array
    {
        $cities         = self::getList($langId);
        $defaultCities  = Options::getValue('TF_LOCATION_DEFAULT_CITIES');
        $result         = [];

        foreach ($cities as $city)
            if(in_array($city['ID'], $defaultCities))
                $result[] = [
                    'NAME'      => htmlspecialcharsEx($city['NAME']),
                    'TRANSLIT'  => Tools::translit($city['LANME'], $langId),
                    'ID'        => $city['ID']
                ];

        return $result;
    }

    /**
     * @param              $q
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return array
     * @throws LoaderException
     */
    public static function find($q, $langId = LANGUAGE_ID, $siteId = SITE_ID): array
    {
        $filter = [
            '>=CITY_NAME'   => $q,
            '%CITY_NAME'    => $q,
            'LID'           => $langId
        ];

        return self::getByFilter(['filter' => $filter, 'nav' => ['nTopCount' => Options::getSearchLimit()]]);
    }

    /**
     * @param array $query
     * @return bool|mixed|null
     * @throws LoaderException
     */
    public static function getByFilter(array $query = [])
    {
        if (!Loader::includeModule('sale'))
            return null;

        $cacheId    = crc32(__METHOD__ . serialize($query));
        $cache      = Application::getInstance()->getManagedCache();

        if ($cache->read(self::CACHE_TTL, $cacheId))
            return $cache->get($cacheId);

        $order  = $query['order'] ?? ["CITY_NAME_LANG"=>"ASC"];
        $select = $query['select'] ?? ['ID', 'CITY_ID', 'CITY_NAME', 'REGION_NAME', 'REGION_ID', 'COUNTRY_ID', 'COUNTRY_NAME'];

        $dbLocations = CSaleLocation::GetList(
            $order,
            $query['filter'] ?? [],
            false,
            $query['nav'] ?? ['nTopCount' => Options::getLocationsLimit()],
            $select);

        $locations = [];
        $langId    = $query['filter']['LID'] ?? LANGUAGE_ID;

        while ($locationRaw = $dbLocations->Fetch()) {

            if (empty($locationRaw['CITY_ID']))
                continue;

            $locationData = Array(
                'NAME'          => htmlspecialcharsEx($locationRaw['CITY_NAME']),
                'ID'            => $locationRaw['ID'],
                'TRANSLIT'      => Tools::translit($locationRaw['CITY_NAME'], $langId),
                'REGION_NAME'   => $locationRaw['REGION_NAME'],
                'REGION_ID'     => $locationRaw['REGION_ID'],
                'COUNTRY_NAME'  => $locationRaw['COUNTRY_NAME'],
                'COUNTRY_ID'    => $locationRaw['COUNTRY_ID'],
                'SHOW_REGION'   => 'N'
            );

            $locations[$locationRaw['ID']] = $locationData;
        }

        $cache->set($cacheId, $locations);

        return $locations;
    }

    /**
     * @param mixed|string       $langId
     * @param false|mixed|string $siteId
     * @return array|bool|mixed|null
     * @throws LoaderException
     */
    public static function getList($langId = LANGUAGE_ID, $siteId = SITE_ID): array
    {
        return self::getByFilter(['filter' => ['LID' => $langId]]);
    }

    /**
     * @param $locationId
     * @return array|false|mixed|null
     * @throws LoaderException
     */
    public static function getZipById($locationId): ?string
    {
        if (!Loader::includeModule('sale'))
            return null;

        $cacheId    = crc32(__METHOD__ . $locationId);
        $cache      = Application::getInstance()->getManagedCache();

        if ($cache->read(self::CACHE_TTL, $cacheId))
            return $cache->get($cacheId);

        $rsZip      = CSaleLocation::GetLocationZIP($locationId);
        $arResult   = '';
        while ($arZip = $rsZip->fetch())
        {
            $arResult = $arZip['ZIP'];
            break;
        }

        $cache->set($cacheId, $arResult);

        return $arResult;
    }
}