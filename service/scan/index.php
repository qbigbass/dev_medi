<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Запись на сканирование стоп");
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"personal",
	array(
		"COMPONENT_TEMPLATE" => "personal",
		"ROOT_MENU_TYPE" => "service",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "36000",
		"MENU_CACHE_USE_GROUPS" => "Y",
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
<h1>Запись на&nbsp;бесплатное сканирование стоп</h1>
<div class="global-block-container">
	<div class="global-content-block">
<style>
	.form_block {
		margin: auto;
	}
</style>
<p>Записавшись на&nbsp;услугу <a href="/services/besplatnoe-skanirovanie-stop/" target="_blank" class="theme-link-dashed">Бесплатное сканирование стоп</a> вы&nbsp;получите всю&nbsp;необходимую информацию о&nbsp;состоянии ваших стоп.</p>
<p>Специалисты по&nbsp;ортезированию фирменных салонов medi проведут диагностику ваших стоп на&nbsp;плантографе, определят степень отклонения их&nbsp;биомеханики от&nbsp;нормы и&nbsp;дадут рекомендации по&nbsp;улучшению состояния.</p>
<h2 class="h2 ff-medium" style="text-align: center;">Чтобы записаться на&nbsp;услугу, заполните форму:</h2>
<?
// Форма бронирования товара в салоне
$APPLICATION->IncludeComponent(
	"bitrix:form.result.new",
	 "scan",
	array(
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "N",
		"CHAIN_ITEM_LINK" => "",
		"CHAIN_ITEM_TEXT" => "",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"EDIT_ADDITIONAL" => "N",
		"EDIT_STATUS" => "N",
		"IGNORE_CUSTOM_TEMPLATE" => "N",
		"NOT_SHOW_FILTER" => array(
			0 => "",
			1 => "",
		),
		"NOT_SHOW_TABLE" => array(
			0 => "",
			1 => "",
		),
		"RESULT_ID" => $_REQUEST[RESULT_ID],
		"SEF_MODE" => "N",
		"SHOW_ADDITIONAL" => "N",
		"SHOW_ANSWER_VALUE" => "Y",
		"SHOW_EDIT_PAGE" => "N",
		"SHOW_LIST_PAGE" => "N",
		"SHOW_STATUS" => "N",
		"SHOW_VIEW_PAGE" => "N",
		"START_PAGE" => "new",
		"SUCCESS_URL" => "",
		"HIDDEN_FIELDS" => array(
			0 => "AGREE",
		),
		"USE_EXTENDED_ERRORS" => "Y",
		"WEB_FORM_ID" => "6",
		"COMPONENT_TEMPLATE" => "scan",
		"LIST_URL" => "",
		"EDIT_URL" => "",
		"VARIABLE_ALIASES" => array(
			"WEB_FORM_ID" => "WEB_FORM_ID",
			"RESULT_ID" => "RESULT_ID",
		)
	),
	false
);
?>
</div>

</div><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
