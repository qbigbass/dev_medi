<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оптовый отдел");
?><h1>Оптовый отдел</h1>
<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"personal",
	array(
		"COMPONENT_TEMPLATE" => "personal",
		"ROOT_MENU_TYPE" => "company",
		"MENU_CACHE_TYPE" => "N",
		"MENU_CACHE_TIME" => "36000",
		"MENU_CACHE_USE_GROUPS" => "N",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => "1",
		"CHILD_MENU_TYPE" => "",
		"USE_EXT" => "N",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N"
	),
	false
);?>
<div class="global-block-container">
	<div class="global-content-block">
		<?$APPLICATION->IncludeComponent(
	"bitrix:form.result.new",
	"twoColumns",
	array(
		"CACHE_TIME" => "360000",
		"CACHE_TYPE" => "Y",
		"CHAIN_ITEM_LINK" => "",
		"CHAIN_ITEM_TEXT" => "",
		"EDIT_URL" => "",
		"IGNORE_CUSTOM_TEMPLATE" => "N",
		"LIST_URL" => "",
		"SEF_MODE" => "N",
		"SUCCESS_URL" => "",
		"USE_EXTENDED_ERRORS" => "Y",
		"WEB_FORM_ID" => "17",
		"COMPONENT_TEMPLATE" => ".default",
		"VARIABLE_ALIASES" => array(
			"WEB_FORM_ID" => "WEB_FORM_ID",
			"RESULT_ID" => "RESULT_ID",
		)
	),
	false
);?>
	</div>

</div><br>
<script>
    var _gcTracker=_gcTracker||[];
    _gcTracker.push(['view_page', { name: 'view_opt' }]);
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
