<?
	//bitrix uses
	use Bitrix\Main,
	    Bitrix\Main\Localization\Loc as Loc,
	    Bitrix\Main\Loader,
	    Bitrix\Main\Config\Option,
	    Bitrix\Sale\Delivery,
	    Bitrix\Sale\PaySystem,
	    Bitrix\Sale\PersonType,
	    Bitrix\Sale,
	    Bitrix\Sale\Order,
	    Bitrix\Sale\DiscountCouponsManager,
	    Bitrix\Main\Context;

	//other uses
	use DigitalWeb\Basket as DwBasket;
	use DigitalWeb\BasketAjax as DwBasketAjax;

	//increase productivity
	define("STOP_STATISTICS", true);
	define("NO_AGENT_CHECK", true);

	//load bitrix core
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

	//langs
	\Bitrix\Main\Localization\Loc::loadMessages(dirname(__FILE__)."/ajax.php");

	//load modules
	if(!\Bitrix\Main\Loader::includeModule("dw.deluxe")){
		die;
	}

	//application
	$application = \Bitrix\Main\Application::getInstance();

	//context
	$context = $application->getContext();

	//get request
	$request = $context->getRequest();

	//get request vars
	$actionType = $request->getPost("actionType");
	$siteId = $request->getPost("siteId");

	//check request act
	if(!empty($actionType)){

		//update basket item (quantity)
		if($actionType == "updateQuantity"){

			//vars
			$arReturn = array(
				"status" => true
			);

			//get basket id
			$basketId = $request->getPost("basketId");

			//get quantity
			$quantity = $request->getPost("quantity");

			//hide measures
			$hideMeasures = $request->getPost("hide-measures");

			//check vars
			if(!empty($basketId) && !empty($siteId) && !empty($quantity)){

		        //check modules
		        if(!Loader::includeModule("sale")){
		            return false;
		        }

		        //update & check state
		        if(DwBasket::updateQuantity($basketId, $quantity, $siteId)){

					//basket object
					$basket = DwBasket::getInstance();

					//currency
					$currencyCode = $basket->getCurrencyCode();

					//basket items
					$arBasketItems = $basket->getBasketItems();

					//append product fields to basket items
					$arProducts = $basket->addProductsInfo($arBasketItems);

					//add prices
					$arProducts = $basket->addProductPrices($arProducts);

					//push to arResult
					foreach($arProducts as $arNextProduct){
						if($arNextProduct["BASKET_ID"] == $basketId){
							$arReturn["compilation"]["item"] = $arNextProduct; break(1);
						}
					}

					//get additonal
					if(!empty($arReturn["compilation"]["item"])){

						//get measures
						if($hideMeasures != "Y"){
							$arReturn["compilation"]["measures"] = $basket->getMeasures();
						}

						//get product sum
					    $arReturn["compilation"]["item"]["BASE_SUM_FORMATED"] = \CCurrencyLang::CurrencyFormat(($arReturn["compilation"]["item"]["BASE_PRICE"] * $arReturn["compilation"]["item"]["QUANTITY"]), $currencyCode);
					    $arReturn["compilation"]["item"]["SUM_FORMATED"] = \CCurrencyLang::CurrencyFormat(($arReturn["compilation"]["item"]["PRICE"] * $arReturn["compilation"]["item"]["QUANTITY"]), $currencyCode);

					}

			        //set error
			        else{
			        	//C4_BASKET_COMPILATION_ERROR
			        	DwBasket::setError(\Bitrix\Main\Localization\Loc::GetMessage("C4_BASKET_COMPILATION_ERROR"));
			        }

		        }

		        //set error
		        else{
		        	//C4_BASKET_UPDATE_ERROR
		        	DwBasket::setError(\Bitrix\Main\Localization\Loc::GetMessage("C4_BASKET_UPDATE_ERROR"));
		        }

		    }

			if($arErrors = DwBasket::getErrors()){
				$arReturn["errors"] = $arErrors;
				$arReturn["status"] = false;
				$arReturn["error"] = true;
			}

			//print json
			echo \Bitrix\Main\Web\Json::encode($arReturn);

		}
		//delete item from basket
		elseif($actionType == "removeItem"){

			//vars
			$arReturn = array(
				"status" => true
			);

			//get basket id
			$basketId = $request->getPost("basketId");

			//check vars
			if(!empty($basketId) && !empty($siteId)){

		        //check modules
		        if(!Loader::includeModule("sale")){
		            return false;
		        }

		        //delete & check state
		        if(!DwBasket::deleteItem($basketId, $siteId)){
		        	//C4_BASKET_DELETE_ERROR
				    DwBasket::setError(\Bitrix\Main\Localization\Loc::GetMessage("C4_BASKET_DELETE_ERROR"));
		        }

			}

			if($arErrors = DwBasket::getErrors()){
				$arReturn["errors"] = $arErrors;
				$arReturn["status"] = false;
				$arReturn["error"] = true;
			}

			//print json
			echo \Bitrix\Main\Web\Json::encode($arReturn);

		}

	}
?>