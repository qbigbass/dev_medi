<?
if ($_SESSION["USER_GEO_POSITION"]['city'] != "Москва" && SITE_ID == 's1')
{
	$phone = $GLOBALS['medi']['phones'][0];
}
else {
	$phone = $GLOBALS['medi']['phones'][SITE_ID];
}
?>

<div class="global-information-block-cn">
	<div class="global-information-block-hide-scroll">
		<div class="global-information-block-hide-scroll-cn">
			<div class="information-heading">Остались вопросы?</div>
			<div class="information-text">Свяжитесь с нами удобным Вам способом</div>
			<div class="information-list">
				<div class="information-list-item">
					<div class="tb">
						<div class="information-item-icon tc">
							<img src="<?=SITE_TEMPLATE_PATH?>/images/cont1.png">
						</div>
						<div class="tc">
							<a href="tel:<?=$phone?>" class="side_phone" id="side_phone"  onclick="ym(30121774, 'reachGoal', 'CLICK_PHONE'); return true;"><?=$phone?></a><br>
						</div>
					</div>
				</div>
				<div class="information-list-item">
					<div class="tb">
						<div class="information-item-icon tc">
							<img src="<?=SITE_TEMPLATE_PATH?>/images/cont2.png">
						</div>
						<div class="tc">
							<a href="mailto:info@mediexp.ru">info@mediexp.ru</a><br>
						</div>
					</div>
				</div>
				<div class="information-list-item">
					<div class="tb">
						<div class="information-item-icon tc">
							<img src="<?=SITE_TEMPLATE_PATH?>/images/cont4.png">
						</div>
						<div class="tc">
							Ежедневно : с 8:00 до 21:00<br>
						</div>
					</div>
				</div>
			</div>
			<div class="information-feedback-container">
				<a href="<?=SITE_DIR?>service/" class="information-feedback">Обратная связь</a>
			</div>
		</div>
	</div>
</div>
