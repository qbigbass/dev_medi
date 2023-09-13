<?php
/**
 * @var \CAdminTabControl $tabControl
 * @var \CMain $APPLICATION
 */

use Bitrix\Main\Localization\Loc;
use TwoFingers\Location\Helper\Tabs;
use TwoFingers\Location\Options;

$confirmOpen = Options::getConfirmOpen();

?>
<tr class="tfl__redirect-rule">
    <td width="40%" class="adm-detail-content-cell-l" valign="top">
        <?= Loc::getMessage('tfl__' . Options::CONFIRM_OPEN) ?>
    </td>
    <td width="60%" class="adm-detail-content-cell-r" valign="top">
        <?php foreach (
            [
                Options::CONFIRM_OPEN_NOT_DETECTED,
                Options::CONFIRM_OPEN_DETECTED,
                Options::CONFIRM_OPEN_ALWAYS
            ] as $curConfirmOpen
        ): ?>
            <input type="checkbox"
                   id="<?= Options::CONFIRM_OPEN ?>_<?= $curConfirmOpen ?>"
                   name="<?= Options::CONFIRM_OPEN ?>[]"
                   value="<?= $curConfirmOpen ?>"
                <?= in_array($curConfirmOpen, (array)$confirmOpen) ? ' checked="checked" ' : '' ?>
                   class="adm-designed-checkbox">
            <label class="adm-designed-checkbox-label" for="<?= Options::CONFIRM_OPEN ?>_<?= $curConfirmOpen ?>"
                   title="">
                <?= Loc::getMessage('tfl__' . Options::CONFIRM_OPEN . '_' . $curConfirmOpen); ?>
            </label><br>
        <?php endforeach; ?>
    </td>
