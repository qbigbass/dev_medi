<?php
namespace VampiRUS\Yookassa;

use \Bitrix\Main;
use \Bitrix\Main\Web\HttpClient;
use \Bitrix\Sale\PaySystem;
use \Bitrix\Main\Localization;

class Api {
	static function send($url, array $headers, array $params = array(), $method = 'POST') {
		if (function_exists("curl_init")) {
			return static::sendCurl($url, $headers, $params, $method);
		}
		$result = new PaySystem\ServiceResult();

		$httpClient = new HttpClient();
		foreach ($headers as $name => $value) {
			$httpClient->setHeader($name, $value);
		}

		$postData = null;
		if ($params) {
			$postData = static::JSencode($params);
		}

		if (class_exists('Bitrix\Sale\PaySystem\Logger')) {
			PaySystem\Logger::addDebugInfo('Yandex.Checkout: request data: ' . $postData);
		}
		if ($method == 'POST') {
			$response = $httpClient->post($url, $postData);
		} else {
			$response = $httpClient->get($url);
		}

		if ($response === false) {
			$errors = $httpClient->getError();
			foreach ($errors as $code => $message) {
				$result->addError(new Main\Error($message, $code));
			}

			return $result;
		}

		if (class_exists('Bitrix\Sale\PaySystem\Logger')) {
			PaySystem\Logger::addDebugInfo('Yandex.Checkout: response data: ' . $response);
		}

		$response = static::JSdecode($response);

		$httpStatus = $httpClient->getStatus();
		if ($httpStatus == 200) {
			$result->setData($response);
		} elseif ($httpStatus == 202) {
			$secondsToSleep = ceil($response['retry_after'] / 1000);
			sleep($secondsToSleep);

			$result = self::send($url, $headers, $params);
		} elseif ($httpStatus != 201) {
			$error = Localization\Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_HTTP_STATUS_' . $httpStatus);
			if (isset($response['type']) && $response['type'] == 'error') {
				$result->addError(new Main\Error($response['description']));
			} elseif ($error) {
				$result->addError(new Main\Error($error));
			}
		}

		return $result;
	}


	static function sendCurl($url, array $headers, array $data = array(), $method = 'POST')
	{
		$result = new PaySystem\ServiceResult();
		$h = [];
		foreach($headers as $name => $header) {
			$h[] = "$name: $header";
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $h);
		if (!empty($data) || $method == 'POST') {
			$data = static::JSencode($data);
			if ($data == "[]") {
				$data = "{}";
			}
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}

		$response = curl_exec($ch);
		$code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		$response = static::JSdecode($response);
		if ($code == 200) {
			$result->setData($response);
		} elseif ($code == 202) {
			$secondsToSleep = ceil($response->retry_after / 1000);
			sleep($secondsToSleep);
			return static::sendCurl($url, $headers, $data,$method);
		} elseif ($code != 201) {
			$error = 'Error: ' . $code;
			if ($code == 0) {
				$error = " ".curl_error($ch);
			}
			if (isset($response['type']) && $response['type'] == 'error') {
				$error .= ' ' . $response['description'];
			}
			$result->addError(new Main\Error($error));
		}
		return $result;
	}



	/**
	 * @return string
	 */
	static function getIdempotenceKey()
	{
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			mt_rand(0, 0xffff),
			mt_rand(0, 0x0fff) | 0x4000,
			mt_rand(0, 0x3fff) | 0x8000,
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	/**
	 * @param array $config
	 * @return array
	 */
	static function getHeaders($config)
	{
		return array(
			'Content-Type'  => 'application/json',
			'Authorization' => 'Basic ' . self::getBasicAuthString($config),
		);
	}

	static function getOption($config, $name)
	{
		return isset($config[$name])?$config[$name]:null;
	}

	/**
	 * @param array $config
	 * @return string
	 */
	public static function getBasicAuthString($config)
	{

		return base64_encode(
			trim(self::getOption($config, 'YANDEX_CHECKOUT_SHOP_ID')) .
			':' .
			trim(self::getOption($config, 'YANDEX_CHECKOUT_SECRET_KEY'))
		);
	}

	/**
	 * @param array $data
	 * @return mixed
	 * @throws Main\ArgumentException
	 */
	public static function JSencode($data)
	{
		return Main\Web\Json::encode($data, JSON_UNESCAPED_UNICODE);
	}

	/**
	 * @param string $data
	 * @return mixed
	 */
	public static function JSdecode($data)
	{
		try
		{
			return Main\Web\Json::decode($data);
		} catch (Main\ArgumentException $exception) {
			return false;
		}
	}
}