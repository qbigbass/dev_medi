<?php

namespace TwoFingers\Location;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;

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
 * @method static getCookieLifetime
 * @method static isSiteLocationsOnly
 * @method static isSxGeoMemory
 * @method static isCapabilityMode
 * @method static isReplacePlaceholders(string $siteId = '')
 * @method static setSiteLocationsOnly(string $value)
 * @method static getDefaultLocation(string $siteId)
 * @method static setDefaultLocation(string $type, string $siteId)
 * @method static isSaveLocationOnRedirect(string $siteId, string $default)
 * @method static getLocationsLimit
 * @method static getSearchLimit
 * @method static getListDesktopPadding
 * @method static getListMobilePadding
 */
class Options
{
    const MODULE_ID = 'twofingers.location';

    const LOCATIONS_TYPE            = 'locations-type';
    const DEFAULT_LOCATION          = 'default-location';
    //const OPTION__SITE_LOCATIONS_ONLY   = 'site-locations-only';
    const CAPABILITY_MODE           = 'capability-mode';
    const SX_GEO_MEMORY             = 'sx-geo-memory';
    const CALLBACK                  = 'callback';

    const LIST_FAVORITES_POSITION       = 'list-favorites-position';
    const LIST_DESKTOP_WIDTH            = 'list-desktop-width';
    const LIST_LINK_CLASS               = 'list-link-class';
    const LIST_PRE_LINK_TEXT            = 'list-pre-link-text';
    const LIST_OPEN_IF_NO_LOCATION      = 'list-open-if-no-location';
    const LIST_MOBILE_BREAKPOINT        = 'list-mobile-breakpoint';
    const LIST_DESKTOP_PADDING          = 'list-desktop-padding';
    const LIST_MOBILE_PADDING           = 'list-mobile-padding';
    const LIST_DESKTOP_RADIUS           = 'list-desktop-radius';
    const LIST_DESKTOP_TITLE_FONT_SIZE  = 'list-desktop-title-font-size';
    const LIST_DESKTOP_INPUT_FONT_SIZE  = 'list-desktop-input-font-size';
    const LIST_DESKTOP_ITEMS_FONT_SIZE  = 'list-desktop-items-font-size';
    const LIST_MOBILE_TITLE_FONT_SIZE   = 'list-mobile-title-font-size';
    const LIST_MOBILE_INPUT_FONT_SIZE   = 'list-mobile-input-font-size';
    const LIST_MOBILE_ITEMS_FONT_SIZE   = 'list-mobile-items-font-size';

    const ORDER_LINK_CLASS          = 'order-link-class';

    const REPLACE_PLACEHOLDERS      = 'replace-placeholders';
    const SAVE_LOCATION_ON_REDIRECT = 'save-location-on-redirect';
    const COOKIE_LIFETIME           = 'cookie-lifetime';
    const LOCATIONS_LIMIT           = 'locations-limit';
    const SEARCH_LIMIT              = 'search-limit';

    /**
     * @param $name
     * @param $arguments
     * @return string
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function __callStatic($name, $arguments)
    {

        if ((strpos($name, 'get') === 0)
            || (strpos($name, 'set') === 0))
        {
            $optionName = mb_substr($name, 3);
            $optionName = self::fromCamelCase($optionName);

            if (strpos($name, 'get') === 0)
                return self::getValue($optionName, $arguments[1], trim($arguments[0]));
            else
                self::setValue($optionName, $arguments[0], $arguments[1]);
        }

        if (strpos($name, 'is') === 0)
        {
            $optionName = mb_substr($name, 2);
            $optionName = self::fromCamelCase($optionName);
            $value      = self::getValue($optionName, $arguments[1], trim($arguments[0]));

            return ($value == 'Y') || ($value === true);
        }
    }

    /**
     * @param        $key
     * @param null   $default
     * @param string $siteId
     * @return array|string
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function getValue($key, $default = null, $siteId = '')
    {
        $key = trim($key);
        if (!strlen($key))
            throw new ArgumentNullException('key');

        $value          = Option::get(self::MODULE_ID, $key, $default, $siteId);
        $unserialized   = null;

        if (!empty($value))
            $unserialized = unserialize($value);

        return isset($unserialized) && is_array($unserialized) ? $unserialized : $value;
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
        if (!strlen($key))
            throw new ArgumentNullException('key');

        if (is_array($value))
            $value = serialize($value);

        Option::set(self::MODULE_ID, $key, $value, $siteId);
    }

    /**
     * @param        $input
     * @param string $separator
     * @param bool   $strToUpper
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function fromCamelCase($input, $separator = '-', $strToUpper = false): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);

        $ret = $matches[0];
        foreach ($ret as &$match)
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);

        $result = implode($separator, $ret);
        if ($strToUpper)
            $result = strtoupper($result);

        return $result;
    }
}