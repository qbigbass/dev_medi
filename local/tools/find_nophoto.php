<?
set_time_limit(90);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
//название - артикул- цвет - размер - модель MODEL  CML2_ARTICLE
$arElements = [];

$obElm = CIBlockElement::GetList(
    ["ID"=>"ASC"],
["IBLOCK_ID" => "17", "ACTIVE"=>"Y", "SECTION_ID" => 75, "INCLUDE_SUBSECTIONS" => "Y", "SECTION_ACTIVE"=>"Y" ],

    false,
    false,
    //["nTopCount"=>50],
    ["ID", "IBLOCK_ID", "NAME", "SECTION_ID", "DETAIL_PAGE_URL", "ACTIVE"]
);echo "<pre>";
while ($arElm = $obElm->GetNext())
{
    //print_r($arElm);
    $obElm2 = CIBlockElement::GetList(
        ["ID"=>"ASC"],
        ["IBLOCK_ID" => "19", "ACTIVE"=>"Y", "PROPERTY_CML2_LINK.ID" => $arElm['ID'], "DETAIL_PICTURE" => false, "!PROPERTY_COLOR"=>false],
        false,
        false,
        //["nTopCount"=>10],
        ["ID", "IBLOCK_ID", "NAME", "PROPERTY_CML2_ARTICLE"]
    );
    while ($arElm2 = $obElm2->GetNext())
    {

        print_r($arElm2);
        $arElements[] = [
            "ID"=>$arElm2['ID'],
            "NAME"=>mb_convert_encoding($arElm2['NAME'],'windows-1251', 'urf-8'),
            "ARTICLE"=> mb_convert_encoding($arElm2['PROPERTY_CML2_ARTICLE_VALUE'],'windows-1251', 'urf-8') ,
            "URL"=>"https://www.medi-salon.ru".$arElm['DETAIL_PAGE_URL'],
            "ADMIN"=> "https://www.medi-salon.ru/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=17&type=catalog&lang=ru&ID=".$arElm['ID']."&find_section_section=-1&WF=Y",

         ];
}

}
//print_r($arElements);

$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/local/tools/nophoto_sku.csv', 'w+');

foreach ($arElements as $fields) {
    fputcsv($fp, $fields,';');
}

fclose($fp);

    echo count($arElements);
