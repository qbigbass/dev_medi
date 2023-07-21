<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

//__($arResult, true);

$arGroups = array( 17, 16);

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

if (!empty($arResult['arrVALUES']['76'][105]['USER_TEXT']))
{
    $shipment =  explode("\r\n", $arResult['arrVALUES']['76'][105]['USER_TEXT']);
    $arResult['SHIPMENTS'] = [];
    if (!empty($shipment))
    {
        foreach ($shipment as $key => $value) {
            if (!empty($value))
            {
                $arResult['SHIPMENTS'][] = explode("|", $value);
            }
        }
    }
}
if (!empty($arResult['arrVALUES']['77'][106]['USER_TEXT']))
{
    $getting =  explode("\r\n", $arResult['arrVALUES']['77'][106]['USER_TEXT']);
    $arResult['GETTING'] = [];
    if (!empty($getting))
    {
        foreach ($getting as $key => $value) {
            if (!empty($value))
            {
                $arResult['GETTING'][] = explode("|", $value);
            }
        }
    }
}
