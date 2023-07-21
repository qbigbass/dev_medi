<?
$cItems = array();
$bItems = array();

if (!empty($arResult['product']))
{?>
    <script>
    window.dataLayer = window.dataLayer || [];
   var _tmr = _tmr || [];
   _tmr.push({
   type: "itemView",
   productid: <?=$arResult['ID']?>,
   pagetype: "cart",
   totalvalue:  <?=$arResult['product']['PRICE']?>,
   list: "1" });
   _tmr.push({
   type: "itemView",
   productid: <?=$arResult['ID']?>,
   pagetype: "cart",
   totalvalue:  <?=$arResult['product']['PRICE']?>,
   list: "102" });
    </script>
<?


global $nUserEmail, $nUserID;?>
<script type='text/javascript'>
        var dataLayer = dataLayer || [];
        dataLayer.push({
            'event': 'crto_basketpage',
            crto: {
                'email': '<?=$nUserEmail?>',
                'products': [
                {
                    'id': <?=$arResult['ID']?>,
                    'price': <?=$arResult['product']['PRICE']?>,
                    'quantity': 1
                },
               ]
            }
        });


        waitForGtag(function () {
            gtag('event', 'add_to_cart', {
              'send_to': 'AW-434927646',
              'value': <?=intval($arResult['product']['PRICE'])?>,
              'items': [
                 {
                'id': <?=$arResult['ID']?>,
                'google_business_vertical': 'retail'
            }]
            });
        });
</script>
<script>



    window.gdeslon_q = window.gdeslon_q || [];
    window.gdeslon_q.push({
        page_type: "basket", //тип страницы: main, list, card, basket, thanks, other
        merchant_id: "104092", //id оффера в нашей системе
        order_id: "", //id заказа
        category_id: "", //id текущей категории
        products: [{id: <?=intval($arResult['ID'])?>, price:<?=intval($arResult['product']['PRICE'])?>,quantity: 1}],
        deduplication: "<?=DEDUPLICATION?>", //параметр дедупликации заказов (по умолчанию - заказ для Gdeslon)
        user_id: "<?=$nUserID?>" //идентификатор пользователя
    });
</script>
<?}?>
