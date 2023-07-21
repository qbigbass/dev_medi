<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)     die();

// Яндекс.Карты
$APPLICATION->AddHeadScript('https://api-maps.yandex.ru/2.1?lang=ru_RU&apikey=391d1c41-5055-400d-8afc-49ee21c8f4a1&load=package.full');
//__($arResult);

if (!empty($arResult['UF_STORE_META_DECRIPTION'])){

    $APPLICATION->SetPageProperty("description", $arResult['UF_STORE_META_DECRIPTION']);
}
if (!empty($arResult['UF_STORE_PUBLIC_NAME'])){

    $APPLICATION->SetPageProperty("title", $arResult['UF_STORE_PUBLIC_NAME']);
}

if (!empty($arResult['UF_STORE_META_KEYWORDS'])){

    $APPLICATION->SetPageProperty("keywords", $arResult['UF_STORE_META_KEYWORDS']);
}
