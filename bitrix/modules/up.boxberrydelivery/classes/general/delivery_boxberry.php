<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\EventManager;
use Bitrix\Main\EventResult;
use Bitrix\Main\Grid\Declension;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;
use Bitrix\Sale\Delivery\Services\Manager;
use Bitrix\Sale\Location\Admin\LocationHelper;
use Bitrix\Sale\Location\LocationTable;
use Bitrix\Sale\Location\TypeTable;
use Bitrix\Sale\ResultError;

Loader::includeModule("sale");
Loc::loadMessages(__FILE__);
class CDeliveryBoxberry
{
    public const PICKUP_DELIVERY_TYPE_ID = 1;
    public const COURIER_DELIVERY_TYPE_ID = 2;
    public static $widget = [];
    protected static $regionBitrixName;
    protected static $cityBitrixName;
    protected static $cityWidgetName;
    protected static $parselCreate;
    protected static $moduleId = 'up.boxberrydelivery';
    protected static $locType;
    protected static $isRussia = false;
    protected static $isKz = false;
    private static $isLocationSet = false;
    private static $profiles = [];
    private static $dimensions = [];
    private static $widgetSettings = [];

    public static function Init()
    {
        Loader::includeModule(self::$moduleId);

        if (!CBoxberry::initApi()) {
            return false;
        }

        return array(
            "SID" => "boxberry",
            "NAME" => Loc::getMessage('DELIVERY_NAME'),
            "DESCRIPTION" => "",
            "DESCRIPTION_INNER" => Loc::getMessage('DESCRIPTION_INNER'),
            "BASE_CURRENCY" => Option::get("sale", "default_currency", "RUB"),
            "HANDLER" => __FILE__,
            "DBGETSETTINGS" => array("CDeliveryBoxberry", "GetSettings"),
            "DBSETSETTINGS" => array("CDeliveryBoxberry", "SetSettings"),
            "GETCONFIG" => array("CDeliveryBoxberry", "GetConfig"),

            "COMPABILITY" => array("CDeliveryBoxberry", "Compability"),
            "CALCULATOR" => array("CDeliveryBoxberry", "Calculate"),

            'PROFILES' => array(
                'PVZ' => array(
                    'TITLE' => Loc::getMessage('BOXBERRY_PVZ'),
                    'DESCRIPTION' => "",
                ),
                'KD' => array(
                    'TITLE' => Loc::getMessage('BOXBERRY_KD'),
                    'DESCRIPTION' => "",
                ),
                'PVZ_COD' => array(
                    'TITLE' => Loc::getMessage('BOXBERRY_PVZ_COD'),
                    'DESCRIPTION' => "",
                ),
                'KD_COD' => array(
                    'TITLE' => Loc::getMessage('BOXBERRY_KD_COD'),
                    'DESCRIPTION' => "",
                )
            )
        );
    }

    public static function widgetInit()
    {
        if (!Context::getCurrent()->getRequest()->isAdminSection()) {
            $GLOBALS['APPLICATION']->IncludeComponent('bberry:boxberry.widget', '', [], false);
        }
    }

    public static function GetConfig()
    {
        return array(
            "CONFIG" => array(
                "default" => array(),
            )
        );
    }

    public static function SetSettings($arSettings)
    {
        foreach ($arSettings as $key => $value)
        {
            if ($value !== '') {
                $arSettings[$key] = ($value);
            }
            else {
                unset($arSettings[$key]);
            }
        }

        return serialize($arSettings);
    }

    public static function GetSettings($strSettings)
    {
        $settings = unserialize($strSettings);
        if (empty($settings)) {
            return;
        }
        return $settings;
    }

