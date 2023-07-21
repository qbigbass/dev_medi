<?
class DwItemInfo{

	//static vars
    protected static $arSectionTree = array();

    //functions
	public static function checkTagSectionTree($sectionId, $mainSectionId){

		//load modules
		if(!\Bitrix\Main\Loader::includeModule("iblock")){
			return false;
		}

		//check transmitted
		if(!empty($sectionId) && !empty($mainSectionId)){

			//get section tree by main section id
			if(empty(self::$arSectionTree[$mainSectionId])){

				//get section path list
				$nav = CIBlockSection::GetNavChain(false, $mainSectionId);
				while($arSectionPath = $nav->GetNext()){
					//push section info
					self::$arSectionTree[$mainSectionId][$arSectionPath["ID"]] = $arSectionPath;
				}

			}

			//check section tree
			return !empty(self::$arSectionTree[$mainSectionId][$sectionId]);

		}

		return false;
	}

	public static function getSeoByTag($tagName, $tagCode, $iblockId, $sectionId){

		//check transmitted
		if(empty($tagName) || empty($tagCode) || empty($iblockId) || empty($sectionId)){
			return false;
		}

		//vars
		$arResult = array(
			"SEO_TITLE" => $tagName,
			"SEO_TAG_TITLE" => $tagName,
			"SEO_TAG_DESCRIPTION" => "",
			"SEO_KEYWORDS" => "",
			"SECTION_CHAIN" => array()
		);

		//cache id
		$cacheID = array(
			"CACHE_NAME" => "DOUBLE_CATALOG_TAGS_CACHE",
			"SECTION_ID" => $sectionId,
			"IBLOCK_ID" => $iblockId,
			"TAG_CODE" => $tagCode,
			"SITE_ID" => SITE_ID,
		);

		//extra settings from cache
		$oExtraCache = new CPHPCache();

		//init cache cache
		if($cacheType != "N" && $oExtraCache->InitCache("36000000", serialize($cacheID), "/")){
			$arResult = $oExtraCache->GetVars();
		}

		elseif($oExtraCache->StartDataCache()){

			//check include modules
			if(!\Bitrix\Main\Loader::includeModule("iblock")){

				$obExtraCache->AbortDataCache();
				ShowError("modules not installed!");
				return 0;

			}

			//get section path list
			$nav = CIBlockSection::GetNavChain(false, $sectionId);
			while($arItem = $nav->Fetch()){
			    $arSectionIds[$arItem["ID"]] = $arItem["ID"];
			}

			//get h1, keywords, description by section's properties
			if(!empty($arSectionIds)){
				$rsList = CIBlockSection::GetList(array("DEPTH_LEVEL" => "DESC"), array("ID" => $arSectionIds, "IBLOCK_ID" => $iblockId), false, array("ID", "IBLOCK_ID", "UF_TAG_TITLE", "UF_TAG_KEYWORDS", "UF_TAG_DESCRIPTION", "UF_TAG_HEADING"));
				while($arNextSection = $rsList->GetNext()){
					$arSections[$arNextSection["ID"]] = $arNextSection;
				}
			}

			//processing
			if(!empty($arSections)){

				//each sections tree
				foreach($arSections as $nextSection){

					//check filling
					if(!empty($nextSection["UF_TAG_TITLE"]) || !empty($nextSection["UF_TAG_KEYWORDS"]) || !empty($nextSection["UF_TAG_DESCRIPTION"]) || !empty($nextSection["UF_TAG_HEADING"])){

						//replace templates
						$arReplace = array(ToUpper(substr($tagName, 0, 1)) . substr($tagName, 1), ToUpper($tagName), ToLower($tagName), $tagName);
						$arSearch = array("#TAG_UPPER_FIRST#", "#TAG_UPPER#", "#TAG_LOWER#", "#TAG#");

						//title
						if(!empty($nextSection["UF_TAG_TITLE"])){
							$arResult["SEO_TITLE"] = str_replace($arSearch, $arReplace, $nextSection["UF_TAG_TITLE"]);
						}

						//meta description
						if(!empty($nextSection["UF_TAG_DESCRIPTION"])){
							$arResult["SEO_DESCRIPTION"] = str_replace($arSearch, $arReplace, $nextSection["UF_TAG_DESCRIPTION"]);
						}

						//meta keywords
						if(!empty($nextSection["UF_TAG_KEYWORDS"])){
							$arResult["SEO_KEYWORDS"] = str_replace($arSearch, $arReplace, $nextSection["UF_TAG_KEYWORDS"]);
						}

						//heading
						if(!empty($nextSection["UF_TAG_HEADING"])){
							$arResult["SEO_HEADING"] = str_replace($arSearch, $arReplace, $nextSection["UF_TAG_HEADING"]);
						}

						//block next level
						break(1);

					}

				}

			}


			//target cache
			global $CACHE_MANAGER;
			$CACHE_MANAGER->StartTagCache($cacheDir);
			$CACHE_MANAGER->RegisterTag("iblock_id_".$iblockId);
			$CACHE_MANAGER->EndTagCache();

			//save cache
			$oExtraCache->EndDataCache($arResult);

			//drop
			unset($oExtraCache);

		}

		return $arResult;

	}

