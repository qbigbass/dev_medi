<?
set_time_limit(1800);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Выгрузка заказов");
if (!$USER->IsAuthorized())
    die();

use Bitrix\Sale,
    Bitrix\Main\Type;

\Bitrix\Main\Loader::IncludeModule("sale");

$timestamp = mktime(0,0,0, 1,1, 2022);
$timestamp2 = mktime(0,0,0, 6,30, 2022);
$myDate = new \Bitrix\Main\Type\Date();
$loadDate = $myDate::createFromTimestamp($timestamp);
$loadDateTo = $myDate::createFromTimestamp($timestamp2);

$parameters = [
    'filter' => [
        "STATUS_ID" => "F",
        "DELIVERY_ID" => [60,61,62,63,105, 108],
        ">=DATE_INSERT" => $loadDate,
        "<=DATE_INSERT" => $loadDateTo
    ],
    'order' => ["DATE_INSERT" => "ASC"]
];

$dbRes = \Bitrix\Sale\Order::getList($parameters);
$count = 0;

$res = [];
$res[] = [
    mb_convert_encoding('Номер заказа', 'windows-1251', "utf-8"),
    mb_convert_encoding('Индекс', 'windows-1251', "utf-8"),
    mb_convert_encoding('Город', 'windows-1251', "utf-8"),
    mb_convert_encoding('Адрес', 'windows-1251', "utf-8"),
    mb_convert_encoding('Уточнение к адресу', 'windows-1251', "utf-8")
];
while ($orders = $dbRes->fetch())
{
    $order = Sale\Order::load($orders['ID']);
    $propertyCollection = $order->getPropertyCollection();

    $locPropValue   = $propertyCollection->getDeliveryLocation();
    $zipPropValue   = $propertyCollection->getDeliveryLocationZip();

    $city = \Bitrix\Sale\Location\LocationTable::getByCode( $locPropValue->getValue(), array(
        'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID),
        'select' => array('*', 'NAME_RU' => 'NAME.NAME')
    ) )->Fetch();

    $address = $propertyCollection->getItemByOrderPropertyId(7);
    $addressDop = $propertyCollection->getItemByOrderPropertyId(20);


    $res[] = array(
        $orders['ACCOUNT_NUMBER'],
        $zipPropValue->getValue(),
        mb_convert_encoding($city['NAME_RU'], 'windows-1251', "utf-8"),
        mb_convert_encoding($address->getValue(), 'windows-1251', "utf-8"),
        mb_convert_encoding($addressDop->getValue(), 'windows-1251', "utf-8"));
    //print_r($city);

    $count++;

}

if (!empty($res))
{

    $filename = 'addresses_msk';
    $fp = fopen($_SERVER['DOCUMENT_ROOT'].'/local/tools/'.$filename.'.csv', 'w+');

    foreach ($res as $fields) {
        fputcsv($fp, $fields,';');
    }

    fclose($fp);

    $file =$_SERVER['DOCUMENT_ROOT'].'/local/tools/'.$filename.'.csv';

    echo '<a href="/local/tools/'.$filename.'.csv">file</a>';
}
echo count($res);