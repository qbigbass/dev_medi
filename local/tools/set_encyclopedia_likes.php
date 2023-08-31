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

    $objProp = CIBlockProperty::GetList(array(), ['iBLOCK_ID' => 3, 'CODE' => 'LIKES_CNT']);
    $arrDataProp = $objProp->Fetch();

    if (!empty($arrDataProp)) {

        $el = new CIBlockElement;
        $PROP = array();

        foreach ($arrIds as $id) {
            $cntLikes = random_int(50, 150);
            $PROP[$arrDataProp['ID']] = $cntLikes;
            $arProps = Array(
                "PROPERTY_VALUES"=> $PROP,
            );
            $el->Update($id, $arProps);
        }
    }
}