    public static function getFullDimensions($basketItems)
    {
        $defaultWeight = trim(Option::get(self::$moduleId, 'BB_WEIGHT'));
        $defaultWidth = trim(Option::get(self::$moduleId, 'BB_WIDTH'));
        $defaultHeight = trim(Option::get(self::$moduleId, 'BB_HEIGHT'));
        $defaultDepth = trim(Option::get(self::$moduleId, 'BB_DEPTH'));

        $fullPackage = [];
        $fullPackage["WEIGHT"] = 0;
        $fullPackage["WIDTH"] = 0;
        $fullPackage["HEIGHT"] = 0;
        $fullPackage["LENGTH"] = 0;

        if (Option::get(self::$moduleId, 'BB_APPLY_DEFAULT_DIMENSIONS_TO_ORDER') === 'Y') {
            $fullPackage["WEIGHT"] = ceil($defaultWeight);
            $fullPackage["WIDTH"] = ceil($defaultWidth);
            $fullPackage["HEIGHT"] = ceil($defaultHeight);
            $fullPackage["LENGTH"] = ceil($defaultDepth);
        } else {
            $itemsVolume = 0;
            foreach ($basketItems as $item) {
                if (is_array($item['DIMENSIONS'])) {
                    $dimensions = $item['DIMENSIONS'];
                } else {
                    $dimensions = unserialize($item['DIMENSIONS']);
                }

                $fullPackage['WEIGHT'] += (($item['WEIGHT'] < 1) ? ($item['QUANTITY'] * ceil(
                        $defaultWeight
                    )) : ($item['QUANTITY'] * ceil(
                        $item['WEIGHT']
                    )));

                if (((float)$dimensions['WIDTH'] / 10 + (float)$dimensions['HEIGHT'] / 10 + (float)$dimensions['LENGTH'] / 10) < 1) {
                    $itemVolume = (int)$item["QUANTITY"] * ((float)$defaultWidth * (float)$defaultHeight * (float)$defaultDepth);
                } else {
                    $itemVolume = (int)$item["QUANTITY"] * ((float)$dimensions['WIDTH'] / 10 * (float)$dimensions['HEIGHT'] / 10 * (float)$dimensions['LENGTH'] / 10);
                }

                $itemsVolume += $itemVolume;
            }

            if ($itemsVolume !== 0) {
                $sideDimension = $itemsVolume ** (1 / 3);
                $fullPackage["WIDTH"] += ceil($sideDimension);
                $fullPackage["HEIGHT"] += ceil($sideDimension);
                $fullPackage["LENGTH"] += ceil($sideDimension);
            }
        }

        return $fullPackage;
    }

    public static function getBitrixRegionNames($location)
    {
        self::$cityBitrixName = false;
        self::$regionBitrixName = false;

        if (!empty($location)){
            $parameters = array();
            $parameters['filter']['=CODE'] = $location;
            $parameters['filter']['NAME.LANGUAGE_ID'] = 'ru';
            $parameters['limit'] = 1;
            $parameters['select'] = array('*','LNAME' => 'NAME.NAME');

            $arVal = LocationTable::getList($parameters)->fetch();
            $fullCityName = LocationHelper::getLocationStringByCode($location);

            $cityLocTypeId = TypeTable::getList(array('filter' => array('=CODE' => 'CITY')))->Fetch();
            $villageLocTypeId = TypeTable::getList(array('filter' => array('=CODE' => 'VILLAGE')))->Fetch();

            if (isset($arVal['LNAME'], $arVal['TYPE_ID'])) {
                if ($arVal['TYPE_ID'] === $cityLocTypeId['ID']) {
                    self::$locType = 'city';
                } elseif ($arVal['TYPE_ID'] === $villageLocTypeId['ID']) {
                    self::$locType = 'village';
                }

                self::$cityBitrixName = mb_strtoupper($arVal['LNAME']);
                self::$regionBitrixName = mb_strtoupper($fullCityName);
                $cityWidgetName = explode(",", self::$regionBitrixName);
                $cityWidgetName = array_reverse($cityWidgetName);

                if (@strpos(self::$regionBitrixName, Loc::getMessage('BOXBERRY_UNION_EXC')) !== false) {
                    self::$isRussia = true;
                } elseif (@strpos(self::$regionBitrixName, Loc::getMessage('BOXBERRY_UNION_KZ')) !== false) {
                    self::$isKz = true;
                }

                if (@strpos(self::$cityBitrixName, Loc::getMessage('BOXBERRY_MOSCOW')) !== false) {
                    self::$cityWidgetName = Loc::getMessage('BOXBERRY_MOSCOW');
                } elseif (@strpos(self::$cityBitrixName, Loc::getMessage('BOXBERRY_ALMATA')) !== false) {
                    self::$cityWidgetName = Loc::getMessage('BOXBERRY_ALMA-ATA');
                } elseif (@strpos(self::$cityBitrixName, Loc::getMessage('BOXBERRY_NUR-SULTAN')) !== false) {
                    self::$cityWidgetName = Loc::getMessage('BOXBERRY_ASTANA');
                } elseif (isset($cityWidgetName[1])) {
                    self::$cityWidgetName = str_replace(Loc::getMessage('yo'), Loc::getMessage('ye'), $cityWidgetName[0]) . ' ' . $cityWidgetName[1];
                }

                self::setIsLocationSet(true);
            }
        }
    }

