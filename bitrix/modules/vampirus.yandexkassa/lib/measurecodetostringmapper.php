<?php

namespace VampiRUS\Yookassa;

class MeasureCodeToStringMapper
{
	/**
	 * @var array
	 */
	private static $map = [
		'796' => 'piece',
		'163' => 'gram',
		'166' => 'kilogram',
		'168' => 'ton',
		'4' => 'centimeter',
		'5' => 'decimeter',
		'6' => 'meter',
		'51' => 'square_centimeter',
		'53' => 'square_decimeter',
		'55' => 'square_meter',
		'111' => 'milliliter',
		'112' => 'liter',
		'113' => 'cubic_meter',
		'245' => 'kilowatt_hour',
		'233' => 'gigacalorie',
		'359' => 'day',
		'356' => 'hour',
		'355' => 'minute',
		'354' => 'second',
		'256' => 'kilobyte',
		'257' => 'megabyte',
		'2553' => 'gigabyte',
		'2554' => 'terabyte',
	];

	private const UNKNOWN_TYPE = 'other';

	/**
	 * @param string|null $measureCode
	 * @return string
	 */
	public static function getStringValue(?string $measureCode): string
	{
		return self::$map[$measureCode] ?? self::UNKNOWN_TYPE;
	}
}
