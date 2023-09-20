<?php

namespace TwoFingers\Location;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SiteTable;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Json;
use Exception;

/**
 * Class Options
 *
 * @package TwoFingers\Location
 * @method static getLocationsType
 * @method static setLocationsType(string $type)
 * @method static getListFavoritesPosition(string $siteId, string $default)
 * @method static setListFavoritesPosition(string $position)
 * @method static getSiteLocationsOnly
 * @method static getCallback
 * @method static getListMobileBreakpoint
 * @method static getListDesktopWidth
 * @method static getRedirectEvent
 * @method static setSiteLocationsOnly(string $value)
 * @method static getDefaultLocation(string $siteId)
 * @method static setDefaultLocation(string $type, string $siteId)
 *
 * @method static isListReloadPage
 * @method static isListShowVillages
 * @method static getListTitleFontFamily
 * @method static getListItemsFontFamily
 * @method static getListDesktopPadding
 * @method static getListMobilePadding
 * @method static getNoDomainAction
 * @method static getConfirmOpen
 *
 * ORDER
 * @method static isOrderSetTemplate
 *
 * @method static isOrderSetLocation
 * @method static isOrderSetZip
 *
 * SETTINGS
 * @method static getIncludeJquery
 * @method static isUseGoogleFonts
 * @method static isReplacePlaceholders(string $siteId = '')
 * @method static getCookieLifetime
 * @method static getLocationsLimit
 * @method static getSearchLimit
 * @method static isSxGeoMemory
 * @method static isCapabilityMode
 * @method static isSxGeoAgentUpdate
 * @method static isSxGeoProxyEnabled
 * @method static getSxGeoProxyName
 * @method static getSxGeoProxyPort
 * @method static getSxGeoProxyPass
 * @method static getSxGeoProxyType
 * @method static isTflRedirect
 */
class Options
{
    const MODULE_ID = 'twofingers.location';

    const LOCATIONS_TYPE   = 'locations-type';
    const DEFAULT_LOCATION = 'default-location';

    const CALLBACK         = 'callback';
    const NO_DOMAIN_ACTION = 'no-domain-action';
    const TFL_REDIRECT     = 'tfl-redirect';

    /** @deprecated */
    const REDIRECT_MODE  = 'redirect-mode';
    const REDIRECT_EVENT = 'redirect-event';

    const LIST_LOCATIONS_LOAD      = 'list-locations-load';
    const LIST_FAVORITES_POSITION  = 'list-favorites-position';
    const LIST_DESKTOP_WIDTH       = 'list-desktop-width';
    const LIST_DESKTOP_HEIGHT      = 'list-desktop-height';
    const LIST_LINK_CLASS          = 'list-link-class';
    const LIST_PRE_LINK_TEXT       = 'list-pre-link-text';
    const LIST_OPEN_IF_NO_LOCATION = 'list-open-if-no-location';
    const LIST_RELOAD_PAGE         = 'list-reload-page';
    const LIST_SHOW_VILLAGES       = 'list-show-villages';
    const LIST_MOBILE_BREAKPOINT   = 'list-mobile-breakpoint';
    const LIST_TITLE_FONT_FAMILY   = 'list-title-font-family';
    const LIST_ITEMS_FONT_FAMILY   = 'list-items-font-family';

    const LIST_DESKTOP_PADDING_TOP    = 'list-desktop-padding-top';
    const LIST_DESKTOP_PADDING_LEFT   = 'list-desktop-padding-left';
    const LIST_DESKTOP_PADDING_RIGHT  = 'list-desktop-padding-right';
    const LIST_DESKTOP_PADDING_BOTTOM = 'list-desktop-padding-bottom';
    const LIST_MOBILE_PADDING_TOP     = 'list-mobile-padding-top';
    const LIST_MOBILE_PADDING_LEFT    = 'list-mobile-padding-left';
    const LIST_MOBILE_PADDING_RIGHT   = 'list-mobile-padding-right';
    const LIST_MOBILE_PADDING_BOTTOM  = 'list-mobile-padding-bottom';

    const LIST_DESKTOP_RADIUS = 'list-desktop-radius';