    public static function getCityCode($locationCode)
    {
        if ($city = self::getCity($locationCode)) {
            return $city['BB_CITY_CODE'];
        }

        $regionBitrixName = self::$regionBitrixName;
        $cityBitrixName = self::$cityBitrixName;
        $isRussia = self::$isRussia;
        $locType = self::$locType;
        $boxberryList = CBoxberry::listCitiesFull();

        if (is_array($boxberryList)) {
            foreach ($boxberryList as $boxberryCities) {
                $cityName = mb_strtoupper($boxberryCities['Name']);
                $regionName = explode(' ', mb_strtoupper($boxberryCities['Region']));
                $districtName = mb_strtoupper($boxberryCities['District']);
                $countryCode = $boxberryCities['CountryCode'];

                if ($locType === 'city') {
                    $bxbRegName = $regionName[0];
                } elseif ($locType === 'village') {
                    $bxbRegName = $districtName;
                } else {
                    return false;
                }

                if ($isRussia) {
                    if ($cityBitrixName === $cityName && @strpos($regionBitrixName, $bxbRegName) !== false) {
                        self::addCity($boxberryCities['Code'], $countryCode, $locationCode);
                        return $boxberryCities['Code'];
                    }
                } elseif (($countryCode !== '643' && $cityBitrixName === $cityName
                        && $locType === 'city') || $cityName === self::$cityWidgetName) {
                    self::addCity($boxberryCities['Code'], $countryCode, $locationCode);
                    return $boxberryCities['Code'];
                }
            }
        }

        return false;
    }

    public static function Compability($arOrder, $arConfig)
    {
        return self::deliveryCalculation($arOrder);
    }

    public static function Calculate($profile, $arConfig, $arOrder, $STEP, $TEMP = false)
    {
        return self::deliveryCalculation($arOrder, $profile);
    }

    private static function setLinkParams($profile, $arrParams = array())
    {
        $_SESSION['link_params_'.$profile] = Json::encode($arrParams, JSON_UNESCAPED_UNICODE);
    }

    public static function getLinkParams($profile)
    {
        if (!empty($_SESSION['link_params_'.$profile])) {
            return implode("','", Json::decode($_SESSION['link_params_'.$profile]));
        }

        return false;
    }

    private static function getDeliveryTypeIdByProfile($profile){
        $deliveryTypeId = '';
        switch ($profile) {
            case 'KD':
            case 'KD_COD':
                $deliveryTypeId = self::COURIER_DELIVERY_TYPE_ID;
                break;
            case 'PVZ':
            case 'PVZ_COD':
                $deliveryTypeId = self::PICKUP_DELIVERY_TYPE_ID;
                break;
        }
        return $deliveryTypeId;
    }

