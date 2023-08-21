<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/form.style.css", true);?>
<div class="form_reserve scan">
<?if (empty($arResult['SALON'])):
?>
	<p>К сожалению, салон не найден. </p>
<?
else:
?>
<span id="result"></span>
	<?if ($arResult["isFormErrors"] == "Y"):?>
	<script >

			document.location.href = "#result";
	</script>
	    <div class="error alert" >
            Не заполнены следующие обязательные поля: <br/>
	    <?=htmlspecialchars_decode(str_replace("Не заполнены следующие обязательные поля:", "", $arResult["FORM_ERRORS_TEXT"]));?>
	    </div>
	<?endif;

		if (!empty($arResult["FORM_NOTE"])):?>
		<script >

				document.location.href = "#result";
		</script>
		    <div class="succes alert">
		    <?=$arResult["FORM_NOTE"]?>
			<p>Наш специалист в ближайшее время свяжется с Вами и согласует время записи.</p>
		    </div>
            <br/><br/><br/>
        <?else:?>

<?
if ($arResult["isFormDescription"] == "Y" || $arResult["isFormTitle"] == "Y" || $arResult["isFormImage"] == "Y")
{
?>
<?/***********************************************************************************
					form header
***********************************************************************************/?>

	<div class="form_description"><p><?=$arResult["FORM_DESCRIPTION"]?></p></div>

<?
}?>

<?

echo $arResult["FORM_HEADER"];

/***********************************************************************************
						form questions
***********************************************************************************/
$offername = htmlspecialchars($arResult['OFFER']['NAME'], ENT_QUOTES);

if ($arResult['arQuestions']['SKU'])
{
	$sku_field_id = $arResult['arAnswers']['SKU'][0]['ID'];
	?><input name="form_text_<?=$sku_field_id?>" type="hidden" value="<?=$offername?>"/><?
}

