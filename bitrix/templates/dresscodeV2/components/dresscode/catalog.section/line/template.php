<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>

<?if (!empty($arResult["ITEMS"])):?>
<?
	if ($arParams["DISPLAY_TOP_PAGER"]){
		?><? echo $arResult["NAV_STRING"]; ?><?
	}
?>
	<div id="catalogLineList">
		<?$pos_counter = 1;?>
		<?foreach($arResult["ITEMS"] as $arElement):?>
			<?$APPLICATION->IncludeComponent(
				"dresscode:catalog.item",
				"line",
				array(
					"CACHE_TIME" => $arParams["CACHE_TIME"],
					"CACHE_TYPE" => $arParams["CACHE_TYPE"],
					"HIDE_MEASURES" => $arParams["HIDE_MEASURES"],
					"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
					"PRODUCT_SKU_FILTER" => $arResult["FILTER"],
					"LAZY_LOAD_PICTURES" => $arParams["LAZY_LOAD_PICTURES"],
					"PRODUCT_ID" => $arElement["ID"],
					"DISPLAY_FORMAT_PROPERTIES" => "Y",
					"PICTURE_HEIGHT" => "",
					"PICTURE_WIDTH" => "",
					"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
					"CURRENCY_ID" => $arParams["CURRENCY_ID"],
					"PRODUCT_PRICE_CODE" => $arParams["PRICE_CODE"],
					"PRODUCT_DISPLAY_PROPERTIES" => $arParams["PROPERTY_CODE"],
					"POS_COUNT" => $pos_counter,
					"LIST_TYPE" => 'line'
				),
				false,
				array("HIDE_ICONS" => "Y")
			);?>
			<?$pos_counter++;?>
		<?endforeach;?>
	</div>

<?
	if ($arParams["DISPLAY_BOTTOM_PAGER"]){
		?><? echo $arResult["NAV_STRING"]; ?><?
	}
?>

	<?if(empty($arParams["HIDE_DESCRIPTION_TEXT"]) || $arParams["HIDE_DESCRIPTION_TEXT"] != "Y"):?>
		<?if(empty($_GET["PAGEN_".$arResult["NAV_NUM_PAGE"]])):?>
			<div><?=$arResult["~DESCRIPTION"]?></div>
		<?endif;?>
	<?endif;?>

	<script>
		//lazy load
		checkLazyItems();
	</script>
	<script>
		if ($('#medi-openning-more-button').length){
			$('#medi-openning-more-button').on("click", function(){
				if (!$(this).data('status')) {
					$('.medi-openning-shadow-block').addClass("is-active");
					$(this).html('Скрыть');
					$(this).data('status', true);
				}
				else {
					$('.medi-openning-shadow-block').removeClass("is-active");
					$(this).html('Подробнее');
					$(this).data('status', false);
				}
			});
		}
	</script>

<?else:?>
	<div id="empty">
		<div class="emptyWrapper">
			<div class="pictureContainer">
				<img src="<?=SITE_TEMPLATE_PATH?>/images/emptyFolder.png" alt="<?=GetMessage("EMPTY_HEADING")?>" class="emptyImg">
			</div>
			<div class="info">
				<h3><?=GetMessage("EMPTY_HEADING")?></h3>
				<p><?=GetMessage("EMPTY_TEXT")?></p>
				<a href="<?=SITE_DIR?>" class="back"><?=GetMessage("MAIN_PAGE")?></a>
			</div>
		</div>
		<?$APPLICATION->IncludeComponent("bitrix:menu", "emptyMenu", Array(
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
		);?>
	</div>
<?endif;?>
