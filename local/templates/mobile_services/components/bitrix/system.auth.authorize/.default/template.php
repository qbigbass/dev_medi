<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 */
?>

<section>
    <div class="auth-form">

        <? /* BEGIN messages */ ?>
        <div class="container">
            <div class="row">
                <div class="twelve columns">
                    <h5 class="auth-form__title"><?= GetMessage("AUTH_PLEASE_AUTH"); ?></h5>
                </div>
            </div>
            <div class="row">
                <div class="twelve columns">
                    <? // Результат авторизации или предупреждения ?>
                    <? if (!empty($arParams["~AUTH_RESULT"])): ?>
                        <? $sErrorText = str_replace(array("<br>", "<br />"), "\n", $arParams["~AUTH_RESULT"]["MESSAGE"]); ?>
                        <div class="message message--note">
                            <?= nl2br(htmlspecialcharsbx($sErrorText)) ?>
                        </div>
                    <? endif; ?>
                    <? if ($arResult['ERROR_MESSAGE'] <> ''): ?>
                        <? $sErrorText = str_replace(array("<br>", "<br />"), "\n", $arResult['ERROR_MESSAGE']); ?>
                        <div class="message message--alert">
                            <?= nl2br(htmlspecialcharsbx($sErrorText)) ?>
                        </div>
                    <? endif ?>
                </div>
            </div>
        </div>
        <? /* #END messages */ ?>

        <? /* BEGIN form */ ?>
        <form name="form_auth" method="post" target="_top" action="<?= $arResult["AUTH_URL"]; ?>">

            <input type="hidden" name="AUTH_FORM" value="Y">
            <input type="hidden" name="TYPE" value="AUTH">
            <? if (strlen($arResult["BACKURL"]) > 0): ?>
                <input type="hidden" name="backurl" value="<?= $arResult["BACKURL"] ?>">
            <? endif; ?>
            <? foreach ($arResult["POST"] as $key => $value): ?>
                <input type="hidden" name="<?= $key ?>" value="<?= $value ?>">
            <? endforeach; ?>

            <div class="container">
                <div class="row"> <? // Login                   ?>
                    <div class="one-half column">
                        <label for="auth-form__login"><?= GetMessage("AUTH_LOGIN"); ?></label>
                        <input id="auth-form__login" name="USER_LOGIN" type="text" maxlength="255" value="<?= $arResult["LAST_LOGIN"] ?>" class="u-full-width">
                    </div>
                </div>
                <div class="row"> <? // Password                   ?>
                    <div class="one-half column">
                        <label for="auth-form__password"><?= GetMessage("AUTH_PASSWORD"); ?></label>
                        <? if ($arResult["SECURE_AUTH"]): ?>
                            <div id="_auth-form__secure-auth" style="display:none">
                                <div>
                                    <? echo GetMessage("AUTH_SECURE_NOTE"); ?>
                                </div>
                            </div>
                            <script type="text/javascript">
                                document.getElementById('_auth-form__secure-auth').style.display = '';
                            </script>
                        <? endif ?>
                        <input id="auth-form__password" name="USER_PASSWORD" type="password" maxlength="255" autocomplete="off" class="u-full-width">
                    </div>
                </div>
                <? // Captha ?>
                <? if ($arResult["CAPTCHA_CODE"]): ?>
                    <div class="row">
                        <div class="one-half column">
                            <input type="hidden" name="captcha_sid" value="<? echo $arResult["CAPTCHA_CODE"]; ?>" />
                            <? echo GetMessage("AUTH_CAPTCHA_PROMT"); ?>
                            <img src="/bitrix/tools/captcha.php?captcha_sid=<? echo $arResult["CAPTCHA_CODE"] ?>" width="180" height="40" alt="CAPTCHA" />
                            <input type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" />
                        </div>
                    </div>
                <? endif; ?>
                <? // Remember        ?>
                <? if ($arResult["STORE_PASSWORD"] == "Y"): ?>
                    <div class="row"> 
                        <div class="one-half column">
                            <label for="auth-form__remember">
                                <input type="checkbox" id="auth-form__remember" name="USER_REMEMBER" value="Y" />
                                <?= GetMessage("AUTH_REMEMBER_ME"); ?>
                            </label>
                        </div>
                    </div>
                <? endif; ?>

                <div class="row"> <? // Submit       ?>
                    <div class="one-half column">
                        <input type="submit"  class="button-primary u-full-width" name="Login" value="<?= GetMessage("AUTH_AUTHORIZE"); ?>">
                    </div>
                </div>
            </div>
        </form>
        <? /* #END form */ ?>
    </div>
</section>

<script type="text/javascript">
<? if (strlen($arResult["LAST_LOGIN"]) > 0): ?>
        try {
            document.form_auth.USER_PASSWORD.focus();
        } catch (e) {
        }
<? else: ?>
        try {
            document.form_auth.USER_LOGIN.focus();
        } catch (e) {
        }
<? endif ?>
</script>

