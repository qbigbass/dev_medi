<?php

namespace TwoFingers\Location\Entity;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SystemException;
use TwoFingers\Location\Factory\ContentFactory;
use TwoFingers\Location\Factory\LocationFactory;
use Twofingers\Location\Internal\Collection;
use Twofingers\Location\Internal\HasCollectionTrait;
use TwoFingers\Location\Model\Iblock\Content as ContentIblock;
use TwoFingers\Location\Model\Iblock\Domain;
use TwoFingers\Location\Options;

/**
 * Class Content
 *
 * @package TwoFingers\Location\Entity
 */
class Content
{
    use HasCollectionTrait;

    /** @var string */
    protected $domain = false;


    /**
     * Content constructor.
     *
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed|null
     */
    public function __call($name, $arguments)
    {
        if (strpos($name, 'get') !== 0) {
            return null;
        }

        $fieldName = Options::fromCamelCase(substr($name, 3), '_', true);

        if ($this->collection->offsetExists($fieldName)) {
            return $this->getFieldValue($fieldName);
        }

        return $this->getPropertyValue($fieldName);
    }

    /**
     * @param $field
     * @return mixed|null
     */
    public function getFieldValue($field)
    {
        $field = trim($field);
        if (!strlen($field)) {
            return null;
        }

        return $this->collection->offsetGet($field);
    }

    /**
     * @param $propertyCode
     * @return mixed|null
     */
    public function getPropertyValue($propertyCode)
    {
        $propertyCode = trim($propertyCode);
        $data         = $this->getData();
        if (!strlen($propertyCode)) {
            return null;
        }

        if (isset($data['PROPERTIES'][$propertyCode]['DISPLAY_VALUE'])) {
            return $data['PROPERTIES'][$propertyCode]['DISPLAY_VALUE'];
        }

        return $data['PROPERTIES'][$propertyCode]['VALUE'] ?? null;
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        return $this->getFieldValue('ID');
    }

    /**
     * @return mixed|null
     */
    public function getName()
    {
        return $this->getFieldValue('NAME');
    }

    /**
     * @param $fieldName
     * @return mixed|null
     */
    public function getValue($fieldName)
    {
        $value = $this->getFieldValue($fieldName);
        if (is_null($value)) {
            $value = $this->getPropertyValue($fieldName);
        }

        return $value;
    }

    /**
     * @return mixed|string|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     */
    public function getDomain()
    {
        if ($this->domain === false) {
            $domainId = $this->getPropertyValue(ContentIblock::PROPERTY_DOMAIN);
            if (!$domainId) {
                $this->domain = null;
            } else {
                $domain       = Domain::getByFilter(['ID' => $domainId]);
                $this->domain = rtrim($domain[0]['PROPERTY_DOMAIN_VALUE'], '/\\') ?? null;
            }
        }

        return $this->domain;
    }

    /**
     * @return mixed|null
     */
    public function getSiteId()
    {
        return $this->getPropertyValue(ContentIblock::PROPERTY_SITE_ID);
    }

    /**
     * @return mixed|null
     */
    public function getPhone()
    {
        return $this->getPropertyValue(ContentIblock::PROPERTY_PHONE);
    }

    /**
     * @return mixed|null
     */
    public function getPriceTypes()
    {
        return $this->getPropertyValue(ContentIblock::PROPERTY_PRICE_TYPES);
    }

    /**
     * @return mixed|null
     */
    public function getStores()
    {
        return $this->getPropertyValue(ContentIblock::PROPERTY_STORES);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->collection->getCollection();
    }


    /**
     * @param Location|null $location
     * @return Content|null
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     * @deprecated
     */
    public static function buildByLocation(Location $location = null): ?Content
    {
        return $location ? ContentFactory::buildByLocation($location) : null;
    }

    /**
     * @return Content|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     * @throws ArgumentException
     * @deprecated
     */
    public static function buildByStorage(): ?Content
    {
        $location = LocationFactory::buildByStorage(SITE_ID, LANGUAGE_ID);
        if (empty($location)) {
            $location = LocationFactory::buildDefault(SITE_ID, LANGUAGE_ID);
        }

        return $location ? $location->getContent() : null;
    }
}