    private static function getProfilesByDeliveryTypeId($deliveryTypeId, $isRussia = false, $isKz = false)
    {
        $profiles = [];
        switch ($deliveryTypeId) {
            case self::COURIER_DELIVERY_TYPE_ID:
                $profiles = $isRussia || $isKz ? ['KD', 'KD_COD'] : ['KD_COD'];
                break;
            case self::PICKUP_DELIVERY_TYPE_ID:
                $profiles = $isRussia || $isKz ? ['PVZ', 'PVZ_COD'] : ['PVZ_COD'];
                break;
        }

        return $profiles;
    }

    public static function getCity($locationCode)
    {
        return CitiesTable::getList([
            'filter' => [
                'BITRIX_CITY_CODE' => $locationCode,
            ],
            'cache' => [
                'ttl' => 86400,
            ],
        ])->fetch();
    }

    private static function addCity($cityCode, $countryCode, $locationCode)
    {
        CitiesTable::add([
            'BB_CITY_CODE' => $cityCode,
            'BB_COUNTRY_CODE' => $countryCode,
            'BITRIX_CITY_CODE' => $locationCode,
        ]);
    }

    private static function deliveryCalculation($arOrder, $profile = '')
    {
        if (!empty(self::$profiles) && !$profile) {
            return self::$profiles;
        }

        if (!self::$isLocationSet) {
            self::getBitrixRegionNames($arOrder['LOCATION_TO']);
        }

        if (empty(self::$dimensions)) {
            self::setDimensions(self::getFullDimensions($arOrder['ITEMS']));
        }

        if (!$profile && (self::$dimensions['WEIGHT'] > 31000 || self::$dimensions['LENGTH'] + self::$dimensions['WIDTH'] + self::$dimensions['HEIGHT'] > 250 ||
                self::$dimensions['HEIGHT'] > 120 || self::$dimensions['WIDTH'] > 120 || self::$dimensions['LENGTH'] > 120)) {
            return [];
        }

        if (!$RecipientCityId = self::getCityCode($arOrder['LOCATION_TO'])) {
            return [];
        }

        if (empty(self::$widgetSettings)) {
            self::setWidgetSettings(CBoxberry::widgetSettings());
        }

        if (in_array($RecipientCityId, self::$widgetSettings['result'][1]['CityCode'])) {
            return [];
        }

        $isRussia = self::$isRussia;
        $isKz = self::$isKz;
        $parcelSize = self::$dimensions;
        $kdSurchOff = Option::get(self::$moduleId, 'BB_KD_SURCH') === 'Y';

        $params = [
            'token' => CBoxberry::$apiToken,
            'method' => 'DeliveryCalculation',
            'RecipientCityId' => $RecipientCityId,
            'OrderSum' => $arOrder['PRICE'],
            'DeliveryType' => self::getDeliveryTypeIdByProfile($profile),
            'PaySum' => ($profile === 'KD' || $profile === 'PVZ' ? $arOrder['PRICE'] : 0),
            'BoxSizes' => [
                [
                    'Weight' => $parcelSize['WEIGHT'],
                    'Depth' => $parcelSize['LENGTH'],
                    'Height' => $parcelSize['HEIGHT'],
                    "Width" => $parcelSize['WIDTH']
                ]
            ],
            'UseShopSettings' => ($kdSurchOff && ($profile === 'KD' || $profile === 'KD_COD') ? '0' : '1'),
            'CmsName' => 'bitrix',
            'Url' => $_SERVER['SERVER_NAME'],
            'Version' => '2.2.24'
        ];

        $deliveryCosts = CBoxberry::deliveryCalculation($params);

        if (empty($deliveryCosts)) {
            return [];
        }

        if (isset($deliveryCosts['error']) && $deliveryCosts['error'] === true) {
            return [];
        }

        if (isset($deliveryCosts['err'])) {
            return [];
        }

        if (isset($deliveryCosts['result']['DeliveryCosts']) && is_array($deliveryCosts['result']['DeliveryCosts'])) {
            $deliveryCosts = $deliveryCosts['result']['DeliveryCosts'];
        } else {
            return [];
        }

        if (!$profile) {
            if (count($deliveryCosts) > 1) {
                self::setProfiles(array_merge(self::getProfilesByDeliveryTypeId(self::PICKUP_DELIVERY_TYPE_ID, $isRussia, $isKz),
                    self::getProfilesByDeliveryTypeId(self::COURIER_DELIVERY_TYPE_ID, $isRussia, $isKz)));
                return self::$profiles;
            }

            self::setProfiles(self::getProfilesByDeliveryTypeId($deliveryCosts[0]['DeliveryTypeId'], $isRussia, $isKz));
            return self::$profiles;
        }

        if (self::$widgetSettings['result'][3]['hide_delivery_day'] !== 1) {
            $period = $deliveryCosts[0]['DeliveryPeriod'];
            $countPeriod = new Declension(Loc::getMessage('DAY'), Loc::getMessage('DAYS'), Loc::getMessage('DAYSS'));
            $period .= ' ' . $countPeriod->get($period);
        } else {
            $period = null;
        }

        if (self::getDeliveryTypeIdByProfile($profile) === self::PICKUP_DELIVERY_TYPE_ID) {

            if (empty(self::$widget)) {
                self::setWidget(CBoxberry::getKeyIntegration());
            }

            self::setLinkParams('boxberry:' . $profile, [
                    self::$widget['key'],
                    self::$cityWidgetName,
                    '',
                    $arOrder['PRICE'],
                    $parcelSize['WEIGHT'],
                    $profile === 'PVZ' ? $arOrder['PRICE'] : 0,
                    $parcelSize['HEIGHT'],
                    $parcelSize['WIDTH'],
                    $parcelSize['LENGTH'],
                    $profile === 'PVZ' ? 1 : 0
                ]
            );

            if (Option::get(self::$moduleId, 'BB_LINK_IN_PERIOD') === 'Y') {
                $period .= self::makeWidgetLink('boxberry:' . $profile);
            }
        }

        return [
            'RESULT' => 'OK',
            'VALUE' => $deliveryCosts[0]['TotalPrice'],
            'TRANSIT' => $period,
        ];

    }

