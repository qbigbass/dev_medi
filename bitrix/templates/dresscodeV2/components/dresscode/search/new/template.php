<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?
$this->setFrameMode(false);
$GLOBALS['searchFilter'] = ["PROPERTY_REGION_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID]];
?>
<div style="display: none">
    <? $arIDS = $APPLICATION->IncludeComponent(
        "arturgolubev:search.page",
        "catalog",
        array(
            "ACTION_VARIABLE" => "action",
            "AJAX_MODE" => "N",
            "AJAX_OPTION_ADDITIONAL" => "",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "BASKET_URL" => "/personal/cart",
            "CACHE_TIME" => "36000000",
            "CACHE_TYPE" => "N",
            "CHECK_DATES" => "Y",
            "COMPOSITE_FRAME_MODE" => "A",
            "COMPOSITE_FRAME_TYPE" => "AUTO",
            "CONVERT_CURRENCY" => "N",
            "DEFAULT_SORT" => "rank",
            "DETAIL_URL" => "",
            "DISPLAY_BOTTOM_PAGER" => "Y",
            "DISPLAY_COMPARE" => "N",
            "DISPLAY_TOP_PAGER" => "N",
            "ELEMENT_SORT_FIELD" => "RANK",
            "ELEMENT_SORT_FIELD2" => "sort",
            "ELEMENT_SORT_ORDER" => "asc",
            "ELEMENT_SORT_ORDER2" => "asc",
            "FILTER_NAME" => "searchFilter",
            "HIDE_NOT_AVAILABLE" => "Y",
            "HIDE_NOT_AVAILABLE_OFFERS" => "N",
            "IBLOCK_ID" => "17",
            "IBLOCK_TYPE" => "catalog",
            "INPUT_PLACEHOLDER" => "",
            "LINE_ELEMENT_COUNT" => "4",
            "NO_WORD_LOGIC" => "N",
            "OFFERS_CART_PROPERTIES" => array("CML2_ARTICLE", "COLOR"),
            "OFFERS_FIELD_CODE" => array("", ""),
            "OFFERS_LIMIT" => "5",
            "OFFERS_PROPERTY_CODE" => array("CML2_ARTICLE", ""),
            "OFFERS_SORT_FIELD" => "sort",
            "OFFERS_SORT_FIELD2" => "id",
            "OFFERS_SORT_ORDER" => "asc",
            "OFFERS_SORT_ORDER2" => "desc",
            "PAGER_DESC_NUMBERING" => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
            "PAGER_SHOW_ALL" => "N",
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_TEMPLATE" => "round",
            "PAGER_TITLE" => "Товары",
            "PAGE_ELEMENT_COUNT" => "24",
            "PAGE_RESULT_COUNT" => "1800",
            "PRICE_CODE" => array(),
            "PRICE_VAT_INCLUDE" => "Y",
            "PRODUCT_ID_VARIABLE" => "id",
            "PRODUCT_PROPERTIES" => array(),
            "PRODUCT_PROPS_VARIABLE" => "prop",
            "PRODUCT_QUANTITY_VARIABLE" => "quantity",
            "PROPERTY_CODE" => array("CML2_ARTICLE", ""),
            "SECTION_ID_VARIABLE" => "SECTION_ID",
            "SECTION_URL" => "",
            "SHOW_HISTORY" => "N",
            "SHOW_PRICE_COUNT" => "1",
            "SHOW_WHEN" => "N",
            "SHOW_WHERE" => "N",
            "USE_LANGUAGE_GUESS" => "Y",
            "USE_PRICE_COUNT" => "N",
            "USE_PRODUCT_QUANTITY" => "N",
            "arrFILTER" => array("iblock_catalog"),
            "arrFILTER_iblock_catalog" => array("17"),
            "arrWHERE" => array()
        )
    ); ?>
    <? global $arrFilter;
    
    $obOffersExists = CIBlockElement::GetList(array(), array("IBLOCK_ID" => 17, "ACTIVE" => "Y", "ID" => $arIDS, "PROPERTY_REGION_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID]), false, false, array("PROPERTY_CML2_LINK.ID"));
    $arOffersIds = [];
    while ($arOffers = $obOffersExists->GetNext()) {
        
        // добавим в поиск товары у которых sku с подзодящим артикулом
        $obOffers = CIBlockElement::GetList(array(), array("IBLOCK_ID" => 19, "ACTIVE" => "Y", "?PROPERTY_CML2_ARTICLE" => $_REQUEST["q"], "PROPERTY_REGION_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID]), false, false, array("PROPERTY_CML2_LINK.ID"));
        $arOffersIds = [];
        while ($arOffers = $obOffers->GetNext()) {
            
            $arIDS[] = $arOffers['PROPERTY_CML2_LINK_ID'];
            echo $arOffers['PROPERTY_CML2_LINK_ID'] . "<br>";
        }
    }
    
    $arrFilter = array();
    $arrFilter["ID"] = array_values($arIDS);
    
    $arResult["ITEMS_ID"] = array_values($arIDS);
    $arResult["ITEMS"] = array_values($arIDS);
    
    
    ?>
</div>
<h1><?= GetMessage("SEARCH_PAGE") ?></h1>
<? if (!empty($arResult["ITEMS"])): ?>
<div id="catalogColumn">
    <div class="leftColumn">
        <?
        $OPTION_CURRENCY = CCurrency::GetBaseCurrency();
        ?>
        
        <? /*if(!empty($arResult["MENU_SECTIONS"]) && count($arResult["MENU_SECTIONS"]) > 1):?>
			<div id="nextSection">
				<span class="title"><?=GetMessage("SELECT_CATEGORY");?></span>
				<ul>
					<?foreach ($arResult["MENU_SECTIONS"] as $ic => $arSection):?>
						<li><a href="<?=$arSection["FILTER_LINK"]?>"<?if($arSection["SELECTED"] == "Y"):?> class="selected"<?endif;?>><?=$arSection["NAME"]?> (<?=$arSection["ELEMENTS_COUNT"]?>)</a></li>
					<?endforeach;?>
				</ul>
			</div>
		<?endif;*/ ?>
        
        <? if (count($arResult["ITEMS"]) > 1): ?>
            <? $APPLICATION->IncludeComponent(
                "dresscode:search.smart.filter",
                "",
                array(
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "SECTION_ID" => $_REQUEST["SECTION_ID"],
                    "PREFILTER_NAME" => "arrFilter",
                    "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                    "SHOW_ALL_WO_SECTION" => "Y",
                    "ELEMENTS_ID" => $arResult["ITEMS_ID"],
                    "CACHE_TYPE" => "N",
                    "CACHE_TIME" => "36000000",
                    "CACHE_GROUPS" => "Y",
                    "SAVE_IN_SESSION" => "N",
                    "INSTANT_RELOAD" => "N",
                    "FILTER_HIDE_PROPS" => [394, 133],
                    "PRICE_CODE" => $arParams["FILTER_PRICE_CODE"],
                    "XML_EXPORT" => "N",
                    "SECTION_TITLE" => "-",
                    "SECTION_DESCRIPTION" => "-",
                    "CONVERT_CURRENCY" => "N",
                    "CURRENCY_ID" => $OPTION_CURRENCY
                ),
                false
            ); ?>
        <? endif; ?>
    </div>
    <div class="rightColumn">
        
        <? $BASE_PRICE = CCatalogGroup::GetBaseGroup(); ?>
        <? $arSortFields = array(
            "SHOWS" => array(
                "ORDER" => "ASC",
                "CODE" => "SORT",
                "NAME" => GetMessage("CATALOG_SORT_FIELD_SHOWS")
            ),
            /*"NAME" => array(
                "ORDER"=> "ASC",
                "CODE" => "NAME",
                "NAME" => GetMessage("CATALOG_SORT_FIELD_NAME")
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
            $arParams["ELEMENT_SORT_ORDER"] = $arSortFields[$_COOKIE["CATALOG_SORT_ORDER"]];
            
            $arSortFields[$_COOKIE["CATALOG_SORT_FIELD"]]["SELECTED"] = "Y";
            
            $arParams["ELEMENT_SORT_FIELD"] = $arSortFields[$_COOKIE["CATALOG_SORT_FIELD"]]["CODE"];
            $arParams["ELEMENT_SORT_ORDER"] = $arSortFields[$_COOKIE["CATALOG_SORT_FIELD"]]["ORDER"];
            
            $arSortFields[$_COOKIE["CATALOG_SORT_FIELD" . $arResult["VARIABLES"]["SECTION_ID"]]]["SELECTED"] = "Y";
        }
        ?>
        
        <? $arSortProductNumber = array(
            30 => array("NAME" => 30),
            60 => array("NAME" => 60),
            90 => array("NAME" => 90)
        ); ?>
        
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
        
        ?>
        <div id="catalogLine">
            <? if (count($arResult["ITEMS"]) > 1): ?>
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
                            <option value="?<?= DeleteParam(array("SORT_FIELD")) . "&SORT_FIELD=" . $arSortFieldCode; ?>"<? if ($arSortField["SELECTED"] == "Y"): ?> selected<? endif; ?>><?= $arSortField["NAME"] ?></option>
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
					<?endif;?>
					<?if(!empty($arTemplates)):?>
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
        <? endif; ?>
        
        <? if (!empty($arResult["SECTIONS"])): ?>
            <h2><?= GetMessage("SECTION_FOR_SEARCH") ?> &laquo;<?= $arResult["QUERY"] ?>&raquo;</h2>
            <ul id="searchSection">
                <? foreach ($arResult["SECTIONS"] as $i => $arSection): ?>
                    <li><a href="<?= $arSection["SECTION_PAGE_URL"] ?>"><?= $arSection["NAME"] ?></a></li>
                <? endforeach; ?>
            </ul>
        <? endif; ?>
        
        <? if (!empty($arResult["ITEMS"])): ?>
        <h3><?= GetMessage("PRODUCT_FOR_SEARCH") ?> &laquo;<?= $arResult["QUERY"] ?>&raquo;</h3>
        <? if (!empty($arResult["QUERY_REPLACE"])): ?>
            <p><?= GetMessage("ERROR_IN_QUERTY") ?></p>
        <? endif; ?>
        <?
        unset($arrFilter["FACET_OPTIONS"]);
        ?>
        <div id="catalog" <? if (count($arResult["ITEMS"]) == 1): ?>class="alone-item"<? endif; ?>
             class="productsListName" data-list-name="Результаты поиска">
            <? // $arrFilter["FACET_OPTIONS"] = array(); ?>
            
            <? //$arrFilter["PROPERTY_REGION_VALUE"] = $GLOBALS['medi']['region_cities'][SITE_ID]; ?>
            <? reset($arTemplates); ?>
            <? _c($arParams) ?>
            <? $APPLICATION->IncludeComponent(
                "dresscode:catalog.section",
                "squares",
                array(
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
                    "ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
                    "PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
                    "PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
                    "PRICE_CODE" => $arParams["PRICE_CODE"],
                    "PAGER_TEMPLATE" => "round",
                    'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
                    'CURRENCY_ID' => $arParams['CURRENCY_ID'],
                    "FILTER_NAME" => 'arrFilter',
                    "ADD_SECTIONS_CHAIN" => "N",
                    "SHOW_ALL_WO_SECTION" => "Y",
                    "SECTION_ID" => "",
                    "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                    "LAZY_LOAD_PICTURES" => $arParams["LAZY_LOAD_PICTURES"],
                    "HIDE_MEASURES" => $arParams["HIDE_MEASURES"],
                    "CACHE_TYPE" => "A",
                    "CACHE_FILTER" => "Y"
                ),
                false
            ); ?>
        </div>
    </div>
</div>
<? else: ?>
    <div id="empty">
        <div class="emptyWrapper">
            <div class="pictureContainer">
                <img src="<?= SITE_TEMPLATE_PATH ?>/images/emptyFolder.png" alt="<?= GetMessage("EMPTY_HEADING") ?>"
                     class="emptyImg">
            </div>
            <div class="info">
                <h3><?= GetMessage("Q") ?></h3>
                <p><?= GetMessage("EMPTY") ?></p>
                <a href="<?= SITE_DIR ?>" class="back"><?= GetMessage("MAIN") ?></a>
            </div>
        </div>
        <? /*$APPLICATION->IncludeComponent("bitrix:menu", "emptyMenu", Array(
					"ROOT_MENU_TYPE" => "left",
						"MENU_CACHE_TYPE" => "N",
						"MENU_CACHE_TIME" => "3600",
						"MENU_CACHE_USE_GROUPS" => "Y",
						"MENU_CACHE_GET_VARS" => "",
						"MAX_LEVEL" => "1",
						"CHILD_MENU_TYPE" => "left",
						"USE_EXT" => "Y",
						"DELAY" => "N",
						"ALLOW_MULTI_SELECT" => "N",
					),
					false
				);*/ ?>
    </div>
<? endif; ?>
<br>
<br>
<br>