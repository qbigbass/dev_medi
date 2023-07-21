<?php


namespace Bitrix\Main\Engine\ActionFilter;


use Bitrix\Main\Context;
use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\Localization\Loc;

final class CheckModulePermissions extends Base
{
    const ERROR_ACCESS_DENIED = 'access_denied';
    const MODULE_ID = "measoft.courier";

    public function __construct()
    {
        parent::__construct();
    }

    public function onBeforeAction(Event $event)
    {
        global $USER;
        $isAjax = $this->getAction()->getController()->getRequest()->getHeader('BX-Ajax');

        global $APPLICATION;
        $FORM_RIGHT = $APPLICATION->GetGroupRight(self::MODULE_ID);
        if ($FORM_RIGHT == "W") {
            $access = true;
        } else {
            $access = false;
        }

        if (!($USER instanceof \CAllUser) || !$USER->getId() || !$isAjax || !$access)
        {

                Context::getCurrent()->getResponse()->setStatus(200);
                $this->addError(new Error(self::ERROR_ACCESS_DENIED, self::ERROR_ACCESS_DENIED)
                );

                return new EventResult(EventResult::ERROR, null, null, $this);
        }

        return null;
    }
}