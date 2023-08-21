<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Loader; 

if (!Loader::includeModule("iblock"))
	return;
$arlibs = array(
	'jquery' => GetMessage('SCODER_LIB_JQUERY'),
	'ajax' => GetMessage('SCODER_LIB_AJAX'),
	'popup' => GetMessage('SCODER_LIB_POPUP'),
);
$arFields = array(
	'PERSONAL_PHONE' => GetMessage('ADDITIONAL_FIELD_PERSONAL_PHONE'),
);
$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
		'TIMEOUT' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('TIMEOUT'),
			'TYPE' => 'STRING',
			'DEFAULT' => 5,
		),
		'TIMECLOSE' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('TIMECLOSE'),
			'TYPE' => 'STRING',
			'DEFAULT' => 1800,
		),
		'LIBS' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('SCODER_LIBS'),
			'TYPE' => 'LIST',
			'ADDITIONAL_VALUES' => 'Y',
			'VALUES' => $arlibs,
			'MULTIPLE' => 'Y',
		),
		'ADDITIONAL_FIELDS' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('SCODER_ADDITIONAL_FIELDS'),
			'TYPE' => 'LIST',
			'VALUES' => $arFields,
			'MULTIPLE' => 'Y',
		),
		'USE_CAPTCHA' => array(
			'NAME' => GetMessage("USE_CAPTCHA"),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'BASE',
			'DEFAULT' => 'N',
			'ADDITIONAL_VALUES' => 'N',
		),
		'RELOAD_CAPTCHA' => array(
			'NAME' => GetMessage("RELOAD_CAPTCHA"),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'BASE',
			'DEFAULT' => 'N',
			'ADDITIONAL_VALUES' => 'N',
		),
		'NOT_SHOW_ICON_AFTER_CLOSE' => array(
			'NAME' => GetMessage("NOT_SHOW_ICON_AFTER_CLOSE"),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'BASE',
			'DEFAULT' => 'N',
			'ADDITIONAL_VALUES' => 'N',
		),
		'IS_CLOSED' => array(
			'NAME' => GetMessage("IS_CLOSED"),
			'TYPE' => 'CHECKBOX',
			'PARENT' => 'BASE',
			'DEFAULT' => 'N',
			'ADDITIONAL_VALUES' => 'N',
		),
		"USER_CONSENT" => array(),
	),
);
?>