<?

use \Bitrix\Main;
use \Bitrix\Sale\Order;
use \Bitrix\Sale\Payment;
use \Bitrix\Sale\PaySystem;
use \Bitrix\Sale\PriceMaths;
use \Bitrix\Sale\Result;
use \Bitrix\Main\Localization;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Localization\Loc;
use \VampiRUS\Yookassa\MeasureCodeToStringMapper;
use \VampiRUS\Yookassa\Api as YooKassaApi;
use \VampiRUS\Yookassa\OldApi as YandexKassaAPI;

$arClasses = array(
    'VampiRUS\Yookassa\MeasureCodeToStringMapper' => 'lib/measurecodetostringmapper.php',
    'VampiRUS\Yookassa\Helper' => 'lib/helper.php',
    'VampiRUS\Yookassa\Api' => 'lib/api.php',
    'VampiRUS\Yookassa\OldApi' => 'lib/oldapi.php',
);

CModule::AddAutoloadClasses("vampirus.yandexkassa", $arClasses);

class CVampiRUSYandexKassaPayment
{
    
    const STATUS_CREATED = 0;
    const STATUS_PREAUTH = 1;
    const STATUS_CONFIRMED = 2;
    const STATUS_CANCELLED = 3;
    const STATUS_RETURNED = 4;
    
    const NO_VAT = 1;
    const VAT_0 = 2;
    const VAT_10 = 3;
    const VAT_18 = 4;
    const VAT_20 = 4;
    const VAT_110 = 5;
    const VAT_118 = 6;
    const VAT_120 = 6;
    const VAT_7_B2B = -1;
    const VAT_18_B2B = -2;
    
    static $module_id = "vampirus.yandexkassa";
    
    static $errorMsg = '';
    
    static function getModuleId()
    {
        return self::$module_id;
    }
    
    static function demoMode()
    {
        $mode = CSalePaySystemAction::GetParamValue("MODE", "");
        if (!$mode) {
            $mode = COption::GetOptionString(self::$module_id, "demo", "");
        }
        return trim($mode);
    }
    
    static function getErrorMsg()
    {
        return self::$errorMsg;
    }
    
    static function getHost()
    {
        return YandexKassaAPI::getHost();
    }
    
    static function getShopId()
    {
        $shopid = CSalePaySystemAction::GetParamValue("SHOPID", "");
        if (!$shopid) {
            $shopid = COption::GetOptionString(self::$module_id, "shopid", "");
        }
        return intval(trim($shopid));
    }
    
    static function getScid()
    {
        $scid = CSalePaySystemAction::GetParamValue("SCID", "");
        if (!$scid) {
            $scid = COption::GetOptionString(self::$module_id, "scid", "");
        }
        return intval(trim($scid));
    }
    
    static function getPassword()
    {
        $pass = CSalePaySystemAction::GetParamValue("PASSWORD", "");
        if (!$pass) {
            $pass = COption::GetOptionString(self::$module_id, "password", "");
        }
        return trim($pass);
    }
    
    static function getTaxSystem()
    {
        $tax = CSalePaySystemAction::GetParamValue("TAX_SYSTEM", "");
        if (!$tax) {
            $tax = COption::GetOptionString(self::$module_id, "tax_system", "");
        }
        return intval(trim($tax));
    }
    
    static function getProductTax()
    {
        $tax = CSalePaySystemAction::GetParamValue("PRODUCT_NDS", "");
        if (!$tax) {
            $tax = COption::GetOptionString(self::$module_id, "product_nds", "");
        }
        return intval(trim($tax));
    }
    
    static function getDeliveryTax()
    {
        $tax = CSalePaySystemAction::GetParamValue("DELIVERY_NDS", "");
        if (!$tax) {
            $tax = COption::GetOptionString(self::$module_id, "delivery_nds", "");
        }
        return intval(trim($tax));
    }
    
    public static function getPhone($phone, $prefix = '+')
    {
        $phone = preg_replace("#[^\d]#", '', $phone);
        $phone = substr($phone, -10);
        if (strlen($phone) != 10) {
            return false;
        }
        return $prefix . '7' . $phone;
    }
    
