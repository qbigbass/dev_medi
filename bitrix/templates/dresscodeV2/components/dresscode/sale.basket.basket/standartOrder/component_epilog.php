<?$APPLICATION->AddHeadScript($templateFolder."/js/fast-order.js");

$bItems = array();
foreach($arResult['GRID']['ROWS'] as $arItem)
{
    //__($arItem);
    if ($arItem['CAN_BUY'] == 'Y')
    {
        $bItems[] = $arItem['PRODUCT_ID'];
        $cItems[] = array(
        'id' => $arItem['PRODUCT_ID'],
        'q' => $arItem['QUANTITY'],
        'p' => $arItem['PRICE']
        );
    }
} 
?>

<script type="text/javascript">
var _tmr = _tmr || [];
_tmr.push({
type: "itemView",
productid: [<?=implode(", ", $bItems)?>],
pagetype: "cart",
totalvalue:  <?=$arResult['allSum']?>,
list: "3" });
</script>
