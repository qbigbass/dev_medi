<?php
namespace Twofingers\Location\Property;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SiteTable;
use Bitrix\Main\SystemException;

/**
 * Class Site
 *
 * @package Twofingers\Location\Property
 */
class Site
{
    const USER_TYPE = 'TfSiteIblockProperty';
    /**
     * @return array
     */
    function GetUserTypeDescription()
    {
        return Array(
            "PROPERTY_TYPE"			=> "S",
            "USER_TYPE"				=> self::USER_TYPE,
            "DESCRIPTION"			=> GetMessage("tf-location__prop-site-description"),
            "GetSettingsHTML"		=> Array("\Twofingers\Location\Property\Site", "GetSettingsHTML"),
            "GetPropertyFieldHtml"	=> Array("\Twofingers\Location\Property\Site", "GetPropertyFieldHtml"),
            "GetAdminListViewHTML"	=> Array("\Twofingers\Location\Property\Site", "GetAdminListViewHTML"),
            "GetAdminFilterHTML"	=> Array("\Twofingers\Location\Property\Site", "GetAdminFilterHTML"),
            "GetPublicViewHTML"		=> Array("\Twofingers\Location\Property\Site", "GetPublicViewHTML"),
            "GetPropertyFieldHtmlMulty"		=> Array("\Twofingers\Location\Property\Site", "GetPropertyFieldHtmlMulty"),
        );
    }

    /**
     * @param $arProperty
     * @param $strHTMLControlName
     * @param $arPropertyFields
     * @return string
     */
    function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
    {
        $arPropertyFields = Array("HIDE" => array("ROW_COUNT", "COL_COUNT", "DEFAULT_VALUE"));

        return '';
    }

    /**
     * @param $arProperty
     * @param $value
     * @param $strHTMLControlName
     * @return mixed|string
     */
    function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
    {
        if ($value['VALUE'])
        {
            $site = SiteTable::getByPrimary($value['VALUE'], ['select' => ['NAME']])->fetch();
            return isset($site['NAME']) ? $site['NAME'] : '&nbsp;';
        }

        return '&nbsp;';
    }

    /**
     * @param $arProperty
     * @param $strHTMLControlName
     * @return string
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    function GetAdminFilterHTML($arProperty, $strHTMLControlName)
    {
        $lAdmin = new \CAdminList($strHTMLControlName["TABLE_ID"]);
        $lAdmin->InitFilter(Array($strHTMLControlName["VALUE"]));
        $filterValue = $GLOBALS[$strHTMLControlName["VALUE"]];

        if (isset($filterValue) && is_array($filterValue)) $values = $filterValue;
        else $values = Array();

        if ($arProperty["MULTIPLE"] === 'Y') $multiple = ' multiple size="5"';
        else $multiple = '';

        $html = "<select name='".$strHTMLControlName['VALUE']."' ".$multiple."><option value=''>".GetMessage("MONDAY_PROP_SITE_NO")."</option>";

        $sites = SiteTable::getList([
            'order' => ['SORT' => 'asc'],
            'select' => ['LID', 'NAME']
        ]);
        //$arSite = CSite::GetList($by="sort", $order="desc", Array());
        while ($site = $sites->fetch())
        {
            $html .= "<option ".($site['LID'] == $filterValue["VALUE"]?'selected':'')." value='".$site['LID']."'>[".$site['LID']."] ".$site['NAME']."</option>";
        }

        $html .= "</select>";

        return  $html;
    }

    /**
     * @param $arProperty
     * @param $value
     * @param $strHTMLControlName
     * @return mixed|string
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        if ($value['VALUE'])
        {
            $site = SiteTable::getByPrimary($value['VALUE'], ['select' => ['NAME']])->fetch();

            return isset($site['NAME']) ? $site['NAME'] : '&nbsp;';
        }

        return '&nbsp;';
    }

    /**
     * @param $arProperty
     * @param $value
     * @param $strHTMLControlName
     * @return string
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $return = "<select name='".$strHTMLControlName['VALUE']."'><option value=''>".GetMessage("MONDAY_PROP_SITE_NO")."</option>";

        $sites = SiteTable::getList([
            'order' => ['SORT' => 'asc'],
            'select' => ['LID', 'NAME']
        ]);
        //$arSite = CSite::GetList($by="sort", $order="desc", Array());
        while ($site = $sites->fetch())
        {
            $return .= "<option ".($site['LID'] == $value["VALUE"]?'selected':'')." value='".$site['LID']."'>[".$site['LID']."] ".$site['NAME']."</option>";
        }

        $return .= "</select>";

        return $return;
    }

    /**
     * @param $arProperty
     * @param $value
     * @param $strHTMLControlName
     * @return string
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function GetPropertyFieldHtmlMulty($arProperty, $value, $strHTMLControlName)
    {
        $return = '';

        $sites = SiteTable::getList([
            'order' => ['SORT' => 'asc'],
            'select' => ['LID', 'NAME']
        ]);

        $newCount = 0;
        while ($site = $sites->fetch())
        {
            $siteValueId = null;
            $checked     = false;
            foreach ($value as $valueId => $valueParams)
            {
                if ($valueParams['VALUE'] == $site['LID'])
                {
                    $siteValueId = $valueId;
                    $checked = true;
                    break;
                }
            }

            if (!$siteValueId)
                $siteValueId = 'n' . $newCount++;

            $return .= "<label><input name='".$strHTMLControlName['VALUE']."[" . $siteValueId . "][VALUE]' type='checkbox' ".($checked?'checked':'')." value='".$site['LID']."'/>[".$site['LID']."] ".$site['NAME']."</label><br>";
        }


        return $return;
    }
}