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
	    Bitrix\Main\Context,
		Bitrix\Main\PhoneNumber\Format,
		Bitrix\Main\PhoneNumber\Parser;

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
				$basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
				$basketItem = $basket->getItemById($basketId);
		////w2l($basketItem, 1, 'basket.log');

				CSaleBasket::Update($basketId, ['QUANTITY'=>$quantity]);
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

				//get arParams
				$arParams = $request->getPost("params");

				//convert encoding
				$arParams = \DigitalWeb\Basket::checkEncoding($arParams);

				//set component params
				DwBasketAjax::setParams($arParams);

				//basket object
				$basketAjax = DwBasketAjax::getInstance();

				//basket items
				$arBasketItems = $basketAjax->getBasketItems();

				//append product fields to basket items
				$arProducts = $basketAjax->addProductsInfo($arBasketItems);

				//add prices
				$arProducts = $basketAjax->addProductPrices($arProducts);

				//push to arResult
				foreach($arProducts as $arNextProduct){
					if($arNextProduct["BASKET_ID"] == $basketId){
						$arRetProduct = $arNextProduct; break(1);
					}
				}
				if (!empty($arRetProduct)){
					$arReturn['product']['ID'] = $arRetProduct['ID'];
					$goodId = '';
					$goodName = '';
					$rsBaseProduct = CIBlockElement::GetList([], ['ID' => $arRetProduct['ID'], "IBLOCK_ID" => $arRetProduct['IBLOCK_ID']], false, false, ['IBLOCK_ID', 'PROPERTY_CML2_LINK.ID', 'PROPERTY_CML2_LINK.IBLOCK_ID']);
					if ($productInfo = $rsBaseProduct->GetNext())
					{
						if ($productInfo['PROPERTY_CML2_LINK_ID'] > 0)
						{
							$arFilter = Array(
								"ID" => $productInfo['PROPERTY_CML2_LINK_ID'],
								"IBLOCK_ID" => $productInfo['PROPERTY_CML2_LINK_IBLOCK_ID'],
								"ACTIVE_DATE" => "Y",
								"ACTIVE" => "Y"
							);
							$rsBaseProduct2 = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'NAME', 'PROPERTY_ATT_BRAND.NAME', 'PROPERTY_CML2_ARTICLE.PROPERTY_ATT_BRAND']);
							if ($productBrand = $rsBaseProduct2->GetNext())
							{
							    $arReturn['product']['BRAND'] = $productBrand['PROPERTY_ATT_BRAND_NAME'];
								$goodId = $productBrand['ID'];
								$goodName = $productBrand['NAME'];
							}
						}
						else {
							$arFilter = Array(
								"ID" => $arRetProduct['ID'],
								"IBLOCK_ID" => $arRetProduct['PROPERTY_CML2_LINK_IBLOCK_ID'],
								"ACTIVE_DATE" => "Y",
								"ACTIVE" => "Y"
							);
							$rsBaseProduct2 = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'NAME', 'PROPERTY_ATT_BRAND.NAME', 'PROPERTY_CML2_ARTICLE.PROPERTY_ATT_BRAND']);
							if ($productBrand = $rsBaseProduct2->GetNext())
							{
							    $arReturn['product']['BRAND'] = $productBrand['PROPERTY_ATT_BRAND_NAME'];
								$goodId = $productBrand['ID'];
								$goodName = $productBrand['NAME'];
							}
						}
					}

					$arReturn['product']['ID'] = $goodId;
					$arReturn['product']['NAME'] = $goodName;
					$arReturn['product']['QUANTITY'] = $arRetProduct['QUANTITY'];
					$arReturn['product']['PRICE'] = $arRetProduct['PRICE'];

					$secturl = explode("/", $arRetProduct['DETAIL_PAGE_URL']);
					$sectcount = count($secturl) - 1;
					unset($secturl[$sectcount]); unset($secturl[0]);unset($secturl[1]);

					$arReturn['product']['CATEGORY'] = implode("/",$secturl);

					$arReturn['product']['CML2_ARTICLE'] = $arRetProduct['PROPERTIES']['CML2_ARTICLE']['VALUE'];
				}
		        //delete & check state
		        if(DwBasket::deleteItem($basketId, $siteId)){

		        	//get arParams
					//$arParams = $request->getPost("params");

					//convert encoding
					//$arParams = \DigitalWeb\Basket::checkEncoding($arParams);

		        	//set component params
		        	//DwBasketAjax::setParams($arParams);

		        	//get ajax object
		        	//$basketAjax = DwBasketAjax::getInstance();

		        	//check last product
		        	/*if($basketAjax::isEmptyBasket()){

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

				   	}*/

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

			global $USER;
			$log = [];
			//vars
			$arReturn = array(
				"status" => true
			);

			//get basket id
			$couponValue = $request->getPost("coupon");

			//check vars
			if(!empty($couponValue) && !empty($siteId)){
				$log['couponValue'] = $couponValue;

		        //check modules
		        if(!Loader::includeModule("sale")){
		            return false;
		        }

				//set
				$sid = Bitrix\Main\Context::getCurrent()->getSite();

				//$price_id = 8;
				//$max_price_id = 7;

				//if (SITE_ID == 's1'){
					$price_id = 1;
					$max_price_id = 2;
				/*}
				else*/if (SITE_ID == 's2'){
					$price_id = 6;
					$max_price_id = 5;
				}

				$user_not_found = '1';

				$lmxapp = new appLmx();
				$lmxapp->authMerchantToken();

				$basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());

				$OUserID = Sale\Fuser::getUserIdById ($basket->getFUserId());

				$log['USER_ID'] = $OUserID;
				if ($OUserID > 0 && $user_not_found == '1')
				{
					$obUser = $USER->GetByID($OUserID);
					if ($arUser = $obUser->Fetch()){

						$parsedPhone = Parser::getInstance()->parse($arUser['LOGIN']);
						$phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));
						if (!$phone){
							$parsedPhone = Parser::getInstance()->parse($arUser['PERSONAL_PHONE']);
							$phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));
						}

						if ($phone != '' && strlen($phone) == 11)
						{
							$checkuser = $lmxapp->checkUser($phone, ['account', 'profile', 'cards']);
							if ($checkuser['status'] == 'found')
							{
								$authclientresult = $lmxapp->authClientToken($checkuser['code']);
								$user_not_found = 0;

								$log['USER_PHONE'] = $phone;
							}
							else{
								$user_not_found = 1;
							}
						}
					}
					else{
						$user_not_found = 1;
					}
				}

				if ($user_not_found == 1)
				{
					unset($_SESSION['lmx']);
					unset($_SESSION['lmxapp']);
				}


				$objDateTime = new DateTime('NOW');
				$purchaseDate = $objDateTime->format("Y-m-d\TH:i:s.v\Z");

				if (isset($_SESSION['lmxapp']['purchaseId']) && $_SESSION['lmxapp']['purchaseTime'] > time()-600)
				{
					$purchaseId = str_replace([" ", "."], "",  $_SESSION['lmxapp']['purchaseId']);
					$purchaseDate = $_SESSION['lmxapp']['purchaseDate'];
					$_SESSION['lmxapp']['purchaseTime'] = time();
				}
				else
				{
					$purchaseId = str_replace([" ", "."], "",  rand(1, 10).microtime().$OUserID);
					$_SESSION['lmxapp']['purchaseId'] = $purchaseId;
					$_SESSION['lmxapp']['purchaseTime'] = time();
					$_SESSION['lmxapp']['purchaseDate'] = $purchaseDate;
				}

				$log['purchaseId'] = $purchaseId;
				//$log['coupon_ok'] = $_SESSION['lmxapp']['coupon_ok'];

				$lines = [];
				$i = 1; // items
				$cc = 0; // lmx lines

				CModule::IncludeModule("iblock");

				$basketItems = $basket->getBasketItems();

				foreach ($basketItems as $basketItem) {

					$iblock_id = CIBlockElement::GetIBlockByID($basketItem->getProductId());
					$obItem = CIBlockElement::GetList([], ['IBLOCK_ID'=>$iblock_id, 'ID'=>$basketItem->getProductId(),'ACTIVE'=>'Y'],
						false,false, ['ID', 'CATALOG_PRICE_'.$price_id,  'CATALOG_PRICE_'.$max_price_id, "PROPERTY_GTIN", "PROPERTY_LMX_GOODID", "NAME", "PROPERTY_CML2_ARTICLE" ] );
					if ($exItem = $obItem->GetNext()) {

						$lines[$cc] = [
							"position" => $i,
							"amount" => $exItem['CATALOG_PRICE_'.$price_id] * $basketItem->getQuantity(),

							"quantity" => $basketItem->getQuantity(),
							"cashback" => 0,
							"discount" => 0,
							"name" => $exItem['PROPERTY_CML2_ARTICLE_VALUE'],
							"price" => $exItem['CATALOG_PRICE_'.$price_id]
						];
						if ($exItem['PROPERTY_LMX_GOODID_VALUE'] != '')
						{
							$lines[$cc] = array_merge($lines[$cc], ['goodsId'=>$exItem['PROPERTY_LMX_GOODID_VALUE']]);
						}elseif ($exItem['PROPERTY_GTIN_VALUE'] != '')
						{
							$lines[$cc] = array_merge($lines[$cc], ['barcode'=>substr($exItem['PROPERTY_GTIN_VALUE'],1)]);
						}
						if ($exItem['CATALOG_PRICE_'.$max_price_id] > $exItem['CATALOG_PRICE_'.$price_id])
						{
							$lines[$cc]['discount'] = $exItem['CATALOG_PRICE_'.$max_price_id] - $exItem['CATALOG_PRICE_'.$price_id];
						}
						$i++;
						$cc++;

					}
				}

				$log['lines'] = $lines;

				if (!empty($lines) )
				{
					$res_without_coupon = 0;
					$res_with_coupon = 0;
					$qResult_free = $lmxapp->calculate($_SESSION['lmxapp']['purchaseId']."tmp", $_SESSION['lmxapp']['purchaseDate'], $lines, '');
					if (is_array($qResult_free['result']) && $qResult_free['result']['state'] == 'Success')
					{
						$res_without_coupon = $qResult_free['data'][0]['cheque']['totalDiscount'];
					}

					$qResult = $lmxapp->calculate($_SESSION['lmxapp']['purchaseId'], $_SESSION['lmxapp']['purchaseDate'], $lines, $couponValue);

					if (is_array($qResult['result']) && $qResult['result']['state'] == 'Success')
					{
						$res_with_coupon = $qResult['data'][0]['cheque']['totalDiscount'];

                        if ($res_with_coupon != $res_without_coupon)
                        {
                            $_SESSION['lmxapp']['coupon'] = $couponValue;
                            $_SESSION['lmxapp']['coupon_ok'] = true;
                            $arReturn['coupon'] = $couponValue;
                            $arReturn['coupon_ok'] = true;
                         }
                        else{
                            unset($_SESSION['lmxapp']['coupon']);
                            unset($_SESSION['lmxapp']['coupon_ok']);
							$arReturn['coupon_ok'] = false;
                        }

						$arReturn['ress'] = [$res_with_coupon, $res_without_coupon];
						foreach ($qResult['data'][0]['cheque']['lines'] as $k=>$line) {
							if ($basket[$k] !== null && !$lines[$k]['exclude']){
								$basket[$k]->setFields(array(
									'CUSTOM_PRICE' => "Y",
									'PRICE' => ($line['amount']/$line['quantity']),
									'BASE_PRICE' => $lines[$k]['price'],
									'DISCOUNT_PRICE' => ($line['discount']/$line['quantity']),
									'DISCOUNT_NAME' => $line['appliedOffers'][0]['name'],
									'NOTES' => $line['appliedOffers'][0]['name']. ' '.$couponValue,
									'DISCOUNT_VALUE' => ($line['discount']/$line['quantity'])
								)); // Изменение полей

								$basketPropertyCollection = $basket[$k]->getPropertyCollection();

								$fullprice = ($line['amount']+$line['discount'])/$line['quantity'];
								$discountprice = $line['discount']/$line['quantity'];

								$basketPropertyCollection->setProperty(array(
									array(
										'NAME' => 'Скидка'.round($discountprice/$fullprice *100, 0).'%',
										'CODE' => 'DISCOUNT_NAME',
										'VALUE' => $line['appliedOffers'][0]['name']. ' '.$couponValue,

									),
								));

							}
						}
						$arReturn["success"] = true;
					}
					//set error
					else{

						//C3_BASKET_COUPON_ERROR
						DwBasket::setError(\Bitrix\Main\Localization\Loc::GetMessage("C3_BASKET_COUPON_ERROR"));
					}
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
		elseif ($actionType == 'unsetCoupon') {
			global $USER;
			$log = [];
			//vars
			$arReturn = array(
				"status" => true,
				"success" => true
			);

			unset($_SESSION['lmxapp']['coupon']);
			unset($_SESSION['lmxapp']['coupon_ok']);

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



			$res = CSaleBasket::GetList(array(), array(
			  'FUSER_ID' => CSaleBasket::GetBasketUserID(),
			  'LID' => $siteId,
			  'ORDER_ID' => 'null',
			  'DELAY' => 'N'));

			while ($row = $res->fetch()) {

				$checkTable =  $DB->Query("SHOW TABLES LIKE 'b_sale_basket_reservation'", false);
				if ($ch = $checkTable->Fetch()){
					$delReserveSql = "DELETE FROM b_sale_basket_reservation WHERE BASKET_ID = ".$row['ID'];

					$dbProperties = $DB->Query($delReserveSql, false);
				}

			    CSaleBasket::Delete($row['ID']);
			}


				unset($_SESSION['lmxapp']['coupon']);
				unset($_SESSION['lmxapp']['coupon_ok']);

			//print json
			echo \Bitrix\Main\Web\Json::encode($arReturn);

		}
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
