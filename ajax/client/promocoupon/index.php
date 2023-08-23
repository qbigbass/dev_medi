<?
set_time_limit(0);
error_reporting(E_ERROR);

// Не собирать статистику
@define('STOP_STATISTICS', true);
@define('NO_KEEP_STATISTIC', true);
@define('NO_AGENT_STATISTIC', 'Y');
// Погасить монитор производительности
@define('PERFMON_STOP', true);

// Пропустить проверку на доступ к файлу
// (на этапе подключения главного модуля)
@define('NOT_CHECK_PERMISSIONS', true);
// Пропустить шаг проверки,
// нужно ли на хите выполнять агентов
@define('NO_AGENT_CHECK', true);

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\PhoneNumber\Parser;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

// ...например, чтобы отключить действия типа
// "объединять css-, js-файлы"
@define('ADMIN_SECTION', true);

// Отключаем режим правки (путает нам json), статистику
$GLOBALS['APPLICATION']->SetShowIncludeAreas(false);

// Не показывать статистику страницы
$_SESSION['SESS_SHOW_TIME_EXEC'] = null;
$_SESSION['SESS_SHOW_INCLUDE_TIME_EXEC'] = null;
$GLOBALS['DB']->ShowSqlStat = false;


$context = Context::getCurrent();

// При вызове через аякс обязательно ожидается реферер
$referer = $context->getServer()->get('HTTP_REFERER');

// ...и сигнал мы разрешаем только с трастовых доменов
$trustedDomains = [
    'www.medi-salon.ru',
    'dev3.medi-salon.ru',
];

$allowedRegExp = sprintf(
    '~https?://(%s)/~is' . BX_UTF_PCRE_MODIFIER,
    str_replace('.', '\.', implode('|', $trustedDomains))
);

$trusted = ($referer && preg_match($allowedRegExp, $referer));

if (
    !defined('B_PROLOG_INCLUDED')
    || B_PROLOG_INCLUDED !== true
    || !$trusted
) {
    die('Access Denied' . $allowedRegExp);
}

$available_actions = [
    'check_phone',
    'send_coupon',
    'get_count'
];

$action = '';
if (
    !isset($_REQUEST['action'])
    || !in_array(strval($_REQUEST['action']), $available_actions)
) {
    die();
}

Loader::includeModule("iblock");
Loader::includeModule("main");

$action = strval($_REQUEST['action']);

