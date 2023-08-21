<?

use Bitrix\Main,
    Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Sale;

use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\Parser;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;

use Bitrix\Main\Context,
    Bitrix\Currency\CurrencyManager,
    Bitrix\Sale\Order,
    Bitrix\Sale\Basket,
    Bitrix\Sale\Delivery,
    Bitrix\Sale\PaySystem;

Loader::includeModule('sale');
Loader::includeModule('catalog');

$eventManager = Main\EventManager::getInstance();

$service_user = 'BitrixUser';
$service_pass = 'Da1Fa5sa';


function GetModifiedOrders()
{
    global $service_user, $service_pass;
    
    $url = 'https://1c.mediexp.ru/OnlineShop/hs/bitrix_ex/GetModifiedOrders';
    $httpClient = new HttpClient();
    
    $httpClient->setHeader('Content-type', 'application/json; charset=UTF-8');
    $httpClient->setAuthorization($service_user, $service_pass);

//$httpClient->post($url, json_encode($order, JSON_UNESCAPED_UNICODE));
    $httpClient->get($url);
    
    $status = $httpClient->getStatus();
    $result = new Result();
    
    if ($status != '200') {
        $result->addError(new Error("HTTP Status error - " . $status));
    }
    
    
    $data = $httpClient->getResult();
    
    if (isset($data['error'])) {
        echo "error";
        medi2log($data['error']);
        
        $result->addError(new Error($data['error']));
    } else {
        $response = json_decode($data);
        if (!empty($response->Lines)) {
            foreach ($response->Lines as $line) {
                $messageNum = $line->MessageNumber;
                $orderInfo = parseOrderData($line->Order);
                
                if (!empty($orderInfo)) {
                    if ($orderInfo['ACCOUNT_NUMBER'] != "0") {
                        // updateOrder
                        print_r("updateOrder");
                    } else {
                        $res = createOrderFrom1c($orderInfo);
                        
                        if (intval($res) > 0) {
                            print_r(["send", $res, $messageNum]);
                            //sendAccountNumber($res, $messageNum, $orderInfo['OrderUID']);
                        }
                    }
                }
                
                break;
            }
        }
        //
    }
}

function sendAccountNumber($orderId, $number, $orderUid)
{
    global $user, $pass;
    
    $url = 'https://1c.mediexp.ru/OnlineShop/hs/bitrix_ex/TransferOrderStatuses';
    $httpClient = new HttpClient();
    
    $httpClient->setHeader('Content-type', 'application/json; charset=UTF-8');
    $httpClient->setAuthorization($user, $pass);
    print_r(['MessageNumber' => $number, 'OrderUID' => $orderUid, 'SiteOrderNumber' => $orderId]);
    $httpClient->post($url, json_encode(['MessageNumber' => $number, 'OrderUID' => $orderUid, 'SiteOrderNumber' => $orderId], JSON_UNESCAPED_UNICODE));
    //$httpClient->get($url);
    
    $status = $httpClient->getStatus();
    $result = new Result();
    
    if ($status != '200') {
        $result->addError(new Error("HTTP Status error - " . $status));
    }
    
    
    $data = $httpClient->getResult();
    return $data;
}

function parseOrderData($order)
{
    if (empty($order)) {
        medi2log("ParseOrderData - order empty");
        return false;
    }
    
    $orderData['OrderNumber1c'] = $order->OrderNumber;
    $orderData['ACCOUNT_NUMBER'] = $order->SiteOrderNumber;
    $orderData['CreatedOnSite'] = $order->CreatedOnSite;
    $orderData['OrderDate'] = strtotime($order->OrderDate);
    $orderData['OrderUID'] = $order->OrderUID;
    $orderData['CLIENT_NAME'] = $order->Client->Name;
    $orderData['CLIENT_GENDER'] = $order->Client->Gender;
    $orderData['CLIENT_BIRTHDAY'] = $order->Client->DateOfBirth;
    $orderData['CLIENT_LOYMAX_ID'] = $order->Client->LoymaxID;
    $orderData['CLIENT_PHONE'] = '7' . $order->PhoneNumberAtTheTimeOfOrder;
    
    $orderData['SALON'] = $order->Salon;
    
    $orderData['RECIPE'] = $order->Recipe;
    $orderData['DOCTOR_ID'] = $order->DoctorID;
    
    $orderData['COMMENT'] = $order->Comment;
    $orderData['SOURCE'] = $order->ClientSources;
    $orderData['TASKS'] = $order->Job;
    $orderData['ADDRESS'] = $order->DeliveryAddress;
    
    $orderData['LoymaxPurchaseID'] = $order->LoymaxPurchaseID;
    
    $orderData['SELLER'] = $order->Seller->Name;
    $orderData['PRODUCTS'] = $order->ProductLines;
    
    return $orderData;
}

