<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent(
	"dresscode:catalog.store.detail",
	"salon",
	Array(
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"STORE" => $arResult["STORE"],
		"TITLE" => $arParams["TITLE"],
		"PATH_TO_ELEMENT" => $arResult["PATH_TO_ELEMENT"],
		"PATH_TO_LISTSTORES" => $arResult["PATH_TO_LISTSTORES"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"MAP_TYPE" => $arParams["MAP_TYPE"],
		"NOW_HOUR" => date("H"),
		"FILE_404" => "/news/404.php",
	),
	$component
);?>
