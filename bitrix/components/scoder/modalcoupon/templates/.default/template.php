<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$this->setFrameMode(true);

if (!isset($arParams['TEMPLATE_THEME']) || strlen($arParams['TEMPLATE_THEME'])==0)
	$arParams['TEMPLATE_THEME'] = 'blue';
?>
<div class="sc-toggle">
	<a class="sc-toggleClose" href="#" id="sc-icon-close"><img src="<?=$templateFolder;?>/images/toggle-close.png" alt="" /></a>
	<?=GetMessage('SCODER_GIFT_TEXT');?>
</div>
<div class="sc-modal" id="sc-modalcoupon-form">
	<noindex>
	<div class="sc-modalInner">
		<a class="sc-modalClose" id="sc-closeCoupon" onclick="scoder_modalcoupon.close();"><img src="<?=$templateFolder;?>/images/modal-close.png" alt="" /></a>
		<div class="sc-modalWrap">
			<div class="sc-modalHead">
				<p>
					<span class="sc-gift"></span>
					<?=$arParams['~HEADER_TEXT']?>
				</p>
				<?=$arParams['~MODAL_DESCRIPTION']?>
			</div>
			<div class="sc-errorMsg"></div>
			<form name="sc_user_add" action="<?=POST_FORM_ACTION_URI?>" id="sc-coupon-form"  method="post" enctype="multipart/form-data">
				<input type="hidden" name="action" value="SC_COUPON">
				<?=bitrix_sessid_post()?>
				<?if ($arParams['USER_CONSENT'] == 'Y'):?>
					<input type="hidden" name="user_consent_id" value="<?=$arParams["USER_CONSENT_ID"]?>">
				<?endif;?>
				<div class="sc-inputLine">
					<div>
						<input type="text" name="name" placeholder="<?=GetMessage('SCODER_NAME')?>" />
					</div>
					<div>
						<input type="email" name="email" placeholder="<?=GetMessage('SCODER_EMAIL')?>" />
					</div>
				</div>
				<?if (isset($arParams['ADDITIONAL_FIELDS']) 
						&& is_array($arParams['ADDITIONAL_FIELDS'])):?>
					<div class="sc-inputLine">
						<?foreach ($arParams['ADDITIONAL_FIELDS'] as $code):?>
							<input type="text" name="<?=$code?>" placeholder="<?=GetMessage($code)?>" /><br/>
						<?endforeach?>
					</div>
				<?endif?>
				<?if($arResult["USE_CAPTCHA"] == "Y"):?>
					<div class="sc-inputLine">
						<div>
							<input type="text" name="captcha_word" maxlength="50" value="" placeholder="<?=GetMessage('SCODER_CAPTCHA')?>" />
						</div>
						<div>
							<div id="whiteBlock">
								<img id="loaderImg" src="<?=$templateFolder;?>/images/ajax-loader.gif" />
							</div>
							<img id="captchaImg" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
							<input id="captchaSid" type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
							<?if ($arParams['RELOAD_CAPTCHA'] != 'Y'):?>
								<div class="captcha-link">
									<a href="#"class="sc-captcha-update" onclick="return false;"><?=GetMessage('RELOAD_CAPTCHA')?></a>
								</div>
							<? endif; ?>
						</div>
					</div>
				<? endif; ?>
				<p class="sc-muted">
					<?=$arParams['FOOTER_TEXT']?><br/>
					<?if ($arParams['USER_CONSENT'] == 'Y'):?>
						<?$APPLICATION->IncludeComponent(
							"bitrix:main.userconsent.request",
							"",
							array(
								"ID" => $arParams["USER_CONSENT_ID"],
								"IS_CHECKED" => $arParams["USER_CONSENT_IS_CHECKED"],
								"AUTO_SAVE" => "N",
								"IS_LOADED" => $arParams["USER_CONSENT_IS_LOADED"],
								"SUBMIT_EVENT_NAME" => 'sc_modalcoupon_userconsent_event',
								"INPUT_NAME" => 'sc_modalcoupon_userconsent_input',
								"REPLACE" => array(
									'button_caption' => $arParams['MODAL_BUTTON'],
									'fields' => array(GetMessage('SCODER_NAME'), GetMessage('SCODER_EMAIL'))
								),
							)
						);?>
					<?endif;?>
				</p>
				<div class="sc-clearfix">
					<input type="submit" name="submit" value="<?=$arParams['MODAL_BUTTON']?>" />
				</div>
			</form>
			<div class="sc-modalBottom">
				<div class="sc-deliv">
					<a <?if ($arParams['IS_CLOSE_IS_CLICK_LINKS']=='Y'):?>href="#" onclick="scoder_modalcoupon.close();location.href ='<?=$arParams['LEFT_LINK']?>'"<?else:?>href="<?=$arParams['LEFT_LINK']?>"<?endif;?>><?=$arParams['~LEFT_TEXT']?></a>
				</div>
				<div class="sc-toCart">
					<a <?if ($arParams['IS_CLOSE_IS_CLICK_LINKS']=='Y'):?>href="#" onclick="scoder_modalcoupon.close();location.href ='<?=$arParams['RIGHT_LINK']?>'"<?else:?>href="<?=$arParams['RIGHT_LINK']?>"<?endif;?>><?=$arParams['~RIGHT_TEXT']?></a>
				</div>
			</div>
		</div>
	</div>
	</noindex>
