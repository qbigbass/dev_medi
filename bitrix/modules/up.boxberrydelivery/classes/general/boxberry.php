<?

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\IO\File;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Text\Encoding;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Diag\Debug;

Loc::loadMessages(__FILE__);
class CBoxberry
{
    private const DADATA_API_URL = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address';
    private const DADATA_API_KEY = 'Token a105367bc6479ffb2a355fad7536e0fb504c1b97';
    private const LOG_DIRECTORY = '/bitrix/cache/log/';
    private const LOG_FILE = 'boxberrydelivery.log';
    public static $moduleId = "up.boxberrydelivery";
    public static $apiToken;
    public static $apiUrl;
    private static $httpClient;
    private static $cache;

    public static function initApi()
    {
        $apiToken = trim(Option::get(self::$moduleId, 'API_TOKEN'));
        if (!empty($apiToken)) {
            self::$apiToken = $apiToken;
        } else {
            return false;
        }

        self::$apiUrl = trim(Option::get(self::$moduleId, 'API_URL'));

        return true;
    }

    private static function getHttpClient()
    {
        if (!isset(self::$httpClient)) {
            self::$httpClient = new HttpClient();
        }

        return self::$httpClient;
    }

    private static function getCache()
    {
        if (!isset(self::$cache)) {
            self::$cache = Cache::createInstance();
        }

        return self::$cache;
    }

    private static function makeHttpRequest($url, $method = 'POST', $params = [], $headers = [], $cacheTime = 86400)
    {
        $cacheKey = md5($url . $method . serialize($params) . serialize($headers));

        $params = self::convertEncoding($params);

        $cache = self::getCache();

        if ($cache->startDataCache($cacheTime, $cacheKey)) {

            $http = self::getHttpClient();

            if ($method === 'POST') {

                $http->clearHeaders();

                $http->setHeader('Content-Type', 'application/x-www-form-urlencoded');

                foreach ($headers as $headerName => $headerValue) {
                    $http->setHeader($headerName, $headerValue);
                }

                if (is_array($params)) {
                    $postData = http_build_query($params, '', '&');
                } else {
                    $postData = $params;
                }

                $postData = self::convertEncoding($postData);
                $response = $http->post($url, $postData);
            } elseif ($method === 'GET') {
                $url .= '?' . http_build_query($params, '', '&');
                $response = $http->get($url);
            } else {
                return false;
            }

            if ($response) {
                $responseBody = $http->getResult();
                $statusCode = $http->getStatus();

                if ($statusCode === 200) {
                    $cache->endDataCache($responseBody);
                } else {
                    self::logRequest($url, $method, $params, $headers, $responseBody);

                    return false;
                }
            } else {
                return false;
            }
        } else {
            $responseBody = $cache->getVars();
        }

        self::logRequest($url, $method, $params, $headers, $responseBody);

        try {
            return Json::decode($responseBody);
        } catch (Throwable $e) {
            return false;
        }
    }

    private static function convertEncoding($data, $targetCharset = 'UTF-8') {
        if (is_array($data)) {
            $convertedData = [];
            foreach ($data as $key => $value) {
                $convertedData[$key] = self::convertEncoding($value, $targetCharset);
            }
        } else {
            $sourceCharset = Encoding::detectUtf8($data) ? 'UTF-8' : 'CP1251';
            $convertedData = Encoding::convertEncoding($data, $sourceCharset, $targetCharset);
        }

        return $convertedData;
    }

    private static function logRequest($url, $method, $params, $headers, $responseBody)
    {
        $logFilePath = Application::getDocumentRoot() . self::LOG_DIRECTORY . self::LOG_FILE;

        if (Option::get(self::$moduleId, 'BB_LOG') === 'Y') {

            $logUrl = self::convertEncoding($url);
            $logMethod = self::convertEncoding($method);
            $logParams = self::convertEncoding($params);
            $logHeaders = self::convertEncoding($headers);
            $logResponseBody = self::convertEncoding($responseBody);

            $logMessage = "URL: $logUrl\n";
            $logMessage .= "Method: $logMethod\n";
            $logMessage .= "Headers: " . print_r($logHeaders, true) . "\n";
            $logMessage .= "Parameters: " . print_r($logParams, true) . "\n";
            $logMessage .= "Response Body: $logResponseBody\n";

            if (!File::isFileExists($logFilePath)) {
                Directory::createDirectory(Application::getDocumentRoot() . self::LOG_DIRECTORY);
            }

            Debug::writeToFile($logMessage, '', substr(self::LOG_DIRECTORY, 1) . self::LOG_FILE);
        } else {
            if (File::isFileExists($logFilePath)) {
                File::deleteFile($logFilePath);
            }
        }
    }

    public static function getKeyIntegration()
    {
        $params = [
            'token' => self::$apiToken,
            'method' => 'GetKeyIntegration'
        ];

        return self::makeHttpRequest(self::$apiUrl, 'GET', $params);
    }

    public static function deliveryCalculation($params)
    {
        $disableCache = Option::get(self::$moduleId, 'BB_DISABLE_CALC_CACHE') === 'Y';

        return self::makeHttpRequest(self::$apiUrl, 'POST', $params, [], $disableCache ? 0 : 86400);
    }

    public static function listCitiesFull()
    {
        $params = [
            'token' => self::$apiToken,
            'method' => 'ListCitiesFull',
        ];

        return self::makeHttpRequest(self::$apiUrl, 'GET', $params);
    }

