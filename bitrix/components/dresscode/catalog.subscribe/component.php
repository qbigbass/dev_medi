<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true){
		die();
	}

	//load modules
	if(!\Bitrix\Main\Loader::includeModule("subscribe")){
		return false;
	}

	//cache options
	if(!isset($arParams["CACHE_TIME"])){
		$arParams["CACHE_TIME"] = 36000000;
	}

	//default params
	$arParams["SITE_ID"] = !empty($arParams["SITE_ID"]) ? $arParams["SITE_ID"] : SITE_ID;

	//check filling
	if(empty($arParams["RUBRIC_ID"])){
		return false;
	}

	//create cache id
	$cacheID = array(
		"USER_GROUPS" => $USER->GetGroups(),
		"RUBRIC_ID" => $arParams["SUBRIC_ID"],
		"SITE_ID" => $arParams["SITE_ID"]
	);

	//vars
	$arResult = array();

	//cache zone
	if($this->StartResultCache($arParams["CACHE_TIME"], serialize($cacheID))){

		//get rubrics
		$rsRubric = CRubric::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array("ID" => intval($arParams["RUBRIC_ID"]), "ACTIVE" => "Y", "VISIBLE" => "Y", "LID" => $arParams["SITE_ID"]));
		while($arRubric = $rsRubric->GetNext()){
			$arResult["RUBRIC"] = $arRubric;
		}

		//push template
		$this->IncludeComponentTemplate();

	}

?>