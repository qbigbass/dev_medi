<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 13.03.2019
 * Time: 12:46
 *
 *
 */

namespace TwoFingers\Location\Model\Iblock;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use TwoFingers\Location\Options;
use \TwoFingers\Location\Entity\Location as LocationEntity;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use TwoFingers\Location\Current;
use TwoFingers\Location\Model\Iblock;
use TwoFingers\Location\Model\Location as LocationModel;

Loc::loadMessages(__FILE__);

/**
 * Class Element
 *
 * @package TwoFingers\Location
 *
 */
class Content extends Iblock
{
    const CODE_DEFAULT              = 'default';
    const CODE                      = 'tf_location_locations';
    const PROPERTY_LOCATION_ID      = 'LOCATION_ID';
    const PROPERTY_SITE_ID          = 'SITE_ID';
    const PROPERTY_DOMAIN           = 'DOMAIN';

    const PROPERTY_H1               = 'H1';
    const PROPERTY_META_TITLE       = 'META_TITLE';
    const PROPERTY_META_DESCRIPTION = 'META_DESCRIPTION';

    const OPTION_IBLOCK_ID      = 'content-iblock-id';

    /**
     * @param Current $current
     * @param null    $siteId
     * @return array|bool|mixed|null
     * @throws ArgumentNullException
     * @throws ArgumentException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     * @deprecated
     */
    public static function getByCurrent(Current $current, $siteId = null)
    {
        if (!$current->isDefined())
            return self::getDefault(LANGUAGE_ID, $siteId);

        $content = self::getByLocationId($current->getLocationId(), $siteId);
        if ($content || !Options::isCapabilityMode())
            return $content;

        $content = self::getByName($current->getLocationName(), $siteId);
        if ($content)
            return $content;

        return self::getDefault(LANGUAGE_ID, $siteId);
    }

    /**
     * @param mixed|string $langId
     * @param null         $siteId
     * @return array|bool|mixed|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     * @deprecated
     */
    public static function getByStorage($siteId = null)
    {
        $content = \TwoFingers\Location\Entity\Content::buildByStorage();

        return $content ? $content->getData() : null;
    }

    /**
     * @return bool|int
     * @throws ArgumentNullException
     * @throws ArgumentException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     *
     */
    public static function build()
    {
        if (!self::createType())
            return false;

        if (!self::createIblock())
            return false;

        if (!self::createLocationRefProperty(100))
            return false;

        if (!self::createSiteProperty(self::PROPERTY_SITE_ID, 200))
            return false;

        if (!self::createElementLinkProperty(self::PROPERTY_DOMAIN, Domain::getId(), 200))
            return false;

        /*if (!self::createStringProperty(self::PROPERTY_H1, 300))
            return false;

        if (!self::createStringProperty(self::PROPERTY_META_TITLE, 400))
            return false;

        if (!self::createStringProperty(self::PROPERTY_META_DESCRIPTION, 500))
            return false;*/

        return self::createDefaultElement();
    }

    /**
     * @param int $sort
     * @return bool|mixed
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    protected static function createLocationRefProperty($sort = 100)
    {
        $property = self::getPropertyByCode(self::PROPERTY_LOCATION_ID);
        if ($property) return true;

        switch (LocationModel::getType())
        {
            case LocationModel::TYPE__INTERNAL:
                return self::createElementLinkProperty(self::PROPERTY_LOCATION_ID, Location::getId(), $sort, 'Y');
            case LocationModel::TYPE__SALE_2:
                return self::createLocationProperty(self::PROPERTY_LOCATION_ID, 100);
            default:
                return self::createStringProperty(self::PROPERTY_LOCATION_ID, 100);
        }
    }

    /**
     * @return bool|int
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     *
     */
    protected static function createDefaultElement()
    {
        $default = self::getDefault();
        if ($default) return true;

        $iblockId = self::getId();
        if (!$iblockId)
            return false;

        $fields = [
            'NAME'      => Loc::getMessage('TFL_IBLOCK_CONTENT_DEFAULT'),
            //'CODE'      => self::CODE_DEFAULT,
            'IBLOCK_ID' => $iblockId
        ];

        return (new \CIBlockElement)->Add($fields);
    }

