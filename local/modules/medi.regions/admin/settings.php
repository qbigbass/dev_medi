<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");

use Bitrix\Main;


if(!$USER->CanDoOperation('edit_other_settings') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$isAdmin = $USER->CanDoOperation('edit_other_settings');


IncludeModuleLangFile(__FILE__);

$sTableID = "tbl_region";

$oSort = new CAdminSorting($sTableID, "SORT", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);


$APPLICATION->SetTitle("Регионы");

$regions = $DB->query("SELECT * FROM medi_regions");

$dbPriceType = CCatalogGroup::GetList(
        array("SORT" => "ASC"),
        array()
    );
	$prices = [];
while ($arPriceType = $dbPriceType->Fetch())
{
    $prices[$arPriceType['NAME']] = $arPriceType;
}

$rsData = new CAdminResult($regions, $sTableID);
$rsData->NavStart();

$lAdmin->NavText($rsData->GetNavPrint(GetMessage("PAGES"), false));

$lAdmin->AddHeaders(array(
	array("id"=>"ID", "content"=>"ID", "sort"=>"id", "default"=>true),
	array("id"=>"ACTIVE", "content"=>"Активность", "sort"=>"ACTIVE", "default"=>true),
	array("id"=>"SORT", "content"=>'Сорт.', "sort"=>"sort", "default"=>true),
	array("id"=>"CITY", "content"=>'Город', "sort"=>"city", "default"=>true),
	array("id"=>"REGION",	"content"=>"Регион", "sort"=>"region",	"default"=>true),
	array("id"=>"PHONE", "content"=>'Телефон', "sort"=>"phone", "default"=>true),
	array("id"=>"DIR", "content"=>'Директория сайта', "sort"=>"dir", "default"=>true),
	array("id"=>"PRICE", "content"=>"Тип цены", "sort"=>"price", "default"=>true),
	array("id"=>"SITE_ID", "content"=>"SITE_ID", "sort"=>"site_id", "default"=>true),
));
while($arRes = $rsData->NavNext(true, "f_"))
{
	$row =& $lAdmin->AddRow($f_ID, $arRes);
	$row->AddViewField("ID", '<a href="medi.regions_edit.php?lang='.LANGUAGE_ID.'&amp;ID='.urlencode($arRes['ID']).'" title="Изменить">'.$f_ID.'</a>');
	$row->AddViewField("SITE_ID", $arRes['SITE_ID']);
	$row->AddViewField("SORT", $arRes['SORT']);
	//$row->AddCheckField("ACTIVE");
	$row->AddViewField("ACTIVE", $f_ACTIVE=="Y"?"Да":"Нет");
 	//$row->AddEditField("ACTIVE", "<b>".($f_ACTIVE=="Y"? "Да":"Нет")."</b>");

    $row->AddViewField("PHONE", $arRes['PHONE']);
    $row->AddViewField("DIR", $arRes['DIR']);
	$row->AddViewField("CITY", $arRes['CITY']);
    $row->AddViewField("PRICE", $prices[$arRes['PRICE']]['NAME']);
	$row->AddViewField("REGION", $arRes['REGION']);
	$arActions = Array();

	$arActions[] = array("ICON"=>"edit", "TEXT"=>"Редактировать", "ACTION"=>$lAdmin->ActionRedirect("medi.regions_edit.php?ID=".urlencode($arRes['ID'])), "DEFAULT"=>true);


	$row->AddActions($arActions);
}


$aContext = array(
	array(
		"TEXT"	=> "Добавить",
		"LINK"	=> "medi.regions_edit.php?lang=".LANGUAGE_ID,
		"TITLE"	=> "Добавить",
		"ICON"	=> "btn_new"
	),
);
$lAdmin->AddAdminContextMenu($aContext);
//$lAdmin->CheckListMode();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$lAdmin->DisplayList();
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
