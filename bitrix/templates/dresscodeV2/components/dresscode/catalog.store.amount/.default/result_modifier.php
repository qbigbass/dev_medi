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

				$sBalloonContent = '<div class="balloon">';
				if ($arStore['IMAGE_ID']) {
					$arFileTmp = CFile::ResizeImageGet(
					$arStore['IMAGE_ID'],
					array("width" => 150, "height" => 110),
					BX_RESIZE_IMAGE_PROPORTIONAL ,
					true
					);
					$arStore['PICTURE'] = array(
					'SRC' => $arFileTmp["src"],
					'WIDTH' => $arFileTmp["width"],
					'HEIGHT' => $arFileTmp["height"],
					'TITLE' => $arStore['TITLE']
					);

					$sBalloonContent .= '<div class="preview_photo"><img src="' . $arStore['PICTURE']['SRC'] . '" alt="'.$arStore['PICTURE']['TITLE'].'"></div>';
				}

				$sBalloonContent .= '<div class="text">';

				$metro  = unserialize($arStore['UF_METRO']);
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
				}

				if ($arStore['METRO']['NAME'] && !empty($arStore['METRO']['SECTION']['ICON']['SRC'])) {
					$sBalloonContent .= '<div class="metro"><img src="' . $arStore['METRO']['SECTION']['ICON']['SRC']. '" alt="">' . $arStore['METRO']['NAME'] . '</div> ';
				}
				$sBalloonContent .= $arStore['DESCRIPTION'];
				$sBalloonContent .= $arStore['ADDRESS'];

				$sBalloonContent .= '<div class="bmore"><a href="#" data-title="Забронировать в салоне" data-action="reserve" data-id="'.$arParams["ELEMENT_ID"].'" data-src="/ajax/catalog/?action=reserve&s='.$arStore['ID'].'&p='.$arParams["ELEMENT_ID"].'" class="greyButton reserve changeID get_medi_popup_Window">Забронировать</a></div></div><div class="clear"></div></div>';


				$arYandexFeatures[] = array(
					'type' => 'Feature',
					'id' => $arStore['ID'],
					'geometry' => array(
						'type' => 'Point',
						'coordinates' => array( $arStore['COORDINATES']["GPS_N"], $arStore['COORDINATES']['GPS_S'])
				),
				'properties' => array(
					'hintContent' => $arStore['TITLE'],
					'balloonContentHeader' => '<div><a href="' . $arStore['DETAIL_PAGE_URL'] . '" class="theme-link-dashed">' .  $arStore['TITLE'] . '</a></div>',
					'balloonContentBody' => $sBalloonContent,
					'balloonContentFooter' => '<div class="bfooter"></div>'
				)
				);




				$count_stores++;
			}
		}


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
            elseif ($work_hour_start[0] < $now_hour && $work_hour_end[0] > $now_hour ) {

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
 
