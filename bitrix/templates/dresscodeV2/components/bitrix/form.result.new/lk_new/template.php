<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="prev">
    <div class="h2 ff-medium title">Написать сообщение директору по&nbsp;клиентскому сервису</div>
    <div class="row">

            <?=$arResult['FORM_DESCRIPTION']?>
    </div>
</div>

    <?=$arResult["FORM_HEADER"]?>

    <?if($arResult["isFormErrors"] == "Y"):?>
    <div class="message fail"><?=$arResult["FORM_ERRORS_TEXT"];?></div>
    <?endif;?>
    <?if($arResult["FORM_NOTE"]):?>
    <div class="message success"><?=$arResult["FORM_NOTE"]?></div>
<?endif;?>
    <?if(!empty($arResult["QUESTIONS"])):?>

    <div class="flex form">
    <div class="col-12 col-md-3">
        <div class="row ff-medium title">Тип обращения</div>
        <select class="lg-box" name="form_dropdown_REQUEST_TYPE" id="form_dropdown_REQUEST_TYPE">
            <?/*<option value="72">Начисление/списание бонусов</option>*/?>
            <option value="73">Задать вопрос</option>
            <option value="74">Предложение по улучшению</option>
        </select>
    </div>
    <div class="col-12 col-lg-4 no-padding flex">
        <div class="col-12 col-sm-12">
            <div class="row ff-medium title">Имя</div>
            <input value="<?=$USER->GetFullName()?>" placeholder="Имя" name="form_text_75" type="text" class="lg-box">

        </div>
    </div>
    <div class="col-12 col-lg-5 no-padding flex">
        <div class="col-12 col-sm-6">
            <div class="row ff-medium title">Телефон</div>
            <input value="<?=$_SESSION['lmx']['phone']?>" type="tel" name="form_text_76" class="lg-box phonemask">
        </div>
        <div class="col-12 col-sm-6">
            <div class="row ff-medium title">Email</div>
            <input value="<?=$USER->GetEmail()?>" name="form_email_77" placeholder="Введите ваш E-mail" type="email"  class="lg-box">
        </div>
    </div>
    <div class="message">
        <div class="row ff-medium title">Сообщение</div>
        <textarea placeholder="Введите ваше сообщение" name="form_textarea_78" id="form_textarea_78" cols="150" rows="5" class="lg-box"></textarea>
    </div>

    <div class="message" data-required="Y">
		<input type="checkbox" required id="personalInfoFieldStatic" name="personalInfo" value="Y"><label for="personalInfoFieldStatic">Я соглашаюсь с <a href="/legality/policy/" class="pilink" target="_blank">Политикой в отношении обработки персональных данных.</a><span class="webFormItemRequired">*</span></label>
	</div>


    <div class="btn-wrap">
        <input type="submit" class="btn-simple btn-small" id="GTM_web_form_DIRECTOR_FORM" name="web_form_submit" value="Отправить">
        <input type="hidden" name="web_form_apply" value="Y">
    </div>
</div>
    <?endif;?>

<?=$arResult["FORM_FOOTER"]?>
