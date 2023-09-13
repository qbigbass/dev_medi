<?php

use Bitrix\Main\Loader;
use TwoFingers\Location\Helper\Tabs;
use TwoFingers\Location\Options;
use TwoFingers\Location\Model\Iblock;

if (Loader::includeModule('twofingers.location')) {
    $MESS['tfl__tab-' . Tabs::LOCATIONS]            = "Местоположения";
    $MESS['tfl__tab-' . Tabs::LOCATIONS . '_TITLE'] = 'Настройки местоположений';

    $MESS['tfl__tab-popup-list']       = 'Окно выбора города';
    $MESS['tfl__tab-popup-list_DESCR'] = 'Настройки окна выбора местоположения';

    $MESS['tfl__tab-popup-confirm']       = 'Окно подтверждения';
    $MESS['tfl__tab-popup-confirm_DESCR'] = 'Настройки окна подтверждения местоположения';

    $MESS['tfl__tab-sale']       = 'Оформление заказа';
    $MESS['tfl__tab-sale_DESCR'] = 'Настройки оформления заказа';

    $MESS['tfl__tab-' . Tabs::SETTINGS]            = 'Дополнительно';
    $MESS['tfl__tab-' . Tabs::SETTINGS . '_DESCR'] = 'Дополнительные настройки модуля';

    // LOCATIONS
    $MESS['tfl__' . Options::REDIRECT_EVENT]                                           =
        '<span id="redirect_event"></span>&nbsp;Автоматическое перенаправление на домен/поддомен, если местоположение:';
    $MESS['tfl__' . Options::REDIRECT_EVENT . '_hint']                                 =
        'Для местоположения должен быть создан <a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=' . Iblock\Content::getId() . '&type=' . Iblock::TYPE . '&lang=' . LANGUAGE_ID . '&find_section_section=0">контент</a> с привязанным <a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=' . Iblock\Domain::getId() . '&type=' . Iblock::TYPE . '&lang=' . LANGUAGE_ID . '&find_section_section=0">доменом</a>';
    $MESS['tfl__' . Options::REDIRECT_EVENT . '_' . Options::REDIRECT_EVENT_SELECTED]  = 'выбрано в списке';
    $MESS['tfl__' . Options::REDIRECT_EVENT . '_' . Options::REDIRECT_EVENT_DETECTED]  = 'определено автоматически';
    $MESS['tfl__' . Options::REDIRECT_EVENT . '_' . Options::REDIRECT_EVENT_CONFIRMED] = 'подтверждено пользователем';

    $MESS['tfl__' . Options::NO_DOMAIN_ACTION]                                                =
        'Если местоположение без своего домена/поддомена';
    $MESS['tfl__' . Options::NO_DOMAIN_ACTION . '_' . Options::NO_DOMAIN_ACTION_NONE]         =
        'Оставить текущий домен';
    $MESS['tfl__' . Options::NO_DOMAIN_ACTION . '_' . Options::NO_DOMAIN_ACTION_CURRENT_SITE] =
        'Перенаправить на основной домен текущего сайта';
    $MESS['tfl__' . Options::NO_DOMAIN_ACTION . '_' . Options::NO_DOMAIN_ACTION_DEFAULT_SITE] =
        'Перенаправить на основной домен сайта по-умолчанию';
    //$MESS['tfl__' . Options::NO_DOMAIN_ACTION . '_' . Options::NO_DOMAIN_ACTION_SITE_DEFAULT_LOCATION_DOMAIN]       = 'Перенаправить на домен местоположения по-умолчанию для текущего сайта';
    //$MESS['tfl__' . Options::NO_DOMAIN_ACTION . '_' . Options::NO_DOMAIN_ACTION_ALL_SITES_DEFAULT_LOCATION_DOMAIN]  = 'Перенаправить на домен местоположения по-умолчанию для всех сайтов';

    // LIST
    $MESS['tfl__' . Options::LIST_RELOAD_PAGE]             =
        '<span id="list_reload_page"></span>&nbsp;Перезагружать страницу после выбора местоположения';
    $MESS['tfl__' . Options::LIST_RELOAD_PAGE . '_hint']   =
        'Используйте, если после изменения местоположения необходимо обновить какие-либо данные на странице.';
    $MESS['tfl__' . Options::LIST_SHOW_VILLAGES]           =
        '<span id="list_show_villages"></span>&nbsp;Добавлять деревни в список';
    $MESS['tfl__' . Options::LIST_SHOW_VILLAGES . '_hint'] =
        'Может вызвать замедление загрузки списка при большом количестве деревень.';

    $MESS['tfl__' . Options::LIST_LOCATIONS_LOAD]               =
        '<span id="list_locations_load"></span>&nbsp;При открытии попапа отображать';
    $MESS['tfl__' . Options::LIST_LOCATIONS_LOAD . '_all']      = 'Все местоположения и избранные';
    $MESS['tfl__' . Options::LIST_LOCATIONS_LOAD . '_cities']   = 'Города и избранные местоположения';
    $MESS['tfl__' . Options::LIST_LOCATIONS_LOAD . '_defaults'] = 'Избранные местоположения';
    $MESS['tfl__' . Options::LIST_LOCATIONS_LOAD . '_hint']     =
        'Если загрузка всех местоположений замедляет работу сайта, то выберите режим <i>"' . $MESS['tfl__' . Options::LIST_LOCATIONS_LOAD . '_cities'] . '"</i> или <i>"' . $MESS['tfl__' . Options::LIST_LOCATIONS_LOAD . '_defaults'] . '"</i>. Подходящие местоположения будут загружены во время поиска.';

    $MESS['tfl__' . Options::LIST_FAVORITES_POSITION]                      = 'Отображать избранные местоположения';
    $MESS['tfl__' . Options::LIST_FAVORITES_POSITION . '-above-search']    = 'Над строкой поиска';
    $MESS['tfl__' . Options::LIST_FAVORITES_POSITION . '-under-search']    = 'Под строкой поиска';
    $MESS['tfl__' . Options::LIST_FAVORITES_POSITION . '-left-locations']  = 'Слева от списка местоположений';
    $MESS['tfl__' . Options::LIST_FAVORITES_POSITION . '-right-locations'] = 'Справа от списка местоположений';

    $MESS['tfl__' . Options::LIST_LINK_CLASS]           = 'Дополнительный класс для ссылки';
    $MESS['tfl__' . Options::LIST_LINK_CLASS . '_help'] = 'Кроме оформления заказа';

    $MESS['tfl__' . Options::LIST_DESKTOP_WIDTH]  = 'Ширина попапа на десктопе';
    $MESS['tfl__' . Options::LIST_DESKTOP_HEIGHT] = 'Высота попапа на десктопе';

    $MESS['tfl__' . Options::LIST_TITLE_FONT_FAMILY]           =
        '<span id="list_title_font_family"></span>&nbsp;Шрифт заголовка';
    $MESS['tfl__' . Options::LIST_TITLE_FONT_FAMILY . '_hint'] =
        'Если выбранный шрифт отсутствует на сайте, нажмите галочку <i>"Загружать шрифты из Google"</i> на вкладке <i>"Дополнительно"</i>';
    $MESS['tfl__' . Options::LIST_ITEMS_FONT_FAMILY]           =
        '<span id="list_items_font_family"></span>&nbsp;Шрифт списка';
    $MESS['tfl__' . Options::LIST_ITEMS_FONT_FAMILY . '_hint'] =
        'Если выбранный шрифт отсутствует на сайте, нажмите галочку <i>"Загружать шрифты из Google"</i> на вкладке <i>"Дополнительно"</i>';
    $MESS['tfl__font-default']                                 = 'Основной для текущего сайта';
    $MESS['tfl__font-open-sans']                               = 'Open Sans';
    $MESS['tfl__font-roboto']                                  = 'Roboto';

    $MESS['tfl__confirm-width'] = 'Ширина';

    // confirm
    //$MESS['tfl__' . Options::CONFIRM_OPEN]                                           = '<span id="redirect_event"></span>&nbsp;Автоматическое перенаправление на домен/поддомен, если местоположение:';
    //$MESS['tfl__' . Options::CONFIRM_OPEN . '_hint']                                 = 'Для местоположения должен быть создан <a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=' . Iblock\Content::getId() . '&type=' . Iblock::TYPE . '&lang=' . LANGUAGE_ID . '&find_section_section=0">контент</a> с привязанным <a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=' . Iblock\Domain::getId() . '&type=' . Iblock::TYPE . '&lang=' . LANGUAGE_ID . '&find_section_section=0">доменом</a>';
    $MESS['tfl__' . Options::CONFIRM_OPEN . '_' . Options::CONFIRM_OPEN_NOT_DETECTED] =
        'если не удалось определить местоположение';
    $MESS['tfl__' . Options::CONFIRM_OPEN . '_' . Options::CONFIRM_OPEN_DETECTED]     =
        'если удалось определить местоположение';
    $MESS['tfl__' . Options::CONFIRM_OPEN . '_' . Options::CONFIRM_OPEN_ALWAYS]       = 'всегда';

    $MESS['tfl__' . Options::CONFIRM_OPEN . '-N'] = 'Никогда';
    $MESS['tfl__' . Options::CONFIRM_OPEN . '-Y'] = 'Если не удалось определить местоположение';
    $MESS['tfl__' . Options::CONFIRM_OPEN . '-A'] = 'Пока не будет закрыто пользователем';
    $MESS['tfl__' . Options::CONFIRM_OPEN . '-U'] =
        'Если не удалось определить местоположение или пока не будет закрыто пользователем';

    $MESS['tfl__' . Options::CONFIRM_TEXT_FONT_FAMILY]           = '<span id="confirm_font_family"></span>&nbsp;Шрифт';
    $MESS['tfl__' . Options::CONFIRM_TEXT_FONT_FAMILY . '_hint'] =
        'Если выбранный шрифт отсутствует на сайте, нажмите галочку <i>"Загружать шрифты из Google"</i> на вкладке <i>"Дополнительно"</i>';

    // sale
    $MESS['tfl__' . Options::ORDER_SET_TEMPLATE]           =
        '<span id="order_set_template"></span>&nbsp;Подключить шаблон выбора местоположения';
    $MESS['tfl__' . Options::ORDER_SET_TEMPLATE . '_hint'] =
        'Будет использован вместо стандартного выбора местоположений';
    $MESS['tfl__' . Options::ORDER_LINK_CLASS]             = 'Дополнительный класс для ссылки';
    $MESS['tfl__' . Options::ORDER_SET_LOCATION]           = 'Устанавливать местоположение при оформлении заказа';
    $MESS['tfl__' . Options::ORDER_SET_ZIP]                =
        '<span id="order_set_zip"></span>&nbsp;Автоматически менять индекс при изменении местоположения';
    $MESS['tfl__' . Options::ORDER_SET_ZIP . '_hint']      =
        'Некоторые службы доставки используют индекс в своих расчетах';

    // settings
    $MESS['tfl__' . Options::INCLUDE_JQUERY]             = '<span id="include_jquery"></span>&nbsp;Подключать JQuery';
    $MESS['tfl__' . Options::INCLUDE_JQUERY . '_no']     = 'Не подключать';
    $MESS['tfl__' . Options::INCLUDE_JQUERY . '_hint']   =
        'Может помочь, если на сайте не используется JavaScript-библиотека jQuery, или не открывается список местоположений. Версия должна присутствовать в ядре сайта.';
    $MESS['tfl__' . Options::USE_GOOGLE_FONTS]           =
        '<span id="use_google_fonts"></span>&nbsp;Загружать шрифты из Google';
    $MESS['tfl__' . Options::USE_GOOGLE_FONTS . '_hint'] = 'Выбранные шрифты будут загружены из Google';
    $MESS['tfl__' . Options::CALLBACK]                   =
        '<span id="callback"></span>&nbsp;Запускать javascript-функцию после выбора местоположения';
    $MESS['tfl__' . Options::CALLBACK . '_hint']         =
        'В названии и аргументах функции можно использовать плейсхолдеры:<ul><li><b>#TF_LOCATION_CITY_ID#</b> - ID выбранного местоположения</li><li><b>#TF_LOCATION_CITY_NAME#</b> - Название выбранного местоположения</li></ul>Например, onSelectLocation(\'#TF_LOCATION_CITY_ID#\', \'#TF_LOCATION_CITY_NAME#\');';
    $MESS['tfl__' . Options::CALLBACK . '_help']         =
        '<span style="color: red;">Функция обязательно должна быть определена!</span>';

    $MESS['tfl__' . Options::REPLACE_PLACEHOLDERS]           =
        '<span id="replace_placeholders"></span>&nbsp;Заменять плейсхолдеры';
    $MESS['tfl__' . Options::REPLACE_PLACEHOLDERS . '_hint'] =
        'Замена производится как в мета-тегах, так и в любом другом месте страницы.';
    $MESS['tfl__' . Options::REPLACE_PLACEHOLDERS . '_help'] = '<ul>
<li><strong>#location_name#, #city_name#</strong> — название текущего местоположения;</li>
<li><strong>#region_name#</strong> — название текущего региона, области, края, республики и т.п.;</li>
<li><strong>#country_name#</strong> — название текущей страны;</li>
<li><strong>#content_%код%#</strong> — значение поля или свойства с кодом %код% из привязанного элемента <a href="/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=' . Iblock\Content::getId() . '&type=' . Iblock::TYPE . '&lang=' . LANGUAGE_ID . '&find_section_section=0">контента</a>. Например, <strong>#content_preview_text#</strong> будет заменено на анонс, а <strong>#content_phone#</strong> — на значение свойства PHONE.</li>
</ul>';
    $MESS['tfl__' . Options::COOKIE_LIFETIME]                =
        '<span id="cookie_lifetime"></span>&nbsp;Сколько дней хранить выбранное местоположение?';
    $MESS['tfl__' . Options::COOKIE_LIFETIME . '_hint']      =
        'Если указать ноль, то после закрытия браузера местоположение будет сброшено';
    $MESS['tfl__' . Options::LOCATIONS_LIMIT]                =
        '<span id="locations_limit"></span>&nbsp;Максимальное кол-во местоположений в списке';
    $MESS['tfl__' . Options::LOCATIONS_LIMIT . '_hint']      =
        'Большое количество может вызвать замедление загрузки списка.';
    $MESS['tfl__' . Options::LOCATIONS_LIMIT . '_help']      =
        'После изменения необходимо <a href="/bitrix/admin/cache.php?lang=' . LANGUAGE_ID . '" target="_blank">сбросить кеш</a>';
    $MESS['tfl__' . Options::SEARCH_LIMIT]                   =
        '<span id="search_limit"></span>&nbsp;Максимальное кол-во результатов поиска';
    $MESS['tfl__' . Options::SEARCH_LIMIT . '_hint']         =
        'Большое количество может вызвать замедление скорости поиска.';
    $MESS['tfl__' . Options::SEARCH_LIMIT . '_help']         =
        'После изменения необходимо <a href="/bitrix/admin/cache.php?lang=' . LANGUAGE_ID . '" target="_blank">сбросить кеш</a>';
    $MESS['tfl__' . Options::CAPABILITY_MODE]                =
        '<span id="capability_mode"></span>&nbsp;Режим совместимости со старыми версиями';
    $MESS['tfl__' . Options::CAPABILITY_MODE . '_hint']      =
        'Рекомендуется включить, если используете кастомный шаблон';

    $MESS['tfl__' . Options::SX_GEO_AGENT_UPDATE]           =
        '<span id="sx_geo_agent_update"></span>&nbsp;Обновлять базу автоматически';
    $MESS['tfl__' . Options::SX_GEO_AGENT_UPDATE . '_hint'] = 'Обновление происходит раз в сутки в 1:00';
    $MESS['tfl__' . Options::SX_GEO_AGENT_UPDATE . '_help'] =
        'Необходимо, чтобы агенты были <a href="https://dev.1c-bitrix.ru/community/webdev/user/8078/blog/implementation-of-all-agents-in-cron/">переведены на cron</a>';
    $MESS['tfl__' . Options::SX_GEO_MEMORY]                 =
        '<span id="sx_geo_memory"></span>&nbsp;Загружать базу в оперативную память';
    $MESS['tfl__' . Options::SX_GEO_MEMORY . '_hint']       =
        'Ускоряет определение местоположения, но требует больше ресурсов.<br>Если столкнетесь с нехваткой памяти, попробуйте отключить эту опцию.';
    $MESS['tfl__' . Options::SX_GEO_PROXY_ENABLED]          = 'Использовать прокси для обновления местоположений';
    $MESS['tfl__' . Options::SX_GEO_PROXY_NAME]             = 'Хост';
    $MESS['tfl__' . Options::SX_GEO_PROXY_PORT]             = '<span id="sx_geo_proxy_port"></span>&nbsp;Порт';
    $MESS['tfl__' . Options::SX_GEO_PROXY_PORT . '_hint']   = 'Если не задан, то будет выставлен по-умолчанию: 80.';
    $MESS['tfl__' . Options::SX_GEO_PROXY_PASS]             = '<span id="sx_geo_proxy_pass"></span>&nbsp;Пароль';
    $MESS['tfl__' . Options::SX_GEO_PROXY_PASS . '_hint']   = 'Если не задан, то указывать не нужно.';
    $MESS['tfl__' . Options::SX_GEO_PROXY_TYPE]             = 'Тип';
    $MESS['tfl__' . CURLPROXY_HTTP]                         = 'http';
    $MESS['tfl__' . CURLPROXY_HTTPS]                        = 'https';
    $MESS['tfl__' . CURLPROXY_SOCKS4]                       = 'socks4';
    $MESS['tfl__' . CURLPROXY_SOCKS5]                       = 'socks5';
    $MESS['tfl__' . CURLPROXY_SOCKS4A]                      = 'socks4a';
    $MESS['tfl__' . CURLPROXY_SOCKS5_HOSTNAME]              = 'cocks5_hostname';

    $MESS['tfl__header-geo-base']   = 'База местоположений';
    $MESS['tfl__update-sx']         = 'Обновить базу сейчас';
    $MESS['tfl__update-sx-submit']  = 'Обновить';
    $MESS['tfl__update-sx-last']    = 'Последнее обновление #date#';
    $MESS['tfl__update-sx-no-curl'] = 'На хостинге не найдена библиотека curl, обновление невозможно';
}