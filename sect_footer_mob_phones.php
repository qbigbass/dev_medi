<?
if ($_SESSION["USER_GEO_POSITION"]['city'] != "Москва" && SITE_ID == 's1') {
    $phone = $GLOBALS['medi']['phones'][0];
    $sphone = str_replace([' ', '-'], '', $phone);
} else {
    $phone = $GLOBALS['medi']['phones'][SITE_ID];
    
    $sphone = str_replace([' ', '-'], '', $phone);
}
?>

<div class="logo">
    <a href="/" rel="nofollow"><img src="/bitrix/templates/dresscodeV2/images/logo.png?v=1586778894?v=1586778894"></a>
</div>
<div class="shedule">
    <div class="telephone ff-medium"><a href="tel:<?= $sphone ?>" class="footer_phone" id="GTM_footer_phone"
                                        onclick="ym(30121774, 'reachGoal', 'CLICK_PHONE'); _tmr.push({type: 'reachGoal', id: 3206755, goal: 'GOAL_CLICK-PHONE'});  return true;"><?= $phone ?></a>
    </div>
    <!--noindex-->

    <ul class="list">
        <li>Ежедневно с 8:00 до 21:00</li>
        <li>Без перерывов и выходных.</li>

    </ul>
    <div class="mail ff-medium marg_5">
        <? if (SITE_ID == 's2') { ?>
            <a href='mai&#108;to&#58;c%61llc&#101;nt&#101;%72-s&#112;b&#64;mediex&#112;%2E&#37;&#55;2u'
               class="medi-color">&#99;&#97;&#108;lc&#101;nt&#101;&#114;&#45;&#115;&#112;b&#64;med&#105;exp&#46;ru</a>
        <? } else { ?>
            <a href='mailt&#111;&#58;&#37;6&#57;n&#37;66&#111;&#64;m&#101;&#37;64i&#37;&#54;5x&#112;&#46;r&#117;'
               class="medi-color">&#105;n&#102;o&#64;&#109;&#101;diex&#112;&#46;ru</a>
        <? } ?></div>
</div>

