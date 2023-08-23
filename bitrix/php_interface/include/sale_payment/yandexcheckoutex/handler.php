<?
namespace Sale\Handlers\PaySystem;
use Bitrix\Main;
use Bitrix\Main\Request;
use Bitrix\Sale\Payment;
use VampiRUS\Yookassa\Helper;
use VampiRUS\Yookassa\Api;

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/vampirus.yandexkassa/payment/yandexcheckoutvs/handler.php");

class YandexCheckoutExHandler extends YandexCheckoutVSHandler
{
    public function getReceiptItems($payment, $total, $secondReceipt)
	{
        $roundCoef = 100;
        if ((int)Main\Config\Option::get('sale', 'value_precision') == 0) {
            $roundCoef = 1;
        }
        $total *= $roundCoef;

		$items              = array();
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
		$ffd120 = $this->getBusinessValue($payment, 'YANDEX_CHECKOUT_FFD120') == '2';
		if (!$paymentMode) {
			$paymentMode = 'full_prepayment';
		}
		if ($secondReceipt) {
			$paymentMode = 'full_payment';
		}


		foreach ($shipmentCollection as $shipment) {
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
					"amount"          => array('value' => intval(\Bitrix\Sale\PriceMaths::roundPrecision($itemPrice) * $roundCoef) , 'currency' => 'RUB'),
					"vat_code"        => $productVat?$productVat:\CVampiRUSYandexKassaPayment::getProductVat($basketItem),
					"payment_mode"    => $paymentMode,
					"payment_subject" => $paymentSubject,
				);

                $imeasure = Helper::getMeasureCode($basketItem);
                if ($imeasure == 'other' && $item['vat_code'] == 2)
                {
                    $item['vat_code'] = 3;
                }

                $vatrate = strtolower( str_replace(" ", "", $basketItem->getField('NOTES')));

                if ($vatrate == 'vat0' || $vatrate == 'ндс0')
                {
                    $item['vat_code'] = 2;
                }
                elseif ($vatrate == 'vat10' || $vatrate == 'ндс10')
                {
                    $item['vat_code'] = 3;
                }
                elseif ($vatrate == 'vat20' || $vatrate == 'ндс20')
                {
                    $item['vat_code'] = 4;
                }
                elseif ($vatrate == 'novat' || $vatrate == 'безндс')
                {
                    $item['vat_code'] = 1;
                }


                if ($item['vat_code'] == '2') $item['vat_code'] = '1';

				if ($agentType) {
					$item['agent_type'] = $agentType;
				}

				if ($ffd120) {
					$item['measure'] = Helper::getMeasureCode($basketItem);
				}

				if (method_exists($basketItem, "isSupportedMarkingCode") && $basketItem->isSupportedMarkingCode())
				{
					$item['quantity'] = 1000;
					$item['amount'] = array('value' => intval(\Bitrix\Sale\PriceMaths::roundPrecision($itemPrice) * $roundCoef) , 'currency' => 'RUB');

					$shipmentItem->getShipmentItemStoreCollection()->rewind();

					$storeCollection = $shipmentItem->getShipmentItemStoreCollection();
					for ($i = $basketItem->getQuantity(); $i > 0; $i--)
					{
						$markingCode = '';
						$markingCodeRaw = '';

						/** @var ShipmentItemStore $itemStore */
						if ($itemStore = $storeCollection->current())
						{
							$markingCode = Helper::buildTag1162(
								$itemStore->getMarkingCode(),
								$basketItem->getMarkingCodeGroup()
							);

							$markingCodeRaw = trim($itemStore->getMarkingCode());

							$storeCollection->next();
						}

						$markingCode = trim($markingCode);
						if(!$ffd120 && $markingCode) {
							$item['product_code'] = $markingCode;
						}

						if($ffd120 && $markingCodeRaw) {
							$item['mark_code_info'] = [
								'mark_code_raw' => $markingCodeRaw
							];
							$item['mark_mode'] = 0;

						}

						$items[] = $item;
					}
				} else {
					$items[] = $item;
				}

			}
			if ($noDelivery != 2 && !$shipment->isSystem() && $shipment->getPrice()) {
				 $item = array(
					"description"     => $this->getShipmentName($config, $shipment),
					"quantity"        => 1000,
					"amount"          => array('value' => intval(\Bitrix\Sale\PriceMaths::roundPrecision($shipment->getPrice()) * $roundCoef), 'currency' => 'RUB'),
					"vat_code"        => $deliveryVat?$deliveryVat:\CVampiRUSYandexKassaPayment::getShipmentVat($shipment),
					"payment_mode"    => $paymentMode,
					"payment_subject" => $paymentSubjectDelivery,
				);

                if ($item['vat_code'] == '2') $item['vat_code'] = '1';
				if ($agentType) {
					$item['agent_type'] = $agentType;
				}
				$items[] = $item;
			}
		}
 
		$items = Helper::normalizeReceiptItems($items, $total);
		$items = Helper::correctDimmensoin($items, $roundCoef);
		return $items;
	}
}
