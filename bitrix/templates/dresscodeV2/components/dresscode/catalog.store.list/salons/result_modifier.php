<?
$arPlacemarks = array();
$showPictures = 0;
$gpsN = '';
$gpsS = '';


if (is_array($arResult["STORES"]) && !empty($arResult["STORES"])):

    $def_city  = $GLOBALS['medi']['region_cities'][$arResult['STORES'][0]['SITE_ID']];


    $day_of_week = [1 => 'понедельник', 2=>'вторник', 3=>'среду', 4=>'четверг', 5=>'пятницу', 6=>'субботу', 7=>'воскресенье'];
    $sday_of_week = [1 => 'MON', 2=>'TUE', 3=>'WED', 4=>'THU', 5=>'FRI', 6=>'SAT', 7=>'SUN'];

    foreach ($arResult["STORES"] as &$arProperty):

        if ($arParams['TODAY'] > 0 && $arParams['TODAY'] <= 7)
            $today = $arParams['TODAY'];
        else {
            $today = date("N");
        }
        if ($arParams['WEEKDAY'] )
            $weekday = strtoupper($arParams['WEEKDAY']);
        else {
            $weekday = strtoupper(date("D"));
        }
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


            if ($arProperty['METRO'][0]['NAME'] && !empty($arProperty['METRO'][0]['SECTION']['ICON']['SRC'])) {
                $sBalloonContent .= '<div class="map-balloon-metro map-balloon-tr"><img src="' . $arProperty['METRO'][0]['SECTION']['ICON']['SRC']. '" title="'.$arProperty['METRO'][0]['SECTION']['NAME'].'"><span>' . $arProperty['METRO'][0]['NAME'] . '</span></div> ';
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

            $sBalloonContent .= '<div class="map-balloon-footer "><a href="' . $arProperty['DETAIL_PAGE_URL'] . '" class="btn-simple btn-micro">Подробнее</a></div>';



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


        // Ветки метро
        $dbLines = CIBlockSection::GetList(
            array('ID' => 'ASC'),
            array(
                'ACTIVE' => "Y",
                //'ID' => array(126,134),
                'IBLOCK_ID' => 23,
                'SECTION_ID' => $arSection['ID']
        ), false, array('ID', 'NAME',"PICTURE","UF_COLOR"), false
        );

        while ($arLines = $dbLines->Fetch())
        {
            $stations = array();
            $arLines['IMG'] = CFile::GetFileArray($arLines['PICTURE']);
            // станции метро
            $obStations = CIBlockElement::GetList(array("SORT"=>"ASC"),
                array(
                    "IBLOCK_ID" => 23,
                    "ACTIVE" => "Y",
                    "SECTION_ID" => $arLines['ID']
                ),
                false,
                false,
                array("ID", "NAME", "PROPERTY_X_POSITION", "PROPERTY_Y_POSITION")
            );
            while ($arStation = $obStations->Fetch())
            {
                $stations[$arStation['ID']] = $arStation;
            }




            $arLines['STATIONS'] = $stations;
            $arResult['LINES'][] = $arLines;
            //print_r($arLines);
        }
    }



    /*
    if ($arResult['ITEMS']) {
        foreach ($arResult['ITEMS'] as &$arItem) {
            if ($arItem['DISPLAY_PROPERTIES']['METRO']) {
                $arItem['METRO_NAME'] = $arItem['DISPLAY_PROPERTIES']['METRO']['LINK_ELEMENT_VALUE'][$arItem['DISPLAY_PROPERTIES']['METRO']['VALUE']]['NAME'];
                $arItem['METRO_SRC'] = CFile::GetPath($arItem['DISPLAY_PROPERTIES']['METRO']['LINK_ELEMENT_VALUE'][$arItem['DISPLAY_PROPERTIES']['METRO']['VALUE']]['PREVIEW_PICTURE']);
            }
        }
    }*/


endif;
