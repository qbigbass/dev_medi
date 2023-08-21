<?
set_time_limit(90);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

use Bitrix\Sale;

\Bitrix\Main\Loader::IncludeModule("sale");

$APPLICATION->SetTitle("Обмен статусами заказов");
if (!$USER->IsAuthorized())
 die();

 $groups = $USER->GetUserGroupArray();

if (!in_array(1, $groups) /*&& !in_array(8, $groups)*/){

     LocalRedirect("/");die;
 }

?>
 <style>
 .tool_notice {padding: 1em;margin-bottom: 2em;}
 .tool_notice.error {
     border: 1px solid #e20074;
     background: #fff5fa;
     color: #e20074;
 }
 .tool_notice.alert {
     border: 1px solid #38e200;
     background: #f7fff5;
     color: #03e200;
 }
 .error {
     border: 1px solid red;
 }
 .green_bg {
     border:1px solid green;
 }
 .highlight.row {
     padding-top: 1em;

 }
 .highlight.row:hover {
     background: #eaeaea;
 }
 </style>
 <div class="container">
     <?
     if (isset($_SESSION['tools'])){?>
     <div class=" tool_notice <?=($_SESSION['tools']['alert'] ? 'alert' : 'error')?>">
         <?=$_SESSION['tools']['alert']?>
         <?=$_SESSION['tools']['error']?>
     </div>
     <?
     unset($_SESSION['tools']);
     }?>
     <form method="post" action="" enctype="multipart/form-data">
         <input type="hidden" name="action" value="upload"/>
     <div class="row">
         <div class="ten  columns">
             <label for="order-form__goodIds" class="order-form__label--truncate">Файл для обмена:</label>
             <input  type="file"  class="u-full-width" cols="10" id="order-form__goodIds" name="file" />
         </div>
     </div>
     <div class="row">

         <div class="ten columns">

             <input type="submit" name="unmark" value="Загрузить" class="button button-second"/>
         </div>
     </div>

     </form>
     <?//if (isset($_SESSION['status_file'] && file_exists())
     $status_files = glob($_SERVER['DOCUMENT_ROOT'].'/local/tmp/order_statuses/*');
     if (!empty($status_files[0])){
         $status_file_name = basename($status_files[0]);
         $status_file_date = date("H:i d-m-Y ", str_replace(".csv", '', $status_file_name));

         if (file_exists($status_files[0])){
             ?>
             <form method="post" action="" enctype="multipart/form-data">
                 <input type="hidden" name="action" value="reupload"/>
             <div class="row">
                 <div class="ten  columns">
                     Обнаружен загруженный файл от <?=$status_file_date;?>
                 </div>
             </div>
             <div class="row">

                 <div class="ten columns">

                     <input type="submit" name="unmark" value="Обработать" class="button button-second"/>
                 </div>
             </div>

             </form>
             <?
         }
     }?>
 </div>


 <?
 if ($_REQUEST['action'] == 'upload')
 {
     if (!empty($_FILES['file']['tmp_name']))
     {
         if ($_FILES['file']['type'] != 'application/vnd.ms-excel'){

            $_SESSION['tools']['error'] = 'Файл должен быть в формате CSV. Загружен - '.$_FILES['file']['type'];
            LocalRedirect("/local/tools/order_status.php");
         }
         else{
            array_map('unlink', glob($_SERVER['DOCUMENT_ROOT'].'/local/tmp/order_statuses/*'));
            $tmp_name = $_FILES["file"]["tmp_name"];
            $new_name = time().'.csv';
            $_SESSION['status_file'] = $new_name;
            $uploads_dir = $_SERVER['DOCUMENT_ROOT'].'/local/tmp/order_statuses/';
            // basename() может предотвратить атаку на файловую систему;
            // может быть целесообразным дополнительно проверить имя файла

            move_uploaded_file($tmp_name, "$uploads_dir/$new_name");

            $data = display_status_conformity($new_name);
            echo $data;

        }
     }
     else{

         $_SESSION['tools']['error'] = 'Невыбран файл.';
         LocalRedirect("/local/tools/order_status.php");
     }
 }
 else if ($_REQUEST['action'] == 'reupload')
{
    $status_files = glob($_SERVER['DOCUMENT_ROOT'].'/local/tmp/order_statuses/*');
    if (!empty($status_files[0])){
        $status_file_name = basename($status_files[0]);

        $data = display_status_conformity($status_file_name);
        echo $data;
    }
    else{

         $_SESSION['tools']['error'] = 'Не удалось обработать файл.';
         LocalRedirect("/local/tools/order_status.php");
    }
}
 elseif ($_REQUEST['action'] == 'update')
 {
     if (!empty($_REQUEST['order']))
     {
         $orders = $_REQUEST['order'];
         $counter = 0;
         foreach($orders['num'] AS $k=>$order_num)
         {
             $saleorder = Sale\Order::loadByAccountNumber($order_num);
             $site_status = $saleorder->getField('STATUS_ID');
             if ($site_status != $orders['new_status'][$k])
             {
                $saleorder->setField('STATUS_ID', $orders['new_status'][$k]);
                $saleorder->setField('EMP_STATUS_ID', 38806);
                $saleorder->save();
                $counter++;
             }
         }
         if ($counter > 0)
         {
             $_SESSION['tools']['alert'] = 'Обновлено заказов: '.$counter;
             LocalRedirect("/local/tools/order_status.php");
         }
     }
     else {
         $_SESSION['tools']['error'] = 'Нет заказов для обновления';
         LocalRedirect("/local/tools/order_status.php");
     }
 }



