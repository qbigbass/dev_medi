<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage sale
 * @copyright 2001-2014 Bitrix
 */

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use TwoFingers\Location\Factory\LocationFactory;
use TwoFingers\Location\Model\Location;
use TwoFingers\Location\Options;
use TwoFingers\Location\Storage;

Loc::loadMessages(__FILE__);

if (!Loader::IncludeModule('twofingers.location')) {
    ShowError(Loc::getMessage('tfl__module-error'));
    return;
}

/**
 * Class TfLocationComponent
 */
class TfLocationComponent extends CBitrixComponent
{
    public static $templateLoaded = false;

    /**
     *
     */
    public function onIncludeComponentLang()
    {
        $this->includeComponentLang(basename(__FILE__));
        Loc::loadMessages(__FILE__);
    }

    /**
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams): array
    {
        $arParams['LANGUAGE_ID'] = $arParams['LANGUAGE_ID'] ?? LANGUAGE_ID;
        $arParams['SITE_ID']     = $arParams['SITE_ID'] ?? SITE_ID;

        return $arParams;
    }

    /**
     * @throws Main\LoaderException
     * @throws Main\SystemException
     */
    protected function checkParams()
    {
        if (!Main\Loader::includeModule('twofingers.location')) {
            throw new Main\SystemException(Loc::getMessage('tfl__module-error'));
        }
    }

    /**
     * @return array
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException|Main\ArgumentException
     */
    protected function prepareSettings(): array
    {
        $settingsMap = Options::getMap();
        $result      = [];
        foreach ($settingsMap as $code => $settings) {
            if (!array_key_exists($code, $this->arParams)) {
                $result[$code] = Options::getValue($code);
                continue;
            }

            switch ($settings['type']) {
                case 'TEXT':
                    $result[$code] = trim($this->arParams[$code]);
                    if (!strlen($result[$code])) {
                        $result[$code] = Options::getValue($code);
                    }
                    break;
                case 'INT':
                    $result[$code] = intval($this->arParams[$code]);
                    if (!$result[$code]) {
                        $result[$code] = intval(Options::getValue($code));
                    }
                    break;
                case 'CHECKBOX':
                    $result[$code] = in_array($this->arParams[$code], ['Y', 'N'])
                        ? $this->arParams[$code] : Options::getValue($code);
                    break;
                case 'LIST':
                    $result[$code] = in_array($this->arParams[$code], array_keys($settings['options']))
                        ? $this->arParams[$code] : Options::getValue($code);
                    break;
                default:
                    $result[$code] = empty($this->arParams[$code])
                        ? Options::getValue($code) : $this->arParams[$code];
            }
        }

        return $result;
    }

    /**
     * @throws Main\ArgumentNullException
     */
    protected function loadLocationDataFromStorage()
    {
        // try to get info
        if (!Storage::isEmpty()) {
            $location = LocationFactory::buildByStorage($this->arParams['SITE_ID'],
                $this->arParams['LANGUAGE_ID']);

            if (isset($location)) {
                $this->arResult['CITY_ID']   = Options::isCapabilityMode() && Location::getType() == Location::TYPE_SALE
                    ? $location->getCode()
                    : $location->getId();
                $this->arResult['CITY_NAME'] = $location->getName();
                $this->arResult['CITY_CODE'] = $location->getCode();
            }
        }

        if (!isset($location)) {
            $this->arResult['CITY_NAME'] = Loc::getMessage("tfl__choose");
            $this->arResult['CITY_ID']   = null;
            $this->arResult['CITY_CODE'] = null;
        }
    }

    /**
     * @param $locationId
     * @return bool
     */
    protected function getConfirmPopupStatus($locationId): string
    {
        $confirmOpenFlags = Options::getConfirmOpen();

        foreach ($confirmOpenFlags as $curFlag) {
            switch ($curFlag) {
                case Options::CONFIRM_OPEN_NOT_DETECTED:
                    if (!isset($locationId)) {
                        return 'Y';
                    }
                    break;
                case Options::CONFIRM_OPEN_DETECTED:
                    if (Storage::isNeedCheck() && !Storage::isConfirmPopupClosed()) {
                        return 'Y';
                    }
                    break;
                case Options::CONFIRM_OPEN_ALWAYS:
                    return 'Y';
            }
        }

        return 'N';
    }

