<?

//__($arResult);
$arReturn['product']['ID'] = $arResult['PRODUCT_ID'];
$goodId = '';
$goodName = '';
$rsBaseProduct = CIBlockElement::GetList([], ['ID' => $arResult['PRODUCT_ID'], "IBLOCK_ID" => $arResult['IBLOCK_ID']], false, false, ['IBLOCK_ID', 'PROPERTY_CML2_LINK.ID', 'PROPERTY_CML2_LINK.IBLOCK_ID', 'PROPERTY_CML2_ARTICLE']);
if ($productInfo = $rsBaseProduct->GetNext())
{
    if ($productInfo['PROPERTY_CML2_LINK_ID'] > 0)
    {
        $arFilter = Array(
            "ID" => $productInfo['PROPERTY_CML2_LINK_ID'],
            "IBLOCK_ID" => $productInfo['PROPERTY_CML2_LINK_IBLOCK_ID'],
            "ACTIVE_DATE" => "Y",
            "ACTIVE" => "Y"
        );
        $rsBaseProduct2 = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'NAME', 'PROPERTY_ATT_BRAND.NAME', 'PROPERTY_CML2_ARTICLE']);
        if ($productBrand = $rsBaseProduct2->GetNext())
        {
            $arReturn['product']['BRAND'] = $productBrand['PROPERTY_ATT_BRAND_NAME'];
            $goodId = $productBrand['ID'];
            $goodName = $productBrand['NAME'];
            $goodArticle = $productInfo['PROPERTY_CML2_ARTICLE_VALUE'];
        }
    }
    else {
        $arFilter = Array(
            "ID" => $arResult['ID'],
            "IBLOCK_ID" => $arResult['IBLOCK_ID'],
            "ACTIVE_DATE" => "Y",
            "ACTIVE" => "Y"
        );
        $rsBaseProduct2 = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'NAME', 'PROPERTY_ATT_BRAND.NAME', 'PROPERTY_CML2_ARTICLE']);
        if ($productBrand = $rsBaseProduct2->GetNext())
        {
            $arReturn['product']['BRAND'] = $productBrand['PROPERTY_ATT_BRAND_NAME'];
            $goodId = $productBrand['ID'];
            $goodName = $productBrand['NAME'];

            $goodArticle = $productBrand['PROPERTY_CML2_ARTICLE_VALUE'];
        }
    }
}

$arReturn['product']['ID'] = $goodId;
$arReturn['product']['NAME'] = $goodName;
$arReturn['product']['QUANTITY'] = $arResult['QUANTITY'];
$arReturn['product']['PRICE'] = $arResult['PRICE'];

$secturl = explode("/", $arResult['DETAIL_PAGE_URL']);
$sectcount = count($secturl) - 1;
unset($secturl[$sectcount]); unset($secturl[0]);unset($secturl[1]);

$arReturn['product']['CATEGORY'] = implode("/",$secturl);

$arReturn['product']['CML2_ARTICLE'] = $goodArticle;

$arResult['product'] = $arReturn['product'];
