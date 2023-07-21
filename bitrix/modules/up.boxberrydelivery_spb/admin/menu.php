<?
IncludeModuleLangFile(__FILE__);
$module_id = "up.boxberrydelivery_spb";
if($APPLICATION->GetGroupRight($module_id) != "D")
{
    $aMenu = array(
        "parent_menu" => "global_menu_store",
        "sort" => 110,
        "text" => GetMessage("BB_MENU_TEXT_spb"),
        "title" => GetMessage("BB_MENU_TITLE_spb"),
        "icon" => "boxberry_menu_icon",
        "page_icon" => "boxberry_page_icon",
        "url" => "boxberry_spb.php?lang=".LANGUAGE_ID,
    );
	return $aMenu;
}
return false;



?>