    const LIST_DESKTOP_CLOSE_AREA_OFFSET_TOP   = 'list-desktop-close-area-offset-top';
    const LIST_DESKTOP_CLOSE_AREA_OFFSET_RIGHT = 'list-desktop-close-area-offset-right';
    const LIST_DESKTOP_CLOSE_AREA_SIZE         = 'list-desktop-close-area-size';
    const LIST_DESKTOP_CLOSE_LINE_HEIGHT       = 'list-desktop-close-line-height';
    const LIST_DESKTOP_CLOSE_LINE_WIDTH        = 'list-desktop-close-line-width';
    const LIST_MOBILE_CLOSE_AREA_OFFSET_TOP    = 'list-mobile-close-area-offset-top';
    const LIST_MOBILE_CLOSE_AREA_OFFSET_RIGHT  = 'list-mobile-close-area-offset-right';
    const LIST_MOBILE_CLOSE_AREA_SIZE          = 'list-mobile-close-area-size';
    const LIST_MOBILE_CLOSE_LINE_HEIGHT        = 'list-mobile-close-line-height';
    const LIST_MOBILE_CLOSE_LINE_WIDTH         = 'list-mobile-close-line-width';

    const LIST_DESKTOP_TITLE_FONT_SIZE = 'list-desktop-title-font-size';
    const LIST_DESKTOP_INPUT_FONT_SIZE = 'list-desktop-input-font-size';
    const LIST_DESKTOP_ITEMS_FONT_SIZE = 'list-desktop-items-font-size';
    const LIST_MOBILE_TITLE_FONT_SIZE  = 'list-mobile-title-font-size';
    const LIST_MOBILE_INPUT_FONT_SIZE  = 'list-mobile-input-font-size';
    const LIST_MOBILE_ITEMS_FONT_SIZE  = 'list-mobile-items-font-size';

    const LIST_DESKTOP_INPUT_FOCUS_BORDER_COLOR = 'list-desktop-input-focus-border-color';
    const LIST_DESKTOP_INPUT_FOCUS_BORDER_WIDTH = 'list-desktop-input-focus-border-width';
    const LIST_MOBILE_INPUT_FOCUS_BORDER_COLOR  = 'list-mobile-input-focus-border-color';
    const LIST_MOBILE_INPUT_FOCUS_BORDER_WIDTH  = 'list-mobile-input-focus-border-width';
    const LIST_DESKTOP_INPUT_OFFSET_TOP         = 'list-desktop-input-offset-top';
    const LIST_MOBILE_INPUT_OFFSET_TOP          = 'list-mobile-input-offset-top';
    const LIST_DESKTOP_INPUT_OFFSET_BOTTOM      = 'list-desktop-input-offset-bottom';
    const LIST_MOBILE_INPUT_OFFSET_BOTTOM       = 'list-mobile-input-offset-bottom';

    const CONFIRM_OPEN = 'confirm-open';

    const CONFIRM_TEXT_FONT_FAMILY       = 'confirm-text-font-family';
    const CONFIRM_MOBILE_PADDING_TOP     = 'confirm-mobile-padding-top';
    const CONFIRM_MOBILE_PADDING_LEFT    = 'confirm-mobile-padding-left';
    const CONFIRM_MOBILE_PADDING_RIGHT   = 'confirm-mobile-padding-right';
    const CONFIRM_MOBILE_PADDING_BOTTOM  = 'confirm-mobile-padding-bottom';
    const CONFIRM_DESKTOP_PADDING_TOP    = 'confirm-desktop-padding-top';
    const CONFIRM_DESKTOP_PADDING_LEFT   = 'confirm-desktop-padding-left';
    const CONFIRM_DESKTOP_PADDING_RIGHT  = 'confirm-desktop-padding-right';
    const CONFIRM_DESKTOP_PADDING_BOTTOM = 'confirm-desktop-padding-bottom';

    const CONFIRM_MOBILE_TEXT_FONT_SIZE    = 'confirm-mobile-text-font-size';
    const CONFIRM_MOBILE_BUTTON_FONT_SIZE  = 'confirm-mobile-button-font-size';
    const CONFIRM_DESKTOP_TEXT_FONT_SIZE   = 'confirm-desktop-text-font-size';
    const CONFIRM_DESKTOP_BUTTON_FONT_SIZE = 'confirm-desktop-button-font-size';

    const CONFIRM_MOBILE_BUTTON_TOP_PADDING      = 'confirm-mobile-button-top-padding';
    const CONFIRM_DESKTOP_BUTTON_TOP_PADDING     = 'confirm-desktop-button-top-padding';
    const CONFIRM_MOBILE_BUTTON_BETWEEN_PADDING  = 'confirm-mobile-button-between-padding';
    const CONFIRM_DESKTOP_BUTTON_BETWEEN_PADDING = 'confirm-desktop-button-between-padding';

