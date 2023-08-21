<?$APPLICATION->AddHeadScript($templateFolder."/js/fast-order.js");?>

<?
$cItems = array();
$bItems = array();
foreach($arResult['ITEMS'] as $pid => $arItem)
{
    $brand = '';
    // sku
    if($arItem['PROPERTIES']['CML2_LINK'])
    {
        $obElmBrand = CIBlockElement::GetList([], ["IBLOCK_ID"=>  $arItem['PROPERTIES']['CML2_LINK']['LINK_IBLOCK_ID'], "ID" => $arItem['PROPERTIES']['CML2_LINK']['VALUE']], false, false, ["ID", "NAME", "PROPERTY_ATT_BRAND.NAME"] );

        if ($arElmBrand = $obElmBrand->GetNext()) {
            $brand = $arElmBrand['PROPERTY_ATT_BRAND_NAME'];
            $goodId = $arElmBrand['ID'];
            $goodName = $arElmBrand['NAME'];
        }

    }
    // simple
    else {
        $obElmBrand = CIBlockElement::GetList([], ["IBLOCK_ID"=>  $arItem['IBLOCK_ID'], "ID" => $arItem['ID']], false, false, ["ID", "NAME","PROPERTY_ATT_BRAND.NAME"] );

        if ($arElmBrand = $obElmBrand->GetNext()) {
            $brand = $arElmBrand['PROPERTY_ATT_BRAND_NAME'];
            $goodId = $arElmBrand['ID'];
            $goodName = $arElmBrand['NAME'];
        }
    }
    $bItems[] = $arItem['PRODUCT_ID'];
    $arResult['allSum'] += $arItem['PRICE'];

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
            $feed_id = MYTARGET_FEED_ID;
        }

        $feed_products[$arItem['ID']] = ['id' => $arItem['ID'], 'price'=>$arItem['PRICE'], 'list'=> $feed_id];

        unset($secturl[$sectcount]); unset($secturl[0]);unset($secturl[1]);

        $cItems[] = array(
        'id' => $arItem['ID'],
        'q' => $arItem['QUANTITY'],
        'price' => $arItem['PRICE'],
        'article' => $arItem['PROPERTIES']['CML2_ARTICLE']['VALUE'],
        'name' => $goodName,
        'category' => implode("/",$secturl),
        'brand' => $brand
        );
}
if (!empty($cItems))
{?>
    <script>
    var _rutarget = window._rutarget || [];
    _rutarget.push({'event': 'cart', 'products': [
        <?foreach ($cItems as  $item) {?>
        {qty: <?=$item['q']?>, sku: <?=$item['id']?>, price:  <?=$item['price']?> },
        <?}?>
     ]});


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
   list: "102" });
   <?}?>
    <?foreach($feed_products as $fitem){?>
    _tmr.push({
        type: "itemView",
        productid: <?=$fitem['id']?>,
        pagetype: "cart",
        totalvalue:  <?=$fitem['price']?>,
        list: "1" });
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

        /*$(function() {
            $("#foundation a").each(function () {
                $href =  $(this).attr("href");

                if ($href != '#' && $href != 'javascript:void(0)') {
                    $(this).attr("target", "_blank");
                }
            });
        });*/
</script>
