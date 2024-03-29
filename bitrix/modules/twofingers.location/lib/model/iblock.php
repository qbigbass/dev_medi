<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 13.03.2019
 * Time: 12:46
 *
 *
 */

namespace TwoFingers\Location\Model;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use TwoFingers\Location\Helper\Tools;
use TwoFingers\Location\Options;
use Twofingers\Location\Property\Site;

Loc::loadMessages(__FILE__);

/**
 * Class Element
 *
 * @package TwoFingers\Location
 *
 */
class Iblock
{
    const TYPE = 'tf_location';

    /**
     * @return array|false
     */
    public static function getType()
    {
        $filter = ['ID' => self::TYPE];

        return \CIBlockType::GetList([], $filter)->Fetch();
    }

    /**
     * @return bool
     * @throws LoaderException
     */
    public static function createType()
    {
        if (!Loader::includeModule('iblock'))
            return false;

        $element = self::getType();
        if ($element) return true;

        $fields = [
            'ID'        => self::TYPE,
            'SECTIONS'  => 'Y',
            'IN_RSS'    => 'N',
            'SORT'      => 1000,
            'LANG' => [
                'en' => [
                    'NAME'          => Loc::getMessage('TFL_IBLOCK_TYPE_NAME', null, 'en'),
                    'SECTION_NAME'  => Loc::getMessage('TFL_IBLOCK_TYPE_SECTION_NAME', null, 'en'),
                    'ELEMENT_NAME'  => Loc::getMessage('TFL_IBLOCK_TYPE_ELEMENT_NAME', null, 'en')
                ],
                'ru' => [
                    'NAME'          => Loc::getMessage('TFL_IBLOCK_TYPE_NAME', null, 'ru'),
                    'SECTION_NAME'  => Loc::getMessage('TFL_IBLOCK_TYPE_SECTION_NAME', null, 'ru'),
                    'ELEMENT_NAME'  => Loc::getMessage('TFL_IBLOCK_TYPE_ELEMENT_NAME', null, 'ru')
                ]
            ]
        ];

        return (new \CIBlockType)->Add($fields);
    }

    /**
     * @param        $code
     * @param string $name
     * @param string $description
     * @return mixed
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     *
     */
    public static function create($code, $name = '', $description = '')
    {
        $name = trim($name);
        if (!strlen($name))
            $name = Loc::getMessage('TFL_IBLOCK_' . $code . '_NAME');

        $description = trim($description);
        if (!strlen($description))
            $description = Loc::getMessage('TFL_IBLOCK_' . $code . '_DESCRIPTION');

        $data = [
            'ACTIVE'            => 'Y',
            'NAME'              => $name,
            'CODE'              => $code,
            'IBLOCK_TYPE_ID'    => self::TYPE,
            'SITE_ID'           => Tools::getSitesIds(),
            'SORT'              => 50,
            'DESCRIPTION'       => $description,
            "GROUP_ID"          => ["2"=>"R"]
        ];

        return (new \CIBlock)->Add($data);
    }

    /**
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function remove()
    {
        if (Loader::includeModule('iblock') && \CIBlock::Delete(static::getId()))
            Option::set(Options::MODULE_ID, static::OPTION_IBLOCK_ID, null);
    }

    /**
     *
     */
    public static function removeType()
    {
        if (Loader::includeModule('iblock'))
            \CIBlockType::Delete(self::TYPE);
    }

    /**
     * @return int
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     *
     */
    public static function getId()
    {
        return intval(Option::get('twofingers.location', static::OPTION_IBLOCK_ID));
    }

    /**
     * @return array|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     *
     */
    public static function getIblock()
    {
        $iblockId = static::getId();
        if (!$iblockId || !Loader::includeModule('iblock'))
            return null;

        return \CIBlock::GetByID($iblockId)->Fetch();
    }

    /**
     * @param $code
     * @return array|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     *
     */
    public static function getPropertyByCode($code)
    {
        $iblockId = static::getId();
        if (!$iblockId) return null;

        $filter = [
            'CODE'      => $code,
            'IBLOCK_ID' => static::getId()
        ];

        return \CIBlockProperty::GetList([], $filter)->Fetch();
    }

