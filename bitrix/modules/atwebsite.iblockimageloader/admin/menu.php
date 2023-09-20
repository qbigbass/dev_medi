<?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$aMenu[] = array(
	"parent_menu" => "global_menu_content",
	"sort" => 100,
	"text" => Loc::GetMessage("LOADER_MENU_PARENT_TITLE"),
	"title" => Loc::GetMessage("LOADER_MENU_PARENT_TITLE"),
	"icon" => "atwebsite_iblockImageLoader_icon",
	"items_id" => "menu_atwebsite_iblockImageLoader",
	"module_id" => "atwebsite.iblockimageloader",
	"url" => "iblock_image_loader.php?lang=".LANGUAGE_ID,
	"items" => array ()
);
return (!empty($aMenu) ? $aMenu : false);
?>