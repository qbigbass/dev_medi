<?
header("Content-type: application/json; charset=utf-8");

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
define("API_KEY", "391d1c41-5055-400d-8afc-49ee21c8f4a1");
$APPLICATION->SetTitle("Мои заявки");
$APPLICATION->SetPageProperty('title', 'Мои заявки');
$APPLICATION->SetPageProperty('robots', 'noindex,nofollow');
$APPLICATION->AddHeadScript("https://api-maps.yandex.ru/2.1/?apikey=".API_KEY."&lang=ru_RU");
?>
<?
CModule::includeModule("form");
$FORM_ID = 9;

$contractor = 'Гололобов';  //$USER->GetLastName();
$data = date("d.m.Y");

if (isset($_REQUEST['date']))
{
    $delivery_date = date("d.m.Y", strtotime($_REQUEST['date']));
}


?>
<script>
$(function () {
$('.datepicker').pickadate({
    format: 'dd.mm.yyyy'
});

});
</script>
<div class="my-orders">
    <form method="get" action="">
    <div class="container">
        <div class="four columns right">
            <h4>Мои заявки</h4>
        </div>

        <div class="four columns ">
            <input type="text" name="date" id="delivery_date" class="u-full-width datepicker" value="<?=date("d-m-Y")?>"/>
        </div>
        <div class="four columns">
            <input type="submit" class="u-full-width" value="Показать">
        </div>
    </div>
    </form>
