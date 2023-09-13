<?php
/**
 * @var \CAdminTabControl $tabControl
 * @var \CMain $APPLICATION
 * @var array $sites
 */


use Bitrix\Main\Localization\Loc;
use TwoFingers\Location\Entity\Location as LocationEntity;
use TwoFingers\Location\Factory\LocationFactory;
use TwoFingers\Location\Helper\Tabs;
use TwoFingers\Location\Model\Location;
use TwoFingers\Location\Options;

__AdmSettingsDrawList($mid, $allOptions[Tabs::LIST]);
?>
    <tr>
        <td colspan="2">
            <table style="margin-top: 15px" class="adm-detail-content-table edit-table tfl__subsettings-table">
                <tr>
                    <th style="text-align: right; font-size: 16px"><?= Loc::getMessage('tfl__list-popup') ?></th>
                    <td align="left"
                        style="text-align: left; color: gray"><?= Loc::getMessage('tfl__list-mobile') ?></td>
                    <td align="left"
                        style="text-align: left; color: gray"><?= Loc::getMessage('tfl__list-desktop') ?></td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                        <label><?= Loc::getMessage('tfl__list-list-padding-top-bottom') ?></label>
                    </td>
                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_MOBILE_PADDING_TOP); ?>
                        <?=Loc::getMessage('tfl__px');?>
                        <?php Tabs::showTextInput(Options::LIST_MOBILE_PADDING_BOTTOM); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>
                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_DESKTOP_PADDING_TOP); ?>
                        <?=Loc::getMessage('tfl__px');?>
                        <?php Tabs::showTextInput(Options::LIST_DESKTOP_PADDING_BOTTOM); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                        <label><?= Loc::getMessage('tfl__list-list-padding-left-right') ?></label>
                    </td>
                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_MOBILE_PADDING_LEFT); ?>
                        <?=Loc::getMessage('tfl__px');?>
                        <?php Tabs::showTextInput(Options::LIST_MOBILE_PADDING_RIGHT); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>
                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_DESKTOP_PADDING_LEFT); ?>
                        <?=Loc::getMessage('tfl__px');?>
                        <?php Tabs::showTextInput(Options::LIST_DESKTOP_PADDING_RIGHT); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                        <label><?= Loc::getMessage('tfl__list-width') ?></label>
                    </td>
                    <td width="30%" class="adm-detail-content-cell-r">
                        <span style="color: gray">100%</span>
                    </td>

                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_DESKTOP_WIDTH); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                        <label><?= Loc::getMessage('tfl__list-height') ?></label>
                    </td>
                    <td width="30%" class="adm-detail-content-cell-r">
                        <span style="color: gray">100%</span>
                    </td>

                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_DESKTOP_HEIGHT); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                        <label><?= Loc::getMessage('tfl__list-border-radius') ?></label>
                    </td>
                    <td width="30%" class="adm-detail-content-cell-r">
                        <span style="color: gray">0 px.</span>
                    </td>

                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_DESKTOP_RADIUS); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td colspan="2">
            <table style="margin-top: 15px" class="adm-detail-content-table edit-table tfl__subsettings-table">
                <tr>
                    <th style="text-align: right; font-size: 16px"><?= Loc::getMessage('tfl__list-input') ?></th>
                    <td align="left"
                        style="text-align: left; color: gray"><?= Loc::getMessage('tfl__list-mobile') ?></td>
                    <td align="left"
                        style="text-align: left; color: gray"><?= Loc::getMessage('tfl__list-desktop') ?></td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                        <label><?= Loc::getMessage('tfl__list-input-font-size') ?></label>
                    </td>
                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_MOBILE_INPUT_FONT_SIZE); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>

                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_DESKTOP_INPUT_FONT_SIZE); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                        <label><?= Loc::getMessage('tfl__list-input-focus-border-color') ?></label>
                    </td>
                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showColorInput(Options::LIST_MOBILE_INPUT_FOCUS_BORDER_COLOR);?>
                        <span class="tfl-help"><?=Loc::getMessage('TF_LOCATION_COLOR_HELP')?></span>
                    </td>

                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showColorInput(Options::LIST_DESKTOP_INPUT_FOCUS_BORDER_COLOR);?>
                        <span class="tfl-help"><?=Loc::getMessage('TF_LOCATION_COLOR_HELP')?></span>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                        <label><?= Loc::getMessage('tfl__list-input-focus-border-width') ?></label>
                    </td>
                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_MOBILE_INPUT_FOCUS_BORDER_WIDTH);?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>

                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_DESKTOP_INPUT_FOCUS_BORDER_WIDTH);?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                        <label><?= Loc::getMessage('tfl__list-input-offset') ?></label>
                    </td>
                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_MOBILE_INPUT_OFFSET_TOP);?>
                        <?=Loc::getMessage('tfl__px');?>
                        <?php Tabs::showTextInput(Options::LIST_MOBILE_INPUT_OFFSET_BOTTOM);?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>

                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_DESKTOP_INPUT_OFFSET_TOP);?>
                        <?=Loc::getMessage('tfl__px');?>
                        <?php Tabs::showTextInput(Options::LIST_DESKTOP_INPUT_OFFSET_BOTTOM);?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <table style="margin-top: 15px" class="adm-detail-content-table edit-table tfl__subsettings-table">
                <tr>
                    <th style="text-align: right; font-size: 16px"><?= Loc::getMessage('tfl__list-close') ?></th>
                    <td align="left"
                        style="text-align: left; color: gray"><?= Loc::getMessage('tfl__list-mobile') ?></td>
                    <td align="left"
                        style="text-align: left; color: gray"><?= Loc::getMessage('tfl__list-desktop') ?></td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                        <label><?= Loc::getMessage('tfl__list-close-area-offset') ?></label>
                    </td>
                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_DESKTOP_CLOSE_AREA_OFFSET_TOP); ?>
                        <?=Loc::getMessage('tfl__px');?>
                        <?php Tabs::showTextInput(Options::LIST_DESKTOP_CLOSE_AREA_OFFSET_RIGHT); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>

                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_MOBILE_CLOSE_AREA_OFFSET_TOP); ?>
                        <?=Loc::getMessage('tfl__px');?>
                        <?php Tabs::showTextInput(Options::LIST_MOBILE_CLOSE_AREA_OFFSET_RIGHT); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                        <label><?= Loc::getMessage('tfl__list-close-area-size') ?></label>
                    </td>
                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_MOBILE_CLOSE_AREA_SIZE); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>

                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_DESKTOP_CLOSE_AREA_SIZE); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                        <label><?= Loc::getMessage('tfl__list-close-line-height') ?></label>
                    </td>
                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_MOBILE_CLOSE_LINE_HEIGHT); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>

                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_DESKTOP_CLOSE_LINE_HEIGHT); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                        <label><?= Loc::getMessage('tfl__list-close-line-width') ?></label>
                    </td>
                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_MOBILE_CLOSE_LINE_WIDTH); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>

                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_DESKTOP_CLOSE_LINE_WIDTH); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td colspan="2">
            <table style="margin-top: 15px" class="adm-detail-content-table edit-table tfl__subsettings-table">
                <tr>
                    <th style="text-align: right; font-size: 16px"><?= Loc::getMessage('tfl__list-other') ?></th>
                    <td align="left"
                        style="text-align: left; color: gray"><?= Loc::getMessage('tfl__list-mobile') ?></td>
                    <td align="left"
                        style="text-align: left; color: gray"><?= Loc::getMessage('tfl__list-desktop') ?></td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                        <label><?= Loc::getMessage('tfl__list-title-font-size') ?></label>
                    </td>
                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_MOBILE_TITLE_FONT_SIZE); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>
                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_DESKTOP_TITLE_FONT_SIZE); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>
                </tr>

                <tr>
                    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                        <label><?= Loc::getMessage('tfl__list-items-font-size') ?></label>
                    </td>
                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_MOBILE_ITEMS_FONT_SIZE); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>

                    <td width="30%" class="adm-detail-content-cell-r">
                        <?php Tabs::showTextInput(Options::LIST_DESKTOP_ITEMS_FONT_SIZE); ?>
                        <?=Loc::getMessage('tfl__px');?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr class="heading">
        <td colspan="2"><?= Loc::getMessage('tf-location__favorite-locations-heading') ?></td>
    </tr>