function display_status_conformity ($file)
{
    $f = $_SERVER['DOCUMENT_ROOT'].'/local/tmp/order_statuses/'.$file;

    $statuses = [
        'Выкуплено' => 'F',
        'Возвращен поставщику' => 'K',
        'Аннулирован' => 'K',
        'Оприходован в салоне' => 'G'
    ];

    $statusResult = \Bitrix\Sale\Internals\StatusLangTable::getList(array(
        'order' => array('STATUS.SORT'=>'ASC'),
        'filter' => array('STATUS.TYPE'=>'O','LID'=>LANGUAGE_ID),
        'select' => array('STATUS_ID','NAME','DESCRIPTION'),
    ));
    $all_statuses = [];
    while($status=$statusResult->fetch())
    {
        $all_statuses[$status['STATUS_ID']] = $status['NAME'];
    }


    $result = [];
    if (($handle = fopen($f, "r")) !== FALSE) {
        $i = 0;
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $num = count($data);


            if ($i == 0) {$i++;continue;}

            if ($num == '4')
            {
                $result[] = $data;
                $i++;
            }

        }
        fclose($handle);

        if (!empty($result))
        {
            $content = '
            <div class="container">
            <form method="post" action="" enctype="multipart/form-data">
                 <input type="hidden" name="action" value="update"/>
                 <div class="row">
                      <div class="three  columns">
                            <b>Номер заказа</b>
                      </div>
                      <div class="three  columns">
                            <b>Статус на сайте:</b>
                      </div>
                      <div class="three  columns">
                            <b>Статус в файле:</b>
                      </div>
                      <div class="three  columns">
                            <b>Новый статус</b>
                      </div>
                  </div>';
            foreach($result AS $k=>$order)
            {
                //$order = explode(";", $res[0]);
                $order_num = str_replace("№", "", $order[0]);
                if (intval($order_num) > 30000000){

                $saleorder = Sale\Order::loadByAccountNumber($order_num);

                    $site_status = $saleorder->getField('STATUS_ID');

                    $content .= '
                    <div class="row highlight">
                         <div class="three  columns">
                            <input type = "text" name="order[num][]" value="'.$order_num.'" readonly class="u-full-width '.($statuses[$order[2]] == $site_status ? 'green_bg' : 'error').'" />
                         </div>
                         <div class="three  columns">
                               <b>'.$all_statuses[$site_status].' ('.$site_status.')</b>
                         </div>
                         <div class="three  columns">
                               <b>'.$order[2].' ('.$statuses[$order[2]].')</b>
                         </div>
                         <div class="three  columns  ">';

                         $content .= '
                        <select class="u-full-width '.($statuses[$order[2]] == $site_status ? 'green_bg' : 'error').'"   name="order[new_status][]" >
                        <option>___</option>';
                        foreach($all_statuses AS $kk=>$st){
                            $content .= '<option value="'.$kk.'" '.($kk == $statuses[$order[2]] ? 'selected="selected"' : '').'>'.$st.'</option>';
                        }
                        $content .= '
                        </select>
                            ';

                        $content .= '
                         </div>
                     </div>';
                 }


            }
            $content .= '<div class="row">
                <div class="ten columns">
                    <input type="submit" name="doit" value="Обновить" class="button button-second"/>
                </div>
            </div>
        </form></div>';

            return $content;
        }
    }
    else {

         $_SESSION['tools']['error'] = 'Не удалось обработать файл. Некорректные данные.';
         LocalRedirect("/local/tools/order_status.php");
    }

}

 require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
