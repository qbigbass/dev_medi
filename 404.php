<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');
CHTTP::SetStatus("404 Not Found");
@define("ERROR_404", "Y");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("tags", "Страница не найдена или не существует");
$APPLICATION->SetPageProperty("description", "Страница не найдена или не существует");
$APPLICATION->SetPageProperty("keywords_inner", "Страница не найдена или не существует");
$APPLICATION->SetPageProperty("title", "Страница не найдена или не существует");
$APPLICATION->SetPageProperty("keywords", "Страница не найдена или не существует");
$APPLICATION->SetPageProperty("robots", "noindex, nofollow");
$APPLICATION->AddChainItem("Страница не найдена или не существует", "");
?>
<div id="error404">
	<div class="wrapper">
		<a href="<?=SITE_DIR?>" class="errorPic"><img src="<?=SITE_TEMPLATE_PATH?>/images/404.jpg"></a>
		<h1>Такой страницы не существует</h1>
		<div class="errorText">начните поиск с <a href="<?=SITE_DIR?>">главной страницы</a> или выберите нужный товар в <a href="<?=SITE_DIR?>catalog/">каталоге</a>:</div>
	</div>
	<div id="empty">
		<div class="wrapper">
			<?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"emptyMenu", 
	array(
		"ROOT_MENU_TYPE" => "left",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "360000",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => "1",
		"CHILD_MENU_TYPE" => "left",
		"USE_EXT" => "Y",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
		"COMPONENT_TEMPLATE" => "emptyMenu"
	),
	false
);?>
		</div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>