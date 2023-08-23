<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
		die();

	//globals
	global $USER;

	//default params
	$arParams["MASKED_FORMAT"] = !empty($arParams["MASKED_FORMAT"]) ? $arParams["MASKED_FORMAT"] : "";
	$arParams["USE_MASKED"] = !empty($arParams["USE_MASKED"]) ? $arParams["USE_MASKED"] : "N";

	//get user info
	if($USER->IsAuthorized()){
		$rsUser = CUser::GetByID($USER->GetID());
		$arUser = $rsUser->Fetch();
		if(!empty($arUser)){
			$arResult["USER_NAME"] = $USER->GetFullName();
			$arResult["USER_PHONE"] = $arUser["PERSONAL_MOBILE"];
		}
	}

	//show template
	$this->IncludeComponentTemplate();

?>