<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
?>
<?
if (!empty($arResult["ITEMS"])) {
    foreach ($arResult['ITEMS'] as $k => $arItem) {
        
        if (!empty($arItem['PROPERTIES']['PREVIEW_IMG']['VALUE'])) {
            $arResult['ITEMS'][$k]['PHOTO'] = CFile::ResizeImageGet($arItem['PROPERTIES']['PREVIEW_IMG']['VALUE'], [
                    "width" => 120, "height" => 135]
                , BX_RESIZE_IMAGE_PROPORTIONAL, false);
        } elseif (!empty($arItem['PREVIEW_PICTURE'])) {
            $arResult['ITEMS'][$k]['PHOTO'] = $arItem["PREVIEW_PICTURE"];
        }
    }
}