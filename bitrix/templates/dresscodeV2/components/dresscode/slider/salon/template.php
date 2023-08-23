<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?if(!empty($arResult["ITEMS"])):?>
	<div id="salonServicesSlider">
		<div class="limiter">
			<ul class="slideBox">
				<?foreach($arResult["ITEMS"] as $i => $arElement):?>
					<? /*
						$this->AddEditAction($arElement["ID"], $arElement["EDIT_LINK"], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
						$this->AddDeleteAction($arElement["ID"], $arElement["DELETE_LINK"], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array());
					*/?>
					<li >
						<?if(!empty($arElement["DETAIL_PAGE_URL"])):?>
							<a href="<?=str_replace("#SITE_DIR#", SITE_DIR, $arElement["DETAIL_PAGE_URL"]).($arElement['CODE'] == 'izgotovlenie-ortopedicheskikh-stelek' ? "#order" : "")?>">
						<?endif;?>
						<?if($arParams["LAZY_LOAD_PICTURES"] == "Y"):?>
							<img src="<?=$templateFolder?>/images/lazy.jpg" data-lazy="<?=$arElement["PREVIEW_PICTURE"]["src"]?>" class="lazy" alt="">
						<?else:?>
							<img src="<?=$arElement["PREVIEW_PICTURE"]["src"]?>" class="lazy" alt="">
						<?endif;?>
						<?if(!empty($arElement["DETAIL_PAGE_URL"])):?>
							</a>
						<?endif;?>
						<?if ($arElement['NAME']){
                           ?>
						<div class="tb">
							<div class="text-wrap tc">
								<div class="head_bg_lining">
								<a href="<?=$arElement["DETAIL_PAGE_URL"].($arElement['CODE'] == 'izgotovlenie-ortopedicheskikh-stelek' ? "#order" : "")?>" class="h2 ff-bold theme-color-hover"><?=$arElement['NAME']?></a>
								<div class="descr"><?=$arElement['PREVIEW_TEXT']?></div>
								<div class="btn-wrapper">
									<a  href="<?=$arElement["DETAIL_PAGE_URL"].($arElement['CODE'] == 'izgotovlenie-ortopedicheskikh-stelek' ? "#order" : "")?>" class="btn-simple btn-micro">Подробнее</a>
								</div>
								</div>
							</div>
						</div>
						<?}?>
					</li>
				<?endforeach;?>
			</ul>
			<a href="#" class="salonServicesSliderBtnLeft"></a>
			<a href="#" class="salonServicesSliderBtnRight"></a>
		</div>
	</div>
<?endif;?>
