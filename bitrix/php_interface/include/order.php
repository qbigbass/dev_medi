<?

use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Main\Loader;
use Bitrix\Sale;
use Bitrix\Main\Numerator\Numerator;
use Bitrix\Main\SystemException;
use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\Parser;

if (defined("ADMIN_SECTION")) {
    Main\EventManager::getInstance()->addEventHandler(
        'sale',
        'OnSaleOrderBeforeSaved',
        'saleOrderBeforeSaved'
    );
    Main\EventManager::getInstance()->addEventHandler(
        'sale',
        'OnSaleOrderBeforeSaved',
        'saleOrderSavedLmxCalculate'
    );
}

Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleOrderSaved',
    'ChangeActiveSelectedSizeSku'
);

function ChangeActiveSelectedSizeSku(Main\Event $event) {
    // Пересчитаем остатки SKU и изменим активную размерную характеристику у товара
    $order = $event->getParameter("ENTITY");
    $isNew = $event->getParameter("IS_NEW");
    $arDataOffer = []; // Все ТП в корзине заказа

    if ($isNew) {
        $basket = $order->getBasket();
        foreach ($basket as $basketItem) {
            $offerId = $basketItem->getProductId();
            $objElement = CIBlockElement::GetList(
                [],
                [
                    'ID' => $offerId,
                    'IBLOCK_ID' => 19
                ],
                false,
                false,
                [
                    'ID',
                    'CATALOG_AVAILABLE',
                    'SORT',
                    'PROPERTY_SELECTED_SIZE_CHARACT',
                    'PROPERTY_SELECTED_SIZE_CHARACT_SPB'
                ]
            );

            while ($arElement = $objElement->GetNext()) {
                $arDataOffer[$offerId] = $arElement;
            }
        }

        if (!empty($arDataOffer)) {

            // Найдем все подразделы раздела "Ортопедическая обувь" (ID=88)
            $arrSubSections = getSubSectionsSection(88);

            foreach ($arDataOffer as $skuId => $data) {

                $isActiveSelectedSize = false;
                if (SITE_ID == 's2') {
                    // Заказ сделан в г. Санкт-Перербург
                    if ($data['PROPERTY_SELECTED_SIZE_CHARACT_SPB_VALUE'] === 'Y') {
                        $isActiveSelectedSize = true;
                    }
                } else {
                    if ($data['PROPERTY_SELECTED_SIZE_CHARACT_VALUE'] === 'Y') {
                        $isActiveSelectedSize = true;
                    }
                }

                if ($isActiveSelectedSize && $data['CATALOG_AVAILABLE'] !== 'Y') {
                    // Торговое предложение с выбранной размерной характеристикой закончилось.
                    // Необходимо изменить выбранную размерную характеристику у товара
                    $skuProductData = CCatalogSku::GetProductInfo($skuId);
                    $skuProductId = $skuProductData['ID'];

                    // Найдем все доступные ТП у товара $skuProductId
                    $offersListProduct = CCatalogSKU::getOffersList(
                        $skuProductId,
                        0,
                        [
                            'ACTIVE' => 'Y',
                            'CATALOG_AVAILABLE' => 'Y',
                        ],
                        [
                            'ID',
                            'SORT'
                        ]
                    );

                    $arrOfferIds = [];

                    if (!empty($offersListProduct)) {
                        // Отсортируем найденные ТП по полю "Сортировка" по возрастанию
                        foreach ($offersListProduct as $productId => &$arrOffers) {
                            foreach ($arrOffers as $id => $dataSku) {
                                $arrOfferIds[] = $id;
                            }
                            usort($arrOffers, function ($a, $b) {
                                return ($a['SORT'] - $b['SORT']);
                            });
                        }
                    }

                    // Найдем разделы к которым принадлежит товар
                    $arrElemGroupSections = getGroupsElements([$skuProductId]);

                    // Проверим принадлежность товара к разделу Обувь
                    $isShoes = false;
                    if (!empty($arrElemGroupSections[$skuProductId])) {
                        foreach ($arrElemGroupSections[$skuProductId] as $productSectionId) {
                            if (in_array($productSectionId, $arrSubSections)) {
                                $isShoes = true;
                                break;
                            }
                        }
                    }

                    if ($isShoes) {
                        $filter = [
                            "ACTIVE" => "Y",
                            "PRODUCT_ID" => $arrOfferIds,
                            [
                                "LOGIC" => "OR",
                                ["UF_STORE" => true],
                                ["UF_SHOES_STORE" => true]
                            ]
                        ];
                    } else {
                        $filter = [
                            "ACTIVE" => "Y",
                            "PRODUCT_ID" => $arrOfferIds,
                            "UF_STORE" => true,
                        ];
                    }

                    if (SITE_ID == 's2') {
                        $filter['SITE_ID'] = SITE_ID;
                    } else {
                        $filter['+SITE_ID'] = SITE_ID;
                    }

                    $rsProps = CCatalogStore::GetList(
                        array('TITLE' => 'ASC', 'ID' => 'ASC'),
                        $filter,
                        false,
                        false,
                        ["ID", "ACTIVE", "ELEMENT_ID", "PRODUCT_AMOUNT", "UF_STORE", "SITE_ID"]
                    );

                    $arrStoreOffer = [];

                    while ($mStore = $rsProps->GetNext()) {
                        $arrStoreOffer[$mStore['ELEMENT_ID']] += $mStore['PRODUCT_AMOUNT'];
                    }

                    // исключить онлайн продажу, только бронь в салоне
                    $exceptionOffers = ["41078", "41079", "41080", "41081", "41082", "41083", "41084", "41085", "41086"];
                    if (!empty($arrStoreOffer)) {
                        foreach ($arrStoreOffer as $xmlId => $sumStoreAmount) {
                            if (in_array($xmlId, $exceptionOffers)) {
                                $arrStoreOffer[$xmlId] = 0;
                            }
                        }

                        // Найдем следующий по полю "Сортировка" доступный SKU
                        unset($arrOffers);

                        $GLOBALS["NOT_RUN_UPDATE_SELECTED_SIZE_OFFER"] = true; // Блокируем запуск обработчика события OnBeforeIBlockElementUpdate с функцией UpdateSelectedSizeOffer
                        $GLOBALS["NOT_RUN_UPDATE_SELECTED_SIZE_SPB_OFFER"] = true; // Блокируем запуск обработчика события OnBeforeIBlockElementUpdate с функцией UpdateSelectedSizeSpbOffer

                        $strCodePropSelectedSize = '';
                        if (SITE_ID == 's2') {
                            $strCodePropSelectedSize = "SELECTED_SIZE_CHARACT_SPB";
                        } else {
                            $strCodePropSelectedSize = "SELECTED_SIZE_CHARACT";
                        }
                        $valueEnumSelectedSize = getEnumSelectedCheckbox(19, $strCodePropSelectedSize);

                        if ($valueEnumSelectedSize > 0) {
                            foreach ($offersListProduct as $productId => $arrOffers) {
                                foreach ($arrOffers as $index => $dataSku) {
                                    $xmlId = $dataSku['ID'];
                                    if ($arrStoreOffer[$xmlId] > 0) {
                                        // Для первого доступного SKU устанавливаем чекбокс у св-ва "Активная размерная характеристика"
                                        $propertyValues = [
                                            $strCodePropSelectedSize => $valueEnumSelectedSize
                                        ];
                                        $nextActiveOfferId = $xmlId;

                                        CIBlockElement::SetPropertyValuesEx($nextActiveOfferId, "19", $propertyValues);

                                        // Для текущего SKU (который стал недоступен) снимаем чекбокс у св-ва "Активная размерная характеристика"
                                        $propertyValues = [
                                            $strCodePropSelectedSize => ''
                                        ];

                                        CIBlockElement::SetPropertyValuesEx($skuId, "19", $propertyValues);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

function updateAccountNumber()
{
    $numerator = Numerator::load(1);
    if ($numerator) {
        $res = $numerator->setNextSequentialNumber(0);
        if (!$res->isSuccess()) {
            w2l('numerator fail', 1, 'numerator.log');
        }
        $accountNumber = $numerator->getNext();
    }
    return 'updateAccountNumber();';
}

function saleOrderBeforeSaved(Main\Event $event)
{
    /** @var \Bitrix\Sale\Order $order */
    $order = $event->getParameter("ENTITY");
    
    $result = new Entity\EventResult;
    $arErrors = [];
    
    /** @var \Bitrix\Sale\PropertyValueCollection $propertyCollection */
    $propertyCollection = $order->getPropertyCollection();
    
    $propsData = [];
    
    /**
     * Собираем все свойства и их значения в массив
     * @var \Bitrix\Sale\PropertyValue $propertyItem
     */
    foreach ($propertyCollection as $propertyItem) {
        if (!empty($propertyItem->getField("CODE"))) {
            
            $propsData[$propertyItem->getField("CODE")] = ['REQUIRED' => $propertyItem->isRequired(), "VALUE" => trim($propertyItem->getValue())];
            
            // Проверяем заполнено ли обязательное поле
            if ($propertyItem->isRequired() && empty($propertyItem->getValue())) {
                $arErrors[] = "Не заполнено обязательное поле \"" . $propertyItem->getName() . "\"";
            }
        }
        //проверяем плановую дату доставки, только в начальных статусах заказа
        if ($propertyItem->getField("CODE") === 'DELIVERY_PLANNED' && !empty($propertyItem->getValue())) {
            if (in_array($order->getField('STATUS_ID'), ['N', 'MN', 'W', 'A'])) {
                $plan_date = strtotime($propertyItem->getValue() . " 23:59:59");
                if ($plan_date < mktime("23", "59", "0")) {
                    $arErrors[] = "Планируемая дата доставки не может быть раньше текущего дня!";
                }
            }
        }
    }
    if (!empty($arErrors)) {
        return new \Bitrix\Main\EventResult(
            \Bitrix\Main\EventResult::ERROR,
            \Bitrix\Sale\ResultError::create(new \Bitrix\Main\Error(implode("<br>", $arErrors), ""))
        );
    }
    return $result;
}

function saleOrderSavedLmxCalculate(Main\Event $event)
{
    global $USER;
    $log = [];
    
    /** @var Order $order */
    $order = $event->getParameter("ENTITY");
    $orderPropertyCollection = $order->getPropertyCollection();
    $orderPropsValues = $orderPropertyCollection->getArray();
    
    $orderId = $order->getId(); // ID заказа
    $sid = $order->getSiteId(); // ID сайта
    $isNew = $event->getParameter("IS_NEW");
    
    
    $log['orderId'] = $orderId;
    
    $price_id = 1;
    $max_price_id = 2;
    
    if ($sid == 's2') {
        $price_id = 6;
        $max_price_id = 5;
    }
    
    if (!$isNew) {
        $DC_NUMBER = '';
        $LOYMAX_TID = '';
        if (is_array($orderPropsValues)) {
            foreach ($orderPropsValues['properties'] as $ind => $prop) {
                if ($prop['CODE'] == 'LOYMAX_TID') {
                    $LOYMAX_TID = $prop['VALUE'][0];
                    $LOYMAX_TID_ID = $prop['ID'];
                }
                if ($prop['CODE'] == 'DC_NUMBER') {
                    $DC_NUMBER = $prop['VALUE'][0];
                }
                if ($prop['CODE'] == 'LOYMAX_TDATE') {
                    $LOYMAX_TDATE = $prop['VALUE'][0];
                    $LOYMAX_TDATE_ID = $prop['ID'];
                }
            }
        }
        
        $status_id = $order->getField("STATUS_ID");
        
        if ($status_id == 'B'): // расчет скидок в ПЛ
            
            $lmxapp = new appLmx();
            $lmxapp->authMerchantToken();
            
            //$oldValues = $event->getParameter("VALUES");
            
            $OUserID = $order->getUserId(); // ID пользователя
            $OUserPhone = $orderPropertyCollection->getPhone()->getValue();
            $user_not_found = 1;
            $log['USER_ID'] = $OUserID;
            if ($OUserPhone > 0) {
                $parsedPhone = Parser::getInstance()->parse($OUserPhone);
                $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));
                
                if ($phone != '' && strlen($phone) == 11) {
                    $checkuser = $lmxapp->checkUser($phone);
                    if ($checkuser['status'] == 'found') {
                        $authclientresult = $lmxapp->authClientToken($checkuser['code']);
                        $user_not_found = 0;
                        $log['USER_PHONE'] = $phone;
                    } else {
                        $user_not_found = 1;
                    }
                }
            }
            
            if ($OUserID > 0 && $user_not_found == '1') {
                $obUser = $USER->GetByID($OUserID);
                if ($arUser = $obUser->Fetch()) {
                    
                    $parsedPhone = Parser::getInstance()->parse($arUser['LOGIN']);
                    $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));
                    if (!$phone) {
                        $parsedPhone = Parser::getInstance()->parse($arUser['PERSONAL_PHONE']);
                        $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));
                    }
                    
                    if ($phone != '' && strlen($phone) == 11) {
                        $checkuser = $lmxapp->checkUser($phone);
                        if ($checkuser['status'] == 'found') {
                            $authclientresult = $lmxapp->authClientToken($checkuser['code']);
                            $user_not_found = 0;
                            $log['USER_PHONE'] = $phone;
                        } else {
                            $user_not_found = 1;
                        }
                    }
                } else {
                    $user_not_found = 1;
                }
            }
            
            if ($DC_NUMBER != '' && $user_not_found == '1') {
                $checkuser = $lmxapp->checkUser($DC_NUMBER);
                if ($checkuser['status'] == 'found') {
                    $authclientresult = $lmxapp->authClientToken($checkuser['code']);
                    $user_not_found = 0;
                } else {
                    $user_not_found = 1;
                }
            }
            
            if ($user_not_found == '0') {
                $objDateTime = new DateTime('NOW');
                $purchaseDate = $objDateTime->format("Y-m-d\TH:i:s.v\Z");
                
                if ($LOYMAX_TID != '' && $LOYMAX_TID != 'error') {
                    $purchaseId = str_replace([" ", "."], "", $LOYMAX_TID);
                } else {
                    $purchaseId = str_replace([" ", "."], "", rand(1, 10) . microtime() . $OUserID);
                    $LOYMAX_TID = $purchaseId;
                }
                if ($LOYMAX_TDATE == '') {
                    $LOYMAX_TDATE = $purchaseDate;
                }
                
                $log['purchaseId'] = $purchaseId;
                
                $basket = $order->getBasket();
                
                $arResult['BASKET_SUM'] = 0;
                $lines = [];
                $i = 1;
                $cc = 0;
                CModule::IncludeModule("iblock");
                
                foreach ($basket as $basketItem) {
                    
                    $iblock_id = CIBlockElement::GetIBlockByID($basketItem->getProductId());
                    $obItem = CIBlockElement::GetList([], ['IBLOCK_ID' => $iblock_id, 'ID' => $basketItem->getProductId(), 'ACTIVE' => 'Y'],
                        false, false, ['ID', 'CATALOG_PRICE_' . $price_id, 'CATALOG_PRICE_' . $max_price_id, "PROPERTY_GTIN", "PROPERTY_LMX_GOODID", "NAME", "PROPERTY_CML2_ARTICLE"]);
                    if ($exItem = $obItem->GetNext()) {
                        
                        $lines[$cc] = [
                            "position" => $i,
                            "amount" => $exItem['CATALOG_PRICE_' . $price_id] * $basketItem->getQuantity(),
                            
                            "quantity" => $basketItem->getQuantity(),
                            "cashback" => 0,
                            "discount" => 0,
                            "name" => $exItem['PROPERTY_CML2_ARTICLE_VALUE'],
                            "price" => $exItem['CATALOG_PRICE_' . $price_id]
                        ];
                        if ($exItem['PROPERTY_LMX_GOODID_VALUE'] != '') {
                            $lines[$cc] = array_merge($lines[$cc], ['goodsId' => $exItem['PROPERTY_LMX_GOODID_VALUE']]);
                        } elseif ($exItem['PROPERTY_GTIN_VALUE'] != '') {
                            $lines[$cc] = array_merge($lines[$cc], ['barcode' => substr($exItem['PROPERTY_GTIN_VALUE'], 1)]);
                        }
                        if ($exItem['CATALOG_PRICE_' . $max_price_id] > $exItem['CATALOG_PRICE_' . $price_id]) {
                            $lines[$cc]['discount'] = $exItem['CATALOG_PRICE_' . $max_price_id] - $exItem['CATALOG_PRICE_' . $price_id];
                        }
                        $i++;
                        $cc++;
                        
                    }
                }
                
                $log['lines'] = $lines;
                
                if (!empty($lines)) {
                    $qResult = $lmxapp->calculate($LOYMAX_TID, $LOYMAX_TDATE, $lines);
                    
                    if ($qResult['result']['state'] == 'Success') {
                        $LOYMAX_TID_PROP = $orderPropertyCollection->getItemByOrderPropertyId($LOYMAX_TID_ID);
                        $LOYMAX_TID_PROP->setValue($LOYMAX_TID);
                        $LOYMAX_TDATE_PROP = $orderPropertyCollection->getItemByOrderPropertyId($LOYMAX_TDATE_ID);
                        $LOYMAX_TDATE_PROP->setValue($LOYMAX_TDATE);
                        
                        $log['cheque'] = $qResult['data'][0]['cheque']['lines'];
                        foreach ($qResult['data'][0]['cheque']['lines'] as $k => $line) {
                            if ($basket[$k] !== null && !$lines[$k]['exclude']) {
                                $basket[$k]->setFields(array(
                                    'CUSTOM_PRICE' => "Y",
                                    'PRICE' => ($line['amount'] / $line['quantity']),
                                    'BASE_PRICE' => $lines[$k]['price'],
                                    'DISCOUNT_PRICE' => ($line['discount'] / $line['quantity']),
                                    'DISCOUNT_NAME' => $line['appliedOffers'][0]['name'],
                                    'NOTES' => $line['appliedOffers'][0]['name'],
                                    'DISCOUNT_VALUE' => ($line['discount'] / $line['quantity'])
                                )); // Изменение полей
                                
                                $basketPropertyCollection = $basket[$k]->getPropertyCollection();
                                
                                $fullprice = ($line['amount'] + $line['discount']) / $line['quantity'];
                                $discountprice = $line['discount'] / $line['quantity'];
                                
                                $basketPropertyCollection->setProperty(array(
                                    array(
                                        'NAME' => 'Скидка ' . round($discountprice / $fullprice * 100, 0) . '%',
                                        'CODE' => 'DISCOUNT_NAME',
                                        'VALUE' => $line['appliedOffers'][0]['name'],
                                    
                                    ),
                                ));
                                
                            }
                        }
                    }
                }
                
                w2l($log, 1, 'lmxorder.log');
                // END запрос в лоймакс
                
                
            } else {
                $LOYMAX_TID == 'клиент не найден';
                $LOYMAX_TID_PROP = $orderPropertyCollection->getItemByOrderPropertyId($LOYMAX_TID_ID);
                $LOYMAX_TID_PROP->setValue($LOYMAX_TID);
                //$order->save();
            }
        endif; // END расчет скидок в ПЛ
    }
}


AddEventHandler("sale", "OnOrderStatusSendEmail", "addPaylink2mail");

function addPaylink2mail($orderID, &$eventName, &$arFields, $val = FALSE)
{
    
    global $USER;
    
    if ($eventName == 'SALE_STATUS_CHANGED_Q') {
        
        $connection = \Bitrix\Main\Application::getConnection();
        
        
        CModule::IncludeModule("sale");
        
        $data = $connection
            ->query("SELECT * FROM vampirus_yandexkassa_order WHERE order_id='" . $orderID . "'")
            ->fetch();
        
        $arOrder = CSaleOrder::GetByID($orderID);
        
        
        $arFields["ACCOUNT_NUMBER"] = $arOrder['ACCOUNT_NUMBER'];
        
        if ($data['id']) {
            $PAYLINK = 'https://www.medi-salon.ru/bitrix/tools/yandexcheckoutvs_pay.php?id=' . $data['id'];
            $arFields['PAYLINK'] = ' Или перейдя по ссылке:&nbsp;<a href="' . $PAYLINK . '">' . $PAYLINK . '</a><br>';
        }
    }
}

// Добавление полей в шаблоне уведомления о новом заказе
AddEventHandler("sale", "OnOrderNewSendEmail", "bxModifySaleMails");
AddEventHandler("sale", "OnOrderStatusSendEmail", "bxModifySaleMails");


//-- Обработчик шаблона письма для менеджеров
function bxModifySaleMails($orderID, &$eventName, &$arFields, $val = FALSE)
{
    global $USER;
    $arOrder = CSaleOrder::GetByID($orderID);
    
    $tracking_number = '-';
    if (!empty($arOrder['TRACKING_NUMBER'])) {
        $tracking_number = $arOrder['TRACKING_NUMBER'];
    }
    
    //-- получаем телефоны и адрес
    $order_props = CSaleOrderPropsValue::GetOrderProps($orderID);
    
    $phone = "";
    $country_name = "";
    $region_name = "";
    $city_name = "";
    $address = "";
    $zip_code = "";
    $discount_type = "";
    
    while ($arProps = $order_props->Fetch()) {
        if ($arProps["CODE"] == "PHONE") {
            $phone = htmlspecialchars($arProps["VALUE"]);
        }
        if ($arProps["CODE"] == "LOCATION") {
            $arLocs = CSaleLocation::GetByID($arProps["VALUE"]);
            $country_name = $arLocs["COUNTRY_NAME_ORIG"];
            $region_name = $arLocs["REGION_NAME_ORIG"];
            $city_name = $arLocs["CITY_NAME_ORIG"];
        }
        
        if ($arProps["CODE"] == "METRO" && !empty($arProps["VALUE"])) {
            $address .= " м. " . $arProps["VALUE"];
        }
        if ($arProps["CODE"] == "ADDRESS") {
            $address .= $arProps["VALUE"];
        }
        if ($arProps["CODE"] == "ADDRESS_INFO") {
            $address .= ", " . $arProps["VALUE"];
        }
        
        
        if ($arProps['CODE'] == 'ZIP') {
            $zip_code = $arProps['VALUE'];
        }
        
        if ($arProps["CODE"] == "ORDER_REF") {
            $arVal = CSaleOrderPropsVariant::GetByValue($arProps["ORDER_PROPS_ID"], $arProps["VALUE"]);
            $source = htmlspecialchars($arVal["NAME"]);
            
        }
        if ($arProps["CODE"] == "GPO") {
            $arVal = CSaleOrderPropsVariant::GetByValue($arProps["ORDER_PROPS_ID"], $arProps["VALUE"]);
            $gpo = htmlspecialchars($arVal["VALUE"]);
        }
        
        // Тип специальной скидки SELECT
        if ($arProps['CODE'] == 'DISCOUNT_TYPE') {
            $arVal = CSaleOrderPropsVariant::GetByValue($arProps["ORDER_PROPS_ID"], $arProps["VALUE"]);
            $discount_type = htmlspecialchars($arVal["NAME"]);
        }
    }
    
    // -- полный адрес доставки
    if ($country_name)
        $full_address .= $country_name;
    if ($region_name)
        $full_address .= ', ' . $region_name;
    if ($city_name)
        $full_address .= ', ' . $city_name;
    if ($address)
        $full_address .= ', ' . $address;
    
    
    //-- получаем название службы доставки
    $arDeliv = CSaleDelivery::GetByID($arOrder["DELIVERY_ID"]);
    $delivery_name = "";
    if ($arDeliv) {
        $delivery_name = $arDeliv["NAME"];
    } else {
        list($sCode, $sProfile) = explode(':', $arOrder["DELIVERY_ID"]);
        if ($sCode) {
            $rsDeliv = CSaleDeliveryHandler::GetBySID($sCode);
            if ($arDeliv = $rsDeliv->GetNext()) {
                $delivery_name = $arDeliv['NAME'];
                if ($sProfile) {
                    $delivery_name .= ', ' . $arDeliv['PROFILES'][$sProfile]['TITLE'];
                }
            }
        } else {
            $delivery_name = "";
        }
    }
    
    //-- получаем название платежной системы
    $arPaySystem = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"]);
    $pay_system_name = "";
    if ($arPaySystem) {
        $pay_system_name = $arPaySystem["NAME"];
    }
    
    $USERID = $USER->GetID();
    $ugroups = CUser::GetUserGroup($USERID);
    
    if ($arOrder['USER_ID'] != $USERID
        && $arOrder['STATUS_ID'] == 'N'
        && !intval($arOrder['EMP_MARKED_ID']) > 0
    ) {
        $arOrderFields = array(
            "MARKED" => "N",
            "DATE_MARKED" => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG))),
            "EMP_MARKED_ID" => $USERID
        );
        $arOrder['EMP_MARKED_ID'] = $USERID;
        CSaleOrder::Update($orderID, $arOrderFields);
    }
    
    
    // Фамилия имя оператора
    $operator_sif = "";
    $arOrder = CSaleOrder::GetByID($orderID);
    if ($arOrder['EMP_MARKED_ID']) {
        
        $rsOperatorUser = CUser::GetByID($arOrder['EMP_MARKED_ID']);
        
        if ($arOperatorUser = $rsOperatorUser->Fetch()) {
            $usergroups = CUser::GetUserGroup($arOperatorUser['ID']);
            if (in_array(9, $usergroups) || in_array(11, $usergroups) || in_array(18, $usergroups) || in_array(19, $usergroups)) {
                $operator_sif = $arOperatorUser['LAST_NAME'] . ' ' . $arOperatorUser['NAME'];
                
            }
        }
    }
    
    
    // Создатель заказа
    $db_vals = CSaleOrderPropsValue::GetList(
        array("SORT" => "ASC"),
        array(
            "ORDER_ID" => $orderID,
            "ORDER_PROPS_ID" => 10
        )
    );
    if ($arVals = $db_vals->Fetch()) {
        if ($operator_sif != '') {
            CSaleOrderPropsValue::Update($arVals['ID'], array("VALUE" => $operator_sif));
        } else {
            CSaleOrderPropsValue::Update($arVals['ID'], array("VALUE" => "Клиент"));
        }
    } else {
        $arFieldsProp = array(
            "ORDER_ID" => $orderID,
            "ORDER_PROPS_ID" => 10,
            "NAME" => "Автор заказа",
            "CODE" => "CREATOR",
            "VALUE" => "Клиент"
        );
        if ($operator_sif != '') {
            $arFieldsProp['VALUE'] = $operator_sif;
        }
        
        CSaleOrderPropsValue::Add($arFieldsProp);
    }
    
    if ($arOrder['STATUS_ID'] == 'N') {
        // Источник заказа
        $db_vals = CSaleOrderPropsValue::GetList(
            array("SORT" => "ASC"),
            array(
                "ORDER_ID" => $orderID,
                "ORDER_PROPS_ID" => 11
            )
        );
        if ($arVals = $db_vals->Fetch()) {
            if ($operator_sif == '' && !in_array(20, $ugroups)) {
                $arNewVals = ['NAME' => $arVals['NAME'],
                    'CODE' => $arVals['CODE'],
                    'ORDER_PROPS_ID' => $arVals['ORDER_PROPS_ID'],
                    'ORDER_ID' => $arVals['ORDER_ID'],
                    'VALUE' => "ORDER_REF_INET"];
                CSaleOrderPropsValue::Update($arVals['ID'], $arNewVals);
            }
            
            if (in_array(20, $ugroups)) {
                mail("makoviychuk@mediexp.ru", "salon order", "salon order " . $arOrder['ACCOUNT_NUMBER'] . "<pre>" . print_r($arNewVals, 1) . "</pre>");
                $arNewVals = ['NAME' => $arVals['NAME'],
                    'CODE' => $arVals['CODE'],
                    'ORDER_PROPS_ID' => $arVals['ORDER_PROPS_ID'],
                    'ORDER_ID' => $arVals['ORDER_ID'],
                    'VALUE' => "ORDER_REF_SALONS"];
                
                CSaleOrderPropsValue::Update($arVals['ID'], $arNewVals);
            }
        }
    }
    
    
    if ($phone) {
        if (isset($_COOKIE['_msuid']) && $operator_sif == '') {
            
            $msuid = $_COOKIE['_msuid'];
        } else {
            $msuid = generate_msuid($phone);
        }
        
        $db_vals = CSaleOrderPropsValue::GetList(
            array("SORT" => "ASC"),
            array(
                "ORDER_ID" => $orderID,
                "ORDER_PROPS_ID" => 29
            )
        );
        if ($arVals = $db_vals->Fetch()) {
            CSaleOrderPropsValue::Update($arVals['ID'], array("VALUE" => $msuid));
        } else {
            
            
            $arFieldsProp = array(
                "ORDER_ID" => $orderID,
                "ORDER_PROPS_ID" => 29,
                "NAME" => "MSUID",
                "CODE" => "MSUID",
                "VALUE" => $msuid
            );
            
            CSaleOrderPropsValue::Add($arFieldsProp);
        }
    }
    
    
    //-- добавляем новые поля в массив результатов
    $arFields["ACCOUNT_NUMBER"] = $arOrder['ACCOUNT_NUMBER'];
    $arFields["ORIGINAL_ORDER_ID"] = $orderID;
    $arFields["ORDER_DESCRIPTION"] = $arOrder["USER_DESCRIPTION"];
    $arFields["PHONE"] = $phone;
    $arFields["DELIVERY_NAME"] = $delivery_name;
    $arFields["DELIVERY_PRICE"] = CCurrencyLang::CurrencyFormat($arOrder['PRICE_DELIVERY'], $arOrder['CURRENCY'], true);
    $arFields["PAY_SYSTEM_NAME"] = $pay_system_name;
    $arFields["FULL_ADDRESS"] = $full_address;
    $arFields['ZIP_CODE'] = $zip_code;
    $arFields['TRACKING_NUMBER'] = $tracking_number;
    $arFields['DISCOUNT_TYPE'] = $discount_type;
    $arFields['SOURCE'] = !empty($source) ? '<span style="font-weight: bold;">Источник:</span>&nbsp;' . $source . "</span><br/>" : "";
    $arFields['GPO'] = $gpo == 'YES_GPO' ? '<span style="font-weight: bold;">ГПО</span>&nbsp;Да</span><br/>' : "";
    
    
    if ($operator_sif) {
        $arFields['ORDER_MAKER'] = $operator_sif;
    } else {
        $arFields['ORDER_MAKER'] = 'клиент';
    }
    
    // вырезаем лишние данные из списка товаров
    
    $arFields['ORDER_LIST'] = preg_replace('/(GTIN: \d+;)/', '', $arFields['ORDER_LIST']);
    $arFields['ORDER_LIST'] = preg_replace('/(Регион продажи: .+;)/U', '', $arFields['ORDER_LIST']);
    
    
}


\Bitrix\Main\EventManager::getInstance()->addEventHandler('sale', 'onSaleDeliveryServiceCalculate', 'mediModifyOrderDelivery');

function mediModifyOrderDelivery(\Bitrix\Main\Event $event)
{
    $calcResult = $event->getParameter('RESULT');
    $shipment = $event->getParameter('SHIPMENT');
    
    $delivPrice = $calcResult->getDeliveryPrice();
    
    
    if ($delivPrice && ($delivPrice > 0)) {
        $delivPrice = ceil($delivPrice);
    }
    
    $calcResult->setDeliveryPrice($delivPrice);
    
    return new \Bitrix\Main\EventResult(
        \Bitrix\Main\EventResult::SUCCESS,
        array(
            "RESULT" => $calcResult,
        )
    );
}


// отправка смс на статус "Исполняется"
\Bitrix\Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleStatusOrderChange', ['StatusHandler', 'OnSaleStatusOrderChange']);

class StatusHandler
{
    function OnSaleStatusOrderChange($event)
    {
        $parameters = $event->getParameters();
        if ($parameters['VALUE'] === 'I') {
            /** @var \Bitrix\Sale\Order $order */
            $order = $parameters['ENTITY'];
            $propertyCollection = $order->getPropertyCollection();
            $phonePropValue = $propertyCollection->getPhone();
            $isGPO = $propertyCollection->getItemByOrderPropertyId(12);
            $nameGPO = $propertyCollection->getItemByOrderPropertyId(13);
            
            $specs = $nameGPO->getProperty();
            if ($isGPO->getValue() == 'YES_GPO' && !empty($nameGPO->getValue())) {
                $selected_spec = $specs['OPTIONS'][$nameGPO->getValue()];
                
                $arFields = [
                    'SPEC' => $selected_spec,
                    'PHONE' => $phonePropValue->getValue()
                
                ];
                // Параметры указаны здесь
                if ($arFields['SPEC'] != '')
                    \CEvent::Send('SALE_STATUS_CHANGED_I', 's1', $arFields, 'N');
                
            }
            
        } elseif ($parameters['VALUE'] === 'A') {
            /** @var \Bitrix\Sale\Order $order */
            $order = $parameters['ENTITY'];
            $propertyCollection = $order->getPropertyCollection();
            $phonePropValue = $propertyCollection->getPhone();
            $orderNum = $order->getField("ACCOUNT_NUMBER");
            $site = $order->getField("LID");
            $isGPO = $propertyCollection->getItemByOrderPropertyId(12);
            $nameGPO = $propertyCollection->getItemByOrderPropertyId(13);
            
            $specs = $nameGPO->getProperty();
            if ($isGPO->getValue() == 'YES_GPO') {
                //$selected_spec = $specs['OPTIONS'][$nameGPO->getValue()];
                
                $arFields = [
                    //'SPEC' => $selected_spec,
                    'ACCOUNT_NUMBER' => $orderNum,
                    'MSG_TEXT' => 'Новый заказ ГПО №' . $orderNum,
                    //'PHONE' => $site == 's2' ? '79019971161' : '79038246596'
                    'PHONE' => $site == 's2' ? '79313609705' : '79851035147'
                
                ];
                // Параметры указаны здесь
                if ($arFields['PHONE'] != '')
                    \CEvent::Send('GPO_NOTIFY', 's1', $arFields, 'N');
                
            }
            
        }
        
        return new \Bitrix\Main\EventResult(
            \Bitrix\Main\EventResult::SUCCESS
        );
    }
}
