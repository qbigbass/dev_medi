<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

//d7 namespace
use Bitrix\Main,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Bitrix\Main\Type\DateTime,
    Bitrix\Currency,
    Bitrix\Catalog,
    Bitrix\Iblock;

//load modules
if (!Loader::includeModule("iblock")) {
    return false;
}

//check section
if (empty($arParams["SECTION_ID"]) && empty($arParams["SECTION_CODE"])) {
    return false;
}

//default params
$arParams["MAX_VISIBLE_TAGS_DESKTOP"] = intval(!empty($arParams["MAX_VISIBLE_TAGS_DESKTOP"]) ? $arParams["MAX_VISIBLE_TAGS_DESKTOP"] : "10");
$arParams["MAX_VISIBLE_TAGS_MOBILE"] = intval(!empty($arParams["MAX_VISIBLE_TAGS_MOBILE"]) ? $arParams["MAX_VISIBLE_TAGS_MOBILE"] : "6");
$arParams["USE_IBLOCK_MAIN_SECTION_TREE"] = !empty($arParams["USE_IBLOCK_MAIN_SECTION_TREE"]) ? $arParams["USE_IBLOCK_MAIN_SECTION_TREE"] : "N";
$arParams["USE_IBLOCK_MAIN_SECTION"] = !empty($arParams["USE_IBLOCK_MAIN_SECTION"]) ? $arParams["USE_IBLOCK_MAIN_SECTION"] : "N";
$arParams["TAGS_MAX_DEPTH_LEVEL"] = intval(!empty($arParams["TAGS_MAX_DEPTH_LEVEL"]) ? $arParams["TAGS_MAX_DEPTH_LEVEL"] : "5");
$arParams["SECTION_DEPTH_LEVEL"] = intval(!empty($arParams["SECTION_DEPTH_LEVEL"]) ? $arParams["SECTION_DEPTH_LEVEL"] : 0);
$arParams["INCLUDE_SUBSECTIONS"] = !empty($arParams["INCLUDE_SUBSECTIONS"]) ? $arParams["INCLUDE_SUBSECTIONS"] : "Y";
$arParams["HIDE_TAGS_ON_MOBILE"] = !empty($arParams["HIDE_TAGS_ON_MOBILE"]) ? $arParams["HIDE_TAGS_ON_MOBILE"] : "N";
$arParams["HIDE_NOT_AVAILABLE"] = !empty($arParams["HIDE_NOT_AVAILABLE"]) ? $arParams["HIDE_NOT_AVAILABLE"] : "N";
$arParams["CURRENT_TAG"] = !empty($arParams["CURRENT_TAG"]) ? $arParams["CURRENT_TAG"] : "";
$arParams["SORT_FIELD"] = !empty($arParams["SORT_FIELD"]) ? $arParams["SORT_FIELD"] : "COUNTER";
$arParams["SORT_TYPE"] = !empty($arParams["SORT_TYPE"]) ? $arParams["SORT_TYPE"] : "DESC";
$arParams["MAX_TAGS"] = intval(!empty($arParams["MAX_TAGS"]) ? $arParams["MAX_TAGS"] : "30");

//globals
global $APPLICATION, $USER, $arrFilter, $preFilter;

//check section depth level
if ($arParams["TAGS_MAX_DEPTH_LEVEL"] < $arParams["SECTION_DEPTH_LEVEL"]) {
    return false;
}

//create cache id
$cacheID = array(
    "USE_MAIN_SECTION_TREE" => $arParams["USE_MAIN_SECTION_TREE"],
    "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
    "HIDE_TAGS_ON_MOBILE" => $arParams["HIDE_TAGS_ON_MOBILE"],
    "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
    "USE_MAIN_SECTION" => $arParams["SECTION_DEPTH_LEVEL"],
    "SECTION_CODE_PATH" => $arParams["SECTION_CODE_PATH"],
    "SECTION_CODE" => $arParams["SECTION_CODE"],
    "CURRENT_TAG" => $arParams["CURRENT_TAG"],
    "SECTION_ID" => $arParams["SECTION_ID"],
    "SEF_FOLDER" => $arParams["SEF_FOLDER"],
    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
    "MAX_TAGS" => $arParams["MAX_TAGS"],
    //"USER_GROUPS" => $USER->GetGroups(),
    "SITE_ID" => SITE_ID
);

