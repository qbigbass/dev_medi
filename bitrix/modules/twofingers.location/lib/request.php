<?php

namespace TwoFingers\Location;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Uri;
use TwoFingers\Location\Helper\Tools;
use \TwoFingers\Location\Model\Location as LocationModel;
use Bitrix\Main\Web\Json;
use \TwoFingers\Location\Entity\Location as LocationEntity;

/**
 * Class Request
 *
 * @package TwoFingers\Location\Service
 */
class Request
{
    const ACTION__SETCITY   = 'setcity';
    const ACTION__GETCITIES = 'getcities';
    const ACTION__CLOSE_CONFIRM_POPUP = 'close_confirm_popup';
    const ACTION__FIND      = 'find';

    /**
     * @param $action
     * @return null
     */
    public static function handle($action)
    {
        $action = trim($action);

        if (!strlen($action) || !in_array($action, [
            self::ACTION__SETCITY, self::ACTION__GETCITIES, self::ACTION__CLOSE_CONFIRM_POPUP, self::ACTION__FIND]))
        {
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

        return Json::encode(['status' => 'success']);
    }

    /**
     * @return mixed
     * @throws ArgumentException
     */
    protected static function actionGetcities()
    {
        try{
            $favorites  = LocationModel::getFavoritesList();
            $action     = self::getRequest()->get('type');

            switch ($action):
                case 'cities':
                    $cities = (bool)LocationModel::getCitiesList(LANGUAGE_ID, SITE_ID)
                        ? LocationModel::getCitiesList(LANGUAGE_ID, SITE_ID)
                        : LocationModel::getCitiesList(LANGUAGE_ID, false);
                    break;
                case 'defaults':
                    $cities = [];
                    break;
                default:
                    $cities = (bool)LocationModel::getList(LANGUAGE_ID, SITE_ID)
                        ? LocationModel::getList(LANGUAGE_ID, SITE_ID)
                        : LocationModel::getList(LANGUAGE_ID, false);
            endswitch;

        } catch (\Exception $e) {
            $cities = $favorites = [];
        }

        $cities     = self::markSameNames($cities);
        $favorites  = self::markSameNames($favorites);

        $response = [
            'CITIES'            => array_values($cities),
            'DEFAULT_CITIES'    => array_values($favorites)
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
        $request    = self::getRequest();
        $locationId = trim($request->get('location_id'));

        if (!strlen($locationId))
            $locationId = trim($request->get('city')); // old style

        if (!strlen($locationId))
            return Json::encode([
                'status'    => 'error',
                'message'   => 'location not found'
            ]);

        $locationName = trim($request->get('location_name'));
        if (!strlen($locationName))
            $locationName = LocationModel::hasLocations(LANGUAGE_ID, SITE_ID)
                ? LocationModel::getNameByPrimary($locationId, LANGUAGE_ID, SITE_ID)
                : LocationModel::getNameByPrimary($locationId, LANGUAGE_ID, false);

        $reload     = Settings::get('TF_LOCATION_RELOAD') == 'Y';
        $redirect   = false;

        if ($locationName)
        {
            $countryLocation = $regionLocation = null;
            if (($request->get('country_name') != 'undefined') || ($request->get('country_id') != 'undefined'))
                try{
                    $countryLocation = LocationEntity::buildByPrimaryName(
                        $request->get('country_id') == 'undefined' ? '' : $request->get('country_id'),
                        $request->get('country_name') == 'undefined' ? '' : $request->get('country_name')
                    );
                } catch (\Exception $e) {}

            if (($request->get('region_name') != 'undefined') || ($request->get('region_id') != 'undefined'))
                try{
                    $regionLocation = LocationEntity::buildByPrimaryName(
                        $request->get('region_id') == 'undefined' ? '' : $request->get('region_id'),
                        $request->get('region_name') == 'undefined' ? '' : $request->get('region_name'),
                        $countryLocation
                    );
                } catch (\Exception $e) {}

            try{
                $location = LocationEntity::buildByPrimaryName(
                    $locationId,
                    iconv('UTF-8', LANG_CHARSET, $locationName),
                    $regionLocation
                );
            } catch (\Exception $e) {
                return Json::encode([
                    'status'    => 'error',
                    'message'   => $e->getMessage()
                ]);
            }

            Storage::setLocation($location);

            if (Settings::get('TF_LOCATION_REDIRECT') != 'N')
            {
                $domain = $location->getDomain();

                if (strlen($domain))
                {
                    $cleanDomain = Tools::clearDomain($domain);
                    $requestUri = $request->get('requestUri') ? : '/';
                    $uri        = new Uri($domain . $requestUri);

                    if (mb_strpos($_SERVER['HTTP_HOST'], $cleanDomain) !== 0) {
                        $uri->addParams(['tfl' => $location->getHash()]);
                        $redirect = $uri->getUri();
                    } elseif ($reload) {
                        $uri->deleteParams(['tfl']);
                        $redirect = $uri->getUri();
                    }
                }
            }
        } else {
            Storage::clear();
        }

        $response = [
            'status' => 'success',
        ];

        if (strlen($redirect)) {
            $response['redirect']   = $redirect;
        } else {
            $response['reload']     = $reload;
        }

        return Json::encode($response);
    }

    /**
     * @return mixed
     * @throws ArgumentException
     */
    protected static function actionFind()
    {
        $result = [];
        $q = self::getRequest()->get('q');

        if (strlen($q))
        {
            $result = LocationModel::hasLocations(LANGUAGE_ID, SITE_ID)
                ? LocationModel::find($q, LANGUAGE_ID, SITE_ID)
                : LocationModel::find($q, LANGUAGE_ID, false);

            $result = self::markSameNames($result);
        }

        $response = ['CITIES' => array_values($result)];

        return Json::encode($response);
    }

    /**
     * @param $cities
     * @return array
     */
    public static function getSameNames($cities): array
    {
        $sameNames = [];
        // setting the same cities
        foreach ($cities as $cityId => $city)
            $sameNames[$city['NAME']][] = $city['ID'];

        return $sameNames;
    }

    /**
     * @param $cities
     * @return mixed
     */
    public static function markSameNames($cities)
    {
        $sameNames = self::getSameNames($cities);

        foreach ($sameNames as $name => $citiesIds){
            if (count($citiesIds) < 2) continue;

            foreach ($citiesIds as $cityId)
                $cities[$cityId]['SHOW_REGION'] = 'Y';
        }

        return $cities;
    }
}