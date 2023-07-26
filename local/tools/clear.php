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

setlocale(LC_ALL, "ru_RU.UTF-8");
setlocale(LC_NUMERIC, 'C');


$APPLICATION->SetTitle("Очистка пользователей | medi");

$user_groups = array (
// old -> new
1, 7, 8, 11, 13,14, 17,18,19,20,21,22,24,25,26,29
);
$rsUsers = CUser::GetList(($by="timestamp_x"), ($order="asc"), $filter, array("NAV_PARAMS"=>["nPageSize"=>"20000"])); // выбираем пользователей

    $user = new CUser;
$i=0;
while($arUser = $rsUsers->Fetch()){
    print_r($arUser);
    $arGroups = CUser::GetUserGroup($arUser['ID']);
    if (array_intersect($user_groups, $arGroups))
    {
        echo '-'. $arUser['ID']."<br>";
        continue;
    }
    else{
        $fields = Array(
          "NAME"              => "",
          "LAST_NAME"         => "",
          "SECOND_NAME"         => "",
          "PERSONAL_PHONE"=>"",
          "PERSONAL_MOBILE"=>"",
          "EMAIL"             => "",
      "LOGIN"             => "bitrix".$arUser['ID'],
          );
        $user->Update($arUser['ID'], $fields);
        $strError .= $user->LAST_ERROR;
        echo $arUser['ID']."<br>";
        //$DB->Query("DELETE  from  b_user_phone_auth where USER_ID = '".$arUser['ID']."'");
    }
    $i++;
if ($i>10) break;
};
