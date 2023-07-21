<?php
/**
 * @var array $settings
 * @var \CAdminTabControl $tabControl
 * @var \CMain $APPLICATION
 * @var array $sites
 */

$tabControl->BeginNextTab();

use Bitrix\Main\Localization\Loc;
use TwoFingers\Location\Helper\Tabs;
use TwoFingers\Location\Model\Location;
use TwoFingers\Location\Options;

?>
<tr class="heading">
    <td colspan="2"><?=Loc::getMessage('tfl__behavior-heading')?></td>
</tr>
<?php

Tabs::showCheckboxRow(Options::LIST_OPEN_IF_NO_LOCATION);

?>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <label for="TF_LOCATION_LOAD_LOCATIONS"><?=Loc::getMessage('TF_LOCATION_LOAD_LOCATIONS') ?>:</label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <select name="TF_LOCATION_LOAD_LOCATIONS" id="TF_LOCATION_LOAD_LOCATIONS">
            <option value="all"<?if ($settings['TF_LOCATION_LOAD_LOCATIONS'] == 'all'):?> selected<?endif?>><?= Loc::getMessage('TF_LOCATION_LOAD_LOCATIONS_all')?></option>
            <? if(Location::getType() == Location::TYPE__SALE_2):?>
                <option value="cities"<?if ($settings['TF_LOCATION_LOAD_LOCATIONS'] == 'cities'):?> selected<?endif?>><?=Loc::getMessage('TF_LOCATION_LOAD_LOCATIONS_cities')?></option>
            <? endif; ?>
            <option value="defaults"<?if ($settings['TF_LOCATION_LOAD_LOCATIONS'] == 'defaults'):?> selected<?endif?>><?=Loc::getMessage('TF_LOCATION_LOAD_LOCATIONS_defaults')?></option>
        </select>
        <div class="tfl-help"><?=Loc::getMessage('TF_LOCATION_LOAD_LOCATIONS_HELP')?></div>
    </td>
</tr>
<?
/*if (Location::getType() == Location::TYPE__SALE_2)
    Tabs::showCheckboxRow(Options::OPTION__SITE_LOCATIONS_ONLY, $settings);*/

Tabs::showCheckboxRow('TF_LOCATION_RELOAD');
Tabs::showTextRow(Options::CALLBACK);
?>
<tr class="heading">
    <td colspan="2"><?=Loc::getMessage('tf-location__favorite-locations-heading')?></td>
