<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 13.03.2019
 * Time: 13:50
 *
 * @author Pavel Shulaev (https://rover-it.me)
 */

use TwoFingers\Location\Model\Iblock\Content;

$MESS['TFL_IBLOCK_CONTENT_NAME']                          = 'Контент';
$MESS['TFL_IBLOCK_CONTENT_DEFAULT']                       = 'По-умолчанию';
$MESS['TFL_IBLOCK_CONTENT_DESCRIPTION']                   = 'Содержит информацию, привязанную к конкретным местоположениям';
$MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_LOCATION_ID] = 'Местоположения';
$MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_LOCATION_ID . '_HINT']     = 'Текущий контент будет доступен для указанных местоположений';
$MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_SITE_ID]     = 'Сайт';
$MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_SITE_ID . '_HINT']     = 'Если не указан, контент будет доступен на всех сайтах';
$MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_H1]                 = 'Заголовок H1';
$MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_H1 . '_HINT']       = 'Если не заполнен, то переопределён не будет';
$MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_META_TITLE]            = 'Заголовок браузера';
$MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_META_TITLE . '_HINT']  = 'Если не заполнен, то переопределён не будет';
$MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_META_DESCRIPTION]      = 'Мета-описание';
$MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_META_DESCRIPTION . '_HINT']     = 'Если не заполнено, то переопределено не будет';
$MESS['TFL_IBLOCK_PROP_' . Content::CODE . Content::PROPERTY_DOMAIN]    = 'Домен';