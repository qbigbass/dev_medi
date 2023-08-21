<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var array $arParams */
/** @var array $arResult */
/** @var CBitrixComponentTemplate $this */

$this->setFrameMode(true);


if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

?>

<div class="bx-pagination">
	<div class="bx-pagination-container row">
		<ul>
<?if($arResult["bDescPageNumbering"] === true):?>

	<?if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		<?if($arResult["bSavePage"]):?>
			<li class="bx-pag-prev"><a href="?PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>" data-page="<?=($arResult["NavPageNomer"]+1)?>"><span class="arrow_left active"></span></a></li>
			<li class=""><a href="?PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>" data-page="<?=($arResult["NavPageNomer"]+1)?>"><span class="num">1</span></a></li>
		<?else:?>
			<?if (($arResult["NavPageNomer"]+1) == $arResult["NavPageCount"]):?>
				<li class="bx-pag-prev"><a href="<?=$strNavQueryStringFull?>" data-page="0"><span class="arrow_left active"></span></a></li>
			<?else:?>
				<li class="bx-pag-prev"><a href="?PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>" data-page="<?=($arResult["NavPageNomer"]+1)?>"><span class="arrow_left active"></span></a></li>
			<?endif?>
			<li class=""><a href="<?=$strNavQueryStringFull?>" data-page="0"><span class="num">1</span></a></li>
		<?endif?>
	<?else:?>
			<li class="bx-pag-prev"><span class="arrow_left"></span></li>
			<li class="bx-active"><span class="num">1</span></li>
	<?endif?>

	<?
	$arResult["nStartPage"]--;
	while($arResult["nStartPage"] >= $arResult["nEndPage"]+1):
	?>
		<?$NavRecordGroupPrint = $arResult["NavPageCount"] - $arResult["nStartPage"] + 1;?>

		<?if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
			<li class="bx-active"><span class="num"><?=$NavRecordGroupPrint?></span></li>
		<?else:?>
			<li class=""><a href="?PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>" data-page="<?=$arResult["nStartPage"]?>"><span class="num"><?=$NavRecordGroupPrint?></span></a></li>
		<?endif?>

		<?$arResult["nStartPage"]--?>
	<?endwhile?>

	<?if ($arResult["NavPageNomer"] > 1):?>
		<?if($arResult["NavPageCount"] > 1):?>
			<li class=""><a href="?PAGEN_<?=$arResult["NavNum"]?>=1" data-page="1"><span class="num"><?=$arResult["NavPageCount"]?></span></a></li>
		<?endif?>
			<li class="bx-pag-next"><a href="?PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>" data-page="<?=($arResult["NavPageNomer"]-1)?>"><span class="arrow_right active"></span></a></li>
	<?else:?>
		<?if($arResult["NavPageCount"] > 1):?>
			<li class="bx-active"><span class="num"><?=$arResult["NavPageCount"]?></span></li>
		<?endif?>
			<li class="bx-pag-next"><span class="arrow_right"></span></li>
	<?endif?>

<?else:?>

	<?if ($arResult["NavPageNomer"] > 1):?>
		<?if($arResult["bSavePage"]):?>
			<li class="bx-pag-prev"><a href="?PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>" data-page="<?=($arResult["NavPageNomer"]-1)?>"><span class="arrow_left active"></span></a></li>
			<li class=""><a href="?PAGEN_<?=$arResult["NavNum"]?>=1" data-page="1"><span class="num">1</span></a></li>
		<?else:?>
			<?if ($arResult["NavPageNomer"] > 2):?>
				<li class="bx-pag-prev"><a href="?PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>" data-page="<?=($arResult["NavPageNomer"]-1)?>"><span class="arrow_left active"></span></a></li>
			<?else:?>
				<li class="bx-pag-prev"><a href="<?=$strNavQueryStringFull?>" data-page="0"><span class="arrow_left active"></span></a></li>
			<?endif?>
			<li class=""><a href="<?=$strNavQueryStringFull?>" data-page="0"><span class="num">1</span></a></li>
		<?endif?>
	<?else:?>
			<li class="bx-pag-prev"><span class="arrow_left"></span></li>
			<li class="bx-active"><span class="num">1</span></li>
	<?endif?>

	<?
	$arResult["nStartPage"]++;
	while($arResult["nStartPage"] <= $arResult["nEndPage"]-1):
	?>
		<?if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
			<li class="bx-active"><span class="num"><?=$arResult["nStartPage"]?></span></li>
		<?else:?>
			<li class=""><a href="?PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>" data-page="<?=$arResult["nStartPage"]?>"><span class="num"><?=$arResult["nStartPage"]?></span></a></li>
		<?endif?>
		<?$arResult["nStartPage"]++?>
	<?endwhile?>

	<?if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		<?if($arResult["NavPageCount"] > 1):?>
			<li class=""><a href="?PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>" data-page="<?=$arResult["NavPageCount"]?>"><span class="num"><?=$arResult["NavPageCount"]?></span></a></li>
		<?endif?>
			<li class="bx-pag-next"><a href="?PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>" data-page="<?=($arResult["NavPageNomer"]+1)?>"><span class="arrow_right active"></span></a></li>
	<?else:?>
		<?if($arResult["NavPageCount"] > 1):?>
			<li class="bx-active"><span class="num"><?=$arResult["NavPageCount"]?></span></li>
		<?endif?>
			<li class="bx-pag-next"><span class="arrow_right"></span></li>
	<?endif?>
<?endif?>


		</ul>
		<div style="clear:both"></div>
	</div>
</div>
