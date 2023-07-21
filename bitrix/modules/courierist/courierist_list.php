<?
ini_set("display_errors", 1);
error_reporting("E_ALL");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

use Bitrix\Sale;
use Bitrix\Main\Web\HttpClient;



$ModuleID = "courierist";
#require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/include.php');

IncludeModuleLangFile(__FILE__);
$POST_RIGHT = $APPLICATION->GetGroupRight('sale');
if ($POST_RIGHT <= "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));


// Статусы заявок
$curStatuses = array(
	10 => 'Черновик (заказ создается) ',
	15 => 'Новый (заказ создан)',
	20 => 'Подтвержден (заказ принят в работу)',
	50 => 'Завершён',
	80 => 'Отменен'
);


$sTableID = "medi_courierist_orders";
$oSort = new CAdminSorting($sTableID, "DELIVERY_DATE", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);
$arOrderProps = array();


$strAdminMessage = '';
$bBreak = FALSE;
if(($arID = $lAdmin->GroupAction()) && $POST_RIGHT>="U"){

	// Формирование акта для курьера
	if ($_REQUEST['action'] == 'printAct'  && !empty($arID))
	{
		$PHPEXCELPATH =  $_SERVER['DOCUMENT_ROOT']."/bitrix/php_interface/include/PHPExcel/Classes_overload0";

		//echo $_SERVER['DOCUMENT_ROOT'];
		include($PHPEXCELPATH.'/PHPExcel.php');
        include($PHPEXCELPATH.'/PHPExcel/Calculation.php');
        include($PHPEXCELPATH.'/PHPExcel/Cell.php');


		$query = 'SELECT * FROM `medi_courierist_orders` WHERE ID IN  ('.implode(',',$arID).') ';
             $obCurOrder = $DB->Query($query);
		$arCurOrders = array();

		while ($arCurOrder = $obCurOrder->Fetch()):
			$arCurOrders[] = $arCurOrder;
		endwhile;

		if (!empty($arCurOrders)){
			$objPHPExcel = new PHPExcel();

			$active_sheet = $objPHPExcel->getActiveSheet();

			$date = $arCurOrders[0]['DELIVERY_DATE'];
			$timestamp = MakeTimeStamp($date, "YYYY-MM-DD");
			$date_title = date("d", $timestamp).' '.FormatDate("F", $timestamp). ' '.date("Y").' года';

			$active_sheet->getPageSetup()->SetPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
			$active_sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE );

			$active_sheet->getPageSetup()->setFitToWidth(1);
			$active_sheet->getPageSetup()->setFitToHeight(0);

			$active_sheet->setTitle('Заказы');
			$active_sheet->getColumnDimension('A')->setWidth(5);
			$active_sheet->getColumnDimension('B')->setWidth(15);
			$active_sheet->getColumnDimension('C')->setWidth(20);
			$active_sheet->getColumnDimension('D')->setWidth(7);
			$active_sheet->getColumnDimension('E')->setWidth(7);
			$active_sheet->getColumnDimension('F')->setWidth(40);
			$active_sheet->getColumnDimension('G')->setWidth(40);
			$active_sheet->getColumnDimension('H')->setWidth(20);
			$active_sheet->getColumnDimension('I')->setWidth(20);


			$active_sheet->getStyle("I5")->getAlignment()->setWrapText(true);


			$active_sheet->mergeCells("A2:H2");
			$active_sheet->SetCellValue('A2', "Акт приема-передач от ".$date_title);
			$style = array(
				'font' => array(
					'name'      => 'Cambria',
					'size'      => 16,
					'bold'      => true,
				),
				'alignment' => array(
							'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_CENTER,
							'vertical' => PHPExcel_STYLE_ALIGNMENT::VERTICAL_CENTER,
						),
			);
			$active_sheet->getStyle('A2')->applyFromArray($style);

			$active_sheet->SetCellValue('A3', "Заказчик:");
			$active_sheet->mergeCells("A3:B3");

			$active_sheet->SetCellValue('C3', "ООО \"МЕДИ РУС\"");
			$active_sheet->mergeCells("C3:F3");

			$active_sheet->SetCellValue('G3', "Клиентский номер: 27664");
			$active_sheet->mergeCells("G3:I3");

			$style2 = array(
				'font' => array(
					'name'      => 'Cambria',
					'size'      => 16,
					'bold'      => false,
				),
				'alignment' => array(
							'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_RIGHT,
							'vertical' => PHPExcel_STYLE_ALIGNMENT::VERTICAL_CENTER,
						),
			);
			$active_sheet->getStyle('G3')->applyFromArray($style2);

			$style3 = array(
				'font' => array(
					'name'      => 'Cambria',
					'size'      => 14,
					'bold'      => false,
				),
			);
			$active_sheet->getStyle('A3')->applyFromArray($style3);
			$active_sheet->getStyle('C3')->applyFromArray($style3);

			$active_sheet->SetCellValue('A4', "Исполнитель:");
			$active_sheet->mergeCells("A4:B4");
			$active_sheet->SetCellValue('C4', 'ООО "Курьерист"');
			$active_sheet->mergeCells("C4:F4");

			//  Заголовки таблицы заказов
			$style4 = array(
				'font' => array(
					'name'      => 'Cambria',
					'size'      => 14,
					'bold'      => true,
				),
			);
			$active_sheet->getStyle('A4')->applyFromArray($style3);
			$active_sheet->getStyle('C4')->applyFromArray($style4);

			$active_sheet->SetCellValue('A5', "№");
			$active_sheet->SetCellValue('B5', "Номер заказа");
			$active_sheet->SetCellValue('C5', "Дата доставки");
			$active_sheet->SetCellValue('D5', "С");
			$active_sheet->SetCellValue('E5', "До");
			$active_sheet->SetCellValue('F5', "Адрес");
			$active_sheet->SetCellValue('G5', "Получатель");
			$active_sheet->SetCellValue('H5', "Сотовый");
			$active_sheet->SetCellValue('I5', "Оценочная стоимость");


			$stylered = array(
				'font' => array(
					'name'      => 'Arial',
					'size'      => 11,
					'bold'      => true,
					'color'     => array('rgb' => 'FF0000'),
				),
				'alignment' => array(
							'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_CENTER,
							'vertical' => PHPExcel_STYLE_ALIGNMENT::VERTICAL_CENTER,
						),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'FBFBFB')
				),
				'borders'=>array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => '000000')
					),
				)
			);
			$styleheadnorm = array(
				'font' => array(
					'name'      => 'Arial',
					'size'      => 11,
					'bold'      => false,
				),
				'fill' => array(
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'FBFBFB')
				),
				'alignment' => array(
					'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_STYLE_ALIGNMENT::VERTICAL_CENTER,
				),
				'borders'=>array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => '000000')
					),
				)
			);
			$stylenorm = array(
				'font' => array(
					'name'      => 'Arial',
					'size'      => 11,
					'bold'      => false,
				),
				'alignment' => array(
					'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_STYLE_ALIGNMENT::VERTICAL_CENTER,
				),
				'borders'=>array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => '000000')
					),
				)
			);
			$active_sheet->getStyle('A5:I5')->applyFromArray($stylered);


			// Строки таблицы со списком заказов
			$curStr = 6;
			$ii = 1;
			$full_sum = 0;
			foreach($arCurOrders AS $i => $cur){

				$active_sheet->SetCellValue('A'.$curStr, $ii);

				$active_sheet->getStyle('A'.$curStr)->applyFromArray($stylered);
				$active_sheet->SetCellValue('B'.$curStr, $cur['ACCOUNT_NUMBER']);
				$timestamp1 = MakeTimeStamp($cur['DELIVERY_DATE'], "YYYY-MM-DD");
				$active_sheet->SetCellValue('C'.$curStr, date("d.m.Y", $timestamp1));
				$active_sheet->SetCellValue('D'.$curStr, $cur['DELIVERY_FROM']);
				$active_sheet->SetCellValue('E'.$curStr, $cur['DELIVERY_TO']);
				$active_sheet->SetCellValue('F'.$curStr, $cur['ADDRESS']);
				$active_sheet->getStyle('F'.$curStr)->getAlignment()->setWrapText(true);
				$active_sheet->SetCellValue('G'.$curStr, $cur['FIO']);
				$active_sheet->getStyle('G'.$curStr)->getAlignment()->setWrapText(true);
				$active_sheet->SetCellValue('H'.$curStr, $cur['PHONE']);
				$active_sheet->SetCellValue('I'.$curStr, $cur['SUM']);

				$full_sum=$full_sum + $cur['SUM'];

				$active_sheet->getStyle('A'.$curStr.':I'.$curStr)->applyFromArray($stylenorm);
				$ii++;
				$curStr++;
			}
			$fullsum_str = $curStr;

			$active_sheet->getStyle('I'.$fullsum_str)->applyFromArray($stylenorm);
			$active_sheet->SetCellValue('I'.$fullsum_str, $full_sum);
			$lastStr = $curStr+1;

			$stylecenter = array(
				'alignment' => array(
					'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_STYLE_ALIGNMENT::VERTICAL_CENTER,
				),

			);
			$active_sheet->getStyle('A6:C'.$lastStr)->applyFromArray($stylecenter);


			// Места для подписей
			$str1 = $curStr+2;
			$str4 = $curStr+5;
			$str5 = $curStr+6;
			$str8 = $curStr+9;

			$active_sheet->SetCellValue('B'.$str1, 'Заказчик');
			$active_sheet->SetCellValue('C'.$str1, 'Юркевич В.А.');
			$active_sheet->SetCellValue('H'.$str1, date("d.m.Y", $timestamp));

			$active_sheet->SetCellValue('B'.$str4, 'Исполнитель');
			$active_sheet->SetCellValue('B'.$str5, 'Курьер');
			$active_sheet->SetCellValue('H'.$str5, date("d.m.Y", $timestamp));

			$active_sheet->SetCellValue('B'.$str8, 'Примечание');

			$styleb = array(
				'font' => array(
					'name'      => 'Arial',
					'size'      => 11,
					'bold'      => true,
				),

			);
			$active_sheet->getStyle('B'.$str1)->applyFromArray($styleb);
			$active_sheet->getStyle('B'.$str4)->applyFromArray($styleb);


			// Рисуем линии
			$border = array(
				'borders'=>array(
					'bottom' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => '000000')
					)
				)
			);
			$active_sheet->getStyle('C'.$str1)->applyFromArray($border);
			$active_sheet->getStyle('H'.$str1)->applyFromArray($border);
			$active_sheet->getStyle('F'.$str1)->applyFromArray($border);
			$active_sheet->getStyle('C'.$str5)->applyFromArray($border);
			$active_sheet->getStyle('H'.$str5)->applyFromArray($border);
			$active_sheet->getStyle('F'.$str5)->applyFromArray($border);

			$active_sheet->getStyle('C'.($str8).':H'.($str8))->applyFromArray($border);




			$file = '/upload/courierist/APP_'.date("d.m.Y", $timestamp).'_'.date("Hi").'.xlsx';
			// Пишем в файл
			$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
			$objWriter->save($_SERVER['DOCUMENT_ROOT'].$file);

			// Сохраняем и отдаём к скачиванию
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			@unlink($_SERVER['DOCUMENT_ROOT'].$file);
			$objWriter->save($_SERVER['DOCUMENT_ROOT'].$file);
			$strAdminMessage = 'Файл сформирован: <a href="'.$file.'">скачать</a>';
			unset($objWriter);
		}
	}

	foreach($arID as $ID)
	{

		 if(strlen($ID)<=0) continue;

		 $ID = IntVal($ID);

	       switch($_REQUEST['action'])
	       {

		       case "reloadStatus":
		       {
				   $access_token = 'B5_kZf9qWi9kGPiO15Puq1GJi-4Guj6J'; // ИП Баженов
				   $access_token =  '6KzrBCN6mYbyagrkm8Cl6id8loRAAlj8'; // ООО МЕДИ РУС

				    $httpClient = new HttpClient();
					$httpClient->setHeader('Content-Type', 'application/json', true);

				$query = 'SELECT * FROM `medi_courierist_orders` WHERE ID = "'.$ID.'" AND STATUS < "80" ';

				 $obCurOrder = $DB->Query($query);

				if ($arCurOrder = $obCurOrder->Fetch()):

					$httpClient->setHeader("Authorization","Bearer ".$access_token);
					$response = $httpClient->get('http://my.courierist.com/api/v1/order/'.$arCurOrder['CUR_ID'].'', json_encode($curOrder));

					$resp = json_decode($response);
wl($resp);
					if (in_array($resp->order->status, array_keys($curStatuses))/* && $arCurOrder['STATUS'] != $resp->order->status*/)
					{
						// Авто смена статусов заказа
						$statuses = ['20' => 'D', '50'=>'F', '80'=>'Y'];

						$rsUser = CUser::GetByLogin("API");
						$arUser = $rsUser->Fetch();
						// Авто смена статусов заказа
						$arOrderFields['EMP_STATUS_ID'] = $arUser['ID'];
						$arOrderFields['STATUS_ID'] = $statuses[$resp->order->status];
						CSaleOrder::Update($arCurOrder['ORDER_ID'], $arOrderFields);


						$updquery = 'UPDATE `medi_courierist_orders` SET STATUS = "'.$resp->order->status.'" WHERE CUR_ID = "'.$arCurOrder['CUR_ID'].'"';
						$DB->Query($updquery);
					}
				endif;
				break;
			}
		}


	       	if($bBreak)
				break;
	}
		//if ($updquery) LocalRedirect("/bitrix/admin/courierist_list.php");
}

