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
use CIBlockSection;
use TwoFingers\Location\Entity\Location as LocationEntity;
use TwoFingers\Location\Model\Iblock\Location as LocationIblock;
use TwoFingers\Location\Model\Location;
use TwoFingers\Location\Options;

class Section
{
    /**
     * @param array $sections
     * @param string $langId
     * @param string|null $siteId
     * @return array|null
     */
    public static function makeLocationsData(
        array  $sections,
        string $langId,
        string $siteId = null
    ): ?array {
        if (!isset($sections)) {
            return null;
        }

        $locations = [];
        foreach ($sections as $section) {
            $locations[] = self::makeLocationData($section, $langId, $siteId);
        }

        return $locations;
    }

    /**
     * @param array $section
     * @param string $langId
     * @param string|null $siteId
     * @return array|null
     */
    public static function makeLocationData(
        array  $section,
        string $langId,
        string $siteId = null
    ): ?array {
        if (!isset($section)) {
            return null;
        }

        return [
            LocationEntity::NAME        => htmlspecialcharsEx($section['NAME']),
            LocationEntity::ID          => $section['ID'],
            LocationEntity::CODE        => $section['CODE'],
            LocationEntity::PARENT_ID   => $section['IBLOCK_SECTION_ID'],
            //LocationEntity::PARENT_CODE => $section['IBLOCK_SECTION_ID'] ? self::getCodeById($section['IBLOCK_SECTION_ID']) : null,
            LocationEntity::TYPE        => $section['IBLOCK_SECTION_ID']
                ? LocationEntity::TYPE_REGION
                : LocationEntity::TYPE_COUNTRY,
            LocationEntity::LATITUDE    => null,
            LocationEntity::LONGITUDE   => null,
            LocationEntity::LANG_ID     => $langId,
            LocationEntity::SITE_ID     => $siteId,
            LocationEntity::SOURCE     => Location::SOURCE_IBLOCK_SECTION,
        ];
    }

    /**
     * @param $filter
     * @return array
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    /*public static function convertElementFilter($filter): ?array
    {
        // featured
        if (isset($filter['filter']['PROPERTY_' . LocationIblock::PROPERTY_FEATURED])
            || isset($filter['filter']['!PROPERTY_' . LocationIblock::PROPERTY_FEATURED])) {
            return null;
        }

        // types
        if (isset($filter['filter']['PROPERTY_' . LocationIblock::PROPERTY_TYPE])) {
            $filterTypesEnumsIds = $filter['filter']['PROPERTY_' . LocationIblock::PROPERTY_TYPE];
            if (!is_array($filterTypesEnumsIds)) {
                $filterTypesEnumsIds = [$filterTypesEnumsIds];
            }

            $enumsToSearch = [];
            foreach ([LocationEntity::TYPE_REGION, LocationEntity::TYPE_COUNTRY] as $typeCode) {
                if (null !== $enumId = LocationIblock::getEnumIdByTypeCode($typeCode)) {
                    $enumsToSearch[] = $enumId;
                }
            }

            if (!$enumsToSearch || !array_intersect($enumsToSearch, $filterTypesEnumsIds)) {
                return null;
            }

            foreach (array_intersect($enumsToSearch, $filterTypesEnumsIds) as $enumId)
            {
                $typeCode = LocationIblock::getTypeCodeByEnumId($enumId);
                switch ($typeCode) {
                    case LocationEntity::TYPE_REGION:
                        $filter['filter']['DEPTH_LEVEL'][] = 2;
                        //  $filter['filter']['!IBLOCK_SECTION_ID'] = false;
                        break;
                    case LocationEntity::TYPE_COUNTRY:
                        $filter['filter']['DEPTH_LEVEL'][] = 1;
                        // $filter['filter']['IBLOCK_SECTION_ID'] = false;
                        break;
                }
            }

            unset($filter['filter']['PROPERTY_' . LocationIblock::PROPERTY_TYPE]);
        }

        return $filter;
    }*/

    /**
     * @return array
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function getDefaultFilter(): array
    {
        return [
            'IBLOCK_ID' => LocationIblock::getId(),
            'ACTIVE'    => 'Y'
        ];
    }

    /**
     * @param string $code
     * @return false|mixed|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getByCode(string $code): ?array
    {
        $filter         = self::getDefaultFilter();
        $filter['CODE'] = $code;

        return self::getList(['filter' => $filter])[0] ?? null;
    }


    /**
     * @param int $id
     * @return false|mixed|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getById(int $id): ?array
    {
        $filter       = self::getDefaultFilter();
        $filter['ID'] = $id;

        return self::getList(['filter' => $filter])[0] ?? null;
    }

    /**
     * @param array $filter
     * @return false|mixed|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     */
    public static function getList(array $filter = [])
    {
        if (!Loader::IncludeModule('iblock')) {
            return null;
        }

        $cacheId = crc32(__METHOD__ . serialize($filter));
        $cache   = Application::getInstance()->getManagedCache();

        if (!$cache->read(Location::CACHE_TTL, $cacheId)) {
            // update type
            $filter['filter'] = self::getDefaultFilter() + $filter['filter'];

            $sectionsDb = CIBlockSection::GetList(
                $filter['order'] ?? ['NAME' => 'ASC'],
                $filter['filter'],
                false,
                $filter['select'] ?? [
                    'ID', 'NAME', 'CODE', 'IBLOCK_SECTION_ID', 'DEPTH_LEVEL'
                ],
                $filter['nav'] ?? ['nTopCount' => Options::getLocationsLimit()]
            );

            $sections = [];
            while ($section = $sectionsDb->Fetch()) {
                $sections[] = $section;
            }

            $cache->set($cacheId, $sections);
        }

        return $cache->get($cacheId);
    }

    /**
     * @param int $id
     * @return int|mixed
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getCodeById(int $id)
    {
        $filter       = self::getDefaultFilter();
        $filter['ID'] = $id;

        $result = self::getList(['filter' => $filter, 'select' => ['CODE']]);

        return $result[0]['CODE'] ?? 0;
    }
}