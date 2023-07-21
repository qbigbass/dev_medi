<?php

namespace Yandex\Market\Component\Plain;

use Yandex\Market;
use Bitrix\Main;

abstract class EditForm extends Market\Component\Base\EditForm
{
	use Market\Reference\Concerns\HasLang;

	protected $fields;

	protected static function includeMessages()
	{
		Main\Localization\Loc::loadMessages(__FILE__);
	}

	public function prepareComponentParams($params)
	{
		$params['FIELDS'] = $this->prepareFields($params['FIELDS']);
		$params['TABS'] = $this->prepareTabs($params['TABS'], $params['FIELDS']);

		return $params;
	}

	protected function prepareFields($fields)
	{
		$fields = $this->extendFields($fields);
		$fields = $this->sortFields($fields);

		return $fields;
	}

	protected function extendFields($fields)
	{
		$result = [];

		foreach ($fields as $name => $field)
		{
			$userField = $field;

			if (!isset($field['USER_TYPE']) && isset($field['TYPE']))
			{
				$userField['USER_TYPE'] = Market\Ui\UserField\Manager::getUserType($field['TYPE']);
			}

			if (
				isset($userField['HIDDEN'], $userField['USER_TYPE']['CLASS_NAME'])
				&& $userField['HIDDEN'] === 'H'
				&& method_exists($userField['USER_TYPE']['CLASS_NAME'], 'GetList')
			)
			{
				$className = $userField['USER_TYPE']['CLASS_NAME'];
				$values = $className::GetList($userField);
				$values = Market\Ui\UserField\Helper\Enum::toArray($values);

				$userField['HIDDEN'] = empty($values) ? 'Y' : 'N';
			}

			$userField += [
				'TAB' => 'COMMON',
				'MULTIPLE' => 'N',
				'EDIT_IN_LIST' => 'Y',
				'EDIT_FORM_LABEL' => $field['NAME'],
				'FIELD_NAME' => $name,
				'SETTINGS' => [],
			];

			$result[$name] = $userField;
		}

		return $result;
	}

	protected function sortFields($fields)
	{
		$fieldsWithSort = array_filter($fields, function($tab) { return isset($tab['SORT']); });

		if (count($fieldsWithSort) > 0)
		{
			uasort($fields, function($fieldA, $fieldB) {
				$sortA = isset($fieldA['SORT']) ? $fieldA['SORT'] : 5000;
				$sortB = isset($fieldB['SORT']) ? $fieldB['SORT'] : 5000;

				if ($sortA === $sortB) { return 0; }

				return $sortA < $sortB ? -1 : 1;
			});
		}

		return $fields;
	}

	protected function prepareTabs($tabs, $fields)
	{
		$tabs = $this->extendTabs($tabs, $fields);
		$tabs = $this->sortTabs($tabs);

		return $tabs;
	}

	protected function extendTabs($tabs, $fields)
	{
		$result = [];
		$usedFields = [];

		foreach ($tabs as $tabKey => $tab)
		{
			// fields

			if (!isset($tab['fields']))
			{
				$tabCode = !is_numeric($tabKey) ? $tabKey : 'COMMON';
				$tabFields = $this->getFieldCodesForTab($fields, $tabCode);

				$tab['fields'] = array_diff($tabFields, $usedFields);
			}

			$usedFields = array_merge($usedFields, $tab['fields']);

			// export

			$result[] = $tab;
		}

		return $result;
	}

	protected function sortTabs($tabs)
	{
		$tabsWithSort = array_filter($tabs, function($tab) { return isset($tab['sort']); });

		if (count($tabsWithSort) > 0)
		{
			uasort($tabs, function($tabA, $tabB) {
				$sortA = isset($tabA['sort']) ? $tabA['sort'] : 5000;
				$sortB = isset($tabB['sort']) ? $tabB['sort'] : 5000;

				if ($sortA === $sortB) { return 0; }

				return $sortA < $sortB ? -1 : 1;
			});
		}

		return $tabs;
	}

	protected function getFieldCodesForTab($fields, $tabCode)
	{
		$result = [];

		foreach ($fields as $fieldCode => $field)
		{
			$fieldTab = isset($field['TAB']) ? $field['TAB'] : 'COMMON';

			if ($fieldTab === $tabCode)
			{
				$result[] = $fieldCode;
			}
		}

		return $result;
	}

