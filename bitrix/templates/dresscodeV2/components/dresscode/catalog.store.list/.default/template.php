<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(strlen($arResult["ERROR_MESSAGE"]) > 0)
	ShowError($arResult["ERROR_MESSAGE"]);

?>
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

<div class="tabs-wrap salon_tabs">
    <div class="tabs-links">
		<?if (!empty($arResult['arPlacemarks'])):?>
        <div class="tab-link tab-btn-link tab-map <?if (!isset($_REQUEST['s_show']) || $_REQUEST['s_show'] == 'map'){?>active<?}?>" data-id="map">Карта</div>
		<?endif;?>
        <?if ($arResult['HAS_METRO'] == true){?>
        <div class="tab-link tab-btn-link tab-metro  <?if ($_REQUEST['s_show'] == 'metro') { ?>active<?} ?>" data-id="metro">Метро</div>
        <?}?>
        <div class="tab-link tab-btn-link tab-list <?if ($_REQUEST['s_show'] == 'list' || empty($arResult['arPlacemarks'])) { ?>active<?} ?>" data-id="list">Список</div>
    </div>
    <div class="tabs-content">

    <!-- Карта-->
	<?if (!empty($arResult['arPlacemarks'])):?>
        <div class="tab-content tab-map active_tab <? if (!isset($_REQUEST['s_show']) || $_REQUEST['s_show'] == 'map') {?>active<?} ?>">
            <div id="map-stores" class="map-container"></div>

        </div><?endif;?>
        <!--Метро-->
        <?if ($arResult['HAS_METRO'] == true) { ?>
        <div class="tab-content  tab-metro  <?if ($_REQUEST['s_show'] == 'metro') { ?>active<?} ?>">
            <div id="metro-stores" class="map-container"></div>
        </div>
        <?} ?>
        <!-- Список -->
        <div class="tab-content  tab-list  <? if ($_REQUEST['s_show'] == 'list' || empty($arResult['arPlacemarks'])) { ?>active<?} ?>">
            <div id="storesList">
                <?
				if (!empty($arResult["STORES"])):
            foreach ($arResult["STORES"] as $ins => $arNextStore) :
                 ?>
                <div class="storesListItem">
                    <div class="storesListItemLeft">
                        <div class="storesListItemContainer">
                            <?
                            if (!empty($arNextStore["PICTURE"])) {
                                $arNextStoreImage = $arNextStore["PICTURE"];
								            } else {
								                $arNextStoreImage["SRC"] = $templateFolder."/images/empty.png";
								            }
								            ?>

                            <div class="storesListItemPicture">
                                <a href="<?=$arNextStore["DETAIL_PAGE_URL"] ?>" class="storesListTableLink"><img src="<?=$arNextStoreImage["SRC"] ?>" alt="<?=$arNextStore["TITLE"] ?>" title="<?=$arNextStore["TITLE"] ?>"></a>
                            </div>
                            <div class="storesListItemName">
                                <a href="<?=$arNextStore["DETAIL_PAGE_URL"] ?>" class="storesListTableLink theme-link-dashed"><?=(str_replace("м. ","м.&nbsp;", $arNextStore["TITLE"]))?></a>
                                <p><?=$arNextStore["ADDRESS"] ?></p>
                                <?
                                if (!empty($arNextStore["DESCRIPTION"])) : ?>
                                    <p class="storeItemDescription"><?=$arNextStore["DESCRIPTION"] ?></p>
                                <?endif; ?>
								<?if (!empty($arNextStore['METRO'])):?>
	                                <span class="storesListItemLabel  storesListTableMetro">м.&nbsp;<?=$arNextStore['METRO'][0]["NAME"] ?></span>

								<?endif;?>
                                <div class="storesListItemScheduleSmall">
                                    <img src="<?=$templateFolder."/images/timeSmall.png"; ?>" alt="<?=$arNextStore["SCHEDULE"] ?>" title="<?=$arNextStore["SCHEDULE"] ?>" class="storeListIconSmall">
                                    <span class="storesListItemScheduleLabel"><?=$arNextStore["SCHEDULE"] ?></span>
									<?if (!empty($arNextStore['HOLIDAY_SHEDULE'])):?>
									<br><a href="<?=$arNextStore["DETAIL_PAGE_URL"] ?>" class="theme-link-dashed">Внимание! Изменения в графике.</a>
									<?endif;?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="storesListItemRight">
                        <div class="storesListItemContainer">
                            <div class="storesListItemSchedule">
                                <img src="<?=$templateFolder."/images/time.png"; ?>" alt="<?=$arNextStore["SCHEDULE"] ?>" title="<?=$arNextStore["SCHEDULE"] ?>" class="storeListIcon">
                                <span class="storesListItemLabel"><?=$arNextStore["SCHEDULE"] ?></span>
								<?if (!empty($arNextStore['HOLIDAY_SHEDULE'])):?>
								<br><a href="<?=$arNextStore["DETAIL_PAGE_URL"] ?>" class="theme-link-dashed">Внимание! Изменения в графике.</a>
								<?endif;?>
                            </div>
                            <div class="storesListItemPhone">
                                <span class="storesListItemPhoneLabel"><?=GetMessage("STORES_LIST_TELEPHONE") ?></span>
                                <img src="<?=$templateFolder."/images/phone.png"; ?>" alt="<?=$arNextStore["PHONE"] ?>" title="<?=$arNextStore["PHONE"] ?>" class="storeListIcon">
                                <a href="tel:<?=$arNextStore["PHONE"] ?>"><span class="storesListItemLabel"><?=$arNextStore["PHONE"] ?></span></a>
                            </div>

                        </div>
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
	        controls: ['geolocationControl', 'fullscreenControl', 'zoomControl']
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
		        strokeColor: ["#333333","<?=$arLine['UF_COLOR']?>"],
		        strokeWidth: [7,6],
		        strokeOpacity: 0.7
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

			    $sBalloonContent = '<div class="balloon">';
				$salon = $arResult['SALONS'][$arStation['ID']];

			    $sBalloonContent .=  '<div class="salon">';

		        if ($salon['PREVIEW_PICTURE']) {
		            $sBalloonContent .= '<div class="preview_photo"><img src="' . $salon['PREVIEW_PICTURE']['SRC'] . '" alt=""></div>';
		        }

		        $sBalloonContent .= '<div class="text">';

		        if ($salon['METRO_NAME']) {
		            $sBalloonContent .= '<div class="metro"><img src="//' .$_SERVER['HTTP_HOST'].$arLine['IMG']['SRC'] . '" alt="">' . $salon['METRO_NAME'] . '</div> ';
		        }
		        $sBalloonContent .= $salon['DESCRIPTION'];
		        $sBalloonContent .= $salon['ADDRESS'];

		        $sBalloonContent .= '</div><div class="clear"></div></div>';
		        $sBalloonContent .= '<div class="bmore"><a href="'.$salon['DETAIL_PAGE_URL'].'" class="btn-simple btn-micro">Подробнее</a></div>';


				$sBalloonContent .= '</div>';
				?>

				 mediMetroStationSalon[<?=$arStation['ID']?>] = new ymaps.Placemark(
				    [<?=$arStation['PROPERTY_X_POSITION_VALUE'].", ".$arStation['PROPERTY_Y_POSITION_VALUE']?>],
				    {
				        <?//id: <?= $arResult['SALONS'][$arStation['ID']]['ID']? >,?>
				        hintContent: '<?=$arStation['NAME'];?>',
				        balloonContentHeader: '<div><a href="<?=$salon['DETAIL_PAGE_URL']?>" class="theme-link-dashed-b">' +
				        '<?=$arResult['SALONS'][$arStation['ID']]['NAME'];?>' + '</a></div>',
				        balloonContentBody: '<?= $sBalloonContent?>',
				        balloonContentFooter: '<div class="bfooter"></div>',


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
