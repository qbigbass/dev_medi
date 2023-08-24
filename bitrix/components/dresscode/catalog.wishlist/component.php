<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

//d7 namespace
use Bitrix\Main,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Bitrix\Main\Type\DateTime,
    Bitrix\Currency,
    Bitrix\Catalog,
    Bitrix\Iblock;

if ($_REQUEST["ajax"] == 'Y') {
    $APPLICATION->RestartBuffer();
}

//set filter name
$arParams["FILTER_NAME"] = !empty($arParams["FILTER_NAME"]) ? $arParams["FILTER_NAME"] : "arrFilter";

//global vars
global $USER;
global $APPLICATION;
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
    } //clear facet values from smart filter
    elseif (!empty($arrFilter["FACET_OPTIONS"])) {
        unset($arrFilter["FACET_OPTIONS"]);
    }
    
}

//price code array
if (empty($arParams["PRICE_CODE"])) {
    $arParams["PRICE_CODE"] = array();
}

//seo settings
$arParams["SET_TITLE"] = $arParams["SET_TITLE"] != "N";
$arParams["SET_BROWSER_TITLE"] = (isset($arParams["SET_BROWSER_TITLE"]) && $arParams["SET_BROWSER_TITLE"] === "N" ? "N" : "Y");
$arParams["SET_META_KEYWORDS"] = (isset($arParams["SET_META_KEYWORDS"]) && $arParams["SET_META_KEYWORDS"] === "N" ? "N" : "Y");
$arParams["SET_META_DESCRIPTION"] = (isset($arParams["SET_META_DESCRIPTION"]) && $arParams["SET_META_DESCRIPTION"] === "N" ? "N" : "Y");
$arParams["ADD_SECTIONS_CHAIN"] = (isset($arParams["ADD_SECTIONS_CHAIN"]) && $arParams["ADD_SECTIONS_CHAIN"] === "Y"); //Turn off by default

//pager settings
$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"] == "Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"] != "N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"] == "Y";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"] == "Y";
$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"] == "Y";
$arParams["PAGE_ELEMENT_COUNT"] = !empty($arParams["PAGE_ELEMENT_COUNT"]) ? $arParams["PAGE_ELEMENT_COUNT"] : 30;
if ($_REQUEST['VIEW'] != '') {
    $arParams['VIEW_MODE'] = ($_REQUEST['VIEW'] ? $_REQUEST['VIEW'] : 'squares');
    $arParams['COMPONENT_TEMPLATE'] = strtolower($arParams['VIEW_MODE']);
    setcookie("CATALOG_VIEW", $_REQUEST["VIEW"], time() + 60 * 60 * 24 * 30 * 12 * 2);
}

//other
$arParams["LAZY_LOAD_PICTURES"] = !empty($arParams["LAZY_LOAD_PICTURES"]) ? $arParams["LAZY_LOAD_PICTURES"] : "N";

//navigation
if ($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"]) {
    
    //clear session nav
    CPageOption::SetOptionString("main", "nav_page_in_session", "N");
    
    $arNavParams = array(
        "nPageSize" => $arParams["PAGE_ELEMENT_COUNT"],
        "bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
        "bShowAll" => $arParams["PAGER_SHOW_ALL"],
    );
    
    $arNavigation = CDBResult::GetNavParams($arNavParams);
    
    if ($arNavigation["PAGEN"] == 0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] > 0) {
        $arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];
    }
    
} else {
    $arNavParams = array(
        "nTopCount" => $arParams["PAGE_ELEMENT_COUNT"],
        "bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
    );
    $arNavigation = false;
    
}

if (empty($arParams["PAGER_PARAMS_NAME"]) || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["PAGER_PARAMS_NAME"])) {
    $pagerParameters = array();
} else {
    $pagerParameters = $GLOBALS[$arParams["PAGER_PARAMS_NAME"]];
    if (!is_array($pagerParameters)) {
        $pagerParameters = array();
    }
}

$arParams["CACHE_GROUPS"] = trim($arParams["CACHE_GROUPS"]);
if ($arParams["CACHE_GROUPS"] != "N") {
    $arParams["CACHE_GROUPS"] = "Y";
}

$arParams["CACHE_FILTER"] = $arParams["CACHE_FILTER"] == "Y";
if (!$arParams["CACHE_FILTER"] && count($arrFilter) > 0) {
    $arParams["CACHE_TIME"] = 0;
}

