<?php

namespace Yandex\Market\Trading\Service\Marketplace\Command;

use Bitrix\Main;
use Yandex\Market\Reference\Storage as ReferenceStorage;
use Yandex\Market\Trading\Service as TradingService;
use Yandex\Market\Trading\Settings as TradingSettings;

class GroupStores
{
	/** @var TradingService\Marketplace\Provider */
	protected $provider;
	/** @var int[] */
	protected $linked;
	/** @var array */
	protected $settings;
	/** @var array */
	protected $preload = [
		'PRODUCT_STORE',
		'USE_ORDER_RESERVE',
	];

	public function __construct(TradingService\Marketplace\Provider $provider, array $linked = [])
	{
		$this->provider = $provider;
		$this->linked = $linked;
	}

	public function useOrderReserve()
	{
		$withReserve = $this->linkedWithReserve();

		return !empty($withReserve);
	}

	public function linkedWithReserve()
	{
		$checkboxes = $this->setting('USE_ORDER_RESERVE');
		$enabled = $this->valuePositive($checkboxes);

		return array_keys($enabled);
	}

	public function stores()
	{
		$stores = $this->setting('PRODUCT_STORE');

		return $this->valueMerge($stores);
	}

	public function linked()
	{
		return $this->linked;
	}

	protected function valueMerge($values)
	{
		$partials = [];

		foreach ($values as $value)
		{
			if (!is_array($value)) { continue; }

			$partials[] = $value;
		}

		return !empty($partials) ? array_merge(...$partials) : [];
	}

	protected function valuePositive($values)
	{
		$result = [];

		foreach ($values as $setupId => $value)
		{
			if ((string)$value === ReferenceStorage\Table::BOOLEAN_Y)
			{
				$result[$setupId] = $value;
				break;
			}
		}

		return $result;
	}

	protected function setting($name)
	{
		$settings = $this->settings();

		if (!isset($settings[$name]))
		{
			throw new Main\ArgumentException(sprintf('register setting %s preload', $name));
		}

		return $settings[$name];
	}

	protected function settings()
	{
		if ($this->settings === null)
		{
			$this->settings = $this->loadSettings();
		}

		return $this->settings;
	}

	protected function loadSettings()
	{
		$setupIds = $this->linked();
		$result = array_fill_keys($this->preload, []);

		if (empty($setupIds)) { return $result; }

		$query = TradingSettings\Table::getList([
			'filter' => [
				'=SETUP_ID' => $setupIds,
				'=NAME' => $this->preload,
			],
			'select' => [ 'SETUP_ID', 'NAME', 'VALUE' ],
		]);

		while ($row = $query->fetch())
		{
			$result[$row['NAME']][$row['SETUP_ID']] = $row['VALUE'];
		}

		return $result;
	}
}