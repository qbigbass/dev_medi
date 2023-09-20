<?
use	Bitrix\Main,
	Bitrix\Main\Loader,
	Bitrix\Iblock;

Loader::includeModule("iblock");
Loader::includeModule("highloadblock");	
Loader::includeModule("catalog");
class CAllIblockImageLoader
{
	public static function GetOffer($ID)
	{

		$result = [];

		$arIblock = CCatalog::GetByID($ID);
		
		$result['OFFER_IBLOCK'] = $arIblock['OFFERS_IBLOCK_ID'];
		if ($arIblock !== false)
		{
			$arOffer = CIBlock::GetProperties($arIblock['OFFERS_IBLOCK_ID']);
			while($offer_arr = $arOffer->Fetch()){
				if ($offer_arr["PROPERTY_TYPE"] == "L" || ($offer_arr["PROPERTY_TYPE"] == "S" && $offer_arr["USER_TYPE_SETTINGS"]["TABLE_NAME"]))
				{
					$result["OFFER_PROPS"][] = $offer_arr;
				}
				if ($offer_arr["PROPERTY_TYPE"] == "F")
				{
					$result["IMAGE_PROPS"][] = $offer_arr;					
				} 
			}
		}
		else
		{
		    $result["ERROR"] = "Выбранный инфоблок не является торговым каталогом";
		}
		return json_encode($result);
	}
	
	public static function getOfferValues ($IBLOCK_ID, $ELEMENT_ID, $PROP_CODE)
	{
		$values = array();

	    $offers = CCatalogSKU::getOffersList(
	        array($ELEMENT_ID),
	        $IBLOCK_ID, 
	        array(), 
	        array("NAME"), 
	        array("CODE" => array($PROP_CODE)) 
	 	);

	 	foreach ($offers[$ELEMENT_ID] as $offer)
		{
			if ($offer["PROPERTIES"][$PROP_CODE]["USER_TYPE"] == "directory"){

				$XML_ID = $offer["PROPERTIES"][$PROP_CODE]["~VALUE"];
				
				$tableName = $offer["PROPERTIES"][$PROP_CODE]['USER_TYPE_SETTINGS']['TABLE_NAME'];
				$hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(
					array("filter" => array(
						'TABLE_NAME' => $tableName
					))
				)->fetch();

				if (isset($hlblock['ID']))
				{
					$entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
					$entity_data_class = $entity->getDataClass();
					$res = $entity_data_class::getList( array('filter'=>array( 'UF_XML_ID' => $XML_ID,)) );
					if ($item = $res->fetch())
					{
						$values[$offer["PROPERTIES"][$PROP_CODE]["~VALUE"]]["NAME"] = $item['UF_NAME'];
						$values[$offer["PROPERTIES"][$PROP_CODE]["~VALUE"]]["VALUE"] = $offer["PROPERTIES"][$PROP_CODE]["~VALUE"];
					}
				}
			}
			else {
				$values[$offer["PROPERTIES"][$PROP_CODE]["~VALUE"]]["NAME"] = $offer["PROPERTIES"][$PROP_CODE]["~VALUE"];
				$values[$offer["PROPERTIES"][$PROP_CODE]["~VALUE"]]["VALUE"] = $offer["PROPERTIES"][$PROP_CODE]["~VALUE"];
			}
		}
		ksort($values);
	    return json_encode($values);
	}
	
	public static function getElement ($ELEMENT_ID)
	{
		$res = CIBlockElement::GetByID($ELEMENT_ID);
		if($ar_res = $res->GetNext())
		  return $ar_res['NAME'];
	}

    protected static function getProperties($properies, $IBLOCK_ID)
    {
        $res = CIBlockProperty::GetList(["IBLOCK_ID" => $IBLOCK_ID]);
        $allProps = [];
        $newProperties = [];

        while ($arProp = $res->fetch()) {
            $allProps[$arProp["CODE"]] = $arProp;
        }


        foreach ($properies as $key => $property) {
            if ($allProps[$property]) {
                $newProperties[$key] = $allProps[$property];
            }
        }

        return $newProperties;
        
    }

