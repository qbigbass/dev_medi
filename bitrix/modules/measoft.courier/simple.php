<?php
/** Courier template for sale.order.ajax component
 * @var $minDateStr
 * @var $arUserRseult
 * @var $arResultDelivery
 */

use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();
$orderData = $request->getPost("order");

IncludeModuleLangFile(__FILE__);

$_propArr = [];
$minDate = new DateTime($minDateStr);
$date_option = MeasoftEvents::configValue('HIDE_DATE_OPTION');

$orderData = $request->getPost("order");

$PERSON_TYPE = isset($orderData["PERSON_TYPE"]) ? $orderData["PERSON_TYPE"] : $arUserRseult["PERSON_TYPE_ID"];

$salePropsRes = CSaleOrderProps::GetList(array(), array('CODE' => [ 'MEASOFT_DATE_PUTN', 'MEASOFT_TIME_MIN', 'MEASOFT_TIME_MAX' ], 'PERSON_TYPE_ID' => $PERSON_TYPE));
while ($propArr = $salePropsRes->Fetch())
{
    $_propArr[ $propArr["CODE"] ] = "ORDER_PROP_". $propArr["ID"];
}

$PERIOD_TEXT_arr = explode( " - ",  $GLOBALS["measoft"]["PERIOD_TEXT"] );

$vals = [
    "MEASOFT_DATE_PUTN" => $minDate->format("d.m.Y"),
    "MEASOFT_TIME_MIN" => "09:00",
    "MEASOFT_TIME_MAX" => "18:00",
];

if (isset($orderData[ $_propArr["MEASOFT_DATE_PUTN"]] ))
{
    if (strtotime($vals["MEASOFT_DATE_PUTN"]) <= strtotime($orderData[ $_propArr["MEASOFT_DATE_PUTN"] ]))
    {
        $vals["MEASOFT_DATE_PUTN"] = $orderData[ $_propArr["MEASOFT_DATE_PUTN"] ];
    }
}

if (isset( $orderData[ $_propArr["MEASOFT_TIME_MIN"] ] ))
{
    $vals["MEASOFT_TIME_MIN"] = $orderData[ $_propArr["MEASOFT_TIME_MIN"] ];
}

if (isset( $orderData[ $_propArr["MEASOFT_TIME_MAX"] ] ))
{
    $vals["MEASOFT_TIME_MAX"] = $orderData[ $_propArr["MEASOFT_TIME_MAX"] ];
}
?>
<?if($arResultDelivery['DESCRIPTION']) {?>
	<p slass="measoft-desc"><?=$arResultDelivery['DESCRIPTION']?></p>
<? } ?>
<link href="/bitrix/components/measoft.courier/css/jquery-ui.css" type="text/css"  rel="stylesheet" />
<table id="ms_courier" onclick="return false;" style="display: <?=($date_option == 'Y')?'none':'block'?>">
    <tr>
        <td><?php print GetMessage("MEASOFT_FIELDS_DATE_PUTN")?>:</td>
        <td width="150">
            <input type="text" name="<?= $_propArr["MEASOFT_DATE_PUTN"] ?>" data-date="<?=$minDate->format("d.m.Y")?>" value="<?=$vals["MEASOFT_DATE_PUTN"]; ?>" id="ms_date_putn" mid="<?= $GLOBALS["measoft"]["profileId"] ?>" />
            <br><?php print GetMessage("MEASOFT_HINTS_DATE_PUTN")?>
        </td>
    </tr>
    <tr style="display: <?=($date_option == 'D')?'none':'block'?>">
        <td><?php print GetMessage("MEASOFT_FIELDS_TIME_MIN")?>:</td>
        <td>
            <select name="<?= $_propArr["MEASOFT_TIME_MIN"] ?>" id="ms_time_min" />
                <?php for ($i = 6; $i < 22; $i++) {
                    $val = ($i < 10 ? '0' : '').$i.':00';
                    echo "\n". '<option value="'. $val.'" '. ( ($val==$vals["MEASOFT_TIME_MIN"]) ? 'selected' : '' ) .' >'.$val.'</value>';
                } ?>
            </select>
            <br><?php print GetMessage("MEASOFT_HINTS_TIME_MIN")?>
        </td>
    </tr>
    <tr style="display: <?=($date_option == 'D')?'none':'block'?>">
        <td><?php print GetMessage("MEASOFT_FIELDS_TIME_MAX")?>:</td>
        <td>
            <select name="<?= $_propArr["MEASOFT_TIME_MAX"] ?>" id="ms_time_max" />
                <?php for ($i = 6; $i < 22; $i++) {
                    $val = ($i < 10 ? '0' : '').$i.':00';
                    echo "\n". '<option value="'. $val.'" '. ( ($val==$vals["MEASOFT_TIME_MAX"]) ? 'selected' : '' ) .' >'.$val.'</value>';
                } ?>
            </select>
            <br><?php print GetMessage("MEASOFT_HINTS_TIME_MAX")?>
        </td>
    </tr>
</table>