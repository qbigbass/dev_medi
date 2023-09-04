<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$GLOBALS['APPLICATION']->RestartBuffer();

global $USER;

if ($_GET['id']) {
    if ($USER->IsAuthorized()) {
        $idUser = $USER->GetID();
        $rsUser = CUser::GetByID($idUser);
        $arUser = $rsUser->Fetch();
        $arElements = $arUser['UF_FAVORITIES'];

        if (!in_array($_GET['id'], $arElements)) {
            $arElements[] = $_GET['id'];
            $result = 1;
        } else {
            $key = array_search($_GET['id'], $arElements);
            unset($arElements[$key]);
            $result = 2;
        }

        $USER->Update($idUser, Array("UF_FAVORITIES" => $arElements));
    }
}

echo json_encode($result);
die();




