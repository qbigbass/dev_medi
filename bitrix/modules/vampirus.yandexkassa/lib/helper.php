<?php

namespace VampiRUS\Yookassa;

class Helper
{
	public static function correctDimmensoin($items, $roundCoef)
	{
		foreach ($items as &$item) {
			$item['quantity']        = number_format(round($item['quantity'] / 1000, 3), 3, '.', '');
			$item['amount']['value'] = number_format(round($item['amount']['value'] / $roundCoef, 2), 2, '.', '');
		}
		return $items;
	}


	public static function normalizeReceiptItems($items, $resultTotal) {
		$currentSum = 0;
		foreach($items as $item) {
			$currentSum += $item['amount']['value']*$item['quantity']/1000;
		}

		if ($resultTotal != 0 && $resultTotal != $currentSum) {
			$coefficient = $resultTotal / $currentSum;
			$realprice   = 0;
			$aloneId     = null;
			foreach ($items as $index => &$item) {
				$item['amount']['value'] = round($coefficient * $item['amount']['value']);
				$realprice += round($item['amount']['value'] * $item['quantity'] / 1000);
				if ($aloneId === null && $item['quantity'] == 1000) {
					$aloneId = $index;
				}

			}
			unset($item);
			if ($aloneId === null) {
				foreach ($items as $index => $item) {
					if ($aloneId === null && $item['quantity'] > 1000) {
						$aloneId = $index;
						break;
					}
				}
			}
			if ($aloneId === null) {
				$aloneId = 0;
			}

			$diff = $resultTotal - $realprice;

			if (abs($diff) >= 0.001) {
				if ($items[$aloneId]['quantity'] == 1000) {
					$items[$aloneId]['amount']['value'] = round($items[$aloneId]['amount']['value'] + $diff);
				} elseif (
					count($items) == 1
					&& abs(round($resultTotal / $items[$aloneId]['quantity']) - $resultTotal / $items[$aloneId]['quantity']) < 0.001
				) {
					$items[$aloneId]['amount']['value'] = round($resultTotal / $items[$aloneId]['quantity']);
				} elseif ($items[$aloneId]['quantity'] > 1000) {
					$tmpItem = $items[$aloneId];
					$item    = array(
						"quantity"        => 1000,
						"amount"          => array('value' => round($tmpItem['amount']['value'] + $diff), 'currency' => 'RUB'),
						"vat_code"        => $tmpItem['vat_code'],
						"description"     => $tmpItem['description'],
						"payment_subject" => $tmpItem['payment_subject'],
						"payment_mode"    => $tmpItem['payment_mode'],
					);
					if (isset($tmpItem['agent_type'])) {
						$item['agent_type'] = $tmpItem['agent_type'];
					}
					$items[$aloneId]['quantity'] -= 1000;
					array_splice($items, $aloneId + 1, 0, array($item));
				} else {
					$items[$aloneId]['amount']['value'] = round($items[$aloneId]['amount']['value'] + $diff / ($items[$aloneId]['quantity'] / 1000));

				}
			}
		}
		return $items;
	}

	static function buildTag1162(string $markingCode, string $markingCodeGroup)
	{
		list($gtin, $serial, ) = self::parseMarkingCode($markingCode);

		return
			//self::convertToBinaryFormat($markingCodeGroup, 2).' '.
			'44 4D '.
			self::convertToBinaryFormat($gtin, 6).' '.
			self::convertCharsToHex($serial);
	}

	/**
	 * @param $code
	 * @return array
	 */
	private static function parseMarkingCode(string $code)
	{
		$gtin = substr($code, 2, 14);
		$serial = substr($code, 18, 13);
		$reserve = substr($code, 27);

		return [$gtin, $serial, $reserve];
	}

	/**
	 * @param $string
	 * @param $size
	 * @return string
	 */
	protected static function convertToBinaryFormat($string, $size)
	{
		$result = '';

		for ($i = 0; $i < $size; $i++)
		{
			$hex = dechex(($string >> (8 * $i)) & 0xFF);
			if (strlen($hex) == 1)
			{
				$hex = '0'.$hex;
			}

			if ($i !== 0)
			{
				$result = ' '.$result;
			}

			$result = ToUpper($hex).$result;
		}

		return $result;
	}

	/**
	 * @param $string
	 * @return string
	 */
	protected static function convertCharsToHex($string)
	{
		$result = '';

		for ($i = 0, $len = strlen($string); $i < $len; $i++)
		{
			$hex = dechex(ord($string[$i]));
			if (strlen($hex) == 1)
			{
				$hex = '0'.$hex;
			}

			$result .= ToUpper($hex);

			if ($i !== $len - 1)
			{
				$result .= ' ';
			}
		}

		return $result;
	}

	static function getMeasureCode($basketItem)
	{
		$measure = $basketItem->getField('MEASURE_CODE');
		return MeasureCodeToStringMapper::getStringValue($measure);
	}
}