<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 13.03.2019
 * Time: 13:50
 *
 *
 */

use Bitrix\Main\Loader;
use TwoFingers\Location\Model\Iblock\Content;

if (class_exists('TwoFingers\Location\Model\Iblock\Content')) {
    $MESS['TFL_IBLOCK_CONTENT_NAME']                                                    = 'Контент';
    $MESS['TFL_IBLOCK_CONTENT_DEFAULT']                                                 = 'По-умолчанию';
    $MESS['TFL_IBLOCK_CONTENT_DESCRIPTION']                                             =
        'Содержит информацию, привязанную к конкретным местоположениям';
    $MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_LOCATION_ID]           = 'Местоположения';
    $MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_LOCATION_ID . '_HINT'] =
        'Текущий контент будет доступен для указанных местоположений';
    $MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_SITE_ID]               = 'Сайт';
    $MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_SITE_ID . '_HINT']     =
        'Если не указан, контент будет доступен на всех сайтах';
    $MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_DOMAIN]                = 'Домен';
    $MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_PHONE]                 = 'Телефон';
    $MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_ADDRESS]               = 'Адрес';
    $MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_PRICE_TYPES]           = 'Типы цен';
    $MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_STORES]                = 'Склады';
}