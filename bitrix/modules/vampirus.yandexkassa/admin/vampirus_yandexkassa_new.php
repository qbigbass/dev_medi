<?php
use \Bitrix\Sale\Order;
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/include.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/general/admin_tool.php";

IncludeModuleLangFile(__FILE__);

$saleModulePermissions = $APPLICATION->GetGroupRight("vampirus.yandexkassa");
if ($saleModulePermissions == "D") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}
ClearVars("l_");

CModule::IncludeModule("vampirus.yandexkassa");
CModule::IncludeModule("sale");

$arUserGroups = $USER->GetUserGroupArray();
$intUserID    = intval($USER->GetID());

$sTableID = "vampirus_yandexkassa_new";

$oSort  = new CAdminSorting($sTableID, "ORDER_ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arPost         = array();
$action_success = false;
if (is_array($_POST) && count($_POST) && check_bitrix_sessid()) {
    $arPost = $_POST;
    $result = CVampiRUSYandexKassaPayment::sendApiRequest($arPost);
    if (!$result->isSuccess()) {
        $lAdmin->AddUpdateError(implode(", ", $result->getErrorMessages()));
    }
}

$statusList = [
    'waiting_for_capture' => GetMessage("VAMPIRUS.YANDEXKASSA_STATUS_WAITING_FOR_CAPTURE"),
    'succeeded' => GetMessage("VAMPIRUS.YANDEXKASSA_STATUS_SUCCEEDED"),
    'refunded' => GetMessage("VAMPIRUS.YANDEXKASSA_STATUS_REFUNDED"),
    'canceled' => GetMessage("VAMPIRUS.YANDEXKASSA_STATUS_CANCELED"),
];

$arFilterFields = array(
    "filter_ACCOUNT_NUMBER",
    "filter_date"   => GetMessage("VAMPIRUS.YANDEXKASSA_DATE"),
    "filter_id"     => GetMessage("VAMPIRUS.YANDEXKASSA_INVOICE_ID"),
    "filter_status" => GetMessage("VAMPIRUS.YANDEXKASSA_STATUS"),
);

$lAdmin->InitFilter($arFilterFields);
$arFilter = [];
if ($filter_ACCOUNT_NUMBER != '') {
    $arFilter["ACCOUNT_NUMBER"] = $filter_ACCOUNT_NUMBER;
}

if ($filter_status != '') {
    $arFilter["status"] = $filter_status;
}

if ($filter_id != '') {
    $arFilter["y.id"] = $filter_id;
}

if (strval(trim($filter_date_from)) != '') {
    if ($arDate = ParseDateTime($filter_date_from, CSite::GetDateFormat("FULL", SITE_ID))) {
        $arFilter["date>"] = date('Y-m-d H:i:s', mktime(0, 0, 0, $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
    } else {
        $filter_date_to = "";
    }
}

if (strval(trim($filter_date_to)) != '') {
    if ($arDate = ParseDateTime($filter_date_to, CSite::GetDateFormat("FULL", SITE_ID))) {
        $arFilter["date<"] = date('Y-m-d H:i:s', mktime(0, 0, 0, $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
    } else {
        $filter_date_to = "";
    }
}

$arFilterFieldsTmp = array(
    "filter_ACCOUNT_NUMBER" => GetMessage("VAMPIRUS.YANDEXKASSA_ORDER_NUMBER"),
    "filter_date"           => GetMessage("VAMPIRUS.YANDEXKASSA_DATE"),
    "filter_id"             => GetMessage("VAMPIRUS.YANDEXKASSA_INVOICE_ID"),
    "filter_status"         => GetMessage("VAMPIRUS.YANDEXKASSA_STATUS"),
);

$oFilter = new CAdminFilter(
    $sTableID . "_filter",
    $arFilterFieldsTmp
);

$arHeaders = array(
    array("id" => "order_id", "content" => GetMessage('VAMPIRUS.YANDEXKASSA_ORDER_NUMBER'), "sort" => 'order_id', "default" => true),
    array("id" => "id", "content" => GetMessage('VAMPIRUS.YANDEXKASSA_INVOICE_ID'), "sort" => false, "default" => true),
    array("id" => "amount", "content" => GetMessage('VAMPIRUS.YANDEXKASSA_ORDER_SUM'), "sort" => false, "default" => true),
    array("id" => "date", "content" => GetMessage('VAMPIRUS.YANDEXKASSA_DATE'), "sort" => 'order_id', "default" => true),
    array("id" => "status", "content" => GetMessage('VAMPIRUS.YANDEXKASSA_STATUS'), "sort" => false, "default" => true),
    array("id" => "rrn", "content" => 'RRN', "sort" => false, "default" => true),
    array("id" => "ACTION", "content" => GetMessage('VAMPIRUS.YANDEXKASSA_ACTION'), "sort" => false, "default" => true),
);

$lAdmin->AddHeaders($arHeaders);

//array("nPageSize"=>CAdminResult::GetNavSize($sTableID))
$arFilterOrder = [];

if (!empty($by)) {
    if (!isset($order) || !is_string($order)) {
        $order = "DESC";
    }

    $arFilterOrder[$by] = $order;
}

if ($del_filter == 'Y') {
    $arFilter = [];
}

$dbOrderList = CVampiRUSYandexKassaPayment::getTransactionList($arFilter, $arFilterOrder);

$dbOrderList = new CAdminResult($dbOrderList, $sTableID);
$dbOrderList->NavStart();

$lAdmin->NavText($dbOrderList->GetNavPrint(""));

while ($arOrder = $dbOrderList->NavNext(true, "f_")) {
    $order = Order::load($arOrder['order_id']);

    $orderAr = CSaleOrder::GetByID($arOrder['order_id']);

    $payment = null;
    if ($order) {
        $payment = $order->getPaymentCollection()->getItemById($arOrder['payment_id']);
    }

    $row = &$lAdmin->AddRow($f_ID, $arOrder, "sale_order_view.php?ID=" . $arOrder['order_id'] . "&lang=" . LANGUAGE_ID . GetFilterParams("filter_"));

    $idTmp = '<a href="/bitrix/admin/sale_order_view.php?ID=' . $arOrder["order_id"] . '" title="' . GetMessage("VAMPIRUS.YANDEXKASSA_VIEW_ORDER") . '">' . $orderAr['ACCOUNT_NUMBER'] . '</a>';

    $row->AddField("order_id", $idTmp);
    $status = GetMessage('VAMPIRUS.YANDEXKASSA_STATUS_' . strtoupper($arOrder["status"]));
    $row->AddField("status", $status);

    $action = '';

    if (in_array($arOrder["status"], array("succeeded", "waiting_for_capture")) && !is_null($payment) && $payment->isPaid()) {

        if (in_array($arOrder["status"], array("succeeded")) && $arOrder["refundable"]) {
            $action .= '
            ' . GetMessage('VAMPIRUS.YANDEXKASSA_RETURN_CAUSE') . ' <input type="text" name="cause[' . $arOrder['id'] . ']" value=""><br>
                <button class="adm-btn" type="submit" name="action[' . $arOrder['id'] . ']" value="return" onclick="return confirm(\''. GetMessage("VAMPIRUS.YANDEXKASSA_CONFIRM_REFUND") .'\')">' . GetMessage('VAMPIRUS.YANDEXKASSA_ACTION_RETURN') . '</button>';
        }
        if (in_array($arOrder["status"], array("waiting_for_capture"))) {
            $action = '
         ' . GetMessage('VAMPIRUS.YANDEXKASSA_ACTION_SUM') . '
            <input type="text" name="sum[' . $arOrder['id'] . ']" value="' . $payment->getSum() . '">
            <br>';
            $action .= '
                <button class="adm-btn" type="submit" name="action[' . $arOrder['id'] . ']" value="cancel"  onclick="return confirm(\''. GetMessage("VAMPIRUS.YANDEXKASSA_CONFIRM_CANCEL") .'\')">' . GetMessage('VAMPIRUS.YANDEXKASSA_ACTION_CANCEL') . '</button>';
            $action .= '
                <button class="adm-btn" type="submit" name="action[' . $arOrder['id'] . ']" value="confirm"  onclick="return confirm(\''. GetMessage("VAMPIRUS.YANDEXKASSA_CONFIRM_CONFIRM") .'\')">' . GetMessage('VAMPIRUS.YANDEXKASSA_ACTION_CONFIRM') . '</button>';
        }
    }
    $row->AddField('ACTION', $action);

}

$lAdmin->CheckListMode();
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/prolog.php";

$APPLICATION->SetTitle(GetMessage("VAMPIRUS.YANDEXKASSA_TRANSACTION_TITLE"));

require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php";
?>
<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage() ?>?">
<?
$oFilter->Begin();
?>
<tr>
    <td><?=GetMessage("VAMPIRUS.YANDEXKASSA_ORDER_NUMBER")?>:</td>
    <td>
        <input type="text" name="filter_ACCOUNT_NUMBER" value="<?echo htmlspecialcharsbx($filter_order_id) ?>" size="40">
    </td>
</tr>
<tr>
    <td><?=GetMessage("VAMPIRUS.YANDEXKASSA_DATE")?>:</td>
    <td>
        <?echo CalendarPeriod("filter_date_from", $filter_date_from, "filter_date_to", $filter_date_to, "find_form", "Y") ?>
    </td>
</tr>
<tr>
    <td><?=GetMessage("VAMPIRUS.YANDEXKASSA_INVOICE_ID")?>:</td>
    <td>
        <input type="text" name="filter_id" value="<?echo htmlspecialcharsbx($filter_id) ?>" size="40">
    </td>
</tr>
<tr>
    <td><?=GetMessage("VAMPIRUS.YANDEXKASSA_STATUS")?>:</td>
    <td>
        <select name="filter_status">
            <option value=""><?=GetMessage("VAMPIRUS.YANDEXKASSA_FILTER_ANY_STATUS")?></option>
            <?foreach ($statusList as $value => $name): ?>
            <?$selected = $value == $filter_status ? 'selected' : '';?>
            <option value="<?=$value?>" <?=$selected?>><?=$name?></option>
            <?endforeach;?>
        </select>
    </td>
</tr>
<?
$oFilter->Buttons(
    array(
        "table_id" => $sTableID,
        "url"      => $APPLICATION->GetCurPage(),
        "form"     => "find_form",
    )
);
$oFilter->End();
?>
</form>
<?
$lAdmin->DisplayList();
?>
<?
require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php";
?>
