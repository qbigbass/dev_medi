<?
use Bitrix\Main,
    Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Sale,
    Bitrix\Catalog,
    Yandex\Market;

$eventManager->addEventHandler('yandex.market', 'onExportOfferExtendData', function(Main\Event $event) {

	$tagValueList = $event->getParameter('TAG_VALUE_LIST');
	$elementList = $event->getParameter('ELEMENT_LIST');
	$context = $event->getParameter('CONTEXT');
	$parentList = $event->getParameter('PARENT_LIST');
    $picts = [];
	foreach ($tagValueList as $elementId => $tagValue)
	{
		$element = $elementList[$elementId];
		$parent = null;

		$tagNameValue = $tagValue->getTagValue('name');
		$tagPriceValue = $tagValue->getTagValue('price');
		$tagCatValue = $tagValue->getTagValue('categoryId');
		$tagParamValue = $tagValue->getTagValue('param', true);
		$tagBarcodeValue = $tagValue->getTagValue('barcode');

        // Преобразуем GTIN в EAN-13, убираем ведущий 0
        if (strlen($tagBarcodeValue) == 14)
        {
           $tagValue->setTagValue('barcode',  substr($tagBarcodeValue, 1));
        }

		$tagBarcodeValue = $tagValue->getTagValue('barcode');

        if (in_array($context['SETUP_ID'], [23, 25]))
        {
            $tagDescValue = $tagValue->getTagValue('description');
            if (strlen($tagDescValue) > 250)
            {
                $tagValue->setTagValue('description', $tagNameValue);
            }

            $tagModelValue = $tagValue->getTagValue('model');
            $aParts = explode(" ",$tagModelValue);
            $firstWord = mb_convert_case($aParts[0],MB_CASE_TITLE,"UTF-8");
            unset($aParts[0]);

            $tagValue->setTagValue('model', $firstWord." ".implode(" ",$aParts));
        }
	}
});


// исключаем товары отсутствующие на основных складах
$eventManager->addEventHandler('yandex.market', 'onExportOfferWriteData', function(Main\Event $event) {

CModule::IncludeModule("catalog");

	$tagResultList = $event->getParameter('TAG_RESULT_LIST');
    $context = $event->getParameter('CONTEXT');

    if ($context['SETUP_ID'] != 22 && $context['SETUP_ID'] != 19  && $context['SETUP_ID'] != 25 )
    {

	foreach ($tagResultList as $elementId => $tagResult)
    	{
    		if ($tagResult->isSuccess())
    		{
    			$tagNode = $tagResult->getXmlElement();
    			$attributeList = $tagNode->attributes();

                $mainStoreAmount = 0;
                if (in_array($tagNode->categoryId, [88, 89, 90, 91, 600, 601, 308,309,310,311,312,313,314,315,316,317,318,319,320,321,300,301,302,303,304,305,306,307,545]) || ($tagNode->categoryId >= 650 && $tagNode->categoryId <= 675))
    			{
    				$filter = array(
    					"ACTIVE" => "Y",
    					"PRODUCT_ID" => $elementId,
    					["LOGIC"=> "OR",
    						["UF_STORE" => true],
    						["UF_SHOES_STORE" => true]
    					]
    				);
    			}
    			else {
    				$filter = array(
    					"ACTIVE" => "Y",
    					"PRODUCT_ID" => $elementId,
    					"UF_STORE" => true,
    				);
    			}
    			if ($context['SETUP_ID'] >= 12 && $context['SETUP_ID'] < 17)
    			{
    				$filter['SITE_ID'] = 's2';
    			}
    			else {
    				$filter['+SITE_ID'] = 's1';
    			}
    			$rsProps = CCatalogStore::GetList(
    				array('TITLE' => 'ASC', 'ID' => 'ASC'),
    				$filter,
    				false,
    				false,
    				["ID", "ACTIVE", "PRODUCT_AMOUNT", "UF_STORE", "SITE_ID"]
    			);
    			while ($mStore = $rsProps->GetNext())
    			{
    				$mainStoreAmount += $mStore['PRODUCT_AMOUNT'];
    			}
    			if ($mainStoreAmount <= 0) {
    				 $tagResult->invalidate();
    			}
    		}
    	}
    }

});


