<?php
/**
 * @copyright Copyright &copy; Компания MEAsoft, 2014
 */

// OPTIONS IN ADMIN!

use \Bitrix\Sale,
    \Bitrix\Main\Config\Option,
    \Bitrix\Main\Application,
    \Bitrix\Sale\Exchange\Integration\Admin\Link,
    \Bitrix\Sale\Helpers\Admin\Blocks\FactoryMode,
    \Bitrix\Sale\Helpers\Admin\Blocks\BlockType;



class MeasoftEvents
{


    /**
     * Настройки модуля.
     */
    public static $_config;

    public static $_deliveryConfig;

    /**
     * Инициализация объекта.
     */
    public static function Init()
    {
        IncludeModuleLangFile(__FILE__);

        return array(
            // общее описание
            "SID" => "courier",
            "NAME" => GetMessage('MEASOFT_HANDLER_NAME'),
            "DESCRIPTION" => GetMessage('MEASOFT_HANDLER_DESCRIPTION'),
            "DESCRIPTION_INNER" => GetMessage('MEASOFT_HANDLER_DESCRIPTION_INNER'),
            "BASE_CURRENCY" => COption::GetOptionString("sale", "default_currency", "RUB"),

            "HANDLER" => __FILE__,

            // методы-обработчики
            "DBGETSETTINGS" => array(__CLASS__, "GetSettings"),
            "DBGETSETTINGS" => array(__CLASS__, "GetSettings"),
            "DBSETSETTINGS" => array(__CLASS__, "SetSettings"),
            "GETCONFIG" => array(__CLASS__, "GetConfig"),

            "COMPABILITY" => array(__CLASS__, "Compability"),
            "CALCULATOR" => array(__CLASS__, "Calculate"),

            // Список профилей
            "PROFILES" => array(
                "simple" => array(
                    "TITLE" => GetMessage('MEASOFT_PROFILE_SIMPLE_TITLE'), // 'Доставка курьером',
                    "DESCRIPTION" => GetMessage('MEASOFT_HANDLER_DESCRIPTION'),
                    "RESTRICTIONS_WEIGHT" => array(0),
                    "RESTRICTIONS_SUM" => array(0),
                ),
                "pickup" => array(
                    "TITLE" => GetMessage('MEASOFT_PROFILE_PICKUP_TITLE'), // 'Самовывоз из ПВЗ',
                    "DESCRIPTION" => GetMessage('MEASOFT_HANDLER_DESCRIPTION'),
                    "RESTRICTIONS_WEIGHT" => array(0),
                    "RESTRICTIONS_SUM" => array(0),
                )
            )
        );
    }
    public static function getCatalogPorps(){
        $props = ["" => GetMessage('MEASOFT_PROPS_NO')];
        $catalogsRes = \CCatalog::GetList(["ID" => "ASC"]);
        $catalogs = [];
        while($catalogsArray = $catalogsRes -> fetch()){
            $catalogs[] = $catalogsArray;
        }
        foreach($catalogs as $catalog){
            $rsProperty = CIBlockProperty::GetList(
                ["NAME" => "ASC"],
                ["IBLOCK_ID" => $catalog["ID"]]
            );
            while($arProperty = $rsProperty -> fetch()){
                $props[$arProperty["CODE"]."@".$catalog["ID"]] = $arProperty["NAME"] . " (".$catalog["NAME"].")";
            }
        }

        return $props;
    }
    /**
     * Запрос конфигурации службы доставки.
     */
    public static function GetConfig()
    {
        $citySender = [];
        $citySender[0] = " ";

        $tableName = '';

        global $DB;
        $results = $DB->Query("SELECT * FROM `measoft_cities`");
        while ($row = $results->Fetch()) {
            $citySender[$row['MEASOFT_ID']] = $row['NAME'];
        }

        // список статусов заказа
        $dbStatusesList = CSaleStatus::GetList(array('SORT' => 'ASC'), array(), false, false, array("ID", "NAME"));
        $sendStatuses = array();
        while ($arResult = $dbStatusesList->Fetch()) {
            $sendStatuses[$arResult['ID']] = $arResult['NAME'];
        }

        // список полей клиента
        $dbProps = CSaleOrderProps::GetList(array("SORT" => "ASC"), array("ACTIVE" => 'Y'), false, false, array());
        $props[0] = " ";
        while ($prop = $dbProps->Fetch()) {
            $props[$prop['CODE']] = $prop['NAME'];
        }

        // список способов оплаты
        $dbPaySystemsList = CSalePaySystem::GetList(array('SORT' => 'ASC'), array("ACTIVE" => 'Y'), false, false, array("ID", "NAME"));
        $paySystems = array();
        while ($arResult = $dbPaySystemsList->Fetch()) {
            $paySystems[$arResult['ID']] = $arResult['NAME'];
        }

        $numberTypes = array(
            0 => GetMessage('MEASOFT_CONFIG_ORDER_NUMBER_ID'),
            1 => GetMessage('MEASOFT_CONFIG_ORDER_NUMBER_NUMBER'),
        );
        $catalogProps = self::getCatalogPorps();
        $arConfig = array(
            "CONFIG_GROUPS" => array(
                'general' => GetMessage('MEASOFT_HANDLER_GROUP_GENERAL'),
                'price' => GetMessage('MEASOFT_HANDLER_GROUP_DELIVERY_PRICE_POLICY')
            ),
            "CONFIG" => array(
                "SECTION_AUTH" => array(
                    'TYPE' => 'SECTION',
                    'TITLE' => GetMessage('MEASOFT_CONFIG_SECTION_AUTH'),
                    "GROUP" => "general",
                ),
                "LOGIN" => array(
                    "TYPE" => "STRING",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_CLIENT_LOGIN'),
                    "DEFAULT" => '',
                    "GROUP" => "general",
                ),
                "PASSWORD" => array(
                    "TYPE" => "STRING",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_CLIENT_PASSWORD'),
                    "DEFAULT" => '',
                    "GROUP" => "general",
                ),
                "CODE" => array(
                    "TYPE" => "STRING",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_CLIENT_CODE'),
                    "DEFAULT" => '',
                    "GROUP" => "general",
                ),
                /*"MAP_CLIENT_CODE" => array(
                    "TYPE" => "HIDDEN",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_CLIENT_CODE_PVZ'),
                    "DEFAULT" => '',
                    "GROUP" => "general",
                ),*/

                "SECTION_PROPS" => array(
                    'TYPE' => 'SECTION',
                    'TITLE' => GetMessage('MEASOFT_CONFIG_SECTION_PROPS'),
                    "GROUP" => "general",
                ),
                "CITY_SENDER" => array(
                    "TYPE" => "DROPDOWN",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_CITY_SENDER'),
                    "VALUES" => $citySender,
                    "DEFAULT" => '',
                    "GROUP" => "general",
                ),
                /* "SEND_STATUS" => array(
                     "TYPE" => "DROPDOWN",
                     "TITLE" => GetMessage('MEASOFT_CONFIG_SEND_STATUS'),
                     "VALUES" => $sendStatuses,
                     "DEFAULT" => '',
                     "GROUP" => "general",
                 ),*/
                "PAYTYPE_CARD" => array(
                    "TYPE" => "DROPDOWN", // "",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_PAYTYPE_CARD'),
                    "VALUES" => $paySystems,
                    "DEFAULT" => '',
                    "GROUP" => "general",
                ),
                "ORDER_NUMBER" => array(
                    "TYPE" => "DROPDOWN",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_ORDER_NUMBER'),
                    "DEFAULT" => '',
                    "VALUES" => $numberTypes,
                    "GROUP" => "general",
                ),
                "ORDER_PREFIX" => array(
                    "TYPE" => "STRING",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_ORDER_PREFIX'),
                    "DEFAULT" => '',
                    "GROUP" => "general",
                ),
                "DELIVERY_SERVICE" => array(
                    "TYPE" => "STRING",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_DELIVERY_SERVICE'),
                    "DEFAULT" => '1',
                    "GROUP" => "general",
                ),
                "USE_ARTICLES" => array(
                    "TYPE" => "CHECKBOX",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_USE_ARTICLES'),
                    "DEFAULT" => '',
                    "GROUP" => "general",
                ),
                'HIDE_DATE_OPTION' => array(
                    'TYPE' => 'DROPDOWN',
                    'TITLE' => GetMessage('MEASOFT_CONFIG_HIDE_DELIVERY_DATE_OPTION'),
                    'DEFAULT' => 'N',
                    'GROUP' => 'general',
					'VALUES' => ['N' => GetMessage('MEASOFT_CONFIG_HIDE_DELIVERY_DATE_OPTION_N'), 'D' => GetMessage('MEASOFT_CONFIG_HIDE_DELIVERY_DATE_OPTION_D'), 'Y' => GetMessage('MEASOFT_CONFIG_HIDE_DELIVERY_DATE_OPTION_Y'), ]
                ),
                "HIDE_MAP_EDITS" => array(
                    "TYPE" => "CHECKBOX",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_HIDE_MAP_EDITS'),
                    "DEFAULT" => '',
                    "GROUP" => "general",
                ),
                "HIDE_MAP_SEARCH" => array(
                    "TYPE" => "CHECKBOX",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_HIDE_MAP_SEARCH'),
                    "DEFAULT" => '',
                    "GROUP" => "general",
                ),
                "DISABLE_CALENDAR" => array(
                    "TYPE" => "CHECKBOX",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_DISABLE_CALENDAR'),
                    "DEFAULT" => '',
                    "GROUP" => "general",
                ),
                "SEND_ZIP" => array(
                    "TYPE" => "CHECKBOX",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_SEND_ZIP'),
                    "DEFAULT" => '',
                    "GROUP" => "general",
                ),
                "SEND_ADDRESS" => array(
                    "TYPE" => "CHECKBOX",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_SEND_ADDRESS'),
                    "DEFAULT" => '',
                    "GROUP" => "general",
                ),

                "SECTION_FIELDS" => array(
                    'TYPE' => 'SECTION',
                    'TITLE' => GetMessage('MEASOFT_CONFIG_SECTION_FIELDS'),
                    "GROUP" => "general",
                ),
                "PROP_FIO" => array(
                    "TYPE" => "DROPDOWN",
                    "DEFAULT" => "FIO",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_PROP_FIO'),
                    "GROUP" => "general",
                    'POST_TEXT' => '',
                    'VALUES' => $props
                ),
                "PROP_CITY" => array(
                    "TYPE" => "DROPDOWN",
                    "DEFAULT" => "CITY",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_PROP_CITY'),
                    "GROUP" => "general",
                    'VALUES' => $props
                ),
                "PROP_COMPANY" => array(
                    "TYPE" => "DROPDOWN",
                    "DEFAULT" => "COMPANY",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_PROP_COMPANY'),
                    "GROUP" => "general",
                    'VALUES' => $props
                ),
                "PROP_ADDRESS" => array(
                    "TYPE" => "DROPDOWN",
                    "DEFAULT" => "ADDRESS",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_PROP_ADDRESS'),
                    "GROUP" => "general",
                    'VALUES' => $props
                ),
                "PROP_PHONE" => array(
                    "TYPE" => "DROPDOWN",
                    "DEFAULT" => "PHONE",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_PROP_PHONE'),
                    "GROUP" => "general",
                    'VALUES' => $props
                ),
                "PROP_ZIP" => array(
                    "TYPE" => "DROPDOWN",
                    "DEFAULT" => "ZIP",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_PROP_ZIP'),
                    "GROUP" => "general",
                    'VALUES' => $props
                ),
                "SECTION_PRODUCT_PROPS" => array(
                    'TYPE' => 'SECTION',
                    'TITLE' => GetMessage('MEASOFT_CONFIG_SECTION_PRODUCT_PROPS'),
                    "GROUP" => "general",
                ),
                "PROP_ITEM_BARCODE" => array(
                    "TYPE" => "DROPDOWN",
                    "DEFAULT" => "",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_DEFAULT_BARCODE'),
                    "GROUP" => "general",
                    'VALUES' => $catalogProps
                ),
                "DEFAULT_PICKPOINT" => array(
                    "TYPE" => "STRING",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_DEFAULT_PICKPOINT'),
                    "DEFAULT" => '',
                    "GROUP" => "general",
                ),
				"DEFAULT_GCODE" => array(
                    "TYPE" => "STRING",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_DEFAULT_GCODE'),
                    "DEFAULT" => '',
                    "GROUP" => "general",
                ),
				"DELIVERY_VATRATE" => array(
                    "TYPE" => "STRING",
                    "TITLE" => GetMessage('MEASOFT_CONFIG_DELIVERY_VATRATE'),
                    "DEFAULT" => '',
                    "GROUP" => "general",
                ),
                "SECTION_SEND_STATUS" => array(
                    'TYPE' => 'SECTION',
                    'TITLE' => GetMessage('MEASOFT_CONFIG_SECTION_SEND_STATUS'),
                    "GROUP" => "general",
                ),
            ),

        );

        foreach($sendStatuses as $statusCode => $statusValue){
            $arConfig["CONFIG"]["SEND_STATUS_".$statusCode] = [
                "TYPE" => "CHECKBOX",
                "TITLE" => $statusValue,
                "VALUES" => "Y",
                "GROUP" => "general",
            ];
        }
        // ценовая политика доставки
        for ($i = 1; $i <= 3; $i++) {
            $arConfig['CONFIG'] += array(
                'SECTION_PRICE_' . $i => array(
                    'TYPE' => 'SECTION',
                    'TITLE' => GetMessage('MEASOFT_CONFIG_PRICE_INTERVAL') . $i,
                    "GROUP" => "price",
                ),
                'PRICE_IF_' . $i . '_MIN' => array(
                    'TYPE' => 'STRING',
                    'TITLE' => GetMessage('MEASOFT_CONFIG_ORDER_MIN_PRICE'),
                    'SIZE' => 1,
                    'DEFAULT' => '',
                    'GROUP' => 'price',
                    'CHECK_FORMAT' => 'NUMBER',
                ),
                'PRICE_IF_' . $i . '_MAX' => array(
                    'TYPE' => 'STRING',
                    'TITLE' => GetMessage('MEASOFT_CONFIG_ORDER_MAX_PRICE'),
                    'SIZE' => 1,
                    'DEFAULT' => '',
                    'GROUP' => 'price',
                    'CHECK_FORMAT' => 'NUMBER',
                ),
                'PRICE_IF_' . $i . '_TYPE' => array(
                    'TYPE' => 'DROPDOWN',
                    'TITLE' => GetMessage('MEASOFT_CONFIG_PAY_TYPE'),
                    'DEFAULT' => 1,
                    'VALUES' => array(
                        1 => GetMessage('MEASOFT_CONFIG_OPTION_CLIENT_PAY_ALL'),
                        2 => GetMessage('MEASOFT_CONFIG_OPTION_MARKET_PAY_ALL'),
                        3 => GetMessage('MEASOFT_CONFIG_OPTION_MARKET_PAY_PRESENT'),
                        4 => GetMessage('MEASOFT_CONFIG_OPTION_MARKET_PAY_RUB'),
                    ),
                    'GROUP' => 'price',
                ),
                'PRICE_IF_' . $i . '_AOMUNT' => array(
                    'TYPE' => 'STRING',
                    'TITLE' => GetMessage('MEASOFT_CONFIG_SUMM'),
                    'SIZE' => 1,
                    'DEFAULT' => '',
                    'GROUP' => 'price',
                    'CHECK_FORMAT' => 'NUMBER',
                ),
            );
        }

        return $arConfig;
    }

