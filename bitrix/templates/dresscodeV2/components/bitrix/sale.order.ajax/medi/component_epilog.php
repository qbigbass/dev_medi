<?

#$APPLICATION->AddHeadScript("https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js");
#$APPLICATION->AddHeadScript("https://cdn.jsdelivr.net/npm/suggestions-jquery@19.7.1/dist/js/jquery.suggestions.min.js");

$APPLICATION->AddHeadScript('https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=391d1c41-5055-400d-8afc-49ee21c8f4a1&load=package.full');

#$APPLICATION->AddHeadString('<link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@19.7.1/dist/css/suggestions.min.css" rel="stylesheet" />');

$cItems = array();
foreach ($arResult['BASKET_ITEMS'] as $pid => $arItem) {
    $goodId = '';
    $goodName = '';
    $obElm = CIBlockElement::GetList([], ["ID" => $arItem['PRODUCT_ID']], false, false, ["ID", "NAME", "IBLOCK_ID"]);
    if ($arElm = $obElm->GetNext()) {
        $obElmProp = CIBlockElement::GetList([], ["ID" => $arItem['PRODUCT_ID'], "IBLOCK_ID" => $arElm['IBLOCK_ID']], false, false, ["ID", "NAME", "PROPERTY_CML2_LINK", "PROPERTY_CML2_LINK.IBLOCK_ID", "PROPERTY_ATT_BRAND.NAME", "PROPERTY_CML2_ARTICLE"]);
        if ($arElmProp = $obElmProp->GetNext()) {
            $goodId = $arElm['ID'];
            $goodName = $arElm['NAME'];
            $brand = '';
            $article = $arElmProp['PROPERTY_CML2_ARTICLE_VALUE'];
            // sku
            if ($arElmProp['PROPERTY_CML2_LINK_VALUE']) {
                $obElmBrand = CIBlockElement::GetList([], ["IBLOCK_ID" => $arElmProp['PROPERTY_CML2_LINK_IBLOCK_ID'], "ID" => $arElmProp['PROPERTY_CML2_LINK_VALUE']], false, false, ["ID", "NAME", "PROPERTY_ATT_BRAND.NAME"]);
                
                if ($arElmBrand = $obElmBrand->GetNext()) {
                    $brand = $arElmBrand['PROPERTY_ATT_BRAND_NAME'];
                    $goodId = $arElmProp['ID'];
                    $goodName = $arElmBrand['NAME'];
                }
            } // simple
            elseif ($arElmProp['PROPERTY_ATT_BRAND_NAME']) {
                
                $brand = $arElmProp['PROPERTY_ATT_BRAND_NAME'];
                $goodId = $arElmProp['ID'];
                $goodName = $arElmProp['NAME'];
            }
            
        }
    }
    
    $secturl = explode("/", $arItem['DETAIL_PAGE_URL']);
    $sectcount = count($secturl) - 1;
    unset($secturl[$sectcount]);
    unset($secturl[0]);
    unset($secturl[1]);
    
    $cItems[] = array(
        'id' => $goodId,
        'q' => $arItem['QUANTITY'],
        'price' => $arItem['PRICE'],
        'article' => $article,
        'name' => $goodName,
        'category' => implode("/", $secturl),
        'brand' => $brand
    );
}
//__($cItems);
if (!empty($cItems)) {
    ?>
    <script>
        window.dataLayer = window.dataLayer || [];
        dataLayer.push({
            'ecommerce': {
                'currencyCode': 'RUB',
                'checkout': {
                    'actionField': {'step': 2},
                    'products': [
                        <?foreach ($cItems as  $item) {
                        $citemsid[] = $item['id'];
                        $citemsprice += $item['price'];?>
                        {
                            'name': '<?=$item['name']?>',
                            'id': <?=$item['id']?>,
                            'price': <?=$item['price']?>,
                            'brand': '<?=$item['brand']?>',
                            'category': '<?=$item['category']?>',
                            'variant': '<?=$item['article']?>',
                            'quantity': <?=$item['q']?>
                        },
                        <?}?>
                    ]
                }
            },
            'event': 'gtm-ee-event',
            'gtm-ee-event-category': 'Enhanced Ecommerce',
            'gtm-ee-event-action': 'Checkout Step 2',
            'gtm-ee-event-non-interaction': 'False',
        });

        var _rutarget = window._rutarget || [];
        _rutarget.push({
            'event': 'confirmOrder', 'products': [
                <?foreach ($cItems as $item) {
                echo '{ qty: ' . intval($item['q']) . ', sku: "' . $item['id'] . '", price: ' . round($item['price']) . ' },';
            }?>
            ]
        });


        waitForFbq(function () {
            fbq('track', 'InitiateCheckout', {
                content_type: 'product',
                content_ids: [<?=implode(",", $citemsid);?>],
                currency: 'RUB',
                value: parseInt(<?=$citemsprice?>)
            });

        });

        waitForVk(function () {
            VK.Goal('initiate_checkout');

            const eventParams = {
                "products": [<?=implode(",", $citemsid);?>],
                //"category_ids" : $section_id,
                //"business_value" : 88,
                "total_price": parseInt(<?=$citemsprice?>)
            };
            VK.Retargeting.ProductEvent(PRICE_LIST_ID, 'init_checkout', eventParams);

        });

    </script>
    <?
}


