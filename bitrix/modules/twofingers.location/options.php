<?
/**
 * @var string $mid
 */

use Bitrix\Main\Config\Option;
use Bitrix\Main\SiteTable;
use TwoFingers\Location\Helper\Tabs;
use TwoFingers\Location\Model\Iblock\Content;
use TwoFingers\Location\Model\Iblock\Domain;
use TwoFingers\Location\Model\Location;
use TwoFingers\Location\Options;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use Bitrix\Main\Application;
use TwoFingers\Location\Service\SxGeo;

if (!Loader::includeModule($mid)) {
    throw new SystemException('module ' . $mid . ' not found');
}

if (!Loader::includeModule('iblock')) {
    throw new SystemException('module iblock not found');
}

Loc::loadMessages(__FILE__);

$request = Application::getInstance()->getContext()->getRequest();

if ($request->get('update-sx')) {
    try {
        $message = [
            'TYPE'    => 'OK',
            "MESSAGE" => SxGeo::updateGeoBase()
        ];
    } catch (\Exception $e) {
        $message = [
            'TYPE'    => 'ERROR',
            "MESSAGE" => $e->getMessage()
        ];
    }

    \CAdminMessage::ShowMessage($message);
}

if (($request->getPost('save') || $request->getPost('apply')) && check_bitrix_sessid()) {
    Options::setList($request->toArray());

    if ($request->getPost(Options::ORDER_SET_TEMPLATE) == 'Y') {
        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $mid . "/install/location/",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/.default/components",
            true, true
        );
    }
    else {
        DeleteDirFilesEx("/bitrix/templates/.default/components/bitrix/sale.ajax.locations/");
        DeleteDirFilesEx("/bitrix/templates/.default/components/bitrix/sale.location.selector.search/");
        DeleteDirFilesEx("/bitrix/templates/.default/components/bitrix/sale.location.selector.steps/");
    }
}

CJSCore::Init(["jquery"]);
$sitesDB = SiteTable::getList(['order' => ['SORT' => 'asc'], 'select' => ['LID', 'NAME']]);
$sites   = [];
while ($site = $sitesDB->fetch()) {
    $sites[$site['LID']] = $site['NAME'];
}

$aTabs = Tabs::getMap();

?>
<style>
    .tfl__cities {
        list-style: none outside none;
        margin: 0;
        padding: 0;
    }

    .tfl__cities li {
        margin-bottom: 5px;
    }

    .tfl__cities li i {
        background: url("/bitrix/panel/main/images/popup_menu_sprite_2.png") no-repeat scroll -8px -787px rgba(0, 0, 0, 0);
        cursor: pointer;
        display: inline-block;
        height: 15px;
        margin-bottom: -2px;
        margin-left: 5px;
        position: relative;
        width: 15px;
    }

    .tfl-help {
        color: gray;
        font-size: 0.85em;
        margin-top: 0.3em
    }

    .tfl-help ul {
        margin-top: 0.3em;
        margin-bottom: 0.6em;
    }

    .tfl-options .adm-detail-content-cell-l {
        vertical-align: top;
    }

    .tfl-options .adm-detail-content-cell-l.adm-detail-content-text {
        padding-top: 0.8em
    }

    .tfl__subsettings-table th,
    .tfl__subsettings-table td {
        border-bottom: 1px dotted #ccc;
        border-right: 1px dotted #ccc;
        padding: 5px 4px 7px 4px;
    }

    .tfl__subsettings-table th:last-child,
    .tfl__subsettings-table td:last-child {
        border-right: none;
    }

    .tfl__subsettings-table tr:last-child th,
    .tfl__subsettings-table tr:last-child td {
        border-bottom: none;
    }

    .tfl__redirect-rule .adm-designed-checkbox-label {
        width: auto !important;
        padding-left: 20px;
    }
</style>
<?php

$buttons = [
    [
        'TEXT'  => Loc::getMessage('tfl__cm-domains'),
        'LINK'  => '/bitrix/admin/iblock_list_admin.php?lang=' . LANGUAGE_ID . '&type=tf_location&IBLOCK_ID=' . Domain::getId(),
        'TITLE' => Loc::getMessage('tfl__cm-domains-title')
    ],
    [
        'TEXT'  => Loc::getMessage('tfl__cm-content'),
        'LINK'  => '/bitrix/admin/iblock_list_admin.php?lang=' . LANGUAGE_ID . '&type=tf_location&IBLOCK_ID=' . Content::getId(),
        'TITLE' => Loc::getMessage('tfl__cm-content-title')
    ],
];

