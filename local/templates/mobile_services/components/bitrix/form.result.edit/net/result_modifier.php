<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

//__($arResult, true);

$arGroups = array(15, 16, 17);

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

if (empty($arResult['arrVALUES']['form_text_60']))
{
    $arResult['arrVALUES']['form_text_60'] = $arResult['sResultCreator'];
}
if (!empty($arResult['arrVALUES']['form_textarea_105']))
{
    $shipment =  explode("\r\n", $arResult['arrVALUES']['form_textarea_105']);
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
if (!empty($arResult['arrVALUES']['form_textarea_106']))
{
    $getting =  explode("\r\n", $arResult['arrVALUES']['form_textarea_106']);
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

    if ($arResult['arrVALUES']['form_text_60'] != '') {
        $arContrInfo[$i]['SELECTED'] = $arResult['arrVALUES']['form_text_60'] == $arContrInfo[$i]['NAME'] ? 'Y' : 'N';
    }

    $i++;
}

if (!empty($arContrInfo)) {
    $arResult['ContrInfo'] = $arContrInfo;
}
