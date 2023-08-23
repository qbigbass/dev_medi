<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 07.03.2019
 * Time: 13:28
 *
 *
 */

namespace TwoFingers\Location;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SiteTable;
use Bitrix\Main\SystemException;
use \Bitrix\Main\Web\Json;

Loc::loadMessages(__FILE__);

/**
 * Class Settings
 *
 * @package TwoFingers\Location
 * @deprecated
 */
class Settings
{
    /**
     * @return array
     * @deprecated
     */
    public static function getMap(): array
    {
        return [
            Options::CALLBACK           => ['type' => 'TEXT'],
            Options::LIST_LINK_CLASS    => ['type' => 'TEXT'],
            Options::LIST_FAVORITES_POSITION => [
                'type'      => 'LIST',
                'options' => [
                    'above-search'  => Loc::getMessage('tf-location__' . Options::LIST_FAVORITES_POSITION . '-above-search'),
                    'under-search'  => Loc::getMessage('tf-location__' . Options::LIST_FAVORITES_POSITION . '-under-search'),
                    'left-locations'=> Loc::getMessage('tf-location__' . Options::LIST_FAVORITES_POSITION . '-left-locations'),
                    'right-locations'=> Loc::getMessage('tf-location__' . Options::LIST_FAVORITES_POSITION . '-right-locations'),
                ]
            ],
            Options::ORDER_LINK_CLASS           => ['type' => 'TEXT'],
            Options::LIST_MOBILE_PADDING        => ['type' => 'INT', 'size' => 2],
            Options::LIST_DESKTOP_PADDING       => ['type' => 'INT', 'size' => 2],
            Options::LIST_DESKTOP_RADIUS        => ['type' => 'INT'],
            Options::LIST_PRE_LINK_TEXT         => ['type' => 'TEXT'],
            'TF_LOCATION_DEFAULT_CITIES'        => ['type' => 'ARRAY'],
            Options::DEFAULT_LOCATION           => ['type' => 'TEXT'],
            Options::LIST_OPEN_IF_NO_LOCATION   => ['type' => 'CHECKBOX'],
            'TF_LOCATION_REDIRECT'=> [
                'type'      => 'LIST',
                'options'   => [
                    'N' => Loc::getMessage('TF_LOCATION_REDIRECT_N'),
                    'A' => Loc::getMessage('TF_LOCATION_REDIRECT_A'),
                    'C' => Loc::getMessage('TF_LOCATION_REDIRECT_C'),
                ]
            ],
            'TF_LOCATION_SHOW_CONFIRM_POPUP'=> [
                'type' => 'LIST',
                'options' => [
                    'N' => Loc::getMessage('TF_LOCATION_SHOW_CONFIRM_POPUP_N'),
                    'Y' => Loc::getMessage('TF_LOCATION_SHOW_CONFIRM_POPUP_Y'),
                    'A' => Loc::getMessage('TF_LOCATION_SHOW_CONFIRM_POPUP_A'),
                    'U' => Loc::getMessage('TF_LOCATION_SHOW_CONFIRM_POPUP_U'),
                ]
            ],
            'TF_LOCATION_LOAD_LOCATIONS'=> [
                'type'      => 'LIST',
                'options'   => [
                    'all'       => Loc::getMessage('TF_LOCATION_LOAD_LOCATIONS_all'),
                    'cities'    => Loc::getMessage('TF_LOCATION_LOAD_LOCATIONS_cities'),
                    'defaults'  => Loc::getMessage('TF_LOCATION_LOAD_LOCATIONS_defaults'),
                ]
            ],
            'TF_LOCATION_CONFIRM_POPUP_TEXT'                => ['type' => 'TEXT'],
            'TF_LOCATION_CONFIRM_POPUP_ERROR_TEXT'          => ['type' => 'TEXT'],
            'TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR'       => ['type' => 'TEXT'],
            'TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR_HOVER' => ['type' => 'TEXT'],
            'TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG'          => ['type' => 'TEXT'],
            'TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG_HOVER'    => ['type' => 'TEXT'],
            'TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR'     => ['type' => 'TEXT'],
            'TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR_HOVER'   => ['type' => 'TEXT'],
            'TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG'        => ['type' => 'TEXT'],
            'TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG_HOVER'  => ['type' => 'TEXT'],
            Options::LIST_DESKTOP_TITLE_FONT_SIZE       => ['type' => 'INT', 'size' => 2],
            Options::LIST_DESKTOP_INPUT_FONT_SIZE       => ['type' => 'INT', 'size' => 2],
            Options::LIST_DESKTOP_ITEMS_FONT_SIZE       => ['type' => 'INT', 'size' => 2],
            Options::LIST_MOBILE_TITLE_FONT_SIZE        => ['type' => 'INT', 'size' => 2],
            Options::LIST_MOBILE_INPUT_FONT_SIZE        => ['type' => 'INT', 'size' => 2],
            Options::LIST_MOBILE_ITEMS_FONT_SIZE        => ['type' => 'INT', 'size' => 2],
            Options::LIST_DESKTOP_WIDTH                 => ['type' => 'INT', 'size' => 4],
            Options::LIST_MOBILE_BREAKPOINT             => ['type' => 'INT', 'size' => 4],
            Options::REPLACE_PLACEHOLDERS               => ['type' => 'CHECKBOX'],
            Options::CAPABILITY_MODE                    => ['type' => 'CHECKBOX'],
            'TF_LOCATION_DELIVERY'                      => ['type' => 'CHECKBOX'],
            'TF_LOCATION_DELIVERY_ZIP'                  => ['type' => 'CHECKBOX'],
            'TF_LOCATION_TEMPLATE'                      => ['type' => 'CHECKBOX'],
            'TF_LOCATION_JQUERY_INCLUDE'                => ['type' => 'CHECKBOX'],
            'TF_LOCATION_RELOAD'                        => ['type' => 'CHECKBOX'],
            'TF_LOCATION_SHOW_VILLAGES'                 => ['type' => 'CHECKBOX'],
            Options::SX_GEO_MEMORY                      => ['type' => 'CHECKBOX'],
            Options::LOCATIONS_LIMIT                    => ['type' => 'INT', 'size' => 5],
            Options::SEARCH_LIMIT                       => ['type' => 'INT', 'size' => 5],
            'TF_LOCATION_FILTER_BY_SITE_LOCATIONS'      => ['type' => 'CHECKBOX'],
            //Options::OPTION__SITE_LOCATIONS_ONLY        => ['type' => 'CHECKBOX', 'default' => 'N'],
            'TF_LOCATION_LOCATION_POPUP_HEADER'         => ['type' => 'TEXT'],
            'TF_LOCATION_LOCATION_POPUP_PLACEHOLDER'    => ['type' => 'TEXT'],
            'TF_LOCATION_LOCATION_POPUP_NO_FOUND'       => ['type' => 'TEXT'],
            //'TF_LOCATION_LOCATION_POPUP_PRELOADER'    => ['type' => 'FILE'],
            'TF_LOCATION_CONFIRM_POPUP_RADIUS'          => ['type' => 'INT'],
            Options::COOKIE_LIFETIME                    => ['type' => 'TEXT', 'size' => 4],
        ];
    }

