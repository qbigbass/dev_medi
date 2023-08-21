<?

if (is_array($arResult["STORES"]) && !empty($arResult["STORES"])):

    $site_id = $arResult['STORES'][0]['SITE_ID'] != '' ? $arResult['STORES'][0]['SITE_ID'] : $arResult['STORES'][1]['SITE_ID'] != '' ? $arResult['STORES'][1]['SITE_ID'] : $arResult['STORES'][2]['SITE_ID'];
    $def_city  = $GLOBALS['medi']['region_cities'][$site_id];

    foreach ($arResult["STORES"] as &$arProperty):
        $pfx= '';
        if ($GLOBALS['medi']['sfolder'][$arProperty['SITE_ID']] != '')
        {
            $pfx = "/".$GLOBALS['medi']['sfolder'][$arProperty['SITE_ID']] ;
        }
        $arProperty['DETAIL_PAGE_URL']  = $pfx.'/salons/'.$arProperty['CODE'].'/';
    endforeach;
endif;