</tr>
<?php

    foreach ($sites as $siteLid => $siteName):

    ?><tr class="">
        <td width="40%" class="adm-detail-content-cell-l" valign="top">
            [<?=$siteLid?>] <?=$siteName?>:
        </td>
        <td width="60%" class="adm-detail-content-cell-r" valign="top">
            <ul class="tfl__cities">
            <?$defaultCities = Location::getFavoritesList(LANGUAGE_ID, $siteLid);

            if (count($defaultCities)):

                foreach ($defaultCities as $defaultCity):

                    $parents = '';
                    if (isset($defaultCity['REGION_NAME']))
                        $parents .= ', ' . $defaultCity['REGION_NAME'];

                    if (isset($defaultCity['COUNTRY_NAME']))
                        $parents .= ', ' . $defaultCity['COUNTRY_NAME'];

                    ?><li data-id="<?=$defaultCity['ID']?>"><?=$defaultCity['NAME'];?><span style="color: #999"><?=$parents?></span><?
                    if (Location::getType() == Location::TYPE__SALE):
                        ?> <input type="hidden" value="<?=$defaultCity['ID']?>" name="TF_LOCATION_DEFAULT_CITIES[]"><i></i><?php
                    endif;

                    ?></li>
                <?php endforeach;

            else: ?>
                <li class="empty_location_el"><?=Loc::getMessage('tf-location__empty-list');?></li>
            <? endif;?>
            </td>
        </tr>
    <?php endforeach;

    if (Location::getType() == Location::TYPE__SALE):?>
    <tr class="">
        <td width="40%" class="adm-detail-content-cell-l" valign="top">
            <?=Loc::getMessage('TF_LOCATION_ADD_CITY');?>:
        </td>
        <td width="60%" class="adm-detail-content-cell-r" valign="top">
            <?
            $arLocationParams = array(
                "AJAX_CALL"             => "N",
                "COUNTRY_INPUT_NAME"    => "COUNTRY_tmp",
                "REGION_INPUT_NAME"     => "REGION_tmp",
                "CITY_INPUT_NAME"       => "tmp",
                "INPUT_NAME"            => 'tmp',
                "CITY_OUT_LOCATION"     => "Y",
                "LOCATION_VALUE"        => "",
                "ONCITYCHANGE"          => "setCity($('#tmp').val())",
            );
            $APPLICATION->IncludeComponent(
                "bitrix:sale.ajax.locations",
                ".default",
                $arLocationParams,
                null,
                array('HIDE_ICONS' => 'Y')
            );
            ?>
            <div class="tfl-help"><?=Loc::getMessage('TF_LOCATION_ADD_CITY_HELP')?></div>
            <script>
                function setCity(cityID) {
                    var ArLocationName = $('#tmp_val_div_' + cityID + '_NAME').text().split(','),
                        cityNAME = ArLocationName[0];

                    $('.empty_location_el').remove();
                    $('.tfl__cities').append('<li data-id="'+cityID+'">'+cityNAME+'<input type="hidden" value="'+cityID+'" name="TF_LOCATION_DEFAULT_CITIES[]"><i></i></li>');
                    $('#LOCATION_tmp select').not('#COUNTRY_tmptmp').remove();
                    $('#COUNTRY_tmptmp option').first().attr('selected', 'selected');
                }

                $(function() {
                    $(document).delegate('.tfl__cities li i', 'click', function() {
                        $(this).parent().remove();
                    });
                    /* $(document).on('click', '.select_place', function(){
                     var locationID = $('input[name="tmp"]').val();
                     if(locationID != ""){
                     var LocationObj = $('.bx-ui-combobox-variant-active');
                     count = LocationObj.length;
                     var LocationSelectedElement = LocationObj[count-1];
                     var LocationName = LocationSelectedElement.textContent;
                     $('.empty_location_el').remove();
                     $('.tfl__cities').append('<li data-id="'+locationID+'">'+LocationName+'<input type="hidden" value="'+locationID+'" name="TF_LOCATION_DEFAULT_CITIES[]"><i></i></li>');
                     $('#LOCATION_tmp select').not('#COUNTRY_tmptmp').remove();
                     $('#COUNTRY_tmptmp option').first().attr('selected', 'selected');
                     }
                     });*/
                });
            </script>
        </td>
    </tr>
<? elseif(Location::getType() == Location::TYPE__SALE_2): ?>
    <tr class="">
        <td width="40%" class="adm-detail-content-cell-l" valign="top">
        </td>
        <td width="60%" class="adm-detail-content-cell-r" valign="top">
            <?=Loc::getMessage('TF_LOCATION_DEFAULT_CITIES_S2')?>
            <div class="tfl-help"><?=Loc::getMessage('TF_LOCATION_ADD_CITY_HELP')?></div>

        </td>
<?php else: ?>
    <tr class="">
        <td width="40%" class="adm-detail-content-cell-l" valign="top"></td>
        <td width="60%" class="adm-detail-content-cell-r" valign="top">
            <?=Loc::getMessage('TF_LOCATION_DEFAULT_CITIES_INTERNAL')?>
            <div class="tfl-help"><?=Loc::getMessage('TF_LOCATION_ADD_CITY_HELP')?></div>
        </td>
    </tr>

<?php endif; ?>
<tr class="heading">
    <td colspan="2"><?=Loc::getMessage('TF_LOCATION_VISUAL_HEADING')?></td>
</tr>
<?
    Tabs::showTextRow(Options::LIST_PRE_LINK_TEXT);
    Tabs::showTextRow(Options::LIST_LINK_CLASS);

    Tabs::showSelectBoxRow(Options::LIST_FAVORITES_POSITION);

    Tabs::showTextRow(Options::LIST_DESKTOP_WIDTH);
    Tabs::showTextRow(Options::LIST_MOBILE_BREAKPOINT);

    Tabs::showTextRow(Options::LIST_DESKTOP_RADIUS);
