<?php

use Bitrix\Main\Loader;
use Bitrix\Sale\Order;

global $USER;

Loader::includeModule('sale');
Loader::includeModule('catalog');

if (array_intersect([1, 20], $USER->GetUserGroupArray())) {
    foreach ($arResult['ORDERS'] as $k => $order) {
        
        $order = Order::load($order['ORDER']['ID']);
        $propertyCollection = $order->getPropertyCollection();
        //$arProps = $propertyCollection->getArray();
        $orderPropertyId = 50; // MTZ
        $consultant = $propertyCollection->getItemByOrderPropertyId($orderPropertyId)->getValue();
        $arResult['ORDERS'][$k]['ORDER']['CONSULTANT'] = $consultant;
        
    }
}
