<?
/**
 * @var array $settings
 * @var \CAdminTabControl $tabControl
 * @var \CMain $APPLICATION
 * @var array $sites
 */

use TwoFingers\Location\Helper\Tabs;
use Bitrix\Main\Localization\Loc;
use TwoFingers\Location\Model\Location;
use TwoFingers\Location\Options;
use TwoFingers\Location\Service\SxGeo;

$tabControl->BeginNextTab(); ?>
    <tr class="heading">
        <td colspan="2"><?=Loc::getMessage('TF_LOCATION_VISUAL_HEADING')?></td>
    </tr>
    <?php
    Tabs::showCheckboxRow('TF_LOCATION_JQUERY_INCLUDE');
    Tabs::showCheckboxRow(Options::REPLACE_PLACEHOLDERS);

    ?>
    <tr class="heading">
        <td colspan="2"><?=Loc::getMessage('TF_LOCATION_REDIRECTING_HEADING')?></td>
    </tr>
    <?
    Tabs::showSelectBoxRow('TF_LOCATION_REDIRECT');
    ?>
    <tr class="heading">
        <td colspan="2"><?=Loc::getMessage('tf-location__default-locations-heading')?></td>
    </tr>
    <?if (in_array(Location::getType(), [Location::TYPE__SALE_2, Location::TYPE__INTERNAL])):

        foreach ($sites as $siteLid => $siteName):

            ?><tr class="">
                <td width="40%" class="adm-detail-content-cell-l" valign="top">
                    <?='[' . $siteLid . '] ' . $siteName?>:
                </td>
                <td width="60%" class="adm-detail-content-cell-r" valign="top">

                    <? if(Location::getType() == Location::TYPE__SALE_2):

                        $APPLICATION->IncludeComponent(
                            "bitrix:sale.location.selector.search",
                            ".default",
                            array(
                                "COMPONENT_TEMPLATE" => ".default",
                                "ID" => '',
                                "INPUT_NAME" => Options::DEFAULT_LOCATION . ":" . $siteLid,
                                "CODE" => Options::getDefaultLocation($siteLid),
                                "PROVIDE_LINK_BY" => "code",
                                "FILTER_BY_SITE" => "N",
                                "SHOW_DEFAULT_LOCATIONS" => "N",
                                "FILTER_SITE_ID" => "current",
                                "CACHE_TYPE" => "A",
                                "CACHE_TIME" => "36000000",
                                "JS_CONTROL_GLOBAL_ID" => "",
                                "SUPPRESS_ERRORS" => "N",
                                "INITIALIZE_BY_GLOBAL_EVENT" => "",
                                "COMPOSITE_FRAME_MODE" => "A",
                                "COMPOSITE_FRAME_TYPE" => "AUTO"
                            ),
                            false
                        );

                    else:

                        $defaultLocation = Location\Internal::getDefault(LANGUAGE_ID, $siteLid);
                        echo isset($defaultLocation['NAME'])
                            ? $defaultLocation['NAME']
                            : Loc::getMessage('tf-location__default-city-none');
                        ?>
                        <?=Loc::getMessage('tf-location__default-city-internal-change')?>
                    <? endif; ?>
                </td>
            </tr><?
        endforeach;
            ?><tr class="">
                <td width="40%" class="adm-detail-content-cell-l" valign="top">
                    <?=Loc::getMessage('tf-location__default-city-all-sites')?>:
                </td>
                <td width="60%" class="adm-detail-content-cell-r" valign="top"><?

                    if(Location::getType() == Location::TYPE__SALE_2):

                        $APPLICATION->IncludeComponent(
                            "bitrix:sale.location.selector.search",
                            ".default",
                            array(
                                "COMPONENT_TEMPLATE" => ".default",
                                "ID" => '',
                                "CODE" => Options::getDefaultLocation(),
                                "INPUT_NAME" => Options::DEFAULT_LOCATION,
                                "PROVIDE_LINK_BY" => "code",
                                "FILTER_BY_SITE" => "N",
                                "SHOW_DEFAULT_LOCATIONS" => "N",
                                "FILTER_SITE_ID" => "current",
                                "CACHE_TYPE" => "A",
                                "CACHE_TIME" => "36000000",
                                "JS_CONTROL_GLOBAL_ID" => "",
                                "SUPPRESS_ERRORS" => "N",
                                "INITIALIZE_BY_GLOBAL_EVENT" => "",
                                "COMPOSITE_FRAME_MODE" => "A",
                                "COMPOSITE_FRAME_TYPE" => "AUTO"
                            ),
                            false
                        );

                    else:
                        $defaultLocation = Location\Internal::getDefault(LANGUAGE_ID, false);
                        echo isset($defaultLocation['NAME'])
                            ? $defaultLocation['NAME']
                            : Loc::getMessage('tf-location__default-city-none');

                        echo Loc::getMessage('tf-location__default-city-internal-change');

                    endif;

                    ?>
                    <div class="tfl-help"><?=Loc::getMessage('tf-location__default-city-help')?><br><?=Loc::getMessage('TF_LOCATION_ADD_CITY_HELP')?></div>
                </td>
            </tr>
        <?php
    endif; ?>
    <tr class="heading">
        <td colspan="2"><?=Loc::getMessage('tf-location__locations-heading')?></td>
    </tr><?php

    Tabs::showCheckboxRow('TF_LOCATION_FILTER_BY_SITE_LOCATIONS');
    Tabs::showTextRow(Options::COOKIE_LIFETIME);
    Tabs::showTextRow(Options::LOCATIONS_LIMIT);
    Tabs::showTextRow(Options::SEARCH_LIMIT);

    if(Location::getType() == Location::TYPE__SALE_2)
        Tabs::showCheckboxRow('TF_LOCATION_SHOW_VILLAGES');

    Tabs::showCheckboxRow(Options::SX_GEO_MEMORY);

    ?>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text" >
            <label for="tfl__update-sx"><?=Loc::getMessage('tfl__update-sx') ?>:</label>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <?if (function_exists('curl_init')): ?>
                <input type="submit" value="<?=Loc::getMessage('tfl__update-sx-submit') ?>" name="update-sx">
                <?
                    $date       = SxGeo::getLastUpdateDate();
                    $dateStr    = $date ? $date->format('d.m.Y H:i:s') : '-';

                ?><div class="tfl-help"><?=Loc::getMessage('tfl__update-sx-last', ['#date#' => $dateStr])?></div>
            <? else: ?>
                <p style="margin-top: 5px"><?=Loc::getMessage('tfl__update-sx-no-curl')?></p>
            <? endif; ?>
        </td><?
        Tabs::showCheckboxRow(Options::CAPABILITY_MODE);
        ?>
    </tr>
<? $tabControl->EndTab();