<a href="/lk/?favorite" class="favorites_link <?if($arResult['CNT_ITEMS']>0):?>has_items<?endif;?> <?= (strpos($APPLICATION->GetCurDir(), 'lk/') ? 'active' : '') ?> <?= ($USER->IsAuthorized() ? 'authorized' : '') ?>">
    <span class="count">
        <?if($arResult['CNT_ITEMS']>0):?><?=$arResult['CNT_ITEMS']?><?endif;?>
    </span>
</a>