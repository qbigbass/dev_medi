<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if (!empty($arResult)):?>
	<ul class="footerMenu">
		<?
			foreach($arResult as $arItem):
				if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
					continue;
		?>

		<?$arInSites = [];
		if ($arItem["PARAMS"]["REGION_SITES"]){
			$arInSites = explode(",", $arItem["PARAMS"]["REGION_SITES"]);
		} ?>
		<?$arItem["HIDE"]  = 0;?>
		<?if (!empty($arInSites) && !in_array(SITE_ID, $arInSites)): $arItem["HIDE"] = 1; endif;?>
			<?if ($arItem["HIDE"] == 0):?>
				<?if($arItem["SELECTED"]):?>
					<li><a class="selected"><?=$arItem["TEXT"]?></a></li>
				<?else:?>
					<li><a href="<?=($arItem["PARAMS"]["CITY_FOLDER"] == "Y" && $GLOBALS['medi']['sfolder'][$_SESSION['MEDI_SITE_ID']] != '' ? "/".$GLOBALS['medi']['sfolder'][$_SESSION['MEDI_SITE_ID']] : '') ?><?=$arItem["LINK"] ?>"><?=$arItem["TEXT"] ?></a></li>
				<?endif?>
			<?endif?>

		<?endforeach?>
	</ul>
<?endif?>