	public function modifyRequest($request, $fields)
	{
		return $this->sanitizeUserFields($request, $fields);
	}

	protected function sanitizeUserFields($request, $fields)
	{
		foreach ($fields as $fieldName => $userField)
		{
			if (!array_key_exists($fieldName, $request))
			{
				// nothing
			}
			else if (!empty($userField['SETTINGS']['READONLY']))
			{
				unset($request[$fieldName]);
			}
			else
			{
				$requestValue = $request[$fieldName];

				if ($userField['MULTIPLE'] === 'Y')
				{
					$sanitizedValues = [];
					$requestValue = is_array($requestValue) ? $requestValue : [];

					foreach ($requestValue as $requestValueItem)
					{
						$sanitizedValue = $this->sanitizeUserFieldValue($userField, $requestValueItem);

						if (!Market\Utils\Value::isEmpty($sanitizedValue))
						{
							$sanitizedValues[] = $sanitizedValue;
						}
					}

					if (!empty($sanitizedValues))
					{
						$request[$fieldName] = $sanitizedValues;
					}
					else
					{
						$request[$fieldName] = [];
					}
				}
				else
				{
					$request[$fieldName] = $this->sanitizeUserFieldValue($userField, $requestValue);
				}
			}
		}

		return $request;
	}

	protected function sanitizeUserFieldValue($userField, $value)
	{
		$result = $value;

		if (
			!empty($userField['USER_TYPE']['CLASS_NAME'])
			&& is_callable([$userField['USER_TYPE']['CLASS_NAME'], 'SanitizeFields'])
		)
		{
			$result = call_user_func(
				[$userField['USER_TYPE']['CLASS_NAME'], 'SanitizeFields'],
				$userField,
				$value
			);
		}

		return $result;
	}

	public function extend($data, array $select = [])
	{
		$result = $this->restoreDefaultsForHiddenFields($data, $select);

		return $result;
	}

	protected function restoreDefaultsForHiddenFields($data, array $select)
	{
		$fields = $this->getComponentResult('FIELDS');
		$result = $data;

		if (empty($select))
		{
			$select = array_keys($fields);
		}

		foreach ($select as $fieldName)
		{
			if (!isset($fields[$fieldName])) { continue; }

			$field = $fields[$fieldName];

			if (!empty($field['DEPEND_HIDDEN']) && isset($field['SETTINGS']['DEFAULT_VALUE']))
			{
				$fieldValue = array_key_exists($fieldName, $data) ? $data[$fieldName] : $field['VALUE'];

				if ($fieldValue === false)
				{
					$result[$fieldName] = $field['SETTINGS']['DEFAULT_VALUE'];
				}
			}
		}

		return $result;
	}

	public function validate($data, array $fields = null)
	{
		$result = new Main\Entity\Result();

		if ($fields !== null)
		{
			$this->validateUserFields($result, $data, $fields);
		}

		return $result;
	}

	protected function validateUserFields(Main\Entity\Result $result, $data, array $fields)
	{
		foreach ($fields as $fieldName => $userField)
		{
			if (!empty($userField['SETTINGS']['READONLY']) || !empty($userField['DEPEND_HIDDEN'])) { continue; }
			if (!empty($userField['HIDDEN']) && $userField['HIDDEN'] !== 'N') { continue; }

			$dataField = isset($data[$fieldName]) ? $data[$fieldName] : null;

			if ($userField['MULTIPLE'] === 'Y')
			{
				$values = is_array($dataField) ? $dataField : [];
			}
			else
			{
				$values = !Market\Utils\Value::isEmpty($dataField) ? [ $dataField ] : [];
			}

			if (!empty($values))
			{
				foreach ($values as $value)
				{
					$checkResult = $this->checkUserFieldValue($fieldName, $userField, $value);

					if (!$checkResult->isSuccess())
					{
						$result->addErrors($checkResult->getErrors());
					}
				}
			}
			else if ($userField['MANDATORY'] === 'Y')
			{
				if (isset($userField['DEPRECATED']) && $userField['DEPRECATED'] === 'Y') { continue; }

				$message = static::getLang('COMPONENT_PLAIN_EDIT_FORM_FIELD_REQUIRED', [
					'#FIELD_NAME#' => $userField['EDIT_FORM_LABEL'] ?: $fieldName
				]);
				$error = new Market\Error\EntityError($message, 0, [ 'FIELD' => $fieldName ]);

				$result->addError($error);
			}
		}
	}

