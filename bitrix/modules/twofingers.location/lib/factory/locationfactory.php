<?php

namespace TwoFingers\Location\Factory;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Service\GeoIp\Manager;
use Bitrix\Main\Text\Encoding;
use TwoFingers\Location\Entity\Location;
use TwoFingers\Location\Helper\Ip;
use Twofingers\Location\Internal\Collection;
use Twofingers\Location\Internal\LocationCollection;
use TwoFingers\Location\Model\Location as LocationModel;
use TwoFingers\Location\Options;
use TwoFingers\Location\Service\SxGeo;
use TwoFingers\Location\Storage;

class LocationFactory
{
    /**
     * @param Location $location
     * @param string|null $parentTypeCode
     * @return Location|null
     * @throws ArgumentNullException
     */
    public static function buildParent(Location $location, string $parentTypeCode = null): ?Location
    {
        if (!$location->hasParent()) {
            return null;
        }

        if (!isset($parentTypeCode)) {
            return self::buildById($location->getParentId(), $location->getSiteId(), $location->getLangId());
        }

        $parentData = LocationModel::getParent($location->getCode(), $parentTypeCode, $location->getLangId(),
            $location->getSiteId());

        return isset($parentData) ? self::buildByData($parentData) : null;
    }

    /**
     * @param string|null $siteId
     * @param string $langId
     * @return Location|null
     * @throws ArgumentNullException
     */
    public static function buildByStorage(string $siteId = null, string $langId = LANGUAGE_ID): ?Location
    {
        // by code
        if (null !== $code = Storage::getCityCode()) {
            $location = self::buildByCode($code, $siteId, $langId);
        }

        // by name
        if (!isset($location) && (null !== $name = Storage::getCityName())) {
            foreach (
                [
                    Location::TYPE_VILLAGE, Location::TYPE_CITY,
                    Location::TYPE_REGION, Location::TYPE_COUNTRY
                ] as $typeCode
            ) {
                $location = self::buildByName($name, [$typeCode], null, $siteId, $langId);
                if ($location) {
                    break;
                }
            }
        }

        // by id
        if (!isset($location) && ($id = intval(Storage::getCityId()))) {
            $location = self::buildById($id, $siteId, $langId);
        }

        return $location ?? null;
    }

    /**
     * @param array $data
     * @return Location
     */
    public static function buildByData(array $data): Location
    {
        return new Location(new Collection($data));
    }

    /**
     * @param string $code
     * @param string|null $siteId
     * @param string $langId
     * @return Location
     * @throws ArgumentNullException
     */
    public static function buildByCode(string $code, string $siteId = null, string $langId = LANGUAGE_ID): ?Location
    {
        $code = trim($code);
        if (!strlen($code)) {
            throw new ArgumentNullException('code');
        }

        $data = LocationModel::getByCode($code, $langId, $siteId);

        return $data ? self::buildByData($data) : null;
    }

    /**
     * @param int $id
     * @param string|null $siteId
     * @param string $langId
     * @return Location
     * @throws ArgumentNullException
     */
    public static function buildById(int $id, string $siteId = null, string $langId = LANGUAGE_ID): ?Location
    {
        if (!$id) {
            throw new ArgumentNullException('id');
        }

        $data = LocationModel::getById($id, $langId, $siteId);

        return $data ? self::buildByData($data) : null;
    }

    /**
     * @param string $name
     * @param array|null $typeCodes
     * @param Location|null $parent
     * @param string|null $siteId
     * @param string $langId
     * @return Location|null
     * @throws ArgumentNullException
     */
    public static function buildByName(
        string   $name,
        array    $typeCodes = null,
        Location $parent = null,
        string   $siteId = null,
        string   $langId = LANGUAGE_ID
    ): ?Location {
        $name = trim($name);
        if (!strlen($name)) {
            throw new ArgumentNullException('name');
        }

        $siteLocationData = LocationModel::getByName($name, $langId, $typeCodes, $parent ? $parent->getId() : null,
            $siteId);

        return $siteLocationData ? self::buildByData($siteLocationData) : null;
    }

    /**
     * @param string $siteId
     * @param string $langId
     * @return Location|null
     */
    public static function buildDefault(string $siteId = SITE_ID, string $langId = LANGUAGE_ID): ?Location
    {
        $default = LocationModel::getDefault($siteId, $langId);

        return isset($default) ? self::buildByData($default) : null;
    }

