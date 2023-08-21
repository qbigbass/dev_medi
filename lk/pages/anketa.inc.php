<div id="user-data" class="flex">
    <div class="row flex">
        <div class="col-lg-8 col-12">
            <div class="data">
                <form id="lk_edit_contacts_form" method="POST">
                    <input type="hidden" name="action" value="save_anketa">
                    <div class="flex h2 ff-medium title">
                        <span>Личные данные</span>
                        <img class="change" src="/bitrix/templates/dresscodeV2/images/pen.svg"
                             alt="Изменить персональные данные" id="lk_edit_contacts">
                    </div>
                    <div class="flex">
                        <div class="col-12 col-md-4">
                            <div class="row ff-medium title">Фамилия</div>
                            <input disabled type="text" name="anketa[last_name]"
                                   value="<?= $user_info['data']['lastName'] ?>"
                                   oldvalue="<?= $user_info['data']['lastName'] ?>" class="lg-box lk_contacts">
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="row ff-medium title">Имя</div>
                            <input disabled type="text" name="anketa[name]"
                                   value="<?= $user_info['data']['firstName'] ?>"
                                   oldvalue="<?= $user_info['data']['firstName'] ?>" class="lg-box lk_contacts">
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="row ff-medium title">Отчество</div>
                            <input disabled type="text" name="anketa[secondname]"
                                   value="<?= $user_info['data']['patronymicName'] ?>"
                                   oldvalue="<?= $user_info['data']['patronymicName'] ?>" class="lg-box lk_contacts">
                        </div>
                        <? if ($user_info['data']['birthDay'] && trim($user_info['data']['birthDay']) != '') $birthday = date("Y-m-d", strtotime($user_info['data']['birthDay'])); ?>
                        <div class="col-12 col-md-6">
                            <div class="row ff-medium title">Дата рождения</div>
                            <? if (!$birthday || $birthday == '1970-01-01' || $birthday == '1970-01-01T00:00:00Z') { ?>
                                <input disabled type="date" name="anketa[birthday]" value=""
                                       min="<?= date("Y") - 90 ?>-01-01" max="<?= date("Y") - 13 ?>-31-12"
                                       oldvalue="<?= $birthday ?>" data-day='<?= $user_info['data']['birthDay'] ?>'
                                       class="lg-box lk_contacts">
                            <? } else { ?>
                                <input disabled type="date" name="anketa[birthday]" value="<?= $birthday ?>"
                                       class="lg-box ">

                                <div class="info">Внимание! Изменение даты рождения возможно только через оператора по
                                    телефону или в фирменных салонах medi.
                                </div>
                            <? } ?>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="row ff-medium title">Пол</div>
                            <div id="sex" class="flex">
                                <input type="hidden" name="anketa[sex]" value="3"/>
                                <div class="col-6">
                                    <input type="radio" id="sex1" value="1"
                                           name="anketa[sex]" <?= ($sex == 'Мужской' ? 'checked' : '') ?>
                                           oldvalue="<?= ($sex == 'Мужской' ? 'checked' : '') ?>" disabled
                                           class=" lk_contacts">
                                    <label for="sex1">МУЖ</label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" id="sex2" value="2"
                                           name="anketa[sex]" <?= ($sex == 'Женский' ? 'checked' : '') ?>
                                           oldvalue="<?= ($sex == 'Женский' ? 'checked' : '') ?>" disabled
                                           class=" lk_contacts">
                                    <label for="sex">ЖЕН</label>
                                </div>
                            </div>
                        </div>
                        <? /*<div class="col-12 col-md-4">
                    <div class="row ff-medium title">Номер карты medi<?if ($OldCard == ''){?> <span class="question_help question" data-id="q1">?</span><span class="answer q1" >Если у Вас уже есть пластиковая карта medi, то введите её номер в данном поле</span><?}?></div>
                    <input disabled type="text" name="anketa[oldcard]" value="<?=$OldCard?>" oldvalue="<?=$OldCard?>" class="lg-box lk_contacts">
                </div>*/ ?>
                        <? /*<div class="col-12 edit mobile row">
                    <div class="link-dashed" id="lk_edit_contact_cancel_m">Отменить</div>
                    <div class="btn-simple btn-small" id="lk_edit_contact_save_m">Сохранить</div>
                </div>*/ ?>
                    </div>
                </form>
            </div>
        </div>
        <div class="ligal col-lg-4 col-12">
            <? /*$show_contacts_notice = 1;
                if (trim($user_info['data']['lastName']) != '' &&
                    trim($user_info['data']['firstName']) != '' &&
                    trim($user_info['data']['patronymicName']) != '' &&
                    (trim($user_info['data']['birthDay']) != '1970-01-01' &&
                    trim($user_info['data']['birthDay']) != ''  && trim($user_info['data']['birthDay']) != '1970-01-01T00:00:00Z') &&
                    trim($sex) != '' &&
                    $user_info['data']['email'] != ''
                ) {
                    $show_contacts_notice = 0;
                }
                    ?>

                <?if ($show_contacts_notice == '1'){?>
                <p class="medi-color">Если вы не&nbsp;заполните поля «Фамилия», «Имя», «Пол», «Дата рождения» и&nbsp;«e-mail»&nbsp;&ndash; вы&nbsp;не&nbsp;сможете бонусами оплатить до&nbsp;30%&nbsp;цены товара без&nbsp;скидки.</p>
                <p>(*по&nbsp;вашей карте будет действовать скидка&nbsp;10% и&nbsp;будут начисляться бонусы)</p>

                <?}*/ ?>

            <div class="edit desctop">
                <div class="btn-simple btn-small" id="lk_edit_contact_save">Сохранить</div>
                <div class="link-dashed" id="lk_edit_contact_cancel">Отменить</div>
            </div>
        </div>

    </div>


    <div class="col-lg-7 col-12">
        <div class="contacts">
            <div class="flex h2 ff-medium title">
                <span>Контакты</span>
                <img class="change" id="change_contacts" src="/bitrix/templates/dresscodeV2/images/pen.svg" alt="">
            </div>
            <div class="flex">
                <div class="col-12 col-md-6">
                    <div class="row ff-medium title">Телефон</div>
                    <div class="row lg-box"><?= $user_info['data']['phoneNumber'] ?></div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="row ff-medium title">
                        E-mail<? /*if ($user_info['data']['email'] == ''){?><span class="question_help question" data-id="q2">?</span><span class="answer q2" >Добавьте свой email адрес и получите 50&nbsp; бонусов.</span><?}*/ ?></div>
                    <input disabled placeholder="" type="email" value="<?= $user_info['data']['email'] ?>"
                           class="lg-box">
                </div>
            </div>
        </div>
    </div>
    <?
    $rsUser = CUser::GetByID($USER->GetID());
    $arUser = $rsUser->Fetch();
    $checkRegPeriond = (time() - strtotime($arUser['DATE_REGISTER'])) / 86400;
    
    ?>
    
    <? if (($user_info['data']['email'] == ''
            || $user_info['data']['email'] == 'denis@makoviychuk.ru')
        && !isset($_SESSION['ls_email_subs'])
        && $checkRegPeriond > 1) {
        $_SESSION['ls_email_subs'] = '1'; ?>

        <div id="appemailSubs" class="medi_popup">
            <div id="appemailSubsContainer" class="medi_popup_wrap">
                <div class="medi_popup_Container">
                    <div class="medi_popup_ContentBox"
                    <div class="medi_popup_Content">
                        <div id="emailSubsOpenContainer">
                            <p class="emailSubs-desc ff-medium">Мы увидели у Вас не заполнен email</p>
                            <p>Заполнив email, Вы раньше других сможете узнавать о наших акциях и специальных
                                предложениях.</p>

                            <div class="column">
                                <form action="" id="emailSubsForm" method="POST">
                                    <input name="action" type="hidden" id="emailSubsFormAct" value="emailSubs">
                                    <input name="SITE_ID" type="hidden" id="emailSubsFormSiteId" value="<?= SITE_ID ?>">
                                    <div class="formLine"><input name="email" type="email"
                                                                 placeholder="Укажите ваш email*" value=""
                                                                 id="emailSubsFormEmail"></div>
                                    <div class="formLine webFormItemField "><input type="checkbox" checked
                                                                                   name="personalInfoemailSubs"
                                                                                   id="personalInfoemailSubs"><label
                                                for="personalInfoemailSubs" class="emailSubsLabelAgree">Согласен/а
                                            получать письма от&nbsp;medi&nbsp;RUS</label></div>
                                    <div class="formLine txtc"><input type="submit"
                                                                      class="btn-simple btn-small emailSubs-submit"
                                                                      id="GTM_email_subs" value="Продолжить"/></div>

                                    <div class="formLine  txtc"><a href="#" class=" emailSubs-later"
                                                                   id="GTM_email_later">Пропустить</a></div>
                                </form>
                            </div>
                        </div>
                        <div id="emailSubsResult">
                            <div id="emailSubsResultTitle">Вам на почту отправлено сообщение.</div>
                            <div id="emailSubsResultMessage">Просьба подтвердить email перейдя по ссылке в сообщении.
                            </div>
                            <div class="emailSubs-ok btn-simple btn-small btn-black">Закрыть окно</div>
                        </div>
                        <script>
                            $(function () {
                                $("#emailSubsForm").on("submit", function () {
                                    $email = $("#emailSubsFormEmail");
                                    $form = $("#emailSubsForm").serialize();
                                    $error = 0;
                                    if ($email.val().length <= 5) {
                                        $email.addClass("error");
                                        $error = 1;
                                    }
                                    if (!$("#personalInfoemailSubs").prop("checked")) {
                                        $error = 1;
                                        $("#personalInfoemailSubs").addClass("error");
                                    }


                                    if ($error == '0') {
                                        $("#emailSubsForm .error").removeClass("error");
                                        showLoader();
                                        $.ajax({
                                            url: '/ajax/lmx/client/',
                                            data: $form,
                                            method: 'POST',
                                            dataType: 'json',
                                            success: function (data) {
                                                if (data.status == 'ok') {

                                                    $("#emailSubsOpenContainer").hide();
                                                    $("#emailSubsResult").show();
                                                } else if (data.status == 'error') {
                                                    $("#emailSubsOpenContainer").hide();
                                                    $("#emailSubsResult").html("<p>Произошла ошибка, пожалуйста, обновите страницу и попробуйте снова.</p>").show();

                                                } else {

                                                    $("#emailSubsOpenContainer").hide();
                                                    $("#emailSubsResult").html("<p>Произошла ошибка, пожалуйста, обновите страницу и попробуйте снова.</p>").show();

                                                }

                                            },
                                            complete: function (data) {
                                                hideLoader();
                                            }
                                        });
                                    }
                                    return false;
                                });

                                $(".emailSubs-later, .emailSubs-ok ").on("click", function () {
                                    $("#appemailSubs").hide();
                                    return false;
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    
    <? } ?></div>

<div class="row flex">
    <div class="col-lg-8 col-12">
        <div style="margin-top:30px;" class="d-block d-lg-none text-center">
            <a href="/exit/" class="link-dashed btn-simple btn-small btn-black ">Выйти</a>
        </div>
    </div>
</div>
