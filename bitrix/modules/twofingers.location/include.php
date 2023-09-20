<?php

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Request;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Uri;
use Bitrix\Sale\Order;
use Bitrix\Sale\PropertyValue;
use TwoFingers\Location\Factory\LocationFactory;
use TwoFingers\Location\Helper\Tools;
use TwoFingers\Location\Model\Iblock\Content;
use TwoFingers\Location\Model\Location as LocationModel;
use TwoFingers\Location\Options;
use TwoFingers\Location\Storage;
use TwoFingers\Location\Entity\Location as LocationEntity;
use TwoFingers\Location\Model\Iblock\Location as LocationIblock;

/**
 * Class TwoFingersLocation
 */
class TwoFingersLocation
{
    /**
     * @param $order
     * @param $arUserResult
     * @param $request
     * @param $arParams
     * @param $arResult
     */
    public static function setZip($order, &$arUserResult, $request, &$arParams, &$arResult)
    {
        if (!Options::isOrderSetZip() || Storage::isEmpty()) {
            return;
        }

        // $orderFake          = Order::create(Application::getInstance()->getContext()->getSite());
        $propertyCollection  = $order->getPropertyCollection();
        $zipPropertyId       = null;
        $checkedPersonTypeId = self::getCheckedPersonTypeId($arResult);

        /** @var PropertyValue $property */
        foreach ($propertyCollection as $property) {
            if ($property->isUtil()) {
                continue;
            }

            $arProperty = $property->getProperty();

            if (($arProperty['IS_ZIP'] === 'Y')
                && ($arProperty['PERSON_TYPE_ID'] == $checkedPersonTypeId)
            ) {
                $zipPropertyId = $arProperty['ID'];
                break;
            }
        }

        if (!$zipPropertyId) {
            return;
        }

        $zip = LocationModel::getZipById(Storage::getCityId());

        if (is_array($zip)) {
            $zip = reset($zip);
        }

        if (!empty($zip)) {
            foreach ($arResult['JS_DATA']['ORDER_PROP']['properties'] as &$property) {
                if ($property['ID'] == $zipPropertyId) {
                    $property['VALUE'] = [$zip];
                }
            }
        }
    }

    /**
     * @param $arResult
     * @return mixed|null
     */
    protected static function getCheckedPersonTypeId($arResult)
    {
        foreach ($arResult['PERSON_TYPE'] as $personType) {
            if (!empty($personType['CHECKED']) && ($personType['CHECKED'] == 'Y')) {
                return $personType['ID'];
            }
        }

        return null;
    }

    /**
     * @param         $arUserResult
     * @param Request $request
     * @param         $arParams
     * @param         $arResult
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws SystemException
     */
    public static function setSaleLocation(&$arUserResult, Request $request, &$arParams, &$arResult)
    {
        if (!Options::isOrderSetLocation()) {
            return;
        }

        $checkedPersonTypeId = self::getCheckedPersonTypeId($arResult);
        if (!$checkedPersonTypeId) {
            return;
        }

        if (Options::isOrderSetTemplate()) {
            $arParams['TEMPLATE_LOCATION'] = 'tf_location';
        }

        $order              = Order::create(Application::getInstance()->getContext()->getSite());
        $propertyCollection = $order->getPropertyCollection();
        $locationProperty   = null;

        /** @var PropertyValue $propertyValue */
        foreach ($propertyCollection as $propertyValue) {
            if ($propertyValue->isUtil()) {
                continue;
            }

            if (($propertyValue->getType() === 'LOCATION')
                && ($propertyValue->getPersonTypeId() == $checkedPersonTypeId)
                && array_key_exists($propertyValue->getField('ORDER_PROPS_ID'), $arUserResult["ORDER_PROP"])
            ) {
                $locationProperty = $propertyValue;
                break;
            }
        }

        if (!$locationProperty) {
            return;
        }

        // check change by another component
        if (($request->get('ORDER_PROP_' . $propertyValue->getField('ORDER_PROPS_ID')) !== null)
            && ($request->get('RECENT_DELIVERY_VALUE') != $request->get('ORDER_PROP_' . $propertyValue->getField('ORDER_PROPS_ID')))
            && (Storage::getCityCode() != $request->get('ORDER_PROP_' . $propertyValue->getField('ORDER_PROPS_ID')))) {
            $location = LocationFactory::buildByCode(
                $request->get('ORDER_PROP_' . $propertyValue->getField('ORDER_PROPS_ID')), SITE_ID, LANGUAGE_ID);

            if (isset($location)) {
                Storage::setLocation($location);
            }
        }

        if (!Storage::isEmpty()) {
            $arUserResult['DELIVERY_LOCATION']       = Storage::getCityCode();
            $arUserResult['DELIVERY_LOCATION_BCODE'] = Storage::getCityCode();
            $arUserResult['ORDER_PROP'][$propertyValue->getField('ORDER_PROPS_ID')]
                                                     = Storage::getCityCode();
            /*if (Options::isOrderSetZip() && $zipPropertyId) {

                $zip = LocationModel::getZipById(Storage::getCityId());
                if (is_array($zip))
                    $zip = reset($zip);

                if (!empty($zip))
                {
                    $arUserResult['ORDER_PROP'][$zipPropertyId] = $zip;
                    $arUserResult['DELIVERY_LOCATION_ZIP']      = $zip;
                }
            }*/
        }
    }

