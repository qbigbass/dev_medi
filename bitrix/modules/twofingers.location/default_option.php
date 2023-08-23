<?php
/**
 * Created by PhpStorm.
 * User: Павел
 * Date: 3/7/2021
 * Time: 12:03 PM
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$twofingers_location_default_option = array(
    'replace-placeholders'      => 'Y',
    'sxgeo-memory'              => 'N',
    'locations-limit'           => 5000,
    'search-limit'              => 500,
    'list-pre-link-text'        => Loc::getMessage('tfl__list-pre-link-text-default'),
    'favorites-position'        => 'left-locations',
    'list-mobile-padding'       => 30,
    'list-desktop-padding'      => 30,
    'list-open-if-no-location'  => 'N',
    'TF_LOCATION_REDIRECT'      => 'N', // @TODO: rename
    'TF_LOCATION_SHOW_CONFIRM_POPUP'            => 'N', // @TODO: rename
    'TF_LOCATION_LOAD_LOCATIONS'                => 'all', // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_TEXT'            => Loc::getMessage('TF_LOCATION_CONFIRM_POPUP_TEXT_DEFAULT'), // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_ERROR_TEXT'      => Loc::getMessage('TF_LOCATION_CONFIRM_POPUP_ERROR_TEXT_DEFAULT'), // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR'   => '#ffffff', // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG'      => '#2b7de0', // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG_HOVER'=> '#468de4', // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR' => '#337ab7', // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR_HOVER' => '#039be5', // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG'    => '#f5f5f5', // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG_HOVER' => '#f5f5f5', // @TODO: rename
    'list-desktop-title-font-size'  => 25,
    'list-desktop-input-font-size'  => 15,
    'list-desktop-items-font-size'  => 14,
    'list-mobile-title-font-size'   => 22,
    'list-mobile-input-font-size'   => 14,
    'list-mobile-items-font-size'   => 13,
    'list-desktop-width'            => 700,
    'list-mobile-breakpoint'        => 767,
    'cookie-lifetime'               => 7,
    'capability-mode'               => 'N',
    'sx-geo-memory'                 => 'N',
    'TF_LOCATION_DELIVERY'          => 'Y', // @TODO: rename
    'TF_LOCATION_DELIVERY_ZIP'      => 'Y', // @TODO: rename
    'TF_LOCATION_TEMPLATE'          => 'Y', // @TODO: rename
    'TF_LOCATION_JQUERY_INCLUDE'    => 'N', // @TODO: rename
    'TF_LOCATION_RELOAD'            => 'N', // @TODO: rename
    'TF_LOCATION_SHOW_VILLAGES'     => 'N', // @TODO: rename
    'TF_LOCATION_FILTER_BY_SITE_LOCATIONS'  => 'N', // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_RADIUS'      => 5, // @TODO: rename

    'TF_LOCATION_LOCATION_POPUP_HEADER'         => Loc::getMessage('TF_LOCATION_LOCATION_POPUP_HEADER_DEFAULT'), // @TODO: rename
    'TF_LOCATION_LOCATION_POPUP_PLACEHOLDER'    => Loc::getMessage('TF_LOCATION_LOCATION_POPUP_PLACEHOLDER_DEFAULT'), // @TODO: rename
    'TF_LOCATION_LOCATION_POPUP_NO_FOUND'       => Loc::getMessage('TF_LOCATION_LOCATION_POPUP_NO_FOUND_DEFAULT'), // @TODO: rename
);