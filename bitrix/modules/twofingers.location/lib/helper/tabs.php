<?php
/**
 * Created by PhpStorm.
 * User: Павел
 * Date: 12.12.2019
 * Time: 17:26
 *
 *
 */

namespace TwoFingers\Location\Helper;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Localization\Loc;
use TwoFingers\Location\Model\Location;
use TwoFingers\Location\Options;
use TwoFingers\Location\Service\SxGeo;

Loc::loadMessages(__FILE__);

/**
 * Class Options
 *
 * @package TwoFingers\Location
 *
 */
class Tabs
{
    const LOCATIONS = 'locations';
    const LIST      = 'list';
    const CONFIRM   = 'confirm';
    const SALE      = 'sale';
    const SETTINGS  = 'settings';

    /**
     * @return array[]
     */
    public static function getMap(): array
    {
        $tabs = [
            [
                'TAB'   => Loc::getMessage('tfl__tab-' . self::LOCATIONS),
                'DIV'   => self::LOCATIONS,
                'TITLE' => Loc::getMessage('tfl__tab-' . self::LOCATIONS . '_TITLE'),
            ],
            [
                'TAB'   => Loc::getMessage('tfl__tab-popup-list'),
                'DIV'   => self::LIST,
                'TITLE' => Loc::getMessage('tfl__tab-popup-list_DESCR'),
            ],
            [
                'TAB'   => Loc::getMessage('tfl__tab-popup-confirm'),
                'DIV'   => self::CONFIRM,
                'TITLE' => Loc::getMessage('tfl__tab-popup-confirm_DESCR'),
            ]
        ];

        if (Location::getType() != Location::TYPE_IBLOCK) {
            $tabs[] = [
                'TAB'   => Loc::getMessage('tfl__tab-sale'),
                'DIV'   => self::SALE,
                'TITLE' => Loc::getMessage('tfl__tab-saleDESCR'),
            ];
        }

        $tabs[] = [
            'TAB'   => Loc::getMessage('tfl__tab-' . self::SETTINGS),
            'DIV'   => self::SETTINGS,
            'TITLE' => Loc::getMessage('tfl__tab-' . self::SETTINGS . '_DESCR'),
        ];

        return $tabs;
    }

    /**
     * @return \array[][]
     */
    /**
     * @return array
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     *
     * @TODO: add all tabs
     */
    public static function getAllOptions(): array
    {
        return [
            self::LOCATIONS => self::getLocationsOptions(),
            self::LIST      => self::getListOptions(),
            self::CONFIRM   => self::getConfirmOptions(),
            self::SALE      => self::getSaleOptions(),
            self::SETTINGS  => self::getSettingsOptions(),
        ];
    }

    /**
     * @return array[]
     */
    public static function getLocationsOptions(): array
    {
        $noDomainActions = [
            Options::NO_DOMAIN_ACTION_NONE         => Loc::getMessage('tfl__' . Options::NO_DOMAIN_ACTION . '_' . Options::NO_DOMAIN_ACTION_NONE),
            Options::NO_DOMAIN_ACTION_CURRENT_SITE => Loc::getMessage('tfl__' . Options::NO_DOMAIN_ACTION . '_' . Options::NO_DOMAIN_ACTION_CURRENT_SITE),
            Options::NO_DOMAIN_ACTION_DEFAULT_SITE => Loc::getMessage('tfl__' . Options::NO_DOMAIN_ACTION . '_' . Options::NO_DOMAIN_ACTION_DEFAULT_SITE),
            //Options::NO_DOMAIN_ACTION_SITE_DEFAULT_LOCATION_DOMAIN  => Loc::getMessage('tfl__' . Options::NO_DOMAIN_ACTION . '_' . Options::NO_DOMAIN_ACTION_SITE_DEFAULT_LOCATION_DOMAIN),
            //Options::NO_DOMAIN_ACTION_ALL_SITES_DEFAULT_LOCATION_DOMAIN  => Loc::getMessage('tfl__' . Options::NO_DOMAIN_ACTION . '_' . Options::NO_DOMAIN_ACTION_ALL_SITES_DEFAULT_LOCATION_DOMAIN),
        ];

        $options = [
            [
                Options::NO_DOMAIN_ACTION,
                Loc::getMessage('tfl__' . Options::NO_DOMAIN_ACTION),
                '',
                ["selectbox", $noDomainActions]
            ],
        ];

       $options[] = [
            Options::REPLACE_PLACEHOLDERS,
            Loc::getMessage('tfl__' . Options::REPLACE_PLACEHOLDERS),
            '',
            [
                "checkbox",
                '',
                self::getCheckboxHelp(Loc::getMessage('tfl__' . Options::REPLACE_PLACEHOLDERS . '_help'))
            ]
        ];
        $options[] = [Options::COOKIE_LIFETIME, Loc::getMessage('tfl__' . Options::COOKIE_LIFETIME), '', ['text', 3]];

        return $options;
    }