    /**
     * Возвращает значение из конфига.
     * (Дополнительный метод)
     */
    public static function configValue($param = null)
    {
        if (!self::$_config) {
            self::$_config = CSaleDeliveryHandler::GetBySID('courier')->Fetch();
        }

        if ($param) {
            return isset(self::$_config['CONFIG']['CONFIG'][$param]) ? self::$_config['CONFIG']['CONFIG'][$param]['VALUE'] : null;
        }

        return self::$_config;
    }

    public static function configValueEx($paramName, $deliveryId)
    {
        static $deliveryPropArr = [];

        if (!isset($deliveryPropArr[$deliveryId])) {
            $currProfile = \Bitrix\Sale\Delivery\Services\Table::getList(array(
                'filter' => array("ID" => $deliveryId),
            ))->fetch();

            if ( isset($currProfile["CODE"]) )
            {
                if ($currProfile["CODE"] == "courier")
                {
                    $delivery = $currProfile;
                }
            }

            if ( !isset($delivery) )
            {
                $result = \Bitrix\Sale\Delivery\Services\Table::getList(array(
                    'filter' => array("ID" => $currProfile["PARENT_ID"]),
                ));

                if ($delivery = $result->fetch()) {
                    $deliveryPropArr[$deliveryId] = unserialize(unserialize($delivery["CONFIG"]["MAIN"]["OLD_SETTINGS"]));
                }
            } else
            {
                $deliveryPropArr[$deliveryId] = unserialize(unserialize($delivery["CONFIG"]["MAIN"]["OLD_SETTINGS"]));
            }
        }

        if (isset($deliveryPropArr[$deliveryId][$paramName])) {
            return $deliveryPropArr[$deliveryId][$paramName];
        }
    }


    public static function deliveryConfigValue($param = null, $delivery_id = null)
    {
        if (!self::$_deliveryConfig && $delivery_id) {

            $delivery = \Bitrix\Sale\Delivery\Services\Table::getById($delivery_id)->fetch();

            $delivery_id = $delivery['PARENT_ID'] ?: $delivery_id;

            $deliveries = [];

            $delivery_list = CSaleDeliveryHandler::GetList(["SORT" => "ASC"], ['ACTIVE' => 'Y', "SID" => "courier"])->arResult;
            foreach ($delivery_list as $item) {
                $deliveries[$item['ID']] = $item;
            }


            if (isset($deliveries[$delivery_id]))
                self::$_deliveryConfig = $deliveries[$delivery_id];
            else
                self::$_deliveryConfig = $delivery_list[0];
        }

        if ($param) {
            return isset(self::$_deliveryConfig['CONFIG']['CONFIG'][$param])
                ? self::$_deliveryConfig['CONFIG']['CONFIG'][$param]['VALUE']
                : self::configValue($param);
        }

        return isset(self::$_deliveryConfig['CONFIG']) ? self::$_deliveryConfig : self::configValue();
    }

