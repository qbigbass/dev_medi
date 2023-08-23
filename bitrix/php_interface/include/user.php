<?

use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\Parser;
use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;
use Bitrix\Main\UserTable;

AddEventHandler("main", "OnBeforeUserAdd", 'modifyLogin');
AddEventHandler("main", "OnAfterUserAdd", 'userAppendGroup');

function userAppendGroup(&$arFields){

    if($arFields["ID"]>0) {
        if ($arFields['LID'] != 's1')
        {

            $arGroups = CUser::GetUserGroup($arFields['ID']);

            if ($arFields['LID'] == 's2')
            {
                // group s2 24
                $arGroups[] = 24;
            }
            else {
                // group s0 27
                $arGroups[] = 27;
            }

        }
        else{
            // group s1 28
            $arGroups[] = 28;
        }
        CUser::SetUserGroup($arFields['ID'], $arGroups);
    }
}

function modifyLogin(&$arFields)
{
    global $APPLICATION;

    if($arFields['EXTERNAL_AUTH_ID'] != 'sale')
    {
        if ($arFields['PHONE_NUMBER'] != '')
        {
            $uphone = $arFields['PHONE_NUMBER'];
        }
        elseif ($arFields['PERSONAL_PHONE'] != '')
        {
            $uphone = $arFields['PERSONAL_PHONE'];
        }
        elseif ($arFields['PERSONAL_MOBILE'] != '')
        {
            $uphone = $arFields['PERSONAL_MOBILE'];
        }
        elseif ($arFields['WORK_PHONE'] != '')
        {
            $uphone = $arFields['WORK_PHONE'];
        }

        $parsedPhone = Parser::getInstance()->parse($uphone);
        $new_phone = $parsedPhone->format(Format::E164);
        if (!empty($new_phone) && strlen($new_phone) == '10' )
        {
            $arFields['LOGIN'] = $new_phone;
            $arFields['PERSONAL_PHONE'] = $arFields['LOGIN'];
            $arFields['PERSONAL_MOBILE'] = $arFields['LOGIN'];

            $APPLICATION->set_cookie("_msuid", generate_msuid($parsedPhone), time() + 86400*365);
        }
    }


    return $arFields;
}


function generate_msuid($phone){
    $uid = false;
    $phone = str_replace("+7", "", $phone);
    $phone = preg_replace("/(\D*)/", "", $phone);
    $str = 'MS.UID.';
    if (strlen($phone) == '10')
    {
        $time = '0'.time();
        $str .= $time[0].$phone[9].$phone[8].$time[1].$phone[6].$phone[7].$time[2].$time[3].$phone[2].$phone[3].$time[4].$time[5];
        $str .= $phone[4].$phone[5].$time[6].$time[7].$phone[0].$phone[1].$time[8].$time[9].$time[10];
    }
    if (strlen($str) == 28 )
    {
        $uid = $str;
    }

    return $uid;
}
function msuid2phone($uid){

    $phone = '';
    $time = '';

    $str = str_replace('MS.UID.', '', $uid);
    if (strlen($str) == '21')
    {
        $time = $str[3].$str[6].$str[7].$str[10].$str[11].$str[14].$str[15].$str[18].$str[19].$str[20];
        $phone = $str[16].$str[17].$str[8].$str[9].$str[12].$str[13].$str[4].$str[5].$str[2].$str[1];
    }


    return $phone.'|'.date("d-m-Y H:i:s", $time);
}

AddEventHandler("main", "OnAfterUserAuthorize", Array("MyClass", "OnAfterUserAuthorizeHandler"));

AddEventHandler("main", "OnBeforeUserLogin", Array("MyClass", "OnBeforeUserLoginHandler"));
AddEventHandler("main", "OnAfterUserLogout", Array("MyClass", "OnAfterUserLogoutHandler"));

class MyClass
{
    // создаем обработчик события "OnAfterUserAuthorize"
    function OnAfterUserAuthorizeHandler($arUser)
    {
        $phone = '';
        if ($arUser['user_fields']['PERSONAL_MOBILE'] != '')
        {
            $parsedPhone = Parser::getInstance()->parse($arUser['user_fields']['PERSONAL_MOBILE']);
            $phone =  $parsedPhone->format(Format::E164);
        }
        elseif ($arUser['user_fields']['PERSONAL_PHONE'] != '')
        {
            $parsedPhone = Parser::getInstance()->parse($arUser['user_fields']['PERSONAL_PHONE']);
            $phone =  $parsedPhone->format(Format::E164);
        }
        if ($phone != '')
        {
            set_msuid($phone);
        }
    }

    // создаем обработчик события "OnBeforeUserLogin"
    function OnBeforeUserLoginHandler(&$arFields)
    {

        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/loymax.php");

        $phone = $arFields['LOGIN'];
        $password = $arFields['PASSWORD'];
        //$phone = str_replace("+", "", $phone);
        $phone = preg_replace("/(\D*)/", "", $phone);

        if ($phone != '' && strlen($phone) == 11)
        {

            $api = new apiLmx;
            $res = $api->authToken($phone, $password);
            if (is_array($res) && $res['access_token'])
            {
                // Авторизация успешна
                $_SESSION['lmx']['phone'] = $phone;
                $api->setAuthToken($res['access_token']);
                $user_data = $api->getUserData();


                $login = '+'.$phone;
                $arFields['LOGIN'] = $login;

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


                if ($find_user == 1)
                {
                    $new_user  = [
                        'NAME' =>$user_data['data']['firstName'],
                        'LAST_NAME' =>$user_data['data']['lastName'],
                        'SECOND_NAME' =>$user_data['data']['patronymicName'],
                        'EMAIL' =>$user_data['data']['email'],
                        'PASSWORD' => $password,
                        'CONFIRM_PASSWORD' => $password,
                        'PERSONAL_PHONE' =>$login,
                        'LOGIN' => $login,
//                      'PHONE_NUMBER' =>$login,
                        'ACTIVE' => 'Y',
                        'XML_ID' => $user_data['data']['id']

                    ];
                    if(!$auser->Update($bx_user['ID'], $new_user, true)) {
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
                        'PASSWORD' => $password,
                        'CONFIRM_PASSWORD' => $password,
                        'ACTIVE' => 'Y',
                        'XML_ID' => $user_data['data']['id']
                    ];
                    $ID = $auser->Add( $new_user, true);
                    if (intval($ID) > 0)
                    {
                        wl("addded");
                    }
                    else
                        wl($auser->LAST_ERROR);
                            return false;
                }


                $arGroups = CUser::GetUserGroup($ID);
                $arGroups[] = 6;
                CUser::SetUserGroup($ID, $arGroups);

            }
            else {
                unset($_SESSION['lmx']);

                setcookie('lmx[token]', '', time());
                setcookie('lmx[phone]', '', time());

                return false;
            }

        }
    }
    function OnAfterUserLogoutHandler($arParams)
    {
        unset($_SESSION['lmx']);
        unset($_SESSION['lmxapp']);

        setcookie('lmx[token]', '', time());
        setcookie('lmx[phone]', '', time());

        setcookie('lmxapp[mtoken]', '', time());
        setcookie('lmxapp[mexpires]', '', time());

    }
}

function set_msuid($phone)
{
    global $APPLICATION;
    $parsedPhone = Parser::getInstance()->parse($phone);
    $phone =  $parsedPhone->format(Format::E164);
    $msuid = generate_msuid($phone);

    setcookie("_msuid", $msuid, time() + 86400*365);
    return $msuid;

}
