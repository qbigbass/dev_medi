<?
try{
	$module = CModule::CreateModuleObject('measoft.courier');
	$version = $module->MODULE_VERSION;
}catch(\Exception $e){
	$version = time();
}

?>
<link href="/bitrix/js/measoft.courier/jquery-ui2.css?v=<?php print $version; ?>" type="text/css"  rel="stylesheet" />
<link href="/bitrix/js/measoft.courier/jquery-ui.structure.css?v=<?php print $version; ?>" type="text/css"  rel="stylesheet" />
<script src='/bitrix/js/measoft.courier/jquery.js?v=<?php print $version; ?>' type='text/javascript'></script>
<script src='/bitrix/js/measoft.courier/jquery-ui.js?v=<?php print $version; ?>' type='text/javascript'></script>
<script src='/bitrix/components/measoft.courier/js/script_admin.js?v=<?php print $version; ?>' type='text/javascript'></script>
<?php
if (isset($_POST['action']) && $_POST['action'] == 'update_options_system') {

    $options = ["measoft_check_date_format" => 'N', "measoft_check_date_weekend" => 'N', "measoft_check_fill_deliverydate" => 'N', 'measoft_sync_disable' => 'N',
        "measoft_check_fill_deliverydate_hour" => '', 'measoft_sync_order_cnt' => 30, 'ADD_DELIVERTY_DAYES_COUNT' => ''];

    foreach ($options as $optionId => $optionDefValue) {
        if (isset($_POST[$optionId])) $optionDefValue = $_POST[$optionId];
        COption::SetOptionString("measoft_courier", $optionId, $optionDefValue);
    }
    ?>
    <script>

        $(document).ready(function () {

            BX.UI.Notification.Center.notify({
                content: '<?echo GetMessage("MEASOFT_OPTION_SAVE_SUCCESS")?>'
            });
        });
    </script>
    <?php
}


IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");

use \Bitrix\Main\UI;

$module_id = "measoft.courier";
CModule::IncludeModule($module_id);


global $DB;

UI\Extension::load("ui.notification");

$tableName = 'measoft_cities';

if (!CModule::IncludeModule("pull")) {
    CAdminMessage::ShowMessage(array(
        "MESSAGE" => GetMessage("MEASOFT_OPTION_PUSH_AND_PULL_MODULE_NOT_INSTALLED")
    ));
}


$results = $DB->Query("SELECT * FROM `measoft_cities`");
$measoft_cities = [];

while ($row = $results->Fetch())
{
    $measoft_cities[] = $row;
};


$results = $DB->Query("SELECT * FROM `measoft_pay_system`");
$measoft_pay_system = [];

while ($row = $results->Fetch())
{
    $measoft_pay_system[$row["PAYSYSTEM_ID"]] = $row;
};


$dbPaySystemsList = CSalePaySystem::GetList(array('SORT' => 'ASC'), array("ACTIVE" => 'Y'), false, false, array("ID", "NAME"));
$paySystems = array();
while ($arResult = $dbPaySystemsList->Fetch()){
    $paySystems[$arResult['ID']] = $arResult['NAME'];
}


$results = $DB->Query("SELECT * FROM `measoft_order_status`");
$measoft_order_status = [];

while ($row = $results->Fetch())
{
    $measoft_order_status[$row["MEASOFT_STATUS_CODE"]] = $row["BITRIX_STATUS_ID"];
};


?>


<script>

$(document).ready(function(){




    $("#frmOrderStatus").submit(function(){
        let request = BX.ajax.runAction('measoft:courier.api.ajax.updateOrderStatuses', {
            data: {
                params: $(this).serialize()
            }
        });
        request.then(
            function (resp) {
                BX.UI.Notification.Center.notify({
                    content: '<?echo GetMessage("MEASOFT_OPTION_SAVE_SUCCESS")?>'
                });
            },
            function (resp) {
                BX.UI.Notification.Center.notify({
                    content: '<?echo GetMessage("MEASOFT_OPTION_SAVE_ERROR")?>'
                });
            }
        );
        return false;
    });

    $("#frmSettings").submit(function(){

        let request = BX.ajax.runAction('measoft:courier.api.ajax.updatePaySystem', {
            data: {
                params: $(this).serialize()
            }
        });
        request.then(
            function (resp) {
                BX.UI.Notification.Center.notify({
                    content: '<?echo GetMessage("MEASOFT_OPTION_SAVE_SUCCESS")?>'
                });
            },
            function (resp) {
                BX.UI.Notification.Center.notify({
                    content: '<?echo GetMessage("MEASOFT_OPTION_SAVE_ERROR")?>'
                });
            }
        );
        return false;
    });


    $("#frmNewCity").submit(function(e){
        e.preventDefault();
        let params = $(this).serialize();

        var request = BX.ajax.runComponentAction('measoft.courier:pickup', 'addCity', {
            mode:'class',
            data: {
                params: params
            }
        });

        request.then(function (resp) {
            if (resp.status == 'success') {
                window.location.reload();
            }
        });
        return false;
    });

    $("#newCity").autocomplete({
        source: function( request, response ) {
            var request = BX.ajax.runComponentAction('measoft.courier:pickup', 'getSenderCity', {
                mode:'class',
                data: {
                    search: request.term
                }
            });

            request.then(function(resp){
                if(resp.status === "success") {
                    response(resp.data[0]);
                }

            });

        },
        select: function (event, ui) {
            $(this).val(ui.item.label); // display the selected text
            $(this).prev().val(ui.item.value); // save selected id to input
            return false;
        }

    });

    $(".measoft-delete-city-i").click(function(e){
        e.preventDefault();
        let params = $(this).attr("href");

        var request = BX.ajax.runComponentAction('measoft.courier:pickup', 'delCityId', {
            mode:'class',
            data: {
                params: params
            }
        });

        request.then(function(resp){
            if(resp.data.success === true) {
                window.location.reload();
            }
        });
        return false;
    });

});


