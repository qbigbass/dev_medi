<?php
namespace Measoft\Courier\Controller;
use Bitrix\Main\Engine\Contract\Controllerable,
    Bitrix\Main\Engine\ActionFilter,
    Bitrix\Main\Engine\Controller,
    Bitrix\Main\Application,
    Bitrix\Sale;

class Ajax extends Controller
{
    private static $orderStatusTable = 'measoft_order_status';
    private static $paySystemTable = 'measoft_pay_system';


    public function configureActions()
    {
        return [
            'checkAuth' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod(
                        array(ActionFilter\HttpMethod::METHOD_POST)
                    ),
                    new ActionFilter\Csrf(),
                    new ActionFilter\CheckModulePermissions(),
                ],
                'postfilters' => []
            ],
            'updateOrderStatuses' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod(
                        array(ActionFilter\HttpMethod::METHOD_POST)
                    ),
                    new ActionFilter\Csrf(),
                    new ActionFilter\CheckModulePermissions(),
                ],
                'postfilters' => []
            ],
            'updatePaySystem' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod(
                        array(ActionFilter\HttpMethod::METHOD_POST)
                    ),
                    new ActionFilter\Csrf(),
                    new ActionFilter\CheckModulePermissions(),
                ],
                'postfilters' => []
            ],
            'checkStatus' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod(
                        array(ActionFilter\HttpMethod::METHOD_POST)
                    ),
                    new ActionFilter\Csrf(),
                    new ActionFilter\CheckGetStatusPermissions(),
                ],
                'postfilters' => []
            ],

        ];
    }


    public static function checkAuthAction($login, $password, $code)
    {
        $measoft = new \Measoft($login, $password, $code);
        $success = $measoft->checkLogin();
        return [
            'success' => $success,
        ];
    }

    public static function updateOrderStatusesAction($params)
    {
        $success = true;
        $arrayParams = array();
        parse_str($params, $arrayParams);

        if ( isset($arrayParams["status"]) )
        {
            $connection = Application::getConnection();
            $connection->truncateTable(self::$orderStatusTable);
            $vals = [];
            foreach($arrayParams["status"] as $measoftCode => $bitrixCode) {
                if ($bitrixCode) {
                    $data[$measoftCode] = $bitrixCode;
                    $vals[] = array(
                        'MEASOFT_STATUS_CODE' => $measoftCode,
                        'BITRIX_STATUS_ID' => $bitrixCode
                    );
                }
            }

            if(count($vals) > 0) {
                $success = $connection->addMulti(self::$orderStatusTable, $vals);
            }
        }
        return [
            'success' => $success,
        ];
    }

    public static function updatePaySystemAction($params)
    {
        $success = true;
        $arrayParams = array();
        parse_str($params, $arrayParams);

        $vals = [];

        foreach( $arrayParams["payCardSystem"] as $systemId )
        {
            $vals[ $systemId ] = [ "PAYSYSTEM_ID" => $systemId, "CASH" => 0, "CARD" => 1 ];
        }

        foreach( $arrayParams["payCashSystem"] as $systemId )
        {
            if ($vals[ $systemId ])
            {
                $vals[ $systemId ][ "CASH" ] = 1;
            } else
            {
                $vals[ $systemId ] = [ "PAYSYSTEM_ID" => $systemId, "CASH" => 1, "CARD" => 0 ];
            }

        }

        $connection = Application::getConnection();
        $connection->truncateTable(self::$paySystemTable);

        if(count($vals) > 0) {
            $success = $connection->addMulti(self::$paySystemTable, $vals);
        }


        return [
            'success' => $success,
        ];
    }

    public static function checkStatusAction($orderId)
    {
        $success = false;
        $message = '';
        $cancelError = '';
        $FIsCanceled = false;

        $order = Sale\Order::load($orderId);

        $deliveryId = $order->getField("DELIVERY_ID");

        $result = Sale\Delivery\Services\Table::getList(array(
            'filter' => array('ID'=> $deliveryId),
            'select' => array('CODE')
        ));

        if($delivery = $result->fetch()){

            $success = true;

            if ($delivery['CODE'] == 'courier:simple' || $delivery['CODE'] == 'courier:pickup') {
                $numberType = \MeasoftEvents::configValueEx('ORDER_NUMBER', $deliveryId);

                $prefix = \MeasoftEvents::configValueEx('ORDER_PREFIX', $deliveryId);

                $orderId = !$numberType ? $orderId : $order->getField('ACCOUNT_NUMBER');

                $measoft = new \Measoft(\MeasoftEvents::configValueEx('LOGIN', $deliveryId), \MeasoftEvents::configValueEx('PASSWORD', $deliveryId), \MeasoftEvents::configValueEx('CODE', $deliveryId));

                if ($status = $measoft->statusRequest($prefix.$orderId))
                {

                    $message = $status;
                    if (\MeasoftEvents::isCp1251Site())
                    {
                        $message = $GLOBALS['APPLICATION']->ConvertCharset($message, 'UTF-8', SITE_CHARSET);
                    }

                } else {

                    $message = \MeasoftEvents::getMessageLang("MEASOFT_ORDER_WASNT_SENT_TO_COURIER_SERVICE");

                }

                $FIsCanceled = $order->isCanceled();

                $propertyCollection = $order->getPropertyCollection();
                foreach ($propertyCollection as $property)
                {

                    if($property->getField('CODE') == 'MEASOFT_ORDER_ERROR' && $property->getField('VALUE')) {
                        $cancelError = $property->getField('VALUE');
                    }

                }
            }

        }

        return [
            'success' => $success,
            'message' => $message,
            'cancelError' => $cancelError,
            'IsCanceled' => $FIsCanceled
        ];
    }


}