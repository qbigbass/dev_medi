<?php
/**
 * @copyright Copyright &copy; Компания MEAsoft, 2014
 */

//define("query_log", "");

//define('dbg_calculator', '');

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Application;

Loc::loadLanguageFile(__FILE__);

class Measoft
{
    /**
     * Константы с модулями, которые используют данный класс
     */
    const BITRIX = 'bitrix';
    const INSALES = 'insales';
    const OPENCART = 'opencart';
    const PRESTASHOP = 'prestashop';
    const SHOPSCRIPT = 'shopscript';
    
    /**
     * Логин
     */
    public $login;
    
    /**
     * Пароль
     */
    public $password;
    
    /**
     * Код клиента
     */
    public $extra;
    
    /**
     * Проверка заказа
     */
    public $errors = array();
    
    /**
     * Последний ответ на запрос
     */
    public $lastResponse;
    
    public $currentStatusId;
    
    public $orderId;
    
    /**
     * Конструктор
     */
    public function __construct($login = null, $password = null, $extra = null)
    {
        if ($login && $password && $extra) {
            $this->login = $login;
            $this->password = $password;
            $this->extra = $extra;
        } else {
            MeasoftLoghandler::log("\n\n Проверьте аутентификационные данные. Логин: '$login', пароль: '$password', код клиента: '$password'.");
            
            $this->errors = $this->convertTxt("Проверьте аутентификационные данные. Логин: '$login', пароль: '$password', код клиента: '$password'.");
            return false;
        }
    }
    
    /**
     * Проверка дынных заказа
     */
    public function orderValidate($order = null, $items = null)
    {
        if (!isset($order['phone'])) {
            $this->errors[] = $this->convertTxt('не заполнен телефон получателя');
        }
        
        if (!isset($order['town']) || !$order['town']) {
            $this->errors[] = $this->convertTxt('не заполнен город получателя');
        }
        
        if (!isset($order['address']) || !$order['address']) {
            $this->errors[] = $this->convertTxt('не заполнен адрес получателя');
        }
        
        $date_parts = explode('-', $order['date']);
        if (!isset($order['date']) || !$order['date']) {
            $this->errors[] = $this->convertTxt('не заполнена дата доставки товара');
        } else {
            if (count($date_parts) != 3 || !checkdate($date_parts[1], $date_parts[2], $date_parts[0])) {
                $this->errors[] = $this->convertTxt('неверный формат даты доставки');
            }
        }
        
        if (!isset($order['time_min']) || !$order['time_min']) {
            $this->errors[] = $this->convertTxt('не заполнено минимальное время доставки');
        } else if (!preg_match('/^([0,1][0-9]|2[0-3]):([0-5][0-9])$/', $order['time_min'])) {
            $this->errors[] = $this->convertTxt('неверный формат минимального времени доставки: "' . $order['time_min'] . '"');
        }
        
        if (!isset($order['time_max']) || !$order['time_max']) {
            $this->errors[] = $this->convertTxt('Не заполнено максимальное время доставки');
        } else if (!preg_match('/^([0,1][0-9]|2[0-3]):([0-5][0-9])$/', $order['time_max'])) {
            $this->errors[] = $this->convertTxt('неверный формат максимального времени доставки: "' . $order['time_max'] . '"');
        }
        
        if ($order['time_min'] > $order['time_max']) {
            $this->errors[] = $this->convertTxt('конечное время доставки должно быть больше начального');
        }
        
        if ($this->errors) {
            $this->errors = "\n - " . implode(";\n - ", $this->errors);
            return false;
        }
        
        return true;
    }
    
    function getCachedCalculatorRequest($order, $xml)
    {
        $cacheID = md5("Calculator_" . $order['mass'] . '_' . $order['townfrom'] . '_' . $order['townto'] . '_' . $this->extra . '_' . $this->login . "_" . $order["paytype"] . '_' . $order['pricetype'] . '_' . $order['addressto'] . '_' . $order['service'] . '_' . $order['pvz']);
        $obCache = new CPHPCache();
        if ($obCache->InitCache(600, $cacheID, 'measoft_calculator')) {
            $dataArr = $obCache->GetVars();
            
            MeasoftLoghandler::log("\n\n\n getCachedCalculatorRequest from cache");
            
        } else {
            if ($obCache->StartDataCache()) {
                MeasoftLoghandler::log("\n\n\n getCachedCalculatorRequest from service");
                $dataArr = $this->sendRequest($xml);
                $obCache->EndDataCache($dataArr);
            }
        }
        
        return $dataArr;
    }
    
