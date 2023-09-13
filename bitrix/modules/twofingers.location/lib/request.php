<?php

namespace TwoFingers\Location;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Context;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SiteTable;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Uri;
use Exception;
use TwoFingers\Location\Entity\Location;
use TwoFingers\Location\Factory\ContentFactory;
use TwoFingers\Location\Factory\LocationFactory;
use TwoFingers\Location\Helper\Tools;
use TwoFingers\Location\Model\Location as LocationModel;
use Bitrix\Main\Web\Json;

/**
 * Class Request
 *
 * @package TwoFingers\Location\Service
 */
class Request
{
    const ACTION__SETCITY             = 'setcity';
    const ACTION__GETCITIES           = 'getcities';
    const ACTION__CLOSE_CONFIRM_POPUP = 'close_confirm_popup';
    const ACTION__FIND                = 'find';

    /**
     * @param $action
     * @return null
     */
    public static function handle($action)
    {
        $action = trim($action);

        if (!strlen($action) || !in_array($action, [
                self::ACTION__SETCITY,
                self::ACTION__GETCITIES,
                self::ACTION__CLOSE_CONFIRM_POPUP,
                self::ACTION__FIND
            ])) {
            return null;
        }

        $actionMethod = 'action' . ucfirst($action);

        return self::$actionMethod();
    }

    /**
     * @return HttpRequest|\Bitrix\Main\Request
     */
    protected static function getRequest()
    {
        return Application::getInstance()->getContext()->getRequest();
    }

    /**
     * @return mixed
     * @throws ArgumentException
     */
    protected static function actionClose_confirm_popup()
    {
        Storage::setConfirmPopupClosed('Y');

        $response = ['status' => 'success'];
        if ((Context::getCurrent()->getRequest()->get('confirm') == 'Y')
            && Options::hasRedirectEvent(Options::REDIRECT_EVENT_CONFIRMED)) {
            $response['reload'] = true;
        }

        return Json::encode($response);
    }

    /**
     * @return mixed
     * @throws ArgumentException
     */
    protected static function actionGetcities()
    {
        try {
            $defaults = LocationModel::getFavoritesList(SITE_ID, LANGUAGE_ID);
            $action   = self::getRequest()->get('type');

            switch ($action):
                case 'cities':
                    $cities = LocationModel::getCitiesList(LANGUAGE_ID, SITE_ID) ?? [];
                    break;
                case 'defaults':
                    $cities = [];
                    break;
                default:
                    $cities = LocationModel::getList(LANGUAGE_ID, SITE_ID) ?? [];
            endswitch;
        } catch (Exception $e) {
            $cities = $defaults = [];
        }

        $cities   = self::prepareToJs($cities);
        $defaults = self::prepareToJs($defaults);

        $response = [
            'CITIES'         => array_values($cities),
            'DEFAULT_CITIES' => array_values($defaults)
        ];

        return Json::encode($response);
    }

    /**
     * @return mixed|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     */
    protected static function actionSetcity()
    {
        $request      = self::getRequest();
        $locationCode = trim($request->get('location_id')); // @TODO: fix to location code

        if (!mb_strlen($locationCode)) {
            $locationCode = trim($request->get('city'));
        } // old style

        if (!mb_strlen($locationCode)) {
            return Json::encode([
                'status'  => 'error',
                'message' => 'no location id'
            ]);
        }

        $location = LocationFactory::buildByCode($locationCode, SITE_ID, LANGUAGE_ID);

        if (!$location && Options::isCapabilityMode()) {
            $location = LocationFactory::buildById($locationCode, SITE_ID, LANGUAGE_ID);
        }

        if (!$location) {
            return Json::encode([
                'status'  => 'error',
                'message' => 'location not found'
            ]);
        }
        Storage::setLocation($location);

        $reload     = Options::isListReloadPage();
        $redirectTo = false;

        if (Options::hasRedirectEvent(Options::REDIRECT_EVENT_SELECTED)) {
            $domain = $location->getDomain();

            if (mb_strlen($domain)) {
                $uri = new Uri($domain . (self::getCorrectRequestUri() ?: '/'));

                if (mb_strpos($_SERVER['HTTP_HOST'], Tools::clearDomain($domain)) !== 0) {
                    $uri->addParams(['tfl' => $location->getCode()]);
                    $redirectTo = $uri->getUri();
                }
            }
        }

        if ($reload && !$redirectTo && mb_strlen($request->get('tfl'))) {
            $protocol = $request->isHttps() ? 'https://' : 'http://';



            $uri = new Uri($protocol . $request->getHttpHost() . (self::getCorrectRequestUri() ?: '/'));
            $uri->deleteParams(['tfl']);
            $redirectTo = $uri->getUri();
        }

        $content = ContentFactory::buildByLocation($location);

        $response = [
            'status'   => 'success',
            'location' => $location->toArray(),
            'content'  => isset($content) ? $content->toArray() : [],
        ];

        // @deprecated
        if (Options::isCapabilityMode()) {
            $response = array_merge($response, [
                'location_id'   => LocationModel::getType() == LocationModel::TYPE_SALE
                    ? $location->getCode() : $location->getId(),
                'location_name' => $location->getName(),
            ]);
        }

        if (mb_strlen($redirectTo)) {
            $response['redirect'] = $redirectTo;
        } else {
            $response['reload'] = $reload;
        }

        return Json::encode($response);
    }

