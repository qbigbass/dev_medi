<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if ($_REQUEST["action"] == 'getLocation')
{
    CModule::IncludeModule('sale');

    $locationId = $_REQUEST['lid'];

    $arLocation = CSaleLocation::GetByID($locationId);

    if($arLocation['CITY_NAME'] != '') {
        echo $arLocation['CITY_NAME'];
    }
}
