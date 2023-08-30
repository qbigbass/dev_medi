<?php


namespace Bitrix\Main\Engine\ActionFilter;


use Bitrix\Main\Context;
use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\Localization\Loc;

final class CheckGetStatusPermissions extends Base
{
    const ERROR_ACCESS_DENIED = 'access_denied';
    const ORDER_ADMIN_URL = "/bitrix/admin/sale_order_view.php";

    public function __construct()
    {
        parent::__construct();
    }

    public function onBeforeAction(Event $event)
    {
        $referer = Context::getCurrent()->getServer()->get('HTTP_REFERER');

        if (!$referer || stripos($referer, self::ORDER_ADMIN_URL) === false)
        {
            Context::getCurrent()->getResponse()->setStatus(200);
            $this->addError(new Error(self::ERROR_ACCESS_DENIED));

            return new EventResult(EventResult::ERROR, null, null, $this);
        }

        return null;
    }
}