	protected function checkUserFieldValue($fieldName, $userField, $value)
	{
		$result = new Main\Entity\Result();

		if (!empty($userField['USER_TYPE']['CLASS_NAME']) && is_callable([$userField['USER_TYPE']['CLASS_NAME'], 'CheckFields']))
		{
			$userErrors = call_user_func(
				[$userField['USER_TYPE']['CLASS_NAME'], 'CheckFields'],
				$userField,
				$value
			);

			if (!empty($userErrors) && is_array($userErrors))
			{
				foreach ($userErrors as $userError)
				{
					$error = new Market\Error\EntityError($userError['text'], 0, [ 'FIELD' => $fieldName ]);
					$result->addError($error);
				}
			}
		}

		return $result;
	}

	protected function sliceFieldsDependHidden($fields, $values)
	{
		$result = $values;

		foreach ($fields as $fieldName => $field)
		{
			if (empty($field['DEPEND_HIDDEN'])) { continue; }

			Market\Utils\Field::unsetChainValue($result, $fieldName, Market\Utils\Field::GLUE_BRACKET);
		}

		return $result;
	}

	protected function applyUserFieldsOnBeforeSave($fields, $values)
	{
		$result = $values;

		foreach ($fields as $fieldName => $field)
		{
			if (
				isset($field['USER_TYPE']['CLASS_NAME'])
				&& is_callable([$field['USER_TYPE']['CLASS_NAME'], 'onBeforeSave'])
			)
			{
				$userField = $field;
				$userField['ENTITY_VALUE_ID'] = $this->getComponentParam('PRIMARY') ?: null;
				$userField['VALUE'] = $this->component->getOriginalValue($field);

				$fieldValue = Market\Utils\Field::getChainValue($values, $fieldName, Market\Utils\Field::GLUE_BRACKET);
				$fieldValue = call_user_func(
					[$field['USER_TYPE']['CLASS_NAME'], 'onBeforeSave'],
					$userField,
					$fieldValue
				);

				Market\Utils\Field::setChainValue($result, $fieldName, $fieldValue, Market\Utils\Field::GLUE_BRACKET);
			}
		}

		return $result;
	}

	public function getFields(array $select = [], $item = null)
	{
		$result = $this->getAllFields();
		$result = $this->applyFieldsSelect($result, $select);
		$result = $this->applyFieldsDeprecated($result, $item);

		return $result;
	}

	protected function applyFieldsSelect(array $fields, array $select)
	{
		if (empty($select)) { return $fields; }

		$selectMap = array_flip($select);

		return array_intersect_key($fields, $selectMap);
	}

	protected function applyFieldsDeprecated(array $fields, $item = null)
	{
		if ($this->needShowDeprecated()) { return $fields; }

		$nextOverrides = [];

		foreach ($fields as &$field)
		{
			if (!empty($nextOverrides))
			{
				$field += $nextOverrides;
				$nextOverrides = [];
			}

			if (!isset($field['DEPRECATED']) || $field['DEPRECATED'] !== 'Y') { continue; }

			$value = $item !== null
				? Market\Utils\Field::getChainValue($item, $field['FIELD_NAME'], Market\Utils\Field::GLUE_BRACKET)
				: null;

			if (empty($value))
			{
				$field['HIDDEN'] = 'Y';
				$nextOverrides += array_intersect_key($field, [
					'INTRO' => true,
				]);
			}
		}
		unset($field);

		return $fields;
	}

	protected function needShowDeprecated()
	{
		$request = Main\Context::getCurrent()->getRequest();

		return $request->get('deprecated') === 'Y';
	}

	protected function getAllFields()
	{
		return (array)$this->getComponentParam('FIELDS');
	}

	public function getRequiredParams()
	{
		return [
			'FIELDS',
		];
	}
}