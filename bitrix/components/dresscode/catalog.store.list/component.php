<?
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
/** @global CCacheManager $CACHE_MANAGER */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arParams['PHONE'] = (isset($arParams['PHONE']) && $arParams['PHONE'] == 'Y' ? 'Y' : 'N');
$arParams['SCHEDULE'] = (isset($arParams['SCHEDULE']) && $arParams['SCHEDULE'] == 'Y' ? 'Y' : 'N');

$arParams['PATH_TO_ELEMENT'] = (isset($arParams['PATH_TO_ELEMENT']) ? trim($arParams['PATH_TO_ELEMENT']) : '');
if ($arParams['PATH_TO_ELEMENT'] == '')
    $arParams['PATH_TO_ELEMENT'] = 'store/#store_id#';

$arParams['MAP_TYPE'] = (int)(isset($arParams['MAP_TYPE']) ? $arParams['MAP_TYPE'] : 0);

$arParams['SET_TITLE'] = (isset($arParams['SET_TITLE']) && $arParams['SET_TITLE'] == 'Y' ? 'Y' : 'N');

if (!isset($arParams['CACHE_TIME']))
    $arParams['CACHE_TIME'] = 3600;


$arParams['CACHE_FILTER'] = !$arParams['CACHE_FILTER'] ? "Y" : "N";

global ${$arParams["FILTER_NAME"]};

//set filter
if (empty($arParams["FILTER_NAME"]) || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])) {
    //create filter array
    $arrFilter = array();
} else {
    //get filter values
    $arrFilter = ${$arParams["FILTER_NAME"]};
    //if not array clear filter var
    if (!is_array($arrFilter)) {
        $arrFilter = array();
    }
}

$arParams['WEEKDAY'] = strtoupper(date("D"));
$arParams['TODAY'] = date("N");


