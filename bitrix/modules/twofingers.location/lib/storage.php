<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 12.07.2019
 * Time: 15:47
 *
 *
 */

namespace TwoFingers\Location;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentNullException;
use \Bitrix\Main\ArgumentException;
use \Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Web\Cookie;
use \TwoFingers\Location\Entity\Location as LocationEntity;

/**
 * Class Storage
 *
 * @package TwoFingers\Location
 *
 * @method static getCityId(string $driver = null)
 * @method static setCityId(int $id, string $driver = null)
 * @method static getCityName(string $driver = null)
 * @method static setCityName(string $name, string $driver = null)
 * @method static getRegionId(string $driver = null)
 * @method static setRegionId(int $id, string $driver = null)
 * @method static getRegionName(string $driver = null)
 * @method static setRegionName(string $name, string $driver = null)
 * @method static getCountryId(string $driver = null)
 * @method static setCountryId(int $id, string $driver = null)
 * @method static getCountryName(string $driver = null)
 * @method static setCountryName(string $name, string $driver = null)
 * @method static getNeedCheck(string $driver = null)
 * @method static isNeedCheck(string $driver = null)
 * @method static setNeedCheck(string $flag)
 * @method static getConfirmPopupClosed(string $driver = null)
 * @method static isConfirmPopupClosed(string $driver = null)
 * @method static setConfirmPopupClosed(string $flag, string $driver = null)
 */
class Storage
{
    const TYPE__COOKIE  = 'cookie';
    const TYPE__SESSION = 'session';

    const CITY_ID       = 'city_id';
    const CITY_NAME     = 'city_name';
    const REGION_ID     = 'region_id';
    const REGION_NAME   = 'region_name';
    const COUNTRY_ID    = 'country_id';
    const COUNTRY_NAME  = 'country_name';
    const NEED_CHECK    = 'need_check';
    const CONFIRM_POPUP_CLOSED  = 'confirm_popup_closed';

    protected static $data = [];

    /** @var string[]  */
    protected static $keys = [
        self::CITY_ID, self::CITY_NAME, self::REGION_ID, self::REGION_NAME, self::COUNTRY_ID,
        self::COUNTRY_NAME, self::NEED_CHECK, self::CONFIRM_POPUP_CLOSED
    ];

