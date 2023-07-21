<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 05.02.2019
 * Time: 11:57
 *
 *
 */

namespace TwoFingers\Location\Helper;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SiteTable;
use Bitrix\Main\SystemException;
use TwoFingers\Location\Model\Iblock\Domain;

/**
 * Class Tools
 *
 * @package TwoFingers\Location\Helper
 *
 */
class Tools
{
    /**
     * @param              $name
     * @param mixed|string $langId
     * @return string
     *
     */
    public static function translit($name, $langId = LANGUAGE_ID): string
    {
        return \CUtil::translit($name, $langId, ['replace_space' => '-', 'replace_other' => '-']);
    }

    /**
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getSitesIds(): array
    {
        $query = [
            'select'    => ['LID'],
            'order'     => ['SORT' => 'ASC']
        ];

        $sites  = SiteTable::getList($query);
        $result = [];
        while ($site = $sites->fetch())
            $result[] = $site['LID'];

        return $result;
    }

    /**
     * @return array|false|mixed
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function getAvailableDomains()
    {
        $cache = Application::getInstance()->getManagedCache();
        $cacheId= crc32(__METHOD__);

        if ($cache->read(360000, $cacheId))
            return $cache->get($cacheId);

        // system domains
        $sites  = \CLang::GetList($by = 'SORT', $order = 'ASC', ['ACTIVE' => 'Y']);
        $result = [];
        while ($site = $sites->Fetch())
        {
            $site['SERVER_NAME'] = trim($site['SERVER_NAME']);
            if (strlen($site['SERVER_NAME']))
                $result[] = self::clearDomain($site['SERVER_NAME']);

            $domains = explode("\r\n", $site['DOMAINS']);
            foreach ($domains as $domain)
            {
                $domain = trim($domain);
                if (!strlen($domain))
                    continue;

                $result[] = self::clearDomain($domain);
            }
        }

        // domains iblock
        $domains = Domain::getByFilter(['!PROPERTY_' . Domain::PROPERTY_DOMAIN => false]);
        foreach ($domains as $domain)
        {
            $domainValue = trim($domain['PROPERTY_' . Domain::PROPERTY_DOMAIN . '_VALUE']);
            if (!strlen($domainValue))
                continue;

            $result[] = self::clearDomain($domainValue);
        }

        $result = array_unique($result);

        $cache->set($cacheId, $result);

        return $result;
    }

    /**
     * @param $domain
     * @return false|mixed|string
     */
    public static function clearDomain($domain)
    {
        return (strpos($domain, '://') !== false)
            ? substr($domain, strpos($domain, '://') + 3)
            : $domain;
    }
}