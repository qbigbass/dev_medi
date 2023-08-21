<?

header("Content-type: application/json; charset=utf-8");

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if( !CModule::IncludeModule( "iblock" ) || !CModule::IncludeModule( "sale" ) ){
    return false;
}


$FORM_ID = 9;

$action = '';
if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], array('shedule', 'orders'))) {
    $action = strval($_REQUEST['action']);
} else
    die("action");

if ($action == 'orders'){

$public_api_url = 'https://orto.medi-salon.ru/tools/shedule/';

$allSpecs = [
    ['CODE' => 'nistuk', 'NAME' => 'Гаркушенко Артем Витальевич +7 (916) 747-73-24', 'LAST_NAME'=>'Гаркушенко'],
    ['CODE' => 'gololobov', 'NAME' => 'Гололобов Денис Владимирович +7 (916) 053-72-63', 'LAST_NAME'=>'Гололобов'],
    ['CODE' => 'rtischev' , 'NAME' => 'Ртищев Виталий Александрович +7 (916) 515-58-07', 'LAST_NAME'=>'Ртищев'],
    ['CODE' => 'bulgakov' , 'NAME' => 'Бондаренко Кирилл Александрович +7 (916) 515-52-24', 'LAST_NAME'=>'Бондаренко'],
    ['CODE' => 'rybakov' , 'NAME' =>  'Бондаренко Кирилл Александрович +7 (905) 594-61-41', 'LAST_NAME'=>'Бондаренко'],
];

if (isset($_REQUEST['date']))
{
    $deliv_date =  date('d.m.Y', strtotime($_REQUEST['date']));
}
else{
    $deliv_date =  date('d.m.Y');
}

$spec = trim($_REQUEST['spec']);

if (isset($_REQUEST['spec']) &&
    array_filter($allSpecs,function($a){
      return $a["LAST_NAME"]==$_REQUEST['spec'];
    })
) {
    $action = strval($_REQUEST['action']);
} else
    die("spec");

    $arSpec = (array_filter($allSpecs,function($a){
          return $a["LAST_NAME"]==$_REQUEST['spec'];
      }));

      $specialist = '';
     foreach($arSpec AS $spec)
     {
         $specialist = $spec['CODE'];
     }

     if ($specialist == '') die('spec empty');

    $address = [];

        $filter = [];
        $filter['@STATUS_ID'] = ['A','I', 'F'];

        $filter['=PROPERTY_VAL.CODE'] = 'GPO_SPECIALIST';
        $filter['=PROPERTY_VAL.VALUE'] = $specialist;

        $filter['=PROPERTY_VAL2.CODE'] = 'DELIVERY_PLANNED';
        $filter['=PROPERTY_VAL2.VALUE'] = $deliv_date;



        $dbRes = \Bitrix\Sale\Order::getList([
            'select' => ['ID', 'PROPERTY_VAL.VALUE', 'PROPERTY_VAL2.VALUE'],
            //'group' => ['PROPERTY_VAL2.VALUE'],
            'filter' => $filter,
            'order' => ['ID' => 'DESC'],
            'runtime' => [
                new \Bitrix\Main\Entity\ReferenceField(
                    'PROPERTY_VAL',
                    '\Bitrix\sale\Internals\OrderPropsValueTable',
                    ["=this.ID" => "ref.ORDER_ID"],
                    ["join_type"=>"left"]
                ),
                new \Bitrix\Main\Entity\ReferenceField(
                    'PROPERTY_VAL2',
                    '\Bitrix\sale\Internals\OrderPropsValueTable',
                    ["=this.ID" => "ref.ORDER_ID"],
                    ["join_type"=>"left"]
                ),


            ]
        ]);

        $address = [] ;
        while ($order = $dbRes->fetch())
        {
            //echo "<pre>";print_r($order);
            $arOrder =  \Bitrix\Sale\Order::load($order['ID']);
            $propertyCollection = $arOrder->getPropertyCollection();
            $location = $propertyCollection->getItemByOrderPropertyCode("LOCATION")->getField('VALUE');
            $ACCOUNT_NUMBER = $arOrder->getField("ACCOUNT_NUMBER");
            $res = \Bitrix\Sale\Location\LocationTable::getList(array(
        	'filter' => array('=CODE' => $location, '=NAME.LANGUAGE_ID' => LANGUAGE_ID),
        	'select' => array('NAME_RU' => 'NAME.NAME')
        	));
        	if ($item = $res->fetch()) {
        		$address_str =$item['NAME_RU'];
        	}

            $address_str .= ', '. $propertyCollection->getItemByOrderPropertyCode("ADDRESS")->getField('VALUE') ;
            $address[] = ['ORDER_ID'=>$order['ID'], "ADDRESS"=> " ".str_replace([ "этаж", "палата", "отделение", "нейрохирургии","Домофон","вход ", "частный ","код ", "подъезд ","домофона ", "ЧЕРЕЗ "], "",$address_str), 'ACCOUNT_NUMBER'=>$ACCOUNT_NUMBER];
        }


        echo json_encode($address);

}
