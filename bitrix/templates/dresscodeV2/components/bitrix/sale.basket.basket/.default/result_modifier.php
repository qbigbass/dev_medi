<?
use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\Parser;

if(!\Bitrix\Main\Loader::includeModule("iblock")){
    return false;
}

$arCurrency = array();

//get currency format
$arCurrency = \CCurrencyLang::GetFormatDescription("RUB");

//check result
if(!empty($arCurrency)){

    //modify data
    $arCurrency["CODE"] = $arCurrency["CURRENCY"];
    $arCurrency["SEPARATORS"] = \CCurrencyLang::GetSeparators();


    $arResult['CURRENCY'] = $arCurrency;
}

$arResult["NEW_DISCOUNT_PRICE"] = 0;
$arResult["NEW_BASKET_SUM"] = 0;

if (!empty($arResult['GRID']['ROWS']))
{
    $arResult['ITEMS'] = $arResult['GRID']['ROWS'];
    $arResult["BASKET_SUM"] = 0;

        $price_id = 1;
        $max_price_id = 2;
    if (SITE_ID == 's2'){
        $price_id = 6;
        $max_price_id = 5;
    }

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
        //$iblock_id = CIBlockElement::GetIBlockByID($k);
        $rsBaseProduct = CIBlockElement::GetList([], ['ID' => $arItem['PRODUCT_ID'], "IBLOCK_ID" => $arItem['IBLOCK_ID']], false, false, ['IBLOCK_ID', 'PROPERTY_CML2_LINK.ID', 'PROPERTY_CML2_LINK.IBLOCK_ID', 'PROPERTY_CML2_ARTICLE']);

        if ($productInfo = $rsBaseProduct->GetNext())
        {
            $exclude[] = $productInfo['PROPERTY_CML2_LINK_ID'] ? $productInfo['PROPERTY_CML2_LINK_ID'] :$arItem['PRODUCT_ID'];

        }
    }
    foreach($arResult['ITEMS'] AS $k=>$arItem)
    {
        if(!$arItem['IBLOCK_ID']){
            $arItem['IBLOCK_ID'] = 17;
        }
        $arReturn = [];
        $goodPrice = 0;
        $goodMaxPrice = 0;
        $arReturn['product']['ID'] = $arItem['PRODUCT_ID'];
        $goodId = '';
        $goodName = '';
        $rsBaseProduct = CIBlockElement::GetList([], ['ID' => $arItem['PRODUCT_ID'], "IBLOCK_ID" => $arItem['IBLOCK_ID']], false, false, ['IBLOCK_ID', 'PROPERTY_CML2_LINK.ID', 'PROPERTY_CML2_LINK.IBLOCK_ID', 'PROPERTY_CML2_ARTICLE', 'DETAIL_PICTURE']);

        if ($productInfo = $rsBaseProduct->GetNext())
        {
            if(!empty($productInfo["DETAIL_PICTURE"])){
                $arResult['ITEMS'][$k]["PICTURE"] = CFile::ResizeImageGet($productInfo["DETAIL_PICTURE"], array("width" => $arParams["PICTURE_WIDTH"], "height" => $arParams["PICTURE_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, $arParams["IMAGE_QUALITY"]);
            }
            if ($productInfo['PROPERTY_CML2_LINK_ID'] > 0)
            {
                $rsBaseProductRealted = CIBlockElement::GetList([], ['ID' => $productInfo['PROPERTY_CML2_LINK_ID'], 'ACTIVE'=>"Y"], false, false, ['ID', 'IBLOCK_ID', 'PROPERTY_RELATED_PRODUCT', 'PROPERTY_NO_CART_BUTTON', 'PROPERTY_INSOLE_BUTTON', 'DETAIL_PICTURE']);

                while($productRelatedInfo = $rsBaseProductRealted->GetNext()){

                    if(!empty($productRelatedInfo["DETAIL_PICTURE"]) && empty($arResult['ITEMS'][$k]["PICTURE"])){
                        $arResult['ITEMS'][$k]["PICTURE"] = CFile::ResizeImageGet($productRelatedInfo["DETAIL_PICTURE"], array("width" => $arParams["PICTURE_WIDTH"], "height" => $arParams["PICTURE_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, $arParams["IMAGE_QUALITY"]);
                    }

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
                    "ID" => $arItem['PRODUCT_ID'],
                    "IBLOCK_ID" => $arItem['IBLOCK_ID'],
                    "ACTIVE_DATE" => "Y",
                    "ACTIVE" => "Y"
                );
                $rsBaseProduct2 = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'NAME', 'PROPERTY_ATT_BRAND.NAME', 'PROPERTY_CML2_ARTICLE', "CATALOG_PRICE_".$price_id, "CATALOG_PRICE_".$max_price_id, 'DETAIL_PICTURE']);
                if ($productBrand = $rsBaseProduct2->GetNext())
                {
                    if(!empty($productBrand["DETAIL_PICTURE"])){
                        $arResult['ITEMS'][$k]["PICTURE"] = CFile::ResizeImageGet($productBrand["DETAIL_PICTURE"], array("width" => $arParams["PICTURE_WIDTH"], "height" => $arParams["PICTURE_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, $arParams["IMAGE_QUALITY"]);
                    }

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
        $arReturn['product']['PRICE'] = round($arResult['ITEMS'][$k]['PRICE'] , 0);
        $arReturn['product']['PRICE_FORMATED'] = number_format (  $arResult['ITEMS'][$k]['PRICE'] , 0, '.', ' ' ).' руб.';
        $arReturn['product']['MAX_PRICE'] = round($goodMaxPrice, 0);
        $arReturn['product']['MAX_PRICE_FORMATED'] = number_format (  $goodMaxPrice, 0, '.', ' ' ).' руб.';

        $arResult["BASKET_SUM"] += round($goodMaxPrice, 0)* $arItem['QUANTITY'];

        if (empty( $arItem['DISCOUNT']) || $arItem['DISCOUNT'] == 0){
            $arItem['DISCOUNT'] = [];
            //$arResult['ITEMS'][$k]['BASE_PRICE_FORMATED'] = number_format (  $goodMaxPrice, 0, '.', ' ' ).' руб.';
            //$arResult['ITEMS'][$k]['PRICE'] = round($goodPrice, 0);

            if ($goodMaxPrice > $arItem['PRICE'])
            {
                $arReturn['product']['DISCOUNT']['SUM_FORMATED'] = number_format (  $goodMaxPrice - $arItem['PRICE'], 0, '.', ' ' ).' руб.';
                $arReturn['product']['DISCOUNT']['SUM'] =   $goodMaxPrice - $arItem['PRICE'];
                $arReturn['product']['DISCOUNT']['SUM_PERCENT'] =  round(100 - ($arItem['PRICE']*100/$goodMaxPrice),0);

            }
            if ($goodMaxPrice > $arResult['ITEMS'][$k]['PRICE'])
            {
                $arResult['ITEMS'][$k]['DISCOUNT']['SUM_FORMATED'] = number_format ($goodMaxPrice - $arResult['ITEMS'][$k]['PRICE'], 0, '.', ' ' ).' руб.';
                $arResult['ITEMS'][$k]['DISCOUNT']['SUM'] = $goodMaxPrice- $arResult['ITEMS'][$k]['PRICE'];
                $arResult['ITEMS'][$k]['DISCOUNT']['SUM_PERCENT'] =  round(100 - ($arResult['ITEMS'][$k]['PRICE']*100/$goodMaxPrice),0);
                $arResult['ITEMS'][$k]['BASE_PRICE_FORMATED'] = number_format (  $goodMaxPrice , 0, '.', ' ' ).' руб.';
                $arResult['ITEMS'][$k]['BASE_PRICE'] = $goodMaxPrice;


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
}
else {
    unset($arResult['ITEMS']);
}
