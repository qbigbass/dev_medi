<?	global $APPLICATION;
	CJSCore::Init(array("jquery"));
	$widget_url = COption::GetOptionString('up.boxberrydelivery_spb', 'WIDGET_URL');
	$APPLICATION->AddHeadScript($widget_url);
?>

<div id="boxberry_spb_widget"></div>

<script>
	boxberry_spb.openOnPage('boxberry_spb_widget');
	boxberry_spb.open();
</script>