if(strlen($strAdminMessage) > 1)
	CAdminMessage::ShowMessage(array("MESSAGE" => $strAdminMessage, "HTML"=>true, "TYPE" => "OK"));

$arHeaders = array(
    array(
        "id"         =>"ID",
        "content"    =>"ID",
        "sort"       =>"id",
        "align"      =>"left",
        "default"    =>true,
    ),
	array(
        "id"         =>"ORDER_ID",
        "content"    =>"ID заказа",
        "sort"       =>"ORDER_ID",
        "align"      =>"left",
        "default"    =>true,
    ),
    array(
        "id"         =>"ACCOUNT_NUMBER",
        "content"    =>"Номер заказа",
        "sort"       =>"ACCOUNT_NUMBER",
        "align"      =>"left",
        "default"    =>true,
    ),
    array(
        "id"         => "CUR_ID",
        "content"    => "Номер заявки",
        "default"    => true,
    ),
	array(
        "id"         => "STATUS",
        "content"    => "Статус заявки",
        "sort"       => "STATUS",
        "default"    => true,
    ),
    array(
        "id"         => "TIME_SEND",
        "content"    => "Отправлено",
        "sort"       => "TIME_SEND",
        "default"    => true,
    ),
    array(
        "id"         => "TIME_UPDATE",
        "content"    => "Обновлено",
        "sort"       => "TIME_UPDATE",
        "default"    => true,
    ),
    array(
        "id"         => "DELIVERY_DATE",
        "content"    => "Дата доставки",
        "sort"       => "DELIVERY_DATE",
        "default"    => true,
    ),
    array(
        "id"         => "DELIVERY_FROM",
        "content"    => "Время, от",
        "default"    => true,
    ),
    array(
        "id"         => "DELIVERY_TO",
        "content"    => "Время, до",
        "default"    => true,
    ),
    array(
        "id"         =>"PRICE",
        "content"    => "Стоимость",
        "default"    => false,
    ),


);

