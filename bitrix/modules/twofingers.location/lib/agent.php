<?php

namespace TwoFingers\Location;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use TwoFingers\Location\Model\Iblock\Location;
use TwoFingers\Location\Service\SxGeo;

/**
 *
 */
class Agent
{
    /**
     * @param bool $onlyName
     * @return string
     */
    public static function updateGeoBase(bool $onlyName = false): string
    {
        if ($onlyName) {
            return __METHOD__ . '();';
        }

        try {
            if (Options::isSxGeoAgentUpdate()) {
                SxGeo::updateGeoBase();
            }
        } catch (\Exception $e) {
        }

        return __METHOD__ . '();';
    }

    /**
     * @return int
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
   /* protected static function getImportLocationsLimit(): int
    {
        return Options::isAgentsOnCron() ? 5000 : 100;
    }*/

    /**
     * @param int $offset
     * @param bool $onlyName
     * @return string
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ObjectPropertyException
     * @throws SystemException|LoaderException
     */
    /*public static function importLocations(int $offset = 0, bool $onlyName = false): string
    {
        if ($onlyName || Option::get('twofingers.location', 'import-process', 'N') == 'Y') {
            return __METHOD__ . '(' . $offset . ');';
        }

        if (Option::get('twofingers.location', 'import-complete', 'N') == 'Y') {
            return false;
        }

        Option::set('twofingers.location', 'import-process', 'Y');

        $limit  = self::getImportLocationsLimit();
        $status = Location::addLocations($offset, $limit);

        Option::set('twofingers.location', 'import-process', 'N');
        Option::set('twofingers.location', 'import-count', $offset + $limit);

        if (!$status) {
            Option::set('twofingers.location', 'import-complete', 'Y');
        }

        return $status ? __METHOD__ . '(' . ($offset + $limit) . ');' : false;
    }*/
}