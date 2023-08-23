<?php

namespace Yandex\Market\Trading\Service\Marketplace\Command;

use Bitrix\Main;
use Yandex\Market\Trading\Service as TradingService;
use Yandex\Market\Trading\Settings as TradingSettings;
use Yandex\Market\Trading\Setup as TradingSetup;

class GroupStoresTweak
{
	protected $provider;
	protected $setupId;
	protected $linked;

	public function __construct(TradingService\Marketplace\Provider $provider, $setupId, array $linked)
	{
		$this->provider = $provider;
		$this->setupId = (int)$setupId;
		$this->linked = $linked;
	}

	public function execute()
	{
		$stored = $this->stored();

		$this->link($stored);
		$this->unlink($stored);
	}

	protected function stored()
	{
		$result = [];

		$query = TradingSettings\Table::getList([
			'filter' => [ '=NAME' => 'STORE_GROUP' ],
			'select' => [ 'SETUP_ID', 'VALUE' ],
		]);

		while ($row = $query->fetch())
		{
			if ((int)$row['SETUP_ID'] === $this->setupId) { continue; }

			$option = $row['VALUE'];

			if (!is_array($option)) { $option = []; }

			Main\Type\Collection::normalizeArrayValuesByInt($option);

			$result[$row['SETUP_ID']] = $option;
		}

		return $result;
	}

	protected function link(array $stored)
	{
		foreach ($this->linked as $setupId)
		{
			$setupId = (int)$setupId;

			if ($setupId === $this->setupId) { continue; }

			$optionExists = isset($stored[$setupId]);
			$option = $optionExists ? $stored[$setupId] : [];

			if (in_array($this->setupId, $option, true)) { continue; }

			$newOption = array_merge($option, [ $this->setupId ]);

			$this->save($setupId, $newOption, $optionExists);
			$this->tweak($setupId);
		}
	}

	protected function unlink(array $stored)
	{
		foreach ($stored as $setupId => $option)
		{
			$setupId = (int)$setupId;

			if ($setupId === $this->setupId) { continue; }
			if (in_array($setupId, $this->linked, true)) { continue; }

			$index = array_search($this->setupId, $option, true);

			if ($index === false) { continue; }

			array_splice($option, $index, 1);

			$this->save($setupId, $option, true);
			$this->tweak($setupId);
		}
	}

	protected function save($setupId, $linked, $optionExists)
	{
		$primary = [
			'SETUP_ID' => $setupId,
			'NAME' => 'STORE_GROUP',
		];
		$fields = [
			'VALUE' => $linked,
		];

		if (empty($linked) && !$optionExists) { return; }

		if ($optionExists)
		{
			TradingSettings\Table::update($primary, $fields);
		}
		else
		{
			TradingSettings\Table::add($primary + $fields);
		}
	}

	protected function tweak($setupId)
	{
		try
		{
			$setup = TradingSetup\Model::loadById($setupId);

			$setup->wakeupService();
			$setup->tweak();
		}
		catch (Main\ObjectNotFoundException $exception)
		{
			// setup not found, then skip
		}
	}
}