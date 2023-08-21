<?
define("NO_REGION_CHECK", "Y");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/loymax.php");

use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\Parser;
use Bitrix\Main\UserTable;

ini_set("display_errors", 1);
// Oauth secret
$secret = 'f3cf72998b4e41dc9b2a0ee237786c6f';


$action = '';
if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], array('get_token', 'get_confirm_code', 'check_confirm_code', 'register', 'login', 'confirm_sms', 'confirm_email', 'return', 'save_anketa', 'finish_reg', 'remind_pass', 'remind_pass_confirm', 'change_phone_start', 'change_email_start', 'change_phone_confirm','change_email_confirm', 'send_auth_sms', 'check_auth_sms', 'emailSubs', 'salon_client'))) {
    $action = strval($_REQUEST['action']);
} else
    die();

$api = new apiLmx;

if  ($action == 'return'){
    //w2l($_REQUEST, 1, 'return.log');
    if ($_REQUEST['code']) echo $_REQUEST['code'];
}
elseif  ($action == 'get_token'){

}
elseif ($action == 'check_phone')
{
    $parsedPhone = Parser::getInstance()->parse($_REQUEST['phone']);

    $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));

    if ($phone) {

        $user_res = $api->changePhone($phone);

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
// отправляем проверочный код
elseif ($action == 'get_confirm_code')
{
    $parsedPhone = Parser::getInstance()->parse($_REQUEST['phone']);

    unset($_SESSION['lmx']);
    $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));

    $result = [];
    $error = 0;

    if ($phone)
    {

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

                        wl('line 102');
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
                    $result = ['status'=> 'send', 'result'=>$reg_res, 'token' => $token];
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
    else {

        $result['status'] = 'error';
        $result['error'] = 'incorrect phone';
    }

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($result);
}
elseif ($action == 'check_confirm_code') {

    $confirm_code = intval($_REQUEST['code']);
    $parsedPhone = Parser::getInstance()->parse($_REQUEST['phone']);
    $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));


    $token = $_SESSION['lmx']['token'];
    $_SESSION['lmx']['phone'] = $phone;
    $api->setAuthToken($token);

    $res = $api->checkConfirmCode($confirm_code);

    if ($res['result']['state'] == 'Error' || $res['status'] == 'error')
    {
        $result = ['status'=>'fail', 'message'=>'Неверный код подтверждения', 'res'=>$res['result']];

    }
    else {

        $api->PasswordRequired('321123');


        $result = ['status'=>'confirmed', 'phone'=>$phone];
    }

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($result);
}
elseif($action == 'save_anketa')
{
    $data = $_REQUEST['anketa'];
    $data['phone'] = $_SESSION['lmx']['phone'];

    if (isset($_SESSION['lmx']['token']))
    {
        $token = $_SESSION['lmx']['token'];
        $api->setAuthToken($token);

        $update = [
            'FirstName' => $data['name'],
            'LastName' => $data['last_name'],
            'PatronymicName' => $data['secondname'],
            'BirthDay' => $data['birthday'],
            'Sex' => $data['sex'],
            'Mobile' => $data['phone'],
            'OldCard' => $data['oldcard']
        ];
w2l($update, 1, 'anketa.log');
        if (SITE_ID == 's1'){
            $update['SourceRegistration'] = '4';
        }
        else if (SITE_ID == 's2'){
            $update['SourceRegistration'] = '3';
        }
        else {
            if (in_array($_SESSION['MEDI_REGION'], ['Московская область', 'Москва и Московская область']) || $_SESSION['MEDI_CITY'] == 'Москва')
            {
                $update['SourceRegistration'] = '4';
            }
            else {
                $update['SourceRegistration'] = '5';
            }
        }


        if (!empty($_SESSION['lmx']['phone']))
        {
            $update['Mobile'] = $_SESSION['lmx']['phone'];
        }

        $update['SKS'] = false;
        if (!empty(strval($_REQUEST['SUBSCRIBE'])) && strval($_REQUEST['SUBSCRIBE']) == "1")
        {
            $update['SKS'] = true;
        }

        $UserQuestions = $api->getUserQuestions();

        $answers = [];
        foreach ($UserQuestions as $key => $value) {
            foreach ($value as $key2 => $value2) {
                if ($value2['type'] == 'QuestionGroup')
                {
                    foreach ($value2['questions'] as $k => $question) {
                        $answer  = [];
                        if ($question['questionType'] == 'String' && in_array($question['logicalName'], ['PatronymicName', 'LastName', 'FirstName', 'OldCard'])){
                            $answer = [
                                'questionId' => $question['id'],
                                'fixedAnswerIds' => '',
                                'value' => $update[$question['logicalName']],
                                'tag' => 0,
                                'questionGroupId' => $value2['id']

                            ];

                            $answers[] = $answer;
                        }
                        if ($question['questionType'] == 'Select' && in_array($question['logicalName'], ['Sex'])){
                            $answer = [
                                'questionId' => $question['id'],

                                'fixedAnswerIds' => [$update[$question['logicalName']]],
                                'value' => '',
                                'tag' => 0,
                                'questionGroupId' => $value2['id']

                            ];

                            $answers[] = $answer;
                        }
                        if ($question['questionType'] == 'Select' && in_array($question['logicalName'], [ 'SourceRegistration'])){
                            $answer = [
                                'questionId' => $question['id'],

                                'fixedAnswerIds' => [$update[$question['logicalName']]],
                                'value' => '',
                                'tag' => 0,
                                'questionGroupId' => $value2['id']

                            ];

                            $answers[] = $answer;
                        }

                        if ($question['questionType'] == 'String' && in_array($question['logicalName'], ['Mobile'])){
                            $answer = [
                                'questionId' => $question['id'],
                                'fixedAnswerIds' => '',
                                'value' => $update[$question['logicalName']],
                                'tag' => 0,
                                'questionGroupId' => $value2['id']

                            ];

                            $answers[] = $answer;
                        }
                        if ($question['questionType'] == 'Date' && in_array($question['logicalName'], ['BirthDay']) && !empty($update['BirthDay'])){
                            $ardate = explode("-", $update[$question['logicalName']]);
                            $tdate =  date("Y-m-d\T00:00:00\Z",  mktime(0,0,0,$ardate[1],$ardate[2], $ardate[0]));
                            $answer = [
                                'questionId' => $question['id'],
                                'fixedAnswerIds' => '',
                                'value' => $tdate,
                                'tag' => 0,
                                'questionGroupId' => $value2['id']

                            ];

                            $answers[] = $answer;
                        }
                        if ($question['questionType'] == 'Boolean' && in_array($question['logicalName'], ['SKS'])){
                            $answer = [
                                'questionId' => $question['id'],
                                'fixedAnswerIds' => '',
                                'value' => $update[$question['logicalName']],
                                'tag' => 0,
                                'questionGroupId' => $value2['id']

                            ];

                            $answers[] = $answer;
                        }

                    }
                }
        }
    }

        $answers = json_encode($answers);
w2l($answers, 1, "anketa.log");
        //print_r($answers);
        $UserAnswer = $api->setUserAnswers($answers);

        if (!$UserAnswer['data']['errors'])
        {
            /*
            $EmitVirtual = $api->EmitVirtual();
            wl($EmitVirtual);
            if ($EmitVirtual['data']['isVirtualCardEmissionAllowed'] == true)
            {
                $finishreg = $api->TryFinishRegistration();
                wl($finishreg);
            }*/
            $result = ['status' => 'finished'];
        }
        else {
            $result = ['status' => 'error', 'data' => $UserAnswer['data']['errors']];
        }

    }
    else {
        $result = ['status'=>'error', 'text'=> 'Истекло время жизни сессии '];
    }

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($result);
}

