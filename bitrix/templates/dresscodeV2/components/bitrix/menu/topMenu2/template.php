<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if (!empty($arResult)):?>
	<ul id="subMenu">
		<?foreach($arResult as $arItem):
			if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
				continue;
		?>
			<?if($arItem["SELECTED"]):?>
				<li><a class="selected"><?=$arItem["TEXT"]?></a></li>
			<?else:?>
				<li><a href="<?=($arItem["PARAMS"]["CITY_FOLDER"] == "Y" && $GLOBALS['medi']['sfolder'][$_SESSION['MEDI_SITE_ID']] != '' ? "/".$GLOBALS['medi']['sfolder'][SITE_ID] : '') ?><?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
			<?endif?>
		<?endforeach?>
	</ul>
<?endif?>
