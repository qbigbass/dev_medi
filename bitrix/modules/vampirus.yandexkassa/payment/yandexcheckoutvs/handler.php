<?php

namespace Sale\Handlers\PaySystem;

use Bitrix\Main;
use Bitrix\Main\Localization;
use Bitrix\Main\Request;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\PriceMaths;
use VampiRUS\Yookassa\Api;
use VampiRUS\Yookassa\Helper;

Localization\Loc::loadMessages(__FILE__);

\CModule::IncludeModule("vampirus.yandexkassa");

if (!interface_exists("Sale\Handlers\PaySystem\YandexCheckoutVSHandlerProxy")) {

	if (interface_exists("\Bitrix\Sale\PaySystem\IPartialHold")) {
		interface YandexCheckoutVSHandlerProxy extends PaySystem\IRefund, PaySystem\IPartialHold
		{}
	} else {
		interface YandexCheckoutVSHandlerProxy extends PaySystem\IRefund, PaySystem\IHold
		{}
	}
}

/**
 * Class YandexCheckoutVSHandler
 * @package Sale\Handlers\PaySystem
 */
class YandexCheckoutVSHandler extends PaySystem\ServiceHandler implements YandexCheckoutVSHandlerProxy
{
	const PAYMENT_STATUS_WAITING_FOR_CAPTURE = 'waiting_for_capture';
	const PAYMENT_STATUS_SUCCEEDED           = 'succeeded';
	const PAYMENT_STATUS_CANCELED            = 'canceled';
	const PAYMENT_STATUS_PENDING             = 'pending';

	const PAYMENT_METHOD_SMART          = '';
	const PAYMENT_METHOD_ALFABANK       = 'alfabank';
	const PAYMENT_METHOD_BANK_CARD      = 'bank_card';
	const PAYMENT_METHOD_YANDEX_MONEY   = 'yoo_money';
	const PAYMENT_METHOD_SBERBANK       = 'sberbank';
	const PAYMENT_METHOD_QIWI           = 'qiwi';
	const PAYMENT_METHOD_WEBMONEY       = 'webmoney';
	const PAYMENT_METHOD_CASH           = 'cash';
	const PAYMENT_METHOD_MOBILE_BALANCE = 'mobile_balance';
	const PAYMENT_METHOD_TINKOFF        = 'tinkoff_bank';
	const PAYMENT_METHOD_INSTALLMENTS   = 'installments';
	const PAYMENT_METHOD_B2B            = 'b2b_sberbank';
	const PAYMENT_METHOD_SBP            = 'sbp';

	const URL = 'https://api.yookassa.ru/v3';

	public $contact = false;

	/**
	 * @param Payment $payment
	 * @param Request|null $request
	 * @return PaySystem\ServiceResult
	 * @throws Main\ArgumentNullException
	 * @throws Main\ArgumentOutOfRangeException
	 * @throws Main\ArgumentTypeException
	 * @throws Main\ObjectException
	 */
	public function initiatePay(Payment $payment, Request $request = null)
	{
		if ($request === null) {
			$request = Main\Context::getCurrent()->getRequest();
		}

		if ($payment->isPaid()) {
			$error = 'Yandex.Checkout: initiatePay: Already payed';
			if (class_exists('Bitrix\Sale\PaySystem\Logger')) {
				PaySystem\Logger::addError($error);
			}
			$result = new PaySystem\ServiceResult();
			$result->addError(new Main\Error($error));
			return $result;
		}

		$result = $this->initiatePayInternal($payment, $request);
		if (!$result->isSuccess()) {
			$error = 'Yandex.Checkout: initiatePay: ' . join('\n', $result->getErrorMessages());
			if (class_exists('Bitrix\Sale\PaySystem\Logger')) {
				PaySystem\Logger::addError($error);
			}
		}

		return $result;
	}

