<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	$this->setFrameMode(true);
?>	<?if(!empty($arResult["ITEMS"])):?>
		<div class="collections banners-list">
			<?foreach ($arResult["ITEMS"] as $ix => $arNextElement):?>
				<?

				if ($arNextElement['PROPERTIES']['IMG_LIST']['VALUE'])
				{
				$arNextElement["PREVIEW_PICTURE_RESIZE"] = CFile::ResizeImageGet($arNextElement['PROPERTIES']['IMG_LIST']['VALUE'], array("width" => 500, "height" => 300), BX_RESIZE_IMAGE_PROPORTIONAL, false);
				}?>
				<?$arNextElement["DETAIL_PICTURE_RESIZE"] = CFile::ResizeImageGet($arNextElement['PROPERTIES']['BG_LIST']['VALUE'], array("width" => 900, "height" => 300), BX_RESIZE_IMAGE_PROPORTIONAL, false);?>
				<?$this->AddEditAction($arNextElement["ID"], $arNextElement["EDIT_LINK"], CIBlock::GetArrayByID($arNextElement["IBLOCK_ID"], "ELEMENT_EDIT"));?>
				<?$this->AddDeleteAction($arNextElement["ID"], $arNextElement["DELETE_LINK"], CIBlock::GetArrayByID($arNextElement["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));?>
				<a class="banner-wrap" id="<?=$this->GetEditAreaId($arNextElement["ID"]);?>" href="<?=$arNextElement["DETAIL_PAGE_URL"]?>">
					<div class="banner-elem">
						<div class="text-wrap ">
							<div class="h2 ff-bold theme-color-hover"><?=$arNextElement["NAME"]?></div>
							<?if(!empty($arNextElement["PREVIEW_TEXT"])):?>
								<div class="descr"><?=$arNextElement["PREVIEW_TEXT"]?></div>
							<?endif;?>
						</div>
						<div class="bg" <?if(!empty($arNextElement["DETAIL_PICTURE_RESIZE"])):?> style="background: url('<?=$arNextElement["DETAIL_PICTURE_RESIZE"]["src"]?>');"<?endif;?>></div>
					</div>
				</a>
			<?endforeach;?>
		</div>
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
			<?=$arResult["NAV_STRING"]?>
		<?endif;?>
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