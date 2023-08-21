<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?$this->setFrameMode(true);?>
<script type="text/javascript">
	var ajaxDir = "<?=$this->GetFolder();?>";
</script>
<?if(!empty($arResult["GROUPS"])):?>
	<?if(empty($arParams["AJAX"])):?>
		<div id="homeCatalog">
			<?if(!empty($arResult["PROPERTY_ENUM"])):?>
				<div class="captionList">
					<div class="limiter">
						<div id="captionCarousel">
							<ul class="slideBox">
								<?foreach ($arResult["PROPERTY_ENUM"] as $ipe => $arPropEnum):?>
									<?if(!empty($arResult["GROUPS"][$ipe]["ITEMS"])):?>
										<li class="cItem">
											<div class="caption<?if($arPropEnum["SELECTED"] == "Y"): ?> selected<?endif;?>"><a href="#" data-name="<?=$arPropEnum["PROP_NAME"]?>" data-group="<?=$arPropEnum["ID"]?>" data-page="1" data-sheet="N" class="getProductByGroup"><?=($arPropEnum["VALUE"]=='%' ? 'Распродажа' : $arPropEnum["VALUE"])?></a></div>
										</li>
									<?endif;?>
								<?endforeach;?>
							</ul>
							<?/*<a href="#" class="captionBtnLeft"></a>
							<a href="#" class="captionBtnRight"></a>*/?>
						</div>
						<?/*<script type="text/javascript">
							$("#mediCarousel").mediCarousel({
								leftButton: ".captionBtnLeft",
								rightButton: ".captionBtnRight",
								countElement: 5,
								resizeElement: true,
								resizeAutoParams: {
									1920: 5,
									600: 4,
									500: 3,
									380: 2,
								}
							});
						</script>*/?>
					</div>
				</div>
			<?endif;?>
		<?endif;?>
			<?foreach ($arResult["GROUPS"] as $itg => $arItemsGroup):
				switch ($itg) {
					case '509':
						$itg_name = 'new';
						$itg_title = 'Новинка';
					break;
					case '510':
						$itg_name = 'hit';
						$itg_title = 'Хит';
					break;
					case '15336':
						$itg_name = 'sale';
						$itg_title = 'Распродажа';
					break;
				}
				?>
				<?if(!empty($arItemsGroup["ITEMS"])):?>
					<?if(empty($arParams["AJAX"])):?>
						<div class="ajaxContainer">
					<?endif;?>
						<div class="limiter">
							<div id="mediCarousel">
							<a href="#" class="btnLeft"></a>
							<a href="#" class="btnRight"></a>
							<div class="items productList productsListName slideBox" data-list-name="<?=$itg_title?>"  >
								<?$pos_counter = 1;?>
							<?foreach ($arItemsGroup["ITEMS"] as $index => $arElement):?>
							<!--<?=$arElement['SORT']?>-->
								<?$APPLICATION->IncludeComponent(
									"dresscode:catalog.item",
									"default_fast",
									array(
										"CACHE_TIME" => $arParams["CACHE_TIME"],
										"CACHE_TYPE" => $arParams["CACHE_TYPE"],
										"HIDE_MEASURES" => $arParams["HIDE_MEASURES"],
										"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
										"IBLOCK_ID" => $arParams["IBLOCK_ID"],
										"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
										"PRODUCT_ID" => $arElement["ID"],
										"PICTURE_HEIGHT" => $arParams["PICTURE_HEIGHT"],
										"PICTURE_WIDTH" => $arParams["PICTURE_WIDTH"],
										"PRODUCT_PRICE_CODE" => $arParams["PRODUCT_PRICE_CODE"],
										"LAZY_LOAD_PICTURES" => $arParams["LAZY_LOAD_PICTURES"],
										"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
										"CURRENCY_ID" => $arParams["CURRENCY_ID"],
										"POS_COUNT" => $pos_counter,
										"LIST_TYPE" => $itg_name
									),
									false
								);?>
								<?$pos_counter++;?>
							<?endforeach;?>
							<?if(!empty($arResult["HIDE_LAST_ELEMENT"])):?>
								<div class="item product last">
									<a href="/catalog/<?=$itg_name?>/" class="showMoreLink" >
										<span class="wp">
											<span class="icon">
												<img class="iconBig" src="<?=SITE_TEMPLATE_PATH?>/images/showMore.png" alt="<?=GetMessage("SHOW_MORE")?>">
												<img class="iconSmall" src="<?=SITE_TEMPLATE_PATH?>/images/showMoreSmall.png" alt="<?=GetMessage("SHOW_MORE")?>">
											</span>
											<span class="ps"><?=GetMessage("SHOW_ALL")?></span><?/*<span class="value"><?=$arParams["NEXT_ELEMENTS_COUNT"]?></span>*/?>
											<?/*<span class="small"><?=GetMessage("SHOWS")?> <?=$arParams["~ELEMENTS_COUNT"]?> <?=GetMessage("FROM2")?> <?=$arResult["FIRST_ITEMS_ALL_COUNT"]?></span>*/?>
										</span>
									</a>
								</div>
							<?endif;?>
							<div class="clear"></div>
						</div>
						</div>
						</div>
					<?if(empty($arParams["AJAX"])):?>
						</div>
					<?endif;?>
					<?break(1);?>
				<?endif;?>
			<?endforeach;?>
<script type="text/javascript">
    $("#mediCarousel").mediCarousel({
        leftButton: "#homeCatalog .btnLeft",
        rightButton: "#homeCatalog .btnRight",
        countElement: 5,
        resizeElement: true,
        resizeAutoParams: {
            1920: 5,
			1480: 4,
            1100: 3,
            800: 2,
			370: 1
        }
    });
</script>
	<?if(empty($arParams["AJAX"])):?>
		</div>
	<?endif;?>

	<script type="text/javascript">
		var offersProductParams = '<?=\Bitrix\Main\Web\Json::encode($arParams);?>';
	</script>

<?endif;?>