<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="reg_page">
    <div class="reg_head">
        <div class="reg_title"><?=GetMessage("REGISTER_TITLE")?></div>
        <div class="reg_steps">
            <div class="reg_step step1 active now">1</div>
            <div class="reg_step_line step1"></div>
            <div class="reg_step step2">2</div>
            <div class="reg_step_line step2"></div>
            <div class="reg_step step3">3</div>
        </div>
        <div class="reg_link2auth">
            Уже есть аккаунт? <a href="/lk/">Войти</a>
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
    <form method="post"  id="regform" action="<?=$arResult["AUTH_URL"]?>" name="regform">
    <div class="tr alert" id="reg_form_info"></div>
    <div class="reg_form">

        <div class="tr">
            <label for="user_regphone">Введите номер телефона <span class="starrequired">*</span></label><br>
            <input type="tel" id="user_regphone" name="USER_LOGIN" value="<?=$arResult["USER_LOGIN"]?>" placeholder="+7 (___) ___-__-__"/>
        </div>

        <div class="tr webFormItemField ">
            <input type="checkbox" id="AGREE" name="AGREE" value="Y" />
            <label for="AGREE"><a href="/legality/policy/" target="_blank">Я соглашаюсь с Политикой в отношении обработки персональных данных</a></label><br>
        </div>

        <div class="tr submit">
            <input type="submit" id="send_code" name="send_code" disabled="disabled"  class="submit_button" value="Отправить код"/>
        </div>
    </div>
    </form>

    <div class="confirm_phone_form">
        <div class="tr">
            <label for="confirm_code">Код подтверждения <span class="starrequired">*</span></label><br>
            <input type="text" id="confirm_code" name="USER_PHONE_CONFIRM" maxlength="6" value="<?=$arResult["USER_PHONE_CONFIRM"]?>" />
        </div>
        <div class="tr submit">
            <?/*<input type="submit" name="change_phone" id="change_phone"    class="submit_button_gray" value="Изменить номер"/>*/?>
            <input type="submit" name="check_code" id="check_code" disabled="disabled"  class="submit_button" value="Далее"/>
        </div>
    </div>

    <form id="anketa_form_data" method="post">
        <input type="hidden" name="action" value="finish_reg"/>

        <input type="hidden" name="user_regphone2" id="user_regphone2" value="<?=$_SESSION['lmx']['phone']?>"/>
    <div class="reg_anketa_form">
        <div class="anketa_title">Заполните поля анкеты <?/* <span class="reg_help question" data-id="q1">?</span><span class="answer q1"><span class="atten">Если Вы не заполните поля "Имя", "Фамилия", "Дата рождения", "Пол" и "E-mail" - вы не сможете бонусами оплатить до&nbsp;30% цены товара без скидки.</span>(По вашей карте будет действовать  скидка 10% и будут начисляться баллы)</span><?*/?></div>
        <div class="anketa_main_data">
            <div class="anketa_row double">
                <div class="anketa_field">
                    <label for="user_name">Имя</label><br>
                    <input type="text" id="user_name" name="NAME" autocomplete="false" value="<?=$arResult["USER_NAME"]?>"  />
                </div>
                <div class="anketa_field">
                    <label for="user_lname">Фамилия</label><br>
                    <input type="text" id="user_lname" name="LAST_NAME" value="<?=$arResult["USER_LAST_NAME"]?>"  />
                </div>
            </div>
            <div class="anketa_row double">
                <div class="anketa_field">
                    <label for="user_date">Дата рождения</label><br>
                    <input type="date" placeholder="__.__.____" pattern="\d{1,2}\.\d{1,2}\.\d{4}" id="user_date" name="BIRTHDATE" value="<?=$arResult["USER_BIRTHDATE"]?>"  />
                </div>
                <div class="anketa_field">
                    <label for="user_sex">Пол</label><br>
                    <label class="field_sex"><input type="radio" name="SEX" value="1" /> Мужской</label>
                    <label class="field_sex"><input type="radio" name="SEX" value="2" /> Женский</label>
                </div>
            </div>
            <div class="anketa_row double">
                <div class="anketa_field">
                    <label for="user_email">Email <?/*<span class="question_help question" data-id="q2">?</span><span class="answer q2" >Добавьте свой email адрес и получите 50&nbsp; бонусов.</span>*/?></label><br>
                    <input type="email" id="user_email" name="EMAIL" value="<?=$arResult["USER_EMAIL"]?>"  />

                </div>
                <div class="anketa_field subs_field">
                    <label for="user_subs">Подписка <span class="subscribe_help question" data-id="q3">?</span><span class="answer q3">Вы получили доступ к&nbsp;уникальным привилегиям. Теперь вы&nbsp;первыми будете знать о&nbsp;ближайших акциях и&nbsp;скидках.</span></label><br>
                    <label class="field_subs"><input type="checkbox" name="SUBSCRIBE" id="susbscibe_checkbox" value="1" checked="checked" /> Согласие на получение информационных сообщений</label>
                    <div id="subscribe_confirm">Уникальные привилегии, скидки и&nbsp;персональные предложения только по&nbsp;подписке. Вы уверены, что&nbsp;хотите отказаться?
                    <span id="subscribe_confirm_close">Продолжить</span></div>
                </div>
            </div>
        </div>
        <?/*
        <div class="second_data">

            <div class="anketa_row">
                <div class="anketa_field">
                    <label for="user_regphone2">Номер телефона <span class="starrequired">*</span></label><br>
                    <input type="tel" id="user_regphone2" name="PHONE" value="<?=($_SESSION['lmx']['phone'] ? $_SESSION['lmx']['phone'] : $arResult["USER_LOGIN"])?>" readonly placeholder="+7 (___) ___-__-__"/>
                </div>
            </div>
            <div class="anketa_row">
                <div class="anketa_field">
                    <label for="user_pass">Пароль <span class="starrequired">*</span></label><br>
                    <input type="password" id="user_pass" name="PASSWORD" autocomplete="off"autofill="off"  value=""  />
                </div>
            </div>
            <div class="anketa_row">
                <div class="anketa_field">
                    <label for="user_pass2">Подтверждение пароля <span class="starrequired">*</span></label><br>
                    <input type="password" id="user_pass2" name="CONFIRM_PASSWORD"  autocomplete="false" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>"  />
                    <br><span class="field_desc">Пароль должен быть не менее 6 символов длиной, содержать латинские символы и цифры</span>
                </div>
            </div>
        </div>
        */?>
        <div class="anketa_submit">
            <input type="submit" name="submit_anketa" id="submit_anketa" class="submit_button" value="Зарегистрироваться"/>
        </div>
    </div>
    </form>