</div>
<?if (isset($delivery_date) &&  $contractor != ''){
    $http = new HttpClient();
    $http->setHeader('Content-type', 'application/json; charset=utf-8');

    // ИМ
    $parameters = [
        'action' => 'orders',
        'spec'   => $contractor,
        'date'   => $delivery_date
    ];
    $query_str = http_build_query($parameters);

    $http->get("https://www.medi-salon.ru/local/tools/orto/".'?'.$query_str);

    $status = $http->getStatus();
    $result = new Result();

    $main_addresses = [];
    if ($status == '200')
    {
        $result->addError(new Error("Error ".$status));

        $main_addresses = Json::decode($http->getResult());


    }

    // ОРТО
    $parameters = [
        'action' => 'orders',
        'spec'   => $contractor,
        'date'   => $delivery_date
    ];
    $query_str = http_build_query($parameters);

    $http->get("https://orto.medi-salon.ru/tools/shedule/".'?'.$query_str);
    $status = $http->getStatus();
    $result = new Result();

    $orto_addresses = [];
    if ($status == '200')
    {
        $result->addError(new Error("Error ".$status));

        $orto_addresses = Json::decode($http->getResult());



    }


    $alladdr = [];
    foreach ($main_addresses as $key => $value) {
       $alladdr[] = htmlspecialchars($value['ADDRESS'], ENT_QUOTES);
    }
    foreach ($orto_addresses as $key => $value) {
        $alladdr[] = htmlspecialchars($value['client_address'], ENT_QUOTES);
    }

    foreach ($alladds as $key => $value) {
        $alladdr[$key] = "МО, ";
        // code...
    }

    ?>
    <script type="text/javascript">

        var main_addr = <?=CUtil::PhpToJSObject($main_addresses);?>;
        var orto_addr = <?=CUtil::PhpToJSObject($orto_addresses);?>;

        function init(){

            var multiRoute = new ymaps.multiRouter.MultiRoute({
                   // Описание опорных точек мультимаршрута.
                   referencePoints: [
                      <?='"'.implode('", "',  $alladdr).'"'?>
                   ],
                   // Параметры маршрутизации.
                   params: {
                       // Ограничение на максимальное количество маршрутов, возвращаемое маршрутизатором.
                       results: 3
                   }
               }, {
                   // Автоматически устанавливать границы карты так, чтобы маршрут был виден целиком.
                   boundsAutoApply: true
               });

               // Создаем кнопки для управления мультимаршрутом.
               var trafficButton = new ymaps.control.Button({
                       data: { content: "Учитывать пробки" },
                       options: { selectOnClick: true }
                   }),
                   viaPointButton = new ymaps.control.Button({
                       data: { content: "Добавить транзитную точку" },
                       options: { selectOnClick: true }
                   });

               // Объявляем обработчики для кнопок.
               trafficButton.events.add('select', function () {
                   multiRoute.model.setParams({ avoidTrafficJams: true }, true);
               });

               trafficButton.events.add('deselect', function () {
                   multiRoute.model.setParams({ avoidTrafficJams: false }, true);
               });

               viaPointButton.events.add('select', function () {
                   var referencePoints = multiRoute.model.getReferencePoints();
                   referencePoints.splice(1, 0, "Москва, ул. Солянка, 7");

                   multiRoute.model.setReferencePoints(referencePoints, [1]);
               });

               viaPointButton.events.add('deselect', function () {
                   var referencePoints = multiRoute.model.getReferencePoints();
                   referencePoints.splice(1, 1);
                   multiRoute.model.setReferencePoints(referencePoints, []);
               });
            // Создание карты.
            var myMap = new ymaps.Map("map", {
                center: [55.76, 37.64],
                zoom: 7,

                controls: [trafficButton, viaPointButton]
                    }, {
                buttonMaxWidth: 300
            });
            myMap.geoObjects.add(multiRoute);

            <?/*foreach ($main_addresses as $key => $value) {
                ?>
                ymaps.geocode('<?=$value['ADDRESS']?>', {results: 1}).then(function (res) {


                  var firstGeoObject = res.geoObjects.get(0),
                        // Координаты геообъекта.
                        coords = firstGeoObject.geometry.getCoordinates(),
                        // Область видимости геообъекта.
                        bounds = firstGeoObject.properties.get('boundedBy');

                    firstGeoObject.options.set('preset', 'islands#darkBlueDotIconWithCaption');
                    // Получаем строку с адресом и выводим в иконке геообъекта.
                    firstGeoObject.properties.set('iconCaption', firstGeoObject.getAddressLine());

                    // Добавляем первый найденный геообъект на карту.
                    myMap.geoObjects.add(firstGeoObject);

                     main_addr.res = firstGeoObject;
               });
             <?}?>
             <?foreach ($orto_addresses as $key => $value) {
                 ?>
                 ymaps.geocode('<?=$value['client_address']?>', {results: 1}).then(function (res) {


                   var firstGeoObject = res.geoObjects.get(0),
                         // Координаты геообъекта.
                         coords = firstGeoObject.geometry.getCoordinates(),
                         // Область видимости геообъекта.
                         bounds = firstGeoObject.properties.get('boundedBy');

                     firstGeoObject.options.set('preset', 'islands#darkBlueDotIconWithCaption');
                     // Получаем строку с адресом и выводим в иконке геообъекта.
                     firstGeoObject.properties.set('iconCaption', firstGeoObject.getAddressLine());

                     // Добавляем первый найденный геообъект на карту.
                     myMap.geoObjects.add(firstGeoObject);

                     orto_addr.res = firstGeoObject;
                });
              <?}*/?>

              console.log(main_addr);
        }


        ymaps.ready(init);
    </script>

<div class="container">
    <div class="row">

        <div class="lg-col-4 md-col-12"><h4>Список</h4>
            <ul>
            <?foreach ($main_addresses as $key => $value) {
                ?><li><?=$value['ACCOUNT_NUMBER']?> <?=$value['ADDRESS']?></li><?

            }?>

            <?foreach ($orto_addresses as $key => $value) {
                ?><li><?=$value['id']?> <?=$value['client_address']?></li><?

            }?>
            </ul>
        </div>
        <div class="lg-col-8 md-col-12">
            <div id="map" style="min-height:300px;height:70vh;"></div>
        </div>

    </div>
</div>
<br/><br/><br/>
<?}?>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>