    /**
     * @return bool
     * @throws ArgumentNullException
     * @throws ArgumentException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     *
     */
    public static function createIblock()
    {
        if (self::getIblock()) return true;

        $id = self::create(self::CODE, Loc::getMessage('TFL_IBLOCK_CONTENT_NAME'), Loc::getMessage('TFL_IBLOCK_CONTENT_DESCRIPTION'));

        if (!$id) return false;

        Option::set('twofingers.location', self::OPTION_IBLOCK_ID, $id);

        return true;
    }

    /**
     * @return array|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     *
     */
    protected static function getIblockLocationProperty()
    {
        return self::getPropertyByCode(self::PROPERTY_LOCATION_ID);
    }

    /**
     * @return array|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     *
     */
    protected static function getIblockSiteProperty()
    {
        return self::getPropertyByCode(self::PROPERTY_SITE_ID);
    }


    /**
     * @return array|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     *
     */
    protected static function getIblockDomainProperty()
    {
        return self::getPropertyByCode(self::PROPERTY_DOMAIN);
    }

    /**
     * @param       $locationId
     * @param false $siteId
     * @param false $reload
     * @return array|false|mixed|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     */
    public static function getByLocationId($locationId, $siteId = false, $reload = false)
    {
        $locationId = trim($locationId);
        if (!$locationId)
            return null;

        $filter = [
            'PROPERTY_' . self::PROPERTY_LOCATION_ID    => $locationId,
            'PROPERTY_' . self::PROPERTY_SITE_ID        => $siteId
        ];

        return self::getByFilter($filter, $reload);
    }

    /**
     * @param       $locationId
     * @param false $siteId
     * @param false $reload
     * @return mixed|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     * @deprecated
     */
    public static function getDomainByLocationId($locationId = null, $siteId = false, $reload = false)
    {
        return empty($locationId)
            ? null
            : LocationEntity::buildByPrimary($locationId, null, LANGUAGE_ID, $siteId)->getDomain();
    }

    /**
     * @param array $filter
     * @param false $reload
     * @return array|false|mixed|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     */
    public static function getByFilter(array $filter, $reload = false)
    {
        $cacheId    = crc32(__METHOD__ . serialize($filter));
        $cache      = Application::getInstance()->getManagedCache();

        if (!$reload && $cache->read(LocationModel::CACHE_TTL, $cacheId))
            return $cache->get($cacheId);

        $iblockId = self::getId();
        if (!$iblockId || !Loader::includeModule('iblock')) return null;

        $filter['IBLOCK_ID']    = $iblockId;
        $filter['ACTIVE']       = 'Y';

        $obElement = \CIBlockElement::GetList([], $filter)->GetNextElement();

        if ($obElement) {
            $element                = $obElement->GetFields();
            $element['PROPERTIES']  = $obElement->GetProperties();
        } else {
            $element = [];
        }

        $cache->set($cacheId, $element);

        return $element;
    }

    /**
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return array|bool|mixed|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     */
    public static function getDefault($langId = LANGUAGE_ID, $siteId = SITE_ID)
    {
        $data       = null;
        $location   = LocationEntity::buildDefault($langId, $siteId);
        if ($location)
        {
            $defaultContent = $location->getContent();
            if ($defaultContent)
                $data = $defaultContent->getData();
        }

        if (!$data && Options::isCapabilityMode())
        {
            $data = self::getByFilter(['CODE' => self::CODE_DEFAULT]);
        }

        return $data;
    }
    
    /**
     * @param      $name
     * @param null $siteId
     * @param bool $reload
     * @return array|mixed|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     * @deprecated
     */
    public static function getByName($name, $siteId = null, $reload = false)
    {
        $name = trim($name);

        if (!strlen($name)) return false;

        $filter = ['=NAME' => $name];

        if (!is_null($siteId))
            $filter['PROPERTY_' . self::PROPERTY_SITE_ID] = $siteId;

        return self::getByFilter($filter, $reload);
    }
}