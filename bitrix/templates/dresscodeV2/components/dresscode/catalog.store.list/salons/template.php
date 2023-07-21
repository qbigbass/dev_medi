<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(strlen($arResult["ERROR_MESSAGE"]) > 0)
	ShowError($arResult["ERROR_MESSAGE"]);

?>
<?// Определяем текущую папку(город)
if (isset($_REQUEST['cur_city']))
{
	$cur_city = $_REQUEST['cur_city'];
}
else
{

	$cur_city_folder = explode("/", $APPLICATION->GetCurDir());

	// Москва
	if ($cur_city_folder[1] == 'salons')
		$cur_city = "";
    elseif ($cur_city_folder[1] == 'rgns')
        $cur_city = "";
	// Остальные
	 else
		$cur_city = $cur_city_folder[1];
}
if ($cur_city == 's0') $cur_city = 's1';
$selected_city = array_search($cur_city, $GLOBALS['medi']['sfolder']);

$clear_but = '';
?>

<div id="salons_city_popup" class="salons_city_popup_overlay">
	<div class="salons_city_popup">
		<div class="salons_city_popup__title">Выберите город:	</div>
		<ul class="salons_city_popup__list">
	<?foreach ($GLOBALS['medi']['region_cities'] AS $sid => $scity):
        if ($scity == 'Россия') continue;?>
			<li>
			<a href="#" data-link="<?=$GLOBALS['medi']['sfolder'][$sid] ?>" class="salons_select_city salons_city_popup__location-link <?// текущий город не активен
	  if ($cur_city == $GLOBALS['medi']['sfolder'][$sid] ) {
	  ?> selected<?} ?> "><?=$scity; ?></a>
  			</li>
	<?endforeach; ?>
		</ul>
		<div class="salons_city_popup__close-container"><div class="salons_city_popup__close"></div></div>
	</div>
</div>
<div class="salons salons-tabs-wrap">
	<div class="salons-menu">
		<div class="salons-city-select">
			<div>
				Ваш город: <span class="ff-medium" data-city="<?=$cur_city?>"><?=$GLOBALS['medi']['region_cities'][$selected_city]?></span>
			</div>
