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

if (!Loader::includeModule('twofingers.location'))
    return;

$MESS['tfl__behavior-heading'] = 'Поведение';
$MESS['tf-location__default-locations-heading'] = 'Местоположения по-умолчанию';
$MESS['tf-location__locations-heading'] = 'Местоположения';
$MESS['TF_LOCATION_REDIRECTING_HEADING'] = 'Перенаправление';

$MESS['TF_LOCATION_DELIVERY']           = 'Устанавливать местоположение при оформлении заказа';
$MESS['TF_LOCATION_DELIVERY_ZIP']       = 'Автоматически менять индекс при изменении местоположения';
$MESS['TF_LOCATION_DELIVERY_ZIP_HELP']  = 'Некотрые службы доставки используют индекс в своих расчетах';
$MESS['TF_LOCATION_TEMPLATE'] = 'Подключить шаблон выбора местоположения';
$MESS['TF_LOCATION_TEMPLATE_HELP'] = 'Будет использован вместо стандартного выбора местоположений';
$MESS['tfl__' . Options::LIST_OPEN_IF_NO_LOCATION] = 'Если город не определен, то открывать автоматически';
$MESS['TF_LOCATION_SHOW_CONFIRM_POPUP'] = 'Автоматически открывать';
$MESS['TF_LOCATION_LOAD_LOCATIONS_all'] = 'Все местоположения и избранные';
$MESS['TF_LOCATION_LOAD_LOCATIONS_cities'] = 'Города и избранные местоположения';
$MESS['TF_LOCATION_LOAD_LOCATIONS_defaults'] = 'Избранные местоположения';
$MESS['TF_LOCATION_LOAD_LOCATIONS'] = 'При открытии попапа отображать';
$MESS['TF_LOCATION_LOAD_LOCATIONS_HELP'] = 'Если загрузка всех местоположений замедляет работу сайта, то выберите режим "' . $MESS['TF_LOCATION_LOAD_LOCATIONS_cities'] . '" или "' . $MESS['TF_LOCATION_LOAD_LOCATIONS_defaults'] . '". Подходящие местоположения будут загружены во время поиска.';

$MESS['TF_LOCATION_CONFIRM_POPUP_TEXT'] = 'Текст подтверждения';
$MESS['tf-location__confirm-popup-text-help'] = '— #location# будет заменено на название населенного пункта<br>— поддерживаются html-теги';
$MESS['TF_LOCATION_CONFIRM_POPUP_ERROR_TEXT'] = 'Текст, если местоположение определить не удалось';
$MESS['TF_LOCATION_CONFIRM_POPUP_ERROR_TEXT_HELP'] = 'Будет выведен, если определить местоположение не удалось';
$MESS['TF_LOCATION_COLOR']        = 'Цвет текста';
$MESS['TF_LOCATION_COLOR_HELP']   = '#rrggbb';
$MESS['TF_LOCATION_COLOR_HOVER']        = 'Цвет текста при наведении';

$MESS['TF_LOCATION_REDIRECT']        = 'Автоматическое перенаправление на домен/поддомен';
$MESS['TF_LOCATION_REDIRECT_HELP']   = 'Осуществляется только если для соотвествующего <a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=' . Iblock\Content::getId() .'&type=' . Iblock::TYPE .'&lang=' . LANGUAGE_ID .'&find_section_section=0">контента</a> задан <a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=' . Iblock\Domain::getId() .'&type=' . Iblock::TYPE .'&lang=' . LANGUAGE_ID .'&find_section_section=0">домен</a>';

$MESS['TF_LOCATION_BG']           = 'Цвет фона';
$MESS['TF_LOCATION_BG_HOVER']           = 'Цвет фона при наведении';
$MESS['tfl__' . Options::LIST_DESKTOP_WIDTH]                     = 'Ширина попапа на десктопе';
$MESS['tfl__' . Options::LIST_DESKTOP_WIDTH . '_post-input']     = 'px.';
$MESS['tfl__' . Options::LIST_MOBILE_BREAKPOINT]                 = 'Ширина экрана, с которой считается, что устройство мобильное';
$MESS['tfl__' . Options::LIST_MOBILE_BREAKPOINT . '_post-input'] = 'px.';
$MESS['tfl__' . Options::CALLBACK]                          = 'Запускать javascript-функцию после выбора местоположения';
$MESS['tfl__' . Options::CALLBACK . '_help']                = '<b style="color: red;">Функция обязательно должна быть определена!</b><br>В названии и аргументах функции можно использовать плейсхолдеры:<ul><li><b>#TF_LOCATION_CITY_ID#</b> - ID выбранного местоположения</li><li><b>#TF_LOCATION_CITY_NAME#</b> - Название выбранного местоположения</li></ul>Например, onSelectLocation(\'#TF_LOCATION_CITY_ID#\',\'#TF_LOCATION_CITY_NAME#\');';
$MESS['tfl__' . Options::LIST_LINK_CLASS]           = 'Дополнительный класс для ссылки';
$MESS['tfl__' . Options::LIST_LINK_CLASS . '_help'] = 'Кроме оформления заказа';
$MESS['tfl__' . Options::ORDER_LINK_CLASS]          = 'Дополнительный класс для ссылки';

