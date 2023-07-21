<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
	//load modules
	if(!\Bitrix\Main\Loader::includeModule("dw.deluxe")){
		return false;
	}

	//use
	use \DigitalWeb\Basket as DwBasket;

	//check params
    $arParams["BASKET_PICTURE_WIDTH"] = !empty($arParams["BASKET_PICTURE_WIDTH"]) ? $arParams["BASKET_PICTURE_WIDTH"] : 220;
	$arParams["BASKET_PICTURE_HEIGHT"] = !empty($arParams["BASKET_PICTURE_HEIGHT"]) ? $arParams["BASKET_PICTURE_HEIGHT"] : 200;
	$arParams["LAZY_LOAD_PICTURES"] = !empty($arParams["LAZY_LOAD_PICTURES"]) ? $arParams["LAZY_LOAD_PICTURES"] : "N";
	$arParams["MIN_SUM_TO_PAYMENT"] = !empty($arParams["MIN_SUM_TO_PAYMENT"]) ? $arParams["MIN_SUM_TO_PAYMENT"] : 0;
	$arParams["PATH_TO_PAYMENT"] = !empty($arParams["PATH_TO_PAYMENT"]) ? $arParams["PATH_TO_PAYMENT"] : "payment.php";
	$arParams["DISABLE_FAST_ORDER"] = !empty($arParams["DISABLE_FAST_ORDER"]) ? $arParams["DISABLE_FAST_ORDER"] : "N";
	$arParams["MASKED_FORMAT"] = !empty($arParams["MASKED_FORMAT"]) ? $arParams["MASKED_FORMAT"] : "";
	$arParams["USE_MASKED"] = !empty($arParams["USE_MASKED"]) ? $arParams["USE_MASKED"] : "N";

	//lang (ajax need)
    $arParams["PART_STORES_AVAILABLE"] = !empty($arParams["PART_STORES_AVAILABLE"]) ? $arParams["PART_STORES_AVAILABLE"] : getMessage("PART_STORES_AVAILABLE");
    $arParams["ALL_STORES_AVAILABLE"] = !empty($arParams["ALL_STORES_AVAILABLE"]) ? $arParams["ALL_STORES_AVAILABLE"] : getMessage("ALL_STORES_AVAILABLE");
    $arParams["NO_STORES_AVAILABLE"] = !empty($arParams["NO_STORES_AVAILABLE"]) ? $arParams["NO_STORES_AVAILABLE"] : getMessage("NO_STORES_AVAILABLE");

	//application
	$application = \Bitrix\Main\Application::getInstance();

	//context
	$context = $application->getContext();

	//get request
	$request = $context->getRequest();

	//get info by orderId
	$orderId = intval($request->getQuery("orderId"));

	//new basket&order
	if(empty($orderId)){

		//set arParams
		DwBasket::setParams($arParams);

		//basket object
		$basket = DwBasket::getInstance();

		//basket
		$arBasketItems = $basket->getBasketItems();

		//check basket
		if(!empty($arBasketItems)){

			//order fileds
			$arOrder = $basket->getOrderInfo();
			$orderSum = $basket->getOrderSum();

			//append product fields to basket items
			$arProducts = $basket->addProductsInfo($arBasketItems);

			//add prices
			$arProducts = $basket->addProductPrices($arProducts);

			//basket fields
			$basketWeight = $basket->getBasketWeight();
			$basketSum = $basket->getBasketSum();

			//get measures
			$arMeasures = $basket->getMeasures();

			//currency
			$arCurrency = $basket->getCurrency();

			//person types
			$arPersonTypes = $basket->getPersonTypes();

			//get order properties & groups
			$arProperties = $basket->getOrderProperties();

			//discount list for gift
			$discountListFull = $basket->getDiscountListFull();
			$appliedDiscounts = $basket->getAppliedDiscounts();

			//get user account info
			$arUserAccount = $basket->getUserAccount();

			//current paysystem from static
			$arCurrentPaysystem = $basket->getFirstPaySystem();

			//current delivery from static
			$arCurrentDelivery = $basket->getFirstDelivery();

			//get stores
			$arStores = $basket->getStores($arProducts);

			//check minimum order amount
			$isMinOrderAmount = $basket->checkMinOrderAmount();

			//push to result array
			$arResult = array(
				"APPLIED_DISCOUNT_LIST" => $appliedDiscounts,
				"IS_MIN_ORDER_AMOUNT" => $isMinOrderAmount,
				"FULL_DISCOUNT_LIST" => $discountListFull,
				"PROPERTY_GROUPS" => $arProperties["GROUPS"],
				"PROPERTIES" => $arProperties["PROPERTIES"],
				"FIRST_PAYSYSTEM" => $arCurrentPaysystem,
				"FIRST_DELIVERY" => $arCurrentDelivery,
				"USER_ACCOUNT" => $arUserAccount,
				"PERSON_TYPES" => $arPersonTypes,
				"CURRENCY" => $arCurrency,
				"BASKET_SUM" => $basketSum,
				"MEASURES" => $arMeasures,
				"WEIGHT" => $basketWeight,
				"ORDER_SUM" => $orderSum,
				"ITEMS" => $arProducts,
				"STORES" => $arStores,
				"ORDER" => $arOrder
			);

		}

	}

	//get order info
	else{

		//get order
		$order = DwBasket::getOrderById($orderId);

		//set flag
		$arResult["CONFIRM_ORDER"] = "Y";

		//check order
		if(!empty($order)){

			//get order fields
			$arResult["ORDER"] = $order->getFieldValues();

			//allow
			$arResult["ORDER"]["ALLOW_PAY"] = $order->isAllowPay();

			//check permissions
			if(!$order->isPaid() && $order->isAllowPay()){

				//init payments
				if(DwBasket::initPayments($order)){

					//get paments
					$arResult["ORDER"]["PAYMENTS"] = DwBasket::getPayments();

					//get payment services
					$arResult["ORDER"]["PAYMENT_SERVICES"] = DwBasket::getPaymentServices();

					//get first payment id
					$arResult["ORDER"]["PAYMENT_ID"] = DwBasket::getPaymentId();

				}

			}

		}

		else{
			//set error
			$arResult["ERRORS"]["ORDER_NOT_FOUND"] = "Y";
		}

	}

	//include template
	$this->IncludeComponentTemplate();

?>