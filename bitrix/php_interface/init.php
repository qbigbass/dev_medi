<?

use \TwoFingers\Location\Storage;
use \TwoFingers\Location\Helper\Ip;
use \TwoFingers\Location\Location\Internal;
use \TwoFingers\Location\Location;
use \TwoFingers\Location\Current;


include_once($_SERVER['DOCUMENT_ROOT'] . '/local/libs/mobile_detect.php');
include("include/const.php");

#define("delivery_attention", "В связи с повышенным спросом и высокой загрузки операторов, срок обработки заказов увеличен.");
#define("order_attention", "В связи с повышенным спросом и высокой загрузки операторов, срок обработки заказов увеличен.");


#define("booking_block", "Y");

define("VK_PRICE_LIST_ID", 136797);
define("MYTARGET_FEED_ID", 102);

if (!function_exists("wl")) {
    function wl($data, $dump = 1, $file = __FILE__)
    {

        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . '/local/debug.log', "a+");
        fwrite($fp, "--------- " . date("H:i:s d-m-Y") . " -------------\r\n" . $file . "\r\n");

        fwrite($fp, print_r($data, $dump));

        fwrite($fp, "--------- end  -------------\r\n");
        fclose($fp);
    }
}

require_once("include/loymax.php");

require_once("include/lmx_app.php");

// допобработчики для  обмена с 1С
include("include/1c_exchange.php");

// обработчики для форм
include("include/forms.php");
// обработчики для каталога товаров
include("include/catalog.php");

// обработчики для заказов
include("include/order.php");

// обработчики для корзины
include("include/basket.php");

// обработчики для брендов
include("include/brand.php");

// обработчики для админ.раздела
if (defined("ADMIN_SECTION")) {
    include("include/admin.php");

    // Реестры для курьеров
    require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/php_interface/include/orders_reestr.php";
}


// Отправка заказа в курьерскую службу Курьерист
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/php_interface/include/orders_couriers.php";
// Отправка заказа в курьерскую службу Боксберри
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/php_interface/include/orders_boxberry.php";

// обработчики данных пользователей
include("include/user.php");

// агент напоминания о необработанных заказах
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/php_interface/include/order_reminder.php";

/**
 * Установка региона пользователя в сессию
 *
 * @return
 */
