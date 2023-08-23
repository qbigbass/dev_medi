<?
	use Bitrix\Main\Localization\Loc;
	\Bitrix\Main\Page\Asset::getInstance()->addCss("/bitrix/themes/.default/sale.css");
	Loc::loadMessages(__FILE__);

	$sum = roundEx($params['SUM'], 2);
?>

<div class="sale-paysystem-wrapper">
	<span class="tablebodytext">
		<?=Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_YANDEX_CHECKOUT_DESCRIPTION')." ".SaleFormatCurrency($params['SUM'], $params['CURRENCY']);?>
	</span>
	<div class="sale-paysystem-yandex-button-container">
		<span class="sale-paysystem-yandex-button">
			<? if ($params['savedCard']) :?>
				<form action="" method="post" style="display:inline-block">
					<input name="payment_method_id" value="<?=$params['savedCard']['id']?>" type="hidden">
					<button class="btn btn-primary btn-lg sale-paysystem-yandex-button-item" name="BuyButton" type="submit"><img src="data:image/svg+xml;base64,<?=$params['savedCard']['image']?>" alt="<?=$params['savedCard']['extra']['payment_method']['card']['card_type']?>"> <?=$params['savedCard']['extra']['payment_method']['card']['first6']?>*****<?=$params['savedCard']['extra']['payment_method']['card']['last4']?></button>
				</form>
				</a>&nbsp;
				<a class="btn btn-primary btn-lg sale-paysystem-yandex-button-item" href="<?=$params['URL'];?>">
					<?=Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_YANDEX_CHECKOUT_BUTTON_PAID_NEW')?>
				</a>
			<? else:?>
			<a class="btn btn-primary btn-lg sale-paysystem-yandex-button-item" href="<?=$params['URL'];?>">
				<?=Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_YANDEX_CHECKOUT_BUTTON_PAID')?>
			</a>
			<? endif;?>
		</span>
	</div>

</div>