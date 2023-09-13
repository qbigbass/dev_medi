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
use TwoFingers\Location\Model\Iblock\Location;

if (class_exists('TwoFingers\Location\Model\Iblock\Location')) {
    $MESS['TFL_IBLOCK_LOCATION_NAME']        = 'Местоположения';
    $MESS['TFL_IBLOCK_LOCATION_DESCRIPTION'] = 'Содержит доступные местоположения';
    $MESS['TFL_IBLOCK_LOCATION_RUSSIA']      = 'Россия';

    $MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_FEATURED]           = 'Избранное';
    $MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_FEATURED . '_HINT'] =
        'Выводить в избранных местоположениях';
    $MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_FEATURED . '_YES']  = 'Да';
    $MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_DEFAULT]            = 'По-умолчанию';
    $MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_DEFAULT . '_HINT']  =
        'Выводить, если не удалось определить местоположение';
    $MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_DEFAULT . '_YES']   = 'Да';
    $MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_SITE_ID]            = 'Сайт';
    $MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_SITE_ID . '_HINT']  =
        'Если не указан, местоположение будет доступно на всех сайтах';
    $MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_ZIP]                = 'Индекс';
    $MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_ZIP . '_HINT']      = '';
    $MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_LONGITUDE]          = 'Долгота';
    $MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_LATITUDE]           = 'Широта';
    $MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_TYPE]               = 'Тип';
    $MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_TYPE . '_CITY']     = 'Город';
    $MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_TYPE . '_VILLAGE']  = 'Село';
}

