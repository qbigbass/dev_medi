<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>
<?if (!empty($arResult["SECTIONS"])):?>
	<div id="nextSection">
		<div class="title"><?=GetMessage("SELECT_SECTION")?></div>
		<ul>
			<?$i = 0;?>
			<?foreach($arResult["SECTIONS"] as $arElement):?>
	    		<?if($arElement["ELEMENT_CNT"] > 0 || $arParams["COUNT_ELEMENTS"] != "Y"):?>
	    			<li <?=($i>4 ? 'class="off"':'')?>>
		    			<span class="sectionLine">
		    				<span class="sectionColumn"><a href="<?=$arElement["SECTION_PAGE_URL"]?>" class="<?=!empty($arElement["SELECTED"]) ? "selected" : ""?>"><?=$arElement["NAME"]?></a></span>
		    				<?if($arParams["COUNT_ELEMENTS"] == "Y"):?>
		    				<span class="sectionColumn last"><a href="<?=$arElement["SECTION_PAGE_URL"]?>" class="cnt"><?=$arElement["ELEMENT_CNT"]?></a></span>
		    				<?endif;?>
		    			</span>
	    			</li>
					<?$i++;?>
	    		<?endif;?>
		    <?endforeach;?>
			<?if($i>5){?>
				<li><a href="#" class=" theme-link-dashed showALL <?=$i;?>">Показать ещё <?=($i-5);?></a></li>
			<?}?>
		</ul>
	</div>
<?endif;?>
