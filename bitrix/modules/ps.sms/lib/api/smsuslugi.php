<?php

namespace Ps\Sms\Api;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;

class SMSUslugi
{
    private $login;
    private $password;

    private $token;
    private $cab_token;

    public function __construct($login, $password, $token, $cab_token)
    {
        $this->login = $login;
        $this->password = $password;
        $this->token = $token;
        $this->cab_token = $cab_token;
    }

    public function getSenderList()
    {
        $result = new Result();
        $senders[] = [];

        //$senders[] = ["id" => "medi-salon", "name" => "medi-salon"];

        $result = $this->getBalance();

        if (!$result->isSuccess()) {
            return [];
        }

        $data = $result->getData();

        if (!empty($data['source'])){
            foreach($data['source'] as $k=>$sname)
                $senders[] = ["id" => $sname, "name"=>$sname];

        }

        $result->setData($senders);

        return $result;
    }

    private function query($method, $parameters = [], $httpMethod = HttpClient::HTTP_POST)
    {
        $auth = ['login' => $this->login, 'password' => $this->password];

        if ($method == 'cabinet/account/getBalance') {
            $cur_token = $this->cab_token;
            $cur_url = 'https://lcab.sms-uslugi.ru/lcabApi/info.php';
        }
        else {
            $cur_token = $this->token;
            $cur_url = 'https://lcab.sms-uslugi.ru/lcabApi/sendSms.php';

        }
        $parameters = array_merge($auth, $parameters);

        $query_str = http_build_query($parameters);

        $http = new HttpClient();
        $http->query(HttpClient::HTTP_GET, $cur_url.'?'.$query_str);

        $result = new Result();


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

    public function send($parameters)
    {
        $result = new Result();

        $response = $this->query('sms/send/text', $parameters);
        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
        }

        return $result;
    }

    public function getBalance()
    {
        $result = new Result();

        $response = $this->query('cabinet/account/getBalance');

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
        }

        $result->setData($response->getData());

        return $result;
    }
}
