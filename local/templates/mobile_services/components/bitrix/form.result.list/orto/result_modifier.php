<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();


$arGroups = array(15, 16, 17);


$rsUser = CUser::GetList(($by="LAST_NAME"), ($order="asc"), array("GROUPS_ID" => $arGroups, "ACTIVE" => "Y"), array
("FIELDS"  => array('ID', 'LAST_NAME', 'LOGIN')));

$arUserInfo = array();
$i = 0;
while ($arUser = $rsUser->Fetch()) {

    $arContrInfo[$i] = array(
        'ID'=> $arUser['ID'],
        'NAME' => (!empty($arUser['LAST_NAME']) ? $arUser['LAST_NAME'] : $arUser['LOGIN'])
    );
    $arContrInfo[$i]['SELECTED'] = $arResult['__find']['find_MEDI_ORTO_contractor_USER_text'] == $arContrInfo[$i]['NAME'] ? 'Y' : 'N';

    $i++;
}

if (!empty($arContrInfo)) {
    $arResult['ContrInfo'] = $arContrInfo;
}