    /**
     * Проверка настроек
     */
    public static function configIsIncorrect($config)
    {
        return false;
    }

    /**
     * Взять настойки.
     */
    public static function GetSettings($strSettings)
    {
        return unserialize($strSettings);
    }

    /**
     * Установить настройки.
     */
    public static function SetSettings($arSettings)
    {
        $arSettings['MAP_CLIENT_CODE'] = (new Measoft($arSettings['LOGIN'], $arSettings['PASSWORD'],$arSettings['CODE']))->getMapCode();

        return serialize($arSettings);
    }

    /**
     * Проверка соответствия профиля доставки заказу.
     */
    public static function Compability($arOrder, $arConfig)
    {
        return array("simple", "pickup");
    }

    /**
     * Проверка статуса.
     */
    public static function statusIsIncorrect($statusID, $config)
    {
        return false;
    }

    /**
     * @param $PAY_SYSTEM_ID
     * @return string
     */
    public static function getPayTypeByPaySystemId($PAY_SYSTEM_ID){
        global $DB;
        $results = $DB->Query("SELECT * FROM `measoft_pay_system` WHERE PAYSYSTEM_ID='".$PAY_SYSTEM_ID."'");
        //$measoft_pay_system = [];
        //s($order);
        if($row = $results->Fetch())
        {
            if($row["CASH"])
                return "CASH";

            if($row["CARD"])
                return "CARD";
        }
        return "NO";
    }

    /**
     * Расчет стоимости доставки.
     */
    public static function Calculate($profile, $arConfig, $arOrder = false, $step = false, $temp = false)
    {

        global $DB;
        $settings = MeasoftSingleton::getInstance();
        $PAY_SYSTEM_ID = $settings->getSetting("PAY_SYSTEN_ID");
        //s($PAY_SYSTEN_ID);
        // определение городов отправления и доставки
        $tf = \Bitrix\Sale\Location\LocationTable::getList([
            'filter' => ['=CODE' => $arOrder['LOCATION_FROM'], '=NAME.LANGUAGE_ID' => LANGUAGE_ID],
            'select' => ['ID', 'CITY_NAME' => 'NAME.NAME']
        ])->fetch();
        $townfrom = $tf['CITY_NAME'] ? $tf['CITY_NAME'] : '';

        if(strlen($arOrder['LOCATION_TO']) >= 10 || !is_numeric($arOrder['LOCATION_TO']))
        {
            $cityToArr = $tt = \Bitrix\Sale\Location\LocationTable::getList([
                'filter' => ['=CODE' => $arOrder['LOCATION_TO'], '=NAME.LANGUAGE_ID' => LANGUAGE_ID],
                'select' => ['ID', 'CITY_NAME' => 'NAME.NAME']
            ])->fetch();
        } else
        {
            $cityToArr = $tt = \Bitrix\Sale\Location\LocationTable::getList([
                'filter' => ['=CODE' => $arOrder['LOCATION_TO'], '=NAME.LANGUAGE_ID' => LANGUAGE_ID],
                'select' => ['ID', 'CITY_NAME' => 'NAME.NAME']
            ])->fetch();
        }

        $city_checkbox = $arConfig['SEND_ADDRESS']['VALUE'];
        if ($city_checkbox == 'Y') {
            $request = \Bitrix\Main\Context::getCurrent()->getRequest();
            $request->getPostList()->toArray();


            $prop_code = $arConfig['PROP_ADDRESS']['VALUE'];

            $dbRes = \Bitrix\Sale\Property::getList(
                array(
                    'select' => array('ID'),
                    'filter' => array(
                        'PERSON_TYPE_ID' => $arOrder['PERSON_TYPE_ID'],
                        'CODE' => $prop_code
                    )
                )
            );

            if ($property = $dbRes->fetch())
            {
                $addressto = $request->get("ORDER_PROP_".$property['ID']);
            }

        }



        if (isset($arConfig['CITY_SENDER']['VALUE'])) {
            if ($arConfig['CITY_SENDER']['VALUE']) {
                $townfrom = $arConfig['CITY_SENDER']['VALUE'];
            }
        }

        // пытаемся отправить запрос расчета стоимости доставки
        $measoft = new Measoft($arConfig['LOGIN']['VALUE'], $arConfig['PASSWORD']['VALUE'], $arConfig['CODE']['VALUE']);

        $measoft->getCity($cityToArr);

        $townto = $tt['CITY_NAME'] ? $tt['CITY_NAME'] : $_COOKIE['MS_CITY_NAME'];

        $paytype = self::getPayTypeByPaySystemId($PAY_SYSTEM_ID);

        $pricetype = 'CUSTOMER';

        $weight = $arOrder['WEIGHT'] / 1000;

        $pvz = '';

        $PERSON_TYPE = $arOrder['PERSON_TYPE_ID'];
        if($PERSON_TYPE){
            $propsIDS = MeasoftEvents::getMeasoftProps($PERSON_TYPE);

            $request = Application::getInstance()->getContext()->getRequest();


			$cacheDeliveryId = self::genDeliveryCacheId($arConfig['LOGIN']['VALUE'], $arConfig['PASSWORD']['VALUE'], $arConfig['CODE']['VALUE'], $arConfig['DELIVERY_SERVICE']['VALUE']);

			$session = Application::getInstance()->getSession();
			$measoftCacheDeliveryId = $session->get('measoftCacheDeliveryId');

            if($propsIDS['PVZ_CODE'] && (!empty($measoftCacheDeliveryId)) && $measoftCacheDeliveryId == $cacheDeliveryId) {
                $orderData = $request->getPostList()->toArray();
				!isset($orderData['order']) ?: $orderData=$orderData['order'];
				$pvz =  $orderData["ORDER_PROP_".$propsIDS['PVZ_CODE']];
            }
        }

        $priceArr = $measoft->calculatorRequest(array(
            'townfrom' => $townfrom,
            'paytype' => $paytype,
            'townto' => $townto,
            'mass' => $weight,
            'service' => $arConfig['DELIVERY_SERVICE']['VALUE'],
            'pvz' => ($profile == 'pickup' ? $pvz : ''),
            'price' => $arOrder['PRICE'],
            'inshprice' => $arOrder['PRICE'],
            'pricetype' => $pricetype,
            'addressto' => $addressto,
        ));

        $orderPrice = $arOrder['PRICE'];

        if ($priceArr) {
            $attributes = $priceArr->attributes();
            $price = $attributes['price'];
        } else {
            $price = false;
        }

        // расчет стоимости доставки с учетом ценовой политики магазина
        if ($price != false) {
            for ($i = 1; $i <= 3; $i++) {
                $min = is_numeric($arConfig['PRICE_IF_' . $i . '_MIN']['VALUE']) ? (int)$arConfig['PRICE_IF_' . $i . '_MIN']['VALUE'] : null;
                $max = is_numeric($arConfig['PRICE_IF_' . $i . '_MAX']['VALUE']) ? (int)$arConfig['PRICE_IF_' . $i . '_MAX']['VALUE'] : null;
                $type = $arConfig['PRICE_IF_' . $i . '_TYPE']['VALUE'];
                $aomunt = $arConfig['PRICE_IF_' . $i . '_AOMUNT']['VALUE'];

                if (($min != null && $max != null && $orderPrice > $min && $orderPrice < $max)
                    || ($min != null && $max == null && $orderPrice > $min)
                    || ($min == null && $max != null && $orderPrice < $max)
                ) {
                    switch ($type) {
                        case 1:
                            $price = $aomunt ? $aomunt : $price;
                            break;
                        case 2:
                            $price = 0;
                            break;
                        case 3:
                            $price = round($price - $price * $aomunt / 100);
                            break;
                        case 4:
                            $price = ($price > $aomunt) ? round($price - $aomunt) : 0;
                            break;
                    }
                }
            }
        }

        if ($measoft->errors) {
			if(!is_array($measoft->errors)) $measoft->errors = [$measoft->errors];
            $measoft->errors = implode(';<br>', $measoft->errors);
            return array("RESULT" => "ERROR", 'TEXT' => $measoft->errors);
        } else {

            $deliveryMinDate = self::xml_value($priceArr, 'mindeliverydate');

            if ( COption::GetOptionString("measoft_courier","measoft_check_fill_deliverydate")=='Y' && is_numeric(COption::GetOptionString("measoft_courier","measoft_check_fill_deliverydate_hour")) )
            {

                if (intval(date("H")) >= (int)COption::GetOptionString("measoft_courier","measoft_check_fill_deliverydate_hour"))
                {
                     if (is_numeric(COption::GetOptionString("measoft_courier","ADD_DELIVERTY_DAYES_COUNT"))) {
                        $daysToAdd = (int)COption::GetOptionString("measoft_courier","ADD_DELIVERTY_DAYES_COUNT");
                        if($daysToAdd > 0){
                            $modifyDeliveryMinDate = new DateTime($deliveryMinDate);
                            $modifyDeliveryMinDate->modify("+".$daysToAdd." day");
                            $deliveryMinDate = $modifyDeliveryMinDate->format("Y-m-d");
                        }
                    }
                }
            }

            $d_date_diff = date_diff(new DateTime( date('d.m.Y') ), new DateTime($deliveryMinDate));
            $minDays = $d_date_diff->days;
            $daysC = $minDays . " " . self::plural_form($minDays, GetMessage("MEASOFT_1_DAY"), GetMessage("MEASOFT_2_DAY"), GetMessage("MEASOFT_5_DAY"));
            return array("RESULT" => "OK", 'VALUE' => $price, 'TRANSIT' => $daysC,"RECOMEND_DATE" => $priceArr["mindeliverydate"] ,"TEXT" => strval($priceArr->mindeliverydate));
        }

    }