    /**
     * @param $pageContent
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws SystemException
     * @throws LoaderException
     */
    public static function onEndBufferContentHandler(&$pageContent)
    {
        if ((Application::getInstance()->getContext()->getRequest()->isAdminSection())
            || !Options::isReplacePlaceholders()) {
            return;
        }

        $search = ['#location_name#', '#city_name#', '#region_name#', '#country_name#'];

        try {
            $location = LocationFactory::buildByStorage(SITE_ID, LANGUAGE_ID);

            if ($location) {
                $regionLocation  = LocationFactory::buildParent($location, LocationEntity::TYPE_REGION);
                $countryLocation = LocationFactory::buildParent($location, LocationEntity::TYPE_COUNTRY);
                $replace         = [
                    $location->getName(),
                    $location->getName(),
                    $regionLocation ? $regionLocation->getName() : '',
                    $countryLocation ? $countryLocation->getName() : ''
                ];
            } else {
                $replace = ['', '', '', ''];
            }
        } catch (\Exception $e) {
            $replace = ['', '', '', ''];
        }

        $pageContent = str_replace($search, $replace, $pageContent);

        try {
            $content = isset($location) && $location instanceof LocationEntity ? $location->getContent() : null;
        } catch (\Exception $e) {
            $content = null;
        }

        $pageContent = preg_replace_callback('/#content_([a-z0-9_]+)#/si', function ($matches) use ($content) {
            $code  = strtoupper(mb_strtoupper($matches[1]));
            $value =
                $content instanceof \TwoFingers\Location\Entity\Content
                    ? ($code == Content::PROPERTY_DOMAIN
                    ? $content->getDomain()
                    : $content->getValue($code))
                    : '';

            return is_array($value) ? implode('/', $value) : $value;
        }, $pageContent);
    }

    /**
     * @return false|int
     */
    public static function isBot()
    {
        return preg_match(
            "~(Google|Yahoo|Rambler|Bot|Yandex|Spider|Snoopy|Crawler|Finder|Mail|curl)~i",
            $_SERVER['HTTP_USER_AGENT']);
    }

    /**
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     */
    public static function onBeforePrologHandler()
    {
        $request = Application::getInstance()->getContext()->getRequest();

        if ($request->isAdminSection() || (strpos($_SERVER['REQUEST_URI'], '/bitrix/') === 0)) {
            return;
        }

        // try to build by request
        if (!empty($request->get('tfl'))) {
            try {
                $requestLocation = LocationFactory::buildByCode($request->get('tfl'), SITE_ID, LANGUAGE_ID);
                if ($requestLocation) {
                    Storage::setLocation($requestLocation);

                    if (Options::isTflRedirect()) {
                        header('Location: ' . (new Uri($_SERVER['REQUEST_URI']))->deleteParams(['tfl'])->getUri(), true,
                            301);

                        require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
                        die();
                    }
                }
            } catch (\Exception $e) {
                self::handleException($e);
            }
        }

        // try to detect location
        if (Storage::isEmpty()) {
            try{
                $currentLocation = LocationFactory::buildCurrent(SITE_ID, LANGUAGE_ID);
                if ($currentLocation) {
                    Storage::setLocation($currentLocation);
                }
            } catch (\Exception $e) {
                TwoFingersLocation::handleException($e);
            }
        }

        // default, if still empty
        if (Storage::isEmpty()) {
            Storage::setLocation(LocationFactory::buildDefault(SITE_ID, LANGUAGE_ID));
        }

        $event = new Event("twofingers.location", "afterLocationDetect");
        $event->send();

        // subdomain redirect if needed
        if ((Options::hasRedirectEvent(Options::REDIRECT_EVENT_DETECTED)) && !Storage::isEmpty()) {
            if ($storageLocation = LocationFactory::buildByStorage(SITE_ID, LANGUAGE_ID)) {
                $domain = $storageLocation->getDomain();

                if ($domain && !self::isBot()) {
                    if (mb_strpos($_SERVER['HTTP_HOST'], Tools::clearDomain($domain)) !== 0) {
                        $uri = new Uri($domain . $_SERVER['REQUEST_URI']);
                        $uri->addParams(['tfl' => $storageLocation->getCode()]);

                        header('Location: ' . $uri->getUri(), true, 301);
                        die();
                    }
                }
            }
        }
    }

