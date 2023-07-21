<?php

/**
 * Индивидуальный фукнционал портала
 */

namespace Medi {

    //    define('MEDI_SITE_ID', 's1');
    //    define('MEDI_REMIND_EVENT', 'MEDI_ORDER_REMINDER');
    //    define('MEDI_DEBUG', true);
    // Параметры указаны непосредственно в функции

    /**
     * Класс с API для отправки E-mail с напоминанием
     */
    class CReminder {

        public function __construct() {

        }

        public function __destruct() {

        }

        public function CheckWorkingTime() {
            //return true; // Проверка на стороне cron
            $arNow = getdate();

            if (10 <= $arNow['hours'] && $arNow['hours'] < 20) {
                return true;
            } else {
                return false;
            }
        }

        public function CheckOrders() {
            $tCurrentTime = time();
            $sHTMLMessage = '';
            $nDelayedOrdersCount = 0;

            if (\CModule::IncludeModule('sale')) {


                $arOrderBy = array(
                    'DATE_INSERT' => 'DESC',
                );
                $arFilter = array(
                    "<=DATE_INSERT" => date(\CDatabase::DateFormatToPHP(\CSite::GetDateFormat("FULL")), $tCurrentTime - 2700), // Которые добавлены ранее 45 мин  назад
                    ">DATE_INSERT" => date(\CDatabase::DateFormatToPHP(\CSite::GetDateFormat("FULL")), $tCurrentTime - 172800), // Но не ранее 2 дней (отсекаем лишние)
                    'STATUS_ID' => 'N',
                    'LID' => ['s1', 's3', 's4', 's5', 's6', 's7', 's8']
                );
                $arSelectFields = array('ACCOUNT_NUMBER', 'DATE_INSERT', 'ID', 'LID');

                $rsOrder = \CSaleOrder::GetList($arOrderBy, $arFilter, false, false, $arSelectFields);

                $arFields = array();
                while ($arOrder = $rsOrder->GetNext()) {
                    $tOrderTime = strtotime($arOrder['DATE_INSERT']);
                    $arOrderTime = getdate($tOrderTime);

                    if ($arOrderTime['hours'] >= 20) {
                        $tOrderTime = mktime(10, 0, 0, $arOrderTime['mon'], $arOrderTime['mday'], $arOrderTime['year']) + 86400;

                    } elseif ($arOrderTime['hours'] < 10) {
                        $tOrderTime = mktime(10, 0, 0, $arOrderTime['mon'], $arOrderTime['mday'], $arOrderTime['year']);

                    }

                    $nDayDifference = floor($tCurrentTime / 86400) - floor($tOrderTime / 86400); // Если разные дни
                    $tDelaySec = $tCurrentTime - $tOrderTime - $nDayDifference * 3600 * 14; // Между двумя рабочими периодами 14 часов перерыва
                    //echo 'DELAY = ' . ($tDelaySec) . "\n";
                    if ($tDelaySec > 2700) {
                        $nDelayedOrdersCount ++; // Добавляем только если заказ действительно считается ожидающим
                        $sHTMLMessage .= '<a style="color: #e2007a;" href="https://www.medi-salon.ru/bitrix/admin/sale_order_detail.php?ID=' . $arOrder['ID'] . '">Заказ №' . $arOrder['ACCOUNT_NUMBER'] . '</a> не обработан, ожидает обработки ' . floor($tDelaySec / 3600) . ' ч. ' . ($tDelaySec / 60) % 60 . ' мин.' . "<br>";
                    }
                }
                $arFields = array(
                    'MESSAGE' => $sHTMLMessage,
                    'DELAYED_COUNT' => $nDelayedOrdersCount,
                );

                if ($nDelayedOrdersCount > 0) {
                    // Параметры указаны здесь
                    \CEvent::Send('MEDI_ORDER_REMINDER', 's1', $arFields, 'N');

                }

                // s2
                $sHTMLMessage = '';
                $nDelayedOrdersCount = 0;
                $arOrderBy = array(
                    'DATE_INSERT' => 'DESC',
                );
                $arFilter = array(
                    "<=DATE_INSERT" => date(\CDatabase::DateFormatToPHP(\CSite::GetDateFormat("FULL")), $tCurrentTime - 2700), // Которые добавлены ранее часа назад
                    ">DATE_INSERT" => date(\CDatabase::DateFormatToPHP(\CSite::GetDateFormat("FULL")), $tCurrentTime - 172800), // Но не ранее 2 дней (отсекаем лишние)
                    'STATUS_ID' => 'N',
                    'LID' => ['s2']
                );
                $arSelectFields = array('ACCOUNT_NUMBER', 'DATE_INSERT', 'ID', 'LID');

                $rsOrder = \CSaleOrder::GetList($arOrderBy, $arFilter, false, false, $arSelectFields);

                $arFields = array();
                while ($arOrder = $rsOrder->GetNext()) {
                    $tOrderTime = strtotime($arOrder['DATE_INSERT']);
                    $arOrderTime = getdate($tOrderTime);

                    if ($arOrderTime['hours'] >= 20) {
                        $tOrderTime = mktime(10, 0, 0, $arOrderTime['mon'], $arOrderTime['mday'], $arOrderTime['year']) + 86400;

                    } elseif ($arOrderTime['hours'] < 10) {
                        $tOrderTime = mktime(10, 0, 0, $arOrderTime['mon'], $arOrderTime['mday'], $arOrderTime['year']);

                    }

                    $nDayDifference = floor($tCurrentTime / 86400) - floor($tOrderTime / 86400); // Если разные дни
                    $tDelaySec = $tCurrentTime - $tOrderTime - $nDayDifference * 3600 * 14; // Между двумя рабочими периодами 14 часов перерыва
                    //echo 'DELAY = ' . ($tDelaySec) . "\n";
                    if ($tDelaySec > 2700) {
                        $nDelayedOrdersCount ++; // Добавляем только если заказ действительно считается ожидающим
                        $sHTMLMessage .= '<a style="color: #e2007a;" href="https://www.medi-salon.ru/bitrix/admin/sale_order_detail.php?ID=' . $arOrder['ID'] . '">Заказ №' . $arOrder['ACCOUNT_NUMBER'] . '</a> не обработан, ожидает обработки ' . floor($tDelaySec / 3600) . ' ч. ' . ($tDelaySec / 60) % 60 . ' мин.' . "<br>";
                    }
                }
                $arFields = array(
                    'MESSAGE' => $sHTMLMessage,
                    'DELAYED_COUNT' => $nDelayedOrdersCount,
                );

                if ($nDelayedOrdersCount > 0) {
                    // Параметры указаны здесь
                    \CEvent::Send('MEDI_ORDER_REMINDER', 's2', $arFields, 'N');
                }
            }


        }

    }

    /**
     * Функция Битрикс-агент отправляющая уведомления.
     */
    function SendRemind() {
        $Reminder = new CReminder();
        if ($Reminder->CheckWorkingTime()) {
            $Reminder->CheckOrders();
        }

        return '\\' . __NAMESPACE__ . '\\SendRemind();';
    }

}