	/**
	 * @param Payment $payment
	 * @param Request $request
	 * @return PaySystem\ServiceResult
	 * @throws Main\ArgumentNullException
	 */
	protected function initiatePayInternal(Payment $payment, Request $request)
	{
		if ($this->hasPaymentMethodFields() &&
			!$this->isFillPaymentMethodFields($request)
		) {
			$params = array(
				'SUM'            => PriceMaths::roundPrecision($payment->getSum()),
				'CURRENCY'       => $payment->getField('CURRENCY'),
				'FIELDS'         => $this->getPaymentMethodFields(),
				'PAYMENT_METHOD' => $this->service->getField('PS_MODE'),
			);
			$this->setExtraParams($params);

			return $this->showTemplate($payment, "template_query");
		}
		$collection = $payment->getCollection();
		$order      = $collection->getOrder();
		try {
			if ($propUserEmail = $order->getPropertyCollection()->getUserEmail()) {
				$this->contact = $propUserEmail->getValue();
			}
		} catch (\Exception $e) {}

		if (!$this->contact) {
			$this->contact = $this->getBusinessValue($payment, 'BUYER_PERSON_EMAIL') ? $this->getBusinessValue($payment, 'BUYER_PERSON_EMAIL') : $request->get('email');
		}

		if (!$this->contact) {
			$phoneField = \CSaleOrderPropsValue::GetList([], ['ORDER_ID' => $order->getId(), 'CODE' => 'PHONE'])->Fetch();
			$phone      = \CVampiRUSYandexKassaPayment::getPhone($phoneField['VALUE'], '');
			if ($phone) {
				$this->contact = $phone;
			}
		}

		if (!$this->contact) {
			return $this->showTemplate($payment, "template_email");
		}

		$result = new PaySystem\ServiceResult();

		$createResult = $this->createYandexPayment($payment, $request);
		if (!$createResult->isSuccess()) {
			$result->addErrors($createResult->getErrors());
			return $result;
		}

		$yandexPaymentData = $createResult->getData();
		if ($yandexPaymentData['status'] === static::PAYMENT_STATUS_CANCELED) {
			return $result->addError(
				new Main\Error(
					Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_ERROR_PAYMENT_CANCELED')
				)
			);
		}

		$result->setPsData(array(
			'PS_INVOICE_ID' => $yandexPaymentData['id'],
		));
		$_SESSION['YANDEXCHECKOUTVS_ID'] = $yandexPaymentData['id'];

		if ($yandexPaymentData['status'] === static::PAYMENT_STATUS_SUCCEEDED) {
			LocalRedirect($this->getReturnUrl($payment), true);
		}

		if(method_exists($result, "setPaymentUrl")) {
			$result->setPaymentUrl($yandexPaymentData['confirmation']['confirmation_url']);
		}

		if ($request->get('PAYMENT_ID') && $request->get('ORDER_ID') && !$request->isAjaxRequest()) {
			LocalRedirect($yandexPaymentData['confirmation']['confirmation_url'], true);
		}

		$params = array(
			'URL'       => $yandexPaymentData['confirmation']['confirmation_url'],
			'ID'        => $yandexPaymentData['id'],
			'CURRENCY'  => $payment->getField('CURRENCY'),
			'SUM'       => PriceMaths::roundPrecision($payment->getSum()),
			'savedCard' => $this->getSavedCard($payment),
		);



		$template = "template";
		if ($this->isSetExternalPaymentType()) {
			$template .= "_success";
		}

		if ($this->getBusinessValue($payment, 'YANDEX_CHECKOUT_WIDGET') === 'Y') {
			$params['TOKEN'] = $yandexPaymentData['confirmation']['confirmation_token'];
			$params['RETURN_URL'] = $this->getReturnUrl($payment);
			$params['TYPE'] = $this->service->getField('PS_MODE');
			$template .= "_widget";
		}

		$this->setExtraParams($params);

		$showTemplateResult = $this->showTemplate($payment, $template);
		if ($showTemplateResult->isSuccess()) {
			$result->setTemplate($showTemplateResult->getTemplate());
		} else {
			$result->addErrors($showTemplateResult->getErrors());
		}

		return $result;
	}

