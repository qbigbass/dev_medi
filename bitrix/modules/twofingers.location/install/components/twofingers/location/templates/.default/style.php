<?php

use TwoFingers\Location\Options;

?>
<style>
    .tfl-popup{
        border-radius:<?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_RADIUS])?>px;
        width: <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_WIDTH])?>px;
        padding: <?=intval($arResult['SETTINGS'][Options::LIST_DESKTOP_PADDING])?>px;
    }
    .tfl-define-popup{
        border-radius:<?=intval($arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_RADIUS'])?>px;
    }
    .tfl-define-popup__main{
        color: <?=$arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR']?>;
        background-color: <?=$arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG']?>;
    }
    .tfl-define-popup__main:hover{
        color: <?=['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_COLOR_HOVER']?>;
        background-color: <?=$arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_PRIMARY_BG_HOVER']?>;
    }

    .tfl-define-popup__second{
        color: <?=$arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR']?>;
        background-color: <?=$arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG']?>;
    }
    .tfl-define-popup__second:hover{
        color: <?=$arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_COLOR_HOVER']?>;
        background-color: <?=$arResult['SETTINGS']['TF_LOCATION_CONFIRM_POPUP_SECONDARY_BG_HOVER']?>;
    }

    .tfl-popup__title{
        font-size: <?=$arResult['SETTINGS'][Options::LIST_DESKTOP_TITLE_FONT_SIZE]?>px;
    }

    .tfl-popup .tfl-popup__search-input{
        font-size: <?=$arResult['SETTINGS'][Options::LIST_DESKTOP_INPUT_FONT_SIZE]?>px;
    }

    .tfl-popup__location-link{
        font-size: <?=$arResult['SETTINGS'][Options::LIST_DESKTOP_ITEMS_FONT_SIZE]?>px;
    }

    @media screen and (max-width: <?=$arResult['SETTINGS'][Options::LIST_MOBILE_BREAKPOINT]?>px)
    {
        .tfl-popup {
            width: 100%;
            height: 100%;
            top: 50%;
            border-radius: 0;
            z-index: 9999999;
           /* grid-template-rows: auto auto minmax(50%, max-content);*/
            grid-template-rows: auto auto minmax(50%, 1fr);
            padding: <?=intval($arResult['SETTINGS'][Options::LIST_MOBILE_PADDING])?>px;
        }

        .tfl-popup.tfl-popup_loading {
            height: 100%;
        }
        .tfl-popup__container {
            height: 100%;
        }

        .tfl-popup__with-locations.tfl-popup__with-defaults .tfl-popup__container{
            grid-template-columns: 1fr;
            grid-template-rows: auto 1fr;
        }

        .tfl-popup__with-defaults .tfl-popup__defaults{
            margin-bottom: 1rem;
            height: auto;
        }

        .tfl-popup .tfl-popup__search-input {
            max-width: none;
            width: 100%;
        }

        .tfl-popup__list {
            width: 100%;
        }

        .tfl-popup__title{
            font-size: <?=$arResult['SETTINGS'][Options::LIST_MOBILE_TITLE_FONT_SIZE]?>px;
        }

        .tfl-popup .tfl-popup__search-input{
            font-size: <?=$arResult['SETTINGS'][Options::LIST_MOBILE_INPUT_FONT_SIZE]?>px;
        }


        .tfl-popup__location-link{
            font-size: <?=$arResult['SETTINGS'][Options::LIST_MOBILE_ITEMS_FONT_SIZE]?>px;
        }
    }
</style>