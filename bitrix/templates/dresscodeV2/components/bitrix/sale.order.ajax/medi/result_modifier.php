<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arParams
 * @var array $arResult
 * @var SaleOrderAjax $component
 */

 use Bitrix\Main\Context,
    Bitrix\Currency\CurrencyManager,
    Bitrix\Sale,
    Bitrix\Sale\Basket,
    Bitrix\Sale\Delivery,
    Bitrix\Sale\PaySystem,
    \Bitrix\Sale\Location;

 if (!empty($arResult["ORDER"])):
     $arOrder  = Sale\Order::load($arResult["ORDER"]['ID']);
     $propertyCollection = $arOrder->getPropertyCollection();
     $order_props = $propertyCollection->getArray();
     foreach($order_props['properties'] AS $o=>$props){

        if ($props['CODE'] == 'EMAIL')
            $arResult["ORDER_PROPS"]['EMAIL'] = $props['VALUE'][0];
        elseif ($props['CODE'] == 'PHONE')
           $arResult["ORDER_PROPS"]['PHONE'] = $props['VALUE'][0];
        elseif ($props['CODE'] == 'ADDRESS')
          $arResult["ORDER_PROPS"]['ADDRESS'] = $props['VALUE'][0];
        elseif ($props['CODE'] == 'ADDRESS_INFO')
         $arResult["ORDER_PROPS"]['ADDRESS_INFO'] = $props['VALUE'][0];
       elseif ($props['CODE'] == 'LOCATION'){
           $item = \Bitrix\Sale\Location\LocationTable::getByCode($props['VALUE'][0], array(
                'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID),
                'select' => array('*', 'NAME_RU' => 'NAME.NAME')
            ))->fetch();
            $arResult["ORDER_PROPS"]['LOCATION'] = $item['NAME_RU'];
    }
    }
     $arResult["ORDER_INFO"] = [
         'NAME' => $USER->getFirstName()
     ];


     $paySystemService = PaySystem\Manager::getObjectById($arOrder->getPaymentSystemId());
     $arResult['PAYMENT_NAME'] = $paySystemService->getField("NAME");

     $service = Delivery\Services\Manager::getById($arResult["ORDER"]['DELIVERY_ID']);
     $arResult['DELIVERY'] = $service;

    $arResult['DELIVERY_NAME'] = $service['NAME'];
     list($delivery_code, $profile_code) = explode(':', $service["CODE"]);
     //$delivFilter = ["SITE_ID"=>$arResult["ORDER"]['LID']];
     if (intval($service["CODE"])> 0) {
         $delivFilter['ID'] = $service["CODE"];
     }
     else {
         $delivFilter["SID"] = $delivery_code;
     }
     $dbDelivery = CSaleDeliveryHandler::GetList([], $delivFilter);

     if ($arDelivery = $dbDelivery->Fetch()) {
         $arResult['DELIVERY_NAME'] = $arDelivery['NAME'].'. '.$service['NAME'];
     }
     else {
         $dbDelivery = CSaleDelivery::GetList([], $delivFilter);

         if ($arDelivery = $dbDelivery->Fetch()) {
             $arResult['DELIVERY_NAME'] = $arDelivery['NAME'];
         }
     }
     $arResult['DELIVERY'] = $arDelivery;


$rsShipment = \Bitrix\Sale\Internals\ShipmentTable::getList(array(
    'filter'=>array('ORDER_ID' => $arResult["ORDER"]['ID']),
));

while($arShipment=$rsShipment->fetch())
{
    $rsExtraService = \Bitrix\Sale\Internals\ShipmentExtraServiceTable::getList(array('filter'=> array(
        'SHIPMENT_ID' => $arShipment['ID'],
        //'EXTRA_SERVICE_ID' => '3', // ID дополнительного сервиса, можно посмотреть в таблице b_sale_order_delivery_es
    )));

    while($arExtraService = $rsExtraService->fetch())
    {
        if(!!$arExtraService['VALUE'])
        {
            $arStore = \Bitrix\Catalog\StoreTable::getById($arExtraService['VALUE'])->fetch();


            if (!empty($arStore))
            {
                $select = array_merge(
        			array( "UF_*")
        		);

        		$filter = array(
        			"ACTIVE" => "Y",
        			"ID" => $arStore["ID"]
        		);

        		$rsProps = CCatalogStore::GetList(
        			array('TITLE' => 'ASC', 'ID' => 'ASC'),
        			$filter,
        			false,
        			false,
        			$select
        		);
                if ($store = $rsProps->GetNext())
        		{
                    // Привязка к станции метро
                    if (!empty($store['UF_METRO'])) {
                        $metro = unserialize($store['UF_METRO']);
                        if (!empty($metro[0])) {
                            $rsElm = CIBlockElement::GetList(array(), array("ID" => $metro[0], "IBLOCK_ID" => "23", "ACTIVE"=>"Y"), false, false, array("ID", "NAME", "IBLOCK_SECTION_ID"));
                            while ($arMetro = $rsElm -> GetNext()) {

                                $rsSect = CIBlockSection::GetList(array("NAME"=>"ASC"), array( "IBLOCK_ID" => "23", "ACTIVE"=>"Y", "ID"=> $arMetro['IBLOCK_SECTION_ID']), false, array("NAME", "PICTURE", "IBLOCK_SECTION_ID", "UF_ICON" ));
                                if ($arSect = $rsSect->GetNext()) {
                                    if ($arSect['UF_ICON'] > 0) {
                                        $arSect['ICON'] = CFile::GetFileArray($arSect["UF_ICON"]);
                                    }
                                    elseif ($arSect['PICTURE'] > 0) {
                                        $arSect['ICON'] = CFile::GetFileArray($arSect["PICTURE"]);
                                    }
                                    $arMetro['SECTION'] = $arSect;
                                }
                                  $store['METRO'] = $arMetro;
                            }
                        }
                    }

                    $arResult['STORE'] = $store;
                }
            }
            break 2;
        }
    }

}


 endif;

$component = $this->__component;
$component::scaleImages($arResult['JS_DATA'], $arParams['SERVICES_IMAGES_SCALING']);