$MESS['tf-location__favorite-locations-heading'] = 'Избранные местоположения';
$MESS['TF_LOCATION_CHOOSE_CITY'] = 'Избранные местоположения';
$MESS['TF_LOCATION_STRINGS_HEADING'] = 'Переопределение строковых констант';
$MESS['TF_LOCATION_VISUAL_HEADING'] = 'Внешний вид';
$MESS['TF_LOCATION_CONFIRM_BUTTON'] = 'Кнопка подтверждения';
$MESS['TF_LOCATION_CANCEL_BUTTON'] = 'Кнопка отмены/выбора';

$MESS['tfl__list-mobile'] = 'Мобильные устройства';
$MESS['tfl__list-desktop'] = 'Десктоп';

$MESS['tfl__list-padding']          = 'Отступы';
$MESS['tfl__list-list-padding']     = 'Отступ от края окна';
$MESS['tfl__list-font-size']        = 'Размер шрифта';
$MESS['tfl__list-title-font-size']  = 'Заголовок';
$MESS['tfl__list-input-font-size']  = 'Поле ввода';
$MESS['tfl__list-items-font-size']  = 'Местоположения';

$MESS['TF_LOCATION_ADD_CITY_HELP'] = 'После изменения необходимо <a href="/bitrix/admin/cache.php?lang=' . LANGUAGE_ID . '" target="_blank">сбросить кеш</a>';
$MESS['TF_LOCATION_DEFAULT_CITIES_S2'] = '<a target="_blank" href="/bitrix/admin/sale_location_default_list.php?lang=' . LANGUAGE_ID . '">изменить список избранных местоположений</a>';
$MESS['TF_LOCATION_DEFAULT_CITIES_INTERNAL'] = '<a target="_blank" href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=' . Iblock\Location::getId() . '&type=tf_location&lang=' . LANGUAGE_ID . '&find_section_section=0">изменить список</a>';
$MESS['tf-location__default-city-all-sites']    = 'Для всех сайтов';
$MESS['tf-location__default-city-help']             = 'Местоположение по-умолчанию будет выведено, если не удастся определить текущее местоположение.<br>Местоположение по-умолчанию для всех сайтов будет выведено, если не удастся определить местоположение по-умолчанию для текущего.';
$MESS['tf-location__default-city-internal-change']  = '<a target="_blank" href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=' . Iblock\Location::getId() . '&type=tf_location&lang=' . LANGUAGE_ID . '&find_section_section=0">изменить</a>';
$MESS['tf-location__default-city-none']             = '[не выбрано]';


