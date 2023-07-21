<?define("NO_HEAD_BREADCRUMB", "Y");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("График работ | Официальный интернет-магазин medi");?>

<?
$salons = [];
$cityid = SITE_ID == 's2' ? 328 : 327;

$def_city  = $GLOBALS['medi']['site_order'][SITE_ID];
$obSalons = CIBlockElement::GetList(
        ["SORT"=>"asc"],
        ['SECTION_ID' => $cityid, 'IBLOCK_ID'=>24, 'ACTIVE'=>'Y', 'ACTIVE_DATE'=>'Y'],
        false,
        false,
        ['ID', 'NAME', 'PREVIEW_TEXT','PROPERTY_STORE']
    );
while ($arSalons = $obSalons->GetNext())
{
    if ($arSalons['PROPERTY_STORE_VALUE'] > 0) {
        $sFilter['UF_CITY'] = $def_city;
        $sFilter  = array(
            "ACTIVE" =>"Y",

            "UF_SALON"=>true,
            "ID" => $arSalons['PROPERTY_STORE_VALUE']
        );
        $resStore = CCatalogStore::GetList(array("SORT"=>"ASC"), $sFilter, false, false, array("ID", "CODE",  "ADDRESS", "DESCRIPTION", "ACTIVE","UF_METRO"));
        if($sklad = $resStore->Fetch())
        {
            $sklad['ADDRESS'] = preg_replace("/[0-9]{6},/", "", $sklad["ADDRESS"]);
            $metro = unserialize($sklad['UF_METRO']);
            if (!empty($metro[0]))
            {
                $rsElm = CIBlockElement::GetList(array(), array("ID" => $metro[0], "IBLOCK_ID" => "23", "ACTIVE"=>"Y"), false, false, array("ID", "NAME", "IBLOCK_SECTION_ID"));
                if ($arMetro = $rsElm -> GetNext()) {

                    $rsSect = CIBlockSection::GetList(array("NAME"=>"ASC"), array( "IBLOCK_ID" => "23", "ACTIVE"=>"Y", "ID"=> $arMetro['IBLOCK_SECTION_ID']), false, array("NAME", "PICTURE", "IBLOCK_SECTION_ID" ));
                    if ($arSect = $rsSect->GetNext()) {
                        if ($arSect['PICTURE'] > 0) {
                            $arSect['ICON'] = CFile::GetFileArray($arSect["PICTURE"]);
                        }
                        $arMetro['SECTION'] = $arSect;
                    }
                    $sklad['METRO'] = $arMetro;
                }

            }

            $arSalons['SALON'] = $sklad;
        }
    }
    $salons[] = $arSalons;
}
__($salons);
?>
<style>
    .medi-new-select {
        padding: 10px 12px!important;
        text-align: left!important;
        width: 230px;
        margin-bottom: 1em;
        background: #fff;
    }
    .medi-new-select__item {
        text-align: left;
    }
    .medi-new-select__item span:hover {
        color: #fff;
        background-color: #e20074;
        font-family: "roboto_ltregular"!important;
    }
    .medi-new-select__item span {

        padding: 6px 12px;
    }
    .shedule_block {
        text-align: center;
        font-size: 16px;
    }
    .select_salon_title {
        color:#9B9B9B;
        font-size: 16px;
        margin: 1em 0;
        font-family: robotoMedium;
    }
    .shedule {
        margin: 0 auto;
        text-align: center;
        display: flex;
        flex-wrap: wrap;
        max-width: 1100px;
    }
    .shedule-item {
        margin-top: 5px;
        max-width: 110px;
        -webkit-flex: 1 1 100px;
        -ms-flex:  1 1 100px;
        flex:  1 1 100px;
        height: 120px;
        box-sizing: border-box;
    }
    .shedule-date {
        border: 1px solid #c4c4c4;
        background: #fff;
        padding: 0.5em 1em;
        font-family: robotoMedium;
        font-size: 14px;
        width: 110px;
        min-height: 60px;
        box-sizing: border-box;
    }
    .shedule-date.holyday{ color:#fff; background: #e20074; }
    .shedule-time {
        border: 1px solid #c4c4c4;
        background: #fff;
        padding: 0.5em 1em;
        font-family: robotoMedium;
        font-size: 14px;
        width: 110px;
        min-height: 60px;
        box-sizing: border-box;
        line-height: 40px;

    }
    .cgray {
        color:#888;
    }
</style>
<br><br>
<div class="limiter light-bg shedule_block" >
<div class="h3 ff-medium">Дорогие покупатели!</div>
<p>Мы стараемся делать всё, чтобы вы могли приходить к нам, когда вам необходимо!<br/>
    Пожалуйста, ознакомьтесь с графиком работы наших салонов в период новогодних каникул:</p>
<br/>
<div class="select_salon_title">Выберите салон:</div>

<select class="medi-select" >
    <?foreach ($salons AS $k=>$arSalon)
    {?>
    <option value="<?=$arSalon['SALON']['ID']?>" data-link="<?=$arSalon['SALON']['CODE']?>">м. <?=$arSalon['SALON']['METRO']['NAME']?></option>
    <?}?>
</select>

<div  class="select_salon_title">Праздничный график салона:</div>

<div class="shedule">
    <div class="shedule-item">
        <div class="shedule-date holyday">31.12.2021<br>ПТ</div>
        <div class="shedule-time sh0"> </div>
    </div>
    <div class="shedule-item">
        <div class="shedule-date">01.01.2022<br>СБ</div>
        <div class="shedule-time sh1">выходной</div>
    </div>
    <div class="shedule-item">
        <div class="shedule-date">02.01.2022<br>ВС</div>
        <div class="shedule-time sh2"> </div>
    </div>
    <div class="shedule-item">
        <div class="shedule-date">03.01.2022<br>ПН</div>
        <div class="shedule-time sh3"> </div>
    </div>
    <div class="shedule-item">
        <div class="shedule-date">04.01.2022<br>ВТ</div>
        <div class="shedule-time sh4"> </div>
    </div>
    <div class="shedule-item">
        <div class="shedule-date">05.01.2022<br>СР</div>
        <div class="shedule-time sh5"> </div>
    </div>
    <div class="shedule-item">
        <div class="shedule-date">06.01.2022<br>ЧТ</div>
        <div class="shedule-time  sh6"> </div>
    </div>
    <div class="shedule-item">
        <div class="shedule-date holyday">07.01.2022<br>ПТ</div>
        <div class="shedule-time sh7"> </div>
    </div>
    <div class="shedule-item">
        <div class="shedule-date">08.01.2022<br>СБ</div>
        <div class="shedule-time sh8"> </div>
    </div>
    <div class="shedule-item">
        <div class="shedule-date">09.01.2022<br>ВС</div>
        <div class="shedule-time sh9"> </div>
    </div>

</div>
</div>
<div class="limiter text-center">
    <p class=" ff-medium cgray" >
        График и режим работы уточняйте на <a href="//www.medi-salon.ru/salons/<?=($GLOBALS['medi']['sfolder'][SITE_ID] != '' ? $GLOBALS['medi']['sfolder'][SITE_ID].'/' : '')?><?=$salons[0]['SALON']['CODE']?>/" class="medi-color" id="salon_link">странице салона</a> или по телефону Контактного центра <a href="tel:<?=$GLOBALS['medi']['phones'][SITE_ID]?>" class="medi-color"><?=$GLOBALS['medi']['phones'][SITE_ID]?></a>.
    </p>
    <p class="ff-medium h2">С Новым 2022 годом!</p>
</div>

<script src="/bitrix/templates/dresscodeV2/js/medi.select.js"></script>
 <script>
     $(document).ready(function() {

         var changeMediSelect = function(){
             if ($(this).data("value") == undefined)
             {
                 $this = $(".medi-new-select__item:first-child");
                 console.log($this.data("value"));
             }
             else {
                 $this = $(this);
                 console.log($this.data("value"));
             }
             if ($this.data("value") > 0)
             {
                $store_id = $this.data("value");
                 showLoader();

                 $.ajax({
                     url: '/ajax/salon/',
                     data: 'id='+$store_id+'&action=get_schedule',

                     dataType: 'json',
                     success:  function(data) {
                         if (data.status == 'ok'){

                             $.each(data.days,function(index,value) {
                                 $(".shedule-time.sh"+index).html(value);
                             });
                             folder = '/';
                             if (data.data.SITE_ID == 's2')
                                 folder = '/spb/';
                             $("#salon_link").attr("href", "//www.medi-salon.ru/salons"+folder+data.data.SALON.CODE+"/");

                         }

                         else if (data.status == 'error')
                         {
                             console.log('error');
                             console.log(data);

                         }

                     },
                     complete: function(data) {
                         hideLoader();
                     }
                 });
             }

         };
         changeMediSelect();


         $("#selectMediParams .medi-new-select__item").on("click", changeMediSelect);
     });
 </script>


<br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
