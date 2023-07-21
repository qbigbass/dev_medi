<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 10.11.2018
 * Time: 18:52
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

use Bitrix\Main\ArgumentException;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use TwoFingers\Location\Model\Location\Sale2;
use TwoFingers\Location\Model\Location\Sale;
use \TwoFingers\Location\Model\Location;
use TwoFingers\Location\Request;

/**
 * Class TF_LOCATION_SaleLocation
 *
 * @author Pavel Shulaev (https://rover-it.me)
 * @deprecated
 */
class TF_LOCATION_SaleLocation
{
    /**
     * @return bool
     * @author Pavel Shulaev (https://rover-it.me)
     * @deprecated
     */
    public static function is20()
    {
        return Location::getType() == Location::TYPE__SALE_2;
    }

    /**
     * @param string       $cityName
     * @param mixed|string $langId
     * @return null
     * @author Pavel Shulaev (https://rover-it.me)
     * @deprecated
     */
    public static function getId($cityName = '', $langId = LANGUAGE_ID)
    {
        if (!$cityName)
            $cityName = TF_LOCATION_Location::getCurrentCityName($langId);

        try {
            return (self::is20())
                ? Sale2::getIdByName($cityName, $langId)
                : Sale::getIdByName($cityName, $langId);

        } catch (\Exception $e) {
            return null;
        }
    }

 
}