<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

function imageXmlSitemapGen(){
    CModule::IncludeModule("iblock");
    $dom = new domDocument("1.0", 'utf-8');
    #$xml = $dom->createElement("xml");
    #$xml ->setAttributeNS(null, 'version', '1.0');
    #$xml ->setAttributeNS(null, 'encoding', 'utf-8');
    #$dom->appendChild($xml);
    $urlset = $dom->createElement("urlset");
    $urlset->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    $urlset->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:image','http://www.google.com/schemas/sitemap-image/1.1');

    $arSelect = Array("ID", "NAME", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "PROPERTY_MORE_PHOTO");
    $arFilter = Array("IBLOCK_ID"=>17, "INCLUDE_SUBSECTIONS" => "Y", "ACTIVE" => "Y", "PROPERTY_CITY_VALUE" => 'msk'); //ID Инфоблока и ID раздела с элементами
    $rsElement = CIBlockElement::GetList(Array("NAME" => "ASC"), $arFilter, false, false, $arSelect);
    $arResult["ITEMS"] = array();
    $i = 0;
    while($arItem = $rsElement->GetNext())
    {
        $i++;
        //$arItem = $obElement->GetFields();
        //$arItem["PROPERTIES"] = $obElement->GetProperties();
        $google_link =  'https://www.medi-salon.ru'.$arItem['DETAIL_PAGE_URL'];
        $google_img =  'https://www.medi-salon.ru'.CFile::GetPath($arItem['DETAIL_PICTURE']);

        $url = $dom->createElement("url");
        $login = $dom->createElement("loc", $google_link);
        $url->appendChild($login);

        $image = $dom->createElement("image:image");
        $image2 = $dom->createElement("image:loc", $google_img);
        $image2n = $dom->createElement("image:title", $arItem['NAME']);
        $image->appendChild($image2);
        $image->appendChild($image2n);

        if (!empty($arItem['PROPERTY_MORE_PHOTO_VALUE']))
        {
            foreach ($arItem['PROPERTY_MORE_PHOTO_VALUE'] as $key => $photo) {

                $google_img2 =  'https://www.medi-salon.ru'.CFile::GetPath($photo);

                $image = $dom->createElement("image:image");
                $image2 = $dom->createElement("image:loc", $google_img);
                $image2n = $dom->createElement("image:title", $arItem['NAME']);
                $image->appendChild($image2n);
                $image->appendChild($image2);
                $url->appendChild($image);

                $i++;
            }

        }

        $url->appendChild($image);

        $urlset->appendChild($url);


    };
    $dom->appendChild($urlset);
    $dom->save($_SERVER['DOCUMENT_ROOT']."/sitemap-image.xml");
    return 'imageXmlSitemapGen();';
}
//в корне директории откуда запускаем скрипт
 imageXmlSitemapGen();
?>
