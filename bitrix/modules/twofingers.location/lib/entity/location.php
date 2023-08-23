<?php
/**
 * Created by PhpStorm.
 * User: Павел
 * Date: 11/17/2020
 * Time: 2:16 PM
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

namespace TwoFingers\Location\Entity;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Service\GeoIp\Manager;
use Bitrix\Main\SystemException;
use TwoFingers\Location\Helper\Ip;
use TwoFingers\Location\Helper\Tools;
use TwoFingers\Location\Model\Location as LocationModel;
use \TwoFingers\Location\Entity\Content as ContentEntity;
use TwoFingers\Location\Options;
use TwoFingers\Location\Service\SxGeo;

/**
 * Class Location
 *
 * @package TwoFingers\Location\Entity
 * @method setPrimary($primary): Location
 * @method setParent($parent)
 * @method setLangId($langId)
 * @method setLat($lat)
 * @method setLon($lon)
 * @method setName($name)
 * @method setSiteId($siteId)
 * @method getParent($parent = ''): ?Location
 * @method getLangId($langId = '')
 * @method getLat($lat = '')
 * @method getLon($lon = '')
 * @method getSiteId($siteId = '')
 * @method getShowRegion($default = 'N')
 * $method setShowRegion($flag)
 */
class Location
{
    protected $data = [];

    /** @var ContentEntity */
    protected $content;
    /**
     * Location constructor.
     */
    protected function __construct(){}

    /**
     * @param $name
     * @param $arguments
     * @return $this|mixed
     */
    public function __call($name, $arguments)
    {
        if ((strpos($name, 'get') === 0) || (strpos($name, 'set') === 0))
        {
            $optionName = mb_substr($name, 3);
            $optionName = Options::fromCamelCase($optionName);

            if (strpos($name, 'get') === 0)
                return $this->getField($optionName, $arguments[0]);

            return $this->setField($optionName, $arguments[0]);
        }
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     */
    public function setField($field, $value): Location
    {
        $this->data[$field] = $value;
        return $this;
    }

    /**
     * @param      $field
     * @param null $default
     * @return mixed|null
     */
    public function getField($field, $default = null)
    {
        return array_key_exists($field, $this->data) ? $this->data[$field] : $default;
    }

    /**
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return Location|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function buildCurrent($langId = LANGUAGE_ID, $siteId = SITE_ID): ?Location
    {
        return self::buildByIp(null, $langId, $siteId);
    }

    /**
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return Location|null
     * @throws ArgumentNullException
     */
    public static function buildDefault($langId = LANGUAGE_ID, $siteId = SITE_ID):? Location
    {
        $default = LocationModel::getDefault($langId, $siteId);
        if (!$default)
            return null;

        $country = $region = null;
        if (isset($default['COUNTRY_ID']) || isset($default['COUNTRY_NAME']))
            $country = self::buildByPrimaryName($default['COUNTRY_ID'], $default['COUNTRY_NAME'], null, $langId, $siteId);

        if (isset($default['REGION_ID']) || isset($default['REGION_NAME']))
            $region = self::buildByPrimaryName($default['REGION_ID'], $default['REGION_NAME'], $country, $langId, $siteId);

        return self::buildByPrimaryName($default['CODE'], $default['NAME'], $region, $langId, $siteId);
    }

    /**
     * @param null         $ip
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return Location|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function buildByIp($ip = null, $langId = LANGUAGE_ID, $siteId = SITE_ID): ?Location
    {
        if (is_null($ip))
            $ip = Manager::getRealIp();

        $ip = trim($ip);
        if (!Ip::isValid($ip))
            throw new ArgumentOutOfRangeException('ip');

        $data = SxGeo::getLocation($ip);

        if (empty($data['city']['id']))
            return null;

        $countryLocation    = self::buildBySxGeoData($data['country'], null, $langId, $siteId);
        $regionLocation     = self::buildBySxGeoData($data['region'], $countryLocation, $langId, $siteId);

        return self::buildBySxGeoData($data['city'], $regionLocation, $langId, $siteId);
    }

    /**
     * @param               $data
     * @param Location|null $parent
     * @param mixed|string  $langId
     * @param mixed|string  $siteId
     * @return Location|null
     * @throws ArgumentNullException
     */
    protected static function buildBySxGeoData($data, Location $parent = null, $langId = LANGUAGE_ID, $siteId = SITE_ID): ?Location
    {
        if (empty($data['id']))
            return null;

        $name = isset($data['name_' . strtolower($langId)])
            ? $data['name_' . strtolower($langId)]
            : $data['name_ru'];

        $name       = iconv('UTF-8', LANG_CHARSET, $name);
        $location   = self::buildByName($name, $parent, $langId, $siteId);

        if (isset($data['lat']))
            $location->setLat($data['lat']);

        if (isset($data['lon']))
            $location->setLon($data['lon']);

        return $location;
    }

    /**
     * @param               $primary
     * @param Location|null $parent
     * @param mixed|string  $langId
     * @param mixed|string  $siteId
     * @return Location
     * @throws ArgumentNullException
     */
    public static function buildByPrimary($primary, Location $parent = null, $langId = LANGUAGE_ID, $siteId = SITE_ID): Location
    {
        $primary = trim($primary);
        if (!strlen($primary))
            throw new ArgumentNullException('primary');

        return (new self())
            ->setPrimary($primary)
            ->setParent($parent)
            ->setLangId($langId)
            ->setSiteId($siteId);
    }

    /**
     * @param $hash
     * @return Location
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function buildByHash($hash): Location
    {
        /** @var array $data */
        $data = unserialize(base64_decode($hash));
        if (!isset($data['location_id']) && !isset($data['location_name']))
            throw new ArgumentOutOfRangeException('hash');

        $langId = isset($data['lang_id']) ? $data['lang_id'] : LANGUAGE_ID;
        $siteId = isset($data['site_id']) ? $data['site_id'] : SITE_ID;
        $country = $region = null;
        if (isset($data['country_id']) || isset($data['country_name']))
            $country = self::buildByPrimaryName($data['country_id'], $data['country_name'], null, $langId, $siteId);

        if (isset($data['region_id']) || isset($data['region_name']))
            $region = self::buildByPrimaryName($data['region_id'], $data['region_name'], $country, $langId, $siteId);

        return self::buildByPrimaryName($data['location_id'], $data['location_name'], $region, $langId, $siteId);
    }

