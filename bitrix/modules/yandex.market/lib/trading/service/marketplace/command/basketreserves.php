<?php

namespace Yandex\Market\Trading\Service\Marketplace\Command;

class BasketReserves extends SkeletonReserves
{
	public function execute(array $storeData)
	{
		$quantities = $this->mapQuantities($storeData);
		$quantities = array_filter($quantities, static function($value) { return $value > 0; });

		if (empty($quantities)) { return $storeData; }

		$this->configureEnvironment();

		list($processingStates, $otherStates) = $this->loadOrders();
		$allUsedStates = $processingStates + $otherStates;
		$productIds = array_keys($quantities);

		$reserves = $this->loadReserves($processingStates, $productIds);
		$siblingReserves = $this->loadSiblingReserves($allUsedStates, $productIds);

		$storeData = $this->applyReserves($storeData, $reserves);
		$storeData = $this->applyReserves($storeData, $siblingReserves);

		return $storeData;
	}

	protected function mapQuantities(array $storeData)
	{
		$result = [];

		foreach ($storeData as $productId => $productValues)
		{
			if (!isset($productValues['AVAILABLE_QUANTITY'])) { continue; }

			$result[$productId] = $productValues['AVAILABLE_QUANTITY'];
		}

		return $result;
	}

	protected function loadReserves(array $orderStates, array $productIds)
	{
		$orderIds = array_column($orderStates, 'INTERNAL_ID');

		return $this->environment->getReserve()->getReserved($orderIds, $productIds);
	}

	protected function applyReserves(array $storeData, array $reserves)
	{
		foreach ($storeData as $productId => &$productValues)
		{
			if (!isset($reserves[$productId])) { continue; }

			$productValues['AVAILABLE_QUANTITY'] -= max(0, $reserves[$productId]['QUANTITY']);
		}
		unset($productValues);

		return $storeData;
	}
}