<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
$arThemesList = array(
	'blue' => GetMessage('CP_BCS_TPL_THEME_BLUE'),
	'green' => GetMessage('CP_BCS_TPL_THEME_GREEN'),
	'red' => GetMessage('CP_BCS_TPL_THEME_RED'),
	'yellow' => GetMessage('CP_BCS_TPL_THEME_YELLOW'),
	'black' => GetMessage('CP_BCS_TPL_THEME_BLACK'),
	'orange' => GetMessage('CP_BCS_TPL_THEME_ORANGE'),
	'purple' => GetMessage('CP_BCS_TPL_THEME_PURPLE'),
);
$dir = trim(preg_replace("'[\\\\/]+'", '/', dirname(__FILE__).'/themes/'));
if (is_dir($dir))
{
	foreach ($arThemesList as $themeID => $themeName)
	{
		if (!is_file($dir.$themeID.'/style.css'))
			continue;

		$arThemes[$themeID] = $themeName;
	}
}

$arTemplateParameters['TEMPLATE_THEME'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CP_BCS_TPL_TEMPLATE_THEME'),
	'TYPE' => 'LIST',
	'VALUES' => $arThemes,
	'DEFAULT' => 'blue',
	'ADDITIONAL_VALUES' => 'Y'
);

$arTemplateParameters['HEADER_TEXT'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('HEADER_TEXT'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('HEADER_TEXT_DEFAULT'),
	'ADDITIONAL_VALUES' => 'Y'
);		
$arTemplateParameters['MODAL_DESCRIPTION'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('MODAL_DESCRIPTION'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('MODAL_DESCRIPTION_DEFAULT'),
	'ADDITIONAL_VALUES' => 'Y'
);
$arTemplateParameters['MODAL_BUTTON'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('MODAL_BUTTON'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('MODAL_BUTTON_DEFAULT'),
	'ADDITIONAL_VALUES' => 'Y'
);
$arTemplateParameters['FOOTER_TEXT'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('FOOTER_TEXT'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('FOOTER_TEXT_DEFAULT'),
	'ADDITIONAL_VALUES' => 'Y'
);
$arTemplateParameters['LEFT_LINK'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('LEFT_LINK'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('LEFT_LINK_DEFAULT'),
	'ADDITIONAL_VALUES' => 'Y'
);
$arTemplateParameters['LEFT_TEXT'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('LEFT_TEXT'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('LEFT_TEXT_DEFAULT'),
	'ADDITIONAL_VALUES' => 'Y'
);
$arTemplateParameters['RIGHT_LINK'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('RIGHT_LINK'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('RIGHT_LINK_DEFAULT'),
	'ADDITIONAL_VALUES' => 'Y'
);
$arTemplateParameters['RIGHT_TEXT'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('RIGHT_TEXT'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('RIGHT_TEXT_DEFAULT'),
	'ADDITIONAL_VALUES' => 'Y'
);
$arTemplateParameters['RESULT_TITLE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('RESULT_TITLE'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('RESULT_TITLE_DEFAULT'),
	'ADDITIONAL_VALUES' => 'Y'
);
$arTemplateParameters['RESULT_TEXT'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('RESULT_TEXT'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('RESULT_TEXT_DEFAULT'),
	'ADDITIONAL_VALUES' => 'Y'
);
$arTemplateParameters['RESULT_BUTTON'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('RESULT_BUTTON'),
	'TYPE' => 'STRING',
	'DEFAULT' => GetMessage('RESULT_BUTTON_DEFAULT'),
	'ADDITIONAL_VALUES' => 'Y'
);
$arTemplateParameters['IS_CLOSE_IS_CLICK_LINKS'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('IS_CLOSE_IS_CLICK_LINKS'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N',
	'ADDITIONAL_VALUES' => 'Y'
);
$arTemplateParameters['IS_RELOAD_WINDOW'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('IS_RELOAD_WINDOW'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N',
	'ADDITIONAL_VALUES' => 'Y'

);
$arTemplateParameters['AUTO_HIDE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('AUTO_HIDE'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N',
	'ADDITIONAL_VALUES' => 'Y'

);
?>