    const CONFIRM_DESKTOP_WIDTH = 'confirm-desktop-width';

    const ORDER_LINK_CLASS   = 'order-link-class';
    const ORDER_SET_TEMPLATE = 'order-set-template';
    const ORDER_SET_ZIP      = 'order-set-zip';
    const ORDER_SET_LOCATION = 'order-set-location';

    const INCLUDE_JQUERY       = 'include-jquery';
    const USE_GOOGLE_FONTS     = 'use-google-fonts';
    const REPLACE_PLACEHOLDERS = 'replace-placeholders';
    //const SAVE_LOCATION_ON_REDIRECT = 'save-location-on-redirect';
    const COOKIE_LIFETIME     = 'cookie-lifetime';
    const LOCATIONS_LIMIT     = 'locations-limit';
    const SEARCH_LIMIT        = 'search-limit';
    const CAPABILITY_MODE     = 'capability-mode';
    const SX_GEO_MEMORY       = 'sx-geo-memory';
    const SX_GEO_AGENT_UPDATE = 'sx-geo-agent-update';

    const SX_GEO_PROXY_ENABLED = 'sx-geo-proxy-enabled';
    const SX_GEO_PROXY_NAME    = 'sx-geo-proxy-name';
    const SX_GEO_PROXY_PORT    = 'sx-geo-proxy-port';
    const SX_GEO_PROXY_PASS    = 'sx-geo-proxy-pass';
    const SX_GEO_PROXY_TYPE    = 'sx-geo-proxy-type';

    const NO_DOMAIN_ACTION_NONE         = 'N';
    const NO_DOMAIN_ACTION_CURRENT_SITE = 'C';
    const NO_DOMAIN_ACTION_DEFAULT_SITE = 'D';
    //const NO_DOMAIN_ACTION_SITE_DEFAULT_LOCATION_DOMAIN         = 'S';
    //const NO_DOMAIN_ACTION_ALL_SITES_DEFAULT_LOCATION_DOMAIN    = 'A';

    const REDIRECT_EVENT_SELECTED  = 'selected';
    const REDIRECT_EVENT_DETECTED  = 'detected';
    const REDIRECT_EVENT_CONFIRMED = 'confirmed';

    const CONFIRM_OPEN_NOT_DETECTED = 'not-detected';
    const CONFIRM_OPEN_DETECTED     = 'detected';
    const CONFIRM_OPEN_ALWAYS       = 'always';

    /**
     * @param $name
     * @param $arguments
     * @return string
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ArgumentException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function __callStatic($name, $arguments)
    {
        if ((mb_strpos($name, 'get') === 0)
            || (mb_strpos($name, 'set') === 0)) {
            $optionName = mb_substr($name, 3);
            $optionName = self::fromCamelCase($optionName);

            if (mb_strpos($name, 'get') === 0) {
                return self::getValue($optionName, $arguments[1], trim($arguments[0]));
            } else {
                self::setValue($optionName, $arguments[0], $arguments[1]);
            }
        }

        if (mb_strpos($name, 'is') === 0) {
            $optionName = mb_substr($name, 2);
            $optionName = self::fromCamelCase($optionName);
            $value      = self::getValue($optionName, $arguments[1], trim($arguments[0]));

            return in_array($value, ['Y', true], true);
        }
    }

    /**
     * @param        $key
     * @param null $default
     * @param string $siteId
     * @return array|string
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException|ArgumentException
     */
    public static function getValue($key, $default = null, $siteId = false)
    {
        $key = trim($key);
        if (!mb_strlen($key)) {
            throw new ArgumentNullException('key');
        }

        $value        = Option::get(self::MODULE_ID, $key, $default, $siteId);
        $unSerialized = null;

        if (!empty($value)) {
            try {
                $unSerialized = unserialize($value);

                $settingsMap = self::getMap();
                if (($unSerialized === false) && isset($settingsMap[$key]) && ($settingsMap[$key]['type'] == 'ARRAY')) {
                    $unSerialized = Json::decode($value);
                }
            } catch (Exception $e) {
                $unSerialized = null;
            }
        }

        return isset($unSerialized) && is_array($unSerialized) ? $unSerialized : $value;
    }

