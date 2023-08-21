<?
$site_id = ($arResult['STORES'][0]['SITE_ID'] != '' ? $arResult['STORES'][0]['SITE_ID'] : ($arResult['STORES'][1]['SITE_ID'] != '' ? $arResult['STORES'][1]['SITE_ID'] : $arResult['STORES'][2]['SITE_ID']));
 $def_city  = $GLOBALS['medi']['region_cities'][$site_id];
$sumAmount  = 0; // общее количество товара в салонах
$mainStoreAmount = 0; // количество на складах

// наличие на основных складах
$filter = array(
	"ACTIVE" => "Y",
	"PRODUCT_ID" => $arParams["ELEMENT_ID"],
	"+SITE_ID" => SITE_ID,
	"UF_SKLAD" => true,
);
$rsProps = CCatalogStore::GetList(
	array('TITLE' => 'ASC', 'ID' => 'ASC'),
	$filter,
	false,
	false,
	["ID", "ACTIVE", "PRODUCT_AMOUNT"]
);
while ($mStore = $rsProps->GetNext())
{
	$mainStoreAmount += $mStore['PRODUCT_AMOUNT'];
}

if(!empty($arResult["STORES"])){


    $def_city  = $GLOBALS['medi']['region_cities'][$arResult['STORES'][0]['SITE_ID']];


    $day_of_week = [1 => 'понедельник', 2=>'вторник', 3=>'среду', 4=>'четверг', 5=>'пятницу', 6=>'субботу', 7=>'воскресенье'];
    $sday_of_week = [1 => 'MON', 2=>'TUE', 3=>'WED', 4=>'THU', 5=>'FRI', 6=>'SAT', 7=>'SUN'];

	$arPlacemarks = array();
	$showPictures = 0;
	$gpsN = '';
	$gpsS = '';
$count_stores = 0;
	foreach ($arResult["STORES"] as $sk => &$arStore) {

		$pfx= '';
		if ($GLOBALS['medi']['sfolder'][$arStore['SITE_ID']] != '')
		{
			$pfx = "/".$GLOBALS['medi']['sfolder'][$arStore['SITE_ID']] ;
		}
		$arStore['DETAIL_PAGE_URL']  = $pfx.'/salons/'.$arStore['CODE'].'/';

		$sumAmount += $arStore['PRODUCT_AMOUNT'];


		if($arStore["REAL_AMOUNT"] > 0 ||  ($mainStoreAmount > 0  && $arStore['UF_ESHOP_ORDERS'] == '1')){
			$arResult["SHOW_STORES"] = "Y";
			//break(1);
		}
        // Если товара нет и заказ не возможен, исключаем склад
        if ($arStore["REAL_AMOUNT"] == 0 && $arStore['UF_ESHOP_ORDERS'] == '0')
        {
            unset($arResult['STORES'][$sk]);
        }
		else {


			if ($arStore['COORDINATES']["GPS_S"] != 0 && $arStore['COORDINATES']["GPS_N"] != 0) {

				$sumS +=  $arStore['COORDINATES']["GPS_S"];
				$sumN +=  $arStore['COORDINATES']["GPS_N"];
				$gpsN = substr(doubleval($arStore['COORDINATES']["GPS_N"]), 0, 15);
				$gpsS = substr(doubleval($arStore['COORDINATES']["GPS_S"]), 0, 15);

				$arPlacemarks[] = array("LON" => $gpsS,"LAT" => $gpsN,"TEXT" => $arStore["TITLE"]);

				$pfx= '';
				if ($GLOBALS['medi']['sfolder'][$arStore['SITE_ID']] != '')
				{
					$pfx = "/".$GLOBALS['medi']['sfolder'][$arStore['SITE_ID']] ;
				}
				$arStore['DETAIL_PAGE_URL']  = $pfx.'/salons/'.$arStore['CODE'].'/';

				// Получаем пользовательские свойства складов
				if (!empty($arStore["IMAGE_ID"])) {
					$showPictures = true;
				}

                /*$metro  = unserialize($arStore['UF_METRO']);
                if (!empty($metro)) {
                    if (!empty($metro[0])) {
                        $metroSalons = [];
                        $rsElm = CIBlockElement::GetList(array(), array("ID" => $metro[0], "IBLOCK_ID" => "23", "ACTIVE"=>"Y"), false, false, array("ID", "NAME", "IBLOCK_SECTION_ID"));
                        while ($arMetro = $rsElm -> GetNext()) {

                            $rsSect = CIBlockSection::GetList(array("NAME"=>"ASC"), array( "IBLOCK_ID" => "23", "ACTIVE"=>"Y", "ID"=> $arMetro['IBLOCK_SECTION_ID']), false, array("NAME", "PICTURE", "IBLOCK_SECTION_ID" ));
                            if ($arSect = $rsSect->GetNext()) {
                                if ($arSect['PICTURE'] > 0) {
                                    $arSect['ICON'] = CFile::GetFileArray($arSect["PICTURE"]);
                                }
                                $arMetro['SECTION'] = $arSect;
                            }
                            $arStore['METRO'] = $arMetro;
                        }
                    }
                }*/

                $today = date("N");

                $weekday = strtoupper(date("D"));

                $now_hour = date("H");
                $PROP_DAY = 'UF_RR_'.$weekday;
                $WORK_TIME = explode("-",$arStore[$PROP_DAY]);
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
                            $WORK_TIME = explode("-", $arStore[$PROP_DAY]);

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
                        $WORK_TIME = explode("-", $arStore[$PROP_DAY]);

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
                if (!empty($WORK_STR)) $arStore['WORK_STR'] = $WORK_STR;
                $arStore['WORKING'] = $WORKING;

                // График работы в праздники
                $obElement = CIBlockElement::GetList([], ["IBLOCK_ID" => 24, "PROPERTY_STORE"=>$arStore['ID'], 'ACTIVE' => 'Y', 'ACTIVE_DATE'=>'Y'], false, false, ["NAME", "PREVIEW_TEXT", "ACTIVE_TO"]);
                if ($arShedule = $obElement->GetNext())
                {
                    $arStore['HOLIDAY_SHEDULE'] = $arShedule;
                }


                $sBalloonContent = '<div class="map-balloon">';

                if ($arStore['METRO'][0]['NAME'] && !empty($arStore['METRO'][0]['SECTION']['ICON']['SRC'])) {
                    $sBalloonContent .= '<div class="map-balloon-metro map-balloon-tr"><img src="' . $arStore['METRO'][0]['SECTION']['ICON']['SRC']. '" title="'.$arStore['METRO'][0]['SECTION']['NAME'].'"><span>' . $arStore['METRO'][0]['NAME'] . '</span></div> ';
                }

                $sBalloonContent .= '<div class="map-balloon-address  map-balloon-tr"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 729.33 729.33"><defs><style>.a{fill:none;stroke:#9e9e9d;stroke-miterlimit:10;stroke-width:25px;}.b{fill:#9e9e9d;}</style></defs><title>адрес место</title><circle class="a" cx="364.66" cy="364.66" r="352.16"/><path class="b" d="M740.19,227.73l-.38-.1c-100.78,0-182.49,81.7-182.49,182.48a182.1,182.1,0,0,0,53.39,129L739.89,668.27l.22.1L869.29,539.2a182.14,182.14,0,0,0,53.39-129C922.68,309.44,841,227.73,740.19,227.73Zm-2.82,288.83a105,105,0,1,1,105-105A105,105,0,0,1,737.37,516.56Z" transform="translate(-375.34 -83.34)"/></svg><span>' .$arStore['ADDRESS'].'</span></div>';

                if (!empty($arStore['HOLIDAY_SHEDULE'])):
                    $sBalloonContent.= '<div class="map-balloon-shedule  map-balloon-tr"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 39 39"><defs><style>.a{fill:none;stroke:#e20074;stroke-miterlimit:10;stroke-width:2px;}.b{fill:#e20074;}</style></defs><title>режим работы</title><circle class="a" cx="19.5" cy="19.5" r="18.5"/><rect class="b" x="17.5" y="7.67" width="4" height="12"/><rect class="b" x="657.42" y="412.33" width="4" height="13" transform="translate(442.83 -639.92) rotate(90)"/></svg><div class="shedule-block"><div class="shedule-str" ><a href="' . $arStore['DETAIL_PAGE_URL'] . '" class="theme-link-dashed">'.$arStore['HOLIDAY_SHEDULE']['NAME'].'</a></span></div></div>';
                elseif(!empty($arStore["SCHEDULE"])):
                    $sBalloonContent.= '<div class="map-balloon-shedule  map-balloon-tr"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 39 39"><defs><style>.a{fill:none;stroke:#9e9e9d;stroke-miterlimit:10;stroke-width:2px;}.b{fill:#9e9e9d;}</style></defs><title>режим работы</title><circle class="a" cx="19.5" cy="19.5" r="18.5"/><rect class="b" x="17.5" y="7.67" width="4" height="12"/><rect class="b" x="657.42" y="412.33" width="4" height="13" transform="translate(442.83 -639.92) rotate(90)"/></svg><div class="shedule-block">';
                    if (!empty($WORK_STR)){
                        $sBalloonContent.= '<div  class="'.($WORKING == "0" ? 'not-':'').'working">'.$WORK_STR.'</div><br>';
                    }
                    $sBalloonContent.= '<div class="shedule-str" data-start="" data-end="">'.$arStore['SCHEDULE'].'</span></div></div>';
                endif;

                $sBalloonContent .=  '<div class="map-balloon-footer "><a href="#" class="btn-simple btn-micro select_button reserve changeID get_medi_popup_Window" data-title="Забронировать в салоне" data-action="reserve" data-id="'.$arParams["ELEMENT_ID"].'" data-src="/ajax/catalog/?action=reserve&s='.$arStore['ID'].'&p='.$arParams["ELEMENT_ID"].'" class="greyButton reserve changeID get_medi_popup_Window" onclick="return false;" >Забронировать</a></div>';





                $arYandexFeatures[] = array(
                    'type' => 'Feature',
                    'id' => $arStore['ID'],
                    'geometry' => array(
                        'type' => 'Point',
                        'coordinates' => array( $arStore['COORDINATES']["GPS_N"], $arStore['COORDINATES']['GPS_S'])
                    ),
                    'properties' => array(
                        'hintContent' => $arStore['TITLE'],
                        'balloonContentBody' => $sBalloonContent,
                    )
                );




				$count_stores++;
			}
		}




	}
    // Схема метро
    // Город
    $dbSection = CIBlockSection::GetList(
        array('ID' => 'ASC'), array(
        'IBLOCK_ID' => 23,
        'NAME' => $def_city
    ), false, array('ID', 'NAME'), false
    );
    if ($arSection = $dbSection->Fetch()) {

        // Салон рядом со станцией
        $arResult['SALONS'] = array();
        foreach ($arResult["STORES"] as $k=>$store)
        {
            $arSalon = [];
            if ($store['METRO'][0]['ID'] > 0)
            {
                $arSalon['NAME'] = $store['TITLE'];
                $arSalon['DESCRIPTION'] = $store['DESCRIPTION'];
                $arSalon['SCHEDULE'] = $store['SCHEDULE'];
                $arSalon['HOLIDAY_SHEDULE'] = $store['HOLIDAY_SHEDULE'];
                $arSalon['WORKING'] = $store['WORKING'];
                $arSalon['WORK_STR'] = $store['WORK_STR'];

                $arSalon['ADDRESS'] = $store['ADDRESS'];
                $arSalon['PREVIEW_PICTURE'] = $store['PICTURE'];
                $arSalon['METRO_NAME'] = $store['METRO'][0]['NAME'];
                $arSalon['METRO_SRC'] =  $store['METRO'][0]['SECTION']['ICON']['SRC'];
                $arSalon['METRO_TYPE'] =  $store['METRO'][0]['SECTION']['ICON']['CONTENT_TYPE'];
                $arSalon['DETAIL_PAGE_URL'] = $pfx.'/salons/'.$store['CODE'].'/';

                $arResult['SALONS'][$store['METRO'][0]['ID']] = $arSalon;
            }
        }
    }

	$avgS = $sumS/$count_stores;
	$avgN = $sumN/$count_stores;
	// Начальные координаты для карты

	$arResult['COORD'] = array($avgN, $avgS);
	$arResult['arYandexFeatures'] = $arYandexFeatures;
	$arResult['arPlacemarks'] = $arPlacemarks;


}
if ($sumAmount > 0) $arResult["SHOW_STORES"] = "Y";
 
