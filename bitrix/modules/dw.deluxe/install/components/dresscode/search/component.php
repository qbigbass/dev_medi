<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
		die();
?>
<?
	if(CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog") && CModule::IncludeModule("sale") && CModule::IncludeModule("search")){

		if(!empty($_REQUEST["q"]) && strlen($_REQUEST["q"]) > 1){

			//globals
			global $APPLICATION;
			global $arrFilter;

			//vars
			$arResult["ITEMS_ID"] = array();

			//set default params
			$arParams["LAZY_LOAD_PICTURES"] = !empty($arParams["LAZY_LOAD_PICTURES"]) ? $arParams["LAZY_LOAD_PICTURES"] : "N";
			$arParams["FILTER_NAME"] = "arrFilter";

			if(empty($arParams["CURRENCY_ID"])){
				$arParams["CURRENCY_ID"] = CCurrency::GetBaseCurrency();
				$arParams["CONVERT_CURRENCY"] = "Y";
			}

			if(isset($_REQUEST["ajax"]) && $_REQUEST["ajax"] == "y"){
				$_REQUEST["q"] = !defined("BX_UTF") ? iconv("UTF-8", "windows-1251//ignore", $_REQUEST["q"]) : $_REQUEST["q"];
			}

			$arResult["ITEMS"] = array();
			$arResult["QUERY"] = $arResult["~QUERY"] = trim($_REQUEST["q"]);

			if(!empty($arParams["CONVERT_CASE"]) && $arParams["CONVERT_CASE"] == "Y"){
				$arLang = CSearchLanguage::GuessLanguage($arResult["QUERY"]);
				if(is_array($arLang) && $arLang["from"] != $arLang["to"]){
	  				$arResult["QUERY"] = CSearchLanguage::ConvertKeyboardLayout($arResult["QUERY"], $arLang["from"], $arLang["to"]);
	  				$arResult["QUERY_REPLACE"] = true;
				}
			}

			$arResult["QUERY_TITLE"] = GetMessage("SEARCH_RESULT")." - &laquo;".trim(htmlspecialcharsbx($arResult["QUERY"])."&raquo;");

			$APPLICATION->SetTitle($arResult["QUERY_TITLE"]);
			$APPLICATION->AddChainItem(trim(htmlspecialcharsbx($arResult["QUERY"])));

			$arResult["MENU_SECTIONS"] = array();

			$arFilter = Array(
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"INCLUDE_SUBSECTIONS" => "Y",
				"ACTIVE" => "Y",
			);

			if($arParams["HIDE_NOT_AVAILABLE"] == "Y"){
				$arFilter["CATALOG_AVAILABLE"] = "Y";
			}

			//search
			$obSearch = new CSearch;
			$arSearchParams = array(
			   "QUERY" => $arResult["QUERY"],
			   "SITE_ID" => SITE_ID,
			   "MODULE_ID" => "iblock",
			   "PARAM2" => $arParams["IBLOCK_ID"]
			);

			$obSearch->Search($arSearchParams, array(), array("STEMMING" => !empty($arParams["STEMMING"]) && $arParams["STEMMING"] == "Y"));
			while($searchItem = $obSearch->fetch()){
				if(is_numeric($searchItem["ITEM_ID"])){
					$arResult["ITEMS_ID"][$searchItem["ITEM_ID"]] = $searchItem["ITEM_ID"];
				}
			}

			//push ids
			$arrFilter["ID"] = $arResult["ITEMS_ID"];

			//check items
			if(!empty($arResult["ITEMS_ID"])){

				if(!empty($_REQUEST["SECTION_ID"])){
					$arFilter["SECTION_ID"] = intval($_REQUEST["SECTION_ID"]);
				}

				$arFilter["SECTION_ID"] = array();
				$res = CIBlockElement::GetList(array(), array_merge($arFilter, $arrFilter), false, false, array("ID"));
				while($nextElement = $res->GetNext()){
					$resGroup = CIBlockElement::GetElementGroups($nextElement["ID"], false);
					while($arGroup = $resGroup->Fetch()){
					    $IBLOCK_SECTION_ID = $arGroup["ID"];
					}

					$arSections[$IBLOCK_SECTION_ID] = $IBLOCK_SECTION_ID;
					$arSectionCount[$IBLOCK_SECTION_ID] = !empty($arSectionCount[$IBLOCK_SECTION_ID]) ? $arSectionCount[$IBLOCK_SECTION_ID] + 1 : 1;
					$arResult["ITEMS"][] = $nextElement;
				}

				if(!empty($arSections)){
					$arFilter = array("ID" => $arSections, "CNT_ACTIVE" => "Y", "ELEMENT_SUBSECTIONS" => "Y", "CNT_ALL" => "N");
					$rsSections = CIBlockSection::GetList(array("SORT" => "DESC"), $arFilter);
					while ($arSection = $rsSections->Fetch()){
						$searchParam = "SECTION_ID=".$arSection["ID"];
						$searchID = intval($_REQUEST["SECTION_ID"]);
						$arSection["SELECTED"] = $arSection["ID"] == $searchID ? "Y" : "N";
						$arSection["FILTER_LINK"] = $APPLICATION->GetCurPageParam($searchParam , array("SECTION_ID"));
						$arSection["ELEMENTS_COUNT"] = $arSectionCount[$arSection["ID"]];
						array_push($arResult["MENU_SECTIONS"], $arSection);
					}
				}

			}

		}

	}

	//fast redirect
	if(!empty($arResult["ITEMS"]) && count($arResult["ITEMS"]) == 1){
		if(!empty($arResult["ITEMS"][0]["ID"])){
			if($gLastProduct = CIBlockElement::GetByID($arResult["ITEMS"][0]["ID"])){
				$arLastProduct = $gLastProduct->GetNext();
				if(!empty($arLastProduct["DETAIL_PAGE_URL"])){
					LocalRedirect($arLastProduct["DETAIL_PAGE_URL"]);
				}
			}
		}
	}

	$this->IncludeComponentTemplate();

?>

