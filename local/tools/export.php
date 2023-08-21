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
    ["IBLOCK_ID" => "17", "ACTIVE" => "Y", "PROPERTY_ATT_BRAND" => 7],
    
    false,
    false,
    //["nTopCount"=>1],
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
        ["ID", "IBLOCK_ID", "NAME", "PROPERTY_CML2_ARTICLE", "PROPERTY_COLOR"]
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
        
        
        $elem = [
            $arElm2['ID'],
            mb_convert_encoding($arElm2['NAME'], 'windows-1251', 'urf-8'),
            $arElm2['PROPERTY_CML2_ARTICLE_VALUE'],
            mb_convert_encoding($color, 'windows-1251', 'urf-8')
        
        ];
        
        
        $arOffers[] = $elem;
    }
    
}
print_r($arOffers);
$fp = fopen($_SERVER['DOCUMENT_ROOT'] . '/local/tools/exp1.csv', 'w+');

foreach ($arOffers as $fields) {
    fputcsv($fp, $fields, ';');
}

fclose($fp);


