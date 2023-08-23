<?php

use Bitrix\Main\Loader;

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
Loader::includeModule('up.boxberrydelivery');

if (isset($_POST['select_pvz_id']) && !empty($_POST['select_pvz_id'])) {
	CBoxberry::updateOrder($_POST['select_pvz_id'], $_POST['order_id'], (isset($_POST['address']) ? $_POST['address'] : NULL));
}

if (isset($_POST['save_pvz_id']) && !empty($_POST['save_pvz_id'])) {
	session_start();

	CBoxberry::savePvz($_POST['save_pvz_id']);
	echo true;
}

if (isset($_POST['remove_pvz']) && !empty($_POST['remove_pvz'])) {
	CBoxberry::removePvz();
	echo true;
}

if (isset($_POST['check_pvz']) && !empty($_POST['check_pvz'])) {
	CBoxberry::checkPvz();
	echo true;
}

if (isset($_POST['disable_check_pvz']) && !empty($_POST['disable_check_pvz'])) {
	CBoxberry::disableCheckPvz();
	echo true;
}

if (isset($_POST['get_link']) && $profile = CDeliveryBoxberry::getDeliveryCode($_POST['get_link'])) {
	echo CDeliveryBoxberry::makeWidgetLink($profile);
}

?>
