<?php
/* Скрипт для установки количества (рандом) лайков постам из энциклопедии */
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

$objElem = CIBlockElement::GetList(
    ["ID" => "ASC"],
    [
        "IBLOCK_ID" => "3",
        "PROPERTY_LIKES_CNT" => false
    ],
    false,
    false,
    ["ID", "IBLOCK_ID"]
);

$arrIds = [];

while ($elem = $objElem->Fetch()) {
    $arrIds[$elem["ID"]] = $elem["ID"];
}

if (!empty($arrIds)) {
    foreach ($arrIds as $id) {
        $cntLikes = random_int(50, 150);
        CIBlockElement::SetPropertyValuesEx($id, "3", array("LIKES_CNT" => $cntLikes));
    }
}