$lAdmin->AddHeaders($arHeaders);

$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();
$bNeedProps = false;
foreach ($arVisibleColumns as $visibleColumn)
{
    if (!$bNeedProps && SubStr($visibleColumn, 0, StrLen("PROP_")) == "PROP_"){
        $bNeedProps = true;
    }

    if(SubStr($visibleColumn, 0, StrLen("PROP_")) != "PROP_") {
        $arSelectFields[] = $visibleColumn;
   	}
}

$arSelectFields[] = 'ID';
$arSelectFields[] = 'CUR_ID';
$arSelectFields[] = 'ORDER_ID';
$arSelectFields[] = 'TIME_SEND';
$arSelectFields[] = 'TIME_UPDATE';
$arSelectFields[] = 'STATUS';
$arSelectFields[] = 'ACCOUNT_NUMBER';
$arSelectFields[] = 'DELIVERY_DATE';
$arSelectFields[] = 'DELIVERY_FROM';
$arSelectFields[] = 'DELIVERY_TO';
$arSelectFields[] = 'PRICE';

	function CheckFilter()
	{
		global $FilterArr, $lAdmin;
		foreach ($FilterArr as $f) global $$f;
		return count($lAdmin->arFilterErrors) == 0; //   ,  false;
	}
	$FilterArr = Array(
		"find_id_from",
		"find_id_to",
		/*"find_account_number_from",
		"find_account_number_to",
		"find_cur_id_from",
		"find_cur_id_to",
		"find_delivery_date_from",
		"find_delivery_date_to",*/
		"find_status"
	);
	$lAdmin->InitFilter($FilterArr);

	if (CheckFilter())
	{
		$arFilter = Array(
			">=ID"				=> $find_id_from,
			"<=ID"				=> $find_id_to,
			/*">=ACCOUNT_NUMBER"	=> $find_account_number_from,
			"<=ACCOUNT_NUMBER"	=> $find_account_number_to,
			">=DATE_INSERT" 	=> $find_date_insert_from,
			"<=DATE_INSERT" 	=> $find_date_insert_to,*/
			"STATUS" 		=> $find_status
		);
	}



	foreach($arFilter as $key => &$value)
	{
		if(empty($value))
		unset($arFilter[$key]);
	}
	 $order_by = 'DELIVERY_DATE';
	$sort_by = 'DESC';
	if ($_REQUEST['by']){
		$order_by  = $_REQUEST['by'];
	}

	if ($_REQUEST['order']){
		$sort_by  = $_REQUEST['order'];
	}

	$query = 'SELECT * FROM `medi_courierist_orders` ORDER BY '.$order_by.' '.$sort_by.'  ';

	$curNewOrder = $DB->Query($query);


	$rsData = new CAdminResult($curNewOrder, $sTableID);
	$rsData->NavStart();
	$lAdmin->NavText($rsData->GetNavPrint(GetMessage("BB_PAGING_TITLE")));



	\Bitrix\Main\Loader::IncludeModule("sale");

	while($arRes = $rsData->NavNext(true, "f_"))
	{

		$arOrder = Bitrix\Sale\Order::load($f_ORDER_ID);

		$row =&$lAdmin->AddRow($f_ID, $arRes);


		$IdField = '<b>'.$arRes["ID"].'</b>';


		$row->AddViewField("ID", $IdField);

		$OrderIdField = '<a href="/bitrix/admin/sale_order_view.php?lang=ru&ID='.$arRes["ORDER_ID"].'" target="_blank">'.$arRes["ORDER_ID"].'</a>';


		$row->AddViewField("ORDER_ID", $OrderIdField);


		$row->AddViewField("STATUS", '['.$arRes["STATUS"].'] '.$curStatuses[$arRes['STATUS']].' ['.$arOrder->getField('STATUS_ID').']');





		$arActions = array(
			"reloadStatus" => array(
					"ICON" 		=> "reload",
					"TEXT" 		=> "Обновить",
					"ACTION" 	=> $lAdmin->ActionDoGroup($f_ID, "reloadStatus"),
			),
			"printAct" => array(
					"ICON" 		=> "print",
					"TEXT" 		=> "Сформировать акт",
					"ACTION" 	=> $lAdmin->ActionDoGroup($f_ID, "printAct"),
			),

		);


		if($arRes["STATUS"] >= "50" )
		{
			//$arActions["reloadStatus"]["DISABLED"]	 = true;
		}
		if($arRes["STATUS"] == "10" )
		{
			$arActions["printAct"]["DISABLED"]	 = true;
		}
		 $row->AddActions($arActions);


}

