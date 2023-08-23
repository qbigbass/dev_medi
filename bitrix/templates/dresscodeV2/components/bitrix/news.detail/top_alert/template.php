<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
$height = ($arResult['PROPERTIES']['HEIGHT']['VALUE'] > 0 ? intval($arResult['PROPERTIES']['HEIGHT']['VALUE']) : 90);
$bg = ($arResult['PROPERTIES']['BG']['VALUE'] != "" ? $arResult['PROPERTIES']['BG']['VALUE'] : "#e20074");
$color = ($arResult['PROPERTIES']['COLOR']['VALUE'] != "" ? $arResult['PROPERTIES']['COLOR']['VALUE'] : "#fff");
?>
<div class="top_alert" style="height:<?=$height;?>px;background-color:<?=$bg;?>;color:<?=$color;?>;">
	<span class="close"></span>
	<div class="top_alert_content">

		<?if (!empty($arResult['PREVIEW_PICTURE'])){?>
		<div class="ta_picture <?=strtolower($arResult['PROPERTIES']['IMG_POS']['VALUE_XML_ID'])?>" style="background:url(<?=$arResult['PREVIEW_PICTURE']['SRC'];?>) no-repeat 50% 50%; height:<?=$height;?>px;"></div>
		<?}?>
		<?if ($arResult['PROPERTIES']['LINK']['VALUE'] != "" ){?><a href="<?=$arResult['PROPERTIES']['LINK']['VALUE']?>"><?}?>
		<?=$arResult["PREVIEW_TEXT"];?>
		<?if ($arResult['PROPERTIES']['LINK']['VALUE'] != "" ){?></a><?}?>
		
	</div>
</div>
