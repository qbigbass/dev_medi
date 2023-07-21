<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

use Bitrix\Main;
use Bitrix\Main\Authentication\ApplicationPasswordTable;

set_time_limit(0);
ignore_user_abort(true);

define('IMPORT_ROOT', dirname(__FILE__)); // папка со скриптами импорта
define("LOG_FILENAME", IMPORT_ROOT.'/log.txt');
define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_CHECK", true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

if (!$USER->IsAdmin()) die();

die;
setlocale(LC_ALL, "ru_RU.UTF-8");
setlocale(LC_NUMERIC, 'C');


$APPLICATION->SetTitle("Перенос пользователей | medi");

$user_groups = array (
// old -> new
1 => 1


);