function createOrderFrom1c($orderData)
{
    global $DB;
    
    if ($orderData['OrderUID'] && $OrderID = checkOrderExistsByGuid($orderData['OrderUID'])) {
        
        return ['error' => 'Order exists', 'OrderID' => $OrderID]; // ИЛИ ОБНОВЛЯТЬ ЗАКАЗ
    } else {
        
        // найти клиента
        $parsedPhone = Parser::getInstance()->parse($orderData['CLIENT_PHONE']);
        $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));
        
        $login = '+' . $phone;
        $find_user = 0;
        // Ищем в битрикс
        $arFilter = [
            [
                "LOGIC" => "OR",
                ['LOGIN' => $login],
                ['PERSONAL_MOBILE' => $login],
                ['PERSONAL_PHONE' => $login],
                // ['PHONE_NUMBER' => $login]
            ]
        ];
        $res = Bitrix\Main\UserTable::getList(array(
            "select" => array("ID", "NAME"),
            "filter" => $arFilter,
        ));
        
        if ($bx_user = $res->fetch()) {
            $find_user = 1;
            $fUser = $bx_user;
        } else {
            $query = 'SELECT USER_ID FROM `b_user_phone_auth` WHERE PHONE_NUMBER = "' . $login . '" ';
            $obRes = $DB->Query($query);
            
            if ($arPhoneExists = $obRes->Fetch()) {
                $find_user = 1;
                $bx_user['ID'] = $arPhoneExists['USER_ID'];
                $fUser = $bx_user;
            }
        }
        if ($find_user == 0) {
            
            $auser = new CUser;
            $client_name = explode(" ", $orderData['CLIENT_NAME']);
            $pass = substr(md5(rand(10)), 0, 7);
            $new_user = [
                'LOGIN' => $login,
                'NAME' => $client_name[1],
                'LAST_NAME' => $client_name[0],
                'SECOND_NAME' => $client_name[2],
                //'EMAIL' => $orderData['CLIENT_EMAIL'],
                'PERSONAL_PHONE' => $login,
                'MOBILE_PHONE' => $login,
                'PHONE_NUMBER' => $login,
                'PASSWORD' => $pass,
                'CONFIRM_PASSWORD' => $pass,
                'ACTIVE' => 'Y',
                'XML_ID' => $orderData['CLIENT_LOYMAX_ID']
            ];
            $bx_user['ID'] = $auser->Add($new_user, true);
            $fUser = $bx_user;
        }
        
        $siteId = 's1';
        $currencyCode = CurrencyManager::getBaseCurrency();
        if ($fUser['ID'] > 0) {
            // Создаёт новый заказ
            $order = Order::create($siteId, $fUser['ID']);
            $order->setPersonTypeId(1);
            $order->setField('CURRENCY', $currencyCode);


// Создаём корзину с одним товаром
            $basket = Basket::create($siteId);
            
            foreach ($orderData['PRODUCTS'] as $cc => $arPRODUCT) {
                $productId = "ln" . $arPRODUCT->LineNumber;
                echo "<pre>";
                print_r($arPRODUCT);
                if ($arPRODUCT->Product->Characteristic->UID) {
                    $obElement = CIBlockElement::GetList([], ['IBLOCK_ID' => 19, 'XML_ID' => $arPRODUCT->Product->Nomenclature->UID . '#' . $arPRODUCT->Product->Characteristic->UID], false, false, ['ID']);
                    if ($arElement = $obElement->GetNext()) {
                        // print_r($arElement);
                        $productId = $arElement['ID'];
                    }
                } elseif ($arPRODUCT->Product->Nomenclature->UID) {
                    
                    if (strpos($arPRODUCT->Product->Nomenclature->Name, 'Доставка') === false) {
                        
                        $obElement = CIBlockElement::GetList([], ['IBLOCK_ID' => 17, 'XML_ID' => $arPRODUCT->Product->Nomenclature->UID], false, false, ['ID']);
                        if ($arElement = $obElement->GetNext()) {
                            //print_r($arElement);
                            $productId = $arElement['ID'];
                        } else {
                            $productId = "ln" . $arPRODUCT->LineNumber;
                            $arProduct = [
                                'NAME' => $arPRODUCT->Product->Nomenclature->Name,
                                'CUSTOM_PRICE' => "Y",
                                'PRICE' => $arPRODUCT->Price,
                                'BASE_PRICE' => $arPRODUCT->WithoutDiscountSum / $arPRODUCT->Quantity,
                                'DISCOUNT_PRICE' => $arPRODUCT->Price,
                                'DISCOUNT_NAME' => $arPRODUCT->LoymaxDiscount->Name,
                                'NOTES' => $arPRODUCT->DiscountJustification->Name,
                                'DISCOUNT_VALUE' => $arPRODUCT->AutomaticDiscountSum->Name / $arPRODUCT->Quantity
                            ];
                        }
                    }
                } else {
                    $productId = "ln" . $arPRODUCT->LineNumber;
                    $arProduct = [
                        'CUSTOM_PRICE' => "Y",
                        'PRICE' => $arPRODUCT->Price,
                        'BASE_PRICE' => $arPRODUCT->WithoutDiscountSum / $arPRODUCT->Quantity,
                        'DISCOUNT_PRICE' => $arPRODUCT->Price,
                        'DISCOUNT_NAME' => $arPRODUCT->LoymaxDiscount->Name,
                        'NOTES' => $arPRODUCT->DiscountJustification->Name,
                        'DISCOUNT_VALUE' => $arPRODUCT->AutomaticDiscountSum->Name / $arPRODUCT->Quantity
                    ];
                }
                
                $item = $basket->createItem('catalog', $productId);
                $item->setFields(array(
                    'QUANTITY' => $arPRODUCT->Quantity,
                    'CURRENCY' => $currencyCode,
                    'PRICE' => $arPRODUCT->Price,
                    'LID' => $siteId,
                    'PRODUCT_PROVIDER_CLASS' => '\CCatalogProductProvider',
                
                
                ));
            }
            
            $order->setBasket($basket);

// Создаём одну отгрузку и устанавливаем способ доставки - "Без доставки" (он служебный)
            $shipmentCollection = $order->getShipmentCollection();
            $shipment = $shipmentCollection->createItem();
            $service = Delivery\Services\Manager::getById(Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId());
            $shipment->setFields(array(
                'DELIVERY_ID' => $service['ID'],
                'DELIVERY_NAME' => $service['NAME'],
            ));
            $shipmentItemCollection = $shipment->getShipmentItemCollection();
            $shipmentItem = $shipmentItemCollection->createItem($item);
            $shipmentItem->setQuantity($item->getQuantity());

// Создаём оплату со способом #1
            $paymentCollection = $order->getPaymentCollection();
            $payment = $paymentCollection->createItem();
            $paySystemService = PaySystem\Manager::getObjectById(1);
            $payment->setFields(array(
                'PAY_SYSTEM_ID' => $paySystemService->getField("PAY_SYSTEM_ID"),
                'PAY_SYSTEM_NAME' => $paySystemService->getField("NAME"),
            ));

// Устанавливаем свойства
            $propertyCollection = $order->getPropertyCollection();
            $phoneProp = $propertyCollection->getPhone();
            $phoneProp->setValue($phone);
            $nameProp = $propertyCollection->getPayerName();
            $nameProp->setValue($orderData['CLIENT_NAME']);
            
            
            $order->setField('STATUS_ID', 'A');
            
            
            $addrPropValue = $propertyCollection->getItemByOrderPropertyId(7);
            $addrPropValue->setValue($orderData['ADDRESS']);
            
            // Менеджер
            $mangPropValue = $propertyCollection->getItemByOrderPropertyId(9);
            $mangPropValue->setValue($orderData['SELLER']);
            
            // Источник
            $sourcePropValue = $propertyCollection->getItemByOrderPropertyId(11);
            $source = 'ORDER_REF_INET';
            switch ($orderData['SOURCE']->Name) {
                case 'Интернет':
                    $source = 'ORDER_REF_INET';
                    break;
                case 'Контактный центр (Интернет)':
                    $source = 'ORDER_REF_INTERNET';
                    break;
                case 'Контактный центр (Розничная сеть)':
                    $source = 'ORDER_REF_RETAIL';
                    break;
                case 'КЦ (ГПО)':
                    $source = 'ORDER_REF_CC_GPO';
                    break;
                case 'КЦ (Ростов-на-Дону)':
                    $source = 'ORDER_REF_RND';
                    break;
                case 'Предзаказ':
                    $source = 'ORDER_REF_PO';
                    break;
                case 'Быстрый заказ':
                    $source = 'ORDER_REF_FO';
                    break;
                case 'Внутренний источник':
                    $source = 'ORDER_REF_CORPORATE';
                    break;
                case 'Салоны РС':
                    $source = 'ORDER_REF_SALONS';
                    break;
                case 'E-mail':
                    $source = 'ORDER_REF_EMAIL';
                    break;
                case 'Чат сайта':
                    $source = 'ORDER_REF_ONLINECHAT';
                    break;
                case 'ОМП':
                    $source = 'ORDER_REF_TP';
                    break;
                case 'Яндекс.Маркет':
                    $source = 'ORDER_REF_YM';
                    break;
                
            }
            $sourcePropValue->setValue($source);
            
            if ($orderData['OrderUID']) {
                $order->setField('XML_ID', $orderData['OrderUID']);
                $order->setField('ID_1C', $orderData['OrderUID']);
            }
            if ($orderData['COMMENT']) {
                $order->setField('COMMENTS', $orderData['COMMENT']); // Устанавливаем поля комментария покупателя
            }
            
            print_r($order->getAvailableFields());
// Сохраняем
            $order->doFinalAction(true);
            $result = $order->save();
            $orderId = $order->getField('ACCOUNT_NUMBER');
            
            return $orderId;
        } else {
            medi2log(['error' => 'Ошибка выбора пользователя ']);
        }
    }
    
    //print_r($OrderID);
    
    /*
     * Свойства заказа в битриксе
    1 FIO
    2 EMAIL
    3 PHONE
    4 ZIP
    5 CITY
    6 LOCATION
    7 ADDRESS
    8 METRO
    9 MANAGER
    10 CREATOR  Автор заказа
    11 ORDER_REF Источник заказа
    12 GPO
    13 GPO_SPECIALIST
    14 DC_NUMBER
    15 RECIPE_SELL Есть рекомендация?
    16 R_NUMBER Номер рецепта
    17 FIO_LPU
    18 DISCOUNT_TYPE
    19 RESERV_NUMBER
    20 ADDRESS_INFO

    23 DELIVERY_PLANNED
    24 DELIVERY_FROM
    25 DELIVERY_TO
    27 DELIVERY  Курьерская служба

    */
}

