<?php

namespace Yandex\Market\Trading\Service\Marketplace;

use Yandex\Market;
use Bitrix\Main;
use Bitrix\Sale;
use Yandex\Market\Trading\Service as TradingService;
use Yandex\Market\Trading\Entity as TradingEntity;

class Options extends TradingService\Common\Options
{
	/** @var Provider */
	protected $provider;

	protected static function includeMessages()
	{
		Main\Localization\Loc::loadMessages(__FILE__);
		parent::includeMessages();
	}

	public function __construct(Provider $provider)
	{
		parent::__construct($provider);
	}

	public function getTitle($version = '')
	{
		$suffix = $version !== '' ? '_' . $version : '';

		return static::getLang('TRADING_SERVICE_MARKETPLACE_TITLE' . $suffix);
	}

	public function getPaySystemId($paymentType)
	{
		$paySystemTypeUpper = Market\Data\TextString::toUpper($paymentType);

		return (string)$this->getValue('PAY_SYSTEM_' . $paySystemTypeUpper);
	}

	public function getDeliveryId()
	{
		return (string)$this->getValue('DELIVERY_ID');
	}

	public function includeBasketSubsidy()
	{
		return (string)$this->getValue('BASKET_SUBSIDY_INCLUDE') === Market\Reference\Storage\Table::BOOLEAN_Y;
	}

	public function getSubsidyPaySystemId()
	{
		return (string)$this->getValue('SUBSIDY_PAY_SYSTEM_ID');
	}

	public function getCashboxCheck()
	{
		return $this->getValue('CASHBOX_CHECK', PaySystem::CASHBOX_CHECK_DISABLED);
	}

	public function useWarehouses()
	{
		return (string)$this->getValue('USE_WAREHOUSES') === Market\Reference\Storage\Table::BOOLEAN_Y;
	}

	public function getWarehouseStoreField()
	{
		return $this->getRequiredValue('WAREHOUSE_STORE_FIELD');
	}

	public function getProductStores()
	{
		$result = array_unique(array_merge(
			parent::getProductStores(),
			$this->getStoreGroupCommand()->stores()
		));

		sort($result);

		return $result;
	}

	public function getStoreGroup()
	{
		$option = (array)$this->getValue('STORE_GROUP');

		Main\Type\Collection::normalizeArrayValuesByInt($option);

		return $option;
	}

	protected function getStoreGroupCommand()
	{
		return $this->provider->getContainer()->single(Command\GroupStores::class, [
			'linked' => $this->getStoreGroup(),
		]);
	}

	public function usePushStocks()
	{
		return (string)$this->getValue('USE_PUSH_STOCKS') === Market\Reference\Storage\Table::BOOLEAN_Y;
	}

	public function getWarehousePrimary()
	{
		return $this->getRequiredValue('WAREHOUSE_PRIMARY');
	}

	public function getWarehousePrimaryField()
	{
		return $this->getRequiredValue('WAREHOUSE_PRIMARY_FIELD');
	}

	public function usePushPrices()
	{
		return (string)$this->getValue('USE_PUSH_PRICES') === Market\Reference\Storage\Table::BOOLEAN_Y;
	}

	public function getProductFeeds()
	{
		$ids = (array)$this->getValue('PRODUCT_FEED');

		Main\Type\Collection::normalizeArrayValuesByInt($ids, false);

		return $ids;
	}

	public function productUpdatedAt()
	{
		$dateFormatted = (string)$this->getValue('PRODUCT_UPDATED_AT');

		return (
			$dateFormatted !== ''
				? new Main\Type\DateTime($dateFormatted, \DateTime::ATOM)
				: null
		);
	}

	public function useOrderReserve()
	{
		return (
			$this->selfOrderReserve()
			|| $this->getStoreGroupCommand()->useOrderReserve()
		);
	}

	public function selfOrderReserve()
	{
		return (string)$this->getValue('USE_ORDER_RESERVE') === Market\Reference\Storage\Table::BOOLEAN_Y;
	}

	public function getReserveGroupSetupIds()
	{
		return $this->getStoreGroupCommand()->linkedWithReserve();
	}

	public function isAllowModifyPrice()
	{
		return true;
	}