<div>
				<a href="#" class="city_link">Сменить город</a>
			</div>
		</div>
		<div class="salons-menu-container">
			<div class="salons-menu-tabs">
				<?if (!empty($arResult['arPlacemarks'])):?>
				<div class="salons-menu-tab tab-map <?if (!isset($_REQUEST['s_show']) || $_REQUEST['s_show'] == 'map'){?>active<?}?>">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 111.3 134.26"  class="tab-logo"><title>карта</title><path class="tla" d="M557.36,351.52l-.12,0a55.59,55.59,0,0,0-39.32,94.89l39.35,39.35.06,0,39.35-39.35a55.59,55.59,0,0,0-39.32-94.88Zm-.86,88a32,32,0,1,1,32-32A32,32,0,0,1,556.5,439.5Z" transform="translate(-501.65 -351.48)"/></svg>Карта
				</div>
				<?endif;?>

				<?if ($arResult['HAS_METRO'] == true): ?>
				<div class="salons-menu-tab tab-metro  <?if ($_REQUEST['s_show'] == 'metro'){?>active<?}?>">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 111 134.23" class="tab-logo"><title>метро</title><path class="tla" d="M689,479.55l6.4,6.4,39.36-39.35a55.59,55.59,0,1,0-78.69,0,4.53,4.53,0,0,0,7.76-3.17,4.65,4.65,0,0,0-1.39-3.27,46.56,46.56,0,1,1,65.88.09Z" transform="translate(-639.82 -351.72)"/><polygon class="tla" points="64.47 66.56 64.47 71.97 85.95 71.97 85.95 66.56 81.77 66.56 67.56 30.6 55.57 51.59 43.59 30.6 29.36 66.56 25.2 66.56 25.2 71.97 46.67 71.97 46.67 66.56 43.46 66.56 46.59 57.59 55.57 72.38 64.56 57.59 67.69 66.56 64.47 66.56"/></svg>Метро
				</div>
				<?endif;?>
				<div class="salons-menu-tab tab-list <?if ($_REQUEST['s_show'] == 'list' || empty($arResult['arPlacemarks'])) { ?>active<?} ?>">

					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 672 572"  class="tab-logo"><title>список</title><rect class="tla" width="116" height="92"/><rect class="tla" x="186" width="486" height="92"/><rect class="tla" y="239" width="116" height="92"/><rect class="tla" x="186" y="239" width="345" height="92"/><rect class="tla" y="479" width="116" height="92"/><rect class="tla" x="186" y="479" width="486" height="92"/></svg>Список
				</div>
			</div>
			<div class="salons-menu-filtr tabs-wrap">
				<form method="get" action="" id="salonFilterForm">
				<div class="tabs-links">
					<div class="tab-link tab-btn-link tab-service active" data-id='service'>Услуги</div>
					<div class="tab-link tab-btn-link tab-assortment" data-id='assortment'>Ассортимент</div>
				</div>
				<div class="tabs-content">
					<div class="tab-content tab-service active">

						<?foreach ($arParams['PROPS']['UF_SERVICES']['VALUES'] AS $sid => $sval): ?>
						<div class="filterCheckboxField webFormItemField ">
							<input type="checkbox" class="" id="s_service_<?=$sid ?>" name="services[]" value="<?=$sid ?>" <?=(in_array($sid, $_REQUEST['services']) ? 'checked="checked"' : '')?>>
							<label for="s_service_<?=$sid ?>"><?=$sval; ?></label>
						</div>
						<?endforeach; ?>

						<div class="filterSubmit">
							<div class="btn-simple btn-micro salon_filter_submit">Показать</div>
							<div class="btn-simple btn-black btn-micro salon_filter_clear">Очистить</div>
						</div>
					</div>


					<div class="tab-content tab-assortment">
						<?foreach($arParams['PROPS']['UF_ASSORTMENT']['VALUES'] AS $sid => $sval ):?>
						<div class="filterCheckboxField webFormItemField ">
							<input type="checkbox" class="" id="s_assortiment_<?=$sid ?>" name="assortiment[]" value="<?=$sid?>" <?=(in_array($sid, $_REQUEST['assortiment']) ? 'checked="checked"' : '')?>>
							 <label for="s_assortiment_<?=$sid ?>"><?=$sval; ?></label>
						</div>
						<?endforeach;?>

						<div class="filterSubmit">
							<div class="btn-simple btn-micro salon_filter_submit">Показать</div>

							<div class="btn-simple btn-black btn-micro salon_filter_clear">Очистить</div>
						</div>
					</div>
				</div>

			        <input type="hidden" name="cur_city" value="<?=$cur_city;?>" id="cur_city" />
			        <input type="hidden" name="action" value="show" />
				</form>
			</div>
		</div>
		<div class="salons-menu-filtr-but"><span>Подбор салона по услугам и ассортименту</span><span class="filtr-but-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 39 39"><defs><style>.fbia{fill:none;stroke:#9e9e9d;stroke-miterlimit:10;stroke-width:2px;}.fbib{fill:#9e9e9d;}</style></defs><title></title><circle class="fbia" cx="19.5" cy="19.5" r="18.5"/><rect class="fbib" x="649.09" y="412.18" width="4" height="12" transform="translate(-740.42 183.54) rotate(-45)"/><rect class="fbib" x="656.39" y="412.62" width="4" height="13" transform="translate(-146.22 -742.12) rotate(45)"/></svg></span></div>
	</div>



	<div class="salons-content">
		<div class="salons-tabs-content">

		<!-- Карта-->
		<?if (!empty($arResult['arPlacemarks'])):?>
			<div class="salons-tab-content tab-map <? if (!isset($_REQUEST['s_show']) || $_REQUEST['s_show'] == 'map') {?>active<?} ?>" data-id="map">
				<div id="map-stores" class="map-container"></div>
			</div>
		<?endif;?>
			<!--Метро-->
			<?if ($arResult['HAS_METRO'] == true) { ?>
			<div class="salons-tab-content  tab-metro  <?if ($_REQUEST['s_show'] == 'metro') { ?>active<?} ?>" data-id="metro">
				<div id="metro-stores" class="map-container"></div>
			</div>
			<?} ?>
			<!-- Список -->
			<div class="salons-tab-content  tab-list  <? if ($_REQUEST['s_show'] == 'list' || empty($arResult['arPlacemarks'])) { ?>active<?} ?>"  data-id="list">
				<div class="storeListHead flex-salons">
					<div class="storeListHeadCol salons-name flex-salons-item">Название и адрес</div>
					<?if ($arResult['HAS_METRO'] == true) { ?>
					<div class="storeListHeadCol salons-metro flex-salons-item">Станция метро</div>
					<?}?>
					<div class="storeListHeadCol salons-shedule flex-salons-item">Режим работы</div>
					<div class="storeListHeadCol salons-phone flex-salons-item">Номер телефона</div>
				</div>
				<div id="storesList">
					<?
					if (!empty($arResult["STORES"])):?>

					<?
				foreach ($arResult["STORES"] as $ins => $arNextStore) :	 ?>
					 <div class="flex-salons">
						<div class="flex-salons-item salons-name">
							<a href="<?=$arNextStore["DETAIL_PAGE_URL"] ?>" class="theme-link-dashed"><?=(str_replace("м. ","м.&nbsp;", $arNextStore["TITLE"]))?></a>
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
							<a href="tel:<?=$arNextStore["PHONE"] ?>"><span class="storesListItemLabel"><?=$arNextStore["PHONE"] ?></span></a>
						</div>

					</div>
					<?endforeach; ?>
					<?else:?>
					<h3 style="text-align:center;" class="ff-medium">К сожалению, подходящие салоны не найдены. Попробуйте изменить условия поиска.</h3>
					<?endif;?>
				</div>
			</div>
		</div>
	</div>
</div>








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
    <? endif; ?>
</script>



<script>

window.metroServices = [];
mediMetroStationSalon = [];

ymaps.ready(initMetro);

    var mediMetroMap;
	var mediMetroObjectManager;

	function initMetro() {
	    if (window.pos === undefined) {
	        window.pos = {
	            map_x: '59.112935',
	            map_y: '83.361806',
	            map_scale: '4',
	            min_scale: '4'
	        };
	    }
	    mediMetroMap = new ymaps.Map("metro-stores", {
	        center: [window.pos.map_x, window.pos.map_y],
	        zoom: window.pos.map_scale,
	        controls: ['geolocationControl', 'fullscreenControl', 'zoomControl', 'searchControl', 'routeButtonControl']
	    });

	    if (window.pos.min_scale === undefined) {
	        window.pos.min_scale = '8';
	    }
	    mediMetroMap.options.set({
	        minZoom: window.pos.min_scale,
	        suppressMapOpenBlock: true,
	    });
	}

ymaps.ready(function() {
	// Рисуем ветки метро
	<?if (!empty($arResult['LINES'])):
		$j = 0;
		foreach($arResult['LINES'] AS $k => $arLine):
	    ?>
			var mediMetroLines<?=$arLine['ID']?> = new ymaps.Polyline(
		    [
		        [<?$count_stations = count($arLine['STATIONS']);
		        $sj = 0;
		        foreach($arLine['STATIONS'] as $i => $arStation){
		            echo $arStation['PROPERTY_X_POSITION_VALUE'].", ".$arStation['PROPERTY_Y_POSITION_VALUE'];
		            if ($count_stations > $sj+1) echo "], [";
		         $sj++;
					 }?><?if ($arLine['ID'] == 277 ){?>], [55.729219, 37.61132<?}?>]
		    ], {
		        balloonContent: " <?=$arLine['NAME']?>"
		    }, {
		        strokeColor: ["<?=$arLine['UF_COLOR']?>"],
		        strokeWidth: [7],
		        strokeOpacity: 0.4
		    }
	);
	// Добавляем на карту
	mediMetroMap.geoObjects.add(mediMetroLines<?=$arLine['ID']?>);
		<?
		// Отмечаем станции метро на ветках
		foreach($arLine['STATIONS'] as $i => $arStation){

			$sBalloonContent = '';
			// Если рядом есть салон, выводим метку
			if (!empty($arResult['SALONS'][$arStation['ID']])):

				$salon = $arResult['SALONS'][$arStation['ID']];


				$sBalloonContent = '<div class="map-balloon">';

	            if ($salon['METRO_NAME'] && !empty($salon['METRO_SRC'])) {

						$sBalloonContent .= '<div class="map-balloon-metro map-balloon-tr"><img src="' . $salon['METRO_SRC']. '" title="'.$salon['METRO_NAME'].'"><span>' . $salon['METRO_NAME'] . '</span></div> ';


	            }
	            $sBalloonContent .= '<div class="map-balloon-address  map-balloon-tr"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 729.33 729.33"><defs><style>.a{fill:none;stroke:#9e9e9d;stroke-miterlimit:10;stroke-width:25px;}.b{fill:#9e9e9d;}</style></defs><title>адрес место</title><circle class="a" cx="364.66" cy="364.66" r="352.16"/><path class="b" d="M740.19,227.73l-.38-.1c-100.78,0-182.49,81.7-182.49,182.48a182.1,182.1,0,0,0,53.39,129L739.89,668.27l.22.1L869.29,539.2a182.14,182.14,0,0,0,53.39-129C922.68,309.44,841,227.73,740.19,227.73Zm-2.82,288.83a105,105,0,1,1,105-105A105,105,0,0,1,737.37,516.56Z" transform="translate(-375.34 -83.34)"/></svg><span>' .$salon['ADDRESS'].'</span></div>';

	            if (!empty($salon['HOLIDAY_SHEDULE'])):
	                $sBalloonContent.= '<div class="map-balloon-shedule  map-balloon-tr"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 39 39"><defs><style>.a{fill:none;stroke:#e20074;stroke-miterlimit:10;stroke-width:2px;}.b{fill:#e20074;}</style></defs><title>режим работы</title><circle class="a" cx="19.5" cy="19.5" r="18.5"/><rect class="b" x="17.5" y="7.67" width="4" height="12"/><rect class="b" x="657.42" y="412.33" width="4" height="13" transform="translate(442.83 -639.92) rotate(90)"/></svg><div class="shedule-block"><div class="shedule-str" ><a href="' . $salon['DETAIL_PAGE_URL'] . '" class="theme-link-dashed">'.$salon['HOLIDAY_SHEDULE']['NAME'].'</a></span></div></div>';
	            elseif(!empty($salon["SCHEDULE"])):
	                $sBalloonContent.= '<div class="map-balloon-shedule  map-balloon-tr"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 39 39"><defs><style>.a{fill:none;stroke:#9e9e9d;stroke-miterlimit:10;stroke-width:2px;}.b{fill:#9e9e9d;}</style></defs><title>режим работы</title><circle class="a" cx="19.5" cy="19.5" r="18.5"/><rect class="b" x="17.5" y="7.67" width="4" height="12"/><rect class="b" x="657.42" y="412.33" width="4" height="13" transform="translate(442.83 -639.92) rotate(90)"/></svg><div class="shedule-block">';
					if (!empty($salon['WORK_STR'])){
						$sBalloonContent .='<div  class="'.($salon['WORKING'] == "0" ? 'not-':'').'working">'.$salon['WORK_STR'].'</div><br>';
					}
					$sBalloonContent .='<div class="shedule-str">'.$salon['SCHEDULE'].'</span></div></div>';
	            endif;

	            $sBalloonContent .= '<div class="map-balloon-footer "><a href="' . $salon['DETAIL_PAGE_URL'] . '" class="btn-simple btn-micro">Подробнее</a></div>';
				?>

				 mediMetroStationSalon[<?=$arStation['ID']?>] = new ymaps.Placemark(
				    [<?=$arStation['PROPERTY_X_POSITION_VALUE'].", ".$arStation['PROPERTY_Y_POSITION_VALUE']?>],
				    {
				        <?//id: <?= $arResult['SALONS'][$arStation['ID']]['ID']? >,?>
				        hintContent: '<?=$arStation['NAME'];?>',
				        balloonContentBody: '<?= $sBalloonContent?>',


				    }, {
				        iconLayout: 'default#image',
				        iconImageHref: '//<?=$_SERVER['HTTP_HOST'].SITE_TEMPLATE_PATH?>/images/metromarker.png',
				        iconImageSize: [20, 20],
				        iconImageOffset: [-10, -10],
				        zIndex: 100
				    });

    			mediMetroMap.geoObjects.add(mediMetroStationSalon[<?=$arStation['ID']?>]);
    		<?endif?>

    // станция
    var mediMetroStation<?=$arStation['ID']?> = new ymaps.Placemark(
    [<?=$arStation['PROPERTY_X_POSITION_VALUE'].", ".$arStation['PROPERTY_Y_POSITION_VALUE']?>],
    {
        hintContent: '<?=$arStation['NAME'];?>',
        balloonContent: '<?=$arStation['NAME'];?>'
    }, {
        iconLayout: 'default#image',
        iconImageHref: '//<?=$_SERVER['HTTP_HOST'].$arLine['IMG']['SRC']?>',
        iconImageSize: [12, 12],
        iconImageOffset: [-6, -6],
        zIndex: 10,
    });

        mediMetroMap.geoObjects.add(mediMetroStation<?=$arStation['ID']?>);
    <?}?>

    <?$j++;?>
    <?endforeach;?>
    <?endif;?>
});

</script>
<script>

fbq('track', 'ViewContent', {content_name: "Salons"});
var _gcTracker=_gcTracker||[];
_gcTracker.push(['view_page', { name: 'Salons'}]);
</script>

<script type="text/javascript"> var _tmr = window._tmr || (window._tmr = []); _tmr.push({ type: "reachGoal", id: 3206755, goal: "GOAL_SALONS"});</script>