    public static function getOrderJsData(&$arResult, &$arParams)
    {
        if (Option::get(self::$moduleId, 'BB_LINK_IN_PERIOD') === 'Y') {
            return;
        }

        $pvzDeliveryIds = self::getPvzDeliveryIds();

        if (isset($arResult['JS_DATA']['DELIVERY']) && is_array($arResult['JS_DATA']['DELIVERY'])) {
            $deliveries = $arResult['JS_DATA']['DELIVERY'];

            foreach ($deliveries as $key => $delivery) {
                if (isset($delivery['CHECKED']) && $delivery['CHECKED'] === 'Y' && in_array(
                        $delivery['ID'],
                        $pvzDeliveryIds,
                        true
                    ) && $deliveryCode = self::getDeliveryCode($delivery['ID'])) {
                    $arResult['JS_DATA']['DELIVERY'][$key]['DESCRIPTION'] .= self::makeWidgetLink($deliveryCode);
                }
            }
        }
    }

    public static function checkOrder($entity, $values)
    {
        if (Context::getCurrent()->getRequest()->isAdminSection()) {
            return true;
        }

        $shipmentCollection = $entity->getShipmentCollection();
        foreach ($shipmentCollection as $shipment) {
            if ($shipment->isSystem()) {
                continue;
            }

            if ($profile = self::getDeliveryCode($shipment->getDeliveryId())) {
                if ($_SESSION['checkPVZ'] && strpos($profile, 'boxberry:PVZ') !== false) {
                    return new EventResult(
                        EventResult::ERROR,
                        new ResultError(Loc::getMessage('BOXBERRY_PVZ_IS_NOT_SELECTED'), 'code'),
                        'sale'
                    );
                }
            }
        }

        return true;
    }