//catalog items sort params
if (empty($arParams["ELEMENT_SORT_FIELD"]))
    $arParams["ELEMENT_SORT_FIELD"] = "sort";
if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["ELEMENT_SORT_ORDER2"]))
    $arParams["ELEMENT_SORT_ORDER2"] = "desc";

$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["IBLOCK_ID"] = (int)$arParams["IBLOCK_ID"];
$arParams["SECTION_ID"] = (int)$arParams["~SECTION_ID"];

if (!isset($arParams["INCLUDE_SUBSECTIONS"]) || !in_array($arParams["INCLUDE_SUBSECTIONS"], array("Y", "A", "N"))) {
    $arParams["INCLUDE_SUBSECTIONS"] = "Y";
}

$arParams["USE_MAIN_ELEMENT_SECTION"] = $arParams["USE_MAIN_ELEMENT_SECTION"] === "Y";
$arParams["SHOW_ALL_WO_SECTION"] = $arParams["SHOW_ALL_WO_SECTION"] === "Y";
$arParams["SET_LAST_MODIFIED"] = $arParams["SET_LAST_MODIFIED"] === "Y";


//cache params
if (!isset($arParams["CACHE_TIME"])) {
    $arParams["CACHE_TIME"] = 1285912;
}
//create cache id
$cacheID = array(
    "USER_GROUPS" => ($arParams["CACHE_GROUPS"] === "N" ? false : $USER->GetGroups()),
    "PRICE_CODE" => implode(",", $arParams["PRICE_CODE"]),
    "PAGER_PARAMS" => $pagerParameters,
    "NAVIGATION" => $arNavigation,
    "SMART_FILTER" => $arrFilter,
    "SITE_ID" => SITE_ID,
    "VIEW" => $arParams["VIEW_MODE"]
);


