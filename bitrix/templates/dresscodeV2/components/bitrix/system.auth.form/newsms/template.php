<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="reg_page">
    <div class="reg_head">
        <div class="reg_title">Вход в личный кабинет</div>

        <div class="reg_link2auth">
            Нет аккаунта? <a href="/personal/register/">Регистрация</a>
        </div>
    </div>
    <?
    //check phone registration
    if($arResult["SHOW_SMS_FIELD"] == true){
    	CJSCore::Init("phone_auth");
    }

    //include module
    \Bitrix\Main\Loader::includeModule("dw.deluxe");

    //get template settings
    $arTemplateSettings = DwSettings::getInstance()->getCurrentSettings();

    //check settings
    if(!empty($arTemplateSettings)){

    	//set params
    	$arParams["USE_MASKED_INPUT"] = !empty($arTemplateSettings["TEMPLATE_USE_MASKED_INPUT"]) ? $arTemplateSettings["TEMPLATE_USE_MASKED_INPUT"] : "N";

    	//get masked input format
    	if($arParams["USE_MASKED_INPUT"] == "Y"){
    		$arParams["MASKED_INPUT_FORMAT"] = !empty($arTemplateSettings["TEMPLATE_MASKED_INPUT_CUSTOM_FORMAT"]) ? $arTemplateSettings["TEMPLATE_MASKED_INPUT_CUSTOM_FORMAT"] : $arTemplateSettings["TEMPLATE_MASKED_INPUT_FORMAT"];
    	}

    }
    ?>
    <?if ($arParams['ERROR_TEXT']):?>
    <div class="tr alert"><?=$arParams['ERROR_TEXT']?></div>
    <?endif;?>

    <div class="reg_form">
        <form method="post"  id="smsloginform" action="<?=$arResult["AUTH_URL"]?>" name="smsloginform">
            <?if(!empty($arResult["BACKURL"])):?>
                <input type="hidden" name="BACKURL" value="<?=$arResult["BACKURL"]?>" />
            <?endif?>
            <input type="hidden" name="AUTH_BY" value="PASSWORD" />
            <input type="hidden" name="SITE_ID" value="<?=SITE_ID?>" />
        <div class="tr">
            <label for="user_phone">Номер телефона <span class="starrequired">*</span></label><br>
            <input type="tel" id="user_phone" autocomplete="false" name="USER_LOGIN" value="" placeholder="+7 (___) ___-__-__"/>
        </div>

        <div class="tr submit">
            <input type="submit" id="send_auth_code" name="SendSms" disabled="disabled"  class="submit_button" value="Получить код"/>
        </div>

        </form>

        <form method="post"  id="smscodeform" action="<?=$arResult["AUTH_URL"]?>" name="smscodeform">
            <?if(!empty($arResult["BACKURL"])):?>
                <input type="hidden" name="BACKURL" value="<?=$arResult["BACKURL"]?>" />
            <?endif?>
            <input type="hidden" name="AUTH_BY" value="PASSWORD" />
            <input type="hidden" name="SITE_ID" value="<?=SITE_ID?>" />
        <div class="tr" id="code_input">
            <label for="user_code">Код из смс <span class="starrequired">*</span></label><br>
            <input type="text" maxlength="6" id="user_code" name="USER_CODE" value="<?=$arResult["USER_CODE"]?>"/>
        </div>

        <div class="tr submit" id="check_auth_submit">
            <input type="submit" id="check_auth_code" name="Login" disabled="disabled"  class="submit_button" value="Войти"/>
        </div>
        <br><br>

    </div>
    </form>
