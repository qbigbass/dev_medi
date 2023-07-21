<?
$arPlacemarks = array();
$showPictures = 0;
$gpsN = '';
$gpsS = '';

if (is_array($arResult["STORES"]) && !empty($arResult["STORES"])):

    $def_city  = $GLOBALS['medi']['region_cities'][$arResult['STORES'][0]['SITE_ID']];

    foreach ($arResult["STORES"] as &$arProperty):


        if ($arProperty["GPS_S"] != 0 && $arProperty["GPS_N"] != 0) {


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


            $count_stores++;
    }

    if (!empty($arProperty['UF_MORE_PHOTO']))
    {
        $photos = unserialize($arProperty['UF_MORE_PHOTO']);
        foreach($photos as $p)
        {
            $arProperty['PHOTOS'][] = CFile::GetFileArray($p);
        }
    }
    endforeach;



endif;
