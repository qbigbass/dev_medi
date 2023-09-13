<?php

// При обновлении св-ва "Значение сортировки бренда" у элемента в ИБ "Бренды" изменяется значение сортировки у товаров этого бренда
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", "UpdateSortProduct");

function UpdateSortProduct(&$arFields) {

    if ($arFields['IBLOCK_ID'] == 1) {
        // Изменение в ИБ "Бренды"

        $brandId = $arFields['ID'];
        $oldSortProductBrand = 0;

        // Получим старое значение св-ва
        $objElemBrand = CIBlockElement::GetList(
            ["ID" => "ASC"],
            [
                "IBLOCK_ID" => "1",
                "ID" => $brandId
            ],
            false,
            false,
            ["ID", "IBLOCK_ID", "PROPERTY_SORT_PRODUCT_BRAND"]
        );

        while ($elem = $objElemBrand->Fetch()) {
            $oldSortProductBrand = $elem["PROPERTY_SORT_PRODUCT_BRAND_VALUE"]; // Старое значение в св-ве "Значение сортировки бренда"
        }

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
            // Изменилось значение св-ва у бренда, обновим значение сортировки у товаров этого бренда

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

            while ($elem = $objElemProduct->Fetch()) {
                $newSortValue = $elem["SORT"] - $oldSortProductBrand + $newValueSortBrand;
                $arrProducts[$elem["ID"]] = $newSortValue;
            }
        }

        if (!empty($arrProducts)) {
            $el = new CIBlockElement;
            foreach ($arrProducts as $productId => $sortValue) {
                $el->Update($productId, ["SORT" => $sortValue]);
            }
        }
    }
}
