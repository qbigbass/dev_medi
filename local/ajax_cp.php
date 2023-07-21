<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Web\HttpClient;
use Bitrix\Sale;

$curStatuses = array(
	10 => 'Черновик (заказ создается) ',
	15 => 'Новый (заказ создан)',
	20 => 'Подтвержден (заказ принят в работу)',
	50 => 'Завершён',
	80 => 'Отменен'
);

$action = '';
if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], array('getZip', 'getLocation', 'courieristOrderForm', 'courieristOrderFormSend', 'courieristOrderFormRecount', 'courieristLogin')))
{
    $action = $_REQUEST['action'];
}

$access_token = 'B5_kZf9qWi9kGPiO15Puq1GJi-4Guj6J'; // ИП Баженов
$access_token =  '6KzrBCN6mYbyagrkm8Cl6id8loRAAlj8'; // ООО МЕДИ РУС

if ($action == 'getZip' && strlen($_REQUEST['lid']) == 10)
{
    CModule::IncludeModule('sale');

    $locationId = $_REQUEST['lid'];

    $arLocation = CSaleLocation::GetByID($locationId);

    $rsZip = CSaleLocation::GetLocationZIP($arLocation['ID']);

    if ($arZip = $rsZip->fetch()) echo json_encode($arZip['ZIP']);
}
// Получение города
elseif ($action == 'getLocation' && strlen($_REQUEST['lid']) == 10)
{
    CModule::IncludeModule('sale');

    $locationId = $_REQUEST['lid'];

    $arLocation = CSaleLocation::GetByID($locationId);

	if($arLocation['CITY_NAME'] != '') {
		echo $arLocation['CITY_NAME'];
	}
}
elseif($action == 'courieristLogin')
{
die;
			$httpClient = new HttpClient();
			$httpClient->setHeader('Content-Type', 'application/json', true);

			$response = $httpClient->post('http://my.courierist.com/api/v1/access/login', json_encode(['login'=>'medirus', 'password'=>'27664']));
			$resp = json_decode($response);
			print_r($resp);
}
// Пересчет стоимости доставки
elseif ($action == 'courieristOrderFormRecount' && intval($_REQUEST['orderID']) > 0)
{
	global $APPLICATION;

	$orderID = intval($_REQUEST['orderID']);
	$data =  ($_REQUEST['recountData']);

	$SALE_RIGHT = $APPLICATION->GetGroupRight("sale");

	if ($SALE_RIGHT != "D"):

		\Bitrix\Main\Loader::IncludeModule("sale");

		$httpClient = new HttpClient();
		$httpClient->setHeader('Content-Type', 'application/json', true);

		$saleOrder = Bitrix\Sale\Order::load($orderID);
		$orderProps = $saleOrder->getPropertyCollection();

		$PropValues = $orderProps->getArray();

		$addressStr = '';

		$assignments = array();

		$basket = $saleOrder->getBasket();
		$basketItems = $basket->getBasketItems();

		$shipment = array();
		foreach ($basket as $basketItem) {

			$shipment[] = array(
				"name" => $basketItem->GetField('NAME'),
				"price" => $basketItem->getFinalPrice(),
				"weight" => round($basketItem->getWeight()/1000, 2),
				"length" => 0,
				"value" =>  $basketItem->getFinalPrice(),
				"tax_item_price" =>  $basketItem->getPrice(),
				"tax_rate" => $basketItem->getVatRate() >= "0" ? $basketItem->getVatRate()*100 : null,
				"unit" =>  $basketItem->getQuantity(),
				"tax_unit_type" => 1
			);
		}

		//$goods = $basket->getListOfFormatText();

		//$assignments[] = array('price' => ($saleOrder->getPrice() - $saleOrder->getSumPaid()));

		$location_from = array(
			'address' =>  $data['cur_from'],
			'delivery_date' => date("Y-m-d", strtotime($data['cur_date_z'])),
			'delivery_from' => $data['cur_timez1'],
			'delivery_to' => $data['cur_timez2'],
			//'assignments' =>  $assignments
		);


		$location_to = array(
			'address' => $data['cur_to'].' '.$data['cur_todetail'],
			'delivery_date' => date("Y-m-d", strtotime($data['cur_date'])),
			'delivery_from' => $data['cur_timed1'],
			'delivery_to' => $data['cur_timed2'],
			//'assignments' => $assignments
		);
		if ($saleOrder->getSumPaid() > 0)
		{
			$location_to['prepayment'] = $saleOrder->getSumPaid();
		}

		$curOrder= array( 'locations' => array($location_from, $location_to), 'shipment' => $shipment );

		$httpClient->setHeader("Authorization","Bearer ".$access_token);
		$response = $httpClient->post('http://my.courierist.com/api/v1/order/evaluate', json_encode($curOrder));
		$resp = json_decode($response);

		if (floatval($resp->order->price) > 0) {?>
		<p>Предварительная стоимость доставки: <strong><?=floatval($resp->order->price)?>р.</strong></p><?}?>
		<?if ($resp->message){?><p style="font-weight:bold;color:red"><?=strval($resp->message)?></p><?}?>
	<?endif;
}
// Отправка заказа на доставку
elseif ($action == 'courieristOrderFormSend' && intval($_REQUEST['orderID']) > 0)
{
	global $APPLICATION;

	$orderID = intval($_REQUEST['orderID']);
	$data =  ($_REQUEST['orderData']);

	$SALE_RIGHT = $APPLICATION->GetGroupRight("sale");

	if ($SALE_RIGHT != "D"):



		\Bitrix\Main\Loader::IncludeModule("sale");

		$httpClient = new HttpClient();
		$httpClient->setHeader('Content-Type', 'application/json', true);

		$saleOrder = Bitrix\Sale\Order::load($orderID);
		$orderProps = $saleOrder->getPropertyCollection();

		$PropValues = $orderProps->getArray();

		$addressStr = '';

		$assignments_to = array();
		$assignments_from = array();

		$basket = $saleOrder->getBasket();
		$basketItems = $basket->getBasketItems();

		$shipment = array();
		foreach ($basket as $basketItem) {

			$shipment[] = array(
				"name" => $basketItem->GetField('NAME'),
				"price" => $basketItem->getFinalPrice(),
				"weight" => round($basketItem->getWeight()/1000, 2),
				"length" => 0,
				"value" =>  $basketItem->getFinalPrice(),
				"tax_item_price" =>  $basketItem->getPrice(),
				"tax_rate" => $basketItem->getVatRate() >= "0" ? $basketItem->getVatRate()*100 : null,
				"unit" =>  $basketItem->getQuantity(),
				"tax_unit_type" => 1
			);
		}

		$accountNumber = $saleOrder->getField('ACCOUNT_NUMBER');

		 $sum_to_recieve = $saleOrder->getPrice() - $saleOrder->getSumPaid();
		 $delivery_price = $saleOrder->getDeliveryPrice();
		 $assignments_to = array();
		if (!empty($data['assignment'])){
			$assignments_to[] = array('name' => $data['assignment']);
		 }

		if($delivery_price > 0){
			/*
			$assignments_to[] = array(
				"price" => $delivery_price,
				"name" => "Курьерская доставка",
				"type" => "1",
				"tax_rate" => null
			);
			if ($delivery_price > 0 )
			{
				$assignments_to[0]['tax_rate'] = null;
			}*/
			$shipment[] = array(
				"name" => 'Доставка',
				"price" => $delivery_price,
				"weight" => 0,
				"length" => 0,
				"value" =>  $delivery_price,
				"tax_item_price" =>  $delivery_price,
				"tax_rate" => 0,
				"unit" =>  1,
				"tax_unit_type" => 1
			);
		}

		 $contact_phone = $data['cur_from_phone'];
		 if (substr($contact_phone, 0, 1) == '7' || substr($contact_phone, 0, 1) == '8'){
			$contact_phone = substr($contact_phone, 1);
		 }
		 elseif (substr($contact_phone, 0, 2) == '+7') {
			$contact_phone =  substr($contact_phone, 2);
		 }
		elseif ($contact_phone == '')
		{
			$contact_phone = '9037902642';
		}

		$contactManager = array(
			'name' => $data['cur_from_fio'],
			'phone' =>  $contact_phone,
			'type' => 1,
			'note' => null
		);

		$location_from = array(
			'address' =>  $data['cur_from'],
			'address_type_id' => $data['from_type'],
			'delivery_date' => date("Y-m-d", strtotime($data['cur_date_z'])),
			'delivery_from' => $data['cur_timez1'],
			'delivery_to' => $data['cur_timez2'],
			'external_id' => $accountNumber,
			'contact' => $contactManager
		);


		$contact_cphone = $data['cur_phone'];
		 if (substr($contact_cphone, 0, 1) == '7' || substr($contact_cphone, 0, 1) == '8'){
			$contact_cphone = substr($contact_cphone, 1);
		 }
		 elseif (substr($contact_cphone, 0, 2) == '+7') {
			$contact_cphone =  substr($contact_cphone, 2);
		 }

		$contactClient = array(
			'name' => $data['cur_fio'],
			'phone' => $contact_cphone,
			'type' => 2,
			'note' => null
		);
		$location_to = array(
			'address' => $data['cur_to'].' '.$data['cur_todetail'],
			'address_type_id' => $data['to_type'],
			'delivery_date' => date("Y-m-d", strtotime($data['cur_date'])),
			'delivery_from' => $data['cur_timed1'],
			'delivery_to' => $data['cur_timed2'],
			'assignments' => $assignments_to,
			'external_id' => $accountNumber,
			'contact' => $contactClient
		);

		if ($saleOrder->getSumPaid() > 0)
		{
			$location_to['prepayment'] = $saleOrder->getSumPaid();
		}

		$curOrder[] = array( 'locations' => array($location_from, $location_to), 'shipment' => $shipment, 'comment' => $data['comment'] );


		 $query = 'SELECT * FROM `medi_courierist_orders` WHERE ORDER_ID =  "'.$orderID.'" ';

             $obCurOrder = $DB->Query($query);

		if (!($arCurOrder = $obCurOrder->Fetch())):

		$httpClient = new HttpClient();
		$httpClient->setHeader('Content-Type', 'application/json', true);
		$httpClient->setHeader("Authorization","Bearer ".$access_token);
		$response = $httpClient->post('http://my.courierist.com/api/v1/order/create', json_encode($curOrder));
		$resp = json_decode($response);

		 if (intval($resp->orders[0]->id) > 0)
		 {
			 $cur_id =  intval($resp->orders[0]->id);
			 $status =  intval($resp->orders[0]->status);
			 $price =  floatval($resp->orders[0]->price);

			 $query = 'SELECT * FROM `medi_courierist_orders` WHERE CUR_ID =  "'.$cur_id.'" ';

            $curNewOrder = $DB->Query($query);


            if (!($arCurNewOrder = $curNewOrder->Fetch())) {

				$insquery = 'INSERT `medi_courierist_orders` VALUES  (null, "'.$orderID.'", "'.$accountNumber.'", "'.date("Y-m-d H:i:s").'", "'.date("Y-m-d H:i:s").'", "'.$cur_id.'", "'.$status.'", "'.$price.'", "'.$location_to['delivery_date'].'", "'.$location_to['delivery_from'].'", "'.$location_to['delivery_to'].'", "'.htmlspecialchars($location_to['address'], ENT_QUOTES).'", "'.htmlspecialchars($data['cur_fio'], ENT_QUOTES).'", "'.$contact_cphone.'", "'.$sum_to_recieve.'")';
				$DB->Query($insquery);

				?><p style="font-weight:bold;color:green">Заявка отправлена. Статус - <?=$curStatuses[$status]?></p><?

			}
			else{
				?><p style="font-weight:bold;color:green">Заявка уже отправлена. Статус - <?=$curStatuses[$arCurNewOrder['STATUS']]?></p><?
			}
		 }
		?>


		<?if ($resp->message){?><p style="font-weight:bold;color:red"><?=strval($resp->message)?></p><?}?>

	<?
	else:
		?><p style="font-weight:bold;color:green">Заявка уже отправлена. Статус - <?=$curStatuses[$arCurOrder['STATUS']]?></p><?
	endif;

	endif;
}
// Форма  отправки заявки
elseif ($action == 'courieristOrderForm' && intval($_REQUEST['orderID']) > 0)
{
	global $APPLICATION;

	$orderID = intval($_REQUEST['orderID']);

	$SALE_RIGHT = $APPLICATION->GetGroupRight("sale");

	if ($SALE_RIGHT != "D"):

		\Bitrix\Main\Loader::IncludeModule("sale");


		$httpClient = new HttpClient();
		$httpClient->setHeader('Content-Type', 'application/json', true);
		//Получение списка LocationType
		$httpClient->setHeader("Authorization","Bearer ".$access_token);
		$response = $httpClient->get('http://my.courierist.com/api/v1/location-type','');
		$resp = json_decode($response);
		$locations_type = $resp->types;


		$saleOrder = Bitrix\Sale\Order::load($orderID);
		$orderProps = $saleOrder->getPropertyCollection();

		$paymentIds = $saleOrder->getPaymentSystemId();


		 $query = 'SELECT * FROM `medi_courierist_orders` WHERE ORDER_ID =  "'.$orderID.'" ';

		 $obCurOrder = $DB->Query($query);

	if (!($arCurOrder = $obCurOrder->Fetch())):

		$PropValues = $orderProps->getArray();

		$addressStr = '';
		foreach ($PropValues['properties'] AS $p=>$prop)
		{
			if ($prop['CODE'] == 'ADDRESS') $address = $prop['VALUE'][0];
			if ($prop['CODE'] == 'FIO') $fio = $prop['VALUE'][0];
			if ($prop['CODE'] == 'PHONE') $phone = $prop['VALUE'][0];
			if ($prop['CODE'] == 'STREET') $street = $prop['VALUE'][0];
			if ($prop['CODE'] == 'METRO') $metro = $prop['VALUE'][0];
			if ($prop['CODE'] == 'ADDRESS_INFO') $address_info = $prop['VALUE'][0];
			if ($prop['CODE'] == 'HOUSE') $house = $prop['VALUE'][0];
			if ($prop['CODE'] == 'FLAT') $flat = $prop['VALUE'][0];
			if ($prop['CODE'] == 'ENTRANCE') $entrance = $prop['VALUE'][0];
			if ($prop['CODE'] == 'FLOOR') $floor = $prop['VALUE'][0];
			if ($prop['CODE'] == 'DELIVERY_PLANNED') $DELIVERY_PLANNED = $prop['VALUE'][0];
			if ($prop['CODE'] == 'DELIVERY_FROM') $DELIVERY_FROM = $prop['VALUE'][0];
			if ($prop['CODE'] == 'DELIVERY_TO') $DELIVERY_TO = $prop['VALUE'][0];
			if ($prop['CODE'] == 'LOCATION') $locationId = $prop['VALUE'][0];
		}




		$assignments = array();

		$basket = $saleOrder->getBasket();
		$basketItems = $basket->getBasketItems();

		$shipment = array();
		foreach ($basket as $basketItem) {

			$shipment[] = array(
				"name" => $basketItem->GetField('NAME'),
				"price" => $basketItem->getFinalPrice(),
				"weight" => round($basketItem->getWeight()/1000, 2),
				"length" => 0,
				"value" =>  $basketItem->getFinalPrice(),
				"tax_item_price" =>  $basketItem->getPrice(),
				"tax_rate" => $basketItem->getVatRate() >= "0" ? $basketItem->getVatRate()*100 : null,
				"unit" =>  $basketItem->getQuantity(),
				"tax_unit_type" => 1
			);
		}

		$goods = $basket->getListOfFormatText();

		//$assignments[] = array('price' => $basket->getPrice());

		$location_from = array(
			'address' => 'Москва, Бережковская наб., д.20 стр.64',
			'delivery_date' => date("Y-m-d", strtotime($DELIVERY_PLANNED)),
			'delivery_from' => "11:00",
			'delivery_to' => "14:00",
			//'assignments' =>  $assignments
		);

		//Получить город
		$city_to = 'Москва';
		$arLocation = CSaleLocation::GetByID($locationId);

		if($arLocation['CITY_NAME'] != '') {
			$city_to =  $arLocation['CITY_NAME'];
		}


		if(empty($street))
		{
			$locstr_to = $city_to.', '.$address;
		}
		else
		{
			$locstr_to = $city_to.', '.trim($street).', '.$house.($flat != '' ? ', кв. '.$flat : '');
		}


		$location_to = array(
		    'address' => $locstr_to,
			'delivery_date' => date("Y-m-d", strtotime($DELIVERY_PLANNED)),
			'delivery_from' => "11:00",
			'delivery_to' => "22:00",
			//'assignments' => $assignments
		);

		if ($saleOrder->getSumPaid() > 0)
		{
			$location_to['prepayment'] = $saleOrder->getSumPaid();
		}

		$curOrder= array( 'locations' => array($location_from, $location_to), 'shipment' => $shipment );

		$httpClient->setHeader("Authorization","Bearer ".$access_token);
		$response = $httpClient->post('http://my.courierist.com/api/v1/order/evaluate', json_encode($curOrder));
		$resp = json_decode($response);
		//print_r($resp);
		//print_r($resp->order->price);


		 ?>


		<table style="width:600px">
		<form name="sendOrder" id="curOrderForm" method="post">
		<?$accountNumber = $saleOrder->getField('ACCOUNT_NUMBER');?>
		<input type="hidden" id="cur_orderid" name="cur_orderid" value="<?=$orderID?>"/>
		<input type="hidden" id="cur_accountNumber" name="cur_accountNumber" value="<?=$accountNumber?>"/>
		<tr class="heading">
		<td colspan="2">Состав заказа №<?=$accountNumber;?></td>
		</tr>
		<tr>
		<td colspan="2">
		<ol>
		<?foreach($goods AS $good){?>
		<li><?=preg_replace("/(\[.+\])/U","", $good);?></li>
		<?}?>
		</ol>
		</td>
		</tr>
		<tr class="heading">
		<td colspan="2">Адреса</td>
		</tr>
		<tr>
		<td><label for="cur_from" style="font-weight:bold;">Забрать:</label></td>
		<td><select name="cur_from_type" id="cur_from_type"><?foreach($locations_type AS $ltype){?><option value="<?=$ltype->id?>" <?=($ltype->id == 2 ? 'selected = "true"' : "")?>><?=$ltype->name?></option><?}?></select><input type="text" name="from" id="cur_from" value="г. Москва, НАО, поселение Сосенское, деревня Николо-Хованское, дом 1006, строение 1." size="35"/></td>
		</tr>
		<tr>
		<td><label for="cur_date_z" style="font-weight:bold;">Дата и время забора:</label></td>
		<td><input type="text" name="datez" id="cur_date_z" value="<?=$DELIVERY_PLANNED; ?>" size="7"/>&nbsp;&nbsp;с&nbsp;<input type="text" name="timez" id="cur_timez1" value="11:00" size="2"/>&nbsp;до&nbsp;<input type="text" name="timez2" id="cur_timez2" value="14:00" size="2"/></td>
		</tr>
		<tr>
		<td><label for="cur_to" style="font-weight:bold;">Доставить:</label></td>
		<td><select name="cur_to_type" id="cur_to_type"><?foreach($locations_type AS $ltype){?><option value="<?=$ltype->id?>"><?=$ltype->name?></option><?}?></select><input type="text" name="to" id="cur_to" value="<?=htmlspecialchars($locstr_to, ENT_QUOTES); ?>" size="35"/></td>
		</tr>
		<tr>
		<td><label for="cur_todetail">Дополнительно:</label></td>
		<td><input type="text" name="todetail" id="cur_todetail" value="<?=htmlspecialchars(($metro ? 'м. '.$metro.', ' : '').(trim($address_info) != '' ? $address_info : ($entrance ? 'подъезд '.$entrance.', ' : '').($floor ? 'этаж '.$floor : '')).'', ENT_QUOTES); ?>" size="35"/></td>
		</tr>
		<tr>
		<td><label for="cur_date" style="font-weight:bold;">Дата и время доставки:</label></td>
		<td><input type="text" name="dated" id="cur_date" value="<?=$DELIVERY_PLANNED; ?>" size="7"/>&nbsp;&nbsp;с&nbsp;<input type="text" name="timed" id="cur_timed1" value="<?=($DELIVERY_FROM ? $DELIVERY_FROM : '11:00')?>" size="2"/>&nbsp;до&nbsp;<input type="text" name="timed" id="cur_timed2" value="<?=($DELIVERY_TO ? $DELIVERY_TO : '22:00')?>" size="2"/></td>
		</tr>
		<tr>
		<td><label for="cur_comment" style="font-weight:bold;">Комментарий:</label></td>
		<td><textarea  name="comment" id="cur_comment" rows="5" cols="36" value="">БЕЗ ПРИМЕРКИ! Звонить за час до доставки! <?if ($saleOrder->getSumPaid() <= 0){
			echo ($paymentIds[0] == 1 ? 'Оплата наличными при получении.' : ($paymentIds[0] == 11 ? 'Оплата картой при получении.' : ''));
		}?> </textarea></td>
		</tr>
		<tr>
		<td><label for="cur_assignment" style="font-weight:bold;">Доп.поручение на адресе:</label></td>
		<td><textarea  name="assignment" id="cur_assignment" rows="5" cols="36" value=""></textarea></td>
		</tr>
		<tr class="heading">
		<td colspan="2">Отправитель</td>
		</tr>
		<tr>
		<td><label for="cur_from_fio" style="font-weight:bold;">Имя:</label></td>
		<td><input type="text" name="from_fio" id="cur_from_fio" value="<?=$USER->GetFullName()?>" size="35"/></td>
		</tr>
		<tr>
		<td><label for="cur_from_phone" style="font-weight:bold;">Телефон:</label></td>
		<td><input type="text" name="from_phone" id="cur_from_phone" value="9037902642" size="35"/></td>
		</tr>
		<tr class="heading">
		<td colspan="2">Получатель</td>
		</tr>
		<tr>
		<td><label for="cur_fio" style="font-weight:bold;">Имя:</label></td>
		<td><input type="text" name="fio" id="cur_fio" value="<?=$fio?>" size="35"/></td>
		</tr>
		<tr>
		<td><label for="cur_phone" style="font-weight:bold;">Телефон:</label></td>
		<td><input type="text" name="phone" id="cur_phone" value="<?=$phone?>" size="35"/></td>
		</tr>
		<tr>
		<tr class="heading">
		<td colspan="2">Оплата</td>
		</tr>
		<tr>
			<td colspan="2">
			<div id="cur_message">
			 <?
			if (floatval($resp->order->price) > 0) {?><p>Предварительная стоимость услуги доставки: <strong><?=floatval($resp->order->price)?>р.</strong></p><?}?>

			<?if ($resp->message){?><p style="font-weight:bold;color:red"><?=strval($resp->message)?></p><?}?>
			</div>
			<div>
			Стоимость заказа - <b><?=$saleOrder->getPrice();?> р.</b>, уже оплачено - <b><?=$saleOrder->getSumPaid();?> р.</b>  Стоимость доставки: <b><?=$saleOrder->getDeliveryPrice();?> р.</b>
			</div>
			</td>
		</tr>
		<tr>
		<td>К оплате</td>
		<td><input type="text" value="<?=$saleOrder->getPrice() - $saleOrder->getSumPaid();?>" id ="cur_sum" name="sum" size="35"/></td>
		</tr>

		<tr>
		<td>Объявленная ценность</td>
		<td><input type="text" value="<?=$saleOrder->getPrice();?>" id ="cur_sumvalue" name="sumvalue" size="35"/></td>
		</tr>
		<tr class="heading">
		<td colspan="2"></td>
		</tr>
		<tr>
		<td><input type="button" name="recount" id="recount" value="Пересчитать"/></td>
		<td style="text-align:center"><input type="button" name="send" id="cur_send" class="adm-btn adm-btn-green" value="Отправить заказ"/></td>
		</tr>
		</form>
		</table>
		<script>
		$(document).ready(function() {
			// Пересчет стоимости доставки
			$("#recount").on("click", function(){
				$recount = new Object();
				$orderId = $("#cur_orderid").val();
				$recount.cur_from = $("#cur_from").val();
				$recount.cur_to = $("#cur_to").val();
				$recount.cur_date_z = $("#cur_date_z").val();
				$recount.cur_timez1 = $("#cur_timez1").val();
				$recount.cur_timez2 = $("#cur_timez2").val();
				$recount.cur_todetail = $("#cur_todetail").val();
				$recount.cur_date = $("#cur_date").val();
				$recount.cur_timed1 = $("#cur_timed1").val();
				$recount.cur_timed2 = $("#cur_timed2").val();
				$recount.cur_sum = $("#cur_sum").val();

				$.ajax({
					url: '/local/ajax_cp.php',
					data: {
						action: "courieristOrderFormRecount",
						orderID: $orderId,
						recountData: $recount
					},
					method: 'POST',
					beforeSend: function() {
						$("#cur_message").html('загрузка...');
					},
					success: function(data){
						$("#cur_message").html(data);
					},
					failure: function(){
					}
				});
			});

			// Отправка заказа в Курьерист API
			$("#cur_send").on("click", function(){
				$curorder = new Object();
				$orderId = $("#cur_orderid").val();
				$curorder.cur_from = $("#cur_from").val();
				$curorder.cur_to = $("#cur_to").val();
				$curorder.cur_date_z = $("#cur_date_z").val();
				$curorder.cur_timez1 = $("#cur_timez1").val();
				$curorder.cur_timez2 = $("#cur_timez2").val();
				$curorder.cur_todetail = $("#cur_todetail").val();
				$curorder.cur_date = $("#cur_date").val();
				$curorder.cur_timed1 = $("#cur_timed1").val();
				$curorder.cur_timed2 = $("#cur_timed2").val();
				$curorder.cur_sum = $("#cur_sum").val();
				$curorder.comment = $("#cur_comment").val();
				$curorder.assignment = $("#cur_assignment").val();


				$curorder.from_type = $("#cur_from_type").val();
				$curorder.to_type = $("#cur_to_type").val();

				$curorder.cur_fio = $("#cur_fio").val();
				$curorder.cur_phone = $("#cur_phone").val();
				$curorder.cur_from_fio = $("#cur_from_fio").val();
				$curorder.cur_from_phone = $("#cur_from_phone").val();

				$.ajax({
					url: '/local/ajax_cp.php',
					data: {
						action: "courieristOrderFormSend",
						orderID: $orderId,
						orderData: $curorder
					},
					method: 'POST',
					beforeSend: function() {
						$("#cur_message").html('загрузка...');
					},
					success: function(data){
						$("#cur_message").html(data);
					},
					failure: function(){
					}
				});
			});
		});
		</script>
		<?

	else:
		?><p style="font-weight:bold;color:green">Заявка уже отправлена. Статус - <?=$curStatuses[$arCurOrder['STATUS']]?></p><?
	endif;

	endif;
}
die;
