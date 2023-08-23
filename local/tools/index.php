<?
set_time_limit(90);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Служебные сервисы");
if (!$USER->IsAuthorized())
 die();

 $groups = $USER->GetUserGroupArray();

 if (!in_array(1, $groups) && !in_array(8, $groups)&& !in_array(9, $groups)){

     LocalRedirect("/");die;
 }

 unset($_SESSION['tools']);
?>


 <div class="container">
     <div class="row">
         <div class="ten  columns">
             Выберите нужный сервис в меню
         </div>
     </div>
 </div>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
