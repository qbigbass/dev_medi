<?

use Bitrix\Main;
use Bitrix\Main\Entity;
use Bitrix\Main\Loader;
use Bitrix\Sale;
use Bitrix\Main\SystemException;


if (defined("ADMIN_SECTION")) {
    AddEventHandler("main", "OnAdminListDisplay", "CourierRegistryListOnAdminListDisplay");
    
    AddEventHandler("main", "OnBeforeProlog", "CourierRegistryListOnBeforeProlog");
}
function CourierRegistryListOnAdminListDisplay(&$list)
{
    //add custom group action
    if ($list->table_id == "tbl_sale_order") {
        $list->arActions["registry_couriers"] = "Реестр Курьеры";
        //$list->arActions["registry_integral"] = "Реестр Интеграл";
        //$list->arActions["registry_courierist"] = "Реестр Курьерист";
        $list->arActions["registry_boxberry"] = "Реестр Боксберри";
        $list->arActions["registry_cdek"] = "Реестр СДЭК";
        //$list->arActions["registry_pochta"] = "Реестр Почта";
    }
}

function CourierRegistryListOnBeforeProlog()
{
    if ($_SERVER["REQUEST_METHOD"] == "POST" && substr($_POST["action"], 0, 9) == "registry_" && is_array($_POST["ID"]) && $GLOBALS["APPLICATION"]->GetCurPage() == "/bitrix/admin/sale_order.php") {
        if ($GLOBALS["APPLICATION"]->GetGroupRight("sale") >= "U" && check_bitrix_sessid()) {
            
            $type = substr($_POST["action"], 9);
            \Bitrix\Main\Loader::IncludeModule("sale");
            
            $orders = [];
            
            foreach ($_POST['ID'] as $k => $order_id) {
                $order = Sale\Order::load($order_id);
                $buyer_id = $order->getUserId();
                $order_sum = $order->getPrice();
                $is_paid = $order->isPaid();
                
                $paymentCollection = $order->getPaymentCollection();
                
                $psName = $paymentCollection[0]->getPaymentSystemName();
                
                $paymentIds = $order->getPaymentSystemId(); // массив id способов оплат
                $deliveryIds = $order->getDeliverySystemId(); // массив id способов доставки
                
                $basket = $order->getBasket();
                $propertyCollection = $order->getPropertyCollection();
                
                $ar = $propertyCollection->getArray();
                
                
                $arOrder['pay'] = $order_sum . "р. " . ($is_paid == 'Y' ? '(оплачен)' : '') . " \r\n" . $psName;
                $arOrder['pay_short'] = $order_sum . "р. " . ($is_paid == 'Y' ? '(оплачен)' : '');
                
                
                $arOrder['ACCOUNT_NUMBER'] = $order->getField("ACCOUNT_NUMBER");
                
                $basket = $order->getBasket();
                $basketItems = $basket->getBasketItems();
                
                $basket_str = '';
                
                foreach ($basket as $basketItem) {
                    $obElement = CIBlockElement::GetList([], ['ID' => $basketItem->getProductId(), 'IBLOCK_ID' => 19], false, false, ['NAME', 'PROPERTY_CML2_ARTICLE']);
                    if ($arElement = $obElement->GetNext()) {
                        $item_name = $arElement['PROPERTY_CML2_ARTICLE_VALUE'];
                    } else {
                        $obElement2 = CIBlockElement::GetList([], ['ID' => $basketItem->getProductId(), 'IBLOCK_ID' => 17], false, false, ['NAME', 'PROPERTY_CML2_ARTICLE']);
                        if ($arElement2 = $obElement2->GetNext()) {
                            $item_name = $arElement2['PROPERTY_CML2_ARTICLE_VALUE'];
                        } else {
                            $item_name = $basketItem->getField('NAME');
                        }
                    }
                    $basket_str .= $item_name . ' (' . $basketItem->getQuantity() . "шт.)\r\n";
                }
                
                $arOrder['BASKET'] = $basket_str;
                
                foreach ($ar['properties'] as $k => $prop) {
                    if ($prop['CODE'] == 'RESERV_NUMBER') {
                        $arOrder['RESERV_NUMBER'] = $prop['VALUE'][0];
                    }
                    if ($prop['CODE'] == 'FIO') {
                        $arOrder['FIO'] = $prop['VALUE'][0];
                    }
                    if ($prop['CODE'] == 'ADDRESS') {
                        $arOrder['ADDRESS'] = $prop['VALUE'][0];
                    }
                    if ($prop['CODE'] == 'ADDRESS_INFO') {
                        $arOrder['ADDRESS_INFO'] = $prop['VALUE'][0];
                    }
                    if ($prop['CODE'] == 'COURIER_MEDI') {
                        if (!empty($prop['VALUE'][0])) {
                            $arOrder['COURIER_MEDI'] = $prop['OPTIONS'][$prop['VALUE'][0]];
                        }
                    }
                    if ($prop['CODE'] == 'DELIVERY_PLANNED') {
                        $arOrder['DELIVERY_PLANNED'] = $prop['VALUE'][0];
                    }
                }
                
                $orders[] = $arOrder;
                
                
            }
            if (!empty($orders)) {
                $res = CourierRegistryCreateFile($type, $orders);
                
                echo '<div class="adm-info-message-wrap adm-info-message-green">
				<div class="adm-info-message">
					<div class="adm-info-message-title">' . $res . '</div>
					<div class="adm-info-message-icon"></div>
				</div>
			</div>';
                die;
            }
            
            
        }
    }
}

