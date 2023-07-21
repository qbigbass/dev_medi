<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?if(!empty($arResult["ITEMS"])):?>
	<div id="slider">
		<ul class="slideBox">
			<?foreach($arResult["ITEMS"] as $i => $arElement):?>
				<?
					$this->AddEditAction($arElement['ID'], $arElement['EDIT_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arElement['ID'], $arElement['DELETE_LINK'], CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE"), array());
				?>
				<?if(!empty($arElement["PROPERTIES"]["VIDEO_SLIDE"]["VALUE"]) || !empty($arElement["DETAIL_PICTURE"]["src"])):?>
					<li id="<?=$this->GetEditAreaId($arElement['ID']);?>">
						<?if (!empty($arElement['PROPERTIES']['ACTION']['VALUE'])){?><a href="<?=$arElement['PROPERTIES']['ACTION']['VALUE']?>" data-id="<?=$i+1;?>" class="slide_link"
							<?=($arElement['PROPERTIES']['TARGET']['VALUE_XML_ID'] == '_blank' ? 'target="_blank"' : '')?>><?}?>
						<?if(!empty($arElement["DETAIL_TEXT"]) || !empty($arElement['PROPERTIES']['ACTION']['VALUE'])):?>
							<div class="limiterContainer">
								<div class="limiter">
									<div class="sliderContent<?if(!empty($arElement["PROPERTIES"]["POSITION"]["VALUE_XML_ID"])):?> <?=$arElement["PROPERTIES"]["POSITION"]["VALUE_XML_ID"]?>Container<?endif;?>" style="display:none"></div>
								</div>
							</div>
						<?endif;?>
						<?if(!empty($arElement["PROPERTIES"]["VIDEO_SLIDE"]["VALUE"])):?>
							<div class="slideVideoContainer">
								<?if(!empty($arElement["PROPERTIES"]["VIDEO_POSTER"]["VALUE"])):?>
									<div class="videoPoster" style="background-image:url(<?=CFile::GetPath($arElement["PROPERTIES"]["VIDEO_POSTER"]["VALUE"]);?>);"></div>
								<?endif;?>
								<?if(!empty($arElement["PROPERTIES"]["VIDEO_COLOR"]["VALUE"])):?>
									<div class="sliderVideoOverBg" style="background-color: <?=$arElement["PROPERTIES"]["VIDEO_COLOR"]["VALUE"]?>;<?if(!empty($arElement["PROPERTIES"]["VIDEO_COLOR"]["VALUE"])):?> opacity: <?=$arElement["PROPERTIES"]["VIDEO_OPACITY"]["VALUE"];?><?endif;?>"></div>
								<?endif;?>
								<video class="slideVideo" width="auto" height="auto" loop="loop" autoplay="autoplay" preload="auto" muted="" poster="<?=CFile::GetPath($arElement["PROPERTIES"]["VIDEO_POSTER"]["VALUE"]);?>">
									<source src="<?=CFile::GetPath($arElement["PROPERTIES"]["VIDEO_SLIDE"]["VALUE"]);?>" type="video/mp4">
									<p><?=GetMessage("VIDEO_NOT_SUPPORTED")?></p>
								</video>
							</div>
						<?else:?>
							<span data-large="<?=$arElement["DETAIL_PICTURE"]["src"]?>" data-normal="<?=$arElement["MOBILE_IMG"]["src"]?>"></span>
						<?endif;?>
						<?if (!empty($arElement['PROPERTIES']['ACTION']['VALUE'])){?></a><?}?>
					</li>
				<?endif;?>
			<?endforeach;?>
		</ul>
		<a href="#" class="sliderBtnLeft"></a>
		<a href="#" class="sliderBtnRight"></a>
	</div>
	<script>
	window.dataLayer = window.dataLayer || [];
	dataLayer.push({
	'ecommerce': {
	  'promoView': {
	    'promotions': [
		<?
		$i=1;
		foreach($arResult["ITEMS"] as $ii => $arElement):?>
	    {
	      'id': 'slider<?=$arElement['ID']?>',
	      'name': '<?=$arElement['PROPERTIES']['ACTION']['VALUE']?>',
	      'creative': '<?=$arElement['NAME']?>',
	      'position': <?=$i?>,
	    },
		<?
		$i++;
		endforeach;?>
		]

	  }
	},
	'event': 'gtm-ee-event',
	'gtm-ee-event-category': 'Enhanced Ecommerce',
	'gtm-ee-event-action': 'Promotion Impressions',
	'gtm-ee-event-non-interaction': 'True',
	});
	</script>
<?endif;?>
<script>
$(document).ready(function(){

	var slides = [];
	<?
	$i=1;
	foreach($arResult["ITEMS"] as $ii => $arElement):?>
	slides[<?=$i?>] = {'id': 'slider<?=$arElement['ID']?>', 'name': '<?=$arElement['PROPERTIES']['ACTION']['VALUE']?>', 'creative': '<?=$arElement['NAME']?>', 'position': <?=$i?>};
	<?
	$i++;
	endforeach;?>
	$(".slide_link").click(function(){
		$id = $(this).attr('data-id');

		window.dataLayer = window.dataLayer || [];
		dataLayer.push({
		'ecommerce': {
		  'promoClick': {
		    'promotions': [{
		      'id': slides[$id]['id'],
		       'name': slides[$id]['name'],
		      'creative': slides[$id]['creative'],
		      'position': slides[$id]['position'],
		    }]
		  }
		},
		'event': 'gtm-ee-event',
		'gtm-ee-event-category': 'Enhanced Ecommerce',
		'gtm-ee-event-action': 'Promotion Clicks',
		'gtm-ee-event-non-interaction': 'False',
		});
	});
});
</script>
