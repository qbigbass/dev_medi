<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Loader; 
use Bitrix\Main\Config\Option; 

$mid = "scoder.modalcoupon";
if (!Loader::includeModule($mid))
{
	ShowError(GetMessage('SCODER_MODALCOUPON_MODULE_INCLUDE_ERROR'));
	return false;
}

global $USER;
if ($USER->IsAuthorized())
	return;

$arResult['ACTIVE'] = Option::get($mid, 'ACTIVE', false, SITE_ID);		//Активен модуль для сайта
$arlibs = array('jquery','ajax','popup');
if (isset($arParams['USE_CAPTCHA']))
	$arResult['USE_CAPTCHA'] = $arParams['USE_CAPTCHA'];
else
	$arResult['USE_CAPTCHA'] = 'N';
//если модуль активен
if ($arResult['ACTIVE']=='Y')
{
	if ($arResult["USE_CAPTCHA"] == "Y")
	{
		$arResult["CAPTCHA_CODE"] = htmlspecialcharsEx($APPLICATION->CaptchaGetCode());
	}
	$arResult['BASKET_DISCOUNTS'] = Option::get($mid, 'BASKET_DISCOUNTS', false, SITE_ID);
	if ($arParams['USER_CONSENT']!='Y')
			$arParams['USER_CONSENT'] = 'N';
	if (is_array($arParams['LIBS']))
	{
		foreach ($arParams['LIBS'] as $key=> $val)
			if (in_array($val,$arlibs))
				$arResult['LIBS_ITEMS'][] = trim($val);
	}
	if ($arResult['BASKET_DISCOUNTS']>0)
	{
		$this->IncludeComponentTemplate();
	}
}
?>