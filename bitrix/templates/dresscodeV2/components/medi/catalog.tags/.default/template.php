<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?><? $this->setFrameMode(true);
?>
<? if (!empty($arResult["TAGS"])):
    ?>
    <? /*$index = 1;*/
    ?>
    <div id="catalogTags">
    <div class="catalogTagItemsWrap">
        <ul class="catalogTagItems <? if ($arParams["HIDE_TAGS_ON_MOBILE"] == "Y"): ?> mobileHidden<? endif; ?>">
            <? foreach ($arResult["TAGS"] as $tagIndex => $nextTag):
                if (empty($nextTag["NAME"])) continue; ?>
                <li class="catalogTagItem<? if ($arParams["MAX_VISIBLE_TAGS_DESKTOP"] < $index): ?> desktopHidden<? endif; ?><? /*if($arParams["MAX_VISIBLE_TAGS_MOBILE"] < $index):?> mobileHidden<?endif;*/
                ?>">
                    <a href="<?= $nextTag["LINK"] ?>"
                       class="catalogTagLink<? if (!empty($nextTag["SELECTED"]) && $nextTag["SELECTED"] == "Y"): ?> selected<? endif; ?>"><?= $nextTag["NAME"] ?><? if (!empty($nextTag["SELECTED"]) && $nextTag["SELECTED"] == "Y"): ?>
                            <span class="reset">&#10006;</span><? endif; ?></a>
                </li>
                <? $index++; ?>
            <? endforeach; ?>
            <? if (count($arResult["TAGS"]) > $arParams["MAX_VISIBLE_TAGS_MOBILE"] || count($arResult["TAGS"]) > $arParams["MAX_VISIBLE_TAGS_DESKTOP"]): ?>
                <li class="catalogTagItem moreButton<? if ($arParams["MAX_VISIBLE_TAGS_DESKTOP"] > count($arResult["TAGS"])): ?> desktopHidden<? endif; ?><? if ($arParams["MAX_VISIBLE_TAGS_MOBILE"] > count($arResult["TAGS"])): ?> mobileHidden<? endif; ?>">
                    <a href="#" class="catalogTagLink moreButtonLink"
                       data-last-label="<?= GetMessage("CATALOG_TAGS_MORE_BUTTON_HIDE"); ?>"><?= GetMessage("CATALOG_TAGS_MORE_BUTTON") ?></a>
                </li>
            <? endif; ?>
        </ul>
    </div>
    <? /*		<a href="#" class="mainSalesBtnLeft btnLeft"></a>
<a href="#" class="mainSalesBtnRight btnRight"></a> */
    ?>
    </div><? endif; ?>