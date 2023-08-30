<?php

namespace TwoFingers\Location\Factory;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\LoaderException;
use TwoFingers\Location\Entity\Content as ContentEntity;
use TwoFingers\Location\Entity\Location;
use Twofingers\Location\Internal\Collection;
use TwoFingers\Location\Model\Iblock\Content as ContentIblock;
use TwoFingers\Location\Model\Location as LocationModel;

class ContentFactory
{
    /**
     * @param array $data
     * @return ContentEntity
     */
    public static function buildByData(array $data = []): ContentEntity
    {
        return new ContentEntity(new Collection($data));
    }

    /**
     * @param Location $location
     * @return ContentEntity|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     */
    public static function buildByLocation(Location $location): ?ContentEntity
    {
        if (LocationModel::getType() == LocationModel::TYPE_SALE) {
            $data = ContentIblock::getByLocationCode($location->getCode(), $location->getSiteId());
        } else {
            $data = ContentIblock::getByLocationId($location->getId(), $location->getSiteId());
        }

        return $data ? self::buildByData($data) : null;
    }

    /**
     * @param Location $location
     * @return ContentEntity|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     */
    public static function buildByFirstSuitableParent(Location $location): ?ContentEntity
    {
        $cloned  = clone $location;
        $content = null;
        $i = 0;
        while (isset($cloned) && $cloned->hasParent() && ++$i < 10) {
            $cloned = LocationFactory::buildById($cloned->getParentId(), $cloned->getSiteId(),
                $cloned->getLangId());

            if ($cloned) {
                $content = self::buildByLocation($cloned);

                if ($content) {
                    break;
                }
            }

            // error
            if ($cloned->getId() == $cloned->getParentId()){
                break;
            }
        }

        return $content;
    }

    /**
     * @param string $langId
     * @param string $siteId
     * @return ContentEntity|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     */
    public static function buildByDefaultLocation(string $langId, string $siteId): ?ContentEntity
    {
        $defaultLocation = LocationFactory::buildDefault($siteId, $langId);

        return $defaultLocation ? self::buildByLocation($defaultLocation) : null;
    }

    /**
     * @return ContentEntity|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     */
    public static function buildDefault(): ?ContentEntity
    {
        $data = ContentIblock::getDefaultData();

        return $data ? self::buildByData($data) : null;
    }

    /**
     * @param Location $location
     * @return ContentEntity|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     */
    public static function buildByFirstSuitableLocation(Location $location): ?ContentEntity
    {
        // by location
        if ($content = self::buildByLocation($location)) {
            return $content;
        }

        // by parents
        if ($content = self::buildByFirstSuitableParent($location)) {
            return $content;
        }

        // by site default location
        if ($content = self::buildByDefaultLocation($location->getLangId(), $location->getSiteId())) {
            return $content;
        }

        if ($content = self::buildDefault()) {
            return $content;
        }

        return null;
    }
}