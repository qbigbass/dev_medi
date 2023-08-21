<?
$arParamsToDelete = array(
    "login",
    "login_form",
    "logout",
    "register",
    "forgot_password",
    "change_password",
    "confirm_registration",
    "confirm_code",
    "confirm_user_id",
    "logout_butt",
    "auth_service_id",
);

if (isset($_REQUEST['backurl'])){
    $currentUrl = str_replace( ['?backurl=', $APPLICATION->GetCurDir()], "", $APPLICATION->GetCurPageParam("", $arParamsToDelete, false));

    $arResult["BACKURL"] = urldecode($currentUrl);
}


$this->IncludeComponentTemplate();
