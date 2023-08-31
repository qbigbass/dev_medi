<!-- Инфо справа в шапке Начало-->
<div class="flex-info">
    <div class="inf"><?= ConvertDateTime($arResult["DATE_CREATE"], "DD.MM.YYYY", "ru")?></div>
    <? if ($arResult["PROPERTIES"]["READING_TIME"]["VALUE"] !== ""):?>
        <div class="inf"><img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/time.svg"><div>Время чтения <?=$arResult["PROPERTIES"]["READING_TIME"]["VALUE"]?> мин</div></div>
    <?endif;?>
    <div class="inf"><img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/view.svg"><div><?=$arResult["SHOW_COUNTER"]?></div></div>
    <div class="inf"><img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/like.svg"><div><?=$arResult["PROPERTIES"]["LIKES_CNT"]["VALUE"]?></div></div>
</div>
<!-- Инфо справа в шапке Конец-->
