<?
/*
Получение настроек для регионов
Сохраняем в глобальную переменную $GLOBALS['medi']
*/

$mysqli = new mysqli($DBHost, $DBLogin, $DBPassword, $DBName);
$mysqli->query('SET NAMES utf8');

$obRegion = $mysqli->query("SELECT * FROM medi_regions WHERE ACTIVE = 'Y' ORDER BY SORT ");
$medi_regions = [];
$medi_regions["phones"][0] = '8 800 511-77-39'; // телефон по умолчанию

while($arRegion = $obRegion->fetch_array())
{
    $medi_regions["region_sites"][$arRegion['SITE_ID']] = $arRegion['REGION'];
    $medi_regions["region_cities"][$arRegion['SITE_ID']] = $arRegion['CITY'];
    $medi_regions["sfolder"][$arRegion['SITE_ID']] = $arRegion['DIR'];
    $medi_regions["site_order"][$arRegion['SITE_ID']] = $arRegion['SORT'];
    $medi_regions["phones"][$arRegion['SITE_ID']] = $arRegion['PHONE'];
    $medi_regions["price"][$arRegion['SITE_ID']] = $arRegion['PRICE'];
    $medi_regions["max_price"][$arRegion['SITE_ID']] = $arRegion['MAX_PRICE'];
}

$medi_regions['location']['s1'] = '0000073738';
$medi_regions['location']['s2'] = '0000103664';
$medi_regions['location']['s4'] = '0000354349';
$medi_regions['location']['s5'] = '0000794760';
$medi_regions['location']['s6'] = '0000445112';
$medi_regions['location']['s7'] = '0000600317';
$medi_regions['location']['s8'] = '0000812044';

$GLOBALS['medi'] = $medi_regions;


$mysqli->close();
session_start();
if (isset($_SESSION['MEDI_SITE_ID']) && !defined("ADMIN_SECTION")) {
    define("SITE_ID", $_SESSION['MEDI_SITE_ID']);
}
elseif(!defined("ADMIN_SECTION")){
     define("SITE_ID", 's1');
}
session_write_close();
