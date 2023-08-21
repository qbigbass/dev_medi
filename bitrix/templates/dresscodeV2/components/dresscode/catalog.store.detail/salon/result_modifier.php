<?
$APPLICATION->AddChainItem($arResult["TITLE"], "/stores/".$arResult["ID"]."/");
$APPLICATION->SetPageProperty("title", $arResult["TITLE"]);
$APPLICATION->SetTitle($arResult["TITLE"]);
//__($arResult);
// Ассортимент товаров в салоне
if (!empty($arResult['UF_ASSORTMENT']))
{
    $assortment = unserialize($arResult['UF_ASSORTMENT']);

    $obEnum = new \CUserFieldEnum;
    $rsEnum = $obEnum->GetList(array(), array("USER_FIELD_NAME" => 'UF_ASSORTMENT'));

    $enum = array();
    while ($arEnum = $rsEnum->Fetch()) {
        if (in_array($arEnum['ID'], $assortment))
        {

            $obSect = CIBlockSection::GetList(["SORT"=>"ASC"], ['IBLOCK_ID'=>17, 'NAME'=>$arEnum['VALUE'], "ACTIVE"=>"Y"], false, ["SORT", "NAME", "SECTION_PAGE_URL"]);
            if ($arSect = $obSect->GetNext())
            {
                $enum[$arSect['SORT']][$arEnum["ID"]]['NAME'] = $arEnum["VALUE"];
                $enum[$arSect['SORT']][$arEnum["ID"]]['LINK'] = $arSect['SECTION_PAGE_URL'];
            }
        }
    }
    ksort($enum);
    $arResult['ASSORTMENT'] = $enum;
}

// Список услуг в салоне
if (!empty($arResult['UF_SERVICES'])) {
    $services = unserialize($arResult['UF_SERVICES']);

    $obEnum = new \CUserFieldEnum;
    $rsEnum = $obEnum->GetList(array(), array("USER_FIELD_NAME" => 'UF_SERVICES'));

    $enum = [];
    $slider_ids = [];

    while ($arEnum = $rsEnum->Fetch()) {

        if (in_array($arEnum['ID'], $services))
        {

            $ObLinks = CIBlockElement::GetList(
              ["SORT"=>"ASC"],
              ["IBLOCK_ID"=>11, "ACTIVE"=>"Y", "PROPERTY_STORE_SERVICES_NAME_VALUE"=>$arEnum['VALUE'], "=PROPERTY_CITY_VALUE"=>$GLOBALS['medi']['region_cities'][SITE_ID]],
               false,  false,
               ["ID","NAME", "CODE", "DETAIL_PAGE_URL", "PREVIEW_TEXT", "PREVIEW_PICTURE", "SORT", "PROPERTY_STORE_SERVICE_NAME"]
             );
            if ($arLinks = $ObLinks->GetNext())
            {
              $enum[$arLinks['SORT']][$arEnum["ID"]]['NAME'] =  $arEnum['VALUE'];
              $enum[$arLinks['SORT']][$arEnum["ID"]]['LINK'] =  $arLinks['DETAIL_PAGE_URL']."?salon=".$arResult['ID'].($arLinks['CODE'] == 'izgotovlenie-ortopedicheskikh-stelek' ? '#order' : '');
              $enum[$arLinks['SORT']][$arEnum["ID"]]['DESC'] = $arLinks['PREVIEW_TEXT'];
              $enum[$arLinks['SORT']][$arEnum["ID"]]['PICTURE'] = CFile::ResizeImageGet($arLinks['PREVIEW_PICTURE'], array("width" => 520, "height" => 400), BX_RESIZE_IMAGE_PROPORTIONAL, false);
              $slider_ids[] = $arLinks['ID'];
            }
            else {
                $enum[][$arEnum["ID"]]['NAME'] = $arEnum["VALUE"];
            }
        }
    }
    ksort($enum);
    $arResult['SERVICES'] = $enum;
    $arResult['SERVICES_SLIDES']  = $slider_ids;

}
//__($arResult);
if ($arResult['UF_CITY'] > 0)
{
    $obEnum = new \CUserFieldEnum;
    $rsEnum = $obEnum->GetList(array(), array("USER_FIELD_NAME" => 'UF_CITY'));
    $arResult['CITIES'] = [];
    $enum = array();
    while ($arEnum = $rsEnum->Fetch()) {
        if ($arResult['UF_CITY'] == $arEnum['ID'])
            $arResult['CITY'] = $arEnum['VALUE'];
    }
}
// Привязка к станции метро
if (!empty($arResult['UF_METRO'])) {
    $metro = unserialize($arResult['UF_METRO']);
    if (!empty($metro[0])) {
        $rsElm = CIBlockElement::GetList(array(), array("ID" => $metro[0], "IBLOCK_ID" => "23", "ACTIVE"=>"Y"), false, false, array("ID", "NAME", "IBLOCK_SECTION_ID"));
        while ($arMetro = $rsElm -> GetNext()) {

            $rsSect = CIBlockSection::GetList(array("NAME"=>"ASC"), array( "IBLOCK_ID" => "23", "ACTIVE"=>"Y", "ID"=> $arMetro['IBLOCK_SECTION_ID']), false, array("NAME", "PICTURE", "IBLOCK_SECTION_ID", "UF_ICON" ));
            if ($arSect = $rsSect->GetNext()) {
                if ($arSect['UF_ICON'] > 0) {
                    $arSect['ICON'] = CFile::GetFileArray($arSect["UF_ICON"]);
                }
                elseif ($arSect['PICTURE'] > 0) {
                    $arSect['ICON'] = CFile::GetFileArray($arSect["PICTURE"]);
                }
                $arMetro['SECTION'] = $arSect;
            }
              $arResult['METRO'][] = $arMetro;
        }
    }
}
if (!empty($arResult["ADDRESS"])):
$arResult["~ADDRESS"] = $arResult["ADDRESS"];
$arResult["ADDRESS"] = preg_replace("/[0-9]{6},/", "", $arResult["ADDRESS"]);
endif;
// основная фотография
if (!empty($arResult["IMAGE_ID"])) {

    $arResult["IMAGES"][] = array(
    "MED"     => CFile::ResizeImageGet($arResult["IMAGE_ID"], array("width" => 520, "height" => 400), BX_RESIZE_IMAGE_PROPORTIONAL, false),
    "SMALL"  => CFile::ResizeImageGet($arResult["IMAGE_ID"], array("width" => 175, "height" => 120), BX_RESIZE_IMAGE_PROPORTIONAL, false),
    "BIG"       => CFile::GetFileArray($arResult["IMAGE_ID"])
    );
}
//  генерируем префью фотографий салона
if (!empty($arResult['UF_MORE_PHOTO'])) {
    $arPhotos = unserialize($arResult['UF_MORE_PHOTO']);
    foreach ($arPhotos AS $k => $arPhoto) {

        $arResult['IMAGES'][] = array(
        'BIG'       => CFile::GetFileArray($arPhoto),
        'MED'     => CFile::ResizeImageGet($arPhoto, array("width" => 520, "height" => 400), BX_RESIZE_IMAGE_PROPORTIONAL, false),
        "SMALL" => CFile::ResizeImageGet($arPhoto, array("width" => 175, "height" => 120), BX_RESIZE_IMAGE_PROPORTIONAL, false),
        );
    }
}



