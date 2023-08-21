<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

$arComponentParameters = array(
	"GROUPS"     => array(),
	"PARAMETERS" => array(
		"SHOP_ID" => array(
			"PARENT"   => "BASE",
			"NAME"     => GetMessage("VAMPIRUS_YOOKASSA.CREDIT_PARAM_SHOP_ID"),
			"TYPE"     => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT"  => "",
		),
		"PRICE" => array(
			"PARENT"   => "BASE",
			"NAME"     => GetMessage("VAMPIRUS_YOOKASSA.CREDIT_PARAM_PRICE"),
			"TYPE"     => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT"  => '={$price["RATIO_PRICE"]}',
		),
		"OB_NAME" => array(
			"PARENT"   => "BASE",
			"NAME"     => GetMessage("VAMPIRUS_YOOKASSA.CREDIT_PARAM_OB_NAME"),
			"TYPE"     => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT"  => '={$obName}',
		),
	),
);
