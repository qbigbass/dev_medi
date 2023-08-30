<?php

namespace Yandex\Market\Trading\Service\Common\Command;

use Yandex\Market\Trading\Service as TradingService;
use Yandex\Market\Trading\Entity as TradingEntity;

class SkuMap
{
	protected $provider;
	protected $environment;

	public function __construct(
		TradingService\Common\Provider $provider,
		TradingEntity\Reference\Environment $environment
	)
	{
		$this->provider = $provider;
		$this->environment = $environment;
	}

	public function make(array $productIds)
	{
		$options = $this->provider->getOptions();
		$optionMap = $options->getProductSkuMap();
		$optionPrefix = $options->getProductSkuPrefix();
		$result = null;

		if (!empty($optionMap))
		{
			$result = $this->environment->getProduct()->getSkuMap($productIds, $optionMap);
		}

		if ($optionPrefix !== '')
		{
			if ($result === null)
			{
				$result = array_combine($productIds, $productIds);
			}

			$result = array_map(static function($sku) use ($optionPrefix) {
				return $optionPrefix . $sku;
			}, $result);
		}

		return $result;
	}
}