//cache zone
if ($this->StartResultCache($arParams["CACHE_TIME"], serialize($cacheID))) {
    
    //products filter
    $arFilter = array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "!PROPERTY_TAGS" => false, "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"], "IBLOCK_LID" => SITE_ID);
    
    //filter by id or code
    if (!empty($arParams["SECTION_ID"])) {
        $arFilter["=SECTION_ID"] = $arParams["SECTION_ID"];
    } //code
    else {
        $arFilter["=SECTION_CODE"] = $arParams["SECTION_CODE"];
    }
    
    //hide not available
    if ($arParams["HIDE_NOT_AVAILABLE"] == "Y") {
        $arFilter["CATALOG_AVAILABLE"] = "Y";
    }
    
    //tag path
    $sectionPath = (!empty($arParams["SECTION_CODE_PATH"]) ? $arParams["SECTION_CODE_PATH"] : $arParams["SECTION_CODE"]);
    
    //by id
    if (empty($sectionPath) && !empty($arParams["SECTION_ID"])) {
        $sectionPath = $arParams["SECTION_ID"];
    }
    //check use main section
    if ($arParams["USE_IBLOCK_MAIN_SECTION"] == "Y") {
        
        //vars
        $currentSectionId = 0;
        
        //get iblock info
        $arIBlock = CIBlock::GetArrayByID($arParams["IBLOCK_ID"]);
        
        //check iblock section params
        if (!empty($arIBlock["FIELDS"]["IBLOCK_SECTION"]["DEFAULT_VALUE"]["KEEP_IBLOCK_SECTION_ID"]) && $arIBlock["FIELDS"]["IBLOCK_SECTION"]["DEFAULT_VALUE"]["KEEP_IBLOCK_SECTION_ID"] == "Y") {
            $keepIblockSectionId = $arIBlock["FIELDS"]["IBLOCK_SECTION"]["DEFAULT_VALUE"]["KEEP_IBLOCK_SECTION_ID"];
        }
        
        //check iblock use main section settings
        if (!empty($keepIblockSectionId)) {
            
            //get current section id from arParams
            if (!empty($arParams["SECTION_ID"])) {
                $currentSectionId = $arParams["SECTION_ID"];
            } //get current section by code
            else {
                
                //get section
                $arSectionFilter = array(
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "=SECTION_CODE" => $arParams["SECTION_CODE"],
                    "=SECTION_ID" => $arParams["SECTION_ID"]
                );
                $rsSection = CIBlockSection::GetList(array($by => $order), $arSectionFilter, false);
                
                //check section info result
                if ($arSection = $rsSect->GetNext()) {
                    $currentSectionId = $arSection["ID"];
                    
                }
                
            }
            
            //set filter
            if ($arParams["USE_IBLOCK_MAIN_SECTION_TREE"] == "N") {
                $arFilter["=IBLOCK_SECTION_ID"] = $arParams["SECTION_ID"];
            }
            
        }
        
    }
    
    //set tags path
    $tagPath = $arParams["SEF_FOLDER"] . $sectionPath . "/";
    
    //get products tags
    $rsProducts = CIBlockElement::GetList(array(), $arFilter, false, false,
        array("ID", "PROPERTY_TAGS", "IBLOCK_SECTION_ID"));
    
    //each products
    while ($obNextProduct = $rsProducts->GetNextElement()) {
        
        //get product fields
        $arProduct = $obNextProduct->GetFields();
        
        //check settings
        if ($arParams["USE_IBLOCK_MAIN_SECTION"] == "Y" && $arParams["USE_IBLOCK_MAIN_SECTION_TREE"] == "Y") {
            if (!DwItemInfo::checkTagSectionTree($currentSectionId, $arProduct["IBLOCK_SECTION_ID"])) {
                continue 1;
            }
        }
        
        //check tags
        if (!empty($arProduct["PROPERTY_TAGS_VALUE"])) {
            
            
            //parse tags
            $arTags = $arProduct["PROPERTY_TAGS_VALUE"];
            
            //each tags
            foreach ($arTags as $inx => $tagName) {
                
                // Получаем значение сортировки для конкретного значения свойста
                $db_enum_list = CIBlockProperty::GetPropertyEnum("TAGS", array(),
                    array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "VALUE" => $tagName));
                $ar_enum_list = $db_enum_list->GetNext();
                
                
                //translit tag
                $tagCode = Cutil::translit($tagName, LANGUAGE_ID,
                    array("change_case" => "L", "replace_space" => "-", "replace_other" => "-"));
                
                //tag info
                $arTag = array("LINK" => $tagPath . "tag/" . $tagCode . "/",
                    "NAME" => $tagName, "CODE" => $tagCode, "COUNTER" => 1, "SORT" => $ar_enum_list['SORT']);
                
                //selected
                if ($arParams["CURRENT_TAG"] == $tagCode) {
                    $arTag["LINK"] = $tagPath;
                    $arTag["SELECTED"] = "Y";
                }
                
                //counter
                if (!empty($arResult["TAGS"][$tagCode])) {
                    $arTag["COUNTER"] = $arResult["TAGS"][$tagCode]["COUNTER"] + 1;
                }
                //if (DwItemInfo::checkTagSectionTree($arParams['SECTION_ID'], $arProduct["IBLOCK_SECTION_ID"])) {
                //push to result array
                
                //  _c($arProduct["PROPERTY_TAGS_VALUE"]);
                
                $arResult["TAGS"][$tagCode] = $arTag;
                //}
                
                if (in_array($arParams['SECTION_ID'], [126])
                    && in_array($arTag['NAME'], ['Новая коллекция CEP', 'Бег и фитнес'])) {
                    unset($arResult["TAGS"][$tagCode]);
                }
                
            }
        }
        
    }
    //limit, sort, etc
    if (!empty($arResult["TAGS"])) {
        
        
        if (!empty($arResult["TAGS"][$arParams["CURRENT_TAG"]])) {
            
            foreach ($arResult["TAGS"] as $key => $value) {
                if ($value['SELECTED'] == 'Y') {
                    $arResult["TAGS"][$key][$arParams["SORT_FIELD"]] = ($arParams["SORT_TYPE"] == "DESC" ? 9999999 : 0);
                }
            }
        }
        
        //sort tags
        
        uasort($arResult["TAGS"], function ($a, $b) use ($arParams) {
            
            if ($a[$arParams["SORT_FIELD"]] == $b[$arParams["SORT_FIELD"]]) {
                return false;
            }
            
            //desc
            if ($arParams["SORT_TYPE"] == "DESC") {
                return ($a[$arParams["SORT_FIELD"]] > $b[$arParams["SORT_FIELD"]]) ? -1 : 1;
            } //asc
            elseif ($arParams["SORT_TYPE"] == "ASC") {
                return ($a[$arParams["SORT_FIELD"]] < $b[$arParams["SORT_FIELD"]]) ? -1 : 1;
                
            }
            
        });
        
        
        //max tags
        $arResult["TAGS"] = array_slice($arResult["TAGS"], 0, intval($arParams["MAX_TAGS"]), true);
        
    }
    //__($arResult['TAGS']);
    
    //push template
    $this->IncludeComponentTemplate();
    
}

