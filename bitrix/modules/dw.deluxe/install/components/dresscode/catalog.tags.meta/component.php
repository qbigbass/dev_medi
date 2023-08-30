<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true){
		die();
	}

	//globals
	global $APPLICATION;

	//chain
	if(!empty($arParams["TAG_NAME"])){
		$APPLICATION->AddChainItem($arParams["TAG_NAME"], "");
	}

	//set title
	if(!empty($arParams["META_TITLE"])){
		$APPLICATION->SetPageProperty("title", $arParams["META_TITLE"]);
	}

	//heading
	if(!empty($arParams["META_HEADING"])){
		$APPLICATION->SetTitle($arParams["META_HEADING"]);
	}

	//keywords
	if(isset($arParams["META_KEYWORDS"])){
		$APPLICATION->SetPageProperty("keywords", $arParams["META_KEYWORDS"]);
	}

	//description
	if(isset($arParams["META_DESCRIPTION"])){
		$APPLICATION->SetPageProperty("description", $arParams["META_DESCRIPTION"]);
	}

?>