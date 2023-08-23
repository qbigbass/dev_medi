<div class="mainTool">
	<?if(!empty($arResult["PRICE"])):?>
		<?if($arResult["EXTRA_SETTINGS"]["COUNT_PRICES"] > 1):?>
			<a class="price changePrice getPricesWindow" data-id="<?=$arResult["ID"]?>">
				<?if(!empty($arResult["PRICE"]["DISCOUNT"])):?>
					<span class="priceBlock">
					<span class="oldPriceLabel"><s class="discount"><?=CCurrencyLang::CurrencyFormat($arResult["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true)?></s></span>
						<?if(!empty($arResult["PRICE"]["RESULT_PRICE"]["DISCOUNT"])):?>
							<span class="oldPriceLabel"><?=GetMessage("OLD_PRICE_DIFFERENCE_LABEL")?> <span class="economy"><?=CCurrencyLang::CurrencyFormat($arResult["PRICE"]["RESULT_PRICE"]["DISCOUNT"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true)?></span></span>
						<?endif;?>
					</span>
				<?endif;?>
				<span class="priceContainer"><span class="priceIcon"></span><span class="priceVal" data-price="<?=$arResult["PRICE"]["DISCOUNT_PRICE"]?>"><?=CCurrencyLang::CurrencyFormat($arResult["PRICE"]["DISCOUNT_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true)?></span></span>
				<?if($arParams["HIDE_MEASURES"] != "Y" && !empty($arResult["EXTRA_SETTINGS"]["MEASURES"][$arResult["CATALOG_MEASURE"]]["SYMBOL_RUS"])):?>
					<span class="measure"> / <?=$arResult["EXTRA_SETTINGS"]["MEASURES"][$arResult["CATALOG_MEASURE"]]["SYMBOL_RUS"]?></span>
				<?endif;?>
				<?if(!empty($arResult["PROPERTIES"]["BONUS"]["VALUE"])):?>
					<span class="purchaseBonus"><span class="theme-color">+ <?=$arResult["PROPERTIES"]["BONUS"]["VALUE"]?></span><?=$arResult["PROPERTIES"]["BONUS"]["NAME"]?></span>
				<?endif;?>
			</a>
		<?else:
			?>
			<a class="price changePrice">
				<?
				$prodid = $arResult['PRICE']['PRODUCT_ID'];
				if (!empty($arResult['MAX_PRICE'][$prodid]))
				{
					$max_price = $arResult['MAX_PRICE'][$prodid];
					$max_price_mindiff = $max_price - 100;
				}
				?>
				<?if(!empty($arResult["PRICE"]["DISCOUNT"])):?>
					<span class="priceBlock">
						<span class="oldPriceLabel"><s class="discount"><?=CCurrencyLang::CurrencyFormat($arResult["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true)?></s></span>
						<?if(!empty($arResult["PRICE"]["RESULT_PRICE"]["DISCOUNT"])):?>
							<span class="oldPriceLabel"><?=GetMessage("OLD_PRICE_DIFFERENCE_LABEL")?> <span class="economy"><?=CCurrencyLang::CurrencyFormat($arResult["PRICE"]["RESULT_PRICE"]["DISCOUNT"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true)?></span></span>
						<?endif;?>
					</span>
				<?elseif ($arResult['PRICE']['PRICE']['PRICE'] < $max_price_mindiff):
						$price_diff = $max_price - $arResult['PRICE']['PRICE']['PRICE']; ?>
					<span class="priceBlock">
						<span class="oldPriceLabel"><s class="discount"><?=CCurrencyLang::CurrencyFormat($max_price, $arResult["EXTRA_SETTINGS"]["CURRENCY"], true)?></s></span>
						<?if(!empty($price_diff)):?>
							<span class="oldPriceLabel"><?=GetMessage("OLD_PRICE_DIFFERENCE_LABEL")?> <span class="economy"><?=CCurrencyLang::CurrencyFormat($price_diff, $arResult["EXTRA_SETTINGS"]["CURRENCY"], true)?></span></span>
						<?endif;?>
					</span>
				<?endif;?>
				<span class="priceContainer">
					<span class="priceVal" data-price="<?=$arResult["PRICE"]["DISCOUNT_PRICE"]?>"><?=CCurrencyLang::CurrencyFormat($arResult["PRICE"]["DISCOUNT_PRICE"], $arResult["EXTRA_SETTINGS"]["CURRENCY"], true)?></span>
					<?if($arParams["HIDE_MEASURES"] != "Y" && !empty($arResult["EXTRA_SETTINGS"]["MEASURES"][$arResult["CATALOG_MEASURE"]]["SYMBOL_RUS"])):?>
						<span class="measure"> / <?=$arResult["EXTRA_SETTINGS"]["MEASURES"][$arResult["CATALOG_MEASURE"]]["SYMBOL_RUS"]?></span>
					<?endif;?>
				</span>
				<?if(!empty($arResult["PROPERTIES"]["BONUS"]["VALUE"])):?>
					<span class="purchaseBonus"><span class="theme-color">+ <?=$arResult["PROPERTIES"]["BONUS"]["VALUE"]?></span><?=$arResult["PROPERTIES"]["BONUS"]["NAME"]?></span>
				<?endif;?>
			</a>
		<?endif;?>
	<?else:?>
		<a class="price changePrice"><?=GetMessage("REQUEST_PRICE_LABEL")?></a>
	<?endif;?>
	<?if ($arResult['ACTION_BLOCK']):?>
	<div class="columnRowWrap">
		<? // Вывод акций через метки "Признак акции" в карточке

		if (is_array($arResult['ACTION_BLOCK'])){?>
			<div class="bindAction">
				<div class="ff-medium row">Товар участвует в акци<?=(count($arResult['ACTION_BLOCK'])>1 ? 'ях' : 'и')?>:</div>
				<?foreach($arResult['ACTION_BLOCK'] AS $sact){
					$hide_link = $sact['PROPERTY_HIDE_VALUE'] == 'Да' ? 'Y' : 'N';?>
					<div class="tb row">
						<div class="tc bindActionImage"><?if($hide_link == 'N'){?><a href="<?=$sact['DETAIL_PAGE_URL']?>" target="_blank"><span class="image" ></span></a><?}else{?><span class="image" ></span></a><?}?></div>
						<div class="tc bindActionTitle"><?if($hide_link == 'N'){?><a href="<?=$sact['DETAIL_PAGE_URL']?>" target="_blank" class="theme-color ff-medium"><?=$sact['NAME']?></a><?}else{?><span class="image" ><span class="theme-color ff-medium" title="<?=$sact['PREVIEW_TEXT']?>"><?=$sact['NAME']?></span></span></a><?}?></div><br>
					</div>
				<?}?>
			</div>
			<?
		}
		// Вывод акции через привязку товаров к акции
		else {
			echo $arResult['ACTION_BLOCK'];
		}
		?>
	</div>
	<?endif;?>
	<div class="columnRowWrap">
		<div class="row columnRow">
			<?if(!empty($arResult["PRICE"])):?>
				<?// Нет в наличии ?>
				<?if($arResult["CATALOG_AVAILABLE"] != "Y" && $arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON'] !== true ):?>
					<?//if($arResult["CATALOG_SUBSCRIBE"] == "Y"  && !$arResult['DISPLAY_BUTTONS']['CART_BUTTON']):?>
						<?/*<a href="#" class="addCart subscribe changeID changeQty changeCart" data-id="<?=$arResult["ID"]?>" data-quantity="<?=$arResult["EXTRA_SETTINGS"]["BASKET_STEP"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/subscribe.png" alt="<?=GetMessage("SUBSCRIBE_LABEL")?>" class="icon"><?=GetMessage("SUBSCRIBE_LABEL")?></a>*/
						if ($arResult['MKT_CAT'] != 'Y' && $arResult['SHOES_CAT'] != 'Y'){?>
						<?/*<a href="#" class="fastBack label changeID changeCart" data-id="<?=$arResult["ID"]?>" <?if ($arResult['SALON_COUNT'] > '0'  || $arResult['DISPLAY_BUTTONS']['CART_BUTTON'] !== true):?>style="display:none;"<?endif;?>><img src="<?=SITE_TEMPLATE_PATH?>/images/incart.png" alt="<?=GetMessage("ORDER_NOTAVAIL_LABEL_ALT")?>" class="icon" ><?=GetMessage("ORDER_NOTAVAIL_LABEL")?></a>*/?>
                            <?/*if ($arResult['SALON_COUNT'] <= '0'){?>нет в наличии<?}*/?>
						<?}else{?>
							<a href="#" class=" label changeID changeCart disabled" data-id="<?=$arResult["ID"]?>" <?if ($arResult['SALON_COUNT'] == '0'  || $arResult['DISPLAY_BUTTONS']['CART_BUTTON'] !== true):?>style="display:none;"<?endif;?>>нет в наличии</a>
						<?}?>

					<?/*else:?>
						<a href="#" class="addCart changeID changeQty changeCart disabled" data-id="<?=$arResult["ID"]?>" data-quantity="<?=$arResult["EXTRA_SETTINGS"]["BASKET_STEP"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/incart.png" alt="<?=GetMessage("ADDCART_LABEL")?>" class="icon"><?=GetMessage("ADDCART_LABEL")?></a>
					<?endif;*/?>

				<?// В наличии?>
				<?else:?>
					<?// показ кнопки В корзину?>
					<?if ($arResult['DISPLAY_BUTTONS']['CART_BUTTON'] == true ):?>
					<a href="#" class="addCart changeID changeQty changeCart" data-id="<?=$arResult["ID"]?>" data-quantity="<?=$arResult["EXTRA_SETTINGS"]["BASKET_STEP"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/incart.png" alt="<?=GetMessage("ADDCART_LABEL")?>" class="icon"  ><?=GetMessage("ADDCART_LABEL")?></a>

					<?
						//  заказная позиция и в наличии нет.
					else:?>
						<?if ($arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON'] !== true){?>
							<a href="#" class="fastBack label changeID changeCart" data-id="<?=$arResult["ID"]?>" <?if($arResult['SALON_COUNT'] == '0' || $arResult['DISPLAY_BUTTONS']['CART_BUTTON'] !== true){?>style="display:none;"<?}?>><img src="<?=SITE_TEMPLATE_PATH?>/images/incart.png" alt="<?=GetMessage("ORDER_NOTAVAIL_LABEL_ALT")?>" class="icon" ><?=GetMessage("ORDER_NOTAVAIL_LABEL")?></a>
						<?}?>
					<?endif;?>

				<?endif;?>
			<?// запрос цены?>
			<?/*else:?>
				<a href="#" class="addCart changeID changeQty changeCart disabled requestPrice" data-id="<?=$arResult["ID"]?>" data-quantity="<?=$arResult["EXTRA_SETTINGS"]["BASKET_STEP"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/request.png" alt="<?=GetMessage("REQUEST_PRICE_BUTTON_LABEL")?>" class="icon"><?=GetMessage("REQUEST_PRICE_BUTTON_LABEL")?></a>
			<?*/endif;?>
		</div>


		<? //NOTE купить в 1 клик
		?>

		<?if ($arResult['DISPLAY_BUTTONS']['CART_BUTTON'] ):?>
		<div class="row columnRow">
			<a href="#" class="fastOrder label changeID<?if(empty($arResult["PRICE"]) || $arResult["CATALOG_AVAILABLE"] != "Y"):?> disabled<?endif;?>" data-id="<?=$arResult["ID"]?>" id="GTM_fastorder_card_get"><?=GetMessage("FASTORDER_LABEL")?></a>
		</div>
		<?endif;?>
	</div>
</div>
<div class="secondTool">
	<? //NOTE: CART BUTOON
	?>
	<?/*if ($arResult['DISPLAY_BUTTONS']['CART_BUTTON'] ):?>
	<div class="qtyBlock row" <?if($arResult["CATALOG_AVAILABLE"] != "Y" && $arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON'] !== true):?>style="display:none"<?endif;?>>
		<img src="<?=SITE_TEMPLATE_PATH?>/images/qty.pn<?if($arResult["CATALOG_AVAILABLE"] != "Y" && $arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON'] !== true):?><?endif;?>g" alt="" class="icon">
		<label class="label"><?=GetMessage("QUANTITY_LABEL")?> </label> <a href="#" class="minus"></a><input type="text" class="qty"<?if(!empty($arResult["PRICE"]["EXTENDED_PRICES"])):?> data-extended-price='<?=\Bitrix\Main\Web\Json::encode($arResult["PRICE"]["EXTENDED_PRICES"])?>'<?endif;?> value="<?=$arResult["EXTRA_SETTINGS"]["BASKET_STEP"]?>" data-step="<?=$arResult["EXTRA_SETTINGS"]["BASKET_STEP"]?>" data-max-quantity="<?=$arResult["CATALOG_QUANTITY"]?>" data-enable-trace="<?=(($arResult["CATALOG_QUANTITY_TRACE"] == "Y" && $arResult["CATALOG_CAN_BUY_ZERO"] == "N") ? "Y" : "N")?>"><a href="#" class="plus"></a>
	</div>
	<?endif;*/?>
	<?// Сканирование стоп, основная кнопка?>
	<?if ($arResult['DISPLAY_BUTTONS']['INSOLE_BUTTON']):?>
	<div class="row columnRow">
	<a href="/services/izgotovlenie-ortopedicheskikh-stelek/#order" class="magentaBigButton scan " >Запись на изготовление</a>
	</div>
	<?endif;?>
	<?use Bitrix\Main\Grid\Declension;
	// Забронировать, основная кнопка?>

	<div class="columnRowWrap">
		<div class="columnRow ">
			<div class="availability_block">
			<?if ($arResult['DISPLAY_BUTTONS']['RESERV_BUTTON'] && $arResult['SALON_COUNT'] > 0 && $arResult['DONT_SHOW_REST'] == false):

			$sDeclension = new Declension('салоне', 'салонах', 'салонах');
			$avail_salons = $sDeclension->get($arResult['SALON_COUNT']);?>

				<a href="#stores" class="available_link link" style="">В наличии в <?=$arResult['SALON_COUNT'].'&nbsp;'.$avail_salons?></a>

			<?endif;?>
			</div>
			<?if($arResult['DISPLAY_BUTTONS']['RESERV_BUTTON']):?>
			<a href="#" class="greyBigButton reserve changeID get_medi_popup_Window" data-src="/ajax/catalog/?action=reserve" data-title="Забронировать в салоне" data-id="<?=$arResult["ID"]?>" <?if($arResult['SALON_AVAILABLE'] == "0" ||  $arResult['SALON_COUNT'] == "0"   || ($arResult['mainStoreAmount'] > '0' && $arResult['DISPLAY_BUTTONS']['CART_BUTTON'])){?>style="display:none;"<?}?> data-action="reserve">Забронировать</a>
			<?endif;?>
		</div>
	</div>

	<div class="row">
		<?/*if($arResult["CATALOG_QUANTITY"] > 0):?>
			<?if(!empty($arResult["EXTRA_SETTINGS"]["STORES"]) && $arResult["EXTRA_SETTINGS"]["STORES_MAX_QUANTITY"] > 0):?>
				<a href="#" data-id="<?=$arResult["ID"]?>" class="inStock label eChangeAvailable getStoresWindow"><img src="<?=SITE_TEMPLATE_PATH?>/images/inStock.png" alt="<?=GetMessage("AVAILABLE")?>" class="icon"><span><?=GetMessage("AVAILABLE")?></span></a>
			<?else:?>
				<span class="inStock label eChangeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/inStock.png" alt="<?=GetMessage("AVAILABLE")?>" class="icon"><span><?=GetMessage("AVAILABLE")?></span></span>
			<?endif;?>
		<?else:?>
			<?if($arResult["CATALOG_AVAILABLE"] == "Y"):?>
				<a class="onOrder label eChangeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/onOrder.png" alt="<?=GetMessage("ON_ORDER")?>" class="icon"><?=GetMessage("ON_ORDER")?></a>
			<?else:?>
				<a class="outOfStock label eChangeAvailable"><img src="<?=SITE_TEMPLATE_PATH?>/images/outOfStock.png" alt="<?=GetMessage("CATALOG_NO_AVAILABLE")?>" class="icon"><?=GetMessage("CATALOG_NO_AVAILABLE")?></a>
			<?endif;?>
		<?endif;*/?>
	</div>
</div>
