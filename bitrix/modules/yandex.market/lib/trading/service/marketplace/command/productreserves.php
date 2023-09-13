<?php

namespace Yandex\Market\Trading\Service\Marketplace\Command;

use Yandex\Market;

class ProductReserves extends SkeletonReserves
{
	public function execute(array $amounts)
	{
		if (empty($amounts)) { return []; }

		$this->configureEnvironment();

		$productIds = array_column($amounts, 'ID');
		list($processingStates, $otherStates) = $this->loadOrders();
		$allUsedStates = $processingStates + $otherStates;

		$reserves = $this->loadReserves($processingStates, $productIds);
		$siblingReserved = $this->loadSiblingReserves($allUsedStates, $productIds);

		$amounts = $this->applyReserves($amounts, $reserves);
		$amounts = $this->applyReserves($amounts, $siblingReserved, true);

		return $amounts;
	}

	protected function loadReserves(array $orderStates, array $productIds)
	{
		$orderIds = array_column($orderStates, 'INTERNAL_ID');

		return $this->environment->getReserve()->getAmounts($orderIds, $productIds);
	}

	protected function applyReserves(array $amounts, array $reserves, $invert = false)
	{
		$sign = ($invert ? -1 : 1);

		foreach ($amounts as &$amount)
		{
			if (!isset($reserves[$amount['ID']])) { continue; }

			$reserve = $reserves[$amount['ID']];

			if (isset($amount['QUANTITY_LIST'][Market\Data\Trading\Stocks::TYPE_FIT]))
			{
				$amount['QUANTITY_LIST'][Market\Data\Trading\Stocks::TYPE_FIT] += $sign * $reserve['QUANTITY'];
			}

			if (isset($amount['QUANTITY']))
			{
				$amount['QUANTITY'] += $sign * $reserve['QUANTITY'];
			}

			if (Market\Data\DateTime::compare($reserve['TIMESTAMP_X'], $amount['TIMESTAMP_X']) === 1)
			{
				$amount['TIMESTAMP_X'] = $reserve['TIMESTAMP_X'];
			}
		}
		unset($amount);

		return $amounts;
	}
}