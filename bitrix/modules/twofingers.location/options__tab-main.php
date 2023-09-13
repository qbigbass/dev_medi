<?php
/**
 * @var CAdminTabControl $tabControl
 * @var CMain $APPLICATION
 * @var array $sites
 */

use TwoFingers\Location\Helper\Tabs;
use Bitrix\Main\Localization\Loc;
use TwoFingers\Location\Model\Location;
use TwoFingers\Location\Options;


$redirectEvent = Options::getRedirectEvent();
if (!is_array($redirectEvent))
    $redirectEvent = [$redirectEvent];
?>
    <tr class="tfl__redirect-rule">
        <td width="40%" class="adm-detail-content-cell-l" valign="top">
            <?= Loc::getMessage('tfl__' . Options::REDIRECT_EVENT) ?>
        </td>
        <td width="60%" class="adm-detail-content-cell-r" valign="top">
            <?php foreach (
                [
                    Options::REDIRECT_EVENT_SELECTED,
                    Options::REDIRECT_EVENT_DETECTED,
                    Options::REDIRECT_EVENT_CONFIRMED
                ] as $curRedirectEvent
            ): ?>
                <input type="checkbox"
                       id="<?= Options::REDIRECT_EVENT ?>_<?= $curRedirectEvent ?>"
                       name="<?= Options::REDIRECT_EVENT ?>[]"
                       value="<?= $curRedirectEvent ?>"
                    <?= in_array($curRedirectEvent, $redirectEvent) ? ' checked="checked" ' : '' ?>
                       class="adm-designed-checkbox">
                <label class="adm-designed-checkbox-label" for="<?= Options::REDIRECT_EVENT ?>_<?= $curRedirectEvent ?>"
                       title="">
                    <?= Loc::getMessage('tfl__' . Options::REDIRECT_EVENT . '_' . $curRedirectEvent); ?>
                </label><br>
            <?php endforeach; ?>
        </td>
    </tr>

<?php

__AdmSettingsDrawList($mid, $allOptions[Tabs::LOCATIONS]);

?>

    <tr class="heading">
        <td colspan="2"><?= Loc::getMessage('tf-location__default-locations-heading') ?></td>
    </tr>
<?php foreach ($sites as $siteLid => $siteName):

    ?>
    <tr class="">
    <td width="40%" class="adm-detail-content-cell-l" valign="top">
        <?= '[' . $siteLid . '] ' . $siteName ?>:
    </td>
    <td width="60%" class="adm-detail-content-cell-r" valign="top">

        <?php if (Location::getType() == Location::TYPE_SALE):

            $APPLICATION->IncludeComponent(
                "bitrix:sale.location.selector.search",
                ".default",
                [
                    "COMPONENT_TEMPLATE"         => ".default",
                    "ID"                         => '',
                    "INPUT_NAME"                 => Options::DEFAULT_LOCATION . ":" . $siteLid,
                    "CODE"                       => Options::getDefaultLocation($siteLid),
                    "PROVIDE_LINK_BY"            => "code",
                    "FILTER_BY_SITE"             => "N",
                    "SHOW_DEFAULT_LOCATIONS"     => "N",
                    "FILTER_SITE_ID"             => "current",
                    "CACHE_TYPE"                 => "A",
                    "CACHE_TIME"                 => "36000000",
                    "JS_CONTROL_GLOBAL_ID"       => "",
                    "SUPPRESS_ERRORS"            => "N",
                    "INITIALIZE_BY_GLOBAL_EVENT" => "",
                    "COMPOSITE_FRAME_MODE"       => "A",
                    "COMPOSITE_FRAME_TYPE"       => "AUTO"
                ],
                false
            );

        else:

            $defaultLocation = Location\Internal::getDefault($siteLid, LANGUAGE_ID);
            echo $defaultLocation['NAME'] ?? Loc::getMessage('tf-location__default-city-none');
            ?>
            <?= Loc::getMessage('tf-location__default-city-internal-change') ?>
        <?php endif; ?>
    </td>
    </tr><?php
endforeach;