	public function isAllowProductSkuPrefix()
	{
		return Market\Config::isExpertMode();
	}

	/** @return Options\SelfTestOption */
	public function getSelfTestOption()
	{
		return $this->getFieldset('SELF_TEST');
	}

	public function getShipmentStatus($action)
	{
		return $this->getValue('STATUS_SHIPMENT_' . $action);
	}

	public function getEnvironmentFieldActions()
	{
		return array_filter([
			$this->getEnvironmentCisActions(),
			$this->getEnvironmentItemsActions(),
			$this->getEnvironmentCashboxActions(),
		]);
	}

	protected function getEnvironmentCisActions()
	{
		return [
			'FIELD' => 'SHIPMENT.ITEM.STORE.MARKING_CODE',
			'PATH' => 'send/cis',
			'PAYLOAD' => static function(array $action) {
				$itemsMap = [];
				$newIndex = 0;
				$result = [
					'items' => [],
				];

				foreach ($action['VALUE'] as $storeItem)
				{
					$markingCode = trim($storeItem['VALUE']);

					if ($markingCode === '') { continue; }

					$itemKey = $storeItem['XML_ID'] . ':' . $storeItem['PRODUCT_ID'];
					$cis = Market\Data\Trading\Cis::formatMarkingCode($markingCode);

					if (isset($itemsMap[$itemKey]))
					{
						$itemIndex = $itemsMap[$itemKey];
						$result['items'][$itemIndex]['instances'][] = [ 'cis' => $cis ];
					}
					else
					{
						$itemsMap[$itemKey] = $newIndex;
						$result['items'][$newIndex] = [
							'productId' => $storeItem['PRODUCT_ID'],
							'xmlId' => $storeItem['XML_ID'],
							'instances' => [
								[ 'cis' => $cis ],
							],
						];

						++$newIndex;
					}
				}

				return !empty($result['items']) ? $result : null;
			}
		];
	}

	protected function getEnvironmentItemsActions()
	{
		if (Market\Config::getOption('trading_silent_basket', 'N') === 'Y') { return null; }

		return [
			'FIELD' => 'BASKET.QUANTITY',
			'PATH' => 'send/items',
			'PAYLOAD' => static function(array $action) {
				$result = [
					'items' => [],
				];

				foreach ($action['VALUE'] as $basketItem)
				{
					$quantity = (float)$basketItem['VALUE'];

					if ($quantity <= 0) { continue; }

					$result['items'][] = [
						'productId' => $basketItem['PRODUCT_ID'],
						'xmlId' => $basketItem['XML_ID'],
						'count' => $quantity,
					];
				}

				return $result;
			}
		];
	}

	protected function getEnvironmentCashboxActions()
	{
		if ($this->getCashboxCheck() !== PaySystem::CASHBOX_CHECK_DISABLED) { return null; }

		return [
			'FIELD' => 'CASHBOX.CHECK',
			'PATH' => 'system/cashbox/reset',
			'PAYLOAD' => [],
			'DELAY' => false,
		];
	}

	protected function applyValues()
	{
		parent::applyValues();
		$this->applyOrderCourierProperties();
		$this->applyElectronicAcceptanceCertificateProperties();
		$this->applyPaySystemId();
	}

	protected function applyProductStoresReserve()
	{
		$stored = (array)$this->getValue('PRODUCT_STORE');
		$required = array_diff($stored, [
			TradingEntity\Common\Store::PRODUCT_FIELD_QUANTITY_RESERVED,
		]);

		if (count($stored) !== count($required))
		{
			$this->values['PRODUCT_STORE'] = array_values($required);
			$this->values['USE_ORDER_RESERVE'] = Market\Ui\UserField\BooleanType::VALUE_Y;
		}
		else if (!empty($stored) && !isset($this->values['USE_ORDER_RESERVE']))
		{
			$this->values['USE_ORDER_RESERVE'] = Market\Ui\UserField\BooleanType::VALUE_N;
		}
	}