    public static function getDeliveryCode($deliveryId)
    {
        try {
            $delivery = Manager::getObjectById($deliveryId);
        } catch (Exception $e) {
            return false;
        }

        if (isset($delivery)) {
            return $delivery->getCode();
        }

        return false;
    }

    public static function getPvzDeliveryIds()
    {
        $allDeliveries = Manager::getActiveList();
        $ids = [];
        foreach ($allDeliveries as $delivery) {
            if ($delivery['ACTIVE'] === 'Y' && @strpos($delivery['CODE'], 'boxberry:PVZ') !== false) {
                $ids[] = $delivery['ID'];
            }
        }
        return $ids;
    }

    public static function makeWidgetLink($profile)
    {
        $bxbLinkStyle = Option::get(self::$moduleId, 'BB_BUTTON') === 'Y' ? 'bxbbutton' : 'bxblink';

        $bxbLinkIcon = Option::get(
            self::$moduleId,
            'BB_BUTTON'
        ) === 'Y' ? '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAYCAYAAAD6S912AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAE+SURBVHgBnVSBccMgDNR5Am9QRsgIjMIGZYN4g2QDpxN0BEagGzgbpBtQqSdiBQuC/Xc6G0m8XogDQEFKaUTzaAHtkVZEtBnNQi8w+bMgof+FTYKIzTuyS7HBKsqdIKfvqUZ2fpv0mj+JDkwZdILMQCcEaSwDuQULO8GDI7hS3VzZYFmJ09RzfFWJP981deJcU+tIhMoPWtDdSo3KJYKSe81tD7imid63zYIFHZr/h79mgDp+K/47NDBwgkG5YxG7VTZ/KT7zLIZEt8ZQjDhwusBeIZNDOcnDD3AAXPT/BkjnUlPZQTjnCUunO6KSWtyoE8HAQb+DcNmoU6ptXw+dLD91cyvJc1JUrpHM63+dROuXStyk9UW30NHKKM7mrJDl2AS9KFR4USiy7wp7kV5fm4coEOEomDQI0qk1LMIfknqE+j7lxtgAAAAASUVORK5CYII=">' : '';

        $bxbLinkText = Option::get(self::$moduleId, 'BB_BUTTON') === 'Y' ? Loc::getMessage(
            "SELECT_BUTTON_TEXT"
        ) : Loc::getMessage("SELECT_LINK_TEXT");

        return '<div id="boxberrySelectPvzWidget"><a href="#" class=' . $bxbLinkStyle . ' onclick="boxberry.checkLocation(1);boxberry.open(delivery, ' . "'" . self::getLinkParams($profile) . "'" . ');return false;" >' . $bxbLinkIcon . '<span>' . $bxbLinkText . '</span></a></div>';
    }