$lAdmin->AddFooter(
    array(
        array("title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"), "value" => $rsData->SelectedRowsCount()),
        array("counter" => true, "title" => GetMessage("MAIN_ADMIN_LIST_CHECKED"), "value" => "0"),
    )
);


$arActionsTable = Array(
    "reloadStatus" 	=> "Обновить статус",
    "printAct" 	=> "Сформировать акт",
);

$arActionsParams = array("select_onchange" =>
	"if(this[this.selectedIndex].value == 'reloadStatus' ){
		//this.selectedIndex = 0;
	}");
$lAdmin->AddGroupActionTable($arActionsTable, $arActionsParams);
$lAdmin->AddAdminContextMenu();
$lAdmin->CheckListMode();
$APPLICATION->SetTitle('Заявки Курьерист');

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?>

<form name="find_form" method="get" action="<?echo $APPLICATION->GetCurPage();?>">

<? $arFilterOpts =   array(
	"ID",
	"ID заказа",
	"Номер заказа",
	"Номер заявки",
	"Статус заявки",

);

$oFilter = new CAdminFilter(
	$sTableID."_filter",
	$arFilterOpts
);

$oFilter->Begin();
?>
<tr>
  <td nowrap><?="ID";?>:</td>
  <td nowrap>
    От:<input type="text" name="find_id_from" size="20" value="<?echo htmlspecialchars($find_id_from)?>">
    До:<input type="text" name="find_id_to" size="20" value="<?echo htmlspecialchars($find_id_to)?>">
  </td>
