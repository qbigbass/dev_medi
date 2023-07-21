<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/general/admin_tool.php");

IncludeModuleLangFile(__FILE__);

$saleModulePermissions = $APPLICATION->GetGroupRight("vampirus.yandexkassa");
if($saleModulePermissions == "D") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}
ClearVars("l_");

CModule::IncludeModule("vampirus.yandexkassa");
CModule::IncludeModule("sale");


$arUserGroups = $USER->GetUserGroupArray();
$intUserID = intval($USER->GetID());

$sTableID = "vampirus_yandexkassa";

$oSort = new CAdminSorting($sTableID, "ORDER_ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arPost = array();
$action_success = false;
if(is_array($_POST) && count($_POST)) {
    $arPost = $_POST;
    $result = CVampiRUSYandexKassaPayment::sendRequest($arPost);
    if(!$result){
        $lAdmin->AddUpdateError(CVampiRUSYandexKassaPayment::getErrorMsg());
    }
}

$arFilterFields = array(
    "filter_order_id",
);

$lAdmin->InitFilter($arFilterFields);
$arFilter = array();
if(IntVal($filter_order_id)>0) $arFilter["ORDER_ID"] = IntVal($filter_order_id);

$arFilterFieldsTmp = array(
    "filter_order_id" => GetMessage("VAMPIRUS.YANDEXKASSA_ORDER_NUMBER"),
);

$oFilter = new CAdminFilter(
    $sTableID."_filter",
    $arFilterFieldsTmp
);

$arHeaders = array(
    array("id"=>"ORDER_ID","content"=>GetMessage('VAMPIRUS.YANDEXKASSA_ORDER_NUMBER'), "sort"=>false, "default"=>true),
    array("id"=>"INVOICE_ID","content"=>GetMessage('VAMPIRUS.YANDEXKASSA_INVOICE_ID'), "sort"=>false, "default"=>true),
    array("id"=>"AMOUNT","content"=>GetMessage('VAMPIRUS.YANDEXKASSA_ORDER_SUM'), "sort"=>false, "default"=>true),
    array("id"=>"DATE","content"=>GetMessage('VAMPIRUS.YANDEXKASSA_DATE'), "sort"=>false, "default"=>true),
    array("id"=>"STATUS","content"=>GetMessage('VAMPIRUS.YANDEXKASSA_STATUS'), "sort"=>false, "default"=>true),
    array("id"=>"ACTION","content"=>GetMessage('VAMPIRUS.YANDEXKASSA_ACTION'), "sort"=>false, "default"=>true),
);

$lAdmin->AddHeaders($arHeaders);


    //array("nPageSize"=>CAdminResult::GetNavSize($sTableID))

$dbOrderList = CVampiRUSYandexKassaPayment::getInvoiceList($arFilter);



$dbOrderList = new CAdminResult($dbOrderList, $sTableID);
$dbOrderList->NavStart();

$lAdmin->NavText($dbOrderList->GetNavPrint(""));

while ($arOrder = $dbOrderList->NavNext(true, "f_"))
{
    $order = CSaleOrder::GetByID($arOrder['ORDER_ID']);
    $row =& $lAdmin->AddRow($f_ID, $arOrder, "sale_order_view.php?ID=".$arOrder['ORDER_ID']."&lang=".LANGUAGE_ID.GetFilterParams("filter_"));

    $idTmp = '<a href="/bitrix/admin/sale_order_view.php?ID='.$arOrder["ORDER_ID"].'" title="'.GetMessage("VAMPIRUS.YANDEXKASSA_VIEW_ORDER").'">'.$order['ACCOUNT_NUMBER'].'</a>';

    $row->AddField("ORDER_ID", $idTmp);
    $status = CVampiRUSYandexKassaPayment::getStatusName($arOrder["STATUS"]);
    $row->AddField("STATUS", $status);

    $action = '';
    if(in_array($arOrder["STATUS"], array(CVampiRUSYandexKassaPayment::STATUS_CONFIRMED, CVampiRUSYandexKassaPayment::STATUS_PREAUTH))) {
    $action = '';
        if(in_array($arOrder["STATUS"], array(CVampiRUSYandexKassaPayment::STATUS_CONFIRMED))) {
            $action .= '
            '.GetMessage('VAMPIRUS.YANDEXKASSA_RETURN_CAUSE').' <input name="cause['.$arOrder['ID'].']" value=""><br>
                <button class="adm-btn" type="submit" name="action['.$arOrder['ID'].']" value="'.CVampiRUSYandexKassaPayment::STATUS_RETURNED.'">'.GetMessage('VAMPIRUS.YANDEXKASSA_ACTION_RETURN').'</button>';
        }
         if(in_array($arOrder["STATUS"],array(CVampiRUSYandexKassaPayment::STATUS_PREAUTH))) {
            $action .= '
                <button class="adm-btn" type="submit" name="action['.$arOrder['ID'].']" value="'.CVampiRUSYandexKassaPayment::STATUS_CANCELLED.'">'.GetMessage('VAMPIRUS.YANDEXKASSA_ACTION_CANCEL').'</button>';
            $action .= '
                <button class="adm-btn" type="submit" name="action['.$arOrder['ID'].']" value="'.CVampiRUSYandexKassaPayment::STATUS_CONFIRMED.'">'.GetMessage('VAMPIRUS.YANDEXKASSA_ACTION_CONFIRM').'</button>';
        }
    }
    $row->AddField('ACTION', $action);

}

$lAdmin->CheckListMode();
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

$APPLICATION->SetTitle(GetMessage("VAMPIRUS.YANDEXKASSA_TRANSACTION_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<?
$oFilter->Begin();
?>
<tr>
    <td><?=GetMessage("VAMPIRUS.YANDEXKASSA_ORDER_NUMBER")?>:</td>
    <td>
        <input type="text" name="filter_order_id" value="<?echo htmlspecialcharsbx($filter_order_id)?>" size="40">
    </td>
</tr>
<?
$oFilter->Buttons(
    array(
        "table_id" => $sTableID,
        "url" => $APPLICATION->GetCurPage(),
        "form" => "find_form"
    )
);
$oFilter->End();
?>
</form>
<?
$lAdmin->DisplayList();
?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>