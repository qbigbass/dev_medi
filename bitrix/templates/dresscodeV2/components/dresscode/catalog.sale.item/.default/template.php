<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?$this->setFrameMode(true);
use Bitrix\Main\Grid\Declension;?>
<?if(!empty($arResult["ITEMS"])):?>
	<?$uniqID = CAjax::GetComponentID($this->__component->__name, $this->__component->__template->__name, false);?>
<div class="bindAction" id="<?=$this->GetEditAreaId($arNextElement["ID"]);?>">
    <div class="ff-medium row h3">Товар участвует в акци<?=(count($arResult['ITEMS'])>1 ? 'ях' : 'и')?>:</div>
	<?foreach($arResult["ITEMS"] as $ii => $arNextElement):

        $hide_link = $arNextElement['PROPERTY_HIDE_VALUE'] == 'Да' ? 'Y' : 'N';?>
		<?
        $dayDiff = '';
        if ($arNextElement['DATE_ACTIVE_TO'] > 0) {
            $date = DateTime::createFromFormat('d.m.Y H:i:s', $arNextElement['DATE_ACTIVE_TO']);
            $now = new DateTime();
            if ($date) {
                $dayDiff = $date->diff($now)->format('%a');
                if ($dayDiff > 0)
                {
                    $sDeclension = new Declension('день', 'дня', 'дней');
                    $dayDiff_str = '<br/><span class="action_over">Заканчивается через '.$dayDiff.'&nbsp;'.$sDeclension->get($dayDiff).'</span>';
                }
            }
        }
			if(!empty($arNextElement["EDIT_LINK"])){
				$this->AddEditAction($arNextElement["ID"], $arNextElement["EDIT_LINK"], CIBlock::GetArrayByID($arNextElement["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arNextElement["ID"], $arNextElement["DELETE_LINK"], CIBlock::GetArrayByID($arNextElement["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage("CT_BNL_ELEMENT_DELETE_CONFIRM")));
			}
		?>

			<div class="tb">
				<div class="tc bindActionImage"><?if($hide_link == 'N'){?><a target="_blank" href="<?=$arNextElement["DETAIL_PAGE_URL"]?>"><?}?><span class="image" title="<?=$arNextElement["NAME"]?>"></span><?if($hide_link == 'N'){?></a><?}?></div>
				<div class="tc"><?if($hide_link == 'N'){?><a target="_blank" href="<?=$arNextElement["DETAIL_PAGE_URL"]?>" class="theme-color ff-medium"><?}?><?=$arNextElement["NAME"]?><?if($hide_link == 'N'){?></a><?}?><?if ($dayDiff> 0){echo $dayDiff_str;}?></div>
			</div>
	<?endforeach;?>
</div>
<?endif;?>