    //functions
    public static function get_extra_content($cacheTime = 21285912, $cacheType = "Y", $cacheID = array(), $cacheDir = "/", $arParams = array(), $arGlobalParams = array(), $arElement = array(), $opCurrency = null){

    	//global vars
    	global $USER;

    	//set cache name
    	$cacheID["NAME"] = "DOUBLE_CATALOG_ITEM_CACHE";

    	//set currency
    	$cacheID["CURRECY"] = $opCurrency;

    	//set extra params
    	$cacheID["EXTRA_PARAMS"] = serialize($arParams);

		//extra settings from cache
		$oExtraCache = new CPHPCache();

		//init cache cache
		if($cacheType != "N" && $oExtraCache->InitCache($cacheTime, serialize($cacheID), $cacheDir)){
			//get info by cache
			$arElement = $oExtraCache->GetVars();
		}

		elseif($oExtraCache->StartDataCache()){

			//check include modules
			if(
				   !\Bitrix\Main\Loader::includeModule("iblock")
				|| !\Bitrix\Main\Loader::includeModule('highloadblock')
				|| !\Bitrix\Main\Loader::includeModule("catalog")
				|| !\Bitrix\Main\Loader::includeModule("sale")
			){

				$obExtraCache->AbortDataCache();
				ShowError("modules not installed!");
				return 0;

			}

			//set vars
			$parentElementId = !empty($arElement["PARENT_PRODUCT"]) ? $arElement["PARENT_PRODUCT"]["ID"] : $arElement["ID"];
			$userId = $USER->GetID();
			$sectionIds = array();
			$arSection = array();

			//get iblock info
			$arIBlock = CIBlock::GetArrayByID($arGlobalParams["IBLOCK_ID"]);

			//check iblock section params
			if(!empty($arIBlock["FIELDS"]["IBLOCK_SECTION"]["DEFAULT_VALUE"]["KEEP_IBLOCK_SECTION_ID"]) && $arIBlock["FIELDS"]["IBLOCK_SECTION"]["DEFAULT_VALUE"]["KEEP_IBLOCK_SECTION_ID"] == "Y"){
				$arGlobalParams["SECTION_ID"] = !empty($arElement["PARENT_PRODUCT"]) ? $arElement["PARENT_PRODUCT"]["IBLOCK_SECTION_ID"] : $arElement["IBLOCK_SECTION_ID"];
				$keepIblockSectionId = $arIBlock["FIELDS"]["IBLOCK_SECTION"]["DEFAULT_VALUE"]["KEEP_IBLOCK_SECTION_ID"];
			}

			//get last section
			if(!empty($arParams["DISPLAY_LAST_SECTION"]) && $arParams["DISPLAY_LAST_SECTION"] == "Y" ||
			   !empty($arParams["DISPLAY_SIMILAR"]) && $arParams["DISPLAY_SIMILAR"] == "Y"
			){

				//if not use main section
				if(empty($keepIblockSectionId)){
					$rsGroups = CIBlockElement::GetElementGroups($parentElementId, true);
					while($arNextGroup = $rsGroups->Fetch()){
						$arSection[$arNextGroup["DEPTH_LEVEL"]] = $arNextGroup["ID"];
					}

					// sort array reverse order
					if(!empty($arSection)){
						krsort($arSection);
					}

					if(!empty($arSection)){
						$arElement["LAST_SECTION"] = array_slice($arSection, 0, 1);
						$rsLastSection = CIBlockSection::GetByID($arElement["LAST_SECTION"][0]);
						if($arLastSection = $rsLastSection->GetNext()){
							$arElement["LAST_SECTION"] = $arLastSection;
							$arGlobalParams["SECTION_ID"] = $arElement["LAST_SECTION"]["ID"];
						}
					}
				}

				//use main section
				else{
					$rsLastSection = CIBlockSection::GetByID($arGlobalParams["SECTION_ID"]);
					if($arLastSection = $rsLastSection->GetNext()){
						$arElement["LAST_SECTION"] = $arLastSection;
					}
				}

				//get section path list
				$nav = CIBlockSection::GetNavChain(false, $arGlobalParams["SECTION_ID"]);
				while($arSectionPath = $nav->GetNext()){
					$arElement["SECTION_PATH_LIST"][$arSectionPath["ID"]] = $arSectionPath;
					$sectionIds[$arSectionPath["ID"]] = $arSectionPath["ID"];
				}

				//get show_sku_table property
				if(!empty($sectionIds)){
					$rsList = CIBlockSection::GetList(array(), array("ID" => $sectionIds, "IBLOCK_ID" => $arGlobalParams["IBLOCK_ID"]), false, array("ID", "IBLOCK_ID", "UF_SHOW_SKU_TABLE"));
					while($arNextSection = $rsList->GetNext()){
						if(!empty($arNextSection["UF_SHOW_SKU_TABLE"])){
							$arElement["SECTION_PATH_LIST"][$arSectionPath["ID"]]["UF_SHOW_SKU_TABLE"] = $arNextSection["UF_SHOW_SKU_TABLE"];
						}
					}
				}

			}

			// related products
			if(!empty($arParams["DISPLAY_RELATED"]) && $arParams["DISPLAY_RELATED"] == "Y"){
				if(!empty($arElement["PROPERTIES"]["RELATED_PRODUCT"]["VALUE"])){
					$arSelect = array("ID");
					$arFilter = array("IBLOCK_ID" => $arGlobalParams["IBLOCK_ID"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "ID" => $arElement["PROPERTIES"]["RELATED_PRODUCT"]["VALUE"]);
					$rsRelated = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
					$arElement["RELATED_COUNT"] = $rsRelated->SelectedRowsCount();
				}
			}

			// similar products
			if(!empty($arParams["DISPLAY_SIMILAR"]) && $arParams["DISPLAY_SIMILAR"] == "Y"){
				if(!empty($arElement["LAST_SECTION"]["ID"]) || !empty($arElement["PROPERTIES"]["SIMILAR_PRODUCT"]["VALUE"])){

					if(empty($arElement["PROPERTIES"]["SIMILAR_PRODUCT"]["VALUE"])){
						$similarFilter = array("IBLOCK_ID" => $arGlobalParams["IBLOCK_ID"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "SECTION_ID" => $arElement["LAST_SECTION"]["ID"], "!ID" => $parentElementId);
						$rsSimilar = CIBlockElement::GetList(array(), $similarFilter, false, false, array("ID"));
					}
					else{
						$similarFilter = array("IBLOCK_ID" => $arGlobalParams["IBLOCK_ID"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "ID" => $arElement["PROPERTIES"]["SIMILAR_PRODUCT"]["VALUE"]);
						$rsSimilar = CIBlockElement::GetList(array(), $similarFilter, false, false, $arSelect);
					}

					$arElement["SIMILAR_COUNT"] = $rsSimilar->SelectedRowsCount();
					$arElement["SIMILAR_FILTER"] = $similarFilter;

				}
			}

			//get brand
			if(!empty($arParams["DISPLAY_BRAND"]) && $arParams["DISPLAY_BRAND"] == "Y"){
				if(!empty($arElement["PROPERTIES"]["ATT_BRAND"]["VALUE"])){
					$arBrandFilter = Array("ID" => $arElement["PROPERTIES"]["ATT_BRAND"]["VALUE"], "ACTIVE" => "Y");
					$rsBrand = CIBlockElement::GetList(array(), $arBrandFilter, false, false, array("ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "DETAIL_PICTURE"));
					if($brandElement = $rsBrand->GetNextElement()){
						$arElement["BRAND"] = $brandElement->GetFields();
						$arElement["BRAND"]["PICTURE"] = CFile::ResizeImageGet($arElement["BRAND"]["DETAIL_PICTURE"], array("width" => 250, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false);
					}
				}
			}

			// video and files
			if(!empty($arParams["DISPLAY_FILES_VIDEO"]) && $arParams["DISPLAY_FILES_VIDEO"] == "Y"){
				if(!empty($arElement["PROPERTIES"])){
					foreach ($arElement["PROPERTIES"] as $ips => $arProperty) {
						if($arProperty["PROPERTY_TYPE"] == "F" && $arProperty["CODE"] != "MORE_PHOTO" && !empty($arProperty["VALUE"])){
							if(is_array($arProperty["VALUE"])){
								foreach ($arProperty["VALUE"] as $ipv => $arPropertyValue) {
									$arTmpFile = CFile::GetByID($arPropertyValue)->Fetch();
									$arTmpFile["PARENT_NAME"] = $arProperty["NAME"];
									$arTmpFile["SRC"] = CFile::GetPath($arTmpFile["ID"]);
									$arElement["FILES"][] = $arTmpFile;
								}
							}else{
								$arTmpFile = CFile::GetByID($arProperty["VALUE"])->Fetch();
								$arTmpFile["PARENT_NAME"] = $arProperty["NAME"];
								$arTmpFile["SRC"] = CFile::GetPath($arTmpFile["ID"]);
								$arElement["FILES"][] = $arTmpFile;
							}
						}elseif($arProperty["CODE"] == "VIDEO" && !empty($arProperty["VALUE"])){
							if(is_array($arProperty["VALUE"])){
								foreach ($arProperty["VALUE"] as $ipv => $arPropertyValue) {
									$arElement["VIDEO"][] = $arPropertyValue;
								}
							}else{
								$arElement["VIDEO"][] = $arProperty["VALUE"];
							}
						}
					}
				}
			}

			// get pictures for slider
			if(!empty($arParams["DISPLAY_MORE_PICTURES"]) && $arParams["DISPLAY_MORE_PICTURES"] == "Y"){
				// resize pictures params for get_more_pictures function
				$arResizeParams = array(
					"SMALL_PICTURE" => array(
						"HEIGHT" => 50,
						"WIDTH" => 50
					),
					"REGULAR_PICTURE" => array(
						"HEIGHT" => 300,
						"WIDTH" => 300
					),
					"MEDIUM_PICTURE" => array(
						"HEIGHT" => 500,
						"WIDTH" => 500
					),
					"LARGE_PICTURE" => array(
						"HEIGHT" => 1200,
						"WIDTH" => 1200
					)
				);

				// push more pictures from detail page
				// get_more_pictures you find in class.php (component)
				if(!empty($arElement["DETAIL_PICTURE"]) && is_numeric($arElement["DETAIL_PICTURE"])){
					// push detail picture in images array
					$arElement["IMAGES"][] = DwItemInfo::get_more_pictures($arElement["DETAIL_PICTURE"], $arResizeParams);
				}else{
					// get picture from parent product
					if(!empty($arElement["PARENT_PRODUCT"]["DETAIL_PICTURE"])){
						// get more images
						$arElement["IMAGES"][] = DwItemInfo::get_more_pictures($arElement["PARENT_PRODUCT"]["DETAIL_PICTURE"], $arResizeParams);
					}
					else{
						// if detail picture is empty
						$arElement["IMAGES"][] = array(
							"SMALL_IMAGE" => array("SRC" => SITE_TEMPLATE_PATH."/images/empty.png"),
							"MEDIUM_IMAGE" => array("SRC" => SITE_TEMPLATE_PATH."/images/empty.png"),
							"LARGE_IMAGE" => array("SRC" => SITE_TEMPLATE_PATH."/images/empty.png")
						);
					}
				}

				// push more pictures from more_photo property
				if(!empty($arElement["PROPERTIES"]["MORE_PHOTO"]["VALUE"])){
					foreach ($arElement["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $nextPictureID){
						$arElement["IMAGES"][] = DwItemInfo::get_more_pictures($nextPictureID, $arResizeParams);
					}
				}
			}


			if(!empty($arParams["DISPLAY_FORMAT_PROPERTIES"]) && $arParams["DISPLAY_FORMAT_PROPERTIES"] == "Y"){
				//create display properties
				foreach ($arElement["PROPERTIES"] as $arNextProperty){
					$arElement["DISPLAY_PROPERTIES"][$arNextProperty["CODE"]] = CIBlockFormatProperties::GetDisplayValue($arElement, $arNextProperty, "catalog_out");
				}
			}

			//tags
			if(!empty($arGlobalParams["CATALOG_SHOW_TAGS"]) && $arGlobalParams["CATALOG_SHOW_TAGS"] == "Y"){

				//vars
				$arElement["ELEMENT_TAGS"] = array();

				//get tags prom parent product
				$arElement["TAGS"] = !empty($arElement["PARENT_PRODUCT"]) ? $arElement["PARENT_PRODUCT"]["TAGS"] : $arElement["TAGS"];

				//check filling
				if(!empty($arElement["TAGS"])){

					//calculate section path
					if(!isset($arGlobalParams["TAGS_DETAIL_SECTION_MAX_DELPH_LEVEL"])){

						//tag path
						$sectionPath = (!empty($arGlobalParams["SECTION_CODE_PATH"]) ? $arGlobalParams["SECTION_CODE_PATH"] : $arGlobalParams["SECTION_CODE"]);

						//by id
						if(empty($sectionPath) && !empty($arGlobalParams["SECTION_ID"])){
							$sectionPath = $arGlobalParams["SECTION_ID"];
						}

						//set tags path
						$tagPath = $arGlobalParams["SEF_FOLDER"] . $sectionPath . "/";

					}

					//by option
					else{

						//TAGS_DETAIL_SECTION_MAX_DELPH_LEVEL
						foreach($arElement["SECTION_PATH_LIST"] as $secId => $nextSection){
							if($arGlobalParams["TAGS_DETAIL_SECTION_MAX_DELPH_LEVEL"] >= $nextSection["DEPTH_LEVEL"]){
								$tagPath = $nextSection["SECTION_PAGE_URL"];
							}
						}

					}

					//parse tags
					$arTags = explode(",", $arElement["TAGS"]);

					//each tags
					foreach($arTags as $inx => $tagName){

						//translit tag
						$tagCode = Cutil::translit($tagName, LANGUAGE_ID, array("change_case" => "L", "replace_space" => "-", "replace_other" => "-"));

						//tag info
						$arTag = array("NAME" => $tagName, "CODE" => $tagCode);

						//links
						//to search
						if($arGlobalParams["TAGS_DETAIL_LINK_VARIANT"] == "SEARCH"){
							$arTag["LINK"] = $arGlobalParams["TAGS_SEARCH_PATH"]."?".$arGlobalParams["TAGS_SEARCH_PARAM"]."=".$tagName;
						}

						//to section
						else{
							$arTag["LINK"] = $tagPath."tag/".$tagCode."/";
						}

						//push to result array
						$arElement["ELEMENT_TAGS"][$tagCode] = $arTag;

					}

				}

				//limit, sort, etc
				if(!empty($arElement["ELEMENT_TAGS"])){

					//max tags
					$arElement["ELEMENT_TAGS"] = array_slice($arElement["ELEMENT_TAGS"], 0, intval($arGlobalParams["MAX_TAGS"]), true);

					//sort tags
					uasort($arElement["ELEMENT_TAGS"], function($a, $b) use($arGlobalParams){

					    if($a[$arGlobalParams["TAGS_SORT_FIELD"]] == $b[$arGlobalParams["TAGS_SORT_FIELD"]]){
					        return false;
					    }

					    //desc
					    if($arGlobalParams["TAGS_SORT_TYPE"] == "DESC"){
					    	return ($a[$arGlobalParams["SORT_FIELD"]] > $b[$arGlobalParams["TAGS_SORT_FIELD"]]) ? -1 : 1;
						}

						//asc
						else{
						    if($arGlobalParams["TAGS_SORT_TYPE"] == "ASC"){
						    	return ($a[$arGlobalParams["TAGS_SORT_FIELD"]] < $b[$arGlobalParams["TAGS_SORT_FIELD"]]) ? -1 : 1;
							}
						}

					});

				}

			}

			//target cache
			global $CACHE_MANAGER;
			$CACHE_MANAGER->StartTagCache($cacheDir);
			$CACHE_MANAGER->RegisterTag("iblock_id_".$arElement["IBLOCK_ID"]);
			$CACHE_MANAGER->EndTagCache();

			//save cache
			$oExtraCache->EndDataCache($arElement);

			//drop
			unset($oExtraCache);

		}

    	//return result
        return $arElement;

    }

	//resize pictures
    public static function get_more_pictures($pictureID, $arResizeParams, $arPushImage = array()){

    	//vars
    	$arWaterMark = array();

    	//get settings
		$arTemplateSettings = DwSettings::getInstance()->getCurrentSettings();

		//watermark options
		if(!empty($arTemplateSettings["TEMPLATE_USE_AUTO_WATERMARK"]) && $arTemplateSettings["TEMPLATE_USE_AUTO_WATERMARK"] == "Y"){
	    	$arWaterMark = Array(
	            array(
	                "alpha_level" => $arTemplateSettings["TEMPLATE_WATERMARK_ALPHA_LEVEL"],
	                "coefficient" => $arTemplateSettings["TEMPLATE_WATERMARK_COEFFICIENT"],
	                "position" => $arTemplateSettings["TEMPLATE_WATERMARK_POSITION"],
	                "file" => $arTemplateSettings["TEMPLATE_WATERMARK_PICTURE"],
					"color" => $arTemplateSettings["TEMPLATE_WATERMARK_COLOR"],
	                "type" => $arTemplateSettings["TEMPLATE_WATERMARK_TYPE"],
	                "size" => $arTemplateSettings["TEMPLATE_WATERMARK_SIZE"],
	                "fill" => $arTemplateSettings["TEMPLATE_WATERMARK_FILL"],
					"font" => $arTemplateSettings["TEMPLATE_WATERMARK_FONT"],
					"text" => $arTemplateSettings["TEMPLATE_WATERMARK_TEXT"],
	                "name" => "watermark",
	            )
	        );
		}

		//get file info
		$arFileInfo = CFile::GetFileArray($pictureID);

		//get resize picture
        $arPushImage["SMALL_IMAGE"] = array_change_key_case(CFile::ResizeImageGet($pictureID, array("width" => $arResizeParams["SMALL_PICTURE"]["WIDTH"], "height" => $arResizeParams["SMALL_PICTURE"]["HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, false), CASE_UPPER);
        $arPushImage["REGULAR_IMAGE"] = array_change_key_case(CFile::ResizeImageGet($pictureID, array("width" => $arResizeParams["REGULAR_PICTURE"]["WIDTH"], "height" => $arResizeParams["REGULAR_PICTURE"]["HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, false), CASE_UPPER);
        $arPushImage["MEDIUM_IMAGE"] = array_change_key_case(CFile::ResizeImageGet($pictureID, array("width" => $arResizeParams["MEDIUM_PICTURE"]["WIDTH"], "height" => $arResizeParams["MEDIUM_PICTURE"]["HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, false, $arWaterMark), CASE_UPPER);
        $arPushImage["LARGE_IMAGE"] = array_change_key_case(CFile::ResizeImageGet($pictureID, array("width" => $arResizeParams["LARGE_PICTURE"]["WIDTH"], "height" => $arResizeParams["LARGE_PICTURE"]["HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, false, $arWaterMark), CASE_UPPER);

        //append file info
        foreach($arPushImage as $index => $nextPicture){
        	$arPushImage[$index] = array_merge($arFileInfo, $arPushImage[$index]);
        }

        return $arPushImage;
    }


}?>