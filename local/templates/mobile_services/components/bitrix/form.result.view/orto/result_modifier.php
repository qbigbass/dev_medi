<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

//__($arResult, true);

$arGroups = array( 15, 16);

if (!empty($arResult['RESULT_USER_ID'])) {
    $rsUser = CUser::GetByID(intval($arResult['RESULT_USER_ID']));
    if ($arUser = $rsUser->Fetch()) {
        if (!empty($arUser['LAST_NAME'])) {
            $arResult['sResultCreator'] = $arUser['LAST_NAME'];
        } elseif (!empty($arUser['LOGIN'])) {
            $arResult['sResultCreator'] = $arUser['LOGIN'];
        }
    }
}


if (empty($arResult['RESULT']['contractor']['ANSWER_VALUE'][0]['USER_TEXT']))
{
    $arResult['RESULT']['contractor']['ANSWER_VALUE'][0]['USER_TEXT'] = $arResult['sResultCreator'];
}

$rsUser = CUser::GetList(($by="LAST_NAME"), ($order="asc"), array("GROUPS_ID" => $arGroups, "ACTIVE" => "Y"), array
("FIELDS"
    => array('ID', 'LAST_NAME', 'LOGIN')));

$arUserInfo = array();
$i = 0;
while ($arUser = $rsUser->Fetch())
{
    $arContrInfo[$i] = array(
        'ID'=> $arUser['ID'],
        'NAME' => (!empty($arUser['LAST_NAME']) ? $arUser['LAST_NAME'] : $arUser['LOGIN'])
    );

    if ($arResult['RESULT']['contractor']['ANSWER_VALUE'][0]['USER_TEXT'] != '') {
        $arContrInfo[$i]['SELECTED'] = $arResult['RESULT']['contractor']['ANSWER_VALUE'][0]['USER_TEXT'] == $arContrInfo[$i]['NAME'] ? 'Y' : 'N';
    }

    $i++;
}

if (!empty($arContrInfo)) {
    $arResult['ContrInfo'] = $arContrInfo;
}
