<?php

namespace TwoFingers\Location\Service;

use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Service\GeoIp\Base;
use Bitrix\Main\Service\GeoIp\Data;
use Bitrix\Main\Service\GeoIp\ProvidingData;
use Bitrix\Main\Service\GeoIp\Result;
use TwoFingers\Location\Entity\Location;
use TwoFingers\Location\Factory\LocationFactory;

class GeoIpService extends Base
{

    public function getTitle(): string
    {
        return Loc::getMessage('tf-location__geo-ip-service-name');
    }

    public function getDescription(): string
    {
        return Loc::getMessage('tf-location__geo-ip-service-description');
    }

    public function getDataResult($ip, $lang = ''): Result
    {
        $dataResult = new Result;
        $geoData    = new Data();

        $geoData->ip   = $ip;
        $geoData->lang = $lang = strlen($lang) > 0 ? $lang : LANGUAGE_ID;

        if (Loader::includeModule('twofingers.location')) {
            $lang     = strlen($lang) ? $lang : LANGUAGE_ID;
            $location = LocationFactory::buildByIp($ip, null, $lang);
            if ($location) {
                $country   = LocationFactory::buildParent($location, Location::TYPE_COUNTRY);
                $region    = LocationFactory::buildParent($location, Location::TYPE_REGION);
                $subRegion = LocationFactory::buildParent($location, Location::TYPE_SUBREGION);

                $geoData->countryName   = isset($country) ? $country->getName() : null;
                $geoData->countryCode   = isset($country) ? $country->getCode() : null;
                $geoData->regionName    = isset($region) ? $region->getName() : null;
                $geoData->regionCode    = isset($region) ? $region->getCode() : null;
                $geoData->subRegionName = isset($subRegion) ? $subRegion->getName() : null;
                $geoData->subRegionCode = isset($subRegion) ? $subRegion->getCode() : null;
                $geoData->cityName      = $location->getName();
                $geoData->latitude      = $location->getLatitude();
                $geoData->longitude     = $location->getLongitude();
                $geoData->zipCode       = $location->getZip();
                //$geoData->timezone = ();
            } else {
                $dataResult->addError(new Error(Loc::getMessage('tf-location__no-location')));
            }
        } else {
            $dataResult->addError(new Error(Loc::getMessage('tf-location__no-module')));
        }

        $dataResult->setGeoData($geoData);
        return $dataResult;
    }

    /**
     * @return ProvidingData Geolocation information witch handler can return.
     */
    public function getProvidingData()
    {
        $result = new ProvidingData();

        $result->countryName   = true;
        $result->countryCode   = true;
        $result->regionName    = true;
        $result->regionCode    = true;
        $result->cityName      = true;
        $result->latitude      = true;
        $result->longitude     = true;
        $result->zipCode       = true;
        $result->subRegionName = true;
        $result->subRegionCode = true;
        //     $result->timezone = true;
        return $result;
    }

    /**
     * Languages supported by handler ISO 639-1
     * @return array
     */
    public function getSupportedLanguages()
    {
        return array('en', 'ru');
    }
}