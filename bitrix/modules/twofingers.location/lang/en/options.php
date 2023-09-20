<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 09.03.2019
 * Time: 14:50
 *
 *
 */

use Bitrix\Main\Loader;
use \TwoFingers\Location\Model\Iblock;
use TwoFingers\Location\Options;

if (!Loader::includeModule('twofingers.location')) {
    return;
}

$MESS['tfl__behavior-heading']                  = 'Поведение';
$MESS['tf-location__default-locations-heading'] = 'Местоположения по-умолчанию';
$MESS['tf-location__locations-heading']         = 'Местоположения';

$MESS['tfl__' . Options::LIST_OPEN_IF_NO_LOCATION] = 'Если город не определен, то открывать автоматически';
$MESS['tfl__' . Options::CONFIRM_OPEN]             = 'Автоматически открывать';


$MESS['tfl__cm-domains']         = 'Домены';
$MESS['tfl__cm-domains-title']   = 'Домены';
$MESS['tfl__cm-content']         = 'Контент';
$MESS['tfl__cm-content-title']   = 'Контент';
$MESS['tfl__cm-locations']       = 'Местоположения';
$MESS['tfl__cm-locations-title'] = 'Местоположения';

$MESS['TF_LOCATION_CONFIRM_POPUP_TEXT']            = 'Текст подтверждения';
$MESS['tf-location__confirm-popup-text-help']      =
    '— #location# будет заменено на название населенного пункта<br>— поддерживаются html-теги';
$MESS['TF_LOCATION_CONFIRM_POPUP_ERROR_TEXT']      = 'Текст, если местоположение определить не удалось';
$MESS['TF_LOCATION_CONFIRM_POPUP_ERROR_TEXT_HELP'] = 'Будет выведен, если определить местоположение не удалось';
$MESS['TF_LOCATION_COLOR']                         = 'Цвет текста';
$MESS['TF_LOCATION_COLOR_HELP']                    = '#rrggbb';
$MESS['TF_LOCATION_COLOR_HOVER']                   = 'Цвет текста при наведении';

$MESS['TF_LOCATION_BG']                                          = 'Цвет фона';
$MESS['TF_LOCATION_BG_HOVER']                                    = 'Цвет фона при наведении';
$MESS['tfl__' . Options::LIST_MOBILE_BREAKPOINT]                 =
    'Ширина экрана, с которой считается, что устройство мобильное';
$MESS['tfl__' . Options::LIST_MOBILE_BREAKPOINT . '_post-input'] = 'px.';


$MESS['tf-location__favorite-locations-heading'] = 'Избранные местоположения';
$MESS['TF_LOCATION_CHOOSE_CITY']                 = 'Избранные местоположения';
$MESS['TF_LOCATION_STRINGS_HEADING']             = 'Переопределение строковых констант';
$MESS['TF_LOCATION_VISUAL_HEADING']              = 'Внешний вид';
$MESS['TF_LOCATION_CONFIRM_BUTTON']              = 'Кнопка подтверждения';
$MESS['TF_LOCATION_CANCEL_BUTTON']               = 'Кнопка отмены/выбора';

$MESS['tfl__list-mobile']  = 'Мобильные устройства';
$MESS['tfl__list-desktop'] = 'Десктоп';

$MESS['tfl__list-padding']                 = 'Отступы';
$MESS['tfl__list-list-padding-top-bottom'] = 'От края сверху и снизу';
$MESS['tfl__list-list-padding-left-right'] = 'От края слева и справа';
$MESS['tfl__list-font-size']               = 'Размер шрифта';
$MESS['tfl__list-title-font-size']         = 'Заголовок';
$MESS['tfl__list-input-font-size']         = 'Поле ввода';
$MESS['tfl__list-items-font-size']         = 'Местоположения';

$MESS['tfl__list-other']              = 'Прочие';
$MESS['tfl__list-border-radius']      = 'Радиус скругления углов';
$MESS['tfl__list-border-radius_help'] = 'Только для десктоп-версии';

$MESS['TF_LOCATION_ADD_CITY_HELP']                 =
    'После изменения необходимо <a href="/bitrix/admin/cache.php?lang=' . LANGUAGE_ID . '" target="_blank">сбросить кеш</a>';
