<?php

namespace TwoFingers\Location\Entity;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SystemException;
use \TwoFingers\Location\Model\Iblock\Content as ContentIblock;
use TwoFingers\Location\Model\Iblock\Domain;
use TwoFingers\Location\Options;
use TwoFingers\Location\Storage;

/**
 * Class Content
 *
 * @package TwoFingers\Location\Entity
 */
class Content
{
    /** @var array */
    protected $data;

    /** @var string */
    protected $domain = false;

    /**
     * Content constructor.
     *
     * @param array $data
     */
    protected function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param Location|null $location
     * @return Content|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     */
    public static function buildByLocation(Location $location = null): ?Content
    {
        $data = ContentIblock::getByLocationId($location->getPrimary(), $location->getSiteId());

        if (empty($data) && Options::isCapabilityMode())
            $data = ContentIblock::getByName($location->getName(), $location->getSiteId());;

        return $data ? new self($data) : null;
    }

    /**
     * @return Content|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws SystemException
     * @throws ArgumentException
     */
    public static function buildByStorage(): ?Content
    {
        $location = Storage::getLocation();
        if (empty($location))
            $location = Location::buildDefault();

        return $location ? $location->getContent() : null;
    }

    /**
     * @param $field
     * @return mixed|null
     */
    public function getFieldValue($field)
    {
        $field = trim($field);
        if (!strlen($field))
            return null;

        return isset($this->data[$field])
            ? $this->data[$field]
            : null;
    }

    /**
     * @param $propertyCode
     * @return mixed|null
     */
    public function getPropertyValue($propertyCode)
    {
        $propertyCode = trim($propertyCode);
        if (!strlen($propertyCode))
            return null;

        if (isset($this->data['PROPERTIES'][$propertyCode]['DISPLAY_VALUE']))
            return $this->data['PROPERTIES'][$propertyCode]['DISPLAY_VALUE'];

        return isset($this->data['PROPERTIES'][$propertyCode]['VALUE'])
            ? $this->data['PROPERTIES'][$propertyCode]['VALUE']
            : null;
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
        if (is_null($value))
            $value = $this->getPropertyValue($fieldName);

        return $value;
    }

    /**
     * @return mixed|string|null
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public function getDomain()
    {
        if ($this->domain === false)
        {
            $domainId = $this->getPropertyValue(ContentIblock::PROPERTY_DOMAIN);
            if (!$domainId)
            {
                $this->domain = null;;
            } else {
                $domain = Domain::getByFilter(['ID' => $domainId]);
                $this->domain = isset($domain[0]['PROPERTY_DOMAIN_VALUE'])
                    ? $domain[0]['PROPERTY_DOMAIN_VALUE']
                    : null;
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
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}