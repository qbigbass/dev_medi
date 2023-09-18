<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>
<? global $APPLICATION; ?>
<?
if (!empty($arResult["VARIABLES"]["ELEMENT_CODE"])) {
    $arSelect = array("ID", "IBLOCK_ID", "NAME", "DETAIL_TEXT", "DETAIL_PICTURE", "PREVIEW_PICTURE", "PREVIEW_TEXT", "SECTION_PAGE_URL", "PROPERTY_BG_IMAGE", "PROPERTY_BG_IMAGE_M");
    $arFilter = array("IBLOCK_ID" => IntVal($arParams["IBLOCK_ID"]), "=CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", 'PROPERTY_SHOW_VALUE' => 'Да');
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    if ($ob = $res->GetNextElement()) {
        $arResult["ITEM"] = $ob->GetFields();
        $ELEMENT_ID = $arResult["ITEM"]["ID"];
        $ELEMENT_NAME = $arResult["ITEM"]["NAME"];
    } else {
        LocalRedirect("/brands/", false, "301 Moved Permanently");
    }
}
?>
<?
if (CModule::IncludeModule("iblock")) {
    if (!empty($ELEMENT_ID)) {
        
        $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues(
            $arParams["IBLOCK_ID"],
            $ELEMENT_ID
        );
        
        if ($arSeoProp = $ipropValues->getValues()) {
            
            $APPLICATION->SetPageProperty("description", $arSeoProp["ELEMENT_META_DESCRIPTION"] . ' 📞 Звоните +7 495 225-06-00.');
            $APPLICATION->SetPageProperty("keywords", $arSeoProp["ELEMENT_META_KEYWORDS"]);
            
            if (!empty($arSeoProp["ELEMENT_META_TITLE"])) {
                $APPLICATION->SetPageProperty("title", $arSeoProp["ELEMENT_META_TITLE"]);
                $APPLICATION->SetTitle($arSeoProp["ELEMENT_META_TITLE"]);
            }
            
            if (!empty($arSeoProp["ELEMENT_PAGE_TITLE"])) {
                $APPLICATION->AddChainItem($arSeoProp["ELEMENT_PAGE_TITLE"]);
            }
            
        } else {
            $APPLICATION->AddChainItem($ELEMENT_NAME);
            $APPLICATION->SetTitle($ELEMENT_NAME);
        }
        
    }
}
?>