    public static function getServiceList()
    {
        static $serviceList = [];

        if (count($serviceList) == 0) {
            $result = \Bitrix\Sale\Delivery\Services\Table::getList(array(
                'select' => ["ID", "CODE"],
                'filter' => array('ACTIVE' => 'Y', "CODE" => ["courier:simple", "courier:pickup"]),
            ));

            while ($delivery = $result->fetch()) {
                $serviceList[$delivery["ID"]] = $delivery["CODE"];
            }
        }

        return $serviceList;
    }

    /**
     * Событие происходит когда выбирается служба доставки.
     */
    public static function OnSaleComponentOrderOneStepDelivery(&$arResult, &$arUserResult, $arParams)
    {

        if (empty($arResult["DELIVERY"])) return;
        $serviceList = self::getServiceList();
        if (is_array($arResult["DELIVERY"])) {
            foreach ($arResult["DELIVERY"] as $id => $delivery) {
                if (isset($serviceList[$id])) {

                    $arResult["DELIVERY"][$id]['DESCRIPTION'] = self::getDeliveryDescription(
                        $serviceList[$id],
                        $id,
						$arResult["DELIVERY"][$id],
                        $arUserResult
                    );
                    unset($arResult["DELIVERY"][$id]["CALCULATE_DESCRIPTION"]);
                }
            }
        }
    }


    /**
     * Событие происходит после создания заказа и всех его параметров.
     */
    public static function OnSaleComponentOrderOneStepComplete($orderId, $order)
    {
        global $APPLICATION;


    }


    /**
     * @param string $serviceCode
     * @param integer $profileId
     * @param array $arResultDelivery
     * @param array $arUserRseult
     * @return mixed
     */

    protected static function getDeliveryDescription($serviceCode, $profileId, $arResultDelivery = array(), &$arUserRseult = array())
    {
        $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
        $weight = $basket->getWeight();
        $price = $basket->getPrice();

        $db_props = CSaleOrderProps::GetList(
            array("SORT" => "ASC"),
            array(
                "PERSON_TYPE_ID" => $arUserRseult['PERSON_TYPE_ID'],
                "IS_LOCATION" => "Y"

            ),
            false,
            false,
            array('ID')
        );

        if ($props = $db_props->Fetch())
        {
            $propLocationId = $props['ID'];
        }

        $location = $arUserRseult['ORDER_PROP'][$propLocationId];


        $minDateStr = MeasoftEvents::getDeliveryMinDate($profileId, $location, $price, $weight, $serviceCode);

        $serviceCodeArr = explode(":", $serviceCode);
        ob_start();

        $GLOBALS["measoft"]["profileId"] = $profileId;

        require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/measoft.courier/{$serviceCodeArr[1]}.php");
        $content = ob_get_clean();
        return str_replace(array("\n", "\r"), array(' ', ''), $content);
    }



    public static function getDeliveryMinDate($shipmentCode, $location, $price, $weight, $serviceCode)
    {

        if(strlen($location) >= 10 || !is_numeric($location))
        {
            $res = \Bitrix\Sale\Location\LocationTable::getList([
                'filter' => ['=CODE' => $location, '=NAME.LANGUAGE_ID' => LANGUAGE_ID],
                'select' => ['ID', 'LOCATION_NAME' => 'NAME.NAME']
            ])->fetch();
        }else
        {
            $res = Sale\Location\LocationTable::getList([
                'filter' => ['=CODE' => $location, '=NAME.LANGUAGE_ID' => LANGUAGE_ID],
                'select' => ['ID', 'LOCATION_NAME' => 'NAME.NAME']
            ])->fetch();
        }

        $townto = $res["LOCATION_NAME"];

        MeasoftLoghandler::log("\n\n\n townto::" . print_r($res, true));


        if ($CITY_SENDER = MeasoftEvents::configValueEx('CITY_SENDER', $shipmentCode)) {
            $townfrom = $CITY_SENDER;

        } else {
            $location = Option::get("sale", "location");

            $res = Sale\Location\LocationTable::getList([
                'filter' => ['=CODE' => $location, '=NAME.LANGUAGE_ID' => LANGUAGE_ID],
                'select' => ['ID', 'LOCATION_NAME' => 'NAME.NAME']
            ])->fetch();


            $townfrom = $res["LOCATION_NAME"];
        }

        $city_checkbox = MeasoftEvents::configValueEx('SEND_ADDRESS', $shipmentCode);

        $addressto='';
        if ($city_checkbox == 'Y') {
            $request = \Bitrix\Main\Context::getCurrent()->getRequest();
            $request->getPostList()->toArray();

            $propAddr = MeasoftEvents::deliveryConfigValue('PROP_ADDRESS', $shipmentCode);
            $props = CSaleOrderProps::GetList(array(),array('CODE' => $propAddr));

            while($prop=$props->Fetch()) {
                $propAddr = $request->get("ORDER_PROP_".$prop['ID']);
                if($propAddr) {
                    $addressto = $propAddr;
                    break;
                }
            }
        }

		$login = MeasoftEvents::configValueEx('LOGIN', $shipmentCode);
		$password = MeasoftEvents::configValueEx('PASSWORD', $shipmentCode);
		$extra = MeasoftEvents::configValueEx('CODE', $shipmentCode);
		$service = MeasoftEvents::configValueEx('DELIVERY_SERVICE', $shipmentCode);

        $measoft = new Measoft(
            $login,
            $password,
            $extra
        );


        $paytype = self::getPayTypeByPaySystemId($shipmentCode);

        $pricetype = 'CUSTOMER';

        $pvz = '';
        $request = Application::getInstance()->getContext()->getRequest();
        $orderData = $request->getPost("order");
        $PERSON_TYPE = $orderData['PERSON_TYPE'];
        if($PERSON_TYPE){

            $propsIDS = MeasoftEvents::getMeasoftProps($PERSON_TYPE);

			$cacheDeliveryId = self::genDeliveryCacheId($login, $password, $extra, $service);

			$session = Application::getInstance()->getSession();
			$measoftCacheDeliveryId = $session->get('measoftCacheDeliveryId');

            if($propsIDS['PVZ_CODE'] && (!empty($measoftCacheDeliveryId)) && $measoftCacheDeliveryId == $cacheDeliveryId) {
                $pvz = $orderData["ORDER_PROP_".$propsIDS['PVZ_CODE']];
            }

        }

        $priceArr = $measoft->calculatorRequest(array(
            'townfrom' => $townfrom,
            'paytype' => $paytype,
            'townto' => $townto,
            'mass' => $weight / 1000,
            'service' => $service,
            'pvz' => ($serviceCode == 'courier:pickup' ? $pvz : ''),
            'price' => $price,
            'inshprice' => $price,
            'pricetype' => $pricetype,
            'addressto' => $addressto,
        ));


        $deliveryMinDate = self::xml_value($priceArr, 'mindeliverydate');

        if ( COption::GetOptionString("measoft_courier","measoft_check_fill_deliverydate")=='Y' && is_numeric(COption::GetOptionString("measoft_courier","measoft_check_fill_deliverydate_hour")) )
        {
            if (intval(date("H")) >= (int)COption::GetOptionString("measoft_courier","measoft_check_fill_deliverydate_hour"))
            {
                if (is_numeric(COption::GetOptionString("measoft_courier","ADD_DELIVERTY_DAYES_COUNT"))) {
                    $daysToAdd = (int)COption::GetOptionString("measoft_courier","ADD_DELIVERTY_DAYES_COUNT");
                    if($daysToAdd > 0){
                        $modifyDeliveryMinDate = new DateTime($deliveryMinDate);
                        $modifyDeliveryMinDate->modify("+".$daysToAdd." day");
                        $deliveryMinDate = $modifyDeliveryMinDate->format("Y-m-d");
                    }
                }
            }
        }
        return $deliveryMinDate;
    }

