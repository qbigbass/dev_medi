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
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use CIBlockElement;
use TwoFingers\Location\Model\Iblock;
use TwoFingers\Location\Model\Location as LocationModel;
use Twofingers\Location\Property\PriceType;
use Twofingers\Location\Property\Store;

Loc::loadMessages(__FILE__);

/**
 * Class Element
 *
 * @package TwoFingers\Location
 *
 */
class Content extends Iblock
{
    const CODE_DEFAULT         = 'default';
    const CODE                 = 'tf_location_locations';
    const PROPERTY_LOCATION_ID = 'LOCATION_ID';
    const PROPERTY_SITE_ID     = 'SITE_ID';
    const PROPERTY_DOMAIN      = 'DOMAIN';
    const PROPERTY_PHONE       = 'PHONE';
    const PROPERTY_ADDRESS     = 'ADDRESS';
    const PROPERTY_PRICE_TYPES = 'PRICE_TYPES';
    const PROPERTY_STORES      = 'STORES';

    /** @deprecated */
    const PROPERTY_H1 = 'H1';
    /** @deprecated */
    const PROPERTY_META_TITLE = 'META_TITLE';
    /** @deprecated */
    const PROPERTY_META_DESCRIPTION = 'META_DESCRIPTION';

    const OPTION_IBLOCK_ID = 'content-iblock-id';

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
        if (!self::createType()) {
            return false;
        }

        if (!self::createIblock()) {
            return false;
        }

        if (!self::createLocationRefProperty()) {
            return false;
        }

        if (!self::createSiteProperty(self::PROPERTY_SITE_ID, 200)) {
            return false;
        }

        if (!self::createElementLinkProperty(self::PROPERTY_DOMAIN, Domain::getId(), 300)) {
            return false;
        }

        if (!self::createStringProperty(self::PROPERTY_ADDRESS, 400)) {
            return false;
        }

        if (!self::createStringProperty(self::PROPERTY_PHONE, 500, 'N', 'Y')) {
            return false;
        }

        if (Loader::includeModule('sale')) {
            if (!self::createStringProperty(self::PROPERTY_PRICE_TYPES, 600, 'N', 'Y', PriceType::USER_TYPE)) {
                return false;
            }
            if (!self::createStringProperty(self::PROPERTY_STORES, 700, 'N', 'Y', Store::USER_TYPE)) {
                return false;
            }
        }

        return self::createDefaultElement();
    }

    /**
     * @param int $sort
     * @return bool
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     */
    protected static function createLocationRefProperty(int $sort = 100): bool
    {
        $property = self::getPropertyByCode(self::PROPERTY_LOCATION_ID);
        if ($property) {
            return true;
        }

        switch (LocationModel::getType()) {
            case LocationModel::TYPE_IBLOCK:
                return self::createElementLinkProperty(self::PROPERTY_LOCATION_ID, Location::getId(), $sort, 'Y');
            case LocationModel::TYPE_SALE:
                return self::createLocationProperty(self::PROPERTY_LOCATION_ID);
            default:
                return self::createStringProperty(self::PROPERTY_LOCATION_ID);
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
        $defaultData = self::getDefaultData();
        if ($defaultData) {
            return true;
        }

        $iblockId = self::getId();
        if (!$iblockId) {
            return false;
        }

        $fields = [
            'NAME'      => Loc::getMessage('TFL_IBLOCK_CONTENT_DEFAULT'),
            'CODE'      => self::CODE_DEFAULT,
            'IBLOCK_ID' => $iblockId
        ];

        return (new CIBlockElement)->Add($fields);
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
    public static function createIblock(): bool
    {
        if (self::getIblock()) {
            return true;
        }

        $id = self::create(self::CODE, Loc::getMessage('TFL_IBLOCK_CONTENT_NAME'),
            Loc::getMessage('TFL_IBLOCK_CONTENT_DESCRIPTION'));

        if (!$id) {
            return false;
        }

        Option::set('twofingers.location', self::OPTION_IBLOCK_ID, $id);

        return true;
    }

    /**
     * @return array|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException|LoaderException
     *
     */
    protected static function getIblockLocationProperty(): ?array
    {
        return self::getPropertyByCode(self::PROPERTY_LOCATION_ID);
    }

    /**
     * @return array|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException|LoaderException
     *
     */
    protected static function getIblockSiteProperty(): ?array
    {
        return self::getPropertyByCode(self::PROPERTY_SITE_ID);
    }

    /**
     * @return array|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     *
     */
    protected static function getIblockDomainProperty(): ?array
    {
        return self::getPropertyByCode(self::PROPERTY_DOMAIN);
    }

    /**
     * @param int $locationId
     * @param string|null $siteId
     * @return array|false|mixed|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     */
    public static function getByLocationId(int $locationId, string $siteId = null)
    {
        if (!$locationId) {
            return null;
        }

        $filter = [
            'PROPERTY_' . self::PROPERTY_LOCATION_ID => $locationId,
            'PROPERTY_' . self::PROPERTY_SITE_ID     => [$siteId, false]
        ];

        return self::getByFilter($filter);
    }

    /**
     * @param string $locationCode
     * @param string|null $siteId
     * @return array|false|mixed|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     */
    public static function getByLocationCode(string $locationCode, string $siteId = null)
    {
        $locationCode = trim($locationCode);
        if (!strlen($locationCode)) {
            return null;
        }

        $filter = [
            'PROPERTY_' . self::PROPERTY_LOCATION_ID => $locationCode,
            'PROPERTY_' . self::PROPERTY_SITE_ID     => [$siteId, false]
        ];

        return self::getByFilter($filter);
    }

    /**
     * @param array $filter
     * @param false $reload
     * @return array|false|mixed|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     */
    public static function getByFilter(array $filter, bool $reload = false)
    {
        $cacheId = crc32(__METHOD__ . serialize($filter));
        $cache   = Application::getInstance()
            ->getManagedCache();

        if (!$reload && $cache->read(LocationModel::CACHE_TTL, $cacheId)) {
            return $cache->get($cacheId);
        }

        $iblockId = self::getId();
        if (!$iblockId || !Loader::includeModule('iblock')) {
            return null;
        }

        $filter['IBLOCK_ID'] = $iblockId;
        $filter['ACTIVE']    = 'Y';

        $obElement = CIBlockElement::GetList([], $filter)
            ->GetNextElement();

        if ($obElement) {
            $element               = $obElement->GetFields();
            $element['PROPERTIES'] = $obElement->GetProperties();
        } else {
            $element = [];
        }

        $cache->set($cacheId, $element);

        return $element;
    }

    /**
     * @return array|bool|mixed|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     */
    public static function getDefaultData()
    {
        return self::getByFilter(['CODE' => self::CODE_DEFAULT]) ?? null;
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
    public static function getByName($name, $siteId = null, bool $reload = false)
    {
        $name = trim($name);

        if (!strlen($name)) {
            return false;
        }

        $filter = ['=NAME' => $name];

        if (!is_null($siteId)) {
            $filter['PROPERTY_' . self::PROPERTY_SITE_ID] = $siteId;
        }

        return self::getByFilter($filter, $reload);
    }
}