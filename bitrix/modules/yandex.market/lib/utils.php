<?php

namespace Yandex\Market;

use Bitrix\Main;
use Yandex\Market;

class Utils
{
	public static function sklon($number, $titles)
	{
		$cases = array (2, 0, 1, 1, 1, 2);
		return $titles[ ($number%100>4 && $number%100<20)? 2: $cases[min($number%10, 5)] ];
	}

	public static function htmlEscape($string)
	{
		static $search = [ '"', '<', '>' ];
		static $replace = [ '&quot;', '&lt;', '&gt;' ];

		return str_replace($search, $replace, $string);
	}

	public static function jsonEncode($data, $options = null)
	{
		$result = Main\Web\Json::encode($data, $options);

		// return string to site charset

		if (!Main\Application::isUtfMode())
		{
			$result = Main\Text\Encoding::convertEncoding($result, 'UTF-8', SITE_CHARSET);
		}

		return $result;
	}

	public static function prettyPrintXml($contents, $indent = 0)
	{
		if ($contents instanceof \SimpleXMLElement)
		{
			$node = $contents;
		}
		else
		{
			$node = new \SimpleXMLElement('<?xml version="1.0" encoding="' . LANG_CHARSET . '" ?>' . $contents);
		}

		$padding = str_pad('', $indent, ' ');
		$hasChildren = false;

		foreach ($node->children() as $child)
		{
			if ($child instanceof \SimpleXMLElement)
			{
				$hasChildren = true;
				break;
			}
		}

		if ($hasChildren)
		{
			$result = $padding . '<' . $node->getName();

			foreach ($node->attributes() as $attributeName => $attributeValue)
			{
				$result .= ' ' . $attributeName . '="' . $attributeValue . '"';
			}

			$result .= '>';

			foreach ($node->children() as $child)
			{
				$result .= PHP_EOL . static::prettyPrintXml($child, $indent + 4);
			}

			$result .= PHP_EOL . $padding;
			$result .= '</' . $node->getName() . '>';
		}
		else
		{
			$result = $padding . $node->asXML();
		}

		if ($indent === 0 && !Main\Application::isUtfMode())
		{
			$result = Main\Text\Encoding::convertEncoding($result, 'UTF-8', LANG_CHARSET);
		}

		return $result;
	}

	public static function isCli()
	{
		if (defined('BX_CRONTAB') && BX_CRONTAB === true)
		{
			$result = true;
		}
		else if (defined('CHK_EVENT') && CHK_EVENT === true) // bitrix hack way
		{
			$result = true;
		}
		else
		{
			$sapi = php_sapi_name();

			$result = (Market\Data\TextString::getPosition($sapi, 'cli') === 0);
		}

		return $result;
	}

	public static function isAgentUseCron()
	{
		return (
			Main\Config\Option::get('main', 'agents_use_crontab', 'N') === 'Y' // agents use crontab
			|| Main\Config\Option::get('main', 'check_agents', 'Y') !== 'Y' // auto call agents disabled
			|| (defined('BX_CRONTAB_SUPPORT') && BX_CRONTAB_SUPPORT === true)
		);
	}

	public static function isOnlySaleDiscount()
	{
		return ((string)Main\Config\Option::get('sale', 'use_sale_discount_only') === 'Y');
	}
}