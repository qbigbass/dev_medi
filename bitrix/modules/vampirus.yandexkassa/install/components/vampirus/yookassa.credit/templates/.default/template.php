<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->addExternalJS("https://static.yoomoney.ru/checkout-credit-ui/v1/index.js");
?>
<div class="vampirus_yookassa_credit_info_block"></div>
<script>
	var $vampirusCheckoutCreditUI = CheckoutCreditUI({shopId:<?=$arParams["SHOP_ID"]?>, sum:<?=$arParams["PRICE"]?>});
	var vampirusCheckoutCreditButton1 = $vampirusCheckoutCreditUI({
	    type: 'info',
	    domSelector: '.vampirus_yookassa_credit_info_block'
	});
	<?php if ($arParams["OB_NAME"]):?>
	var vampirusYookassaCreditBlock = document.getElementsByClassName('vampirus_yookassa_credit_info_block')[0];
	BX.addCustomEvent('onCatalogElementChangeOffer', function(event){
		if (!!window.<?=$arParams["OB_NAME"]?>)
		{
			vampirusYookassaCreditBlock.innerHTML = '';
			$vampirusCheckoutCreditUI = CheckoutCreditUI({shopId:<?=$arParams["SHOP_ID"]?>, sum:window.<?=$arParams["OB_NAME"]?>.currentPrices[0].RATIO_PRICE});
			vampirusCheckoutCreditButton1 = $vampirusCheckoutCreditUI({
			    type: 'info',
			    domSelector: '.vampirus_yookassa_credit_info_block'
			});
		}
	});
	<? endif;?>
</script>