$MESS['TF_LOCATION_DEFAULT_CITIES_S2']             =
    '<a target="_blank" href="/bitrix/admin/sale_location_default_list.php?lang=' . LANGUAGE_ID . '">изменить список избранных местоположений</a>';
$MESS['TF_LOCATION_DEFAULT_CITIES_INTERNAL']       =
    '<a target="_blank" href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=' . Iblock\Location::getId() . '&type=tf_location&lang=' . LANGUAGE_ID . '&find_section_section=0">изменить список</a>';
$MESS['tf-location__default-city-all-sites']       = 'Для всех сайтов';
$MESS['tf-location__default-city-help']            =
    'Местоположение по-умолчанию будет выведено, если не удастся определить текущее.<br>Местоположение по-умолчанию для всех сайтов будет выведено, если не удастся определить местоположение по-умолчанию для текущего.';
$MESS['tf-location__default-city-internal-change'] =
    '<a target="_blank" href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=' . Iblock\Location::getId() . '&type=tf_location&lang=' . LANGUAGE_ID . '&find_section_section=0">изменить</a>';
$MESS['tf-location__default-city-none']            = '[не выбрано]';


$MESS['tfl__' . Options::LIST_PRE_LINK_TEXT]                           = 'Текст перед ссылкой';
$MESS['TF_LOCATION_LOCATION_POPUP_HEADER']                             = 'Заголовок';
$MESS['TF_LOCATION_LOCATION_POPUP_PLACEHOLDER']                        = 'Плейсхолдер в строке поиска';
$MESS['TF_LOCATION_LOCATION_POPUP_NO_FOUND']                           =
    'Надпись, если не найдено ни одного нас. пункта';
$MESS['tfl__' . Options::LIST_DESKTOP_PADDING_LEFT . '_post-input']    = 'px.';
$MESS['tfl__' . Options::LIST_DESKTOP_PADDING_RIGHT . '_post-input']   = 'px.';
$MESS['tfl__' . Options::LIST_DESKTOP_PADDING_TOP . '_post-input']     = 'px.';
$MESS['tfl__' . Options::LIST_DESKTOP_PADDING_BOTTOM . '_post-input']  = 'px.';
$MESS['tfl__' . Options::LIST_MOBILE_PADDING_LEFT . '_post-input']     = 'px.';
$MESS['tfl__' . Options::LIST_MOBILE_PADDING_RIGHT . '_post-input']    = 'px.';
$MESS['tfl__' . Options::LIST_MOBILE_PADDING_TOP . '_post-input']      = 'px.';
$MESS['tfl__' . Options::LIST_MOBILE_PADDING_BOTTOM . '_post-input']   = 'px.';
$MESS['tfl__' . Options::LIST_DESKTOP_RADIUS . '_post-input']          = 'px.';
$MESS['tfl__' . Options::LIST_DESKTOP_TITLE_FONT_SIZE . '_post-input'] = 'px.';
$MESS['tfl__' . Options::LIST_DESKTOP_INPUT_FONT_SIZE . '_post-input'] = 'px.';
$MESS['tfl__' . Options::LIST_DESKTOP_ITEMS_FONT_SIZE . '_post-input'] = 'px.';
$MESS['tfl__' . Options::LIST_MOBILE_TITLE_FONT_SIZE . '_post-input']  = 'px.';
$MESS['tfl__' . Options::LIST_MOBILE_INPUT_FONT_SIZE . '_post-input']  = 'px.';
$MESS['tfl__' . Options::LIST_MOBILE_ITEMS_FONT_SIZE . '_post-input']  = 'px.';

//$MESS['tfl__' . Options::OPTION__SITE_LOCATIONS_ONLY] = 'Ограничить список <a href="/bitrix/admin/sale_location_zone_list.php?lang=' . LANGUAGE_ID . '" target="_blank">местоположениями для текущего сайта</a>';
//$MESS['tfl__' . Options::OPTION__SITE_LOCATIONS_ONLY . '_help'] = 'Если для сайта не задано ни одного местоположения, будет выведен общий список.';


$MESS['tf-location__empty-list'] = '(не выбраны)';