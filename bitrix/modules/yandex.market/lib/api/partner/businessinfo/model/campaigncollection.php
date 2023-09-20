<?php

namespace Yandex\Market\Api\Partner\BusinessInfo\Model;

use Yandex\Market;

/** @method Campaign current() */
class CampaignCollection extends Market\Api\Reference\Collection
{
	public static function getItemReference()
	{
		return Campaign::class;
	}
}