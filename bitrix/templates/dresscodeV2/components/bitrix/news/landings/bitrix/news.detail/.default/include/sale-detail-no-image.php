<div class="global-block-container">
	<div class="global-content-block">
		<div class="blog-banner banner-no-image">
			<div class="banner-elem">
				<div class="tb">
					<div class="text-wrap tc">
						<div class="tb">
							<div class="tr">
								<div class="tc">
									<?if(!empty($arResult["DISPLAY_PROPERTIES"]["STOCK_DATE"]["VALUE"])):?>
										<div class="date"><?=$arResult["DISPLAY_PROPERTIES"]["STOCK_DATE"]["VALUE"];?></div>
									<?endif;?>
									<?if(!empty($arResult["NAME"])):?>
										<h1 class="ff-medium"><?=$arResult["NAME"]?></h1>
									<?endif;?>
									<?if(!empty($arResult["PREVIEW_TEXT"])):?>
										<div class="descr"><?=$arResult["PREVIEW_TEXT"]?></div>
									<?endif;?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="detail-text-wrap">
			<?=$arResult["DETAIL_TEXT"]?>
		</div>
	</div>
	<?global $arrFilter; $arrFilter["!ID"] = $arResult["ID"];
	$arrFilter["PROPERTY_CITY_VALUE"] = $GLOBALS['medi']['region_cities'][SITE_ID];?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:news.list",
		"saleDetail",
		array_merge($arParams, array("CHECK_DATES" => "Y", "NEWS_COUNT" => 3, "FILTER_NAME" => "arrFilter", "INCLUDE_IBLOCK_INTO_CHAIN" => "N", "ADD_SECTIONS_CHAIN" => "N", "ADD_ELEMENT_CHAIN" => "N", "SET_TITLE" => "N", "DISPLAY_TOP_PAGER" => "N", "DISPLAY_BOTTOM_PAGER" => "N")),
		$component
	);?>
</div>

<script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
<script src="//yastatic.net/share2/share.js" charset="utf-8"></script>