    /**
     * @return array[]
     */
    public static function getListOptions(): array
    {
        $locationsLoad = [
            'all'      => Loc::getMessage('tfl__' . Options::LIST_LOCATIONS_LOAD . '_all'),
            'cities'   => Loc::getMessage('tfl__' . Options::LIST_LOCATIONS_LOAD . '_cities'),
            'defaults' => Loc::getMessage('tfl__' . Options::LIST_LOCATIONS_LOAD . '_defaults'),
        ];

        $favoritePosition = [
            'above-search'    => Loc::getMessage('tfl__' . Options::LIST_FAVORITES_POSITION . '-above-search'),
            'under-search'    => Loc::getMessage('tfl__' . Options::LIST_FAVORITES_POSITION . '-under-search'),
            'left-locations'  => Loc::getMessage('tfl__' . Options::LIST_FAVORITES_POSITION . '-left-locations'),
            'right-locations' => Loc::getMessage('tfl__' . Options::LIST_FAVORITES_POSITION . '-right-locations'),
        ];

        $availableFonts = [
            ''          => Loc::getMessage('tfl__font-default'),
            'Open Sans' => Loc::getMessage('tfl__font-open-sans'),
            'Roboto'    => Loc::getMessage('tfl__font-roboto'),
        ];

        $options = [
            [
                Options::LIST_OPEN_IF_NO_LOCATION,
                Loc::getMessage('tfl__' . Options::LIST_OPEN_IF_NO_LOCATION),
                '',
                ["checkbox"]
            ],


        ];

        if (Location::getType() == Location::TYPE_SALE) {
            $options[] = [
                Options::LIST_SHOW_VILLAGES,
                Loc::getMessage('tfl__' . Options::LIST_SHOW_VILLAGES),
                '',
                ["checkbox"]
            ];
        }


        $options = array_merge($options, [
            [Options::LIST_RELOAD_PAGE, Loc::getMessage('tfl__' . Options::LIST_RELOAD_PAGE), '', ["checkbox"]],
            [
                Options::CALLBACK,
                Loc::getMessage('tfl__' . Options::CALLBACK)
                . self::getHelp(Loc::getMessage('tfl__' . Options::CALLBACK . '_help'), true),
                '',
                ["text", 80]
            ],
            Loc::getMessage('TF_LOCATION_VISUAL_HEADING'),
            [
                Options::LIST_LOCATIONS_LOAD,
                Loc::getMessage('tfl__' . Options::LIST_LOCATIONS_LOAD),
                '',
                ["selectbox", $locationsLoad]
            ],
            [
                Options::LIST_FAVORITES_POSITION,
                Loc::getMessage('tfl__' . Options::LIST_FAVORITES_POSITION),
                '',
                ["selectbox", $favoritePosition]
            ],
            [
                Options::LIST_TITLE_FONT_FAMILY,
                Loc::getMessage('tfl__' . Options::LIST_TITLE_FONT_FAMILY),
                '',
                ["selectbox", $availableFonts]
            ],
            [
                Options::LIST_ITEMS_FONT_FAMILY,
                Loc::getMessage('tfl__' . Options::LIST_ITEMS_FONT_FAMILY),
                '',
                ["selectbox", $availableFonts]
            ],
            [Options::LIST_LINK_CLASS, Loc::getMessage('tfl__' . Options::LIST_LINK_CLASS), '', ['text', 40]],
            //[Options::LIST_DESKTOP_WIDTH, Loc::getMessage('tfl__' . Options::LIST_DESKTOP_WIDTH), '', ['text', 4, self::getPostInput(Options::LIST_DESKTOP_WIDTH)]],
            [
                Options::LIST_MOBILE_BREAKPOINT,
                Loc::getMessage('tfl__' . Options::LIST_MOBILE_BREAKPOINT),
                '',
                ['text', 4, self::getPostInput(Options::LIST_MOBILE_BREAKPOINT)]
            ],
        ]);

        return $options;
    }

    /**
     * @return array[]
     */
    public static function getConfirmOptions(): array
    {
        $availableFonts = [
            ''          => Loc::getMessage('tfl__font-default'),
            'Open Sans' => Loc::getMessage('tfl__font-open-sans'),
            'Roboto'    => Loc::getMessage('tfl__font-roboto'),
        ];

        return [
            Loc::getMessage('TF_LOCATION_VISUAL_HEADING'),
            [
                Options::CONFIRM_TEXT_FONT_FAMILY,
                Loc::getMessage('tfl__' . Options::CONFIRM_TEXT_FONT_FAMILY),
                '',
                ["selectbox", $availableFonts]
            ]
        ];
    }