    /**
     * @return string|null
     */
    private static function getCorrectRequestUri(): ?string
    {
        require_once(Application::getDocumentRoot() . "/bitrix/modules/main/include/mainpage.php");
        $mainPage        = new \CMainPage();
        $curSiteId = $mainPage->GetSiteByHost();

        $site = SiteTable::getRow(['filter' => ['=LID' => $curSiteId], 'select' => ['DIR'] ]);

        $requestUri = Context::getCurrent()->getRequest()->get('requestUri');
        if (strpos($requestUri, $site['DIR'] ?? SITE_DIR) === 0) {
            $requestUri = '/' . substr($requestUri, strlen($site['DIR'] ?? SITE_DIR));
        }

        return $requestUri;
    }

    /**
     * @return mixed
     * @throws ArgumentException
     */
    protected static function actionFind()
    {
        $result = [];
        $q      = self::getRequest()->get('q');

        if (strlen($q)) {
            $result = LocationModel::find($q, LANGUAGE_ID, SITE_ID) ?? [];
            $result = self::prepareToJs($result);
        }

        $response = ['CITIES' => array_values($result)];

        return Json::encode($response);
    }

    /**
     * @param $locationsData
     * @return array
     */
    public static function getSameNames($locationsData): array
    {
        $sameNames = [];
        // setting the same cities
        foreach ($locationsData as $locationData) {
            $sameNames[$locationData[Location::NAME]][] = $locationData[Location::ID];
        }

        return $sameNames;
    }

    /**
     * @param $locations
     * @return array
     * @throws ArgumentNullException
     */
    public static function prepareToJs($locations): array
    {
        $result    = [];
        $sameNames = self::getSameNames($locations);

        foreach ($locations as $locationData) {
            $item = [
                Location::ID   => $locationData[Location::ID],
                Location::NAME => $locationData[Location::NAME],
                Location::CODE => $locationData[Location::CODE],
            ];

            if (Options::isCapabilityMode()) {
                $item['ID']   = $locationData[Location::ID];
                $item['NAME'] = $locationData[Location::NAME];
            }

            if (isset($locationData[Location::PARENT_ID]) && isset($sameNames[$item[Location::NAME]]) && count($sameNames[$item[Location::NAME]]) > 1) {
                $location                  = LocationFactory::buildByData($locationData);
                $locationParentsCollection = LocationFactory::buildParentsCollection($location);


                if ($locationParentsCollection->count()) {
                    $parents = [];
                    /** @var Location $locationParent */
                    foreach ($locationParentsCollection->getCollection() as $locationParent) {
                        if ($locationParent->getType() != Location::TYPE_COUNTRY) {
                            $parents[] = $locationParent->getName();
                        }
                    }

                    $item['description'] = implode(', ', array_reverse($parents));
                }

                if (Options::isCapabilityMode()) {
                    $item['CITY_NAME']   = $locationData['CITY_NAME'] ?? '';
                    $item['REGION_NAME'] = $locationData['REGION_NAME'] ?? '';
                    $item['SHOW_REGION'] = 'Y';
                }
            }

            $result[] = $item;
        }

        return $result;
    }
}