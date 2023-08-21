<?
$price_id = $GLOBALS['medi']['price_id'][SITE_ID];
$max_price_id = $GLOBALS['medi']['max_price_id'][SITE_ID];

$count_cart = count($arResult['ITEMS']);
if ($count_cart == 1){
    $related_count = 10;
}
elseif ($count_cart <= 2){
    $related_count = 5;
}
elseif ($count_cart <=3){
    $related_count = 4;
}
else{
    $related_count = 3;
}
foreach($arResult['ITEMS'] AS $k=>$arItem)
{
    $rsBaseProduct = CIBlockElement::GetList([], ['ID' => $arItem['ID'], "IBLOCK_ID" => $arItem['IBLOCK_ID']], false, false, ['IBLOCK_ID', 'PROPERTY_CML2_LINK.ID', 'PROPERTY_CML2_LINK.IBLOCK_ID', 'PROPERTY_CML2_ARTICLE']);

    if ($productInfo = $rsBaseProduct->GetNext())
    {
        $exclude[] = $productInfo['PROPERTY_CML2_LINK_ID'] ? $productInfo['PROPERTY_CML2_LINK_ID'] :$arItem['ID'];

    }
}
foreach($arResult['ITEMS'] AS $k=>$arItem)
{
    $arReturn = [];
    $goodPrice = 0;
    $goodMaxPrice = 0;
    $arReturn['product']['ID'] = $arItem['ID'];
    $goodId = '';
    $goodName = '';
    $rsBaseProduct = CIBlockElement::GetList([], ['ID' => $arItem['ID'], "IBLOCK_ID" => $arItem['IBLOCK_ID']], false, false, ['IBLOCK_ID', 'PROPERTY_CML2_LINK.ID', 'PROPERTY_CML2_LINK.IBLOCK_ID', 'PROPERTY_CML2_ARTICLE']);

    if ($productInfo = $rsBaseProduct->GetNext())
    {
        if ($productInfo['PROPERTY_CML2_LINK_ID'] > 0)
        {
            $rsBaseProductRealted = CIBlockElement::GetList([], ['ID' => $productInfo['PROPERTY_CML2_LINK_ID'], 'ACTIVE'=>"Y"], false, false, ['ID', 'IBLOCK_ID', 'PROPERTY_RELATED_PRODUCT', 'PROPERTY_NO_CART_BUTTON', 'PROPERTY_INSOLE_BUTTON']);

            while($productRelatedInfo = $rsBaseProductRealted->GetNext()){

                if (!empty($productRelatedInfo['PROPERTY_RELATED_PRODUCT_VALUE']))
                {
                    $rc = 0;
                    foreach ($productRelatedInfo['PROPERTY_RELATED_PRODUCT_VALUE'] as $key => $value) {
                        if (!in_array($value, $exclude))
                            $arResult['RELATED_CART'][$value] = $value;

                            $rc++;
                        if ($rc > $related_count)
                            break;
                    }
                }
            }

            $arFilter = Array(
                "ID" => $productInfo['PROPERTY_CML2_LINK_ID'],
                "IBLOCK_ID" => $productInfo['PROPERTY_CML2_LINK_IBLOCK_ID'],
                "ACTIVE_DATE" => "Y",
                "ACTIVE" => "Y"
            );
            $rsBaseProduct2 = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'NAME', 'PROPERTY_ATT_BRAND.NAME', 'PROPERTY_CML2_ARTICLE', "CATALOG_PRICE_".$price_id, "CATALOG_PRICE_".$max_price_id]);
            if ($productBrand = $rsBaseProduct2->GetNext())
            {
                $arReturn['product']['BRAND'] = $productBrand['PROPERTY_ATT_BRAND_NAME'];
                $goodId = $productBrand['ID'];
                $goodName = $productBrand['NAME'];
                $goodArticle = $productInfo['PROPERTY_CML2_ARTICLE_VALUE'];
                $goodPrice = $productBrand['CATALOG_PRICE_'.$price_id];
                $goodMaxPrice = $productBrand['CATALOG_PRICE_'.$max_price_id];
            }
        }
        else {

            $arFilter = Array(
                "ID" => $arItem['ID'],
                "IBLOCK_ID" => $arItem['IBLOCK_ID'],
                "ACTIVE_DATE" => "Y",
                "ACTIVE" => "Y"
            );
            $rsBaseProduct2 = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'NAME', 'PROPERTY_ATT_BRAND.NAME', 'PROPERTY_CML2_ARTICLE', "CATALOG_PRICE_".$price_id, "CATALOG_PRICE_".$max_price_id]);
            if ($productBrand = $rsBaseProduct2->GetNext())
            {
                $arReturn['product']['BRAND'] = $productBrand['PROPERTY_ATT_BRAND_NAME'];
                $goodId = $productBrand['ID'];
                $goodName = $productBrand['NAME'];
                $goodPrice = $productBrand['CATALOG_PRICE_'.$price_id];
                $goodMaxPrice = $productBrand['CATALOG_PRICE_'.$max_price_id];

                $goodArticle = $productBrand['PROPERTY_CML2_ARTICLE_VALUE'];
            }
        }
    }

    $arReturn['product']['ID'] = $goodId;
    $arReturn['product']['NAME'] = $goodName;
    $arReturn['product']['QUANTITY'] = $arItem['QUANTITY'];
    $arReturn['product']['PRICE'] = round($goodPrice, 0);
    $arReturn['product']['PRICE_FORMATED'] = number_format (  $goodPrice, 0, '.', ' ' ).' руб.';
    $arReturn['product']['MAX_PRICE'] = round($goodMaxPrice, 0);
    $arReturn['product']['MAX_PRICE_FORMATED'] = number_format (  $goodMaxPrice, 0, '.', ' ' ).' руб.';

    if (empty( $arItem['DISCOUNT'])){
        $arResult['ITEMS'][$k]['BASE_PRICE_FORMATED'] = number_format (  $goodMaxPrice, 0, '.', ' ' ).' руб.';
        $arResult['ITEMS'][$k]['PRICE'] = round($goodPrice, 0);
        if ($goodMaxPrice > $goodPrice)
        {
            $arReturn['product']['DISCOUNT']['SUM_FORMATED'] = number_format (  $goodMaxPrice - $goodPrice, 0, '.', ' ' ).' руб.';
            $arReturn['product']['DISCOUNT']['SUM'] =   $goodMaxPrice - $goodPrice;
            $arReturn['product']['DISCOUNT']['SUM_PERCENT'] =  round(100 - ($goodPrice*100/$goodMaxPrice),0);

        }
    }
    $secturl = explode("/", $arItem['DETAIL_PAGE_URL']);
    $sectcount = count($secturl) - 1;
    unset($secturl[$sectcount]); unset($secturl[0]);unset($secturl[1]);

    $arReturn['product']['CATEGORY'] = implode("/",$secturl);

    $arReturn['product']['CML2_ARTICLE'] = $goodArticle;

    $arResult['ITEMS'][$k]['product'] = $arReturn['product'];

}

if (!empty($arResult['RELATED_CART']))
{
    foreach ($arResult['RELATED_CART'] as $key => $value) {
        $rsRelated = CIBlockElement::GetList([], ['ID' => $value, 'ACTIVE'=>"Y", "IBLOCK_ID"=>17], false, false, ['ID', 'IBLOCK_ID', "PROPERTY_REGION_VALUE"]);

        if($productRelatedI = $rsRelated->GetNext())
        {
            if (!in_array($GLOBALS['medi']['region_cities'][SITE_ID], array_values($productRelatedI['PROPERTY_REGION_VALUE_VALUE'])))
                unset($arResult['RELATED_CART'][$key]);
        }
        else {
            unset($arResult['RELATED_CART'][$key]);
        }
    }
} 