function checkRegion()
{
    global $APPLICATION, $USER, $medi_regions;
    // Работает только в публичной части
    if (!defined("ADMIN_SECTION")) {
        $LOG = [];
        $cur_site_id = ($_SESSION['MEDI_SITE_ID'] ? $_SESSION['MEDI_SITE_ID'] : 's1');
        $LOG['cur_site_id'] = $cur_site_id;
        $LOG['MEDI_SITE_ID'] = $_SESSION['MEDI_SITE_ID'];
        $LOG['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
        //$LOG['_SESSION'] = $_SESSION;
        if (CModule::IncludeModule('twofingers.location')) {
            $mregions = $medi_regions;
            $mregions["sfolder"]['s1'] = 'msk';
            if (isset($_REQUEST['set_location']) && in_array($_REQUEST['set_location'], array_values($mregions["sfolder"]))) {
                $site_id = array_search($_REQUEST['set_location'], $mregions["sfolder"]);
                $city = $medi_regions["region_cities"][$site_id];
                $region = $medi_regions["region_sites"][$site_id];

                $location = \TwoFingers\Location\Entity\Location::buildByPrimaryName($medi_regions['location'][$site_id], $city, null, "ru", $site_id);
                $location->setSiteId($site_id);

                \TwoFingers\Location\Storage::setLocation($location);
            } elseif ($location = \TwoFingers\Location\Storage::getLocation()) {

                $parent = $location->getParent();

                $LOG['lin96-$parent'] = $parent;
                if ($parent)
                    $region = $parent->getField("REGION_NAME");
                $LOG['lin96-$region'] = $region;
                $city = $location->getName();
                $site_id = $location->getSiteId();
                $LOG['lin96-city'] = $city;
                $LOG['lin96-site_id'] = $site_id;
            } elseif ($location = \TwoFingers\Location\Entity\Location::buildCurrent()) {

                $parent = $location->getParent();
                if ($parent)
                    $region = $parent->getField("REGION_NAME");
                $city = $location->getName();
                $site_id = $location->getSiteId();

                $LOG['lin106-city'] = $city;
                $LOG['lin106-site_id'] = $site_id;
            } else {

                $ip = TwoFingers\Location\Helper\Ip::getCur();
                if ($ip) {
                    if ($location = \TwoFingers\Location\Entity\Location::buildByIp($ip)) {

                        $parent = $location->getParent();
                        if ($parent)
                            $region = $parent->getField("REGION_NAME");
                        $city = $location->getName();
                        $site_id = $location->getSiteId();

                        $LOG['lin121-city'] = $city;
                        $LOG['lin121-site_id'] = $site_id;
                    }

                }
            }
        }
        $LOG['lin127-region'] = $region;
        $LOG['lin127-city'] = $city;

        if (empty($region) && $location) {
            $db_vars = CSaleLocation::GetList([], ["CODE" => $location->buildDefault()->getPrimary()], false, false, []);
            if ($vars = $db_vars->Fetch())
                if ($vars['REGION_NAME']) $region = $vars['REGION_NAME'];
        }

        if (!empty($region) || !empty($city)) {
            // Регион определен, проверям есть ли отдельный сайт для него
            if (in_array($city, $GLOBALS['medi']['region_cities'])) {
                $_SESSION['MEDI_REGION'] = $region;
                $_SESSION['MEDI_SITE_ID'] = array_search($city, $GLOBALS['medi']['region_cities']);
                $location->setSiteId($_SESSION['MEDI_SITE_ID']);

            } elseif (in_array($region, $GLOBALS['medi']['region_sites'])) {
                $_SESSION['MEDI_REGION'] = $region;
                $_SESSION['MEDI_SITE_ID'] = array_search($region, $GLOBALS['medi']['region_sites']);
                $location->setSiteId($_SESSION['MEDI_SITE_ID']);

            } elseif ($city == 'Санкт-Петербург') {

                $_SESSION['MEDI_REGION'] = "Ленинградская область";
                $_SESSION['MEDI_SITE_ID'] = "s2";
                $location->setSiteId('s2');
            } // Для всех остальных
            elseif ($region == '') {
                if ($city == 'Санкт-Петербург') {

                    $_SESSION['MEDI_REGION'] = "Ленинградская область";
                    $_SESSION['MEDI_SITE_ID'] = "s2";
                    $location->setSiteId('s2');
                } elseif ($city == 'Москва') {
                    $_SESSION['MEDI_REGION'] = "Москва и Московская область";
                    $_SESSION['MEDI_SITE_ID'] = "s1";
                    $location->setSiteId('s1');
                } else {
                    $_SESSION['MEDI_REGION'] = "Россия";
                    $_SESSION['MEDI_SITE_ID'] = "s0";
                    $location->setSiteId('s0');
                }
            } // Для всех остальных

            else {
                $_SESSION['MEDI_REGION'] = "Россия";
                $_SESSION['MEDI_SITE_ID'] = "s0";
                $location->setSiteId('s0');
            }
        } else {

            $_SESSION['MEDI_REGION'] = "Россия";
            $_SESSION['MEDI_SITE_ID'] = "s0";
            #$location->setSiteId('s0');
        }


        $_SESSION['MEDI_REGION'] = $region;
        $_SESSION['MEDI_CITY'] = $city;
        if ($location) $_SESSION['MEDI_CITY_CODE'] = $location->getCode();
        $_SESSION["USER_GEO_POSITION"]['city'] = $city;
        $ip = TwoFingers\Location\Helper\Ip::getCur();
        $LOG['ip'] = $ip;
        //if ($ip == '46.229.191.110') w2l($LOG, 1, "geo.log");
        if ($cur_site_id != $_SESSION['MEDI_SITE_ID'] && $_SESSION['MEDI_SITE_ID'] != '') {
            //w2l(['request'=>$_SERVER['REQUEST_URI'], 'cur_site_id'=> $cur_site_id, "sess_sid" => $_SESSION['MEDI_SITE_ID']], 1, 'region.log');
            LocalRedirect($_SERVER['REQUEST_URI']);
        }
    }

}

AddEventHandler("main", "OnBeforeProlog", "MyOnBeforePrologHandler");

function MyOnBeforePrologHandler()
{
    global $USER;

    if (!defined("ADMIN_SECTION")) {
        $arGroups = $arGroupsBak = $USER->GetUserGroupArray();
        if (SITE_ID == 's2' && !in_array('24', $arGroups)) {
            $arGroups[] = 24;
        } elseif (SITE_ID == 's1' && !in_array('28', $arGroups)) {
            $arGroups[] = 28;
        } elseif (in_array(SITE_ID, ['s0', 's3', 's4', 's5', 's6', 's7', 's8']) && !in_array('27', $arGroups)) {
            $arGroups[] = 27;
        }

        foreach ($arGroups as $g => $group) {
            if ($group == '24' && SITE_ID != 's2') {
                unset($arGroups[$g]);
            }
            if ($group == '28' && SITE_ID != 's1') {
                unset($arGroups[$g]);
            }
            if ($group == '27' && !in_array(SITE_ID, ['s0', 's3', 's4', 's5', 's6', 's7', 's8'])) {
                unset($arGroups[$g]);
            }
        }

        if (!empty(array_diff($arGroups, $arGroupsBak))) {
            $USER->SetUserGroupArray($arGroups);
        }
    }
}

if (isset($_SERVER['HTTP_REFERER'])) {
    $metrikaHosts = [
        'webvisor.com',
        'metrika.yandex',
        'metrika.yandex.ru',
        'metrika.yandex.ua',
        'metrika.yandex.com',
        'metrika.yandex.by',
        'metrika.yandex.kz',
        $_SERVER['HTTP_HOST'],
    ];

    $refHost = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);

    if (in_array($refHost, $metrikaHosts)) {
        define('BX_SECURITY_SKIP_FRAMECHECK', true);
    }
}


