<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 05.02.2019
 * Time: 11:47
 *
 *
 */

namespace TwoFingers\Location\Model;

use Bitrix\Main\Loader;
use TwoFingers\Location\Options;

/**
 * Class Location
 *
 * @package TwoFingers\Location
 *
 */
class Location
{
    const TYPE__SALE        = 'sale';
    const TYPE__SALE_2      = 'sale2';
    const TYPE__INTERNAL    = 'internal';

    const CACHE_TTL         = 360000;

    /**
     * @return string
     */
    public static function getType(): string
    {
        $locationsType = Options::getLocationsType();
        if (in_array($locationsType, [self::TYPE__SALE, self::TYPE__SALE_2, self::TYPE__INTERNAL]))
            return $locationsType;

        try{
            if (Loader::includeModule('sale'))
                return method_exists('CSaleLocation','isLocationProMigrated')
                && \CSaleLocation::isLocationProMigrated()
                    ? self::TYPE__SALE_2
                    : self::TYPE__SALE;

            return self::TYPE__INTERNAL;

        } catch (\Exception $e) {
            return self::TYPE__INTERNAL;
        }
    }

    /**
     * @return string|null
     */
    public static function getClassName(): ?string
    {
        $className = __NAMESPACE__ . '\Location\\' . self::getType();

        return class_exists($className) && is_subclass_of($className, __CLASS__) ? $className : null;
    }

    /**
     * @param              $cityName
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return null|int
     */
    public static function getIdByName($cityName, $langId = LANGUAGE_ID, $siteId = SITE_ID)
    {
        $className = self::getClassName();

        if ($className && method_exists($className, 'getIdByName'))
            return $className::getIdByName($cityName, $langId, $siteId);

        return null;
    }

    /**
     * @param              $cityId
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return string|null
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
     */
    public static function getNameByPrimary($primary, $langId = LANGUAGE_ID, $siteId = SITE_ID): ?string
    {
        $className = self::getClassName();

        if ($className && method_exists($className, 'getNameByPrimary'))
            return $className::getNameByPrimary($primary, $langId, $siteId);

        return null;
    }

    /**
     * @param $locationId
     * @return string|null
     *
     */
    public static function getZipById($locationId): ?string
    {
        $className = self::getClassName();

        if ($className && method_exists($className, 'getZipById'))
            return $className::getZipById($locationId);

        return null;
    }

    /**
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return array
     */
    public static function getFavoritesList($langId = LANGUAGE_ID, $siteId = SITE_ID): array
    {
        $className = self::getClassName();

        if ($className && method_exists($className, 'getFavoritesList'))
            return $className::getFavoritesList($langId, $siteId);

        return [];
    }

    /**
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return null|array
     */
    public static function getDefault($langId = LANGUAGE_ID, $siteId = SITE_ID): ?array
    {
         $className = self::getClassName();

        if ($className && method_exists($className, 'getDefault'))
            return $className::getDefault($langId, $siteId);

        return null;
    }

    /**
     * @param mixed|string       $langId
     * @param false|mixed|string $siteId
     * @return array
     */
    public static function getList($langId = LANGUAGE_ID, $siteId = SITE_ID): array
    {
        $className = self::getClassName();

        if ($className && method_exists($className, 'getList'))
            return $className::getList($langId, $siteId);

        return [];
    }

    /**
     * @param              $q
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return array
     */
    public static function find($q, $langId = LANGUAGE_ID, $siteId = SITE_ID): array
    {
        $className = self::getClassName();

        if ($className && method_exists($className, 'find'))
        {
            $locations = $className::find($q, $langId, $siteId);

            foreach ($locations as &$location)
                $location['NAME'] = htmlspecialcharsEx(preg_replace('#(' . $q .')#is', '<b>$1</b>', $location['NAME']));

            return $locations;
        }

        return [];
    }

    /**
     * @param mixed|string       $langId
     * @param false|mixed|string $siteId
     * @return array
     */
    public static function getCitiesList($langId = LANGUAGE_ID, $siteId = SITE_ID): array
    {
        $className = self::getClassName();

        if ($className && method_exists($className, 'getCitiesList'))
            return $className::getCitiesList($langId, $siteId);

        return self::getList($langId);
    }

    /**
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return bool
     */
    public static function hasLocations($langId = LANGUAGE_ID, $siteId = SITE_ID): bool
    {
        return (bool)self::getList($langId, $siteId);
    }

    /**
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return bool
     */
    public static function hasFavorites($langId = LANGUAGE_ID, $siteId = SITE_ID): bool
    {
        return (bool)self::getFavoritesList($langId, $siteId);
    }
}