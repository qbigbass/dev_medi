<?
/**
 * @copyright Copyright &copy; Компания MEAsoft, 2014
 */

if (!check_bitrix_sessid()) {
		return;
	}

	if ($message !== false) {
		echo CAdminMessage::ShowMessage(array('MESSAGE' => $message, 'TYPE' => $type));
	};

	if ($installOk) {
		echo BeginNote();
		echo GetMessage("MEASOFT_MODULE_INSTALLED_OK");
		echo EndNote();
	}
?>
<form action="<?php echo $APPLICATION->GetCurPage(); ?>">
	<input type="hidden" name="lang" value="<?php echo LANGUAGE_ID; ?>">
	<input type="submit" name="" value="<?php echo GetMessage("MOD_BACK"); ?>">
<form>