    /**
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     */
    protected function getResult()
    {
        $this->arResult['CALL_CONFIRM_POPUP']  = 'N';
        $this->arResult['CALL_LOCATION_POPUP'] = 'N';
        $this->arResult['SETTINGS']            = $this->prepareSettings();

        $this->loadLocationDataFromStorage();

        $confirmOpenFlags = Options::getConfirmOpen();

        /*if (!Storage::isConfirmPopupClosed() && Storage::isNeedCheck()) {

            switch (Options::getConfirmOpen()) {
                case 'A': $this->arResult['CALL_CONFIRM_POPUP'] = 'Y'; break;
                case 'Y': if (!isset($this->arResult['CITY_ID'])) {
                    $this->arResult['CALL_CONFIRM_POPUP'] = 'Y';
                }
                break;
                case 'U'
            }
            $this->arResult['CALL_CONFIRM_POPUP'] =
                (Options::getConfirmOpen() == 'A') // не было закрыто вручную
                || ((Options::getConfirmOpen() == 'Y') && (!isset($this->arResult['CITY_ID']))

            if (Storage::isNeedCheck() && in_array(Options::getConfirmOpen(), ['Y', 'U'])) {
                $this->arResult['CALL_CONFIRM_POPUP'] = 'Y';
            } elseif (Options::getConfirmOpen() == 'A') {
                $this->arResult['CALL_CONFIRM_POPUP'] = 'Y';
            }
        }*/
        $this->arResult['CALL_CONFIRM_POPUP'] = self::getConfirmPopupStatus($this->arResult['CITY_ID']);

        if (!isset($this->arResult['CITY_ID'])
            && ($this->arResult['SETTINGS'][Options::LIST_OPEN_IF_NO_LOCATION] == 'Y')
            && ($this->arResult['CALL_CONFIRM_POPUP'] == 'N')) {
            $this->arResult['CALL_LOCATION_POPUP'] = 'Y';
        }

        $this->arResult['COMPONENT_PATH'] = $this->getPath();
        $this->arResult['AJAX_SEARCH']    = in_array($this->arResult['SETTINGS'][Options::LIST_LOCATIONS_LOAD],
            ['cities', 'defaults']);
    }

