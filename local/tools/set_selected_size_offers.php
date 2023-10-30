<?php

/**
 * Скрипт для разового заполнения св-ва "Активная размерная характеристика" и
 * "Активная размерная характеристика СПБ" у всех ТП
 */
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

    $GLOBALS["NOT_RUN_UPDATE_SELECTED_SIZE_OFFER"] = true; // Блокируем запуск обработчика события OnBeforeIBlockElementUpdate с функцией UpdateSelectedSizeOffer

    $arrOffers = []; // Найденные ТП
    $arrProductIdsSkuIds = []; // Массив товаров с Ids ТП
    // Отсортируем ТП по полю "Сортировка"
    foreach ($arrOffersProducts as $productId => $arrSkuIds) {
        foreach ($arrSkuIds as $offerId => $offerData) {
            $arrOffers[] = $offerId;
            $arrProductIdsSkuIds[$productId][] = $offerId;
            $arrOffersProductsSort[$productId][$offerData["SORT"]] = $offerId;
        }
        ksort($arrOffersProductsSort[$productId]);
        $arrOffersUpdated[] = current($arrOffersProductsSort[$productId]);
    }

    foreach ($arrOffersUpdated as $id) {
        $propertyValues = [
            "SELECTED_SIZE_CHARACT" => 16684
        ];

        // Заполняем св-во "Активная размерная характеристика" (Для Москвы и др городов кроме Санкт Петербурга)
        CIBlockElement::SetPropertyValuesEx($id, "19", $propertyValues);
    }

    // Логика заполнения св-ва "Активная размерная характеристика СПБ" (Для г. Санкт Петербург)
    // Найдем все подразделы раздела "Ортопедическая обувь" (ID=88)
    $arrSubSections = getSubSectionsSection(88);
    // Найдем разделы к которым принадлежат ранее полученные товары
    $arrElemGroupSections = getGroupsElements($arrProducts);
    // Проверим принадлежность каждого товара к разделу "Ортопедическая обувь"
    // Найдем товары которые принадлежат разделу "Ортопедическая обувь"
    $arrProductsShoes = []; // Продукты из раздела "Ортопедическая обувь"
    if (!empty($arrElemGroupSections)) {
        foreach ($arrElemGroupSections as $productId => $arrSections) {
            foreach ($arrSections as $sectionId) {
                if (in_array($sectionId, $arrSubSections)) {
                    $arrProductsShoes[] = $productId;
                    break;
                }
            }
        }
    }

    $arrProductsNotShoes = array_diff($arrProducts, $arrProductsShoes); // Товары из других разделов и товары которые не имеют предложений
    $arrOffersShoes = []; // ТП принадлежавшие разделу "Ортопедическая обувь"
    if (!empty($arrProductsShoes)) {
        foreach ($arrProductsShoes as $productId) {
            if (array_key_exists($productId, $arrProductIdsSkuIds)) {
                $arrOffersShoes = array_merge($arrProductIdsSkuIds[$productId], $arrOffersShoes);
            }
        }
    }

    $arrOffersNotShoes = array_diff($arrOffers, $arrOffersShoes); // ТП из других разделов

    // Проверим наличие всех ТП на складах
    $arrStoreOfferShoes = [];
    $arrStoreOfferNotShoes = [];

    if (!empty($arrOffersShoes)) {
        // Проверим наличие всех ТП из раздела "Ортопедическая обувь" на складах в г.Санкт-Петербург
        $filter = [
            "ACTIVE" => "Y",
            "PRODUCT_ID" => $arrOffersShoes,
            "SITE_ID" => "s2",
            [
                "LOGIC" => "OR",
                ["UF_STORE" => true],
                ["UF_SHOES_STORE" => true]
            ]
        ];

        $rsProps = CCatalogStore::GetList(
            array('TITLE' => 'ASC', 'ID' => 'ASC'),
            $filter,
            false,
            false,
            ["ID", "ACTIVE", "ELEMENT_ID", "PRODUCT_AMOUNT", "UF_STORE", "SITE_ID"]
        );

        while ($mStore = $rsProps->GetNext()) {
            $arrStoreOfferShoes[$mStore['ELEMENT_ID']] += $mStore['PRODUCT_AMOUNT'];
        }
    }

    unset($filter);

    if (!empty($arrOffersNotShoes)) {
        // Проверим наличие всех ТП из других разделов на складах в г.Санкт-Петербург
        $filter = [
            "ACTIVE" => "Y",
            "PRODUCT_ID" => $arrOffersNotShoes,
            "SITE_ID" => "s2",
            "UF_STORE" => true,
        ];

        $rsProps = CCatalogStore::GetList(
            array('TITLE' => 'ASC', 'ID' => 'ASC'),
            $filter,
            false,
            false,
            ["ID", "ACTIVE", "ELEMENT_ID", "PRODUCT_AMOUNT", "UF_STORE", "SITE_ID"]
        );

        while ($mStore = $rsProps->GetNext()) {
            $arrStoreOfferNotShoes[$mStore['ELEMENT_ID']] += $mStore['PRODUCT_AMOUNT'];
        }
    }

    // исключить онлайн продажу, только бронь в салоне
    $exceptionOffers = ["41078", "41079", "41080", "41081", "41082", "41083", "41084", "41085", "41086"];
    // Обнулим остаток на складе для ТП попадающих в исключения

    $arrOfferIdsAmount = []; // Итоговый массив ТП с остатками по складам в г. Санкт-Перербург
    if (!empty($arrStoreOfferShoes)) {
        foreach ($arrStoreOfferShoes as $offerId => $storeAmountShoes) {
            if (in_array($offerId, $exceptionOffers)) {
                $arrOfferIdsAmount[$offerId] = 0;
            } else {
                $arrOfferIdsAmount[$offerId] = $storeAmountShoes;
            }
        }
    }

    if (!empty($arrStoreOfferNotShoes)) {
        foreach ($arrStoreOfferNotShoes as $offerId => $storeAmountNotShoes) {
            if (in_array($offerId, $exceptionOffers)) {
                $arrOfferIdsAmount[$offerId] = 0;
            } else {
                $arrOfferIdsAmount[$offerId] = $storeAmountNotShoes;
            }
        }
    }

    // Добавим результаты по складам в итоговый массив
    foreach ($arrOffersProducts as $productId => $arrSkuIds) {
        foreach ($arrSkuIds as $offerId => $offerData) {
            $arrOffersProducts[$productId][$offerId]["AMOUNT"] = $arrOfferIdsAmount[$offerId];
        }
    }

    // Отсортируем ТП у каждого товара по полю "Сортировка"
    unset($arrSkuIds);
    $arrOffersProductsSort = [];
    $arrOffersUpdated = [];
    foreach ($arrOffersProducts as $productId => $arrSkuIds) {
        foreach ($arrSkuIds as $offerId => $offerData) {
            $arrOffersProductsSort[$productId][$offerData["SORT"]][$offerId] = $offerData["AMOUNT"];
        }
        ksort($arrOffersProductsSort[$productId]);
    }

    // Найдем для каждого товара только одно ТП для установки активной размерной характеристики для г. Санкт-Петербург
    if (!empty($arrOffersProductsSort)) {
        foreach ($arrOffersProductsSort as $productId => $arrSort) {
            foreach ($arrSort as $sortId => $arrSkuIds) {
                foreach ($arrSkuIds as $offerId => $amount) {
                    if ($amount > 0) {
                        $arrOffersUpdated[$productId] = $offerId;
                    }
                }
            }

            if (empty($arrOffersUpdated[$productId])) {
                // У всех ТП внутри продукта отсутствуют остатки
                // Выберем активную размерную размерную характеристику по полю "Сортировка"
                $firstSortOffer = array_key_first($arrOffersProductsSort[$productId]);
                $activeOfferId = array_key_first($arrOffersProductsSort[$productId][$firstSortOffer]);

                if ($activeOfferId > 0) {
                    $arrOffersUpdated[$productId] = $activeOfferId;
                }
            }
        }
    }

    if (!empty($arrOffersUpdated)) {
        foreach ($arrOffersUpdated as $productId => $skuId) {
            $propertyValues = [
                "SELECTED_SIZE_CHARACT_SPB" => 16685
            ];

            // Заполняем св-во "Активная размерная характеристика СБП" (Для г. Санкт Петербург)
            CIBlockElement::SetPropertyValuesEx($skuId, "19", $propertyValues);
        }
    }
}