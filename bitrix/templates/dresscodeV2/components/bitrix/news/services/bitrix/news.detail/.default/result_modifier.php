<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


$arResult['DETAIL_TEXT'] =  str_replace("#PHONE#", $GLOBALS['medi']['phones'][SITE_ID], $arResult['DETAIL_TEXT']);
$arResult['PREVIEW_TEXT'] =  str_replace("#PHONE#", $GLOBALS['medi']['phones'][SITE_ID], $arResult['PREVIEW_TEXT']);

$arResult['FORM_ID'] = '';
if ($arResult['PROPERTIES']['WEBFORM_LIST']['VALUE'] != "" && $arResult['PROPERTIES']['WEBFORM_LIST']['VALUE_SORT'] > 0):
    if (CModule::IncludeModule("form")):
        $arFilter = Array(
            "ID"       => $arResult['PROPERTIES']['WEBFORM_LIST']['VALUE_SORT'],
            "SITE"      => SITE_ID
        );

        // получим список всех форм, для которых у текущего пользователя есть право на заполнение
        $rsForms = CForm::GetList($by="s_id", $order="desc", $arFilter, $is_filtered);
        if   ($arForm = $rsForms->Fetch())
        {

            $arResult['FORM_ID']  = $arForm['ID'];
        }
endif;
endif;


if (strpos($arResult['DETAIL_TEXT'], "#WEBFORM") > 0)
{
    $arResult['FORM_IN_TEXT'] = 1;
    $replace = preg_match_all("/(#WEBFORM#)/U", $arResult['DETAIL_TEXT'], $matches);
    if (!empty($matches[1][0])){


            //buffer
            ob_start();

			if ($arResult['PROPERTIES']['WEBFORM_LIST']['VALUE'] != "" && $arResult['PROPERTIES']['WEBFORM_LIST']['VALUE_SORT'] > 0 && $arResult['FORM_ID'] > 0):?>
				<div class="serviceWebForm">
					<?
					// Форма бронирования товара в салоне
					$APPLICATION->IncludeComponent(
						"bitrix:form.result.new",
						 $arResult['PROPERTIES']['WEBFORM_LIST']['VALUE_XML_ID'],
						array(
							"AJAX_MODE" => "N",
							"AJAX_OPTION_ADDITIONAL" => "",
							"AJAX_OPTION_HISTORY" => "N",
							"AJAX_OPTION_JUMP" => "N",
							"AJAX_OPTION_STYLE" => "Y",
							"CACHE_TIME" => "3600",
							"CACHE_TYPE" => "N",
							"CHAIN_ITEM_LINK" => "",
							"CHAIN_ITEM_TEXT" => "",
							"COMPOSITE_FRAME_MODE" => "A",
							"COMPOSITE_FRAME_TYPE" => "AUTO",
							"EDIT_ADDITIONAL" => "N",
							"EDIT_STATUS" => "N",
							"IGNORE_CUSTOM_TEMPLATE" => "N",
							"NOT_SHOW_FILTER" => array(
								0 => "",
								1 => "",
							),
							"NOT_SHOW_TABLE" => array(
								0 => "",
								1 => "",
							),
							"RESULT_ID" => $_REQUEST[RESULT_ID],
							"SEF_MODE" => "N",
							"SHOW_ADDITIONAL" => "N",
							"SHOW_ANSWER_VALUE" => "Y",
							"SHOW_EDIT_PAGE" => "N",
							"SHOW_LIST_PAGE" => "N",
							"SHOW_STATUS" => "N",
							"SHOW_VIEW_PAGE" => "N",
							"START_PAGE" => "new",
							"SUCCESS_URL" => "",
							"HIDDEN_FIELDS" => array(
								0 => "AGREE",
							),
							"USE_EXTENDED_ERRORS" => "Y",
							"WEB_FORM_ID" => $arResult['PROPERTIES']['WEBFORM_LIST']['VALUE_SORT'],
							"COMPONENT_TEMPLATE" => $arResult['PROPERTIES']['WEBFORM_LIST']['VALUE_XML_ID'],
							"LIST_URL" => "",
							"EDIT_URL" => "",
							"VARIABLE_ALIASES" => array(
								"WEB_FORM_ID" => "WEB_FORM_ID",
								"RESULT_ID" => "RESULT_ID",
							)
						),
						false
					); ?>
				</div>
			<?endif;

            $componentData = ob_get_contents();
            ob_end_clean();

            $arResult['DETAIL_TEXT'] = str_replace($matches[0][0], $componentData, $arResult['DETAIL_TEXT']);


    }
}


