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
    $feed_id = '1';
?>
<script>
$product_price = parseInt( $(".price .priceVal").attr("data-price"));
$product_id = parseInt( $(".changeID").attr("data-id"));
$section_id = parseInt( $("#catalogElement").attr("data-section"));

var _tmr = _tmr || [];
_tmr.push({
type: "itemView",
productid: $product_id,
pagetype: "product",
totalvalue:  $product_price,
list: "<?=$feed_id?>" });


waitForFbq(function () {
    fbq('track', 'ViewContent', {content_type: 'product', content_ids:[$product_id]});
});
waitForGtag(function(){
    gtag('event', 'view_item', {
      'send_to': 'AW-434927646',
      'value': $product_price,
      'items': [{
        'id': $product_id,
        'google_business_vertical': 'retail'
      }]
    });

});
waitForVk(function(){
    const eventParams = {
	"products" : [$product_id],
    "category_ids" : $section_id,
	//"business_value" : 88,
	"total_price" : $product_price
	}; 
     VK.Retargeting.ProductEvent(PRICE_LIST_ID, 'view_product', eventParams);
});
_gcTracker.push(['view_product', { category_id: $section_id , product_id: $product_id }]);
</script>
