<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Новинки");
$APPLICATION->SetTitle("Новинки");


//global vars

$arSortFields = array(
    "SORT" => array(
        "ORDER"=> "ASC",
        "CODE" => "SORT",
        "NAME" => "по популярности"
    ),
    /*"NAME" => array( // параметр в url
        "ORDER"=> "ASC", //в возрастающем порядке
        "CODE" => "NAME", // Код поля для сортировки
        "NAME" => GetMessage("CATALOG_SORT_FIELD_NAME") // имя для вывода в публичной части, редактировать в (/lang/ru/section.php)
    ),*/
    "PRICE_ASC"=> array(
        "ORDER"=> "ASC",
        "CODE" => "PROPERTY_MINIMUM_PRICE",  // изменен для сортировки по ТП
        "NAME" => "по увеличению цены"
    ),
    "PRICE_DESC" => array(
        "ORDER"=> "DESC",
        "CODE" => "PROPERTY_MAXIMUM_PRICE", // изменен для сортировки по ТП
        "NAME" => "по уменьшению цены"
    )
);

$arTemplates = array(
    "SQUARES" => array(
        "CLASS" => "squares"
    ),
    /*"LINE" => array(
        "CLASS" => "line"
    ),
    "TABLE" => array(
        "CLASS" => "table"
    )*/
);




//get section ID for smart filter
$arFilter = array(
    "ACTIVE" => "Y",
    "GLOBAL_ACTIVE" => "Y",
    "IBLOCK_ID" => 17,
    "SITE_ID" => SITE_ID
);




    $arSortFields["PRICE_ASC"] = array(
        "ORDER"=> "ASC",
        "CODE" => "CATALOG_PRICE_2",
        "NAME" => "по увеличению цены"
    );
    $arSortFields["PRICE_DESC"] = array(
        "ORDER"=> "DESC",
        "CODE" => "CATALOG_PRICE_2",
        "NAME" => "по уменьшению цены"
    );


?>
<h1><?=$APPLICATION->ShowTitle(false);?></h1>


