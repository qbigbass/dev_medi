<?
//push captcha
function getCaptcha($captchaCode, $captchaTitle){
	return '
		<input type="hidden" name="captcha_sid" value="'.$captchaCode.'>" />
		<div class="dbg_captha">
			<div class="bx-authform-label-container">'.$captchaTitle.'</div>
			<div class="bx-captcha"><img src="/bitrix/tools/captcha.php?captcha_sid='.$captchaCode.'" width="180" height="40" alt="CAPTCHA" class="bx-auth-captcha-picture" /></div>
			<div class="bx-authform-input-container">
				<input type="text" name="captcha_word" maxlength="50" autocomplete="off" value="" />
			</div>
		</div>
	';
}
function checkEncoding($data){

    //array
    if(is_array($data)){
        return array_map(function($value){
            return \Bitrix\Main\Text\Encoding::convertEncoding($value, "UTF-8", LANG_CHARSET);
        }, $data);
    }

    //string
    else{
        return !defined("BX_UTF") ? \Bitrix\Main\Text\Encoding::convertEncoding($data, "UTF-8", LANG_CHARSET) : $data;
    }

}
?>