?>
<tr>
    <td colspan="2">
        <table style="margin-top: 15px" class="adm-detail-content-table edit-table tfl__subsettings-table">
            <tr>
                <th style="text-align: right; font-size: 16px"><?=Loc::getMessage('tfl__list-padding')?></th>
                <td align="left" style="text-align: left; color: gray"><?=Loc::getMessage('tfl__list-mobile')?></td>
                <td align="left" style="text-align: left; color: gray"><?=Loc::getMessage('tfl__list-desktop')?></td>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                    <label><?=Loc::getMessage('tfl__list-list-padding') ?></label>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?Tabs::showTextInput(Options::LIST_DESKTOP_PADDING);?>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?Tabs::showTextInput(Options::LIST_MOBILE_PADDING);?>
                </td>
            </tr>
            <tr>
                <th style="text-align: right; font-size: 16px"><?=Loc::getMessage('tfl__list-font-size')?></th>
                <td align="left" style="text-align: left; color: gray"><?=Loc::getMessage('tfl__list-mobile')?></td>
                <td align="left" style="text-align: left; color: gray"><?=Loc::getMessage('tfl__list-desktop')?></td>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                    <label><?=Loc::getMessage('tfl__list-title-font-size') ?></label>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?Tabs::showTextInput(Options::LIST_MOBILE_TITLE_FONT_SIZE);?>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?Tabs::showTextInput(Options::LIST_DESKTOP_TITLE_FONT_SIZE);?>
                </td>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                    <label><?=Loc::getMessage('tfl__list-input-font-size') ?></label>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?Tabs::showTextInput(Options::LIST_MOBILE_INPUT_FONT_SIZE);?>
                </td>

                <td width="30%" class="adm-detail-content-cell-r">
                    <?Tabs::showTextInput(Options::LIST_DESKTOP_INPUT_FONT_SIZE);?>
                </td>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
                    <label><?=Loc::getMessage('tfl__list-items-font-size') ?></label>
                </td>
                <td width="30%" class="adm-detail-content-cell-r">
                    <?Tabs::showTextInput(Options::LIST_MOBILE_ITEMS_FONT_SIZE);?>
                </td>

                <td width="30%" class="adm-detail-content-cell-r">
                    <?Tabs::showTextInput(Options::LIST_DESKTOP_ITEMS_FONT_SIZE);?>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr class="heading">
    <td colspan="2"><?=Loc::getMessage('TF_LOCATION_STRINGS_HEADING')?></td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
        <label for="TF_LOCATION_LOCATION_POPUP_HEADER"><?=Loc::getMessage('TF_LOCATION_LOCATION_POPUP_HEADER') ?>:</label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input size="40" type="text" name="TF_LOCATION_LOCATION_POPUP_HEADER" id="TF_LOCATION_LOCATION_POPUP_HEADER" value="<?=$settings['TF_LOCATION_LOCATION_POPUP_HEADER']?>">
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
        <label for="TF_LOCATION_LOCATION_POPUP_PLACEHOLDER"><?=Loc::getMessage('TF_LOCATION_LOCATION_POPUP_PLACEHOLDER') ?>:</label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input size="40" type="text" name="TF_LOCATION_LOCATION_POPUP_PLACEHOLDER" id="TF_LOCATION_LOCATION_POPUP_PLACEHOLDER" value="<?=$settings['TF_LOCATION_LOCATION_POPUP_PLACEHOLDER']?>">
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l adm-detail-content-text">
        <label for="TF_LOCATION_LOCATION_POPUP_NO_FOUND"><?=Loc::getMessage('TF_LOCATION_LOCATION_POPUP_NO_FOUND') ?>:</label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input size="40" type="text" name="TF_LOCATION_LOCATION_POPUP_NO_FOUND" id="TF_LOCATION_LOCATION_POPUP_NO_FOUND" value="<?=$settings['TF_LOCATION_LOCATION_POPUP_NO_FOUND']?>">
    </td>
</tr>
<?	$tabControl->EndTab();