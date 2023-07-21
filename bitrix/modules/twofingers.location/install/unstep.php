<?php use Bitrix\Main\Localization\Loc; ?>
<form action="<?=$APPLICATION->GetCurPage()?>">
<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="hidden" name="id" value="twofingers.location">
	<input type="hidden" name="uninstall" value="Y">
	<input type="hidden" name="step" value="2">
	<?=\CAdminMessage::ShowMessage(Loc::getMessage("MOD_UNINST_WARN"))?>
	<!--<p><?=Loc::getMessage("MOD_UNINST_SAVE")?></p>-->
	<p><input type="checkbox" name="saveiblock" id="saveiblock" value="Y" checked><label for="saveiblock"><?=Loc::getMessage("tf-location__uninstall-saveiblock")?></label></p>
	<input type="submit" name="inst" value="<?=Loc::getMessage("MOD_UNINST_DEL")?>">
</form>