?>
<div class="column">
<?
	foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion)
	{
		if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden')
		{
			echo $arQuestion["HTML_CODE"];
		}
		else
		{
            if (in_array($arQuestion['STRUCTURE'][0]['ID'], $arResult['HIDE_FIELDS'] )):
				continue;
            else:
				if ($FIELD_SID == 'CARD'):
					$field_name = "form_text_".$arQuestion['STRUCTURE'][0]['ID'];?>
				<div class="columnRow row" >

					<div class="<?=$field_name?>  webFormItemField" >
						<?if(!empty($arQuestion["IMAGE"])):?>
							<?$imageCaption = CFile::ResizeImageGet($arQuestion["IMAGE"]["ID"], array("width" => 24, "height" => 24), BX_RESIZE_IMAGE_PROPORTIONAL, false);?>
							<img src="<?=$imageCaption["src"]?>" class="webFormItemImage" alt="<?=$arQuestion["CAPTION"]?>">
						<?endif;?>
    					<label for="<?=$field_name?>"><?=htmlspecialchars_decode($arResult['QUESTIONS']['CARD']['CAPTION'])?><?=($arQuestion['REQUIRED']=='Y' ? '<span class="webFormItemRequired">*</span>' : '');?></label>
    					<input type="text" id="<?=$field_name?>" class="<?=$FIELD_SID?>_input" maxlength="7"  name="<?=$field_name?>" value="<?=(isset($_REQUEST[$field_name]) ? htmlspecialchars($_REQUEST[$field_name]) : '');?>">
    					<p class="error_<?=$field_name?>"><span style="display:none">Проверьте введенный номер</span></p>
		            </div>
                </div>
                <?elseif ($FIELD_SID == 'NAME'):
					$field_name = "form_text_".$arQuestion['STRUCTURE'][0]['ID'];?>
    				<div class="columnRow row">
    					<div class="<?=$field_name?>  webFormItemField" data-required="Y">
						<?if(!empty($arQuestion["IMAGE"])):?>
							<?$imageCaption = CFile::ResizeImageGet($arQuestion["IMAGE"]["ID"], array("width" => 24, "height" => 24), BX_RESIZE_IMAGE_PROPORTIONAL, false);?>
							<img src="<?=$imageCaption["src"]?>" class="webFormItemImage" alt="<?=$arQuestion["CAPTION"]?>">
						<?endif;?>
    					<label class="webFormItemCaption" for="<?=$field_name?>"><?=htmlspecialchars_decode($arResult['QUESTIONS']['NAME']['CAPTION'])?><?=($arQuestion['REQUIRED']=='Y' ? '<span class="webFormItemRequired">*</span>' : '');?></label>
    					<input type="text" id="<?=$field_name?>" class="<?=$FIELD_SID?>_input" required name="<?=$field_name?>" value="<?=(isset($_REQUEST[$field_name]) ? htmlspecialchars($_REQUEST[$field_name]) : '' );?>">
    					<p class="error_<?=$field_name?>"><span style="display:none">Поле  не заполнено</span></p>
    				</div></div>

	            <?elseif ($FIELD_SID == "DATE"):

                    $field_name = "form_text_".$arQuestion['STRUCTURE'][0]['ID'];?>

                    <div class="columnRow row">
                    <div class="<?=$field_name;?> form_error_wrap webFormItemField"  data-required="Y">
						<?if(!empty($arQuestion["IMAGE"])):?>
							<?$imageCaption = CFile::ResizeImageGet($arQuestion["IMAGE"]["ID"], array("width" => 24, "height" => 24), BX_RESIZE_IMAGE_PROPORTIONAL, false);?>
							<img src="<?=$imageCaption["src"]?>" class="webFormItemImage" alt="<?=$arQuestion["CAPTION"]?>">
						<?endif;?>
                        <label for="<?=$field_name?>"><?=htmlspecialchars_decode($arResult['QUESTIONS']['DATE']['CAPTION'])?><?=($arQuestion['REQUIRED']=='Y' ? '<span class="webFormItemRequired">*</span>' : '');?></label>
                        <?/*
						<input type="date"  id="<?=$field_name?>" min="<?=date("Y-m-d", time()+86400)?>" <?=($arQuestion['REQUIRED']=='Y' ? 'required' : '');?> class="<?=$FIELD_SID?>_input" name="<?=$field_name;?>" value="<?=(isset($_REQUEST[$field_name]) ? htmlspecialchars($_REQUEST[$field_name]) : date("Y-m-d", time()+86400));?>">
                        <p class="error_<?=$field_name?>"><span style="display:none">Поле  не заполнено</span></p>
						*/?>
						<select  id="<?=$field_name?>" <?=($arQuestion['REQUIRED']=='Y' ? 'required' : '');?> class="<?=$FIELD_SID?>_input" title="Пожалуйста, при выборе, учитывайте время работы салона." name="<?=$field_name;?>" value="<?=(isset($_REQUEST[$field_name]) ? htmlspecialchars($_REQUEST[$field_name]) : '');?>">
							<?
							$i  = 1;
							while($i <= 20 ){
								$nextd = mktime(0,0,0,date("m", time()+$i*86400),date("d", time()+$i*86400));?>
								<option value="<?=date("d.m.Y", $nextd);?>" class="<?=(date("w", $nextd) == 0 || date("w", $nextd) == 6 ? 'weekend' : '')?>"><?=strtolower(FormatDate("j F, D", $nextd));?></option>
							<?
								$i++;
							}?>
						</select>
                    </div>
                    </div>

                <?elseif ($FIELD_SID == "PHONE"):
                    $field_name = "form_text_".$arQuestion['STRUCTURE'][0]['ID'];?>
    				<div class="columnRow row">
        				<div class="<?=$field_name;?>   webFormItemField" data-required="Y">
							<?if(!empty($arQuestion["IMAGE"])):?>
								<?$imageCaption = CFile::ResizeImageGet($arQuestion["IMAGE"]["ID"], array("width" => 24, "height" => 24), BX_RESIZE_IMAGE_PROPORTIONAL, false);?>
								<img src="<?=$imageCaption["src"]?>" class="webFormItemImage" alt="<?=$arQuestion["CAPTION"]?>">
							<?endif;?>
        					<label for="<?=$field_name?>"><?=$arQuestion['CAPTION']?><?=($arQuestion['REQUIRED']=='Y' ? '<span class="webFormItemRequired">*</span>' : '');?></label>
        					<input type="tel" placeholder="+7 (___) ___-__-__"  id="<?=$field_name?>" <?=($arQuestion['REQUIRED']=='Y' ? 'required' : '');?> class="<?=($FIELD_SID == 'PHONE' ? 'phonemask' : '');?> <?=$FIELD_SID?>_input"   name="<?=$field_name;?>" value="<?=(isset($_REQUEST[$field_name]) ? htmlspecialchars($_REQUEST[$field_name]) : '');?>">
        					<p class="error_<?=$field_name?>"><span style="display:none">Поле  не заполнено</span></p>
        				</div>
    				</div>
                    <?elseif ($FIELD_SID == 'RECIPE'):
                        $field_name = "form_file_".$arQuestion['STRUCTURE'][0]['ID'];?>
                        <div class="columnRow row">
                            <div class="<?=$field_name?>  webFormItemField">
								<?if(!empty($arQuestion["IMAGE"])):?>
									<?$imageCaption = CFile::ResizeImageGet($arQuestion["IMAGE"]["ID"], array("width" => 24, "height" => 24), BX_RESIZE_IMAGE_PROPORTIONAL, false);?>
									<img src="<?=$imageCaption["src"]?>" class="webFormItemImage" alt="<?=$arQuestion["CAPTION"]?>">
								<?endif;?>
                            <label for="<?=$field_name?>"><?=htmlspecialchars_decode($arResult['QUESTIONS']['RECIPE']['CAPTION'])?><?=($arQuestion['REQUIRED']=='Y' ? '<span class="webFormItemRequired">*</span>' : '');?></label>
                            <input type="file" id="<?=$field_name?>" class="<?=$FIELD_SID?>_input"  name="<?=$field_name?>">

                            <i class="input_note"></i>
                        </div></div>

	            <?elseif ($FIELD_SID == "TIME"):
                    $field_name = "form_dropdown_".$FIELD_SID;?>
                    <div class="columnRow row">
                        <div class="<?=$field_name;?>  webFormItemField" >
							<?if(!empty($arQuestion["IMAGE"])):?>
								<?$imageCaption = CFile::ResizeImageGet($arQuestion["IMAGE"]["ID"], array("width" => 24, "height" => 24), BX_RESIZE_IMAGE_PROPORTIONAL, false);?>
								<img src="<?=$imageCaption["src"]?>" class="webFormItemImage" alt="<?=$arQuestion["CAPTION"]?>">
							<?endif;?>
                            <label  for="<?=$field_name?>"><?=htmlspecialchars_decode($arResult['QUESTIONS']['TIME']['CAPTION'])?><?=($arQuestion['REQUIRED']=='Y' ? '<span class="webFormItemRequired">*</span>' : '');?></label>
                            <select  id="<?=$field_name?>" <?=($arQuestion['REQUIRED']=='Y' ? 'required' : '');?> class="<?=$FIELD_SID?>_input" title="Пожалуйста, при выборе, учитывайте время работы салона." name="<?=$field_name;?>" value="<?=(isset($_REQUEST[$field_name]) ? htmlspecialchars($_REQUEST[$field_name]) : '');?>">
                                <?foreach($arQuestion['STRUCTURE'] AS $a =>$ans){?>
                                    <option value="<?=$ans['ID']?>"><?=$ans['VALUE']?></option>
                                <?}?>
                            </select>

                            <p class="error_<?=$field_name?>"><span style="display:none">Поле  не заполнено</span></p>
                        </div>
                    </div>

				<?elseif ($FIELD_SID == 'ADDRESS'):
					$field_name = "form_text_".$arQuestion['STRUCTURE'][0]['ID'];?>
					<input type="hidden" id="address_input" name="<?=$field_name?>" value="<?=(isset($_REQUEST[$field_name]) ? htmlspecialchars($_REQUEST[$field_name]) : '');?>">

	            <?elseif ($FIELD_SID == "SALON"):?>
				</div><div class="column">
                <div class="columnRow row">
    	            <div class="form_text_<?=$arQuestion['STRUCTURE'][0]['ID']?> webFormItemField">
						<?if(!empty($arQuestion["IMAGE"])):?>
							<?$imageCaption = CFile::ResizeImageGet($arQuestion["IMAGE"]["ID"], array("width" => 24, "height" => 24), BX_RESIZE_IMAGE_PROPORTIONAL, false);?>
							<img src="<?=$imageCaption["src"]?>" class="webFormItemImage" alt="<?=$arQuestion["CAPTION"]?>">
						<?endif;?>
    					<label for="select_salon"><?=htmlspecialchars_decode($arResult['QUESTIONS']['SALON']['CAPTION'])?><?=($arQuestion['REQUIRED']=='Y' ? '<span class="webFormItemRequired">*</span>' : '');?></label>
    	                <select name="form_text_<?=$arQuestion['STRUCTURE'][0]['ID']?>" id="select_salon" required="true">
    	                    <?foreach($arResult['SALON'] as $k=>$salon):?>
    	                    <option value="<?=htmlspecialchars($salon['METRO']['NAME'], ENT_QUOTES)?>" data-id="<?=$salon['ID']?>" data-title="<?=$salon['DESCRIPTION']?>" data-worktime="<?=$salon['SCHEDULE']?>" data-address="<?=$salon['ADDRESS']?>" <?if (isset($_REQUEST['form_text_'.$FIELD_SID]) && $_REQUEST['form_text_'.$FIELD_SID] == $salon['METRO']['NAME']){?>selected="true"<?}elseif (isset($_REQUEST['s']) && $_REQUEST['s'] == $salon['ID']){?>selected="true"<?}?>><?=(!empty($salon['METRO']['NAME']) ? 'м. '.htmlspecialchars($salon['METRO']['NAME'], ENT_QUOTES) : htmlspecialchars($salon['ADDRESS'], ENT_QUOTES) ) ?></option>
    	                    <?endforeach;?>
    	                </select>
    	                <div id="salon_reserve_desc">

    	                    <div class="salon-contacts">
    	                        <span id="salon-adres">ул. Дмитрия Ульянова, 16, к. 1 </span>
    	                    </div>
    	                    <div class="salon-shedule">
    	                        <span id="salon-shedule">ежедневно с 9:00 до 21:00</span>
    	                    </div>


    	                </div>
    	                <script>
    	                $(document).ready(function() {
    	                    $("#select_salon").on("change", set_salon_info);

    	                    function set_salon_info(){
    	                        $salon = $("#select_salon option:selected");
    	                        $salon_address = $salon.attr("data-address");
    	                        $salon_title = $salon.attr("data-title");
    	                        $salon_worktime = $salon.attr("data-worktime");

    	                        $salon_info = '<div class="salon-contacts"><span id="salon-adres">' + $salon_address
    	                         + '</span></div><div class="salon-shedule"><span id="salon-shedule">' + $salon_worktime + '</span>';

    							$("#address_input").val($salon_address);

    	                        $("#salon_reserve_desc").html($salon_info);
    	                    }

    	                    set_salon_info();
    	                });
    	                </script>
    	            </div>
                </div>
	            <?
            else:

				$field_name = "form_text_".$arQuestion['STRUCTURE'][0]['ID'];?>
				<div class="columnRow row">
    				<div class="<?=$field_name;?> form_error_wrap webFormItemField">

    					<label for="<?=$field_name?>"><?=$arQuestion['CAPTION']?><?=($arQuestion['REQUIRED']=='Y' ? '*' : '');?>:</label>
    					<input type="text"  id="<?=$field_name?>" <?=($arQuestion['REQUIRED']=='Y' ? 'required' : '');?> class="<?=($FIELD_SID == 'PHONE' ? 'phonemask' : '');?> <?=$FIELD_SID?>_input" <?=($FIELD_SID == 'PHONE' ? 'placeholder="+7 (___) ___-__-__"' : '');?> name="<?=$field_name;?>" value="<?=(isset($_REQUEST[$field_name]) ? htmlspecialchars($_REQUEST[$field_name]) : '');?>">
    					<p class="error_<?=$field_name?>"><span style="display:none">Поле  не заполнено</span></p>
    				</div>
				</div>
            <?endif;?>
		<?endif;?>

	<?
		}
	} //endwhile
	?>
