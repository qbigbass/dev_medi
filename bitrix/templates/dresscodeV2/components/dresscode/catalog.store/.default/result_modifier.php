<?

$cur_city_folder = explode("/", $APPLICATION->GetCurDir());

// Москва
if ($cur_city_folder[1] == 'salons')
    $cur_city = "";
// Остальные
 else
    $cur_city = $cur_city_folder[1];

$show_city = array_search($cur_city, $GLOBALS['medi']['sfolder']);
if ($show_city == 's0') $show_city = 's1';

// Получаем пользовательские свойства складов для формирования фильтра
$rsData = CUserTypeEntity::GetList( array("SORT"=>"ASC"), array("ACTIVE"=>"Y", "ENTITY_ID" =>  "CAT_STORE") );
$arStoresUF = array();
while ($arRes = $rsData->Fetch())
{
    // Получаем значения для свойств типа список ()
    if ($arRes['USER_TYPE_ID'] == "enumeration")
    {
        $obEnum = new \CUserFieldEnum;
        $rsEnum = $obEnum->GetList(array(), array("USER_FIELD_ID" => $arRes['ID']));

        $enum = array();

        $arrFilter2 = [];
        $arrFilter2['ACTIVE'] ='Y';
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
    }

    $arStoresUF[$arRes['FIELD_NAME']] = $arRes;
}
if (!empty($arStoresUF))
{
    $arResult['UF_PROPS'] = $arStoresUF;
}