    /**
     * Отправка запроса стоимости
     */
    public function calculatorRequest($order = null)
    {
        
        $errorsText = array(
            0 => Loc::getMessage('MEASOFT_OK'),
            1 => Loc::getMessage('MEASOFT_NEVERNIY_XML'),
            2 => Loc::getMessage('MEASOFT_SHIROTA_NE_UKAZANA'),
            3 => Loc::getMessage('MEASOFT_DOLGOTA_NE_UKAZANA'),
            4 => Loc::getMessage('MEASOFT_DATE_TIME_NE_UKAZANY'),
            5 => Loc::getMessage('MEASOFT_TOCHNOST_NE_UKAZANA'),
            6 => Loc::getMessage('MEASOFT_TEL_ID_NE_UKAZAN'),
            7 => Loc::getMessage('MEASOFT_TEL_ID_NE_NAYDEN'),
            8 => Loc::getMessage('MEASOFT_NEVERNAYA_SHIROTA'),
            9 => Loc::getMessage('MEASOFT_NEVERNAYA_DOLGOTA'),
            10 => Loc::getMessage('MEASOFT_NEVERNAYA_TOCHNOST'),
            11 => Loc::getMessage('MEASOFT_ZAKAZY_NE_NAYDENY'),
            12 => Loc::getMessage('MEASOFT_DATE_TIME_ZAKAZA_NE_VERNIYE'),
            13 => Loc::getMessage('MEASOFT_OSHIBKA_MYSQL'),
            14 => Loc::getMessage('MEASOFT_NEVERNAYA_FUNC'),
            15 => Loc::getMessage('MEASOFT_TRIF_NE_NAYDEN'),
            18 => Loc::getMessage('MEASOFT_GOROD_OTPRAVLENIYA_NE_UKAZAN'),
            19 => Loc::getMessage('MEASOFT_GOROD_NAZNACHENIYA_NE_UKAZAN'),
            20 => Loc::getMessage('MEASOFT_NEVERNAYA_MASSA'),
            21 => Loc::getMessage('MEASOFT_GOROD_OTPRAVLENIYA_NE_NAYDEN'),
            22 => Loc::getMessage('MEASOFT_GOROD_NAZNACHENIYA_NE_NAYDEN'),
            23 => Loc::getMessage('MEASOFT_MASSA_NE_UKAZANA'),
            24 => Loc::getMessage('MEASOFT_LOGIN_NE_UKAZAN'),
            25 => Loc::getMessage('MEASOFT_OSHIBKA_AUTH'),
            26 => Loc::getMessage('MEASOFT_LOGIN_UJE_SUSCHESTVUET'),
            27 => Loc::getMessage('MEASOFT_KLIENT_UJE_SUSCHESTVUET'),
            28 => Loc::getMessage('MEASOFT_ADDRESS_NE_UKAZAN'),
            29 => Loc::getMessage('MEASOFT_NE_PODDERJIVAETSYA'),
            30 => Loc::getMessage('MEASOFT_NE_NASTROEN_SIP'),
            31 => Loc::getMessage('MEASOFT_TEL_NE_UKAZAN'),
            32 => Loc::getMessage('MEASOFT_TEL_CURIER_NE_UKAZAN'),
            33 => Loc::getMessage('MEASOFT_OSHIBKA_SOEDIN'),
            34 => Loc::getMessage('MEASOFT_NEVERNIY_NOMER'),
            35 => Loc::getMessage('MEASOFT_NEVERNIY_NOMER'),
            36 => Loc::getMessage('MEASOFT_OSHIBKA_TARIFF'),
            37 => Loc::getMessage('MEASOFT_OSHIBKA_TARIFF'),
            38 => Loc::getMessage('MEASOFT_TARIFF_NE_NAYDEN'),
            39 => Loc::getMessage('MEASOFT_TARIFF_NE_NAYDEN'),
        );
        
        if (!$order) {
            $this->errors = $this->convertTxt(Loc::getMessage('MEASOFT_NE_UKAZANY_PARAMETRY_ZAKAZA'));
            return false;
        }
        
        
        $order['mass'] = isset($order['mass']) && $order['mass'] ? $order['mass'] : '0.1';
        
        $level = 0;
        $xml = $this->startXML();
        
        $xml .= $this->makeXMLNode('calculator', '', $level, '', 1);
        
        if (MeasoftEvents::isCp1251Site()) {
            $order['townfrom'] = iconv('CP1251', 'UTF-8', $order['townfrom']);
            $order['townto'] = iconv('CP1251', 'UTF-8', $order['townto']);
            $order['pricetype'] = iconv('CP1251', 'UTF-8', $order['pricetype']);
            $order['addressto'] = iconv('CP1251', 'UTF-8', $order['addressto']);
        }
        
        if (empty($order['townto'])) {
            
            $this->errors[] = $this->convertTxt(Loc::getMessage('MEASOFT_NE_ZADAN_GOROD_NAZNACHENIYA'));
            return false;
        }
        //$paytype = $order['PAYED'] == 'Y' ? 'NO' : ($order['PAY_SYSTEM_ID'] == self::configValueEx('PAYTYPE_CARD', $order['DELIVERY_ID']) ? 'CARD' : 'CASH');
        $level++;
        $xml .= $this->makeXMLNode('auth', '', $level, 'extra="' . $this->extra . '" login="' . $this->login . '" pass="' . $this->password . '"');
        $xml .= $this->makeXMLNode('calc', '', $level, 'townfrom="' . $order['townfrom'] . '" townto="' . $order['townto'] . '"
        
        mass="' . $order['mass'] . '"
        price="' . $order['price'] . '"
        inshprice="' . $order['inshprice'] . '"
        paytype="' . $order['paytype'] . '"
        pricetype="' . $order['pricetype'] . '"
        pvz="' . $order['pvz'] . '"
        addressto="' . $order['addressto'] . '"
        service="' . (isset($order['service']) && $order['service'] ? $order['service'] : 1) . '"  ');
        
        $level--;
        
        $xml .= $this->makeXMLNode('calculator', '', $level, '', 2);
        
        $this->lastResponse = $result = simplexml_load_string($this->getCachedCalculatorRequest($order, $xml));
        //$this->lastResponse = $result = simplexml_load_string($this->sendRequest($xml));
        
        if (defined('dbg_calculator')) {
            $this->lastResponse = $result = simplexml_load_string($this->dbg_calculator());
        }
        
        //file_put_contents($_SERVER["DOCUMENT_ROOT"]."/1.txt",print_r($this->lastRespons,true));
        MeasoftLoghandler::log("\n\n\n LANG={" . LANG_CHARSET . "} $xml \n\n test=Yget_price::" . print_r($this->lastResponse, true));
        
        if (!$result || !isset($result)) {
            $this->errors[] = $this->convertTxt(Loc::getMessage('MEASOFT_OSHIBKA_SERVISA'));
            return false;
        }
        
        if ($attributes = $result->attributes()) {
            if (isset($attributes['error']) && $attributes['error'] > 0) {
                $errTxt = isset($errorsText[(int)$attributes['error']]) ? $errorsText[(int)$attributes['error']] : "error: " . $attributes['error'];
                
                $this->errors[] = $this->convertTxt($errTxt);
            }
        }
        
        if (!$this->errors) {
            if (isset($result->calc)) {
                if ($attributes = $result->calc->attributes()) {
                    if (isset($attributes['price'])) {
                        return $result->calc;
                    }
                }
            } else {
                $this->errors[] = $this->convertTxt(Loc::getMessage('MEASOFT_OSHIBKA_PEREDACHI_DANNYH'));
                return false;
            }
        } else {
            return false;
        }
    }
    
    /**
     * Отправка заказа.
     */
    public function orderRequest($order = null, $items = null, $tagOrder = 'neworder')
    {
        if (!$order || !$items) {
            $this->errors = $this->convertTxt(Loc::getMessage('MEASOFT_PUSTOY_MASSIV_ZAKAZA'));
            $this->sendPullMessage('MEASOFT_PULL_ORDER_UPDATE', Loc::getMessage('MEASOFT_PUSTOY_MASSIV_ZAKAZA'));
            return false;
        }
        $xml = $this->createXML($order, $items, $tagOrder);
        
        if (MeasoftEvents::isCp1251Site()) {
            $xml = iconv('CP1251', 'UTF-8', $xml);
        }
        $ans = $this->sendRequest($xml);
        $response = simplexml_load_string($ans);
        
        MeasoftLoghandler::log("\n\n\n orderRequestXml::\n $xml \n\n ans::{$ans} \n\n orderRequest::" . print_r($response, true));
        
        if ($this->getRequestErrors($response)) {
            if (isset($response->createorder[0]['orderno'])) {
                
                MeasoftEvents::setOrderPropsValue($this->orderId, 'MEASOFT_ORDER_ERROR', "");
                
                return (string)$response->createorder[0]['orderno'];
            } else {
                if ($attributes = $response->createorder->attributes()) {
                    MeasoftLoghandler::log("\n\n\n errors::\n   " . print_r($this->errors, true));
                    $this->sendPullMessage('MEASOFT_PULL_ORDER_UPDATE', $this->errors);
                }
            }
        } else {
            if ($attributes = $response->createorder->attributes()) {
                if (isset($attributes['error']) && $attributes['error'] > 0) {
                    $errormsgru = $this->convertTxt(Loc::getMessage('MEASOFT_OSHIBKA_SOZDANIYA_ZAKAZA') . $attributes['errormsgru']);
                    MeasoftEvents::setOrderPropsValue($this->orderId, 'MEASOFT_ORDER_ERROR', $errormsgru);
                    $this->sendPullMessage('MEASOFT_PULL_ORDER_UPDATE', $errormsgru);
                }
            }
            
            
            return false;
        }
    }
    
    
    /**
     * @param $tag
     * @param $message
     * @return mixed
     */
    public function sendPullMessage($tag, $message)
    {
        
        if (CModule::IncludeModule("pull")) {
            global $USER;
            $user_id = $USER->GetId();
            $session = Application::getInstance()->getSession();
            
            
            $request = Application::getInstance()->getContext()->getRequest();
            $action = $request->getPost('action');
            $isAjax = ($action == 'saveStatus') ? true : false;
            
            
            if ($session->has('fixed_session_id')) {
                $session_id = $session->get('fixed_session_id');
                \CPullWatch::AddToStack($tag . '_' . $user_id,
                    array(
                        'module_id' => 'measoft.courier',
                        'command' => 'error',
                        'params' => array(
                            "message" => $message,
                            "userid" => $user_id,
                            "sessionid" => $session_id,
                            "isajax" => $isAjax
                        )
                    )
                );
            }
        }
    }
    
    /** Get delivery status
     * @param null $orderNumber
     * @return bool|mixed|string
     * @throws Exception
     */
    public function statusRequest($orderNumber = null)
    {
        $statuses = array(
            'NEW' => Loc::getMessage('MEASOFT_STATUS_NEW'),
            'PICKUP' => Loc::getMessage('MEASOFT_STATUS_PICKUP'),
            'ACCEPTED' => Loc::getMessage('MEASOFT_STATUS_ACCEPTED'),
            'INVENTORY' => Loc::getMessage('MEASOFT_STATUS_INVENTORY'),
            'DEPARTURING' => Loc::getMessage('MEASOFT_STATUS_DEPARTURING'),
            'DEPARTURE' => Loc::getMessage('MEASOFT_STATUS_DEPARTURE'),
            'DELIVERY' => Loc::getMessage('MEASOFT_STATUS_DELIVERY'),
            'COURIERDELIVERED' => Loc::getMessage('MEASOFT_STATUS_COURIERDELIVERED'),
            'COMPLETE' => Loc::getMessage('MEASOFT_STATUS_COMPLETE'),
            'PARTIALLY' => Loc::getMessage('MEASOFT_STATUS_PARTIALLY'),
            'COURIERRETURN' => Loc::getMessage('MEASOFT_STATUS_COURIERRETURN'),
            'CANCELED' => Loc::getMessage('MEASOFT_STATUS_CANCELED'),
            'RETURNING' => Loc::getMessage('MEASOFT_STATUS_RETURNING'),
            'RETURNED' => Loc::getMessage('MEASOFT_STATUS_RETURNED'),
            'CONFIRM' => Loc::getMessage('MEASOFT_STATUS_CONFIRM'),
            'DATECHANGE' => Loc::getMessage('MEASOFT_STATUS_DATECHANGE'),
            'NEWPICKUP' => Loc::getMessage('MEASOFT_STATUS_NEWPICKUP'),
            'UNCONFIRM' => Loc::getMessage('MEASOFT_STATUS_UNCONFIRM'),
            'PICKUPREADY' => Loc::getMessage('MEASOFT_STATUS_PICKUPREADY'),
            "AWAITING_SYNC" => Loc::getMessage('MEASOFT_STATUS_AWAITING_SYNC'),
            "WMSASSEMBLED" => Loc::getMessage('MEASOFT_STATUS_WMSASSEMBLED'),
            "WMSDISASSEMBLED" => Loc::getMessage('MEASOFT_STATUS_WMSDISASSEMBLED'),
            "LOST" => Loc::getMessage('MEASOFT_STATUS_LOST'),
            "COURIERPARTIALLY" => Loc::getMessage('MEASOFT_STATUS_COURIERPARTIALLY'),
            "COURIERCANCELED" => Loc::getMessage('MEASOFT_STATUS_COURIERCANCELED')
        );
        
        if (!$orderNumber) {
            $this->errors = $this->convertTxt(Loc::getMessage('MEASOFT_OSHIBKA_NET_NOMREA_ZAKAZA'));
            return false;
        }
        
        $level = 0;
        $xml = $this->startxml();
        $xml .= $this->makexmlnode('statusreq', '', $level, '', 1);
        
        $level++;
        $xml .= $this->makeXMLNode('auth', '', $level, 'extra="' . $this->extra . '" login="' . $this->login . '" pass="' . $this->password . '"');
        $xml .= $this->makexmlnode('orderno', $orderNumber, $level, '');
        $level--;
        
        $xml .= $this->makexmlnode('statusreq', '', $level, '', 2);
        
        $response = simplexml_load_string($this->sendRequest($xml));
        
        MeasoftLoghandler::log("  \n\n check::{$orderNumber}\n  " . print_r($response, true));
        
        
        if ($this->getRequestErrors($response)) {
            $this->currentStatusRow = isset($response->order->status) ? $response->order->status : "";
            
            $this->currentStatusId = $status = trim((string)$response->order[0]->status->attributes()['title']);
            if (!$status) {
                $status = trim((string)$response->order[0]->status);
                if (isset($statuses[$status])) {
                    $status = $statuses[$status];
                }
            }
            
            return $status;
        } else {
            return false;
        }
    }
    
    /**
     * Получение всех заказов, которые изменились со времени последнего запроса.
     */
    public function changedOrdersRequest()
    {
        $level = 0;
        
        $xml = $this->startxml();
        $xml .= $this->makexmlnode('statusreq', '', $level, '', 1);
        
        $level++;
        $xml .= $this->makexmlNode('auth', '', $level, 'extra="' . $this->extra . '" login="' . $this->login . '" pass="' . $this->password . '"');
        $xml .= $this->makexmlnode('changes', 'ONLY_LAST', $level, '');
        $xml .= $this->makexmlnode('quickstatus', 'NO', $level, '');
        $level--;
        
        $xml .= $this->makexmlnode('statusreq', '', $level, '', 2);
        
        $response = simplexml_load_string($this->sendRequest($xml));
        
        $this->commitLastStatusRequest();
        if ($this->getRequestErrors($response)) {
            $status = trim((string)$response->order[0]->status);
            if (isset($statuses[$status])) {
                return $statuses[$status];
            }
        } else {
            return false;
        }
    }
    
    /**
     * Подтверждение получения измененных заказов.
     */
    protected function commitLastStatusRequest()
    {
        $level = 0;
        $xml = $this->startxml();
        $xml .= $this->makexmlnode('commitlaststatus', '', $level, '', 1);
        $xml .= $this->makexmlNode('auth', '', $level + 1, 'extra="' . $this->extra . '" login="' . $this->login . '" pass="' . $this->password . '"');
        $xml .= $this->makexmlnode('commitlaststatus', '', $level, '', 2);
        
        $response = simplexml_load_string($this->sendRequest($xml));
        
        if ($this->getRequestErrors($response)) {
            $status = trim((string)$response->order[0]->status);
            if (isset($statuses[$status])) {
                return $statuses[$status];
            }
        } else {
            return false;
        }
    }
    
    /**
     * Выполнение POST запроса
     */
    public function sendRequest($content)
    {
        
        if (defined("query_log")) {
            MeasoftLoghandler::log("  \n\n query:\n {$content}");
            
        }
        
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: text/xml',
                'charset' => 'utf-8',
                'content' => $content,
            )
        );
        $context = stream_context_create($opts);
        
