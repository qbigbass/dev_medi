<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div id="filterSalonsResult">
<?// Определяем текущую папку(город)
$cur_city_folder = explode("/", $APPLICATION->GetCurDir());
// Москва
if ($cur_city_folder[1] == 'salons')
	$cur_city = "";
// Остальные
 else
	$cur_city = $cur_city_folder[1];

if (($cur_city == "" && count($cur_city_folder) > 3) || ($cur_city != "" && count($cur_city_folder) > 4)){

    include($_SERVER['DOCUMENT_ROOT']."/404.php");
	return;
}
?>
<?$show_city = array_search($cur_city, $GLOBALS['medi']['sfolder']);

if ($cur_city == 'rgns') {$cur_city = ''; $show_city = 's1';}

$GLOBALS['arrFilter']['UF_CITY'] = $GLOBALS['medi']['site_order'][$show_city];
?>
<?$APPLICATION->IncludeComponent(
	"dresscode:catalog.store.list",
	"salons",
	Array(
        "FILTER_NAME" => $arParams['FILTER_NAME'],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"PHONE" => $arParams["PHONE"],
		"EMAIL" => $arParams["EMAIL"],
		"SCHEDULE" => $arParams["SCHEDULE"],
		"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
		"TITLE" => $arParams["TITLE"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"PATH_TO_ELEMENT" => $arResult["PATH_TO_ELEMENT"],
		"PATH_TO_LISTSTORES" => $arResult["PATH_TO_LISTSTORES"],
		"MAP_TYPE" => $arParams["MAP_TYPE"],
		"PROPS" => $arResult['UF_PROPS'],
		"NOW_HOUR" => date("H")
	),
	$component
);?>
</div>