if (!defined("NO_AGENT_CHECK")
    && !defined("NO_REGION_CHECK")
    && !defined("BX_CRONTAB")
    && !defined("ADMIN_SECTION")
    && !empty($_SERVER['HTTP_USER_AGENT'])
    //&& !empty($_SESSION)
    && !strpos($_SERVER['HTTP_USER_AGENT'], "bot")
    && !strpos($_SERVER['HTTP_USER_AGENT'], "AHC/2.1")
    && !strpos($_SERVER['HTTP_USER_AGENT'], "yandex")
    && !strpos($_SERVER['HTTP_USER_AGENT'], "google")
) {
    AddEventHandler("main", "OnPageStart", 'checkRegion');
} else {
    $GLOBALS['price_code'] = 'CATALOG_PRICE_' . $GLOBALS['medi']['price_id']['s1'];
    $GLOBALS['max_price_code'] = 'CATALOG_PRICE_' . $GLOBALS['medi']['max_price_id']['s1'];
    $_SESSION['MEDI_REGION'] = "Москва и Московская область";
    $_SESSION['MEDI_SITE_ID'] = "s1";
}


if (!function_exists("wl2")) {
    function wl2($data, $dump = 1, $file = __FILE__)
    {

        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . '/local/debug2.log', "a+");
        fwrite($fp, "--------- " . date("H:i:s d-m-Y") . " -------------\r\n" . $file . "\r\n");

        fwrite($fp, print_r($data, $dump));

        fwrite($fp, "--------- end  -------------\r\n");
        fclose($fp);
    }
}
if (!function_exists("w2l")) {
    function w2l($data, $dump = 1, $log = 'debug3.log', $file = __FILE__)
    {

        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . '/local/' . $log, "a+");
        fwrite($fp, "--------- " . date("H:i:s d-m-Y") . " -------------\r\n" . $file . "\r\n");

        fwrite($fp, print_r($data, $dump));

        fwrite($fp, "--------- end  -------------\r\n");
        fclose($fp);
    }
}
if (!function_exists("w2t")) {
    function w2t($data, $dump = 1, $log = 'log.csv', $file = __FILE__)
    {

        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . '/local/' . $log, "a+");
        //fwrite($fp, "start ".date("H:i:s d-m-Y")."----".$file.";\r\n");

        fwrite($fp, implode(";", $data) . "\r\n");

        //fwrite($fp, "end  -------------;\r\n");
        fclose($fp);
    }
}
if (!function_exists("ct")) {
    function ct($str = '')
    {
        return (microtime(1));
    }
}
if (!function_exists("ctr")) {
    function ctr($start)
    {
        return round((microtime(1)) - $start, 8);
    }
}

if (!function_exists("getGroupsElements")) {
    function getGroupsElements($arrIds): array
    {
        $arrElemGroupSections = [];
        if (!empty($arrIds)) {
            $objGroups = CIBlockElement::GetElementGroups($arrIds, true, ["ID", "NAME", "IBLOCK_ELEMENT_ID"]);
            while($arrGroup = $objGroups->Fetch()) {
                $arrElemGroupSections[$arrGroup["IBLOCK_ELEMENT_ID"]][] = $arrGroup["ID"];
            }
        }

        return $arrElemGroupSections;
    }
}

if (!function_exists("getSubSectionsSection")) {
    function getSubSectionsSection($sectionId): array
    {
        $arrSubSections = [$sectionId];
        $objParentSection = CIBlockSection::GetByID($sectionId);

        if ($arParentSection = $objParentSection->Fetch()) {
            $arFilter = [
                "IBLOCK_ID" => "17",
                ">LEFT_MARGIN" => $arParentSection["LEFT_MARGIN"],
                "<RIGHT_MARGIN" => $arParentSection["RIGHT_MARGIN"],
                ">DEPTH_LEVEL" => $arParentSection["DEPTH_LEVEL"]
            ];

            $rsSect = CIBlockSection::GetList(
                [],
                $arFilter,
                false,
                ["ID", "IBLOCK_ID"],
                false

            );
            while ($arrSect = $rsSect->Fetch()) {
                $arrSubSections[] = $arrSect["ID"];
            }
        }

        return $arrSubSections;
    }
}

if (!function_exists("getSortProductBrand")) {
    function getSortProductBrand($brandId)
    {
        $sortProductBrand = 0;
        $objElemBrand = CIBlockElement::GetList(
            ["ID" => "ASC"],
            [
                "IBLOCK_ID" => "1",
                "ID" => $brandId
            ],
            false,
            false,
            ["ID", "IBLOCK_ID", "PROPERTY_SORT_PRODUCT_BRAND"]
        );

        while ($elem = $objElemBrand->Fetch()) {
            $sortProductBrand = $elem["PROPERTY_SORT_PRODUCT_BRAND_VALUE"];
        }

        return $sortProductBrand;
    }
}