    /**
     * @param string|null $siteId
     * @param string $langId
     * @return Location|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function buildCurrent(string $siteId = null, string $langId = LANGUAGE_ID): ?Location
    {
        return self::buildByIp(Manager::getRealIp(), $siteId, $langId);
    }

    /**
     * @param string $ip
     * @param string|null $siteId
     * @param string $langId
     * @return Location|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function buildByIp(string $ip, string $siteId = null, string $langId = LANGUAGE_ID): ?Location
    {
        $ip = trim($ip);
        if (!Ip::isValid($ip)) {
            throw new ArgumentOutOfRangeException('ip');
        }

        $map = [
            'country' => [Location::TYPE_COUNTRY],
            'region'  => [Location::TYPE_REGION],
            'city'    => [Location::TYPE_CITY, Location::TYPE_VILLAGE],
        ];

        $geoData = SxGeo::getLocation($ip);
        if (!is_array($geoData)) {
            return null;
        }

        $parent = $lastSuccessLocation = null;
        foreach ($map as $sxGeoType => $bxType) {
            if (!isset($geoData[$sxGeoType]['id'])) {
                continue;
            }

            $location = self::buildBySxGeoData($geoData[$sxGeoType], $bxType, $parent, $siteId, $langId);

            if ($location) {
                $parent = $lastSuccessLocation = $location;
            } else {
                $parent = null;
            }
        }

        if (!$lastSuccessLocation && Options::isCapabilityMode()) {
            $lastSuccessLocation = self::buildBySxGeoDataCapability($geoData, $langId);
        }

        return $lastSuccessLocation ?? null;
    }

    /**
     * @param array $geoData
     * @param string $langId
     * @return Location|null
     */
    protected static function buildBySxGeoDataCapability(array $geoData, string $langId = LANGUAGE_ID): ?Location
    {
        if (!isset($geoData['country']['name_' . $langId])) {
            return null;
        }

        $countryData = [
            Location::NAME      => $geoData['country']['name_' . $langId],
            Location::LONGITUDE => $geoData['country']['lon'] ?? '',
            Location::LATITUDE  => $geoData['country']['lat'] ?? '',
            Location::ID        => $geoData['country']['id'],
        ];

        $country = self::buildByData($countryData);
        if (!isset($geoData['region']['name_' . $langId])) {
            return $country;
        }

        $regionData = [
            Location::NAME      => $geoData['region']['name_' . $langId],
            Location::LONGITUDE => $geoData['region']['lon'] ?? '',
            Location::LATITUDE  => $geoData['region']['lat'] ?? '',
            Location::ID        => $geoData['region']['id'],
        ];

        $region = self::buildByData($regionData);
        $region->setParent($country);


        if (!isset($geoData['city']['name_' . $langId])) {
            return $region;
        }

        $data = [
            Location::NAME      => $geoData['city']['name_' . $langId],
            Location::LONGITUDE => $geoData['city']['lon'] ?? '',
            Location::LATITUDE  => $geoData['city']['lat'] ?? '',
            Location::ID        => $geoData['city']['id'],
        ];

        $location = self::buildByData($data);
        $location->setParent($region);

        return $location;
    }

    /**
     * @param array $geoData
     * @param array|null $typeCode
     * @param Location|null $parent
     * @param string|null $siteId
     * @param string $langId
     * @return Location|null
     * @throws ArgumentNullException
     */
    public static function buildBySxGeoData(
        array    $geoData,
        array    $typeCode = null,
        Location $parent = null,
        string   $siteId = null,
        string   $langId = LANGUAGE_ID
    ): ?Location {
        if (empty($geoData['id'])) {
            return null;
        }

        $name     = $geoData['name_' . mb_strtolower($langId)] ?? $geoData['name_ru'];
        $name     = Encoding::convertEncoding($name, 'UTF-8', LANG_CHARSET);
        $location = self::buildByName($name, $typeCode, $parent, $siteId, $langId);

        if (!$location) {
            return null;
        }

        if (isset($geoData['lat'])) {
            $location->setLatitude($geoData['lat']);
        }

        if (isset($geoData['lon'])) {
            $location->setLongitude($geoData['lon']);
        }

        return $location;
    }

    /**
     * @param Location $location
     * @return LocationCollection
     * @throws ArgumentNullException
     */
    public static function buildParentsCollection(Location $location): LocationCollection
    {
        $collection = [];

        if ($location->hasParent()) {
            $parent = clone $location;
            do {
                $parent = self::buildById($parent->getParentId(), $parent->getSiteId(),
                    $parent->getLangId());
                if (isset($parent)) {
                    $collection[] = $parent;
                }
            } while (isset($parent) && $parent->hasParent());
        }

        return new LocationCollection($collection);
    }

    /**
     * @param string $siteId
     * @param string $langId
     * @return LocationCollection
     */
    public static function buildFavoritesCollection(string $siteId = SITE_ID, string $langId = LANGUAGE_ID): LocationCollection
    {
        $locationsData = LocationModel::getFavoritesList($siteId, $langId);
        $collection    = [];
        foreach ($locationsData as $locationData) {
            $collection[] = self::buildByData($locationData);
        }

        return new LocationCollection($collection);
    }

    /**
     * @param array $data
     * @return LocationCollection
     */
    public static function buildCollection(array $data): LocationCollection
    {
        $result = [];
        foreach ($data as $locationData) {
            $result[] = self::buildByData($locationData);
        }

        return new LocationCollection($result);
    }
}