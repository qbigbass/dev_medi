<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
$APPLICATION->SetAdditionalCSS($templateFolder.'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css');

global $APPLICATION;
if (strlen($arResult['LIBS'])>0)
	$arResult['LIBS_ITEMS'] = unserialize($arResult['LIBS']);
if (is_array($arResult['LIBS_ITEMS']) && count($arResult['LIBS_ITEMS'])>0)
	CJSCore::Init($arResult['LIBS_ITEMS']);
?>