<?php

namespace TwoFingers\Location\Model\Sale;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Sale\Location\LocationTable;
use TwoFingers\Location\Entity\Location as LocationEntity;
use TwoFingers\Location\Model\Location as LocationModel;
use TwoFingers\Location\Options;

class Location
{
    /**
     * @param array $query
     * @param null $siteId
     * @return array|false|mixed|null
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getList(array $query = [], $siteId = null)
    {
        if (!Loader::IncludeModule('sale')) {
            return null;
        }

        $cacheId = crc32(__METHOD__ . serialize($query) . $siteId);
        $cache   = Application::getInstance()->getManagedCache();

        if ($cache->read(LocationModel::CACHE_TTL, $cacheId)) {
            return $cache->get($cacheId);
        }

        if (isset($langId)) {
            $query['filter']['=NAME.LANGUAGE_ID'] = $langId;
        }

        if (!isset($query['select'])) {
            $query['select'] = [];
        }

        $query['select'] = array_merge($query['select'], [
            'ID',
            'CODE',
            'LEFT_MARGIN',
            'RIGHT_MARGIN',
            'LNAME'     => 'NAME.NAME',
            //'PARENT_CODE' => 'PARENT.CODE',
            'PARENT_ID',
            //'TYPE_ID',
            'LATITUDE',
            'LONGITUDE',
            'TYPE_CODE' => 'TYPE.CODE'
        ]);

        if (!isset($query['order'])) {
            $query['order'] = ['LNAME' => 'asc'];
        }

        if (!isset($query['limit'])) {
            $query['limit'] = Options::getLocationsLimit();
        }

        $dbResult  = LocationTable::getList($query);
        $locations = [];

        while ($item = $dbResult->fetch()) {
            $locations[$item['ID']] = [
                LocationEntity::NAME      => htmlspecialcharsEx($item['LNAME']),
                LocationEntity::ID        => $item['ID'],
                LocationEntity::CODE      => $item['CODE'],
                LocationEntity::PARENT_ID => $item['PARENT_ID'],
                // LocationEntity::PARENT_CODE => $item['PARENT_CODE'],
                LocationEntity::TYPE      => $item['TYPE_CODE'],
                LocationEntity::LATITUDE  => $item['LATITUDE'],
                LocationEntity::LONGITUDE => $item['LONGITUDE'],
                LocationEntity::LANG_ID   => $query['filter']['=NAME.LANGUAGE_ID'] ?? LANGUAGE_ID,
                LocationEntity::SITE_ID   => $siteId,
                LocationEntity::SOURCE    => LocationModel::SOURCE_SALE,
            ];
        }

        $cache->set($cacheId, $locations);

        return $locations;
    }

    /**
     * @param string $locationCode
     * @param string $parentTypeCode
     * @param string $langId
     * @param string|null $siteId
     * @return array|null
     * @throws ArgumentException
     * @throws ArgumentNullException
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
        $locationCode = trim($locationCode);
        if (!strlen($locationCode)) {
            throw new ArgumentNullException('locationCode');
        }

        $parentTypeCode = trim($parentTypeCode);
        if (!strlen($parentTypeCode)) {
            throw new ArgumentNullException('parentTypeCode');
        }

        if (!SiteLocation::isCodeAllowsToSite($locationCode, $siteId)) {
            return null;
        }

        $cacheId = crc32(__METHOD__ . $locationCode . $parentTypeCode . $langId . $siteId);
        $cache   = Application::getInstance()->getManagedCache();

        if ($cache->read(LocationModel::CACHE_TTL, $cacheId)) {
            return $cache->get($cacheId) ?: null;
        }

        $filter = [
            '=CODE'              => $locationCode,
            '=PARENTS.TYPE.CODE' => $parentTypeCode
        ];

        if ($langId) {
            $filter['=PARENTS.NAME.LANGUAGE_ID']      = $langId;
            $filter['=PARENTS.TYPE.NAME.LANGUAGE_ID'] = $langId;
        }

        $query = [
            'filter' => $filter,
            'select' => [
                'I_ID'        => 'PARENTS.ID',
                'I_CODE'      => 'PARENTS.CODE',
                'I_NAME'      => 'PARENTS.NAME.NAME',
                'I_TYPE'      => 'PARENTS.TYPE.CODE',
                'I_PARENT_ID' => 'PARENTS.PARENT_ID',
                //  'I_PARENT_CODE' => 'PARENTS.PARENT.CODE',
                'I_TYPE_CODE' => 'PARENTS.TYPE.CODE',
                'I_LATITUDE'  => 'PARENTS.LATITUDE',
                'I_LONGITUDE' => 'PARENTS.LONGITUDE',
                //  'I_TYPE_NAME_RU' => 'PARENTS.TYPE.NAME.NAME'
            ],
            'order'  => [
                'PARENTS.DEPTH_LEVEL' => 'asc'
            ]
        ];

        $item = LocationTable::getRow($query);
        if (!$item) {
            return null;
        }

        $data = [
            LocationEntity::NAME      => htmlspecialcharsEx($item['I_NAME']),
            LocationEntity::ID        => $item['I_ID'],
            LocationEntity::CODE      => $item['I_CODE'],
            LocationEntity::PARENT_ID => $item['I_PARENT_ID'],
            // LocationEntity::PARENT_CODE => $item['I_PARENT_CODE'],
            LocationEntity::TYPE      => $item['I_TYPE_CODE'],
            LocationEntity::LATITUDE  => $item['I_LATITUDE'],
            LocationEntity::LONGITUDE => $item['I_LONGITUDE'],
            LocationEntity::LANG_ID   => $langId,
            LocationEntity::SITE_ID   => $siteId,
            LocationEntity::SOURCE    => LocationModel::SOURCE_SALE,
        ];

        $cache->set($cacheId, $data);

        return $data;
    }
}