<div class="global-block-container">
	<div class="global-content-block">
		<div class="blog-banner banner-wrap banner-no-bg">

			<div class="banner-animated banner-elem" style="background: url('<?=$arResult['PROPERTIES']["BG_IMG_DETAIL"]["src"]?>') center center / cover no-repeat;">
				<div class="limiter" style="height:0">
					<div class="top_pad"><?$APPLICATION->IncludeComponent("bitrix:breadcrumb", ".default", Array(
						"START_FROM" => "0",
							"PATH" => "",
							"SITE_ID" => "-",
						),
						false
					);?></div>
				</div>

				<div class="tb limiter">

					<div class="text-wrap tc">

						<div class="tb">
							<div class="tr">
								<div class="tc">

									<?if(!empty($arResult["PROPERTIES"]["HEADER_CONTENT"]["~VALUE"]["TEXT"])):?>
										<div><?=$arResult["PROPERTIES"]["HEADER_CONTENT"]["~VALUE"]["TEXT"]?></div>
									<?endif;?>
								</div>
							</div>
						</div>
					</div>
					<?if(!empty($arResult["RESIZE_DETAIL_PICTURE"])):?>
						<div class="image tc">
							<img src="<?=$arResult["RESIZE_DETAIL_PICTURE"]["src"]?>" alt="<?=$arResult["NAME"]?>">
						</div>
					<?endif;?>
				</div>
			</div>
		</div>
		<div class="detail-text-wrap">
				<?=$arResult["DETAIL_TEXT"]?>
			<div class="limiter">
				<div class="btn-simple-wrap">
					<a href="<?=$arResult["LIST_PAGE_URL"]?>" class="btn-simple btn-small btn-border"><?=GetMessage("NEWS_BACK")?></a>
				</div>
			</div>
		</div>
	</div>
	<?/*global $arrFilter; $arrFilter["!ID"] = $arResult["ID"];?>
	<?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"saleDetail",
	array(

	),
	"\$component"
);*/?>
</div>
