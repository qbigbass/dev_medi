<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?$this->setFrameMode(true);?>
<?if(!empty($arResult["ITEMS"])):?>
	<?$uniqID = CAjax::GetComponentID($this->__component->__name, $this->__component->__template->__name, false);?>
	<?foreach($arResult["ITEMS"] as $ii => $arNextElement):?>
		<?
			if(!empty($arNextElement["EDIT_LINK"])){
				$this->AddEditAction($arNextElement["ID"], $arNextElement["EDIT_LINK"], CIBlock::GetArrayByID($arNextElement["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arNextElement["ID"], $arNextElement["DELETE_LINK"], CIBlock::GetArrayByID($arNextElement["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage("CT_BNL_ELEMENT_DELETE_CONFIRM")));
			}
		?>
		<div class="bindAction" id="<?=$this->GetEditAreaId($arNextElement["ID"]);?>">
			<div class="ff-medium"><?=GetMessage("BIND_ACTION_LABEL")?></div><br>
			<div class="tb">
				<div class="tc bindActionImage"><a target="_blank" href="<?=$arNextElement["DETAIL_PAGE_URL"]?>"><span class="image" title="<?=$arNextElement["NAME"]?>"></span></a></div>
				<div class="tc"><a target="_blank" href="<?=$arNextElement["DETAIL_PAGE_URL"]?>" class="theme-color ff-medium"><?=$arNextElement["NAME"]?></a></div>
			</div>
		</div>		
	<?endforeach;?>

<?endif;?>