elseif($action == 'finish_reg')
{
    global $USER;

    if (isset($_SESSION['lmx']['token']))
    {
        $token = $_SESSION['lmx']['token'];
        $api->setAuthToken($token);

        $update = [];

        if (!empty(strval($_REQUEST['NAME'])))
        {
            $update['FirstName'] = strval($_REQUEST['NAME']);
        }
        if (!empty(strval($_REQUEST['LAST_NAME'])))
        {
            $update['LastName'] = strval($_REQUEST['LAST_NAME']);
        }
        if (!empty($_REQUEST['BIRTHDATE']))
        {
            $ardate = explode("-", $_REQUEST['BIRTHDATE']);
            $tdate =  date("Y-m-d\T00:00:00\Z",  mktime(0,0,0,$ardate[1],$ardate[2], $ardate[0]));

            $update['BirthDay'] = $tdate;
        }
        if (!empty(strval($_REQUEST['SEX'])))
        {
            $update['Sex'] = strval($_REQUEST['SEX']);
            //$update['Sex'] = strval($_REQUEST['SEX']);
        }
        if (!empty(strval($_REQUEST['EMAIL'])))
        {
            $update['Email'] = strval($_REQUEST['EMAIL']);
        }
        if (!empty($_SESSION['lmx']['phone']))
        {
            $update['Mobile'] = $_SESSION['lmx']['phone'];
        }
        elseif($_REQUEST['PHONE'])
        {
            $parsedPhone = Parser::getInstance()->parse($_REQUEST['phone']);
            $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));

            $update['Mobile'] = $phone;
        }
        $update['SMS'] = false;
        if (!empty(strval($_REQUEST['SUBSCRIBE'])) && strval($_REQUEST['SUBSCRIBE']) == "1")
        {
            $update['SMS'] = true;
            //$update['SKS'] = true;
        }
        if (SITE_ID == 's1'){

            $update['SourceRegistration'] = '4';

        }
        else if (SITE_ID == 's2'){
            $update['SourceRegistration'] = '3';
        }
        else {
            if (in_array($_SESSION['MEDI_REGION'], ['Московская область', 'Москва и Московская область']) || $_SESSION['MEDI_CITY'] == 'Москва')
            {
                $update['SourceRegistration'] = '4';
            }
            else {
                $update['SourceRegistration'] = '5';
            }
        }


        // Anketa

        $UserQuestions = $api->getUserQuestions();
        w2l("UserQuestions", 1, 'reg.log');
