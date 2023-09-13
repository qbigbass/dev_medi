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
use Bitrix\Main\Context;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SiteTable;
use Bitrix\Main\SystemException;
use NCLNameCaseRu;
use TwoFingers\Location\Factory\ContentFactory;
use TwoFingers\Location\Factory\LocationFactory;
use Twofingers\Location\Internal\Collection;
use Twofingers\Location\Internal\HasCollectionTrait;
use TwoFingers\Location\Model\Location as LocationModel;
use TwoFingers\Location\Entity\Content as ContentEntity;
use TwoFingers\Location\Options;

/**
 * Class Location
 *
 * @package TwoFingers\Location\Entity
 * @method setPrimary($primary): Location
 * @method getId()
 * @method getCode()
 * @method setLangId($langId)
 * @method setLatitude($lat)
 * @method setLongitude($lon)
 * @method setName($name)
 * @method setSiteId($siteId)
 * @method getLangId($langId = '')
 * @method getLatitude($lat = '')
 * @method getLongitude($lon = '')
 * @method getSiteId($siteId = '')
 * @method getCustomType
 * @method setCustomType($type)
 * @method setParentId($parentId)
 * @method getParentId()
 * @method getParentCode()
 * @method getType()
 * @method setType($type)
 */
class Location
{
    use HasCollectionTrait;

    public const TYPE_COUNTRY   = 'COUNTRY';
    public const TYPE_REGION    = 'REGION';
    public const TYPE_SUBREGION = 'SUBREGION';
    public const TYPE_CITY      = 'CITY';
    public const TYPE_VILLAGE   = 'VILLAGE';

    const NAME        = 'name';
    const LATITUDE    = 'latitude';
    const LONGITUDE   = 'longitude';
    const ID          = 'id';
    const CODE        = 'code';
    const PARENT_ID   = 'parent_id';
    const PARENT_CODE = 'parent_code';
    const TYPE        = 'type';
    const SOURCE      = 'source';
    const ZIP         = 'zip';
    const SITE_ID     = 'site_id';
    const LANG_ID     = 'lang_id';

    const CASE_IM   = 0;
    const CASE_ROD  = 1;
    const CASE_DAT  = 2;
    const CASE_VIN  = 3;
    const CASE_TVOR = 4;
    const CASE_PRED = 5;

    /** @deprecated */
    const TRANSLIT = 'translit';
    /** @deprecated */
    const PRIMARY = 'primary';
    /** @deprecated */
    const PARENT = 'parent';

    /** @var ContentEntity */
    protected $content;

    /**
     * Location constructor.
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this|mixed
     */
    public function __call($name, $arguments)
    {
        if ((mb_strpos($name, 'get') === 0) || (strpos($name, 'set') === 0)) {
            $optionName = mb_substr($name, 3);
            $optionName = Options::fromCamelCase($optionName, '_');

            if (mb_strpos($name, 'get') === 0) {
                return $this->get($optionName, $arguments[0]);
            }

            return $this->set($optionName, $arguments[0]);
        }

        return null;
    }

    /**
     * @param $offset
     * @param $value
     * @return $this
     */
    public function set($offset, $value): Location
    {
        $this->collection->offsetSet($offset, $value);

        return $this;
    }

    /**
     * @param $offset
     * @param $default
     * @return mixed|null
     */
    public function get($offset, $default = null)
    {
        return $this->collection->offsetGet($offset) ?? $default;
    }

    /**
     * @return bool
     */
    public function hasParent(): bool
    {
        return (bool)$this->getParentId();
    }

    /**
     * @return Location|null
     * @throws ArgumentNullException
     * @deprecated
     */
    public function getParent(): ?Location
    {
        if (!$this->collection->offsetExists(self::PARENT)) {
            $this->set(self::PARENT, LocationFactory::buildParent($this));
        }
        return $this->get(self::PARENT);
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

        if ($content && $content->getDomain()) {
            return $content->getDomain();
        }

        switch (Options::getNoDomainAction()) {
            case Options::NO_DOMAIN_ACTION_CURRENT_SITE:
                $filter = ['=LID' => SITE_ID];
            case Options::NO_DOMAIN_ACTION_DEFAULT_SITE:
                if (!isset($filter)) {
                    $filter = ['=DEF' => 'Y'];
                }

                $query = [
                    'filter' => $filter + ['!SERVER_NAME' => false],
                    'select' => ['SERVER_NAME'],
                    'cache'  => ['ttl' => 3600]
                ];

                $site = SiteTable::getRow($query);
                if (!$site) {
                    return null;
                }

                $protocol = Context::getCurrent()->getRequest()->isHttps() ? 'https://' : 'http://';

                return $protocol . $site['SERVER_NAME'];

            /* case Options::NO_DOMAIN_ACTION_SITE_DEFAULT_LOCATION_DOMAIN:
                 $defaultSiteLocation = Location::buildDefault(LANGUAGE_ID, SITE_ID);
             case Options::NO_DOMAIN_ACTION_ALL_SITES_DEFAULT_LOCATION_DOMAIN:
                 if (!isset($defaultSiteLocation))
                     $defaultSiteLocation = Location::buildDefault(LANGUAGE_ID, false);

                 if (isset($defaultSiteLocation))
                     return $defaultSiteLocation->getContent()->getDomain();*/

            case Options::NO_DOMAIN_ACTION_NONE:
            default:
                return null;
        }
    }

    /**
     * @return mixed|null
     */
    public function getZip()
    {
        if (!$this->getCollection()->offsetExists(self::ZIP)) {
            $this->set(self::ZIP, LocationModel::getZipById($this->getId()));
        }

        return $this->get(self::ZIP);
    }

