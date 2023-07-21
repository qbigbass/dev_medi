<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (empty($arResult["CATEGORIES"]) && $arResult["DEBUG"]["SHOW"] != 'Y') return;

IncludeTemplateLangFile(__FILE__);

$arParams["SHOW_PREVIEW_TEXT"] = ($arParams["SHOW_PREVIEW_TEXT"]) ? $arParams["SHOW_PREVIEW_TEXT"] : 'Y';
?>
<?/* <div class="bx_smart_searche bx_searche <?=$arResult["VISUAL_PARAMS"]["THEME_CLASS"]?>">
	<?
	if($arResult["DEBUG"]["SHOW"] == 'Y')
	{
		echo '<pre>';
			echo 'Query: '; print_r($arResult["DEBUG"]["QUERY"]); echo "\r\n";
			echo 'Type: '; print_r($arResult["DEBUG"]["TYPE"]); echo "\r\n";
			echo 'Time: '; print_r($arResult["DEBUG"]["TIME"]); echo "\r\n";
			echo 'Max count: '; print_r($arResult["DEBUG"]["TOP_COUNT"]); echo "\r\n";
			echo 'Query: '; print_r($arResult["DEBUG"]["QUERY_COUNT"]); echo "\r\n";
		echo '</pre>';
		
		
		if($arResult["DEBUG"]["Q"])
		{
			// echo '<pre>'; print_r($arResult["DEBUG"]["Q"]); echo '</pre>';
		}
		
		if($arResult["DEBUG"]["OTHER"])
		{
			echo '<pre>'; print_r($arResult["DEBUG"]["OTHER"]); echo '</pre>';
		}
	}
	?>
	
	
	<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory):?>
		<?foreach($arCategory["ITEMS"] as $i => $arItem):?>
			<?if(isset($arResult["SECTIONS"][$arItem["ITEM_ID"]])):
				$arElement = $arResult["SECTIONS"][$arItem["ITEM_ID"]];?>
				<a class="bx_item_block_href" href="<?echo $arItem["URL"]?>">
					<?if($arResult["VISUAL_PARAMS"]["SHOW_SECTION_PICTURE"]):
						if(is_array($arElement["PICTURE"]))
							$image_url = $arElement["PICTURE"]["src"];
						else
							$image_url = '/bitrix/components/arturgolubev/search.title/templates/.default/images/noimg.png';
					?>
						<span class="bx_item_block_item_image">
							<img src="<?=$image_url?>" alt="">
						</span>
					<?endif;?>
					<span class="bx_item_block_href_category_title"><?=($arElement["PATH"]) ? $arElement["PATH"] : GetMessage("AG_SMARTIK_SECTION_TITLE");?></span><br>
					<span class="bx_item_block_href_category_name"><?echo strip_tags($arItem["NAME"])?></span>
				</a>
				<div class="bx_item_block_hrline"></div>
			<?endif;?>
		<?endforeach;?>
	<?endforeach;?>
	
	<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory):?>
		<?foreach($arCategory["ITEMS"] as $i => $arItem):?>
			<?if(isset($arResult["ELEMENTS"][$arItem["ITEM_ID"]])):
				$arElement = $arResult["ELEMENTS"][$arItem["ITEM_ID"]];
			
				$arElement["PREVIEW_TEXT"] = strip_tags($arElement["PREVIEW_TEXT"]);
			
				if(is_array($arElement["PICTURE"]))
					$image_url = $arElement["PICTURE"]["src"];
				else
					$image_url = '/bitrix/components/arturgolubev/search.title/templates/.default/images/noimg.png';
			?>
				
				<a class="bx_item_block_href" href="<?echo $arItem["URL"]?>">
					<span class="bx_item_block_item_info">
						<span class="bx_item_block_item_image">
							<img src="<?=$image_url?>" alt="">
						</span>
						
						<?
						foreach($arElement["PRICES"] as $code=>$arPrice)
						{
							if ($arPrice["MIN_PRICE"] != "Y")
								continue;

							if($arPrice["CAN_ACCESS"])
							{
								if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
									<span class="bx_item_block_item_price">
										<span class="bx_price_new">
											<?=$arPrice["PRINT_DISCOUNT_VALUE"]?>
										</span>
										<span class="bx_price_old"><?=$arPrice["PRINT_VALUE"]?></span>
									</span>
								<?else:?>
									<span class="bx_item_block_item_price bx_item_block_item_price_only_one">
										<span class="bx_price_new"><?=$arPrice["PRINT_VALUE"]?></span>
									</span>
								<?endif;
							}
							if ($arPrice["MIN_PRICE"] == "Y")
								break;
						}
						?>
						
						<span class="bx_item_block_item_name">
							<span class="bx_item_block_item_name_flex_align">
								<?echo $arItem["NAME"]?>
							</span>
						</span>
						<span class="bx_item_block_item_clear"></span>
					</span>
				</a>
			<?endif;?>
		<?endforeach;?>
	<?endforeach;?>

	<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory):?>
		<?foreach($arCategory["ITEMS"] as $i => $arItem):?>
			<?if($category_id === "all"):?>
				<div class="bx_item_block all_result">
					<div class="bx_item_element bx_item_element_all_result">
						<a class="all_result_button" href="<?echo $arItem["URL"]?>"><?echo $arItem["NAME"]?></a>
					</div>
					<div style="clear:both;"></div>
				</div>
			<?
			elseif(isset($arResult["ELEMENTS"][$arItem["ITEM_ID"]]) || isset($arResult["SECTIONS"][$arItem["ITEM_ID"]])):
				continue;
			else:?>
				<a class="bx_item_block_href" href="<?echo $arItem["URL"]?>">
					<span class="bx_item_block_item_simple_name"><?echo $arItem["NAME"]?></span>
				</a>
			<?endif;?>
		<?endforeach;?>
	<?endforeach;?>
</div> */?>


