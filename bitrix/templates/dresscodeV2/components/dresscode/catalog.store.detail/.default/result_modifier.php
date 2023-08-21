<?
$APPLICATION->AddChainItem($arResult["TITLE"], "/stores/".$arResult["ID"]."/");
$APPLICATION->SetPageProperty("title", $arResult["TITLE"]);
$APPLICATION->SetTitle($arResult["TITLE"]);

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

    $enum = array();
    while ($arEnum = $rsEnum->Fetch()) {

        if (in_array($arEnum['ID'], $services))
        {
            $ObLinks = CIBlockElement::GetList(
              ["SORT"=>"ASC"],
              ["IBLOCK_ID"=>11, "PROPERTY_STORE_SERVICES_NAME_VALUE"=>$arEnum['VALUE']],
               false,  false,
               ["NAME", "DETAIL_PAGE_URL", "PREVIEW_TEXT", "SORT", "PROPERTY_STORE_SERVICE_NAME"]
             );
            if ($arLinks = $ObLinks->GetNext())
            {
              $enum[$arLinks['SORT']][$arEnum["ID"]]['NAME'] =  $arEnum['VALUE'];
              $enum[$arLinks['SORT']][$arEnum["ID"]]['LINK'] = $arLinks['DETAIL_PAGE_URL']."?salon=".$arResult['ID'];
              $enum[$arLinks['SORT']][$arEnum["ID"]]['DESC'] = $arLinks['PREVIEW_TEXT'];
            }
            else {
                $enum[][$arEnum["ID"]]['NAME'] = $arEnum["VALUE"];
            }
        }
    }
    ksort($enum);
    $arResult['SERVICES'] = $enum;
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

            $rsSect = CIBlockSection::GetList(array("NAME"=>"ASC"), array( "IBLOCK_ID" => "23", "ACTIVE"=>"Y", "ID"=> $arMetro['IBLOCK_SECTION_ID']), false, array("NAME", "PICTURE", "IBLOCK_SECTION_ID" ));
            if ($arSect = $rsSect->GetNext()) {
                if ($arSect['PICTURE'] > 0) {
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
    "MED"     => CFile::ResizeImageGet($arResult["IMAGE_ID"], array("width" => 720, "height" => 500), BX_RESIZE_IMAGE_PROPORTIONAL, false),
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
        'MED'     => CFile::ResizeImageGet($arPhoto, array("width" => 720, "height" => 500), BX_RESIZE_IMAGE_PROPORTIONAL, false),
        "SMALL" => CFile::ResizeImageGet($arPhoto, array("width" => 175, "height" => 120), BX_RESIZE_IMAGE_PROPORTIONAL, false),
        );
    }
}

// График работы в праздники
$obElement = CIBlockElement::GetList([], ["IBLOCK_ID" => 24, "PROPERTY_STORE"=>$arResult['ID'], 'ACTIVE' => 'Y', 'ACTIVE_DATE'=>'Y'], false, false, ["NAME", "PREVIEW_TEXT"]);
if ($arShedule = $obElement->GetNext())
{
    $arResult['HOLIDAY_SHEDULE'] = $arShedule;
}

  //  __($arResult);
?>
