<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;

$this->setFrameMode(true);
$this->createFrame()->begin();

if($arParams['ORDER_TEMPLATE'] == 'Y'): ?>    <span class="tfl__link-container">
        <a href="#tfLocationPopup"
            data-location-id="<?=$arResult['CITY_ID']?>"
            data-order="true"
            class="<?=$arResult['SETTINGS']['TF_LOCATION_ORDERLINK_CLASS']?> tfl__link tfl__link_order"
        ><?=$arResult['CITY_NAME']?></a>
        <input type="hidden" name="<?=$arParams['PARAMS']['INPUT_NAME']?>" class="tf_location_city_input" value="<?=$arResult['CITY_ID']?>">
        <input type="hidden" autocomplete="off" class="bx-ui-sls-route" style="padding: 0px; margin: 0px;" value="<?=$arResult['CITY_NAME']?>">
    </span>
<? else: ?>
    <span class="tfl__link-container">
        <span class="tfl_label"><? if(strlen($arResult['SETTINGS']['TF_LOCATION_HEADLINK_TEXT']) > 0 )
            echo $arResult['SETTINGS']['TF_LOCATION_HEADLINK_TEXT'], ': ';
        ?></span><a href="#tfLocationPopup"
           data-location-id="<?=$arResult['CITY_ID']?>"
           class="<?=$arResult['SETTINGS']['TF_LOCATION_HEADLINK_CLASS']?> tfl__link"
        ><?=$arResult['CITY_NAME']?></a>
    </span>
<?endif;

if ($GLOBALS['TF_LOCATION_TEMPLATE_LOADED'] == 'Y')
    return;

$GLOBALS['TF_LOCATION_TEMPLATE_LOADED'] = 'Y';

include_once 'style.php';

?><div class="tfl-popup-overlay" style="display:none;">
    <div class="tfl-popup">
        <? $title = !is_null($arResult['SETTINGS']['TF_LOCATION_LOCATION_POPUP_HEADER'])
            ? $arResult['SETTINGS']['TF_LOCATION_LOCATION_POPUP_HEADER']
            : GetMessage("TF_LOCATION_CHECK_CITY");

        if (strlen($title)): ?>
            <div class="tfl-popup__title-container">
                <div class="tfl-popup__title"><?=$title?></span></div>
            </div>
        <? endif; ?>
        <div class="tfl-popup__search-wrapper">
            <input
                    type="text"
                    autocomplete="off"
                    name="search"
                    placeholder="<?=!is_null($arResult['SETTINGS']['TF_LOCATION_LOCATION_POPUP_PLACEHOLDER'])
                        ? $arResult['SETTINGS']['TF_LOCATION_LOCATION_POPUP_PLACEHOLDER']
                        : GetMessage("TF_LOCATION_CHECK_CITY_PLACEHOLDER")?>"
                    class="tfl-popup__search-input">
            <a href="#" class="tfl-popup__clear-field">
                <span class="tfl-popup__close"></span>
            </a>
            <div class="tfl-popup__search-icon">
                <svg class="svg svg-search" width="17" height="17" viewBox="0 0 17 17" aria-hidden="true"><path class="cls-1" d="M16.709,16.719a1,1,0,0,1-1.412,0l-3.256-3.287A7.475,7.475,0,1,1,15,7.5a7.433,7.433,0,0,1-1.549,4.518l3.258,3.289A1,1,0,0,1,16.709,16.719ZM7.5,2A5.5,5.5,0,1,0,13,7.5,5.5,5.5,0,0,0,7.5,2Z"></path></svg>
            </div>
        </div>
        <div class="tfl-popup__lists-container">
            <div class="tfl-popup__container tfl-popup__defaults">
                <div class="tfl-popup__scroll-container">
                    <ul class="tfl-popup__list"></ul>
                </div>
            </div>
            <div class="tfl-popup__container tfl-popup__locations">
                <div class="tfl-popup__scroll-container">
                    <ul class="tfl-popup__list"></ul>
                </div>
                <div class="tfl-popup__nofound-mess"><?= $arResult['SETTINGS']['TF_LOCATION_LOCATION_POPUP_NO_FOUND']?></div>
            </div>
        </div>
        <div class="tfl-popup__close-container"><div class="tfl-popup__close"></div></div>
    </div>
</div>


<div class="tfl-define-popup" style="display:none;"><div class="tfl-define-popup__text"><?=$arResult['CONFIRM_POPUP_TEXT']?><div class="tfl-popup__close-container"><div class="tfl-popup__close" onclick="document.getElementById('parent_popup').style.display='none';"></div></div></div>
    <div class="tfl-define-popup__buttons" style="border-radius: 0 0 <?=intval($arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_RADIUS'])?>px <?=intval($arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_RADIUS'])?>px; padding: 0 2em 1.5em;">
        <? if (strlen($arResult['CITY_ID'])): ?>
			<a href="#" class="btn-simple btn-custom tfl-define-popup__yes" style="border-radius: 2px;" onclick="document.getElementById('parent_popup').style.display='none';"><?=Loc::getMessage('TF_LOCATION_YES')?></a>
            <a href="#" class="btn-simple btn-custom btn-border tfl-define-popup__list" style="border-radius: 2px;" onclick="document.getElementById('parent_popup').style.display='none';"><?=Loc::getMessage('TF_LOCATION_LIST')?></a>
        <? else: ?>
            <a href="#" class="tfl-define-popup__button tfl-define-popup__main tfl-define-popup__list"><?=Loc::getMessage('TF_LOCATION_LIST')?></a>
            <a href="#" class="tfl-define-popup__button tfl-define-popup__second tfl-define-popup__yes"><?=Loc::getMessage('TF_LOCATION_CLOSE')?></a>
        <? endif;?>
    </div>


</div>

<script>
    $(function()
    {
        var Location = new TfLocation(<?=$arResult['JS_PARAMS']?>, '<?=$arResult['JS_CALLBACK']?>');

        Location.initPopupLocationsHandler($('.tfl__link'));

        BX.addCustomEvent('onAjaxSuccess', function() {
            Location.initPopupLocationsHandler($('.tfl__link'));
        });

        <? if($arResult['CALL_CONFIRM_POPUP'] == 'Y'):?>
            Location.openConfirmPopup();
        <? endif;

        if($arResult['CALL_LOCATION_POPUP'] == 'Y'):?>
            Location.openLocationsPopup();
        <? endif;?>
    });
</script>
