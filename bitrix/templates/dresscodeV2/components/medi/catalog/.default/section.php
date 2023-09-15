<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true); ?>
<?
if (CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog")){

//global vars
global $APPLICATION;

$arSortFields = array(
    "SORT" => array(
        "ORDER" => "DESC",
        "CODE" => "SORT",
        "NAME" => GetMessage("CATALOG_SORT_FIELD_SHOWS")
    ),
    "PRICE_ASC" => array(
        "ORDER" => "ASC",
        "CODE" => "PROPERTY_MINIMUM_PRICE",  // изменен для сортировки по ТП
        "NAME" => GetMessage("CATALOG_SORT_FIELD_PRICE_ASC")
    ),
    "PRICE_DESC" => array(
        "ORDER" => "DESC",
        "CODE" => "PROPERTY_MAXIMUM_PRICE", // изменен для сортировки по ТП
        "NAME" => GetMessage("CATALOG_SORT_FIELD_PRICE_DESC")
    )
);

$arTemplates = array(
    "SQUARES" => array(
        "CLASS" => "squares"
    ),
);

//get section ID for smart filter
$arFilter = array(
    "ACTIVE" => "Y",
    "GLOBAL_ACTIVE" => "Y",
    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
);

if (!empty($arResult["VARIABLES"]["SECTION_ID"])) {
    $arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
} elseif (!empty($arResult["VARIABLES"]["SECTION_CODE"])) {
    $arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
}


if ($arResult['VARIABLES']['SECTION_CODE_PATH'] && empty($arResult["VARIABLES"]["TAG"])) {
    
    $APPLICATION->AddHeadString('<link href="https://www.medi-salon.ru/catalog/' . $arResult['VARIABLES']['SECTION_CODE_PATH'] . '/' . (isset($_GET['PAGEN_1']) ? "?PAGEN_1=" . intval($_GET['PAGEN_1']) : "") . '" rel="canonical"/>');
} elseif ($arResult['VARIABLES']['SECTION_CODE_PATH'] && !empty($arResult["VARIABLES"]["TAG"])) {
    $APPLICATION->AddHeadString('<link href="https://www.medi-salon.ru/catalog/' . $arResult['VARIABLES']['SECTION_CODE_PATH'] . '/tag/' . $arResult["VARIABLES"]["TAG"] . '/' . (isset($_GET['PAGEN_1']) ? "?PAGEN_1=" . intval($_GET['PAGEN_1']) : "") . '" rel="canonical"/>');
    
}

//start cache
$obCache = new CPHPCache();

//get from cache
if ($obCache->InitCache(36000, serialize(array_merge($arFilter, [SITE_ID])), "/")) {
    $arCachedVars = $obCache->GetVars();
    $arCurSection = $arCachedVars["SECTION"];
    $arResult["PRICE_SORT_FROM_PROPERTY"] = $arCachedVars["PRICE_SORT_FROM_PROPERTY"];
    $arResult["IPROPERTY_VALUES"] = $arCachedVars["IPROPERTY_VALUES"];
    $arResult["SECTION_BANNERS"] = $arCachedVars["SECTION_BANNERS"];
    $arResult["BASE_PRICE"] = $arCachedVars["BASE_PRICE"];
    $arResult["SECTION_META"] = $arCachedVars["SECTION_META"];
    
} //no cache
elseif ($obCache->StartDataCache()) {
    
    $arCurSection = array();
    $dbRes = CIBlockSection::GetList(array(), $arFilter, false, array("ID", "NAME", "IBLOCK_ID", "UF_*"));
    
    if ($arCurSection = $dbRes->GetNext()) {
        
        if ($arCurSection['UF_H2_HEAD']) {
            $arCurSection['SECTION_META']['H2_HEAD'] = $arCurSection['UF_H2_HEAD'];
        }
        
        if (defined("BX_COMP_MANAGED_CACHE")) {
            
            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache("/");
            $CACHE_MANAGER->RegisterTag("iblock_id_" . $arParams["IBLOCK_ID"] . "_" . serialize($arParams["CATALOG_TEMPLATE"]));
            $CACHE_MANAGER->EndTagCache();
            
        }
        
    } else {
        if (!$arCurSection = $dbRes->GetNext()) {
            $arCurSection = array();
        }
    }
    $arResult["SECTION_META"] = $arCurSection['SECTION_META'];
    $ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arCurSection["IBLOCK_ID"], $arCurSection["ID"]);
    $arResult["IPROPERTY_VALUES"] = $ipropValues->getValues();
    $arResult["BASE_PRICE"] = CCatalogGroup::GetBaseGroup();
    
    //check for available min_price and max_price property
    $rsMinPriceProperty = CIBlock::GetProperties($arParams["IBLOCK_ID"], array(), array("CODE" => "MINIMUM_PRICE"));
    $arResult["PRICE_SORT_FROM_PROPERTY"] = $rsMinPriceProperty->SelectedRowsCount() == 1 ? "Y" : "N";
    
    // get section banner
    $arResult["SECTION_BANNERS"] = array();
    if (empty($arParams["SHOW_SECTION_BANNER"]) || !empty($arParams["SHOW_SECTION_BANNER"]) && $arParams["SHOW_SECTION_BANNER"] == "Y") {
        if (!empty($arResult["VARIABLES"]["SECTION_ID"])) {
            /*$arSectionID = array();
            $navChain = CIBlockSection::GetNavChain($arParams["IBLOCK_ID"], $arResult["VARIABLES"]["SECTION_ID"]);
            while($arNextSection = $navChain->GetNext()){
                $arSectionID[$arNextSection["ID"]] = $arNextSection["ID"];
            }*/
            if (!empty($arCurSection["ID"])) {
                $rsElements = CIBlockElement::GetList(array("SORT" => "ASC"), array("IBLOCK_ID" => 31, "PROPERTY_SECTION" => $arCurSection["ID"], "ACTIVE" => "Y", "ACTIVE_DATE" => "Y", "PROPERTY_REGION_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID]), false, false, array("ID", "IBLOCK_ID", "DETAIL_PICTURE", "PREVIEW_PICTURE", 'PROPERTY_LINK'));
                $ib = 0;
                while ($arElems = $rsElements->GetNext()) {
                    
                    if (!empty($arElems["DETAIL_PICTURE"])) {
                        $arResult["SECTION_BANNERS"][$ib]["IMAGE"] = CFile::ResizeImageGet($arElems["DETAIL_PICTURE"], array("width" => 2560, "height" => 1440), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                        if (!empty($arElems["PROPERTY_LINK_VALUE"])) {
                            $arResult["SECTION_BANNERS"][$ib]["LINK"] = $arElems["PROPERTY_LINK_VALUE"];
                        }
                    }
                    if (!empty($arElems["PREVIEW_PICTURE"])) {
                        $arResult["SECTION_BANNERS"][$ib]["MOB_IMAGE"] = CFile::ResizeImageGet($arElems["PREVIEW_PICTURE"], array("width" => 2560, "height" => 1440), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                        if (!empty($arElems["PROPERTY_LINK_VALUE"])) {
                            $arResult["SECTION_BANNERS"][$ib]["MOB_LINK"] = $arElems["PROPERTY_LINK_VALUE"];
                        }
                    }
                    $ib++;
                }
            }
        }
    }
    $obCache->EndDataCache(
        array(
            "SECTION" => $arCurSection,
            "BASE_PRICE" => $arResult["BASE_PRICE"],
            "SECTION_BANNERS" => $arResult["SECTION_BANNERS"],
            "IPROPERTY_VALUES" => $arResult["IPROPERTY_VALUES"],
            "PRICE_SORT_FROM_PROPERTY" => $arResult["PRICE_SORT_FROM_PROPERTY"],
            "CATALOG_TEMPLATE" => $arTemplates,
            "SECTION_META" => $arCurSection["SECTION_META"]
        )
    );
    
}

if ($arResult["PRICE_SORT_FROM_PROPERTY"] == "N") {
    $arSortFields["PRICE_ASC"] = array(
        "ORDER" => "ASC",
        "CODE" => "CATALOG_PRICE_" . $arResult["BASE_PRICE"]["ID"],
        "NAME" => GetMessage("CATALOG_SORT_FIELD_PRICE_ASC")
    );
    $arSortFields["PRICE_DESC"] = array(
        "ORDER" => "DESC",
        "CODE" => "CATALOG_PRICE_" . $arResult["BASE_PRICE"]["ID"],
        "NAME" => GetMessage("CATALOG_SORT_FIELD_PRICE_DESC")
    );
}

?>
<h1><?= $APPLICATION->ShowTitle(false); ?>
    <?/*
if(!empty($arResult["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"])):?>
<?=$arResult["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"]?>
<?else:?><?=$arCurSection["NAME"]?><?endif;
*/
    ?></h1>
<? if (!empty($arResult["SECTION_BANNERS"])
    /*&& (!strpos($APPLICATION->GetCurDir(), '/action_sign-is-')
        || strpos($APPLICATION->GetCurDir(), '/action_sign-is-medo0423/')
    )*/): ?>
    <div id="catalog-section-banners">
        <ul class="slideBox">
            <? foreach ($arResult["SECTION_BANNERS"] as $isc => $arNextBanner): ?>
                <? if (!empty($arNextBanner["IMAGE"])): ?>
                    <li>
                        <a<? if (!empty($arNextBanner["LINK"])): ?> href="<?= $arNextBanner["LINK"] ?>"<? endif; ?>
                                class="cat-slider">
                            <? if (!empty($arParams["LAZY_LOAD_PICTURES"]) && $arParams["LAZY_LOAD_PICTURES"] == "Y"): ?>
                                <img src="<?= $templateFolder ?>/images/lazy.jpg"
                                     data-lazy="<?= $arNextBanner["IMAGE"]["src"] ?>" class="lazy cat-slider-img"
                                     alt="">
                            <? else: ?>
                                <img src="<?= $arNextBanner["IMAGE"]["src"] ?>" class="lazy  cat-slider-img" alt="">
                            <? endif; ?>
                        </a>
                        <? if ($arNextBanner['MOB_IMAGE']) {
                            ?>
                            <a<? if (!empty($arNextBanner["MOB_LINK"])): ?> href="<?= $arNextBanner["MOB_LINK"] ?>"<? endif; ?>
                                    class="cat-slider-mob">
                                <? if (!empty($arParams["LAZY_LOAD_PICTURES"]) && $arParams["LAZY_LOAD_PICTURES"] == "Y"): ?>
                                    <img src="<?= $templateFolder ?>/images/lazy.jpg"
                                         data-lazy="<?= $arNextBanner["MOB_IMAGE"]["src"] ?>"
                                         class="lazy cat-slider-img" alt="">
                                <? else: ?>
                                    <img src="<?= $arNextBanner["MOB_IMAGE"]["src"] ?>" class="lazy  cat-slider-img"
                                         alt="">
                                <? endif; ?>
                            </a>
                            <?
                        } ?>

                    </li>
                <? endif; ?>
            <? endforeach; ?>
        </ul>
        <? if (count($arResult["SECTION_BANNERS"]) > 1) {
            ?>
            <a href="#" class="catalog-section-banners-btn-left"></a>
            <a href="#" class="catalog-section-banners-btn-right"></a>

            <script>
                $(function () {
                    $("#catalog-section-banners").dwSlider({
                        rightButton: ".catalog-section-banners-btn-right",
                        leftButton: ".catalog-section-banners-btn-left",
                        delay: 4500,
                        speed: 1000
                    });
                });
            </script>
            <?
        } ?>
    </div>
<? endif; ?>

<? if (!empty($arParams["CATALOG_SHOW_TAGS"]) && $arParams["CATALOG_SHOW_TAGS"] == "Y"):
    
    $allSects = [];
    if ($arResult['VARIABLES']['SECTION_ID']) {
        $allSects = CIBlockSection::GetNavChain(false, $arResult['VARIABLES']['SECTION_ID'], ['ID'], true);
        
    } ?>
    
    <?
    if (!empty($allSects) && in_array($allSects[0]['ID'], [75, 88, 116, 126, 84])) {
        $arTags = $APPLICATION->IncludeComponent(
            "medi:catalog.tags",
            "",
            array(
                "CACHE_TIME" => $arParams["CACHE_TIME"],
                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                "SEF_FOLDER" => $arResult["FOLDER"],
                "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                "SECTION_CODE_PATH" => $arResult["VARIABLES"]["SECTION_CODE_PATH"],
                "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
                "USE_IBLOCK_MAIN_SECTION" => $arParams["CATALOG_TAGS_USE_IBLOCK_MAIN_SECTION"],
                "USE_IBLOCK_MAIN_SECTION_TREE" => $arParams["CATALOG_TAGS_USE_IBLOCK_MAIN_SECTION_TREE"],
                "CURRENT_TAG" => $arResult["VARIABLES"]["TAG"],
                "MESSAGE_404" => $arParams["MESSAGE_404"],
                "SET_STATUS_404" => $arParams["SET_STATUS_404"],
                "SHOW_404" => $arParams["SHOW_404"],
                "FILE_404" => $arParams["FILE_404"],
                "MAX_TAGS" => $arParams["CATALOG_MAX_TAGS"],
                "SECTION_DEPTH_LEVEL" => $arCurSection["DEPTH_LEVEL"],
                "TAGS_MAX_DEPTH_LEVEL" => $arParams["CATALOG_TAGS_MAX_DEPTH_LEVEL"],
                "MAX_VISIBLE_TAGS_DESKTOP" => $arParams["CATALOG_MAX_VISIBLE_TAGS_DESKTOP"],
                "MAX_VISIBLE_TAGS_MOBILE" => $arParams["CATALOG_MAX_VISIBLE_TAGS_MOBILE"],
                "HIDE_TAGS_ON_MOBILE" => $arParams["CATALOG_HIDE_TAGS_ON_MOBILE"],
                "SORT_FIELD" => $arParams["CATALOG_TAGS_SORT_FIELD"],
                "SORT_TYPE" => $arParams["CATALOG_TAGS_SORT_TYPE"],
            ),
            false,
            array("HIDE_ICONS" => "Y", "ACTIVE_COMPONENT" => "Y")
        );
    } ?>
<? endif; ?>
<div id="catalogColumn">
    <div class="leftColumn">
        <? $APPLICATION->IncludeComponent(
            "bitrix:catalog.section.list",
            "level2",
            array(
                "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                "CACHE_TIME" => $arParams["CACHE_TIME"],
                "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                "COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
                "TOP_DEPTH" => 1,
                "FILTER_NAME" => $arParams["FILTER_NAME"],
                "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
                "VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
                "SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
                "HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
                "ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : '')
            ),
            $component
        ); ?>
        <div id="smartFilterCont">
            <? $GLOBALS['smartPreFilter'] = ["PROPERTY_REGION_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID]];
            if (!empty($arResult["VARIABLES"]["TAG"])) {
                $GLOBALS['smartPreFilter'] = [
                    "PROPERTY_TAGS_VALUE" => $arTags['CURRENT_TAG']['NAME'],
                    "PROPERTY_REGION_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID]
                ];
            } elseif (!empty($arResult["VARIABLES"]['SMART_FILTER_PATH']) && strpos($arResult["VARIABLES"]['SMART_FILTER_PATH'], "action_sign") !== false) {
                $filter_parts = explode("/", $arResult["VARIABLES"]['SMART_FILTER_PATH']);
                
                foreach ($filter_parts as $k => $part) {
                    if (strpos($part, "action_sign") !== false) {
                        $action_sign = str_replace("action_sign-is-", "", $part);
                        $GLOBALS['smartPreFilter'] = [
                            "PROPERTY_ACTION_SIGN_VALUE" => $action_sign,
                            "PROPERTY_REGION_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID]
                        ];
                    }
                }
            } ?>
            
            <? $APPLICATION->IncludeComponent(
                "bitrix:catalog.smart.filter",
                ".default",
                array(
                    "AJAX_MODE" => "N",
                    "INSTANT_RELOAD" => !empty($arParams["FILTER_INSTANT_RELOAD"]) ? $arParams["FILTER_INSTANT_RELOAD"] : "Y",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_HISTORY" => "Y",
                    "IBLOCK_TYPE" => "catalog",
                    "IBLOCK_ID" => "17",
                    "SECTION_ID" => $arCurSection["ID"],
                    "FILTER_NAME" => $arParams["FILTER_NAME"],
                    "PREFILTER_NAME" => "smartPreFilter",
                    "PRICE_CODE" => array(),
                    "CACHE_TYPE" => "A",
                    "CACHE_FILTER" => "Y",
                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                    "CACHE_GROUPS" => "N",
                    "SAVE_IN_SESSION" => "N",
                    "FILTER_VIEW_MODE" => $arParams["FILTER_VIEW_MODE"],
                    "XML_EXPORT" => "Y",
                    "SECTION_TITLE" => "NAME",
                    "SECTION_DESCRIPTION" => "DESCRIPTION",
                    "HIDE_NOT_AVAILABLE" => "Y",
                    "TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
                    "CONVERT_CURRENCY" => "N",
                    "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                    "SEF_MODE" => "Y",
                    "FILTER_HIDE_PROPS" => $arParams['FILTER_HIDE_PROPS'],
                    "SEF_RULE" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["smart_filter"],
                    "SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
                    "PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
                    "COMPONENT_TEMPLATE" => ".default",
                    "SECTION_CODE" => "",
                    "SECTION_CODE_PATH" => ""
                ),
                false
            ); ?></div>
        <?
        }
        ?>
    </div>
    <div class="rightColumn">
        
        <? if (strpos($APPLICATION->GetCurDir(), '/action_sign-is-')) {
            
            $arParams["ELEMENT_SORT_FIELD"] = "PROPERTY_ACTION_SORT";
            $arParams["ELEMENT_SORT_ORDER"] = 'DESC';
            
            $arParams["ELEMENT_SORT_FIELD2"] = "SORT";
            $arParams["ELEMENT_SORT_ORDER2"] = 'DESC';
            setcookie("CATALOG_SORT_FIELD" . $arResult["VARIABLES"]["SECTION_ID"], "PROPERTY_ACTION_SORT", time() + 60 * 60 * 24 * 30 * 12 * 2, "/");
        } ?>
        
        <? if (!empty($_REQUEST["SORT_FIELD"]) && !empty($arSortFields[$_REQUEST["SORT_FIELD"]])) {
            
            setcookie("CATALOG_SORT_FIELD" . $arResult["VARIABLES"]["SECTION_ID"], $_REQUEST["SORT_FIELD"], time() + 60 * 60 * 24 * 30 * 12 * 2, "/");
            
            $arParams["ELEMENT_SORT_FIELD"] = $arSortFields[$_REQUEST["SORT_FIELD"]]["CODE"];
            $arParams["ELEMENT_SORT_ORDER"] = $arSortFields[$_REQUEST["SORT_FIELD"]]["ORDER"];
            
            $arSortFields[$_REQUEST["SORT_FIELD"]]["SELECTED"] = "Y";
            
        } elseif (!empty($_COOKIE["CATALOG_SORT_FIELD" . $arResult["VARIABLES"]["SECTION_ID"]])
            && !empty($arSortFields[$_COOKIE["CATALOG_SORT_FIELD" . $arResult["VARIABLES"]["SECTION_ID"]]])) { // COOKIE
            
            $arParams["ELEMENT_SORT_FIELD"] = $arSortFields[$_COOKIE["CATALOG_SORT_FIELD" . $arResult["VARIABLES"]["SECTION_ID"]]]["CODE"];
            $arParams["ELEMENT_SORT_ORDER"] = $arSortFields[$_COOKIE["CATALOG_SORT_FIELD" . $arResult["VARIABLES"]["SECTION_ID"]]]["ORDER"];
            
            $arSortFields[$_COOKIE["CATALOG_SORT_FIELD" . $arResult["VARIABLES"]["SECTION_ID"]]]["SELECTED"] = "Y";
        }
        ?>
        
        
        <? $arTemplates = array(
            "SQUARES" => array(
                "CLASS" => "squares"
            ),
        ); ?>
        
        <? $APPLICATION->IncludeComponent(
            "dresscode:slider",
            "middle",
            array(
                "IBLOCK_TYPE" => "sliders",
                "IBLOCK_ID" => "27",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "3600000",
                "PICTURE_WIDTH" => "1476",
                "PICTURE_HEIGHT" => "202",
                "COMPONENT_TEMPLATE" => "middle",
                "LAZY_LOAD_PICTURES" => !empty($arParams["LAZY_LOAD_PICTURES"]) ? $arParams["LAZY_LOAD_PICTURES"] : "N",
            ),
            false
        ); ?>

        <div id="catalog" class="productsListName" data-list-name="Каталог"
             data-section-path="<?= $arResult['VARIABLES']['SECTION_CODE_PATH'] ?>">
            <? $APPLICATION->IncludeComponent(
                "bitrix:catalog.section.list",
                "catalog-pictures",
                array(
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                    "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                    "COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
                    "TOP_DEPTH" => 1,
                    "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
                    "VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
                    "SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
                    "LAZY_LOAD_PICTURES" => !empty($arParams["LAZY_LOAD_PICTURES"]) ? $arParams["LAZY_LOAD_PICTURES"] : "N",
                    "HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
                    "ADD_SECTIONS_CHAIN" => "N"
                ),
                $component
            ); ?>
            <div id="catalogLine">
                <?
                if ($arResult['SECTION_META']['H2_HEAD']) { ?>
                    <div class="column">
                        <h2><?= $arResult['SECTION_META']['H2_HEAD'] ?></h2>
                    </div>
                <? } ?>

                <div class="column oFilter">
                    <a href="#" class="oSmartFilter btn-simple btn-micro"><span
                                class="ico"></span><?= GetMessage("CATALOG_FILTER") ?></a>
                </div>
                <? if (!empty($arSortFields)): ?>
                    <div class="column">
                        <div class="label">
                            <?= GetMessage("CATALOG_SORT_LABEL"); ?>
                        </div>
                        <select class="medi-select">
                            <? foreach ($arSortFields as $arSortFieldCode => $arSortField): ?>
                                <option value="<?= "?SORT_FIELD=" . $arSortFieldCode; ?>"<? if ($arSortField["SELECTED"] == "Y"): ?> selected<? endif; ?>><?= $arSortField["NAME"] ?></option>
                            <? endforeach; ?>
                        </select>
                    </div>
                <? endif; ?>
                <? /*if(!empty($arSortProductNumber)):?>
					<div class="column">
						<div class="label">
							<?=GetMessage("CATALOG_SORT_TO_LABEL");?>
						</div>
						<select name="countElements" id="selectCountElements">
							<?foreach ($arSortProductNumber as $arSortNumberElementId => $arSortNumberElement):?>
								<option value="?SORT_TO="<?=$arSortNumberElementId;?>"<?if($arSortNumberElement["SELECTED"] == "Y"):?> selected<?endif;?>><?=$arSortNumberElement["NAME"]?></option>
							<?endforeach;?>
						</select>
					</div>
				<?endif;*/ ?>
                <? /*if(!empty($arTemplates)):?>
					<div class="column">
						<div class="label">
							<?=GetMessage("CATALOG_VIEW_LABEL");?>
						</div>
						<div class="viewList">
							<?foreach ($arTemplates as $arTemplatesCode => $arNextTemplate):?>
								<div class="element"><a<?if($arNextTemplate["SELECTED"] != "Y"):?> href="<?=$APPLICATION->GetCurPageParam("VIEW=".$arTemplatesCode, array("VIEW"));?>"<?endif;?> class="<?=$arNextTemplate["CLASS"]?><?if($arNextTemplate["SELECTED"] == "Y"):?> selected<?endif;?>"></a></div>
							<?endforeach;?>
						</div>
					</div>
				<?endif;*/ ?>
            </div>
            <? reset($arTemplates); ?>
            <div id="ajaxSection">
                <?
                $view = "squares";
                
                $arParams['VIEW_MODE'] = $view; ?>
                <? $APPLICATION->IncludeComponent(
                    "dresscode:catalog.section",
                    'squares',
                    array(
                        "AJAX_MODE" => "Y",
                        "VIEW_MODE" => "squares",
                        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                        "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
                        "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
                        "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                        "ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
                        "PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
                        "META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
                        "META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
                        "BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
                        "INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
                        "BASKET_URL" => $arParams["BASKET_URL"],
                        "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                        "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                        "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
                        "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                        "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                        "FILTER_NAME" => $arParams["FILTER_NAME"],
                        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                        "CACHE_TIME" => $arParams["CACHE_TIME"],
                        "CACHE_FILTER" => $arParams["CACHE_FILTER"],
                        "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                        "SET_TITLE" => $arParams["SET_TITLE"],
                        "SET_STATUS_404" => $arParams["SET_STATUS_404"],
                        "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                        "PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
                        "LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
                        "PRICE_CODE" => $arParams["PRICE_CODE"],
                        "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                        "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
                        "HIDE_MEASURES" => $arParams["HIDE_MEASURES"],
                        "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                        "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                        "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                        "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                        "PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
                        "SHOW_SECTION_BANNER" => !empty($arParams["SHOW_SECTION_BANNER"]) ? $arParams["SHOW_SECTION_BANNER"] : "Y",
                        "DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
                        "DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
                        "PAGER_TITLE" => $arParams["PAGER_TITLE"],
                        "PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
                        "PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
                        "PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
                        "PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
                        "PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
                        
                        "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
                        "OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
                        "OFFERS_PROPERTY_CODE" => $arParams["LIST_OFFERS_PROPERTY_CODE"],
                        "OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
                        "OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
                        "OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
                        "OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
                        "OFFERS_LIMIT" => $arParams["LIST_OFFERS_LIMIT"],
                        
                        "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
                        "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
                        "SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
                        "DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
                        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                        'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                        'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
                        
                        'LABEL_PROP' => $arParams['LABEL_PROP'],
                        'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
                        'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
                        
                        'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
                        'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
                        'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
                        'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
                        'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
                        'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
                        'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
                        'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
                        'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
                        'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
                        
                        "USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
                        'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
                        "LAZY_LOAD_PICTURES" => !empty($arParams["LAZY_LOAD_PICTURES"]) ? $arParams["LAZY_LOAD_PICTURES"] : "N",
                        'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                        "ENABLED_SKU_FILTER" => "Y",
                        "ADD_SECTIONS_CHAIN" => "N"
                    ),
                    $component
                ); ?>
            </div>
            
            <? if (!empty($arCurSection["DESCRIPTION"]) && empty($arTags) && empty($arResult["VARIABLES"]["TAG"])): ?>
                <? if (!DwSettings::isPagen()): ?>
                <div class="sectionTopDescription"><?= $arCurSection["~DESCRIPTION"] ?></div>
            <? endif; ?>
            <? elseif (!empty($arTags) && !empty($arTags['SEO']['PAGE_TEXT'])): ?>
                <div class="medi-openning-shadow-block">
                    <div class="medi-shadow-block-content">
                        <div class="medi-shadow-block-text-content"></div>
                        <div><?= $arTags['SEO']['PAGE_TEXT'] ?></div>
                    </div>
                    <div id="medi-openning-more-button" class="medi-position-more-button">Подробнее</div>
                </div>
                <script>
                    if ($('#medi-openning-more-button').length) {
                        $('#medi-openning-more-button').on("click", function () {
                            if (!$(this).data('status')) {
                                $('.medi-openning-shadow-block').addClass("is-active");
                                $(this).html('Скрыть');
                                $(this).data('status', true);
                            } else {
                                $('.medi-openning-shadow-block').removeClass("is-active");
                                $(this).html('Подробнее');
                                $(this).data('status', false);
                            }
                        });
                    }
                </script>
            <? endif; ?>
            
            <? if (!empty($arParams["CATALOG_SHOW_TAGS"]) && $arParams["CATALOG_SHOW_TAGS"] == "Y"): ?>
                <? if (!empty($arTags)): ?>
                    <?
                    if (!empty(intval($_GET['PAGEN_1']))
                        && intval($_GET['PAGEN_1']) > 1
                        && !empty($arTags["SEO"]["SEO_TITLE"])) {
                        $arTags["SEO"]["SEO_TITLE"] = explode("|", $arTags["SEO"]["SEO_TITLE"])[0] .
                            ' | Страница ' . intval($_GET['PAGEN_1']) . ' | ' . explode("|", $arTags["SEO"]["SEO_TITLE"])[1];
                    }
                    ?>
                    <? $APPLICATION->IncludeComponent(
                        "dresscode:catalog.tags.meta",
                        "",
                        array(
                            "META_HEADING" => $arTags["SEO"]["SEO_HEADING"],
                            "META_TITLE" => $arTags["SEO"]["SEO_TITLE"],
                            "PAGE_TITLE" => $arTags["SEO"]["SEO_TITLE"],
                            "META_KEYWORDS" => $arTags["SEO"]["SEO_KEYWORDS"],
                            "META_DESCRIPTION" => $arTags["SEO"]["SEO_DESCRIPTION"],
                            "TAG_NAME" => $arTags["CURRENT_TAG"]["NAME"]
                        ),
                        false,
                        array("HIDE_ICONS" => "Y", "ACTIVE_COMPONENT" => "Y")
                    ) ?>
                <? endif; ?>
            <? endif; ?>
        </div>
    </div>
</div>