    protected static function getFileArrayField($array, $field)
    {
        if ($array["${field}[name]"] && $array["${field}[size]"] && $array["${field}[tmp_name]"] && $array["${field}[type]"]) {
            return [
                "name"      => $array["${field}[name]"],
                "size"      => $array["${field}[size]"],
                "tmp_name"  => BX_TEMPORARY_FILES_DIRECTORY ? BX_TEMPORARY_FILES_DIRECTORY.$array["${field}[tmp_name]"] : $_SERVER['DOCUMENT_ROOT']."/upload/tmp".$array["${field}[tmp_name]"],
                "type"      =>  $array["${field}[type]"],
            ];
        }
       
        return false;        
    }
	
	public static function uploadImages ($uploadArray)
	{
		$uploadArray = json_decode($uploadArray, true);
		
		$skuFilter = [];
		$offerFilter = [];
		foreach ($uploadArray["PROP_FILTER"] as $key => $val) {
			$skuFilter["PROPERTY_".$val."_VALUE"] = $uploadArray["PROP_FILTER_VALUE"][$key];
		}
        
		$offers = CCatalogSKU::getOffersList(
	        array($uploadArray["IBLOCK_ELEMENT"]),
	        0, 
	        array(), 
	        array("NAME"), 
	        array("CODE" => array($uploadArray["PROP_FILTER"])) 
	 	);
         
			
		if (isset($uploadArray['IMAGE_PROP']) && !empty($uploadArray['IMAGE_PROP'])){
			$count = -1;
			$more_pictures = array();
			$morePictures = array();
			foreach ($uploadArray as $key => $val){
				if (strpos($key, 'MORE_PICTURE') === 0){
					if (strpos($key, 'MORE_PICTURE[name]') === 0){
						$count++;
					}
					$clear_key = str_replace($count, '', $key);
					$more_pictures[$count][$clear_key] = $val;
				}
			}
			foreach ($more_pictures as $key => $more_picture) {
                $picture = self::getFileArrayField($more_picture,'MORE_PICTURE');
                if ($picture) {
                    $morePictures[$key]["VALUE"] = $picture;
                    $morePictures[$key]['DESCRIPTION'] = "";
                }
			}
		}
		$offer_count = 0;
		$skuFilter = [];
		$offerFilter = [];
        
		
		
		
		foreach ($offers[$uploadArray["IBLOCK_ELEMENT"]] as $offerItem)
		{
			$offerFilter["IBLOCK_ID"] = intval($offerItem["IBLOCK_ID"]);
			$offerFilter["ID"][] = intval($offerItem['ID']);
		}

        $properies = self::getProperties($uploadArray["PROP_FILTER"], $offerFilter["IBLOCK_ID"]);

        foreach ($properies as $key => $property) {
            
            if ($property["USER_TYPE"] == "directory") {
                $skuFilter["PROPERTY_".$property["CODE"]] = $uploadArray["PROP_FILTER_VALUE"][$key];
            }else {
                $skuFilter["PROPERTY_".$property["CODE"]."_VALUE"] = $uploadArray["PROP_FILTER_VALUE"][$key];
            }
			
		}

		$arFilter = array_merge($offerFilter, $skuFilter);
		
		$arSelect = Array("ID", "IBLOCK_ID", "NAME");
		$offerRes = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
        $offers = [];
		while($offerOb = $offerRes->GetNextElement())
		{ 
			$offer = $offerOb->GetFields();
            $offers[] = $offer;
            $arFields = [];
			$el = new CIBlockElement;
            $previewPicture = self::getFileArrayField($uploadArray,'PREVIEW_PICTURE');
            if ($previewPicture) {
                $arFields["PREVIEW_PICTURE"] = $previewPicture;
            }
            $detailPicture = self::getFileArrayField($uploadArray,'DETAIL_PICTURE');
            if ($detailPicture) {
                $arFields["DETAIL_PICTURE"] = $detailPicture;
            }
            if ($arFields) {
                $el->Update($offer['ID'],$arFields);
            }

			if (isset($uploadArray['IMAGE_PROP']) && !empty($uploadArray['IMAGE_PROP'] && $morePictures)){
				$el->SetPropertyValuesEx(
					   $offer['ID'],             
					   $offer["IBLOCK_ID"],             
					   array($uploadArray['IMAGE_PROP'] => array('VALUE' => array()))         
				);
				CIBlockElement::SetPropertyValueCode($offer['ID'], $uploadArray['IMAGE_PROP'], $morePictures);
			}
			$offer_count++;
		}

		return json_encode($offer_count);
	}
}
?>