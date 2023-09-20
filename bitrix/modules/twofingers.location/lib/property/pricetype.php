<?php

namespace Twofingers\Location\Property;

use Bitrix\Catalog\StoreTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use CCatalogGroup;

Loc::loadMessages(__FILE__);

/**
 * Class PriceType
 *
 * @package Rover\ExtraShop\Property
 * @author  Pavel Shulaev (https://rover-it.me)
 */
class PriceType
{
    const USER_TYPE = 'TfPriceTypeIblockProperty';

    /**
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function GetUserTypeDescription(): array
    {
        return [
            'PROPERTY_TYPE'             => 'S',
            'USER_TYPE'                 => self::USER_TYPE,
            'DESCRIPTION'               => Loc::getMessage('tf-location__prop-price-type-description'),
            'GetPropertyFieldHtml'      => [__CLASS__, 'GetPropertyFieldHtml'],
            'GetPropertyFieldHtmlMulty' => [__CLASS__, 'GetPropertyFieldHtmlMulty'],
            'GetSettingsHTML'           => [__CLASS__, 'GetSettingsHTML'],
        ];
    }


    /**
     * @return array
     */
    protected static function getPriceTypes(): array
    {
        static $cache;

        if (!isset($cache)) {
            $cache       = [];
            $rsPriceType = CCatalogGroup::GetList(["SORT" => "ASC"], []);
            while ($priceType = $rsPriceType->GetNext()) {
                $cache[] = $priceType;
            }
        }

        return $cache;
    }

    /**
     * @param $arProperty
     * @param $value
     * @param $strHTMLControlName
     * @return string
     * @throws LoaderException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName): string
    {
        $html = '';

        if (!Loader::includeModule('catalog')) {
            return $html;
        }

        $priceTypes = self::getPriceTypes();

        $varName = str_replace("VALUE", "DESCRIPTION", $strHTMLControlName["VALUE"]);
        $val     = ($value["VALUE"] ?: $arProperty["DEFAULT_VALUE"]);
        $html    = '<select name="' . $strHTMLControlName["VALUE"] . '" onchange="document.getElementById(\'DESCR_' . $varName . '\').value=this.options[this.selectedIndex].text">';
        $html    .= '<option value="">' . Loc::getMessage('tf-location__prop-price-type-no-type') . '</option>';
        //'<option value="component" '.($val == "component" ? 'selected' : '').'>'.Loc::getMessage("tf-location__from-components-params").'</option>';
        foreach ($priceTypes as $arPrice) {
            $html .= '<option value="' . $arPrice["ID"] . '"';
            if ($val == $arPrice["~ID"]) {
                $html .= ' selected';
            }
            $html .= '>' . $arPrice["NAME"] . '</option>';
        }

        $html .= '</select>';

        return $html;
    }

    /**
     * @param $arProperty
     * @param $value
     * @param $strHTMLControlName
     * @return string
     * @throws LoaderException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function GetPropertyFieldHtmlMulty($arProperty, $value, $strHTMLControlName): string
    {
        $html = '';
        if (!Loader::includeModule('catalog')) {
            return $html;
        }

        $priceTypes = self::getPriceTypes();

        $varName  = str_replace("VALUE", "DESCRIPTION", $strHTMLControlName["VALUE"]);
        $arValues = [];
        if ($value && is_array($value)) {
            foreach ($value as $arValue) {
                $arValues[] = $arValue["VALUE"];
            }
        } else {
            $arValues[] = $arProperty["DEFAULT_VALUE"];
        }

        if ($arProperty['MULTIPLE'] == 'Y') {
            $html .= '<select name="' . $strHTMLControlName["VALUE"] . '[]" multiple size="5" onchange="document.getElementById(\'DESCR_' . $varName . '\').value=this.options[this.selectedIndex].text">';
        } else {
            $html .= '<select name="' . $strHTMLControlName["VALUE"] . '" onchange="document.getElementById(\'DESCR_' . $varName . '\').value=this.options[this.selectedIndex].text">';
        }

        //$html .= '<option value="component" '.(in_array("component", $arValues) ? 'selected' : '').'>'.Loc::getMessage("FROM_COMPONENTS_TITLE").'</option>';
        $html .= '<option value="">' . Loc::getMessage('tf-location__prop-price-type-no-type') . '</option>';
        foreach ($priceTypes as $arPrice) {
            $html .= '<option value="' . $arPrice["ID"] . '"';
            if (in_array($arPrice["~ID"], $arValues)) {
                $html .= ' selected';
            }
            $html .= '>' . $arPrice["NAME"] . '</option>';
        }
        $html .= '</select>';

        return $html;
    }

    /**
     * @param $arProperty
     * @param $strHTMLControlName
     * @param $arPropertyFields
     * @return mixed
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
    {
        $arPropertyFields = [
            'HIDE' => [
                'SMART_FILTER',
                'SEARCHABLE',
                'COL_COUNT',
                'ROW_COUNT',
                'FILTER_HINT',
            ],
            'SET'  => [
                'SMART_FILTER' => 'N',
                'SEARCHABLE'   => 'N',
                'ROW_COUNT'    => '10',
            ],
        ];

        return '';
    }
}
