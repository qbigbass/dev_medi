<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) { die(); }

use Bitrix\Main\Web\Json;
use Bitrix\Main\Localization\Loc;
use Yandex\Market;
use Yandex\Market\Trading\Entity as TradingEntity;

if (empty($arResult['BASKET']['ITEMS'])) { return; }

Market\Ui\Assets::loadPlugins([
	'OrderView.Basket',
	'OrderView.BasketItem',
]);

if (isset($arResult['BASKET']['COLUMNS']['CIS']))
{
	Market\Ui\Assets::loadPlugins([
		'OrderView.BasketItemCisSummary',
		'OrderView.BasketItemCis',
	]);
}

if (isset($arResult['BASKET']['COLUMNS']['DIGITAL']))
{
	Market\Ui\Assets::loadPlugins([
		'OrderView.BasketItemDigitalSummary',
		'OrderView.BasketItemDigital',
	]);
}

Market\Ui\Assets::loadMessages([
	'T_TRADING_ORDER_VIEW_BASKET_ITEM_CIS_MODAL_TITLE',
	'T_TRADING_ORDER_VIEW_BASKET_ITEM_CIS_REQUIRED',
	'T_TRADING_ORDER_VIEW_BASKET_ITEM_CIS_SUMMARY_EMPTY',
	'T_TRADING_ORDER_VIEW_BASKET_ITEM_CIS_SUMMARY_WAIT',
	'T_TRADING_ORDER_VIEW_BASKET_ITEM_CIS_SUMMARY_READY',
	'T_TRADING_ORDER_VIEW_BASKET_ITEM_DIGITAL_REQUIRED',
	'T_TRADING_ORDER_VIEW_BASKET_ITEM_DIGITAL_SUMMARY_WAIT',
	'T_TRADING_ORDER_VIEW_BASKET_ITEM_DIGITAL_SUMMARY_READY',
]);

$allowItemsEdit = isset($arResult['ORDER_ACTIONS'][TradingEntity\Operation\Order::ITEM]);
$allowCisEdit = isset($arResult['ORDER_ACTIONS'][TradingEntity\Operation\Order::CIS]);
$columns = $arResult['BASKET']['COLUMNS'];
$columnsCount = count($arResult['BASKET']['COLUMNS']) + 1;
$baseInputName = 'YAMARKET_ORDER[BASKET]';

if ($allowItemsEdit)
{
	$columns['DELETE'] = Loc::getMessage('YANDEX_MARKET_T_TRADING_ORDER_VIEW_BASKET_ITEM_ACTION_DELETE');
	++$columnsCount;
}

