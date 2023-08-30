<?
define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_CHECK", true);
define("NO_AGENT_STATISTIC", true);
define("STOP_STATISTICS", true);
define("PERFMON_STOP", true);

if (!defined('PUBLIC_AJAX_MODE'))
    define('PUBLIC_AJAX_MODE', true);

use Bitrix\Main\Application;
use TwoFingers\Location\Request;

require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule('twofingers.location')
    || ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest'))
{
    require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/epilog_after.php");
    return;
}

$request    = Application::getInstance()->getContext()->getRequest();
$result     = Request::handle($request->get('request'));

if (!empty($result))
    echo $result;

require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/epilog_after.php");