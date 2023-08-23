<?


use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\Parser;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;

class appLmx {

    private $phone;
    private $public_api_url;
    private $system_api_url;
    private $api_url;
    private $secret;
    private $authToken;
    private $client_id;
    private $merchantToken;


    public function __construct()
    {

        $this->secret = '5097fb9143ca40b99716a22501554de3'; // boy
        $this->public_api_url = 'https://medirus.loymax.tech'; // boy
        $this->client_id = 'OAmedi';
        $this->system_api_url = 'https://medirus.loymax.tech/api';
        $this->authToken = '';
        $this->merchantToken = '';
    }

    public function authClientToken($code)
    {
        //grant_type=authorization_code&client_id={app_id}&client_secret={app_secret}&redirect_uri={redirect_uri}&code={code}
        $method = '/authorizationService/token';

        $result = new Result();

        $response = $this->query('post', $method,
            [
                'client_id'=> $this->client_id,
                'grant_type'=>'authorization_code',
                'client_secret' => $this->secret,
                'redirect_uri'=>('https://www.medi-salon.ru/ajax/lmx/client/?action=return'),
                'code' => $code
            ], 0, 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());

            return ['result' => $result, 'status'=>'error'];
        }
        else {
            $data = $response->getData();

            $this->setAuthToken($data['access_token'], $data['expires_in']);

            return $data;
        }
    }

    public function authMerchantToken()
    {
        $method = '/authorizationService/token';

        $result = new Result();

        $response = $this->query('post', $method,  ['client_id'=> $this->client_id,'grant_type'=>'anonymous'], 0, 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());

            return ['result' => $result, 'status'=>'error'];
        }
        else {
            $data = $response->getData();

            $this->setMerchantToken($data['access_token'], $data['expires_in']);

            return $data;
        }
        return $result;
    }

    public function checkUser($phone='', $scope = 'account')
    {
        $method = '/authorizationService/oauth/authorize';

        $result = new Result();

        if (!empty($scope)  && is_array($scope))
        {
            $scope = implode('%20', $scope);
        }

        $parameters =  [
            'client_id'=> $this->client_id,
            'redirect_uri'=>'https://www.medi-salon.ru/ajax/lmx/client/?action=return',
            'response_type'=>'code',
            'scope'=>$scope
        ];

        $query_str = "client_id=".$this->client_id."&redirect_uri=https://www.medi-salon.ru/ajax/lmx/client/?action=return&response_type=code&scope=".$scope;

        $http = new HttpClient();

        $http->setHeader('Authorization', 'Bearer '.$this->merchantToken);
        $http->setHeader('Content-type', 'application/json; charset=utf-8');
        $http->setHeader('X-Identifier', $phone);

        $http->get($this->public_api_url.$method.'?'.$query_str);

        $status = $http->getStatus();
        $data = $http->getResult();

        if ($status != '200' && $status != '302' )
        {
            $result->addError(new Error("Error ".$status));

            return ['status' => 'not_found', 'result' => $result];
        }
        if ($status == '302')
        {
            $loc = explode("code=",$http->getHeaders()->get('location'));
            return ['status' => 'found', 'code'=> $loc[1]];
        }

        try {
            $data = $http->getResult();
            if (isset($data['error']) && $data['error'] != 'registered') {
                $result->addError(new Error($data['error']));

                return $result;
            }

            if (strlen($data) == '32')
            {
                return ['status' => 'found', 'code'=>$data];
            }
            else{
                return ['status' => 'not_found', 'error_text' => $data];
            }
        } catch (ArgumentException $e) {
        }


    }
    function getUserData() {

        $method = '/v1.2/User';
        $result = new Result();

        $response = $this->query('get', $method,  [], 1 );

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            wl('user data errors');
            wl($result);

        }
        else {

            $data = $response->getData();

            return $data;
        }

    }

    public function checkPhoneExists()
    {
        $method = '/publicapi/v1.2/User/Logins';

        $result = new Result();

        $response = $this->query('get', $method,  []);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
        }
        else {
            $data = $response->getData();

            return $data;
        }
        return $result;
    }

    public function setAuthToken($token, $expires = '')
    {
        if ($expires != '')
        {
             $_SESSION['lmxapp']['expires'] = time() + $expires;
        }

        $this->authToken = $token;

        $_SESSION['lmxapp']['token'] = $token;

        return true;
    }
    public function setMerchantToken($token, $expires = '')
    {
        if ($expires != '')
        {
             $_SESSION['lmxapp']['mexpires'] = time() + $expires;
        }

        $this->merchantToken = $token;

        $_SESSION['lmxapp']['mtoken'] = $token;

        return true;
    }
    public function getAuthToken()
    {
        return $this->authToken;
    }

    public function getLoymaxPrice($id, $iblock_id, $quantity = '1', $userid = 0)
    {
        global $USER;
        $price_id = $GLOBALS['medi']['price_id'][SITE_ID];
        $max_price_id = $GLOBALS['medi']['max_price_id'][SITE_ID];

        if ($userid == '0' && $USER->IsAuthorized()) {
            $userid = $USER->GetId();
        }

        $objDateTime = new DateTime('NOW');
        $purchaseDate = $objDateTime->format("Y-m-d\TH:i:s.v\Z");

        if (isset($_SESSION['lmxapp']['good'][$id]['purchaseId']) && $_SESSION['lmxapp']['good'][$id]['purchaseTime'] > time()-600)
        {
            $purchaseId = str_replace([" ", "."], "",  $_SESSION['lmxapp']['good'][$id]['purchaseId']);
            $purchaseDate = $_SESSION['lmxapp']['good'][$id]['purchaseDate'];
            $_SESSION['lmxapp']['good'][$id]['purchaseTime'] = time();
        }
        else
        {
            $purchaseId = str_replace([" ", "."], "",  rand(1, 10).microtime().$userid.$id);
            $_SESSION['lmxapp']['good'][$id]['purchaseId'] = $purchaseId;

            $_SESSION['lmxapp']['good'][$id]['purchaseTime'] = time();

            $_SESSION['lmxapp']['good'][$id]['purchaseDate'] = $purchaseDate;
        }
        if ($_SESSION['lmxapp']['mtoken'] && $_SESSION['lmxapp']['mexpires'] > time()){
            $this->setMerchantToken($_SESSION['lmxapp']['mtoken'], $_SESSION['lmxapp']['mexpires']);
        }
        else
        {
            $this->authMerchantToken();
        }
        if ($USER->IsAuthorized())
        {
            $obUser = $USER->GetByLogin($USER->GetLogin());
            if ($arUser = $obUser->Fetch()){

                $parsedPhone = Parser::getInstance()->parse($arUser['LOGIN']);
                $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));
                if (!$phone){
                    $parsedPhone = Parser::getInstance()->parse($arUser['PERSONAL_PHONE']);
                    $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));
                }
            }
            if ($phone != '' && strlen($phone) == 11)
            {
                $checkuser = $this->checkUser($phone);
                if ($checkuser['status'] == 'found')
                {
                    $authclientresult = $this->authClientToken($checkuser['code']);
                }
            }
        }

        $lines = [];

        $obItem = CIBlockElement::GetList([], ['IBLOCK_ID'=>$iblock_id, 'ID'=>$id,'ACTIVE'=>'Y'],
            false,false, ['ID', 'CATALOG_PRICE_'.$price_id, "PROPERTY_GTIN", "PROPERTY_LMX_GOODID", "NAME", "PROPERTY_CML2_ARTICLE"] );
        if ($exItem = $obItem->GetNext()) {

            $lines[] = [
                "position" => 1,
                "amount" => $exItem['CATALOG_PRICE_'.$price_id],
                "quantity" => $quantity,
                "cashback" => 0,
                "discount" => 0,
                "name" => $exItem['PROPERTY_CML2_ARTICLE_VALUE'],
                "price" => $exItem['CATALOG_PRICE_'.$price_id]
            ];
            if ($exItem['PROPERTY_LMX_GOODID_VALUE'] != '')
            {
                $lines[0]['goodsId'] = $exItem['PROPERTY_LMX_GOODID_VALUE'];
            }elseif ($exItem['PROPERTY_GTIN_VALUE'] != '')
            {
                $lines[0]['barcode'] = substr($exItem['PROPERTY_GTIN_VALUE'],1);
            }
        }



        if (!empty($lines))
        {

            $lmxItem = [];
            $qResult = $this->calculate($purchaseId, $purchaseDate, $lines);

            if ($qResult['result'] && $qResult['result']['state'] == 'Success')
            {
                foreach ($qResult['data'][0]['cheque']['lines'] as $line) {


                    $lmxItem = [
                        "BASE_PRICE" => ceil($line['amount']+$line['discount']),
                        "PRICE" => ceil($line['amount']/$line['quantity']),
                        "DISCOUNT" =>  ceil($line['discount']/$line['quantity']),
                        "PRICE_FORMATED" => CCurrencyLang::CurrencyFormat(ceil($line['amount']/$line['quantity']), 'RUB', true)
                    ];

                }
                return $lmxItem;
            }
        }
        return  false;
    }

    public function getLoymaxBasketPrices($prodid, $userid = 0, $prices = [], $purchaseId)
    {
        global $USER;
        $price_id = $prices['price_id'];
        $max_price_id = $prices['max_price_id'];;

        if ($userid == '0' && $USER->IsAuthorized()) {
            $userid = $USER->GetId();
        }

        $objDateTime = new DateTime('NOW');
        $purchaseDate = $objDateTime->format("Y-m-d\TH:i:s.v\Z");

        if (isset($_SESSION['lmxapp']['good'][$id]['purchaseId']) && $_SESSION['lmxapp']['good'][$id]['purchaseTime'] > time()-600)
        {
            $purchaseId = str_replace([" ", "."], "",  $_SESSION['lmxapp']['good'][$id]['purchaseId']);
            $purchaseDate = $_SESSION['lmxapp']['good'][$id]['purchaseDate'];
            $_SESSION['lmxapp']['good'][$id]['purchaseTime'] = time();
        }
        else
        {
            $purchaseId = str_replace([" ", "."], "",  rand(1, 10).microtime().$userid.$id);
            $_SESSION['lmxapp']['good'][$id]['purchaseId'] = $purchaseId;

            $_SESSION['lmxapp']['good'][$id]['purchaseTime'] = time();

            $_SESSION['lmxapp']['good'][$id]['purchaseDate'] = $purchaseDate;
        }
        if ($_SESSION['lmxapp']['mtoken'] && $_SESSION['lmxapp']['mexpires'] > time()){
            $this->setMerchantToken($_SESSION['lmxapp']['mtoken'], $_SESSION['lmxapp']['mexpires']);
        }
        else
        {
            $this->authMerchantToken();
        }
        if ($USER->IsAuthorized())
        {
            $obUser = $USER->GetByLogin($USER->GetLogin());
            if ($arUser = $obUser->Fetch()){

                $parsedPhone = Parser::getInstance()->parse($arUser['LOGIN']);
                $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));
                if (!$phone){
                    $parsedPhone = Parser::getInstance()->parse($arUser['PERSONAL_PHONE']);
                    $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));
                }
            }
            if ($phone != '' && strlen($phone) == 11)
            {
                $checkuser = $this->checkUser($phone);
                if ($checkuser['status'] == 'found')
                {
                    $authclientresult = $this->authClientToken($checkuser['code']);
                }
            }
        }

        $lines = [];

        $obItem = CIBlockElement::GetList([], ['IBLOCK_ID'=>$iblock_id, 'ID'=>$id,'ACTIVE'=>'Y'],
            false,false, ['ID', 'CATALOG_PRICE_'.$price_id, "PROPERTY_GTIN", "PROPERTY_LMX_GOODID", "NAME", "PROPERTY_CML2_ARTICLE" ] );
        if ($exItem = $obItem->GetNext()) {

            $lines[] = [
                "position" => 1,
                "amount" => $exItem['CATALOG_PRICE_'.$price_id],
                "quantity" => $quantity,
                "cashback" => 0,
                "discount" => 0,
                "name" => $exItem['PROPERTY_CML2_ARTICLE_VALUE'],
                "price" => $exItem['CATALOG_PRICE_'.$price_id]
            ];
            if ($exItem['PROPERTY_LMX_GOODID_VALUE'] != '')
            {
                array_push($lines, ['goodsId'=>$exItem['PROPERTY_LMX_GOODID_VALUE']]);
            }elseif ($exItem['PROPERTY_GTIN_VALUE'] != '')
            {
                array_push($lines, ['barcode'=>substr($exItem['PROPERTY_GTIN_VALUE'],1)]);
            }
        }



        if (!empty($lines))
        {

            $lmxItem = [];
            $qResult = $this->calculate($purchaseId, $purchaseDate, $lines);

            if ($qResult['result'] && $qResult['result']['state'] == 'Success')
            {
                foreach ($qResult['data'][0]['cheque']['lines'] as $line) {


                    $lmxItem = [
                        "BASE_PRICE" => ceil($line['amount']+$line['discount']),
                        "PRICE" => ceil($line['amount']/$line['quantity']),
                        "DISCOUNT" =>  ceil($line['discount']/$line['quantity']),
                        "PRICE_FORMATED" => CCurrencyLang::CurrencyFormat(ceil($line['amount']/$line['quantity']), 'RUB', true)
                    ];

                }
                return $lmxItem;
            }
        }
        return  false;
    }

    public function checkCoupon($coupon)
    {
        $method = '/publicapi/v1.2/Coupons/'.$coupon;

        $result = new Result();

        $response = $this->query('get', $method,  [], 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
        }
        else {
            $data = $response->getData();

            return $data;
        }
        return $result;
    }

    public function calculate($purchaseId, $purchaseDate, $items, $coupon = ''){

        $_SESSION['purchaseId'] = $purchaseId;
        $method = '/publicapi/v1.2/Processing/Purchases/'.$purchaseId.'/Calculate';

        $result = new Result();

        if (!empty($items)) {

            $cheque = [
                "number" => "ms".$purchaseId,
                "date" => $purchaseDate,
                "lines" => $items
                ];


            $request = [
                'operationId' => $purchaseId,
                'operationDate' => $purchaseDate,
                'cashier'=> 'medi-salon.cart',
                'cheque' => $cheque
            ];
            if ($coupon != '')
            {
                $request['coupons'][] = ["number"=>$coupon];
            }

            $response = $this->query('post', $method, $request, 1, 1);

            if (!$response->isSuccess()) {
                $result->addErrors($response->getErrors());

                return ['result' => $result, 'status' => 'error'];
            } else {
                $data = $response->getData();


                return $data;
            }
        }
        else
        {

            return ['result' => 'empty cheque', 'status' => 'error'];
        }
        return $result;

    }



    private function query($type = 'post', $method, $parameters = [], $need_auth = false, $json=0)
    {

        //$parameters = array_merge($auth, $parameters);

        $query_str = http_build_query($parameters);

        $http = new HttpClient();
        if ($need_auth)
        {
            if ($_SESSION['lmxapp']['token']){
                $http->setHeader('Authorization', 'Bearer '.$_SESSION['lmxapp']['token']);

            }
            else
            {
                $http->setHeader('Authorization', 'Bearer '.$this->merchantToken);

            }
        }

        if ($json == 1) {
            $http->setHeader('Content-type', 'application/json; charset=utf-8');
        }
        if ($type == 'post'){
            $http->setHeader('Content-type', 'application/x-www-form-urlencoded');
            $http->post($this->public_api_url.$method, $parameters);

        }
        elseif ($type == 'put') {
            $http->setHeader('Content-length', 0);
            $http->query(HttpClient::HTTP_PUT , $this->public_api_url.$method, $parameters);
        }
        else {
            $http->get($this->public_api_url.$method.'?'.$query_str, $parameters);
        }

        $status = $http->getStatus();
        $result = new Result();

        if ($status != '200')
        {
            $result->addError(new Error("Error ".$status));

            return $result;

        }

        try {
            $data = Json::decode($http->getResult());

            if (isset($data['error'])) {
                $result->addError(new Error($data['error']));

                return $result;
            }

            $result->setData($data);
        } catch (ArgumentException $e) {
        }

        return $result;
    }
}