</tr>
<tr>
  <td nowrap><?="Номер заказа";?>:</td>
  <td nowrap>
    От<input type="text" name="find_account_number_from" size="20" value="<?echo htmlspecialchars($find_account_number_from)?>">
    До<input type="text" name="find_account_number_to" size="20" value="<?echo htmlspecialchars($find_account_number_to)?>">
  </td>
</tr>
<tr>
  <td nowrap><?="Номер заявки";?>:</td>
  <td nowrap>
    От<input type="text" name="find_cur_id_from" size="20" value="<?echo htmlspecialchars($find_cur_id_from)?>">
    До<input type="text" name="find_cur_id_to" size="20" value="<?echo htmlspecialchars($find_cur_id_to)?>">
  </td>
</tr>
<?/*<tr>
    <td nowrap><?=GetMessage("BB_FILTER_DATE_INSERT")?>:</td>
    <td nowrap><?echo CalendarPeriod("find_date_insert_from", $find_date_insert_from, "find_date_insert_to", $find_date_insert_to, "find_form", "Y")?></td>
</tr>
<tr>
    <td valign="top"><?echo GetMessage("BB_FIELD_STATUS")?>:<br /><img src="/bitrix/images/sale/mouse.gif" width="44" height="21" border="0" alt=""></td>
    <td valign="top">
	    <select name="find_status[]" multiple size="4">
	    	<option <?if(!$find_status) echo "";?>>(<?=strtolower(GetMessage("BB_NO"));?>)</option>
	    <?
	        $dbStatusListFillter = array("LID" => LANGUAGE_ID);
	        if($StatusExclude){
	            $dbStatusListFillter["!ID"] = $StatusExclude;
	        }
	        $dbStatusList = CSaleStatus::GetList(
	            array("SORT" => "ASC"),
	            $dbStatusListFillter,
	            false,
	            false,
	            array("ID", "NAME", "SORT")
	        );
	        while ($arStatusList = $dbStatusList->Fetch())
	        {
	        ?><option value="<?= htmlspecialchars($arStatusList["ID"]) ?>"<?if (is_array($find_status) && in_array($arStatusList["ID"], $find_status)) echo " selected"?>>[<?= htmlspecialchars($arStatusList["ID"]) ?>] <?= htmlspecialcharsEx($arStatusList["NAME"]) ?></option><?
	        }
	    ?>
	    </select>
	</td>
</tr>

<tr>
    <td><?echo GetMessage("BB_FIELD_PAYED");?>:</td>
    <td>
        <select name="find_payed">
            <option value=""><?echo GetMessage("BB_ALL")?></option>
            <option value="Y"<?if ($filter_payed=="Y") echo " selected"?>><?echo GetMessage("BB_YES")?></option>
            <option value="N"<?if ($filter_payed=="N") echo " selected"?>><?echo GetMessage("BB_NO")?></option>
        </select>
    </td>
</tr>
<tr>
    <td><?echo GetMessage("BB_FIELD_CANCELED")?>:</td>
    <td>
        <select name="find_canceled">
            <option value=""><?echo GetMessage("BB_ALL")?></option>
            <option value="Y"<?if ($find_canceled=="Y") echo " selected"?>><?echo GetMessage("BB_YES")?></option>
            <option value="N"<?if ($find_canceled=="N") echo " selected"?>><?echo GetMessage("BB_NO")?></option>
        </select>
    </td>
</tr>*/?>
<?
$oFilter->Buttons(array("table_id"=>$sTableID, "url"=>$APPLICATION->GetCurPage(), "form"=>"find_form"));
$oFilter->End();
?>
</form>

<script type="text/javascript">
/*var selected_bxb_id = null;
openStatusBox = function (val){
	var block = document.getElementById(val);
	block.style['display'] = (block.style['display'] == 'none' ? 'block' : 'none');
}*/
</script>

<?$lAdmin->DisplayList();?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
