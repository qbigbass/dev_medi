<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');
CHTTP::SetStatus("404 Not Found");
@define("ERROR_404", "Y");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("tags", "Акция завершена или недоступна");
$APPLICATION->SetPageProperty("description", "Акция недоступна в вашем регионе или уже завершена");
$APPLICATION->SetPageProperty("keywords_inner", "Акция завершена или недоступна");
$APPLICATION->SetPageProperty("title", "Акция завершена или недоступна");
$APPLICATION->SetPageProperty("keywords", "Акция завершена или недоступна");
$APPLICATION->SetPageProperty("robots", "noindex, nofollow");
$APPLICATION->AddChainItem("Акция завершена или недоступна", "");
?>
<div id="error404">
	<div class="wrapper">
		<h1>Акция недоступна</h1>
        <p>Возможно, она уже&nbsp;завершена или недоступна в&nbsp;выбранном регионе</p>
		<div class="errorText">Ознакомьтесь с&nbsp;действующими <a href="/stock/" class="theme-link-dashed">акциями</a></div>
	</div>
	<div id="other">
		<div class="wrapper">
            <?$GLOBALS['stockFilter'] = ["PROPERTY_CITY_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID], "ACTIVE_DATE"=>"Y", "PROPERTY_HIDE"=>false]?>
            <?$APPLICATION->IncludeComponent(
           	"bitrix:news.list",
           	"stockList",
           	Array(
           		"ACTIVE_DATE_FORMAT" => "d.m.Y",
           		"ADD_SECTIONS_CHAIN" => "N",
           		"AJAX_MODE" => "N",
           		"AJAX_OPTION_ADDITIONAL" => "",
           		"AJAX_OPTION_HISTORY" => "N",
           		"AJAX_OPTION_JUMP" => "N",
           		"AJAX_OPTION_STYLE" => "Y",
           		"CACHE_FILTER" => "Y",
           		"CACHE_GROUPS" => "N",
           		"CACHE_TIME" => "36000000",
           		"CACHE_TYPE" => "A",
           		"CHECK_DATES" => "Y",
           		"DETAIL_URL" => "",
           		"DISPLAY_BOTTOM_PAGER" => "Y",
           		"DISPLAY_DATE" => "Y",
           		"DISPLAY_NAME" => "Y",
           		"DISPLAY_PICTURE" => "Y",
           		"DISPLAY_PREVIEW_TEXT" => "Y",
           		"DISPLAY_TOP_PAGER" => "N",
           		"FIELD_CODE" => array("", ""),
           		"FILTER_NAME" => "stockFilter",
           		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
           		"IBLOCK_ID" => "6",
           		"IBLOCK_TYPE" => "info",
           		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
           		"INCLUDE_SUBSECTIONS" => "N",
           		"MESSAGE_404" => "",
           		"NEWS_COUNT" => "3",
           		"PAGER_BASE_LINK_ENABLE" => "N",
           		"PAGER_DESC_NUMBERING" => "N",
           		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
           		"PAGER_SHOW_ALL" => "N",
           		"PAGER_SHOW_ALWAYS" => "N",
           		"PAGER_TEMPLATE" => ".default",
           		"PAGER_TITLE" => "Акции",
           		"PARENT_SECTION" => "",
           		"PARENT_SECTION_CODE" => "",
           		"PREVIEW_TRUNCATE_LEN" => "",
           		"PROPERTY_CODE" => array("", ""),
           		"SET_BROWSER_TITLE" => "N",
           		"SET_LAST_MODIFIED" => "N",
           		"SET_META_DESCRIPTION" => "N",
           		"SET_META_KEYWORDS" => "N",
           		"SET_STATUS_404" => "N",
           		"SET_TITLE" => "N",
           		"SHOW_404" => "N",
           		"SORT_BY1" => "ACTIVE_FROM",
           		"SORT_BY2" => "SORT",
           		"SORT_ORDER1" => "DESC",
           		"SORT_ORDER2" => "ASC",
           		"STRICT_SECTION_CHECK" => "N"
           	)
           );?>
		</div>
	</div>
</div>
<br>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
