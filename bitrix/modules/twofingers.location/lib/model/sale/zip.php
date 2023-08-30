<?php

namespace TwoFingers\Location\Model\Sale;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Sale\Location\ExternalTable;
use Twofingers\Location\Internal\LocationServiceInterface;
use TwoFingers\Location\Model\Location;

class Zip
{
    /**
     * @param int $locationId
     * @return string|null
     * @throws ArgumentNullException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getByLocationId(int $locationId): ?string
    {
        if (!$locationId) {
            throw new ArgumentNullException('locationId');
        }

        $result = ExternalTable::getList([
            'filter' => [
                '=SERVICE.CODE' => 'ZIP',
                '=LOCATION_ID'  => $locationId
            ],
            'select' => [
                //'ID',
                /*'ZIP' => */'XML_ID'
            ],
            'cache'  => ['ttl' => Location::CACHE_TTL],
            'limit'  => 1,
        ])->fetch();

        return $result['XML_ID'] ?: null;
    }
}