function SendNewAccountNumber($ACCOUNT_NUMBER)
{
    if (empty($ACCOUNT_NUMBER)) {
        medi2log("SendNewAccountNumber - ACCOUNT_NUMBER empty");
        return;
    }
    
    
}

function checkOrderExistsByGuid($guid)
{
    $dbRes = Sale\Order::getList([
        'select' => [
            "ID",
            "PROPERTY_VAL.VALUE"
        ],
        'filter' => [
            '=PROPERTY_VAL.CODE' => 'GUID',
            '=PROPERTY_VAL.VALUE' => $guid
        ],
        'runtime' => [
            new \Bitrix\Main\Entity\ReferenceField(
                'PROPERTY_VAL',
                '\Bitrix\sale\Internals\OrderPropsValueTable',
                ["=this.ID" => "ref.ORDER_ID"],
                ["join_type" => "left"]
            )
        ]
    ]);
    
    if ($order = $dbRes->fetch()) {
        return $order['ID'];
    } else {
        return false;
    }
}

function getPreRecieptData(array $data)
{
    
    if (empty($data)) {
        medi2log(['empty prereciept data'], 'getprecheck');
        return false;
    }
    
}

function sendPreRecieptData(array $data)
{
    global $service_user, $service_pass;
    
    if (empty($data)) {
        medi2log(['empty prereciept data'], 'precheck');
        return false;
    }
    
    $url = 'https://1c.mediexp.ru/OnlineShop/hs/bitrix_ex/CreateShoppingCart';
    $httpClient = new HttpClient();
    
    $httpClient->setHeader('Content-type', 'application/json; charset=UTF-8');
    $httpClient->setAuthorization($service_user, $service_pass);
    
    function urlencode_str($value, $key)
    {
        if (is_string($value)) {
            $value = urlencode($value);
        }
    }
    
    //array_walk_recursive($data, 'urlencode_str');
    
    $httpClient->post($url, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    
    $status = $httpClient->getStatus();
    $result = new Result();
    
    if ($status != '200') {
        $result->addError(new Error("HTTP Status error - " . $status));
        
        medi2log("HTTP Status error - " . $status);
        $result = [
            'status' => 'error',
            'message' => "HTTP Status error - " . $status
        ];
    }
    
    
    $data = $httpClient->getResult();
    
    if (isset($data['error'])) {
        echo "error";
        medi2log($data['error']);
        
        $result->addError(new Error($data['error']));
        $result = [
            'status' => 'error',
            'message' => $data['error']
        ];
        
    } else {
        $response = json_decode($data);
        return $response;
    }
    
    return $result;
}

function medi2log($data, $file_pfx = '')
{
    if (!empty($file_pfx)) {
        $file = $_SERVER['DOCUMENT_ROOT'] . "/local/logs/orders_1c/" . $file_pfx . "_" . date("d-m-Y") . '.log';
    } else {
        $file = $_SERVER['DOCUMENT_ROOT'] . "/local/logs/orders_1c/log_" . date("d-m-Y") . '.log';
    }
    
    $fp = fopen($file, "a+");
    fwrite($fp, "--------- " . date("H:i:s d-m-Y") . " -------------\r\n" . "\r\n");
    
    fwrite($fp, print_r($data, 1));
    
    fwrite($fp, "--------- end  -------------\r\n");
    fclose($fp);
}