<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
		die();

		if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("sale"))
			die();

		//set default params
		$arParams["LAZY_LOAD_PICTURES"] = !empty($arParams["LAZY_LOAD_PICTURES"]) ? $arParams["LAZY_LOAD_PICTURES"] : "N";

		global ${$arParams["FILTER_NAME"]};

		//set filter
		if (empty($arParams["FILTER_NAME"]) || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])) {
		    //create filter array
		    $arrFilter = array();
		} else {
		    //get filter values
		    $arrFilter = ${$arParams["FILTER_NAME"]};
		    //if not array clear filter var
		    if (!is_array($arrFilter)) {
		        $arrFilter = array();
		    }
		}

		//get items
		if ($this->StartResultCache()){
			if (!defined('SITE_ID')) $site_id = 's1';
			else $site_id = SITE_ID;

			$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=> $arParams["IBLOCK_ID"], "XML_ID"=>$site_id));
			if($enum_fields = $property_enums->GetNext())
			{
				$region_prop_id = $enum_fields['ID'];
			}


			$arSelect = Array("ID", "NAME", "IBLOCK_ID", "PREVIEW_PICTURE", "DETAIL_TEXT", "DETAIL_PICTURE", "PROPERTY_POSITION", "EDIT_LINK", "DELETE_LINK", "DETAIL_PAGE_URL");
			$arFilter = Array("IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"], "IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
			if ($region_prop_id > 0)
			{
				$arFilter["PROPERTY_REGION"] = $region_prop_id;
			}
			$res = CIBlockElement::GetList(array("SORT" => "ASC"), array_merge($arrFilter, $arFilter), false, Array(), $arSelect);
			while($ob = $res->GetNextElement()){
				$fields = $ob->GetFields();
				$fields["PROPERTIES"] = $ob->GetProperties();
				$arButtons = CIBlock::GetPanelButtons(
					$fields["IBLOCK_ID"],
					$fields["ID"],
					$fields["ID"],
					array("SECTION_BUTTONS" => false,
						  "SESSID" => false,
						  "CATALOG" => true
					)
				);
				$fields["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
				$fields["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];

				if(!empty($fields['PREVIEW_PICTURE']) && !empty($fields['DETAIL_PICTURE'])){
					$fields["PREVIEW_PICTURE"] = CFile::ResizeImageGet($fields['PREVIEW_PICTURE'], array('width' => $arParams["PICTURE_WIDTH"], 'height' => $arParams["PICTURE_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, true);
					$fields["DETAIL_PICTURE"] = CFile::ResizeImageGet($fields['DETAIL_PICTURE'], array('width' => $arParams["PICTURE_WIDTH"], 'height' => $arParams["PICTURE_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, true);
				}elseif(!empty($fields['PREVIEW_PICTURE']) && empty($fields['DETAIL_PICTURE'])){
					$fields["DETAIL_PICTURE"] = $fields["PREVIEW_PICTURE"] = CFile::ResizeImageGet($fields['PREVIEW_PICTURE'], array('width' => $arParams["PICTURE_WIDTH"], 'height' => $arParams["PICTURE_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, true);
				}elseif(!empty($fields['DETAIL_PICTURE']) && empty($fields['PREVIEW_PICTURE'])){
					$fields["PREVIEW_PICTURE"] = $fields["DETAIL_PICTURE"] = CFile::ResizeImageGet($fields['DETAIL_PICTURE'], array('width' => $arParams["PICTURE_WIDTH"], 'height' => $arParams["PICTURE_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, true);
				}
				$arResult["ITEMS"][] = $fields;
			}
			$this->setResultCacheKeys(array(SITE_ID));
			$this->IncludeComponentTemplate();
		}

?>
