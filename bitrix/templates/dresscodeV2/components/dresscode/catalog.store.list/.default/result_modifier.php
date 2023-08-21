<?
$arPlacemarks = array();
$showPictures = 0;
$gpsN = '';
$gpsS = '';

if (is_array($arResult["STORES"]) && !empty($arResult["STORES"])):

    $def_city  = $GLOBALS['medi']['region_cities'][$arResult['STORES'][0]['SITE_ID']];

    foreach ($arResult["STORES"] as &$arProperty):


        // График работы в праздники
        $obElement = CIBlockElement::GetList([], ["IBLOCK_ID" => 24, "PROPERTY_STORE"=>$arProperty['ID'], 'ACTIVE' => 'Y', 'ACTIVE_DATE'=>'Y'], false, false, ["NAME", "PREVIEW_TEXT"]);
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

            // Получаем пользовательские свойства складов
            if (!empty($arProperty["DETAIL_IMG"])) {
                $showPictures = true;
            }

            $sBalloonContent = '<div class="balloon">';
            if ($arProperty['DETAIL_IMG']) {
                $arFileTmp = CFile::ResizeImageGet(
                $arProperty['DETAIL_IMG'],
                array("width" => 150, "height" => 110),
                BX_RESIZE_IMAGE_PROPORTIONAL ,
                true
                );
                $arProperty['PICTURE'] = array(
                'SRC' => $arFileTmp["src"],
                'WIDTH' => $arFileTmp["width"],
                'HEIGHT' => $arFileTmp["height"],
                'TITLE' => $arProperty['TITLE']
                );

                $sBalloonContent .= '<div class="preview_photo"><img src="' . $arProperty['PICTURE']['SRC'] . '" alt="'.$arProperty['PICTURE']['TITLE'].'"></div>';
            }

            $sBalloonContent .= '<div class="text">';

            if ($arProperty['METRO'][0]['NAME'] && !empty($arProperty['METRO'][0]['SECTION']['ICON']['SRC'])) {
                $sBalloonContent .= '<div class="metro"><img src="' . $arProperty['METRO'][0]['SECTION']['ICON']['SRC']. '" alt="">' . $arProperty['METRO'][0]['NAME'] . '</div> ';
            }
            $sBalloonContent .= $arProperty['DESCRIPTION'];
            $sBalloonContent .= $arProperty['ADDRESS'];

            $sBalloonContent .= '<div class="bmore"><br><a href="' . $arProperty['DETAIL_PAGE_URL'] . '" class="btn-simple btn-micro">Подробнее</a></div></div><div class="clear"></div></div>';


            $arYandexFeatures[] = array(
                'type' => 'Feature',
                'id' => $arProperty['ID'],
                'geometry' => array(
                    'type' => 'Point',
                    'coordinates' => array( $arProperty["GPS_N"], $arProperty['GPS_S'])
            ),
            'properties' => array(
                'hintContent' => $arProperty['TITLE'],
                'balloonContentHeader' => '<div><a href="' . $arProperty['DETAIL_PAGE_URL'] . '" class="theme-link-dashed-b">' .  $arProperty['TITLE'] . '</a></div>',
                'balloonContentBody' => $sBalloonContent,
                'balloonContentFooter' => '<div class="bfooter"></div>'
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

                $arSalon['ADDRESS'] = $store['ADDRESS'];
                $arSalon['PREVIEW_PICTURE'] = $store['PICTURE'];
                $arSalon['METRO_NAME'] = $store['METRO'][0]['NAME'];
                $arSalon['METRO_SRC'] =  $store['METRO'][0]['SECTION']['ICON']['SRC'];
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
