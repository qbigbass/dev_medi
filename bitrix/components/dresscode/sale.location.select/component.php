<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true){
		die();
	}

	//check modules
	if(!\Bitrix\Main\Loader::includeModule("sale")){
		return false;
	}

	//check filling
	if(empty($arParams["LOCATION_VALUE"])){
		return false;
	}

	//check site id
	if(!isset($arParams["SITE_ID"])){
		$arParams["SITE_ID"] = SITE_ID;
	}

	//result array
	$arResult = array();
	$arLocations = array();

	//set location search params
	$arLocParams = array(
		"filter" => array(
			"PHRASE" => htmlspecialcharsbx($arParams["LOCATION_VALUE"]),
			"SITE_ID" => $arParams["SITE_ID"],
			"LANGUAGE_ID" => LANGUAGE_ID
		)
	);

	//search locations
	$rsLocation = \Bitrix\Sale\Location\Search\Finder::find($arLocParams);

	//processing
	while($nextLocation = $rsLocation->fetch()){

		//vars
		$arPath = array();

		//set path params
		$pathParams = array(
			"select" => array(
				"PNAME" => "NAME.NAME",
			),
			"filter" => array(
				"NAME.LANGUAGE_ID" => LANGUAGE_ID
			)
		);

		//get path
		if(!empty($nextLocation["ID"])){
			$rsPath = \Bitrix\Sale\Location\LocationTable::getPathToNode($nextLocation["ID"], $pathParams);
		}

		elseif(!empty($nextLocation["CODE"])){
			$rsPath = \Bitrix\Sale\Location\LocationTable::getPathToNodeByCode($nextLocation["CODE"], $pathParams);
		}

		//save path
		while($nextPath = $rsPath->Fetch()){
			$arPath[] = $nextPath["PNAME"];
		}

		//save
		$nextLocation["PATH"] = implode($arPath, ", ");
	    $arLocations[$nextLocation["ID"]] = $nextLocation;

	}

	//write to result array
	if(!empty($arLocations)){
		$arResult["LOCATIONS"] = $arLocations;
	}

	$this->setResultCacheKeys(array());
	$this->IncludeComponentTemplate();
?>