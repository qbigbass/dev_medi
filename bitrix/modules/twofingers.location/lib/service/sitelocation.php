<?php

namespace TwoFingers\Location\Service;

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Sale\Location\Connector;
use Bitrix\Sale\Location\EO_GroupLocation_Result;
use Bitrix\Sale\Location\EO_SiteLocation_Result;
use Bitrix\Sale\Location\GroupLocationTable;
use Bitrix\Sale\Location\LocationTable;
use Bitrix\Sale\Location\SiteLocationTable;
use TwoFingers\Location\Model\Location;

class SiteLocation
{
    /**
     * @param false|mixed|string $siteId
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\SystemException|Main\LoaderException
     */
    public static function getCodesBySiteId($siteId): array
    {
        $siteId = trim($siteId);
        if (!strlen($siteId)) {
            throw new Main\ArgumentNullException('siteId');
        }

        if (!Loader::includeModule('sale')) {
            throw new Main\LoaderException('sale');
        }

        $result = array_merge(
            self::getSiteLocationsCodes($siteId),
            self::getSiteGroupLocationsCodes($siteId)
        );

        return array_unique($result);
    }

    /**
     * @param $siteId
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    protected static function getSiteGroupLocationsCodes($siteId): array
    {
        $groupsIds = [];
        $groups    = self::getLocationsIteratorBySiteId($siteId, Connector::DB_GROUP_FLAG);
        while ($group = $groups->fetch()) {
            $groupsIds[] = $group;
        }

        if (empty($groupsIds))
            return [];

        $groupLocations = self::getLocationsIteratorByGroupsIds($groupsIds);
        $result         = [];

        while ($groupLocation = $groupLocations->fetch()) {
            $result = array_merge($result, self::getLocationsCodesByParentLocationData($groupLocation));
        }

        return $result;
    }

    /**
     * @param $siteId
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    protected static function getSiteLocationsCodes($siteId): array
    {
        $locations = self::getLocationsIteratorBySiteId($siteId, Connector::DB_LOCATION_FLAG);
        $result    = [];

        while ($location = $locations->fetch()) {
            $result = array_merge($result, self::getLocationsCodesByParentLocationData($location));
        }

        return $result;
    }


    /**
     * @param array $groupId
     * @return Main\ORM\Query\Result|EO_GroupLocation_Result
     * @throws Main\ArgumentException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    protected static function getLocationsIteratorByGroupsIds(array $groupId)
    {
        if (empty($groupId)) {
            throw new Main\ArgumentNullException('groupId');
        }

        return GroupLocationTable::getList([
            'filter' => ['=LOCATION_GROUP_ID' => $groupId],
            'cache'  => ['ttl' => Location::CACHE_TTL, 'cache_joins' => true],
            'select' => [
                'LOCATION_ID',
                'LOCATION_LEFT_MARGIN'  => 'LOCATION.LEFT_MARGIN',
                'LOCATION_RIGHT_MARGIN' => 'LOCATION.RIGHT_MARGIN',
                'LOCATION_CODE'         => 'LOCATION.CODE',
            ],
        ]);
    }

    /**
     * @param string $siteId
     * @param string $type
     * @return Main\ORM\Query\Result|EO_SiteLocation_Result
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    protected static function getLocationsIteratorBySiteId(string $siteId, string $type = Connector::DB_LOCATION_FLAG)
    {
        $siteId = trim($siteId);
        if (!$siteId) {
            throw new Main\ArgumentNullException('siteId');
        }

        $type = trim($type);
        if (!$type) {
            throw new Main\ArgumentNullException('type');
        }

        $select = [
            'LOCATION_ID'
        ];

        if ($type == Connector::DB_LOCATION_FLAG) {
            $select = array_merge($select, [
                'LOCATION_LEFT_MARGIN'  => 'LOCATION.LEFT_MARGIN',
                'LOCATION_RIGHT_MARGIN' => 'LOCATION.RIGHT_MARGIN',
                'LOCATION_CODE'         => 'LOCATION.CODE',
            ]);
        }

        return SiteLocationTable::getList([
            'filter' => [
                '=SITE_ID'       => $siteId,
                '=LOCATION_TYPE' => $type
            ],
            'cache'  => ['ttl' => Location::CACHE_TTL, 'cache_joins' => true],
            'select' => $select,
        ]);
    }

    /**
     * @param array $location
     * @return array
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     */
    protected static function getLocationsCodesByParentLocationData(array $location): array
    {
        $code = trim($location['LOCATION_CODE'] ?? '');

        if (!$code) {
            throw new Main\ArgumentNullException('code');
        }

        $result[] = $code;

        $res = LocationTable::getList([
            'filter' => [
                '>LEFT_MARGIN'  => $location['LOCATION_LEFT_MARGIN'],
                '<RIGHT_MARGIN' => $location['LOCATION_RIGHT_MARGIN'],
            ],
            'cache'  => ['ttl' => Location::CACHE_TTL],
            'select' => ['CODE']
        ]);

        while ($locParent = $res->fetch()) {
            $result[] = $locParent['CODE'];
        }

        return $result;
    }
}