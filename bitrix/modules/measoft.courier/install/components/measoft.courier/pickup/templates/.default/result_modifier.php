<?php
/**
 * Created by PhpStorm.
 * User: AS
 * Date: 24.03.2020
 * Time: 15:13
 */
use Bitrix\Sale;

$basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());

$arResult["orderWeight"] = $basket->getWeight() / 1000; // Общий вес корзины


if ( $arResult["orderWeight"] == 0 )
{
    $arResult["orderWeight"] = 0.1;
}

// courier

$courierArrSettings = [];

$result = \Bitrix\Sale\Delivery\Services\Table::getList(array(
    'filter' => array("CODE" => "courier"),
));

while ($delivery = $result->fetch())
{
    $resultProfiles = \Bitrix\Sale\Delivery\Services\Table::getList(array(
        'filter' => array("PARENT_ID" => $delivery['ID'] ),
        'select' => [ "ID" ]
    ))->fetchAll();
 
    $settings = unserialize( unserialize( $delivery["CONFIG"]["MAIN"]["OLD_SETTINGS"]) );

    if ( empty($settings["MAP_CLIENT_CODE"]) || (!isset($settings["MAP_CLIENT_CODE"])) )
    {
        if ($settings["MAP_CLIENT_CODE"] = MeasoftEvents::getMapCode($settings) )
        {
            $delivery["CONFIG"]["MAIN"]["OLD_SETTINGS"] = serialize( serialize( $settings ) );

            $res = \Bitrix\Sale\Delivery\Services\Manager::update($delivery['ID'], $delivery);
        }
    }

    foreach ( $resultProfiles as $profileArr )
    {
        $courierArrSettings[$profileArr["ID"]] = [
            "PROFILE_ID" => $profileArr["ID"],
            "USER_CODE" => $settings["CODE"],
            "MAP_CLIENT_CODE" => $settings["MAP_CLIENT_CODE"],
            "DISABLE_CALENDAR" => ($settings["DISABLE_CALENDAR"] == "Y"),
            "MAP_CSS" => ( ( ($settings["HIDE_MAP_EDITS"]=="Y") ? "hide-filter" : "" ) .' '.  ( ($settings["HIDE_MAP_SEARCH"]=="Y") ? "hide-search" : "" ) )
        ];
    }

    if ( !isset($arResult["PROP_ADDRESS"]) )
    {
        $propAddr = MeasoftEvents::deliveryConfigValue('PROP_ADDRESS', $delivery['ID']);
        $props = CSaleOrderProps::GetList(array(),array('CODE' => $propAddr));
        $propAddr='';
        while($prop=$props->Fetch()) {
            $propAddr .= $prop['ID'] . ',';
        }
        $arResult["PROP_ADDRESS"] = $propAddr;
    }

}

$arResult["courierArrSettings"] = json_encode($courierArrSettings);


?>