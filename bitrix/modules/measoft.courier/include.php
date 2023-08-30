<?php
/**
 * @copyright Copyright &copy; MEAsoft, 2014
 */

if(!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog")) {
    exit;
}

global $APPLICATION;
global $USER;
IncludeModuleLangFile(__FILE__);

use \Bitrix\Main\UI;

\Bitrix\Main\Loader::registerAutoLoadClasses(
    'measoft.courier',
    array(
        'MeasoftEvents'     => 'MeasoftEvents.php',
        'Measoft'           => 'classes/Measoft.php',
        'Errors'            => 'classes/Errors.php',
        'MeasoftSingleton'  => 'classes/MeasoftSingleton.php',
        'MeasoftLoghandler' => 'classes/MeasoftLoghandler.php',
        'Bitrix\Main\Engine\ActionFilter\CheckModulePermissions' => 'lib/checkmodulepermissions.php',
        'Bitrix\Main\Engine\ActionFilter\CheckGetStatusPermissions' => 'lib/checkgetstatuspermissions.php',
    )
);

try{
	$module = CModule::CreateModuleObject('measoft.courier');
	$version = $module->MODULE_VERSION;
}catch(\Exception $e){
	$version = time();
}

$fLoadMeasoftScript = true;

$request = \Bitrix\Main\Context::getCurrent()->getRequest();

if ( $request->isAdminSection() )
{
    if ($APPLICATION->GetCurPage() == "/bitrix/admin/sale_delivery_service_edit.php")
    {
        $APPLICATION->AddHeadScript("/bitrix/components/measoft.courier/js/measoft_settings.js?v=" . $version);
    }
    if ($APPLICATION->GetCurPage() == "/bitrix/admin/sale_order_view.php")
    {
        $orderId = $request->getQuery("ID");

        $fLoadMeasoftScript = MeasoftEvents::orderIsMeasoft($orderId);

        if ($fLoadMeasoftScript)
        {
            echo '
<script>
    var MEASOFT_ERROR_CALCEL_MESS = "' . GetMessage("MEASOFT_ERROR_CALCEL_MESS") . '";
    var MEASOFT_ERROR_ORDER_STATUS = "' . GetMessage("MEASOFT_ERROR_ORDER_STATUS") . '";
    var MEASOFT_DELIVERY_STATUS = "' . GetMessage("MEASOFT_DELIVERY_STATUS") . ' ";
    var MEASOFT_ERROR_CALCEL_MESS_AFTER = "' . GetMessage("MEASOFT_ERROR_CALCEL_MESS_AFTER") . '";
    var MEASOFT_DELIVERY_CHECK_AUTH = "' . GetMessage("MEASOFT_DELIVERY_CHECK_AUTH") . '";
    var MEASOFT_DELIVERY_CHECK_AUTH_TRUE = "' . GetMessage("MEASOFT_DELIVERY_CHECK_AUTH_TRUE") . '";
    var MEASOFT_DELIVERY_CHECK_AUTH_FALSE = "' . GetMessage("MEASOFT_DELIVERY_CHECK_AUTH_FALSE") . '";
</script>
';
            $APPLICATION->AddHeadScript("/bitrix/components/measoft.courier/js/script_admin.js?v=" . $version);
        }
    }
}


if ($fLoadMeasoftScript)
{
    if (CModule::IncludeModule("pull")) {
        UI\Extension::load("ui.notification");
        $tag = 'MEASOFT_PULL_ORDER_UPDATE';
        $user_id = $USER->GetId();
        \CPullWatch::Add($user_id, $tag.'_'.$user_id);
    }

    $APPLICATION->AddHeadScript("/bitrix/components/measoft.courier/js/script.js?v=" . $version);
}

