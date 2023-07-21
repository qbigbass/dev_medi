<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


$action = '';
if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], array( 'form_send'))) {
    $action = strval($_REQUEST['action']);
} else
    die();

if ($action == 'form_send') {
    /*if(!empty($_REQUEST) && !defined("BX_UTF")){
        foreach ($_REQUEST as $key => $nextValue) {
            if(is_array($nextValue)){
                foreach ($_REQUEST[$key] as $kkey => $nextElement) {
                    if ($kkey != 'form_file_60' && $kkey != 'form_file_88' && $kkey != 'form_file_118')
                    $_REQUEST[$key][$kkey] = iconv("UTF-8", "WINDOWS-1251//IGNORE",  $nextElement);
                }
            }else{
                if ($kkey != 'form_file_60' && $kkey != 'form_file_88' && $kkey != 'form_file_118')
                $_REQUEST[$key] = iconv("UTF-8", "WINDOWS-1251//IGNORE",  $nextValue);
            }
        }
    }*/

    $APPLICATION->IncludeComponent("bitrix:form.result.new", "ajax", Array(
        "CACHE_TIME" => "0",
            "CACHE_TYPE" => "N",
            "CHAIN_ITEM_LINK" => "",
            "CHAIN_ITEM_TEXT" => "",
            "EDIT_URL" => "",
            "IGNORE_CUSTOM_TEMPLATE" => "N",
            "LIST_URL" => "",
            "SEF_MODE" => "N",
            "SUCCESS_URL" => "",
            "USE_EXTENDED_ERRORS" => "Y",
            "WEB_FORM_ID" => intval($_REQUEST['WEB_FORM_ID']),
            "COMPONENT_TEMPLATE" => "",
            "VARIABLE_ALIASES" => array(
                "WEB_FORM_ID" => "WEB_FORM_ID",
                "RESULT_ID" => "RESULT_ID",
            )
        ),
        false
    );
}
