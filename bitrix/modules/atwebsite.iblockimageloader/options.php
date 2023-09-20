<?
use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Loader;
$moduleId = 'atwebsite.iblockimageloader';
$moduleJsId = 'atwebsite_iblockimageloader';
Loader::includeModule($moduleId);
CJSCore::Init(array($moduleJsId));

if($USER->IsAdmin())
{
	Loc::loadMessages(__FILE__);

	$aTabs = array(
		array("DIV" => "edit0", "TAB" => Loc::getMessage("MAIN_TAB_RIGHTS"), "ICON" => "", "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS")),
	);
	$tabControl = new CAdminTabControl("atwebsitevisualeditorTabControl", $aTabs, true, true);

	if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['RestoreDefaults']) && !empty($_GET['RestoreDefaults']) && check_bitrix_sessid())
	{
		COption::RemoveOption($moduleId);
		$arGROUPS = array();
		$z = CGroup::GetList($v1, $v2, array("ACTIVE" => "Y", "ADMIN" => "N"));
		while($zr = $z->Fetch())
		{
			$ar = array();
			$ar["ID"] = intval($zr["ID"]);
			$ar["NAME"] = htmlspecialcharsbx($zr["NAME"])." [<a title=\"".GetMessage("MAIN_USER_GROUP_TITLE")."\" href=\"/bitrix/admin/group_edit.php?ID=".intval($zr["ID"])."&lang=".LANGUAGE_ID."\">".intval($zr["ID"])."</a>]";
			$groups[$zr["ID"]] = "[".$zr["ID"]."] ".$zr["NAME"];
			$arGROUPS[] = $ar;
		}
		reset($arGROUPS);
		while (list(,$value) = each($arGROUPS))
			$APPLICATION->DelGroupRight($moduleId, array($value["ID"]));
	
		LocalRedirect($APPLICATION->GetCurPage().'?lang='.LANGUAGE_ID.'&mid_menu=1&mid='.$moduleId);
	}

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && check_bitrix_sessid())
	{
		if(isset($_POST['Update']) && $_POST['Update'] === 'Y' && is_array($_POST['SETTINGS']))
		{
			foreach($_POST['SETTINGS'] as $k=>$v)
			{
				if(in_array($k, array('TRANS_PARAMS')))
				{
					$v = serialize($v);
				}
				COption::SetOptionString($moduleId, $k, (is_array($v) ? serialize($v) : $v));
			}
		}
	}

	$tabControl->Begin();
	?>
	<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo LANGUAGE_ID?>&mid_menu=1&mid=<?=$moduleId?>" name="atwebsite_visualeditor_settings">
	<? echo bitrix_sessid_post();

	$arTransParams = COption::GetOptionString($moduleId, 'TRANS_PARAMS', '');
	if(is_string($arTransParams) && !empty($arTransParams)) $arTransParams = unserialize($arTransParams);
	if(!is_array($arTransParams)) $arTransParams = array();
	$tabControl->BeginNextTab();?>
	<?
	$module_id = $moduleId;
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
	?>
	<?
	$tabControl->Buttons();?>
	<script type="text/javascript">
	function RestoreDefaults()
	{
		if (confirm('<? echo CUtil::JSEscape(Loc::getMessage("EDIT_OPTIONS_BTN_HINT_RESTORE_DEFAULT_WARNING")); ?>'))
			window.location = "<?echo $APPLICATION->GetCurPage()?>?lang=<? echo LANGUAGE_ID; ?>&mid_menu=1&mid=<? echo $moduleId; ?>&RestoreDefaults=Y&<?=bitrix_sessid_get()?>";
	}
	</script>
	<input type="submit" name="Update" value="<?echo Loc::getMessage("EDIT_OPTIONS_BTN_SAVE")?>">
	<input type="hidden" name="Update" value="Y">
	<input type="reset" name="reset" value="<?echo Loc::getMessage("EDIT_OPTIONS_BTN_RESET")?>">
	<input type="button" title="<?echo Loc::getMessage("EDIT_OPTIONS_BTN_HINT_RESTORE_DEFAULT")?>" onclick="RestoreDefaults();" value="<?echo Loc::getMessage("EDIT_OPTIONS_BTN_RESTORE_DEFAULT")?>">
	<?$tabControl->End();?>
	</form>
<?
}
?>