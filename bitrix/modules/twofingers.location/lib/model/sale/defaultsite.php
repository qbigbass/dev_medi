<?php

namespace TwoFingers\Location\Model\Sale;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Sale\Location\DefaultSiteTable;
use TwoFingers\Location\Entity\Location as LocationEntity;
use TwoFingers\Location\Model\Location;

class DefaultSite
{
    /**
     * @param $langId
     * @param $siteId
     * @return array
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getList($langId = null, $siteId = null): array
    {
        if (!Loader::IncludeModule('sale')) {
            return [];
        }

        $cacheId = crc32(__METHOD__ . $langId . $siteId);
        $cache   = Application::getInstance()->getManagedCache();

        if ($cache->read(Location::CACHE_TTL, $cacheId)) {
            return $cache->get($cacheId);
        }

        $query = [
            'filter' => [
                //    'SITE_ID'                   => $siteId,
                //    'LOCATION.NAME.LANGUAGE_ID' => $langId
            ],
            'order'  => [
                'SORT' => 'asc'
            ],
            'select' => [
                'CODE'      => 'LOCATION.CODE',
                'ID'        => 'LOCATION.ID',
                'NAME'      => 'LOCATION.NAME.NAME',
                //'TYPE_ID'   => 'LOCATION.TYPE_ID',
                'PARENT_ID' => 'LOCATION.PARENT_ID',
                //'PARENT_CODE' => 'LOCATION.PARENT.CODE',
                'LONGITUDE' => 'LOCATION.LONGITUDE',
                'LATITUDE'  => 'LOCATION.LATITUDE',
                'TYPE_CODE' => 'LOCATION.TYPE.CODE',
            ]
        ];

        if (isset($siteId)) {
            $query['filter']['SITE_ID'] = $siteId;
        }

        if (isset($langId)) {
            $query['filter']['LOCATION.NAME.LANGUAGE_ID'] = $langId;
        }

        // default
        $res = DefaultSiteTable::getList($query);

        $defaults = [];
        while ($item = $res->fetch()) {
            $defaults[] = $item;
        }

        $cache->set($cacheId, $defaults);

        return $defaults;
    }

    /**
     * @param array $rawData
     * @param string|null $langId
     * @param string|null $siteId
     * @return array|null
     */
    public static function makeLocationData(array $rawData = [], string $langId = null, string $siteId = null): ?array
    {
        if (!isset($rawData)) {
            return null;
        }

        $result = [];
        foreach ($rawData as $rawLocationData) {
            $result[] = [
                LocationEntity::NAME      => htmlspecialcharsEx($rawLocationData['NAME']),
                LocationEntity::ID        => $rawLocationData['ID'],
                LocationEntity::CODE      => $rawLocationData['CODE'],
                //LocationEntity::TRANSLIT  => Tools::translit($rawLocationData['NAME'], $langId),
                //LocationEntity::PARENT_CODE => $item['PARENT_CODE'],
                LocationEntity::PARENT_ID => $rawLocationData['PARENT_ID'],
                LocationEntity::TYPE      => $rawLocationData['TYPE_CODE'],
                LocationEntity::LATITUDE  => $rawLocationData['LATITUDE'],
                LocationEntity::LONGITUDE => $rawLocationData['LONGITUDE'],
                LocationEntity::LANG_ID   => $langId,
                LocationEntity::SITE_ID   => $siteId,
                LocationEntity::SOURCE    => Location::SOURCE_SALE,
            ];
        }

        return $result;
    }

}