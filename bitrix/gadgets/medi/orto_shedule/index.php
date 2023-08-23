<?if( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die();
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;

if( !CModule::IncludeModule( "iblock" ) || !CModule::IncludeModule( "sale" ) ){
    return false;
}

$public_api_url = 'https://orto.medi-salon.ru/tools/shedule/';

$allSpecs = [
    ['CODE' => 'nistuk', 'NAME' => 'Гаркушенко Артем Витальевич +7 (916) 747-73-24', 'LAST_NAME'=>'Гаркушенко'],
    ['CODE' => 'gololobov', 'NAME' => 'Гололобов Денис Владимирович +7 (916) 053-72-63', 'LAST_NAME'=>'Гололобов'],
    ['CODE' => 'rtischev' , 'NAME' => 'Ртищев Виталий Александрович +7 (916) 515-58-07', 'LAST_NAME'=>'Ртищев'],
   // ['CODE' => 'bulgakov' , 'NAME' => 'Гуров Алексей Дмитриевич +7 (916) 515-52-24', 'LAST_NAME'=>'Гуров'],
    ['CODE' => 'volkov' , 'NAME' =>  'Волков Дмитрий Петрович +7 (985) 103-51-47', 'LAST_NAME'=>'Волков'],
];


$d = 0;
while( $d < 4 )
{
    $deliv_date =  date('d.m.Y', time() + $d*86400);


    $shedule = [];
    foreach ($allSpecs AS $k=>$spec)
    {
        $filter = [];
        $filter['@STATUS_ID'] = ['A','I'];
        $filter['=PROPERTY_VAL.CODE'] = 'GPO_SPECIALIST';

        $filter['=PROPERTY_VAL.VALUE'] = $spec['CODE'];

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

        $shedule[$d] = 0;
        while ($order = $dbRes->fetch())
        {
            $shedule[$d]++;
        }

        $allSpecs[$k]['IM'][$d] = $shedule[$d];

    }

    $http = new HttpClient();
    $http->setHeader('Content-type', 'application/json; charset=utf-8');

    $http->disableSslVerification();
    foreach ($allSpecs AS $k=>$spec)
    {
        $parameters = [
            'action' => 'shedule',
            'spec'   => $spec['LAST_NAME'],
            'date'   => $deliv_date
        ];
        $query_str = http_build_query($parameters);

        $http->get($public_api_url.'?'.$query_str);

        $status = $http->getStatus();
        $result = new Result();

        if ($status == '200')
        {
            $result->addError(new Error("Error ".$status));

            $data = Json::decode($http->getResult());
            $allSpecs[$k]['ORTO'][$d] = $data[0];

        }
        else
        {
            $allSpecs[$k]['ORTO'][$d] = '-';

        }


    }
    $d++;
    /*echo  "<pre>";
    print_r($allSpecs);
    echo  "</pre>";*/
}

?>
<div class="bx-gadgets-info">
    <div class="bx-gadgets-content-padding-rl bx-gadgets-content-padding-t" style="font-weight: bold; line-height: 28px;">Уже созданные заявки по дням (ИМ / ГПО):</div>
    <div style="margin: 0 1px 0 1px; border-bottom: 1px solid #D7E0E8;"></div>
    <div class="bx-gadgets-content-padding-rl">
        <?if(!empty($allSpecs)  ){?>
        <table class="bx-gadgets-info-site-table" style="border-collapse: collapse;">
            <tr>
                <th style="width:170px;padding: 5px;">Специалист:</th>
                <?$d = 0;
                while($d<4){?>
                <th style="<?=(in_array(date('N', time() + $d*86400), [6,7]) ? 'color:red' : '') ?>"><?=$d==0 ? 'Сегодня' : ($d == '1' ? 'Завтра' : date('d.m', time() + $d*86400));?></th>
                <?
                $d++;
                }?>
            </tr>
            <?foreach ($allSpecs AS $k=>$spec){?>
            <tr style="<?=$k%2 == 0 ? 'background:#f7f7f7;' : ''?>">
                <td style="width:180px;"><?=str_replace('+', '<br>+', $spec['NAME'])?></td>
                <?$d = 0;
                while($d<4){?>
                    <td style="text-align: center" title="<?=$allSpecs[$k]['IM'][$d];?> / <?=$allSpecs[$k]['ORTO'][$d];?>"><?=$allSpecs[$k]['IM'][$d]+$allSpecs[$k]['ORTO'][$d];?></td>
                    <?
                    $d++;
                }?>
            </tr>
            <?}?>
        </table>
        <?}?>
    </div>
</div>
