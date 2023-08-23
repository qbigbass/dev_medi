<?
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main;
use TwoFingers\Location\Model\Iblock;
use TwoFingers\Location\Model\Iblock\Content;
use TwoFingers\Location\Model\Iblock\Domain;
use TwoFingers\Location\Model\Iblock\Location;
use TwoFingers\Location\Model\Location as LocationModel;

Loc::loadMessages(__FILE__);

/**
 * Class twofingers_location
 */
class twofingers_location extends CModule
{
	var $MODULE_ID = 'twofingers.location';
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;

    /**
     * twofingers_location constructor.
     */
	function __construct()
	{
		$arModuleVersion = array();

		include(__DIR__ . "/version.php");
		
		$this->MODULE_VERSION       = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE  = $arModuleVersion["VERSION_DATE"];
		
		$this->MODULE_NAME          = GetMessage('tf-location__install-name');
		$this->MODULE_DESCRIPTION   = GetMessage('tf-location__install-description');
		$this->PARTNER_NAME         = GetMessage("tf-location__partner");
		$this->PARTNER_URI          = GetMessage("tf-location__partner_uri");
	}

    /**
     * @throws Main\ArgumentException
     * @throws Main\ArgumentNullException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\LoaderException
     * @throws Main\ObjectPropertyException
     * @throws Main\SystemException
     * 
     */
	public function DoInstall()
	{
		$this->InstallDB();

		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/components/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID . "/install/location/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/templates/.default/components", true, true);

		self::includeClasses();
        if (LocationModel::getType() == LocationModel::TYPE__INTERNAL)
            Location::build();

		Domain::build();
        Content::build();

        Main\ModuleManager::registerModule($this->MODULE_ID);
        $this->InstallEvents();

		LocalRedirect('/bitrix/admin/settings.php?lang=ru&mid=twofingers.location&mid_menu=1');
	}

    /**
     *
     */
	protected static function includeClasses()
    {
        include_once __DIR__ . '/../lib/model/location.php';
        include_once __DIR__ . '/../lib/model/location/sale.php';
        include_once __DIR__ . '/../lib/model/location/sale2.php';
        include_once __DIR__ . '/../lib/model/location/internal.php';
        include_once __DIR__ . '/../lib/helper/tools.php';
        include_once __DIR__ . '/../lib/settings.php';
        include_once __DIR__ . '/../lib/options.php';
        include_once __DIR__ . '/../lib/model/iblock.php';
        include_once __DIR__ . '/../lib/model/iblock/content.php';
        include_once __DIR__ . '/../lib/model/iblock/domain.php';
        include_once __DIR__ . '/../lib/model/iblock/location.php';
        include_once __DIR__ . '/../lib/property/site.php';
        include_once __DIR__ . '/../lib/property/location.php';
        include_once __DIR__ . '/../lib/entity/location.php';
        include_once __DIR__ . '/../lib/entity/content.php';
    }

    /**
     *
     */
    public function InstallEvents()
    {
        parent::InstallEvents();

        $eventManager = Main\EventManager::getInstance();
        $eventManager->registerEventHandler('main', 'OnBeforeProlog', $this->MODULE_ID, '\TwoFingersLocation', 'onBeforePrologHandler');
        $eventManager->registerEventHandler('iblock', 'OnAfterIBlockElementAdd', $this->MODULE_ID, '\TwoFingersLocation', 'onAfterIBlockElementUpdateHandler');
        $eventManager->registerEventHandler('iblock', 'OnAfterIBlockElementUpdate', $this->MODULE_ID, '\TwoFingersLocation', 'onAfterIBlockElementUpdateHandler');
        $eventManager->registerEventHandler("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, "\TwoFingers\Location\Property\Site", "GetUserTypeDescription");
    }

    /**
     *
     */
    public function UnInstallEvents()
    {
        parent::UnInstallEvents();

        $eventManager = Main\EventManager::getInstance();
        $eventManager->unRegisterEventHandler('main', 'OnBeforeProlog', $this->MODULE_ID, '\TwoFingersLocation', 'onBeforePrologHandler');
        $eventManager->unRegisterEventHandler('iblock', 'OnAfterIBlockElementAdd', $this->MODULE_ID, '\TwoFingersLocation', 'onAfterIBlockElementUpdateHandler');
        $eventManager->unRegisterEventHandler('iblock', 'OnAfterIBlockElementUpdate', $this->MODULE_ID, '\TwoFingersLocation', 'onAfterIBlockElementUpdateHandler');
        $eventManager->unRegisterEventHandler("iblock", "OnIBlockPropertyBuildList", $this->MODULE_ID, "\TwoFingers\Location\Property\Site", "GetUserTypeDescription");
    }

    /**
     * @return bool|void
     */
	public function InstallDB()
    {
	
	}

    /**
     *
     */
	public function DoUninstall()
	{
        global $APPLICATION, $step;
        $step = intval($step);

        self::includeClasses();

        if($step<2){
            $APPLICATION->IncludeAdminFile(Loc::getMessage("tf-location__uninstall_title"),
                dirname(__FILE__) . "/unstep.php");
        } else {

            $request = Main\Application::getInstance()->getContext()->getRequest();

            if ($request->get('saveiblock') != 'Y')
            {
                if (LocationModel::getType() == LocationModel::TYPE__INTERNAL)
                    Location::remove();

                Domain::remove();
                Content::remove();
                Iblock::removeType();
            }

            DeleteDirFilesEx("/bitrix/components/twofingers/location/");
            DeleteDirFilesEx("/bitrix/templates/.default/components/bitrix/sale.ajax.locations/tf_location/");
            DeleteDirFilesEx("/bitrix/templates/.default/components/bitrix/sale.location.selector.search/.default/");
            DeleteDirFilesEx("/bitrix/templates/.default/components/bitrix/sale.location.selector.steps/.default/");

            $this->UnInstallEvents();

            Main\ModuleManager::unRegisterModule($this->MODULE_ID);
        }
	}
}
?>