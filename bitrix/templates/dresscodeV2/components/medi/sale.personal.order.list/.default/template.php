<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
 
<?if(!empty($arResult['ERRORS']['FATAL'])):?>
	<?foreach($arResult['ERRORS']['FATAL'] as $error):?>
		<?=ShowError($error)?>
	<?endforeach?>
<?else:?>
	<?if(!empty($arResult['ERRORS']['NONFATAL'])):?>
		<?foreach($arResult['ERRORS']['NONFATAL'] as $error):?>
			<?=ShowError($error)?>
		<?endforeach?>
	<?endif?>

	<?if(!empty($arResult['ORDERS'])):?>
		<div class="table-simple-wrap">
			<table class="table-simple">
				<thead>
				<tr>
				<th>№ заказа</th>
				<th><?=GetMessage('SPOL_DATE')?></th>
				<th><?=GetMessage('SPOL_NUMBERS')?></th>
				<th><?=GetMessage('SPOL_PAY_SUM')?></th>
				<th><?=GetMessage('SPOL_STATUS')?></th>
				<th><?=GetMessage('SPOL_BUTTON')?></th>
				<th><?=GetMessage('SPOL_BUTTON2')?></th>
				</tr>
				</thead>

				<?foreach($arResult["ORDERS"] as $k => $order):?>
					<tr>
						<td><?=GetMessage('SPOL_ORDER')?> <?=GetMessage('SPOL_NUM_SIGN')?><?=$order["ORDER"]["ACCOUNT_NUMBER"]?></td>
						<td><?=$order["ORDER"]["DATE_INSERT_FORMATED"];?></td>
						<td><?=count($order["BASKET_ITEMS"])." шт."?></td>
						<td><?=$order["ORDER"]["FORMATED_PRICE"]?></td>
						<td><div><?=($order["ORDER"]["CANCELED"]=='Y')? GetMessage('SPOL_CANCELED') : $arResult["INFO"]["STATUS"][$order["ORDER"]["STATUS_ID"]]["NAME"]?></div>
						<td><a href="<?=htmlspecialcharsbx($order["ORDER"]["URL_TO_DETAIL"])?>" class="btn-simple btn-micro"><?=GetMessage('SPOL_BUTTON')?></a></td>
						<td><a href="<?=htmlspecialcharsbx($order["ORDER"]["URL_TO_COPY"])?>" class="btn-simple btn-micro"><?=GetMessage('SPOL_BUTTON2')?></a></td>
					</tr>
				<?endforeach?>
			</table>
		</div>
		<?if(strlen($arResult['NAV_STRING'])):?>
			<div class="navig">
			<?=$arResult['NAV_STRING']?>
			</div>
		<?endif?>
	<?else:?>
		<?=GetMessage('SPOL_NO_ORDERS')?>
	<?endif?>
<?endif?>
