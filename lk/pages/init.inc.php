<?
if (!$USER->IsAuthorized())
{

	if (isset($_REQUEST['USER_LOGIN']) && isset($_REQUEST['USER_PASSWORD']))
	{
		if ($auth_res = $USER->Login($_REQUEST['USER_LOGIN'], $_REQUEST['USER_PASSWORD'], "Y","Y") === true)
		{

			$backurl = !empty($_REQUEST['BACKURL']) ? $_REQUEST['BACKURL'] : '/lk/';
			LocalRedirect($backurl);
		}
		else {
			$error = "Неверный телефон или пароль";
		}
	}



	$APPLICATION->IncludeComponent("medi:auth.form", "", Array(
		'ERROR_TEXT' => $error
		),
		false
	);

}
else {

	if (!$_SESSION['lmx']['token'])
	{
		if (isset($_COOKIE['lmx']['token']))
		{
			$_SESSION['lmx']['token'] = $_COOKIE['lmx']['token'];
			$_SESSION['lmx']['phone'] = $_COOKIE['lmx']['phone'];
		}
		else{
			$USER->Logout();

	         unset($_SESSION['lmx']);
	         unset($_SESSION['lmxapp']);

	         setcookie('lmx[token]', '', time(), '/');
	         setcookie('lmx[phone]', '', time(), '/');
			LocalRedirect('/lk/');
		}

	}

    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/loymax.php");

    $api = new apiLmx;
    $api->setAuthToken($_SESSION['lmx']['token']);

    $info = $api->getUserData();
    //_c($_SESSION['lmx']);
    $cards = $api -> getUserCards();
 
    if (!is_array($cards) || empty($cards['data']))
    {
    	$card_emit = $api->EmitVirtual();wl("card_emit lk");
    	//wl($card_emit);
    	$cards = $api -> getUserCards();

    	if (!is_array($cards))
    	{

         	unset($_SESSION['lmx']);

	         setcookie('lmx[token]', '', time(), "/");
	         setcookie('lmx[phone]', '', time(), "/");

    		LocalRedirect('/lk/');
    	}
    }

    $user_status = $api->userStatus();

    if ($user_status['status'] == 'ok')
    {
        $status = $user_status['data']['data'];
    }

}
