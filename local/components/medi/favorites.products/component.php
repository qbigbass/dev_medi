<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $USER;

if ($USER->IsAuthorized()) {
    $idUser = $USER->GetID();
    $rsUser = CUser::GetByID($idUser);
    $arUser = $rsUser->Fetch();
    $arElements = $arUser['UF_FAVORITIES'];

    if (!empty($arElements)) {
        $arResult['CNT_ITEMS'] = count($arElements);
    }
}

$this->IncludeComponentTemplate();