function getBasketPropertyValues(\Bitrix\Sale\BasketItem $item)
{
    $values = array();
    
    foreach ($item->getPropertyCollection() as $property)
        
        $values[$property->getField('CODE')] = $property->getField('VALUE');
    
    return $values;
}

function CourierRegistryCreateFile($type, $data)
{
    $PHPEXCELPATH = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/php_interface/include/PHPExcel/Classes_overload0";
    
    include($PHPEXCELPATH . '/PHPExcel.php');
    include($PHPEXCELPATH . '/PHPExcel/Calculation.php');
    include($PHPEXCELPATH . '/PHPExcel/Cell.php');
    
    $objPHPExcel = new PHPExcel();
    
    $active_sheet = $objPHPExcel->getActiveSheet();
    
    $active_sheet->getPageSetup()->SetPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
    $active_sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
    
    $active_sheet->getPageSetup()->setFitToWidth(1);
    $active_sheet->getPageSetup()->setFitToHeight(0);
    
    $style_norm = array(
        'font' => array('name' => 'Cambria', 'size' => 14, 'bold' => false),
        'alignment' => array('horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_LEFT, 'vertical' => PHPExcel_STYLE_ALIGNMENT::VERTICAL_CENTER));
    
    $style_bold_center = array(
        'font' => array('name' => 'Cambria', 'size' => 16, 'bold' => true),
        'alignment' => array('horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_CENTER, 'vertical' => PHPExcel_STYLE_ALIGNMENT::VERTICAL_CENTER,));
    
    $style_norm_right = array(
        'font' => array('name' => 'Cambria', 'size' => 16, 'bold' => false),
        'alignment' => array('horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_RIGHT, 'vertical' => PHPExcel_STYLE_ALIGNMENT::VERTICAL_CENTER));
    
    $active_sheet->getStyle('A1:J40')->applyFromArray($style_norm);
    
    $active_sheet->getStyle('A2')->applyFromArray($style_bold_center);
    
    $active_sheet->setTitle('Реестр заказов');
    
    
    switch ($type) {
        case "cdek":
            
            $active_sheet->getColumnDimension('A')->setWidth(15); // Номер заказа
            $active_sheet->getColumnDimension('B')->setWidth(16); // Номер резерва
            
            $active_sheet->getColumnDimension('C')->setWidth(35); //Оплата
            $active_sheet->getColumnDimension('D')->setWidth(35); //Позиции
            $active_sheet->getColumnDimension('E')->setWidth(30); //ФИО
            $active_sheet->getColumnDimension('F')->setWidth(40); // Адрес доставки
            $active_sheet->getColumnDimension('G')->setWidth(40); //Уточнение к адресу
            $active_sheet->getColumnDimension('H')->setWidth(30); // Комментарий
            
            
            $active_sheet->getStyle("I5")->getAlignment()->setWrapText(true);
            
            $active_sheet->SetCellValue('A1', "Служба доставки");
            $active_sheet->mergeCells("A1:B1");
            
            $active_sheet->SetCellValue('F1', "Дата выдачи");
            $active_sheet->SetCellValue('F2', "Выдал:");
            //$active_sheet->mergeCells("G1:H1");
            //$active_sheet->mergeCells("G2:H2");
            
            $style_border = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000')
                    ),
                )
            );
            $active_sheet->getStyle('F1')->applyFromArray($style_border);
            $active_sheet->getStyle('F2')->applyFromArray($style_border);
            $active_sheet->getStyle('G1')->applyFromArray($style_border);
            $active_sheet->getStyle('G2')->applyFromArray($style_border);
            
            $active_sheet->SetCellValue('C1', "СДЭК");
            
            $active_sheet->SetCellValue('A4', "Заказ");
            $active_sheet->SetCellValue('B4', "Резерв");
            $active_sheet->SetCellValue('C4', "Оплата");
            $active_sheet->SetCellValue('D4', "Позиции");
            $active_sheet->SetCellValue('E4', "ФИО");
            $active_sheet->SetCellValue('F4', "Адрес доставки");
            $active_sheet->SetCellValue('G4', "Уточнения к адресу");
            $active_sheet->SetCellValue('H4', "Комментарий");
            
            $index = 5;
            foreach ($data as $k => $val) {
                $active_sheet->SetCellValue('A' . $index, $val['ACCOUNT_NUMBER']);
                $active_sheet->SetCellValue('B' . $index, $val['RESERV_NUMBER']);
                $active_sheet->SetCellValue('C' . $index, $val['pay']);
                #$active_sheet->SetCellValue('D'.$index, "");
                $active_sheet->SetCellValue('D' . $index, $val['BASKET']);
                $active_sheet->SetCellValue('E' . $index, $val['FIO']);
                $active_sheet->SetCellValue('F' . $index, $val['ADDRESS']);
                $active_sheet->SetCellValue('G' . $index, $val['ADDRESS_INFO']);
                $active_sheet->SetCellValue('H' . $index, "");
                
                
                $active_sheet->getStyle("A{$index}:H{$index}")->applyFromArray($style_border);
                
                $index++;
                
            }
            
            $active_sheet->getStyle("C5:C" . $index)->getAlignment()->setWrapText(true);
            $active_sheet->getStyle("D5:D" . $index)->getAlignment()->setWrapText(true);
            $active_sheet->getStyle("E5:E" . $index)->getAlignment()->setWrapText(true);
            $active_sheet->getStyle("F5:F" . $index)->getAlignment()->setWrapText(true);
            $active_sheet->getStyle("G5:G" . $index)->getAlignment()->setWrapText(true);
            $active_sheet->getStyle("H5:I" . $index)->getAlignment()->setWrapText(true);
            
            $index = $index + 2;
            
            
            $active_sheet->SetCellValue('F' . $index, 'Номер пломбы');
            $active_sheet->getStyle("F{$index}:G{$index}")->applyFromArray($style_border);
            $index++;
            $active_sheet->SetCellValue('F' . $index, 'Номер квитанции');
            
            $active_sheet->getStyle("F{$index}:G{$index}")->applyFromArray($style_border);
            
            
            break;
        
        case "boxberry":
            
            $active_sheet->getColumnDimension('A')->setWidth(15); // Номер заказа
            $active_sheet->getColumnDimension('B')->setWidth(16); // Номер резерва
            
            $active_sheet->getColumnDimension('C')->setWidth(35); //Оплата
            $active_sheet->getColumnDimension('D')->setWidth(35); //Позиции
            $active_sheet->getColumnDimension('E')->setWidth(30); //ФИО
            $active_sheet->getColumnDimension('F')->setWidth(40); // Адрес доставки
            $active_sheet->getColumnDimension('G')->setWidth(40); //Уточнение к адресу
            $active_sheet->getColumnDimension('H')->setWidth(30); // Комментарий
            
            
            $active_sheet->getStyle("I5")->getAlignment()->setWrapText(true);
            
            $active_sheet->SetCellValue('A1', "Служба доставки");
            $active_sheet->mergeCells("A1:B1");
            
            $active_sheet->SetCellValue('F1', "Дата выдачи");
            $active_sheet->SetCellValue('F2', "Выдал:");
            //$active_sheet->mergeCells("G1:H1");
            //$active_sheet->mergeCells("G2:H2");
            
            $style_border = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000')
                    ),
                )
            );
            $active_sheet->getStyle('F1')->applyFromArray($style_border);
            $active_sheet->getStyle('F2')->applyFromArray($style_border);
            $active_sheet->getStyle('G1')->applyFromArray($style_border);
            $active_sheet->getStyle('G2')->applyFromArray($style_border);
            
            $active_sheet->SetCellValue('C1', "Боксберри");
            
            $active_sheet->SetCellValue('A4', "Заказ");
            $active_sheet->SetCellValue('B4', "Резерв");
            $active_sheet->SetCellValue('C4', "Оплата");
            $active_sheet->SetCellValue('D4', "Позиции");
            $active_sheet->SetCellValue('E4', "ФИО");
            $active_sheet->SetCellValue('F4', "Адрес доставки");
            $active_sheet->SetCellValue('G4', "Уточнения к адресу");
            $active_sheet->SetCellValue('H4', "Комментарий");
            
            $index = 5;
            foreach ($data as $k => $val) {
                $active_sheet->SetCellValue('A' . $index, $val['ACCOUNT_NUMBER']);
                $active_sheet->SetCellValue('B' . $index, $val['RESERV_NUMBER']);
                $active_sheet->SetCellValue('C' . $index, $val['pay']);
                $active_sheet->SetCellValue('D' . $index, $val['BASKET']);
                $active_sheet->SetCellValue('E' . $index, $val['FIO']);
                $active_sheet->SetCellValue('F' . $index, $val['ADDRESS']);
                $active_sheet->SetCellValue('G' . $index, $val['ADDRESS_INFO']);
                $active_sheet->SetCellValue('H' . $index, "");
                
                
                $active_sheet->getStyle("A{$index}:H{$index}")->applyFromArray($style_border);
                
                $index++;
                
            }
            
            $active_sheet->getStyle("C5:C" . $index)->getAlignment()->setWrapText(true);
            $active_sheet->getStyle("D5:D" . $index)->getAlignment()->setWrapText(true);
            $active_sheet->getStyle("E5:E" . $index)->getAlignment()->setWrapText(true);
            $active_sheet->getStyle("F5:F" . $index)->getAlignment()->setWrapText(true);
            $active_sheet->getStyle("G5:G" . $index)->getAlignment()->setWrapText(true);
            $active_sheet->getStyle("H5:I" . $index)->getAlignment()->setWrapText(true);
            
            $index = $index + 2;
            
            
            $active_sheet->SetCellValue('F' . $index, 'Номер пломбы');
            $active_sheet->getStyle("F{$index}:G{$index}")->applyFromArray($style_border);
            $index++;
            $active_sheet->SetCellValue('F' . $index, 'Номер квитанции');
            
            $active_sheet->getStyle("F{$index}:G{$index}")->applyFromArray($style_border);
            
            
            break;
        
        
        case "couriers":
            $active_sheet->getColumnDimension('A')->setWidth(4); // Планируемая дата
            $active_sheet->getColumnDimension('B')->setWidth(15); // Планируемая дата
            $active_sheet->getColumnDimension('C')->setWidth(15); // Номер заказа
            $active_sheet->getColumnDimension('D')->setWidth(16); // Номер резерва
            $active_sheet->getColumnDimension('E')->setWidth(35); //Сумма
            $active_sheet->getColumnDimension('F')->setWidth(30); //Подпись в получении
            
            $active_sheet->getColumnDimension('G')->setWidth(30); // Сдана выручка
            $active_sheet->getColumnDimension('H')->setWidth(40); // Получен возврат
            $active_sheet->getColumnDimension('I')->setWidth(30); // Подпись менеджера
            $active_sheet->getColumnDimension('J')->setWidth(30); // Комментарии
            
            $active_sheet->getStyle("J5")->getAlignment()->setWrapText(true);
            
            $active_sheet->SetCellValue('A1', "Курьер medi");
            $active_sheet->mergeCells("A1:B1");
            
            $active_sheet->SetCellValue('H1', "Дата выдачи");
            $active_sheet->SetCellValue('H2', "Выдал:");
            
            $style_border = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array('rgb' => '000000')
                    ),
                )
            );
            
            $style_border_med = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                        'color' => array('rgb' => '000000')
                    ),
                )
            );
            $active_sheet->getStyle('H1:H2')->applyFromArray($style_border_med);
            
            $active_sheet->getStyle('I1:I2')->applyFromArray($style_border_med);
            
            
            $active_sheet->SetCellValue('C1', $data[0]['COURIER_MEDI']);
            
            
            $active_sheet->SetCellValue('A4', "ВЫДАЧА");
            $active_sheet->mergeCells("A4:F4");
            $active_sheet->getStyle('A4:F4')->applyFromArray($style_border_med);
            $active_sheet->getStyle('A4:F4')->applyFromArray($style_bold_center);
            
            $active_sheet->SetCellValue('G4', "ОТЧЕТ");
            $active_sheet->mergeCells("G4:J4");
            $active_sheet->getStyle('G4:J4')->applyFromArray($style_border_med);
            
            $active_sheet->getStyle('G4:J4')->applyFromArray($style_bold_center);
            
            $active_sheet->SetCellValue('A5', "№");
            $active_sheet->SetCellValue('B5', "Планируемая дата");
            $active_sheet->SetCellValue('C5', "Номер заказа");
            $active_sheet->SetCellValue('D5', "Номер резерва");
            $active_sheet->SetCellValue('E5', "Сумма");
            $active_sheet->SetCellValue('F5', "Подпись в получении");
            
            $active_sheet->SetCellValue('G5', "Сдана выручка");
            $active_sheet->SetCellValue('H5', "Получен возврат");
            $active_sheet->SetCellValue('I5', "Подпись менеджера");
            $active_sheet->SetCellValue('J5', "Комментарии");
            
            $active_sheet->getStyle('A5:F5')->applyFromArray($style_border_med);
            $active_sheet->getStyle('G5:J5')->applyFromArray($style_border_med);
            
            $index = 6;
            $ii = 1;
            foreach ($data as $k => $val) {
                $active_sheet->SetCellValue('A' . $index, $ii);
                $active_sheet->SetCellValue('B' . $index, $val['DELIVERY_PLANNED']);
                $active_sheet->SetCellValue('C' . $index, $val['ACCOUNT_NUMBER']);
                $active_sheet->SetCellValue('D' . $index, $val['RESERV_NUMBER']);
                $active_sheet->SetCellValue('E' . $index, $val['pay']);
                $active_sheet->SetCellValue('F' . $index, '');
                $active_sheet->SetCellValue('G' . $index, '');
                $active_sheet->SetCellValue('H' . $index, '');
                $active_sheet->SetCellValue('I' . $index, '');
                $active_sheet->SetCellValue('J' . $index, '');
                
                
                $active_sheet->getStyle("A{$index}:J{$index}")->applyFromArray($style_border);
                
                $index++;
                $ii++;
            }
            
            $active_sheet->getStyle("E6:E" . $index)->getAlignment()->setWrapText(true);
            
            $active_sheet->getStyle("I6:J" . $index)->getAlignment()->setWrapText(true);
            
            
            break;
        
        default:
            return false;
    }
    
    $file = '/upload/delivery_registry/reg_' . $type . '_' . date("d.m.Y") . '_' . date("Hi") . '.xlsx';
    // Пишем в файл
    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
    $objWriter->save($_SERVER['DOCUMENT_ROOT'] . $file);
    
    // Сохраняем и отдаём к скачиванию
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    @unlink($_SERVER['DOCUMENT_ROOT'] . $file);
    $objWriter->save($_SERVER['DOCUMENT_ROOT'] . $file);
    return $strAdminMessage = 'Реестр сформирован: <a href="' . $file . '">скачать</a>';
    unset($objWriter);
    
}