    public static function widgetSettings()
    {
        $params = [
            'token' => self::$apiToken,
            'method' => 'WidgetSettings',
        ];

        return self::makeHttpRequest(self::$apiUrl, 'GET', $params);
    }

    public static function parselCreate($sdata)
    {
        $params = [
            'token' => self::$apiToken,
            'method' => 'ParselCreate',
            'sdata' => $sdata
        ];

        return self::makeHttpRequest(self::$apiUrl, 'POST', $params, [], 0);
    }

    public static function parselSend($ids)
    {
        $params = [
            'token' => self::$apiToken,
            'method' => 'ParselSend',
            'ImIds' => $ids,
        ];

        return self::makeHttpRequest(self::$apiUrl, 'GET', $params, [], 0);
    }

    public static function callApiDadata($address)
    {
        $params = [
            'query' => $address,
            'locations' => [
                [
                    'country' => '*',
                ]
            ]
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => self::DADATA_API_KEY
        ];

        return self::makeHttpRequest(self::DADATA_API_URL, 'POST', Json::encode($params, JSON_UNESCAPED_UNICODE), $headers);
    }

    public static function saveFilesFromApi($request, $orderId)
    {
        $pdfDir = '/bitrix/pdf/';
        $serverPdfDir = Application::getDocumentRoot() . $pdfDir;
        $pathToPdf = $serverPdfDir . $orderId . '-' . date('d_m_Y-h_i_s') . '.pdf';
        $linkToPdf = $pdfDir . $orderId . '-' . date('d_m_Y-h_i_s') . '.pdf';
        if (!Directory::isDirectoryExists($serverPdfDir)) {
            Directory::createDirectory($serverPdfDir);
        }

        if (self::getHttpClient()->download($request, $pathToPdf)){
            return $linkToPdf;
        }

        return '';
    }

    public static function GetFullOrderData($orderId)
    {
        if ((int)$orderId <= 0) {
            return false;
        }

        $order = CSaleOrder::GetByID($orderId);
        $bxbOrderInfo = CBoxberryOrder::GetByOrderId($orderId);
        $order["PVZ_CODE"] = $bxbOrderInfo["PVZ_CODE"];

        if (!$order) {
            return false;
        }

        $dbProps = CSaleOrderPropsValue::GetOrderProps($orderId);
        while ($prop = $dbProps->Fetch()) {
            $order['PROPS'][$prop['ORDER_PROPS_ID']] = $prop;
        }

        $order['ITEMS'] = [];

        $dbBasket = CSaleBasket::GetList(['ID' => 'ASC'], ['ORDER_ID' => $orderId]);
        while ($arItem = $dbBasket->Fetch()) {
            $order['ITEMS'][] = $arItem;
        }

        return $order;
    }

    public static function makePropsArray($order)
    {
        $arReturn = [];

        $arReturn['VID'] = (($order['DELIVERY_ID'] === 'boxberry:PVZ' || $order['DELIVERY_ID'] === 'boxberry:PVZ_COD') ? 1 : 2);
        $arOptFields = Option::getForModule(self::$moduleId);

        foreach ($arOptFields as $key => $optName) {
            foreach ((array)$order['PROPS'] as $curProp) {
                if ($optName === $curProp['CODE']) {
                    $arReturn[$key] = $curProp["VALUE"];
                }
            }
        }

        return (count($arReturn) > 0 ? $arReturn : false);
    }

    public static function changeAddress ($ORDER_ID=NULL, $address=NULL)
    {
        if (!empty($address)){
            $dbProps = CSaleOrderPropsValue::GetOrderProps($ORDER_ID);
            $address_prop_bb = Option::get(self::$moduleId, 'BB_ADDRESS');
            $address = Encoding::convertEncodingToCurrent($address);


            while($prop = $dbProps->Fetch())
            {
                if ($prop['CODE'] == $address_prop_bb)
                {
                    CSaleOrderPropsValue::Update($prop['ID'], array("ORDER_ID"=>$ORDER_ID, "CODE"=>$prop['CODE'] ,"VALUE"=>$address));
                }
            }
        }

    }

    public static function updateOrder($pvzId, $orderId = null, $address = null)
    {
        $currentDate = date('d.m.Y H:i:s');
        $arFields = [
            'ORDER_ID' => $orderId,
            'PVZ_CODE' => Encoding::convertEncodingToCurrent($pvzId),
            'STATUS_DATE' => $currentDate
        ];

        CBoxberryOrder::Update($orderId, $arFields);
        self::changeAddress($orderId, $address);
        echo true;

    }

    public static function savePvz($pvzId)
    {
        $_SESSION['selPVZ'] = Encoding::convertEncodingToCurrent($pvzId);
    }

    public static function removePvz()
    {
        unset($_SESSION['selPVZ']);
    }

    public static function checkPvz()
    {
        $_SESSION['checkPVZ'] = true;
    }

    public static function disableCheckPvz()
    {
        unset($_SESSION['checkPVZ']);
    }

    public static function addWidgetJs()
    {
        $widgetUrl = trim(Option::get(self::$moduleId, 'WIDGET_URL'));

        if (!$widgetUrl) {
            $widgetUrl = 'https://points.boxberry.de/js/boxberry.js';
        }

        Asset::getInstance()->addJs($widgetUrl);
    }

}

?>