<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>
<?if(!empty($arResult)):?>
	<?
		//get banner picture resize
		if(!empty($arResult["DISPLAY_PROPERTIES"]["BG_IMAGE"]["FILE_VALUE"])){
			$arResult["RESIZE_BANNER_PICTURE"] = CFile::ResizeImageGet($arResult["DISPLAY_PROPERTIES"]["BG_IMAGE"]["FILE_VALUE"], array("width" => 1480, "height" => 500), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 90);
		}
		//get detail picture resize
		if(!empty($arResult["DETAIL_PICTURE"])){
			$arResult["RESIZE_DETAIL_PICTURE"] = CFile::ResizeImageGet($arResult["DETAIL_PICTURE"], array("width" => 550, "height" => 500), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 90);
		}

		if(!empty($arResult["PROPERTIES"]["BG_IMAGE_M"]["VALUE"])){
			$arResult["RESIZE_PREVIEW_PICTURE"] = CFile::ResizeImageGet($arResult["PROPERTIES"]["BG_IMAGE_M"]["VALUE"], array("width" => 480, "height" => 330), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 90);
		}
		elseif(!empty($arResult["PREVIEW_PICTURE"])){
			$arResult["RESIZE_PREVIEW_PICTURE"] = CFile::ResizeImageGet($arResult["PREVIEW_PICTURE"], array("width" => 480, "height" => 330), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 90);
		}
	?>
	<?//include view
	?>
	<?//$this->SetViewTarget("before_breadcrumb_container");?>
		<div class="blog-banner banner-wrap banner-bg">
			<div class="banner-animated fullscreen-banner banner-elem" style="background-image: url('<?=$arResult["RESIZE_BANNER_PICTURE"]["src"]?>');">

				<div class="limiter">
					<div class="tb">
						<div class="text-wrap tc">
							<div class="tb">
								<div class="tr">
									<div class="tc">
										<div class="header__title_bg">
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
						<div class="image tc">
							<?if(!empty($arResult["RESIZE_DETAIL_PICTURE"])):?>
									<img src="<?=$arResult["RESIZE_DETAIL_PICTURE"]["src"]?>" alt="<?=$arResult["NAME"]?>">
							<?endif;?>
						</div>
					</div>
				</div>

			</div>

			<div class="small-header">
			<?if(!empty($arResult["NAME"])):?>
				<h1 class="ff-medium"><?=$arResult["NAME"]?></h1>
			<?endif;?>
			<?if(!empty($arResult["PREVIEW_TEXT"])):?>
				<div class="descr"><?=$arResult["PREVIEW_TEXT"]?></div>
			<?endif;?>
			</div>
			<div class="banner-animated-small fullscreen-banner banner-elem" style="background-image: url('<?=$arResult["RESIZE_PREVIEW_PICTURE"]["src"]?>')">

				<div class="limiter">
					<div class="tb">
						<div class="text-wrap tc">
							<div class="tb">

							</div>
						</div>
						<div class="image tc">
							<?if(!empty($arResult["RESIZE_DETAIL_PICTURE"])):?>
									<img src="<?=$arResult["RESIZE_DETAIL_PICTURE"]["src"]?>" alt="<?=$arResult["NAME"]?>">
							<?endif;?>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?//$this->EndViewTarget();?>
	<div class="global-block-container">
		<div class="global-content-block">
			<div class="detail-text-wrap">
				<?=$arResult["DETAIL_TEXT"]?>

				<br>
			</div>
		</div>

	</div>

	<meta property="og:title" content="<?=$arResult["NAME"]?>" />
	<meta property="og:description" content="<?=htmlspecialcharsbx($arResult["PREVIEW_TEXT"])?>" />
	<meta property="og:url" content="<?=$arResult["DETAIL_PAGE_URL"]?>" />
	<meta property="og:type" content="website" />
	<?if(!empty($arResult["RESIZE_DETAIL_PICTURE"])):?>
		<meta property="og:image" content="<?=$arResult["RESIZE_DETAIL_PICTURE"]["src"]?>" />
	<?endif;?>
<?endif;?>
