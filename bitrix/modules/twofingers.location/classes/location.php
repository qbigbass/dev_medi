<?php

/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 09.11.2018
 * Time: 13:53
 *
 */
use \TwoFingers\Location\Helper\Ip;
use \TwoFingers\Location\Model\Location\Internal;
use \TwoFingers\Location\Model\Location;
use \TwoFingers\Location\Current;
use TwoFingers\Location\Request;

/**
 * Class TF_LOCATION_Location
 *
 * @deprecated
 */
class TF_LOCATION_Location
{
    /** @var array */
    protected static $currentLocation;
    
    /**
     * @param mixed|string $langId
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentOutOfRangeException
     * @deprecated
     */
    public static function getCurrent($langId = LANGUAGE_ID)
    {
        if (is_null(self::$currentLocation))
        {
            self::$currentLocation = Current::getByIp(Ip::getCur());

            if (!empty(self::$currentLocation['city']['id'])) {

                $langId = trim($langId);
                if (!strlen($langId)) $langId = LANGUAGE_ID;

                $langId = strtolower($langId);

                self::$currentLocation['city_name'] = isset(self::$currentLocation['city']['name_' . $langId])
                    ? self::$currentLocation['city']['name_' . $langId]
                    : self::$currentLocation['city']['name_ru'];

                self::$currentLocation['sale_location_code'] = Location::getIdByName(self::$currentLocation['city_name'], $langId);

                self::$currentLocation['city_id'] = isset(self::$currentLocation['sale_location_code'])
                    ? self::$currentLocation['sale_location_code']
                    : self::$currentLocation['city']['id'];
            }
        }

        return self::$currentLocation;
    }




    /**
     * @param mixed|string $langId
     * @return mixed
     * @author Pavel Shulaev (https://rover-it.me)
     * @deprecated
     */
    public static function getCurrentCityName($langId = LANGUAGE_ID)
    {
        $location = self::getCurrent($langId);
        if (isset($location['city_name']))
            return $location['city_name'];

        return null;
    }

}