        if (!$contents = @file_get_contents('https://home.courierexe.ru/api/', false, $context)) {
            if (!$curl = curl_init()) {
                $this->errors = $this->convertTxt('Возможно не поддерживается передача по HTTPS. Проверьте наличие open_ssl');
                return false;
            }
            curl_setopt($curl, CURLOPT_URL, 'https://home.courierexe.ru/api/');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: text/xml; charset=utf-8'));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $contents = curl_exec($curl);
            
            
            curl_close($curl);
        }
        
        if (!$contents) {
            $this->errors = $this->convertTxt(Loc::getMessage('MEASOFT_OSHIBKA_SERVISA'));
            
            if (defined("query_log")) {
                MeasoftLoghandler::log("  \n\n aswer: service error");
            }
            
            return false;
        }
        
        if (defined("query_log")) {
            MeasoftLoghandler::log("  \n\n answer:\n {$contents}");
        }
        
        $this->lastResponse = simplexml_load_string($contents);
        
        return $contents;
    }
    
    /**
     * Проверяем ошибки возвращаемые АПИ
     */
    public function getRequestErrors($response)
    {
        $errorsText = array(
            Loc::getMessage("MEASOFT_ERROR_NO"),
            Loc::getMessage("MEASOFT_ERROR_AUTH"),
            Loc::getMessage("MEASOFT_ERROR_EMPTY"),
            Loc::getMessage("MEASOFT_ERROR_SUM"),
            Loc::getMessage("MEASOFT_ERROR_WEIGHT"),
            Loc::getMessage("MEASOFT_ERROR_TOWNTO"),
            Loc::getMessage("MEASOFT_ERROR_TOWNFROM"),
            Loc::getMessage("MEASOFT_ERROR_ADDR"),
            Loc::getMessage("MEASOFT_ERROR_TEL"),
            Loc::getMessage("MEASOFT_ERROR_NAME"),
            Loc::getMessage("MEASOFT_ERROR_COMPANY"),
            Loc::getMessage("MEASOFT_ERROR_INSH_SUM"),
            Loc::getMessage("MEASOFT_ERROR_ARTICUL"),
            Loc::getMessage("MEASOFT_ERROR_SENDER_COMPANY"),
            Loc::getMessage("MEASOFT_ERROR_SENDER_NAME"),
            Loc::getMessage("MEASOFT_ERROR_SENDER_TEL"),
            Loc::getMessage("MEASOFT_ERROR_SENDER_ADDR"),
            Loc::getMessage("MEASOFT_ERROR_ORDER_IS_EXIST"),
            Loc::getMessage("MEASOFT_ERROR_HOLIDAY_DATE"),
        );
        $this->errors = array();
        
        if (!$response || !isset($response)) {
            return false;
        }
        
        if ($attributes = $response->attributes()) {
            if (isset($attributes['count']) && $attributes['count'] == 0) {
                $this->errors[] = $this->convertTxt('Заказ с таким номером не найден');
            }
            if (isset($attributes['error']) && $attributes['error'] > 0) {
                $errTxt = isset($errorsText[(int)$attributes['error']]) ? $errorsText[(int)$attributes['error']] : (string)$response;
                
                $this->errors[] = $this->convertTxt($errTxt);
            }
        }
        
        if (isset($response->createorder)) {
            foreach ($response->createorder as $order) {
                if ($attributes = $order->attributes()) {
                    if (isset($attributes['error']) && $attributes['error'] > 0) {
                        $this->errors[] = $this->convertTxt(isset($attributes['errormsgru']) ? (string)$attributes['errormsgru'] : (string)$attributes['errormsg']);
                    }
                }
            }
        }
        
        if (isset($response->error)) {
            foreach ($response->error as $error) {
                if ($attributes = $error->attributes()) {
                    if (isset($attributes['error']) && $attributes['error'] > 0) {
                        $this->errors[] = $this->convertTxt(isset($errorsText[(int)$attributes['error']]) ? $errorsText[(int)$attributes['error']] : $attributes['errormsg']);
                    }
                } else {
                    $this->errors[] = $this->convertTxt('Ошибка синтаксиса XML: ' . (string)$error);
                }
            }
        }
        
        if ($this->errors) {
            $this->errors = implode(';<br>', $this->errors);
            return false;
        }
        
        return true;
    }
    
    
    /**
     * Подготавливаем данные для запроса
     */
    public function createXML($order, $items, $tagOrder = 'neworder')
    {
        //file_put_contents( $_SERVER["DOCUMENT_ROOT"] .'/upload/measoft_order2.log',  "  \n\n order_create_data::". print_r($order, true) );
        
        //Обработка вложений
        $resultItems = '';
        $itemInshprice = 0;
        $itemWeight = 0;
        if (isset($items) && $items) {
            $resultItems .= $this->makeXMLNode('items', '', 3, '', 1);
            foreach ($items as $item) {
                
                //Расчёт стоимости
                if ($item['name'] !== 'Доставка' && $item['name'] !== 'Скидка' && $item['name'] !== 'Наценка') {
                    $itemInshprice += $item['retprice'] * $item['quantity'];
                }
                
                //Расчёт массы
                if ($item['name'] !== 'Доставка' && $item['name'] !== 'Скидка' && $item['name'] !== 'Наценка') {
                    $itemWeight += $item['mass'] * $item['quantity'];
                }
                
                $resultItems .= $this->makeXMLNode('item', $item['name'], 4,
                    'quantity="' . $item['quantity'] . '"' .
                    ' mass="' . $item['mass'] . '"' .
                    ' retprice="' . $item['retprice'] . '"' .
                    ' barcode="' . $this->stripTagsHTML($item['barcode']) . '"' .
                    ' VATrate="' . $this->stripTagsHTML($item['VATrate']) . '"' .
                    ' governmentCode="' . $this->stripTagsHTML($item['governmentCode']) . '"' .
                    ($item['article'] ? ' article="' . $this->stripTagsHTML($item['article']) . '"' : '')
                );
            }
            
            if (floatval($order["price_paid"])) {
                $MEASOFT_PREDOPLATA = 'Предоплата';
                
                $resultItems .= $this->makeXMLNode('item', $MEASOFT_PREDOPLATA, 4,
                    'quantity="1" type="4" ' .
                    ' retprice="-' . $order["price_paid"] . '"'
                );
            }
            
            $resultItems .= $this->makeXMLNode('items', '', 3, '', 2);
        }
        
        $level = 0;
        $result = $this->startXML();
        
        $result .= $this->makeXMLNode($tagOrder, '', $level, '', 1);
        
        
        $level++;
        
        if (isset($order['sender']) and is_array($order['sender'])) {
            $result .= $this->makeXMLNode('sender', '', $level, 'type="4" module="' . $order['sender']['module'] . '" module_version="' . $order['sender']['module_version'] . '" cms_version="' . $order['sender']['cms_version'] . '"');
        }
        $result .= $this->makeXMLNode('auth', '', $level, 'extra="' . $this->extra . '" login="' . $this->login . '" pass="' . $this->password . '"');
        $result .= $this->makeXMLNode('order', '', $level, 'orderno="' . $order['orderno'] . '"', 1);
        
        $level++;
        
        $result .= $this->makeXMLNode('barcode', $order['barcode'], $level);
        
        $result .= $this->makeXMLNode('receiver', '', $level, '', 1);
        
        $level++;
        if (isset($order['company'])) $result .= $this->makeXMLNode('company', $order['company'], $level);
        if (isset($order['person'])) $result .= $this->makeXMLNode('person', $order['person'], $level);
        
        if (isset($order['pvz_code'])) {
            if (!empty($order['pvz_code'])) {
                $result .= $this->makeXMLNode('pvz', $order['pvz_code'], $level);
            }
        }
        
        $result .= $this->makeXMLNode('phone', $order['phone'], $level);
        
        if (isset($order['zipcode'])) {
            if (!empty($order['zipcode'])) {
                $result .= $this->makeXMLNode('zipcode', (isset($order['zipcode']) ? $order['zipcode'] : ''), $level);
            }
        }
        
        $result .= $this->makeXMLNode('town', $order['town'], $level);
        $result .= $this->makeXMLNode('address', $order['address'], $level);
        
        if (isset($order['date'])) {
            if (!empty($order['date'])) {
                $result .= $this->makeXMLNode('date', $order['date'], $level);
            }
        }
        if (isset($order['time_min'])) {
            if (!empty($order['time_min'])) {
                $result .= $this->makeXMLNode('time_min', $order['time_min'], $level);
            }
        }
        if (isset($order['time_max'])) {
            if (!empty($order['time_max'])) {
                $result .= $this->makeXMLNode('time_max', $order['time_max'], $level);
            }
        }

//        $result .= $this->makeXMLNode('date', $order['date'], $level);
//        $result .= $this->makeXMLNode('time_min', $order['time_min'], $level);
//        $result .= $this->makeXMLNode('time_max', $order['time_max'], $level);
        $level--;
        
        $result .= $this->makeXMLNode('receiver', '', $level, '', 2);
        
        $result .= $this->makeXMLNode('weight', ($order['weight'] ? $order['weight'] : $itemWeight), $level);
        $result .= $this->makeXMLNode('quantity', (isset($order['quantity'])) ? $order['quantity'] : 1, $level);
        $result .= $this->makeXMLNode('paytype', (isset($order['paytype'])) ? $order['paytype'] : 'NO', $level);
        $result .= $this->makeXMLNode('service', (isset($order['service'])) ? $order['service'] : '', $level);
        $result .= $this->makeXMLNode('price', $order['price'], $level);
        $result .= $this->makeXMLNode('discount', (isset($order['discount'])) ? $order['discount'] : '0', $level);
        $result .= $this->makeXMLNode('inshprice', ($order['inshprice'] ? $order['inshprice'] : $itemInshprice), $level);
        $result .= $this->makeXMLNode('enclosure', $order['enclosure'], $level);
        $result .= $this->makeXMLNode('instruction', $order['instruction'], $level);
        
        $result .= $resultItems;
        
        $level--;
        $result .= $this->makeXMLNode('order', '', $level, '', 2);
        
        $level--;
        $result .= $this->makeXMLNode($tagOrder, '', $level, '', 2);
        
        return $result;
    }
    
    public function startXML()
    {
        return ('<?xml version="1.0" encoding="UTF-8"?>');
    }
    
    public function stripTagsHTML($s)
    {
        $s = str_replace('&', '&amp;', $s);
        $s = str_replace("'", '&apos;', $s);
        $s = str_replace('<', '&lt;', $s);
        $s = str_replace('>', '&gt;', $s);
        $s = str_replace('"', '&quot;', $s);
        
        return $s;
    }
    
    public function stripTagsHTMLRecursive($element)
    {
        if (is_array($element)) {
            $return = array();
            foreach ($element as $key => $value) {
                $return[$key] = $this->stripTagsHTMLRecursive($value);
            }
            return $return;
        } else {
            return $this->stripTagsHTML($element);
        }
    }
    
    public function makeXMLNode($nodename, $nodetext, $level = 0, $attr = '', $justopen = 0)
    {
        $result = "\r\n";
        for ($i = 0; $i < $level; $i++) $result .= '    ';
        
        $emptytag = ($nodetext === '') && ($justopen == 0);
        $nodetext = $this->stripTagsHTML($nodetext);
        
        if ($justopen < 2) $result .= '<' . $nodename . ($attr ? $attr = ' ' . $attr : '') . ($emptytag ? ' /' : '') . '>' . $nodetext;
        if ((($justopen == 0) && !$emptytag) || ($justopen == 2)) $result .= '</' . $nodename . '>';
        
        return ($result);
    }
    
    /**
     *
     */
    public function clearErrors()
    {
        $this->errors = array();
    }
    
    /**
     * Возвращает отформатированный номер заказа с префиксом и учетом фиксированной длины.
     */
    public static function orderNoTransform($orderNo, $prefix = '', $fixLength = 0)
    {
        return $prefix . (!$fixLength ? $orderNo : str_pad($orderNo, $fixLength, 0, STR_PAD_LEFT));
    }