</div> <?// end .reg_page?>
<?
/*?>
<div class="bx-auth">

	<noindex>

		<?if($arResult["SHOW_SMS_FIELD"] == true):?>

			<form method="post" action="<?=$arResult["AUTH_URL"]?>" name="regform" class="bx-auth-register-form 1">
				<input type="hidden" name="SIGNED_DATA" value="<?=htmlspecialcharsbx($arResult["SIGNED_DATA"])?>" />
				<div class="bx-auth-form-line-container">
					<div class="bx-auth-form-line">
						<div class="bx-authform-label-container"><?=GetMessage("main_register_sms_code")?><span class="starrequired">*</span></div>
						<div class="bx-authform-input-container">
							<input size="30" type="text" name="SMS_CODE" value="<?=htmlspecialcharsbx($arResult["SMS_CODE"])?>" autocomplete="off" data-required="Y" />
						</div>
					</div>
				</div>
				<div class="alert small"><?=strip_tags(nl2br(htmlspecialcharsbx(str_replace(array(".", "<br>", "<br />"), "\n", ShowMessage($arParams["~AUTH_RESULT"])))), "<br>")?></div>
				<div class="bx-auth-submit-container">
					<input type="submit" name="code_submit_button" value="<?echo GetMessage("main_register_sms_send")?>" class="btn btn-primary submit" />
				</div>
			</form>

			<script>
				new BX.PhoneAuth({
					containerId: 'bx_register_resend',
					errorContainerId: 'bx_register_error',
					interval: <?=$arResult["PHONE_CODE_RESEND_INTERVAL"]?>,
					data:
						<?=CUtil::PhpToJSObject(array(
							'signedData' => $arResult["SIGNED_DATA"],
						))?>,
					onError:
						function(response){
							var errorDiv = BX('bx_register_error');
							var errorNode = BX.findChildByClassName(errorDiv, 'errortext');
							errorNode.innerHTML = '';
							for(var i = 0; i < response.errors.length; i++){
								errorNode.innerHTML = errorNode.innerHTML + BX.util.htmlspecialchars(response.errors[i].message) + '<br>';
							}
							errorDiv.style.display = '';
						}
				});
			</script>

			<div class="bx-register-phone-messages">
				<div id="bx_register_error" style="display:none"><?ShowError("error")?></div>
				<div id="bx_register_resend"></div>
			</div>

		<?elseif(!$arResult["SHOW_EMAIL_SENT_CONFIRMATION"]):?>
			<div class="registerText"><?=GetMessage("REGISTER_TEXT")?></div>
			<form method="post" action="<?=$arResult["AUTH_URL"]?>" name="bform" enctype="multipart/form-data" class="bx-auth-register-form">

				<input type="hidden" name="AUTH_FORM" value="Y" />
				<input type="hidden" name="TYPE" value="REGISTRATION" />
				<div class="bx-auth-form-line-container">
					<div class="bx-auth-form-line">
						<div class="bx-authform-label-container"><?=GetMessage("AUTH_NAME")?></div>
						<div class="bx-authform-input-container">
							<input type="text" name="USER_NAME" maxlength="50" value="<?=$arResult["USER_NAME"]?>" class="bx-auth-input" />
						</div>
					</div>

					<div class="bx-auth-form-line">
						<div class="bx-authform-label-container"><?=GetMessage("AUTH_LAST_NAME")?></div>
						<div class="bx-authform-input-container">
							<input type="text" name="USER_LAST_NAME" maxlength="50" value="<?=$arResult["USER_LAST_NAME"]?>" class="bx-auth-input" />
						</div>
					</div>


							<input type="hidden" name="USER_LOGIN" maxlength="50" value="login"   />


					<?if($arResult["EMAIL_REGISTRATION"]):?>

						<div class="bx-auth-form-line">
							<div class="bx-authform-label-container"><?=GetMessage("AUTH_EMAIL")?><?if($arResult["EMAIL_REQUIRED"]):?><span class="starrequired">*</span><?endif?></div>
							<div class="bx-authform-input-container">
								<input type="text" name="USER_EMAIL" maxlength="255" value="<?=$arResult["USER_EMAIL"]?>" class="bx-auth-input"<?if($arResult["EMAIL_REQUIRED"]):?> data-required="Y" <?endif;?> />
							</div>
						</div>

					<?endif?>

					<?if($arResult["PHONE_REGISTRATION"]):?>

						<div class="bx-auth-form-line">
							<div class="bx-authform-label-container"><?echo GetMessage("main_register_phone_number")?><?if($arResult["PHONE_REQUIRED"]):?><span class="starrequired">*</span><?endif?></div>
							<div class="bx-authform-input-container">
								<input type="text" name="USER_PHONE_NUMBER" maxlength="255" value="<?=$arResult["USER_PHONE_NUMBER"]?>" class="bx-auth-input register-user-phone-field" <?if($arResult["PHONE_REQUIRED"]):?> data-required="Y" <?endif;?>/>
							</div>
						</div>
						<?if(!empty($arParams["MASKED_INPUT_FORMAT"])):?>
							<script>
								$(function(){
									$(".register-user-phone-field").mask("<?=$arParams["MASKED_INPUT_FORMAT"]?>");
								});
							</script>
						<?endif;?>
					<?endif?>

					<div class="bx-auth-form-line">
						<div class="bx-authform-label-container"><?=GetMessage("AUTH_PASSWORD_REQ")?><span class="starrequired">*</span></div>
						<div class="bx-authform-input-container">
							<input type="password" name="USER_PASSWORD" maxlength="255" value="<?=$arResult["USER_PASSWORD"]?>" class="bx-auth-input" autocomplete="off" data-required="Y" />
						</div>
					</div>

					<?if($arResult["SECURE_AUTH"]):?>
						<div class="bx-auth-form-line">
							<span class="bx-auth-secure" id="bx_auth_secure" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
								<div class="bx-auth-secure-icon"></div>
							</span>
							<noscript>
							<span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
								<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
							</span>
							</noscript>
							<script>
								document.getElementById('bx_auth_secure').style.display = 'inline-block';
							</script>
						</div>
					<?endif?>

					<div class="bx-auth-form-line">
						<div class="bx-authform-label-container"><?=GetMessage("AUTH_CONFIRM")?><span class="starrequired">*</span></div>
						<div class="bx-authform-input-container">
							<input type="password" name="USER_CONFIRM_PASSWORD" maxlength="255" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" class="bx-auth-input" autocomplete="off" data-required="Y" />
						</div>
					</div>

					<?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>

						<div class="bx-auth-form-line">
							<div class="bx-authform-label-container"><?=strlen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB")?></div>
						</div>

						<?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
							<div class="bx-auth-form-line">
								<div class="bx-authform-label-container"><?if($arUserField["MANDATORY"]=="Y"):?><span class="starrequired">*</span><?endif;?><?=$arUserField["EDIT_FORM_LABEL"]?>:</div>
								<div class="bx-authform-input-container">
									<?$APPLICATION->IncludeComponent(
										"bitrix:system.field.edit",
										$arUserField["USER_TYPE"]["USER_TYPE_ID"],
										array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField, "form_name" => "bform"),
										null,
										array("HIDE_ICONS"=>"Y")
									);?>
								</div>
							</div>
						<?endforeach;?>

					<?endif;?>
					<?if(!empty($arResult["USE_CAPTCHA"]) && $arResult["USE_CAPTCHA"] == "Y"):?>
						<div class="bx-auth-form-line">
							<div class="bx-auth-captha-container">
								<div class="bx-auth-input-line">
									<div class="bx-authform-label-container"><?=GetMessage("CAPTCHA_REGF_PROMT")?><span class="starrequired">*</span></div>
									<div class="bx-authform-input-container">
										<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
										<div class="bx-auth-form-captha-table">
											<div class="bx-auth-form-captha-field">
												<input type="text" name="captcha_word" maxlength="50" value="" class="bx-auth-captcha-field" data-required="Y" />
											</div>
											<div class="bx-auth-form-captha-image">
												<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?endif?>
				</div>

				<div class="bx-authform-formgroup-container-line">
					<div class="bx-authform-formgroup-container">
						<div class="bx-authform-input-container">
							<input type="checkbox" name="USER_PERSONAL_INFO" maxlength="255" value="Y" data-required="Y" id="userPersonalInfoReg" /><label for="userPersonalInfoReg"><?=GetMessage("USER_PERSONAL_INFO")?>*</label>
						</div>
					</div>
				</div>

				<div class="alert small"><?=strip_tags(nl2br(htmlspecialcharsbx(str_replace(array(".", "<br>", "<br />"), "\n", ShowMessage($arParams["~AUTH_RESULT"])))), "<br>")?></div>
				<div class="bx-auth-submit-container"><input type="submit" name="Register" value="<?=GetMessage("AUTH_REGISTER")?>" class="btn btn-primary submit" /></div>

			</form>

			<?if($arResult["SHOW_EMAIL_SENT_CONFIRMATION"]):?>
				<p class="bx-auth-info-message"><?echo GetMessage("AUTH_EMAIL_SENT")?></p>
			<?endif;?>
			<?if(!$arResult["SHOW_EMAIL_SENT_CONFIRMATION"] && $arResult["USE_EMAIL_CONFIRMATION"] === "Y"):?>
				<p class="bx-auth-info-message"><b><?echo GetMessage("AUTH_EMAIL_WILL_BE_SENT")?></b></p>
			<?endif?>
			<p class="bx-auth-info-message"><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p>
			<p class="bx-auth-info-message"><span class="starrequired">*</span><?=GetMessage("AUTH_REQ")?></p>
			<script>document.bform.USER_NAME.focus();</script>

		<?else:?>
			<div class="bx-auth-success-heading h2 ff-medium"><?=strip_tags(nl2br(htmlspecialcharsbx(str_replace(array(".", "<br>", "<br />"), "\n", ShowMessage($arParams["~AUTH_RESULT"])))), "<br>")?></div>
			<?if($arResult["SHOW_EMAIL_SENT_CONFIRMATION"]):?>
				<p class="bx-auth-info-message"><?echo GetMessage("AUTH_EMAIL_SENT")?></p>
			<?endif;?>
			<?if(!$arResult["SHOW_EMAIL_SENT_CONFIRMATION"] && $arResult["USE_EMAIL_CONFIRMATION"] === "Y"):?>
				<p class="bx-auth-info-message"><?echo GetMessage("AUTH_EMAIL_WILL_BE_SENT")?></p>
			<?endif?>
			<a href="<?=SITE_DIR?>auth/" class="btn-simple btn-small bx-auth-button-link"><?=GetMessage("AUTH_TITLE")?></a>
		<?endif?>

	</noindex>

</div>*/?>
