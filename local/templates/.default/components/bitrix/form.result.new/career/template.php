<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/form.style.css", true);?>

<span id="result"></span>
	<?if ($arResult["isFormErrors"] == "Y"):?>
	<script >
			document.location.href = "#result";
	</script>
	    <div class="error alert ff-medium webFormError" >
            Исправьте неправильно заполненные поля: <br/>
	    <?=htmlspecialchars_decode(str_replace("Не заполнены следующие обязательные поля:", "", $arResult["FORM_ERRORS_TEXT"]));?>
	    </div>
	<?endif;

		if (!empty($arResult["FORM_NOTE"])):?>
		<script >

				document.location.href = "#result";
		</script>
		    <div class="succes alert">
		    <?=$arResult["FORM_NOTE"]?>
		    </div>
            <br/><br/><br/>
        <?else:?>

		<?echo $arResult["FORM_HEADER"];?>
<div id="career" class="flex">
	<div class="row col-12 col-md-6 webFormItemField"    id="WEB_FORM_ITEM_FIO">

		<?
		if ($arResult["QUESTIONS"]['FIO']):
			$arQuestion = $arResult["QUESTIONS"]['FIO'];
			$field_name = "form_text_".$arQuestion['STRUCTURE'][0]['ID'];?>

		<p class="ff-medium box-title"><label for="career_<?=$field_name?>"> <?=htmlspecialchars_decode($arResult['QUESTIONS']['FIO']['CAPTION'])?><?=($arQuestion['REQUIRED']=='Y' ? '<sup class="medi-color"> *</sup>' : '');?></label></p>
		<input name="<?=$field_name?>" id="career_<?=$field_name?>" value="<?=(isset($_REQUEST[$field_name]) ? htmlspecialchars($_REQUEST[$field_name]) : '');?>" <?=($arQuestion['REQUIRED']=='Y' ? 'required="required"' : '');?> placeholder="Иванов Иван Иванович" type="text" class="lg-box">
		<p class="webFormItemError">Заполните поле</p>

		<?endif;?>
	</div>
	<div class="row col-12 col-md-6 webFormItemField" id="WEB_FORM_ITEM_BIRTHDATE">
		<?
		if ($arResult["QUESTIONS"]['BIRTHDATE']):
			$arQuestion = $arResult["QUESTIONS"]['BIRTHDATE'];
			$field_name = "form_text_".$arQuestion['STRUCTURE'][0]['ID'];?>
		<p class="ff-medium box-title"><label for="career_<?=$field_name?>"><?=htmlspecialchars_decode($arResult['QUESTIONS']['BIRTHDATE']['CAPTION'])?> <?=($arQuestion['REQUIRED']=='Y' ? '<sup class="medi-color"> *</sup>' : '');?></label></p>
		<input type="date" name="<?=$field_name?>" id="career_<?=$field_name?>" <?=($arQuestion['REQUIRED']=='Y' ? 'required="required"' : '');?> value="<?=(isset($_REQUEST[$field_name]) ? htmlspecialchars($_REQUEST[$field_name]) : '');?>" min="<?=date("Y")-65;?>-01-01" max="<?=date("Y")-18;?>-01-01" class="lg-box">
		<p class="webFormItemError"  >Заполните поле</p>
		<?endif;?>
	</div>
	<div class="row col-12 col-md-6 webFormItemField" id="WEB_FORM_ITEM_PHONE">
		<?
		if ($arResult["QUESTIONS"]['PHONE']):
			$arQuestion = $arResult["QUESTIONS"]['PHONE'];
			$field_name = "form_text_".$arQuestion['STRUCTURE'][0]['ID'];?>
		<p class="ff-medium box-title"><label for="career_<?=$field_name?>"><?=htmlspecialchars_decode($arResult['QUESTIONS']['PHONE']['CAPTION'])?> <?=($arQuestion['REQUIRED']=='Y' ? '<sup class="medi-color"> *</sup>' : '');?></label></p>
		<input placeholder="+7 (___) ___-__-__" name="<?=$field_name?>" id="career_<?=$field_name?>" value="<?=(isset($_REQUEST[$field_name]) ? htmlspecialchars($_REQUEST[$field_name]) : '');?>" <?=($arQuestion['REQUIRED']=='Y' ? 'required="required"' : '');?>  type="tel" class="lg-box phonemask">
		<p class="webFormItemError" >Заполните поле</p>
		<?endif;?>
	</div>
	<div class="row col-12 col-md-6 webFormItemField"id="WEB_FORM_ITEM_EMAIL">
		<?
		if ($arResult["QUESTIONS"]['EMAIL']):
			$arQuestion = $arResult["QUESTIONS"]['EMAIL'];
			$field_name = "form_email_".$arQuestion['STRUCTURE'][0]['ID'];?>
		<p class="ff-medium box-title"><label for="career_<?=$field_name?>"><?=htmlspecialchars_decode($arResult['QUESTIONS']['EMAIL']['CAPTION'])?> <?=($arQuestion['REQUIRED']=='Y' ? '<sup class="medi-color"> *</sup>' : '');?></label></p>
		<input placeholder="example@mediexp.ru" name="<?=$field_name?>" id="career_<?=$field_name?>" value="<?=(isset($_REQUEST[$field_name]) ? htmlspecialchars($_REQUEST[$field_name]) : '');?>" <?=($arQuestion['REQUIRED']=='Y' ? 'required="required"' : '');?> type="email" class="lg-box">
		<p class="webFormItemError" >Заполните поле</p>
		<?endif;?>
	</div>
	<div class="row webFormItemField col-12 col-md-6">
		<?
		if ($arResult["QUESTIONS"]['METRO']):
			$arQuestion = $arResult["QUESTIONS"]['METRO'];
			$field_name = "form_text_".$arQuestion['STRUCTURE'][0]['ID'];?>
		<p class="ff-medium box-title"><label for="career_<?=$field_name?>"><?=htmlspecialchars_decode($arResult['QUESTIONS']['METRO']['CAPTION'])?> <?=($arQuestion['REQUIRED']=='Y' ? '<sup class="medi-color"> *</sup>' : '');?></label></p>
		<input placeholder="" name="<?=$field_name?>" id="career_<?=$field_name?>" value="<?=(isset($_REQUEST[$field_name]) ? htmlspecialchars($_REQUEST[$field_name]) : '');?>" <?=($arQuestion['REQUIRED']=='Y' ? 'required="required"' : '');?> type="text" class="lg-box">
		<?endif;?>
	</div>
	<div class="row col-12 col-md-6 flex webFormItemField" style="padding: 0;">
		<div class="col-12 col-md-6">
			<?
			if ($arResult["QUESTIONS"]['SALARY']):
				$arQuestion = $arResult["QUESTIONS"]['SALARY'];
				$field_name = "form_text_".$arQuestion['STRUCTURE'][0]['ID'];?>

			<p class="ff-medium box-title"><label for="career_<?=$field_name?>"><?=htmlspecialchars_decode($arResult['QUESTIONS']['SALARY']['CAPTION'])?> <?=($arQuestion['REQUIRED']=='Y' ? '<sup class="medi-color"> *</sup>' : '');?></label></p>
			<input name="<?=$field_name?>" id="career_<?=$field_name?>" value="<?=(isset($_REQUEST[$field_name]) ? htmlspecialchars($_REQUEST[$field_name]) : '');?>" type="text" <?=($arQuestion['REQUIRED']=='Y' ? 'required="required"' : '');?> class="lg-box">
			<?endif;?>
		</div>
		<div class="col-12 col-md-6 webFormItemField">
			<?
			if ($arResult["QUESTIONS"]['VACANCY']):
				$arQuestion = $arResult["QUESTIONS"]['VACANCY'];
				$field_name = "form_text_".$arQuestion['STRUCTURE'][0]['ID'];?>
			<p class="ff-medium box-title"><label for="career_<?=$field_name?>"><?=htmlspecialchars_decode($arResult['QUESTIONS']['VACANCY']['CAPTION'])?> <?=($arQuestion['REQUIRED']=='Y' ? '<sup class="medi-color"> *</sup>' : '');?></label></p>
			<input type="text" name="<?=$field_name?>" id="career_<?=$field_name?>" value="<?=(isset($_REQUEST[$field_name]) ? htmlspecialchars($_REQUEST[$field_name]) : '');?>" <?=($arQuestion['REQUIRED']=='Y' ? 'required="required"' : '');?> class="lg-box">
			<?endif;?>
		</div>
	</div>
	<div class="row flex" style="padding: 0;">
		<div class="col-12 col-md-6">
			<div class="webFormItemField" data-required="Y">
				<input type="checkbox" id="personalInfoFieldStatic" name="personalInfo" value="Y" required="required">
				<label for="personalInfoFieldStatic" class="gray">Я соглашаюсь с <a href="/legality/policy/" target="_blank" class="pilink">Политикой в отношении обработки персональных данных</a><span class="medi-color">*</span></label>
			</div>
		</div>
		<div class="col-12 col-md-6" style="text-align: right;">
			<input type="hidden" name="web_form_apply" value="Y" />
			<input   class="btn-simple btn-black-border btn-small" type="reset" value="<?=GetMessage("FORM_RESET");?>" />
			<input type="submit" name="web_form_submit" class="btn-simple btn-small" value="Отправить"/>
		</div>
	</div>
</div>
		<?echo $arResult["FORM_FOOTER"];?>
<?endif;?>

<div class="webFormMessage" id="careerFormMessage_<?=$arResult["arForm"]["ID"]?>">
	<div class="webFormMessageContainer">
		<div class="webFormMessageMiddle">
			<div class="webFormMessageHeading">Анкета отправлена</div>
			<div class="webFormMessageDescription">Ваше сообщение успешно отправлено. Мы обязательно рассматриваем все обращения и возвращаемся с обратной связью.</div>
			<a href="#" class="webFormMessageExit_c">Закрыть окно</a>
		</div>
	</div>
</div>
 <script type="text/javascript">
 	var webFormAjaxDir = "/local/templates/.default/components/bitrix/form.result.new/career/ajax.php";
 	var webFormSiteId = "<?=SITE_ID?>";
 </script>
