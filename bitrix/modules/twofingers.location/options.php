<?
/**
 * @var string $mid
 */
use Bitrix\Main\SiteTable;
use TwoFingers\Location\Helper\Tabs;
use TwoFingers\Location\Model\Location;
use TwoFingers\Location\Settings;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use \Bitrix\Main\SystemException;
use Bitrix\Main\Application;
use TwoFingers\Location\Service\SxGeo;

if (!Loader::includeModule($mid))
    throw new SystemException('module ' . $mid . ' not found');

if (!Loader::includeModule('iblock'))
    throw new SystemException('module iblock not found');

Loc::loadMessages(__FILE__);

$request = Application::getInstance()->getContext()->getRequest();

if ($request->get('update-sx'))
{
    try{
        $message = [
            'TYPE'      => 'OK',
            "MESSAGE"   => SxGeo::run()
        ];
    } catch (\Exception $e) {
        $message = [
            'TYPE' => 'ERROR',
            "MESSAGE"   => $e->getMessage()
        ];
    }

    \CAdminMessage::ShowMessage($message);
}

if (($request->getPost('save') || $request->getPost('apply')) && check_bitrix_sessid())
{
    Settings::SetList($request->toArray());

    if ($request->getPost('TF_LOCATION_TEMPLATE') == 'Y') {
        CopyDirFiles(
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/".$mid."/install/location/",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/.default/components",
            true, true
        );
    } else {
        DeleteDirFilesEx("/bitrix/templates/.default/components/bitrix/sale.ajax.locations/");
        DeleteDirFilesEx("/bitrix/templates/.default/components/bitrix/sale.location.selector.search/");
        DeleteDirFilesEx("/bitrix/templates/.default/components/bitrix/sale.location.selector.steps/");
    }
}

CJSCore::Init(array("jquery"));
$settings   = Settings::getList();
$sitesDB    = SiteTable::getList(['order' =>['SORT' => 'asc'], 'select' => ['LID', 'NAME']]);
$sites      = [];
while ($site = $sitesDB->fetch())
    $sites[$site['LID']] = $site['NAME'];

$aTabs = Tabs::getMap();

?><style>
    .tfl__cities {
        list-style: none outside none;
        margin: 0;
        padding: 0;
    }
    .tfl__cities li {
        margin-bottom: 3px;
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
    #LOCATION_tmp > select {
        display: block;
        margin-bottom: 5px;
    }
    .bx-ui-slst-pool .bx-ui-slst-input-block:nth-child(4){
        display: none;
    }
    .tfl-help{
        color: #999;
        font-size: 0.85em;
        margin-top: 0.3em
    }

    .tfl-help ul{
        margin-top: 0.3em;
        margin-bottom: 0.6em;
    }

    .tfl-options .adm-detail-content-cell-l{
        vertical-align: top;
    }

    .tfl-options .adm-detail-content-cell-l.adm-detail-content-text{
        padding-top: 0.8em
    }

    .tfl__subsettings-table{

    }
    .tfl__subsettings-table th,
    .tfl__subsettings-table td{
        border-top: 1px dotted #ccc;
        border-right: 1px dotted #ccc;
        padding: 5px 4px 7px 4px;
    }
    .tfl__subsettings-table th:last-child,
    .tfl__subsettings-table td:last-child{
        border-right: none;
    }
    .tfl__subsettings-table tr:last-child th,
    .tfl__subsettings-table tr:last-child td{
        border-bottom: none;
    }
</style>
<form method="post" class="tfl-options"><?php
    echo bitrix_sessid_post();

    $allOptions = Tabs::getAllOptions();

    $tabControl = new \CAdminTabControl('TwoFingersLocation', $aTabs);
    $tabControl->Begin();

    require __DIR__ . '/options__tab-main.php';
    require __DIR__ . '/options__tab-select.php';
    require __DIR__ . '/options__tab-confirm.php';

    if (Location::getType() != Location::TYPE__INTERNAL)
    {
        $tabControl->BeginNextTab();
        __AdmSettingsDrawList($mid, $allOptions[Tabs::SALE]);
    }

    $tabControl->Buttons([]);
    $tabControl->End();?>
</form>