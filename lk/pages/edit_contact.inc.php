<div id="edit-contact">
    <div class="h2 ff-medium title">Редактирование контактов</div>
    <div id="status_message_edit"></div>
    <?/*<div class="flex phone">
        <div class="current-phone col-8 col-md-6 col-lg-3">
            <div class="row ff-medium title">Текущий телефон</div>
            <div class="row lg-box"><nobr><?=$user_info['data']['phoneNumber']?></nobr></div>
        </div>
        <div class="change-phone col-8 col-lg-9 flex">
            <div class="col-12 col-md-6 col-lg-7">
                <div class="row ff-medium title">Введите новый номер телефона&nbsp;<span class="medi-color">*</span></div>
                <input class="phonemask lg-box" id="change_phone" type="tel">
                <div class="row aright">
                    <input class="btn-simple btn-small btn-light"  id="change_phone_start" disabled value="Отправить код">
                </div>
            </div>
            <div class="code col-12 col-md-6 col-lg-5" id="confirm_phone">
                <div class="row ff-medium title">Код подтверждение&nbsp;<span class="medi-color">*</span></div>
                <input class="lg-box" type="tel" id="check_phone_code" placeholder="Введите код из SMS">
                <div class="row aright">
                    <input class="btn-simple btn-small btn-light" id="change_code_check" value="Подтвердить" disabled>
                </div>
            </div>
        </div>

    </div>*/?>
    <div class="flex mail">
        <div class="current-mail col-12 col-md-6 col-lg-3">
            <div class="row ff-medium title">Текущий E-mail</div>
            <div class="row lg-box"><nobr><?=$user_info['data']['email']?>&nbsp;</nobr></div>

        </div>
        <div class="change-mail col-12 col-lg-9 flex">
            <div class="col-12 col-md-6 col-lg-7">
                <div class="row ff-medium title">Введите новый <nobr>E-mail</nobr></div>
                <input class="lg-box" type="email" id="change_email" placeholder="example@mail.ru">
                <div class="row aright">
                    <input class="btn-simple btn-small btn-light" id="change_email_start" disabled value="Отправить код">
                </div>
            </div>
            <div class="code col-12 col-md-6 col-lg-5" id="confirm_email">
                <div class="row ff-medium title">Код подтверждение&nbsp;<span class="medi-color">*</span></div>
                <input class="lg-box" type="text" id="change_email_code" placeholder="Введите код из письма">
                <div class="row aright">
                    <input  class="btn-simple btn-small btn-light"disabled id="change_emailcode_check" value="Подтвердить">
                </div>
            </div>
        </div>
    </div>
    <div class="row aleft ">
        <div class="btn-simple btn-small btn-gray" id="change_contact_back">&laquo; назад</div>
    </div>
</div>
