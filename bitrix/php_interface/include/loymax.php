<?


use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\Parser;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;

class apiLmx {

    private $phone;
    private $public_api_url;
    private $system_api_url;
    private $api_url;
    private $secret;
    private $authToken;


    public function __construct()
    {

        $this->secret = 'f3cf72998b4e41dc9b2a0ee237786c6f';
        $this->public_api_url = 'https://medirus.loymax.tech/publicapi';
        $this->system_api_url = 'https://medirus-stg.loymax.tech/api';
        $this->authToken = '';
    }
/*
    public function checkPhoneExists($phone)
    {
        $method = '/Users/';

        $result = new Result();

        $response = $this->query('get', $method,  ['phone'=>$phone]);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
        }
        else {
            $data = $response->getData();

            return $data;
        }
        return $result;
    }*/

    public function setUserPhone($phone)
    {

    }

    public function authToken($phone, $password)
    {
        $method = '/token';

        $parsedPhone = Parser::getInstance()->parse($phone);
        $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));

        $result = new Result();

        $response = $this->query('post', $method,  ['username'=>$phone, 'password'=>$password, 'grant_type'=>'password']);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());

            return ['result' => $result, 'status'=>'error'];
        }
        else {
            $data = $response->getData();

            return $data;
        }
        return $result;
    }


    public function setAuthToken($token)
    {

        $this->authToken = $token;

        $_SESSION['lmx']['token'] = $token;


        setcookie('lmx[token]', $token, time()+30*86400, "/");
        if ($_SESSION['lmx']['phone']) {
            setcookie('lmx[phone]', $_SESSION['lmx']['phone'], time() + 30 * 86400, '/');
        }

        return true;
    }
    public function getAuthToken()
    {
        return $this->authToken;
    }

    public function BeginRegistration($phone, $password = '')
    {
        $method = '/v1.2/Registration/BeginRegistration';

        $parsedPhone = Parser::getInstance()->parse($phone);
        $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));

        $result = new Result();

        $data = ['login'=>$phone];

        if ($password != '')
        {
            $data['password'] = $password;
        }
        $response = $this->query('post', $method, $data );

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            return ['result' => $result, 'status'=>'error'];
        }
        else {
            $data = $response->getData();
            //wl($data);
            if($data['data']['state'] == 'Success') {
                $access_token = $data['data']['authToken'];
                //$refresh_token = $data['data']['authResult']['refresh_token'];

                // запоминаем токен
                $_SESSION['lmx']['phone'] = $phone;
                $this->setAuthToken($access_token);


                // получаем список обязательных шагов регистрации
                $reg_steps = $this->getRegSteps();
                $reg_finished = true;


                if (!empty($reg_steps['data']['data']['actions']))
                {
                    foreach ($reg_steps['data']['data']['actions'] as $k=>$step)
                    {
                        // если еще не выполнено, делаем
                        if ($step['actionState'] == 'Required' && !$step['isDone'])
                        {
                            $reg_finished = false;
                        }
                    }
                }
                if (!$reg_finished)
                {
                    return $reg_steps['data']['data']['actions'];
                }
                else {
                    wl("lmx 142");
                    return true;
                }

            }
            else {
                return $data['data'];
            }
        }
    }

    public function getRegSteps()
    {
        $method = '/v1.2/User/RegistrationActions';

        $result = new Result();

        $response = $this->query('get', $method,  [], 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());

            return ['result' => $result->getErrorMessages(), 'status'=>'error'];
        }
        else {
            $data = $response->getData();

            return ['status' => 'ok', 'data' => $data];
        }
        return $result;
    }

    public function AcceptTenderOffer(){
        $method = '/v1.2/User/AcceptTenderOffer';

        $result = new Result();

        $response = $this->query('post', $method,  [], 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            return ['result' => $result->getErrorMessages(), 'status'=>'error'];
        }
        else {
            $data = $response->getData();

            return ['status' => 'ok', 'data' => $data];
        }
    }
    public function GetUserPhone(){
        $method = '/v1.2/User';

        $result = new Result();

        $response = $this->query('get', $method,  ['payload'=>'Attributes.Mobile'], 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());

            return ['result' => $result->getErrorMessages(), 'status'=>'error'];
        }
        else
        {
            $data = $response->getData();

            return ['status' => 'ok', 'data' => $data];
        }
    }
    public function ChangePhone($phone){
        $method = '/v1.2/User/PhoneNumber/';


        $parsedPhone = Parser::getInstance()->parse($phone);
        $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));

        $result = new Result();

        $response = $this->query('post', $method,  ['phoneNumber'=>$phone], 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());

            return ['result' => $result->getErrorMessages(), 'status'=>'error'];
        }
        else
        {
            $data = $response->getData();

            return ['status' => 'ok', 'data' => $data];
        }
    }
    public function SendConfirmCode($phone){
        //$method = '/v1.2/User/PhoneNumber/SendConfirmCode';

        $parsedPhone = Parser::getInstance()->parse($phone);
        $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));

        $method = '/v1.2/User/PhoneNumber/';
        $result = new Result();

        $response = $this->query('post', $method, ['phoneNumber'=>$phone], 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            return ['result' => $result->getErrorMessages(), 'status'=>'error'];
        }
        else {

            $data = $response->getData();

            return ['status' => 'ok', 'data' => $data];
        }
    }
    public function checkConfirmCode($code){


        $code = preg_replace("/(\D)*/", "", $code);

        $method = '/v1.2/User/PhoneNumber/Confirm';

        $result = new Result();
        $params = ['confirmCode'=>$code, 'password'=>$code];

        $response = $this->query('post', $method,  $params , 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());

            return ['result' => $result->getErrorMessages(), 'status'=>'error'];
        }
        else {

            $data = $response->getData();

            if ($data['data']['result']['state'] == 'Error')
            {
                 return ['result' => $data['data']['result']['message'], 'status'=>'error'];
                 die('error');
            }

            if ($data['data']['access_token']){

                $access_token = $data['data']['access_token'];
                $refresh_token = $data['data']['refresh_token'];
            }
            elseif ($data['data']['authResult']['access_token']){

                $access_token = $data['data']['authResult']['access_token'];
                $refresh_token = $data['data']['authResult']['refresh_token'];
            }



            // запоминаем токен
            $this->setAuthToken($access_token);

            return ['status' => 'ok', 'data' => $data];
        }
    }
    public function PasswordRequired($password){
        $method = '/v1.2/User/Password/Set';
        $result = new Result();

        $response = $this->query('post', $method,  ['password'=>$password], 1 );

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            wl('pass errors');
            //wl($result);
            return ['result' => $result->getErrorMessages(), 'status'=>'error'];
        }
        else {

            $data = $response->getData();

            if ($data['data']['access_token']){

                $access_token = $data['data']['access_token'];
                $refresh_token = $data['data']['refresh_token'];
            }
            elseif ($data['data']['authResult']['access_token']){

                $access_token = $data['data']['authResult']['access_token'];
                $refresh_token = $data['data']['authResult']['refresh_token'];
            }

            // запоминаем токен
            $this->setAuthToken($access_token);

            return $data;
        }
    }

    public function SetEmail($email){
        //$method = '/v1.2/User/PhoneNumber/SendConfirmCode';

        $parsedPhone = Parser::getInstance()->parse($phone);
        $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));

        $method = '/v1.2/User/Email/';
        $result = new Result();

        $response = $this->query('post', $method, ['email'=>$email], 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            return ['result' => $result->getErrorMessages(), 'status'=>'error'];
        }
        else {

            $data = $response->getData();

            return ['status' => 'ok', 'data' => $data];
        }
    }

    public function getUserQuestions(){
        $method = '/v1.2/User/Questions';

        $result = new Result();

        $response = $this->query('get', $method,  [], 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
        }
        else {
            $data = $response->getData();

            return $data;
        }
        return $result;
    }
    public function setUserAnswers($answers) {
        $method = '/v1.2/User/Answers';
        $result = new Result();

        $response = $this->query('post', $method, $answers, 1, 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            wl('set answers errors');
            wl($result);wl($answers);
            return $result;
        }
        else {

            $data = $response->getData();

            return $data;
        }
    }
    public function getUserAnswers(){
        $method = '/v1.2/User/Answers';

        $result = new Result();

        $response = $this->query('get', $method,  [], 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
        }
        else {
            $data = $response->getData();

            return $data;
        }
        return $result;
    }

    function getUserByPhone($phone) {


        $url = '/api/Users/';



    }
    function getUserById($uid) {
        global $api_url;

        $url = '/api/Users/'.$uid;



    }
    function getUserToken($login, $password) {

        $method = '/token';

        $result = new Result();

        $data = json_encode(['username'=>$login, 'password'=>$password, 'grant_type'=>'password']);

        $response = $this->query('post', $method,  $data);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            return $result;
        }
        else {
            $data = $response->getData();

            return $data;
        }

        return $result;

    }
    function getUserBalance() {

        $method = '/v1.2/User/Balance';
        $result = new Result();

        $response = $this->query('get', $method,  [], 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            wl('balance errors');
            //wl($result);
            return $result;
        }
        else {

            $data = $response->getData();

            return $data;
        }

    }

    function getUserBalanceDetailed() {

        $method = '/v1.2/User/DetailedBalance';
        $result = new Result();

        $response = $this->query('get', $method,  [], 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            wl('detail balance errors');
            //wl($result);
            return $result;
        }
        else {

            $data = $response->getData();

            return $data;
        }

    }

    function getUserData() {

        $method = '/v1.2/User';
        $result = new Result();

        $response = $this->query('get', $method,  [], 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            wl('user data errors');
            //wl($result);

            $response = $this->query('get', $method,  [], 1);

            if (!$response->isSuccess()) {
                $result->addErrors($response->getErrors());
                wl('user data errors');
                wl($result);
                return $result;
            }
            else {

                $data = $response->getData();

                return $data;
            }
        }
        else {

            $data = $response->getData();

            return $data;
        }

    }
    function getUserCards() {

        $method = '/v1.2/Cards';
        $result = new Result();

        $response = $this->query('get', $method,  [], 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            wl('cards data errors');
            wl($result);
            return $result;
        }
        else {

            $data = $response->getData();

            return $data;
        }

    }

    function EmitVirtualCheck(){
        $method = '/v1.2/Cards/EmitVirtual';
        $result = new Result();

        $response = $this->query('get', $method,  [], 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            wl('EmitVirtuala errors');
            wl($result);
            return $result;
        }
        else {

            $data = $response->getData();

            return $data;
        }
    }
    function EmitVirtual(){
        $method = '/v1.2/Cards/EmitVirtual';
        $result = new Result();

        $response = $this->query('put', $method,  [], 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            wl('EmitVirtual 2 errors');
            wl($result);
            return $result;
        }
        else {

            $data = $response->getData();

            if ($data['data']['access_token']){

                $access_token = $data['data']['access_token'];
                $refresh_token = $data['data']['refresh_token'];
            }
            elseif ($data['data']['authResult']['access_token']){

                $access_token = $data['data']['authResult']['access_token'];
                $refresh_token = $data['data']['authResult']['refresh_token'];
            }




            // запоминаем токен
            $this->setAuthToken($access_token);

            return $data;
        }
    }

    function TryFinishRegistration(){

        $method = '/v1.2/Registration/TryFinishRegistration';

        $result = new Result();

        $response = $this->query('post', $method,  [], 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            return $result;
        }
        else {
            $data = $response->getData();
            //wl("try finiush");
            //wl($data);
            if ($data['data']['access_token']){

                $access_token = $data['data']['access_token'];
                $refresh_token = $data['data']['refresh_token'];
            }
            elseif ($data['data']['authResult']['access_token']){

                $access_token = $data['data']['authResult']['access_token'];
                $refresh_token = $data['data']['authResult']['refresh_token'];
            }

            // запоминаем токен
            if ($access_token)
                $this->setAuthToken($access_token);

            return $data;
        }

        return $result;
    }

    public function ResetPasswordStart($phone){
        $method = '/v1.2/ResetPassword/Start';

        $result = new Result();

        $data = json_encode(['notifierIdentity'=>$phone]);

        $response = $this->query('post', $method,  $data, 0, 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            return ['status'=>'fail', 'result'=>$result];
        }
        else {
            $data = $response->getData();

            return ['status' => 'ok', 'data' =>$data];
        }
    }

    public function ResetPasswordConfirm($phone, $code, $password){
        $method = '/v1.2/ResetPassword/Confirm';

        $result = new Result();

        $data = json_encode(['notifierIdentity'=>$phone, 'confirmCode'=>$code, 'newPassword'=>$password]);

        $response = $this->query('post', $method,  $data, 0, 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            return ['status'=>'fail', 'result'=>$result];
        }
        else {

            $data = $response->getData();

            if ($data['result']['state'] == 'Error')
            {
                return ['status'=>'fail', 'result'=>'incorrect code'];
            }
            else {


                if ($data['data']['access_token']){

                    $access_token = $data['data']['access_token'];
                    $refresh_token = $data['data']['refresh_token'];
                }
                elseif ($data['data']['authResult']['access_token']){

                    $access_token = $data['data']['authResult']['access_token'];
                    $refresh_token = $data['data']['authResult']['refresh_token'];
                }

                // запоминаем токен
                $this->setAuthToken($access_token);
            }

            return $data;
        }

        return $result;
    }

    public function ChangePhoneStart($phone){
        $method = '/v1.2/User/PhoneNumber';

        $result = new Result();

        $data = ['phoneNumber'=>$phone];

        $response = $this->query('post', $method,  $data, 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            return ['status'=>'fail', 'result'=>$result];
        }
        else {
            $data = $response->getData();

            return ['status' => 'ok', 'data' =>$data];
        }
    }

    public function ChangePhoneConfirm($code){
        $method = '/v1.2/User/PhoneNumber/Confirm';

        $result = new Result();

        $data = ['confirmCode'=>$code];

        $response = $this->query('post', $method,  $data, 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            return ['status'=>'fail', 'result'=>$result];
        }
        else {
            $data = $response->getData();

            return ['status' => 'ok', 'data' =>$data];
        }
    }

    public function ChangeEmailStart($email){
        $method = '/v1.2/User/Email';

        $result = new Result();

        $data = ['email'=>$email];

        $response = $this->query('post', $method,  $data, 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            return ['status'=>'fail', 'result'=>$result];
        }
        else {
            $data = $response->getData();

            return ['status' => 'ok', 'data' =>$data];
        }
    }

    public function ChangeEmailConfirm($code){
        $method = '/v1.2/User/Email/Confirm';

        $result = new Result();

        $data = ['confirmCode'=>$code];

        $response = $this->query('post', $method,  $data, 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            return ['status'=>'fail', 'result'=>$result];
        }
        else {
            $data = $response->getData();

            return ['status' => 'ok', 'data' =>$data];
        }
    }

    public function ChangeEmailLinkConfirm($code, $person){
        $method = '/v1.2/User/Email/LinkConfirm';

        $result = new Result();

        $data = ['confirmCode'=>$code, 'personID'=>$person];

        $response = $this->query('post', $method,  $data);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            return ['status'=>'fail', 'result'=>$result];
        }
        else {
            $data = $response->getData();

            return ['status' => 'ok', 'data' =>$data];
        }
    }
    public function ChangeEmailReSend($email){
        $method = '/v1.2/User/Email/SendConfirmCode';

        $result = new Result();

        $data = [];

        $response = $this->query('post', $method,  $data, 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            return ['status'=>'fail', 'result'=>$result];
        }
        else {
            $data = $response->getData();

            return ['status' => 'ok', 'data' =>$data];
        }
    }
    public function ChangeEmailCancel(){
        $method = '/v1.2/User/Email/CancelChange';

        $result = new Result();

        $data = [];

        $response = $this->query('post', $method,  $data, 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            return ['status'=>'fail', 'result'=>$result];
        }
        else {
            $data = $response->getData();

            return ['status' => 'ok', 'data' =>$data];
        }
    }


    public function userHistory ($filter = ['filter'=>['count'=>10]]){
        $method = '/v1.2/History';
        $result = new Result();

        //$filter['filter']['currentUser'] = "true";
        $filter['filter']['historyItemType']  = "All";

        $response = $this->query('get', $method,  $filter, 1, 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            return ['status'=>'fail', 'result'=>$result];
        }
        else {
            $data = $response->getData();

            return ['status' => 'ok', 'data' =>$data];
        }
    }


    public function userStatus (){
        $method = '/v1.2/User/Status';
        $result = new Result();

        $response = $this->query('get', $method,  [], 1);

        if (!$response->isSuccess()) {
            $result->addErrors($response->getErrors());
            return ['status'=>'fail', 'result'=>$result];
        }
        else {
            $data = $response->getData();

            return ['status' => 'ok', 'data' =>$data];
        }
    }

    private function query($type = 'post', $method, $parameters = [], $need_auth = false, $json=0)
    {

        //$parameters = array_merge($auth, $parameters);

        $query_str = http_build_query($parameters);
        //wl($query_str);
        $http = new HttpClient();
        if ($need_auth)
        {
            $http->setHeader('Authorization', 'Bearer '.$this->authToken);
        }

        if ($json == 1) {
            $http->setHeader('Content-type', 'application/json; charset=utf-8');
        }
        if ($type == 'post')
            $http->post($this->public_api_url.$method, $parameters);
        elseif ($type == 'put') {
            $http->setHeader('Content-length', 0);
            $http->query(HttpClient::HTTP_PUT , $this->public_api_url.$method, $parameters);
        }
        else {
            $http->get($this->public_api_url.$method.(!empty($query_str) ? '?'.$query_str : ''), $parameters);
        }

        $status = $http->getStatus();
        $result = new Result();

        if ($status != '200')
        {
            $result->addError(new Error("Error ".$status));

            return $result;

        }

        try {
            $data = Json::decode($http->getResult());

            if (isset($data['error'])) {
                $result->addError(new Error($data['error']));

                return $result;
            }

            $result->setData($data);
        } catch (ArgumentException $e) {
        }

        return $result;
    }


}
