<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
		die();

	//default params
	$arParams["LAZY_LOAD_PICTURES"] = !empty($arParams["LAZY_LOAD_PICTURES"]) ? $arParams["LAZY_LOAD_PICTURES"] : "N";

	$arResult["SELECTED"] = !empty($_GET["where"]) ? intval($_GET["where"]) : 0;
	$cacheID = !empty($_GET["q"]) || !empty($_GET["where"]) ? time() : 0;

	if ($this->StartResultCache($arParams["CACHE_TIME"], $cacheID)){

		if(!empty($_GET["q"]) && $_GET["r"] == "Y"){
			$arResult["q"] = htmlspecialchars($_GET["q"]);
			$this->AbortResultCache();
		}

		$this->IncludeComponentTemplate();
	}

?>