$MESS['tfl__' . Options::LIST_PRE_LINK_TEXT]          = 'Текст перед ссылкой';
$MESS['TF_LOCATION_LOCATION_POPUP_HEADER']  = 'Заголовок';
$MESS['TF_LOCATION_LOCATION_POPUP_PLACEHOLDER'] = 'Плейсхолдер в строке поиска';
$MESS['TF_LOCATION_LOCATION_POPUP_NO_FOUND']= 'Надпись, если не найдено ни одного нас. пункта';
$MESS['tfl__' . Options::LIST_DESKTOP_PADDING . '_post-input']  = 'px.';
$MESS['tfl__' . Options::LIST_MOBILE_PADDING . '_post-input']   = 'px.';
$MESS['tfl__' . Options::LIST_DESKTOP_RADIUS]                   = 'Радиус скругления углов';
$MESS['tfl__' . Options::LIST_DESKTOP_RADIUS . '_help']         = 'Для десктоп-версии';
$MESS['tfl__' . Options::LIST_DESKTOP_RADIUS . '_post-input']   = 'px.';
$MESS['tfl__' . Options::LIST_DESKTOP_TITLE_FONT_SIZE . '_post-input']  = 'px.';
$MESS['tfl__' . Options::LIST_DESKTOP_INPUT_FONT_SIZE . '_post-input']  = 'px.';
$MESS['tfl__' . Options::LIST_DESKTOP_ITEMS_FONT_SIZE . '_post-input']  = 'px.';
$MESS['tfl__' . Options::LIST_MOBILE_TITLE_FONT_SIZE . '_post-input']   = 'px.';
$MESS['tfl__' . Options::LIST_MOBILE_INPUT_FONT_SIZE . '_post-input']   = 'px.';
$MESS['tfl__' . Options::LIST_MOBILE_ITEMS_FONT_SIZE . '_post-input']   = 'px.';
$MESS['TF_LOCATION_JQUERY_INCLUDE']         = 'Подключать JQuery';
$MESS['TF_LOCATION_JQUERY_INCLUDE_HELP']    = 'Нажмите эту галочку, если в на Вашем сайте не используется JavaScript-библиотека JQuery';
$MESS['tfl__' . Options::COOKIE_LIFETIME]                   = 'Хранить местоположение';
$MESS['tfl__' . Options::COOKIE_LIFETIME . '_post-input']   = 'дн.';
$MESS['tfl__' . Options::COOKIE_LIFETIME . '_help']         = 'Если указать ноль, то после закрытия браузера местоположение будет сброшено';
$MESS['TF_LOCATION_RELOAD']                 = 'Перезагружать страницу после выбора местоположения';
$MESS['TF_LOCATION_RELOAD_HELP']            = 'Используйте, если после изменения местоположения необходимо обновить какие-либо данные на странице.<br>Если для местоположения указано перенаправление, то произойдёт оно, а не перезагрузка страницы.';
$MESS['TF_LOCATION_SHOW_VILLAGES']          = 'Добавлять деревни в список местоположений';
$MESS['TF_LOCATION_SHOW_VILLAGES_HELP']     = 'Может вызвать замедление загрузки списка при большом количестве деревень';
//$MESS['tfl__' . Options::OPTION__SITE_LOCATIONS_ONLY] = 'Ограничить список <a href="/bitrix/admin/sale_location_zone_list.php?lang=' . LANGUAGE_ID . '" target="_blank">местоположениями для текущего сайта</a>';
//$MESS['tfl__' . Options::OPTION__SITE_LOCATIONS_ONLY . '_help'] = 'Если для сайта не задано ни одного местоположения, будет выведен общий список.';
$MESS['TF_LOCATION_FILTER_BY_SITE_LOCATIONS']           = 'Ограничить автоматически определяемые местоположения теми, которые загружены на сайт';
$MESS['TF_LOCATION_FILTER_BY_SITE_LOCATIONS_HELP']      = 'Определяемые местоположения будут ограничены теми, которые заданы для текущего сайта, либо всеми загруженными местоположениями ядра, если ни одного местоположения для сайта не задано.<br>Если совпадение обнаружить не удалось, то будет выведено по-умолчанию (если есть), либо "не определено".<br><br>';
$MESS['tfl__' . Options::LIST_FAVORITES_POSITION]            = 'Отображать избранные местоположения';
$MESS['tfl__' . Options::CAPABILITY_MODE]               = 'Режим совместимости со старыми версиями';
$MESS['tfl__' . Options::REPLACE_PLACEHOLDERS]          = 'Заменять плейсхолдеры';
$MESS['tfl__' . Options::REPLACE_PLACEHOLDERS . '_help'] = '<ul>
<li><strong>#location_name#, #city_name#</strong> — название текущего местоположения;</li>
<li><strong>#region_name#</strong> — название текущего региона, области, края, республики и т.п.;</li>
<li><strong>#country_name#</strong> — название текущей страны;</li>
<li><strong>#content_%код%#</strong> — значение поля или свойства с кодом %код% из привязанного элемента <a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=' . Iblock\Content::getId() .'&type=' . Iblock::TYPE .'&lang=' . LANGUAGE_ID .'&find_section_section=0">контента</a>. Например, <strong>#content_preview_text#</strong> будет заменено на анонс, а <strong>#content_phone#</strong> — на значение свойства PHONE.</li>
</ul>Замена производится как в мета-тегах, так и в любом другом месте страницы.';

$MESS['tfl__' . Options::SX_GEO_MEMORY]           = 'Загружать базу местоположений в оперативную память';
$MESS['tfl__' . Options::SX_GEO_MEMORY . '_help'] = 'Ускоряет определение местоположения, но требует больше ресурсов.<br>Если перестанет хватать памяти, попробуйте отключить эту опцию.';
$MESS['tfl__' . Options::LOCATIONS_LIMIT]           = 'Максимальное кол-во местоположений в списке';
$MESS['tfl__' . Options::LOCATIONS_LIMIT . '_help'] = 'Большое количество может вызвать замедление загрузки списка. <br>После изменения необходимо <a href="/bitrix/admin/cache.php?lang=' . LANGUAGE_ID . '" target="_blank">сбросить кеш</a>';
$MESS['tfl__' . Options::SEARCH_LIMIT]           = 'Максимальное кол-во результатов поиска';
$MESS['tfl__' . Options::SEARCH_LIMIT . '_help'] = 'Большое количество может вызвать замедление скорости поиска. <br>После изменения необходимо <a href="/bitrix/admin/cache.php?lang=' . LANGUAGE_ID . '" target="_blank">сбросить кеш</a>';
$MESS['tfl__update-sx']                 = 'Обновить базу местоположений';
$MESS['tfl__update-sx-submit']          = 'Обновить';
$MESS['tfl__update-sx-last']            = 'Последнее обновление #date#';
$MESS['tfl__update-sx-no-curl']         = 'На хостинге не найдена библиотека curl, обновление невозможно';

$MESS['tf-location__empty-list'] = '(не выбраны)';