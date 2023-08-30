<?php

namespace TwoFingers\Location\Factory;

use Bitrix\Main\ArgumentNullException;
use TwoFingers\Location\Entity\Location;
use Twofingers\Location\Internal\LocationCollection;

/**
 * @deprecated
 */
class LocationCollectionFactory
{
    /**
     * @param Location $location
     * @return LocationCollection
     * @throws ArgumentNullException
     * @deprecated
     */
    public static function buildParentsCollection(Location $location): LocationCollection
    {
        return LocationFactory::buildParentsCollection($location);
    }

    /**
     * @param string $langId
     * @param string $siteId
     * @return LocationCollection
     * @deprecated
     */
    public static function buildFavoritesCollection(string $langId, string $siteId): LocationCollection
    {
       return LocationFactory::buildFavoritesCollection($langId, $siteId);
    }

    /**
     * @param array $data
     * @return LocationCollection
     * @deprecated
     */
    public static function buildCollection(array $data): LocationCollection
    {
       return LocationFactory::buildCollection($data);
    }
}