    /**
     * @return array
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @deprecated
     */
    public static function getList(): array
    {
        $settings   = array();
        $map        = self::getMap();

        foreach($map as $code => $config)
        {
            $value = Options::getValue($code);

            if($config['type'] == 'ARRAY') {
                try{
                    $value = Json::decode($value);
                } catch (\Exception $e) {
                    $value = $config['default'];
                }
            }

            $settings[$code] = $value;
        }

        return $settings;
    }

    /**
     * @param $key
     * @return mixed|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @deprecated
     */
    public static function get($key)
    {
        $key = trim($key);
        if (!$key) return null;

        $settings = self::getList();
        if (isset($settings[$key]))
            return $settings[$key];

        return null;
    }

    /**
     * @param $fields
     * @throws ArgumentException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @deprecated
     */
    public static function setList($fields)
    {
        $val    = null;
        $map    = self::getMap();
        $sites  = SiteTable::getList(['select' => ['LID']]);
        $sitesIds = [];
        while ($site = $sites->fetch())
            $sitesIds[] = $site['LID'];

        foreach ($map as $code => $config)
        {
            $fieldsCode = [false => $code];
            foreach ($sitesIds as $siteId)
                if (array_key_exists($code . ':' . $siteId, $fields))
                    $fieldsCode[$siteId] = $code . ':' . $siteId;

            foreach ($fieldsCode as $siteId => $fieldCode)
            {
                if ($config['type'] == 'CHECKBOX' && !isset($fields[$fieldCode])) {
                    $val = 'N';
                } elseif (isset($fields[$fieldCode])) {
                    $val = $fields[$fieldCode];
                }

                if ($config['type'] == 'ARRAY')
                    $val = Json::encode($val);

                if ($config['type'] == 'INT')
                    $val = intval($val);

                Option::set(Options::MODULE_ID, $code, $val, empty($siteId) ? false : $siteId);
            }
        }
    }
}