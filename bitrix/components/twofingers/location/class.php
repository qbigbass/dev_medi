<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage sale
 * @copyright 2001-2014 Bitrix
 */

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use TwoFingers\Location\Options;
use TwoFingers\Location\Settings;
use TwoFingers\Location\Storage;

Loc::loadMessages(__FILE__);

if(!Loader::IncludeModule('twofingers.location'))
{
    ShowError(Loc::getMessage('tfl__module-error'));
    return;
}

/**
 * Class TfLocationComponent
 */
class TfLocationComponent extends CBitrixComponent
{
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
        return $arParams;
    }

    /**
     * @throws Main\LoaderException
     * @throws Main\SystemException
     */
    protected function checkParams()
    {
        if (!Main\Loader::includeModule('twofingers.location'))
            throw new Main\SystemException(Loc::getMessage('tfl__module-error'));
    }

    /**
     * @return array
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     */
    protected function prepareSettings(): array
    {
        $settingsMap = Settings::getMap();
        $result = [];
        foreach ($settingsMap as $code => $settings)
        {
            if (!array_key_exists($code, $this->arParams))
            {
                $result[$code] = Options::getValue($code);
                continue;
            }

            switch ($settings['type'])
            {
                case 'TEXT':
                    $result[$code] = trim($this->arParams[$code]);
                    if (!strlen($result[$code]))
                        $result[$code] = Options::getValue($code);
                    break;
                case 'INT':
                    $result[$code] = intval($this->arParams[$code]);
                    if (!$result[$code])
                        $result[$code] = intval(Options::getValue($code));
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
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     */
    protected function getResult()
    {
        $this->arResult['CALL_CONFIRM_POPUP']   = 'N';
        $this->arResult['CALL_LOCATION_POPUP']  = 'N';
        $this->arResult['SETTINGS']             = $this->prepareSettings();

        if (!Storage::isConfirmPopupClosed())
        {
            if (Storage::isNeedCheck() && in_array($this->arResult['SETTINGS']['TF_LOCATION_SHOW_CONFIRM_POPUP'],  ['Y', 'U']))
            {
                $this->arResult['CALL_CONFIRM_POPUP'] = 'Y';
            }
            elseif ($this->arResult['SETTINGS']['TF_LOCATION_SHOW_CONFIRM_POPUP'] == 'A')
            {
                $this->arResult['CALL_CONFIRM_POPUP'] = 'Y';
            }
        }

        // try to get info
        if (Storage::isEmpty())
        {
            $this->arResult['CITY_NAME']  = Loc::getMessage("tfl__choose");
            $this->arResult['CITY_ID']    = false;

            if (($this->arResult['SETTINGS'][Options::LIST_OPEN_IF_NO_LOCATION] == 'Y')
                && ($this->arResult['CALL_CONFIRM_POPUP'] == 'N'))
            {
                $this->arResult['CALL_LOCATION_POPUP'] = 'Y';
            }
        }
        else
        {
            $this->arResult['CITY_ID']    = Storage::getCityId();
            $this->arResult['CITY_NAME']  = Storage::getCityName();
        }

        $this->arResult['COMPONENT_PATH'] = $this->getPath();
        $this->arResult['AJAX_SEARCH']    = in_array($this->arResult['SETTINGS']['TF_LOCATION_LOAD_LOCATIONS'], ['cities', 'defaults']);
    }

    /**
     *
     */
    protected function addCapabilityResult()
    {
        $this->arResult['PRIMARY_COLOR']        = $this->arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR'];
        $this->arResult['PRIMARY_BG']           = $this->arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG'];
        $this->arResult['PRIMARY_COLOR_HOVER']  = $this->arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR_HOVER'];
        $this->arResult['PRIMARY_BG_HOVER']     = $this->arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG_HOVER'];
        $this->arResult['SECONDARY_COLOR']      = $this->arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR'];
        $this->arResult['SECONDARY_BG']         = $this->arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG'];
        $this->arResult['SECONDARY_COLOR_HOVER']= $this->arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR_HOVER'];
        $this->arResult['SECONDARY_BG_HOVER']   = $this->arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG_HOVER'];

        $this->arResult['SETTINGS']['TF_LOCATION_DEFAULT_CITY']     = $this->arResult['SETTINGS'][Options::DEFAULT_LOCATION];
        $this->arResult['SETTINGS']['TF_LOCATION_COOKIE_LIFETIME']  = $this->arResult['SETTINGS'][Options::COOKIE_LIFETIME];
        $this->arResult['SETTINGS']['TF_LOCATION_CALLBACK']         = $this->arResult['SETTINGS'][Options::CALLBACK];
        $this->arResult['SETTINGS']['TF_LOCATION_MOBILE_WIDTH']     = $this->arResult['SETTINGS'][Options::LIST_MOBILE_BREAKPOINT];
        $this->arResult['SETTINGS']['TF_LOCATION_POPUP_RADIUS']     = $this->arResult['SETTINGS'][Options::LIST_DESKTOP_RADIUS];
        $this->arResult['SETTINGS']['TF_LOCATION_SXGEO_MEMORY']     = $this->arResult['SETTINGS'][Options::SX_GEO_MEMORY];
        $this->arResult['SETTINGS']['TF_LOCATION_HEADLINK_CLASS']   = $this->arResult['SETTINGS'][Options::LIST_LINK_CLASS];
        $this->arResult['SETTINGS']['TF_LOCATION_HEADLINK_TEXT']    = $this->arResult['SETTINGS'][Options::LIST_PRE_LINK_TEXT];
        $this->arResult['SETTINGS']['TF_LOCATION_ORDERLINK_CLASS']  = $this->arResult['SETTINGS'][Options::ORDER_LINK_CLASS];
        $this->arResult['SETTINGS']['favorites-position']           = $this->arResult['SETTINGS'][Options::LIST_FAVORITES_POSITION];
        $this->arResult['SETTINGS']['TF_LOCATION_ONUNKNOWN']        = $this->arResult['SETTINGS'][Options::LIST_OPEN_IF_NO_LOCATION];

        $this->arParams['LOAD_TYPE'] = $this->arResult['SETTINGS']['TF_LOCATION_LOAD_LOCATIONS'];
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
            if (Options::isCapabilityMode())
                $this->addCapabilityResult();

            $this->includeComponentTemplate();

            return $this->arResult;
        } catch (Exception $e) {
            ShowError($e->getMessage());
        }
    }
}