    /**
     * @return false|string
     * @throws ArgumentOutOfRangeException
     */
    public function getHash()
    {
        $data = [
            'location_id' => $this->getPrimary(),
            'location_name' => $this->getName(),
            'region_id' => $this->hasParent() ? $this->getParent()->getPrimary() : null,
            'region_name' => $this->hasParent() ? $this->getParent()->getName() : null,
            'country_id' => $this->hasParent() && $this->getParent()->hasParent()
                ? $this->getParent()->getParent()->getPrimary() : null,
            'country_name' =>$this->hasParent() && $this->getParent()->hasParent()
                ? $this->getParent()->getParent()->getName() : null,
            'lang_id' => $this->getLangId(),
            'site_id' => $this->getSiteId(),
        ];

        return base64_encode(serialize($data));
    }

    /**
     * @param               $primary
     * @param               $name
     * @param Location|null $parent
     * @param mixed|string  $langId
     * @param mixed|string  $siteId
     * @return Location
     * @throws ArgumentNullException
     */
    public static function buildByPrimaryName($primary, $name, Location $parent = null, $langId = LANGUAGE_ID, $siteId = SITE_ID): Location
    {
        $primary    = trim($primary);
        $name       = trim($name);

        if (!strlen($name) && !strlen($primary))
            throw new ArgumentNullException('primary and name');

        return (new self())
            ->setPrimary($primary)
            ->setName($name)
            ->setParent($parent)
            ->setLangId($langId)
            ->setSiteId($siteId);
    }

    /**
     * @param               $name
     * @param mixed|string  $langId
     * @param mixed|string  $siteId
     * @param Location|null $parent
     * @return Location|null
     * @throws ArgumentNullException
     */
    public static function buildByName($name, Location $parent = null, $langId = LANGUAGE_ID, $siteId = SITE_ID): ?Location
    {
        $name = trim($name);
        if (!strlen($name))
            throw new ArgumentNullException('name');

        return (new self())
            ->setName($name)
            ->setParent($parent)
            ->setLangId($langId)
            ->setSiteId($siteId);
    }

    /**
     * @return int|string|null
     * @throws ArgumentOutOfRangeException
     */
    public function getPrimary()
    {
        if (!isset($this->data['primary']))
            $this->data['primary'] = $this->getSiteId() && LocationModel::hasLocations($this->getLangId(), $this->getSiteId())
                ? LocationModel::getIdByName($this->getName(), $this->getLangId(), $this->getSiteId())
                : LocationModel::getIdByName($this->getName(), $this->getLangId(), false);

        return $this->data['primary'];
    }

    /**
     * @return string|null
     * @throws ArgumentOutOfRangeException
     */
    public function getName()
    {
        if (!isset($this->data['name']))
        {
            $this->data['name'] = $this->getSiteId() && LocationModel::hasLocations($this->getLangId(), $this->getSiteId())
                ? LocationModel::getNameByPrimary($this->getPrimary(), $this->getLangId(), $this->getSiteId())
                : LocationModel::getNameByPrimary($this->getPrimary(), $this->getLangId(), false);

            if (!strlen($this->data['name']))
                throw new ArgumentOutOfRangeException('primary');
        }

        return $this->data['name'];
    }

    /**
     * @return bool
     */
    public function hasParent(): bool
    {
        return $this->getParent() instanceof self;
    }

    /**
     * @return mixed|string|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     */
    public function getDomain(): ?string
    {
        $content = $this->getContent();

        return $content ? $content->getDomain() : null;
    }

    /**
     * @return Content|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     */
    public function getContent(): ?Content
    {
        if (is_null($this->content))
            $this->content = ContentEntity::buildByLocation($this);

        // try to build by parent
        if (is_null($this->content) && $this->hasParent())
            $this->content = ContentEntity::buildByLocation($this->getParent());

        // try to build by parent parent
        if (is_null($this->content) && $this->hasParent() && $this->getParent()->hasParent())
            $this->content = ContentEntity::buildByLocation($this->getParent()->getParent());

        // default for cur site
        if (is_null($this->content))
        {
            $location = self::buildDefault($this->getLangId(), $this->getSiteId());
            if ($location)
                $this->content = ContentEntity::buildByLocation($location);
        }

        // default for all sites
        if (is_null($this->content))
        {
            $location = self::buildDefault($this->getLangId(), false);
            if ($location)
                $this->content = ContentEntity::buildByLocation($location);
        }

        return $this->content;
    }

    /**
     * @return string
     * @throws ArgumentOutOfRangeException
     */
    public function getCode(): ?string
    {
        return Tools::translit($this->getName(), $this->getLangId());
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}