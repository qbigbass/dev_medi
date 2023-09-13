<?php

// При обновлении св-ва "Значение сортировки бренда" у элемента в ИБ "Бренды" изменяется значение сортировки у товаров этого бренда
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", "UpdateSortProduct");

function UpdateSortProduct(&$arFields) {

    if ($arFields['IBLOCK_ID'] == 1) {
        // Изменение в ИБ "Бренды"

        $brandId = $arFields['ID'];

        // Получим старое значение св-ва
        $oldSortProductBrand = getSortProductBrand($brandId);

        $newValueSortBrand = 0;
        // Получим новое значение св-ва
        if (!empty($arFields["PROPERTY_VALUES"])) {
            foreach ($arFields["PROPERTY_VALUES"] as $propId => $arrValues) {
                if ($propId == 504) {
                    if (!empty($arrValues)) {
                        foreach ($arrValues as $key => $data) {
                            $newValueSortBrand = $data["VALUE"]; // Актуальное значение в св-ве "Значение сортировки бренда"
                        }
                    }
                }
            }
        }

        if ($oldSortProductBrand !== $newValueSortBrand) {
            // Изменилось значение св-ва у бренда. Обновим значение сортировки у товаров этого бренда если товар принадлежит разделу "Ортопедическая обувь" (ID=88)

            // Найдем все подразделы раздела "Ортопедическая обувь" (ID=88)
            $arrSubSections = getSubSectionsSection(88);

            // Найдем товары принадлежавшие этому бренду
            $objElemProduct = CIBlockElement::GetList(
                ["ID" => "ASC"],
                [
                    "IBLOCK_ID" => "17",
                    "PROPERTY_ATT_BRAND.ID" =>  $brandId
                ],
                false,
                false,
                ["ID", "IBLOCK_ID", "SORT"]
            );

            $arrProducts = [];
            $arrIds = [];

            while ($elem = $objElemProduct->Fetch()) {
                $arrProducts[$elem["ID"]]["SORT"] = $elem["SORT"]; // Текущее значение в поле "Сортировка" у товара
                $arrIds[] = $elem["ID"];
            }

            // Найдем разделы к которым принадлежат найденные товары
            $arrElemGroupSections = getGroupsElements($arrIds);

            // Для товаров, которые принадлежат разделу "Ортопедическая обувь" (ID=88), рассчитаем сортировку с учетом сортировки брендов
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

            $GLOBALS["NOT_RUN_UPDATE_SORT_PRODUCT_CATALOG"] = true; // Блокируем запуск обработчика события OnBeforeIBlockElementUpdate с функцией UpdateSortProductCatalog

            if (!empty($arrProducts)) {
                $el = new CIBlockElement;
                foreach ($arrProducts as $productId => $dataProduct) {
                    if ($dataProduct["IS_SHOES"] === "Y") {
                        $newSortValue = $dataProduct["SORT"] - $oldSortProductBrand + $newValueSortBrand;
                        $el->Update($productId, ["SORT" => $newSortValue]);
                    }
                }
            }
        }
    }
}
