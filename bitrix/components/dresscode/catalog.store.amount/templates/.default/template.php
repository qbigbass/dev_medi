<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);$arPlacemarks = array();?>

<?if(!empty($arResult["STORES"]) && $arResult["SHOW_STORES"] == "Y"):?>
	<div id="stores">
		<div class="heading"><?=GetMessage("STORES_HEADING")?></div>
		<div class="wrap">
			<table class="storeTable">
				<tbody>
				<tr>
					<th class="name"><?=GetMessage("STORES_NAME")?></th>
					<th><?=GetMessage("STORES_GRAPH")?></th>
					<th class="amount"><?=GetMessage("STORES_AMOUNT")?></th>
					<th class="action"></th>
				</tr>
					<?foreach($arResult["STORES"] as $pid => $arProperty):?>
						<?
						if($arProperty["COORDINATES"]["GPS_S"] != 0 && $arProperty["COORDINATES"]["GPS_N"] != 0){
							$gpsN = substr(doubleval($arProperty["COORDINATES"]["GPS_N"]), 0, 15);
							$gpsS = substr(doubleval($arProperty["COORDINATES"]["GPS_S"]), 0, 15);
							$arPlacemarks[] = array("LON" => $gpsS, "LAT"=>$gpsN, "TEXT"=>$arProperty["TITLE"], "AVAIL" => $arProperty["REAL_AMOUNT"] > 0 ? "Y" : "N");
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
				</tbody>
			</table>
		</div>
		<div id="storeMap">
			<?$APPLICATION->IncludeComponent(
				"bitrix:map.yandex.view",
				"fastView",
				array(
					"COMPONENT_TEMPLATE" => ".default",
					"CONTROLS" => array(
						"SMALL_ZOOM_CONTROL",
						"TYPECONTROL",
						"SCALELINE"
					),
					"INIT_MAP_TYPE" => "ROADMAP",
					"MAP_DATA" => serialize(array("yandex_lat" => $gpsN, "yandex_lon" => $gpsS, "yandex_scale" => 10, "PLACEMARKS" => $arPlacemarks)),
					"MAP_HEIGHT" => "auto",
					"MAP_ID" => "",
					"MAP_WIDTH" => "auto",
					"OPTIONS" => array(
						"ENABLE_DBLCLICK_ZOOM",
						"ENABLE_DRAGGING",
						"ENABLE_KEYBOARD"
					)
				),
				false
			);?>
		</div>
	</div>
	<script type="text/javascript">
		var elementStoresComponentParams = <?=\Bitrix\Main\Web\Json::encode($arParams)?>;
	</script>
<?endif;?>
