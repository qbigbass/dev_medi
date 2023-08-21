<?
if(!empty($arResult["SECTIONS"][0])){
	foreach($arResult["SECTIONS"] as $k=>$section){

		$arFilter = Array(
			"IBLOCK_ID" => $arResult["SECTION"]["IBLOCK_ID"],
			"GLOBAL_ACTIVE" => "Y",
			"ACTIVE" => "Y",
			"SECTION_ID" => $section["ID"],
			"CNT_ACTIVE" => "Y"
		);

			$obSect = CIBlockSection::GetList(
				["ID"=>"ASC"],
				["IBLOCK_ID" => "17", "ACTIVE"=>"Y", "ID"=>$section["ID"]],
			false,
				["ID", "NAME", "SECTION_PAGE_URL", "UF_REGIONS"]
			);
			$arSect = $obSect->GetNext();

			$rsRegs = CUserFieldEnum::GetList(array(), array(
				"ID" => $arSect['UF_REGIONS']
			));
			$arProps = [];
			while($arRegs = $rsRegs->GetNext())
			{
				$arProps[$arRegs['ID']] =  $arRegs['XML_ID'];
			}
			if (in_array(SITE_ID, $arProps)){

				$arFilterElm = Array(
					"IBLOCK_ID" => $arResult["SECTION"]["IBLOCK_ID"],
					"ACTIVE" => "Y",
					"SECTION_ID" =>  $arSect["ID"],
					"INCLUDE_SUBSECTIONS" => "Y",
					"PROPERTY_REGION_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID]
				);

				$arResult["SECTIONS"][$k] = array(
					"ID" => $arSect["ID"],
					"SELECTED" => ($arResult["SECTION"]["ID"] == $arSect["ID"] ? true : false),
					"SECTION_PAGE_URL" => $arSect["SECTION_PAGE_URL"],
					"NAME" => $arSect["NAME"],
					"ELEMENT_CNT" =>  CIBlockElement::GetList([],$arFilterElm, [], false)//$ar_result["ELEMENT_CNT"]
				);
			}
			else {
				unset($arResult['SECTIONS'][$k]);
			}


	}

	$arResult['SUM_ELM'] = 0;
	foreach($arResult['SECTIONS'] AS $arSect) {
		$arResult['SUM_ELM'] += $arSect['ELEMENT_CNT'];
	}
}
