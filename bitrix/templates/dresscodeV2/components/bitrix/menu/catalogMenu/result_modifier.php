<?
$obCache = new CPHPCache();
if($obCache->InitCache(36000, SITE_ID."v2.27", "/")){
    $arResult = $obCache->GetVars();
}
elseif($obCache->StartDataCache()){

    $sfolder = ($GLOBALS['medi']['sfolder'][SITE_ID] != '' ? $GLOBALS['medi']['sfolder'][SITE_ID] : 'msk');

    if(!empty($arResult)){
        $i = 0;
        $b = 0;

        foreach($arResult as $k => $arElement){
            $obSect = CIBlockSection::GetList(
                ["ID"=>"ASC"],
                ["IBLOCK_ID" => "17", "ACTIVE"=>"Y", "ID"=>$arElement["PARAMS"]["ID"]],
                false,
                ["ID","UF_REGIONS", "UF_MENU_TMPL", "UF_MENU_IMG", "UF_ADDLINK", "UF_ADDPHOTO", "UF_ADDNAME", "SORT"]
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

            if (in_array(SITE_ID, $arProps)){
                if($arElement["DEPTH_LEVEL"] == 1){
                    $i++;
                    $sectionID = $arElement["PARAMS"]["ID"];
                    $IBLOCK_ID = $arElement["PARAMS"]["IBLOCK_ID"];
                    $arResult["SECTIONS"][$sectionID] = $sectionID;
                    $arResult["ITEMS"][$i] = array(
                        "TEXT" => $arElement["TEXT"],
                        "LINK" => $arElement["LINK"],
                        "ID" => $arElement["PARAMS"]["ID"],
                        "SELECTED" => $arElement["SELECTED"],
                        "PICTURE" => $arElement["PARAMS"]["PICTURE"],
                        "BIG_PICTURE" => $arElement["PARAMS"]["BIG_PICTURE"],
                        "DETAIL_PICTURE" => $arElement["PARAMS"]["DETAIL_PICTURE"],
                        "IBLOCK_ID" => $arElement["PARAMS"]["IBLOCK_ID"],
                        "ELEMENT_CNT" => $arSect["UF_CNT_".$cnt_indx],
                        "MENU_TMPL" => $arSect["UF_MENU_TMPL"],
                        "ADDLINK" => $arSect["UF_ADDLINK"],
                        "ADDNAME" => $arSect["UF_ADDNAME"],
                        "ADDPHOTO" => ($arSect["UF_ADDPHOTO"] > 0 ? CFile::GetFileArray($arSect["UF_ADDPHOTO"]) : '')
                    );
                }

					elseif($arElement["DEPTH_LEVEL"] == 2){
						$b++;
						if($arElement["PARAMS"]["FROM_IBLOCK"] <= 50){
							$from = 1;
						}elseif($arElement["PARAMS"]["FROM_IBLOCK"] <= 100){
							$from = 2;
						}elseif($arElement["PARAMS"]["FROM_IBLOCK"] <= 150){
							$from = 3;
						}elseif($arElement["PARAMS"]["FROM_IBLOCK"] <= 200){
							$from = 4;
						}else{
							$from = 1;
						}
						$arResult["SECTIONS"][$arElement["PARAMS"]["ID"]] = $sectionID;
						$arResult["ITEMS"][$i]["ELEMENTS"][$from][$b] = array(
							"TEXT" => $arElement["TEXT"],
							"LINK" => $arElement["LINK"],
							"SELECTED" => $arElement["SELECTED"],
							"PICTURE" => $arElement["PARAMS"]["PICTURE"],
							"ELEMENT_CNT" => $arSect["UF_CNT_".$cnt_indx],
							"MENU_IMG" => $arSect["UF_MENU_IMG"]
						);
					}elseif($arElement["DEPTH_LEVEL"] == 3){
						$arResult["SECTIONS"][$arElement["PARAMS"]["ID"]] = $sectionID;
						$arResult["ITEMS"][$i]["ELEMENTS"][$from][$b]["ELEMENTS"][] = array(
							"TEXT" => $arElement["TEXT"],
							"LINK" => $arElement["LINK"],
							"SELECTED" => $arElement["SELECTED"],
							"ELEMENT_CNT" => $arSect["UF_CNT_".$cnt_indx]
						);
					}

				}
				else {

						unset($arResult[$k]);
				}

			}

			// new drop menu from iblock
			$obSect = CIBlockSection::GetList(
				["SORT"=>"ASC"],
				["IBLOCK_ID" => "32", "ACTIVE"=>"Y", "UF_CITY_VALUE"=>$GLOBALS['medi']['region_cities'][SITE_ID], "DEPTH_LEVEL"=>"1" ],
			false,
				["ID", "NAME", "DEPTH_LEVEL", "UF_ICON", "UF_ADDITIONAL", "UF_CATLINK", "UF_LINK", "UF_FULLNAME"]
			);

	 	   //__(	["IBLOCK_ID" => "31", "ACTIVE"=>"Y", "UF_CITY_VALUE"=>$GLOBALS['medi']['region_cities'][SITE_ID],  "DEPTH_LEVEL"=>"1" ]);
		   $i =0;
			while($arSect = $obSect->GetNext()){
				//print_r($arSect);
				// иконка
				if ($arSect['UF_ICON'] > 0)
				{
					$icon = CFile::GetFileArray($arSect['UF_ICON']);
					$arSect['ICON'] = $icon;
				}
				// привязка к разделу
				if (intval($arSect['UF_CATLINK']) > 0)
				{
					$obCatSect = CIBlockSection::GetList(
						["SORT"=>"ASC"],
						["IBLOCK_ID" => "17", "ACTIVE"=>"Y", "ID"=>$arSect['UF_CATLINK'],
						'UF_REGIONS_VALUE' => $GLOBALS['medi']['region_cities'][SITE_ID]
					],
					false,
						["ID", "NAME", "DEPTH_LEVEL", "SECTION_PAGE_URL", "UF_REGIONS"]
					);


					if($arCatSect = $obCatSect->GetNext()){
						$rsRegs = CUserFieldEnum::GetList(array(), array(
				            "ID" => $arCatSect['UF_REGIONS']
				        ));
				        $arProps = [];
				        while($arRegs = $rsRegs->GetNext())
				        {
				            $arProps[$arRegs['ID']] =  $arRegs['XML_ID'];
				        }

						if (in_array(SITE_ID, $arProps)){

							$arSect['CATLINK'] = $arCatSect;
						}
						else {
							unset($arSect);
							continue;
						}
					}
				}
				// второстепенное меню
				$obSubSect = CIBlockSection::GetList(
					[  "SORT"=>"ASC"],
					["IBLOCK_ID" => "32", "ACTIVE"=>"Y", "UF_CITY_VALUE"=>$GLOBALS['medi']['region_cities'][SITE_ID],  "DEPTH_LEVEL"=>"2", "SECTION_ID"=>$arSect['ID'] ],
				false,
					["ID", "NAME", "DEPTH_LEVEL", "UF_ICON", "UF_ADDITIONAL", "UF_CATLINK", "UF_LINK", "UF_FULLNAME"]
				);
				$subElements = [];
				while($arSubSect = $obSubSect->GetNext()){
					// иконка

					if ($arSubSect['UF_ICON'] > 0)
					{
						$icon = CFile::GetFileArray($arSubSect['UF_ICON']);
						$arSubSect['ICON'] = $icon;
					}
					// привязка к разделу
					if (intval($arSubSect['UF_CATLINK']) > 0)
					{
						$obCatSect = CIBlockSection::GetList(
							["SORT"=>"ASC"],
							["IBLOCK_ID" => "17", "ACTIVE"=>"Y", "ID"=>$arSubSect['UF_CATLINK']],
						false,
							["ID", "NAME", "DEPTH_LEVEL", "SECTION_PAGE_URL", "UF_REGIONS"]
						);
						if($arCatSubSect = $obCatSect->GetNext()){
							$rsRegs = CUserFieldEnum::GetList(array(), array(
					            "ID" => $arCatSect['UF_REGIONS']
					        ));
					        $arProps = [];
					        while($arRegs = $rsRegs->GetNext())
					        {
					            $arProps[$arRegs['ID']] =  $arRegs['XML_ID'];
					        }

							if (in_array(SITE_ID, $arProps)){

								$arSubSect['CATLINK'] = $arCatSubSect;
							}
							else {
								unset($arSubSect);
								continue;
							}

						}
					}
					$subElements[] = $arSubSect;
				}
				if ($arSect['ID'] == '546'){
					//__($subElements);
				}
				$arSect['ELEMENTS'] = $subElements;
				$arResult['DROP_MENU'][$i] = $arSect;

				$i++;
			}
		}
	   $obCache->EndDataCache($arResult);
	}


?>