    /**
     * @return Content|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     * @deprecated
     */
    public function getContent(): ?Content
    {
        if (!isset($this->content)) {
            $this->content = ContentFactory::buildByFirstSuitableLocation($this);
        }

        return $this->content;
    }

    /**
     * @param               $primary
     * @param Location|null $parent
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return Location
     * @throws ArgumentNullException
     * @deprecated delete in 2023
     */
    public static function buildByPrimary(
        $primary,
        Location $parent = null,
        $langId = LANGUAGE_ID,
        $siteId = SITE_ID
    ): Location {
        if ((LocationModel::getType() == LocationModel::TYPE_SALE) && Options::isCapabilityMode()) {
            $location = LocationFactory::buildByCode($primary, $siteId, $langId);
        } else {
            $location = LocationFactory::buildById($primary, $siteId, $langId);
        }

        if (isset($parent)) {
            $location->setParent($parent);
        }

        return $location;
    }

    /**
     * @param               $name
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @param Location|null $parent
     * @return Location|null
     * @throws ArgumentNullException
     * @deprecated delete in 2023
     */
    public static function buildByName(
        $name,
        Location $parent = null,
        $langId = LANGUAGE_ID,
        $siteId = SITE_ID
    ): ?Location {
        $location = LocationFactory::buildByName($name, null, $parent, $siteId, $langId);
        if (isset($parent)) {
            $location->setParent($location);
        }

        return $location;
    }


    /**
     * @param               $primary
     * @param               $name
     * @param Location|null $parent
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return Location
     * @throws ArgumentNullException
     * @deprecated delete in 2023
     */
    public static function buildByPrimaryName(
        $primary,
        $name,
        Location $parent = null,
        $langId = LANGUAGE_ID,
        $siteId = SITE_ID
    ): Location {
        if (!strlen($name) && !strlen($primary)) {
            throw new ArgumentNullException('primary and name');
        }

        if (strlen($name)) {
            $location = LocationFactory::buildByName($name, null, $parent, $siteId, $langId);
        } else {
            $location = self::buildByPrimary($primary, $langId, $siteId);
        }

        if (isset($parent)) {
            $location->setParent($parent);
        }

        return $location;
    }

    /**
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return Location|null
     * @deprecated delete in 2023
     */
    public static function buildDefault($langId = LANGUAGE_ID, $siteId = SITE_ID): ?Location
    {
        return LocationFactory::buildDefault($siteId, $langId);
    }

    /**
     * @param null $ip
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return Location|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @deprecated delete in 2023
     */
    public static function buildByIp($ip = null, $langId = LANGUAGE_ID, $siteId = SITE_ID): ?Location
    {
        return LocationFactory::buildByIp($ip, $siteId, $langId);
    }

    /**
     * @param mixed|string $langId
     * @param mixed|string $siteId
     * @return Location|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @deprecated delete in 2023
     */
    public static function buildCurrent($langId = LANGUAGE_ID, $siteId = SITE_ID): ?Location
    {
        return LocationFactory::buildCurrent($siteId, $langId);
    }

    /**
     * @param $field
     * @param $value
     * @return $this
     * @deprecated
     */
    public function setField($field, $value): Location
    {
        return $this->set($field, $value);
    }

    /**
     * @param      $field
     * @param null $default
     * @return mixed|null
     * @deprecated
     */
    public function getField($field, $default = null)
    {
        return $this->get($field, $default);
    }

    /**
     * @return int|mixed|null
     * @deprecated
     */
    public function getPrimary()
    {
        $primary = $this->get(self::PRIMARY);
        if (!isset($primary)) {
            // @TODO: add sections
            $primary = LocationModel::getIdByName($this->getName(), $this->getLangId(), $this->getSiteId());
            $this->setPrimary($primary);
        }

        return $this->get(self::PRIMARY);
    }

    /**
     * @param $hash
     * @return Location
     * @throws ArgumentNullException
     * @deprecated
     */
    public static function buildByHash($hash): Location
    {
        return LocationFactory::buildByCode($hash, SITE_ID, LANGUAGE_ID);
    }

    /**
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws SystemException
     * @deprecated
     */
    public function loadParents()
    {
        if (false === $this->getParent() instanceof self) {
            $locationData = LocationModel::getByPrimary($this->getPrimary());
            if (isset($locationData['REGION_ID'])) {
                $region = self::buildByPrimary($locationData['REGION_ID'], null, LANGUAGE_ID, SITE_ID);
                if ($region && (LocationModel::getType() == LocationModel::TYPE_IBLOCK)) {
                    $region->setCustomType('section'); // @TODO: refactor
                }
            }

            if (isset($locationData['COUNTRY_ID'])) {
                $country = self::buildByPrimary($locationData['COUNTRY_ID'], null, LANGUAGE_ID, SITE_ID);
                if ($country && (LocationModel::getType() == LocationModel::TYPE_IBLOCK)) {
                    $country->setCustomType('section'); // @TODO: refactor
                }
            }

            if (isset($region)) {
                if (isset($country)) {
                    $region->setParent($country);
                }

                $this->setParent($region);
            } elseif (isset($country)) {
                $this->setParent($country);
            }
        }
    }


    /**
     * @param Location $location
     * @return $this
     * @deprecated
     */
    public function setParent(Location $location): Location
    {
        $this->set(self::PARENT, $location);
        $this->setParentId($location->getId());

        return $this;
    }

    /**
     * @param int $case
     * @param int $gender
     * @return string|null
     */
    public function getName(int $case = self::CASE_IM, int $gender = 0): ?string
    {
        $name = $this->get(self::NAME);

        if ($case != self::CASE_IM) {
            $nc   = new NCLNameCaseRu();
            $name = $nc->q($name, $case, $gender);
        }

        return $name;
    }
}