<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("QS_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("QS_COMPONENT_DESCR"),
	"ICON" => "/images/icon.gif",
	"COMPLEX" => "N",
	"SORT" => 10,
	"PATH" => array(
		"ID" => "scoder",
		"NAME" => GetMessage("QS_COMPONENT_PATH"),
		"CHILD" => array(
			"ID" => "scoder_modalcoupon",
			"NAME" => GetMessage("QS_COMPONENT_GROUP_NAME"),
		),
	),
);
?>