<?
$item_ids = array_keys($arResult["ELEMENTS"]);
global $arrFilter;
$arrFilter = array("ID" => $item_ids);?>
<?if(!empty($item_ids)):?>
	<h1>Результаты поиска <a href="#" id="searchProductsClose"></a></h1>
	<?$APPLICATION->IncludeComponent(
		"dresscode:catalog.section", 
		"squares",
		array(
			"IBLOCK_TYPE" => $arParams["CATEGORY_0"][0],
			"IBLOCK_ID" => $arParams["CATEGORY_0_".$arParams["CATEGORY_0"][0]][0],
			"ELEMENT_SORT_FIELD" => "ID",
			"ELEMENT_SORT_ORDER" => $item_ids,
			"PROPERTY_CODE" => $_GET["PROPERTY_CODE"],
			"PAGE_ELEMENT_COUNT" => "9999",
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"PAGER_TEMPLATE" => "round_search",
			"CONVERT_CURRENCY" => "Y",
			"CURRENCY_ID" => "RUB",
			"FILTER_NAME" => "arrFilter",
			"ADD_SECTIONS_CHAIN" => "N",
			"SHOW_ALL_WO_SECTION" => "Y",
			"AJAX_MODE" => "N",
			"AJAX_OPTION_JUMP" => "N",
			"CACHE_TYPE" => "Y",
			"CACHE_FILTER" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
			"HIDE_NOT_AVAILABLE" => "N",
			"HIDE_MEASURES" => "N",
		)
	);
	?>
	
	<?foreach($arResult["CATEGORIES"] as $category_id => $arCategory):?>
		<?foreach($arCategory["ITEMS"] as $i => $arItem):?>
			<?if($category_id === "all"):?>
				<a href="<?echo $arItem["URL"]?>" class="searchAllResult"><span>Смотреть все результаты</span></a>
			<?endif;?>
		<?endforeach;?>
	<?endforeach;?>
	
<?else:?>
	<div class="errorMessage">По вашему поисковому запросу ничего не найдено<a href="#" id="searchProductsClose"></a></div>
<?endif;?>