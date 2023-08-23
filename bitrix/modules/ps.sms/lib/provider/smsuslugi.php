<?php

namespace Ps\Sms\Provider;

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\MessageService\Sender\Base;
use Bitrix\MessageService\Sender\Result\SendMessage;
use Ps\Sms\Api\SMSUslugi as Api;
use Ps\Sms\Interfaces\HasBalance;
use Ps\Sms\Interfaces\HasToken;
use Ps\Sms\Interfaces\HasCabToken;
use Ps\Sms\Interfaces\HasPreferences;

Loc::loadMessages(__FILE__);

class SMSUslugi extends Base implements HasPreferences, HasBalance, HasToken, HasCabToken
{
    private $login;

    private $password;

    private $client;

    private $token;

    private $cab_token;

    private $codename = 'medi-salon';

    public function __construct()
    {
        try {
            $this->login = Option::get('ps.sms', $this->getId().'_login');
            $this->password = Option::get('ps.sms', $this->getId().'_password');
            $this->token = Option::get('ps.sms', $this->getId().'_token');
            $this->cab_token = Option::get('ps.sms', $this->getId().'_cab_token');
        } catch (ArgumentNullException $e) {
        } catch (ArgumentOutOfRangeException $e) {
        }

        $this->client = new Api($this->login, $this->password, $this->token, $this->cab_token);
    }

    public function getId()
    {
        return Loc::getMessage('PS_SMS_SMSUSLUGI_ID');
    }

    public function sendMessage(array $messageFields)
    {
        if (!$this->canUse()) {
            $result = new SendMessage();
            $result->addError(new Error(Loc::getMessage('PS_SMS_SMSUSLUGI_CAN_USE_ERROR')));
            return $result;
        }

        $parameters = [
            'to' => trim($messageFields['MESSAGE_TO']),
            'txt' => $messageFields['MESSAGE_BODY'],
        ];

        if ($messageFields['MESSAGE_FROM']) {
            $parameters['source'] = $messageFields['sender'];
        }
        $result = new SendMessage();
        $response = $this->client->send($parameters);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            return $result;
        }

        return $result;
    }

    public function canUse()
    {
        return $this->login && $this->password;
    }

    public function getShortName()
    {
        return Loc::getMessage('PS_SMS_SMSUSLUGI_SHORT_NAME');
    }

    public function getName()
    {
        return Loc::getMessage('PS_SMS_SMSUSLUGI_NAME');
    }

    public function getFromList()
    {
        $data = $this->client->getSenderList();
        if ($data->isSuccess()) {
            return $data->getData();
        }

        return [];
    }

    public function getLoginTitle()
    {
        return Loc::getMessage('PS_SMS_SMSUSLUGI_LOGIN');
    }

    public function getPasswordTitle()
    {
        return Loc::getMessage('PS_SMS_SMSUSLUGI_PASSWORD');
    }

    public function getTokenTitle()
    {
        return Loc::getMessage('PS_SMS_SMSUSLUGI_TOKEN');
    }

    public function getCabTokenTitle()
    {
        return Loc::getMessage('PS_SMS_SMSUSLUGI_TOKEN_CABINET');
    }

    public function getBalance()
    {
        $result = $this->client->getBalance();

        if (!$result->isSuccess()) {
            return 0;
        }

        $data = $result->getData();
        return str_replace(',', '.', $data['account']);
    }
}
