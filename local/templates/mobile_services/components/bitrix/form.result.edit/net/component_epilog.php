<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$sResultCreator = '';
if (!empty($arResult['RESULT_USER_ID'])) {
    $rsUser = CUser::GetByID(intval($arResult['RESULT_USER_ID']));
    if ($arUser = $rsUser->Fetch()) {
        if (!empty($arUser['LAST_NAME'])) {
            $sResultCreator = $arUser['LAST_NAME'];
        } elseif (!empty($arUser['LOGIN'])) {
            $sResultCreator = $arUser['LOGIN'];
        }
    }
}

if (!empty($arResult['RESULT_ID'])) {
    $sPageTitle = 'Заявка №' . $arResult['RESULT_ID'];
    if(!empty($sResultCreator)) {
        //$sPageTitle .= " ($sResultCreator)";
    }
    
    $APPLICATION->SetPageProperty('title', $sPageTitle);
}