</script>
<?
$aTabs = array(
//    array("DIV" => "edit1", "TAB" => GetMessage('SETTINGS'). "_MAIN SETTINGS 1", "ICON" => "", "TITLE" => GetMessage("SETTINGS"). "_MAIN SETTINGS"),
//    array("DIV" => "edit2", "TAB" => "ORDER_STATUS", "ICON" => "", "TITLE" => "ORDER_STATUS"),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>
<?// $tabControl->Begin();  ?>

<?// $tabControl->BeginNextTab(); ?>
<style>
    #MEASOFT_senderCityPlace input[type=text] { margin-top: 5px; }
</style>
    <?= GetMessage("MEASOFT_CITY_SENDERS")?>:
    <table border="0" >
        <? foreach($measoft_cities as $cityArr) : ?>
        <tr>
            <td style="vertical-align:top;" class="adm-detail-content-cell-l">

            </td>
            <td class="adm-detail-content-cell-r">
                [<?= $cityArr["MEASOFT_ID"] ?>] <?= $cityArr["NAME"] ?>
            </td>
            <td>
                <a class="measoft-delete-city-i" href="del_city_id=<?= $cityArr["MEASOFT_ID"] ?>" ><?= GetMessage("MEASOFT_CITY_DELETE")?></a>
            </td>
        </tr>
        <? endforeach; ?>
    </table>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&amp;lang=<?echo LANG?>" id="frmNewCity" >
    <input type="hidden" name="add_city" value="new" >

    <table border="0" >
        <tr>
            <td class="adm-detail-content-cell-r">
                <input type="hidden" name="MEASOFT_ID" value="" >
                <input type="text"  name="NAME"   value="" id="newCity" >

                <input type="hidden" name="Update" value="Y">
                <input type="submit"  name="Update" value="<?echo GetMessage("MEASOFT_CITY_ADD")?>">

            </td>

        </tr>
    </table>

    <? bitrix_sessid_post();?>
</form>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&amp;lang=<?echo LANG?>" id="frmSettings" >
    <input type="hidden" name="action" value="update_pay_system" >

    <div>
        <p><b><?= GetMessage("MEASOFT_PAYTYPE_CARD") ?></b></p>
        <? foreach($paySystems as $payId => $payName) : ?>
            <?
            $checked = "";

            if (isset($measoft_pay_system[$payId]))
            {
                $checked = ($measoft_pay_system[$payId]["CARD"] == 1) ? "checked" : "";
            }

            ?>
        <input type="checkbox" name="payCardSystem[]" value="<?= $payId ?>" <?= $checked?> ><?= $payName ?> <br>
        <? endforeach; ?>
    </div>
    <div>
        <p><b><?= GetMessage("MEASOFT_PAYTYPE_CASH") ?></b></p>
        <? foreach($paySystems as $payId => $payName) : ?>
            <?
            $checked = "";

            if (isset($measoft_pay_system[$payId]))
            {
                $checked = ($measoft_pay_system[$payId]["CASH"] == 1) ? "checked" : "";
            }

            ?>
            <input type="checkbox" name="payCashSystem[]" value="<?= $payId ?>" <?= $checked?> ><?= $payName ?> <br>
        <? endforeach; ?>
    </div>

    <div class="save" style="margin-top: 10px;" >
        <input type="hidden" name="Update" value="Y">
        <input type="submit"  name="Update" value="<?= GetMessage("MEASOFT_BTN_SAVE") ?>">
    </div>

    <?=bitrix_sessid_post();?>
</form>

 <p><b><?= GetMessage("MEASOFT_OPTIONS") ?></b></p>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&amp;lang=<?echo LANG?>" id="frmOptions2" >
    <input type="hidden" name="action" value="update_options_system" >

    <div>
        <p><label for="measoft_check_date_format"><input id="measoft_check_date_format" type="checkbox" name="measoft_check_date_format" value="Y" <?php if(COption::GetOptionString("measoft_courier","measoft_check_date_format")=='Y') print ' checked=checked ';?> /> (measoft_check_date_format) <?= GetMessage("MEASOFT_CHECK_FORMAT_DELVIERY_DATE") ?></label>
        </p>
    </div>

	  <div>
        <p><label for="measoft_check_date_weekend"><input id="measoft_check_date_weekend" type="checkbox" name="measoft_check_date_weekend" value="Y" <?php if(COption::GetOptionString("measoft_courier","measoft_check_date_weekend")=='Y') print ' checked=checked ';?> /> (measoft_check_date_weekend) <?= GetMessage("MEASOFT_CHECK_WEEKEND_DATE") ?></label>
        </p>
    </div>

	<div>
        <p><label for="measoft_check_fill_deliverydate"><input id="measoft_check_fill_deliverydate" type="checkbox" name="measoft_check_fill_deliverydate" value="Y" <?php if(COption::GetOptionString("measoft_courier","measoft_check_fill_deliverydate")=='Y') print ' checked=checked ';?> /> (measoft_check_fill_deliverydate) <?= GetMessage("MEASOFT_CHECK_DELVIERY_DATE") ?></label>
        </p>
    </div>

	<div>
        <p><label for="measoft_check_fill_deliverydate_hour"><input id="measoft_check_fill_deliverydate_hour" type="input" name="measoft_check_fill_deliverydate_hour" value="<?php print (COption::GetOptionString("measoft_courier","measoft_check_fill_deliverydate_hour"));?>"  /> (measoft_check_fill_deliverydate_hour) <?= GetMessage("MEASOFT_CHECK_DELVIERY_DATE_TIME") ?></label>
        </p>
    </div>

	<div>
        <p><label for="ADD_DELIVERTY_DAYES_COUNT"><input id="ADD_DELIVERTY_DAYES_COUNT" type="input" name="ADD_DELIVERTY_DAYES_COUNT" value="<?php print (COption::GetOptionString("measoft_courier","ADD_DELIVERTY_DAYES_COUNT"));?>"  /> (ADD_DELIVERTY_DAYES_COUNT) <?= GetMessage("MEASOFT_CHECK_DELVIERY_DATE_TIME_DAYS_CNT") ?></label>
        </p>
    </div>

	<div>
        <p><label for="measoft_sync_disable"><input id="measoft_sync_disable" type="checkbox" name="measoft_sync_disable" value="Y" <?php if(COption::GetOptionString("measoft_courier","measoft_sync_disable")=='Y') print ' checked=checked ';?> /> (measoft_sync_disable) <?= GetMessage("MEASOFT_SYNC_DISABLED") ?></label>
        </p>
    </div>

	<div>
        <p><label for="measoft_sync_order_cnt"><input id="measoft_sync_order_cnt" type="input" name="measoft_sync_order_cnt" value="<?php print (COption::GetOptionString("measoft_courier","measoft_sync_order_cnt"));?>"  /> (measoft_sync_order_cnt) <?= GetMessage("MEASOFT_SYNC_CNT") ?></label>
        </p>
    </div>

    <div class="save" style="margin-top: 10px;" >
        <input type="hidden" name="Update" value="Y">
        <input type="submit"  name="Update" value="<?= GetMessage("MEASOFT_BTN_SAVE") ?>">
    </div>

    <?=bitrix_sessid_post();?>
</form>


<?// $tabControl->BeginNextTab(); ?>

<?
    $measoftStatuses = [

        [ "code" => "AWAITING_SYNC", "title" => GetMessage("MEASOFT_OSTATUS_AWAITING_SYNC") ],
        [ "code" => "NEW", "title" => GetMessage("MEASOFT_OSTATUS_NEW") ],
        [ "code" => "PICKUP", "title" => GetMessage("MEASOFT_OSTATUS_ACCEPTED") ],
        [ "code" => "ACCEPTED", "title" => GetMessage("MEASOFT_OSTATUS_MEASOFT_OSTATUS_NEW") ],
        [ "code" => "INVENTORY", "title" => GetMessage("MEASOFT_OSTATUS_INVENTORY") ],
        [ "code" => "DEPARTURING", "title" => GetMessage("MEASOFT_OSTATUS_DEPARTURING") ],
        [ "code" => "DEPARTURE", "title" => GetMessage("MEASOFT_OSTATUS_DEPARTURE") ],
        [ "code" => "DELIVERY", "title" => GetMessage("MEASOFT_OSTATUS_DELIVERY") ],
        [ "code" => "COURIERDELIVERED", "title" => GetMessage("MEASOFT_OSTATUS_COURIERDELIVERED") ],
        [ "code" => "COMPLETE", "title" => GetMessage("MEASOFT_OSTATUS_COMPLETE") ],
        [ "code" => "PARTIALLY", "title" => GetMessage("MEASOFT_OSTATUS_PARTIALLY") ],
        [ "code" => "COURIERRETURN", "title" => GetMessage("MEASOFT_OSTATUS_COURIERRETURN") ],
        [ "code" => "CANCELED", "title" => GetMessage("MEASOFT_OSTATUS_CANCELED") ],
        [ "code" => "RETURNING", "title" => GetMessage("MEASOFT_OSTATUS_RETURNING") ],
        [ "code" => "RETURNED", "title" => GetMessage("MEASOFT_OSTATUS_RETURNED") ],
        [ "code" => "WMSASSEMBLED", "title" => GetMessage("MEASOFT_OSTATUS_WMSASSEMBLED") ],
        [ "code" => "WMSDISASSEMBLED", "title" => GetMessage("MEASOFT_OSTATUS_WMSDISASSEMBLED") ],
        [ "code" => "CONFIRM", "title" => GetMessage("MEASOFT_OSTATUS_CONFIRM") ],
        [ "code" => "DATECHANGE", "title" => GetMessage("MEASOFT_OSTATUS_DATECHANGE") ],
        [ "code" => "NEWPICKUP", "title" => GetMessage("MEASOFT_OSTATUS_NEWPICKUP") ],
        [ "code" => "UNCONFIRM", "title" => GetMessage("MEASOFT_OSTATUS_UNCONFIRM") ],
        [ "code" => "PICKUPREADY", "title" => GetMessage("MEASOFT_OSTATUS_PICKUPREADY") ],
        [ "code" => "LOST", "title" => GetMessage("MEASOFT_OSTATUS_LOST") ],
        [ "code" => "COURIERPARTIALLY", "title" => GetMessage("MEASOFT_OSTATUS_COURIERPARTIALLY") ],
        [ "code" => "COURIERCANCELED", "title" => GetMessage("MEASOFT_OSTATUS_COURIERCANCELED") ],
    ];




\Bitrix\Main\Loader::IncludeModule("sale");



$statusResult =\Bitrix\Sale\Internals\StatusLangTable::getList(array(
    'order' => array('STATUS.SORT'=>'ASC'),
    'filter' => array('STATUS.TYPE'=>'O','LID'=>LANGUAGE_ID),
    'select' => array('STATUS_ID','NAME'),
))->fetchAll();

$statusesOptions = "";

foreach($statusResult as $statusArr)
{
    $statusesOptions .= "\n<option value='{$statusArr['STATUS_ID']}' >[{$statusArr['STATUS_ID']}] {$statusArr['NAME']}</option>";
}

?>
<style>
    .order-status-tbl td { padding: 5px; }
</style>
<div style="margin-top: 20px;" >
    <b><?= GetMessage("MEASOFT_STATUS_LIST_TITLE") ?></b>
    <form id="frmOrderStatus" style="margin-top: 10px;" >
        <input type="hidden" name="action" value="update_order_statuses" >

        <table border="0" class="order-status-tbl" >
            <? foreach( $measoftStatuses as $statusArr ) : ?>
                <?
                $statusesOptionsV = $statusesOptions;
                // $measoft_order_status[
                if ( isset($measoft_order_status[ $statusArr["code"] ]) )
                {
                    $bcode = $measoft_order_status[ $statusArr["code"] ];
                    $statusesOptionsV = str_replace( "value='{$bcode}'", "value='{$bcode}' selected ", $statusesOptionsV );
                }
                ?>
            <tr>
                <td>
                    <?= $statusArr["title"] ?>
                </td>
                <td>
                    <select name="status[<?= $statusArr["code"] ?>]" >
                        <option value="" ><?= GetMessage("MEASOFT_STATUS_NOTHING") ?></option>
                        <?= $statusesOptionsV ?>
                    </select>
                </td>
            </tr>
            <? endforeach; ?>


        </table>
        <div class="save" style="margin-top: 10px;" >
            <input type="hidden" name="Update" value="Y">
            <input type="submit"  name="Update" value="<?= GetMessage("MEASOFT_BTN_SAVE") ?>">
        </div>

        <? bitrix_sessid_post();?>
    </form>
</div>
<?//$tabControl->End();?>