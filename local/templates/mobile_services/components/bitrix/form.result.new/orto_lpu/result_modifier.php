<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$arGroups = array(15, 16);

$rsUser = CUser::GetList(($by="LAST_NAME"), ($order="asc"), array("GROUPS_ID" => $arGroups, "ACTIVE" => "Y"), array
("FIELDS"
    => array('ID', 'LAST_NAME', 'LOGIN')));

$arUserInfo = array();

while ($arUser = $rsUser->Fetch()) {

    $arUserInfo[] = array(
        'ID'=> $arUser['ID'],
        'NAME' => (!empty($arUser['LAST_NAME']) ? $arUser['LAST_NAME'] : $arUser['LOGIN']),
        'SELECTED' => $USER->GetID() == $arUser['ID'] ? 'Y' : 'N',
    );
}

if (!empty($arUserInfo)) {
    $arResult['QUESTIONS']['contractor']['STRUCTURE'] = $arUserInfo;
}

$rsUser = CUser::GetByID(intval(CUser::GetID()));
if ($arUser = $rsUser->Fetch()) {
    if (!empty($arUser['LAST_NAME'])) {
        $arResult['sResultCreator'] = $arUser['LAST_NAME'];
    } elseif (!empty($arUser['LOGIN'])) {
        $arResult['sResultCreator'] = $arUser['LOGIN'];
    }
}
