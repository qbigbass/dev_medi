<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\Parser;

ini_set("display_errors", 1);
// интеграция с loymax
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/php_interface/include/lmx_app.php");

$lmxapp = new appLmx;


require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/php_interface/include/loymax.php");

$api = new apiLmx;

// установка token клиента
/*
if ($_COOKIE['lmxapp']['token']){
    if (!$_SESSION['lmxapp']['token'] || $_SESSION['lmxapp']['expires'] < time() )
    {
        $_SESSION['lmxapp']['token'] = $_COOKIE['lmxapp']['token'];
        $_SESSION['lmxapp']['expires'] = $_COOKIE['lmxapp']['expires'];
    }
    else {
        setcookie('lmxapp[token]', 'token', time()+30*86400);
        setcookie('lmxapp[expires]', 'expires', time()+30*86400);
    }
}

if (!$_SESSION['lmxapp']['token'] || $_SESSION['lmxapp']['expires'] < time() )
{
    $lmxapp->authToken($_SESSION['lmxapp']['phone']);
}*/

$action = '';
if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], array('check_phone', 'get_confirm_code', 'check_confirm_code', 'register', 'login', 'save_anketa', 'finish_reg', 'salon_client', 'salon_client_exit'))) {
    $action = strval($_REQUEST['action']);
} else
    die();