    /**
     * @return array[]
     */
    public static function getSaleOptions(): array
    {
        return [
            [Options::ORDER_SET_TEMPLATE, Loc::getMessage('tfl__' . Options::ORDER_SET_TEMPLATE), '', ["checkbox"]],
            [Options::ORDER_LINK_CLASS, Loc::getMessage('tfl__' . Options::ORDER_LINK_CLASS), '', ['text', 40]],
            [Options::ORDER_SET_LOCATION, Loc::getMessage('tfl__' . Options::ORDER_SET_LOCATION), '', ["checkbox"]],
            [Options::ORDER_SET_ZIP, Loc::getMessage('tfl__' . Options::ORDER_SET_ZIP), '', ["checkbox"]],
        ];
    }

    /**
     * @return array[]
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function getSettingsOptions(): array
    {
        ob_start();
        if (function_exists('curl_init')): ?>
            <input type="submit" value="<?= Loc::getMessage('tfl__update-sx-submit') ?>" name="update-sx">
            <?
            $date    = SxGeo::getLastUpdateDate();
            $dateStr = $date ? $date->format('d.m.Y H:i:s') : '-';

            ?>
            <div class="tfl-help"><?= Loc::getMessage('tfl__update-sx-last', ['#date#' => $dateStr]) ?></div>
        <? else: ?>
            <p style="margin-top: 5px"><?= Loc::getMessage('tfl__update-sx-no-curl') ?></p>
        <? endif;

        $updateButton = ob_get_clean();

        return [
            [
                Options::INCLUDE_JQUERY, Loc::getMessage('tfl__' . Options::INCLUDE_JQUERY), '', [
                "selectbox", [
                    ''        => Loc::getMessage('tfl__' . Options::INCLUDE_JQUERY . '_no'),
                    'jquery'  => 'jQuery 1.x.x',
                    'jquery2' => 'jQuery 2.x.x',
                    'jquery3' => 'jQuery 3.x.x',
                ]
            ]
            ],
            [Options::USE_GOOGLE_FONTS, Loc::getMessage('tfl__' . Options::USE_GOOGLE_FONTS), '', ["checkbox"]],
            [
                Options::LOCATIONS_LIMIT,
                Loc::getMessage('tfl__' . Options::LOCATIONS_LIMIT)
                . self::getHelp(Loc::getMessage('tfl__' . Options::LOCATIONS_LIMIT . '_help'), true),
                '',
                ['text', 5]
            ],
            [
                Options::SEARCH_LIMIT,
                Loc::getMessage('tfl__' . Options::SEARCH_LIMIT)
                . self::getHelp(Loc::getMessage('tfl__' . Options::SEARCH_LIMIT . '_help'), true),
                '',
                ['text', 5]
            ],
            [Options::CAPABILITY_MODE, Loc::getMessage('tfl__' . Options::CAPABILITY_MODE), '', ["checkbox"]],
            Loc::getMessage('tfl__header-geo-base'),
            [Options::SX_GEO_MEMORY, Loc::getMessage('tfl__' . Options::SX_GEO_MEMORY), '', ["checkbox"]],
            [
                Options::SX_GEO_AGENT_UPDATE,
                Loc::getMessage('tfl__' . Options::SX_GEO_AGENT_UPDATE),
                '',
                [
                    "checkbox",
                    '',
                    Options::isAgentsOnCron()
                        ? ''
                        : self::getCheckboxHelp(Loc::getMessage('tfl__' . Options::SX_GEO_AGENT_UPDATE . '_help'))
                ],
                Options::isAgentsOnCron() ? '' : 'Y'
            ],
            ['', Loc::getMessage('tfl__update-sx'), $updateButton, ['statichtml']],
            [Options::SX_GEO_PROXY_ENABLED, Loc::getMessage('tfl__' . Options::SX_GEO_PROXY_ENABLED), '', ["checkbox"]],
            [Options::SX_GEO_PROXY_NAME, Loc::getMessage('tfl__' . Options::SX_GEO_PROXY_NAME), '', ['text', 30]],
            [Options::SX_GEO_PROXY_PORT, Loc::getMessage('tfl__' . Options::SX_GEO_PROXY_PORT), '', ['text', 5]],
            [Options::SX_GEO_PROXY_PASS, Loc::getMessage('tfl__' . Options::SX_GEO_PROXY_PASS), '', ['text', 30]],
            [
                Options::SX_GEO_PROXY_TYPE,
                Loc::getMessage('tfl__' . Options::SX_GEO_PROXY_TYPE),
                '',
                [
                    'selectbox',
                    [
                        CURLPROXY_HTTP            => Loc::getMessage('tfl__' . CURLPROXY_HTTP),
                        CURLPROXY_HTTPS           => Loc::getMessage('tfl__' . CURLPROXY_HTTPS),
                        CURLPROXY_SOCKS4          => Loc::getMessage('tfl__' . CURLPROXY_SOCKS4),
                        CURLPROXY_SOCKS4A         => Loc::getMessage('tfl__' . CURLPROXY_SOCKS4A),
                        CURLPROXY_SOCKS5          => Loc::getMessage('tfl__' . CURLPROXY_SOCKS5),
                        CURLPROXY_SOCKS5_HOSTNAME => Loc::getMessage('tfl__' . CURLPROXY_SOCKS5_HOSTNAME),
                    ]
                ]
            ],
        ];
    }

    /**
     * @param      $help
     * @param bool $br
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getCheckboxHelp($help, bool $br = false): string
    {
        $help = trim($help);
        if (!strlen($help)) {
            return '';
        }

        return '>' . ($br ? '<br>' : '') . '<span style="color: gray; font-size: 85%"> ' . $help . '</span';
    }

    /**
     * @param       $help
     * @param false $br
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public static function getHelp($help, bool $br = false): string
    {
        $help = trim($help);
        if (!strlen($help)) {
            return '';
        }

        return ($br ? '<br>' : '') . '<span style="color: gray; font-size: 85%"> ' . $help . '</span>';
    }

    /**
     * @param $code
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function showTextRow($code)
    {
        ?>
        <tr><?php
        self::showLabel($code);
        self::showTextCell($code);
        ?></tr><?php
    }

    /**
     * @param $code
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function showCheckboxRow($code)
    {
        ?>
        <tr><?php
        self::showLabel($code);
        self::showInputCheckbox($code);
        ?></tr><?php
    }

    /**
     * @param $code
     */
    public static function showLabel($code)
    {
        $loc = Loc::getMessage($code) ?: Loc::getMessage('tfl__' . $code);

        ?>
        <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
        <label for="<?= $code ?>"><?= $loc ?>:</label>
        </td><?php
    }