<?php

foreach ($sites as $siteLid => $siteName):

    ?>
    <tr class="">
        <td width="40%" class="adm-detail-content-cell-l" valign="top">
            [<?= $siteLid ?>] <?= $siteName ?>:
        </td>
        <td width="60%" class="adm-detail-content-cell-r" valign="top">
            <ul class="tfl__cities">
                <?php

                $defaultsCollection = LocationFactory::buildFavoritesCollection($siteLid,
                    LANGUAGE_ID);

                if ($defaultsCollection->count()):

                    /** @var LocationEntity $defaultLocation */
                    foreach ($defaultsCollection as $defaultLocation):

                        $parentsCollection = LocationFactory::buildParentsCollection($defaultLocation);
                        $parents    = [];
                        foreach ($parentsCollection as $parent) {
                            $parents[] = $parent->getName();
                        }

                        ?>
                        <li data-id="<?= $defaultLocation->getId(); ?>">
                            <?= $defaultLocation->getName(); ?>
                            <div style="color: #999; font-size: 0.85em">
                                <?= $parents ? implode(', ', $parents) : '' ?>
                            </div>
                        </li>
                    <?php endforeach;

                else: ?>
                    <li class="empty_location_el"><?= Loc::getMessage('tf-location__empty-list'); ?></li>
                <?php endif; ?>
            </ul>
        </td>
    </tr>
