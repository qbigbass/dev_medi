<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;

Loader::includeModule("highloadblock");

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

$GLOBALS['APPLICATION']->RestartBuffer();

global $USER;

file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/logs/logs_".date("Y_m_d").".txt", "REMOTE_ADDR  = " . print_r($_SERVER['REMOTE_ADDR'], true)."\n\n", FILE_APPEND);
file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/logs/logs_".date("Y_m_d").".txt", "postId  = " . print_r($_GET['postId'], true)."\n\n", FILE_APPEND);

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

        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/logs/logs_".date("Y_m_d").".txt", "arrLikesFromIp:\n\n" . print_r($arrLikesFromIp, true), FILE_APPEND);

        if (empty($arrLikesFromIp)) {
            // Добавляем лайк от пользователя (по IP проверка)
            $data = [
                "UF_ENC_POST_ID" => (int)$_GET['postId'],
                "UF_CLIENT_IP" => $_SERVER['REMOTE_ADDR']
            ];

            $entityDataClass::add($data);
            $result = 1;
        } else {
            // Снимаем лайк
        }
    }
}

echo json_encode($result);
die();