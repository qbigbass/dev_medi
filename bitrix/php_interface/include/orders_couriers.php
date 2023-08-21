<?


function sysAgentLog($arAgent = false, $state = false, $eval_result = false, $e = false)
{
    AddMessage2Log(array('STATE' => $state, 'AGENT' => $arAgent, 'EVAL' => $eval_result, 'E' => $e));
}

ini_set('log_errors', 'on');
ini_set('error_log', '/home/bitrix/tmp/main_error.log');

use Bitrix\Sale;
use Bitrix\Main\Web\HttpClient;


function CourieristCheckStatuses()
{
    global $DB;
    
    \Bitrix\Main\Loader::IncludeModule("sale");
    
    
    if (date("H") < 8 || date("H") > 22) return 'CourieristCheckStatuses();';
    
    $curStatuses = array(
        10 => 'Черновик (заказ создается) ',
        15 => 'Новый (заказ создан)',
        20 => 'Подтвержден (заказ принят в работу)',
        50 => 'Завершён',
        80 => 'Отменен'
    );
    $access_token = 'B5_kZf9qWi9kGPiO15Puq1GJi-4Guj6J'; // ИП Баженов
    $access_token = '6KzrBCN6mYbyagrkm8Cl6id8loRAAlj8'; // ООО МЕДИ РУС
    
    $query = 'SELECT * FROM `medi_courierist_orders` WHERE STATUS  <  "50"  ORDER BY ID ASC';
    
    $obCurOrder = $DB->Query($query);
    
    $counter = 0;
    $all = 0;
    $status_error = 0;
    $arFields['MSG'] = '';
    while ($arCurOrder = $obCurOrder->Fetch()):
        if ($all >= 20) {
            if ($status_error > 0) {
                $arFields['SUBJ'] = "Ошибка обновления статусов заказов в Курьерист.";
                $arFields['MSG'] .= "Ошибка обновления статусов заказов в Курьерист. 20.<br>";
                
                \CEvent::SendImmediate('MSG_LOG', 's1', $arFields);
            }
            return 'CourieristCheckStatuses();';
        }
        if ($counter >= 4) return 'CourieristCheckStatuses();';
        $httpClient = new HttpClient();
        $httpClient->setHeader('Content-Type', 'application/json', true);
        $httpClient->setHeader("Authorization", "Bearer " . $access_token);
        $response = $httpClient->get('http://my.courierist.com/api/v1/order/' . $arCurOrder['CUR_ID']);
        
        $http_status = $httpClient->getStatus();
        if ($http_status != '200') {
            $status_error++;
            $arFields['MSG'] .= "Ошибка обновления статусов заказов в Курьерист  #" . $arCurOrder['CUR_ID'] . ". <br>Ответ сервера: " . $http_status . "<br>";
            $resp = json_decode($response);
            wl2($resp);
            $arFields['MSG'] .= $resp->message;
            
        } else {
            
            $resp = json_decode($response);
            
            if (in_array($resp->order->status, array_keys($curStatuses))) {
                // Авто смена статусов заказа
                $statuses = ['20' => 'D', '50' => 'F', '80' => 'Y'];
                $check_ctatuses = ['D', 'P', 'S'];
                
                $arOrder = CSaleOrder::GetByID($arCurOrder['ORDER_ID']);
                
                $new_cur_status = $resp->order->status;
                if (($arCurOrder['STATUS'] != $new_cur_status || $arOrder['STATUS_ID'] != $statuses[$new_cur_status]) && in_array($arOrder['STATUS_ID'], $check_ctatuses)) {
                    
                    $rsUser = CUser::GetByLogin("API");
                    $arUser = $rsUser->Fetch();
                    // Авто смена статусов заказа
                    $arOrderFields['EMP_STATUS_ID'] = $arUser['ID'];
                    $arOrderFields['STATUS_ID'] = $statuses[$resp->order->status];
                    CSaleOrder::Update($arCurOrder['ORDER_ID'], $arOrderFields);
                    
                    
                    $counter++;
                }
                if ($arCurOrder['STATUS'] != $new_cur_status) {
                    $updquery = 'UPDATE `medi_courierist_orders` SET STATUS = "' . $resp->order->status . '", TIME_UPDATE = NOW() WHERE CUR_ID = "' . $arCurOrder['CUR_ID'] . '"';
                    $DB->Query($updquery);
                }
            }
            
            if ($resp->message) {
                
                $arFields['SUBJ'] = "Ошибка обновления статусов заказов в Курьерист.";
                $arFields['MSG'] = "Ошибка обновления статусов заказов в Курьерист. <br>Ответ сервера: " . $http_status . "<br>";
                
                $arFields['MSG'] .= $resp->message;
                \CEvent::SendImmediate('MSG_LOG', 's1', $arFields);
            }
            
        }
        $all++;
    
    endwhile;
    
    if ($status_error > 0) {
        $arFields['SUBJ'] = "Ошибка обновления статусов заказов в Курьерист.";
        $arFields['MSG'] .= "Ошибка обновления статусов заказов в Курьерист. $status_error <br>Ответ сервера: " . $http_status . "<br>";
        
        \CEvent::SendImmediate('MSG_LOG', 's1', $arFields);
    }
    
    return 'CourieristCheckStatuses();';
}

