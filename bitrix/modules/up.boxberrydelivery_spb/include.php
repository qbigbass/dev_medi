<?	global $APPLICATION;
	global $arOptions;
	IncludeModuleLangFile(__FILE__);
	CModule::IncludeModule('sale');
	require_once(__DIR__ .'/classes/general/boxberry_spb.php');
	require_once(__DIR__ .'/classes/general/boxberry_parsel_spb.php');
	require_once(__DIR__ .'/classes/general/delivery_boxberry_spb.php');
	require_once(__DIR__ .'/classes/mysql/boxberry_order_spb.php');
?>
