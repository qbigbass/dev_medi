<?php
/* Скрипт для разового заполнения св-ва "Активная размерная характеристика" у всех ТП */
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);

if (strlen($argv[1])) {
    if ($argv[1] === 'prod') {
        $_SERVER['DOCUMENT_ROOT'] = "/home/bitrix/www/";
    } elseif ($argv[1] === 'dev2') {
        $_SERVER['DOCUMENT_ROOT'] = "/home/bitrix/ext_www/dev2.medi-salon.ru/";
    }
} else {
    $_SERVER['DOCUMENT_ROOT'] = "E:/htdocs/medi-salon/domains/local.medi-salon.ru";
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

set_time_limit(0);

\Bitrix\Main\Loader::includeModule('iblock');

// Получим все товары
$objElemProduct = CIBlockElement::GetList(
    ["ID" => "ASC"],
    [
        "IBLOCK_ID" => "17",
    ],
    false,
    false,
    ["ID", "IBLOCK_ID"]
);

$arrProducts = [];

while ($elem = $objElemProduct->Fetch()) {
    $arrProducts[] = $elem["ID"];
}

$arrOffersProducts = CCatalogSKU::getOffersList(
    $arrProducts,
     17,
    ["ACTIVE" => "Y"],
    ["ID", "IBLOCK_ID", "SORT"],
    []
);

$arrOffersProductsSort = [];
$arrOffersUpdated = [];

if (!empty($arrOffersProducts)) {
    // Отсортируем ТП по полю "Сортировка"
    foreach ($arrOffersProducts as $productId => $arrOffers) {
        foreach ($arrOffers as $offerId => $offerData) {
            $arrOffersProductsSort[$productId][$offerData["SORT"]] = $offerId;
        }
        ksort($arrOffersProductsSort[$productId]);
        $arrOffersUpdated[] = current($arrOffersProductsSort[$productId]);
    }

    foreach ($arrOffersUpdated as $id) {
        $propertyValues = [
            "SELECTED_SIZE_CHARACT" => 16684
        ];

        CIBlockElement::SetPropertyValuesEx($id, "19", $propertyValues);
    }
}