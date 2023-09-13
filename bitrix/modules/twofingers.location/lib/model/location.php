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
use Exception;
use TwoFingers\Location\Entity\Location as LocationEntity;
use TwoFingers\Location\Options;
use TwoFingersLocation;

/**
 * Class Location
 *
 * @package TwoFingers\Location
 *
 */
class Location
{
    public const CACHE_TTL   = 360000;

    public const TYPE_SALE   = 'sale2';
    public const TYPE_IBLOCK = 'internal';

    public const SOURCE_SALE           = 'sale';
    public const SOURCE_IBLOCK_SECTION = 'iblock_section';
    public const SOURCE_IBLOCK_ELEMENT = 'iblock_element';

    /** @deprecated */
    public const TYPE__INTERNAL = 'internal';
    /** @deprecated */
    public const TYPE__SALE_2 = 'sale2';

    /**
     * @return string
     */
    public static function getType(): string
    {
        $locationsType = Options::getLocationsType();
        if (in_array($locationsType, [self::TYPE_SALE, self::TYPE_IBLOCK])) {
            return $locationsType;
        }

        try {
            return Loader::includeModule('sale') ? self::TYPE_SALE : self::TYPE_IBLOCK;
        } catch (Exception $e) {
            TwoFingersLocation::handleException($e);

            return self::TYPE_IBLOCK;
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
     * @param string $cityName
     * @param string $langId
     * @param string|null $siteId
     * @return null|int|string
     */
    public static function getIdByName(string $cityName, string $langId, string $siteId = null)
    {
        $className = self::getClassName();

        if ($className && method_exists($className, 'getIdByName')) {
            return $className::getIdByName($cityName, $langId, $siteId);
        }

        return null;
    }


    /**
     * @param string $code
     * @param string $langId
     * @param string|null $siteId
     * @return null|int|string
     */
    public static function getByCode(string $code, string $langId, string $siteId = null): ?array
    {
        $className = self::getClassName();

        if ($className && method_exists($className, 'getByCode')) {
            return $className::getByCode($code, $langId, $siteId);
        }

        return null;
    }

    /**
     * @param int $id
     * @param string $langId
     * @param string|null $siteId
     * @return null|int|string
     */
    public static function getById(int $id, string $langId, string $siteId = null): ?array
    {
        $className = self::getClassName();

        if ($className && method_exists($className, 'getById')) {
            return $className::getById($id, $langId, $siteId);
        }

        return null;
    }


    /**
     * @param string $name
     * @param string $langId
     * @param string|array|null $typesCodes
     * @param int|null $parentId
     * @param string|null $siteId
     * @return void|null
     */
    public static function getByName(
        string $name,
        string $langId,
        array  $typesCodes = null,
        int    $parentId = null,
        string $siteId = null
    ): ?array {
        $className = self::getClassName();

        if ($className && method_exists($className, 'getByName')) {
            return $className::getByName($name, $langId, $typesCodes, $parentId, $siteId);
        }

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

        if ($className && method_exists($className, 'getZipById')) {
            return $className::getZipById($locationId);
        }

        return null;
    }

    /**
     * @param string $siteId
     * @param string|mixed $langId
     * @return array
     */
    public static function getFavoritesList(string $siteId, string $langId = LANGUAGE_ID): ?array
    {
        $className = self::getClassName();

        if ($className && method_exists($className, 'getFavoritesList')) {
            return $className::getFavoritesList($siteId, $langId);
        }

        return [];
    }

    /**
     * @param string $siteId
     * @param string|mixed $langId
     * @return null|array
     */
    public static function getDefault(string $siteId, string $langId = LANGUAGE_ID): ?array
    {
        $className = self::getClassName();

        if ($className && method_exists($className, 'getDefault')) {
            return $className::getDefault($siteId, $langId);
        }

        return null;
    }

    /**
     * @param      $primary
     * @param bool $byCode
     * @return array|null
     * @deprecated
     */
    public static function getByPrimary($primary, bool $byCode = false): ?array
    {
        $className = self::getClassName();

        if ($className && method_exists($className, 'getByPrimary')) {
            return $className::getByPrimary($primary);
        }

        return null;
    }

    /**
     * @param string $langId
     * @param string|null $siteId
     * @return array
     */
    public static function getList(string $langId, string $siteId = null): ?array
    {
        $className = self::getClassName();

        if ($className && method_exists($className, 'getList')) {
            return $className::getList($langId, $siteId);
        }

        return null;
    }

    /**
     * @param string $locationCode
     * @param string $parentTypeCode
     * @param string $langId
     * @param string|null $siteId
     * @return array|null
     */
    public static function getParent(
        string $locationCode,
        string $parentTypeCode,
        string $langId,
        string $siteId = null
    ): ?array {
        $className = self::getClassName();

        if ($className && method_exists($className, 'getParent')) {
            return $className::getParent($locationCode, $parentTypeCode, $langId, $siteId);
        }

        return null;
    }

    /**
     * @param              $q
     * @param string $langId
     * @param string|false|mixed $siteId
     * @return array
     */
    public static function find($q, string $langId, string $siteId = SITE_ID): ?array
    {
        $className = self::getClassName();

        if ($className && method_exists($className, 'find')) {
            $locations = $className::find($q, $langId, $siteId);

            foreach ($locations as &$location) {
                $location[LocationEntity::NAME] = htmlspecialcharsEx(preg_replace('#(' . $q . ')#iu',
                    '<b>$1</b>',
                    $location[LocationEntity::NAME]));

                if (Options::isCapabilityMode()) {
                    $location['NAME'] = $location[LocationEntity::NAME];
                }
            }

            return $locations;
        }

        return [];
    }

    /**
     * @param string $langId
     * @param string|null $siteId
     * @return array
     */
    public static function getCitiesList(string $langId, string $siteId = null): ?array
    {
        $className = self::getClassName();

        if ($className && method_exists($className, 'getCitiesList')) {
            return $className::getCitiesList($langId, $siteId);
        }

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
        return (bool)self::getFavoritesList($siteId, $langId);
    }

    /**
     * for capability with old versions
     *
     * @param mixed|string $langId
     * @param false|mixed|string $siteId
     * @return array
     * @deprecated delete in 2023
     */
    public static function getDefaultList($langId = LANGUAGE_ID, $siteId = SITE_ID): array
    {
        return self::getFavoritesList($siteId, $langId);
    }

    /**
     * @param              $cityId
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return string|null
     * @deprecated
     * @remove in 2023
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
     * @deprecated
     */
    public static function getNameByPrimary($primary, $langId = LANGUAGE_ID, $siteId = SITE_ID): ?string
    {
        $className = self::getClassName();

        if ($className && method_exists($className, 'getNameByPrimary')) {
            return $className::getNameByPrimary($primary, $langId, $siteId);
        }

        return null;
    }
}