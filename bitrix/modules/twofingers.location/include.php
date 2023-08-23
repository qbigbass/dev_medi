<?

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Request;
use Bitrix\Main\SystemException;
use Bitrix\Sale\Order;
use Bitrix\Sale\PropertyValue;
use TwoFingers\Location\Helper\Tools;
use TwoFingers\Location\Model\Iblock\Content;
use TwoFingers\Location\Model\Location as LocationModel;
use TwoFingers\Location\Options;
use TwoFingers\Location\Settings;
use TwoFingers\Location\Storage;
use \TwoFingers\Location\Entity\Location as LocationEntity;
use TwoFingers\Location\Model\Iblock\Location as LocationIblock;

/**
 * Class TwoFingersLocation
 */
class TwoFingersLocation
{
    /**
     * @param $moduleName
     * @return false|mixed|string
     */
    public static function getModuleVersion($moduleName)
    {
        $moduleName = preg_replace("/[^a-zA-Z0-9_.]+/i", "", trim($moduleName));
        if ($moduleName == '')
            return false;

        if (!ModuleManager::isModuleInstalled($moduleName))
            return false;

        if ($moduleName == 'main')
        {
            if (!defined("SM_VERSION"))
                include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/version.php");

            return SM_VERSION;
        }

        $modulePath = getLocalPath("modules/".$moduleName."/install/version.php");
        if ($modulePath === false)
            return false;

        $arModuleVersion = array();
        include($_SERVER["DOCUMENT_ROOT"] . $modulePath);

        return array_key_exists("VERSION", $arModuleVersion)
            ? $arModuleVersion["VERSION"]
            : false;
    }

    /**
     * @param $order
     * @param $arUserResult
     * @param $request
     * @param $arParams
     * @param $arResult
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws SystemException
     *
     */
    public static function setZip($order, &$arUserResult, $request, &$arParams, &$arResult)
    {
        if ((Settings::get('TF_LOCATION_DELIVERY_ZIP') != 'Y') || Storage::isEmpty())
            return;

        $orderFake          = Order::create(Application::getInstance()->getContext()->getSite());
        $propertyCollection = $orderFake->getPropertyCollection();
        $zipPropertyId      = null;
        $checkedPersonTypeId= self::getCheckedPersonTypeId($arResult);

        /** @var PropertyValue $property */
        foreach ($propertyCollection as $property)
        {
            if ($property->isUtil()) continue;

            $arProperty = $property->getProperty();

            if(($arProperty['IS_ZIP'] === 'Y')
                && ($arProperty['PERSON_TYPE_ID'] == $checkedPersonTypeId)
            ) {
                $zipPropertyId  = $arProperty['ID'];
                break;
            }
        }

        if (!$zipPropertyId) return;

        $zip = LocationModel::getZipById(Storage::getCityId());
        if (is_array($zip))
            $zip = reset($zip);

        if (!empty($zip))
        {
            foreach ($arResult['JS_DATA']['ORDER_PROP']['properties'] as &$property){

                if ($property['ID'] == $zipPropertyId)
                    $property['VALUE'] = [$zip];
            }
        }
    }

