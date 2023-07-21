<?
error_reporting(E_ALL);
ini_set("display_errors", 1);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH."/css/lk.css");
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/lk.js');

/*
include("pages/init.inc.php");

if (!$_SESSION['lmx']['token'])
{
    $USER->Logout();
    LocalRedirect('/lk/');

}
else {
*/
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/loymax.php");

    $api = new apiLmx;
    //$api->setAuthToken($_SESSION['lmx']['token']);

    if (isset($_REQUEST['code']) && strlen($_REQUEST['code']) == '6'){
        $res = $api->ChangeEmailLinkConfirm($_REQUEST['code'], $_REQUEST['id']);

        $result = ['status'=>'ok', 'data'=> $res['data']];

        /*if ($USER->GetID()){
            $USER->Update($USER->GetID(), ['EMAIL'=>$_SESSION['lmx']['new_email']]);
        }*/
        ?>
<div  id="main">
    <div class="limiter">
        <div class="row flex">
            <br><br><br>
            <div class='status_message_edit success'>Email успешно обновлен. Сейчас вы будете перенаправлены в <a href='/lk/' class="theme-link-dashed">личный кабинет</a></div>";
            <script>
            setTimeout(function () {

                window.location.href = '/lk/';
            },  2000);
            </script>
            <br><br><br><br><br>
        </div>
    </div>
</div>
        <?
    }
//}



require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
