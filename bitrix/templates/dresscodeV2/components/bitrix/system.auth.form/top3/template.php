<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);
global $USER;
$link = 'lk';
if ($USER->IsAuthorized() && !empty(array_intersect([20], $USER->GetUserGroupArray())))
{
	?>
	<?if($arResult["FORM_TYPE"] == "login"):?>
		<a href="<?=SITE_DIR?>salons/auth/?backurl=<?=$APPLICATION->GetCurPageParam();?>" class="topAuthIcon"></a>
	<?else:?>
	<a href="#" class="topAuthIcon topAuthMenu"></a>
	<div id="topAuth">
		<ul>
            <li class="top-auth-personal head"><a href="<?=SITE_DIR?>salons/profile/"><?=$USER->GetFirstName();?> <?=$USER->GetLastName();?></a></li>
			<li class="top-auth-personal"><a href="<?=SITE_DIR?>salons/profile/"><?=GetMessage("PERSONAL")?></a></li>
			<li class="top-auth-personal"><a href="<?=SITE_DIR?>salons/orders/"><?=GetMessage("MYORDERS")?></a></li>
			<li class="top-auth-personal"><a href="<?=SITE_DIR?>exit/"><?=GetMessage("EXIT")?></a></li>
		</ul>
	</div>
	<?endif?>
	<?
}
elseif ($USER->IsAuthorized() && !empty(array_intersect([29], $USER->GetUserGroupArray())))
{
    ?>
    <?if($arResult["FORM_TYPE"] == "login"):?>
    <a href="<?=SITE_DIR?>smp/auth/?backurl=<?=$APPLICATION->GetCurPageParam();?>" class="topAuthIcon"></a>
<?else:?>
    <a href="#" class="topAuthIcon topAuthMenu"></a>
    <div id="topAuth">
        <ul>
            <li class="top-auth-personal head"><a href="<?=SITE_DIR?>smp/profile/"><?=$USER->GetFirstName();?> <?=$USER->GetLastName();?></a></li>
            <li class="top-auth-personal"><a href="<?=SITE_DIR?>smp/profile/"><?=GetMessage("PERSONAL")?></a></li>
            <li class="top-auth-personal"><a href="<?=SITE_DIR?>smp/order/"><?=GetMessage("MYORDERS")?></a></li>
            <li class="top-auth-personal"><a href="<?=SITE_DIR?>exit/"><?=GetMessage("EXIT")?></a></li>
        </ul>
    </div>
<?endif?>
    <?
}
else{


?>

<?if($arResult["FORM_TYPE"] == "login"):?>
	<a href="<?=SITE_DIR?>lk/?backurl=<?=$APPLICATION->GetCurPageParam();?>" class="topAuthIcon"></a>
<?else:?>
<a href="#" class="topAuthIcon topAuthMenu"></a>
<div id="topAuth">
	<ul>
        <li class="top-auth-personal head"><a href="<?=SITE_DIR?>lk/"><?=$USER->GetFirstName();?> <?=$USER->GetLastName();?></a></li>
		<li class="top-auth-personal"><a href="<?=SITE_DIR?>lk/"><?=GetMessage("PERSONAL")?></a></li>
		<li class="top-auth-personal"><a href="<?=SITE_DIR?>lk/?orders">Статус заказа</a></li>
		<li class="top-auth-personal"><a href="<?=SITE_DIR?>lk/?history">История покупок</a></li>
		<li class="top-auth-exit"><a href="<?=SITE_DIR?>exit/"><?=GetMessage("EXIT")?></a></li>
	</ul>
</div>
<?endif?>

<?}
