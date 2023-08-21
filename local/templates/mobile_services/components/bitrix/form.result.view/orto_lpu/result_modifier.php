<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

//__($arResult, true);

$arGroups = array(15, 16);

$rsUser = CUser::GetList(($by="LAST_NAME"), ($order="asc"), array("GROUPS_ID" => $arGroups, "ACTIVE" => "Y"), array
("FIELDS"
    => array('ID', 'LAST_NAME', 'LOGIN')));

$arUserInfo = array();
$i = 0;
while ($arUser = $rsUser->Fetch()) {

    $arAuthorInfo[$i] = array(
        'ID'=> $arUser['ID'],
        'NAME' => (!empty($arUser['LAST_NAME']) ? $arUser['LAST_NAME'] : $arUser['LOGIN'])
    );
    $arAuthorInfo[$i]['SELECTED'] = $arResult['RESULT']['author']['ANSWER_VALUE'][0]['USER_TEXT'] == $arAuthorInfo[$i]['NAME'] ? 'Y' : 'N';
    $arContrInfo[$i] = array(
        'ID'=> $arUser['ID'],
        'NAME' => (!empty($arUser['LAST_NAME']) ? $arUser['LAST_NAME'] : $arUser['LOGIN'])
    );
    $arContrInfo[$i]['SELECTED'] = $arResult['RESULT']['contractor']['ANSWER_VALUE'][0]['USER_TEXT'] == $arContrInfo[$i]['NAME'] ? 'Y' : 'N';

    $i++;
}

if (!empty($arAuthorInfo)) {
    $arResult['AuthorInfo'] = $arAuthorInfo;
}
if (!empty($arContrInfo)) {
    $arResult['ContrInfo'] = $arContrInfo;
}