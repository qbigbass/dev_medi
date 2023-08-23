<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
$arColumnParams = array(true, true, false, true, true, false, true);
$currentIndex = 0;
?><?if(!empty($arResult["ITEMS"])):?>
        <div class="limiter">
			<div class="plitka">
			<?foreach($arResult["ITEMS"] as $arNextElement):?>
				<?
				//get edit actions
				$this->AddEditAction($arNextElement["ID"], $arNextElement["EDIT_LINK"], CIBlock::GetArrayByID($arNextElement["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arNextElement["ID"], $arNextElement["DELETE_LINK"], CIBlock::GetArrayByID($arNextElement["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage("CT_BNL_ELEMENT_DELETE_CONFIRM")));
				//get resize picture
				if(!empty($arNextElement["DETAIL_PICTURE"])){
					$arNextElement["RESIZE_PICTURE"] = CFile::ResizeImageGet($arNextElement["DETAIL_PICTURE"], array("width" => 500, "height" => 500), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 100);
				}
                if ($currentIndex == '0'):?>
                    <div class="elem-column">
                <?
                endif;
                if ($currentIndex == '1'):?>
                    </div>
                    <div class="elem-column">
                <?
                endif;
			?>
				<div class="plitka-item-wrap">
					<div class="plitka-item"  id="<?=$this->GetEditAreaId($arNextElement["ID"]);?>">
						<?if ($arNextElement['PROPERTIES']['LINK']['VALUE']){?><a href="<?=$arNextElement['PROPERTIES']['LINK']['VALUE']?>"><?}?>

							<div class="bg" style="background-image: url('<?=$arNextElement["RESIZE_PICTURE"]["src"]?>');"></div>
						<?if ($arNextElement['PROPERTIES']['LINK']['VALUE']){?></a><?}?>
						<div class="text-wrap">
							<?=$arNextElement["~DETAIL_TEXT"]?>
						</div>
					</div>
				</div>
				<?$currentIndex++;?>
			<?endforeach;?>
                </div>
			</div>
		</div>
<?endif;?>