	//generate cache deliveryId
	public static function genDeliveryCacheId($login, $password, $code, $service)
    {
        $strArr = [
		$login,
        $password,
		$code,
        $service,
		  ];
		$cacheId = md5(implode('MEASOFT', $strArr));
		return $cacheId;
    }



    /** Получение xml атрибута
     * @param $object
     * @param $attribute
     * @return string
     */
    public static function xml_value($object, $attribute)
    {
        return (string) $object->$attribute[0];
    }

    /**
     * Описание службы доставки.
     * (Дополнительный метод)
     */
    public static function getOrderArray($order, $props)
    {
        if (!$order || !$props) {
            return false;
        }

        $propCompany = MeasoftEvents::configValueEx('PROP_COMPANY', $order['DELIVERY_ID']);
        $propFio = MeasoftEvents::configValueEx('PROP_FIO', $order['DELIVERY_ID']);
        $propCity = MeasoftEvents::configValueEx('PROP_CITY', $order['DELIVERY_ID']);
        $propAddress = MeasoftEvents::configValueEx('PROP_ADDRESS', $order['DELIVERY_ID']);
        $propPhone = MeasoftEvents::configValueEx('PROP_PHONE', $order['DELIVERY_ID']);
        $propZIP = MeasoftEvents::configValueEx('PROP_ZIP', $order['DELIVERY_ID']);
        $prefix = MeasoftEvents::configValueEx('ORDER_PREFIX', $order['DELIVERY_ID']);
        $orderNumber = MeasoftEvents::configValueEx('ORDER_NUMBER', $order['DELIVERY_ID']);



        if (MeasoftEvents::configValueEx('PROP_ZIP', $order['DELIVERY_ID']) == "Y")
        {
            $propZIP = '';
        }

        if (is_numeric($props[$propCity])) {
            $res = \Bitrix\Sale\Location\LocationTable::getList([
                'filter' => ['=CODE' => $props[$propCity], '=NAME.LANGUAGE_ID' => LANGUAGE_ID],
                'select' => ['ID', 'CITY_NAME' => 'NAME.NAME']
            ])->fetch();


            if (\MeasoftEvents::isCp1251Site())
            {
                $recipientCity = $GLOBALS['APPLICATION']->ConvertCharset($res['CITY_NAME'], 'UTF-8', SITE_CHARSET);
            } else{
                $recipientCity = $res['CITY_NAME'];
            }

        } else {
            $recipientCity = (isset($props[$propCity]) && $props[$propCity]) ? $props[$propCity] : $order['CITY'];
            if (empty($recipientCity)) {
                $res = \Bitrix\Sale\Location\LocationTable::getList([
                    'filter' => ['=CODE' => $props["LOCATION"], '=NAME.LANGUAGE_ID' => LANGUAGE_ID],
                    'select' => ['ID', 'CITY_NAME' => 'NAME.NAME']
                ])->fetch();
                $recipientCity = $res['CITY_NAME'];
            }
        }

        /*$instruction = $order['USER_DESCRIPTION'];

        if ($order['COMMENTS']) {
            if ($instruction)
                $instruction .= '; ';

            $instruction .= $order['COMMENTS'];
        }*/
        if ($props['MEASOFT_INSTRUCTION']) {
            if ($instruction)
                $instruction .= '; ';

            $instruction .= $props['MEASOFT_INSTRUCTION'];
        }

        //$paytype = $order['PAYED'] == 'Y' ? 'NO' : ($order['PAY_SYSTEM_ID'] == self::configValueEx('PAYTYPE_CARD', $order['DELIVERY_ID']) ? 'CARD' : 'CASH');
        $paytype = self::getPayTypeByPaySystemId($order['PAY_SYSTEM_ID']);
        return array(
            'sender' => self::getSender(),
            'orderno' => !$orderNumber ? $prefix . $order['ID'] : $order['ACCOUNT_NUMBER'],
//            'barcode' => $order[''],
            'company' => $props[$propCompany],
            'person' => $props[$propFio],
            'phone' => $props[$propPhone],
            'town' => $recipientCity,
            'address' => $props[$propAddress],
            'zipcode' => $props[$propZIP],
            'date' => ConvertDateTime($props['DELIVERY_PLANNED'], "YYYY-MM-DD"),
            'time_min' => $props['DELIVERY_FROM'],//$props['MEASOFT_TIME_MIN'],
            'time_max' => $props['DELIVERY_TO'] ,//$props['MEASOFT_TIME_MAX'],
            'pvz_code' => $props['PVZ_CODE'],
//            'weight' => $order[''],
//            'quantity' => $order[''],
            'price' => $order['PRICE'],
            'price_paid' => $order['SUM_PAID'],
            'inshprice' => $order['PRICE'],
            'paytype' => $paytype,
            'service' => MeasoftEvents::configValueEx('DELIVERY_SERVICE', $order['DELIVERY_ID']),
            'enclosure' => '',
            'instruction' => $instruction,
        );
    }

    public static function getPropValue($product,$PROP_CODE_TMP){
        $propCodeArray = explode("@",$PROP_CODE_TMP);
        $propCode = $propCodeArray[0];
        $iblockId = $propCodeArray[1];

        if($product["IBLOCK_ID"] == $iblockId){
            return $product["PROPERTIES"][$propCode]["VALUE"];
        }else{ // Товар является ТП
            $iblockRes = CCatalogSku::GetInfoByIBlock(
                $product["IBLOCK_ID"]
            );
            if($iblockRes["PRODUCT_IBLOCK_ID"] && $iblockRes["SKU_PROPERTY_ID"]){
                $skuPropRes = \CIBlockProperty::GetByID($iblockRes["SKU_PROPERTY_ID"]);
                if($skuPropArray = $skuPropRes -> fetch()){
                    if($mainProductId = $product["PROPERTIES"][$skuPropArray["CODE"]]["VALUE"]){
                        $iterator = CIBlockElement::GetProperty($iblockRes["PRODUCT_IBLOCK_ID"], $mainProductId, [],["CODE" => $propCode]);
                        if ($row = $iterator->Fetch())
                        {
                            return $row["VALUE"];
                        }
                    }

                }

            }
        }
        return "";
    }


    /** Получаем массив штрих кодов и маркировочных кодов
     * @param Bitrix\Sale\Order $order
     * @return array
     */
    public static function getItemMarketingCodes($saleOrder) {
        $marketingCodes = array();
        $shipmentCollection = $saleOrder->getShipmentCollection();
        foreach($shipmentCollection as $shipment) {
            $collection = $shipment->getShipmentItemCollection();
            $link = Link::getInstance();
            $factory = FactoryMode::create($link->getType());
            $shipmentOrderBasket = $factory::create(BlockType::SHIPMENT_BASKET, [
                'shipment'=>$shipment
            ]);

            $productInfo = $shipmentOrderBasket->getProductsInfo($collection);

            foreach($productInfo['ITEMS'] as $product){
                $arBarcode = array_shift($product['BARCODE_INFO']);

                $productId = ($product['OFFER_ID'] > 0) ? $product['OFFER_ID'] : $product['PRODUCT_ID'];
                if(!$marketingCodes[$productId]) {
                    $marketingCodes[$productId] = $arBarcode;
                } else{
                    $marketingCodes[$productId] = array_merge($marketingCodes[$product['PRODUCT_ID']], $arBarcode);
                }
            }
        }
        return $marketingCodes;
    }



    /**
     * Получаем массив вложения.
     * (Дополнительный метод)
     */
    public static function getItemsArray($items, $order = null, $delivery_id = null)
    {
        if (!$items) {
            return false;
        }

        $shipping = $order->getDeliveryPrice();
        $governmentCode = self::getItemMarketingCodes($order);
		$gcode = trim(self::configValueEx('DEFAULT_GCODE', $delivery_id));
        $result = array();
        foreach ($items as $item) {

            $article = '';

            $product = CCatalogProduct::GetByIDEx($item['PRODUCT_ID']);
            if (isset($product['PROPERTIES']['ARTNUMBER']['VALUE'])) {
                $article = $product['PROPERTIES']['ARTNUMBER']['VALUE'];
            }

            $item_barcode = self::getPropValue($product,self::configValueEx('PROP_ITEM_BARCODE', $delivery_id));

            if($governmentCode[$item['PRODUCT_ID']]) {

            }
            if($governmentCode[$item['PRODUCT_ID']]) {
                foreach ($governmentCode[$item['PRODUCT_ID']] as $itemGovernmentCode) {
                    $result[] = array(
                        'name' => $item['NAME'],
                        'quantity' => $itemGovernmentCode['QUANTITY'],
                        'mass' => $item['WEIGHT'] / 1000,
                        'retprice' => $item['PRICE'],
                        'VATrate' => (int)round($item['VAT_RATE'] * 100, 0),
                        'article' => self::configValueEx('USE_ARTICLES', $delivery_id) == 'Y' ? $article : '',
                        'barcode' => $item_barcode,
                        'governmentCode' => $itemGovernmentCode['MARKING_CODE']
                    );
                }
            } else {
                $obPropRes = CIBlockElement::GetByID($item['PRODUCT_ID'])->GetNextElement();
                unset($prop);
                if ($obPropRes)
                    $prop = $obPropRes->GetProperties();
                $result[] = array(
                    'name' => $item['NAME'],
                    'quantity' => $item['QUANTITY'],
                    'mass' => $item['WEIGHT'] / 1000,
                    'retprice' => $item['PRICE'],
                    'VATrate' => (int)round($item['VAT_RATE'] * 100, 0),
                    'article' => self::configValueEx('USE_ARTICLES', $delivery_id) == 'Y' ? $article : '',
                    'barcode' => $item_barcode,
                    'governmentCode' => (!empty($gcode) && $prop && is_array($prop) && isset($prop[$gcode]) && isset($prop[$gcode]['VALUE'])) ? $prop[$gcode]['VALUE'] : '',
                );
            }
        }
        // включение доставки в список вложений
        if ($shipping) {
            $result[] = array(
                'name' => GetMessage("MEASOFT_COURIER_DELIVERY_NAME"),
                'quantity' => 1,
                'mass' => 0,
                'retprice' => $shipping,
                'VATrate' => self::configValueEx('DELIVERY_VATRATE', $delivery_id),
                'article' => '',
                'is_delivery' => 1,
            );
        }

        return $result;
    }

