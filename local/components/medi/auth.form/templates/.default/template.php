<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="reg_page">
    <div class="reg_head">
        <div class="reg_title">Вход или регистрация</div>

        <div class="reg_link2auth">

        </div>
    </div>


    <?if ($arParams['ERROR_TEXT']):?>
        <div class="tr alert"><?=$arParams['ERROR_TEXT']?></div>
    <?endif;?>

    <div  id="reg_form_info" class="tr alert"></div>

    <div class="reg_form">
        <form method="post"  id="loginform" action="<?=$arResult["AUTH_URL"]?>" name="loginform">
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
        <div class="tr webFormItemField agree_block ">
            <input type="checkbox" id="AGREE" name="AGREE" value="Y" />
            <label for="AGREE"><a href="/legality/policy/" target="_blank">Я соглашаюсь с Политикой в отношении обработки персональных данных</a></label><br>
        </div>

        <div class="tr submit" id="check_auth_submit">
            <input type="submit" id="check_auth_code" name="Login" disabled="disabled"  class="submit_button" value="Войти"/>
        </div>

        <div class="tr submit" id="start_reg">
            <input type="submit" id="start_reg_submit" name="Register" disabled="disabled"  class="submit_button" value="Зарегистрироваться"/>
        </div>

        </form>


        <div class="tr submit" id="resend_auth" disabled="disabled">
            <span id="resend_auth_submit" class="link-dashed">Отправить код ещё раз - 60</span>
        </div>


        <div class="tr loyalty-info">
            <a href="/about/loyalty/" target="_blank" class="ff-medium medi-color ">Клуб лояльности medi</a>
        </div>

    </div>

    <div class="reg_anketa_form">
        <form id="anketa_form_data" method="post">
            <input type="hidden" name="action" value="finish_reg"/>

            <input type="hidden" name="user_regphone2" id="user_regphone2" value="<?=$_SESSION['lmx']['phone']?>"/>
        <div class="reg_anketa_form">
            <div class="anketa_title">Заполните поля анкеты <?/* <span class="reg_help question" data-id="q1">?</span><span class="answer q1"><span class="atten">Если Вы не заполните поля "Имя", "Фамилия", "Дата рождения", "Пол" и "E-mail" - вы не сможете бонусами оплатить до&nbsp;30% цены товара без скидки.</span>(По вашей карте будет действовать  скидка 10% и будут начисляться баллы)</span><?*/?></div>
            <div class="anketa_main_data">
                <div class="anketa_row double">
                    <div class="anketa_field">
                        <label for="user_name">Имя</label><span class="starrequired">*</span><br>
                        <input type="text" id="user_name" name="NAME" autocomplete="false" required="true" value="<?=$arResult["USER_NAME"]?>"  />
                    </div>
                    <div class="anketa_field">
                        <label for="user_lname">Фамилия</label><span class="starrequired">*</span><br>
                        <input type="text" id="user_lname" name="LAST_NAME" value="<?=$arResult["USER_LAST_NAME"]?>"  required="true"  />
                    </div>
                </div>
                <div class="anketa_row double">
                    <div class="anketa_field">
                        <label for="user_date">Дата рождения</label><span class="starrequired">*</span><br>
                        <input type="date" placeholder="__.__.____" pattern="\d{1,2}\.\d{1,2}\.\d{4}" id="user_date" name="BIRTHDATE"  required="true" value="<?=$arResult["USER_BIRTHDATE"]?>"  />
                    </div>
                    <div class="anketa_field">
                        <label for="user_sex">Пол</label><span class="starrequired">*</span><br>
                        <div class="user_sex_block">
                            <label class="field_sex"><input type="radio" name="SEX" value="1"  required="true"/> Мужской</label>
                            <label class="field_sex"><input type="radio" name="SEX" value="2"   required="true"/> Женский</label>
                        </div>
                    </div>
                </div>
                <div class="anketa_row double">
                    <div class="anketa_field">
                        <label for="user_email">Email <?/*<span class="question_help question" data-id="q2">?</span><span class="answer q2" >Добавьте свой email адрес и получите 50&nbsp; бонусов.</span>*/?></label><br>
                        <input type="email" id="user_email" name="EMAIL" value="<?=$arResult["USER_EMAIL"]?>"  />

                    </div>
                    <div class="anketa_field subs_field">
                        <label for="user_subs">Подписка <span class="subscribe_help question" data-id="q3">?</span><span class="answer q3 answer_close">Вы получили доступ к&nbsp;уникальным привилегиям. Теперь вы&nbsp;первыми будете знать о&nbsp;ближайших акциях и&nbsp;скидках.</span></label><br>
                        <label class="field_subs"><input type="checkbox" name="SUBSCRIBE" id="susbscibe_checkbox" value="1" checked="checked" /> Согласие на получение информационных сообщений</label>
                        <div id="subscribe_confirm" class="answer_close">Уникальные привилегии, скидки и&nbsp;персональные предложения только по&nbsp;подписке. Вы уверены, что&nbsp;хотите отказаться?
                        <span id="subscribe_confirm_close">Продолжить</span></div>
                    </div>
                </div>
            </div>

            <div class="anketa_submit">
                <input type="submit" name="submit_anketa" id="submit_anketa" class="submit_button" value="Зарегистрироваться"/>
            </div>
        </div>
        </form>
    </div>