    /**
     * @param $fields
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException|LoaderException
     */
    public static function onAfterIBlockElementUpdateHandler($fields)
    {
        self::checkDefaultLocations($fields);
    }

    /**
     * @param array $fields
     * @return void
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     */
    protected static function checkDefaultLocations(array $fields)
    {
        if (LocationIblock::getId() != $fields['IBLOCK_ID']) {
            return;
        }

        $defaultProperty = LocationIblock::getPropertyByCode(LocationIblock::PROPERTY_DEFAULT);
        if (!isset($fields['PROPERTY_VALUES'][$defaultProperty['ID']])) {
            return;
        }

        $siteProperty = LocationIblock::getPropertyByCode(LocationIblock::PROPERTY_SITE_ID);
        $sites        = [];
        foreach ($fields['PROPERTY_VALUES'][$siteProperty['ID']] as $valueId => $siteValue) {
            $sites[] = $siteValue['VALUE'];
        }

        // try to find another default locations
        $filter = [
            'IBLOCK_ID'                                     => LocationIblock::getId(),
            '!ID'                                           => $fields['ID'],
            '!PROPERTY_' . LocationIblock::PROPERTY_DEFAULT => false,
            'PROPERTY_' . LocationIblock::PROPERTY_SITE_ID  => count($sites) ? $sites : false,
        ];

        $elements = CIBlockElement::GetList([], $filter, false, false, ['ID', 'IBLOCK_ID']);

        while ($element = $elements->Fetch()) {
            CIBlockElement::SetPropertyValuesEx($element['ID'], $element['IBLOCK_ID'],
                [LocationIblock::PROPERTY_DEFAULT => false]);
        }
    }

    public static function addCustomGeoIpHandler()
    {
        $localPath = getLocalPath('modules/twofingers.location/');

        return new \Bitrix\Main\EventResult(
            \Bitrix\Main\EventResult::SUCCESS,
            [
                '\TwoFingers\Location\Service\GeoIpService' => $localPath . 'lib/service/geoipservice.php'
            ],
            'twofingers.location'
        );
    }

    public static function handleException(Exception $e)
    {
        // @TODO
    }
}

if (Loader::includeModule('sale')) {
    $eventManager = EventManager::getInstance();

    $eventManager->addEventHandler("sale", "OnSaleComponentOrderResultPrepared", ["\TwoFingersLocation", "setZip"]);
    $eventManager->addEventHandler("sale", "OnSaleComponentOrderProperties",
        ["\TwoFingersLocation", "setSaleLocation"]);
    $eventManager->addEventHandler("iblock", "OnIBlockPropertyBuildList",
        ["\TwoFingers\Location\Property\Location", "GetUserTypeDescription"]);

    $eventManager->addEventHandler('main', 'onMainGeoIpHandlersBuildList',
        ["\TwoFingersLocation", "addCustomGeoIpHandler"]);
}

//autoload files
$localPath              = getLocalPath('modules/twofingers.location/');
$localPathForDeprecated = $localPath . 'deprecated/';

require_once $_SERVER['DOCUMENT_ROOT'] . $localPath . 'vendor/NameCaseLib/Library/NCLNameCaseRu.php';

Loader::registerAutoLoadClasses('twofingers.location', [
    // 'NCLNameCaseRu'                                         => $localPath . 'vendor/NameCaseLib/Library/NCLNameCaseRu.php',
    'TwoFingers\Location\Iblock'                            => $localPathForDeprecated . 'iblock.php',
    'TwoFingers\Location\Iblock\Content'                    => $localPathForDeprecated . 'iblock/content.php',
    'TwoFingers\Location\Iblock\Domain'                     => $localPathForDeprecated . 'iblock/domain.php',
    'TwoFingers\Location\Iblock\Location'                   => $localPathForDeprecated . 'iblock/location.php',
    'TwoFingers\Location\Location'                          => $localPathForDeprecated . 'location.php',
    'TwoFingers\Location\Location\Internal'                 => $localPathForDeprecated . 'location/internal.php',
    'TwoFingers\Location\Sale'                              => $localPathForDeprecated . 'location/sale.php',
    'TwoFingers\Location\Sale2'                             => $localPathForDeprecated . 'location/sale2.php',
    'TwoFingers\Location\Factory\ContentEntityFactory'      => $localPathForDeprecated . 'factory/contententityfactory.php',
    'TwoFingers\Location\Factory\LocationCollectionFactory' => $localPathForDeprecated . 'factory/locationcollectionfactory.php',
    'TwoFingers\Location\Factory\LocationEntityFactory'     => $localPathForDeprecated . 'factory/locationentityfactory.php',
]);

// php8 bug
if (!defined('CURLPROXY_HTTPS'))
    define('CURLPROXY_HTTPS', 2);

?>