</tr>
<?php __AdmSettingsDrawList($mid, $allOptions[Tabs::CONFIRM]); ?>
<tr>
    <td colspan="2">
        <table class="adm-detail-content-table edit-table tfl__subsettings-table">
            <tr>
                <th></th>
                <th align="left" style="text-align: left"><?= Loc::getMessage('TF_LOCATION_CONFIRM_BUTTON') ?></th>
                <th align="left" style="text-align: left"><?= Loc::getMessage('TF_LOCATION_CANCEL_BUTTON') ?></th>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                    <label><?= Loc::getMessage('TF_LOCATION_COLOR') ?>:</label>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?php Tabs::showColorInput('TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR'); ?> /
                    <?php Tabs::showColorInput('TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR_HOVER'); ?>
                    <span class="tfl-help"><?= Loc::getMessage('TF_LOCATION_COLOR_HELP') ?></span>
                </td>

                <td width="30%" class="adm-detail-content-cell-r">
                    <?php Tabs::showColorInput('TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR'); ?> /
                    <?php Tabs::showColorInput('TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR_HOVER'); ?>
                    <span class="tfl-help"><?= Loc::getMessage('TF_LOCATION_COLOR_HELP') ?></span>
                </td>
            </tr>

            <tr>
                <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                    <label><?= Loc::getMessage('TF_LOCATION_BG') ?>:</label>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?php Tabs::showColorInput('TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG'); ?> /
                    <?php Tabs::showColorInput('TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG_HOVER'); ?>
                    <span class="tfl-help"><?= Loc::getMessage('TF_LOCATION_COLOR_HELP') ?></span>
                </td>

                <td width="30%" class="adm-detail-content-cell-r">
                    <?php Tabs::showColorInput('TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG'); ?> /
                    <?php Tabs::showColorInput('TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG_HOVER'); ?>
                    <span class="tfl-help"><?= Loc::getMessage('TF_LOCATION_COLOR_HELP') ?></span>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td colspan="2">
        <table style="margin-top: 15px" class="adm-detail-content-table edit-table tfl__subsettings-table">
            <tr>
                <th style="text-align: right; font-size: 16px"><?= Loc::getMessage('tfl__list-padding') ?></th>
                <td align="left" style="text-align: left; color: gray"><?= Loc::getMessage('tfl__list-mobile') ?></td>
                <td align="left" style="text-align: left; color: gray"><?= Loc::getMessage('tfl__list-desktop') ?></td>
            </tr>

            <tr>
                <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                    <label><?= Loc::getMessage('tfl__list-list-padding-top-bottom') ?></label>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?php Tabs::showTextInput(Options::CONFIRM_MOBILE_PADDING_TOP); ?>
                    <?= Loc::getMessage('tfl__px'); ?>
                    <?php Tabs::showTextInput(Options::CONFIRM_MOBILE_PADDING_BOTTOM); ?>
                    <?= Loc::getMessage('tfl__px'); ?>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?php Tabs::showTextInput(Options::CONFIRM_DESKTOP_PADDING_TOP); ?>
                    <?= Loc::getMessage('tfl__px'); ?>
                    <?php Tabs::showTextInput(Options::CONFIRM_DESKTOP_PADDING_BOTTOM); ?>
                    <?= Loc::getMessage('tfl__px'); ?>
                </td>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                    <label><?= Loc::getMessage('tfl__list-list-padding-left-right') ?></label>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?php Tabs::showTextInput(Options::CONFIRM_MOBILE_PADDING_LEFT); ?>
                    <?= Loc::getMessage('tfl__px'); ?>
                    <?php Tabs::showTextInput(Options::CONFIRM_MOBILE_PADDING_RIGHT); ?>
                    <?= Loc::getMessage('tfl__px'); ?>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?php Tabs::showTextInput(Options::CONFIRM_DESKTOP_PADDING_LEFT); ?>
                    <?= Loc::getMessage('tfl__px'); ?>
                    <?php Tabs::showTextInput(Options::CONFIRM_DESKTOP_PADDING_RIGHT); ?>
                    <?= Loc::getMessage('tfl__px'); ?>
                </td>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                    <label><?= Loc::getMessage('tfl__confirm-button-top-padding') ?></label>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?php Tabs::showTextInput(Options::CONFIRM_MOBILE_BUTTON_TOP_PADDING); ?>
                    <?= Loc::getMessage('tfl__px'); ?>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?php Tabs::showTextInput(Options::CONFIRM_DESKTOP_BUTTON_TOP_PADDING); ?>
                    <?= Loc::getMessage('tfl__px'); ?>
                </td>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                    <label><?= Loc::getMessage('tfl__confirm-button-between-padding') ?></label>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?php Tabs::showTextInput(Options::CONFIRM_MOBILE_BUTTON_BETWEEN_PADDING); ?>
                    <?= Loc::getMessage('tfl__px'); ?>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?php Tabs::showTextInput(Options::CONFIRM_DESKTOP_BUTTON_BETWEEN_PADDING); ?>
                    <?= Loc::getMessage('tfl__px'); ?>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td colspan="2">
        <table style="margin-top: 15px" class="adm-detail-content-table edit-table tfl__subsettings-table">
            <tr>
                <th style="text-align: right; font-size: 16px"><?= Loc::getMessage('tfl__list-font-size') ?></th>
                <td align="left" style="text-align: left; color: gray"><?= Loc::getMessage('tfl__list-mobile') ?></td>
                <td align="left" style="text-align: left; color: gray"><?= Loc::getMessage('tfl__list-desktop') ?></td>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                    <label><?= Loc::getMessage('tfl__confirm-text-font-size') ?></label>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?php Tabs::showTextInput(Options::CONFIRM_MOBILE_TEXT_FONT_SIZE); ?>
                    <?= Loc::getMessage('tfl__px'); ?>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?php Tabs::showTextInput(Options::CONFIRM_DESKTOP_TEXT_FONT_SIZE); ?>
                    <?= Loc::getMessage('tfl__px'); ?>
                </td>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                    <label><?= Loc::getMessage('tfl__confirm-button-font-size') ?></label>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?php Tabs::showTextInput(Options::CONFIRM_MOBILE_BUTTON_FONT_SIZE); ?>
                    <?= Loc::getMessage('tfl__px'); ?>
                </td>

                <td width="30%" class="adm-detail-content-cell-r">
                    <?php Tabs::showTextInput(Options::CONFIRM_DESKTOP_BUTTON_FONT_SIZE); ?>
                    <?= Loc::getMessage('tfl__px'); ?>
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
                <td align="left" style="text-align: left; color: gray"><?= Loc::getMessage('tfl__list-mobile') ?></td>
                <td align="left" style="text-align: left; color: gray"><?= Loc::getMessage('tfl__list-desktop') ?></td>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                    <label><?= Loc::getMessage('tfl__confirm-width') ?></label>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <span style="color: gray">100%</span>
                </td>

                <td width="30%" class="adm-detail-content-cell-r">
                    <?php Tabs::showTextInput(Options::CONFIRM_DESKTOP_WIDTH); ?>
                    <?= Loc::getMessage('tfl__' . Options::LIST_DESKTOP_RADIUS . '_post-input'); ?>
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
                    <?php Tabs::showTextInput('TF_LOCATION_CONFIRM_POPUP_RADIUS'); ?>
                    <?= Loc::getMessage('tfl__' . Options::LIST_DESKTOP_RADIUS . '_post-input'); ?>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr class="heading">
    <td colspan="2"><?= Loc::getMessage('TF_LOCATION_STRINGS_HEADING') ?></td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
        <label for="TF_LOCATION_CONFIRM_POPUP_TEXT"><?= Loc::getMessage('TF_LOCATION_CONFIRM_POPUP_TEXT') ?>:</label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input size="40" type="text" name="TF_LOCATION_CONFIRM_POPUP_TEXT" id="TF_LOCATION_CONFIRM_POPUP_TEXT"
               value="<?= Options::getValue('TF_LOCATION_CONFIRM_POPUP_TEXT') ?>">
        <div class="tfl-help"><?= Loc::getMessage('tf-location__confirm-popup-text-help') ?></div>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
        <label for="TF_LOCATION_CONFIRM_POPUP_TEXT"><?= Loc::getMessage('TF_LOCATION_CONFIRM_POPUP_ERROR_TEXT') ?>
            :</label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input size="40" type="text" name="TF_LOCATION_CONFIRM_POPUP_ERROR_TEXT"
               id="TF_LOCATION_CONFIRM_POPUP_ERROR_TEXT"
               value="<?= Options::getValue('TF_LOCATION_CONFIRM_POPUP_ERROR_TEXT') ?>">
        <div class="tfl-help"><?= Loc::getMessage('TF_LOCATION_CONFIRM_POPUP_ERROR_TEXT_HELP') ?></div>
    </td>
</tr>