$eventManager->addEventHandler('yandex.market', 'onExportOfferWriteData', function(Main\Event $event) {


	$tagResultList = $event->getParameter('TAG_RESULT_LIST');
	$elementList = $event->getParameter('ELEMENT_LIST');
	$context = $event->getParameter('CONTEXT');
	$parentList = $event->getParameter('PARENT_LIST');


    CModule::IncludeModule("iblock");

    if ($context['SETUP_ID'] == 21 )
    {
        foreach ($tagResultList as $elementId => $tagResult)
    	{
            if ($tagResult->isSuccess())
            {
                if (isset($element['PARENT_ID']))
                {
                    $tagNode = $tagResult->getXmlElement();
                    $parent = $parentList[$element['PARENT_ID']];

                    $obElement=CIBlockElement::GetList([], ['ID'=>$parent['ID'], "IBLOCK_ID"=>$parent['IBLOCK_ID']], false, false, ['ID',"IBLOCK_ID", 'PROPERTY_OFFERS']);
                    if ($arElement = $obElement->GetNext())
                    {
                        if (!empty($arElement['PROPERTY_OFFERS_VALUE']))
                        {
                            foreach ($arElement['PROPERTY_OFFERS_VALUE'] as $key => $value) {

                                $tagNode->addChild('action', ($value ==  '%' ? 'Распродажа' : $value));

                            }
                        }
                    }

                    //w2l($parent,1, 'ym21.log');
                }
                else{
                    $tagNode = $tagResult->getXmlElement();
                    $element = $elementList[$elementId];
                    $parent = null;
                    $obElement=CIBlockElement::GetList([], ['ID'=>$element['ID'], "IBLOCK_ID"=>$element['IBLOCK_ID']], false, false, ['ID',"IBLOCK_ID", 'PROPERTY_OFFERS']);
                    if ($arElement = $obElement->GetNext())
                    {
                        if (!empty($arElement['PROPERTY_OFFERS_VALUE']))
                        {
                            foreach ($arElement['PROPERTY_OFFERS_VALUE'] as $key => $value) {

                                $tagNode->addChild('action', ($value ==  '%' ? 'Распродажа' : $value));

                            }
                        }
                    }
                }
                //$tagResult->invalidateXmlContents();
            }
    	}
    }
});

$eventManager->addEventHandler('yandex.market', 'onExportOfferWriteData', function(Main\Event $event) {

	/** @var $tagResultList Market\Result\XmlNode[] */
	/** @var $elementList array */
	/** @var $context array */
	/** @var $parentList array */
	/** @var $tagElement \SimpleXMLElement */
	$tagResultList = $event->getParameter('TAG_RESULT_LIST');
	$elementList = $event->getParameter('ELEMENT_LIST');
	$context = $event->getParameter('CONTEXT');
	$parentList = $event->getParameter('PARENT_LIST');


    CModule::IncludeModule("sale");
    CModule::IncludeModule("catalog");
    $site_id = 's1';
    if ($context['SETUP_ID'] == 33 || $context['SETUP_ID'] == 34) {
        foreach ($tagResultList as $elementId => $tagResult) {
            $tagNode = $tagResult->getXmlElement();
            $tagNode->oldprice = '';
        }
    }
    else {

        if (($context['SETUP_ID'] >= 12 && $context['SETUP_ID'] <= 16) || $context['SETUP_ID'] == 25 || $context['SETUP_ID'] == 26 || $context['SETUP_ID'] == 27 || $context['SETUP_ID'] == 34 || $context['SETUP_ID'] == 29) {
            $site_id = 's2';
        } elseif ($context['SETUP_ID'] == 17 || $context['SETUP_ID'] == 30) {
            $site_id = 's0';
        }
        // oldprice
        foreach ($tagResultList as $elementId => $tagResult) {

            if ($tagResult->isSuccess()) {
                $tagNode = $tagResult->getXmlElement();
                $element = $elementList[$elementId];

                $oldprice = (array)$tagNode->oldprice;

                if (empty($oldprice)) {
                    $arPrice = CCatalogProduct::GetOptimalPrice($elementId, 1, [2], "N", [], $site_id);

                    if ($arPrice['DISCOUNT_PRICE'] < $arPrice['RESULT_PRICE']['BASE_PRICE']) {
                        $tagNode->addChild('oldprice', $arPrice['RESULT_PRICE']['BASE_PRICE']);
                        $tagNode->price = $arPrice['DISCOUNT_PRICE'];
                        $tagResult->invalidateXmlContents();
                    }
                }
            }
        }
    }
});
