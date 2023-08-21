<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if (!empty($arResult)):?>
	<ul id="subMenu">
		<?foreach($arResult as $arItem):
			if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
				continue;
            if (!empty($_SESSION['MEDI_SITE_ID'])
                && $arItem["PARAMS"]["CITY_FOLDER"] == "Y"
                && $GLOBALS['medi']['sfolder'][SITE_ID] != ''
            )
            {
                $curlink = "/" . $GLOBALS['medi']['sfolder'][SITE_ID] . $arItem["LINK"];
            }
            else
            {
                $curlink = $arItem["LINK"];
            }?>
			<?if ($APPLICATION->GetCurDir() == $curlink) $arItem['SELECTED'] = true; ?>

			<?if($arItem["SELECTED"]):?>
				<li><a href="<?=$curlink?>" class="selected"><?=$arItem["TEXT"]?></a></li>
			<?else:?>
				<li><a href="<?=$curlink?>"><?=$arItem["TEXT"]?></a></li>
			<?endif?>
		<?endforeach?>
	</ul>
<?endif?>
