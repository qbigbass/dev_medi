<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->AddHeadString('<meta property="og:title" content="'.$arResult["NAME"].'" />');
$APPLICATION->AddHeadString('<meta property="og:type" content="website" />');
$APPLICATION->AddHeadString('<meta property="og:url" content="'.(CMain::IsHTTPS() ? "https://" : "http://").SITE_SERVER_NAME.$arResult["DETAIL_PAGE_URL"].'" />');
if(!empty($arResult["IPROPERTY_VALUES"]['ELEMENT_META_DESCRIPTION'])):
	$APPLICATION->AddHeadString('<meta property="og:description" content="'.strip_tags(html_entity_decode($arResult["IPROPERTY_VALUES"]['ELEMENT_META_DESCRIPTION'], ENT_QUOTES)).'" />');
elseif(!empty($arResult["PREVIEW_TEXT"])):
	$APPLICATION->AddHeadString('<meta property="og:description" content="'.strip_tags(html_entity_decode($arResult["PREVIEW_TEXT"], ENT_QUOTES)).'" />');
endif;
if(!empty($arResult["PREVIEW_PICTURE"]["SRC"])){
	$APPLICATION->AddHeadString('<meta property="og:image" content="'.(CMain::IsHTTPS() ? "https://" : "http://").SITE_SERVER_NAME.$arResult["PREVIEW_PICTURE"]["SRC"].'" />');
}
?>
<script>
window.dataLayer = window.dataLayer || [];
dataLayer.push({
'ecommerce': {
 'promoClick': {
   'promotions': [{
     'id': 'encyclopedia<?=$arResult['ID']?>',
     'name': '<?=$arResult["DETAIL_PAGE_URL"]?>',
     'creative': '<?=$arResult["NAME"]?>',
   }]
 }
},
'event': 'gtm-ee-event',
'gtm-ee-event-category': 'Enhanced Ecommerce',
'gtm-ee-event-action': 'Promotion Clicks',
'gtm-ee-event-non-interaction': 'False',
});

</script>
<?php
// Получим итого кол-во лайков у статьи
$curCntLikesCached = $arResult["LIKES_CNT"];
$objElem = CIBlockElement::GetList(
    ["ID" => "ASC"],
    [
        "IBLOCK_ID" => "3",
        "ID" => $arResult['ID']
    ],
    false,
    false,
    ["ID", "IBLOCK_ID", "PROPERTY_LIKES_CNT"]
);

while ($elem = $objElem->Fetch()) {
    $curCntLikes = $elem["PROPERTY_LIKES_CNT_VALUE"];
}

if ($curCntLikesCached !== $curCntLikes) {?>
    <script>
        $('.head_like_cnt').html('<?=$curCntLikes?>'); // Лайк в шапке статьи
        $('.like .like-count').html('<?=$curCntLikes?>'); // Лайк в футере статьи
    </script><?php
}