</div>
<div class="sc-modal" id="sc-modal-answer">
	<noindex>
	<div class="sc-modalInner">
		<a class="sc-modalClose" id="sc-closeAnswer"  onclick="scoder_modalcoupon_answer.close();"><img src="<?=$templateFolder;?>/images/modal-close.png" alt="" /></a>
		<div class="sc-modalWrap">
			<div class="sc-mail">
				<p class="cs-mailText1"><?=$arParams['RESULT_TITLE']?></p>
				<p class="cs-mailText2"><?=$arParams['RESULT_TEXT']?></p>
				<button class="sc-mailBtn" id="sc-button-close" onclick="scoder_modalcoupon_answer.close();"><?=$arParams['RESULT_BUTTON']?></button>
			</div>
			<div class="sc-modalBottom">
				<div class="sc-deliv">
					<a <?if ($arParams['IS_CLOSE_IS_CLICK_LINKS']=='Y'):?>href="#" onclick="scoder_modalcoupon_answer.close();location.href ='<?=$arParams['LEFT_LINK']?>'"<?else:?>href="<?=$arParams['LEFT_LINK']?>"<?endif;?>><?=$arParams['~LEFT_TEXT']?></a>
				</div>
				<div class="sc-toCart">
					<a <?if ($arParams['IS_CLOSE_IS_CLICK_LINKS']=='Y'):?>href="#" onclick="scoder_modalcoupon_answer.close();location.href ='<?=$arParams['RIGHT_LINK']?>'"<?else:?>href="<?=$arParams['RIGHT_LINK']?>"<?endif;?>><?=$arParams['~RIGHT_TEXT']?></a>
				</div>
			</div>
		</div>
	</div>
	</noindex>
</div>
<script type="text/javascript">
	BX.message({
		TIMEOUT: '<?=(int) $arParams['TIMEOUT']*1000;?>',						//количество милисекунд после первого захода на сайт
		IS_RELOAD_WINDOW: '<?=($arParams['IS_RELOAD_WINDOW']=='Y'?'Y':'N')?>',	//перезагружать окно
		TIMECLOSE: '<?=$arParams['TIMECLOSE'];?>',
		bitrix_sessid_get: '<?=bitrix_sessid_get()?>',
		SCODER_ERROR: '<?=GetMessage('SCODER_ERROR');?>',
		SCODER_ERROR_USERCONSENT: '<?=GetMessage('SCODER_ERROR_USERCONSENT');?>',
		SCODER_ERROR_EMAIL: '<?=GetMessage('SCODER_ERROR_EMAIL');?>',
		USER_CONSENT: '<?=($arParams['USER_CONSENT']=='Y'?'Y':'N')?>',			//Согласие на обработку персональных данных
		USE_CAPTCHA: '<?=($arResult['USE_CAPTCHA']=='Y'?'Y':'N')?>',
		AUTO_HIDE: '<?=($arParams['AUTO_HIDE']=='Y'?'Y':'N')?>',
		NOT_SHOW_ICON_AFTER_CLOSE: '<?=($arParams['NOT_SHOW_ICON_AFTER_CLOSE']=='Y'?'Y':'N')?>',		//Не отображать иконку после закрытия
		IS_CLOSED: '<?=($arParams['IS_CLOSED']=='Y'?'Y':'N')?>',					//Окно по умолчанию закрыто
	});
</script>