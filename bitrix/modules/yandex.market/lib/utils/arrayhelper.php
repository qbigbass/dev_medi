<?php

namespace Yandex\Market\Utils;

class ArrayHelper
{
	public static function column(array $array, $column)
	{
		$result = [];

		foreach ($array as $key => $values)
		{
			if (!isset($values[$column])) { continue; }

			$result[$key] = $values[$column];
		}

		return $result;
	}
}