	protected function getSavedCard($payment)
	{
		global $USER;
		if ($this->getBusinessValue($payment, 'SAVE_CARD') !== '2') {
			return false;
		}

		$connection = \Bitrix\Main\Application::getConnection();
		$sqlHelper  = $connection->getSqlHelper();
		$userId     = $USER->GetID();

		if (!$userId) {
			return false;
		}

		$res = $connection->query("
			SELECT y.id, y.extra
			FROM b_sale_order as o
			LEFT JOIN vampirus_yandexkassa_new as y on o.ID=y.order_id
			WHERE y.saved=1 AND o.PAYED='Y' AND o.USER_ID=" . $userId);
		$data = $res->fetch();
		if (!$data) {
			return false;
		}
		$data['extra'] = Api::JSdecode($data['extra']);
		$data['image'] = base64_encode($this->getCardTypeImage($data['extra']['payment_method']['card']['card_type']));
		return $data;
	}

	/**
	 * @return bool
	 */
	protected function isSetExternalPaymentType()
	{
		$externalPayment = array(static::PAYMENT_METHOD_ALFABANK);

		return in_array($this->service->getField('PS_MODE'), $externalPayment);
	}

	/**
	 * @param Payment $payment
	 * @param Request $request
	 * @return PaySystem\ServiceResult
	 * @throws Main\ArgumentException
	 * @throws Main\ArgumentNullException
	 */
	protected function createYandexPayment(Payment $payment, Request $request)
	{
		$result = new PaySystem\ServiceResult();

		$url = $this->getUrl($payment, 'pay');

		$params = $this->getYandexPaymentQueryParams($payment, $request);
		$cfg    = $this->getParamsBusValue($payment);

		$headers                    = Api::getHeaders($cfg);
		$headers['Idempotence-Key'] = Api::getIdempotenceKey();

		$sendResult = $this->send($url, $headers, $params);
		if (!$sendResult->isSuccess()) {
			$result->addErrors($sendResult->getErrors());
			return $result;
		}

		$response = $sendResult->getData();
		\CVampiRUSYandexKassaPayment::insertTransaction($payment, $response, $params);
		$result->setData($response);

		return $result;
	}

	/**
	 * @param $url
	 * @param array $headers
	 * @param array $params
	 * @return PaySystem\ServiceResult
	 * @throws Main\ArgumentException
	 */
	protected function send($url, array $headers, array $params = array(), $method = 'POST')
	{
		return Api::send($url, $headers, $params, $method);
	}

	protected function getReturnUrl(Payment $payment)
	{
		$url   = $this->getBusinessValue($payment, 'YANDEX_CHECKOUT_RETURN_URL') ?: $this->service->getContext()->getUrl();
		$order = $payment->getCollection()->getOrder();
		$hash  = '';
		if (method_exists($order, 'getHash')) {
			$hash = $order->getHash();
		}
		$replace = [
			'#ID#'           => $payment->getId(),
			'#ORDER_ID#'     => $order->getId(),
			'#ORDER_NUMBER#' => $this->getBusinessValue($payment, 'ORDER_NUMBER'),
			'#ORDER_HASH#'   => $hash,
		];
		return str_replace(array_keys($replace), array_values($replace), $url);
	}

	/**
	 * @param Payment $payment
	 * @param Request $request
	 * @return array
	 * @throws Main\ArgumentNullException
	 */
	protected function getYandexPaymentQueryParams(Payment $payment, Request $request)
	{
		include __DIR__ . '/../../install/version.php';
		$sum              = \CCurrencyRates::ConvertCurrency($payment->getSum(), $payment->getField('CURRENCY'), 'RUB');
		$paymentShouldPay = (string) PriceMaths::roundPrecision($sum);
		$query            = array(
			'amount'       => array(
				'value'    => $paymentShouldPay,
				'currency' => 'RUB',
			),
			'capture'      => $this->getBusinessValue($payment, 'YANDEX_CHECKOUT_HOLD') == '2' ? false : true,
			'confirmation' => array(
				'type'       => 'redirect',
				'return_url' => $this->getReturnUrl($payment),
			),
			'description'  => $this->getPaymentDescription($payment),
			'metadata'     => array(
				'BX_PAYMENT_NUMBER' => $payment->getId(),
				'BX_PAYSYSTEM_CODE' => $this->service->getField('ID'),
				'BX_HANDLER'        => 'YANDEX_CHECKOUT_VAMPIRUS',
				'cms_name'          => '1c-bitrix_vampirus',
				'version'           => $arModuleVersion['VERSION'],
			),
		);
		if (
			!(
				$request->get('PAYMENT_ID') && $request->get('ORDER_ID') && !$request->isAjaxRequest()
			) &&
			$this->getBusinessValue($payment, 'YANDEX_CHECKOUT_WIDGET') === 'Y') {
			$query['confirmation'] = ['type' => 'embedded'];
		}

		$articleId = $this->getBusinessValue($payment, 'YANDEX_CHECKOUT_SHOP_ARTICLE_ID');
		if ($articleId) {
			$query['recipient'] = ['gateway_id' => $articleId];
		}

		if ($this->service->getField('PS_MODE') !== static::PAYMENT_METHOD_SMART
			&& $this->getBusinessValue($payment, 'YANDEX_CHECKOUT_WIDGET') !== 'Y') {
			$query['payment_method_data'] = array(
				'type' => $this->service->getField('PS_MODE'),
			);

			if ($this->isSetExternalPaymentType()) {
				$query['confirmation']['type'] = 'external';
			}

			if ($this->hasPaymentMethodFields()) {
				$fields = $this->getPaymentMethodFields();
				if ($fields) {
					foreach ($fields as $field) {
						$query['payment_method_data'][$field] = $request->get($field);
					}
				}
			}
		}

		if ($this->service->getField('PS_MODE') === static::PAYMENT_METHOD_B2B) {
			$query['payment_method_data'] += $this->getB2BFields($payment, $paymentShouldPay);
		}
		$query['receipt'] = $this->getReceipt($payment, $paymentShouldPay);
		if ($paymentMethodId = $request->get('payment_method_id')) {
			$query['payment_method_id'] = $paymentMethodId;
			unset($query['payment_method_data']);
		}

		if ($this->getBusinessValue($payment, 'SAVE_CARD') === '2') {
			$query['save_payment_method'] = true;
		}

		$meta = $this->getMetadata($payment, $request, $query);

		$query['metadata'] = array_merge($query['metadata'], $meta);

		return $query;
	}

	protected function getMetadata(Payment $payment, Request $request, $query)
	{
		return [];
	}

	protected function getB2BFields(Payment $payment, $paymentShouldPay)
	{
		$fields = [
			'payment_purpose' => $this->getPaymentDescription($payment),
		];
		$receipt  = $this->getReceipt($payment, $paymentShouldPay);
		$vatCodes = [];
		foreach ($receipt['items'] as $item) {
			$vatCodes[$item['vat_code']] = 1;
		}
		if (count($vatCodes) != 1) {
			throw new Exception("Unsupporded VAT", 1);
		}
		$vatXref = [
			\CVampiRUSYandexKassaPayment::VAT_10     => 10,
			\CVampiRUSYandexKassaPayment::VAT_20     => 20,
			\CVampiRUSYandexKassaPayment::VAT_18_B2B => 18,
			\CVampiRUSYandexKassaPayment::VAT_7_B2B  => 7,
		];
		$vat                = key($vatCodes);
		$fields['vat_data'] = [];
		if ($vat === \CVampiRUSYandexKassaPayment::NO_VAT) {
			$fields['vat_data']['type'] = 'untaxed';
		} else {
			$fields['vat_data']['type']   = 'calculated';
			$fields['vat_data']['amount'] = [
				'value'    => number_format($paymentShouldPay * $vatXref[$vat] / 100, 2, '.', ''),
				'currency' => 'RUB',
			];
			$fields['vat_data']['rate'] = $vatXref[$vat];
		}

		return $fields;
	}

	/**
	 * @param Payment $payment
	 * @return mixed
	 * @throws Main\ArgumentException
	 * @throws Main\ArgumentOutOfRangeException
	 * @throws Main\NotImplementedException
	 */
	protected function getPaymentDescription(Payment $payment)
	{
		/** @var PaymentCollection $collection */
		$collection = $payment->getCollection();
		$order      = $collection->getOrder();
		$userEmail  = $order->getPropertyCollection()->getUserEmail();

		$name = '';
		try {
			if ($prop = $order->getPropertyCollection()->getPayerName()) {
				$name = $prop->getValue();
			}
		} catch (\Exception $e) {}

		$description = str_replace(
			[
				'#PAYMENT_NUMBER#',
				'#ORDER_NUMBER#',
				'#PAYMENT_ID#',
				'#ORDER_ID#',
				'#USER_EMAIL#',
				'#FIO#',
			],
			[
				$payment->getField('ACCOUNT_NUMBER'),
				$order->getField('ACCOUNT_NUMBER'),
				$payment->getId(),
				$order->getId(),
				($userEmail) ? $userEmail->getValue() : '',
				$name,
			],
			$this->getBusinessValue($payment, 'YANDEX_CHECKOUT_DESCRIPTION')
		);
		if (!$description) {
			$description = Localization\Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_ORDER_PAYMENT") . $this->getBusinessValue($payment, 'ORDER_NUMBER');
		}

		return substr($description, 0, 128);
	}

	public function getYandexkassaPaymentSubject(Payment $payment)
	{
		$paymentObject = $this->getBusinessValue($payment, 'YANDEX_CHECKOUT_PAYMENT_SUBJECT');
		if (!$paymentObject) {
			$paymentObject = 'commodity';
		}
		return $paymentObject;
	}

	public function getYandexkassaPaymentSubjectDelivery(Payment $payment)
	{
		$paymentObjectDelivery = $this->getBusinessValue($payment, 'YANDEX_CHECKOUT_PAYMENT_SUBJECT_DELIVERY');
		if (!$paymentObjectDelivery) {
			$paymentObjectDelivery = 'commodity';
		}
		return $paymentObjectDelivery;
	}

	public function getYandexkassaPaymentMode(Payment $payment)
	{
		$paymentMethod = $this->getBusinessValue($payment, 'YANDEX_CHECKOUT_PAYMENT_MODE');
		if (!$paymentMethod) {
			$paymentMethod = 'full_prepayment';
		}
		return $paymentMethod;
	}

	protected function getReceipt($payment, $paymentShouldPay, $forceFullPayment = false)
	{
		$config   = $this->getParamsBusValue($payment);
		$customer = [];
		if (strpos($this->contact, "@") !== false) {
			$customer['email'] = $this->contact;
		} else {
			$customer['phone'] = $this->contact;
		}
		$receipt = [
			"tax_system_code" => $this->getBusinessValue($payment, 'YANDEX_CHECKOUT_SNO'),
			"customer"        => $customer,
			"items"           => $this->getReceiptItems($payment, $paymentShouldPay, $forceFullPayment),
		];
		if ($this->getBusinessValue($payment, 'BUYER_PERSON_INN')) {
			$receipt["customer"]["inn"]       = $this->getBusinessValue($payment, 'BUYER_PERSON_INN');
			$receipt["customer"]["full_name"] = $this->getBusinessValue($payment, 'BUYER_PERSON_NAME');
		}
		return $receipt;
	}

	public function getReceiptItems($payment, $total, $secondReceipt)
	{
		$roundCoef = 100;
		if ((int)Main\Config\Option::get('sale', 'value_precision') == 0) {
			$roundCoef = 1;
		}
		$total *= $roundCoef;
		$items              = [
			'system' => [],
			'other' => []
		];
		$shipmentCollection = $payment
			->getCollection()
			->getOrder()
			->getShipmentCollection();

		$productVat  = intval($this->getBusinessValue($payment, 'YANDEX_CHECKOUT_PRODUCT_NDS'));
		$deliveryVat = intval($this->getBusinessValue($payment, 'YANDEX_CHECKOUT_DELIVERY_NDS'));
		$noDelivery  = intval($this->getBusinessValue($payment, 'YANDEX_CHECKOUT_NO_DELIVERY'));
		$agentType   = $this->getBusinessValue($payment, 'YANDEX_CHECKOUT_PAYMENT_AGENT_TYPE');

		$paymentSubject = $this->getBusinessValue($payment, 'YANDEX_CHECKOUT_PAYMENT_SUBJECT');
		if (!$paymentSubject) {
			$paymentSubject = 'commodity';
		}

		$paymentSubjectDelivery = $this->getBusinessValue($payment, 'YANDEX_CHECKOUT_PAYMENT_SUBJECT_DELIVERY');
		if (!$paymentSubjectDelivery) {
			$paymentSubjectDelivery = 'commodity';
		}

		$paymentMode = $this->getBusinessValue($payment, 'YANDEX_CHECKOUT_PAYMENT_MODE');
		$ffd120      = $this->getBusinessValue($payment, 'YANDEX_CHECKOUT_FFD120') == '2';
		if (!$paymentMode) {
			$paymentMode = 'full_prepayment';
		}
		if ($secondReceipt) {
			$paymentMode = 'full_payment';
		}

		$deliveryItem = null;

		foreach ($shipmentCollection as $shipment) {
			$itemKey = 'other';
			if ($shipment->isSystem()) {
				$itemKey = 'system';
			}
			$shipmentItemCollection = $shipment->getShipmentItemCollection();

			foreach ($shipmentItemCollection as $shipmentItem) {
				$basketItem = $shipmentItem->getBasketItem();
				if ($basketItem->isBundleChild()) {
					continue;
				}

				if ($basketItem->getFinalPrice() <= 0) {
					continue;
				}

				if ($shipmentItem->getQuantity() <= 0) {
					continue;
				}
				$itemPrice = $basketItem->getPrice();
				if (method_exists($basketItem, "getPriceWithVat")) {
					$itemPrice = $basketItem->getPriceWithVat();
				}
				$item = array(
					"description"     => mb_substr($basketItem->getField('NAME'), 0, 127),
					"quantity"        => $shipmentItem->getQuantity() * 1000,
					"amount"          => array('value' => intval(round(\Bitrix\Sale\PriceMaths::roundPrecision($itemPrice) * $roundCoef)), 'currency' => 'RUB'),
					"vat_code"        => $productVat ? $productVat : \CVampiRUSYandexKassaPayment::getProductVat($basketItem),
					"payment_mode"    => $paymentMode,
					"payment_subject" => $paymentSubject,
				);
				if ($agentType) {
					$item['agent_type'] = $agentType;
				}

				if ($ffd120) {
					$item['measure'] = Helper::getMeasureCode($basketItem);
				}

				if (method_exists($basketItem, "isSupportedMarkingCode") && $basketItem->isSupportedMarkingCode()) {
					$item['quantity'] = 1000;
					$item['amount']   = array('value' => intval(\Bitrix\Sale\PriceMaths::roundPrecision($itemPrice) * $roundCoef), 'currency' => 'RUB');

					$shipmentItem->getShipmentItemStoreCollection()->rewind();

					$storeCollection = $shipmentItem->getShipmentItemStoreCollection();
					for ($i = $basketItem->getQuantity(); $i > 0; $i--) {
						$markingCode    = '';
						$markingCodeRaw = '';

						/** @var ShipmentItemStore $itemStore */
						if ($itemStore = $storeCollection->current()) {
							$markingCode = Helper::buildTag1162(
								$itemStore->getMarkingCode(),
								$basketItem->getMarkingCodeGroup()
							);

							$markingCodeRaw = trim($itemStore->getMarkingCode());

							$storeCollection->next();
						}

						$markingCode = trim($markingCode);
						if (!$ffd120 && $markingCode) {
							$item['product_code'] = $markingCode;
						}

						if ($ffd120 && $markingCodeRaw) {
							$markFormat = $this->getBusinessValue($payment, 'YANDEX_CHECKOUT_FFD120_CODE_FORMAT');
							if (!$markFormat) {
								$markFormat = 'mark_code_raw';
							}
							$item['mark_code_info'] = [
								$markFormat => $markingCodeRaw,
							];
							$item['mark_mode'] = 0;

						}

						$items[$itemKey][] = $item;
					}
				} else {
					$items[$itemKey][] = $item;
				}

			}
			if ($noDelivery != 2 && !$shipment->isSystem() && $shipment->getPrice()) {
				$deliveryItem = array(
					"description"     => $this->getShipmentName($payment, $shipment),
					"quantity"        => 1000,
					"amount"          => array('value' => intval(\Bitrix\Sale\PriceMaths::roundPrecision($shipment->getPrice()) * $roundCoef), 'currency' => 'RUB'),
					"vat_code"        => $deliveryVat ? $deliveryVat : \CVampiRUSYandexKassaPayment::getShipmentVat($shipment),
					"payment_mode"    => $paymentMode,
					"payment_subject" => $paymentSubjectDelivery,
				);
				if ($agentType) {
					$item['agent_type'] = $agentType;
				}
				//$items[] = $item;
			}
		}
		if ($deliveryItem) {
			$total -= $deliveryItem['amount']['value'];
		}
		if (empty($items['other'])) {
			$items = $items['system'];
		} else {
			$items = $items['other'];
		}
		$items = Helper::normalizeReceiptItems($items, $total);
		if ($total == 0) {
			//only delivery
			$items = [];
		}
		if ($deliveryItem) {
			$items[] = $deliveryItem;
		}
		$items = Helper::correctDimmensoin($items, $roundCoef);
		return $items;
	}

	protected function getShipmentName($payment, $shipment)
	{
		$deliveryName = $this->getBusinessValue($payment, 'YANDEX_CHECKOUT_SINGLE_DELIVERY_NAME');
		if (!$deliveryName) {
			$deliveryName = $shipment->getDeliveryName();
		}
		$deliveryName = substr($deliveryName, 0, 128);
		return $deliveryName;
	}

	/**
	 * @return array
	 */
	public function getCurrencyList()
	{
		return array('RUB');
	}

	public function proccessRefundNotify(Payment $payment, $data)
	{
		$result     = new PaySystem\ServiceResult();
		$connection = \Bitrix\Main\Application::getConnection();
		$sqlHelper  = $connection->getSqlHelper();

		if ($data['status'] !== static::PAYMENT_STATUS_SUCCEEDED) {
			return $result;
		}

		if (!$payment->isPaid()) {
			return $result;
		}

		\CVampiRUSYandexKassaPayment::insertRefundInfo($data);

		if (PriceMaths::roundPrecision($data['amount']['value']) != $payment->getSum()) {
			return $result;
		}

		$result->setOperationType(PaySystem\ServiceResult::MONEY_LEAVING);

		$connection
			->query("UPDATE vampirus_yandexkassa_new set status='refunded'
					WHERE id='" . $sqlHelper->forSql($data['payment_id']) . "'")
		;

		return $result;
	}

	/**
	 * @param Payment $payment
	 * @param Request $request
	 * @return PaySystem\ServiceResult
	 * @throws Main\ObjectException
	 * @throws \Exception
	 *
	 */
	public function processRequest(Payment $payment, Request $request)
	{
		$connection = \Bitrix\Main\Application::getConnection();
		$sqlHelper  = $connection->getSqlHelper();

		$result = new PaySystem\ServiceResult();

		$inputStream = static::readFromStream();

		if (class_exists('Bitrix\Sale\PaySystem\Logger')) {
			PaySystem\Logger::addDebugInfo('Yandex.Checkout: inputStream: ' . $inputStream);
		}

		$data = Api::JSdecode($inputStream);

		if ($data === false) {
			$result->addError(new Main\Error('VAMPIRUS.YANDEXKASSA_CHECKOUT_ERROR_QUERY'));
			return $result;
		}

		if ($data['event'] === 'refund.succeeded') {
			return $this->proccessRefundNotify($payment, $data['object']);
		}

		$sendResult = $this->getTransactionInfo($payment, $data['object']['id']);
		if (!$sendResult->isSuccess()) {
			$result->addErrors($sendResult->getErrors());
			return $result;
		}

		$response = $sendResult->getData();
		if (empty($response['metadata']['BX_PAYMENT_NUMBER']
			|| $response['metadata']['BX_PAYMENT_NUMBER'] != $payment->getId())
		) {
			$result->addError(new Main\Error(Localization\Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_WRONG_PAYMENT_ID")));
			return $result;
		}
		//$response = $data['object'];

		$transactionInfo = $connection
			->query("
					SELECT *
					FROM vampirus_yandexkassa_new
					WHERE id='" . $sqlHelper->forSql($response['id']) . "'")
			->fetch();
		\CVampiRUSYandexKassaPayment::updateTransaction($payment, $response);

		if (
			(
				$response['status'] === static::PAYMENT_STATUS_SUCCEEDED
				|| $response['status'] === static::PAYMENT_STATUS_WAITING_FOR_CAPTURE
			) && !$payment->isPaid()
		) {
			$description = Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_TRANSACTION') . $response['id'];
			$fields      = array(
				"PS_STATUS_CODE"        => substr($response['status'], 0, 5),
				"PS_STATUS_DESCRIPTION" => $description,
				"PS_SUM"                => $response['amount']['value'],
				"PS_STATUS"             => 'N',
				"PS_CURRENCY"           => $response['amount']['currency'],
				"PS_INVOICE_ID"         => $response['id'],
				"PS_RESPONSE_DATE"      => new Main\Type\DateTime(),
				"PS_STATUS_MESSAGE"     => "RRN: " . $response['authorization_details']['rrn'] . ", Auth code: " . $response['authorization_details']['auth_code'],
			);

			if ($this->isSumCorrect($payment, $response)) {
				$fields["PS_STATUS"] = 'Y';

				if (class_exists('Bitrix\Sale\PaySystem\Logger')) {
					PaySystem\Logger::addDebugInfo(
						'Yandex.Checkout: PS_CHANGE_STATUS_PAY=' . $this->getBusinessValue($payment, 'PS_CHANGE_STATUS_PAY')
					);
				}
				$result->setOperationType(PaySystem\ServiceResult::MONEY_COMING);
			} else {
				$error = Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_ERROR_SUM');
				$fields['PS_STATUS_DESCRIPTION'] .= ' ' . $error;
				$result->addError(new Main\Error($error));
			}

			$result->setPsData($fields);
		} elseif (
			$response['status'] === static::PAYMENT_STATUS_CANCELED
			&& $payment->isPaid()
			&& $payment->getField('PS_INVOICE_ID') === $response['id']
			&& $this->isSumCorrect($payment, $response)
			&& in_array($transactionInfo['status'], [static::PAYMENT_STATUS_SUCCEEDED, static::PAYMENT_STATUS_WAITING_FOR_CAPTURE])
			&& $this->getBusinessValue($payment, 'CAN_CANCEL_PAYMENT') === '2'
		) {
			$fields = array(
				"PS_STATUS_CODE"   => substr($response['status'], 0, 5),
				"PS_SUM"           => $response['amount']['value'],
				"PS_STATUS"        => 'N',
				"PS_CURRENCY"      => $response['amount']['currency'],
				"PS_INVOICE_ID"    => $response['id'],
				"PS_RESPONSE_DATE" => new Main\Type\DateTime(),
			);
			$result->setPsData($fields);
			$result->setOperationType(PaySystem\ServiceResult::MONEY_LEAVING);
		}

		if (!$result->isSuccess()) {
			$error = 'Yandex.Checkout: processRequest: ' . join('\n', $result->getErrorMessages());
			if (class_exists('Bitrix\Sale\PaySystem\Logger')) {
				PaySystem\Logger::addError($error);
			}
		}

		return $result;
	}

	protected function getTransactionInfo($payment, $id)
	{
		$url = $this->getUrl($payment, 'pay');

		$cfg = $this->getParamsBusValue($payment);

		$headers                    = Api::getHeaders($cfg);
		$headers['Idempotence-Key'] = Api::getIdempotenceKey();

		$sendResult = $this->send($url . "/" . $id, $headers, [], 'GET');
		return $sendResult;
	}

	/**
	 * @param Payment $payment
	 * @param array $paymentData
	 * @return bool
	 * @throws Main\ArgumentNullException
	 * @throws Main\ArgumentOutOfRangeException
	 * @throws Main\ObjectException
	 */
	protected function isSumCorrect(Payment $payment, array $paymentData)
	{
		$endsum = \CCurrencyRates::ConvertCurrency($payment->getSum(), $payment->getField('CURRENCY'), 'RUB');
		if (class_exists('Bitrix\Sale\PaySystem\Logger')) {
			PaySystem\Logger::addDebugInfo(
				'Yandex.Checkout: yandexSum=' . PriceMaths::roundPrecision($paymentData['amount']['value']) . "; paymentSum=" . PriceMaths::roundPrecision($endsum)
			);
		}

		return PriceMaths::roundPrecision($paymentData['amount']['value']) === PriceMaths::roundPrecision($endsum);
	}

	/**
	 * @param Payment $payment
	 * @param $refundableSum
	 * @return PaySystem\ServiceResult
	 * @throws Main\ArgumentNullException
	 * @throws \Exception
	 */
	public function refund(Payment $payment, $refundableSum)
	{
		global $DB;
		$result = new PaySystem\ServiceResult();

		$id            = $payment->getField('PS_INVOICE_ID');
		$data          = $DB->Query("SELECT receipt FROM vampirus_yandexkassa_new WHERE id='" . $DB->ForSql($id) . "'")->Fetch();
		$oldReceipt    = \Bitrix\Main\Web\Json::decode($data['receipt']);
		$this->contact = current($oldReceipt['customer']);

		$url     = $this->getUrl($payment, 'refund');
		$params  = $this->getRefundQueryParams($payment, $refundableSum);
		$cfg     = $this->getParamsBusValue($payment);
		$headers = Api::getHeaders($cfg);

		$headers['Idempotence-Key'] = Api::getIdempotenceKey();

		$sendResult = $this->send($url, $headers, $params);
		if (!$sendResult->isSuccess()) {
			$result->addErrors($sendResult->getErrors());

			$error = 'Yandex.Checkout: refund: ' . join('\n', $sendResult->getErrorMessages());
			if (class_exists('Bitrix\Sale\PaySystem\Logger')) {
				PaySystem\Logger::addError($error);
			}

			return $result;
		}

		$response = $sendResult->getData();

		\CVampiRUSYandexKassaPayment::insertRefundInfo($response);

		if ($response['status'] === static::PAYMENT_STATUS_SUCCEEDED
			&& PriceMaths::roundPrecision($response['amount']['value']) === PriceMaths::roundPrecision($refundableSum)
		) {
			$result->setOperationType(PaySystem\ServiceResult::MONEY_LEAVING);
			$fields = array(
				"PS_STATUS_CODE"        => substr("refund", 0, 5),
				"PS_STATUS_DESCRIPTION" => Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_PAYMENT_REFUND') . ' ' . $refundableSum,
				"PS_RESPONSE_DATE"      => new Main\Type\DateTime(),
			);
			$result->setPsData($fields);
			$DB->Query("UPDATE vampirus_yandexkassa_new set status='refunded' where id='" . $DB->ForSql($id) . "'");
		}

		return $result;
	}

	/**
	 * @param Payment $payment
	 * @return PaySystem\ServiceResult
	 * @throws \Exception
	 */
	public function cancel(Payment $payment)
	{
		$result                     = new PaySystem\ServiceResult();
		$url                        = $this->getUrl($payment, 'cancel');
		$cfg                        = $this->getParamsBusValue($payment);
		$headers                    = Api::getHeaders($cfg);
		$headers['Idempotence-Key'] = Api::getIdempotenceKey();

		$sendResult = $this->send($url, $headers, [], "POST");
		if (!$sendResult->isSuccess()) {
			$result->addErrors($sendResult->getErrors());
			$error = 'Yandex.Checkout: cancel: ' . join('\n', $sendResult->getErrorMessages());
			if (class_exists('Bitrix\Sale\PaySystem\Logger')) {
				PaySystem\Logger::addError($error);
			}
			return $result;
		}
		$fields = array(
			"PS_STATUS_CODE"        => substr("canceled", 0, 5),
			"PS_STATUS_DESCRIPTION" => Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_ERROR_PAYMENT_CANCELED'),
			"PS_RESPONSE_DATE"      => new Main\Type\DateTime(),
			"PS_STATUS"             => 'N',
		);
		$result->setPsData($fields);

		return $result;
	}

	/**
	 * @param Payment $payment
	 * @return PaySystem\ServiceResult
	 * @throws Main\ArgumentNullException
	 * @throws Main\ObjectException
	 * @throws \Exception
	 */
	public function confirm(Payment $payment, $sum = 0)
	{
		global $DB;

		$result                     = new PaySystem\ServiceResult();
		$url                        = $this->getUrl($payment, 'confirm');
		$cfg                        = $this->getParamsBusValue($payment);
		$headers                    = Api::getHeaders($cfg);
		$headers['Idempotence-Key'] = Api::getIdempotenceKey();

		$id            = $payment->getField('PS_INVOICE_ID');
		$data          = $DB->Query("SELECT receipt FROM vampirus_yandexkassa_new WHERE id='" . $DB->ForSql($id) . "'")->Fetch();
		$oldReceipt    = \Bitrix\Main\Web\Json::decode($data['receipt']);
		$this->contact = current($oldReceipt['customer']);

		if ($sum == 0) {
			$sum = $payment->getSum();
		}

		$receipt = $this->getReceipt($payment, $sum);
		$params  = array(
			'amount'  => array(
				'value'    => (string) PriceMaths::roundPrecision($sum),
				'currency' => $payment->getField('CURRENCY'),
			),
			'receipt' => $receipt,
		);

		$sendResult = $this->send($url, $headers, $params);
		if ($sendResult->isSuccess()) {
			$data = $sendResult->getData();
			if (!isset($data['status'])) {
				return $result->addError(new Error(Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_NO_STATUS_IN_RESPONSE')));
			}
			if ($data['status'] !== static::PAYMENT_STATUS_SUCCEEDED) {
				return $result->addError(new Error(Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CONFIRMATION_ERROR')));
			}
			$DB->Query("UPDATE vampirus_yandexkassa_new set receipt='" . $DB->ForSql(\Bitrix\Main\Web\Json::encode($receipt)) . "' WHERE id='" . $DB->ForSql($id) . "'");
			$response    = $sendResult->getData();
			$description = Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_TRANSACTION') . $response['id'];

			$fields = array(
				"PS_STATUS_CODE"        => substr($response['status'], 0, 5),
				"PS_STATUS_DESCRIPTION" => $description,
				"PS_SUM"                => $response['amount']['value'],
				"PS_CURRENCY"           => $response['amount']['currency'],
				"PS_RESPONSE_DATE"      => new Main\Type\DateTime(),
				"PS_STATUS"             => "Y",
			);
/*
if ($response['status'] === static::PAYMENT_STATUS_SUCCEEDED) {
$fields["PS_STATUS"] = "Y";
$result->setOperationType(PaySystem\ServiceResult::MONEY_COMING);
} else {
$fields["PS_STATUS"] = "N";
}*/

			$result->setPsData($fields);
		} else {
			$result->addErrors($sendResult->getErrors());
			$error = 'Yandex.Checkout: confirm: ' . join('\n', $sendResult->getErrorMessages());
			if (class_exists('Bitrix\Sale\PaySystem\Logger')) {
				PaySystem\Logger::addError($error);
			}
			return $result;
		}

		return $result;
	}

	/**
	 * @param Payment $payment
	 * @param $refundableSum
	 * @return array
	 * @throws Main\ArgumentNullException
	 */
	protected function getRefundQueryParams(Payment $payment, $refundableSum)
	{
		global $DB;
		$res           = $DB->Query("SELECT second FROM vampirus_yandexkassa_new WHERE id='" . $DB->ForSql($payment->getField('PS_INVOICE_ID')) . "'");
		$secondPrinted = $res->Fetch()['second'];
		$receipt       = $this->getReceipt($payment, $refundableSum, $secondPrinted);
		return array(
			'payment_id'  => $payment->getField('PS_INVOICE_ID'),
			'amount'      => array(
				'value'    => (string) PriceMaths::roundPrecision($refundableSum),
				'currency' => $payment->getField('CURRENCY'),
			),
			'receipt'     => $receipt,
			'description' => $_POST['refund_cause'],
		);
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getPaymentIdFromRequest(Request $request)
	{
		$inputStream = static::readFromStream();

		if (!$inputStream) {
			return false;
		}

		$data = Api::JSdecode($inputStream);
		if ($data === false) {
			return false;
		}

		if ($data['event'] === 'refund.succeeded') {
			$connection = \Bitrix\Main\Application::getConnection();
			$sqlHelper  = $connection->getSqlHelper();

			$result = $connection
				->query("SELECT payment_id FROM vampirus_yandexkassa_new
					WHERE id='" . $sqlHelper->forSql($data['object']['payment_id']) . "'")
				->fetch()
			;
			if (!$result) {
				return false;
			}

			return $result['payment_id'];
		}

		return $data['object']['metadata']['BX_PAYMENT_NUMBER'];

		return false;
	}

	/**
	 * @return array
	 */
	public static function getHandlerModeList()
	{
		return array(
			static::PAYMENT_METHOD_SMART        => Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_SMART'),
			static::PAYMENT_METHOD_BANK_CARD    => Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_BANK_CARDS'),
			static::PAYMENT_METHOD_YANDEX_MONEY => Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_YANDEX_MONEY'),
			static::PAYMENT_METHOD_SBERBANK     => Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_SBERBANK'),
			static::PAYMENT_METHOD_QIWI         => Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_QIWI'),
			static::PAYMENT_METHOD_WEBMONEY     => Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_WEBMONEY'),
			static::PAYMENT_METHOD_ALFABANK     => Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_ALFABANK'),
			static::PAYMENT_METHOD_CASH         => Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_CASH'),
			static::PAYMENT_METHOD_TINKOFF      => Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_TINKOFF'),
			static::PAYMENT_METHOD_INSTALLMENTS => Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_INSTALLMENTS'),
			static::PAYMENT_METHOD_B2B          => Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_B2B'),
			static::PAYMENT_METHOD_SBP          => Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_SBP'),
		);
	}

	/**
	 * @return array
	 */
	protected function getUrlList()
	{
		return array(
			'pay'      => static::URL . '/payments',
			'payment'  => static::URL . '/payments/#payment_id#',
			'refund'   => static::URL . '/refunds',
			'receipts' => static::URL . '/receipts',
			'confirm'  => static::URL . '/payments/#payment_id#/capture',
			'cancel'   => static::URL . '/payments/#payment_id#/cancel',
		);
	}

	/**
	 * @param Request $request
	 * @param int $paySystemId
	 * @return bool
	 * @throws Main\ArgumentNullException
	 * @throws Main\ArgumentOutOfRangeException
	 * @throws Main\ArgumentTypeException
	 * @throws Main\ObjectException
	 */
	public static function isMyResponse(Request $request, $paySystemId)
	{
		$inputStream = static::readFromStream();

		if ($inputStream) {
			if (class_exists('Bitrix\Sale\PaySystem\Logger')) {
				PaySystem\Logger::addDebugInfo('Yandex.Checkout: Check my response: paySystemId=' . $paySystemId . ' inputStream=' . $inputStream);
			}

			$data = Api::JSdecode($inputStream);
			if ($data === false) {
				return false;
			}

			if (isset($data['object']['metadata']['BX_HANDLER'])
				&& $data['object']['metadata']['BX_HANDLER'] === 'YANDEX_CHECKOUT_VAMPIRUS'
				&& isset($data['object']['metadata']['BX_PAYSYSTEM_CODE'])
				&& (int) $data['object']['metadata']['BX_PAYSYSTEM_CODE'] === (int) $paySystemId
			) {
				return true;
			}
		}

		if ($request->get('vs_refund_support') == 1) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool|string
	 */
	protected static function readFromStream()
	{
		return file_get_contents('php://input');
	}

	/**
	 * @param Payment $payment
	 * @param string $action
	 * @return string
	 */
	protected function getUrl(Payment $payment = null, $action)
	{
		$url = parent::getUrl($payment, $action);
		if ($payment !== null &&
			(
				$action === 'cancel'
				|| $action === 'confirm'
				|| $action === 'payment'
			)
		) {
			$url = str_replace('#payment_id#', $payment->getField('PS_INVOICE_ID'), $url);
		}

		return $url;
	}

	/**
	 * @return array
	 */
	protected function getPaymentMethodFields()
	{
		$paymentMethodFields = array(
			static::PAYMENT_METHOD_ALFABANK       => array('login'),
			static::PAYMENT_METHOD_QIWI           => array('phone'),
			static::PAYMENT_METHOD_MOBILE_BALANCE => array('phone'),
		);

		if (isset($paymentMethodFields[$this->service->getField('PS_MODE')])) {
			return $paymentMethodFields[$this->service->getField('PS_MODE')];
		}

		return [];
	}

	/**
	 * @param Request $request
	 * @return bool
	 */
	protected function isFillPaymentMethodFields(Request $request)
	{
		$fields = $this->getPaymentMethodFields();
		if ($fields) {
			foreach ($fields as $field) {
				if (!$request->get($field)) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * @return bool
	 */
	protected function hasPaymentMethodFields()
	{
		$fields = $this->getPaymentMethodFields();
		return (bool) $fields;
	}

	protected function getCardTypeImage($type)
	{
		$icon = __DIR__ . '/template/icons/' . strtolower($type) . '.svg';
		if (!file_exists($icon)) {
			return file_get_contents(__DIR__ . '/template/icons/unknown.svg');

		}
		return file_get_contents($icon);
	}
}
