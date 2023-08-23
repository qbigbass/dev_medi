<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

IncludeTemplateLangFile(__FILE__);
?>
<!DOCTYPE html>
<html lang="ru">
    <head>

        <base href="<?= (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://" ?><?=$_SERVER['SERVER_NAME'];?>/">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1">
        <meta http-equiv='Content-Language' content='ru' />

        <? $APPLICATION->AddHeadString('<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&subset=cyrillic" rel="stylesheet">'); ?>

        <? // Small adaptive css framework + Normalize ?>
        <? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/skeleton/normalize.css'); ?>
        <? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/skeleton/skeleton.css'); ?>
        <? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/fonts/fontello/css/fontello.css'); ?>

        <? // Железобетонное подключение jquery ?>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script type="text/javascript">
            if (typeof jQuery === 'undefined') {
                document.write(unescape("%3Cscript src='<?= SITE_TEMPLATE_PATH; ?>/js/jquery/1.11.0/jquery.min.js' type='text/javascript'%3E%3C/script%3E"));
            }
        </script>

        <? // Выбор времени и даты во всплывающем окне ?>
        <? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/js/pickadate/themes/default.css'); ?>
        <? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/js/pickadate/themes/default.date.css'); ?>
        <? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/js/pickadate/themes/default.time.css'); ?>
        <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/pickadate/picker.js'); ?>
        <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/pickadate/picker.date.js'); ?>
        <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/pickadate/picker.time.js'); ?>
        <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/pickadate/legacy.js'); ?>
        <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/pickadate/translations/ru_RU.js'); ?>

        <? // Авто ресайз текстовых полей ?>
        <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/autosize.min.js'); ?>

        <? // Боковое меню mobile-like ?>
        <? $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/js/sidr/jquery.sidr.light.css'); ?>
        <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/sidr/jquery.sidr.min.js'); ?>
        <? $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/menu.js'); ?>

        <? CJSCore::Init(); ?>
        <? $APPLICATION->ShowHead(); ?>
        <title><? $APPLICATION->ShowTitle(); ?></title>
        
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="/manifest.json">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="theme-color" content="#ffffff">
        
    </head>
    <body>
        <div id="panel">
            <? // $APPLICATION->ShowPanel(); ?>
        </div>
        <header>
            <div class="container">
                <div class="header">
                    <div class="holder">
                        <a id="main-menu" href="#sidr" class="header__menu cursor__pointer"></a>
                    </div>
                    <div class="row">
                        <div class="eleven columns ">
                                <img class="header__logo u-pull-left" src="<?= SITE_TEMPLATE_PATH; ?>/images/medi-logo.png">
                            <h3 class="header__title u-pull-left"><? $APPLICATION->ShowTitle(); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main>