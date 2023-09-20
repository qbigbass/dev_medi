<?php

namespace TwoFingers\Location\Model\Iblock\Location;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use CIBlockElement;
use TwoFingers\Location\Entity\Location as LocationEntity;
use TwoFingers\Location\Model\Iblock\Location as LocationIblock;
use TwoFingers\Location\Model\Location;
use TwoFingers\Location\Options;

class Element
{
    /**
     * @param array $elements
     * @param string $langId
     * @param string|null $siteId
     * @return array|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function makeLocationsData(
        array  $elements,
        string $langId,
        string $siteId = null
    ): ?array {
        $locations = [];
        foreach ($elements as $element) {
            $locations[] = self::makeLocationData($element, $langId, $siteId);
        }

        return $locations;
    }

    /**
     * @param array $element
     * @param string $langId
     * @param string|null $siteId
     * @return array|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function makeLocationData(
        array  $element,
        string $langId,
        string $siteId = null
    ): ?array {
        if (!isset($element)) {
            return null;
        }

        /*$typeCode = isset($element['PROPERTY_' . LocationIblock::PROPERTY_TYPE . '_ENUM_ID'])
            ? LocationIblock::getTypeCodeByEnumId($element['PROPERTY_' . LocationIblock::PROPERTY_TYPE . '_ENUM_ID'])
            : null;*/

        return [
            LocationEntity::NAME      => htmlspecialcharsEx($element['NAME']),
            LocationEntity::ID        => $element['ID'],
            LocationEntity::CODE      => $element['CODE'],
            LocationEntity::PARENT_ID => $element['IBLOCK_SECTION_ID'],
            //LocationEntity::PARENT_CODE => $element['IBLOCK_SECTION_ID'] ? Section::getCodeById($element['IBLOCK_SECTION_ID']) : null,
            LocationEntity::TYPE      => LocationEntity::TYPE_CITY,//$typeCode ?: null,
            LocationEntity::LATITUDE  => null,//$element['PROPERTY_' . LocationIblock::PROPERTY_LATITUDE . '_VALUE'],
            LocationEntity::LONGITUDE => null,//$element['PROPERTY_' . LocationIblock::PROPERTY_LONGITUDE . '_VALUE'],
            LocationEntity::LANG_ID   => $langId,
            LocationEntity::SITE_ID   => $siteId,
            LocationEntity::SOURCE    => Location::SOURCE_IBLOCK_ELEMENT,
        ];
    }

    /**
     * @param array $filter
     * @return false|mixed|null
     * @throws LoaderException
     */
    public static function getList(array $filter = [])
    {
        if (!Loader::IncludeModule('iblock')) {
            return null;
        }

        $cacheId = crc32(__METHOD__ . serialize($filter));
        $cache   = Application::getInstance()->getManagedCache();

        if (!$cache->read(Location::CACHE_TTL, $cacheId)) {
            $elementsDb = CIBlockElement::GetList(
                $filter['order'] ?? ['NAME' => 'ASC'],
                $filter['filter'],
                false,
                $filter['nav'] ?? ['nTopCount' => Options::getLocationsLimit()],
                $filter['select'] ?? [
                    'ID', 'NAME', 'CODE', 'IBLOCK_SECTION_ID',
                    'PROPERTY_' . LocationIblock::PROPERTY_FEATURED,
                    'PROPERTY_' . LocationIblock::PROPERTY_DEFAULT,
                   // 'PROPERTY_' . LocationIblock::PROPERTY_TYPE,
                   // 'PROPERTY_' . LocationIblock::PROPERTY_ZIP,
                   // 'PROPERTY_' . LocationIblock::PROPERTY_LATITUDE,
                   // 'PROPERTY_' . LocationIblock::PROPERTY_LONGITUDE,
                ]
            );

            $elements = [];

            while ($element = $elementsDb->Fetch()) {
                $elements[] = $element;
            }

            $cache->set($cacheId, $elements);
        }

        return $cache->get($cacheId);
    }

    /**
     * @param $langId
     * @param $siteId
     * @return array|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     */
    public static function getDefaultsData($langId = null, $siteId = null): ?array
    {
        $filter                                                   = self::getDefaultFilter($siteId);
        $filter['!PROPERTY_' . LocationIblock::PROPERTY_FEATURED] = false;

        return self::getList($filter);
    }

    /**
     * @param $siteId
     * @return array
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function getDefaultFilter($siteId = null): array
    {
        $filter = [
            'IBLOCK_ID' => LocationIblock::getId(),
            'ACTIVE'    => 'Y'
        ];

        if (isset($siteId)) {
            $filter['PROPERTY_' . LocationIblock::PROPERTY_SITE_ID] = [$siteId, false];
        } else {
            $filter['PROPERTY_' . LocationIblock::PROPERTY_SITE_ID] = false;
        }

        return $filter;
    }
}