AddEventHandler('main', 'OnAdminContextMenuShow', 'OrderCourierSendButtonMenu');


function OrderCourierSendButtonMenu(&$items)
{
    
    global $APPLICATION;
    
    $delivery = array(17, 18, 19, 58, 61, 62, 63, 109);
    $statuses = array("A", "P", "S");
    $showButton = false;
    
    if ($_SERVER['REQUEST_METHOD'] == 'GET' && $GLOBALS['APPLICATION']->GetCurPage() == '/bitrix/admin/sale_order_view.php' && $_REQUEST['ID'] > 0) {
        $orderID = intval($_REQUEST['ID']);
        
        $SALE_RIGHT = $APPLICATION->GetGroupRight("sale");
        
        if ($SALE_RIGHT != "D"):
            
            \Bitrix\Main\Loader::IncludeModule("sale");
            
            $saleOrder = Bitrix\Sale\Order::load($orderID);
            
            $collection = $saleOrder->getShipmentCollection();
            foreach ($collection as $shipment) {
                if (!$shipment->isSystem() && in_array($shipment->getField('DELIVERY_ID'), $delivery) && in_array($saleOrder->getField('STATUS_ID'), $statuses)) {
                    $showButton = true;
                }
            }
            
            if ($showButton) {
                $items[] = array(
                    "TEXT" => "Отправить в Курьерист",
                    "LINK" => "javascript:mediCourierist(" . $orderID . ")",
                    "TITLE" => "Отправить заявку на забор и доставку в Курьерист",
                    "ICON" => "btn_green",
                );
            }
        
        
        endif;
    }
}

AddEventHandler("main", "OnBuildGlobalMenu", "CourieristMenu");
function CourieristMenu(&$adminMenu, &$moduleMenu)
{
    $moduleMenu[] = array(
        "parent_menu" => "global_menu_store", // поместим в раздел "Сервис"
        "section" => "Заявки Курьерист",
        "sort" => 100,                    // сортировка пункта меню
        "url" => "/bitrix/admin/courierist_list.php?lang=" . LANG,  // ссылка на пункте меню
        "text" => 'Заявки Курьерист',       // текст пункта меню
        "title" => 'Заявки Курьерист', // текст всплывающей подсказки
        "icon" => "form_menu_icon", // малая иконка
        "page_icon" => "form_page_icon", // большая иконка
        "items_id" => "menu_courierist",  // идентификатор ветви
        "items" => array()          // остальные уровни меню сформируем ниже.
    );
}