    /**
     *
     */
    protected function addCapabilityResult()
    {
        $this->arResult['PRIMARY_COLOR']         = $this->arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR'];
        $this->arResult['PRIMARY_BG']            = $this->arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG'];
        $this->arResult['PRIMARY_COLOR_HOVER']   = $this->arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR_HOVER'];
        $this->arResult['PRIMARY_BG_HOVER']      = $this->arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG_HOVER'];
        $this->arResult['SECONDARY_COLOR']       = $this->arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR'];
        $this->arResult['SECONDARY_BG']          = $this->arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG'];
        $this->arResult['SECONDARY_COLOR_HOVER'] = $this->arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR_HOVER'];
        $this->arResult['SECONDARY_BG_HOVER']    = $this->arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG_HOVER'];

        $this->arResult['SETTINGS']['TF_LOCATION_DEFAULT_CITY']       = $this->arResult['SETTINGS'][Options::DEFAULT_LOCATION];
        $this->arResult['SETTINGS']['TF_LOCATION_COOKIE_LIFETIME']    = $this->arResult['SETTINGS'][Options::COOKIE_LIFETIME];
        $this->arResult['SETTINGS']['TF_LOCATION_CALLBACK']           = $this->arResult['SETTINGS'][Options::CALLBACK];
        $this->arResult['SETTINGS']['TF_LOCATION_MOBILE_WIDTH']       = $this->arResult['SETTINGS'][Options::LIST_MOBILE_BREAKPOINT];
        $this->arResult['SETTINGS']['TF_LOCATION_POPUP_RADIUS']       = $this->arResult['SETTINGS'][Options::LIST_DESKTOP_RADIUS];
        $this->arResult['SETTINGS']['TF_LOCATION_SXGEO_MEMORY']       = $this->arResult['SETTINGS'][Options::SX_GEO_MEMORY];
        $this->arResult['SETTINGS']['TF_LOCATION_HEADLINK_CLASS']     = $this->arResult['SETTINGS'][Options::LIST_LINK_CLASS];
        $this->arResult['SETTINGS']['TF_LOCATION_HEADLINK_TEXT']      = $this->arResult['SETTINGS'][Options::LIST_PRE_LINK_TEXT];
        $this->arResult['SETTINGS']['TF_LOCATION_ORDERLINK_CLASS']    = $this->arResult['SETTINGS'][Options::ORDER_LINK_CLASS];
        $this->arResult['SETTINGS']['favorites-position']             = $this->arResult['SETTINGS'][Options::LIST_FAVORITES_POSITION];
        $this->arResult['SETTINGS']['TF_LOCATION_ONUNKNOWN']          = $this->arResult['SETTINGS'][Options::LIST_OPEN_IF_NO_LOCATION];
        $this->arResult['SETTINGS']['TF_LOCATION_SHOW_CONFIRM_POPUP'] = $this->arResult['SETTINGS'][Options::CONFIRM_OPEN];
        $this->arResult['SETTINGS']['TF_LOCATION_JQUERY_INCLUDE']     = strlen($this->arResult['SETTINGS'][Options::INCLUDE_JQUERY]) ? 'Y' : 'N';
        $this->arResult['SETTINGS']['list-desktop-padding']           = $this->arResult['SETTINGS'][Options::LIST_DESKTOP_PADDING_TOP];
        $this->arResult['SETTINGS']['list-mobile-padding']            = $this->arResult['SETTINGS'][Options::LIST_MOBILE_PADDING_TOP];
        $this->arResult['SETTINGS']['TF_LOCATION_LOAD_LOCATIONS']     = $this->arResult['SETTINGS'][Options::LIST_LOCATIONS_LOAD];
        $this->arResult['SETTINGS']['TF_LOCATION_RELOAD']             = $this->arResult['SETTINGS'][Options::LIST_RELOAD_PAGE];
        $this->arResult['SETTINGS']['TF_LOCATION_SHOW_VILLAGES']      = $this->arResult['SETTINGS'][Options::LIST_SHOW_VILLAGES];

        $this->arParams['LOAD_TYPE'] = $this->arResult['SETTINGS']['TF_LOCATION_LOAD_LOCATIONS'];

        // add font
        Main\Page\Asset::getInstance()->addString('<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin,cyrillic">');
    }

    /**
     *
     */
    protected function includeGoogleFonts()
    {
        $fonts = array_unique(
            [
                Options::getListTitleFontFamily(),
                Options::getListItemsFontFamily()
            ]
        );

        foreach ($fonts as $font) {
            $font = trim($font);
            if (!mb_strlen($font)) {
                continue;
            }

            $font = str_replace(' ', '+', $font);

            Main\Page\Asset::getInstance()->addString('<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=' . $font . ':400,700&subset=latin,cyrillic">');
        }
    }

    /**
     * @return mixed|void|null
     */
    public function executeComponent()
    {
        try {
            $this->setFrameMode(true);
            $this->checkParams();
            $this->getResult();

            if (Options::isCapabilityMode()) {
                $this->addCapabilityResult();
            }

            if (Options::isUseGoogleFonts()) {
                $this->includeGoogleFonts();
            }

            $this->includeComponentTemplate();

            return $this->arResult;
        } catch (Exception $e) {
            ShowError($e->getMessage());
        }
    }
}
