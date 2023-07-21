<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<div id="personalMenuWrap">
<ul id="personalMenu">

<?
foreach($arResult as $arItem):
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
		continue;

	$curlink = ($arItem["PARAMS"]["CITY_FOLDER"] == "Y" && $GLOBALS['medi']['sfolder'][$_SESSION['MEDI_SITE_ID']] != '' ? "/".$GLOBALS['medi']['sfolder'][SITE_ID] : '').$arItem["LINK"];?>
	<?if ($APPLICATION->GetCurDir() == $curlink) $arItem['SELECTED'] = true; ?>
	<?if($arItem["SELECTED"]):?>
		<li><a href="<?=$curlink?>" class="selected"><?=$arItem["TEXT"]?></a></li>
	<?else:?>
		<li><a href="<?=$curlink?>"><?=$arItem["TEXT"]?></a></li>
	<?endif?>

<?endforeach?>

</ul>
</div>
<?endif?>