if ($this->startResultCache()) {
    if (!\Bitrix\Main\Loader::includeModule("catalog")) {
        $this->abortResultCache();
        ShowError(GetMessage("CATALOG_MODULE_NOT_INSTALL"));
        return;
    }
    
    $arResult["TITLE"] = GetMessage("SCS_DEFAULT_TITLE");
    $arResult["MAP"] = $arParams["MAP_TYPE"];
    
    $arSelect = array(
        "ID",
        "TITLE",
        "CODE",
        "ADDRESS",
        "DESCRIPTION",
        "GPS_N",
        "GPS_S",
        "IMAGE_ID",
        "PHONE",
        "DATE_MODIFY",
        "SCHEDULE",
        "SITE_ID",
        "EMAIL",
        
        "UF_*"
    );
    
    
    $arStoreFilter = array("ACTIVE" => "Y", "UF_SALON" => true);
    
    $dbStoreProps = CCatalogStore::GetList(array('SORT' => 'ASC', 'ID' => 'ASC'), array_merge($arrFilter, $arStoreFilter), false, false, $arSelect);
    $arResult["PROFILES"] = array();
    $viewMap = false;
    $arResult['HAS_METRO'] = false;
    while ($arProp = $dbStoreProps->GetNext()) {
        $metro = unserialize($arProp['UF_METRO']);
        if (!empty($metro)) {
            $arResult['HAS_METRO'] = true;
            if (!empty($metro[0])) {
                $metroSalons = [];
                $rsElm = CIBlockElement::GetList(array(), array("ID" => $metro[0], "IBLOCK_ID" => "23", "ACTIVE" => "Y"), false, false, array("ID", "NAME", "IBLOCK_SECTION_ID"));
                while ($arMetro = $rsElm->GetNext()) {
                    
                    $rsSect = CIBlockSection::GetList(array("NAME" => "ASC"), array("IBLOCK_ID" => "23", "ACTIVE" => "Y", "ID" => $arMetro['IBLOCK_SECTION_ID']), false, array("NAME", "PICTURE", "IBLOCK_SECTION_ID", "UF_ICON"));
                    if ($arSect = $rsSect->GetNext()) {
                        if ($arSect['UF_ICON']) {
                            
                            $arSect['ICON'] = CFile::GetFileArray($arSect["UF_ICON"]);
                        } elseif ($arSect['PICTURE'] > 0) {
                            $arSect['ICON'] = CFile::GetFileArray($arSect["PICTURE"]);
                        }
                        $arMetro['SECTION'] = $arSect;
                    }
                    $metroSalons[] = $arMetro;
                }
            }
        }
        $storeImg = false;
        $arProp['IMAGE_ID'] = (int)$arProp['IMAGE_ID'];
        if ($arProp['IMAGE_ID'] > 0)
            $storeImg = CFile::GetFileArray($arProp['IMAGE_ID']);
        $arProp['IMAGE_ID'] = (empty($storeImg) ? false : $storeImg);
        
        if ($arProp["TITLE"] == '' && $arProp["ADDRESS"] != '')
            $storeName = $arProp["ADDRESS"];
        elseif ($arProp["ADDRESS"] == '' && $arProp["TITLE"] != '')
            $storeName = $arProp["TITLE"];
        else
            $storeName = $arProp["TITLE"] . " (" . $arProp["ADDRESS"] . ")";
        
        if ($arParams["PHONE"] == 'Y' && $arProp["PHONE"] != '')
            $storePhone = $arProp["PHONE"];
        else
            $storePhone = null;
        if ($arParams["SCHEDULE"] == 'Y' && $arProp["SCHEDULE"] != '')
            $storeSchedule = $arProp["SCHEDULE"];
        else
            $storeSchedule = null;
        if ($arProp["GPS_N"] && $arProp["GPS_S"]) {
            $viewMap = true;
            $this->AbortResultCache();
        }
        $arResult["STORES"][] = array(
            'ID' => $arProp["ID"],
            'TITLE' => $arProp['UF_STORE_PUBLIC_NAME'],
            'PHONE' => $storePhone,
            'SITE_ID' => $arProp['SITE_ID'],
            'CODE' => $arProp['CODE'],
            'SCHEDULE' => $storeSchedule,
            'DETAIL_IMG' => $arProp['IMAGE_ID'],
            'GPS_N' => $arProp["GPS_N"],
            'GPS_S' => $arProp["GPS_S"],
            'ADDRESS' => $arProp["ADDRESS"],
            'EMAIL' => $arProp["EMAIL"],
            'UF_CITY' => $arProp["UF_CITY"],
            "UF_INDEX" => $arProp["UF_INDEX"],
            "UF_YARUBRICS" => $arProp["~UF_YARUBRICS"],
            "UF_YANAME" => $arProp["~UF_YANAME"],
            "UF_MORE_PHOTO" => $arProp['UF_MORE_PHOTO'],
            "UF_RR_MON" => $arProp["UF_RR_MON"],
            "UF_RR_TUE" => $arProp["UF_RR_TUE"],
            "UF_RR_WED" => $arProp["UF_RR_WED"],
            "UF_RR_THU" => $arProp["UF_RR_THU"],
            "UF_RR_FRI" => $arProp["UF_RR_FRI"],
            "UF_RR_SAT" => $arProp["UF_RR_SAT"],
            "UF_RR_SUN" => $arProp["UF_RR_SUN"],
            "UF_YMAP_PHONE" => $arProp['UF_YMAP_PHONE'],
            'DATE_MODIFY' => $arProp["DATE_MODIFY"],
            "METRO" => $metroSalons,
            'URL' => $url,
            'DESCRIPTION' => (string)$arProp['DESCRIPTION']
        );
    }
    $arResult['VIEW_MAP'] = $viewMap;
    $this->IncludeComponentTemplate();
}
if ($arParams["SET_TITLE"] == "Y")
    $APPLICATION->SetTitle($arParams["TITLE"]);