    /**
     * Установка значения доп свойства заказа.
     */
    public static function setOrderPropsValue($orderId, $code, $value)
    {
        if ($arProp = CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $orderId, 'CODE' => $code))->Fetch()) {
            return CSaleOrderPropsValue::Update($arProp['ID'], array(
                'VALUE' => $value,
            ));
        } else {
            $arProp = CSaleOrderProps::GetList(array(), array('CODE' => $code))->Fetch();
            return CSaleOrderPropsValue::Add(array(
                'NAME' => $arProp['NAME'],
                'CODE' => $arProp['CODE'],
                'ORDER_PROPS_ID' => $arProp['ID'],
                'ORDER_ID' => $orderId,
                'VALUE' => $value,
            ));
        }
    }

    public static function getOrderPropsValue($orderId, $code, $value)
    {
        static $propList = [];

        if (!$propList) {
            $res = CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $orderId, 'CODE' => $code));
            while ($arProp = $res->Fetch()) {
                $propList[$arProp["CODE"]] = $arProp;
            }
        }

        print_r($propList);

    }

    /**
     * Возвращает название CMS и Bitrix версию.
     * (Дополнительный метод)
     */
    protected static function getSender()
    {
        $module = CModule::CreateModuleObject('measoft.courier');

        return array(
            'module' => Measoft::BITRIX,
            'cms_version' => defined('SM_VERSION') ? SM_VERSION : '',
            'module_version' => $module ? $module->MODULE_VERSION : '1.5.10'
        );
    }


    public static function searchCity($cityName)
    {
        $result = \Bitrix\Sale\Delivery\Services\Table::getList(array(
            'select' => ["ID", "CODE"],
            'filter' => array('ACTIVE' => 'Y', "CODE" => ["courier"]),
        ));
		$delivery=false;
		while ($currProfile = $result->fetch()) {
                if ($currProfile["CODE"] == "courier")
                {
                    $delivery = $currProfile;
                }
        }


        if ($delivery) {
            $measoft = new Measoft(self::configValueEx('LOGIN', $delivery["ID"]), self::configValueEx('PASSWORD', $delivery["ID"]), self::configValueEx('CODE', $delivery["ID"]));
            return $measoft->searchCity($cityName);
        }
    }

    /**
     * Правильно склоняем слово
     *
     * @param  int  количество единиц, которые надо просклонять
     * @param  string  единственное число (одна единица)
     * @param  string  множественное число (две единицы)
     * @param  string  множественное число (пять единиц)
     *
     * @return  string  верная форма
     */
    public static function plural_form($n, $form1, $form2, $form5)
    {
        $n = abs($n) % 100;
        $n1 = $n % 10;
        if ($n > 10 && $n < 20) return $form5;
        if ($n1 > 1 && $n1 < 5) return $form2;
        if ($n1 == 1) return $form1;
        return $form5;
    }

    /**
     * @return array
     */
    public function getEmbeddedExtraServicesList()
    {
        $result = array(
            "SMS" => array(
                "NAME" => Loc::getMessage('SALE_DLV_SRV_SPSR_SMS'),
                "SORT" => 100,
                "RIGHTS" => "NYN",
                "ACTIVE" => "Y",
                "CLASS_NAME" => '\Bitrix\Sale\Delivery\ExtraServices\Checkbox',
                "DESCRIPTION" => Loc::getMessage('SALE_DLV_SRV_SPSR_SMS_DESCR'),
                "INIT_VALUE" => "N",
                "PARAMS" => array("PRICE" => 0)
            ),
            "SMS_RECV" => array(
                "NAME" => Loc::getMessage('SALE_DLV_SRV_SPSR_SMS_RECV'),
                "SORT" => 100,
                "RIGHTS" => "NYY",
                "ACTIVE" => "Y",
                "CLASS_NAME" => '\Bitrix\Sale\Delivery\ExtraServices\Checkbox',
                "DESCRIPTION" => Loc::getMessage('SALE_DLV_SRV_SPSR_SMS_RECV_DESCR'),
                "INIT_VALUE" => "Y",
                "PARAMS" => array("PRICE" => 0)
            ),
        );

        return $result;
    }


    /**
     * @param $arFields
     * @return Main\EventResult|bool
     */
    public static function OnBeforeOrderAdd(&$arFields)
    {
        global $APPLICATION;

        if (isset($_REQUEST['DELIVERY_ID']) && $_REQUEST['DELIVERY_ID'] != 'courier:simple') {
            return true;
        }

        return new \Bitrix\Main\EventResult(
            \Bitrix\Main\EventResult::ERROR,
            new \Bitrix\Sale\ResultError('Необходимо выбрать пункт выдачи!', 'PVZ_NOT_FOUND'),
            'sale'
        );

        return false;
    }

    static function OnSaleOrderSaved(Bitrix\Main\Event $event)
    {
        /** @var Order $order */
//        $order = $event->getParameter("ENTITY");
//        $oldValues = $event->getParameter("VALUES");
//        $isNew = $event->getParameter("IS_NEW");
//
//        if ($isNew)
//        {
//
//        }
    }

	static function OnSaleStatusOrder($event)
    {

	}
    static function StatusUpdate(Bitrix\Main\Event $event)
    {

        $order = $event->getParameter("ENTITY");

        $newStatus = $event->getParameter("VALUE");

        $orderId = $order->Getid();

        if (MeasoftEvents::orderIsMeasoft($orderId, $order)) {
            $orderArr = CSaleOrder::GetByID($orderId);

            $deliveryId = $order->getField("DELIVERY_ID");

            $orderArr['DELIVERY_ID'] = $deliveryId;

            $sendOrderStatusFlag = self::configValueEx('SEND_STATUS_'.$newStatus, $deliveryId);

            if($sendOrderStatusFlag == "Y"){ // в новой версии модуля есть конфиг для каждого статуса отдельно
                $sendOrderStatus = $newStatus;
            }else{
                $sendOrderStatus = NULL;
            }

            MeasoftLoghandler::log("  \n\n StatusUpdate {$sendOrderStatus} == {$newStatus} deliveryId=$deliveryId");

            if ($sendOrderStatus == $newStatus) {
                //MeasoftLoghandler::log(  "  \n\n sendingOrder ID=$orderId::". print_r($order, true)  );

                // получение вложений заказа
                $dbItems = CSaleBasket::GetList(array(), array('ORDER_ID' => $orderId), false, false, array());
                $items = array();
                while ($item = $dbItems->Fetch()) {
                    $items[] = $item;
                }

                // получаем поля покупателя
                $props = [];
                foreach ($order->getPropertyCollection() as $propValue) {
					if($propValue->getPropertyObject()->getPersonTypeId() == $order->getPersonTypeId()){
						$propValFields = $propValue->getFieldValues();
						$props[$propValFields['CODE']] = $propValFields['VALUE'];
					}
                }

                //MeasoftLoghandler::log("  \n\n sendingOrder ID=$orderId::" . print_r($orderArr, true) . "\n\n props::" . print_r($props, true));


                // отправка заказа, если статус отправки "Принят, ожидается оплата"
                $measoft = new Measoft(self::configValueEx('LOGIN', $deliveryId), self::configValueEx('PASSWORD', $deliveryId), self::configValueEx('CODE', $deliveryId));

                $measoft->orderId = $order->getId();
                $measoft->orderRequest(
                    self::getOrderArray($orderArr, $props),
                    self::getItemsArray($items, $order, $deliveryId)
                );

            }
        }
    }

    static function SaleCancelOrder(Bitrix\Main\Event $event)
    {
        $order = $event->getParameter("ENTITY");

        $orderId = $order->Getid();

        if (MeasoftEvents::orderIsMeasoft($orderId, $order)) {
            $deliveryId = $order->getField("DELIVERY_ID");

            $orderArr = CSaleOrder::GetByID($orderId);
            $orderArr["DELIVERY_ID"] = $deliveryId;

            $measoft = new Measoft(self::configValueEx('LOGIN', $deliveryId), self::configValueEx('PASSWORD', $deliveryId), self::configValueEx('CODE', $deliveryId));
            $measoft->CancelOrder($orderArr);

        }


    }

    public static function loadComponent($arParams = array())
    {
        if (!is_array($arParams))
            $arParams = array();


        $GLOBALS['APPLICATION']->IncludeComponent("measoft.courier:pickup", "", [], false);

    }

    /**
     * @param Bitrix\Sale\Order $order
     * @param object $newDateDelivery
     * @return bool
     * @throws Exception
     */

    public static function getOrderDeliveryDate($order)
    {
        $basket = $order->getBasket();
        $weight = $basket->getWeight();
        $price = $basket->getPrice();
        $shipmentId = $order->getField("DELIVERY_ID");

        /** @var \Bitrix\Sale\PropertyValueCollection $propertyCollection */
        $propertyCollection = $order->getPropertyCollection();

        $location = $propertyCollection->getDeliveryLocation()->getValue();

        $propArr = [];
		foreach ($propertyCollection as $propertyItem)
        {
            $propertyItemCode = $propertyItem->getField("CODE");
			$propertyItemValue = $propertyItem->getValue();
			is_array($propertyItemValue) ?: $propertyItemValue = trim($propertyItemValue);
            $propArr[ $propertyItemCode ] = $propertyItemValue;
        }

        $deliveryMinDate = self::getDeliveryMinDate($shipmentId, $location, $price, $weight);


        $minDateFormatted = date('d.m.Y', strtotime($deliveryMinDate));

        $d_date_diff = date_diff(new DateTime($minDateFormatted), new DateTime($propArr["MEASOFT_DATE_PUTN"]));

        MeasoftLoghandler::log( "\n\n d_date_diff::". print_r($d_date_diff, true) );

        if ($d_date_diff->days >= 0 && $d_date_diff->invert == 0)
        {
            $result = array(
                'MIN_DATE' => $minDateFormatted,
                'DATE_ALLOW' => true
            );
        } else {
            // выбранная дата доставки в прошлом
            $result = array(
                'MIN_DATE' => $minDateFormatted,
                'DATE_ALLOW' => false
            );
        }
        return $result;
    }

    public static function getPvzInfo($LOCATION_NAME, $pvzCode, $paySystemId)
    {
        $measoft = new Measoft(MeasoftEvents::configValueEx('LOGIN', $paySystemId), MeasoftEvents::configValueEx('PASSWORD', $paySystemId), MeasoftEvents::configValueEx('CODE', $paySystemId));
        return $measoft->getPVZ( $pvzCode);
    }

    public static function checkPvzPayment($LOCATION_NAME, $pvzCode, $paySystemArr, $deliveryId)
    {
        global $DB;

        $paySystemId = $paySystemArr[0];

        $res = ["res" => false, "error_txt" => ""];

        $pvzInfoArr = MeasoftEvents::getPvzInfo($LOCATION_NAME, $pvzCode, $deliveryId);

        $paySystemChechArr = $DB->Query("SELECT * FROM `measoft_pay_system` WHERE `PAYSYSTEM_ID`={$paySystemId}", false, "File: " . __FILE__ . "<br>Line: " . __LINE__)->fetch();

        if ($paySystemChechArr["ID"]) {

            if ($paySystemChechArr["CARD"]) {
                $res["res"] = ($pvzInfoArr["acceptcard"] == "YES");

                if (!$res["res"]) {
                    $res["error_txt"] = GetMessage("MEASOFT_PVZ_PAYMENT_ERR_CARD");
                }
            }

            if (!$res["res"]) {
                if ($paySystemChechArr["CASH"]) {
                    $res["res"] = ($pvzInfoArr["acceptcash"] == "YES");

                    if (!$res["res"]) {
                        $res["error_txt"] = GetMessage("MEASOFT_PVZ_PAYMENT_ERR_CASH");
                    }
                }
            }


        } else {

            // старые настройки типа оплаты
            $PAYTYPE_CARD = MeasoftEvents::configValueEx('PAYTYPE_CARD', $deliveryId);

            if (in_array($PAYTYPE_CARD, $paySystemArr)) {
                // оплата картой
                $res["res"] = ($pvzInfoArr["acceptcard"] == "YES");

                if (!$res["res"]) {
                    $res["error_txt"] = GetMessage("MEASOFT_PVZ_PAYMENT_ERR_CARD");
                }
            } else {
                // разрешаем оплату если доставки нет в настройках
                $res["res"] = true;
            }
        }

        if ($res["res"]) {
            $res["error_txt"] = "";
        }

        return $res;
    }

    public static function orderIsMeasoft($orderId, $order = null)
    {
        if ($orderId) {
            $order = Sale\Order::load($orderId);
        }


        if (isset($order)) {
            $deliveryId = $order->getField("DELIVERY_ID");

            if ($deliveryId) {
                $currProfile = \Bitrix\Sale\Delivery\Services\Table::getList(array(
                    'filter' => array("ID" => $deliveryId),
                    'select' => ['CODE']
                ))->fetch();

                return
                    ($currProfile["CODE"] == "courier:pickup") ||
                    ($currProfile["CODE"] == "courier:simple") ||
                    ($currProfile["CODE"] == "courier");
            }
        }
    }

    public static function getMessageLang($message)
    {
        IncludeModuleLangFile(__FILE__);

        return GetMessage($message);
    }


    public static function log($str)
    {
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/upload/measoft.log', "\n\n" . date('d.m.Y H:i:s') . '  ' . $str, FILE_APPEND);
    }


    /* агент для проверки статуса доставки заказа и синхронизации статуса заказа в битриксе */
    public static function ordersSyncAgent()
    {
        if(COption::GetOptionString("measoft_courier","measoft_sync_disable") != 'Y')
        {
            //$measoft = new Measoft(self::configValue('LOGIN'), self::configValue('PASSWORD'), self::configValue('CODE'));

            $res = \Bitrix\Sale\Delivery\Services\Table::getList(array('filter' => array('ACTIVE' => 'Y', 'CODE' => 'courier')))->fetchAll();


            $settings = unserialize( unserialize( $res[0]["CONFIG"]["MAIN"]["OLD_SETTINGS"]) );

            $measoft = new Measoft($settings['LOGIN'], $settings['PASSWORD'], $settings['CODE']);
            //print_r($res);
            $measoft->getLastOrderStatuses((int)COption::GetOptionString("measoft_courier","measoft_sync_order_cnt"));

            //file_put_contents('/home/c/ck31528/shop/public_html/measoft_agent.log', "\n\n ". date("d.m.Y H:i:s") .". agent exec ", FILE_APPEND );
        }

        return "MeasoftEvents::ordersSyncAgent();";
    }



    public static function checkDeliveryTime($order)
    {

        /** @var \Bitrix\Sale\PropertyValueCollection $propertyCollection */
        $propertyCollection = $order->getPropertyCollection();

        $propArr = [];
		foreach ($propertyCollection as $propertyItem)
        {
            $propertyItemCode = $propertyItem->getField("CODE");
			$propertyItemValue = $propertyItem->getValue();
			is_array($propertyItemValue) ?: $propertyItemValue = trim($propertyItemValue);
            $propArr[ $propertyItemCode ] = $propertyItemValue;
        }

        $MEASOFT_TIME_MIN = intval( str_replace(':00', '', $propArr["MEASOFT_TIME_MIN"]) );
        $MEASOFT_TIME_MAX = intval( str_replace(':00', '', $propArr["MEASOFT_TIME_MAX"]) );

        return ($MEASOFT_TIME_MIN <= $MEASOFT_TIME_MAX);
    }

    public static function getMapCode($arConfig)
    {
        // LOGIN] => test [PASSWORD] => testm [CODE] => 8 [MAP_CLIENT_CODE]
        $measoft = new Measoft($arConfig['LOGIN'], $arConfig['PASSWORD'], $arConfig['CODE']);

        return $measoft->getMapCode();
    }

    public static function isCp1251Site()
    {
        return ( (LANG_CHARSET != "UTF-8") && (LANG_CHARSET != "utf-8") );
    }

    /**
     * @param $arUserResult
     */
    public static function setSettingsForCalculte($arUserResult,$request,$arParams,$arResult){
        // это для рассчета доставки в зависимости от платежки
        $settings = MeasoftSingleton::getInstance();
        $settings -> setSetting("PAY_SYSTEN_ID",$arUserResult["PAY_SYSTEM_ID"]);
    }

    public static function getDefaultPVZ($shipmentCode){
        $measoft = new Measoft(
            self::configValueEx('LOGIN', $shipmentCode),
            self::configValueEx('PASSWORD', $shipmentCode),
            self::configValueEx('CODE', $shipmentCode)
        );
        $pvzCode =  self::configValueEx('DEFAULT_PICKPOINT', $shipmentCode);
        if($pvzCode){
            $pvzInfo = $measoft->getPVZ( $pvzCode);
            return $pvzInfo;
        }
        return false;
    }

    public static function getMeasoftProps($PERSON_TYPE){
        $_pros = [
            'PVZ_CODE' => '',
            'PVZ_ADDRESS' => '',
            'PVZ_PHONE' => '',
            'PVZ_WORKTIME' => ''
        ];
        $salePropsRes = CSaleOrderProps::GetList(array(), array('CODE' => array_keys($_pros), 'PERSON_TYPE_ID' => $PERSON_TYPE));
        while ($propArr = $salePropsRes->Fetch())
        {
            $_pros[$propArr["CODE"]] = $propArr["ID"];
        }
        return $_pros;

    }
}

