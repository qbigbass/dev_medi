<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

use Bitrix\Main;

$key = '61978df889ea57c51d490c09a24f4441';

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


    CModule::IncludeModule('sale');

if (isset($_REQUEST['key']) && strval($_REQUEST['key']) == $key)
{

    if ($_REQUEST["action"] == 'show')
    {
        if (isset($_REQUEST['day']))
        {
            $date_from = $_REQUEST['day'].' 00:00:00';
            $date_to = $_REQUEST['day'].' 23:59:59';
        }
        else {

            $date_from = date("Y-m-d", time()-86400).' 00:00:00';
            $date_to = date("Y-m-d", time()-86400).' 23:59:59';

        }

        if (isset($_REQUEST['from']))
        {
            $date_from = $_REQUEST['from'].' 00:00:00';
        }
        if (isset($_REQUEST['to']))
        {
            $date_to = $_REQUEST['to'].' 23:59:59';
        }


        $status = 'succeeded';
        if (isset($_REQUEST['status']) && in_array($_REQUEST['status'], ['canceled','pending', 'all', 'waiti', 'refun']))
        {
            $status = strval($_REQUEST['status']);
        }
        if ($status == 'refun'){


            $date_from = $DB->forSql($date_from);
            $date_to = $DB->forSql($date_to);

            $query =  "SELECT id, payment_id,  amount, status, description, `date` FROM `vampirus_yandexkassa_refund` WHERE `date` BETWEEN '$date_from' AND '$date_to' ";
//echo $query;

            //

            $rsPayments = $DB->Query($query);

            $arResult = [];

           $ii = 0;
            while ($arPayment = $rsPayments->Fetch()) {
                if ($_REQUEST['extra'])
                {
                    $resPayment = [
                        'id' => $arPayment['id'],
                        'payment_id' => $arPayment['payment_id'],
                        'amount' => $arPayment['amount'],
                        'status' =>$arPayment['status']
                    ];


                    $arResult[$ii] = $resPayment;
                }
                else{

                    $arResult[$ii] = ['id'=>$arPayment['id']];
                }
                $ii++;
            }

        }
        else {


            $query_add = '';
            if ($status !=  'all')
            {
                $query_add = 'AND status = "'.$status.'"';
            }
            $date_from = $DB->forSql($date_from);
            $date_to = $DB->forSql($date_to);

            $query =  "SELECT id, order_id, amount, status FROM `vampirus_yandexkassa_new` WHERE `date` BETWEEN '$date_from' AND '$date_to'  ".$query_add;

            //

            $rsPayments = $DB->Query($query);

            $arResult = [];

           $ii = 0;
            while ($arPayment = $rsPayments->Fetch()) {
                if ($_REQUEST['extra'])
                {
                    $arResult[$ii] = $arPayment;

                    $arOrder = CSaleOrder::GetByID($arPayment['order_id']);

                    $arResult[$ii]['ACCOUNT_NUMBER'] = $arOrder['ACCOUNT_NUMBER'];

                }
                else{

                    $arResult[$ii] = ['id'=>$arPayment['id']];
                }
                $ii++;
            }
        }

        header("Content-type: application/json; charset=utf-8");
        if (!empty($arResult))
        {
            echo json_encode($arResult);
        }
        else {
            echo json_encode(['error'=> 'not found']);
        }

    }
    else {
        echo json_encode(['error'=> 'invalid request']);
    }
}
else {
    header('HTTP/1.0 403 Forbidden');die;
}
