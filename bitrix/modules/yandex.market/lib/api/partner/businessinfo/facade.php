<?php

namespace Yandex\Market\Api\Partner\BusinessInfo;

use Yandex\Market;
use Yandex\Market\Reference\Concerns;
use Yandex\Market\Api\Reference\HasOauthConfiguration;
use Yandex\Market\Psr\Log\LoggerInterface;
use Bitrix\Main;

class Facade
{
	use Concerns\HasMessage;

	const CACHE_TTL = 86400;

	public static function businessId(HasOauthConfiguration $options, LoggerInterface $logger = null)
	{
		$cache = Main\Application::getInstance()->getManagedCache();
		$cacheTtl = static::CACHE_TTL;
		$cacheKey = Market\Config::getLangPrefix() . 'BUSINESS_INFO_' . $options->getCampaignId();

		if ($cache->read($cacheTtl, $cacheKey))
		{
			$result = $cache->get($cacheKey);
		}
		else
		{
			$result = static::fetchBusinessId($options, $logger);

			$cache->set($cacheKey, $result);
		}

		return $result;
	}

	protected static function fetchBusinessId(HasOauthConfiguration $options, LoggerInterface $logger = null)
	{
		$request = new Request();

		$request->setLogger($logger);
		$request->setOauthClientId($options->getOauthClientId());
		$request->setOauthToken($options->getOauthToken()->getAccessToken());
		$request->setCampaignId($options->getCampaignId());

		$sendResult = $request->send();

		if (!$sendResult->isSuccess())
		{
			$errorMessage = implode(PHP_EOL, $sendResult->getErrorMessages());
			$exceptionMessage = static::getMessage('FETCH_FAILED', [ '#MESSAGE#' => $errorMessage ]);

			throw new Main\SystemException($exceptionMessage);
		}

		/** @var $response Response */
		$response = $sendResult->getResponse();

		return $response->getBusinessId();
	}
}