//set filter, title, h1, keywords, description, etc
if (!empty($arResult["TAGS"][$arParams["CURRENT_TAG"]])) {
    
    //write tag
    $arResult["CURRENT_TAG"] = $arResult["TAGS"][$arParams["CURRENT_TAG"]];
    
    //filter for products
    $arrFilter["?PROPERTY_TAGS_VALUE"] = $arResult["CURRENT_TAG"]["NAME"];
    
    //for smart filter
    $preFilter["?PROPERTY_TAGS_VALUE"] = $arResult["CURRENT_TAG"]["NAME"];
    
    $METATAGS_IB = 36;
    $META_SECTION_ID = $arParams["SECTION_ID"];
    $META_TAG = $arResult["CURRENT_TAG"]['NAME'];
    $metaFilter = [
        "IBLOCK_ID" => $METATAGS_IB,
        "NAME" => $META_TAG,
        "ACTIVE" => "Y",
        "PROPERTY_SECTION" => $META_SECTION_ID
    ];
    $obElements = CIBlockElement::GetList([], $metaFilter, false, false, [
        "PROPERTY_H1",
        "PROPERTY_TITLE",
        "PROPERTY_DESCRIPTION",
        "PREVIEW_TEXT"
    ]);
    if ($metaElement = $obElements->GetNext()) {
        $arResult["SEO"] = [
            'SEO_TITLE' => $metaElement['PROPERTY_TITLE_VALUE'],
            'SEO_DESCRIPTION' => $metaElement['PROPERTY_DESCRIPTION_VALUE'],
            'SEO_HEADING' => $metaElement['PROPERTY_H1_VALUE'],
            'PAGE_TEXT' => $metaElement['PREVIEW_TEXT']
        ];
    } else {
        
        //get seo templates by sections
        $arResult["SEO"] = DwItemInfo::getSeoByTag(
            trim($arResult["CURRENT_TAG"]["NAME"]),
            $arParams["CURRENT_TAG"],
            $arParams["IBLOCK_ID"],
            $arParams["SECTION_ID"]);
    }
    
    /*if ($arParams["SECTION_ID"] > 0)
    {
        $obTagSect = CIBlockSection::GetList(
            ["ID" => "ASC"],
            ['ID'=>$arParams["SECTION_ID"]],
            false,
            ["ID", "IBLOCK_SECTION_ID", "NAME"]
        );
        if ($arTagSect = $obTagSect->GetNext()) {

            $ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arParams["IBLOCK_ID"], $arParams["SECTION_ID"] ); //для раздела

            $arSeoProps = $ipropValues->getValues();

            $sect_title = ($arSeoProps['SECTION_PAGE_TITLE'] ? $arSeoProps['SECTION_PAGE_TITLE'] : $arTagSect['NAME']);

            $addTitle = $sect_title.' '.mb_strtolower($arResult["CURRENT_TAG"]["NAME"]);

            $APPLICATION->SetTitle($addTitle);
            $APPLICATION->SetPageProperty("title", $addTitle);
            $arResult["SEO"]["SEO_TITLE"] = $addTitle;
            $arResult["SEO"]["SEO_TAG_TITLE"] = $addTitle;
            $arResult["SEO"]["SEO_HEADING"] = $addTitle;


        }
    }
    else {
        $APPLICATION->SetTitle($arResult["CURRENT_TAG"]);
        $APPLICATION->SetPageProperty("title", $arResult["CURRENT_TAG"]);
    }*/
    //
    return $arResult;
    
} //set 404 error
else {
    if (!empty($arParams["CURRENT_TAG"])) {
        if (Loader::includeModule("iblock")) {
            Iblock\Component\Tools::process404(
                trim($arParams["MESSAGE_404"]) ?: GetMessage("CATALOG_TAGS_MESSAGE_404")
                , true
                , $arParams["SET_STATUS_404"] === "Y"
                , $arParams["SHOW_404"] === "Y"
                , $arParams["FILE_404"]
            );
        }
    }
    
    return false;
}

?>
