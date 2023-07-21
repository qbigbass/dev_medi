<?
$can_url = explode("/", $arResult['CANONICAL_PAGE_URL']);
switch ($can_url[4]){
    case "kompressionnyy-trikotazh":
        $feed_id = '2';
    break;
    case "ortopedicheskaya-obuv":
        $feed_id = '3';
    break;
    case "odezhda-dlya-sporta":
        $feed_id = '4';
    break;
    default:
        $feed_id = '';
}
?>
<script>
$product_price = parseInt( $(".price .priceVal").attr("data-price"));
$product_id = parseInt( $(".changeID").attr("data-id"));
var _tmr = _tmr || [];
_tmr.push({
type: "itemView",
productid: $product_id,
pagetype: "product",
totalvalue:  $product_price,
list: "<?=$feed_id?>" });
</script>
