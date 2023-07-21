<?

set_time_limit(120);

use Bitrix\Main\Loader;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Sale;

$ModuleID = "up.boxberrydelivery";

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $ModuleID . '/include.php');

function BoxberryCheckStatuses()
{
    global $DB, $USER;
    
    \Bitrix\Main\Loader::IncludeModule("sale");
    $curStatuses = array(
        10 => 'Принято к доставке',
        20 => 'Поступило в пункт выдачи',
        30 => 'Выдано',
        40 => 'Возвращено в ИМ',
    );
    $access_token = '26d272c330a07433ee109972cea88b40';
    
    $allDeliverys = \Bitrix\Sale\Delivery\Services\Manager::getActiveList();
    foreach ($allDeliverys as $profile) {
        if (strpos($profile['CODE'], 'boxberry') !== false && strpos($profile['CODE'], 'KD') !== false) {
            $boxberry_profiles[] = $profile['ID'];
            $boxberry_profiles_kd[] = $profile['ID'];
            
        } elseif (strpos($profile['CODE'], 'boxberry') !== false && strpos($profile['CODE'], 'PVZ') !== false) {
            $boxberry_profiles[] = $profile['ID'];
            $boxberry_profiles_pvz[] = $profile['ID'];
            
        }
    }
    
    $status_errors = 0;
    
    $arFilter = [
        "STATUS_ID" => ["A", "D", "DP"],
        "DELIVERY_ID" => $boxberry_profiles
    ];
    $arSelectFields[] = 'DATE_INSERT';
    $arSelectFields[] = 'ID';
    $arSelectFields[] = 'ACCOUNT_NUMBER';
    $arSelectFields[] = 'DELIVERY_ID';
    $arSelectFields[] = 'STATUS_ID';
    $arSelectFields[] = 'PERSON_TYPE_ID';
    
    $obOrder = CSaleOrder::GetList(
        array('ID' => 'ASC'),
        $arFilter,
        false,
        false,
        $arSelectFields
    );
    
    $i = 0;
    while ($arCurOrder = $obOrder->NavNext(true, "f_")) {
        
        $arBbOrder = CBoxberryOrder::GetByOrderId($arCurOrder['ID']);
        
        $httpClient = new HttpClient();
        $httpClient->setHeader('Content-Type', 'application/json', true);
        
        if ($arBbOrder['TRACKING_CODE']) {
            $response = $httpClient->get('http://api.boxberry.ru/json.php?token=' . $access_token . '&method=ListStatuses&ImId=' . $arBbOrder['TRACKING_CODE']);
            
            $http_status = $httpClient->getStatus();
            
            if ($http_status != '200') {
                
                $status_errors++;
                
            } else {
                
                $resp = json_decode($response);
                
                if (count($resp) >= 1) {
                    
                    $lastStatus = end($resp);
                    $new_status = '';
                    if (strval($lastStatus->Name) == 'Принято к доставке' ||
                        strval($lastStatus->Name) == 'Передан на доставку до пункта выдачи' ||
                        strval($lastStatus->Name) == 'Передано на курьерскую доставку') {
                        $new_status = 'D';
                    } elseif (strval($lastStatus->Name) == 'Поступило в пункт выдачи') {
                        $new_status = 'DP';
                    } elseif (strval($lastStatus->Name) == 'Выдано') {
                        $new_status = 'F';
                    } elseif (strval($lastStatus->Name) == 'Возвращено в ИМ') {
                        $new_status = 'Y';
                    }
                    
                    if ($new_status != $arCurOrder['STATUS_ID'] && $new_status != '') {
                        
                        $i++;
                        if ($i > 10) break;
                        
                        $rsUser = CUser::GetByLogin("API");
                        $arUser = $rsUser->Fetch();
                        // Авто смена статусов заказа
                        $arOrderFields['EMP_STATUS_ID'] = $arUser['ID'];
                        
                        $arOrderFields['STATUS_ID'] = $new_status;
                        w2l([$arOrderFields, $resp], 1, 'boxberry.log');
                        
                        try {
                            $order = Sale\Order::load($arCurOrder['ID']);
                            
                            $order->setField('STATUS_ID', $new_status);
                            $order->setField('EMP_STATUS_ID', $arUser['ID']);
                            
                            $order->save();
                            //$res = CSaleOrder::StatusOrder($arCurOrder['ID'], $new_status);
                        } catch (Exception $e) {
                            $err = $e->getMessage();
                            w2l([$err, $e], 1, 'boxberry.log');
                        }
                        //$res = CSaleOrder::Update($arCurOrder['ID'], $arOrderFields);
                        
                    }
                    
                } elseif (strval($resp->err)) {
                    
                    $arFields['SUBJ'] = "Ошибка обновления статусов заказов в Боксберри.";
                    $arFields['MSG'] = "Ошибка обновления статусов заказов в Боксберри. <br>Ответ сервера: "
                        . $http_status . "<br>";
                    $arFields['MSG'] .= print_r(json_decode($response), 1);
                    $arFields['MSG'] .= strval($resp->err);
                    \CEvent::SendImmediate('MSG_LOG', 's1', $arFields);
                }
            }
        }
        
    }
    
    if ($status_errors > 0) {
        $arFields['SUBJ'] = "Ошибка обновления статусов заказов в Боксберри.";
        $arFields['MSG'] = "Ошибка обновления статусов заказов ($status_errors) в Боксберри. <br>Ответ сервера: " . $http_status . "<br>";
        
        $arFields['MSG'] .= strval($resp->err);
        $arFields['MSG'] .= var_dump(json_decode($response));
        \CEvent::SendImmediate('MSG_LOG', 's1', $arFields);
    }
    
    return 'BoxberryCheckStatuses();';
}
