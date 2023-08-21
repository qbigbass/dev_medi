<?

use \Bitrix\Main\Loader;
use \Bitrix\Highloadblock as HL;

set_time_limit(90);

global $DB;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$arElements = [];

\Bitrix\Main\Loader::includeModule('iblock');

$obElm = CIBlockElement::GetList(
    ["ID" => "ASC"],
    ["IBLOCK_ID" => "17", "ACTIVE" => "Y", "SECTION_ID" => 75, "INCLUDE_SUBSECTIONS" => "Y"],
    
    false,
    false,
    //["nTopCount" => 30],
    ["ID", "IBLOCK_ID", "NAME", "SECTION_ID", "DETAIL_PAGE_URL"]
);
echo "<pre>";
$arOffers = [];
while ($arElm = $obElm->GetNext()) {
    
    
    $obElm2 = CIBlockElement::GetList(
        ["ID" => "ASC"],
        ["IBLOCK_ID" => "19", "ACTIVE" => "Y", "PROPERTY_CML2_LINK" => $arElm['ID']],
        false,
        false,
        //["nTopCount"=>1],
        [
            "ID",
            "IBLOCK_ID",
            "NAME",
            "PROPERTY_CML2_ARTICLE",
            "PROPERTY_COLOR",
            "PROPERTY_SIZE",
            "PROPERTY_SIDE",
            "PROPERTY_LENGTH",
            "PROPERTY_WIDE_THIGH",
        ]
    );
    while ($arElm2 = $obElm2->GetNext()) {
        
        $query = "SELECT * FROM b_color WHERE UF_XML_ID = '" . $arElm2['PROPERTY_COLOR_VALUE'] . "'";
        $sc = $DB->Query($query);
        $ing = [];
        if ($scv = $sc->GetNext()) {
            
            $color = '';
            if ($scv['UF_NAME']) {
                $color = $scv['UF_NAME'];
            }
        }
        $query = "SELECT * FROM b_size WHERE UF_XML_ID = '" . $arElm2['PROPERTY_SIZE_VALUE'] . "'";
        $sc = $DB->Query($query);
        $ing = [];
        if ($scv = $sc->GetNext()) {
            
            $size = '';
            if ($scv['UF_NAME']) {
                $size = $scv['UF_NAME'];
            }
        }
        
        $query = "SELECT * FROM b_length WHERE UF_XML_ID = '" . $arElm2['PROPERTY_LENGTH_VALUE'] . "'";
        $sc = $DB->Query($query);
        $ing = [];
        if ($scv = $sc->GetNext()) {
            
            $length = '';
            if ($scv['UF_NAME']) {
                $length = $scv['UF_NAME'];
            }
        }
        
        
        $query = "SELECT * FROM b_wide_thigh WHERE UF_XML_ID = '" . $arElm2['PROPERTY_WIDE_THIGH_VALUE'] . "'";
        $sc = $DB->Query($query);
        $ing = [];
        if ($scv = $sc->GetNext()) {
            
            $wide_thigh = '';
            if ($scv['UF_NAME']) {
                $wide_thigh = $scv['UF_NAME'];
            }
        }
        $query = "SELECT * FROM b_side WHERE UF_XML_ID = '" . $arElm2['PROPERTY_SIDE_VALUE'] . "'";
        $sc = $DB->Query($query);
        $ing = [];
        if ($scv = $sc->GetNext()) {
            
            $side = '';
            if ($scv['UF_NAME']) {
                $side = $scv['UF_NAME'];
            }
        }
        // print_r($arElm2);
        $elem = [
            $arElm2['ID'],
            mb_convert_encoding($arElm2['NAME'], 'windows-1251', 'urf-8'),
            $arElm2['PROPERTY_CML2_ARTICLE_VALUE'],
            mb_convert_encoding($color, 'windows-1251', 'urf-8'),
            mb_convert_encoding($size, 'windows-1251', 'urf-8'),
            mb_convert_encoding($length, 'windows-1251', 'urf-8'),
            mb_convert_encoding($wide_thigh, 'windows-1251', 'urf-8'),
            mb_convert_encoding($side, 'windows-1251', 'urf-8')
        
        ];
        
        
        $arOffers[] = $elem;
    }
    
}
//print_r($arOffers);
$fp = fopen($_SERVER['DOCUMENT_ROOT'] . '/local/tools/expmkt.csv', 'w+');

foreach ($arOffers as $fields) {
    fputcsv($fp, $fields, ';');
}

fclose($fp);

echo "https://www.medi-salon.ru/local/tools/expmkt.csv";