if ($action == 'check_phone')
{
    $parsedPhone = Parser::getInstance()->parse($_REQUEST['phone']);

    $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));

    if ($phone) {
        $result = $lmxapp->authMerchantToken();

        $user_res = $lmxapp->checkUser($phone);

        // Найден, сбрасываем пароль
        if ($user_res['status'] == 'found'){

            $result = [];
            $error = 0;

            if ($phone)
            {
                $res = $api->ResetPasswordStart($phone);
                if ($res['status'] == 'fail')
                {

                    $result = ['status'=>'error', 'text'=> 'Произошла ошибка', 'data'=> $res];

                }
                else{
                    $result = ['status'=>'ok', 'data'=> $res];
                }
            }
            else {
                $result = ['status'=>'error', 'text'=> 'Неправильный формат номера телефона'];
            }

        }
        // Не найден, начинаем регистрацию
        else {

            $api = new apiLmx;

            $new_reg = 1;

            $reg_res = $api->BeginRegistration($phone);
            $token = $api->getAuthToken();


            if ($token){
                $_SESSION['lmx']['token'] = $token;
                $_SESSION['lmx']['phone'] = $phone;
            }
            else {

                unset($_SESSION['lmx']);
            }


            $reg_status = 0;
            if (!empty($reg_res) && !is_object($reg_res['result']))
            {
                // Обрабатываем ошибки
                if (!empty($reg_res['errorMessage']))
                {
                    // Уже зарегистрирован
                    if ($reg_res['errorMessage'] == 'Registration of this number has been completed.')
                    {
                        $result['error'] = 'registered';
                        $result['status'] = 'error';
                        $result['error_text'] = 'registered';
                        $result['info'] = $reg_res;
                        $error = 1;
                    }elseif ($reg_res['state'] == 'RegistrationAlreadyCompleted')
                    {
                        $result['error'] = 'registered';
                        $result['status'] = 'error';
                        $result['error_text'] = 'registered';
                        $result['info'] = $reg_res;
                        $error = 1;
                    }
                    elseif ($reg_res['errorMessage'] == 'A password is required.'){
                        $result['error'] = 'need_pass';
                        $result['status'] = 'error';
                        $result['error_text'] = 'pass required';
                        $result['info'] = $reg_res;
                    }
                    else {

                        wl($reg_res);
                        $result['error'] = 'reg_error';
                        $result['status'] = 'error';
                        $result['error_text'] = 'reg error';
                        $result['info'] = $reg_res;
                    }
                    $reg_status = false;

                }
                else {

                    if(is_array($reg_res)){

                        $phone_confirmed =  0;

                        foreach ($reg_res as $k=>$step)
                        {
                            // если еще не выполнено, делаем
                            //else
                            if ($step['userActionType'] == 'AcceptTenderOffer' && $step['actionState'] == 'Required' && !$step['isDone'])
                            {
                                $res = $api->AcceptTenderOffer();

                                if (!$res)
                                {
                                    $reg_status = false;
                                    $result['result'] = $res;
                                    break;
                                }
                            }
                            elseif ($step['userActionType'] == 'ChangePhone' && $step['actionState'] == 'Required' && !$step['isDone'])
                            {
                                //$res = $api->ChangePhone($phone);

                                //if (!$res)          {
                                    $reg_status = 2;
                                    $result['status'] = 'send';
                                    $result['result'] = $res;
                                    $result['token'] = $token;
                                    break;
                                //}
                                $reg_status = 2;
                                break;
                            }
                            elseif ($step['userActionType'] == 'Questions' && $step['actionState'] == 'Required' && !$step['isDone'])
                            {
                                $reg_status = 3;

                                break;

                            }
                            else {
                                $reg_status = 4;
                            }
                        }
                    }
                    if ($reg_status == 2)
                    {
                        $result = ['status'=> 'new_send', 'result'=>$reg_res, 'token' => $token];
                    }
                    elseif ($reg_status == 3)
                    {
                        $result = ['status'=> 'anketa', 'result'=>$reg_res];
                    }
                    elseif ($reg_status == 4)
                    {
                        $result = ['status'=> 'lk', 'result'=>$reg_res];
                    }
                    else {
                        $result['status'] = 'error';
                    }
                }
            }
            else {
                $result['status'] = 'error';

                $result['error_text'] = 'reg error';
            }

        }
        header("Content-type: application/json; charset=utf-8");
        echo json_encode($result);
    }
}
//check_confirm_code
elseif ($action == 'check_confirm_code')
{
    $parsedPhone = Parser::getInstance()->parse($_REQUEST['phone']);
    $code = preg_replace("/(\D)*/", "", $_REQUEST['code'] );
    $pass = $_REQUEST['code'];

    $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));

    $result = [];
    $error = 0;

    if ($phone)
    {
        $api = new apiLmx;

        $res = $api->ResetPasswordConfirm($phone, $code, $pass);

        if ($res['result']['state'] == 'Fail')
        {

            $result = ['status'=>'error', 'text'=> 'Неправильный код'];
        }
        elseif ($res['status'] == 'fail')
        {

            $result = ['status'=>'error', 'text'=> 'Неправильный код'];
        }
        else {

            $result = ['status'=>'confirmed', 'data'=> $res];
            $token = $api->getAuthToken($phone, $pass);
            $_SESSION['lmx']['token'] = $token;
            $_SESSION['lmx']['phone'] = $phone;
            $user_data = $api->getUserData();


            $login = '+'.$phone;

            $arFields['LOGIN'] = $login;

            $auser = new CUser;

            // Ищем в битриксе
            $find_user = 0;
            // Ищем в битриксе
            $arFilter = [
               [
                  "LOGIC"=>"OR",
                  ['LOGIN' => $login],
                  ['PERSONAL_MOBILE' => $login],
                  ['PERSONAL_PHONE'=>$login],
                 // ['PHONE_NUMBER' => $login]
                ]
            ];
            $res = Bitrix\Main\UserTable::getList(Array(
               "select"=>Array("ID","NAME"),
               "filter"=>$arFilter,
            ));

            if ($bx_user = $res->fetch())
            {
                $find_user = 1;
                $fUser = $bx_user;
            }
            else {
                $query = 'SELECT USER_ID FROM `b_user_phone_auth` WHERE PHONE_NUMBER = "'.$login.'" ';
                $obRes = $DB->Query($query);

                if ($arPhoneExists = $obRes->Fetch()) {
                    $find_user = 1;
                    $bx_user['ID'] =  $arPhoneExists['USER_ID'];
                    $fUser = $bx_user;
                }
            }


            if ($find_user == 1)
            {
                $ID = $fUser['ID'];
                $new_user  = [
                    'NAME' =>$user_data['data']['firstName'],
                    'LAST_NAME' =>$user_data['data']['lastName'],
                    'SECOND_NAME' =>$user_data['data']['patronymicName'],
                    'EMAIL' =>$user_data['data']['email'],
                    'PERSONAL_PHONE' =>$login,
                    /*'LOGIN' => $login,*/
                    'ACTIVE' => 'Y',
                    'XML_ID' => $user_data['data']['id'],

                    /*'PASSWORD' => $pass,
                    'CONFIRM_PASSWORD' => $pass,*/
                    'PERSONAL_PHONE' =>$login,

                ];
                if(!$auser->Update($fUser['ID'], $new_user, true)) {
                    wl("update user error");
                    wl($auser->LAST_ERROR);
                }

                $ID = $bx_user['ID'];

            }
            else {
                // Не найден, добавляем
                $new_user  = [
                    'LOGIN' => $login,
                    'NAME' =>$user_data['data']['firstName'],
                    'LAST_NAME' =>$user_data['data']['lastName'],
                    'SECOND_NAME' =>$user_data['data']['patronymicName'],
                    'EMAIL' =>$user_data['data']['email'],
                    'PERSONAL_PHONE' =>$login,
                    'PHONE_NUMBER' =>$login,
                    'PASSWORD' => $pass,
                    'CONFIRM_PASSWORD' => $pass,
                    'ACTIVE' => 'Y',
                    'XML_ID' => $user_data['data']['id']
                ];
                $ID = $auser->Add( $new_user, true);


            }
            if ($ID > 0)
            {

                $arGroups = CUser::GetUserGroup($ID);
                $arGroups[] = 6;
                CUser::SetUserGroup($ID, $arGroups);

                $USER->Authorize($ID, 1);


                wl("login ok ".$login);

                $result = ['status'=>'ok', 'logged' =>$USER->GetID(), 'data'=> $res];
    		}
            else{


                wl("login error ".$login);
                wl($auser->LAST_ERROR);
            }
        }
    }
    else {
        $result = ['status'=>'error', 'text'=> 'Произошла ошибка', 'data'=> $res];
    }

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($result);
}
elseif ($action == 'salon_client')
{
    global $USER;

    CModule::IncludeModule('sale');
    $client = preg_replace("/\W/", "", $_REQUEST['code'] );

    $api = new apiLmx();

    $result = [];
    $error = 0;

    if (strlen($client) >= 5)
    {
        $lmxapp->authMerchantToken();

        $checkuser = $lmxapp->checkUser($client, ['account','profile','cards', 'coupon']);

        if ($checkuser['status'] == 'found')
        {
            $authclientresult = $lmxapp->authClientToken($checkuser['code']);

            if ($authclientresult['access_token']) {
                if (strlen($client) == 11)
                    $_SESSION['lmx']['phone'] = $client;
                else
                    $_SESSION['lmx']['card'] = $client;
                $api->setAuthToken($authclientresult['access_token']);

                $user_not_found = 0;
                $userinfo = $api->getUserData();
                $usercards = $api -> getUserCards();

                if (!is_array($usercards) || empty($usercards['data'])) {
                    $card_emit = $api->EmitVirtual();
                    //wl($card_emit);
                    $usercards = $api->getUserCards();

                    if (is_array($usercards) && !empty($usercards['data'])) {
                        $_SESSION['lmx']['client_card'] = $usercards['data'][0]['number'];
                    }
                }
                else{
                    $_SESSION['lmx']['client_card'] = $usercards['data'][0]['number'];
                }

                $_SESSION['lmx']['firstName'] = $userinfo['data']['firstName'];
                $_SESSION['lmx']['lastName'] = $userinfo['data']['lastName'];
                $_SESSION['lmx']['patronymicName'] = $userinfo['data']['patronymicName'];
                $_SESSION['lmx']['client'] = $client;
                if (is_array($usercards) && !empty($usercards['data'])) {
                    $data['client_cards'] = $usercards['data'];
                }
                $data['clientinfo'] = $userinfo;
                $data['clientauth'] = $authclientresult;


                $db_sales = CSaleOrderUserProps::GetList(
                    array("DATE_UPDATE" => "DESC"),
                    array("USER_ID" => $USER->GetID())
                );
                $count_profiles = 0;
                while ($ar_sales = $db_sales->Fetch())
                {

                    $count_profiles++;
                    if ($count_profiles >= 5){
                        CSaleOrderUserProps::Delete($ar_sales['ID']);
                        CSaleOrderUserPropsValue::DeleteAll($ar_sales['ID']);
                    }

                    //if ($count_profiles >= 5) break;
                }
                $db_sales = CSaleOrderUserProps::GetList(
                    array("DATE_UPDATE" => "DESC"),
                    array("USER_ID" => $USER->GetID(), "NAME"=>$_SESSION['lmx']['lastName']." ".$_SESSION['lmx']['firstName']." ".$_SESSION['lmx']['patronymicName'])
                );
                if ($ar_sales = $db_sales->Fetch()){//2022-10-14 19:32:48
                    CSaleOrderUserProps::Update($ar_sales['ID'], ['DATE_UPDATE'=>date("Y-m-d H:i:s")]);
                }
                else{

                    $arUpdate = [
                        //'DATE_UPDATE'=>date("Y-m-d H:i:s"),
                        "NAME"=>$_SESSION['lmx']['lastName']." ".$_SESSION['lmx']['firstName']." ".$_SESSION['lmx']['patronymicName'],
                        "USER_ID"=>$USER->GetID(),
                        "PERSON_TYPE_ID"=>"1"
                    ];

                    $PROF_ID = CSaleOrderUserProps::Add($arUpdate);

                    if ($PROF_ID > 0) {

                        $arFields = array(
                            "USER_PROPS_ID" => $PROF_ID,
                            "ORDER_PROPS_ID" => 1,
                            "NAME" => "Ф.И.О.",
                            "VALUE" => $_SESSION['lmx']['lastName']." ".$_SESSION['lmx']['firstName']." ".$_SESSION['lmx']['patronymicName']
                        );
                        CSaleOrderUserPropsValue::Add($arFields);
                        $arFields = array(
                            "USER_PROPS_ID" => $PROF_ID,
                            "ORDER_PROPS_ID" => 2,
                            "NAME" => "E-Mail",
                            "VALUE" => $USER->GetEmail()
                        );
                        CSaleOrderUserPropsValue::Add($arFields);
                        // Телефон
                        $arFields = array(
                            "USER_PROPS_ID" => $PROF_ID,
                            "ORDER_PROPS_ID" => 3,
                            "NAME" => "Телефон",
                            "VALUE" => $_SESSION['lmx']['phone']
                        );
                        CSaleOrderUserPropsValue::Add($arFields);
                        // ДК
                        $arFields = array(
                            "USER_PROPS_ID" => $PROF_ID,
                            "ORDER_PROPS_ID" => 14,
                            "NAME" => "№ дисконтной карты",
                            "VALUE" => $_SESSION['lmx']['client_card']
                        );
                        CSaleOrderUserPropsValue::Add($arFields);
                        //ENUM
                       $arFields = array(
                            "USER_PROPS_ID" => $PROF_ID,
                            "ORDER_PROPS_ID" => 18,
                            "NAME" => "Специальная скидка",
                            'VALUE' => 'D_PARTNER'
                        );

                        CSaleOrderUserPropsValue::Add($arFields);
                    }
                }
            }

            $log['USER_PHONE'] = $phone;
            $result = ['status'=>'ok', 'data'=> $data];
        }
        else{
            $user_not_found = 1;
            $result = ['status'=>'error', 'text'=> 'Произошла ошибка. Не найден.'.$client];
        }


        //$res = $api->ChangeEmailConfirm($code);



    }
    else {
        $result = ['status'=>'error', 'text'=> 'Произошла ошибка. Формат. '.$client];
    }

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($result);
}
elseif ($action == 'salon_client_exit') {
    unset($_SESSION['lmx']); unset($_SESSION['lmxapp']);

    $result = ['status'=>'ok'];

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($result);
}

