<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//__($arResult);?>
<div class="salon-page">

	<div class="spage_print"><a href="#" class="active-link" onclick="window.print();return false;">Распечатать</a></div>
	<div class="salon-page-section">

		<div class="salon-info salon-col-margin">
			<div class="si__header">Информация о салоне</div>
			<div class="si__content">
				<?if (!empty($arResult["ADDRESS"])): ?>
				<div class="si_address si_row">
					<div class="si_icon_address si_icon">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 729.33 729.33"><defs><style>.si_icon_address .a{fill:none;stroke:#e20074;stroke-miterlimit:10;stroke-width:25px;}.si_icon_address .b{fill:#e20074;}</style></defs><title>Адрес салона</title><circle class="a" cx="364.66" cy="364.66" r="352.16"/><path class="b" d="M740.19,227.73l-.38-.1c-100.78,0-182.49,81.7-182.49,182.48a182.1,182.1,0,0,0,53.39,129L739.89,668.27l.22.1L869.29,539.2a182.14,182.14,0,0,0,53.39-129C922.68,309.44,841,227.73,740.19,227.73Zm-2.82,288.83a105,105,0,1,1,105-105A105,105,0,0,1,737.37,516.56Z" transform="translate(-375.34 -83.34)"/></svg>
					</div>
					<div class="si_address_title si_title">Адрес салона</div>
					<div class="si_address_content si_content"><?=$arResult["ADDRESS"]?></div>
				</div>
				<?endif; ?>
				<?if ($arResult['METRO']) {?>

					<?foreach ($arResult['METRO'] AS $metro) {?>

				<div class="si_metro  si_row">
					<?if($metro['SECTION']['ICON']['SRC']){?>
					<div class="si_icon si_icon_metro"><img src="<?=$metro['SECTION']['ICON']['SRC']?>" alt="<?=$metro['SECTION']['NAME']?>" title="<?=$metro['SECTION']['NAME'] ?>"/></div>
					<?}?>
					<div class="si_title si_metro_title">Станция метро</div>
					<div class=" si_content si_metro_content">
						<?=$metro['NAME'] ?>
					</div>
				</div>
					<?} ?>
				<?} ?>

				<div class="si_shedule si_row">
					<div class="si_icon_shedule si_icon">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 39 39"><defs><style>.si_icon_shedule .a{fill:none;stroke:#e20074;stroke-miterlimit:10;stroke-width:2px;}.si_icon_shedule .b{fill:#e20074;}</style></defs><title>Режим работы</title><circle class="a" cx="19.5" cy="19.5" r="18.5"/><rect class="b" x="17.5" y="7.67" width="4" height="12"/><rect class="b" x="657.42" y="412.33" width="4" height="13" transform="translate(442.83 -639.92) rotate(90)"/></svg>
					</div>
					<div class="si_shedule_title  si_title">Режим работы
						<?if ($arResult['HOLIDAY_SHEDULE']['PROPERTY_HIDE_RR_VALUE'] != 'Да'):?>
						<div  class="<?=($arResult['WORKING'] == "0" ? 'not-':'')?>working"><?=$arResult['WORK_STR']?></div>
						<?endif;?>
					</div>
					<div class="si_shedule_content si_content">
					<?if(!empty($arResult["HOLIDAY_SHEDULE"])):?>
					  <div id="holiday_shedule_text">
					  	<span class="holiday_shedule_title"><?=$arResult["HOLIDAY_SHEDULE"]["NAME"]?></span>
						<?=$arResult["HOLIDAY_SHEDULE"]["PREVIEW_TEXT"]?>
					  </div>
				  	<?endif;?>
					<?if ($arResult['HOLIDAY_SHEDULE']['PROPERTY_HIDE_RR_VALUE'] != 'Да'):?>
						<?if (!empty($arResult['UF_RR_MON'])):?>
						<table>
							<tr>
								<td <?=(date('N') == 1 ? 'class="current"' : '')?>>Пн.</td>
								<td <?=(date('N') == 1 ? 'class="current"' : '')?>><?=$arResult['UF_RR_MON']?></td>
							</tr>
							<tr>
								<td <?=(date('N') == 2 ? 'class="current"' : '')?>>Вт.</td>
								<td <?=(date('N') == 2 ? 'class="current"' : '')?>><?=$arResult['UF_RR_TUE']?></td>
							</tr>
							<tr>
								<td <?=(date('N') == 3 ? 'class="current"' : '')?>>Ср.</td>
								<td <?=(date('N') == 3 ? 'class="current"' : '')?>><?=$arResult['UF_RR_WED']?></td>
							</tr>
							<tr>
								<td <?=(date('N') == 4 ? 'class="current"' : '')?>>Чт.</td>
								<td <?=(date('N') == 4 ? 'class="current"' : '')?>><?=$arResult['UF_RR_THU']?></td>
							</tr>
							<tr>
								<td <?=(date('N') == 5 ? 'class="current"' : '')?>>Пт.</td>
								<td <?=(date('N') == 5 ? 'class="current"' : '')?>><?=(empty($arResult['UF_RR_FRI']) ? 'выходной' : $arResult['UF_RR_FRI'])?></td>
							</tr>
							<tr>
								<td <?=(date('N') == 6 ? 'class="current"' : '')?>>Сб.</td>
								<td <?=(date('N') == 6 ? 'class="current"' : '')?>><?=(empty($arResult['UF_RR_SAT']) ? 'выходной': $arResult['UF_RR_SAT'])?></td>
							</tr>
							<tr>
								<td <?=(date('N') == 7 ? 'class="current"' : '')?>>Вс.</td>
								<td <?=(date('N') == 7 ? 'class="current"' : '')?>><?=(empty($arResult['UF_RR_SUN']) ? 'выходной': $arResult['UF_RR_SUN'])?></td>
							</tr>
						</table>
						<?else:?>
						<?=$arResult["SCHEDULE"]?>
						<?endif;?>
					<?endif;?>	
					</div>
				</div>

				<?if (!empty($arResult["PHONE"])): ?>
				<div class="si_phone si_row">
					<div class="si_icon_phone si_icon">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 39 39"><defs><style>.si_icon_phone .a{fill:none;stroke:#e20074;stroke-miterlimit:10;stroke-width:2px;}.si_icon_phone .b{fill:#e20074;}</style></defs><title>Номер телефона</title><circle class="a" cx="19.5" cy="19.5" r="18.5"/><path class="b" d="M651.47,414.17a3.31,3.31,0,0,0-1.13,3.41,10.58,10.58,0,0,0,3.65,6.6,3.38,3.38,0,0,0,3.67.73l3.74,6.45a4.29,4.29,0,0,1-5.5.38,20.18,20.18,0,0,1-5.45-5.41,31,31,0,0,1-5.55-11.84c-.09-.47-.16-.94-.22-1.42-.36-2.7.45-4.17,3-5.42Z" transform="translate(-635.42 -399.33)"/><path class="b" d="M652.69,414.29a4.08,4.08,0,0,1-.82-.73c-1.17-2-2.31-4-3.47-6a.88.88,0,0,1,.39-1.35l1.62-1a.87.87,0,0,1,1.35.38c1.16,2,2.32,4,3.44,6,.15.26.19.84,0,1C654.46,413.22,653.61,413.7,652.69,414.29Z" transform="translate(-635.42 -399.33)"/><path class="b" d="M662.57,431.4a3.89,3.89,0,0,1-.8-.69c-1.18-2-2.33-4-3.5-6a.85.85,0,0,1,.31-1.3c.59-.35,1.18-.7,1.78-1a.83.83,0,0,1,1.23.34c1.19,2.06,2.39,4.12,3.55,6.21.12.21.13.71,0,.81C664.33,430.3,663.47,430.81,662.57,431.4Z" transform="translate(-635.42 -399.33)"/></svg>
					</div>
					<div class="si_phone_title si_title">Телефон</div>
					<div class="si_phone_content si_content"><a href="tel:<?=$arResult["PHONE"]?>" id="GTM_salon_phone" class="salon_phone"><?=$arResult["PHONE"]?></a></div>
				</div>
				<?endif; ?>

				<?if (!empty($arResult["EMAIL"])): ?>
				<div class="si_email  si_row">
					<div class="si_icon_email si_icon">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 180.26 128.59"><defs><style>.si_icon_email .a{fill:#e20074;}</style></defs><title>почта</title><path class="a" d="M618,425.31V521a5,5,0,0,0-.22.75,19.12,19.12,0,0,1-19.48,16.59q-70.74,0-141.47-.09A18.47,18.47,0,0,1,443.78,533a19.12,19.12,0,0,1-6-14.52q.08-31.75.09-63.5c0-8.82-.1-17.64-.05-26.46a19.1,19.1,0,0,1,12.36-17.48c1.54-.55,3.16-.88,4.74-1.31h146a4.71,4.71,0,0,0,.73.22,18.74,18.74,0,0,1,12.62,7.26A20.87,20.87,0,0,1,618,425.31ZM456.9,420c.2.24.29.39.41.51l24.14,24.07,28.23,28.11c5.32,5.3,10.61,10.62,15.94,15.91,1.81,1.79,3.5,1.73,5.28-.05l26.05-26,42.06-42a4.39,4.39,0,0,0,.36-.53Zm9.7,108.63H588.77l-42.23-42.3a17.3,17.3,0,0,1-1.18,1.44q-7.27,7.29-14.58,14.56c-1.62,1.61-3.46,1.68-5,.18q-5.33-5.34-10.61-10.7c-2-2-3.92-4-5.85-6Zm87.22-49c.06.07.18.21.31.34l12.73,12.74,23.46,23.49c3.77,3.77,7.52,7.55,11.32,11.28a1.68,1.68,0,0,0,1.42.42,6.19,6.19,0,0,0,4.15-3.84,19.55,19.55,0,0,0,1.07-5.58c.1-9.64,0-19.28,0-28.93q0-16,0-32.08V426.2c0-.35,0-.7-.05-1.07Zm-51.74-.27-53.55-53.66v37.95q0,18.63,0,37.26c0,6.79,0,13.58,0,20.37a6.8,6.8,0,0,0,3.6,6.43,1.21,1.21,0,0,0,1.66-.27c4.59-4.64,9.22-9.24,13.84-13.85l30.17-30.14C499.32,482,500.81,480.6,502.08,479.38Z" transform="translate(-437.74 -409.77)"/></svg>
					</div>
					<div class="si_email_title si_title">E-mail</div>
					<div class="si_email_content si_content"><a href="mailto:<?=$arResult["EMAIL"]?>" id="GTM_salon_email"><?=$arResult["EMAIL"]?></a></div>
				</div>
				<?endif; ?>

			</div>

		</div>

		<div class="salon-path salon-col-margin">
			<div class="sp_block">
				<div class="sp__header">Как добраться:</div>
				<div class="sp_content"><p><?=$arResult["UF_VECTOR"] ?></p></div>
			</div>
			<div class="sp_map">
			  <div id="map_salon"></div>
			</div>
		</div>

	</div>

	<?if (!empty($arResult['ASSORTMENT'])){?>
	<div class="storeAssortment">
		<h3 class="h3 ff-medium">Ассортимент салона</h3>
		<div class="section-tags">
			<?foreach($arResult['ASSORTMENT'] AS $assort){
				$vals = array_shift($assort);?>
			<a href="<?=$vals['LINK']?>" class="section-tag"><?=$vals['NAME']?></a>
			<?}?>
		</div>
	</div>
	<?}?>

	<div class="salon-page-section">
		<?if (!empty($arResult['SERVICES'])){
			$GLOBALS['serviceFilter'] = ['ID' => $arResult['SERVICES_SLIDES']];?>
		<div class="salon-services salon-col-margin">

			<h3 class="h3 ff-medium">Услуги в салоне</h3>
				<div class="servicesSlider mainService">

					<div id="mainSalonCarousel" class="mainServiceContainer">
						<?$APPLICATION->IncludeComponent(
							"dresscode:slider",
							"salon",
							array(
								"IBLOCK_TYPE" => "",
								"IBLOCK_ID" => "11",
								"CACHE_TYPE" => "A",
								"CACHE_TIME" => "360000",
								"PICTURE_WIDTH" => "440",
								"PICTURE_HEIGHT" => "440",
								"COMPONENT_TEMPLATE" => ".default",
								"FILTER_NAME" => "serviceFilter"
							),
							false
						);?>
					</div>
					<?/*?>
					<div id="mainSalonCarousel" class="mainServiceContainer">
						<div class="slideContainer">
							<ul class="slideBox items">
								<?foreach($arResult["SERVICES"] as $ixd => $services):
									$arElement = array_shift($services);?>

									<?$image =  $arElement["PICTURE"];?>
									<li class="item">
										<div class="wrap">
											<?if(!empty($image["src"])):?>
												<div class="bigPicture"><a href="<?=$arElement["LINK"]?>"><img src="<?=$image["src"]?>" alt="<?=$arElement["NAME"]?>"></a></div>
											<?endif;?>
											<div class="title"><a href="<?=$arElement["LINK"]?>"><span><?=$arElement["NAME"]?></span></a></div>
											<a href="<?=$arElement["LINK"]?>" class="more">Подробнее</a>
										</div>
									</li>
								<?endforeach;?>
							</ul>
						</div>
						<a href="#" class="mainSalonBtnLeft btnLeft"></a>
						<a href="#" class="mainSalonBtnRight btnRight"></a>
					</div>
					<script>
						$("#mainSalonCarousel").dwCarousel({
							leftButton: ".mainSalonBtnLeft",
							rightButton: ".mainSalonBtnRight",
							countElement: 1,
							resizeElement: true,
							resizeAutoParams: {
								1920: 1,
								1024: 1,
								550: 1
							}
						});
					</script>
					<?*/?>
			</div>
		</div>
		<?}?>
		<div  class="salon-photo salon-col-margin">

			  <?if(!empty($arResult["IMAGES"])):?>
			<div class=" storePictureContainer">

			  			<h3 class="h3 ff-medium">Фото салона</h3>
	          <div id="pictureContainer">
	            <div class="pictureSlider">
	              <?foreach ($arResult["IMAGES"] as $ipr => $arNextPicture):?>
	              <div class="item"> <a href="<?=$arNextPicture["BIG"]["SRC"]?>" title=""  class="zoom" data-small-picture="<?=$arNextPicture["SMALL"]["src"]?>" data-large-picture="<?=$arNextPicture["BIG"]["SRC"]?>"><img src="<?=$arNextPicture["MED"]["src"]?>" alt="<?=$arResult['TITLE'];?>" title="<?=$arResult['TITLE']; ?>"></a> </div>
	              <?endforeach;?>
	            </div>
	          </div>
	          <div id="moreImagesCarousel"<?if(empty($arResult["IMAGES"]) || count($arResult["IMAGES"]) <= 1):?> class="hide"<?endif;?>>
	            <div class="carouselWrapper">
	              <div class="slideBox">
	                <?if(empty($arResult["IMAGES"]) || count($arResult["IMAGES"]) > 1):?>
	                <?foreach ($arResult["IMAGES"] as $ipr => $arNextPicture):?>
	                <div class="item"> <a href="<?=$arNextPicture["BIG"]["SRC"]?>" data-large-picture="<?=$arNextPicture["BIG"]["SRC"]?>" data-small-picture="<?=$arNextPicture["SMALL"]["src"]?>"> <img src="<?=$arNextPicture["SMALL"]["src"]?>" alt=""> </a> </div>
	                <?endforeach;?>
	                <?endif;?>
	              </div>
	            </div>
	            <div class="controls"> <a href="#" id="moreImagesLeftButton"></a> <a href="#" id="moreImagesRightButton"></a> </div>
	          </div>
	          <div class="clear"></div>
	        </div>
			<?endif;?>
		</div>
	</div>