<? if (!empty($arResult)): ?>
    <?
    //get banner picture resize
    if (!empty($arResult['ITEM']["PROPERTY_BG_IMAGE_VALUE"])) {
        $arResult['ITEM']["RESIZE_BANNER_PICTURE"] = CFile::ResizeImageGet($arResult['ITEM']["PROPERTY_BG_IMAGE_VALUE"], array("width" => 1480, "height" => 500), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 90);
    }
    //get detail picture resize
    if (!empty($arResult['ITEM']["DETAIL_PICTURE"])) {
        $arResult['ITEM']["RESIZE_DETAIL_PICTURE"] = CFile::ResizeImageGet($arResult['ITEM']["DETAIL_PICTURE"], array("width" => 550, "height" => 500), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 90);
    }
    if (!empty($arResult['ITEM']["PROPERTY_BG_IMAGE_M_VALUE"])) {
        $arResult['ITEM']["RESIZE_PREVIEW_PICTURE"] = CFile::ResizeImageGet($arResult['ITEM']["PROPERTY_BG_IMAGE_M_VALUE"], array("width" => 480, "height" => 330), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 90);
    }
    /*elseif(!empty($arResult['ITEM']["PREVIEW_PICTURE"])){
        $arResult['ITEM']["RESIZE_PREVIEW_PICTURE"] = CFile::ResizeImageGet($arResult['ITEM']["PREVIEW_PICTURE"], array("width" => 480, "height" => 330), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 90);
    }*/
    ?>
    <? //include view
    ?>
    <? //$this->SetViewTarget("before_breadcrumb_container");?>
    <div class="blog-banner banner-wrap banner-bg">
        <div class="banner-animated fullscreen-banner banner-elem"
             style="background-image: url('<?= $arResult['ITEM']["RESIZE_BANNER_PICTURE"]["src"] ?>');">

            <div class="limiter">
                <div class="tb">
                    <div class="text-wrap tc">
                        <div class="tb">
                            <div class="tr">
                                <div class="tc">
                                    <div class="header__title_bg">
                                        
                                        <? if (!empty($arResult['ITEM']["NAME"])): ?>
                                            <? $BIG_PICTURE = CFile::ResizeImageGet($arResult["ITEM"]["DETAIL_PICTURE"], array("width" => 250, "height" => 150), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 100); ?>
                                            
                                            <? if (!empty($BIG_PICTURE["src"])): ?>
                                                <div class="brandsBigPicture"><img src="<?= $BIG_PICTURE["src"] ?>"
                                                                                   alt="<?= $arResult["ITEM"]["NAME"] ?>">
                                                </div>
                                            <? endif; ?>
                                            
                                            <? if (!empty($arResult["ITEM"]["PREVIEW_TEXT"])): ?>
                                                <div class="brandsDescription"><?= $arResult["ITEM"]["PREVIEW_TEXT"] ?></div>
                                            <? endif; ?>
                                        
                                        <? endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="image tc">
                        <? /*if(!empty($arResult['ITEM']["RESIZE_PREVIEW_PICTURE"])):?>
									<img src="<?=$arResult['ITEM']["RESIZE_PREVIEW_PICTURE"]["src"]?>" alt="<?=$arResult['ITEM']["NAME"]?>">
							<?endif;*/ ?>
                    </div>
                </div>
            </div>

        </div>
        
        <? if (!empty($arResult['ITEM']["PROPERTY_BG_IMAGE_M_VALUE"])) { ?>
            <div class="banner-animated-small fullscreen-banner banner-elem"
                 style="background-image: url('<?= $arResult['ITEM']["RESIZE_PREVIEW_PICTURE"]["src"] ?>')">

                <div class="limiter">
                    <div class="tb">
                        <div class="text-wrap tc">
                            <div class="tb">
                                <? if (!empty($arResult['ITEM']["NAME"])): ?>
                                    <? $BIG_PICTURE = CFile::ResizeImageGet($arResult["ITEM"]["DETAIL_PICTURE"], array("width" => 250, "height" => 150), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 100); ?>
                                    
                                    <? if (!empty($BIG_PICTURE["src"])): ?>
                                        <div class="brandsBigPicture"><img src="<?= $BIG_PICTURE["src"] ?>"
                                                                           alt="<?= $arResult["ITEM"]["NAME"] ?>"></div>
                                    <? endif; ?>
                                <? endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>
        <div class="small-header">
            <? if (!empty($arResult['ITEM']["NAME"])): ?>
                <? if (!empty($arResult['ITEM']["NAME"])): ?>
                    
                    <? if (!empty($arResult["ITEM"]["PREVIEW_TEXT"])): ?>
                        <div class="brandsDescription"><?= $arResult["ITEM"]["~PREVIEW_TEXT"] ?></div>
                    <? endif; ?>
                
                <? endif; ?>
            
            <? endif; ?>
        </div>

    </div>


    <meta property="og:title" content="<?= $arResult["NAME"] ?>"/>
    <meta property="og:description" content="<?= htmlspecialcharsbx($arResult["PREVIEW_TEXT"]) ?>"/>
    <meta property="og:url" content="<?= $arResult["DETAIL_PAGE_URL"] ?>"/>
    <meta property="og:type" content="website"/>
    <? if (!empty($arResult["RESIZE_DETAIL_PICTURE"])): ?>
        <meta property="og:image" content="<?= $arResult["RESIZE_DETAIL_PICTURE"]["src"] ?>"/>
    <? endif; ?>
<? endif; ?>

<? if (!empty($ELEMENT_ID)): ?>
    
    <? $BASE_PRICE = CCatalogGroup::GetBaseGroup(); ?>
    <? $arSortFields = array(
        "SHOWS" => array(
            "ORDER" => "DESC",
            "CODE" => "SHOWS",
            "NAME" => GetMessage("CATALOG_SORT_FIELD_SHOWS")
        ),
        /*"NAME" => array( // параметр в url
            "ORDER"=> "ASC", //в возрастающем порядке
            "CODE" => "NAME", // Код поля для сортировки
            "NAME" => GetMessage("CATALOG_SORT_FIELD_NAME") // имя для вывода в публичной части, редактировать в (/lang/ru/section.php)
        ),*/
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
    ); ?>
    
    <?
    //get template settings
    $arTemplateSettings = DwSettings::getInstance()->getCurrentSettings();
    if (empty($arTemplateSettings["TEMPLATE_USE_AUTO_SAVE_PRICE"]) || $arTemplateSettings["TEMPLATE_USE_AUTO_SAVE_PRICE"] == "N") {
        $arSortFields["PRICE_ASC"] = array(
            "ORDER" => "ASC",
            "CODE" => "CATALOG_PRICE_" . $BASE_PRICE["ID"],
            "NAME" => GetMessage("CATALOG_SORT_FIELD_PRICE_ASC")
        );
        $arSortFields["PRICE_DESC"] = array(
            "ORDER" => "DESC",
            "CODE" => "CATALOG_PRICE_" . $BASE_PRICE["ID"],
            "NAME" => GetMessage("CATALOG_SORT_FIELD_PRICE_DESC")
        );
    }
    ?>
    <? if (!empty($_REQUEST["SORT_FIELD"]) && !empty($arSortFields[$_REQUEST["SORT_FIELD"]])) {
        
        setcookie("CATALOG_SORT_FIELD", $_REQUEST["SORT_FIELD"], time() + 60 * 60 * 24 * 30 * 12 * 2, "/");
        
        $arParams["ELEMENT_SORT_FIELD"] = $arSortFields[$_REQUEST["SORT_FIELD"]]["CODE"];
        $arParams["ELEMENT_SORT_ORDER"] = $arSortFields[$_REQUEST["SORT_FIELD"]]["ORDER"];
        
        $arSortFields[$_REQUEST["SORT_FIELD"]]["SELECTED"] = "Y";
        
    } elseif (!empty($_COOKIE["CATALOG_SORT_FIELD"]) && !empty($arSortFields[$_COOKIE["CATALOG_SORT_FIELD"]])) { // COOKIE
        
        $arParams["ELEMENT_SORT_FIELD"] = $arSortFields[$_COOKIE["CATALOG_SORT_FIELD"]]["CODE"];
        $arParams["ELEMENT_SORT_ORDER"] = $arSortFields[$_COOKIE["CATALOG_SORT_FIELD"]]["ORDER"];
        
        $arSortFields[$_COOKIE["CATALOG_SORT_FIELD"]]["SELECTED"] = "Y";
    } else {
        
        $arParams["ELEMENT_SORT_FIELD"] = 'SORT';
        $arParams["ELEMENT_SORT_ORDER"] = 'ASC';
    }
    ?>
    
    <? $arSortProductNumber = array(
        24 => array("NAME" => 24),
        36 => array("NAME" => 36),
        48 => array("NAME" => 48)
    );
    $arParams["PAGE_ELEMENT_COUNT"] = 24; ?>
    
    <? if (!empty($_REQUEST["SORT_TO"]) && $arSortProductNumber[$_REQUEST["SORT_TO"]]) {
        setcookie("CATALOG_SORT_TO", $_REQUEST["SORT_TO"], time() + 60 * 60 * 24 * 30 * 12 * 2, "/");
        $arSortProductNumber[$_REQUEST["SORT_TO"]]["SELECTED"] = "Y";
        $arParams["PAGE_ELEMENT_COUNT"] = $_REQUEST["SORT_TO"];
    } elseif (!empty($_COOKIE["CATALOG_SORT_TO"]) && $arSortProductNumber[$_COOKIE["CATALOG_SORT_TO"]]) {
        $arSortProductNumber[$_COOKIE["CATALOG_SORT_TO"]]["SELECTED"] = "Y";
        $arParams["PAGE_ELEMENT_COUNT"] = $_COOKIE["CATALOG_SORT_TO"];
    } ?>
    
    <? $arTemplates = array(
        "SQUARES" => array(
            "CLASS" => "squares"
        ),
        /*"LINE" => array(
            "CLASS" => "line"
        ),
        "TABLE" => array(
            "CLASS" => "table"
        )*/
    ); ?>
    
    <? if (!empty($_REQUEST["VIEW"]) && $arTemplates[$_REQUEST["VIEW"]]) {
        setcookie("CATALOG_VIEW", $_REQUEST["VIEW"], time() + 60 * 60 * 24 * 30 * 12 * 2);
        $arTemplates[$_REQUEST["VIEW"]]["SELECTED"] = "Y";
        $arParams["CATALOG_TEMPLATE"] = $_REQUEST["VIEW"];
    } elseif (!empty($_COOKIE["CATALOG_VIEW"]) && $arTemplates[$_COOKIE["CATALOG_VIEW"]]) {
        $arTemplates[$_COOKIE["CATALOG_VIEW"]]["SELECTED"] = "Y";
        $arParams["CATALOG_TEMPLATE"] = $_COOKIE["CATALOG_VIEW"];
    } else {
        $arTemplates[key($arTemplates)]["SELECTED"] = "Y";
    }
    
    
    //filter for component
    global $arrFilter;
    $arrFilter["PROPERTY_ATT_BRAND"] = $ELEMENT_ID;
    $arrFilter["IBLOCK_ID"] = $arParams["PRODUCT_IBLOCK_ID"];
    $arrFilter["ACTIVE"] = "Y";
    $arrFilter["PROPERTY_CITY_VALUE"] = $GLOBALS['medi']['region_cities'][SITE_ID];
    
    //filter for calc
    $arFilter = array(
        "IBLOCK_ID" => $arParams["PRODUCT_IBLOCK_ID"],
        "PROPERTY_ATT_BRAND" => $ELEMENT_ID,
        "ACTIVE" => "Y",
        "PROPERTY_REGION_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID]
    );
    
    if ($arParams["HIDE_NOT_AVAILABLE"] == "Y") {
        $arFilter["CATALOG_AVAILABLE"] = "Y";
    }
    
    $countElements = CIBlockElement::GetList(array(), $arrFilter, array(), false);
    
    ?>
    <? if ($countElements > 1) {
        
        $arSections = array();
        $arResult["MENU_SECTIONS"] = array();
        $arFilter["SECTION_ID"] = array();
        
        $res = CIBlockElement::GetList(array("NAME" => "ASC"), $arFilter, false, false, array("ID"));
        while ($nextElement = $res->GetNext()) {
            $resGroup = CIBlockElement::GetElementGroups($nextElement["ID"], false);
            while ($arGroup = $resGroup->Fetch()) {
                if ($arGroup["ACTIVE"] == "Y") {
                    $IBLOCK_SECTION_ID = $arGroup["ID"];
                    $arSections[$IBLOCK_SECTION_ID] = $IBLOCK_SECTION_ID;
                    $arSectionCount[$IBLOCK_SECTION_ID] = !empty($arSectionCount[$IBLOCK_SECTION_ID]) ? $arSectionCount[$IBLOCK_SECTION_ID] + 1 : 1;
                }
            }
            
            $arResult["ITEMS"][] = $nextElement;
        }
        
        if (!empty($arSections)) {
            $arFilter = array("ID" => $arSections);
            $rsSections = CIBlockSection::GetList(array("NAME" => "ASC"), $arFilter);
            while ($arSection = $rsSections->Fetch()) {
                $searchParam = "SECTION_ID=" . $arSection["ID"];
                $searchID = intval($_GET["SECTION_ID"]);
                $arSection["SELECTED"] = $arSection["ID"] == $searchID ? Y : N;
                $arSection["FILTER_LINK"] = $APPLICATION->GetCurPageParam($searchParam, array("SECTION_ID"));
                $arSection["ELEMENTS_COUNT"] = $arSectionCount[$arSection["ID"]];
                array_push($arResult["MENU_SECTIONS"], $arSection);
            }
        }
        
    } ?>
    
    <?
    $OPTION_CURRENCY = CCurrency::GetBaseCurrency();
    ?>
    
    
    <? if ($countElements > 0): ?>
        <div id="catalog" data-list-name="Бренды" class="productsListName"
             <? if ($countElements == 1): ?>class="alone-item"<? endif; ?>>
            <h1 class="brandsHeading"><? if (!empty($arSeoProp["ELEMENT_PAGE_TITLE"])): ?><?= $arSeoProp["ELEMENT_PAGE_TITLE"] ?><? else: ?><?= GetMessage("CATALOG_TITLE") ?><?= $ELEMENT_NAME ?><? endif; ?></h1>
            <? if ($countElements > 1): ?>
            <div id="catalogColumn">
                <div class="leftColumn">
                    
                    <? $GLOBALS['arrFilter']["PROPERTY_REGION_VALUE"] = $GLOBALS['medi']['region_cities'][SITE_ID]; ?>
                    <? $APPLICATION->IncludeComponent(
                        "dresscode:cast.smart.filter",
                        ".default",
                        array(
                            "FILTER_HIDE_PROPS" => [394, 134, 133],
                            "IBLOCK_TYPE" => "catalog",
                            "IBLOCK_ID" => "17",
                            "SECTION_ID" => $_REQUEST["SECTION_ID"],
                            "FILTER_NAME" => $arParams["PRODUCT_FILTER_NAME"],
                            "HIDE_NOT_AVAILABLE" => "N",
                            "SHOW_ALL_WO_SECTION" => "Y",
                            "INCLUDE_SUBSECTIONS" => "Y",
                            "CACHE_TYPE" => "A",
                            "CACHE_TIME" => "360000",
                            "CACHE_GROUPS" => "N",
                            "SAVE_IN_SESSION" => "N",
                            "INSTANT_RELOAD" => "N",
                            "PRICE_CODE" => $arParams["PRICE_CODE"],
                            "XML_EXPORT" => "N",
                            "SECTION_TITLE" => "-",
                            "SECTION_DESCRIPTION" => "-",
                            "CONVERT_CURRENCY" => "N",
                            "CURRENCY_ID" => "RUB",
                            "FILTER_ADD_PROPERTY_NAME" => "ATT_BRAND",
                            "FILTER_ADD_PROPERTY_VALUE" => $ELEMENT_ID,
                            "COMPONENT_TEMPLATE" => ".default",
                            "SEF_MODE" => "Y",
                            "SEF_RULE" => "",
                            "SECTION_CODE" => "",
                            "SECTION_CODE_PATH" => "",
                            "SMART_FILTER_PATH" => "",
                            "PAGER_PARAMS_NAME" => "arrPager"
                        ),
                        false
                    ); ?>
                </div>
                <? endif; ?>
                <? if ($countElements > 1): ?>
                <div class="rightColumn">
                    <div class="catalog_items">
                        <? endif; ?>
                        <div id="catalogLine">
                            <? if ($countElements > 1): ?>
                                <div class="column oFilter">
                                    <a href="#" class="oSmartFilter btn-simple btn-micro"><span
                                                class="ico"></span><?= GetMessage("CATALOG_FILTER") ?></a>
                                </div>
                            <? endif; ?>
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
										<option value="<?=$APPLICATION->GetCurPageParam("SORT_TO=".$arSortNumberElementId, array("SORT_TO"));?>"<?if($arSortNumberElement["SELECTED"] == "Y"):?> selected<?endif;?>><?=$arSortNumberElement["NAME"]?></option>
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
                        <?
                        reset($arTemplates);
                        ?>
                        
                        <?
                        global $arrFilter;
                        unset($arrFilter["FACET_OPTIONS"]);
                        ?>
                        
                        <? $arrFilter["FACET_OPTIONS"] = array(); ?>
                        
                        <? $GLOBALS['arrFilter'][] = ["PROPERTY_REGION_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID]]; ?>
                        <? $APPLICATION->IncludeComponent(
                            "dresscode:catalog.section",
                            'brand_items',
                            array(
                                "IBLOCK_TYPE" => $arParams["PRODUCT_IBLOCK_TYPE"],
                                "IBLOCK_ID" => $arParams["PRODUCT_IBLOCK_ID"],
                                "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
                                "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
                                "INCLUDE_SUBSECTIONS" => "N",
                                "FILTER_NAME" => $arParams["PRODUCT_FILTER_NAME"],
                                "PRICE_CODE" => $arParams["PRODUCT_PRICE_CODE"],
                                "PROPERTY_CODE" => $arParams["PRODUCT_PROPERTY_CODE"],
                                "PAGER_TEMPLATE" => "round",
                                "PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
                                "CONVERT_CURRENCY" => $arParams["PRODUCT_CONVERT_CURRENCY"],
                                "CURRENCY_ID" => $arParams["PRODUCT_CURRENCY_ID"],
                                "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                                "HIDE_MEASURES" => $arParams["HIDE_MEASURES"],
                                "SECTION_ID" => $_REQUEST["SECTION_ID"],
                                "HIDE_DESCRIPTION_TEXT" => "Y",
                                "SHOW_ALL_WO_SECTION" => "Y",
                                "ENABLED_SKU_FILTER" => "N",
                                "ADD_SECTIONS_CHAIN" => "N",
                                "SET_BROWSER_TITLE" => "N",
                                "SET_TITLE" => "N",
                                "CACHE_FILTER" => "Y",
                                "CACHE_TYPE" => "A",
                                "CACHE_GROUPS" => "N",
                                "AJAX_MODE" => "N"
                            ),
                            $component
                        ); ?>
                        <? if ($countElements > 1): ?>
                    </div>
                </div>
            </div>
        <? endif; ?>
        </div>
    <? else: ?>
        <style>
            .backToList {
                display: inline-block;
                margin-bottom: 24px;
                float: none;
            }
        </style>
    <? endif; ?>


<? else: ?>
    
    <?
    if (!defined("ERROR_404"))
        define("ERROR_404", "Y");
    
    \CHTTP::setStatus("404 Not Found");
    
    if ($APPLICATION->RestartWorkarea()) {
        require(\Bitrix\Main\Application::getDocumentRoot() . "/404.php");
        die();
    }
    ?>

<?endif;

$viewBrandName = 'view_' . strtolower($arResult['ITEM']['NAME']);
?>
<script>
    var _gcTracker = _gcTracker || [];
    _gcTracker.push(['view_page', {name: '<?=$viewBrandName?>'}]);
</script>