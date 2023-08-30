<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

	//use
	use \DigitalWeb\Basket as DwBasket;

	//check product id
	if(empty(intval($arParams["PRODUCT_ID"]))){
		return false;
	}

	//load modules
	if(!\Bitrix\Main\Loader::includeModule("dw.deluxe")){
		return false;
	}

	//check siteId
	if(empty($arParams["SITE_ID"])){
		$arParams["SITE_ID"] = \Bitrix\Main\Context::getCurrent()->getSite();
	}

	//check measures params
	if(empty($arParams["HIDE_MEASURES"])){
		$arParams["HIDE_MEASURES"] = "N";
	}

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
	foreach($arProducts as $basketId => $arNextProduct){
		if($arNextProduct["PRODUCT_ID"] == $arParams["PRODUCT_ID"]){
			$arResult = $arNextProduct; break(1);
		}
	}

	//get additonal
	if(!empty($arResult)){

		//get measures
		if($arParams["HIDE_MEASURES"] != "Y"){
			$arResult["MEASURES"] = $basket->getMeasures();
		}

		//get product sum
	    $arResult["BASE_SUM_FORMATED"] = \CCurrencyLang::CurrencyFormat(($arResult["BASE_PRICE"] * $arResult["QUANTITY"]), $currencyCode);
	    $arResult["SUM_FORMATED"] = \CCurrencyLang::CurrencyFormat(($arResult["PRICE"] * $arResult["QUANTITY"]), $currencyCode);

	}

	//include template
	$this->IncludeComponentTemplate();

?>