    /**
     * @param $arResult
     * @return mixed|null
     */
    protected static function getCheckedPersonTypeId($arResult)
    {
        $checkedPersonTypeId = null;
        foreach ($arResult['PERSON_TYPE'] as $personType)
            if (!empty($personType['CHECKED']) && ($personType['CHECKED'] == 'Y'))
                return $personType['ID'];

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
     *
     */
    public static function setSaleLocation( &$arUserResult, Request $request, &$arParams, &$arResult)
    {
        $settings = Settings::getList();

        if ($settings['TF_LOCATION_DELIVERY'] != 'Y')
            return;

        $checkedPersonTypeId = self::getCheckedPersonTypeId($arResult);
        if (!$checkedPersonTypeId)
            return;

        if ($settings['TF_LOCATION_TEMPLATE'] == 'Y')
            $arParams['TEMPLATE_LOCATION'] = 'tf_location';

        $order              = Order::create(Application::getInstance()->getContext()->getSite());
        $propertyCollection = $order->getPropertyCollection();
        $locationPropertyId = null;
        $zipPropertyId      = null;

        foreach ($propertyCollection as $property)
        {
            /*if ($property->isUtil())
                continue;*/

            $arProperty = $property->getProperty();

            if ($arProperty['UTIL'] == 'Y') continue;

            if(
                (($arProperty['TYPE'] === 'LOCATION')
                    || ($arProperty['IS_ZIP'] === 'Y'))
                && ($arProperty['PERSON_TYPE_ID'] == $checkedPersonTypeId)
                && array_key_exists($arProperty['ID'],$arUserResult["ORDER_PROP"])
                && !$request->getPost("ORDER_PROP_".$arProperty['ID'])
                && (
                    !is_array($arOrder=$request->getPost("order"))
                    || !$arOrder["ORDER_PROP_".$arProperty['ID']]
                )
            ) {
                if ($arProperty['TYPE'] === 'LOCATION')
                    $locationPropertyId = $arProperty['ID'];
                else
                    $zipPropertyId  = $arProperty['ID'];
            }
        }

        if (!$locationPropertyId)
            return;

        if (!Storage::isEmpty())
        {
            $arUserResult['DELIVERY_LOCATION']                  = Storage::getCityId();
            $arUserResult['DELIVERY_LOCATION_BCODE']            = Storage::getCityId();
            $arUserResult['ORDER_PROP'][$locationPropertyId]    = Storage::getCityId();

            if (($settings['TF_LOCATION_DELIVERY_ZIP'] == 'Y') && $zipPropertyId) {

                $zip = LocationModel::getZipById(Storage::getCityId());
                if (is_array($zip))
                    $zip = reset($zip);

                if (!empty($zip))
                {
                    $arUserResult['ORDER_PROP'][$zipPropertyId] = $zip;
                    $arUserResult['DELIVERY_LOCATION_ZIP']      = $zip;
                }
            }
        }
    }

    /**
     * @param $arResult
     * @param $arUserResult
     * @param $arParams
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws SystemException
     */
    public static function setSaleLocationOld(&$arResult, &$arUserResult, $arParams)
    {
        $request = Application::getInstance()->getContext()->getRequest();

        self::setSaleLocation($arUserResult, $request, $arParams, $arResult);
        self::setZip(null, $arUserResult, $request, $arParams, $arResult);
    }

    /**
     * @param $content
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws SystemException
     * @throws LoaderException
     */
    public static function onEndBufferContentHandler(&$content)
    {
        if ((Application::getInstance()->getContext()->getRequest()->isAdminSection())
            || !Options::isReplacePlaceholders())
            return;

        $location   = Storage::getLocation();
        $search     = ['#location_name#', '#city_name#', '#region_name#', '#country_name#'];
        $replace    = $location
            ? [$location->getName(), $location->getName(),
                $location->hasParent() ? $location->getParent()->getName() : '',
                $location->hasParent() ? ($location->getParent()->hasParent() ? $location->getParent()->getParent()->getName() : '') : ''
            ]
            : ['', '', '', ''];

        $content = str_replace($search, $replace, $content);

        $locationContent = $location ? $location->getContent() : null;
        $content = preg_replace_callback('/#content_([a-z0-9_]+)#/si', function ($matches) use ($locationContent){

            $code   = strtoupper(strtoupper($matches[1]));
            $value  =
                $locationContent instanceof \TwoFingers\Location\Entity\Content
                    ? ($code == Content::PROPERTY_DOMAIN
                        ? $locationContent->getDomain()
                        : $locationContent->getValue($code))
                    : '';

            return is_array($value) ? implode('/', $value) : $value;

        }, $content);
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
        if ($request->isAdminSection())
            return;

        // try to build by hash
        if (!empty($request->get('tfl'))) {
            $location = LocationEntity::buildByHash($request->get('tfl'));
            if ($location)
                Storage::setLocation($location);
        }

        // try to detect location
        if (Storage::isEmpty())
            Storage::setLocation(LocationEntity::buildCurrent());

        // filter location
        if (!Storage::isEmpty() && (Settings::get('TF_LOCATION_FILTER_BY_SITE_LOCATIONS') == 'Y')) {
            $locationName = LocationModel::hasLocations(LANGUAGE_ID, SITE_ID)
                ? LocationModel::getNameByPrimary(Storage::getCityId(), LANGUAGE_ID, SITE_ID)
                : LocationModel::getNameByPrimary(Storage::getCityId(), LANGUAGE_ID, false);

            if (empty($locationName))
                Storage::clear();
        }

        // default, if still empty
        if (Storage::isEmpty())
            Storage::setLocation(LocationEntity::buildDefault());

        $event = new Event("twofingers.location", "afterLocationDetect");
        $event->send();

        // subdomain redirect if needed
        if (!Storage::isEmpty() && (Settings::get('TF_LOCATION_REDIRECT') == 'C')) {
            $domain = Storage::getLocation()->getDomain();

            if ($domain) {
                $cleanDomain = Tools::clearDomain($domain);
                if (mb_strpos($_SERVER['HTTP_HOST'], $cleanDomain) !== 0) {
                    header('Location: ' . $domain . $_SERVER['REQUEST_URI'], true, 301);
                    die();
                }
            }
        }
    }

    /**
     * @param $fields
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function onAfterIBlockElementUpdateHandler($fields)
    {
        if (LocationIblock::getId() != $fields['IBLOCK_ID'])
            return;

        $defaultProperty = LocationIblock::getPropertyByCode(LocationIblock::PROPERTY_DEFAULT);
        if (!isset($fields['PROPERTY_VALUES'][$defaultProperty['ID']]))
            return;

        $siteProperty = LocationIblock::getPropertyByCode(LocationIblock::PROPERTY_SITE_ID);
        $sites        = [];
        foreach ($fields['PROPERTY_VALUES'][$siteProperty['ID']] as $valueId => $siteValue)
            $sites[] = $siteValue['VALUE'];

        // try to find another default locations
        $filter = ['IBLOCK_ID' => LocationIblock::getId(), '!ID' => $fields['ID'], '!PROPERTY_' . LocationIblock::PROPERTY_DEFAULT => false, 'PROPERTY_' . LocationIblock::PROPERTY_SITE_ID => count($sites) ? $sites : false,];

        $elements = \CIBlockElement::GetList([], $filter, false, false, ['ID', 'IBLOCK_ID']);

        while ($element = $elements->Fetch())
            \CIBlockElement::SetPropertyValuesEx($element['ID'], $element['IBLOCK_ID'], [LocationIblock::PROPERTY_DEFAULT => false]);
    }
}

if (Loader::includeModule('sale'))
{
    $eventManager = EventManager::getInstance();

    if (CheckVersion(TwoFingersLocation::getModuleVersion('sale'), '16.0.26'))
    {
        $eventManager->addEventHandler("sale", "OnSaleComponentOrderResultPrepared", ["\TwoFingersLocation", "setZip"]);
        $eventManager->addEventHandler("sale", "OnSaleComponentOrderProperties", ["\TwoFingersLocation", "setSaleLocation"]);
    }
    else
    {
        $eventManager->addEventHandler("sale", "OnSaleComponentOrderOneStepOrderProps", ["\TwoFingersLocation", "setSaleLocationOld"]);
    }

    $eventManager->addEventHandler("iblock", "OnIBlockPropertyBuildList", ["\TwoFingers\Location\Property\Location", "GetUserTypeDescription"]);
}

CModule::AddAutoloadClasses(
    'twofingers.location',
    array(
        'TF_LOCATION_Settings'      => 'classes/settings.php',
        'TF_LOCATION_Helpers'       => 'classes/helpers.php',
        'TF_LOCATION_Location'      => 'classes/location.php',
        'TF_LOCATION_SaleLocation'  => 'classes/salelocation.php',
    )
);

?>