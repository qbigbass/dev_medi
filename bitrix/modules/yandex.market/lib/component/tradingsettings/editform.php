<?php

namespace Yandex\Market\Component\TradingSettings;

use Yandex\Market;
use Bitrix\Main;

class EditForm extends Market\Component\Plain\EditForm
{
	public function processPostAction($action, $data)
	{
		switch ($action)
		{
			case 'reset':
				$this->processResetAction($data);
			break;

			default:
				parent::processPostAction($action, $data);
			break;
		}
	}

	protected function processResetAction($data)
	{
		if (!$this->getComponentParam('ALLOW_SAVE'))
		{
			$message = $this->getComponentLang('SAVE_DISALLOW');
			throw new Main\AccessDeniedException($message);
		}

		Market\Trading\Setup\Table::update($data['PRIMARY'], [ 'SETTINGS' => [] ]);
	}

	public function modifyRequest($request, $fields)
	{
		$result = parent::modifyRequest($request, $fields);
		$result = $this->modifyRequestPushStocks($result, $fields);

		return $result;
	}

	protected function modifyRequestPushStocks($request, $fields)
	{
		if (
			isset($fields['USE_PUSH_STOCKS'], $fields['WAREHOUSE_PRIMARY'])
			&& !empty($request['USE_PUSH_STOCKS'])
			&& empty($request['WAREHOUSE_PRIMARY'])
			&& (string)$request['USE_PUSH_STOCKS'] === Market\Ui\UserField\BooleanType::VALUE_Y
		)
		{
			$request['WAREHOUSE_PRIMARY'] = $this->fetchCampaignWarehousePrimary($request);
		}

		return $request;
	}

	protected function fetchCampaignWarehousePrimary($request)
	{
		try
		{
			$primary = $this->getComponentParam('PRIMARY');
			$setup = Market\Trading\Setup\Model::loadById($primary);
			$options = $setup->wakeupService()->getOptions();
			$options->setValues($request);

			Market\Utils\HttpConfiguration::stamp();
			Market\Utils\HttpConfiguration::setGlobalTimeout(5);

			$request = new Market\Api\Partner\BusinessInfo\Request();

			$request->setOauthClientId($options->getOauthClientId());
			$request->setOauthToken($options->getOauthToken()->getAccessToken());
			$request->setCampaignId($options->getCampaignId());

			$sendResult = $request->send();

			if (!$sendResult->isSuccess()) { return null; }

			/** @var Market\Api\Partner\BusinessInfo\Response $response */
			$response = $sendResult->getResponse();
			$result = null;

			/** @var Market\Api\Partner\BusinessInfo\Model\Campaign $campaign */
			foreach ($response->getCampaigns() as $campaign)
			{
				if ((string)$options->getCampaignId() !== (string)$campaign->getId()) { continue; }

				$warehouseIds = $campaign->getWarehouseIds();
				$result = is_array($warehouseIds) ? reset($warehouseIds) : null;
				break;
			}

			Market\Utils\HttpConfiguration::restore();
		}
		catch (Main\SystemException $exception)
		{
			$result = null;

			Market\Utils\HttpConfiguration::restore();
		}

		return $result;
	}

	public function load($primary, array $select = [], $isCopy = false)
	{
		$result = $this->loadSetupSettings($primary, $select);

		if (empty($result))
		{
			$result = $this->loadFieldsDefaults($select);
		}
		else
		{
			$result += $this->fillFieldsValueEmpty($select);
		}

		return $result;
	}

	protected function loadSetupSettings($primary, array $select = [])
	{
		try
		{
			$setup = Market\Trading\Setup\Model::loadById($primary);
			$settings = $setup->getSettings()->getValues();

			if (!empty($settings))
			{
				$reservedKeys = $setup->getReservedSettingsKeys();

				$result = $setup->wakeupService()->getOptions()->getValues();
				$result = array_diff_key($result, array_flip($reservedKeys));
			}
			else
			{
				$result = [];
			}
		}
		catch (Main\ObjectNotFoundException $exception)
		{
			$result = [];
		}

		return $result;
	}

	protected function loadFieldsDefaults(array $select = [])
	{
		$result = [];

		foreach ($this->getFields($select) as $fieldName => $field)
		{
			if (!isset($field['SETTINGS']['DEFAULT_VALUE'])) { continue; }

			Market\Utils\Field::setChainValue($result, $fieldName, $field['SETTINGS']['DEFAULT_VALUE'], Market\Utils\Field::GLUE_BRACKET);
		}

		return $result;
	}

	protected function fillFieldsValueEmpty(array $select = [])
	{
		$result = [];

		foreach ($this->getFields($select) as $fieldName => $field)
		{
			if (!empty($field['SETTINGS']['READONLY'])) { continue; }

			$isHidden = isset($field['HIDDEN']) && $field['HIDDEN'] === 'Y';
			$hasDefaultValue = isset($field['SETTINGS']['DEFAULT_VALUE']);
			$value = ($isHidden && $hasDefaultValue)
				? $field['SETTINGS']['DEFAULT_VALUE']
				: false;

			Market\Utils\Field::setChainValue($result, $fieldName, $value, Market\Utils\Field::GLUE_BRACKET);
		}

		return $result;
	}

	public function add($fields)
	{
		throw new Main\NotSupportedException();
	}

	public function update($primary, $values)
	{
		if (!empty($values))
		{
			$fields = $this->getComponentResult('FIELDS');

			$values = $this->applyUserFieldsOnBeforeSave($fields, $values);
			$values = $this->sliceFieldsDependHidden($fields, $values);
			$values = $this->takeOptionChanges($primary, $values);

			$rows = $this->convertValuesToRows($values);
		}
		else
		{
			$rows = [];
		}

		$updateResult = Market\Trading\Setup\Table::update($primary, [ 'SETTINGS' => $rows ]);

		if ($updateResult->isSuccess())
		{
			$setup = Market\Trading\Setup\Model::loadById($primary);
			$setup->wakeupService();
			$setup->tweak();
		}

		return $updateResult;
	}

	protected function takeOptionChanges($primary, $values)
	{
		$stored = Market\Trading\Setup\Model::loadById($primary);
		$previous = $stored->wakeupService()->getOptions();
		$options = clone $previous;

		$options->setValues($values);

		$previous->suppressRequired();
		$options->suppressRequired();
		$options->takeChanges($previous);

		return $options->getValues();
	}

	protected function convertRowsToValues($rows)
	{
		$result = [];

		foreach ($rows as $row)
		{
			$result[$row['NAME']] = $row['VALUE'];
		}

		return $result;
	}

	protected function convertValuesToRows($values)
	{
		$result = [];

		foreach ($values as $key => $value)
		{
			$result[] = [
				'NAME' => $key,
				'VALUE' => $value,
			];
		}

		return $result;
	}
}