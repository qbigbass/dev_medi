<? 

foreach($arResult['ITEMS'] AS &$arElement){
if(!empty($arElement["PROPERTIES"]['BG_IMAGE_M']['VALUE'])){
    $arElement["MOBILE_IMG"] = CFile::ResizeImageGet($arElement["PROPERTIES"]['BG_IMAGE_M']['VALUE'], array("width" => 480, "height" => 330), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 90);
}
}
?>