	protected function applyOrderCourierProperties()
	{
		if (
			empty($this->values['PROPERTY_VEHICLE_NUMBER'])
			|| !empty($this->values['PROPERTY_COURIER_VEHICLE_NUMBER'])
		)
		{
			return;
		}

		$this->values['PROPERTY_COURIER_VEHICLE_NUMBER'] = $this->values['PROPERTY_VEHICLE_NUMBER'];
		unset($this->values['PROPERTY_VEHICLE_NUMBER']);
	}

	protected function applyElectronicAcceptanceCertificateProperties()
	{
		if (
			empty($this->values['PROPERTY_ELECTRONIC_ACCEPTANCE_CERTIFICATE'])
			|| !empty($this->values['PROPERTY_EAC_CODE'])
		)
		{
			return;
		}

		$this->values['PROPERTY_EAC_CODE'] = $this->values['PROPERTY_ELECTRONIC_ACCEPTANCE_CERTIFICATE'];
		unset($this->values['PROPERTY_ELECTRONIC_ACCEPTANCE_CERTIFICATE']);
	}

	protected function applyPaySystemId()
	{
		if (empty($this->values['PAY_SYSTEM_ID'])) { return; }

		foreach ($this->provider->getPaySystem()->getTypes() as $paymentType)
		{
			$optionName = 'PAY_SYSTEM_' . $paymentType;

			if (isset($this->values[$optionName])) { continue; }

			$this->values[$optionName] = $this->values['PAY_SYSTEM_ID'];
		}

		unset($this->values['PAY_SYSTEM_ID']);
	}

	public function takeChanges(TradingService\Reference\Options\Skeleton $previous)
	{
		/** @var Options $previous */
		Market\Reference\Assert::typeOf($previous, static::class, 'previous');

		$this->takeProductChanges($previous);
	}

	protected function takeProductChanges(Options $previous)
	{
		if ($this->compareStoreChanges($previous) || $this->compareSkuChanges($previous) || $this->compareReserveChanges($previous))
		{
			$timestamp = new Main\Type\DateTime();

			$this->values['PRODUCT_UPDATED_AT'] = $timestamp->format(\DateTime::ATOM);
		}
	}

	protected function compareStoreChanges(Options $previous)
	{
		if ($previous->useWarehouses() !== $this->useWarehouses())
		{
			$changed = true;
		}
		else if ($this->useWarehouses())
		{
			$changed = $previous->getWarehouseStoreField() !== $this->getWarehouseStoreField();
		}
		else
		{
			$currentStores = (array)$this->getValue('PRODUCT_STORE');
			$previousStores = (array)$previous->getValue('PRODUCT_STORE');
			$newStores = array_diff($currentStores, $previousStores);
			$deletedStores = array_diff($previousStores, $currentStores);

			$changed = !empty($newStores) || !empty($deletedStores);
		}

		return $changed;
	}

	protected function compareSkuChanges(Options $previous)
	{
		$currentMap = $this->getProductSkuMap();
		$previousMap = $previous->getProductSkuMap();

		if (empty($currentMap) !== empty($previousMap))
		{
			$changed = true;
		}
		else if (!empty($previousMap))
		{
			$changed = false;

			foreach ($previousMap as $key => $previousLink)
			{
				$currentLink = isset($currentMap[$key])
					? $currentMap[$key]
					: null;

				if (
					$currentLink === null
					|| $currentLink['IBLOCK'] !== $previousLink['IBLOCK']
					|| $currentLink['FIELD'] !== $previousLink['FIELD']
				)
				{
					$changed = true;
					break;
				}
			}
		}
		else
		{
			$changed = false;
		}

		return $changed;
	}

	protected function compareReserveChanges(Options $previous)
	{
		return (string)$this->getValue('USE_ORDER_RESERVE') !== (string)$previous->getValue('USE_ORDER_RESERVE');
	}

	public function getTabs()
	{
		return [
			'COMMON' => [
				'name' => static::getLang('TRADING_SERVICE_MARKETPLACE_TAB_COMMON'),
				'sort' => 1000,
			],
			'STORE' => [
				'name' => static::getLang('TRADING_SERVICE_MARKETPLACE_TAB_STORE'),
				'sort' => 2000,
			],
			'STATUS' => [
				'name' => static::getLang('TRADING_SERVICE_MARKETPLACE_TAB_STATUS'),
				'sort' => 3000,
				'data' => [
					'WARNING' => static::getLang('TRADING_SERVICE_MARKETPLACE_TAB_STATUS_NOTE'),
				]
			],
		];
	}

