<?php
/**
 * Created by PhpStorm.
 * User: Павел
 * Date: 3/7/2021
 * Time: 12:03 PM
 *
 *
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$twofingers_location_default_option = [
    'replace-placeholders' => 'Y',
    'sxgeo-memory'         => 'N',
    'locations-limit'      => 5000,
    'search-limit'         => 500,
    'redirect-mode'        => 'N',
    'no-domain-action'     => 'N',
    'tfl-redirect'         => 'Y',

    'list-pre-link-text'                   => Loc::getMessage('tfl__list-pre-link-text-default'),
    'list-favorites-position'              => 'left-locations',
    'list-mobile-padding-top'              => 30,
    'list-mobile-padding-left'             => 30,
    'list-mobile-padding-right'            => 30,
    'list-mobile-padding-bottom'           => 30,
    'list-desktop-padding-top'             => 30,
    'list-desktop-padding-left'            => 30,
    'list-desktop-padding-right'           => 30,
    'list-desktop-padding-bottom'          => 30,
    'list-desktop-close-area-offset-top'   => 20,
    'list-desktop-close-area-offset-right' => 20,
    'list-mobile-close-area-offset-top'    => 20,
    'list-mobile-close-area-offset-right'  => 20,
    'list-open-if-no-location'             => 'N',

    'list-desktop-input-focus-border-color' => '#1f2949',
    'list-mobile-input-focus-border-color'  => '#1f2949',
    'list-desktop-input-focus-border-width' => '2',
    'list-mobile-input-focus-border-width'  => '2',
    'list-desktop-input-offset-top'         => '26',
    'list-mobile-input-offset-top'          => '26',
    'list-desktop-input-offset-bottom'      => '15',
    'list-mobile-input-offset-bottom'       => '15',


    'list-locations-load'          => 'all',
    'list-reload-page'             => 'N',
    'list-show-villages'           => 'N',
    'list-desktop-title-font-size' => 25,
    'list-desktop-input-font-size' => 15,
    'list-desktop-items-font-size' => 14,
    'list-mobile-title-font-size'  => 22,
    'list-mobile-input-font-size'  => 14,
    'list-mobile-items-font-size'  => 13,
    'list-desktop-width'           => 700,
    'list-desktop-height'          => 512,
    'list-mobile-breakpoint'       => 767,

    'list-desktop-close-area-size'   => 40,
    'list-desktop-close-line-height' => 20,
    'list-desktop-close-line-width'  => 2,
    'list-mobile-close-area-size'    => 40,
    'list-mobile-close-line-height'  => 20,
    'list-mobile-close-line-width'   => 2,

    'cookie-lifetime'     => 7,
    'capability-mode'     => 'N',
    'sx-geo-memory'       => 'N',
    'order-set-template'  => 'Y',
    'order-set-zip'       => 'Y',
    'order-set-location'  => 'Y',
    'include-jquery'      => '',
    'sx-geo-agent-update' => 'N',
    'sx-geo-proxy-port'   => 80,
    'sx-geo-proxy-type'   => 0,//CURLPROXY_HTTP,

    'confirm-open' => serialize(['not-detected', 'detected']),

    'confirm-mobile-padding-top'             => 30,
    'confirm-mobile-padding-left'            => 20,
    'confirm-mobile-padding-right'           => 20,
    'confirm-mobile-padding-bottom'          => 10,
    'confirm-desktop-padding-top'            => 30,
    'confirm-desktop-padding-left'           => 20,
    'confirm-desktop-padding-right'          => 20,
    'confirm-desktop-padding-bottom'         => 10,
    'confirm-mobile-button-top-padding'      => 10,
    'confirm-desktop-button-top-padding'     => 10,
    'confirm-mobile-button-between-padding'  => 10,
    'confirm-desktop-button-between-padding' => 10,

    'confirm-mobile-text-font-size'    => 14,
    'confirm-mobile-button-font-size'  => 12,
    'confirm-desktop-text-font-size'   => 14,
    'confirm-desktop-button-font-size' => 12,
    'confirm-desktop-width'            => 240,

    'TF_LOCATION_CONFIRM_POPUP_TEXT'                  => Loc::getMessage('TF_LOCATION_CONFIRM_POPUP_TEXT_DEFAULT'),
    // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_ERROR_TEXT'            => Loc::getMessage('TF_LOCATION_CONFIRM_POPUP_ERROR_TEXT_DEFAULT'),
    // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR'         => '#ffffff',
    // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG'            => '#2b7de0',
    // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG_HOVER'      => '#468de4',
    // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR'       => '#337ab7',
    // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR_HOVER' => '#039be5',
    // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG'          => '#f5f5f5',
    // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG_HOVER'    => '#f5f5f5',

    // @TODO: rename
    'TF_LOCATION_CONFIRM_POPUP_RADIUS'                => 5,
    // @TODO: rename

    'TF_LOCATION_LOCATION_POPUP_HEADER'      => Loc::getMessage('TF_LOCATION_LOCATION_POPUP_HEADER_DEFAULT'),
    // @TODO: rename
    'TF_LOCATION_LOCATION_POPUP_PLACEHOLDER' => Loc::getMessage('TF_LOCATION_LOCATION_POPUP_PLACEHOLDER_DEFAULT'),
    // @TODO: rename
    'TF_LOCATION_LOCATION_POPUP_NO_FOUND'    => Loc::getMessage('TF_LOCATION_LOCATION_POPUP_NO_FOUND_DEFAULT'),
    // @TODO: rename
    'TF_LOCATION_DEFAULT_CITIES'             => [],
    // @TODO: rename
];