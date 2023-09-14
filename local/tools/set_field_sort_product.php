<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);

/* Скрипт для установки значений в поле "Сортировка" ИБ "Основной каталог товаров" */
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

set_time_limit(0);

\Bitrix\Main\Loader::includeModule('iblock');

// Получим товары и значения св-в: "Значение сортировки без метки без бренда", "Бренд", "Наши предложения"
$objElem = CIBlockElement::GetList(
    ["ID" => "ASC"],
    [
        "IBLOCK_ID" => "17"
    ],
    false,
    false,
    ["ID", "IBLOCK_ID", "PROPERTY_SORT_DEFAULT", "PROPERTY_ATT_BRAND", "PROPERTY_OFFERS"]
);

$arrProducts = [];
$arrIds = [];

while ($elem = $objElem->Fetch()) {
    $arrProducts[$elem["ID"]]["SORT_DEFAULT"] = $elem["PROPERTY_SORT_DEFAULT_VALUE"];
    $arrProducts[$elem["ID"]]["BRAND_ID"] = $elem["PROPERTY_ATT_BRAND_VALUE"];

    if (!empty($elem["PROPERTY_OFFERS_VALUE"])) {
        $arrProducts[$elem["ID"]]["SIGNS"] = $elem["PROPERTY_OFFERS_VALUE"];
    }

    $arrIds[] = $elem["ID"];
}

// Найдем все подразделы раздела "Ортопедическая обувь" (ID=88)
$arrSubSections = getSubSectionsSection(88);
// Найдем разделы к которым принадлежат найденные товары
$arrElemGroupSections = getGroupsElements($arrIds);

// Объединим сортировку товара и принадлежность товара к разделу "Обувь"
if (!empty($arrElemGroupSections)) {
    foreach ($arrElemGroupSections as $productId => $arrSections) {
        $isShoes = false;
        $arrProducts[$productId]["IS_SHOES"] = "N";
        foreach ($arrSections as $sectionId) {
            if (in_array($sectionId, $arrSubSections)) {
                $isShoes = true;
                break;
            }
        }
        if ($isShoes) {
            $arrProducts[$productId]["IS_SHOES"] = "Y";
        }
    }
}

// Получим бренды со значением св-ва "Значение сортировки бренда"
$objElem = CIBlockElement::GetList(
    ["ID" => "ASC"],
    [
        "IBLOCK_ID" => "1"
    ],
    false,
    false,
    ["ID", "IBLOCK_ID", "PROPERTY_SORT_PRODUCT_BRAND"]
);
$arrBrands = [];

while ($elem = $objElem->Fetch()) {
    $arrBrands[$elem["ID"]] = $elem["PROPERTY_SORT_PRODUCT_BRAND_VALUE"];
}

// Рассчитаем итоговое значение сортировки для каждого товара
if (!empty($arrProducts)) {

    foreach ($arrProducts as $productId => $data) {
        $newSortValue = $data["SORT_DEFAULT"]; // Значение из св-ва "Значение сортировки без метки без бренда"
        if ($data["IS_SHOES"] == "Y") {
            // Вкл. в расчет сортировку по бренду, которому принадлежит товар
            $newSortValue += $arrBrands[$data["BRAND_ID"]];
        }
        if (!empty($data["SIGNS"])) {
            // Вкл. в расчет сортировку по признаку (Наши предложения)
            foreach ($data["SIGNS"] as $signId => $signName) {
                $newSortValue += SORT_SIGN[$signId];
            }
        }
        $arrProducts[$productId]["SORT"] = $newSortValue;
    }

    $el = new CIBlockElement;
    foreach ($arrProducts as $productId => $data) {
        $el->Update($productId, ["SORT" => $data["SORT"]]);
    }
}