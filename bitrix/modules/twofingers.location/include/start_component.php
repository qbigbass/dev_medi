<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * @var array $arParams
 * @var CMain $APPLICATION
 */
CJSCore::Init();

use Bitrix\Main\Page\Asset;
use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();

if($request->isAdminSection())
{
    // include original template for backoffice
    $componentName          = str_replace('bitrix:', '', $this->__component->__name);
    $originalTemplateDir    = "/bitrix/components/bitrix/$componentName/templates/.default/";
    $originalTemplateDirAbs = $_SERVER['DOCUMENT_ROOT'] . $originalTemplateDir;

    Asset::getInstance()->addJs($originalTemplateDir . 'script.js');
    $APPLICATION->SetAdditionalCSS($originalTemplateDir . 'style.css');

    foreach(['result_modifier.php', 'template.php', 'component_epilog.php'] as $fileName )
    {
        $originalTemplateFileAbs = $originalTemplateDirAbs . $fileName;

        if(is_file($originalTemplateFileAbs))
            require $originalTemplateFileAbs;
    }
}
else
{
    if( !empty($arParams['CITY_INPUT_NAME']) )
        $arParams['INPUT_NAME'] = $arParams['CITY_INPUT_NAME'];

    $APPLICATION->IncludeComponent("twofingers:location",
        \Bitrix\Main\Config\Option::get('twofingers.location', 'order-template', '.default'),
        array(
        'ORDER_TEMPLATE'=> 'Y',
        'PARAMS'        => $arParams)
    );
}