    static function convertVatId($vat_id)
    {
        $ndsArr = CCatalogVat::GetByID($vat_id)->Fetch();
        if ($ndsArr['NAME'] == GetMessage('VAMPIRUS.YANDEXKASSA_NO_NSD')) {
            $taxId = self::NO_VAT;
        } else {
            $rate = intval($ndsArr['RATE']);
            $vatIncluded = !isset($ndsArr['VAT_INCLUDED']) || $ndsArr['VAT_INCLUDED'] == 'Y';
            switch ($rate) {
                case 18:
                    $taxId = self::VAT_18_B2B;
                    break;
                case 20:
                    if (!$vatIncluded) {
                        $taxId = self::VAT_120;
                    } else {
                        $taxId = self::VAT_20;
                    }
                    
                    break;
                case 10:
                    if (!$vatIncluded) {
                        $taxId = self::VAT_110;
                        break;
                    } else {
                        $taxId = self::VAT_10;
                        break;
                    }
                
                case  0:
                    $taxId = self::VAT_0;
                    break;
                case  7:
                    $taxId = self::VAT_7_B2B;
                    break;
                default:
                    $taxId = self::NO_VAT;
                    break;
            }
        }
        return $taxId;
    }
    
    
    static function answer($action, $invoiceId, $code)
    {
        return YandexKassaAPI::answer($action, $invoiceId, $code);
    }
    
    static function preparePrice($sum)
    {
        if (class_exists("Bitrix\Sale\PriceMaths")) {
            return number_format(Bitrix\Sale\PriceMaths::roundByFormatCurrency($sum, Bitrix\Sale\Internals\SiteCurrencyTable::getSiteCurrency(SITE_ID)), 2, ".", "");
        } else {
            return number_format(round($sum, 2), 2, ".", "");
        }
    }
    
