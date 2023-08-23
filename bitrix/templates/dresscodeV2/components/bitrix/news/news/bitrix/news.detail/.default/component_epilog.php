<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->AddHeadString('<meta property="og:title" content="'.$arResult["NAME"].'" />');
$APPLICATION->AddHeadString('<meta property="og:type" content="website" />');
$APPLICATION->AddHeadString('<meta property="og:url" content="'.(CMain::IsHTTPS() ? "https://" : "http://").SITE_SERVER_NAME.$arResult["DETAIL_PAGE_URL"].'" />');
if(!empty($arResult["IPROPERTY_VALUES"]['ELEMENT_META_DESCRIPTION'])):
	$APPLICATION->AddHeadString('<meta property="og:description" content="'.strip_tags(html_entity_decode($arResult["IPROPERTY_VALUES"]['ELEMENT_META_DESCRIPTION'], ENT_QUOTES)).'" />');
elseif(!empty($arResult["PREVIEW_TEXT"])):
	$APPLICATION->AddHeadString('<meta property="og:description" content="'.strip_tags(html_entity_decode($arResult["PREVIEW_TEXT"], ENT_QUOTES)).'" />');
endif;
if(!empty($arResult["DETAIL_PICTURE"]["SRC"])){
	$APPLICATION->AddHeadString('<meta property="og:image" content="'.(CMain::IsHTTPS() ? "https://" : "http://").SITE_SERVER_NAME.$arResult["DETAIL_PICTURE"]["SRC"].'" />');
}
?>