    public static function orderCreate($id, $arOrder)
    {
        if (!function_exists('findParentBXB'))
        {
            function findParentBXB($profiles){
                if ($profiles['CODE']=='boxberry'){
                    return $profiles['ID'];
                }
            }
        }

        $allDeliveries = Manager::getActiveList();
        $parent = array_filter ($allDeliveries, 'findParentBXB');
        $boxberryProfiles = array();

        foreach ($allDeliveries as $profile){
            foreach ($parent as $key=>$value){
                if($profile["PARENT_ID"]==$key){
                    $boxberryProfiles[] = $profile["ID"];
                }
            }
        }

        if (!empty($id) && in_array($arOrder['DELIVERY_ID'], $boxberryProfiles))
        {
            $result = CBoxberry::MakePropsArray($arOrder);
            $arFields = array(
                'ORDER_ID' 			=> $id,
                'DATE_CHANGE' 		=> date('d.m.Y H:i:s'),
                'LID' 				=> $result['LID'],
                'PVZ_CODE' 			=> (isset($_SESSION['selPVZ']) && !empty($_SESSION['selPVZ']) ? $_SESSION['selPVZ'] : '' ),
                'STATUS'			=> '0',
                'STATUS_TEXT' 		=> 'NEW',
                'STATUS_DATE' 		=> date('d.m.Y H:i:s')
            );

            CBoxberryOrder::Add($arFields);
        }

        $checkOrderStatus = $arOrder['STATUS_ID'];
        $checkPCOption = Option::get(self::$moduleId, 'BB_PARSELCREATE');
        $checkPCOnStatusOption = Option::get(self::$moduleId, 'BB_STATUS_PARSELCREATE');

        if (!empty($id) && ($checkPCOption == 'Y') && !$checkPCOnStatusOption && in_array($arOrder['DELIVERY_ID'], $boxberryProfiles))
        {
            self::$parselCreate = new CBoxberryParsel();
            self::$parselCreate->parselCreate($id);
        }

        if (!empty($id) && !empty($checkPCOnStatusOption) && ($checkOrderStatus == $checkPCOnStatusOption) && in_array($arOrder['DELIVERY_ID'], $boxberryProfiles))
        {
            self::$parselCreate = new CBoxberryParsel();
            self::$parselCreate->parselCreate($id);
        }

        return true;
    }

    /**
     * @return bool
     */
    public static function isLocationSet(): bool
    {
        return self::$isLocationSet;
    }

    /**
     * @param bool $isLocationSet
     */
    public static function setIsLocationSet(bool $isLocationSet): void
    {
        self::$isLocationSet = $isLocationSet;
    }

    /**
     * @return array
     */
    public static function getProfiles(): array
    {
        return self::$profiles;
    }

    /**
     * @param array $profiles
     */
    public static function setProfiles(array $profiles): void
    {
        self::$profiles = $profiles;
    }

    /**
     * @return array
     */
    public static function getDimensions(): array
    {
        return self::$dimensions;
    }

    /**
     * @param array $dimensions
     */
    public static function setDimensions(array $dimensions): void
    {
        self::$dimensions = $dimensions;
    }

    /**
     * @return array
     */
    public static function getWidgetSettings(): array
    {
        return self::$widgetSettings;
    }

    /**
     * @param array $widgetSettings
     */
    public static function setWidgetSettings(array $widgetSettings): void
    {
        self::$widgetSettings = $widgetSettings;
    }

    /**
     * @return array
     */
    public static function getWidget(): array
    {
        return self::$widget;
    }

    /**
     * @param array $widget
     */
    public static function setWidget(array $widget): void
    {
        self::$widget = $widget;
    }

}

EventManager::getInstance()->addEventHandlerCompatible('sale', 'onSaleDeliveryHandlersBuildList', array('CDeliveryBoxberry', 'Init'));
EventManager::getInstance()->addEventHandlerCompatible('sale', 'OnSaleComponentOrderJsData', array('CDeliveryBoxberry', 'getOrderJsData'));
EventManager::getInstance()->addEventHandlerCompatible('sale', 'OnSaleComponentOrderOneStepComplete', array('CDeliveryBoxberry', 'orderCreate'));
EventManager::getInstance()->addEventHandlerCompatible('sale', 'OnSaleOrderBeforeSaved', array('CDeliveryBoxberry', 'checkOrder'));
EventManager::getInstance()->addEventHandlerCompatible('sale', 'OnOrderUpdate', array('CDeliveryBoxberry', 'orderCreate'));
EventManager::getInstance()->addEventHandlerCompatible('sale', 'OnOrderSave', array('CDeliveryBoxberry', 'orderCreate'));
?>