    /**
     * @param     $code
     * @param int $sort
     * @return bool
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    protected static function createCheckBoxProperty($code, $sort = 100)
    {
        $code = trim($code);
        if (!strlen($code))
            throw new ArgumentNullException('code');

        $property = self::getPropertyByCode($code);
        if ($property) return true;

        $iblockId = self::getId();
        if (!$iblockId) return false;

        $arFields = Array(
            "NAME"          => Loc::getMessage('TFL_IBLOCK_PROP_' . static::CODE . $code),
            "HINT"          => Loc::getMessage('TFL_IBLOCK_PROP_' . static::CODE . $code .'_HINT'),
            "ACTIVE"        => "Y",
            "SORT"          => $sort,
            "CODE"          => $code,
            "PROPERTY_TYPE" => "L",
            "IBLOCK_ID"     => $iblockId,
            'LIST_TYPE'     => 'C',
            'VALUES'        => [
                [
                    'VALUE' => Loc::getMessage('TFL_IBLOCK_PROP_' . $code . '_YES'),
                    'DEF'   => 'N',
                    'SORT'  => 100,
                    'XML_ID'=> 'Y'
                ]
            ]
        );

        return (new \CIBlockProperty)->Add($arFields);
    }

    /**
     * @param        $code
     * @param array  $values
     * @param string $multiple
     * @param int    $sort
     * @return bool|mixed
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    protected static function createListProperty($code, array $values = [], $multiple = 'N', $sort = 100)
    {
        $code = trim($code);
        if (!strlen($code))
            throw new ArgumentNullException('code');

        $property = self::getPropertyByCode($code);
        if ($property)
            return true;

        $iblockId = self::getId();
        if (!$iblockId)
            return false;

        $arFields = Array(
            "NAME"          => Loc::getMessage('TFL_IBLOCK_PROP_' . static::CODE . $code),
            "HINT"          => Loc::getMessage('TFL_IBLOCK_PROP_' . static::CODE . $code .'_HINT'),
            "ACTIVE"        => "Y",
            "SORT"          => $sort,
            "CODE"          => $code,
            "PROPERTY_TYPE" => "L",
            "IBLOCK_ID"     => $iblockId,
            'LIST_TYPE'     => 'L',
            'MULTIPLE'      => $multiple,
            'VALUES'        => $values
        );

        return (new \CIBlockProperty)->Add($arFields);
    }
    /**
     * @param        $code
     * @param int    $sort
     * @param string $required
     * @return bool
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function createStringProperty($code, $sort = 100, $required = 'N')
    {
        $code = trim($code);
        if (!strlen($code))
            throw new ArgumentNullException('code');

        $property = self::getPropertyByCode($code);
        if ($property) return true;

        $iblockId = self::getId();
        if (!$iblockId) return false;

        $arFields = Array(
            "NAME"          => Loc::getMessage('TFL_IBLOCK_PROP_' . static::CODE . $code),
            "HINT"          => Loc::getMessage('TFL_IBLOCK_PROP_' . static::CODE . $code . '_HINT'),
            "ACTIVE"        => "Y",
            "SORT"          => $sort,
            "CODE"          => $code,
            "PROPERTY_TYPE" => "S",
            "IBLOCK_ID"     => $iblockId,
            'IS_REQUIRED'   => $required
        );

        return (new \CIBlockProperty)->Add($arFields);
    }

    /**
     * @param        $code
     * @param int    $sort
     * @param string $multiple
     * @return bool
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function createSiteProperty($code, $sort = 100, $multiple = 'Y')
    {
        $code = trim($code);
        if (!strlen($code))
            throw new ArgumentNullException('code');

        $property = self::getPropertyByCode($code);
        if ($property) return true;

        $iblockId = self::getId();
        if (!$iblockId) return false;

        $arFields = Array(
            "NAME"          => Loc::getMessage('TFL_IBLOCK_PROP_' . static::CODE . $code),
            "HINT"          => Loc::getMessage('TFL_IBLOCK_PROP_' . static::CODE . $code . '_HINT'),
            "ACTIVE"        => "Y",
            "SORT"          => $sort,
            "CODE"          => $code,
            "PROPERTY_TYPE" => "S",
            'USER_TYPE'     => Site::USER_TYPE,
            "IBLOCK_ID"     => $iblockId,
            'IS_REQUIRED'   => 'N',
            'MULTIPLE'      => $multiple == 'Y' ? 'Y' : 'N'
        );

        return (new \CIBlockProperty)->Add($arFields);
    }

    /**
     * @param        $code
     * @param int    $sort
     * @param string $multiple
     * @return bool
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    protected static function createLocationProperty($code, $sort = 100, $multiple = 'Y')
    {
        $code = trim($code);
        if (!strlen($code))
            throw new ArgumentNullException('code');

        $property = self::getPropertyByCode($code);
        if ($property) return true;

        $iblockId = self::getId();
        if (!$iblockId) return false;

        $arFields = Array(
            "NAME"          => Loc::getMessage('TFL_IBLOCK_PROP_' . static::CODE . $code),
            "HINT"          => Loc::getMessage('TFL_IBLOCK_PROP_' . static::CODE . $code . '_HINT'),
            "ACTIVE"        => "Y",
            "SORT"          => $sort,
            "CODE"          => $code,
            "PROPERTY_TYPE" => "S",
            'USER_TYPE'     => \Twofingers\Location\Property\Location::USER_TYPE,
            "IBLOCK_ID"     => $iblockId,
            'IS_REQUIRED'   => 'N',
            'MULTIPLE'      => $multiple == 'Y' ? 'Y' : 'N'
        );

        return (new \CIBlockProperty)->Add($arFields);
    }

    /**
     * @param        $code
     * @param        $linkIblockId
     * @param int    $sort
     * @param string $multiple
     * @return bool|mixed
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function createElementLinkProperty($code, $linkIblockId, $sort = 100, $multiple = 'N')
    {
        $code = trim($code);
        if (!strlen($code))
            throw new ArgumentNullException('code');

        $linkIblockId = intval($linkIblockId);
        if (!$linkIblockId)
            throw new ArgumentNullException('linkIblockId');

        $property = self::getPropertyByCode($code);
        if ($property) return true;

        $iblockId = self::getId();

        if (!$iblockId) return false;

        $arFields = Array(
            "NAME"          => Loc::getMessage('TFL_IBLOCK_PROP_' . static::CODE . $code),
            "HINT"          => Loc::getMessage('TFL_IBLOCK_PROP_' . static::CODE . $code . '_HINT'),
            "ACTIVE"        => "Y",
            "SORT"          => $sort,
            "CODE"          => $code,
            "PROPERTY_TYPE" => "E",
            "IBLOCK_ID"     => $iblockId,
            'LINK_IBLOCK_ID'=> $linkIblockId,
            'MULTIPLE'      => $multiple == 'Y' ? 'Y' : 'N'
        );

        return (new \CIBlockProperty)->Add($arFields);
    }
}