<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;

Loader::includeModule("highloadblock");

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

$GLOBALS['APPLICATION']->RestartBuffer();

global $USER;

if ($_GET['postId']) {
    $hlBlock = HL\HighloadBlockTable::getList([
        'filter' => ['=NAME' => 'LikesEncPosts']
    ])->fetch();

    if (!empty($hlBlock)) {
        $hlBlockId = $hlBlock["ID"];
        $entity = HL\HighloadBlockTable::compileEntity($hlBlock);
        $entityDataClass = $entity->getDataClass();

        $rsData = $entityDataClass::getList([
            "select" => ["*"],
            "order" => ["ID" => "ASC"],
            "filter" => [
                "UF_ENC_POST_ID" => (int)$_GET['postId'],
                "UF_CLIENT_IP" => $_SERVER['REMOTE_ADDR']
            ],
            "limit" => 1
        ]);

        $arrLikesFromIp = [];

        while ($arData = $rsData->Fetch()) {
            $arrLikesFromIp[] = $arData["UF_ENC_POST_ID"];
        }

        if (empty($arrLikesFromIp)) {
            // Добавляем лайк от пользователя (по IP проверка) в HL
            $data = [
                "UF_ENC_POST_ID" => (int)$_GET['postId'],
                "UF_CLIENT_IP" => $_SERVER['REMOTE_ADDR']
            ];

            $entityDataClass::add($data);

            // Изменяем кол-во в св-ве "Количество лайков" в ИБ "Энциклопедия"
            $objElem = CIBlockElement::GetList(
                ["ID" => "ASC"],
                [
                    "IBLOCK_ID" => "3",
                    "ID" => (int)$_GET['postId']
                ],
                false,
                false,
                ["ID", "IBLOCK_ID", "PROPERTY_LIKES_CNT"]
            );

            $newCntLikes = 0;
            while ($elem = $objElem->Fetch()) {
                $curCntLikes = $elem["PROPERTY_LIKES_CNT_VALUE"];
                $newCntLikes = (int)$curCntLikes + 1;
            }

            if ($newCntLikes > 0) {
                CIBlockElement::SetPropertyValuesEx((int)$_GET['postId'], "3", array("LIKES_CNT" => $newCntLikes));
            }

            $result = 1;
        } else {
            // Лайк от пользователя уже имеется
            $result = 2;
        }
    }
}

echo json_encode($result);
die();