</div>

	<div class="personalInfo">
		<div class="webFormItem">
			<div class="webFormItemError"></div>
			<div class="webFormItemField" data-required="Y">
				<input type="checkbox" id="personalInfoFieldStatic" name="personalInfo" value="Y"><label for="personalInfoFieldStatic">Я соглашаюсь с <a href="/legality/policy/" class="pilink">Политикой в отношении обработки персональных данных</a><span class="webFormItemRequired">*</span></label>
			</div>
		</div>
	</div>
	<div class="webFormError"></div>
	<div class="webFormTools">
		<input <?=(intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : "");?> type="submit" name="web_form_submit" id="GTM_web_form_<?=($APPLICATION->GetCurDir()== '/services/konsultatsiya-spetsialista-po-ortezirovaniyu/'  ? 'consult' : $arResult["arForm"]["SID"])?>" value="<?=htmlspecialcharsbx(strlen(trim($arResult["arForm"]["BUTTON"])) <= 0 ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]);?>" class="sendWebFormDw"  onclick="ym(30121774, 'reachGoal', '<?=($APPLICATION->GetCurDir()== '/services/konsultatsiya-spetsialista-po-ortezirovaniyu/'  ? 'consult_click' : 'scan_click')?>'); return   true;"  />
		<input type="hidden" name="web_form_apply" value="Y" />
		<input type="reset" value="<?=GetMessage("FORM_RESET");?>" />
		<p><span class="form-required starrequired">*</span>  - Поля, обязательные для заполнения</p>
	</div>
	<?=$arResult["FORM_FOOTER"]?>



	<?=$arResult["FORM_FOOTER"]?>


<?endif;?>
</form>

<?endif;?>
 </div>

 <div class="webFormMessage" id="webFormMessage_<?=$arResult["arForm"]["ID"]?>">
	 <div class="webFormMessageContainer">
		 <div class="webFormMessageMiddle">
			 <div class="webFormMessageHeading">Заявка отправлена</div>
			 <div class="webFormMessageDescription">Спасибо! Ваша заявка принята. Наш специалист в ближайшее время свяжется с Вами и согласует время записи.</div>
			 <a href="#" class="webFormMessageExit">Закрыть окно</a>
		 </div>
	 </div>
 </div>
 <script type="text/javascript">
 	var webFormAjaxDir = "/ajax/salon/";
 	var webFormSiteId = "<?=SITE_ID?>";
 </script>