if (strpos($arResult['DETAIL_TEXT'], "#MAPSERVICE") > 0 )
{
    $arResult['MAP_IN_TEXT'] = 1;
    $replace = preg_match_all("/(#MAPSERVICE\[(.*)\]#)/U", $arResult['DETAIL_TEXT'], $matches);
    $servicesid = explode(',',$matches[2][0]);
    if (!empty($matches[1][0]) && !empty($servicesid)){



        $sFilter  = array(
        	"ACTIVE" =>"Y",
        	"ISSUING_CENTER" => "Y",
        	"UF_SALON"=>true,
        	"UF_SERVICES" => $servicesid
        );


        $def_city  = SITE_ID == 's0' ? $GLOBALS['medi']['site_order']['s1'] : $GLOBALS['medi']['site_order'][SITE_ID];
        $sFilter['UF_CITY'] = $def_city;

        $resStore = CCatalogStore::GetList(array("SORT"=>"ASC"), $sFilter, false, false, array("ID", "ADDRESS", "SITE_ID", "GPS_N", "GPS_S", "TITLE", "CODE", "SCHEDULE",  "UF_*"));
        while($sklad = $resStore->Fetch())
        {
        	$sklad['ADDRESS'] = preg_replace("/[0-9]{6},/", "", $sklad["ADDRESS"]);
        	$metro = unserialize($sklad['UF_METRO']);
        	if (!empty($metro[0]))
        	{
        			$rsElm = CIBlockElement::GetList(array(), array("ID" => $metro[0], "IBLOCK_ID" => "23", "ACTIVE"=>"Y"), false, false, array("ID", "NAME", "IBLOCK_SECTION_ID"));
        			if ($arMetro = $rsElm -> GetNext()) {

        				$rsSect = CIBlockSection::GetList(array("NAME"=>"ASC"), array( "IBLOCK_ID" => "23", "ACTIVE"=>"Y", "ID"=> $arMetro['IBLOCK_SECTION_ID']), false, array("NAME", "PICTURE", "IBLOCK_SECTION_ID" ));
        				if ($arSect = $rsSect->GetNext()) {
        					if ($arSect['PICTURE'] > 0) {
        						$arSect['ICON'] = CFile::GetFileArray($arSect["PICTURE"]);
        					}
        					$arMetro['SECTION'] = $arSect;
        				}
        				  $sklad['METRO'] = $arMetro;
        			}

        	}

        	$arStores[] = $sklad;
        }
                    //buffer
                    ob_start();
        if (!empty($arStores))
        {
            $arPlacemarks = array();
            $showPictures = 0;
            $gpsN = '';
            $gpsS = '';

            $def_city  = $GLOBALS['medi']['region_cities'][$arStores[0]['SITE_ID']];


            $day_of_week = [1 => 'понедельник', 2=>'вторник', 3=>'среду', 4=>'четверг', 5=>'пятницу', 6=>'субботу', 7=>'воскресенье'];
            $sday_of_week = [1 => 'MON', 2=>'TUE', 3=>'WED', 4=>'THU', 5=>'FRI', 6=>'SAT', 7=>'SUN'];

            foreach ($arStores as &$arProperty):
                    $today = date("N");

                    $weekday = strtoupper(date("D"));

                $now_hour = date("H");
                $PROP_DAY = 'UF_RR_'.$weekday;
                $WORK_TIME = explode("-",$arProperty[$PROP_DAY]);
                $WORK_STR = '';
                $WORKING = "0";
                if (!empty($WORK_TIME[0]))
                {
                    $work_hour_start = explode(":", $WORK_TIME[0]);
                    $work_hour_end = explode(":", $WORK_TIME[1]);
                    // еще не открылся
                    if ($now_hour < $work_hour_start[0])
                    {
                        $WORKING = "0";
                        $WORK_STR = 'Салон откроется в '.$WORK_TIME[0];
                    }
                    // сейчас рабочее время
                    elseif ($work_hour_start[0] <= $now_hour && $work_hour_end[0] > $now_hour ) {

                        $WORKING = "1";
                        $WORK_STR = 'Салон открыт';
                    }
                    // уже закрылся сегодня, ищем следующий
                    elseif ($work_hour_end[0] < $now_hour){

                        $sweekday = array_search($weekday, $sday_of_week);

                        $find_day = false;
                        $i = $sweekday;
                        $cnt = 1;
                        while ($find_day == false)
                        {
                            $sweekday++;
                            if ($sweekday > 7) $sweekday = 1;

                            $PROP_DAY = 'UF_RR_'.$sday_of_week[$sweekday];
                            $WORK_TIME = explode("-", $arProperty[$PROP_DAY]);

                            if (!empty($WORK_TIME[0]))
                            {
                                $work_hour_start = explode(":", $WORK_TIME[0]);
                                $work_hour_end = explode(":", $WORK_TIME[1]);

                                // еще не открылся
                                if ($work_hour_start[0] > 0)
                                {
                                    $find_day = true;
                                    $when = '';
                                    if ($cnt == "1") $when = 'завтра';
                                    else $when = 'в'.($sweekday==2? "о":"").' '.$day_of_week[$sweekday];
                                    $WORK_STR = 'Салон откроется '.$when.' в '.$WORK_TIME[0];

                                    $WORKING = "0";
                                    break;
                                }
                            }
                            $cnt++;
                            if ($cnt > 7) break;
                        }
                    }
                }
                else{
                    $sweekday = array_search($weekday, $sday_of_week);

                    $find_day = false;
                    $i = $sweekday;
                    $cnt = 1;
                    while ($find_day == false)
                    {
                        $sweekday++;
                        if ($sweekday > 7) $sweekday = 1;

                        $PROP_DAY = 'UF_RR_'.$sday_of_week[$sweekday];
                        $WORK_TIME = explode("-", $arProperty[$PROP_DAY]);

                        if (!empty($WORK_TIME[0]))
                        {
                            $work_hour_start = explode(":", $WORK_TIME[0]);
                            $work_hour_end = explode(":", $WORK_TIME[1]);

                            // еще не открылся
                            if ($work_hour_start[0] > 0)
                            {
                                $find_day = true;
                                $when = '';
                                if ($cnt == "1") $when = 'завтра';
                                else $when = 'в'.($sweekday==2? "о":"").' '.$day_of_week[$sweekday];
                                $WORK_STR = 'Салон откроется '.$when.' в '.$WORK_TIME[0];
                                $WORKING = "0";

                                break;
                            }
                        }
                        $cnt++;
                        if ($cnt > 7) break;
                    }
                }
                if (!empty($WORK_STR)) $arProperty['WORK_STR'] = $WORK_STR;
                $arProperty['WORKING'] = $WORKING;

                // График работы в праздники
                $obElement = CIBlockElement::GetList([], ["IBLOCK_ID" => 24, "PROPERTY_STORE"=>$arProperty['ID'], 'ACTIVE' => 'Y', 'ACTIVE_DATE'=>'Y'], false, false, ["NAME", "PREVIEW_TEXT", "ACTIVE_TO"]);
                if ($arShedule = $obElement->GetNext())
                {
                    $arProperty['HOLIDAY_SHEDULE'] = $arShedule;
                }

                if ($arProperty["GPS_S"] != 0 && $arProperty["GPS_N"] != 0) {
                    $sumS +=  $arProperty["GPS_S"];
                    $sumN +=  $arProperty["GPS_N"];
                    $gpsN = substr(doubleval($arProperty["GPS_N"]), 0, 15);
                    $gpsS = substr(doubleval($arProperty["GPS_S"]), 0, 15);

                    $arPlacemarks[] = array("LON" => $gpsS,"LAT" => $gpsN,"TEXT" => $arProperty["TITLE"]);

                    $pfx= '';
                    if ($GLOBALS['medi']['sfolder'][$arProperty['SITE_ID']] != '')
                    {
                        $pfx = "/".$GLOBALS['medi']['sfolder'][$arProperty['SITE_ID']] ;
                    }
                    $arProperty['DETAIL_PAGE_URL']  = $pfx.'/salons/'.$arProperty['CODE'].'/';



                    $sBalloonContent = '<div class="map-balloon">';


                    if ($arProperty['METRO']['NAME'] && !empty($arProperty['METRO']['SECTION']['ICON']['SRC'])) {
                        $sBalloonContent .= '<div class="map-balloon-metro map-balloon-tr"><img src="' . $arProperty['METRO']['SECTION']['ICON']['SRC']. '" title="'.$arProperty['METRO']['SECTION']['NAME'].'"><span>' . $arProperty['METRO']['NAME'] . '</span></div> ';
                    }
                    $sBalloonContent .= '<div class="map-balloon-address  map-balloon-tr"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 729.33 729.33"><defs><style>.a{fill:none;stroke:#9e9e9d;stroke-miterlimit:10;stroke-width:25px;}.b{fill:#9e9e9d;}</style></defs><title>адрес место</title><circle class="a" cx="364.66" cy="364.66" r="352.16"/><path class="b" d="M740.19,227.73l-.38-.1c-100.78,0-182.49,81.7-182.49,182.48a182.1,182.1,0,0,0,53.39,129L739.89,668.27l.22.1L869.29,539.2a182.14,182.14,0,0,0,53.39-129C922.68,309.44,841,227.73,740.19,227.73Zm-2.82,288.83a105,105,0,1,1,105-105A105,105,0,0,1,737.37,516.56Z" transform="translate(-375.34 -83.34)"/></svg><span>' .$arProperty['ADDRESS'].'</span></div>';

                    if (!empty($arProperty['HOLIDAY_SHEDULE'])):
                        $sBalloonContent.= '<div class="map-balloon-shedule  map-balloon-tr"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 39 39"><defs><style>.a{fill:none;stroke:#e20074;stroke-miterlimit:10;stroke-width:2px;}.b{fill:#e20074;}</style></defs><title>режим работы</title><circle class="a" cx="19.5" cy="19.5" r="18.5"/><rect class="b" x="17.5" y="7.67" width="4" height="12"/><rect class="b" x="657.42" y="412.33" width="4" height="13" transform="translate(442.83 -639.92) rotate(90)"/></svg><div class="shedule-block"><div class="shedule-str" ><a href="' . $arProperty['DETAIL_PAGE_URL'] . '" class="theme-link-dashed">'.$arProperty['HOLIDAY_SHEDULE']['NAME'].'</a></span></div></div>';
                    elseif(!empty($arProperty["SCHEDULE"])):
                        $sBalloonContent.= '<div class="map-balloon-shedule  map-balloon-tr"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 39 39"><defs><style>.a{fill:none;stroke:#9e9e9d;stroke-miterlimit:10;stroke-width:2px;}.b{fill:#9e9e9d;}</style></defs><title>режим работы</title><circle class="a" cx="19.5" cy="19.5" r="18.5"/><rect class="b" x="17.5" y="7.67" width="4" height="12"/><rect class="b" x="657.42" y="412.33" width="4" height="13" transform="translate(442.83 -639.92) rotate(90)"/></svg><div class="shedule-block">';
                        if (!empty($WORK_STR)){
                            $sBalloonContent.= '<div  class="'.($WORKING == "0" ? 'not-':'').'working">'.$WORK_STR.'</div><br>';
                        }
                        $sBalloonContent.= '<div class="shedule-str" data-start="" data-end="">'.$arProperty['SCHEDULE'].'</span></div></div>';
                    endif;

                    $sBalloonContent .=  '<div class="map-balloon-footer "><a href="#" class="btn-simple btn-micro select_button" data-id="'.$arProperty['METRO']['NAME'].'" onclick="$(\'#select_salon\').val(\''.$arProperty['METRO']['NAME'].'\');$.scrollTo(\'#webForm-title\', {margin:true,over:{ top:-0.5}, duration: \'easing\'});return false;">Записаться</a></div>';


                    $arYandexFeatures[] = array(
                        'type' => 'Feature',
                        'id' => $arProperty['ID'],
                        'geometry' => array(
                            'type' => 'Point',
                            'coordinates' => array( $arProperty["GPS_N"], $arProperty['GPS_S'])
                    ),
                    'properties' => array(
                        'hintContent' => $arProperty['TITLE'],
                        'balloonContentBody' => $sBalloonContent,
                    )
                    );



                    $count_stores++;
            }
            endforeach;
            $avgS = $sumS/$count_stores;
            $avgN = $sumN/$count_stores;
            // Начальные координаты для карты

            $arResult['COORD'] = array($avgN, $avgS);

            $arResult['arYandexFeatures'] = $arYandexFeatures;
            $arResult['arPlacemarks'] = $arPlacemarks;

        }
 ?>

				<div class="serviceMap" id="map-stores">
					<?
					// Карта салонов с фильтром по услуге
					?>
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
                    <?else:?>
                    window.pos = {
                        map_x: '55.7410',
                        map_y: '38.04495',
                        map_scale: '8',
                        min_scale: '8'
                    };
                    <? endif; ?>

                    // Яндекс карта

                    ymaps.ready(init);
                    var mediMap;
                    var mediObjectManager;
                    function init()
                    {
                        if (window.pos === undefined) {
                            window.pos = {
                                map_x: '55.7410',
                                map_y: '38.04495',
                                map_scale: '8',
                                min_scale: '8'
                            };
                        }
                        mediMap = new ymaps.Map("map-stores", {
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

                        mediObjectManager.objects.events.add('click', function (e) {
                            var objectId = e.get('objectId');
                            $(".select_button").on("click", function(){
                                $sid = $(this).data("id");
                                $("#select_salon").val($sid);
                            });
                            }
                        );
                        $(".select_button").on("click", function(){
                            $sid = $(this).data("id");
                            $("#select_salon").val($sid);
                            $.scrollTo('#select_salon', {margin:true,over:{ top:-0.5}, duration: "easing"});

                        });
                        if (window.pos.features !== undefined) {
                            mediObjectManager.add({
                                type: 'FeatureCollection',
                                features: window.pos.features
                            });

                        }

                        sid= $("#select_salon option:selected").data("id");
                        mediObjectManager.objects.balloon.open(sid);

                        if ($("#select_salon").length){

                            $("#select_salon").on("change", function(){
                                sid = $("#select_salon option:selected").data("id");
                                mediObjectManager.objects.balloon.open(sid);
                                $(".select_button").off("click").on("click", function(){
                                    $sid = $(this).data("id");
                                    $("#select_salon").val($sid);
                                   // $.scrollTo('#select_salon', {margin:true,over:{ top:-0.5}, duration: "easing"});
                                });
                            });

                            $("#salon-adres").on("click", function(){

                                $.scrollTo('#map-stores', {margin:true,over:{ top:-0.5}, duration: "easing"});
                            });
                        }
                    }
                </script>

			<?

            $componentData = ob_get_contents();
            ob_end_clean();

            $arResult['DETAIL_TEXT'] = str_replace($matches[0][0], $componentData, $arResult['DETAIL_TEXT']);



    }
}




$this->__component->SetResultCacheKeys(array(
    "NAME",
    "PREVIEW_TEXT",
    "PREVIEW_PICTURE",
    "DETAIL_PICTURE",
    "DETAIL_PAGE_URL",
    "SITE_ID",
    "FORM_ID",
    "PROPERTIES",
    "RESIZE_DETAIL_PICTURE",
    "RESIZE_BANNER_PICTURE"
));?>