w2l($UserQuestions, 1, 'reg.log');
        $answers = [];
        foreach ($UserQuestions as $key => $value) {
            foreach ($value as $key2 => $value2) {
                if ($value2['type'] == 'QuestionGroup')
                {
                    foreach ($value2['questions'] as $k => $question) {
                        $answer  = [];
                        if ($question['questionType'] == 'String' && in_array($question['logicalName'], ['PatronymicName', 'LastName', 'FirstName'])){
                            $answer = [
                                'questionId' => $question['id'],
                                'fixedAnswerIds' => '',
                                'value' => $update[$question['logicalName']],
                                'tag' => 0,
                                'questionGroupId' => $value2['id']

                            ];

                            $answers[] = $answer;
                        }
                        if ($question['questionType'] == 'Select' && in_array($question['logicalName'], [ 'Sex'])){
                            $answer = [
                                'questionId' => $question['id'],

                                'fixedAnswerIds' => [$update[$question['logicalName']]],
                                'value' => '',
                                'tag' => 0,
                                'questionGroupId' => $value2['id']

                            ];

                            $answers[] = $answer;
                        }
                        if ($question['questionType'] == 'Select' && in_array($question['logicalName'], [ 'SourceRegistration'])){
                            $answer = [
                                'questionId' => $question['id'],

                                'fixedAnswerIds' => [$update[$question['logicalName']]],
                                'value' => '',
                                'tag' => 0,
                                'questionGroupId' => $value2['id']

                            ];

                            $answers[] = $answer;
                        }

                        if ($question['questionType'] == 'Date' && in_array($question['logicalName'], ['BirthDay'])){
                            $ardate = explode("-", $update[$question['logicalName']]);
                            $tdate =  date("Y-m-d\T00:00:00\Z",  mktime(0,0,0,$ardate[1],$ardate[2], $ardate[0]));
                            $answer = [
                                'questionId' => $question['id'],
                                'fixedAnswerIds' => '',
                                'value' => $tdate,
                                'tag' => 0,
                                'questionGroupId' => $value2['id']

                            ];

                            $answers[] = $answer;
                        }

                        if ($question['questionType'] == 'String' && in_array($question['logicalName'], ['Mobile'])){
                            $answer = [
                                'questionId' => $question['id'],
                                'fixedAnswerIds' => '',
                                'value' => $update[$question['logicalName']],
                                'tag' => 0,
                                'questionGroupId' => $value2['id']

                            ];

                            $answers[] = $answer;
                        }

                        if ($question['questionType'] == 'Boolean' && in_array($question['logicalName'], ['SMS', 'SKS'])){
                            $answer = [
                                'questionId' => $question['id'],
                                'fixedAnswerIds' => '',
                                'value' => $update[$question['logicalName']],
                                'tag' => 0,
                                'questionGroupId' => $value2['id']

                            ];

                            $answers[] = $answer;
                        }

                    }
                }
        }
    }

        $answers = json_encode($answers);

        //print_r($answers);
        $UserAnswer = $api->setUserAnswers($answers);
        w2l("setUserAnswer1", 1, 'reg.log');
        w2l($UserAnswer, 1, 'reg.log');
        if (is_object($UserAnswer) && !$UserAnswer->isSuccess()) {
            $UserAnswer = $api->setUserAnswers($answers);

            w2l("setUserAnswer", 1, 'reg.log');
            w2l($UserAnswer, 1, 'reg.log');
        }
        if (!$UserAnswer['data']['errors'])
        {

            if ($_REQUEST['EMAIL'] != '')
            {
                $email_res = $api->SetEmail($_REQUEST['EMAIL']);wl("email_res");
                if($email_res['status'] == 'ok')
                {
                    $email_wait_confirm = 1;
                }
            }
            $card_res = $api->EmitVirtualCheck();wl("card_res");
            wl($card_res);
            if ($card_res)
            {
                $card_emit = $api->EmitVirtual();wl("card_emit");
                wl($card_emit);

                // Questions
                $res_finish = $api->TryFinishRegistration();

            }

            $token = $_SESSION['lmx']['token'];
            $api->setAuthToken($token);

            if (!empty($_SESSION['lmx']['phone']))
            {
                $phone = $_SESSION['lmx']['phone'];
                $user_data = $api->getUserData();
            wl("userdata");wl($user_data);
                $pass = '321123';

                $login = '+'.$phone;

                $arFields['LOGIN'] = $login;

                $auser = new CUser;

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

                $obUser = CUser::GetByLogin($login);

                if ($find_user == 1)
                {

                    $ID = $bx_user['ID'];
                    $new_user  = [
                        'NAME' =>$user_data['data']['firstName'],
                        'LAST_NAME' =>$user_data['data']['lastName'],
                        'SECOND_NAME' =>$user_data['data']['patronymicName'],
                        'EMAIL' =>$user_data['data']['email'],
                        'PERSONAL_PHONE' =>$login,
                        'LOGIN' => $login,
                        'ACTIVE' => 'Y',
                        'XML_ID' => $user_data['data']['id'],

                        'PASSWORD' => $pass,
                        'CONFIRM_PASSWORD' => $pass,
                        'PERSONAL_PHONE' =>$login,

                    ];
                    if(!$auser->Update($bx_user['ID'], $new_user, false)) {
                        wl("auser->LAST_ERROR");
                        wl($auser->LAST_ERROR);
                    }


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
                        wl("new_user");
                        wl($auser);
                    $ID = $auser->Add( $new_user);


                }
                if ($ID > 0)
                {
                    $USER->Authorize($ID, 1);


                    $result = ['status'=>'ok', 'logged' =>$USER->GetID(), 'data'=> $res];
        		}
                else{

                    wl("login error");
                }
            }

            #$_SESSION['lmx']['token'] = $res['access_token'];
            #$_SESSION['lmx']['phone'] = $phone;


            $result = ['status' => 'finished'];
        }
        else {
            $result = ['status' => 'error', 'data' => $UserAnswer['data']['errors']];
        }

    }
    else {
        $result = ['status'=>'error', 'text'=> 'Истекло время жизни сессии '];
    }

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($result);
}
elseif ($action == 'remind_pass')
{
    $parsedPhone = Parser::getInstance()->parse($_REQUEST['phone']);

    $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));

    $result = [];
    $error = 0;

    if ($phone)
    {

        $res = $api->ResetPasswordStart($phone);
        $result = ['status'=>'ok', 'data'=> $res];
    }
    else {
        $result = ['status'=>'error', 'text'=> 'Неправильный формат номера телефона'];
    }

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($result);
}
elseif ($action == 'remind_pass_confirm')
{
    $parsedPhone = Parser::getInstance()->parse($_REQUEST['phone']);
    $code = preg_replace("/(\D)*/", "", $_REQUEST['code'] );
    $pass = $_REQUEST['pass'];

    $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));

    $result = [];
    $error = 0;

    if ($phone)
    {

        $res = $api->ResetPasswordConfirm($phone, $code, $pass);
        $result = ['status'=>'ok', 'data'=> $res];
    }
    else {
        $result = ['status'=>'error', 'text'=> 'Неправильный формат номера телефона'];
    }

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($result);
}
elseif ($action == 'send_auth_sms')
{
    $parsedPhone = Parser::getInstance()->parse($_REQUEST['phone']);

    $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));

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
    header("Content-type: application/json; charset=utf-8");
    echo json_encode($result);
}
elseif ($action == 'check_auth_sms')
{
    $parsedPhone = Parser::getInstance()->parse($_REQUEST['phone']);
    $code = preg_replace("/(\D)*/", "", $_REQUEST['code'] );
    $pass = $_REQUEST['code'];

    $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));

    $result = [];
    $error = 0;

    if ($phone)
    {

        $res = $api->ResetPasswordConfirm($phone, $code, $pass);

        if ($res['result']['state'] == 'Fail')
        {
            wl("loginerror1");
                wl($phone);
            wl($res);
            $result = ['status'=>'error', 'text'=> 'Неправильный код'];
        }
        elseif ($res['status'] == 'fail')
        {

                wl("loginerror2");
                wl($phone);
                wl($res);
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
elseif ($action == 'change_phone_start')
{
    $parsedPhone = Parser::getInstance()->parse($_REQUEST['phone']);

    $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));

    $result = [];
    $error = 0;

    if ($phone)
    {
        $api->setAuthToken($_SESSION['lmx']['token']);


        $res = $api->ChangePhoneStart($phone);

        if ($res['status'] == 'fail')
        {
            $result = ['status'=>'fail', 'data'=> $res];
            unset($_SESSION['lmx']['new_phone']);
        }
        else {
            $result = ['status'=>'ok', 'data'=> $res['data']];
            $_SESSION['lmx']['new_phone'] = '+'.$phone;
        }
    }
    else {
        $result = ['status'=>'error', 'text'=> 'Неправильный формат номера телефона'];
    }

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($result);
}
elseif ($action == 'change_phone_confirm')
{
    global $USER;
    $code = preg_replace("/(\D)*/", "", $_REQUEST['code'] );


    $result = [];
    $error = 0;

    if (strlen($code) == 6)
    {

        $api->setAuthToken($_SESSION['lmx']['token']);


        $res = $api->ChangePhoneConfirm($code);
        $result = ['status'=>'ok', 'data'=> $res['data']];

        $USER->Update($USER->GetID(), ['LOGIN'=>$_SESSION['lmx']['new_phone']]);
        unset($_SESSION['lmx']);
        $USER->Logout();
    }
    else {
        $result = ['status'=>'error', 'text'=> 'Произошла ошибка'];
    }

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($result);
}
elseif($action == 'emailSubs') {

    $email = $_REQUEST['email'];
    $result = [];
    $error = 0;
    if ($email)
    {
        $api->setAuthToken($_SESSION['lmx']['token']);
        wl("subs_email");
        wl($email);
        $api->ChangeEmailCancel();

        $res = $api->SetEmail($email);

        if ($res['status'] == 'error')
        {
            $result = ['status'=>'fail', 'data'=> $res];
            unset($_SESSION['lmx']['new_email']);
        }
        else {
            $result = ['status'=>'ok', 'data'=> $res['data']];
            $_SESSION['lmx']['new_email'] = $email;
            $_SESSION['ls_email_subs'] = '1';
            unset($_SESSION['lmx']['new_email_send']);
        }
    }
    else {
        $result = ['status'=>'error', 'text'=> 'Неправильный формат email'];
    }

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($result);
}
elseif ($action == 'change_email_start')
{
    $email = $_REQUEST['email'];


    $result = [];
    $error = 0;

    if ($email)
    {
        $api->setAuthToken($_SESSION['lmx']['token']);
        wl("change_email");
        wl($email);
        $api->ChangeEmailCancel();

        if (!isset($_SESSION['lmx']['new_email_send'] ))
        {

            $res = $api->ChangeEmailStart($email);
            $_SESSION['lmx']['new_email_send'] = '1';
        }
        else {
            $res = $api->ChangeEmailReSend($email);

        }

        $res = $api->ChangeEmailStart($email);

        wl($res);
        if ($res['status'] == 'fail')
        {
            $result = ['status'=>'fail', 'data'=> $res];
            unset($_SESSION['lmx']['new_email']);
        }
        else {
            $result = ['status'=>'ok', 'data'=> $res['data']];
            $_SESSION['lmx']['new_email'] = $email;
            unset($_SESSION['lmx']['new_email_send']);
        }
    }
    else {
        $result = ['status'=>'error', 'text'=> 'Неправильный формат email'];
    }

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($result);
}
elseif ($action == 'change_email_confirm')
{
    global $USER;
    $code = preg_replace("/(\D)*/", "", $_REQUEST['code'] );


    $result = [];
    $error = 0;

    if (strlen($code) == 6)
    {
        $api->setAuthToken($_SESSION['lmx']['token']);


        $res = $api->ChangeEmailConfirm($code);

        $result = ['status'=>'ok', 'data'=> $res['data']];
        wl("change_email_confirm");
        wl($USER->GetID());
        $USER->Update($USER->GetID(), ['EMAIL'=>$_SESSION['lmx']['new_email']]);

    }
    else {
        $result = ['status'=>'error', 'text'=> 'Произошла ошибка'];
    }

    header("Content-type: application/json; charset=utf-8");
    echo json_encode($result);
}
die;
