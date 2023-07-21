<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install.php"));

Class vampirus_yandexkassa extends CModule
{
	var $MODULE_ID = "vampirus.yandexkassa";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";
	var $PARTNER_NAME;
	var $PARTNER_URI;

	function __construct()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
		else
		{
			$this->MODULE_VERSION = CURRENCY_VERSION;
			$this->MODULE_VERSION_DATE = CURRENCY_VERSION_DATE;
		}

		$this->PARTNER_URI  = "https://shop.vampirus.ru";
		$this->PARTNER_NAME = GetMessage("VAMPIRUS.YANDEXKASSA_PARTNER_NAME");
		$this->MODULE_NAME = GetMessage("VAMPIRUS.YANDEXKASSA_INSTALL_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("VAMPIRUS.YANDEXKASSA_INSTALL_DESCRIPTION");
	}

	function DoInstall()
	{
		global $APPLICATION, $step;
		$GLOBALS["errors"] = false;
			$this->InstallFiles();
			$this->InstallDB();
			$GLOBALS["errors"] = $this->errors;

	}

	function DoUninstall()
	{
		global $APPLICATION, $step;
		$this->UnInstallFiles();
		$this->UnInstallDB();
		UnRegisterModule("vampirus.yandexkassa");
	}

	function InstallDB()
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = false;
		RegisterModule("vampirus.yandexkassa");
		if(!$DB->Query("SELECT 'x' FROM vampirus_yandexkassa WHERE 1=0", true)){
            $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/vampirus.yandexkassa/install/db/".$DBType."/install.sql");
        }
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->registerEventHandler("sale", "OnSaleOrderSaved", $this->MODULE_ID, 'CVampiRUSYandexKassaPayment', 'OnSaleOrderSavedHandler');
        $eventManager->registerEventHandler("sale", "OnSaleStatusOrderChange", $this->MODULE_ID, 'CVampiRUSYandexKassaPayment', 'OnSaleStatusOrderChangeHandler');
        $eventManager->registerEventHandler("sale", "OnSalePaymentEntitySaved", $this->MODULE_ID, 'CVampiRUSYandexKassaPayment', 'OnSalePaymentEntitySavedHandler');
		$eventManager->registerEventHandler("sale", "onSaleAdminOrderInfoBlockShow", $this->MODULE_ID, 'CVampiRUSYandexKassaPayment', 'onSaleAdminOrderInfoBlockShowHandler');
		$eventManager->registerEventHandler("sale", "OnOrderNewSendEmail", $this->MODULE_ID, 'CVampiRUSYandexKassaPayment', 'onNewOrderEmail');
		$eventManager->registerEventHandler("sale", "OnOrderStatusSendEmail", $this->MODULE_ID, 'CVampiRUSYandexKassaPayment', 'onOrderStatusSendEmail');
		return true;
	}

	function UnInstallDB($arParams = array())
	{
		global $DB, $DBType, $APPLICATION;
		$this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/vampirus.yandexkassa/install/db/".$DBType."/uninstall.sql");
		$eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->unRegisterEventHandler("sale", "OnSaleOrderSaved", $this->MODULE_ID, 'CVampiRUSYandexKassaPayment', 'OnSaleOrderSavedHandler');
        $eventManager->unRegisterEventHandler("sale", "OnSaleStatusOrderChange", $this->MODULE_ID, 'CVampiRUSYandexKassaPayment', 'OnSaleStatusOrderChangeHandler');
        $eventManager->unRegisterEventHandler("sale", "OnSalePaymentEntitySaved", $this->MODULE_ID, 'CVampiRUSYandexKassaPayment', 'OnSalePaymentEntitySavedHandler');
		$eventManager->unRegisterEventHandler("sale", "onSaleAdminOrderInfoBlockShow", $this->MODULE_ID, 'CVampiRUSYandexKassaPayment', 'onSaleAdminOrderInfoBlockShowHandler');
		$eventManager->unRegisterEventHandler("sale", "OnOrderNewSendEmail", $this->MODULE_ID, 'CVampiRUSYandexKassaPayment', 'onNewOrderEmail');
		$eventManager->unRegisterEventHandler("sale", "OnOrderStatusSendEmail", $this->MODULE_ID, 'CVampiRUSYandexKassaPayment', 'onOrderStatusSendEmail');
		return true;
	}


	function InstallFiles()
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/vampirus.yandexkassa/install/sale_payment/vampirus.yandexkassa/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_payment/vampirus.yandexkassa/");
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/vampirus.yandexkassa/install/sale_payment/yandexcheckoutvs/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_payment/yandexcheckoutvs/");
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/vampirus.yandexkassa/install/sale_payment/yandexcheckoutvs/template", $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_payment/yandexcheckoutvs/template");
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/vampirus.yandexkassa/install/tools/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/");
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes/.default", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes/.default/icons", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/icons", true, true);
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin/vampirus_yandexkassa.php", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/vampirus_yandexkassa.php");
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/admin/vampirus_yandexkassa_new.php", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/vampirus_yandexkassa_new.php");
        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/vampirus.yandexkassa/install/components/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/", true, true);
		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFilesEx("/bitrix/admin/vampirus_yandexkassa.php");
		DeleteDirFilesEx("/bitrix/admin/vampirus_yandexkassa_new.php");
		DeleteDirFilesEx("/bitrix/php_interface/include/sale_payment/vampirus.yandexkassa");
		DeleteDirFilesEx("/bitrix/php_interface/include/sale_payment/yandexcheckoutvs");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/vampirus.yandexkassa/install/tools/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes/.default", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");
        DeleteDirFilesEx( "/bitrix/themes/.default/icons/vampirus.yandexkassa");
        DeleteDirFilesEx( "/bitrix/components/vampirus/yookassa.credit");
		return true;
	}

}
?>