if ($this->StartResultCache($arParams["CACHE_TIME"], serialize($cacheID))) {
    
    //check include modules
    if (
        !CModule::IncludeModule("dw.deluxe")
        || !CModule::IncludeModule("iblock")
        || !CModule::IncludeModule("highloadblock")
        || !CModule::IncludeModule("catalog")
        || !CModule::IncludeModule("sale")
    ) {
        
        $this->AbortResultCache();
        ShowError("module(s) not available!");
        return 0;
        
    }

    // main array for product info
    $arResult = array();
    
    //set vars
    $sectionId = 0;
    $woSection = false;
    $skuIblockId = null;
    
    //sku iblock id
    $arCatalogType = CCatalogSKU::GetInfoByIBlock($arParams["IBLOCK_ID"]);
    if (!empty($arCatalogType["IBLOCK_ID"])) {
        $skuIblockId = $arCatalogType["IBLOCK_ID"];
    }
    
    //get section
    //section select fields
    $arSectionSelect = array(
        "ID",
        "NAME",
        "CODE",
        "UF_*",
        "XML_ID",
        "ACTIVE",
        "PICTURE",
        "IBLOCK_ID",
        "TIMESTAMP_X",
        "DESCRIPTION",
        "DETAIL_PICTURE",
        "SECTION_PAGE_URL",
        "IBLOCK_SECTION_ID"
    );
    
    //section filter
    $arSectionFilter = array(
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "GLOBAL_ACTIVE" => "Y",
        "ACTIVE" => "Y"
    );
    
    //if section id set
    if (!empty($arParams["SECTION_ID"])) {
        $arSectionFilter["ID"] = $arParams["SECTION_ID"];
    } //if section code set
    elseif (!empty($arParams["SECTION_CODE"])) {
        $arSectionFilter["=CODE"] = $arParams["SECTION_CODE"];
    } //root section
    else {
        $arSectionFilter["ID"] = 0;
    }
    
    if (!empty($arParams["SECTION_ID"]) || !empty($arParams["SECTION_CODE"])) {
        
        //get section from db
        $rsSection = CIBlockSection::GetList(array(), $arSectionFilter, false, $arSectionSelect);
        
        //get section data
        if ($arResult = $rsSection->GetNext()) {
            $sectionId = $arResult["ID"];
        }
        
        if (!empty($sectionId)) {
            
            //seo values
            $ipropValues = new Iblock\InheritedProperty\SectionValues($arResult["IBLOCK_ID"], $arResult["ID"]);
            $arResult["IPROPERTY_VALUES"] = $ipropValues->getValues();
            
            //get section path
            if ($arParams["ADD_SECTIONS_CHAIN"]) {
                
                $arResult["PATH"] = array();
                $rsPath = CIBlockSection::GetNavChain(
                    $arResult["IBLOCK_ID"],
                    $arResult["ID"],
                    array(
                        "ID", "CODE", "XML_ID", "EXTERNAL_ID", "IBLOCK_ID",
                        "IBLOCK_SECTION_ID", "SORT", "NAME", "ACTIVE",
                        "DEPTH_LEVEL", "SECTION_PAGE_URL"
                    )
                );
                
                $rsPath->SetUrlTemplates("", $arParams["SECTION_URL"]);
                while ($arPath = $rsPath->GetNext()) {
                    
                    //get seo values
                    $ipropValues = new Iblock\InheritedProperty\SectionValues($arParams["IBLOCK_ID"], $arPath["ID"]);
                    $arPath["IPROPERTY_VALUES"] = $ipropValues->getValues();
                    
                    //save path
                    $arResult["PATH"][] = $arPath;
                }
                
            }
            
        } else {
            $arResult["IPROPERTY_VALUES"] = array();
        }
        
    } else {
        
        if ($arParams["SHOW_ALL_WO_SECTION"] == "Y") {
            $woSection = true;
        }
        
    }

    //catalog items sort
    $arSort = array();
    
    //not available sort
    if ($arParams["HIDE_NOT_AVAILABLE"] == "L") {
        $arSort["CATALOG_AVAILABLE"] = "desc, nulls";
    }
    
    //sort 1
    if (!empty($arParams["ELEMENT_SORT_FIELD"])) {
        $arSort[$arParams["ELEMENT_SORT_FIELD"]] = $arParams["ELEMENT_SORT_ORDER"];
    }
    
    //sort 2
    if (!empty($arParams["ELEMENT_SORT_FIELD2"])) {
        $arSort[$arParams["ELEMENT_SORT_FIELD2"]] = $arParams["ELEMENT_SORT_ORDER2"];
    }
    
    //catalog items filter
    $arFilter = array(
        "INCLUDE_SUBSECTIONS" => ($arParams["INCLUDE_SUBSECTIONS"] == "N" ? "N" : "Y"),
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        "CHECK_PERMISSIONS" => "Y",
        "MIN_PERMISSION" => "R",
        "IBLOCK_LID" => SITE_ID,
        "ACTIVE_DATE" => "Y",
        "ACTIVE" => "Y",
    );
    
    //active subSections
    if ($arParams["INCLUDE_SUBSECTIONS"] == "A") {
        $arFilter["SECTION_GLOBAL_ACTIVE"] = "Y";
    }
    
    //section id filter
    if ($arResult["ID"]) {
        $arFilter["SECTION_ID"] = array(intval($sectionId));
    } elseif (!$arParams["SHOW_ALL_WO_SECTION"]) {
        $arFilter["SECTION_ID"] = 0;
    } else {
        
        if (is_set($arFilter, "INCLUDE_SUBSECTIONS")) {
            unset($arFilter["INCLUDE_SUBSECTIONS"]);
        }
        
        if (is_set($arFilter, "SECTION_GLOBAL_ACTIVE")) {
            unset($arFilter["SECTION_GLOBAL_ACTIVE"]);
        }
    }
    
    //price filter
    $arPriceFilter = array();
    
    //additonalFilter
    $additonalFilter = array();
    
    //
    $offersFilter = array();
    
    //each filter vars
    foreach ($arrFilter as $inx => $nextValue) {
        
        //check price on base filter
        if (preg_match('/^(>=|<=|><)CATALOG_PRICE_/', $inx)) {
            
            //filter by currency
            if ($arParams["CONVERT_CURRENCY"] == "Y" && !empty($arParams["CURRENCY_ID"])) {
                $priceIndex = str_replace("CATALOG_PRICE", "CATALOG_PRICE_SCALE", $inx);
                $arPriceFilter[$priceIndex] = $nextValue;
                $arPriceFilter["CATALOG_CURRENCY_1"] = $arParams["CURRENCY_ID"];
            } //other
            else {
                $arPriceFilter[$inx] = $nextValue;
            }
            
            //clear base
            unset($arrFilter[$inx]);
            
        }
        
    }
    
    //hide not available
    if ($arParams["HIDE_NOT_AVAILABLE"] == "Y") {
        $additonalFilter["=CATALOG_AVAILABLE"] = "Y";
        $additonalFilter["PROPERTY_REGION_VALUE"] = $GLOBALS['medi']['region_cities'][SITE_ID];
    }
    
    //additional filter of goods with sku offers
    if (!empty($arParams["ENABLED_SKU_FILTER"]) && $arParams["ENABLED_SKU_FILTER"] == "Y") {
        if (!empty($arrFilter["OFFERS"])) {
            $offersFilter = $arrFilter["OFFERS"];
        }
    }
    
    
    //price & hide not available filter
    if (!empty($offersFilter)) {
        $arFilter[] = array("=ID" => CIBlockElement::SubQuery("PROPERTY_CML2_LINK", array_merge($arPriceFilter, $additonalFilter, $offersFilter, array("IBLOCK_ID" => $skuIblockId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y"))));
    } else {
        
        $arFilter[] = array(
            "LOGIC" => "OR",
            array(
                array_merge(
                    array("!CATALOG_TYPE" => \Bitrix\Catalog\ProductTable::TYPE_SKU), $arPriceFilter, $additonalFilter
                ),
            ),
            array(
                "=ID" => CIBlockElement::SubQuery("PROPERTY_CML2_LINK", array_merge($arPriceFilter, $additonalFilter, $offersFilter, array("IBLOCK_ID" => $skuIblockId, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y")))
            )
        );
    }
    
    
    //catalog items fileds
    $arSelect = array(
        "ID",
        "IBLOCK_ID",
        "SORT",
        "CODE",
        "XML_ID",
        "NAME"
    );

    //get elements
    $arResult["ITEMS"] = array();
    $rsElements = CIBlockElement::GetList($arSort, array_merge($arrFilter, $arFilter), false, $arNavParams, $arSelect);
    $rsElements->SetSectionContext($arResult);
    
    while ($arItem = $rsElements->GetNext()) {
        $arResult["ITEMS"][$arItem["ID"]] = $arItem;
    }

    if (!empty($arResult["ITEMS"]) && !empty($arParams["ENABLED_SKU_FILTER"]) && $arParams["ENABLED_SKU_FILTER"] == "Y") {
        $arResult["FILTER"] = $arrFilter;
    }
    
    $navComponentParameters = array();
    if ($arParams["PAGER_BASE_LINK_ENABLE"] === "Y") {
        
        $pagerBaseLink = trim($arParams["PAGER_BASE_LINK"]);
        if ($pagerBaseLink === "") {
            $pagerBaseLink = $arResult["SECTION_PAGE_URL"];
        }
        
        if ($pagerParameters && isset($pagerParameters["BASE_LINK"])) {
            $pagerBaseLink = $pagerParameters["BASE_LINK"];
            unset($pagerParameters["BASE_LINK"]);
        }
        
        $navComponentParameters["BASE_LINK"] = CHTTP::urlAddParams($pagerBaseLink, $pagerParameters, array("encode" => true));
    } else {
        
        $uri = new \Bitrix\Main\Web\Uri($this->request->getRequestUri());
        $uri->deleteParams(
            array_merge(
                array(
                    "PAGEN_" . $rsElements->NavNum,
                    "SIZEN_" . $rsElements->NavNum,
                    "SHOWALL_" . $rsElements->NavNum,
                    "ajax",
                    "AJAX",
                    "PHPSESSID",
                    "clear_cache",
                    "bitrix_include_areas"
                ),
                \Bitrix\Main\HttpRequest::getSystemParameters()
            )
        );
        
        $navComponentParameters["BASE_LINK"] = $uri->getUri();
        
    }
    
    $arResult["NAV_STRING"] = $rsElements->GetPageNavStringEx(
        $navComponentObject,
        $arParams["PAGER_TITLE"],
        $arParams["PAGER_TEMPLATE"],
        $arParams["PAGER_SHOW_ALWAYS"],
        $this,
        $navComponentParameters
    );
    
    $arResult["NAV_CACHED_DATA"] = null;
    $arResult["NAV_NUM_PAGE"] = $rsElements->NavNum;
    $arResult["NAV_PARAM"] = $navComponentParameters;
    
    //save cache keys
    $this->setResultCacheKeys(array(
        "ID",
        "NAME",
        "PATH",
        "TIMESTAMP_X",
        "NAV_CACHED_DATA",
        "IPROPERTY_VALUES",
        "IBLOCK_SECTION_ID",
        $arParams['VIEW_MODE'],
        $arParams["BROWSER_TITLE"],
        $arParams["META_KEYWORDS"],
        $arParams["META_DESCRIPTION"]
    ));

    $this->IncludeComponentTemplate();
    
}

if ($_REQUEST['ajax'] == 'Y') {
    die();
}

return $arResult["ID"];