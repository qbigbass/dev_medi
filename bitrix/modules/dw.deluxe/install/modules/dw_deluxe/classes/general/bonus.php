<?
//bonus program
class DwBonus{

    //functions
    public static function addBonus($entity){

    	//check order
    	if(!empty($entity)){

	    	//load modules
	    	\Bitrix\Main\Loader::includeModule("currency");
	    	\Bitrix\Main\Loader::includeModule("iblock");
			\Bitrix\Main\Loader::includeModule("sale");

			//new
			if($entity instanceof \Bitrix\Main\Event){

				//get event parameters
				$parameters = $entity->getParameters();

				//get current order
				$order = $parameters["ENTITY"];

			}

			//compatibility
			elseif($entity instanceof \Bitrix\Sale\Order){
				$order = $entity;
			}

			//check instance
			if(!$order instanceof \Bitrix\Sale\Order){
				return false;
			}

	    	//vars
	    	$bonusValue = 0;
	    	$arProcessedOrders = array();

	    	//get user id
	    	$userId = $order->getUserId();

	    	//get base currency
	    	$currencyCode = \Bitrix\Currency\CurrencyManager::getBaseCurrency();

			//get order payment
			$paymentCollection = $order->getPaymentCollection();

			//order is paid or all payment paid
			if(!$order->isPaid() && !$paymentCollection->isPaid()){
				return false;
			}

			//paid from user account
			if($paymentCollection->isExistsInnerPayment()){
				return false;
			}

	    	//check user
	    	if(!empty($userId)){

	    		//check user accounts
				if(!$arUserAccount = CSaleUserAccount::GetByUserID($userId, $currencyCode)){

					//create new account
					$arNewAccountFields = array("USER_ID" => $userId, "CURRENCY" => $currencyCode, "CURRENT_BUDGET" => 0);
					$accountID = CSaleUserAccount::Add($arNewAccountFields);
					if(!empty($accountID)){
						$arUserAccount = array_merge($arNewAccountFields, array(
							"ID" => $accountID,
							"NOTES" => "",
							"LOCKED" => "",
							"DATE_LOCKED" => ""
						));
					}

				}

	   			//check locked user account
	   			if(!empty($arUserAccount) && $arUserAccount["LOCKED"] != "Y"){

	   				//get order id
	   				$orderId = $order->getId();

	   				//get processed orders id
	   				if(!empty($arUserAccount["NOTES"])){
	   					$arProcessedOrders = explode(",", $arUserAccount["NOTES"]);
	   				}

	   				//check processed orders
	   				if(!empty($arProcessedOrders)){
	   					foreach($arProcessedOrders as $nextOrderId){
	   						if($orderId == $nextOrderId){
	   							return false;
	   						}
	   					}
	   				}

	   				//push order id to note
	   				$arProcessedOrders[] = $orderId;

			    	//get basket
				    $basket = $order->getBasket();

				    //get basket items
					$basketItems = $basket->getBasketItems();

					//each elements
					foreach($basketItems as $basketItem){

						//get basket item fields
						$productId = $basketItem->getProductId();
			   			$productQuantity = $basketItem->getQuantity();

			   			//get product info
						$dbProduct = CIBlockElement::GetByID($productId);

						//find product
						if($arProduct = $dbProduct->GetNext()){

							//get bonus property value by product id
				   			$dbBonus = CIBlockElement::GetProperty($arProduct["IBLOCK_ID"], $arProduct["ID"], array(), array("CODE" => "BONUS"));
							$arBonus = $dbBonus->Fetch();

							//save value
							if(!empty($arBonus["VALUE"])){
								$bonusValue += ($arBonus["VALUE"] * $productQuantity);
							}

							//check sku parent product
							else{

								//is a trade offer
								$arParentSkuProduct = CCatalogSku::GetProductInfo($arProduct["ID"]);

								//if exist parent product id
								if(is_array($arParentSkuProduct)){

									//get bonus property value
						   			$dbBonusParentProduct = CIBlockElement::GetProperty($arParentSkuProduct["IBLOCK_ID"], $arParentSkuProduct["ID"], array(), array("CODE" => "BONUS"));
									if($arBonusParentProduct = $dbBonusParentProduct->Fetch()){
										//save value
										if(!empty($arBonusParentProduct["VALUE"])){
											$bonusValue += ($arBonusParentProduct["VALUE"] * $productQuantity);
										}
									}

								}

							}

						}

					}

					//add sum to account
					if(!empty($bonusValue)){
						CSaleUserAccount::Update(
							$arUserAccount["ID"],
							array(
								"USER_ID" => $arUserAccount["USER_ID"],
								"CURRENT_BUDGET" => ($arUserAccount["CURRENT_BUDGET"] + $bonusValue),
								"CURRENCY" => $arUserAccount["CURRENCY"],
								"NOTES" => implode($arProcessedOrders, ","),
								"LOCKED" => $arUserAccount["LOCKED"],
								"DATE_LOCKED" => $arUserAccount["DATE_LOCKED"],
							)
						);
					}

				}

			}

		}

    	return $order;
    }
}
?>