<?
error_reporting(E_ERROR | E_PARSE);
ob_start();
define("STOP_STATISTICS", true);
define("NOT_CHECK_PERMISSIONS", true);

if ($_GET["admin_section"] == "Y") {
	define("ADMIN_SECTION", true);
} else {
	define("BX_PUBLIC_TOOLS", true);
}

if (!require ($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php")) {
	die('prolog_before.php not found!');
}

IncludeModuleLangFile(__FILE__);
if (CModule::IncludeModule("vampirus.yandexkassa") && CModule::IncludeModule("sale")) {

	$order_number   = $_REQUEST['orderNumber'];
	$amount         = floatval($_REQUEST['orderSumAmount']);
	$action         = $_REQUEST['action'];
	$order_currency = $_REQUEST['orderSumCurrencyPaycash'];
	$order_bank     = $_REQUEST['orderSumBankPaycash'];
	$order_customer = $_REQUEST['customerNumber'];
	$order_invoice  = $_REQUEST['invoiceId'];
	$md5            = $_REQUEST['md5'];
	if (!is_numeric($order_customer)) {
		//в $order_customer фио
		$order_id = $order_number;
	} else {
		$order_id = $order_customer;
	}
	if (!empty($_REQUEST['payment_id']) && class_exists('\Bitrix\Sale\PaySystem\Manager') && class_exists('\Bitrix\Sale\Registry')) {
		list($orderId, $paymentId) = \Bitrix\Sale\PaySystem\Manager::getIdsByPayment($_REQUEST['payment_id'], \Bitrix\Sale\Registry::REGISTRY_TYPE_ORDER);
		if ($orderId != $order_id) {
			return;
		}

	}
	$arOrder = CSaleOrder::GetByID($order_id);
	if (!$arOrder) {
		return;
	}

	$paymentObject = null;
	$isOldBitrix   = false;
	if (class_exists('\Bitrix\Sale\Order') && class_exists('\Bitrix\Sale\Internals\PaymentTable')) {
		$order             = \Bitrix\Sale\Order::load($order_id);
		$paymentCollection = $order->getPaymentCollection();
		if (!empty($_REQUEST['payment_id'])) {
			$paymentObject = $paymentCollection->getItemById($_REQUEST['payment_id']);
			CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"], "", array(), $paymentObject->getFieldValues());
		} else {
			foreach ($paymentCollection as $p) {
				if (method_exists($p, "getPaySystem")) {
					$paymentSystem = $p->getPaySystem();
					$actionFile    = $paymentSystem->getField('ACTION_FILE');
				} else {
					$paymentSystem = CSalePaySystem::GetByID($p->getPaymentSystemId(), 1);
					$actionFile    = $paymentSystem['PSA_ACTION_FILE'];
					$isOldBitrix   = true;
				}
				if (
					(
						$actionFile == '/bitrix/php_interface/include/sale_payment/vampirus.yandexkassa' &&
						$actionFile == '/bitrix/php_interface/include/sale_payment/yandexkassaone'
					) &&
					($p->getField('PAID') == 'N' || count($paymentCollection) == 1) &&
					CVampiRUSYandexKassaPayment::preparePrice($p->getField('SUM')) == $amount
				) {
					$paymentObject = $p;
					$payment       = \Bitrix\Sale\Internals\PaymentTable::getRow(
						array(
							'select' => array('*'),
							'filter' => array('ORDER_ID' => $order_id, 'ID' => $p->getId()),
						)
					);
					CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"], "", array(), $payment);
					break;
				}
			}
		}
		if (is_null($paymentObject)) {
			CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"]);
		}
	} else {
		CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"]);
	}

	$sum = floatval(CVampiRUSYandexKassaPayment::preparePrice(CSalePaySystemAction::GetParamValue("SHOULD_PAY", 0)));
	if (!$arOrder || $amount != $sum || ($action != 'checkOrder' && $action != 'paymentAviso')) {
		CEventLog::Add(array(
			"SEVERITY"      => "WARNING",
			"AUDIT_TYPE_ID" => "YandexKassa: " . GetMessage("VAMPIRUS.YANDEXKASSA_NOTIFY_ERROR"),
			"MODULE_ID"     => "vampirus.yandexkassa",
			"ITEM_ID"       => GetMessage("VAMPIRUS.YANDEXKASSA_ERROR_CODE") . "200",
			"DESCRIPTION"   => "ORDER:" . boolval($arOrder) . "<br>" . "SUM {$amount}={$sum}<br>" . var_export($_REQUEST, true),
		));
		echo CVampiRUSYandexKassaPayment::answer($action, $order_invoice, 200);
	} else {
		$hash = md5("$action;{$_REQUEST['orderSumAmount']};$order_currency;$order_bank;" . CVampiRUSYandexKassaPayment::getShopId() . ";$order_invoice;$order_customer;" . CVampiRUSYandexKassaPayment::getPassword());

		if (strcasecmp($hash, $md5) === 0) {
			if ($action == 'paymentAviso') {
				if (CSalePaySystemAction::GetParamValue("HOLD", 0)) {
					CVampiRUSYandexKassaPayment::updateInvoice($_REQUEST);
				}
				$arFields = array(
					"PS_STATUS"             => 'Y',
					"PS_STATUS_CODE"        => "success",
					"PS_STATUS_DESCRIPTION" => $_REQUEST['OrderDetails'],
					"PS_SUM"                => $amount,
					"PS_RESPONSE_DATE"      => new \Bitrix\Main\Type\DateTime(),
				);
				if (!$isOldBitrix) {
					$arFields['PS_INVOICE_ID'] = $_REQUEST['invoiceId'];
				}
				if ($paymentObject) {
					$paymentObject->setFields($arFields);
					$paymentObject->setPaid('Y');
					$order->save();
				} else {
					$arFields["PS_RESPONSE_DATE"] = Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG)), strtotime($_REQUEST['requestDatetime']));
					try {
						CSaleOrder::PayOrder($arOrder["ID"], "Y", true, true);
					} catch (Exception $e) {
					}
					CSaleOrder::Update($arOrder["ID"], $arFields);
				}
			}
			ob_end_clean();
			echo CVampiRUSYandexKassaPayment::answer($action, $order_invoice, 0);
		} else {
			CEventLog::Add(array(
				"SEVERITY"      => "WARNING",
				"AUDIT_TYPE_ID" => "YandexKassa: " . GetMessage("VAMPIRUS.YANDEXKASSA_NOTIFY_ERROR"),
				"MODULE_ID"     => "vampirus.yandexkassa",
				"ITEM_ID"       => GetMessage("VAMPIRUS.YANDEXKASSA_ERROR_CODE") . "1",
				"DESCRIPTION"   => "hash={$hash}<br>" . var_export($_REQUEST, true),
			));
			ob_end_clean();
			echo CVampiRUSYandexKassaPayment::answer($action, $order_invoice, 1);
		}
	}

}
exit();
require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php";
