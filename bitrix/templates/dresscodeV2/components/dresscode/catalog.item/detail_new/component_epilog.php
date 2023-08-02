<?
$mytaget_feed_id = '102';

global $nUserID;

if ($USER->IsAuthorized()) {
    $idUser = $USER->GetID();
    $rsUser = CUser::GetByID($idUser);
    $arUser = $rsUser->Fetch();
    $arElements = $arUser['UF_FAVORITIES'];
}

if (!empty($arElements)) {
    foreach ($arElements as $favoriteProductItem) {?>
        <script>
            if ($('.b-card-favorite[data-product-id="<?=$favoriteProductItem?>"]')) {
                $('.b-card-favorite[data-product-id="<?=$favoriteProductItem?>"]').addClass('active');
            }
        </script>
    <?}
}

?>
<script>
    $product_price = parseInt( $(".price .priceVal").attr("data-price"));
    $product_id = parseInt( $(".changeID").attr("data-id"));
    $section_id = parseInt( $("#catalogElement").attr("data-section"));
    $parent_section_id = ( $("#catalogElement").attr("data-parent-section"));

    var _tmr = _tmr || [];
    _tmr.push({
        type: "itemView",
        productid: $product_id,
        pagetype: "product",
        totalvalue:  $product_price,
        list: "1"
    });
    _tmr.push({
        type: "itemView",
        productid: $product_id,
        pagetype: "product",
        totalvalue:  $product_price,
        list: "102"
    });

    window.gdeslon_q = window.gdeslon_q || [];
    window.gdeslon_q.push({
        page_type: "card", //тип страницы: main, list, card, basket, thanks, other
        merchant_id: "104092", //id оффера в нашей системе
        order_id: "", //id заказа
        category_id: $section_id, //id текущей категории
        products: [
            { id: $product_id, price: $product_price, quantity: 1 } //массив с перечнем товаров: id из фида, цена и количество
        ],
        deduplication: "<?=DEDUPLICATION?>", //параметр дедупликации заказов (по умолчанию - заказ для Gdeslon)
        user_id: "<?=$nUserID?>" //идентификатор пользователя
    });


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
            "products" : [{'id': $product_id}],
            "category_ids" : $section_id,
            //"business_value" : 88,
            "total_price" : $product_price
        };

        VK.Retargeting.ProductEvent(PRICE_LIST_ID, 'view_product', eventParams);


    });

    var _gcTracker = window._gcTracker || [];
    _gcTracker.push(['view_product', { category_id: $section_id , product_id: $product_id }]);

    var _rutarget = window._rutarget || [];
    _rutarget.push({'event': 'showOffer', 'sku': $product_id, 'price': $product_price});

</script>