	public function getFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		return
			$this->getCommonFields($environment, $siteId)
			+ $this->getCompanyFields($environment, $siteId)
			+ $this->getIncomingRequestFields($environment, $siteId)
			+ $this->getOauthRequestFields($environment, $siteId)
			+ $this->getOrderDeliveryFields($environment, $siteId)
			+ $this->getOrderPaySystemFields($environment, $siteId)
			+ $this->getOrderBasketSubsidyFields($environment, $siteId)
			+ $this->getOrderCashboxFields($environment, $siteId)
			+ $this->getOrderPersonFields($environment, $siteId)
			+ $this->getOrderPropertyBuyerFields($environment, $siteId)
			+ $this->getOrderPropertyUtilFields($environment, $siteId)
			+ $this->getOrderPropertyCourierFields($environment, $siteId)
			+ $this->getProductSkuMapFields($environment, $siteId)
			+ $this->getProductStoreFields($environment, $siteId)
			+ $this->getStoreGroupFields($environment, $siteId)
			+ $this->getPushStocksFields($environment, $siteId)
			+ $this->getPushPricesFields($environment, $siteId)
			+ $this->getProductPriceFields($environment, $siteId)
			+ $this->getProductFeedFields($environment, $siteId)
			+ $this->getStatusInFields($environment, $siteId)
			+ $this->getStatusOutFields($environment, $siteId)
			+ $this->getStatusOutSyncFields($environment, $siteId)
			+ $this->getStatusShipmentFields($environment, $siteId);
	}

	protected function getCommonFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		$result = parent::getCommonFields($environment, $siteId);

		return $this->applyFieldsOverrides($result, [
			'GROUP' => static::getLang('TRADING_SERVICE_COMMON_GROUP_SERVICE_REQUEST'),
		]);
	}

	protected function getCompanyFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		$result = parent::getCompanyFields($environment, $siteId);

		return $this->applyFieldsOverrides($result, [
			'GROUP' => static::getLang('TRADING_SERVICE_COMMON_GROUP_SERVICE_REQUEST'),
			'DEPRECATED' => 'Y',
		]);
	}

	protected function getPersonTypeDefaultValue(TradingEntity\Reference\PersonType $personType, $siteId)
	{
		return $personType->getLegalId($siteId);
	}

	protected function getOrderPersonFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		$result = parent::getOrderPersonFields($environment, $siteId);

		return $this->applyFieldsOverrides($result, [
			'GROUP' => static::getLang('TRADING_SERVICE_MARKETPLACE_GROUP_ORDER_PROPERTY'),
			'SORT' => 3480,
		]);
	}

	protected function getOrderPaySystemFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		try
		{
			$paySystem = $environment->getPaySystem();
			$paySystemEnum = $paySystem->getEnum($siteId);
			$firstPaySystem = reset($paySystemEnum);
			$servicePaySystem = $this->provider->getPaySystem();
			$result = [];
			$sort = 3400;

			foreach ($servicePaySystem->getTypes() as $paymentType)
			{
				$result['PAY_SYSTEM_' . $paymentType] = [
					'TYPE' => 'enumeration',
					'MANDATORY' => $paySystem->isRequired() ? 'Y' : 'N',
					'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_PAY_SYSTEM', [
						'#TYPE#' => $servicePaySystem->getTypeTitle($paymentType, 'SHORT'),
					]),
					'GROUP' => static::getLang('TRADING_SERVICE_MARKETPLACE_GROUP_ORDER'),
					'GROUP_DESCRIPTION' => static::getLang('TRADING_SERVICE_MARKETPLACE_GROUP_ORDER_DESCRIPTION'),
					'VALUES' => $paySystemEnum,
					'SETTINGS' => [
						'DEFAULT_VALUE' => $firstPaySystem !== false ? $firstPaySystem['ID'] : null,
						'STYLE' => 'max-width: 220px;',
					],
					'SORT' => ++$sort,
				];
			}
		}
		catch (Market\Exceptions\NotImplemented $exception)
		{
			$result = [];
		}

		return $result;
	}

	protected function getOrderDeliveryFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		try
		{
			$delivery = $environment->getDelivery();
			$deliveryEnum = $delivery->getEnum($siteId);
			$defaultDelivery = null;
			$emptyDelivery = array_filter($deliveryEnum, function($option) {
				return $option['TYPE'] === Market\Data\Trading\Delivery::EMPTY_DELIVERY;
			});

			if (empty($emptyDelivery))
			{
				$firstEmptyDelivery = reset($emptyDelivery);
				$defaultDelivery = $firstEmptyDelivery['ID'];
			}
			else if (!empty($deliveryEnum))
			{
				$firstDelivery = reset($deliveryEnum);
				$defaultDelivery = $firstDelivery['ID'];
			}

			$result = [
				'DELIVERY_ID' => [
					'TYPE' => 'enumeration',
					'MANDATORY' => $delivery->isRequired() ? 'Y' : 'N',
					'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_DELIVERY_ID'),
					'GROUP' => static::getLang('TRADING_SERVICE_MARKETPLACE_GROUP_ORDER'),
					'GROUP_DESCRIPTION' => static::getLang('TRADING_SERVICE_MARKETPLACE_GROUP_ORDER_DESCRIPTION'),
					'VALUES' => $deliveryEnum,
					'SETTINGS' => [
						'DEFAULT_VALUE' => $defaultDelivery,
						'STYLE' => 'max-width: 220px;',
					],
					'SORT' => 3300,
				],
			];
		}
		catch (Market\Exceptions\NotImplemented $exception)
		{
			$result = [];
		}

		return $result;
	}

	protected function getOrderBasketSubsidyFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		try
		{
			$paySystem = $environment->getPaySystem();
			$paySystemEnum = $paySystem->getEnum($siteId);

			$result = [
				'BASKET_SUBSIDY_INCLUDE' => [
					'TYPE' => 'boolean',
					'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_BASKET_SUBSIDY_INCLUDE'),
					'SORT' => 3450,
					'SETTINGS' => [
						'DEFAULT_VALUE' => Market\Ui\UserField\BooleanType::VALUE_Y,
					],
				],
				'SUBSIDY_PAY_SYSTEM_ID' => [
					'TYPE' => 'enumeration',
					'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_SUBSIDY_PAY_SYSTEM_ID'),
					'HELP_MESSAGE' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_SUBSIDY_PAY_SYSTEM_ID_HELP'),
					'VALUES' => $paySystemEnum,
					'SETTINGS' => [
						'DEFAULT_VALUE' => $paySystem->getInnerPaySystemId(),
						'CAPTION_NO_VALUE' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_SUBSIDY_PAY_SYSTEM_ID_NO_VALUE'),
						'STYLE' => 'max-width: 220px;'
					],
					'SORT' => 3451,
					'DEPEND' => [
						'BASKET_SUBSIDY_INCLUDE' => [
							'RULE' => 'ANY',
							'VALUE' => Market\Ui\UserField\BooleanType::VALUE_Y,
						],
					],
				],
			];
		}
		catch (Market\Exceptions\NotImplemented $exception)
		{
			$result = [];
		}

		return $result;
	}

	protected function getOrderCashboxFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		$paySystem = $this->provider->getPaySystem();
		$default = $paySystem::CASHBOX_CHECK_DISABLED;
		$values = $paySystem->getCashboxCheckEnum();

		uasort($values, function($optionA, $optionB) use ($default) {
			$sortA = $optionA['ID'] === $default ? 0 : 1;
			$sortB = $optionB['ID'] === $default ? 0 : 1;

			if ($sortA === $sortB) { return 0; }

			return ($sortA < $sortB ? -1 : 1);
		});

		return [
			'CASHBOX_CHECK' => [
				'TYPE' => 'enumeration',
				'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_CASHBOX_CHECK'),
				'HELP_MESSAGE' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_CASHBOX_CHECK_HELP'),
				'VALUES' => $values,
				'HIDDEN' => !Main\Loader::includeModule('sale') || !class_exists(Sale\Cashbox\Cashbox::class) ? 'Y' : 'N',
				'SETTINGS' => [
					'DEFAULT_VALUE' => $default,
					'ALLOW_NO_VALUE' => 'N',
					'STYLE' => 'max-width: 220px;',
				],
				'SORT' => 3470,
			],
		];
	}

	protected function getOrderPropertyBuyerFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		$buyerClass = $this->provider->getModelFactory()->getBuyerClassName();
		$fields = $buyerClass::getMeaningfulFields();
		$options = [];

		foreach ($fields as $fieldName)
		{
			$options[$fieldName] = [
				'NAME' => $buyerClass::getMeaningfulFieldTitle($fieldName),
			];
		}

		return $this->createPropertyFields($environment, $siteId, $options, 3550);
	}

	protected function getOrderPropertyUtilFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		$result = parent::getOrderPropertyUtilFields($environment, $siteId);

		return $this->applyFieldsOverrides($result, [
			'GROUP' => static::getLang('TRADING_SERVICE_MARKETPLACE_GROUP_ORDER_PROPERTY'),
			'SORT' => 3500,
		]);
	}

	protected function getOrderPropertyCourierFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		$options = [];

		foreach (Model\Order\Delivery\Courier::getMeaningfulFields() as $field)
		{
			$options['COURIER_' . $field] = [
				'NAME' => Model\Order\Delivery\Courier::getMeaningfulFieldTitle($field),
				'GROUP' => static::getLang('TRADING_SERVICE_MARKETPLACE_GROUP_COURIER_PROPERTY'),
			];
		}

		return $this->createPropertyFields($environment, $siteId, $options, 3600);
	}

	protected function getProductSkuMapFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		$result = parent::getProductSkuMapFields($environment, $siteId);
		$overridable = array_diff_key($result, [
			'PRODUCT_SKU_ADV_PREFIX' => true,
		]);

		return
			$this->applyFieldsOverrides($overridable, [ 'HIDDEN' => 'N' ])
			+ $result;
	}

	protected function getProductStoreFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		global $APPLICATION;

		try
		{
			$store = $environment->getStore();
			$supportsWarehouses = $this->provider->getFeature()->supportsWarehouses();

			$warehouseFields = [
				'USE_WAREHOUSES' => [
					'TYPE' => 'boolean',
					'TAB' => 'STORE',
					'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_USE_WAREHOUSES'),
					'HELP_MESSAGE' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_USE_WAREHOUSES_HELP'),
					'SORT' => 1100,
					'HIDDEN' => $supportsWarehouses ? 'N' : 'Y',
					'DEPRECATED' => 'Y',
				],
				'WAREHOUSE_STORE_FIELD' => [
					'TYPE' => 'enumeration',
					'TAB' => 'STORE',
					'MANDATORY' => 'Y',
					'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_WAREHOUSE_STORE_FIELD'),
					'HELP_MESSAGE' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_WAREHOUSE_STORE_FIELD_HELP', [
						'#LANG#' => LANGUAGE_ID,
						'#BACKURL#' => rawurlencode($APPLICATION->GetCurPageParam('')),
					]),
					'SORT' => 1105,
					'VALUES' => $store->getFieldEnum($siteId),
					'HIDDEN' => $supportsWarehouses ? 'N' : 'Y',
					'SETTINGS' => [
						'DEFAULT_VALUE' => $store->getWarehouseDefaultField(),
						'STYLE' => 'max-width: 220px;',
					],
					'DEPEND' => [
						'USE_WAREHOUSES' => [
							'RULE' => 'EMPTY',
							'VALUE' => false,
						],
					],
				],
			];
			$commonFields = parent::getProductStoreFields($environment, $siteId);
			$commonFields += [
				'USE_ORDER_RESERVE' =>  [
					'TYPE' => 'boolean',
					'TAB' => 'STORE',
					'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_USE_ORDER_RESERVE'),
					'HELP_MESSAGE' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_USE_ORDER_RESERVE_HELP'),
					'SORT' => 1105,
					'SETTINGS' => [
						'DEFAULT_VALUE' => Market\Ui\UserField\BooleanType::VALUE_Y,
					],
				],
			];

			if ($supportsWarehouses)
			{
				$excludeDepend = [
					'PRODUCT_RATIO_SOURCE' => true,
				];

				foreach ($commonFields as $commonFieldKey => &$commonField)
				{
					if (isset($commonField['INTRO']))
					{
						$warehouseFields['USE_WAREHOUSES']['INTRO'] = $commonField['INTRO'];
						unset($commonField['INTRO']);
					}

					$commonField['SORT'] += 5;

					if (!isset($excludeDepend[$commonFieldKey]))
					{
						$commonField['DEPEND'] = [
							'USE_WAREHOUSES' => [
								'RULE' => 'EMPTY',
								'VALUE' => true,
							],
						];
					}
				}
				unset($commonField);
			}

			$result = $warehouseFields + $commonFields;
		}
		catch (Market\Exceptions\NotImplemented $exception)
		{
			$result = [];
		}

		return $result;
	}

	protected function getStoreGroupFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		return [
			'STORE_GROUP' => [
				'TYPE' => 'enumeration',
				'TAB' => 'STORE',
				'SORT' => 1300,
				'HIDDEN' => 'H',
				'MULTIPLE' => 'Y',
				'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_STORE_GROUP'),
				'HELP_MESSAGE' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_STORE_GROUP_HELP'),
				'VALUES' => function() {
					$configured = $this->provider->getContainer()->single(Command\BusinessCampaigns::class)->configured();
					$configured = array_filter($configured, function($option) {
						return (int)$option['ID'] !== (int)$this->getValue('SETUP_ID');
					});

					return $configured;
				},
				'SETTINGS' => [
					'DISPLAY' => 'CHECKBOX',
				],
			],
		];
	}

	protected function getPushStocksFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		global $APPLICATION;

		try
		{
			$store = $environment->getStore();

			$result = [
				'USE_PUSH_STOCKS' => [
					'TYPE' => 'boolean',
					'TAB' => 'STORE',
					'GROUP' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_GROUP_PUSH_DATA'),
					'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_USE_PUSH_STOCKS'),
					'HELP_MESSAGE' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_USE_PUSH_STOCKS_HELP'),
					'SORT' => 2200,
					'SETTINGS' => [
						'DEFAULT_VALUE' => Market\Ui\UserField\BooleanType::VALUE_Y,
					],
				],
				'WAREHOUSE_PRIMARY' => [
					'TYPE' => 'string',
					'TAB' => 'STORE',
					'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_WAREHOUSE_PRIMARY'),
					'HELP_MESSAGE' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_WAREHOUSE_PRIMARY_HELP'),
					'MANDATORY' => 'Y',
					'SORT' => 2205,
					'DEPEND' => [
						'USE_WAREHOUSES' => [
							'RULE' => 'EMPTY',
							'VALUE' => true,
						],
						'USE_PUSH_STOCKS' => [
							'RULE' => 'EMPTY',
							'VALUE' => false,
						],
					],
				],
				'WAREHOUSE_PRIMARY_FIELD' => [
					'TYPE' => 'enumeration',
					'TAB' => 'STORE',
					'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_WAREHOUSE_PRIMARY_FIELD'),
					'HELP_MESSAGE' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_WAREHOUSE_PRIMARY_FIELD_HELP', [
						'#LANG#' => LANGUAGE_ID,
						'#BACKURL#' => rawurlencode($APPLICATION->GetCurPageParam('')),
					]),
					'MANDATORY' => 'Y',
					'VALUES' => $store->getFieldEnum($siteId),
					'SORT' => 2205,
					'DEPEND' => [
						'USE_WAREHOUSES' => [
							'RULE' => 'EMPTY',
							'VALUE' => false,
						],
						'USE_PUSH_STOCKS' => [
							'RULE' => 'EMPTY',
							'VALUE' => false,
						],
					],
				],
			];
		}
		catch (Market\Exceptions\NotImplemented $exception)
		{
			$result = [];
		}

		return $result;
	}

	protected function getPushPricesFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		return [
			'USE_PUSH_PRICES' => [
				'TYPE' => 'boolean',
				'TAB' => 'STORE',
				'GROUP' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_GROUP_PUSH_DATA'),
				'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_USE_PUSH_PRICES'),
				'HELP_MESSAGE' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_USE_PUSH_PRICES_HELP'),
				'SORT' => 2225,
			],
		];
	}

	protected function getProductFeedFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		return [
			'PRODUCT_FEED' => [
				'TYPE' => 'enumeration',
				'TAB' => 'STORE',
				'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_PRODUCT_FEED'),
				'HELP_MESSAGE' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_PRODUCT_FEED_HELP'),
				'MULTIPLE' => 'Y',
				'VALUES' => $this->getFeedEnum(),
				'SORT' => 2250,
				'SETTINGS' => [
					'STYLE' => 'max-width: 220px;',
					'VALIGN_PUSH' => true,
				],
				'DEPEND' => [
					'LOGIC' => 'OR',
					'USE_PUSH_STOCKS' => [
						'RULE' => 'EMPTY',
						'VALUE' => false,
					],
					'USE_PUSH_PRICES' => [
						'RULE' => 'EMPTY',
						'VALUE' => false,
					],
				],
			]
		];
	}

	protected function getFeedEnum()
	{
		$result = [];

		$query = Market\Export\Setup\Table::getList([
			'select' => [ 'ID', 'NAME', 'GROUP_NAME' => 'GROUP.NAME' ],
			'order' => [ 'GROUP.ID' => 'ASC', 'ID' => 'ASC' ],
		]);

		while ($row = $query->fetch())
		{
			$result[] = [
				'ID' => $row['ID'],
				'VALUE' => sprintf('[%s] %s', $row['ID'], $row['NAME']),
				'GROUP' => $row['GROUP_NAME'],
			];
		}

		return $result;
	}

	protected function getProductPriceFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		$result = parent::getProductPriceFields($environment, $siteId);
		$overrides = [
			'SORT' => 2230,
		];

		if (!Market\Config::isExpertMode())
		{
			$overrides['DEPEND'] = [
				'USE_PUSH_PRICES' => [
					'RULE' => Market\Utils\UserField\DependField::RULE_EMPTY,
					'VALUE' => false,
				],
			];
		}

		return $this->applyFieldsOverrides($result, $overrides);
	}

	protected function getProductSelfTestFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		$result = [];
		$defaults = [
			'TAB' => 'STORE',
			'GROUP' => static::getLang('TRADING_SERVICE_MARKETPLACE_OPTION_SELF_TEST'),
			'SORT' => 2300,
		];

		foreach ($this->getSelfTestOption()->getFields($environment, $siteId) as $name => $field)
		{
			$key = sprintf('SELF_TEST[%s]', $name);

			$result[$key] = $field + $defaults;
		}

		return $result;
	}

	protected function getStatusInFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		$result = parent::getStatusInFields($environment, $siteId);

		if (isset($result['STATUS_IN_PROCESSING_SHIPPED']))
		{
			$result['STATUS_IN_PROCESSING_SHIPPED']['DEPRECATED'] = 'Y';
		}

		return $result;
	}

	protected function getStatusShipmentFields(TradingEntity\Reference\Environment $environment, $siteId)
	{
		$environmentStatus = $environment->getStatus();
		$variants = $environmentStatus->getVariants();
		$enum = $environmentStatus->getEnum($variants);
		$meaningfulMap = $environmentStatus->getMeaningfulMap();

		return [
			'STATUS_SHIPMENT_CONFIRM' => [
				'TYPE' => 'enumeration',
				'TAB' => 'STATUS',
				'GROUP' => static::getLang('TRADING_SERVICE_MARKETPLACE_GROUP_STATUS_SHIPMENT'),
				'NAME' => static::getLang('TRADING_SERVICE_MARKETPLACE_STATUS_SHIPMENT_CONFIRM'),
				'VALUES' => $enum,
				'SETTINGS' => [
					'DEFAULT_VALUE' =>
							isset($meaningfulMap[Market\Data\Trading\MeaningfulStatus::DEDUCTED])
								? $meaningfulMap[Market\Data\Trading\MeaningfulStatus::DEDUCTED]
								: null,
					'STYLE' => 'max-width: 300px;',
				],
				'SORT' => 3000,
			],
		];
	}

	protected function getFieldsetMap()
	{
		return [
			'SELF_TEST' => Options\SelfTestOption::class,
		];
	}
}