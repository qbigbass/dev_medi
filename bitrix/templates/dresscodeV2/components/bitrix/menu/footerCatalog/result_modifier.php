<?
$obCache = new CPHPCache();
if($obCache->InitCache(3600000, SITE_ID."v2.1", "/")){
	$arResult = $obCache->GetVars();
}
elseif($obCache->StartDataCache()){
	if(!empty($arResult)){
		$i = 0;
		$b = 0;

		foreach($arResult as $k => $arElement){


			$obSect = CIBlockSection::GetList(
				["ID"=>"ASC"],
				["IBLOCK_ID" => "17", "ACTIVE"=>"Y", "ID"=>$arElement["PARAMS"]["ID"]],
				false,
				["ID","UF_REGIONS", "SORT"]
			);
			$arSect = $obSect->GetNext();


			if ($arSect['SORT'] > 6000 ) {unset($arResult[$k]); continue;}

			$rsRegs = CUserFieldEnum::GetList(array(), array(
				"ID" => $arSect['UF_REGIONS']
			));
			$arProps = [];
			while($arRegs = $rsRegs->GetNext())
			{
				$arProps[$arRegs['ID']] =  $arRegs['XML_ID'];
			}

			if (!in_array(SITE_ID, $arProps)){
				unset($arResult[$k]);
			}

		}
	}
	$obCache->EndDataCache($arResult);
}


?>
