<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arResult['DETAIL_TEXT'] =  str_replace("#PHONE#", $GLOBALS['medi']['phones'][SITE_ID], $arResult['DETAIL_TEXT']);
$arResult['PREVIEW_TEXT'] =  str_replace("#PHONE#", $GLOBALS['medi']['phones'][SITE_ID], $arResult['PREVIEW_TEXT']);

if (strpos($arResult['DETAIL_TEXT'], "#STORE_LIST") > 0)
{
    $replace = preg_match_all("/#STORE_LIST\[(.*)\]#/U", $arResult['DETAIL_TEXT'], $matches);
    if (!empty($matches[1][0])){
        $salon_ids = explode(",", $matches[1][0]);
        if (!empty($salon_ids))
        {
            //buffer
            ob_start();

            $GLOBALS['storesNewsList'] = ['SITE_ID'=>SITE_ID, "ID"=>$salon_ids];
            $APPLICATION->IncludeComponent(
            	"dresscode:catalog.store.list",
            	"newslist",
            	Array(
            		"CACHE_TIME" => "36000000",
            		"CACHE_TYPE" => "A",
            		"MAP_TYPE" => "0",
            		"PATH_TO_ELEMENT" => "store/#store_code#",
            		"FILTER_NAME" => "storesNewsList",
            		"PHONE" => "N",
            		"SCHEDULE" => "N",
            		"SET_TITLE" => "N"
            	)
            );
            //save buffer
            $componentData = ob_get_contents();
            //end buffer
            ob_end_clean();

            $arResult['DETAIL_TEXT'] = str_replace($matches[0][0], $componentData, $arResult['DETAIL_TEXT']);

        }
        else {
            $arResult['DETAIL_TEXT'] = str_replace($matches[0][0], "", $arResult['DETAIL_TEXT']);
        }
    }
}

$this->__component->SetResultCacheKeys(array(
    "NAME",
    "PREVIEW_TEXT",
    "DETAIL_PICTURE",
    "DETAIL_PAGE_URL"
));?>