<?php endforeach; ?>


    <tr class="">
        <td width="40%" class="adm-detail-content-cell-l" valign="top"></td>
        <td width="60%" class="adm-detail-content-cell-r" valign="top">
            <?= Location::getType() == Location::TYPE_SALE
                ? Loc::getMessage('TF_LOCATION_DEFAULT_CITIES_S2')
                : Loc::getMessage('TF_LOCATION_DEFAULT_CITIES_INTERNAL') ?>
            <div class="tfl-help"><?= Loc::getMessage('TF_LOCATION_ADD_CITY_HELP') ?></div>
        </td>
    </tr>

    <tr class="heading">
        <td colspan="2"><?= Loc::getMessage('TF_LOCATION_STRINGS_HEADING') ?></td>
    </tr>
<?php Tabs::showTextRow(Options::LIST_PRE_LINK_TEXT); ?>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
            <label for="TF_LOCATION_LOCATION_POPUP_HEADER"><?= Loc::getMessage('TF_LOCATION_LOCATION_POPUP_HEADER') ?>
                :</label>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input size="40" type="text" name="TF_LOCATION_LOCATION_POPUP_HEADER" id="TF_LOCATION_LOCATION_POPUP_HEADER"
                   value="<?= Options::getValue('TF_LOCATION_LOCATION_POPUP_HEADER') ?>">
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
            <label for="TF_LOCATION_LOCATION_POPUP_PLACEHOLDER"><?= Loc::getMessage('TF_LOCATION_LOCATION_POPUP_PLACEHOLDER') ?>
                :</label>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input size="40" type="text" name="TF_LOCATION_LOCATION_POPUP_PLACEHOLDER"
                   id="TF_LOCATION_LOCATION_POPUP_PLACEHOLDER"
                   value="<?= Options::getValue('TF_LOCATION_LOCATION_POPUP_PLACEHOLDER') ?>">
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
            <label for="TF_LOCATION_LOCATION_POPUP_NO_FOUND"><?= Loc::getMessage('TF_LOCATION_LOCATION_POPUP_NO_FOUND') ?>
                :</label>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input size="40" type="text" name="TF_LOCATION_LOCATION_POPUP_NO_FOUND"
                   id="TF_LOCATION_LOCATION_POPUP_NO_FOUND"
                   value="<?= Options::getValue('TF_LOCATION_LOCATION_POPUP_NO_FOUND') ?>">
        </td>
    </tr>
