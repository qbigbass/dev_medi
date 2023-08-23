<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

$mid = "scoder.modalcoupon";


global $USER, $APPLICATION;

if (check_bitrix_sessid() && (
	$_SERVER['REQUEST_METHOD'] == 'POST' 
		|| $_SERVER['REQUEST_METHOD'] == 'GET'
		)
	)
{	
	if (!CModule::IncludeModule($mid))
		return;
	
	foreach ($_REQUEST as $key => $val)
		if (!is_array($val))
			$_REQUEST[$key] = mb_convert_encoding($val, LANG_CHARSET, "UTF-8");
	
	
	switch (htmlspecialcharsbx($_REQUEST['action'])) {
		case 'SC_COUPON':
			
			if (check_email($_REQUEST['email']))
			{
				$cUser = new CUser; 
				$sort_by = "ID";
				$sort_ord = "DESC";
				$arFilter = array(
					"EMAIL" => $_REQUEST['email'],
				);
				$dbUsers = $cUser->GetList($sort_by, $sort_ord, $arFilter,array("FIELDS"=>array("ID"),"NAV_PARAMS"=>array("nTopCount"=>"1")));
				if ($arUser = $dbUsers->Fetch()) 
				{
					$data = array(
						'RESULT'=>"FIND"
					);
				}
				else
				{
					$arResult['PASSWORD_LENGTH'] = (int) Option::get($mid, 'PASSWORD_LENGTH', false, SITE_ID);
					$arResult['PASSWORD_SYMBOL'] = Option::get($mid, 'PASSWORD_SYMBOL', false, SITE_ID);
					$pass = randString($arResult['PASSWORD_LENGTH'], array($arResult['PASSWORD_SYMBOL']));	//password generation
					
					$arload = Array(
						"NAME"              => htmlspecialcharsEx($_REQUEST['name']),
						"EMAIL"             => htmlspecialcharsEx($_REQUEST['email']),
						"LOGIN"             => htmlspecialcharsEx($_REQUEST['email']),
						"ACTIVE"            => "Y",
						"PASSWORD"          => $pass,
						"CONFIRM_PASSWORD"  => $pass,
					);
					if (isset($_REQUEST['PERSONAL_PHONE']) && $_REQUEST['PERSONAL_PHONE']!='')
						$arload['PERSONAL_PHONE'] = htmlspecialcharsEx($_REQUEST['PERSONAL_PHONE']);
					
					$arResult['GROUPS'] = Option::get($mid, 'GROUPS', false, SITE_ID);
					if (strlen($arResult['GROUPS'])>0)
					{
						$arResult['GROUPS_ITEMS'] = unserialize($arResult['GROUPS']);
						$arload["GROUP_ID"] = $arResult['GROUPS_ITEMS'];
					}	
					$error = '';
					if ($_REQUEST['USE_CAPTCHA'] == 'Y' 
						&& !$APPLICATION->CaptchaCheckCode($_REQUEST["captcha_word"], $_REQUEST["captcha_sid"])
					)
					{
						$add = 0;
						$error .= GetMessage("ERRROR_WRONG_CAPTCHA")."\n";
					}
					$events = GetModuleEvents('scoder.modalcoupon', "OnBeforeUserAdd", true);
					foreach($events as $arEvent)
					{
						$bEventRes = ExecuteModuleEventEx($arEvent, array(&$arload));
						if($bEventRes===false)
						{
							if($err = $APPLICATION->GetException())
								$error .= $err->GetString()." ";
							else
							{
								$APPLICATION->ThrowException("Unknown error");
								$error .= "Unknown error. ";
							}
							break;
						}
					}
					
					if ($error == '')
					{
						if ($ID = $cUser->Add($arload))
						{
							$data = array(
								'RESULT'=>"SUCCES",
								'ID' => $ID,
							);
							if (Option::get($mid, 'IS_AUTHORIZE', false, SITE_ID)=='Y')
							{
								$USER->Authorize($ID);
								
								$data['IS_AUTHORIZE'] = 'Y';
							}
							//согласие
							if (isset($_REQUEST['sc_modalcoupon_userconsent_input']) 
									&& $_REQUEST['sc_modalcoupon_userconsent_input']=='Y'
										&& isset($_REQUEST['user_consent_id']) 
											&& $_REQUEST['user_consent_id']>0
								)
							{
								 \Bitrix\Main\UserConsent\Consent::addByContext($_REQUEST["user_consent_id"]);
							}
						}
						else
						{
							$error = $cUser->LAST_ERROR;
						}
					}
				}
			}
			else
			{
				$error = GetMessage("ERRROR_EMAIL_INCORRECT");
			}	
			if ($error!='')
			{			
				$data = array(
					'RESULT'=>"ERROR",
					'TEXT' => $error,
				);
			}
			echo $enc_value = \Bitrix\Main\Web\Json::encode($data, $options = null);
			break;
		case 'SC_CLOSE':		//записать в куки, что окно закрыто и не нужно обновлять
			$arResult['TIMECLOSE'] = (int) $_REQUEST['TIMECLOSE'];
			
			$APPLICATION->set_cookie("SC_CLOSE_COUPON", 'Y', time()+$arResult['TIMECLOSE'], "/");		//время жизни
			break;
		case 'SC_UPDATE_CAPTCHA':
			echo htmlspecialcharsbx($APPLICATION->CaptchaGetCode());
			break;
	}
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>