<?
if ($_SESSION['MEDI_CITY'] != "Москва" && SITE_ID == 's1') {
    $phone = $GLOBALS['medi']['phones'][0];
} else {
    $phone = $GLOBALS['medi']['phones'][SITE_ID];
}
?>

<span class="menu_phone"><a href="tel:<?= $phone ?>" id="GTM_menu_phone"
                            onclick="ym(30121774, 'reachGoal', 'CLICK_PHONE'); _tmr.push({type: 'reachGoal', id: 3206755, goal: 'GOAL_CLICK-PHONE'});   return true;"><?= $phone ?></a></span>
<span class="callback callBack openWebFormModal" data-id="2">Заказать звонок</span>
