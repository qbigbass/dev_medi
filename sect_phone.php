<?
/*if ($_SESSION['MEDI_CITY'] != "Москва" && SITE_ID == 's1')
{
	$phone = $GLOBALS['medi']['phones'][0];
	$sphone = str_replace([' ', '-'], '', $phone);
}
else {*/
$phone = $GLOBALS['medi']['phones'][SITE_ID];
$sphone = str_replace([' ', '-'], '', $phone);
/*}*/
?>

<span class="heading"><a href="tel:<?= $sphone ?>" class="top_phone" id="GTM_top_phone"
                         onclick="ym(30121774, 'reachGoal', 'CLICK_PHONE'); _tmr.push({type: 'reachGoal', id: 3206755, goal: 'GOAL_CLICK-PHONE'});  return true;"><?= $phone ?></a></span>
<br>
