<?php
/* Скрипт для установки значений в св-во "Значение сортировки бренда" ИБ "Бренды" */
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");

$objElem = CIBlockElement::GetList(
    ["ID" => "ASC"],
    [
        "IBLOCK_ID" => "1"
    ],
    false,
    false,
    ["ID", "IBLOCK_ID", "NAME"]
);

$arrElem = [];

while ($elem = $objElem->Fetch()) {
    $arrElem[$elem["ID"]] = $elem["NAME"];
}

$arrDefaultSort = [
    "solidus" => 100500,
    "afs" => 90500,
    "jomos" => 80500,
    "frankenschuhe" => 70500,
    "helix" => 60500,
    "schawos" => 50500,
    "the flexx" => 40500,
    "memo" => 30500,
    "sursil ortho" => 20500,
    "orthoboom" => 10500
];

foreach ($arrElem as $id => $name) {
    $name = strtolower($name);
    if (array_key_exists($name, $arrDefaultSort)) {
        $sortValue = $arrDefaultSort[$name];
    } else {
        $sortValue = 0;
    }
    CIBlockElement::SetPropertyValuesEx($id, "1", array("SORT_PRODUCT_BRAND" => $sortValue));
}