    /**
     * @param $code
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function showSelectBoxRow($code)
    {
        ?>
        <tr><?php
        self::showLabel($code);
        self::showInputSelectBox($code);
        ?></tr><?php
    }

    /**
     * @param $code
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function showTextCell($code)
    {
        ?>
        <td width="60%" class="adm-detail-content-cell-r">
        <? self::showTextInput($code); ?>
        </td><?php
    }

    /**
     * @param $code
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException|ArgumentException
     */
    public static function showTextInput($code)
    {
        $settingsMap = Options::getMap();

        ?><input size="<?= $settingsMap[$code]['size'] ?? '40' ?>" type="text" name="<?= $code ?>" id="<?= $code ?>"
                 value="<?= Options::getValue($code) ?>">
        <?= self::getPostInput($code); ?>
        <? self::showHelp($code);
    }

    /**
     * @param $code
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException|ArgumentException
     */
    public static function showColorInput($code)
    {
        $settingsMap = Options::getMap();

        ?><input size="<?= $settingsMap[$code]['size'] ?? '40' ?>"
                 type="color"
                 name="<?= $code ?>"
                 id="<?= $code ?>"
                 value="<?= Options::getValue($code) ?>">
        <?= self::getPostInput($code); ?>
        <? self::showHelp($code);
    }

    /**
     * @param $code
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException|ArgumentException
     */
    public static function showInputSelectBox($code)
    {
        $settingsMap = Options::getMap();
        ?>
        <td width="60%" class="adm-detail-content-cell-r">
        <select name="<?= $code ?>" id="<?= $code ?>">
            <?php foreach ($settingsMap[$code]['options'] as $value => $name): ?>
                <option value="<?= $value ?>"<? if (Options::getValue($code) == $value): ?> selected<? endif ?>><?= $name ?></option>
            <?php endforeach; ?>
        </select>
        <?php self::showHelp($code); ?>
        </td><?php
    }

    /**
     * @param $code
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     */
    public static function showInputCheckbox($code)
    {
        ?>
        <td width="60%" class="adm-detail-content-cell-r">
        <input type="checkbox" name="<?= $code ?>" id="<?= $code ?>"
               value="Y" <? if (Options::getValue($code) == 'Y'): ?> checked<? endif ?>>
        <?php self::showHelp($code); ?>
        </td><?php
    }

    /**
     * @param $code
     */
    protected static function showHelp($code)
    {
        $help = Loc::getMessage($code . '_HELP') ?: Loc::getMessage('tfl__' . $code . '_help');
        if (strlen($help)): ?>
            <div class="tfl-help"><?= $help ?></div>
        <?php endif;
    }

    /**
     * @param $code
     * @return string|null
     */
    protected static function getPostInput($code): ?string
    {
        return Loc::getMessage($code . '_POST_INPUT') ?: Loc::getMessage('tfl__' . $code . '_post-input');
    }
}