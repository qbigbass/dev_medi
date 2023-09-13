<?php

namespace Twofingers\Location\Property;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Sale\Location\Admin\LocationHelper;

/**
 * Class Site
 *
 * @package Twofingers\Location\Property
 */
class Location
{
    const USER_TYPE = 'TfLocationIblockProperty';

    /**
     * @return array
     */
    public static function GetUserTypeDescription(): array
    {
        return [
            "PROPERTY_TYPE"        => "S",
            "USER_TYPE"            => self::USER_TYPE,
            "DESCRIPTION"          => GetMessage("tf-location__prop-location-description"),
            "GetPropertyFieldHtml" => ["\Twofingers\Location\Property\Location", "GetPropertyFieldHtml"],
            "GetAdminListViewHTML" => ["\Twofingers\Location\Property\Location", "GetAdminListViewHTML"],
            "GetPublicViewHTML"    => ["\Twofingers\Location\Property\Location", "GetAdminListViewHTML"],
            /* "GetSettingsHTML"		=> Array("\Twofingers\Location\Property\Site", "GetSettingsHTML"),
            "GetPropertyFieldHtml"	=> Array("\Twofingers\Location\Property\Site", "GetPropertyFieldHtml"),
            "GetAdminListViewHTML"	=> Array("\Twofingers\Location\Property\Site", "GetAdminListViewHTML"),
            "GetAdminFilterHTML"	=> Array("\Twofingers\Location\Property\Site", "GetAdminFilterHTML"),
            "GetPublicViewHTML"		=> Array("\Twofingers\Location\Property\Site", "GetPublicViewHTML"),
           */
            //"GetPropertyFieldHtmlMulty"		=> Array("\Twofingers\Location\Property\Site", "GetPropertyFieldHtmlMulty"),
        ];
    }

    /**
     * @param $arProperty
     * @param $value
     * @param $strHTMLControlName
     * @return false|string
     * @throws LoaderException
     */
    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {

        if (!Loader::IncludeModule('sale')) {
            return false;
        }

        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "bitrix:sale.location.selector.search",
            ".default",
            [
                "COMPONENT_TEMPLATE"     => "search",
                "ID"                     => "",
                "CODE"                   => htmlspecialcharsbx($value['VALUE']),
                "INPUT_NAME"             => htmlspecialcharsbx($strHTMLControlName['VALUE']),
                "PROVIDE_LINK_BY"        => "code",
                "JSCONTROL_GLOBAL_ID"    => "",
                "JS_CALLBACK"            => "",
                "SEARCH_BY_PRIMARY"      => "Y",
                "EXCLUDE_SUBTREE"        => "",
                "FILTER_BY_SITE"         => "Y",
                "SHOW_DEFAULT_LOCATIONS" => "Y",
                "CACHE_TYPE"             => "A",
                "CACHE_TIME"             => "36000000"
            ],
            false
        );

        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    /**
     * @param $arProperty
     * @param $value
     * @param $strHTMLControlName
     * @return false|string
     * @throws LoaderException
     */
    public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        if (!Loader::IncludeModule('sale')) {
            return false;
        }

        return LocationHelper::getLocationStringById($arProperty['VALUE']);
    }
}