if ($action == "check_phone") {
    $request = Application::getInstance()->getContext()->getRequest();
    
    if ($request->isAjaxRequest() && $request->isPost()) {
        $phone = $request->getPost("phone");
        $name = $request->getPost("name");
        $sessid = $request->getPost("sessid");
        $action_id = $request->getPost("action_id");
        $recaptcha = $request->getPost("recaptcha");
        
        if ($sessid && bitrix_sessid() !== $sessid) {
            
            $result = false;
            $error = "Ошибка проверки запроса. Попробуйте снова.";
            
            echo json_encode(["status" => "error", "text" => $error]);
            die;
        }
        
        // Проверяем, что пользователь прошел reCAPTCHA
        $recaptcha_secret_key = "6LfbK6IlAAAAAKAQJdxtUWJbsJvonXd9uIEn76bE";
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $params = [
            "secret" => $recaptcha_secret_key,
            "response" => $recaptcha,
            "remoteip" => $_SERVER["REMOTE_ADDR"]
        ];
        $client = new HttpClient();
        $result = $client->post($url, $params);
        $data = json_decode($result);
        
        if (!$data->success) {
            $result = false;
            $error = "Ошибка проверки reCAPTCHA. Попробуйте снова.";
            
            echo json_encode(["status" => "error", "text" => $error]);
            die;
        } else {
            
            // Проверяем, что номер телефона уникальный
            $phonePropCode = "PHONE"; // код свойства инфоблока, в котором хранится номер телефона
            $namePropCode = "NAME"; // код свойства инфоблока, в котором хранится Имя
            $couponPropCode = "COUPON"; // код свойства инфоблока, в котором хранится купон
            $actionPropCode = "COUPON_ACTION"; // код свойства инфоблока, в котором хранится акция купона
            
            $iblockId = 37; // ID инфоблока заявок
            $action_iblockId = 38; // ID инфоблока купонных акций
            
            $phone = Parser::getInstance()->parse($phone, "RU")->format(); // форматируем номер телефона
            $dbRes = CIBlockElement::GetList([], [
                "IBLOCK_ID" => $iblockId,
                "PROPERTY_" . $actionPropCode => $action_id,
                "PROPERTY_" . $phonePropCode => $phone
            ],
                false, false, ["ID"]
            );
            if ($dbRes->SelectedRowsCount() > 0) {
                
                $result = false;
                $error = "Номер телефона уже использовался для отправки купона к этой акции";
                
                echo json_encode(["status" => "error", "text" => $error]);
                die;
            }
            
            $couponsList = [];
            $dbRes = CIBlockElement::GetList([], [
                "IBLOCK_ID" => $action_iblockId,
                "IBLOCK_SECTION_ID" => $action_id,
                "ACTIVE" => "Y",
                "PROPERTY_SENDED" => false
            ],
                false, false, ["ID", "NAME"]
            );
            if ($arCoupon = $dbRes->GetNext()) {
                $coupon = $arCoupon['NAME'];
                $couponId = $arCoupon['ID'];
            } else {
                $result = false;
                $error = "К сожалению, купоны уже закончились.";
                
                echo json_encode(["status" => "error", "text" => $error]);
                die;
            }
            
            // Сохраняем номер телефона в инфоблоке
            $el = new CIBlockElement();
            $fields = [
                "IBLOCK_ID" => $iblockId,
                "NAME" => $phone,
                "PROPERTY_VALUES" => [
                    $phonePropCode => $phone,
                    $namePropCode => $name,
                    $couponPropCode => $coupon,
                    $actionPropCode => $action_id
                ]
            ];
            $app_phone_id = $el->Add($fields);
            // исключаем купон
            if (!$app_phone_id) {
                
                echo json_encode(["status" => "error", "text" => $el->LAST_ERROR . print_r($fields)]);
                die;
                
            }
            CIBlockElement::SetPropertyValuesEx($couponId, false, ['SENDED' => $app_phone_id]);
            
            $dbRes = CIBlockSection::GetList(['sort' => 'asc'], ["IBLOCK_ID" => $action_iblockId,
                "ACTIVE" => "Y", "ID" => $action_id], false, ["ID", "NAME", "DESCRIPTION", "UF_*"]);
            
            if ($arAction = $dbRes->Fetch()) {
                if (!empty($arAction['UF_SMS_TEXT'])) {
                    $message = str_replace("#COUPON#", $coupon, $arAction['UF_SMS_TEXT']);
                    $message = str_replace("#DATE#", date("d.m", time() + 14 * 86400), $message);
                } else {
                    $message = "Ваш купон: " . $coupon;
                }
            }
            
            // Отправляем купон
            require_once $_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/vendor/smpp/smpp_init.php';
            
            $destination_addr = preg_replace("/(\D)*/", "", $phone);
            
            
            $service = new \PhpSmpp\Service\Sender([$host], $system_id, $password,
                PhpSmpp\Client::BIND_MODE_TRANSMITTER);
            $smsId = $service->send($destination_addr, $message, $source_addr);
            
            // Отправляем уведомление
            
            $arFields['EMAIL'] = 'info@mediexp.ru';
            $arFields['SUBJ'] = "Отправлен купон клиенту";
            $arFields['MSG'] .= "
            На сайте заполнена форма получения купона на скидку:
            <br><br>
            Имя: " . $name . "<br>
            Телефон: " . $phone . "<br><br>
            Акция: " . $arAction['NAME'] . "<br>
            Купон: " . $coupon . "<br>
            <br><br>";
            
            \CEvent::SendImmediate('MSG_LOG', 's1', $arFields);
            
            $result = true;
            $error = "$name, спасибо за обращение!<br/>Код купона отправлен на ваш телефон.";
        }
        
        
    } else {
        
        $result = false;
        $error = "Ошибка в запросе. Попробуйте снова.";
        
    }
    
    if ($result !== false) {
        echo json_encode(["status" => "success", "text" => $error]);
    } else {
        echo json_encode(["status" => "error", "text" => $error]);
    }
    
} elseif ($action == 'get_count') {
    $request = Application::getInstance()->getContext()->getRequest();
    
    if ($request->isAjaxRequest() && $request->isPost()) {
        $iblockId = 38; // ID инфоблока купонных акций
        
        $action_id = $request->getPost("action_id");
        $sessid = $request->getPost("sessid");
        
        if ($sessid && bitrix_sessid() !== $sessid) {
            
            $result = false;
            $error = "Ошибка проверки запроса. Попробуйте снова.";
            
            echo json_encode(["status" => "error", "text" => $error]);
            die;
        }
        
        $dbRes = CIBlockElement::GetList([],
            [
                "IBLOCK_ID" => $iblockId,
                "IBLOCK_SECTION_ID" => $action_id,
                "PROPERTY_SENDED" => false,
                "ACTIVE" => "Y"
            ], false, false, ["ID"]);
        if ($result = $dbRes->SelectedRowsCount()) {
            
            echo json_encode(["status" => "success", "count" => $result]);
            die;
        } else {
            echo json_encode(["status" => "success", "count" => 0]);
            die;
        }
        
    }
}