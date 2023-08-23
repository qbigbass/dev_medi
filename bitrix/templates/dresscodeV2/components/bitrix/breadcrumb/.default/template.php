<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION;
if(empty($arResult))
	return "";

$strReturn = '<div id="breadcrumbs"><ul>';

$num_items = count($arResult);
for($index = 0, $itemSize = $num_items; $index < $itemSize; $index++){

	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);

	if(!empty($title)){

		if(!empty($arResult[$index]["LINK"]) && $index != $itemSize-1){
			$strReturn .= '<li><a href="'.$arResult[$index]["LINK"].'" title="'.$title.'"><span>'.$title.'</span></a></li><li><span class="arrow"> &bull; </span></li>';
		}

		else{
			$strReturn .= '<li><span class="changeName">'.$title.'</span></li>';
		}

	}

}

$strReturn .= '</ul></div>';

$strReturn .= '<script type="application/ld+json">
{
    "@context": "https://schema.org/", 
    "@type": "BreadcrumbList", 
    "itemListElement": [';

$num_items = count($arResult);
for($index = 0, $itemSize = $num_items; $index < $itemSize; $index++){

    $title = htmlspecialcharsex($arResult[$index]["TITLE"]);

    if(!empty($title)){
        if ($index > 0) $strReturn .= ',';
        if(!empty($arResult[$index]["LINK"]) && $index != $itemSize-1){
            $strReturn .= '{"@type": "ListItem", "position": '.($index+1).', "name": "'.$title.'", "item": "https://'.SITE_SERVER_NAME.$arResult[$index]["LINK"].'" }';
        }
        else{
            $strReturn .= '{"@type": "ListItem", "position": '.($index+1).', "name": "'.$title.'", "item": "https://'.SITE_SERVER_NAME.explode("?", $APPLICATION->GetCurUri("", false))[0].'" }';
        }
    }

}
$strReturn .= ']}</script>';


return $strReturn;
?>