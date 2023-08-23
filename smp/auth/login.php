<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
/*
// https://www.medi-salon.ru//salons/auth/login.php?authcode=$6$TVmqYVgKcLx8PwaN$g3puHGllek
global $USER;

$filter = Array
(
    "ACTIVE"    => "Y",
    "GROUPS_ID" => [29],
    //"UF_AUTH"   => $authcode
);
$rsUsers = CUser::GetList(($by="id"), ($order="desc"), $filter);
while ($arUser = $rsUsers->GetNext()) {

    if ($arUser['LOGIN'])
    {
        $fields = [];
        $authcode = substr(md5($arUser['EMAIL']), 0,30);

        $fields = Array(
            "UF_AUTH" => $authcode,
        );
        $USER->Update($arUser['ID'], $fields);

    }

}

die;*/
if (isset($_REQUEST['authcode']) && strlen($_REQUEST['authcode']) == 30) {
    $authcode = $_REQUEST['authcode'];
    
    $filter = array
    (
        "ACTIVE" => "Y",
        "GROUPS_ID" => [29],
        "UF_AUTH" => $authcode
    );
    $rsUsers = CUser::GetList(($by = "id"), ($order = "desc"), $filter);
    if ($arUser = $rsUsers->GetNext()) {
        $USER->Authorize($arUser['ID'], true);
        
    } else {
        CHTTP::SetStatus("500 Internal Server Error");
        die();
    }
}
LocalRedirect('/');
?>