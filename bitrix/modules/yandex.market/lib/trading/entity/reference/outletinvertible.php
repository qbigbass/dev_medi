<?php

namespace Yandex\Market\Trading\Entity\Reference;

use Yandex\Market;
use Yandex\Market\Trading\Service as TradingService;

interface OutletInvertible
{
	public function isMatchService(Market\Api\Delivery\Services\Model\DeliveryService $deliveryService);

	public function searchOutlet(Order $order, Market\Api\Model\Region $region, TradingService\MarketplaceDbs\Model\Order\Delivery\Address $address);
}