</div><?//end .salon-page?>







<div itemscope itemtype="http://schema.org/LocalBusiness" class="microdata">
    <span itemprop="name"><?=$arResult['CITY']?></span>
    <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
        <span itemprop="streetAddress"><?=$arResult['PHONE']?></span>
        <span itemprop="postalCode"><?=$arResult['EMAIL']?></span>
        <span itemprop="addressLocality"><?=$arResult["SCHEDULE"]?></span>,
    </div>
    <span itemprop="telephone"><?=$gpsN?></span>
    <span itemprop="email"><?=$gpsS?></span>
    <span itemprop="openingHours"><??></span>
    <div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
        <span itemprop="latitude" ><??></span>
        <span itemprop="longitude" >&lt;??&gt;</span>
    </div>
</div>

<?
  $gpsN = substr($arResult["GPS_N"], 0, 15);
  $gpsS = substr($arResult["GPS_S"], 0, 15);
  $gpsText = $arResult["ADDRESS"];
  $gpsTextLen = strlen($arResult["ADDRESS"]);
  ?>
<script>
	var map;
	ymaps.ready(initMap);
	function initMap()
	{
		features = [{
		  'type':'Feature',
		  'id':'<?=$arResult['ID']?>',
		  'geometry':{
			'type':'Point',
			'coordinates':[<?=$gpsN?>, <?=$gpsS?>]},
			'properties':{
			  'hintContent':'<?=$arResult['NAME']?>',
			  'balloonContentHeader':'<div class="bheader"><?=$arResult['NAME']?></div>',
			  'balloonContentBody':'<p class="city_value address" ><?=$arResult['ADDRESS']?></p>\n<p class="city_value worktime" ><?=$arResult['SCHEDULE']?></p>',
			  'balloonContentFooter':'<div class="bfooter"></div>'
			}
		  }];
		map = new ymaps.Map("map_salon", {
			center: [<?=$gpsN?>, <?=$gpsS?>],
			zoom: 16,
			controls: ['geolocationControl', 'fullscreenControl', 'zoomControl']
		});
		map.options.set( {
			suppressMapOpenBlock: true,
		});
		mediObjectManager = new ymaps.ObjectManager({
			clusterize: false,
			geoObjectIconLayout: 'default#image',
			geoObjectIconImageHref:  '//<?=SITE_SERVER_NAME?><?=SITE_TEMPLATE_PATH;?>/images/placemarker.png',
			geoObjectIconImageSize: [28, 37],
			geoObjectIconImageOffset: [-14, -37]
		});
		map.geoObjects.add(mediObjectManager);
		if (features !== undefined) {
			mediObjectManager.add({
				type: 'FeatureCollection',
				features: features
			});
		}
	}
</script>