//$cityName == "Санкт-Петербург" ) || ( $cityName == "Москва" ) )
    
    function sendRequestCache($cityName)
    {
        $resArr = [
            "Санкт-Петербург" => '<?xml version="1.0" encoding="UTF-8" ?>
                                    <townlist count="1" page="1">
                                      <town>
                                        <code>153361</code>
                                        <city>
                                          <code>78</code>
                                          <name>Санкт-Петербург город</name>
                                        </city>
                                        <name>Санкт-Петербург город</name>
                                        <fiascode>c2deb16a-0330-4f05-821f-1d09c93331e6</fiascode>
                                        <kladrcode>7800000000000</kladrcode>
                                        <shortname />
                                        <typename />
                                        <coords lat="59.9387" lon="30.3162" />
                                      </town>
                                    </townlist>',
            "Москва" => '<?xml version="1.0" encoding="UTF-8" ?>
                            <townlist count="1" page="1">
                              <town>
                                <code>1</code>
                                <city>
                                  <code>77</code>
                                  <name>Москва город</name>
                                </city>
                                <name>Москва город</name>
                                <fiascode>0c5b2444-70a0-4932-980c-b4dc0d3f02b5</fiascode>
                                <kladrcode>7700000000000</kladrcode>
                                <shortname />
                                <typename />
                                <coords lat="55.7507" lon="37.6177" />
                              </town>
                            </townlist>',
        ];
        
        if (isset($resArr[$cityName])) {
            return $resArr[$cityName];
        }
    }
    
    
    public function getCity($cityArr)
    {
        $level = 0;
        $xml = $this->startXML();
        
        $xml .= $this->makeXMLNode('townlist', '', $level, '', 1);
        
        $level++;
        $xml .= $this->makeXMLNode('auth', '', $level, 'extra="' . $this->extra . '" ');
        $xml .= $this->makeXMLNode('conditions', '', $level, '', 1);
        
        $level++;  // REGION_NAME] => Ленинградская область
        if ($cityArr['REGION_NAME']) {
            $xml .= $this->makeXMLNode('city', $cityArr['REGION_NAME'], $level, '', 0);
        }
        
        if ($cityArr['CITY_NAME']) {
            $xml .= $this->makeXMLNode('name', $cityArr['CITY_NAME'], $level, '', 0);
        } else {
            return false;
        }
        
        $level--;
        $xml .= $this->makeXMLNode('conditions', '', $level, '', 2);
        $level--;
        
        $xml .= $this->makeXMLNode('townlist', '', $level, '', 2);
        
        if (MeasoftEvents::isCp1251Site()) {
            $xml = iconv('CP1251', 'UTF-8', $xml);
        }
        
        $cityName = $cityArr['CITY_NAME'];
        
        if (MeasoftEvents::isCp1251Site()) {
            $cityName = iconv('CP1251', 'UTF-8', $cityName);
        }
        
        if (($cityName == "Санкт-Петербург") || ($cityName == "Москва")) {
            $result = simplexml_load_string($this->sendRequestCache($cityName));
        } else {
            $result = simplexml_load_string($this->sendRequest($xml));
        }
        
        // file_put_contents( $_SERVER["DOCUMENT_ROOT"] .'/upload/measoft.log', "\n\n\n $xml \n\n town_arr::". print_r($result, true) ."\n\n\n cityArr::". print_r($cityArr, true), FILE_APPEND);
        
        if (isset($result->town->coords)) {
            $coords = $result->town->coords->attributes();
            
            $_SESSION["measoft"]["shop_coord"] = ["lat" => (string)$coords["lat"], "lon" => (string)$coords["lon"], "code" => (int)$result->town->code];
        }
    }
    
    public static function deliveryConfigValue($param = null, $delivery_id = null)
    {
        if (!self::$_deliveryConfig && $delivery_id) {
            
            $delivery = \Bitrix\Sale\Delivery\Services\Table::getById($delivery_id)->fetch();
            
            $delivery_id = $delivery['PARENT_ID'] ?: $delivery_id;
            
            $deliveries = [];
            
            $delivery_list = CSaleDeliveryHandler::GetList()->arResult;
            foreach ($delivery_list as $item) {
                $deliveries[$item['ID']] = $item;
            }
            
            
            if (isset($deliveries[$delivery_id]))
                self::$_deliveryConfig = $deliveries[$delivery_id];
            else
                self::$_deliveryConfig = $delivery_list[0];
        }
        
        if ($param) {
            return isset(self::$_deliveryConfig['CONFIG']['CONFIG'][$param])
                ? self::$_deliveryConfig['CONFIG']['CONFIG'][$param]['VALUE']
                : self::configValue($param);
        }
        
        return isset(self::$_deliveryConfig['CONFIG']) ? self::$_deliveryConfig : self::configValue();
    }
    
    
    public function searchCity($partCityName)
    {
        
        $level = 0;
        $xml = $this->startXML();
        
        
        $xml .= $this->makeXMLNode('townlist', '', $level, '', 1);
        
        $level++;
        $xml .= $this->makeXMLNode('auth', '', $level, 'extra="' . $this->extra . '" ');
        $xml .= $this->makeXMLNode('conditions', '', $level, '', 1);
        
        $level++;  // REGION_NAME] => Ленинградская область
        
        $xml .= $this->makeXMLNode('namestarts', $partCityName, $level, '', 0);
        
        $level--;
        $xml .= $this->makeXMLNode('conditions', '', $level, '', 2);
        
        $level++;
        $xml .= $this->makeXMLNode('limit', '', $level, '', 1);
//        $xml .= $this->makeXMLNode('limitfrom', 30, $level, '', 0);
        $xml .= $this->makeXMLNode('limitcount', 10, $level, '', 0);
//        $xml .= $this->makeXMLNode('countall', "YES", $level, '', 0);
        $level--;
        $xml .= $this->makeXMLNode('limit', '', $level, '', 2);
        
        
        $level--;
        
        $xml .= $this->makeXMLNode('townlist', '', $level, '', 2);

//        if (LANG_CHARSET != "UTF-8")
//        {
//            $xml = iconv( 'CP1251', 'UTF-8', $xml);
//        }
//        $xml = iconv( 'UTF-8', 'CP1251', $xml);
//        echo $xml;


//        file_put_contents( $_SERVER["DOCUMENT_ROOT"] .'/upload/measoft77.log', $xml);

//        if (LANG_CHARSET != "UTF-8")
//        {
//            $xml = iconv( 'CP1251', 'UTF-8', $xml);
//        }
//
//
        
        $result = simplexml_load_string($this->sendRequest($xml));
        
        $result = json_decode(json_encode($result), true);
        
        if (isset($result["town"])) {
            $cityList = [];
            
            foreach ($result["town"] as $townArr) {
                if (isset($townArr["city"]["name"])) {
                    if ($townArr["city"]["name"] != $townArr["name"]) {
                        $townArr["name"] .= " ({$townArr["city"]["name"]})";
                    }
                }
                
                $cityList[] = ["value" => $townArr["code"], "label" => $townArr["name"]];
                
            }

//            file_put_contents( $_SERVER["DOCUMENT_ROOT"] .'/upload/measoft77.log', "\n\n\n". print_r($cityList, true), FILE_APPEND);
            
            return $cityList;
        }
        
    }
    
    public function CancelOrder($order)
    {
        
        
        $prefix = MeasoftEvents::deliveryConfigValue('ORDER_PREFIX', $order['DELIVERY_ID']);
        $orderNumber = MeasoftEvents::deliveryConfigValue('ORDER_NUMBER', $order['DELIVERY_ID']);
        
        $orderno = !$orderNumber ? $prefix . $order['ID'] : $order['ACCOUNT_NUMBER'];
        
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
                <cancelorder>
                  <auth extra=\"{$this->extra}\" login=\"{$this->login}\" pass=\"{$this->password}\" />
                  <order orderno=\"{$orderno}\" ordercode=\"\" /> 
                </cancelorder>";
        
        $result = simplexml_load_string($this->sendRequest($xml));
        
        $result = json_decode(json_encode($result), true);

//        if ($attributes = $result->attributes()) {
////            if (isset($attributes['error']) && $attributes['error'] > 0) {
////                $this->errors[] = isset($errorsText[(int) $attributes['error']]) ? $errorsText[(int) $attributes['error']] : (string) $response;
////            }
//        }
        
        $attrs = $result['order']['@attributes'];
        
        if ($attrs["error"]) {
            $errormsgru = $attrs["errormsgru"];
            
            if (MeasoftEvents::isCp1251Site()) {
                $errormsgru = iconv('UTF-8', 'CP1251', $errormsgru);
            }
            
            MeasoftEvents::setOrderPropsValue($order['ID'], 'MEASOFT_ORDER_ERROR', $errormsgru);
        }

//        file_put_contents( $_SERVER["DOCUMENT_ROOT"] .'/upload/measoft_order_cancel.log',  "  \n\n canc_order({$orderId}) \n\n {$xml} \n\n:: ". print_r($result, true) . "\n\n". print_r($attributes, true), FILE_APPEND );

//        file_put_contents( $_SERVER["DOCUMENT_ROOT"] .'/upload/measoft_order_cancel.log',  "  \n\n attrs ::". print_r($attrs, true), FILE_APPEND );


//        error="0" errormsg="OK" errormsgru="Успешно" />
    
    }
    
    function getPVZ($pvzCode)
    { //бывшая pvzList
        $res = [];
        
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
                <pvzlist>
                  <auth extra=\"{$this->extra}\" login=\"{$this->login}\" pass=\"{$this->password}\" />
                  <code>{$pvzCode}</code>
                </pvzlist>";
        
        $result = simplexml_load_string($this->sendRequest($xml));
        
        $result = json_decode(json_encode($result), true);
        
        if (is_array($result["pvz"])) {
            return $result["pvz"];
            /*foreach($result["pvz"] as $pvzArr)
            {
                if ($pvzArr["code"]==$pvzCode)
                {
                    $res = $pvzArr;

                    break;
                }
            }*/
        }
        
        return $res;
    }
    
    function pvzList($LOCATION_NAME, $pvzCode)
    {
        $res = [];
        
        if (MeasoftEvents::isCp1251Site()) {
            $LOCATION_NAME = iconv("CP2151", "UTF-8", $LOCATION_NAME);
        }
        
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
                <pvzlist>
                  <auth extra=\"{$this->extra}\" login=\"{$this->login}\" pass=\"{$this->password}\" />
                  <town>{$LOCATION_NAME}</town>
                </pvzlist>";
        
        $result = simplexml_load_string($this->sendRequest($xml));
        
        $result = json_decode(json_encode($result), true);
        
        if (isset($result["pvz"])) {
            foreach ($result["pvz"] as $pvzArr) {
                if ($pvzArr["code"] == $pvzCode) {
                    $res = $pvzArr;
                    
                    break;
                }
            }
        }
        
        return $res;
        
    }
    
    function convertTxt($txt)
    {
        if (MeasoftEvents::isCp1251Site()) {
            $txt = iconv("CP2151", "UTF-8", $txt);
        }
        
        return $txt;
        
    }
    
    function getLastOrderStatuses($cnt)
    {
        
        global $DB;
        
        $results = $DB->Query("SELECT * FROM `measoft_order_status`");
        $measoft_order_status = [];
        
        while ($row = $results->Fetch()) {
            $measoft_order_status[$row["MEASOFT_STATUS_CODE"]] = $row["BITRIX_STATUS_ID"];
        };
        
        $xml = '<statusreq>
                  <auth extra="' . $this->extra . '" login="' . $this->login . '" pass="' . $this->password . '"></auth>
                  <changes>ONLY_LAST</changes>
                </statusreq>';
        //echo $xml."\n";
        $answer = $this->sendRequest($xml);
        
        //file_put_contents($_SERVER["DOCUMENT_ROOT"]."/answer.txt",$answer);
        $this->lastResponse = $result = simplexml_load_string($answer);
        
        //MeasoftLoghandler::log( "\n\n\n -----------------   getLastOrderStatuses  ----------------- \n\n $xml \n\n answer::". print_r($this->lastResponse, true) );
        
        MeasoftLoghandler::log("\n\n\n -----------------   getLastOrderStatuses  -----------------");
        //file_put_contents($_SERVER["DOCUMENT_ROOT"]."/1.txt",print_r($result,true),FILE_APPEND);
        //$result->order->status = $statusesKeys[24];
        
        if (is_object($result))
            if ($attributes = $result->attributes()) {
                if (isset($attributes['count'])) {
                    if ($attributes['count'] == 0) {
                        // проверим, если ли подвисшие заказы, для которых надо обновить статус
                        $this->refreshClosingOrders($cnt);
                    } else {
                        
                        $prefix = MeasoftEvents::configValue('ORDER_PREFIX');
                        
                        MeasoftLoghandler::log("\n\n prefix=$prefix");
                        
                        $orderList = [];
                        
                        $errors = 0;
                        
                        foreach ($result->order as $orderObject) {
                            $orderAttributes = $orderObject->attributes();
                            
                            $orderId = str_replace($prefix, "", $orderAttributes["orderno"]);
                            
                            $statusVal = (string)$orderObject->status;
                            
                            if (is_numeric($orderId)) {
                                $orderList[$orderId] = $statusVal;
                                
                                
                                if (isset($measoft_order_status[$statusVal])) {
                                    if (defined('measoft_dbg')) {
                                        echo '$orderId=' . $orderId;
                                        print_r($orderAttributes);
                                        echo '$prefix==' . $prefix;
                                        
                                    }
                                    
                                    MeasoftLoghandler::log("\n checking orderId=$orderId statusVal=$statusVal db_s==" . $measoft_order_status[$statusVal]);
                                    
                                    if ($orderO = Bitrix\Sale\Order::load($orderId)) {
                                        
                                        if (defined('measoft_dbg')) {
                                            echo "\n order loaded";
                                        }
                                        
                                        $dbStatus = $orderO->getField("STATUS_ID");
                                        
                                        MeasoftLoghandler::log("\n orderId=$orderId statusVal=$statusVal db_s==" . $measoft_order_status[$statusVal] . ' $dbStatus=' . $dbStatus);
                                        
                                        if (defined('measoft_dbg')) {
                                            
                                            echo "\n orderId=$orderId statusVal=$statusVal db_s==" . $measoft_order_status[$statusVal] . ' $dbStatus=' . $dbStatus;
                                            
                                        }
                                        
                                        if ($dbStatus != $measoft_order_status[$statusVal]) {
                                            $orderO->setField("STATUS_ID", $measoft_order_status[$statusVal]);
                                            
                                            $saveResult = $orderO->save();
                                            
                                            if ($saveResult->isSuccess()) {
                                            
                                            } else {
                                                $errors++;
                                                MeasoftLoghandler::log("\n\n   ErrorOrderStatusSet ::: " . print_r($saveResult->getErrorMessages(), true));
                                                
                                            }
                                            
                                        }
                                        
                                    } else {
                                        //exit;
                                    }
                                    
                                }
                                
                            }
                            
                            
                        }
                        
                        
                        if ($errors == 0) $this->commitLastStatusRequest();
                        
                        $dbOrderList = \Bitrix\Sale\Internals\OrderTable::getList(array(
                            
                            'filter' => array('ID' => array_keys($orderList)),
                            'select' => ["ID", "STATUS_ID"],
                        
                        ))->fetchAll();
                        
                        if (defined('measoft_dbg')) {
                            print_r($dbOrderList);
                            
                        }
                        
                        foreach ($dbOrderList as $orderRow) {
                            $orderId = $orderRow["ID"];
                            $dbStatus = $orderRow["STATUS_ID"];
                            
                            $statusVal = $orderList[$orderId];
                            
                            if (!isset($measoft_order_status[$statusVal])) {
                                
                                if (defined('measoft_dbg')) {
                                    echo "\n\n statusVal=$statusVal";
                                }
                                
                                continue;
                            }
                            
                            if ($dbStatus != $measoft_order_status[$statusVal]) {
                                if (defined('measoft_dbg')) {
                                    echo "\n\n" . 'ch  $orderId=' . $orderId . "STATUS_ID=" . $measoft_order_status[$statusVal];
                                    
                                }
                                
                                MeasoftLoghandler::log("\n\n" . 'ch  $orderId=' . $orderId . "  STATUS_ID=" . $measoft_order_status[$statusVal]);
                                
                                if (\Bitrix\Sale\Internals\OrderTable::update($orderId, ["STATUS_ID" => $measoft_order_status[$statusVal]])) {
                                
                                }
                                
                                //if ( $orderO = Bitrix\Sale\Order::load($orderId) )
//                            {
//                                $orderO->setField("STATUS_ID", $measoft_order_status[ $statusVal ]);
//
//                                $saveResult = $orderO->save();
//
//                                if ($saveResult->isSuccess())
//                                {
//
//                                }
//                                else
//                                {
//                                    MeasoftLoghandler::log( "\n\n   ErrorOrderStatusSet ::: ". print_r($saveResult->getErrorMessages(), true) );
//
//                                }
//
//                            }
                            
                            }
                            
                        }
                        
                        
                        MeasoftLoghandler::log("\n\n orderList:::" . print_r($orderList, true));
                    }
                }
            }
        
    }
    
    function refreshClosingOrders($cnt)
    {
        global $DB;
        
        $results = $DB->Query("SELECT * FROM `measoft_order_status`");
        $measoft_order_status = [];
        
        while ($row = $results->Fetch()) {
            $measoft_order_status[$row["MEASOFT_STATUS_CODE"]] = $row["BITRIX_STATUS_ID"];
        };
        
        $result = \Bitrix\Sale\Delivery\Services\Table::getList(array(
            'select' => ["ID"],
            'filter' => array('ACTIVE' => 'Y', "CODE" => ["courier:simple", "courier:pickup"]),
        ));
        
        $deliveryList = [];
        
        while ($delivery = $result->fetch()) {
            $deliveryList[] = $delivery["ID"];
        }
        
        $orderList = Bitrix\Sale\Internals\OrderTable::getList([
            'filter' => ["!STATUS_ID" => ["F", "K", "Y", "G"], "DELIVERY_ID" => $deliveryList],
            'limit' => $cnt ? $cnt : 5,
            'order' => ['ID' => "ASC"]
        ])->fetchAll();
        
        foreach ($orderList as $order) {
            $orderId = $order["ID"];
            
            $deliveryId = $order["DELIVERY_ID"];
            
            $numberType = MeasoftEvents::configValueEx('ORDER_NUMBER', $deliveryId);
            
            $prefix = MeasoftEvents::configValueEx('ORDER_PREFIX', $deliveryId);
            
            $orderId = $order['ACCOUNT_NUMBER'];
            
            // создание объекта Measoft
            $measoft = new Measoft(MeasoftEvents::configValueEx('LOGIN', $deliveryId), MeasoftEvents::configValueEx('PASSWORD', $deliveryId), MeasoftEvents::configValueEx('CODE', $deliveryId));
            
            $measoft->statusRequest($prefix . $orderId);
            
            //echo $prefix . $orderId . "<br>";
            $status = (array)$measoft->currentStatusRow;
            
            // print_r($status) . "<br>";
            if (isset($measoft_order_status[$status[0]])) {
                $orderO = Bitrix\Sale\Order::load($order["ID"]);
                $orderO->setField("STATUS_ID", $measoft_order_status[$status[0]]);
                
                $saveResult = $orderO->save();
                //PARTLYRETURNED
                if ($saveResult->isSuccess()) {
//                    echo ' - OK! ';
                } else {
                    MeasoftLoghandler::log("\n\n   ErrorOrderStatusSet ::: " . print_r($saveResult->getErrorMessages(), true));
                    
                }
            } elseif ($status[0] == "CANCELED") {
                $orderO = Bitrix\Sale\Order::load($order["ID"]);
                $orderO->setField("CANCELED", "Y");
                
                $saveResult = $orderO->save();
                
                if ($saveResult->isSuccess()) {
//                    echo ' - OK! ';
                } else {
                    MeasoftLoghandler::log("\n\n   ErrorOrderStatusSet ::: " . print_r($saveResult->getErrorMessages(), true));
                }
            } elseif ($status[0] == "COMPLETE") {
                $orderO = Bitrix\Sale\Order::load($order["ID"]);
                $orderO->setField("STATUS_ID", "F");
                
                $saveResult = $orderO->save();
                
                if ($saveResult->isSuccess()) {
//                    echo ' - OK! ';
                } else {
                    MeasoftLoghandler::log("\n\n   ErrorOrderStatusSet ::: " . print_r($saveResult->getErrorMessages(), true));
                    
                }
                
            } elseif ($status[0] == "PARTLYRETURNED") {
                $orderO = Bitrix\Sale\Order::load($order["ID"]);
                $orderO->setField("STATUS_ID", "F");
                
                $saveResult = $orderO->save();
                
                if ($saveResult->isSuccess()) {
//                    echo ' - OK! ';
                } else {
                    MeasoftLoghandler::log("\n\n   ErrorOrderStatusSet ::: " . print_r($saveResult->getErrorMessages(), true));
                    
                }
                
            }
            
        }
    }
    
    function dbg_calculator()
    {
        $xml = '<calculator>
  <calc price="190">
    <townfrom code="1">Москва город</townfrom>
    <townto code="15209">Казань город</townto>
    <mass>0.2</mass>
    <service name="Не срочно">1</service>
    <zone>Внутри МКАД и КАД</zone>
    <price>190</price>
    <addressto code="15209">Казань город</addressto>
    <pricetype name="CUSTOMER">CUSTOMER</pricetype>
    <mindeliverydays>1</mindeliverydays>
    <maxdeliverydays>1</maxdeliverydays>
    <mindeliverydate>2020-07-13</mindeliverydate>
  </calc>
</calculator>';
        
        return $xml;
    }
    
    public function checkLogin()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
                <client>
                  <auth extra="' . $this->extra . '" login="' . $this->login . '" pass="' . $this->password . '" />
                </client>';
        $result = json_decode(json_encode(simplexml_load_string($this->sendRequest($xml))), true);
        
        if (is_array($result["error"]) && !empty($result["error"])) {
            return "N";
        }
        return "Y";
        
    }
    
    function getMapCode()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
                <client>
                  <auth extra="' . $this->extra . '" login="' . $this->login . '" pass="' . $this->password . '" />
                </client>';
        
        $this->lastResponse = $result = simplexml_load_string($this->sendRequest($xml));
        //print_r(json_decode(json_encode($result),true));
        if (isset($result->code)) {
            return intval($result->code);
        }
        
    }
}