    static function updateInvoice($data)
    {
        global $DB;
        $DB->Query("UPDATE vampirus_yandexkassa SET INVOICE_ID='" . $DB->ForSql($data['invoiceId']) . "',
			STATUS=1,
			DATE='" . Date("Y-m-d H:i:s", strtotime($data['requestDatetime'])) . "'
			WHERE ID=" . intval($data['custom_field']));
    
    }
    
    static function insertInvoice($orderId, $sum, $receipt)
    {
        global $DB;
        $DB->Query("INSERT INTO vampirus_yandexkassa (ORDER_ID, AMOUNT, ACTION_AMOUNT, STATUS,DATE, RECEIPT) VALUES ('" . $DB->ForSql($orderId) . "','" . $DB->ForSql($sum) . "','" . $DB->ForSql($sum) . "', 0,NOW(),'" . $DB->ForSql($receipt) . "')");
        return $DB->LastID();
    }
    
    static function insertTransaction(Payment $payment, $data, $params)
    {
        global $DB;
        $sum = PriceMaths::roundPrecision($payment->getSum());
        $collection = $payment->getCollection();
        $order = $collection->getOrder();
        $orderId = $order->getId();
        $paymentId = $payment->getId();
        $id = $data['id'];
        $date = new Main\Type\DateTime(preg_replace("#\.d{3}Z#", "", $data['created_at']), 'Y-m-d\TH:i:s', new \DateTimeZone("UTC"));
        
        $DB->Query("INSERT INTO vampirus_yandexkassa_new (id, payment_id, order_id, amount, status,`date`,receipt) VALUES ('" . $DB->ForSql($id) . "','$paymentId','" . $DB->ForSql($orderId) . "','" . $DB->ForSql($sum) . "', 'pending','" . $date->format('Y-m-d H:i:s') . "','" . $DB->ForSql(YookassaApi::JSencode($params['receipt'])) . "')");
        return $DB->LastID();
    }
    
    static function updateTransaction(Payment $payment, $data)
    {
        global $DB;
        $setExpire = '';
        if (isset($data['expires_at'])) {
            $date = new Main\Type\DateTime(preg_replace("#\.d{3}Z#", "", $data['expires_at']), 'Y-m-d\TH:i:s', new \DateTimeZone("UTC"));
            $setExpire = "expires_at = '" . $date->format('Y-m-d H:i:s') . "',";
        }
        
        $DB->Query("UPDATE vampirus_yandexkassa_new SET
			status='" . $DB->ForSql($data['status']) . "',
			rrn ='" . $DB->ForSql($data['authorization_details']['rrn']) . "',
			extra = '" . $DB->ForSql(YookassaApi::JSencode($data)) . "',
			refundable = " . intval($data['refundable']) . ",
			$setExpire
			saved = " . intval($data['payment_method']['saved']) . "
			WHERE id='" . $DB->ForSql($data['id']) . "'");
    
    }
    
    
    static function getInvoiceList($filter)
    {
        global $DB;
        $where = array();
        foreach ($filter as $key => $item) {
            $where[] = "$key = '" . $DB->ForSql($item) . "'";
        }
        if ($where) {
            $where = ' AND ' . implode(" AND ", $where);
        } else {
            $where = '';
        }
        $sql = "SELECT * FROM vampirus_yandexkassa WHERE STATUS!=0 $where order by ORDER_ID desc";
        $dbRes = $DB->Query($sql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        return $dbRes;
    }
    
    static function getTransactionList($filter, $sort = [])
    {
        global $DB;
        
        $byOrder = [];
        if (!empty($sort)) {
            foreach ($sort as $by => $order) {
                $order = mb_strtolower($order);
                if ($order != 'asc') {
                    $order = 'desc';
                }
                $byOrder[] = "$by $order";
            }
            $byOrder = implode(',', $byOrder);
        } else {
            $byOrder = 'order_id desc, `date` desc';
        }
        
        $where = array();
        foreach ($filter as $key => $item) {
            $where[] = "$key= '" . $DB->ForSql($item) . "'";
        }
        
        if ($where) {
            $where = ' AND ' . implode(" AND ", $where);
        } else {
            $where = '';
        }
        $sql = "
			SELECT *
			FROM vampirus_yandexkassa_new as y
			LEFT JOIN b_sale_order as o on o.ID=y.order_id
			WHERE status!='pending' $where order by $byOrder";
        
        
        $dbRes = $DB->Query($sql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        return $dbRes;
    }
    
    static function getTransactionStatus($payment, $transactionId)
    {
        $paymentSystem = $payment->getPaySystem();
        $params = $paymentSystem->getParamsBusValue($payment);
        
        $headers = YooKassaApi::getHeaders($params);
        $headers['Idempotence-Key'] = YooKassaApi::getIdempotenceKey();
        
        $url = "https://api.yookassa.ru/v3/payments/" . $transactionId;
        
        
        $sendResult = YooKassaApi::send($url, $headers, array(), 'GET');
        
        $response = $sendResult->getData();
        if (!$sendResult->isSuccess()) {
            CEventLog::Add(array(
                "SEVERITY" => "ERROR",
                "AUDIT_TYPE_ID" => Localization\Loc::getMessage("VAMPIRUS.YANDEXKASSA_STATUS_CHECK_ERROR"),
                "MODULE_ID" => "vampirus.yandexkassa",
                "ITEM_ID" => $payment->getId(),
                "DESCRIPTION" => implode(',', $sendResult->getErrorMessages()),
            ));
            return false;
        }
        return $response['status'];
    }
    
    static function getStatusName($status_id)
    {
        $status_id = intval($status_id);
        return GetMessage('VAMPIRUS.YANDEXKASSA_STATUS_' . $status_id);
    }
    
    
    static function sendApiRequest($post)
    {
        global $DB;
        $res = false;
        $result = new Result();
        foreach ($post['action'] as $id => $action) {
            $sum = $post['sum'][$id];
            $res = $DB->Query("SELECT * FROM vampirus_yandexkassa_new WHERE id='" . $DB->ForSql($id) . "'");
            $data = $res->Fetch();
            $order = Order::load($data['order_id']);
            $payment = $order->getPaymentCollection()->getItemById($data['payment_id']);
            $service = PaySystem\Manager::getObjectById($payment->getPaymentSystemId());
            switch ($action) {
                case 'cancel':
                    $canResult = $service->cancel($payment);
                    if (!$canResult->isSuccess()) {
                        return $result->addErrors($canResult->getErrors());
                    }
                    $DB->Query("UPDATE vampirus_yandexkassa_new SET status='canceled' WHERE id='" . $DB->ForSql($id) . "'");
                    $r = $payment->setPaid('N');
                    
                    $payment->setFields($canResult->getPsData());
                    break;
                case 'confirm':
                    $conResult = $service->confirm($payment, $sum);
                    if (!$conResult->isSuccess()) {
                        return $result->addErrors($conResult->getErrors());
                    }
                    $DB->Query("UPDATE vampirus_yandexkassa_new SET status='succeeded' WHERE id='" . $DB->ForSql($id) . "'");
                    if ($payment->getSum() != $sum) {
                        $payment->setField('PAID', 'N');
                        $payment->setField('SUM', $sum);
                        $payment->setField('PAID', 'Y');
                    }
                    $order->setField('STATUS_ID', 'PS');
                    $payment->setFields($conResult->getPsData());
                    break;
                case 'return':
                    $_POST['refund_cause'] = $post['cause'][$id];
                    $sum = $payment->getSum();
                    $sum = \CCurrencyRates::ConvertCurrency($sum, $payment->getField('CURRENCY'), 'RUB');
                    $refResult = $service->refund($payment, $sum);
                    if (!$refResult->isSuccess()) {
                        return $result->addErrors($refResult->getErrors());
                    }
                    
                    $resultSum = $payment->getSum() - $sum;
                    if ($resultSum == 0) {
                        $payment->setField('PAID', 'N');
                        
                    } else {
                        $payment->setField('PAID', 'N');
                        $payment->setField('SUM', $resultSum);
                        $payment->setField('PAID', 'Y');
                    }
                    $payment->setFields($refResult->getPsData());
                    break;
            }
            /*
            $r = $order->getPaymentCollection()->save();
            if (!$r->isSuccess())
            {
                $result->addErrors($r->getErrors());
                return $result;
            }*/
            //$order->refreshData();
            $order->save();
            return $result;
            
        }
    }
    
    static function getReceiptItems(Payment $payment, $config, $total, $secondReceipt = false)
    {
        $handler = self::getPaymentHandler($payment);
        return $handler->getReceiptItems($payment, $config, $total, $secondReceipt);
    }
    
    public static function getProductVat($item)
    {
        if (\Bitrix\Main\Loader::includeModule('catalog')) {
            $dbRes = \CCatalogProduct::GetVATInfo($item->getProductId());
            $ndsArr = $dbRes->Fetch();
            $taxId = self::convertVatId($ndsArr['ID']);
            return $taxId;
        }
        return self::NO_VAT;
    }
    
    public static function getShipmentVat($shipment)
    {
        $delivery = \Bitrix\Sale\Delivery\Services\Manager::getById($shipment->getDeliveryId());
        if (is_null($delivery['VAT_ID'])) {
            return self::NO_VAT;
        }
        return self::convertVatId($delivery['VAT_ID']);
    }
    
    public static function insertRefundInfo($data)
    {
        global $DB;
        $DB->Query("INSERT IGNORE INTO vampirus_yandexkassa_refund (id, payment_id, status, description, date, amount) values ('" . $DB->ForSql($data['id']) . "', '" . $DB->ForSql($data['payment_id']) . "', '" . $DB->ForSql($data['status']) . "', '" . $DB->ForSql($_POST['refund_cause']) . "', NOW() , '" . $DB->ForSql($data['amount']['value']) . "')");
    }
    
    private static function getPaymentHandler(Payment $payment)
    {
        $paymentSystem = $payment->getPaySystem();
        if (!$paymentSystem) {
            return null;
        }
        $paymentSystemClass = get_class($paymentSystem);
        $reflectionClass = new ReflectionClass($paymentSystemClass);
        $reflectionProperty = $reflectionClass->getProperty('handler');
        $reflectionProperty->setAccessible(true);
        $handler = $reflectionProperty->getValue($paymentSystem);
        return $handler;
    }
    
    public static function OnSaleStatusOrderChangeHandler(\Bitrix\Main\Event $event)
    {
        $order = $event->getParameter("ENTITY");
        $orderStatus = $event->getParameter("VALUE");
        $paymentCollection = $order->getPaymentCollection();
        
        foreach ($paymentCollection as $payment) {
            $handler = self::getPaymentHandler($payment);
            if (is_null($handler)) {
                continue;
            }
            $paymentSystem = $payment->getPaySystem();
            $params = $paymentSystem->getParamsBusValue($payment);
            if (
                $handler instanceof Sale\Handlers\PaySystem\YandexCheckoutVSHandler
                && $payment->isPaid()
                && $params['YANDEX_CHECKOUT_PRINT_SECOND_ON_STATUS'] === $orderStatus
            ) {
                self::printSecondReceipt($payment);
            }
        }
    }
    
    public static function OnSaleOrderSavedHandler(\Bitrix\Main\Event $event)
    {
        $order = $event->getParameter("ENTITY");
        $oldValues = $event->getParameter("VALUES");
        $paymentCollection = $order->getPaymentCollection();
        $selectedPayment = null;
        if (!(isset($oldValues['DEDUCTED']) && $oldValues['DEDUCTED'] == 'N')) {
            return;
        }
        foreach ($paymentCollection as $payment) {
            $handler = self::getPaymentHandler($payment);
            $paymentSystem = $payment->getPaySystem();
            $params = $paymentSystem->getParamsBusValue($payment);
            if (
                $handler instanceof Sale\Handlers\PaySystem\YandexCheckoutVSHandler
                && $payment->isPaid()
                && $params['YANDEX_CHECKOUT_PRINT_SECOND'] === '2'
            ) {
                self::printSecondReceipt($payment);
            }
        }
    }
    
    public static function OnSalePaymentEntitySavedHandler(\Bitrix\Main\Event $event)
    {
        $connection = \Bitrix\Main\Application::getConnection();
        $sqlHelper = $connection->getSqlHelper();
        
        $payment = $event->getParameter("ENTITY");
        $orderId = intval($payment->getOrderId());
        
        $handler = self::getPaymentHandler($payment);
        
        if (!($handler instanceof Sale\Handlers\PaySystem\YandexCheckoutVSHandler)) {
            return false;
        }
        $id = sha1($orderId . time() . mt_rand());
        $connection->query("
			INSERT IGNORE INTO vampirus_yandexkassa_order (id, order_id) values ('$id',$orderId)");
    }
    
    public static function onSaleAdminOrderInfoBlockShowHandler(\Bitrix\Main\Event $event)
    {
        $connection = \Bitrix\Main\Application::getConnection();
        $sqlHelper = $connection->getSqlHelper();
        
        $order = $event->getParameter("ORDER");
        if ($order->isPaid()) {
            return new \Bitrix\Main\EventResult(
                \Bitrix\Main\EventResult::ERROR, [], 'sale'
            );
        }
        $orderId = $order->getId();
        
        $data = $connection
            ->query("
						SELECT id FROM vampirus_yandexkassa_order WHERE order_id=$orderId")
            ->fetch();
        if (!$data) {
            return new \Bitrix\Main\EventResult(
                \Bitrix\Main\EventResult::ERROR, [], 'sale'
            );
        }
        
        return new \Bitrix\Main\EventResult(
            \Bitrix\Main\EventResult::SUCCESS,
            [
                [
                    'TITLE' => Localization\Loc::getMessage("VAMPIRUS.YANDEXKASSA_PAYMENT_LINK"),
                    'VALUE' => '<a href="https://' . $_SERVER["SERVER_NAME"] . '/bitrix/tools/yandexcheckoutvs_pay.php?id=' . $data['id'] . '" onclick="(function(link){navigator.clipboard.writeText(link.href);})(this);return false;">' . Localization\Loc::getMessage("VAMPIRUS.YANDEXKASSA_COPY_PAYMENT_LINK") . '</a>',
                    'ID' => 'vs_yookassa_payment_link'
                ],
            ],
            'sale'
        );
    }
    
    static public function printSecondReceipt($payment)
    {
        global $DB;
        $paymentSystem = $payment->getPaySystem();
        $params = $paymentSystem->getParamsBusValue($payment);
        $sum = \CCurrencyRates::ConvertCurrency($payment->getSum(), $payment->getField('CURRENCY'), 'RUB');
        $sum = (string)PriceMaths::roundPrecision($sum);
        $customer = [];
        
        $id = $payment->getField('PS_INVOICE_ID');
        $data = $DB->Query("SELECT receipt, second FROM vampirus_yandexkassa_new WHERE id='" . $DB->ForSql($id) . "'")->Fetch();
        if ($data['second']) {
            return;
        }
        $oldReceipt = \Bitrix\Main\Web\Json::decode($data['receipt']);
        $contact = current($oldReceipt['customer']);
        
        if (strpos($contact, "@") !== false) {
            $customer['email'] = $contact;
        } else {
            $customer['phone'] = $contact;
        }
        $receipt = [
            "customer" => $customer,
            "items" => static::getReceiptItems($payment, $sum, true),
            "settlements" => [[
                "type" => "prepayment",
                "amount" => [
                    "value" => $sum,
                    "currency" => "RUB",
                ]
            ]],
            "type" => "payment",
            "send" => "true",
            "payment_id" => $payment->getField('PS_INVOICE_ID'),
        ];
        if (!empty($params['YANDEX_CHECKOUT_SNO'])) {
            $receipt['tax_system_code'] = $params['YANDEX_CHECKOUT_SNO'];
        }
        $url = 'https://api.yookassa.ru/v3/receipts';
        $headers = YooKassaApi::getHeaders($params);
        $headers['Idempotence-Key'] = YooKassaApi::getIdempotenceKey();
        
        $sendResult = YooKassaApi::send($url, $headers, $receipt);
        $response = $sendResult->getData();
        if (!$sendResult->isSuccess()) {
            CEventLog::Add(array(
                "SEVERITY" => "ERROR",
                "AUDIT_TYPE_ID" => Localization\Loc::getMessage("VAMPIRUS.YANDEXKASSA_SEND_RECEIPT_ERROR"),
                "MODULE_ID" => "vampirus.yandexkassa",
                "ITEM_ID" => $payment->getId(),
                "DESCRIPTION" => implode(',', $sendResult->getErrorMessages()),
            ));
        } else {
            $DB->Query("UPDATE vampirus_yandexkassa_new set second=1 WHERE id='" . $DB->ForSql($id) . "'");
        }
    }
    
    static public function onNewOrderEmail($orderId, &$eventName, &$arFields)
    {
        $arFields['VS_YOOKASSA_PAYMENT'] = static::prepareEmailTemplate($orderId);
    }
    
    static public function onOrderStatusSendEmail($orderId, &$eventName, &$arFields, $val)
    {
        $arFields['VS_YOOKASSA_PAYMENT'] = static::prepareEmailTemplate($orderId);
    }
    
    static public function prepareEmailTemplate($orderId)
    {
        $template = Option::get(self::getModuleId(), "email_template");
        \Bitrix\Main\Loader::includeModule('sale');
        
        
        $connection = \Bitrix\Main\Application::getConnection();
        $sqlHelper = $connection->getSqlHelper();
        
        $order = Order::load($orderId);
        
        if (!$order->isAllowPay()) {
            return '';
        }
        
        if ($order->isPaid()) {
            return '';
        }
        
        $data = $connection
            ->query("
						SELECT id FROM vampirus_yandexkassa_order WHERE order_id=$orderId")
            ->fetch();
        if (!$data) {
            return '';
        }
        
        $siteData = \Bitrix\Main\SiteTable::getList(array(
            'filter' => array('LID' => $order->getSiteId()),
        ));
        $site = $siteData->fetch();
        
        $link = 'https://' . $site["SERVER_NAME"] . '/bitrix/tools/yandexcheckoutvs_pay.php?id=' . $data['id'];
        
        $replace = [
            '#PAYMENT_LINK#' => "<a href='$link'>" . Loc::getMessage("VAMPIRUS.YANDEXKASSA_EMAIL_PAY") . "</a>",
            '#PAYMENT_LINK_RAW#' => $link,
        ];
        $message = str_replace(array_keys($replace), array_values($replace), $template);
        return $message;
    }
    
}


?>