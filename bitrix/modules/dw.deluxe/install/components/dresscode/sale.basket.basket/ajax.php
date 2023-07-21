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

	//get basket vars
	$personTypeId = $request->getPost("personTypeId");
	$paysystemId = $request->getPost("paysystemId");
	$deliveryId = $request->getPost("deliveryId");
	$locationId = $request->getPost("locationId");
	$extraServices = $request->getPost("extraServices");

	//check siteId from get request
	if(empty($siteId)){
		$siteId = $request->getQuery("siteId");
	}

	//check actionType from get request
	if(empty($actionType)){
		$actionType = $request->getQuery("actionType");
	}

	//check request act
	if(!empty($actionType)){

		//order make
		if($actionType == "orderMake"){

			//vars
			$arReturn = array(
				"status" => true
			);

			//get transmitted data
			$arTransmitted = $request->getPostList()->toArray();
			$arTransmitted["files"] = $request->getFileList()->toArray();

			//check
			if(!empty($arTransmitted)){

		        //check modules
		        if(!Loader::includeModule("sale")){
		            return false;
		        }

				//convert encoding
				$arTransmitted = \DigitalWeb\Basket::checkEncoding($arTransmitted);

	        	//get arParams
	        	$arParams = !empty($arTransmitted["params"]) ? $arTransmitted["params"] : array();

	        	//set component params
	        	DwBasketAjax::setParams($arParams);

	        	//get ajax object
	        	$basketAjax = DwBasketAjax::getInstance();

	        	//set params
	        	$basketAjax->setFields($siteId, $deliveryId, $paysystemId, $personTypeId, $locationId);

	            //set extraServices
	            if(!empty($extraServices)){
	                $basketAjax::setExtraServices($extraServices);
	            }

	            //set store
	            if(!empty($arTransmitted["store"])){
	                $basketAjax::setStoreId($arTransmitted["store"]);
	            }

	        	//create new order
	        	if($orderResult = $basketAjax->orderMake($arTransmitted)){
	        		$arReturn["orderResult"] = $orderResult;
	        	}

		        //set error
		        else{
		        	//C3_ORDER_MAKE_ERROR
		        	if(empty(DwBasket::getErrors())){
		        		DwBasket::setError(\Bitrix\Main\Localization\Loc::GetMessage("C3_ORDER_MAKE_ERROR"));
		        	}
		        }

	        	//check errors
				if($arErrors = DwBasket::getErrors()){
					$arReturn["errors"] = $arErrors;
					$arReturn["status"] = false;
					$arReturn["error"] = true;
				}

				//print json
				echo \Bitrix\Main\Web\Json::encode($arReturn);

			}

		}

		//get location select component
		elseif($actionType == "getCompilation"){

			//vars
			$arReturn = array(
				"status" => true
			);

			//check vars
			if(!empty($siteId)){

		        //check modules
		        if(!Loader::includeModule("sale")){
		            return false;
		        }

	        	//get arParams
				$arParams = $request->getPost("params");

				//convert encoding
				$arParams = \DigitalWeb\Basket::checkEncoding($arParams);

	        	//set component params
	        	DwBasketAjax::setParams($arParams);

	        	//get ajax object
	        	$basketAjax = DwBasketAjax::getInstance();

	        	//set params
	        	$basketAjax->setFields($siteId, $deliveryId, $paysystemId, $personTypeId, $locationId);

	            //set extraServices
	            if(!empty($extraServices)){
	                $basketAjax::setExtraServices($extraServices);
	            }

	        	//compilation data (get basket & order info)
	        	$arCompilation = $basketAjax->compilation();

	        	//check result
	        	if(!empty($arCompilation)){

		        	//push
	        		$arReturn["compilation"] = $arCompilation;

		        	//get gifts
		        	$arReturn["gifts"] = getGiftsComponent($arParams, $arCompilation["applied_discount_list"], $arCompilation["full_discount_list"]);

	        	}

		        //set error
		        else{
		        	//C3_BASKET_COMPILATION_ERROR
		        	DwBasket::setError(\Bitrix\Main\Localization\Loc::GetMessage("C3_BASKET_COMPILATION_ERROR"));
		        }

			}

			//check errors
			if($arErrors = DwBasket::getErrors()){
				$arReturn["errors"] = $arErrors;
				$arReturn["status"] = false;
				$arReturn["error"] = true;
			}

			//print json
			echo \Bitrix\Main\Web\Json::encode($arReturn);

		}

		//update basket item (quantity)
		elseif($actionType == "updateItem"){

			//vars
			$arReturn = array(
				"status" => true
			);

			//get basket id
			$basketId = $request->getPost("basketId");

			//get quantity
			$quantity = $request->getPost("quantity");

			//check vars
			if(!empty($basketId) && !empty($siteId) && !empty($quantity)){

		        //check modules
		        if(!Loader::includeModule("sale")){
		            return false;
		        }

		        //delete & check state
		        if(DwBasket::updateQuantity($basketId, $quantity, $siteId)){

		        	//get arParams
					$arParams = $request->getPost("params");

					//convert encoding
					$arParams = \DigitalWeb\Basket::checkEncoding($arParams);

		        	//set component params
		        	DwBasketAjax::setParams($arParams);

		        	//get ajax object
		        	$basketAjax = DwBasketAjax::getInstance();

		        	//set params
		        	$basketAjax->setFields($siteId, $deliveryId, $paysystemId, $personTypeId, $locationId);

		            //set extraServices
		            if(!empty($extraServices)){
		                $basketAjax::setExtraServices($extraServices);
		            }

		        	//compilation data (get basket & order info)
		        	$arCompilation = $basketAjax->compilation();

		        	//check result
		        	if(!empty($arCompilation)){

			        	//push
		        		$arReturn["compilation"] = $arCompilation;

			        	//get gifts
			        	$arReturn["gifts"] = getGiftsComponent($arParams, $arCompilation["applied_discount_list"], $arCompilation["full_discount_list"]);

		        	}

			        //set error
			        else{
			        	//C3_BASKET_COMPILATION_ERROR
			        	DwBasket::setError(\Bitrix\Main\Localization\Loc::GetMessage("C3_BASKET_COMPILATION_ERROR"));
			        }

		        }

		        //set error
		        else{
		        	//C3_BASKET_UPDATE_ERROR
		        	DwBasket::setError(\Bitrix\Main\Localization\Loc::GetMessage("C3_BASKET_UPDATE_ERROR"));
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
		        if(DwBasket::deleteItem($basketId, $siteId)){

		        	//get arParams
					$arParams = $request->getPost("params");

					//convert encoding
					$arParams = \DigitalWeb\Basket::checkEncoding($arParams);

		        	//set component params
		        	DwBasketAjax::setParams($arParams);

		        	//get ajax object
		        	$basketAjax = DwBasketAjax::getInstance();

		        	//check last product
		        	if($basketAjax::isEmptyBasket()){

			        	//set params
			        	$basketAjax->setFields($siteId, $deliveryId, $paysystemId, $personTypeId, $locationId);

			            //set extraServices
			            if(!empty($extraServices)){
			                $basketAjax::setExtraServices($extraServices);
			            }

			        	//compilation data (get basket & order info)
			        	$arCompilation = $basketAjax->compilation();

			        	//check result
			        	if(!empty($arCompilation)){

				        	//push
			        		$arReturn["compilation"] = $arCompilation;

				        	//get gifts
				        	$arReturn["gifts"] = getGiftsComponent($arParams, $arCompilation["applied_discount_list"], $arCompilation["full_discount_list"]);

			        	}

				        //set error
				        else{
			        		//C3_BASKET_COMPILATION_ERROR
				        	DwBasket::setError(\Bitrix\Main\Localization\Loc::GetMessage("C3_BASKET_COMPILATION_ERROR"));
				        }

				   	}

		        }

		        //set error
		        else{
		        	//C3_BASKET_DELETE_ERROR
				    DwBasket::setError(\Bitrix\Main\Localization\Loc::GetMessage("C3_BASKET_DELETE_ERROR"));
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

		//set coupon
		elseif($actionType == "setCoupon"){

			//vars
			$arReturn = array(
				"status" => true
			);

			//get basket id
			$couponValue = $request->getPost("coupon");

			//check vars
			if(!empty($couponValue) && !empty($siteId)){

		        //check modules
		        if(!Loader::includeModule("sale")){
		            return false;
		        }

		        //set
		        if(DwBasket::setCoupon($couponValue, $siteId)){
		        	$arReturn["success"] = true;
		        }

		        //set error
		        else{
	        		//C3_BASKET_COUPON_ERROR
		        	DwBasket::setError(\Bitrix\Main\Localization\Loc::GetMessage("C3_BASKET_COUPON_ERROR"));
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

		//clear basket
		elseif($actionType == "clearAll"){

			//vars
			$arReturn = array(
				"status" => true
			);

			//check vars
			if(!empty($siteId)){

		        //check modules
		        if(!Loader::includeModule("sale")){
		            return false;
		        }

		        //set siteId
		        DwBasket::setSiteId($siteId);

		        //delete & check state
		        if(!DwBasket::clearBasket($siteId)){
	        		//C3_BASKET_CLEAR_ERROR
		        	DwBasket::setError(\Bitrix\Main\Localization\Loc::GetMessage("C3_BASKET_CLEAR_ERROR"));
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

		//get location select component
		elseif($actionType == "pushLocation"){

			//vars
			$arReturn = array(
				"status" => true
			);

			//get search value
			$searchValue = $request->getPost("value");

			//utf8 convert
			$searchValue = \DigitalWeb\Basket::checkEncoding($searchValue);

			//start buffering
			ob_start();

			//push component
			$APPLICATION->IncludeComponent(
				"dresscode:sale.location.select",
				".default",
				array(
					"SITE_ID" => empty($siteId) ? $siteId : $context->getSite(),
					"LOCATION_VALUE" => $searchValue
				),
				false,
				Array(
					//hide hermitage actions
					"HIDE_ICONS" => "Y"
				)
			);

			//capture
			$componentData = ob_get_contents();

			//stop buffering
			ob_end_clean();

			//check result
			if(!empty($componentData)){
				$arReturn["component"] = $componentData;
			}

			//print json
			echo \Bitrix\Main\Web\Json::encode($arReturn);

		}

		//get fast order component
		elseif($actionType == "getFastBasketWindow"){

			//get masked params
			$maskedUse = $request->getQuery("maskedUse");
			$maskedFormat = $request->getQuery("maskedFormat");

			//push component html
			$APPLICATION->IncludeComponent(
				"dresscode:basket.fast.order",
				".default",
				array(
					"SITE_ID" => empty($siteId) ? $siteId : $context->getSite(),
					"USE_MASKED" => !empty($maskedUse) ? $maskedUse : "N",
					"MASKED_FORMAT" => !empty($maskedFormat) ? $maskedFormat : "",
				),
				false,
				Array(
					//hide hermitage actions
					"HIDE_ICONS" => "Y"
				)
			);

		}

	}

	//gifts include
	function getGiftsComponent($arParams, $appliedDiscount = array(), $fullDiscount = array()){

		//globals
		global $APPLICATION;

		//vars
		$componentHTML = "";

		//start buffering
		ob_start();

		//push component
		$APPLICATION->IncludeComponent("bitrix:sale.gift.basket", ".default", array(
				"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
				"PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
				"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
				"PRODUCT_PRICE_CODE" => $arParams["PRODUCT_PRICE_CODE"],
				"APPLIED_DISCOUNT_LIST" => $appliedDiscount,
				"FULL_DISCOUNT_LIST" => $fullDiscount,
				"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
				"HIDE_MEASURES" => $arParams["HIDE_MEASURES"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"CURRENCY_ID" => $arParams["CURRENCY_ID"]
			),
			false
		);

		//save buffer
		$componentHTML = ob_get_contents();

		//clean buffer
		ob_end_clean();

		return $componentHTML;

	}
?>