    /**
     * @param $event
     * @return bool
     */
    public static function hasRedirectEvent($event): bool
    {
        $events = self::getRedirectEvent();

        if (!is_array($events)) {
            $events = [$events];
        }
        return (bool)in_array($event, $events);
    }


    /**
     * @param        $key
     * @param        $value
     * @param string $siteId
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function setValue($key, $value, $siteId = '')
    {
        $key = trim($key);
        if (!strlen($key)) {
            throw new ArgumentNullException('key');
        }

        if (is_array($value)) {
            $value = serialize($value);
        }

        Option::set(self::MODULE_ID, $key, $value, $siteId);
    }

    /**
     * @param        $input
     * @param string $separator
     * @param bool $strToUpper
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function fromCamelCase($input, string $separator = '-', bool $strToUpper = false): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);

        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        $result = implode($separator, $ret);
        if ($strToUpper) {
            $result = strtoupper($result);
        }

        return $result;
    }

    /**
     * @return bool
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function isAgentsOnCron(): bool
    {
        return (Option::get('main', 'agents_use_crontab', '') == 'N'
                && Option::get('main', 'check_agents', '') == 'N')
            || (in_array(Option::get('main', 'agents_use_crontab', 'not-set'), ['Y', 'not-set'])/*
                && Option::get('main', 'check_agents', '') == 'Y'*/);
    }

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::CALLBACK                => ['type' => 'TEXT'],
            self::LIST_LINK_CLASS         => ['type' => 'TEXT'],
            self::LIST_FAVORITES_POSITION => ['type' => 'LIST'],
            self::ORDER_LINK_CLASS        => ['type' => 'TEXT'],
            self::REDIRECT_EVENT          => ['type' => 'ARRAY'],

            self::LIST_TITLE_FONT_FAMILY                      => ['type' => 'LIST'],
            self::LIST_ITEMS_FONT_FAMILY                      => ['type' => 'LIST'],
            self::LIST_MOBILE_PADDING_LEFT                    => ['type' => 'INT', 'size' => 2],
            self::LIST_MOBILE_PADDING_RIGHT                   => ['type' => 'INT', 'size' => 2],
            self::LIST_MOBILE_PADDING_TOP                     => ['type' => 'INT', 'size' => 2],
            self::LIST_MOBILE_PADDING_BOTTOM                  => ['type' => 'INT', 'size' => 2],
            self::LIST_DESKTOP_PADDING_LEFT                   => ['type' => 'INT', 'size' => 2],
            self::LIST_DESKTOP_PADDING_RIGHT                  => ['type' => 'INT', 'size' => 2],
            self::LIST_DESKTOP_PADDING_TOP                    => ['type' => 'INT', 'size' => 2],
            self::LIST_DESKTOP_PADDING_BOTTOM                 => ['type' => 'INT', 'size' => 2],
            self::LIST_DESKTOP_RADIUS                         => ['type' => 'INT', 'size' => 2],
            self::LIST_DESKTOP_CLOSE_AREA_SIZE                => ['type' => 'INT', 'size' => 2],
            self::LIST_DESKTOP_CLOSE_LINE_HEIGHT              => ['type' => 'INT', 'size' => 2],
            self::LIST_DESKTOP_CLOSE_LINE_WIDTH               => ['type' => 'INT', 'size' => 2],
            self::LIST_MOBILE_CLOSE_AREA_SIZE                 => ['type' => 'INT', 'size' => 2],
            self::LIST_MOBILE_CLOSE_LINE_HEIGHT               => ['type' => 'INT', 'size' => 2],
            self::LIST_MOBILE_CLOSE_LINE_WIDTH                => ['type' => 'INT', 'size' => 2],
            self::LIST_DESKTOP_CLOSE_AREA_OFFSET_TOP          => ['type' => 'INT', 'size' => 2],
            self::LIST_DESKTOP_CLOSE_AREA_OFFSET_RIGHT        => ['type' => 'INT', 'size' => 2],
            self::LIST_MOBILE_CLOSE_AREA_OFFSET_TOP           => ['type' => 'INT', 'size' => 2],
            self::LIST_MOBILE_CLOSE_AREA_OFFSET_RIGHT         => ['type' => 'INT', 'size' => 2],
            self::LIST_PRE_LINK_TEXT                          => ['type' => 'TEXT'],
            'TF_LOCATION_DEFAULT_CITIES'                      => ['type' => 'ARRAY'],
            self::DEFAULT_LOCATION                            => ['type' => 'TEXT'],
            self::LIST_OPEN_IF_NO_LOCATION                    => ['type' => 'CHECKBOX'],
            self::USE_GOOGLE_FONTS                            => ['type' => 'CHECKBOX'],
            self::NO_DOMAIN_ACTION                            => ['type' => 'LIST'],
            self::CONFIRM_OPEN                                => ['type' => 'ARRAY'],
            self::LIST_RELOAD_PAGE                            => ['type' => 'CHECKBOX'],
            self::LIST_SHOW_VILLAGES                          => ['type' => 'CHECKBOX'],
            self::LIST_LOCATIONS_LOAD                         => ['type' => 'LIST'],
            'TF_LOCATION_CONFIRM_POPUP_TEXT'                  => ['type' => 'TEXT'],
            'TF_LOCATION_CONFIRM_POPUP_ERROR_TEXT'            => ['type' => 'TEXT'],
            'TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR'         => ['type' => 'TEXT'],
            'TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR_HOVER'   => ['type' => 'TEXT'],
            'TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG'            => ['type' => 'TEXT'],
            'TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG_HOVER'      => ['type' => 'TEXT'],
            'TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR'       => ['type' => 'TEXT'],
            'TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR_HOVER' => ['type' => 'TEXT'],
            'TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG'          => ['type' => 'TEXT'],
            'TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG_HOVER'    => ['type' => 'TEXT'],

            self::LIST_DESKTOP_INPUT_OFFSET_TOP    => ['type' => 'INT', 'size' => 2],
            self::LIST_MOBILE_INPUT_OFFSET_TOP     => ['type' => 'INT', 'size' => 2],
            self::LIST_DESKTOP_INPUT_OFFSET_BOTTOM => ['type' => 'INT', 'size' => 2],
            self::LIST_MOBILE_INPUT_OFFSET_BOTTOM  => ['type' => 'INT', 'size' => 2],

            self::LIST_DESKTOP_TITLE_FONT_SIZE          => ['type' => 'INT', 'size' => 2],
            self::LIST_DESKTOP_INPUT_FONT_SIZE          => ['type' => 'INT', 'size' => 2],
            self::LIST_DESKTOP_ITEMS_FONT_SIZE          => ['type' => 'INT', 'size' => 2],
            self::LIST_MOBILE_TITLE_FONT_SIZE           => ['type' => 'INT', 'size' => 2],
            self::LIST_MOBILE_INPUT_FONT_SIZE           => ['type' => 'INT', 'size' => 2],
            self::LIST_MOBILE_ITEMS_FONT_SIZE           => ['type' => 'INT', 'size' => 2],
            self::LIST_DESKTOP_INPUT_FOCUS_BORDER_WIDTH => ['type' => 'INT', 'size' => 2],
            self::LIST_MOBILE_INPUT_FOCUS_BORDER_WIDTH  => ['type' => 'INT', 'size' => 2],
            self::LIST_DESKTOP_INPUT_FOCUS_BORDER_COLOR => ['type' => 'TEXT'],
            self::LIST_MOBILE_INPUT_FOCUS_BORDER_COLOR  => ['type' => 'TEXT'],

            self::LIST_DESKTOP_WIDTH     => ['type' => 'INT', 'size' => 4],
            self::LIST_DESKTOP_HEIGHT    => ['type' => 'INT', 'size' => 4],
            self::LIST_MOBILE_BREAKPOINT => ['type' => 'INT', 'size' => 4],

            self::CONFIRM_TEXT_FONT_FAMILY       => ['type' => 'TEXT'],
            self::CONFIRM_MOBILE_PADDING_LEFT    => ['type' => 'INT', 'size' => 2],
            self::CONFIRM_MOBILE_PADDING_RIGHT   => ['type' => 'INT', 'size' => 2],
            self::CONFIRM_MOBILE_PADDING_TOP     => ['type' => 'INT', 'size' => 2],
            self::CONFIRM_MOBILE_PADDING_BOTTOM  => ['type' => 'INT', 'size' => 2],
            self::CONFIRM_DESKTOP_PADDING_LEFT   => ['type' => 'INT', 'size' => 2],
            self::CONFIRM_DESKTOP_PADDING_RIGHT  => ['type' => 'INT', 'size' => 2],
            self::CONFIRM_DESKTOP_PADDING_TOP    => ['type' => 'INT', 'size' => 2],
            self::CONFIRM_DESKTOP_PADDING_BOTTOM => ['type' => 'INT', 'size' => 2],

            self::CONFIRM_MOBILE_BUTTON_TOP_PADDING      => ['type' => 'INT', 'size' => 2],
            self::CONFIRM_DESKTOP_BUTTON_TOP_PADDING     => ['type' => 'INT', 'size' => 2],
            self::CONFIRM_MOBILE_BUTTON_BETWEEN_PADDING  => ['type' => 'INT', 'size' => 2],
            self::CONFIRM_DESKTOP_BUTTON_BETWEEN_PADDING => ['type' => 'INT', 'size' => 2],

            self::CONFIRM_MOBILE_TEXT_FONT_SIZE    => ['type' => 'INT', 'size' => 2],
            self::CONFIRM_MOBILE_BUTTON_FONT_SIZE  => ['type' => 'INT', 'size' => 2],
            self::CONFIRM_DESKTOP_TEXT_FONT_SIZE   => ['type' => 'INT', 'size' => 2],
            self::CONFIRM_DESKTOP_BUTTON_FONT_SIZE => ['type' => 'INT', 'size' => 2],

            self::CONFIRM_DESKTOP_WIDTH => ['type' => 'INT', 'size' => 4],

            self::REPLACE_PLACEHOLDERS => ['type' => 'CHECKBOX'],
            self::CAPABILITY_MODE      => ['type' => 'CHECKBOX'],

            self::ORDER_SET_TEMPLATE => ['type' => 'CHECKBOX'],
            self::ORDER_SET_ZIP      => ['type' => 'CHECKBOX'],
            self::ORDER_SET_LOCATION => ['type' => 'CHECKBOX'],

            self::INCLUDE_JQUERY => ['type' => 'LIST'],

            self::SX_GEO_MEMORY                      => ['type' => 'CHECKBOX'],
            self::SX_GEO_AGENT_UPDATE                => ['type' => 'CHECKBOX'],
            self::LOCATIONS_LIMIT                    => ['type' => 'INT', 'size' => 5],
            self::SEARCH_LIMIT                       => ['type' => 'INT', 'size' => 5],
            'TF_LOCATION_LOCATION_POPUP_HEADER'      => ['type' => 'TEXT'],
            'TF_LOCATION_LOCATION_POPUP_PLACEHOLDER' => ['type' => 'TEXT'],
            'TF_LOCATION_LOCATION_POPUP_NO_FOUND'    => ['type' => 'TEXT'],
            //'TF_LOCATION_LOCATION_POPUP_PRELOADER'    => ['type' => 'FILE'],
            'TF_LOCATION_CONFIRM_POPUP_RADIUS'       => ['type' => 'INT', 'size' => 2],
            self::COOKIE_LIFETIME                    => ['type' => 'TEXT', 'size' => 4],
            self::SX_GEO_PROXY_ENABLED               => ['type' => 'CHECKBOX'],
            self::SX_GEO_PROXY_NAME                  => ['type' => 'TEXT', 'size' => 30],
            self::SX_GEO_PROXY_PORT                  => ['type' => 'TEXT', 'size' => 5],
            self::SX_GEO_PROXY_PASS                  => ['type' => 'TEXT', 'size' => 30],
            self::SX_GEO_PROXY_TYPE                  => ['type' => 'LIST'],
        ];
    }

    /**
     * @param $fields
     * @throws ArgumentException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function setList($fields)
    {
        $map      = self::getMap();
        $sites    = SiteTable::getList(['select' => ['LID'], 'cache' => ['ttl' => 3600]]);
        $sitesIds = [];
        while ($site = $sites->fetch()) {
            $sitesIds[] = $site['LID'];
        }

        foreach ($map as $code => $config) {
            $fieldsCode = [false => $code];
            foreach ($sitesIds as $siteId) {
                if (array_key_exists($code . ':' . $siteId, $fields)) {
                    $fieldsCode[$siteId] = $code . ':' . $siteId;
                }
            }

            foreach ($fieldsCode as $siteId => $fieldCode) {
                $val = $fields[$fieldCode] ?? ($config['type'] == 'CHECKBOX' ? 'N' : null);

                if ($config['type'] == 'ARRAY') {
                    $val = serialize($val);
                }

                if ($config['type'] == 'INT') {
                    $val = intval($val);
                }

                Option::set(self::MODULE_ID, $code, $val, empty($siteId) ? false : $siteId);
            }
        }
    }
}