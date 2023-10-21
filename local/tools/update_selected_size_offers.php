<?php
/* Скрипт для обновления св-ва "Активная размерная характеристика" у всех ТП после импорта каталога из 1С */
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

// Получим все товары из ИБ "Основной каталог товаров"
$objElemProduct = CIBlockElement::GetList(
    [
        "ID" => "ASC"
    ],
    [
        "IBLOCK_ID" => "17",
        "ACTIVE" => "Y"
    ],
    false,
    false,
    [
        "ID",
        "IBLOCK_ID"
    ]
);

$arrProducts = [];

while ($elem = $objElemProduct->Fetch()) {
    $arrProducts[] = $elem["ID"];
}

// Получим все предложения с активной опцией "Активная размерная характеристики" для всех ранее полученных товаров
$arrOffersProducts = CCatalogSKU::getOffersList(
    $arrProducts,
    17,
    [
        "ACTIVE" => "Y",
        "PROPERTY_SELECTED_SIZE_CHARACT" => 16684
    ],
    [
        "ID",
        "IBLOCK_ID",
        "SORT"
    ]
);

if (!empty($arrOffersProducts)) {

    $GLOBALS["NOT_RUN_UPDATE_SELECTED_SIZE_OFFER"] = true; // Блокируем запуск обработчика события OnBeforeIBlockElementUpdate с функцией UpdateSelectedSizeOffer

    // Проверим у выбранных ТП доступность на складах в г.Москва
    $arrOffers = []; // Найденные ТП
    $arrOfferProduct = [];
    foreach ($arrOffersProducts as $productId => $offers) {
        $offerId = key($offers);
        $arrOffers[] = $offerId;
        $arrOfferProduct[$offerId] = $productId;
    }

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
            $offerId = key($arrOffersProducts[$productId]);
            $arrOffersShoes[] = $offerId;
        }
    }

    $arrOffersNotShoes = array_diff($arrOffers, $arrOffersShoes); // ТП из других разделов

    // Проверим наличие всех ТП на складах
    $sideId = 's1'; // Проверим доступность ТП по складам в г.Москва
    $arrStoreOfferShoes = [];
    $arrStoreOfferNotShoes = [];

    if (!empty($arrOffersShoes)) {
        // Проверим наличие всех ТП из раздела "Ортопедическая обувь" на складах в г.Москва
        $filter = [
            "ACTIVE" => "Y",
            "PRODUCT_ID" => $arrOffersShoes,
            "+SITE_ID" => $sideId,
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
        // Проверим наличие всех ТП из других разделов на складах в г.Москва
        $filter = [
            "ACTIVE" => "Y",
            "PRODUCT_ID" => $arrOffersNotShoes,
            "+SITE_ID" => $sideId,
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
    // Оставим только те ТП, которых нет на складах в г. Москва
    $arrOfferNotAvailable = []; // Для этих ТП нужно изменить активную размерную характеристику
    if (!empty($arrStoreOfferShoes)) {
        foreach ($arrStoreOfferShoes as $offerId => $storeAmountShoes) {
            if (in_array($offerId, $exceptionOffers) || ($storeAmountShoes === 0)) {
                $arrOfferNotAvailable[] = $offerId;
            }
        }
    }

    if (!empty($arrStoreOfferNotShoes)) {
        foreach ($arrStoreOfferNotShoes as $offerId => $storeAmountNotShoes) {
            if (in_array($offerId, $exceptionOffers) || ($storeAmountNotShoes === 0)) {
                $arrOfferNotAvailable[] = $offerId;
            }
        }
    }

    if (!empty($arrOfferNotAvailable)) {
        // Изменяем активную размерную характеристику
        foreach ($arrOfferNotAvailable as $offerId) {
            $currentSelectedSizeOfferId = $offerId;

            $skuProductId = $arrOfferProduct[$offerId];

            // Найдем все доступные ТП у товара $skuProductId
            $offersListProduct = CCatalogSKU::getOffersList(
                $skuProductId,
                0,
                [
                    'ACTIVE' => 'Y',
                    'CATALOG_AVAILABLE' => 'Y',
                    '!ID' => $offerId
                ],
                [
                    'ID',
                    'SORT'
                ],
                [
                    'CODE' => [
                        'SELECTED_SIZE_CHARACT'
                    ]
                ]
            );

            $arrOfferIds = [];

            if (!empty($offersListProduct)) {
                // Отсортируем найденные ТП по полю "Сортировка" по возрастанию
                foreach ($offersListProduct as $productId => &$arrSKU) {
                    foreach ($arrSKU as $id => $dataSku) {
                        $arrOfferIds[] = $id;
                    }
                    usort($arrSKU, function ($a, $b) {
                        return ($a['SORT'] - $b['SORT']);
                    });
                }
            }

            // Проверим принадлежность товара к разделу Обувь
            $isShoes = false;
            if (!empty($arrElemGroupSections[$skuProductId])) {
                foreach ($arrElemGroupSections[$skuProductId] as $productSectionId) {
                    if (in_array($productSectionId, $arrSubSections)) {
                        $isShoes = true;
                        break;
                    }
                }
            }

            $sideId = 's1'; // Проверим доступность ТП по складам в г.Москва

            if ($isShoes) {
                $filter = [
                    "ACTIVE" => "Y",
                    "PRODUCT_ID" => $arrOfferIds,
                    "+SITE_ID" => $sideId,
                    [
                        "LOGIC" => "OR",
                        ["UF_STORE" => true],
                        ["UF_SHOES_STORE" => true]
                    ]
                ];
            } else {
                $filter = [
                    "ACTIVE" => "Y",
                    "PRODUCT_ID" => $arrOfferIds,
                    "+SITE_ID" => $sideId,
                    "UF_STORE" => true,
                ];
            }

            $rsProps = CCatalogStore::GetList(
                array('TITLE' => 'ASC', 'ID' => 'ASC'),
                $filter,
                false,
                false,
                ["ID", "ACTIVE", "ELEMENT_ID", "PRODUCT_AMOUNT", "UF_STORE", "SITE_ID"]
            );

            $arrStoreOffer = [];
            while ($mStore = $rsProps->GetNext()) {
                $arrStoreOffer[$mStore['ELEMENT_ID']] += $mStore['PRODUCT_AMOUNT'];
            }

            // исключить онлайн продажу, только бронь в салоне
            $exceptionOffers = ["41078", "41079", "41080", "41081", "41082", "41083", "41084", "41085", "41086"];

            if (!empty($arrStoreOffer)) {
                foreach ($arrStoreOffer as $xmlId => $sumStoreAmount) {
                    if (in_array($xmlId, $exceptionOffers)) {
                        $arrStoreOffer[$xmlId] = 0;
                    }
                }

                // Найдем следующий по полю "Сортировка" доступный SKU
                unset($arrSKU);

                foreach ($offersListProduct as $productId => $arrOffers) {
                    foreach ($arrOffers as $index => $dataSku) {
                        $xmlId = $dataSku['ID'];

                        // Устанавливаем чекбокс у св-ва "Активная размерная характеристика" для первого доступного SKU
                        if ($arrStoreOffer[$xmlId] > 0) {

                            // Снимаем чекбокс в св-ве "Активная размерная характеристика" у текущего ТП
                            $propertyValues = [
                                "SELECTED_SIZE_CHARACT" => ''
                            ];

                            CIBlockElement::SetPropertyValuesEx($currentSelectedSizeOfferId, "19", $propertyValues);

                            // Устанавливаем чекбокс у св-ва "Активная размерная характеристика" для первого доступного SKU
                            $propertyValues = [
                                "SELECTED_SIZE_CHARACT" => 16684
                            ];
                            $nextActiveOfferId = $xmlId;

                            CIBlockElement::SetPropertyValuesEx($nextActiveOfferId, "19", $propertyValues);
                            break 2;
                        }
                    }
                }
            }
        }
    }
}