    /**
     * @param null $driver
     * @return bool
     */
    public static function isEmpty($driver = null): bool
    {
        return empty(self::getCityId($driver));
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|mixed|string|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     */
    public static function __callStatic($name, $arguments)
    {

        if ((strpos($name, 'get') === 0)
            || (strpos($name, 'set') === 0))
        {
            $optionName = mb_substr($name, 3);
            $optionName = Options::fromCamelCase($optionName, '_');

            if (strpos($name, 'get') === 0)
                return self::getValue($optionName, $arguments[0]);
            else
                self::setValue($optionName, $arguments[0], $arguments[1]);
        }

        if (strpos($name, 'is') === 0)
        {
            $optionName = mb_substr($name, 2);
            $optionName = Options::fromCamelCase($optionName, '_');
            $value      = self::getValue($optionName, $arguments[0]);

            return ($value == 'Y') || ($value === true);
        }
    }

    /**
     * @param LocationEntity|null $location
     * @param null                $driver
     * @throws ArgumentException
     * @throws ArgumentOutOfRangeException
     */
    public static function setLocation(LocationEntity $location = null, $driver = null)
    {
        if (is_null($location))
            return;

        self::setCityId($location->getPrimary(), $driver);
        self::setCityName($location->getName(), $driver);

        $regionLocation = $location->getParent();
        if ($regionLocation instanceof LocationEntity)
        {
            self::setRegionId($regionLocation->getPrimary(), $driver);
            self::setRegionName($regionLocation->getName(), $driver);

            $countryLocation = $regionLocation->getParent();
            if ($countryLocation instanceof LocationEntity)
            {
                self::setCountryId($countryLocation->getPrimary(), $driver);
                self::setCountryName($countryLocation->getName(), $driver);
            }
        }

        self::setNeedCheck('Y');
    }

    /**
     * @param null $driver
     * @return LocationEntity|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     */
    public static function getLocation($driver = null): ?LocationEntity
    {
        $countryLocation = self::getLocationByIdNameKeys(self::COUNTRY_ID, self::COUNTRY_NAME, null, $driver);
        $regionLocation = self::getLocationByIdNameKeys(self::REGION_ID, self::REGION_NAME, $countryLocation, $driver);

        return self::getLocationByIdNameKeys(self::CITY_ID, self::CITY_NAME, $regionLocation, $driver);
    }

    /**
     * @param                      $idKey
     * @param                      $nameKey
     * @param LocationEntity|null $parent
     * @param null                 $driver
     * @return LocationEntity|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     */
    protected static function getLocationByIdNameKeys($idKey, $nameKey, LocationEntity $parent = null, $driver = null): ?LocationEntity
    {
        $location = null;
        if (self::getValue($idKey, $driver))
        {
            $location = LocationEntity::buildByPrimary(self::getValue($idKey, $driver), $parent);
            if (self::getValue($nameKey))
                $location->setName(self::getValue($nameKey, $driver));
        } elseif (self::getValue($nameKey, $driver)) {
            $location = LocationEntity::buildByName(self::getValue($nameKey, $driver), $parent);
        }

        return $location;
    }

    /**
     * @param      $key
     * @param null $driver
     * @return mixed|string|null
     */
    public static function getValue($key, $driver = null)
    {
        $key = trim($key);
        if (empty($key))
            return null;

        if (isset(self::$data[$key]))
            return self::$data[$key];

        if (self::getDriver($driver) == self::TYPE__COOKIE)
        {
            self::$data[$key] = Application::getInstance()->getContext()->getRequest()->getCookie("tfl__" . $key);
        } else {
            self::$data[$key] = isset($_SESSION['TF_LOCATION'][$key]) ? $_SESSION['TF_LOCATION'][$key] : null;
        }

        return self::$data[$key];
    }

    /**
     * @param       $key
     * @param       $value
     * @param null  $driver
     * @throws ArgumentException
     * @throws ArgumentNullException
     */
    public static function setValue($key, $value, $driver = null)
    {
        $key = trim($key);
        if (!strlen($key))
            throw new ArgumentNullException('key');

        if (self::getDriver($driver) == self::TYPE__COOKIE)
        {
            $lifetime = intval(Options::getCookieLifetime());

            $cookie = new Cookie("tfl__" . $key, $value, time() + 60 * 60 * 24 * $lifetime);
            $cookie->setHttpOnly(false);
            $cookie->setSecure(false);

            Application::getInstance()->getContext()->getResponse()->addCookie($cookie);

        } else {
            $_SESSION['TF_LOCATION'][$key] = $value;
        }

        self::$data[$key] = $value; // local cache
    }

    /**
     * @param $driver
     * @return string
     */
    protected static function getDriver($driver): string
    {
        return in_array($driver, [self::TYPE__COOKIE, self::TYPE__SESSION])
            ? $driver
            : (intval(Options::getCookieLifetime()) ? self::TYPE__COOKIE : self::TYPE__SESSION);
    }

    /**
     * @param null $driver
     */
    public static function clear($driver = null)
    {
        if (self::getDriver($driver) == self::TYPE__COOKIE)
        {
            foreach (self::$keys as $key)
            {
                $cookie = new Cookie("tfl__" . $key, '', time() - 60 * 60 * 24 * 7);
                $cookie->setHttpOnly(false);
                $cookie->setSecure(false);

                Application::getInstance()->getContext()->getResponse()->addCookie($cookie);
            }
        } else {
            unset($_SESSION['TF_LOCATION']);
        }

        self::$data = [];
    }
}