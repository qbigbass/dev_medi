<?
if (  SITE_ID == 's1')
{
	$addurl = '';
}
else {

	$addurl = "/".$GLOBALS['medi']['sfolder'][SITE_ID];
}
?>
<a href="<?=$addurl?>/delivery/" class="menu_delivery_icon">Доставка</a><a href="<?=$addurl?>/salons/" class="menu_salons_icon">Адреса салонов</a>