if (Location::getType() == Location::TYPE_IBLOCK) {
    $buttons[] = [
        'TEXT'  => Loc::getMessage('tfl__cm-locations'),
        'LINK'  => '/bitrix/admin/iblock_list_admin.php?lang=' . LANGUAGE_ID . '&type=tf_location&IBLOCK_ID=' . \TwoFingers\Location\Model\Iblock\Location::getId(),
        'TITLE' => Loc::getMessage('tfl__cm-locations-title')
    ];
}

(new \CAdminContextMenu($buttons))->Show();

if (Option::get('twofingers.location', 'import-complete', 'Y') == 'N'):

?><p><b>Импорт местоположений</b>: <?=Option::get('twofingers.location', 'import-count', 0)?> из 56704</p><?php

endif;?>
<form method="post" class="tfl-options"><?php
    echo bitrix_sessid_post();

    $allOptions = Tabs::getAllOptions();

    $tabControl = new \CAdminTabControl('TwoFingersLocation', $aTabs);
    $tabControl->Begin();

    $tabControl->BeginNextTab();
    require __DIR__ . '/options__tab-main.php';

    $tabControl->BeginNextTab();
    require __DIR__ . '/options__tab-select.php';

    $tabControl->BeginNextTab();
    require __DIR__ . '/options__tab-confirm.php';

    if (Location::getType() != Location::TYPE_IBLOCK) {
        $tabControl->BeginNextTab();
        __AdmSettingsDrawList($mid, $allOptions[Tabs::SALE]);
    }

    $tabControl->BeginNextTab();
    __AdmSettingsDrawList($mid, $allOptions[Tabs::SETTINGS]);

    $tabControl->Buttons([]);
    $tabControl->End(); ?>
</form>
<script>
    BX.hint_replace(BX('redirect_event'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::REDIRECT_EVENT . '_hint')); ?>');

    BX.hint_replace(BX('list_reload_page'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::LIST_RELOAD_PAGE . '_hint')); ?>');
    BX.hint_replace(BX('list_show_villages'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::LIST_SHOW_VILLAGES . '_hint')); ?>');
    BX.hint_replace(BX('list_locations_load'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::LIST_LOCATIONS_LOAD . '_hint')); ?>');
    BX.hint_replace(BX('list_title_font_family'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::LIST_TITLE_FONT_FAMILY . '_hint')); ?>');
    BX.hint_replace(BX('list_items_font_family'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::LIST_ITEMS_FONT_FAMILY . '_hint')); ?>');

    BX.hint_replace(BX('order_set_template'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::ORDER_SET_TEMPLATE . '_hint')); ?>');
    BX.hint_replace(BX('order_set_zip'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::ORDER_SET_ZIP . '_hint')); ?>');

    BX.hint_replace(BX('cookie_lifetime'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::COOKIE_LIFETIME . '_hint')); ?>');
    BX.hint_replace(BX('include_jquery'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::INCLUDE_JQUERY . '_hint')); ?>');
    BX.hint_replace(BX('replace_placeholders'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::REPLACE_PLACEHOLDERS . '_hint')); ?>');
    BX.hint_replace(BX('callback'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::CALLBACK . '_hint')); ?>');
    BX.hint_replace(BX('use_google_fonts'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::USE_GOOGLE_FONTS . '_hint')); ?>');
    BX.hint_replace(BX('locations_limit'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::LOCATIONS_LIMIT . '_hint')); ?>');
    BX.hint_replace(BX('search_limit'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::SEARCH_LIMIT . '_hint')); ?>');
    BX.hint_replace(BX('sx_geo_memory'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::SX_GEO_MEMORY . '_hint')); ?>');
    BX.hint_replace(BX('capability_mode'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::CAPABILITY_MODE . '_hint')); ?>');
    BX.hint_replace(BX('sx_geo_agent_update'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::SX_GEO_AGENT_UPDATE . '_hint')); ?>');
    BX.hint_replace(BX('sx_geo_proxy_port'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::SX_GEO_PROXY_PORT . '_hint')); ?>');
    BX.hint_replace(BX('sx_geo_proxy_pass'), '<?=CUtil::JSEscape(Loc::getMessage('tfl__' . Options::SX_GEO_PROXY_PASS . '_hint')); ?>');
</script>