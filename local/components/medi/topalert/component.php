<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 3600;

$arResult['IS_MOBILE'] = "N";
if ($arParams['IS_MOBILE'] == "Y")
{
    $arResult['IS_MOBILE'] = "Y";
}
$arParams['SITE_ID'] = SITE_ID;
$arParams['HIDE'] = $_SESSION['top_alert_hide'];

if( $this->StartResultCache( $arParams["CACHE_TIME"]))
{
    CModule::IncludeModule("iblock");

    $IBLOCK_ID = $arParams['IBLOCK_ID'];

    $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>26, "CODE"=>"SITE"));
    while($enum_fields = $property_enums->GetNext())
    {
        if ($enum_fields["XML_ID"] == SITE_ID) {
            $site_id = $enum_fields['ID'];
        }
    }

    $arFilter = ["IBLOCK_ID"=>$IBLOCK_ID, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y"];
    if ($arResult['IS_MOBILE'] == "Y"){
        $arFilter['!PREVIEW_PICTURE'] = false;
    }
    else{

        $arFilter['!DETAIL_PICTURE'] = false;
    }
    if ($site_id)
    {
        $arFilter["PROPERTY_SITE"] = $site_id;
    }
    if (!empty($_SESSION['top_alert_hide']))
    {
        $arFilter['!ID'] = $_SESSION['top_alert_hide'];
    }
    $obElement = CIBlockElement::GetList(["SORT"=>"ASC"], $arFilter, false, false, ["ID", "NAME",  "PREVIEW_PICTURE", "DETAIL_PICTURE", "PROPERTY_LINK", "PROPERTY_HEIGHT"]);
    if ($arElement = $obElement->GetNext() ){

        $arResult = $arElement;

        $DETAIL_PICTURE = CFile::GetFileArray($arElement['DETAIL_PICTURE']);
        $PREVIEW_PICTURE = CFile::GetFileArray($arElement['PREVIEW_PICTURE']);

        $arResult['PREVIEW_PICTURE'] = $PREVIEW_PICTURE;
        $arResult['DETAIL_PICTURE'] = $DETAIL_PICTURE;

        $arResult['HIDE'] = (!$_SESSION['top_alert_hide'][$arElement['ID']] ? 'N' : 'Y');
        $arResult['HEIGHT'] = ($arElement['PROPERTY_HEIGHT_VALUE'] > 0 ? intval($arElement['PROPERTY_HEIGHT_VALUE']) : 90);


        $this->setResultCacheKeys(array_keys($arResult));
        //include template
        $this->IncludeComponentTemplate();

    }
    else
    {
        $this->AbortResultCache();
    }


}