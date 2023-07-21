<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/prolog.php");


if(!$USER->CanDoOperation('edit_other_settings') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('edit_other_settings');

IncludeModuleLangFile(__FILE__);

$aMsg = array();
$message = null;
$bVarsFromForm = false;
$ID = $_REQUEST["ID"];
$bNew = ($ID == '' || $_REQUEST['new'] == 'Y');


$aTabs = array(
	array("DIV" => "edit1", "TAB" => "Регион", "ICON" => "site_edit", "TITLE" => "Регион"),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
/*
$arTemplates = array();
if(!$bNew)
{
	$dbSiteRes = $DB->query("SELECT * FROM medi_regions WHERE ID = '$ID'");
	while($arSiteRes = $dbSiteRes->Fetch())
		$arTemplates[$arSiteRes["ID"]] = $arSiteRes['CONDITION'];
}
*/

if($_SERVER["REQUEST_METHOD"] == "POST" && ($_POST["save"] <> '' || $_POST["apply"] <> '') && $isAdmin && check_bitrix_sessid())
{
	$arFields = array(
		"ACTIVE" => "'".($_POST["ACTIVE"] == "Y"? "Y" : "N")."'",
		"SORT" => $_POST["SORT"],
		"CITY" => "'".$_POST["CITY"]."'",
		"DIR" => "'".$_POST["DIR"]."'",
		"REGION" => "'".$_POST["REGION"]."'",
		"PHONE" => "'".$_POST["PHONE"]."'",
		"SITE_ID" => "'".$_POST["SITE_ID"]."'",
		"PRICE" => "'".$_POST["PRICE"]."'",
		"MAX_PRICE" => "'".$_POST["MAX_PRICE"]."'",

	);


	if($bNew)
	{
		$arFields["ID"]=$ID;
	}

	$res = false;
	$ber = true;


	if ($ber)
	{

		if(!$bNew)
		{
			$res = $DB->Update("medi_regions", $arFields, " WHERE ID = '".$ID."'");
		}
		else
		{
			$res = $DB->Insert("medi_regions", $arFields, $err_mess.__LINE__);
		}
	}

	if(!$res)
	{
		$bVarsFromForm = true;
	}
	else
	{


		if ($_POST["save"] <> '')
			LocalRedirect(BX_ROOT."/admin/medi.regions.php?lang=".LANGUAGE_ID);
		else
			LocalRedirect(BX_ROOT."/admin/medi.regions_edit.php?lang=".LANGUAGE_ID."&ID=".$ID."&".$tabControl->ActiveTabParam());
	}
}

if($bNew && $COPY_ID == '')
{
	$str_ACTIVE = 'Y';
	$str_SORT = '1';
	$str_DIR = '';
}

if($COPY_ID <> '')
{
	$ID = $COPY_ID;
	$reg = $DB->query("SELECT * FROM medi_regions WHERE ID = '".$COPY_ID."' ");
	if(!$reg->ExtractFields("str_"))
		$bNew = true;
}
elseif(!$bNew)
{
	$reg = $DB->query("SELECT * FROM medi_regions WHERE ID = '".$ID."' ");
	if(!$reg->ExtractFields("str_"))
		$bNew = true;
}

$APPLICATION->SetTitle(($bNew? "Новый регион" : "Редактирование региона ".$ID));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if($bNew)
{
	$sites_cnt = 0;
	$r = $DB->query("SELECT * FROM medi_regions");
	while($r->Fetch())
		$sites_cnt++;
}

$aMenu = array(
	array(
		"TEXT"	=> "Список",
		"LINK"	=> "/bitrix/admin/medi.regions.php?lang=".LANGUAGE_ID."",
		"TITLE"	=> "Список",
		"ICON"	=> "btn_list"
	)
);

if(!$bNew)
{
	$aMenu[] = array("SEPARATOR"=>"Y");

	$aMenu[] = array(
		"TEXT"	=> "Новый",
		"LINK"	=> "/bitrix/admin/medi.regions_edit.php?lang=".LANGUAGE_ID,
		"TITLE"	=> "Новый",
		"ICON"	=> "btn_new"
		);

	$aMenu[] = array(
		"TEXT"	=> "Копировать",
		"LINK"	=> "/bitrix/admin/medi.regions_edit.php?lang=".LANGUAGE_ID."&amp;COPY_ID=".urlencode($str_ID),
		"TITLE"	=> "Копировать",
		"ICON"	=> "btn_copy"
		);

	/*$aMenu[] = array(
		"TEXT"	=> "Удалить",
		"LINK"	=> "javascript:if(confirm('Подтверите удаление')) window.location='/bitrix/admin/medi.regions.php?ID=".urlencode(urlencode($str_ID))."&lang=".LANGUAGE_ID."&action=delete&".bitrix_sessid_get()."';",
		"TITLE"	=> "Удалить",
		"ICON"	=> "btn_delete"
    );*/
}

$context = new CAdminContextMenu($aMenu);
$context->Show();

if ($e = $APPLICATION->GetException())
	$message = new CAdminMessage(GetMessage("MAIN_ERROR_SAVING"), $e);

if($message)
	echo $message->Show();

?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="bform" >
<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?echo LANG?>">
<?if($bNew):?>
<input type="hidden" name="new" value="Y">
<?endif?>
<?if($COPY_ID <> ''):?>
<input type="hidden" name="COPY" value="<?echo htmlspecialcharsbx($COPY_ID)?>">
<?endif?>
<?if($ID > 0 ):?>
<input type="hidden" name="ID" value="<?echo intval($ID)?>">
<?endif?>
<?
$tabControl->Begin();
$tabControl->BeginNextTab();

$dbPriceType = CCatalogGroup::GetList(
        array("SORT" => "ASC"),
        array()
    );
	$prices = [];
while ($arPriceType = $dbPriceType->Fetch())
{
    $prices[] = $arPriceType;
}
?>
	<tr class="adm-detail-required-field">
		<td><label for="ACTIVE">Активность</label></td>
		<td><input type="checkbox" name="ACTIVE" value="Y" id="ACTIVE"<?if($str_ACTIVE=="Y")echo " checked"?>></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td>Город:</td>
		<td><input type="text" name="CITY" size="30" value="<? echo $str_CITY?>"></td>
	</tr>
    <tr class="adm-detail-required-field">
        <td>Регион:</td>
        <td><input type="text" name="REGION" size="30" value="<? echo $str_REGION?>"></td>
    </tr>

	<tr class="adm-detail-required-field">
		<td>Телефон:</td>
		<td><input type="text" name="PHONE" size="30" value="<? echo $str_PHONE?>"></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td>Директория сайта</td>
		<td><input type="text" name="DIR" size="30" value="<? echo $str_DIR?>"></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td>Сортировка</td>
		<td><input type="text" name="SORT" size="10" value="<? echo $str_SORT?>"></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td>Базовая цена</td>
		<td><select  name="PRICE">
			<?foreach($prices AS $k=>$val){?>
			<option <?=( $val['NAME'] ==  $str_PRICE ? 'selected="selected"' : '')?> value="<?=$val['NAME']?>"><?=$val['NAME']?></option>
			<?}?>
			</select>
		</td>
	</tr>
	<tr class="adm-detail-required-field">
		<td>Максимальная цена</td>
		<td><select  name="MAX_PRICE">
			<?foreach($prices AS $k=>$val){?>
			<option <?=( $val['NAME'] ==  $str_MAX_PRICE ? 'selected="selected"' : '')?> value="<?=$val['NAME']?>"><?=$val['NAME']?></option>
			<?}?>
			</select>
		</td>
	</tr>

	<tr class="adm-detail-required-field">
		<td>SITE ID:</td>
		<td><input type="text" name="SITE_ID" size="30" value="<? echo $str_SITE_ID?>"></td>
	</tr>

<?$tabControl->Buttons(array("disabled" => !$isAdmin, "back_url"=>"medi.regions.php?lang=".LANGUAGE_ID));
$tabControl->End();
$tabControl->ShowWarnings("bform", $message);
?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");