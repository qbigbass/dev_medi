<?
if(!empty($arResult["SECTIONS"][0])){

    foreach($arResult["SECTIONS"] as $k=>$arSection){

        $obSect = CIBlockSection::GetList(
            ["ID"=>"ASC"],
            ["IBLOCK_ID" => "17", "ACTIVE"=>"Y", "ID"=>$arSection["ID"]],
        false,
            ["ID","UF_REGIONS"]
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
        if (!in_array(SITE_ID, $arProps)){
		    unset($arResult['SECTIONS'][$k]);
	    }
    }
}
