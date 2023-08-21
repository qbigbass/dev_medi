<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);$arPlacemarks = array();?>

<?if(!empty($arResult["STORES"]) && $arResult["SHOW_STORES"] == "Y"):?>
	<div id="stores">
		<div class="heading"><?=GetMessage("STORES_HEADING")?></div>
		<div class="wrap">
			<div class="tabs-wrap">
				<div class="tabs-links">
		          <div class="tab-link tab-dashed-link active">Список</div>
		          <div class="tab-link tab-dashed-link map-link">Карта</div>
		        </div>
				<div class="tabs-content" >

					<div class="tab-content active" id="storesList">
						<?
						if (!empty($arResult["STORES"])):?>
						<div class="storeListHead flex-salons">
							<div class="storeListHeadCol salons-name flex-salons-item">Название и адрес</div>
												<div class="storeListHeadCol salons-metro flex-salons-item">Станция метро</div>
												<div class="storeListHeadCol salons-shedule flex-salons-item">Режим работы</div>
							<div class="storeListHeadCol salons-phone flex-salons-item">&nbsp;</div>
						</div>
						<?
					foreach ($arResult["STORES"] as $ins => $arNextStore) :	 ?>
						 <div class="flex-salons" data-salonid="<?=$arNextStore["ID"] ?>">
							<div class="flex-salons-item salons-name">
								<b class="ff-medium"><?=(str_replace("м. ","м.&nbsp;", $arNextStore["TITLE"]))?></b>
								<p><?=$arNextStore["ADDRESS"] ?></p>
								<?
								if (!empty($arNextStore["DESCRIPTION"])) : ?>
									<p><?=$arNextStore["DESCRIPTION"] ?></p>
								<?endif; ?>
							</div>

							<?if ($arResult['HAS_METRO'] == true) { ?>
							<div class="salons-metro flex-salons-item">
							<?if (!empty($arNextStore['METRO'])):

								if ($arNextStore['METRO'][0]['SECTION']['ICON']['CONTENT_TYPE'] == 'image/svg+xml') {
									?>
									<img src="<?=$arNextStore['METRO'][0]['SECTION']['ICON']["SRC"] ?>" title="<?=$arNextStore['METRO'][0]['SECTION']["NAME"] ?> линия"><?
								}else{?>
									<img src="<?=$arNextStore['METRO'][0]['SECTION']['ICON']["SRC"] ?>" title="<?=$arNextStore['METRO'][0]['SECTION']["NAME"] ?> линия">
								<?}?>
								<?=$arNextStore['METRO'][0]["NAME"] ?>
							<?endif;?>

							</div>
							<?}?>
							<div class="salons-shedule  flex-salons-item">
								<div class="shedule-block">
								<?if (!empty($arNextStore['HOLIDAY_SHEDULE'])):?>
								<a href="<?=$arNextStore["DETAIL_PAGE_URL"] ?>" class="theme-link-dashed"><?=$arNextStore["HOLIDAY_SHEDULE"]["NAME"] ?></a>
								<?else:?>
									<div  class="<?=($arNextStore['WORKING'] == "0" ? 'not-':'')?>working"><?=$arNextStore['WORK_STR']?></div><br>
									<div class="shedule-str" data-start="" data-end=""><?=$arNextStore['SCHEDULE'];?></span></div>
								<?endif;?></div>

							</div>
							<div class="salons-phone flex-salons-item">
								<?if ($arNextStore['BOOKING'] == '1' && $arNextStore["REAL_AMOUNT"] > 0){?><a href="#" class="greyButton reserve get_medi_popup_Window" data-src="/ajax/catalog/?action=reserve&s=<?=$arNextStore['ID']?>&p=<?=$arParams['OFFER_ID']?>" data-title="Забронировать в салоне" data-action="reserve">Забронировать</a><?}elseif( $arNextStore['UF_ESHOP_ORDERS'] == '1'  && $arNextStore["REAL_AMOUNT"] == 0){?><a href="#" class="greyButton order_delivery2salon get_medi_popup_Window" data-src="/ajax/catalog/?action=order_delivery2salon&s=<?=$arNextStore['ID']?>&p=<?=($arParams['OFFER_ID']>0
									 ? $arParams['OFFER_ID'] :  $arParams["ELEMENT_ID"])?>" data-title="Заказать и забрать в салоне" data-action="order_delivery2salon">Заказать</a><?}?>
							</div>

						</div>
						<?endforeach; ?>
						<?else:?>
						<h3 style="text-align:center;" class="ff-medium">К сожалению, подходящие салоны не найдены. Попробуйте изменить условия поиска.</h3>
						<?endif;?>

					<?/*<table class="storeTable">
						<tbody>
						<tr>
							<th class="name"><?=GetMessage("STORES_NAME")?></th>
							<th><?=GetMessage("STORES_GRAPH")?></th>
							<th class="amount"><?=GetMessage("STORES_AMOUNT")?></th>
							<th class="action"></th>
						</tr>
							<?$sum_gps= 0;
							foreach($arResult["STORES"] as $pid => $arProperty):
								 ?>
								<?
								if($arProperty["COORDINATES"]["GPS_S"] != 0 && $arProperty["COORDINATES"]["GPS_N"] != 0){
									$gpsN = substr(doubleval($arProperty["COORDINATES"]["GPS_N"]), 0, 15);
									$gpsS = substr(doubleval($arProperty["COORDINATES"]["GPS_S"]), 0, 15);
									$arPlacemarks[] = array("LON" => $gpsS, "LAT"=>$gpsN, "TEXT"=>$arProperty["TITLE"], "AVAIL" => $arProperty["REAL_AMOUNT"] > 0 ? "Y" : "N", "SCHEDULE" => $arProperty["SCHEDULE"], "DETAIL_PAGE_URL" =>$arProperty["DETAIL_PAGE_URL"],
									"PICTURE" =>  (!empty($arProperty['PICTURE']) ? $arProperty['PICTURE']['SRC'] : '')
								);

									$sum_gpsN += substr(doubleval($arProperty["COORDINATES"]["GPS_N"]), 0, 15);
									$sum_gpsS += substr(doubleval($arProperty["COORDINATES"]["GPS_S"]), 0, 15);
									$sum_gps ++;
								}
								?>
								<?$image = CFile::ResizeImageGet($arProperty["IMAGE_ID"], array('width' => 50, 'height' => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false);?>
								<?if(!($arParams["SHOW_EMPTY_STORE"] == "N" && isset($arProperty["REAL_AMOUNT"]))):?>
									<tr data-id="<?=$arProperty['ID']?>">
										<td class="name"><a href="<?=$arProperty["DETAIL_PAGE_URL"]?>"><?=$arProperty["TITLE"]?></a><?if ($arProperty['ADDRESS'] != ""){?><br/><span class="store_address"><?=$arProperty['ADDRESS']?></span><?}?></td>
										<td><?=$arProperty["SCHEDULE"]?></td>
										<td<?if($arProperty["REAL_AMOUNT"] > 0):?> class="amount green"<?else:?> class="amount red"<?endif;?>><img src="<?=SITE_TEMPLATE_PATH?>/images/<?if($arProperty["REAL_AMOUNT"] > 0):?>inStock<?else:?>outOfStock<?endif;?>.png" alt="<?=$arProperty["AMOUNT"]?>" class="icon"><?=$arProperty["AMOUNT"]?></td>
										<td  class="action"><?if ($arProperty['BOOKING'] == '1' && $arProperty["REAL_AMOUNT"] > 0){?><a href="#" class="greyButton reserve get_medi_popup_Window" data-src="/ajax/catalog/?action=reserve&s=<?=$arProperty['ID']?>&p=<?=$arParams['OFFER_ID']?>" data-title="Забронировать в салоне" data-action="reserve">Забронировать</a><?}elseif( $arProperty['UF_ESHOP_ORDERS'] == '1'  && $arProperty["REAL_AMOUNT"] == 0){?><a href="#" class="greyButton order_delivery2salon get_medi_popup_Window" data-src="/ajax/catalog/?action=order_delivery2salon&s=<?=$arProperty['ID']?>&p=<?=$arParams['OFFER_ID']?>" data-title="Заказать и забрать в салоне" data-action="order_delivery2salon">Заказать</a><?}?></td>
									</tr>
								<?endif;?>
							<?endforeach;?>
							<? $avg_gpsN = $sum_gpsN / $sum_gps;
								$avg_gpsS = $sum_gpsS / $sum_gps;
							?>
						</tbody>
					</table>*/?>
				</div>
				<div class="tab-content stores_tab">
					<script>
					    <?  // Данные о салонах

					    if (!empty($arResult['arPlacemarks'])):
					    ?>
					    window.pos = {
					        map_x: '<?=$arResult['COORD'][0] ?>',
					        map_y: '<?=$arResult['COORD'][1] ?>',
					        map_scale: 10
					    };

					    window.pos.features = <?= CUtil::PhpToJSObject($arResult['arYandexFeatures']); ?>;

						ymaps.ready(init);
						var mediMap;
						var mediObjectManager;
						function init()
						{
						    if (window.pos === undefined) {
						        window.pos = {
						            map_x: '59.112935',
						            map_y: '83.361806',
						            map_scale: '4',
						            min_scale: '4'
						        };
						    }
						    mediMap = new ymaps.Map("storeMap", {
						        center: [window.pos.map_x, window.pos.map_y],
						        zoom: window.pos.map_scale,
						        controls: ['geolocationControl', 'fullscreenControl', 'zoomControl']
						    });

						    if (window.pos.min_scale === undefined) {
						        window.pos.min_scale = '8';
						    }
						    mediMap.options.set( {
						        minZoom: window.pos.min_scale,
						        suppressMapOpenBlock: true,
						    });

						    mediObjectManager = new ymaps.ObjectManager({
						        clusterize: false,
						        geoObjectIconLayout: 'default#image',
						        geoObjectIconImageHref: '/upload/images/placemarker.png',
						        geoObjectIconImageSize: [28, 37],
						        geoObjectIconImageOffset: [-14, -37]
						    });


						    mediMap.geoObjects.add(mediObjectManager);

						    if (window.pos.features !== undefined) {
						        mediObjectManager.add({
						            type: 'FeatureCollection',
						            features: window.pos.features
						        });
						    }
						}
					    <? endif; ?>
					</script>
					<div id="storeMap" class="map-container">

					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		var elementStoresComponentParams = <?=\Bitrix\Main\Web\Json::encode($arParams)?>;
	</script>
</div>
<?endif;?>
