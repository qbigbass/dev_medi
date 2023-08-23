<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


$action = '';
if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], array('show', 'scan_send', 'alert', 'hide_alert', 'get_schedule', 'get_schedule_salons'))) {
    $action = strval($_REQUEST['action']);
} else
    die();

    if ($action == 'get_schedule_salons'){

        $salons = [];

        $cityid = SITE_ID == 's2' ? 328 : 327;

        $def_city  = $GLOBALS['medi']['site_order'][SITE_ID];

        $obSalons = CIBlockElement::GetList(
            ["SORT"=>"asc"],
            ['SECTION_ID' => $cityid, 'IBLOCK_ID'=>24, 'ACTIVE'=>'Y', '>PROPERTY_STORE'=>0, 'ACTIVE_DATE'=>'Y'],
            false,
            false,
            ['ID', 'NAME', 'PREVIEW_TEXT','PROPERTY_STORE']
        );
        while ($arSalons = $obSalons->GetNext())
        {
            if ($arSalons['PROPERTY_STORE_VALUE'] > 0) {
                $sFilter['UF_CITY'] = $def_city;
                $sFilter  = array(
                    "ACTIVE" =>"Y",

                    "UF_SALON"=>true,
                    "ID" => $arSalons['PROPERTY_STORE_VALUE']
                );
                $resStore = CCatalogStore::GetList(array("SORT"=>"ASC"), $sFilter, false, false, array("ID", "CODE",  "ADDRESS", "DESCRIPTION", "ACTIVE","UF_METRO", "UF_STORE_PUBLIC_NAME"));
                if($sklad = $resStore->Fetch())
                {
                    $sklad['ADDRESS'] = preg_replace("/[0-9]{6},/", "", $sklad["ADDRESS"]);
                    $sklad['ADDRESS'] = str_replace(['Москва, ', 'Санкт-Петербург, '], '',  $sklad["ADDRESS"]);
                    $metro = unserialize($sklad['UF_METRO']);
                    if (!empty($metro[0]))
                    {
                        $rsElm = CIBlockElement::GetList(array(), array("ID" => $metro[0], "IBLOCK_ID" => "23", "ACTIVE"=>"Y"), false, false, array("ID", "NAME", "IBLOCK_SECTION_ID"));
                        if ($arMetro = $rsElm -> GetNext()) {

                            $rsSect = CIBlockSection::GetList(array("NAME"=>"ASC"), array( "IBLOCK_ID" => "23", "ACTIVE"=>"Y", "ID"=> $arMetro['IBLOCK_SECTION_ID']), false, array("NAME", "PICTURE", "IBLOCK_SECTION_ID" ));
                            if ($arSect = $rsSect->GetNext()) {
                                if ($arSect['PICTURE'] > 0) {
                                    $arSect['ICON'] = CFile::GetFileArray($arSect["PICTURE"]);
                                }
                                $arMetro['SECTION'] = $arSect;
                            }
                            $sklad['METRO'] = $arMetro;
                        }

                    }

                    $arSalons['SALON'] = $sklad;
                }
                $salons[] = $arSalons;
            }
        }
        $result = ['status'=>'ok', 'data'=> $salons];

        header("Content-type: application/json; charset=utf-8");
        echo json_encode($result);
    }
    elseif ($action == 'get_schedule')
    {
        CModule::IncludeModule("catalog");
        if (intval($_REQUEST['id']) > 0)
        {
            $obSalons = CIBlockElement::GetList(
                ["SORT"=>"asc"],
                ['PROPERTY_STORE' => intval($_REQUEST['id']), 'IBLOCK_ID'=>24, 'ACTIVE'=>'Y', 'ACTIVE_DATE' => 'Y'],
                false,
                false,
                ['ID', 'NAME', 'PREVIEW_TEXT','PROPERTY_STORE']
            );
            if ($arSalons = $obSalons->GetNext()) {

                $sFilter  = array(
                    "ACTIVE" =>"Y",

                    "UF_SALON"=>true,
                    "ID" => $arSalons['PROPERTY_STORE_VALUE']
                );
                $resStore = CCatalogStore::GetList(array("SORT"=>"ASC"), $sFilter, false, false, array("ID", "CODE",  "ADDRESS", "DESCRIPTION", "ACTIVE","UF_METRO"));
                if($sklad = $resStore->Fetch())
                {
                    $arSalons['SALON'] = $sklad;
                }

                $arSalons['SITE_ID'] = SITE_ID;

                $arSalons['PREVIEW_TEXT'] = str_replace(["\r","\n", "\t"], "", $arSalons['PREVIEW_TEXT']);
                $res_days = preg_match_all("/<td>(.*)<\/td>/smU", $arSalons['PREVIEW_TEXT'], $days);
                $time = [];
                if (!empty($days[1]))
                {
                    foreach($days[1] AS $k=>$d)
                    {
                        if ($k % 2 == 1)
                        {
                           $time[] = trim(str_replace(" ", "",$d));
                        }
                    }
                }
                $result = ['status'=>'ok', 'data'=> $arSalons, 'days'=>$time];
            }
            else {
                $result = ['status'=>'error', 'text'=> 'Произошла ошибка 1'];
            }

        }
        else {
            $result = ['status'=>'error', 'text'=> 'Произошла ошибка 2'];
        }

        header("Content-type: application/json; charset=utf-8");
        echo json_encode($result);
    }
    elseif ($action == 'show') {

        if (strval($_REQUEST['cur_city']) == '')
        {
            $cur_city = "";
        }

        if (in_array(strval($_REQUEST['cur_city']), $GLOBALS['medi']['sfolder']))
        {
            $cur_city = strval($_REQUEST['cur_city']);
        }


        $show_city = array_search($cur_city, $GLOBALS['medi']['sfolder']);

        $assort = [];
        if(!empty($_REQUEST['assortiment']))
        {
            foreach($_REQUEST['assortiment'] AS $a)
            {
                if (intval($a) > 0)
                    $assort[] = intval($a);
            }
            $GLOBALS['arrFilterAjax']['UF_ASSORTMENT'] = implode(",",  $assort);
        }

        $service = [];
        if (!empty($_REQUEST['services'])) {
            foreach ($_REQUEST['services'] AS $s) {
                if (intval($s) > 0)
                    $service[] = intval($s);
            }
            $GLOBALS['arrFilterAjax']['UF_SERVICES'] = implode(",",  $service);
        }

        // Получаем пользовательские свойства складов для формирования фильтра
        $rsData = CUserTypeEntity::GetList( array("SORT"=>"ASC"), array("ACTIVE"=>"Y", "ENTITY_ID" =>  "CAT_STORE") );
        $arStoresUF = [];
        while ($arRes = $rsData->Fetch())
        {
            // Получаем значения для свойств типа список ()
            if ($arRes['USER_TYPE_ID'] == "enumeration")
            {
                $obEnum = new \CUserFieldEnum;
                $rsEnum = $obEnum->GetList(array(), array("USER_FIELD_ID" => $arRes['ID']));

                $enum = array();

                $arrFilter2 = [];
                $arrFilter2['UF_SALON'] = true;
                $arrFilter2['UF_CITY'] = $GLOBALS['medi']['site_order'][$show_city];
                while ($arEnum = $rsEnum->Fetch()) {
                    $arrFilter2[$arRes['FIELD_NAME']] = $arEnum["ID"];

                    $cntStores = CCatalogStore::GetList([], $arrFilter2,[]);

                    if($cntStores > 0) {
                        $enum[$arEnum["ID"]] = $arEnum["VALUE"];
                    }
                }


                $arRes['VALUES'] = $enum;
                $arStoresUF[$arRes['FIELD_NAME']] = $arRes;
            }

        }
        if (!empty($arStoresUF))
        {
            $UF_PROPS = $arStoresUF;
        }
        $show_city = array_search($cur_city, $GLOBALS['medi']['sfolder']);
        $GLOBALS['arrFilterAjax']['UF_CITY'] = $GLOBALS['medi']['site_order'][$show_city];

        $APPLICATION->IncludeComponent(
            "dresscode:catalog.store.list",
            "salons",
            Array(

                "FILTER_NAME" => "arrFilterAjax",
                "CACHE_TIME" => "360000",
                "CACHE_TYPE" => "N",
                "COMPONENT_TEMPLATE" => ".default",
                "MAP_TYPE" => "0",
                "PHONE" => "Y",
                "SCHEDULE" => "Y",
                "EMAIL" => "Y",
                "SEF_FOLDER" => "/salons/",
                "SEF_MODE" => "Y",
                "SET_TITLE" => "N",
                "TITLE" => "Список салонов с подробной информацией",
                "PATH_TO_LISTSTORES" => "",
                "PATH_TO_ELEMENT" => "#store_id#/",
                "PROPS" => $UF_PROPS

            ),
            $component
        ); ?>
        <script>
            ymaps.ready(init);</script>
        <?
    }
    elseif ($action == 'scan_send') {
        if(!empty($_REQUEST) && !defined("BX_UTF")){
            foreach ($_REQUEST as $key => $nextValue) {
                if(is_array($nextValue)){
                    foreach ($_REQUEST[$key] as $kkey => $nextElement) {
                        if ($kkey != 'form_file_60' && $kkey != 'form_file_88' && $kkey != 'form_file_118')
                            $_REQUEST[$key][$kkey] = iconv("UTF-8", "WINDOWS-1251//IGNORE",  $nextElement);
                    }
                }else{
                    if ($kkey != 'form_file_60' && $kkey != 'form_file_88' && $kkey != 'form_file_118')
                        $_REQUEST[$key] = iconv("UTF-8", "WINDOWS-1251//IGNORE",  $nextValue);
                }
            }
        }

        $APPLICATION->IncludeComponent("bitrix:form.result.new", "ajax", Array(
            "CACHE_TIME" => "0",
            "CACHE_TYPE" => "N",
            "CHAIN_ITEM_LINK" => "",
            "CHAIN_ITEM_TEXT" => "",
            "EDIT_URL" => "",
            "IGNORE_CUSTOM_TEMPLATE" => "N",
            "LIST_URL" => "",
            "SEF_MODE" => "N",
            "SUCCESS_URL" => "",
            "USE_EXTENDED_ERRORS" => "Y",
            "WEB_FORM_ID" => intval($_REQUEST['WEB_FORM_ID']),
            "COMPONENT_TEMPLATE" => "",
            "VARIABLE_ALIASES" => array(
                "WEB_FORM_ID" => "WEB_FORM_ID",
                "RESULT_ID" => "RESULT_ID",
            )
        ),
            false
        );
    }
    elseif ($action == 'alert') {

        $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>27, "CODE"=>"SITE"));
        while($enum_fields = $property_enums->GetNext())
        {
            if ($enum_fields["XML_ID"] == SITE_ID) {
                $site_id = $enum_fields['ID'];
            }
        }

        $arFilter = ["IBLOCK_ID"=>27, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y"];
        if ($site_id)
        {
            $arFilter["PROPERTY_SITE"] = $site_id;
        }
        $obElement = CIBlockElement::GetList(["SORT"=>"ASC"], $arFilter, false, false, ["ID", "PREVIEW_TEXT"]);
        if ($arElement = $obElement->GetNext() ){


            $arElement['PREVIEW_TEXT'] =  str_replace("#PHONE#", $GLOBALS['medi']['phones'][SITE_ID], $arElement['PREVIEW_TEXT']);

            echo $arElement['PREVIEW_TEXT'];

        }
    }
    elseif ($action == 'hide_alert'){
        if ($_REQUEST['id'] > 0)
            $_SESSION['top_alert_hide'][] = $_REQUEST['id'];
    }
