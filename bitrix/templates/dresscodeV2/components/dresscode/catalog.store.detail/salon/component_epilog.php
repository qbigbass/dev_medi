<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)     die();

if (!empty($arResult['UF_STORE_META_DECRIPTION'])){

    $APPLICATION->SetPageProperty("description", $arResult['UF_STORE_META_DECRIPTION']);
}
if (!empty($arResult['UF_STORE_PUBLIC_NAME'])){

    $APPLICATION->SetPageProperty("title", $arResult['UF_STORE_PUBLIC_NAME']);
}

if (!empty($arResult['UF_STORE_META_KEYWORDS'])){

    $APPLICATION->SetPageProperty("keywords", $arResult['UF_STORE_META_KEYWORDS']);
}

$APPLICATION->AddHeadString('<link href="https://www.medi-salon.ru'.$APPLICATION->GetCurDir().'" rel="canonical">');