$day_of_week = [1 => 'понедельник', 2=>'вторник', 3=>'среду', 4=>'четверг', 5=>'пятницу', 6=>'субботу', 7=>'воскресенье'];
$sday_of_week = [1 => 'MON', 2=>'TUE', 3=>'WED', 4=>'THU', 5=>'FRI', 6=>'SAT', 7=>'SUN'];

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
$WORK_TIME = explode("-",$arResult[$PROP_DAY]);
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
            $WORK_TIME = explode("-", $arResult[$PROP_DAY]);

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
        $WORK_TIME = explode("-", $arResult[$PROP_DAY]);

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
if (!empty($WORK_STR)) $arResult['WORK_STR'] = $WORK_STR;
$arResult['WORKING'] = $WORKING;

// График работы в праздники
$obElement = CIBlockElement::GetList([], ["IBLOCK_ID" => 24, "PROPERTY_STORE"=>$arResult['ID'], 'ACTIVE' => 'Y', 'ACTIVE_DATE'=>'Y'], false, false, ["NAME", "PREVIEW_TEXT", "ACTIVE_TO", "PROPERTY_HIDE_RR"]);
if ($arShedule = $obElement->GetNext())
{
    $arResult['HOLIDAY_SHEDULE'] = $arShedule;
}


if (file_exists($_SERVER['DOCUMENT_ROOT'].'/upload/content/tc_maps/'.$arResult['ID'].'.png'))
{
    $arResult['TCMAP'] = '/upload/content/tc_maps/'.$arResult['ID'].'.png';
}

echo '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "'.$arResult['TITLE'].'",
  "image": "https://www.medi-salon.ru/bitrix/templates/dresscodeV2/headers/header8/images/logo.png",
  "@id": "",
  "url": "https://www.medi-salon.ru'.$APPLICATION->GetCurDir().'",
  "telephone": "'.$arResult['PHONE'].'",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "'.str_replace($arResult['CITY'].',', '', $arResult['ADDRESS']).'",
    "addressLocality": "'.$arResult['CITY'].'",
    "postalCode": "'.$arResult['UF_INDEX'].'",
    "addressCountry": "RU"
  },
  "openingHoursSpecification": [{
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": "Monday",
    "opens": "'.explode("-",$arResult['UF_RR_MON'])[0].'",
    "closes": "'.explode("-",$arResult['UF_RR_MON'])[1].'"
  },{
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": "Tuesday",
    "opens": "'.explode("-",$arResult['UF_RR_TUE'])[0].'",
    "closes": "'.explode("-",$arResult['UF_RR_TUE'])[1].'"
  },{
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": "Wednesday",
    "opens": "'.explode("-",$arResult['UF_RR_WED'])[0].'",
    "closes": "'.explode("-",$arResult['UF_RR_WED'])[1].'"
  },{
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": "Thursday",
    "opens": "'.explode("-",$arResult['UF_RR_THU'])[0].'",
    "closes": "'.explode("-",$arResult['UF_RR_THU'])[1].'"
  },{
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": "Friday",
    "opens": "'.explode("-",$arResult['UF_RR_FRI'])[0].'",
    "closes": "'.explode("-",$arResult['UF_RR_FRI'])[1].'"
  }';
  if (!empty($arResult['UF_RR_SAT'])) {
      echo
          ',{
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": "Saturday",
    "opens": "' . explode("-", $arResult['UF_RR_SAT'])[0] . '",
    "closes": "' . explode("-", $arResult['UF_RR_SAT'])[1] . '"
  } ';
  }
if (!empty($arResult['UF_RR_SUN'])) {
    echo
        ',{
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": "Sunday",
        "opens": "' . explode("-", $arResult['UF_RR_SUN'])[0] . '",
        "closes": "' . explode("-", $arResult['UF_RR_SUN'])[1] . '"
      }
      ';
}
echo '
  ] 
}
</script>
';
?>
