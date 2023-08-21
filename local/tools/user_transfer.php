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

define('SYNC_HOST', 'localhost');
define('SYNC_DB', 'sitemanager');
define('SYNC_LOGIN', 'bitrix0');
define('SYNC_PASSWORD', '{zML3gxwazKOHSj542U}');


$next = 0;
$PER_PAGE = intval($_REQUEST['PER_PAGE']) > 0 ? intval($_REQUEST['PER_PAGE']) : 100;
if (isset($_REQUEST['next']))
{

    $next = intval($_REQUEST['next']);
    $LIMIT_FROM = intval($_REQUEST['next'] * $PER_PAGE);
}
else
{
    $LIMIT_FROM = intval($_REQUEST['LIMIT_FROM']) > 0 ? intval($_REQUEST['LIMIT_FROM']) : 0;
}

$DBN = new CDatabase;

if (!($DBN->Connect(SYNC_HOST, SYNC_DB, SYNC_LOGIN, SYNC_PASSWORD))) {
    // подключение к БД не удалось → log
    echo "No connection";die;
}

$DBN->Query('SET NAMES utf8');
//AND u.LAST_LOGIN  IS NOT NULL
 $query = "SELECT u.* FROM b_user as u WHERE u.ACTIVE = 'Y' AND u.ID >= 30699 ORDER BY ID LIMIT $LIMIT_FROM, $PER_PAGE ";
 $rsOldUsers = $DBN->Query($query);

