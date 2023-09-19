<?php
/* Скрипт для установки/снятия меток "SALE"/"Новинка" (св-во Тэги) у товаров из категории "Ортопедическая обувь" в ИБ "Основной каталог товаров" */

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

// Найдем все подразделы раздела "Ортопедическая обувь" (ID=88)
$arrSubSections = getSubSectionsSection(88);

// Получим товары из раздела "Ортопедическая обувь"
$objElem = CIBlockElement::GetList(
    ["ID" => "ASC"],
    [
        "IBLOCK_ID" => "17",
        "SECTION_ID" => $arrSubSections,
        "ACTIVE" => "Y"
    ],
    false,
    false,
    ["ID", "IBLOCK_ID", "PROPERTY_OFFERS", "PROPERTY_TAGS", "CATALOG_GROUP_1", "CATALOG_GROUP_2"]
);

$arrProducts = [];

while ($elem = $objElem->Fetch()) {
    if (!empty($elem["PROPERTY_OFFERS_VALUE"])) {
        $arrProducts[$elem["ID"]]["SIGNS"] = $elem["PROPERTY_OFFERS_VALUE"];
    }
    $arrProducts[$elem["ID"]]["PRICE_MIN"] = $elem["CATALOG_PRICE_1"];
    $arrProducts[$elem["ID"]]["PRICE_MAX"] = $elem["CATALOG_PRICE_2"];
    $arrProducts[$elem["ID"]]["TAGS"] =  $elem["PROPERTY_TAGS_VALUE"];
}

if (!empty($arrProducts)) {
    
    $GLOBALS["NOT_RUN_UPDATE_SORT_PRODUCT_CATALOG"] = true; // Блокируем запуск обработчика события OnBeforeIBlockElementUpdate с функцией UpdateSortProductCatalog

    foreach ($arrProducts as $productId => $dataProps) {

        $arrPropTags = [];

        if ($dataProps["PRICE_MIN"] !=  $dataProps["PRICE_MAX"]) {
            $arrPropTags[] = 16359; // Sale
        }

        if (!empty($dataProps["SIGNS"])) {
            if (in_array('Новинка', $dataProps["SIGNS"])) {
                $arrPropTags[] = 16552; // Новинки
            }
        }

        if (!empty($dataProps["TAGS"])) {
            foreach ($dataProps["TAGS"] as $xmlId => $value) {
                if (!in_array($xmlId, [16359, 16552])) {
                    $arrPropTags[] = $xmlId;
                }
            }
        }

        $propertyValues = [
            "TAGS" => $arrPropTags
        ];

        CIBlockElement::SetPropertyValuesEx($productId, "17", $propertyValues);
    }
}