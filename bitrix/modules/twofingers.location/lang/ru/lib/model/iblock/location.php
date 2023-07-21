<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 13.03.2019
 * Time: 13:50
 *
 *
 */

use TwoFingers\Location\Model\Iblock\Location;

$MESS['TFL_IBLOCK_LOCATION_NAME']           = 'Местоположения';
$MESS['TFL_IBLOCK_LOCATION_DESCRIPTION']    = 'Содержит доступные местоположения';
$MESS['TFL_IBLOCK_LOCATION_RUSSIA']         = 'Россия';

$MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_FEATURED]             = 'Избранное';
$MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_FEATURED . '_HINT']   = 'Выводить в избранных местоположениях';
$MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_FEATURED . '_YES']    = 'Да';
$MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_DEFAULT]              = 'По-умолчанию';
$MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_DEFAULT . '_HINT']    = 'Выводить, если не удалось определить местоположение';
$MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_DEFAULT . '_YES']     = 'Да';
$MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_SITE_ID]              = 'Сайт';
$MESS['TFL_IBLOCK_PROP_' . Location::CODE . Location::PROPERTY_SITE_ID . '_HINT']    = 'Если не указан, местоположение будет доутупно на всех сайтах';