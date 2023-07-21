<?php

namespace VampiRUS\Yookassa;

class OldApi {

	static function getHost(){
		$domain = $_SERVER['SERVER_NAME'];
		$domain = str_ireplace(array('http://','https://'),'',rtrim($domain,'/'));

		return self::getSchema().$domain;
	}


	static function checkHTTPS() {
		if(!empty($_SERVER['HTTPS'])){
			if($_SERVER['HTTPS'] !== 'off')
				return true; //https
			else
				return false; //http
		} elseif(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
			return true;
		} else
			if($_SERVER['SERVER_PORT'] == 443)
				return true; //https
			else
				return false; //http
	}

	static function getSchema() {
		return (self::checkHTTPS())?'https://':'http://';
	}

	static function answer($action, $invoiceId, $code)
	{
		switch ($action)
		{
			case 'checkOrder':
				return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".'<checkOrderResponse performedDatetime="'.date(DATE_ATOM).'" code="'.(int)$code.'" invoiceId="'.$invoiceId.'" shopId="'.\CVampiRUSYandexKassaPayment::getShopId().'"/>';
			case 'paymentAviso':
				return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".'<paymentAvisoResponse performedDatetime="'.date(DATE_ATOM).'" code="'.(int)$code.'" invoiceId="'.$invoiceId.'" shopId="'.\CVampiRUSYandexKassaPayment::getShopId().'"/>';
			default:
				return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".'<'.$action.'Response performedDatetime="'.date(DATE_ATOM).'" code="'.(int)$code.'" invoiceId="'.$invoiceId.'" shopId="'.\CVampiRUSYandexKassaPayment::getShopId().'"/>';
		}
		self::inline();
	}

