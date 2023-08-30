<?php

namespace Yandex\Market\Trading\Routine;

use Bitrix\Main;
use Yandex\Market\Ui;
use Yandex\Market\Api;
use Yandex\Market\Utils;
use Yandex\Market\Config;
use Yandex\Market\Reference\Storage;
use Yandex\Market\Reference\Concerns;
use Yandex\Market\Trading\Setup as TradingSetup;
use Yandex\Market\Trading\Service as TradingService;

class EnablePushStocks
{
	use Concerns\HasMessage;

	protected $filter = [];
	protected $changed = [];
	protected $warehouses = [];

	public function __construct(array $filter)
	{
		$this->filter = $filter;
	}

	public function run()
	{
		$this->prepareEnvironment();

		$tradingSetups = TradingSetup\Model::loadList($this->filter);

		foreach ($tradingSetups as $tradingSetup)
		{
			$this->process($tradingSetup);
		}

		$this->resetEnvironment();
	}

	/** @noinspection DuplicatedCode */
	public function notify()
	{
		if (empty($this->changed)) { return; }

		$url = Ui\Admin\Path::getModuleUrl('trading_list', [
			'lang' => LANGUAGE_ID,
			'service' => 'marketplace',
			'find_id_numsel' => 'range',
			'find_id_from' => min($this->changed),
			'find_id_to' => max($this->changed),
			'set_filter' => 'Y',
			'apply_filter' => 'Y',
		]);

		\CAdminNotify::Add([
			'MODULE_ID' => Config::getModuleName(),
			'NOTIFY_TYPE' => \CAdminNotify::TYPE_NORMAL,
			'MESSAGE' => str_replace('#URL#', $url, self::getMessage('NOTIFY')),
			'TAG' => 'YAMARKET_PUSH_STOCKS',
		]);
	}

	protected function prepareEnvironment()
	{
		Utils\HttpConfiguration::stamp();
		Utils\HttpConfiguration::setGlobalTimeout(5);
	}

	protected function resetEnvironment()
	{
		Utils\HttpConfiguration::restore();
	}

	protected function process(TradingSetup\Model $setup)
	{
		try
		{
			if (!$setup->isActive()) { return; }

			$options = $setup->wakeupService()->getOptions();

			if (
				!($options instanceof TradingService\Marketplace\Options)
				|| $options->usePushStocks()
				|| $options->useWarehouses()
			)
			{
				return;
			}

			$warehouse = $this->warehouse($options);

			if ((string)$warehouse === '') { return; }

			$this->overwriteOptions($setup, $options, [
				'USE_PUSH_STOCKS' => Storage\Table::BOOLEAN_Y,
				'WAREHOUSE_PRIMARY' => $warehouse,
			]);

			$this->saveOptions($setup, $options);

			$this->changed[] = $setup->getId();
		}
		/** @noinspection PhpRedundantCatchClauseInspection */
		catch (Main\SystemException $exception)
		{
			trigger_error($exception->getMessage(), E_USER_WARNING);
		}
	}

	protected function warehouse(TradingService\Marketplace\Options $options)
	{
		// test local option

		$options->suppressRequired();
		$option = trim($options->getWarehousePrimary());
		$options->suppressRequired(false);

		if ($option !== '') { return $option; }

		// fetch business

		$campaignId = $options->getCampaignId();

		if (array_key_exists($campaignId, $this->warehouses))
		{
			$result = $this->warehouses[$campaignId];
		}
		else
		{
			$newWarehouses = $this->loadWarehouses($options);

			$this->warehouses += $newWarehouses;
			$result = isset($newWarehouses[$campaignId]) ? $newWarehouses[$campaignId] : null;
		}

		return $result;
	}

	protected function loadWarehouses(TradingService\Marketplace\Options $options)
	{
		$result = [
			$options->getCampaignId() => null,
		];

		$request = new Api\Partner\BusinessInfo\Request();

		$request->setOauthClientId($options->getOauthClientId());
		$request->setOauthToken($options->getOauthToken()->getAccessToken());
		$request->setCampaignId($options->getCampaignId());

		$sendResult = $request->send();

		if (!$sendResult->isSuccess()) { return $result; }

		/** @var Api\Partner\BusinessInfo\Response $response */
		$response = $sendResult->getResponse();

		/** @var Api\Partner\BusinessInfo\Model\Campaign $campaign */
		foreach ($response->getCampaigns() as $campaign)
		{
			$campaignWarehouses = $campaign->getWarehouseIds();
			$result[$campaign->getId()] = is_array($campaignWarehouses) ? reset($campaignWarehouses) : null;
		}

		return $result;
	}

	protected function overwriteOptions(TradingSetup\Model $setup, TradingService\Marketplace\Options $options, array $values)
	{
		$options->setValues(array_merge($options->getValues(), $values));

		$setup->wakeupService();
		$setup->tweak();
	}

	protected function saveOptions(TradingSetup\Model $setup, TradingService\Marketplace\Options $options)
	{
		$valuesRows = [];

		foreach ($options->getValues() as $key => $value)
		{
			$valuesRows[] = [
				'NAME' => $key,
				'VALUE' => $value,
			];
		}

		TradingSetup\Table::update($setup->getId(), [ 'SETTINGS' => $valuesRows ]);
	}
}