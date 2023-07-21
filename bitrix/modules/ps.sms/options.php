<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\MessageService\Sender\Base;
use Ps\Sms\Events;
use Ps\Sms\Interfaces\HasBalance;
use Ps\Sms\Interfaces\HasToken;
use Ps\Sms\Interfaces\HasCabToken;
use Ps\Sms\Interfaces\HasPreferences;
use Ps\Sms\Interfaces\HasSender;
use Ps\Sms\Interfaces\HasWarning;

Loc::loadMessages(__FILE__);

try {
    Loader::includeModule('messageservice');
    Loader::includeSharewareModule('ps.sms');
} catch (LoaderException $e) {
}

$context = Context::getCurrent();
$post = $context->getRequest()->getPostList()->toArray();

if (is_array($post['settings']) && (count($post['settings']) > 0)) {
    foreach ($post['settings'] as $name => $val) {
        if (isset($val)) {
            Option::set('ps.sms', $name, $val);
        } else {
            Option::delete('ps.sms', ['name' => $name]);
        }
    }
}

$providers = [];
$services = new Events();
foreach ($services->registerProvider() as $provider) {
    if ($provider instanceof HasPreferences) {
        $providers[] = $provider;
    }
}

$tabs = [];
/** @var $provider Base */
foreach ($providers as $provider) {
    $tabs[] = [
        'DIV' => $provider->getId(),
        'TAB' => $provider->getName(),
        'TITLE' => Loc::getMessage(
            'PS_SMS_OPTIONS_TAB_TITLE',
            [
                '#NAME#' => $provider->getName(),
                '#SHORT_NAME#' => $provider->getShortName(),
            ]
        )
    ];
}

$tabControl = new CAdminTabControl('tabControl', $tabs);
$tabControl->Begin();

echo '<form name="ps.sms" method="POST" action="'.$APPLICATION->GetCurPage(
    ).'?mid=ps.sms&lang='.LANGUAGE_ID.'" enctype="multipart/form-data">'.bitrix_sessid_post();

/** @var $provider Base */
foreach ($providers as $provider) {
    $tabControl->BeginNextTab();

    if ($provider instanceof HasBalance && $provider->canUse()) {
        $balance = $provider->getBalance();
        ?>
        <tr>
            <td colspan="2">
                <?php

                $message = new CAdminMessage(
                    Loc::getMessage(
                        'PS_SMS_OPTIONS_BALANCE',
                        [
                            '#SUM#' => $balance
                        ]
                    )
                );
                $message->ShowNote(
                    Loc::getMessage(
                        'PS_SMS_OPTIONS_BALANCE',
                        [
                            '#SUM#' => $balance
                        ]
                    )
                );
                ?>
            </td>
        </tr>
        <?php
    }

    $loginField = $provider->getId().'_login';
    $passwordField = $provider->getId().'_password';
    $senderField = $provider->getId().'_sender';
    $tokenField = $provider->getId().'_token';
    $cabtokenField = $provider->getId().'_cab_token';
    ?>

    <tr class="heading">
        <td colspan="2"><?= Loc::getMessage('PS_SMS_OPTIONS_CONNECTION') ?></td>
    </tr>
    <tr>
        <td width="40%" nowrap="" class="adm-detail-content-cell-l">
            <label for="ps_sms_<?= $loginField ?>"><?= $provider->getLoginTitle() ?></label>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="text" id="ps_sms_<?= $loginField ?>"
                   name="settings[<?= $loginField ?>]"
                   value="<?= Option::get('ps.sms', $loginField) ?>"/>
        </td>
    </tr>

    <tr>
        <td width="40%" nowrap="" class="adm-detail-content-cell-l">
            <label for="ps_sms_<?= $passwordField ?>"><?= $provider->getPasswordTitle() ?></label>
        </td>
        <td width="60%" class="adm-detail-content-cell-r">
            <input type="password" id="ps_sms_<?= $passwordField ?>"
                   name="settings[<?= $passwordField ?>]"
                   value="<?= Option::get('ps.sms', $passwordField) ?>"/>
        </td>
    </tr>

    <?php

    if ($provider instanceof HasSender) { ?>
        <tr>
            <td width="40%" nowrap="" class="adm-detail-content-cell-l">
                <label for="ps_sms_<?= $senderField ?>"><?= $provider->getSenderTitle() ?></label>
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <input type="text" id="ps_sms_<?= $senderField ?>"
                       name="settings[<?= $senderField ?>]"
                       value="<?= Option::get('ps.sms', $senderField) ?>"/>
            </td>
        </tr>
        <?php
    }

    if ($provider instanceof HasToken) { ?>
        <tr>
            <td width="40%" nowrap="" class="adm-detail-content-cell-l">
                <label for="ps_sms_<?= $tokenField ?>"><?= $provider->getTokenTitle() ?></label>
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <input type="text" id="ps_sms_<?= $tokenField ?>"
                       name="settings[<?= $tokenField ?>]"
                       value="<?= Option::get('ps.sms', $tokenField) ?>"/>
            </td>
        </tr>
        <?php
    }

    if ($provider instanceof HasCabToken) { ?>
        <tr>
            <td width="40%" nowrap="" class="adm-detail-content-cell-l">
                <label for="ps_sms_<?= $cabtokenField ?>"><?= $provider->getCabTokenTitle() ?></label>
            </td>
            <td width="60%" class="adm-detail-content-cell-r">
                <input type="text" id="ps_sms_<?= $cabtokenField ?>"
                       name="settings[<?= $cabtokenField ?>]"
                       value="<?= Option::get('ps.sms', $cabtokenField) ?>"/>
            </td>
        </tr>
        <?php
    }
    if ($provider instanceof HasWarning) { ?>
        <tr>
            <td colspan="2" align="center">
                <div class="adm-info-message-wrap" align="center">
                    <div class="adm-info-message">
                        <p><?= $provider->getWarning() ?></p>
                    </div>
                </div>
            </td>
        </tr>
        <?php
    }
}

$tabControl->End();

$tabControl->Buttons();

echo '<input type="hidden" name="update" value="Y" />';
echo '<input type="submit" name="save" value="'.Loc::getMessage('PS_SMS_OPTIONS_SAVE').'" class="adm-btn-save" />';
echo '<input type="reset" name="reset" value="'.Loc::getMessage('PS_SMS_OPTIONS_RESET').'" />';
echo '</form>';
$tabControl->End();