echo "<pre>";
$ii = 0;
while ($arOldUser = $rsOldUsers->Fetch()) {

    $transfer = 1;

    $query2 = "SELECT u.* FROM b_user as u WHERE  u.DATE_REGISTER = '".($arOldUser['DATE_REGISTER'])."' AND u.XML_ID = '".$arOldUser['ID']."' ";
    $rsUsers = $DB->Query($query2);
    if ($arUser = $rsUsers->Fetch())
    {
        echo "found user ".$arOldUser['EMAIL']."<br />";
    }
    else
    {
        // transfer
        echo "<br />need transfer<br /> ";

        if ($arOldUser['PERSONAL_PHONE'] == '' && $arOldUser['PERSONAL_MOBILE'] != '')
        {
            $arOldUser['PERSONAL_PHONE'] = $arOldUser['PERSONAL_MOBILE'];
        }

        if ($arOldUser['PERSONAL_PHONE'] == '')
        {
            $obres = $DBN->Query("SELECT p.VALUE FROM b_sale_order AS o, b_sale_order_props_value AS p WHERE o.USER_ID = '".$arOldUser['ID']."' AND o.ID = p.ORDER_ID AND p.ORDER_PROPS_ID = 3");
             if ($arORder = $obres->Fetch())
            {
                $arOldUser['PERSONAL_PHONE'] = $arORder['VALUE'];
            }
        }

        if ($arOldUser['PERSONAL_PHONE'] != '') {
            $arOldUser['PERSONAL_PHONE'] = str_replace(array("+","-","(",")"," "),"", $arOldUser['PERSONAL_PHONE']);
            $arOldUser['PERSONAL_PHONE'] =  Main\UserPhoneAuthTable::normalizePhoneNumber($arOldUser['PERSONAL_PHONE']);
        }

        if ($arOldUser['PERSONAL_PHONE'] != '')
        {
            if (substr($arOldUser['PERSONAL_PHONE'],0,5) == '+7495')
            {
                $transfer = 2;
            }
        }


        if (strpos($arOldUser['EMAIL'], 'medi-shop.ru'))
        {
            if ($arOldUser['PERSONAL_PHONE'] != '')
            {
                $arOldUser['LOGIN'] = $arOldUser['PERSONAL_PHONE'];
                $arOldUser['EMAIL'] = "";
            }
            else
            {
                $transfer = 3;
            }
        }

        if (!check($arOldUser['LOGIN']))
        {
            if ($arOldUser['PERSONAL_PHONE'] != '')
            {
                $arOldUser['LOGIN'] = $arOldUser['PERSONAL_PHONE'];
            }
            else
            {
                $transfer = 4;
            }
        }

        unset($arOldUser['PERSONAL_BIRTHDAY']);

        if (empty($arOldUser['LAST_ACTIVITY_DATE']))
        {
            unset($arOldUser['LAST_ACTIVITY_DATE']);
        }
        if (empty($arOldUser['LAST_LOGIN']))
        {
            unset($arOldUser['LAST_LOGIN']);
        }
        if (empty($arOldUser['TIMESTAMP_X']))
        {
            unset($arOldUser['TIMESTAMP_X']);
        }


        unset($arOldUser['EXTERNAL_AUTH_ID']);
        $arOldUser['EMAIL'] = $DB->ForSql($arOldUser['EMAIL']);
        $arOldUser['NAME'] = $DB->ForSql($arOldUser['NAME']);
        $arOldUser['LAST_NAME'] = $DB->ForSql($arOldUser['LAST_NAME']);
        $arOldUser['SECOND_NAME'] = $DB->ForSql($arOldUser['SECOND_NAME']);
        $arOldUser['PERSONAL_WWW'] = $DB->ForSql($arOldUser['PERSONAL_WWW']);
        $arOldUser['XML_ID'] = $DB->ForSql($arOldUser['XML_ID']);
        $arOldUser['BX_USER_ID'] = $DB->ForSql($arOldUser['BX_USER_ID']);
        $arOldUser['TIMESTAMP_X'] = $DB->ForSql($arOldUser['TIMESTAMP_X']);


        /*if ($arOldUser['TIMESTAMP_X'] == null)
        {
            $arOldUser['TIMESTAMP_X'] = time();
        }*/

        print_r($arOldUser);
        echo "<br />";
            unset($arOldUser['TIMESTAMP_X']);

        $arOldUser['XML_ID'] = $arOldUser['ID'];

        unset($arOldUser['ID']);

        foreach ($arOldUser AS $k=>$us)
        {
            $arOldUser[$k] = str_replace(array("/","\\","\"","'"), "", $us);
        }


        if ($transfer == '1') {
            $update = "INSERT INTO b_user (";
            $cols = array_keys($arOldUser);
            $vals = array_values($arOldUser);

            $update .= implode(", ", $cols);
            $update .= ") VALUES ('";

            $update .= implode("', '", $vals)."' ); ";

            $DB->Query($update);

            $last = $DB->Query("SELECT LAST_INSERT_ID();");

            if ($arLastID = $last->Fetch())
            {

                $ID = intval($arLastID['LAST_INSERT_ID()']);


                if ($ID > 0)
                {

                // b_user_access   2 3 4 6
                    $DB->Query("INSERT INTO b_user_access ( USER_ID, PROVIDER_ID, ACCESS_CODE ) VALUES ($ID, 'user', 'U.$ID') ");
                    $DB->Query("INSERT INTO b_user_access ( USER_ID, PROVIDER_ID, ACCESS_CODE ) VALUES ($ID, 'group', 'G3') ");
                    $DB->Query("INSERT INTO b_user_access ( USER_ID, PROVIDER_ID, ACCESS_CODE ) VALUES ($ID, 'group', 'G4') ");
                    $DB->Query("INSERT INTO b_user_access ( USER_ID, PROVIDER_ID, ACCESS_CODE ) VALUES ($ID, 'group', 'G6') ");
                    $DB->Query("INSERT INTO b_user_access ( USER_ID, PROVIDER_ID, ACCESS_CODE ) VALUES ($ID, 'group', 'G2') ");

                // b_user_access_check  id group id user

                    $DB->Query("INSERT INTO b_user_access_check ( USER_ID, PROVIDER_ID ) VALUES ($ID, 'user') ");
                    $DB->Query("INSERT INTO b_user_access_check ( USER_ID, PROVIDER_ID ) VALUES ($ID, 'group') ");


                // b_user_group 3 4 6
                    $DB->Query("INSERT INTO b_user_group ( USER_ID, GROUP_ID ) VALUES ($ID, '3') ");
                    $DB->Query("INSERT INTO b_user_group ( USER_ID, GROUP_ID ) VALUES ($ID, '4') ");
                    $DB->Query("INSERT INTO b_user_group ( USER_ID, GROUP_ID ) VALUES ($ID, '6') ");

                    $arFields["PHONE_NUMBER"] = Main\UserPhoneAuthTable::normalizePhoneNumber($arOldUser['PERSONAL_PHONE']);

                    Main\UserPhoneAuthTable::add([
							"USER_ID" => $ID,
							"PHONE_NUMBER" => $arFields["PHONE_NUMBER"],
						]);


                }
            }


        }
        else
        {
            echo "ERROR $transfer<br />";
            $fp = fopen($_SERVER['DOCUMENT_ROOT'].'/local/users_transfer.log', "a+");

            //fwrite($fp, '"'.implode('";"', array_keys($arOldUser)).'"'."\r\n");
            fwrite($fp, '"'.implode('";"', array_values($arOldUser)).'"'."\r\n");

            fclose($fp);
        }



    }


    $ii++;

}
echo $ii."<br />";
echo "</pre>";


$next++;

echo "<META HTTP-EQUIV='REFRESH' CONTENT='2;URL=/local/tools/user_transfer.php?next={$next}'/>";

function check($s)
{
    $len = strlen($s);
    if ($len < 3 || $len > 35) {
        echo "len";
        return false;
    }/*
    if (!preg_match("/^[a-z0-9][a-z0-9\.\-_@]+[a-z0-9]$/is", $s))
    {

            echo "preg1";
        return false;
    }*/
    foreach (["--", "__", "-_", "_-"] as $v)
        if (strpos($s, $v))
            return false;
    return true;
}
