<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var string $componentPath */

use TwoFingers\Location\Options;
use TwoFingers\Location\Settings,
    Bitrix\Main\Loader,
    \TwoFingers\Location\Storage;

if(!Loader::IncludeModule('twofingers.location'))
{
    ShowError('Module twofingers.location not installed');
    return;
}

$settings = Settings::getList();

// @TODO: ... prepare to use params
$arParams['CONFIRM_POPUP_ACTION'] = trim($arParams['CONFIRM_POPUP_ACTION']);
if (!in_array($arParams['CONFIRM_POPUP_ACTION'], ['Y', 'U', 'A', 'N']))
    $arParams['CONFIRM_POPUP_ACTION'] = $settings['TF_LOCATION_SHOW_CONFIRM_POPUP'];

$arParams['LOCATIONS_POPUP_ACTION'] = trim($arParams['LOCATIONS_POPUP_ACTION']);
if (!in_array($arParams['LOCATIONS_POPUP_ACTION'], ['Y', 'N']))
    $arParams['LOCATIONS_POPUP_ACTION'] = $settings['TF_LOCATION_ONUNKNOWN'];

$arParams['FAVORITES_POSITION'] = trim($arParams['FAVORITES_POSITION']);
if (!in_array($arParams['FAVORITES_POSITION'], ['above-search', 'under-search', 'left-locations', 'right-locations']))
    $arParams['FAVORITES_POSITION'] = Options::getFavoritesPosition('', 'left-locations');

$arParams['LOAD_TYPE'] = trim($arParams['LOAD_TYPE']);
if (!in_array($arParams['LOAD_TYPE'], ['all', 'cities', 'defaults']))
    $arParams['LOAD_TYPE'] = $settings['TF_LOCATION_LOAD_LOCATIONS'];

$arResult['CALL_CONFIRM_POPUP'] = 'N';
$arResult['CALL_LOCATION_POPUP']= 'N';

// getting new data
if (Storage::getNeedCheck() == 'Y')
{
    if (($arParams['SHOW_CONFIRM_POPUP'] == 'Y')
        || (($arParams['SHOW_CONFIRM_POPUP'] == 'U') && (Storage::getConfirmPopupClosed() != 'Y')))
    {
        $arResult['CALL_CONFIRM_POPUP'] = 'Y';
    }
} elseif (($arParams['SHOW_CONFIRM_POPUP'] == 'A')
    && (Storage::getConfirmPopupClosed() != 'Y'))
{
    $arResult['CALL_CONFIRM_POPUP'] = 'Y';
}

// try to get info
if (Storage::isEmpty())
{
    $arResult['CITY_NAME']  = GetMessage("TF_LOCATION_CHOOSE");
    $arResult['CITY_ID']    = false;

    if (($arParams['LOCATIONS_POPUP_ACTION'] == 'Y') && ($arResult['CALL_CONFIRM_POPUP'] == 'N'))
        $arResult['CALL_LOCATION_POPUP'] = 'Y';

} else {
    $arResult['CITY_ID']    = Storage::getCityId();
    $arResult['CITY_NAME']  = Storage::getCityName();
}

$arResult['SETTINGS']       = $settings;
$arResult['COMPONENT_PATH'] = $componentPath;
$arResult['AJAX_SEARCH']    = in_array($arParams['LOAD_TYPE'], ['cities', 'defaults']);

$this->IncludeComponentTemplate();

return $arResult;