if ($allowItemsEdit && !empty($arResult['ITEMS_CHANGE_REASON']))
{
	Market\Ui\Assets::loadMessages([
		'T_TRADING_ORDER_VIEW_BASKET_CONFIRM_MODAL_TITLE',
		'T_TRADING_ORDER_VIEW_BASKET_CONFIRM_ITEM_CHANGE',
	]);

	Market\Ui\Assets::loadPlugins([
		'OrderView.BasketConfirmSummary',
		'OrderView.BasketConfirmForm',
	]);

	?>
	<div class="js-yamarket-order__field" data-plugin="OrderView.BasketConfirmSummary" data-name="BASKET_CONFIRM">
		<div class="is--hidden js-yamarket-basket-confirm-summary__modal">
			<?php
			include __DIR__ . '/basket-confirm.php';
			?>
		</div>
	</div>
	<?php
}
?>
<h2 class="yamarket-section-title"><?= Loc::getMessage('YANDEX_MARKET_T_TRADING_ORDER_VIEW_BASKET_TITLE'); ?></h2>
<div class="yamarket-basket-wrapper adm-s-order-table-ddi js-yamarket-order__field" data-plugin="OrderView.Basket" data-name="BASKET">
	<table class="yamarket-basket-table adm-s-order-table-ddi-table adm-s-bus-ordertable-option">
		<thead>
			<tr>
				<td class="tal"><?= Loc::getMessage('YANDEX_MARKET_T_TRADING_ORDER_VIEW_BASKET_ITEM_INDEX'); ?></td>
				<?php
				foreach ($columns as $columnTitle)
				{
					?>
					<td class="tal"><?= $columnTitle; ?></td>
					<?php
				}
				?>
			</tr>
		</thead>
		<tbody>
			<tr></tr><?php // hack for bitrix css ?>
			<?php
			$itemIndex = 0;

			foreach ($arResult['BASKET']['ITEMS'] as $item)
			{
				$itemInputName = sprintf($baseInputName . '[%s]', $itemIndex);

				?>
				<tr class="bdb-line yamarket-basket-item js-yamarket-basket-item" data-plugin="OrderView.BasketItem" data-id="<?= $item['ID']; ?>">
					<td class="tal">
						<input class="js-yamarket-basket-item__data" type="hidden" name="<?= $itemInputName . '[ID]' ?>" value="<?= htmlspecialcharsbx($item['ID']) ?>" data-name="ID" />
						<?= $item['INDEX']; ?>
					</td>
					<?php
					foreach ($columns as $column => $columnTitle)
					{
						$columnValue = isset($item[$column]) ? $item[$column] : null;
						$columnFormattedKey = $column . '_FORMATTED';

						if (isset($item[$columnFormattedKey]))
						{
							$columnFormatted = $item[$columnFormattedKey];
						}
						else if ($columnValue !== null)
						{
							$columnFormatted = $columnValue;
						}
						else
						{
							$columnFormatted = '&mdash;';
						}

						switch ($column)
						{
							case 'CIS':
								$internalCis = array_filter(array_column($item['INTERNAL_INSTANCES'], 'CIS'));
								$hasInternalCis = !empty($internalCis);
								$isCisRequired = !empty($item['MARKING_GROUP']);
								$itemCis = array_filter(array_column($item['INSTANCES'], 'CIS'));
								$itemCisCount = count($itemCis);

								if ($itemCisCount >= $item['COUNT'])
								{
									$itemCisStatus = 'READY';
								}
								else if ($itemCisCount > 0 || $isCisRequired)
								{
									$itemCisStatus = 'WAIT';
								}
								else
								{
									$itemCisStatus = 'EMPTY';
								}

								if ($itemCisStatus === 'EMPTY' && !$allowCisEdit)
								{
									?>
									<td class="tal for--<?= Market\Data\TextString::toLower($column); ?>">
										<span class="yamarket-cis-summary is--disabled" data-status="<?= $itemCisStatus; ?>"><?php
											echo Loc::getMessage('YANDEX_MARKET_T_TRADING_ORDER_VIEW_BASKET_ITEM_CIS_SUMMARY_' . $itemCisStatus) ?: $itemCisStatus;
										?></span>
									</td>
									<?php
								}
								else
								{
									?>
									<td
										class="tal for--<?= Market\Data\TextString::toLower($column); ?> js-yamarket-basket-item__field"
										data-plugin="OrderView.BasketItemCisSummary"
										<?= $hasInternalCis ? sprintf("data-copy='%s'", Json::encode($internalCis)) : ''; ?>
										<?= $isCisRequired ? 'data-required="true"' : ''; ?>
										data-name="CIS"
										data-count="<?= (int)$item['COUNT'] ?>"
									>
										<a class="yamarket-cis-summary js-yamarket-basket-item-cis__summary" href="#" data-status="<?= $itemCisStatus; ?>"><?php
											echo Loc::getMessage('YANDEX_MARKET_T_TRADING_ORDER_VIEW_BASKET_ITEM_CIS_SUMMARY_' . $itemCisStatus) ?: $itemCisStatus;
										?></a>
										<?php
										if ($hasInternalCis && $allowCisEdit)
										{
											?>
											<button
												class="yamarket-copy-icon js-yamarket-basket-item-cis__summary-copy"
												type="button"
												title="<?= Loc::getMessage('YANDEX_MARKET_T_TRADING_ORDER_VIEW_BASKET_ITEM_CIS_COPY'); ?>"
											>
												<?= Loc::getMessage('YANDEX_MARKET_T_TRADING_ORDER_VIEW_BASKET_ITEM_CIS_COPY'); ?>
											</button>
											<?php
										}
										?>
										<div class="is--hidden js-yamarket-basket-item-cis__modal">
											<h3 class="yamarket-cis-modal__title"><?= $item['NAME']; ?></h3>
											<table class="yamarket-cis-table js-yamarket-basket-item-cis__field" data-plugin="OrderView.BasketItemCis">
												<?php
												for ($cisIndex = 0; $cisIndex < $item['COUNT']; ++$cisIndex)
												{
													$cisInputName = sprintf($itemInputName . '[CIS][%s]', $cisIndex);
													$cisNumber = '&numero;' . ($cisIndex + 1);
													$cisValue = isset($itemCis[$cisIndex]) ? (string)$itemCis[$cisIndex] : '';

													?>
													<tr>
														<td class="yamarket-cis-table__number"><?= $cisNumber; ?></td>
														<td class="yamarket-cis-table__control">
															<input
																class="yamarket-cis-table__input js-yamarket-basket-item-cis__input"
																type="text"
																name="<?= $cisInputName; ?>"
																value="<?= htmlspecialcharsbx($cisValue); ?>"
																size="45"
																<?= $allowCisEdit ? '' : 'readonly'; ?>
																data-name="<?= $cisIndex ?>"
															/>
														</td>
													</tr>
													<?php
												}

												if ($hasInternalCis && $allowCisEdit)
												{
													?>
													<tr>
														<td class="yamarket-cis-table__actions" colspan="2">
															<button class="yamarket-btn adm-btn js-yamarket-basket-item-cis__copy" type="button">
																<?= Loc::getMessage('YANDEX_MARKET_T_TRADING_ORDER_VIEW_BASKET_ITEM_CIS_COPY'); ?>
															</button>
														</td>
													</tr>
													<?php
												}
												?>
											</table>
										</div>
									</td>
									<?php
								}
							break;

							case 'DIGITAL':
								include __DIR__ . '/basket-column-digital.php';
							break;

							case 'COUNT':
								?>
								<td class="tal for--<?= Market\Data\TextString::toLower($column); ?>">
									<?php
									if ($allowItemsEdit)
									{
										?>
										<input class="js-yamarket-basket-item__data" type="hidden" name="<?= $itemInputName . '[INITIAL_COUNT]' ?>" value="<?= (float)$columnValue ?>" data-name="INITIAL_COUNT" />
										<input
											class="adm-input yamarket-basket-item__count js-yamarket-basket-item__data"
											type="number"
											name="<?= $itemInputName . '[COUNT]' ?>"
											value="<?= (float)$columnValue ?>"
											min="1"
											max="<?= (float)$columnValue ?>"
											step="1"
											data-name="COUNT"
										/>
										<?php
									}
									else
									{
										echo sprintf(
											'%s %s',
											(float)$columnValue,
											Loc::getMessage('YANDEX_MARKET_T_TRADING_ORDER_VIEW_BASKET_ITEM_MEASURE')
										);
									}
									?>
								</td>
								<?php
							break;

							case 'DELETE':
								?>
								<td class="tal for--<?= Market\Data\TextString::toLower($column); ?>">
									<label>
										<input class="adm-designed-checkbox js-yamarket-basket-item__data" type="checkbox" name="<?= $itemInputName . '[DELETE]'; ?>" value="Y" data-name="DELETE">
										<span class="adm-designed-checkbox-label"></span>
									</label>
								</td>
								<?php
							break;

							case 'BOX_COUNT':
								?>
								<td class="tal for--<?= Market\Data\TextString::toLower($column); ?>">
									<?= (float)$columnValue; ?>
									<?= Loc::getMessage('YANDEX_MARKET_T_TRADING_ORDER_VIEW_BASKET_ITEM_MEASURE'); ?>
								</td>
								<?php
							break;

							case 'SUBSIDY':
								$hasPromos = !empty($item['PROMOS']);

								?>
								<td class="tal for--<?= Market\Data\TextString::toLower($column); ?>">
									<?php
									if ($columnValue !== null || !$hasPromos)
									{
										echo $columnFormatted;
									}

									if ($hasPromos)
									{
										foreach ($item['PROMOS'] as $promo)
										{
											echo sprintf('<div>%s</div>', $promo);
										}
									}
									?>
								</td>
								<?php
							break;

							default:
								?>
								<td class="tal for--<?= Market\Data\TextString::toLower($column); ?> js-yamarket-basket-item__data" data-name="<?= $column ?>"><?= $columnFormatted; ?></td>
								<?php
							break;
						}
					}
					?>
				</tr>
				<?php

				++$itemIndex;
			}
			?>
		</tbody>
		<?php
		if (!empty($arResult['BASKET']['SUMMARY']))
		{
			?>
			<tfoot>
				<tr>
					<td class="yamarket-basket-summary js-yamarket-order__area" data-type="basketSummary" colspan="<?= $columnsCount; ?>">
						<?php
						$isFirstSummaryItem = true;

						foreach ($arResult['BASKET']['SUMMARY'] as $summaryItem)
						{
							echo $isFirstSummaryItem ? '' : '<br />';
							echo $summaryItem['NAME'] . ': ' . $summaryItem['VALUE'];

							$isFirstSummaryItem = false;
						}
						?>
					</td>
				</tr>
			</tfoot>
			<?php
		}
		?>
	</table>
</div>