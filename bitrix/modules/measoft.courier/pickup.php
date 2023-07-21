<?
/**
 * PVZ template for sale.order.ajax component
 * @var $minDateStr
 * @var $arUserRseult
 * @var $arResultDelivery
 */

use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();
$orderData = $request->getPost("order");

$_propArr = [];

$_pros = [
    'PVZ_CODE' => '',
    'PVZ_ADDRESS' => '',
    'PVZ_PHONE' => '',
    'PVZ_WORKTIME' => '',
    'MEASOFT_DATE_PUTN' => ''
];

$PERSON_TYPE = isset($orderData["PERSON_TYPE"]) ? $orderData["PERSON_TYPE"] : $arUserRseult["PERSON_TYPE_ID"];

$salePropsRes = CSaleOrderProps::GetList(array(), array('CODE' => array_keys($_pros), 'PERSON_TYPE_ID' => $PERSON_TYPE));
while ($propArr = $salePropsRes->Fetch())
{
    $compName = "ORDER_PROP_". $propArr["ID"];
    $_propArr[ $propArr["CODE"] ] = $compName;

    if (isset($orderData[ $compName ]))
    {
        $_pros[ $propArr["CODE"] ] = $orderData[ $compName ];
        if (MeasoftEvents::isCp1251Site())
        {
            if ( ($propArr["CODE"] == "PVZ_ADDRESS") || ($propArr["CODE"] == "PVZ_WORKTIME") )
            {
                $_pros[ $propArr["CODE"] ] = iconv('UTF-8', 'CP1251', $_pros[ $propArr["CODE"] ]);
            }
        }
    }
}
?>
<?if($arResultDelivery['DESCRIPTION']) {?>
	<p slass="measoft-desc"><?=$arResultDelivery['DESCRIPTION']?></p>
<? } ?>
<? foreach($_propArr as $propCode => $compName) : ?>
<input type="hidden" name="<?= $compName ?>" value="<?= $_pros[$propCode] ?>" id="MEASOFT_<?= $propCode ?>" >
<? endforeach; ?>

<?$minDate = new DateTime($minDateStr);?>

<input hidden type="text" name="<?= $_propArr["MEASOFT_DATE_PUTN"] ?>" data-date="<?=$minDate->format("d.m.Y")?>" value="<?=$minDate->format("d.m.Y")//= $vals["MEASOFT_DATE_PUTN"]; ?>" id="ms_date_putn" mid="<?= $GLOBALS["measoft"]["profileId"] ?>" />

<div class="pvz-select-holder" >
    <input type="hidden" name="measoft_pickup" value="measoft_pickup" >
    <a href="#" id="btnMapOPening" onclick="measoftObjectInit(<?= $GLOBALS["measoft"]["profileId"] ?>); return false;" style="text-decoration: none;border-radius: 3px;border: 1px solid gray;padding: 5px;"><?= GetMessage("MEASOFT_PVZ_SELECT")?></a>
</div>

<div id="pvz-info-holder">
<? if ($_pros["PVZ_CODE"]) {?>
    <ul class="bx-soa-pp-list"><li><div class="bx-soa-pp-list-termin"><?= GetMessage("MEASOFT_PVZ_SELECT_ON_MAP"); ?>:</div><div class="bx-soa-pp-list-description"><?= $_pros["PVZ_ADDRESS"] ?></div></li></ul>
<? }else{

    if($arUserRseult["DELIVERY_ID"]){
        $defaultPVZ = MeasoftEvents::getDefaultPVZ($arUserRseult["DELIVERY_ID"]);
        if($defaultPVZ){
            ?>
            <ul class="bx-soa-pp-list">
                <li><div class="bx-soa-pp-list-termin"><?= GetMessage("MEASOFT_PVZ_SELECT_ON_MAP"); ?>:</div>
                    <div class="bx-soa-pp-list-description"><?=$defaultPVZ["address"] ?></div>
                </li>
            </ul>
            <?
        }
    }
    ?>
<? } ?>
</div>