<div id="catalogColumn">
    <div class="leftColumn">

        <div id="smartFilterCont">

            <?
            $GLOBALS['arrFilter'] = ['PROPERTY_OFFERS' => '509'];
            $GLOBALS['smartPreFilter'] = ["PROPERTY_REGION_VALUE"=> $GLOBALS['medi']['region_cities'][SITE_ID]];?>
            <?//$GLOBALS['smartPreFilter'] = ["PROPERTY_REGION_VALUE"=> $GLOBALS['medi']['region_cities'][SITE_ID]];?>
            <?$APPLICATION->IncludeComponent(
                "bitrix:catalog.smart.filter",
                ".default",
                array(
                    "AJAX_MODE" => "N",
                    "INSTANT_RELOAD" => "Y",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_HISTORY" => "Y",
                    "IBLOCK_TYPE" => "catalog",
                    "IBLOCK_ID" => "17",
                    "SECTION_ID" => "",
                    "SECTION_CODE" => "new",
                    "FILTER_NAME" => "arrFilter",
                    "PREFILTER_NAME"=> "smartPreFilter",
                    "PRICE_CODE" => array(

                    ),
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" =>  86400,
                    "CACHE_GROUPS" => "N",
                    "SAVE_IN_SESSION" => "N",
                    "FILTER_VIEW_MODE" => 'VERTICAL',
                    "XML_EXPORT" => "Y",
                    "SECTION_TITLE" => "NAME",
                    "SECTION_DESCRIPTION" => "DESCRIPTION",
                    "HIDE_NOT_AVAILABLE" => "N",
                    "TEMPLATE_THEME" => "site",
                    "CONVERT_CURRENCY" => "N",
                    "CURRENCY_ID" => "RUB",
                    "SEF_MODE" => "Y",
                    "FILTER_HIDE_PROPS" => [394,133],
                    "SEF_RULE" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"],
                    "SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
                    "PAGER_PARAMS_NAME" => "arrPager",
                    "COMPONENT_TEMPLATE" => ".default",
                    "SECTION_CODE" => "",
                    "SECTION_CODE_PATH" => ""
                ),
                false
            );?></div>
        <?

        ?>
    </div>
    <div class="rightColumn">
        <?if(!empty($_REQUEST["SORT_FIELD"]) && !empty($arSortFields[$_REQUEST["SORT_FIELD"]])){

            setcookie("CATALOG_SORT_FIELD", $_REQUEST["SORT_FIELD"], time() + 60 * 60 * 24 * 30 * 12 * 2, "/");

            $arParams["ELEMENT_SORT_FIELD"] = $arSortFields[$_REQUEST["SORT_FIELD"]]["CODE"];
            $arParams["ELEMENT_SORT_ORDER"] = $arSortFields[$_REQUEST["SORT_FIELD"]]["ORDER"];

            $arSortFields[$_REQUEST["SORT_FIELD"]]["SELECTED"] = "Y";

        }elseif(!empty($_COOKIE["CATALOG_SORT_FIELD"]) && !empty($arSortFields[$_COOKIE["CATALOG_SORT_FIELD"]])){ // COOKIE

            $arParams["ELEMENT_SORT_FIELD"] = $arSortFields[$_COOKIE["CATALOG_SORT_FIELD"]]["CODE"];
            $arParams["ELEMENT_SORT_ORDER"] = $arSortFields[$_COOKIE["CATALOG_SORT_FIELD"]]["ORDER"];

            $arSortFields[$_COOKIE["CATALOG_SORT_FIELD"]]["SELECTED"] = "Y";
        }
        ?>

        <?/*$arSortProductNumber = array(
			24 => array("NAME" => 24),
			36 => array("NAME" => 36),
			48 => array("NAME" => 48)
		);?>

		<?if(!empty($_REQUEST["SORT_TO"]) && $arSortProductNumber[$_REQUEST["SORT_TO"]]){
			setcookie("CATALOG_SORT_TO", $_REQUEST["SORT_TO"], time() + 60 * 60 * 24 * 30 * 12 * 2, "/");
			$arSortProductNumber[$_REQUEST["SORT_TO"]]["SELECTED"] = "Y";
			$arParams["PAGE_ELEMENT_COUNT"] = $_REQUEST["SORT_TO"];
		}elseif (!empty($_COOKIE["CATALOG_SORT_TO"]) && $arSortProductNumber[$_COOKIE["CATALOG_SORT_TO"]]){
			$arSortProductNumber[$_COOKIE["CATALOG_SORT_TO"]]["SELECTED"] = "Y";
			$arParams["PAGE_ELEMENT_COUNT"] = $_COOKIE["CATALOG_SORT_TO"];
		}*/?>

        <?$arTemplates = array(
            "SQUARES" => array(
                "CLASS" => "squares"
            ),
            /*"LINE" => array(
                "CLASS" => "line"
            ),
            "TABLE" => array(
                "CLASS" => "table"
            )*/
        );?>
        <?

        /*if(!empty($_REQUEST["VIEW"]) && $arTemplates[$_REQUEST["VIEW"]]){
            setcookie("CATALOG_VIEW", $_REQUEST["VIEW"], time() + 60 * 60 * 24 * 30 * 12 * 2);
            $arTemplates[$_REQUEST["VIEW"]]["SELECTED"] = "Y";
            $arParams["CATALOG_TEMPLATE"] = $_REQUEST["VIEW"];
        }elseif (!empty($_COOKIE["CATALOG_VIEW"]) && $arTemplates[$_COOKIE["CATALOG_VIEW"]]){
            $arTemplates[$_COOKIE["CATALOG_VIEW"]]["SELECTED"] = "Y";
            $arParams["CATALOG_TEMPLATE"] = $_COOKIE["CATALOG_VIEW"];
        }else{
            $arTemplates[key($arTemplates)]["SELECTED"] = "Y";
        }*/
        ?>

        <?$APPLICATION->IncludeComponent(
            "dresscode:slider",
            "middle",
            array(
                "IBLOCK_TYPE" => "sliders",
                "IBLOCK_ID" => "27",
                "CACHE_TYPE" => "Y",
                "CACHE_TIME" => "3600000",
                "PICTURE_WIDTH" => "1476",
                "PICTURE_HEIGHT" => "202",
                "COMPONENT_TEMPLATE" => "middle",
                "LAZY_LOAD_PICTURES" =>  "Y",
            ),
            false
        );?>
        <div id="catalog" class="productsListName" data-list-name="Каталог"  data-section-path="<?=$arResult['VARIABLES']['SECTION_CODE_PATH']?>">

            <div id="catalogLine">
                <div class="column oFilter">
                    <a href="#" class="oSmartFilter btn-simple btn-micro"><span class="ico"></span>Фильтр</a>
                </div>
                <?if(!empty($arSortFields)):?>
                    <div class="column">
                        <div class="label">
                            Сортировать
                        </div>
                        <select class="medi-select">
                            <?foreach ($arSortFields as $arSortFieldCode => $arSortField):?>
                                <option value="<?="?SORT_FIELD=".$arSortFieldCode;?>"<?if($arSortField["SELECTED"] == "Y"):?> selected<?endif;?>><?=$arSortField["NAME"]?></option>
                            <?endforeach;?>
                        </select>
                    </div>
                <?endif;?>
                <?/*if(!empty($arSortProductNumber)):?>
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
				<?endif;*/?>
                <?/*if(!empty($arTemplates)):?>
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
				<?endif;*/?>
            </div>
            <?reset($arTemplates);?>
            <div id="ajaxSection">
                <?
                $view =  "squares";

                $arParams['VIEW_MODE'] =$view; ?>
                <?$APPLICATION->IncludeComponent(
                    "dresscode:catalog.section",
                    'squares',
                    array(
                        "AJAX_MODE" => "Y",
                        "VIEW_MODE" =>  "squares",
                        "ELEMENT_SORT_FIELD" =>  $arParams["ELEMENT_SORT_FIELD"],
                        "ELEMENT_SORT_ORDER" =>  $arParams["ELEMENT_SORT_ORDER"],
                        "ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                        "ELEMENT_SORT_ORDER2" =>  $arParams["ELEMENT_SORT_ORDER2"],
                        "PROPERTY_CODE" => "",
                        "META_KEYWORDS" => "",
                        "META_DESCRIPTION" => "",
                        "BROWSER_TITLE" => "-",
                        "INCLUDE_SUBSECTIONS" => "Y",
                        "BASKET_URL" => "/personal/cart/",
                        "ACTION_VARIABLE" => "action",
                        "PRODUCT_ID_VARIABLE" => "id",
                        "SECTION_ID_VARIABLE" => "SECTION_ID",
                        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                        "PRODUCT_PROPS_VARIABLE" => "prop",
                        "FILTER_NAME" => "arrFilter",
                        "IBLOCK_TYPE" => "catalog",
                        "IBLOCK_ID" => "17",
                        "SECTION_ID" => "",
                        "SECTION_CODE" => "new",
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" =>  86400,
                        "CACHE_GROUPS" => "N",
                        "SET_TITLE" => "Y",
                        "SET_STATUS_404" => "Y",
                        "DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
                        "PAGE_ELEMENT_COUNT" => "24",
                        "LINE_ELEMENT_COUNT" => "3",
                        "PRICE_CODE" => array(
                            0 => (SITE_ID == 's2' ? "BASE_SPB" : "BASE"),
                        ),
                        "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                        "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
                        "HIDE_MEASURES" => $arParams["HIDE_MEASURES"],
                        "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                        "USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
                        "ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
                        "PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
                        "PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
                        "SHOW_SECTION_BANNER" => !empty($arParams["SHOW_SECTION_BANNER"]) ? $arParams["SHOW_SECTION_BANNER"] : "Y",
                        "PAGER_TEMPLATE" => "round",
                        "DISPLAY_TOP_PAGER" => "N",
                        "DISPLAY_BOTTOM_PAGER" => "Y",
                        "PAGER_TITLE" => "Товары",
                        "PAGER_SHOW_ALWAYS" => "N",
                        "PAGER_DESC_NUMBERING" => "N",
                        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
                        "PAGER_SHOW_ALL" => "N",

                        "OFFERS_CART_PROPERTIES" => array(
                            0 => "COLOR",
                        ),
                        "OFFERS_FIELD_CODE" => [],
                        "OFFERS_PROPERTY_CODE" => [],
                        "OFFERS_SORT_FIELD" => "sort",
                        "OFFERS_SORT_ORDER" => "asc",
                        "OFFERS_SORT_FIELD2" => "NAME",
                        "OFFERS_SORT_ORDER2" => "asc",
                        "OFFERS_LIMIT" => 1,

                        'CONVERT_CURRENCY' => "N",
                        'CURRENCY_ID' => "RUB",
                        'HIDE_NOT_AVAILABLE' => "L",

                        'LABEL_PROP' => "-",
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

                        "USE_MAIN_ELEMENT_SECTION" => "Y",
                        'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
                        "LAZY_LOAD_PICTURES" => !empty($arParams["LAZY_LOAD_PICTURES"]) ? $arParams["LAZY_LOAD_PICTURES"] : "N",
                        'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
                        "ENABLED_SKU_FILTER" => "Y",
                        "ADD_SECTIONS_CHAIN" => "N"
                    ),
                    $component
                );?>
            </div>


        </div>
    </div>
</div>
<br /><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
