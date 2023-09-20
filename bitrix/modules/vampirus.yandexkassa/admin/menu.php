<?php

IncludeModuleLangFile(__FILE__);
$res = $DB->Query("SHOW TABLES LIKE 'vampirus_yandexkassa'");
if ($res->SelectedRowsCount() && $APPLICATION->GetGroupRight("vampirus.yandexkassa") != "D") {
	$data    = $DB->Query("SELECT count(*) as total FROM vampirus_yandexkassa WHERE STATUS!=0")->Fetch();
	$dataNew = $DB->Query("SELECT count(*) as total FROM vampirus_yandexkassa_new WHERE status!='pending'")->Fetch();
	if ($data['total'] || $dataNew['total']) {
		$aMenu = array(
			"parent_menu" => "global_menu_store",
			"section"     => "vampirus_yandexkassa",
			"sort"        => 100,
			"text"        => GetMessage("VAMPIRUS.YANDEXKASSA_MENU_TITLE"),
			"title"       => GetMessage("VAMPIRUS.YANDEXKASSA_MENU_TITLE"),
			"icon"        => "vampirus_yandexkassa_menu_icon",
			"page_icon"   => "vampirus_yandexkassa_menu_icon",
			"items_id"    => "menu_vampirus_yandexkassa",
			'items'       => array(),
		);
		if ($data['total']) {
			$aMenu['items'][] = array(
				"sort"        => 100,
				"text"        => GetMessage("VAMPIRUS.YANDEXKASSA_MENU_TITLE_OLD"),
				"title"       => GetMessage("VAMPIRUS.YANDEXKASSA_MENU_TITLE_OLD"),
				"url"         => "vampirus_yandexkassa.php?lang=" . LANGUAGE_ID,
				"items_id"    => "menu_vampirus_yandexkassa_old",
			);
		}

		if ($dataNew['total']) {
			$aMenu['items'][] = array(
				"sort"        => 110,
				"text"        => GetMessage("VAMPIRUS.YANDEXKASSA_MENU_TITLE_NEW"),
				"title"       => GetMessage("VAMPIRUS.YANDEXKASSA_MENU_TITLE_NEW"),
				"url"         => "vampirus_yandexkassa_new.php?lang=" . LANGUAGE_ID,
				"items_id"    => "menu_vampirus_yandexkassa_new",
			);
		}
		return $aMenu;
	}
}

return false;