	static function sendRequest($post) {
		global $DB;
		$res = false;
		foreach($post['action'] as $id => $action) {
			$res = $DB->Query("SELECT * FROM vampirus_yandexkassa WHERE ID=".intval($id));
			$data = $res->Fetch();
			$arOrder = CSaleOrder::GetByID($data['ORDER_ID']);
			if(class_exists("Bitrix\Sale\Internals\PaymentTable")) {
				$payment = \Bitrix\Sale\Internals\PaymentTable::getRow(
					array(
						'select' => array('*'),
						'filter' => array(
							'ORDER_ID' => $data['ORDER_ID'],
							'SUM' => $data['AMOUNT'],
							'PAID'=> 'Y',
							'!PAY_SYSTEM_ID' => \Bitrix\Sale\PaySystem\Manager::getInnerPaySystemId()
						)
					)
				);
				CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"],"",array(), $payment);
			} else {
				CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"]);
			}

			$cert = CSalePaySystemAction::GetParamValue("HOLD_CERT","");
			$key = CSalePaySystemAction::GetParamValue("HOLD_KEY","");
			$pass = CSalePaySystemAction::GetParamValue("HOLD_CERT_PASS","");

			$server = (self::demoMode())?"penelope-demo.yamoney.ru:8083":"shop.yookassa.ru";
			//$date = new DateTime();
			//$dateTime = $date->format("Y-m-d") . "T" . $date->format("H:i:s") . ".000Z";

			if ($action == self::STATUS_CANCELLED) {
				$requestParams = array(
					'requestDT'				=> date('c'),
					'orderId'				=> $data['INVOICE_ID'],
					'ym_merchant_receipt'	=> $data['RECEIPT']
				);
				$url = "https://".$server."/webservice/mws/api/cancelPayment";
				$errText = GetMessage('VAMPIRUS.YANDEXKASSA_CANCEL_ERROR');
				$requestBody = http_build_query($requestParams);
			} elseif ($action == self::STATUS_CONFIRMED) {
				$requestParams = array(
					'requestDT'				=> date('c'),
					'orderId'				=> $data['INVOICE_ID'],
					'amount'				=> $data['AMOUNT'],
					'currency'				=> 'RUB',
					'ym_merchant_receipt'	=> $data['RECEIPT']
				);
				$requestBody = http_build_query($requestParams);
				$url = "https://".$server."/webservice/mws/api/confirmPayment";
				$errText = GetMessage('VAMPIRUS.YANDEXKASSA_CONFIRM_ERROR');
			} elseif ($action == self::STATUS_RETURNED) {
				$cause = (ToUpper(SITE_CHARSET) != "UTF-8")?iconv('cp1251', 'utf-8', $post['cause'][$id]):$post['cause'][$id];
				$xml_request = '<?xml version="1.0" encoding="UTF-8"?><returnPaymentRequest'
					. ' clientOrderId="' .$data['ID'] . '"'
					. ' invoiceId="' . $data['INVOICE_ID'] . '"'
					. ' amount="' . $data['AMOUNT'] . '"'
					. ' currency="10643"'
					. ' cause="' . htmlspecialchars($cause) . '"'
					. ' shopId="' . self::getShopId() . '"'
					. ' requestDT="' . date('c') . '"'
					. '/>';
				$requestBody = self::encryptPKCS7(
					$cert,
					$key,
					$pass,
					$xml_request);
				if($requestBody == 0) return 0;
				$url = "https://".$server."/webservice/mws/api/returnPayment";
				$errText = GetMessage('VAMPIRUS.YANDEXKASSA_RETURN_ERROR');
			}
			$result = 0;
			if($url) {

				$curl = curl_init();
				$params = array(
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_HTTPHEADER => array('Content-type: application/' . "x-www-form-urlencoded"),
					CURLOPT_URL => $url,
					CURLOPT_POST => 1,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_SSLCERT => $cert,
					CURLOPT_SSLKEY => $key,
					CURLOPT_SSLCERTPASSWD => $pass,
					CURLOPT_SSL_VERIFYHOST => false,
					CURLOPT_VERBOSE => 1,
					CURLOPT_POSTFIELDS => $requestBody,
					CURLOPT_FORBID_REUSE => TRUE,
					CURLOPT_FRESH_CONNECT => TRUE
				);

				curl_setopt_array($curl, $params);
				$result = curl_exec($curl);
				if (!$result) {
					self::$errorMsg = $errText.curl_error($curl);
					$result = 0;
				} else {
					$xml = simplexml_load_string($result);
					if(!$xml){
						self::$errorMsg = GetMessage('VAMPIRUS.YANDEXKASSA_ERROR_PARSE_ANSWER');
						file_put_contents(__DIR__.'/log.txt', $result, FILE_APPEND);
						$result = 0;
					}
					if($xml['status']=='3') {
						self::$errorMsg = GetMessage('VAMPIRUS.YANDEXKASSA_REQUEST_ERROR_'.(string)$xml['error']);
						$result = 0;
					} elseif($xml['status']=='1') {
						self::$errorMsg = GetMessage('VAMPIRUS.YANDEXKASSA_NEED_REPEAT');
						$result = 0;
					} elseif($xml['status']=='0') {
						$result = 1;
					} else {
						$result = 0;
						self::$errorMsg = GetMessage('VAMPIRUS.YANDEXKASSA_UNKNOWN_STATUS');
						file_put_contents(__DIR__.'/log.txt', var_export($result,true), FILE_APPEND);
					}
				}
				curl_close($curl);
			}
			if ($result) {
				$DB->Query("UPDATE vampirus_yandexkassa SET
				STATUS='".$DB->ForSql($action)."'
				WHERE ID=".intval($id));
			}
		}

		return $result;
	}

	static function encryptPKCS7($cert, $key, $pass, $data) {
		self::$errorMsg = '';
		$descriptorspec = array(
			0 => array("pipe", "r"),
			1 => array("pipe", "w"),
		);
		$descriptorspec[2] = $descriptorspec[1];
		$opensslCommand = 'openssl smime -sign -signer ' . $cert .
			' -inkey ' . $key .
			' -nochain -nocerts -outform PEM -nodetach -passin pass:'.$pass;
		$process = proc_open($opensslCommand, $descriptorspec, $pipes);
		if (is_resource($process)) {
			fwrite($pipes[0], $data);
			fclose($pipes[0]);
			$pkcs7 = stream_get_contents($pipes[1]);
			fclose($pipes[1]);
			$resCode = proc_close($process);
			if ($resCode != 0) {
				self::$errorMsg = 'OpenSSL call failed:' . $resCode . '\n' . $pkcs7;
			}
			return $pkcs7;
		}
		self::$errorMsg = GetMessage('VAMPIRUS.YANDEXKASSA_ERROR_POPEN');
		return 0;
	}
}