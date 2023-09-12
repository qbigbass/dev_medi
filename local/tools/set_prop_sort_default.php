<?php
/* Скрипт для установки значений в св-во "Значение сортировки без метки без бренда" ИБ "Основной каталог товаров" */
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

\Bitrix\Main\Loader::includeModule('iblock');

$objElem = CIBlockElement::GetList(
    ["ID" => "ASC"],
    [
        "IBLOCK_ID" => "17",
        "PROPERTY_SORT_DEFAULT" => false
    ],
    false,
    false,
    ["ID", "IBLOCK_ID", "SORT"]
);

$arrElem = [];

while ($elem = $objElem->Fetch()) {
    $arrElem[$elem["ID"]] = $elem["SORT"];
}

$arrElemSortDefault = [];

if (!empty($arrElem)) {
    $maxSortValue = max($arrElem);

    foreach ($arrElem as $id => $sortValue) {
        $value = $maxSortValue - $sortValue;
        $arrElemSortDefault[$id] = $value;
    }

    foreach ($arrElemSortDefault as $id => $sortDefaultValue) {
        CIBlockElement::SetPropertyValuesEx($id, "17", array("SORT_DEFAULT" => $sortDefaultValue));
    }
}