use Bitrix\Main;
Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleOrderBeforeSaved',
    'BeforeOrderAdd'
);



function BeforeOrderAdd(Main\Event $event)
{
    /** @var Order $order */
    $order = $event->getParameter("ENTITY");

    if (MeasoftEvents::orderIsMeasoft( $order->getId(), $order ))
    {

        /** @var \Bitrix\Sale\PropertyValueCollection $propertyCollection */
        $propertyCollection = $order->getPropertyCollection();

        $propArr = [];
		foreach ($propertyCollection as $propertyItem)
        {
            $propertyItemCode = $propertyItem->getField("CODE");
			$propertyItemValue = $propertyItem->getValue();
			is_array($propertyItemValue) ?: $propertyItemValue = trim($propertyItemValue);
            $propArr[ $propertyItemCode ] = $propertyItemValue;
        }

        $PVZ_CODE = $propArr["PVZ_CODE"];

        $shipmentCode = $order->getField("DELIVERY_ID");

        $statusId = $order->getField("STATUS_ID");

        $paymentIds = $order->getPaymentSystemId();

        $resultDel = \Bitrix\Sale\Delivery\Services\Table::getList(array(
            'select' => ["ID", "CODE"],
            'filter' => array('ACTIVE' => 'Y', 'ID' => $shipmentCode ),
        ))->fetch();


        if ($resultDel["CODE"] == "courier:simple")
        {
            if ( COption::GetOptionString("measoft_courier","measoft_check_fill_deliverydate")=='Y' )
            {
                if ( empty($propArr["MEASOFT_DATE_PUTN"]) )
                {
                    foreach ($propertyCollection as $propertyItem)
                    {
                        if ($propertyItem->getField("CODE") == "MEASOFT_DATE_PUTN")
                        {
                            $basket = $order->getBasket();
                            $location = $propertyCollection->getDeliveryLocation()->getValue();

                            $day_plus = (intval(date("H")) >= (int)COption::GetOptionString("measoft_courier","measoft_check_fill_deliverydate_hour")) ? 2 : 1;

                            $propArr["MEASOFT_DATE_PUTN"] = MeasoftEvents::getDeliveryMinDate(
                                $shipmentCode,
                                $location,
                                $basket->getPrise(),
                                $basket->getWeight()
                            );

                            //$propArr["MEASOFT_DATE_PUTN"] = MeasoftEvents::getNextWorkDate(["day_plus" => $day_plus]);

                            $propertyItem->setValue( $propArr["MEASOFT_DATE_PUTN"] );
                        }

                    }
                }


            }

            $FIsDelete = false;

            if ( isset($_REQUEST['action']) )
            {
                $FIsDelete = ( $_REQUEST['action'] == 'delete' );
            }



            if ( !$FIsDelete )
            {

                if(COption::GetOptionString("measoft_courier","measoft_check_date_format")=='Y')
                {
                    if (empty($propArr["MEASOFT_DATE_PUTN"]) )
                    {
                        return new \Bitrix\Main\EventResult(
                            \Bitrix\Main\EventResult::ERROR,
                            new \Bitrix\Sale\ResultError( GetMessage("MEASOFT_ORDER_DATE_DELIVERY_EMPTY_ERR") , "" ),
                            'sale'
                        );
                    } else
                        if (preg_match('@^(\d\d).(\d\d).(\d\d\d\d)$@', $propArr["MEASOFT_DATE_PUTN"], $m) == false)
                        {
                            return new \Bitrix\Main\EventResult(
                                \Bitrix\Main\EventResult::ERROR,
                                new \Bitrix\Sale\ResultError( GetMessage("MEASOFT_ORDER_DATE_DELIVERY_FORMAT_ERR") .' v='. $propArr["MEASOFT_DATE_PUTN"], "" ),
                                'sale'
                            );
                        }
                }

                if(COption::GetOptionString("measoft_courier","measoft_check_date_weekend")=='Y')
                {
                    $selDateArr = getdate( strtotime($propArr["MEASOFT_DATE_PUTN"]) );

                    if ( ($selDateArr["wday"] == 0) || ($selDateArr["wday"] == 6) )
                    {
                        return new \Bitrix\Main\EventResult(
                            \Bitrix\Main\EventResult::ERROR,
                            new \Bitrix\Sale\ResultError( GetMessage("MEASOFT_ORDER_DATE_DELIVERY_WEEKEND_ERR"), "" ),
                            'sale'
                        );
                    }

                }

            }

            if ($statusId=="N")
            {
                //$minOdredDeliveryDate = MeasoftEvents::getOrderDeliveryDate($order);

//                if ( !$minOdredDeliveryDate['DATE_ALLOW'] )
//                {
//                    return new \Bitrix\Main\EventResult(
//                        \Bitrix\Main\EventResult::ERROR,
//                        new \Bitrix\Sale\ResultError( GetMessage("MEASOFT_ORDER_DATE_DELIVERY_ERR") . "{$minOdredDeliveryDate['MIN_DATE']}", "" ),
//                        'sale'
//                    );
//                }

                if ( !MeasoftEvents::checkDeliveryTime($order) )
                {
                    return new \Bitrix\Main\EventResult(
                        \Bitrix\Main\EventResult::ERROR,
                        new \Bitrix\Sale\ResultError( GetMessage("MEASOFT_ORDER_TIME_DELIVERY_ERR"), "" ),
                        'sale'
                    );
                }
            }

        } else
            if ($resultDel["CODE"] == "courier:pickup")
            {
                if (!$PVZ_CODE)
                {
                    $defaultPVZ = MeasoftEvents::getDefaultPVZ($shipmentCode);
                    if(!$defaultPVZ){
                        return new \Bitrix\Main\EventResult(
                            \Bitrix\Main\EventResult::ERROR,
                            new \Bitrix\Sale\ResultError( GetMessage("MEASOFT_PVZ_NOT_SELECTED"), 'PVZ_NOT_FOUND'),
                            'sale'
                        );
                    }else{
                        $PERSON_TYPE = $order->getPersonTypeId();
                        if($PERSON_TYPE){
                            $propsIDS = MeasoftEvents::getMeasoftProps($PERSON_TYPE);
                            $propertyCollection = $order->getPropertyCollection();
                            foreach($propsIDS as $propCode => $propId){
                                if($propId){
                                    $somePropValue = $propertyCollection->getItemByOrderPropertyId($propId);
                                    switch($propCode){
                                        case "PVZ_CODE":
                                            $somePropValue->setValue($defaultPVZ["code"]);
                                            break;
                                        case "PVZ_ADDRESS":
                                            $somePropValue->setValue($defaultPVZ["address"]);
                                            break;
                                        case "PVZ_PHONE":
                                            $somePropValue->setValue($defaultPVZ["phone"][0]);
                                            break;
                                        case "PVZ_WORKTIME":
                                            $somePropValue->setValue($defaultPVZ["worktime"]);
                                            break;
                                    }
                                }
                            }
                        }

                    }

                } else
                {

                    $resLocation = \Bitrix\Sale\Location\LocationTable::getList([
                        'filter' => ['=CODE' => $propArr["LOCATION"], '=NAME.LANGUAGE_ID' => LANGUAGE_ID ],
                        'select' => [ 'ID', 'LOCATION_NAME' => 'NAME.NAME' ]
                    ])->fetch();

                    $res = MeasoftEvents::checkPvzPayment($resLocation["LOCATION_NAME"], $PVZ_CODE, $paymentIds, $shipmentCode);

                    if ( !$res["res"] )
                    {
                        return new \Bitrix\Main\EventResult(
                            \Bitrix\Main\EventResult::ERROR,
                            new \Bitrix\Sale\ResultError( $res["error_txt"], 'PVZ_NOT_FOUND'),
                            'sale'
                        );
                    }


                }
            }

    }


}

Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleStatusOrderChange',
    [ 'MeasoftEvents', 'StatusUpdate' ]
);


Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleOrderCanceled',
    [ 'MeasoftEvents', 'SaleCancelOrder' ]
);


Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleComponentOrderOneStepProcess',
    [ 'MeasoftEvents', 'loadComponent' ]
);