?>
<? if ($_SESSION['lmx']['lastName']
    || $_SESSION['lmx']['firstName']
    || array_intersect([20], $USER->GetUserGroupArray())
) {
    echo "<input type='hidden' id='client_info'
             data-name='" . $_SESSION['lmx']['lastName'] . " " . $_SESSION['lmx']['firstName'] .
        " " . $_SESSION['lmx']['patronymicName'] . "'
             data-phone='" . $_SESSION['lmx']['phone'] . "'
             data-card='" . $_SESSION['lmx']['client_card'] . "'
             data-consult='" . $_SESSION['lmx']['consultant'] . "'
             data-date-min = '" . date("Y-m-d", time() - 86400) . "'
             data-date-max = '" . date("Y-m-d", time() + 30 * 86400) . "'
             data-date-val = '" . date("Y-m-d", time() + 86400) . "'
             data-salon = '" . $_SESSION['SESS_AUTH']['FIRST_NAME'] . " " . $_SESSION['SESS_AUTH']['LAST_NAME'] . "'
             />";
}
?>
<script>

    $(function () {

        $("#foundation a").each(function () {
            $href = $(this).attr("href");

            if ($href != '#' && $href != 'javascript:void(0)') {
                $(this).attr("target", "_blank");
            }
        });
        
        <?if ($_SESSION['lmx']['lastName']
    || $_SESSION['lmx']['firstName']
    || array_intersect([20], $USER->GetUserGroupArray())
        ){
        ?>
        $("#soa-property-7").val("");
        $("#soa-property-20").html("");
        $("#orderDescription").html($("#salon_name_field").val());

        $str = '<div class="form-group bx-soa-customer-field" data-property-id-row="50">' +
            '<label for="soa-property-50" class="bx-soa-custom-label">Консультант</label>' +
            '<div class="soa-property-container"><input type="text" size="35" name="ORDER_PROP_50"' +
            ' id="soa-property-50" placeholder="" value="<?=$_SESSION['lmx']['consultant']?>" class="form-control ' +
            'bx-soa-customer-input bx-ios-fix"></div></div>';

        $("#orderDescription").parents("div.col-sm-12").append($str);

        $str2 = '<div class="form-group bx-soa-customer-field" data-property-id-row="52">' +
            '<label for="soa-property-52-inp" class="bx-soa-custom-label">Дата доставки</label>' +
            '<div class="soa-property-container"><input type="date" id="soa-property-52-inp" class="form-control' +
            ' bx-soa-customer-input bx-ios-fix" ' +
            'value="<?=date("Y-m-d", time() + 86400)?>" name="ORDER_PROP_52_inp"' +
            '/></div></div>';

        $("#orderDescription").parents("div.col-sm-12").append($str2);
        $("#soa-property-52-inp").on("change", function () {
            $("#soa-property-52").val($("#soa-property-52-inp").val());
        });

        $str3 = '<div class="form-group bx-soa-customer-field" data-property-id-row="51"><label for="soa-property-51" class="bx-soa-custom-label">Время доставки</label><div class="soa-property-container"><select name="ORDER_PROP_51"><option value="">Не выбрано</option><option value="1">с 9:00 до 15:00</option><option value="2">с 15:00 до 22:00</option></select></div></div>';

        $("#orderDescription").parents("div.col-sm-12").append($str3);

        $("#soa-property-1").val("<?=$_SESSION['lmx']['lastName']?> <?=$_SESSION['lmx']['firstName']?> <?=$_SESSION['lmx']['patronymicName']?>");
        <?}?>
        <?if ($_SESSION['lmx']['phone']){?>
        $("#soa-property-3").val("<?=$_SESSION['lmx']['phone']?>");

        $("select[name='ORDER_PROP_18']").val("D_PARTNER");
        <?}?>
        
        <?if ($_SESSION['lmx']['client_card']){?>
        $("select[name='ORDER_PROP_18']").val("D_PARTNER");
        $("#soa-property-14").val("<?=$_SESSION['lmx']['client_card']?>");
        <?}?>
        <?if (!$_SESSION['lmx']['phone']){?>
        $("#soa-property-3").val("");
        $("select[name='ORDER_PROP_18']").val("D_PARTNER");
        $("#soa-property-14").val("<?=$_SESSION['lmx']['client_card']?>");
        <?}?>


    });

</script>
