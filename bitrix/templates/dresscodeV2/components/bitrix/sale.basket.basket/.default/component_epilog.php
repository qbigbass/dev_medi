<?//$APPLICATION->AddHeadScript($templateFolder."/js/fast-order.js");
global $nUserID;

$cItems = array();
$bItems = array();
foreach($arResult['ITEMS'] as $pid => $arItem)
{
    if(!$arItem['IBLOCK_ID']){
        $arItem['IBLOCK_ID'] = 17;
    }

    $arResult['allSum'] += $arItem['product']['PRICE'];

        $secturl = explode("/", $arItem['DETAIL_PAGE_URL']);
        $sectcount = count($secturl) - 1;
        switch ($secturl[2]){
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

        $feed_products[$arItem['ID']] = ['id' => $arItem['PRODUCT_ID'], 'price'=>$arItem['PRICE'], 'list'=> $feed_id];

        unset($secturl[$sectcount]); unset($secturl[0]);unset($secturl[1]);

        $cItems[] = array(
        'id' =>  $arItem['PRODUCT_ID'],
        'q' => $arItem['QUANTITY'],
        'price' => $arItem['product']['PRICE'],
        'article' => $arItem['product']['CML2_ARTICLE'],
        'name' => $arItem['product']['NAME'],
        'category' => implode("/",$secturl),
        'brand' => $arItem['product']['BRAND']
        );
}
if (!empty($cItems))
{?>
    <script>
    window.dataLayer = window.dataLayer || [];
    dataLayer.push({
    'ecommerce': {
      'currencyCode': 'RUB',
      'checkout': {
        'actionField': {'step': 1},
        'products': [
<?foreach ($cItems as  $item) {?>
        {'name': '<?=$item['name']?>', 'id': <?=$item['id']?>,'price': <?=$item['price']?>,'brand': '<?=$item['brand']?>','category': '<?=$item['category']?>','variant': '<?=$item['article']?>', 'quantity': <?=$item['q']?>},
<?}?>
       ]
      }
    },
    'event': 'gtm-ee-event',
    'gtm-ee-event-category': 'Enhanced Ecommerce',
    'gtm-ee-event-action': 'Checkout Step 1',
    'gtm-ee-event-non-interaction': 'False',
    });

    <?if (!empty($feed_products)):?>
   var _tmr = _tmr || [];
   <?foreach($feed_products as $fitem){?>
   _tmr.push({
   type: "itemView",
   productid: <?=$fitem['id']?>,
   pagetype: "cart",
   totalvalue:  <?=$fitem['price']?>,
   list: "1" });
   <?}?>
   <?foreach($feed_products as $fitem){?>
   _tmr.push({
   type: "itemView",
   productid: <?=$fitem['id']?>,
   pagetype: "cart",
   totalvalue:  <?=$fitem['price']?>,
   list: "102" });
   <?}?>
   <?endif;?>
    </script>
<?
}

global $nUserEmail;?>
<script type='text/javascript'>
        var dataLayer = dataLayer || [];
        dataLayer.push({
            'event': 'crto_basketpage',
            crto: {
                'email': '<?=$nUserEmail?>',
                'products': [
        <?foreach ($cItems as  $item) {?>
                {
                    'id': <?=$item['id']?>,
                    'price': <?=$item['price']?>,
                    'quantity': <?=$item['q']?>
                },
        <?}?>
               ]
            }
        });
</script>

<script>
    $products = new Array();
    $(".item.product").each(function () {
        $products.push({id: $(this).data("product-id"), price: $(this).data("product-price"), quantity: $(this).data("product-quantity")});
    });


    window.gdeslon_q = window.gdeslon_q || [];
    window.gdeslon_q.push({
        page_type: "basket", //тип страницы: main, list, card, basket, thanks, other
        merchant_id: "104092", //id оффера в нашей системе
        order_id: "", //id заказа
        category_id: "", //id текущей категории
        products: $products,
        deduplication: "<?=DEDUPLICATION?>", //параметр дедупликации заказов (по умолчанию - заказ для Gdeslon)
        user_id: "<?=$nUserID?>" //идентификатор пользователя
    });
</script>
