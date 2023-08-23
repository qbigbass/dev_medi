<?
set_time_limit(90);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Разметка акций");
if (!$USER->IsAuthorized())
 die();

 $groups = $USER->GetUserGroupArray();

 if (!in_array(1, $groups) && !in_array(8, $groups)){

     LocalRedirect("/");die;
 }


$IBLOCK_ID = 17;
$OFFERS_IBLOCK_ID = 19;
$action_code = 'ACTION_SIGN';
$offers_code = 'OFFERS';

$arSections = [];

$obSect = CIBlockSection::GetList(
    ["ID"=>"ASC"],
    ["IBLOCK_ID" => $IBLOCK_ID, "ACTIVE"=>"Y", "SECTION_ID" =>false],
    false,
    ["ID", "NAME"]
);
while ($arSect = $obSect->GetNext()) {
    $arSections[] = ['ID' => $arSect['ID'], "NAME" => $arSect['NAME']];
}

$obActions =  CIBlockPropertyEnum::GetList(Array("VALUE"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>$action_code));
$exists_actions = [];
$PROPERTY_ID = '';
while($arActions = $obActions->GetNext())
{
    $exists_actions[$arActions["ID"]]['CODE'] = $arActions["VALUE"];
    $obActElm = CIBlockElement::GetList(
        ["ID"=>"ASC"],
        ["IBLOCK_ID" => $IBLOCK_ID,  "PROPERTY_ACTION_SIGN_VALUE"=>$arActions["VALUE"], "ACTIVE"=>"Y" ],
        [],
        false,
        ["ID"]
    );
    if($obActElm)
    {
        $exists_actions[$arActions["ID"]]['CNT'] = $obActElm;
    }
    else {
        $exists_actions[$arActions["ID"]]['CNT'] = 0;
    }
    $PROPERTY_ID = $arActions['PROPERTY_ID'];
}

$obOffers =  CIBlockPropertyEnum::GetList(Array("ID"=>"ASC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>$offers_code));
$exists_offers = [];
$OFFERS_PROPERTY_ID = '';
while($arOffers = $obOffers->GetNext())
{
    $exists_offers[$arOffers["ID"]]['ID'] = $arOffers["ID"];
    $exists_offers[$arOffers["ID"]]['CODE'] = $arOffers["VALUE"];
    $exists_offers[$arOffers["ID"]]['XML_ID'] = $arOffers["XML_ID"];
    $exists_offers[$arOffers["ID"]]['NAME'] = $arOffers["PROPERTY_NAME"];
    $obOffElm = CIBlockElement::GetList(
        ["ID"=>"ASC"],
        ["IBLOCK_ID" => $IBLOCK_ID,  "%PROPERTY_".$offers_code."_VALUE"=>$arOffers["VALUE"], "ACTIVE"=>"Y" ],
        [],
        false,
        ["ID"]
    );
    if($obOffElm)
    {
        $exists_offers[$arOffers["ID"]]['CNT'] = $obOffElm;
    }
    else {
        $exists_offers[$arOffers["ID"]]['CNT'] = '0';
    }
    $OFFERS_PROPERTY_ID = $arOffers['PROPERTY_ID'];

}
// Получаем список свойств для обновления
// 345  - Я.Маркет   114 - Бренд   154 - Похожие   152 - Сопутствующие

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
</style>
<div class="container">
    <?
    if (isset($_SESSION['tools'])){?>
    <div class=" tool_notice <?=($_SESSION['tools']['alert'] ? 'alert' : 'error')?>">
        <?=$_SESSION['tools']['alert']?>
        <?=$_SESSION['tools']['error']?>

        <?w2l($_SESSION['tools']['alert'], 1, 'marks_alerts.log');?>
        <?w2l($_SESSION['tools']['error'], 1, 'marks_errors.log');?>
    </div>
    <?
    unset($_SESSION['tools']);
    }?>
    <form method="post" action="">
        <input type="hidden" name="action" value="find"/>
        <div class="row">
            <div class="ten  columns">
                <label for="order-form__goodIds" class="order-form__label--truncate">Категория:</label>
                <select  class="u-full-width"  id="order-form__section"   name="section" >
                    <option value="">Все</option>
                    <?foreach ($arSections as $key => $value) {?>
                        <option value="<?=$value['ID']?>"><?=$value['NAME']?></option>
                    <?}?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="ten  columns">
                <label for="order-form__goodIds" class="order-form__label--truncate">Список артикулов ТП (с новой строки):</label>
                <textarea  class="u-full-width" cols="10" id="order-form__articuls" type="text"  name="articuls" ></textarea>
            </div>
        </div>
        <div class="row">
            <div class="five  columns">
                <label ><input type="checkbox" name="strong_search" value="1"/> строгий поиск</label>
            </div>
            <div class="five  columns">
                <label ><input type="checkbox" name="group_art"  value="1"/> группировать артикулы</label>
            </div>
        </div>
        <div class="row">
            <div class="ten  columns">
                <input type="submit" name="findid" value="Найти" class="button button-second"/>
            </div>
        </div>

    </form>
    <form method="post" action="">
        <input type="hidden" name="action" value="find_ids"/>
        <div class="row">
            <div class="ten  columns">
                <label for="order-form__goodIds" class="order-form__label--truncate">Список артикулов (с новой строки):</label>
                <textarea  class="u-full-width" cols="10" id="order-form__articuls_good" type="text"  name="articuls_goods" ></textarea>
            </div>
        </div>
        <div class="row">
            <div class="ten  columns">
            <input type="submit" name="findid" value="Найти" class="button button-second"/>
        </div>

    </form>
    <form method="post" action="">
        <input type="hidden" name="action" value="prepare"/>

    <div class="row">
        <div class="ten  columns">
            <label for="order-form__goodIds" class="order-form__label--truncate">Список ID товаров (с новой строки):</label>
            <textarea  class="u-full-width" cols="10" id="order-form__goodIds" type="text"  name="goodIds" ><?if($_SESSION['find_ids']){ echo implode( "\r\n", array_keys($_SESSION['find_ids']));unset($_SESSION['find_ids']);}?></textarea>
        </div>
    </div>

        <div class="row">
            <hr><h6>Акции</h6><br>
        </div>
    <div class="row">
        <div class="five columns">
            <label for="order-form__add" class="order-form__label--truncate">Прикрепить к акции:</label>
            <select class="u-full-width" id="order-form__add" type="text"  name="form__add" >
                <option value="">не выбрано</option>
                <?foreach($exists_actions AS $k=>$act){?>
                <option value="<?=$k?>"><?=$act['CODE']?> (<?=$act['CNT'] ?>)</option>
                <?}?>
            </select>
            <br>
            <input type="submit" name="mark" value="Прикрепить" class="button button-primary"/>

            <input type="submit" name="showbylabel" value="Показать список ID" class="button button-second"/>
        </div>
        <div class="five columns">
            <label for="order-form__del" class="order-form__label--truncate">Открепить от акции:</label>
            <select class="u-full-width" id="order-form__del" type="text"  name="form__del" >
                <option value="">не выбрано</option>
                <?foreach($exists_actions AS $k=>$act){?>
                <option value="<?=$act['CODE']?>"><?=$act['CODE']?> (<?=($act['CNT'] > 0 ? $act['CNT'] : 0)?>)</option>
                <?}?>
            </select>
            <br>
            <input type="submit" name="unmark" value="Открепить" class="button button-second"/>
        </div>
    </div>
        <div class="row">
            <hr><h6>Метки</h6><br>
        </div>
    <div class="row">
        <div class="five columns">
            <label for="order-form__addlabel" class="order-form__label--truncate">Прикрепить к метке:</label>
            <select class="u-full-width" id="order-form__addlabel" type="text"  name="form__addlabel" >
                <option value="">не выбрано</option>
                <?foreach($exists_offers AS $k=>$act){?>
                    <option value="<?=$act['ID']?>"><?=$act['CODE']?> (<?=($act['CNT'] > 0 ? $act['CNT'] : 0)?>)</option>
                <?}?>
            </select>
            <br>
            <input type="submit" name="setlabel" value="Прикрепить" class="button button-primary"/>

            <input type="submit" name="showbylabel" value="Показать список ID" class="button button-second"/>
        </div>
        <div class="five columns">
            <label for="order-form__dellabel" class="order-form__label--truncate">Открепить от метки:</label>
            <select class="u-full-width" id="order-form__dellabel" type="text"  name="form__dellabel" >
                <option value="">не выбрано</option>
                <?foreach($exists_offers AS $k=>$act){?>
                    <option value="<?=$act['ID']?>"><?=$act['CODE']?> (<?=($act['CNT'] > 0 ? $act['CNT'] : 0)?>)</option>
                <?}?>
            </select>
            <br>
            <input type="submit" name="unsetlabel" value="Открепить" class="button button-second"/>
        </div>
    </div>

    </form>
</div>
<div class="container">
    <div class="row">
        <br><br><hr><br><br>
    </div>
    <form method="post" action="" onsubmit="return confirm('Создать метку?');">
        <div class="row">
            <div class="three  columns">
                Создать метку акции:
            </div>

            <div class="four  columns">
                <input type="text" name="new_act" id="new_act" value="" />
            </div>
            <div class="three columns">
                <input type="hidden" name="action" value="create"/>
                <input type="submit" name="addact" value="Создать" class="button button-primary"/>
            </div>
        </div>
    </form>
</div>
<?
if ($_REQUEST['action'] == 'create')
{
    $act_name = trim($_REQUEST['new_act']);

    if ($act_name != '')
    {

        $act_name = str_replace(" ", "-", $act_name);
        $act_name_id = str_replace("_", "-", $act_name);

        $act_name = strtolower(str_replace("--", "-", $act_name));

        $ibpenum = new CIBlockPropertyEnum;
        if($PropID = $ibpenum->Add(Array('PROPERTY_ID'=>$PROPERTY_ID, 'VALUE'=>$act_name, 'XML_ID' =>$act_name_id))){
            $_SESSION['tools']['alert'] = 'Метка создана (ID '.$PropID.' XML_ID '.$act_name_id.' VALUE '.$act_name.')';
        }
        else {
            $_SESSION['tools']['error'] = 'Ошибка создания метки';
        }
        LocalRedirect("/local/tools/marks.php");die;
    }
    else {
        $_SESSION['tools']['error'] = 'Ошибка создания метки. Укажите значение.';
        LocalRedirect("/local/tools/marks.php");die;
    }


}
elseif ($_REQUEST['action'] == 'find')
{
    $start_time = time();
    $arArticuls = explode("\r\n", trim($_REQUEST['articuls']));
    $section =  $_REQUEST['section'];
    if ($_REQUEST['group_art'] == '1')
    {
        $new_arts = [];
        foreach ($arArticuls as $key => $articul) {
            $new = substr($articul, 0, 7);
            $new_arts[$new] = $new;
        }
    }
    if (!empty($new_arts))
    {
        $arArticuls = $new_arts;
    }
    foreach ($arArticuls as $key => $articul) {
        if (empty(trim($articul))) continue;

        if ($_REQUEST['strong_search'] != '1')
            $articul = $articul.'%';


        $filter = ["IBLOCK_ID" => $OFFERS_IBLOCK_ID,  'PROPERTY_CML2_ARTICLE'=>$articul, "ACTIVE"=>"Y" ];
        if ($section != '')
        {
            //$filter['PROPERTY_CML2_LINK_SECTION_ID ']
        }

        $obElm = CIBlockElement::GetList(
            [],
            $filter,
            false, //false,
            false,
            ["ID", "IBLOCK_ID","PROPERTY_CML2_LINK.ID", "PROPERTY_CML2_LINK.NAME", "PROPERTY_CML2_LINK.PROPERTY_ACTION_SIGN"]
        );
        while ($arElm = $obElm->GetNext()) {

            $arIds[$arElm['PROPERTY_CML2_LINK_ID']] = ['ID' => $arElm['PROPERTY_CML2_LINK_ID'], 'NAME' => $arElm['PROPERTY_CML2_LINK_NAME'], 'SEARCH'=>$articul];
        }

    }
    $end_time = time();

    $_SESSION['find_ids'] = $arIds;

    $_SESSION['tools']['alert'] = 'Получены ID: '.count($arIds).'. время '.($end_time-$start_time).'<br/>';
    foreach ($arIds as $key => $value) {
        $_SESSION['tools']['alert'] .= $value['ID']. ' - '.$value['NAME'].' ' .$value['TAG'] .' ' .$value['SEARCH'] .'<br/>';
    }
    LocalRedirect("/local/tools/marks.php");
}
elseif ($_REQUEST['action'] == 'find_ids')
{
    $arArticuls = explode("\r\n", trim($_REQUEST['articuls_goods']));

    foreach ($arArticuls as $key => $articul) {


        $obElm = CIBlockElement::GetList(
            ["ID"=>"ASC"],
            ["IBLOCK_ID" => $IBLOCK_ID,  '=PROPERTY_CML2_ARTICLE'=>$articul , "ACTIVE"=>"Y"],

            false, //false,
            false,
            ["ID", "NAME"]
        );
        if ($arElm = $obElm->GetNext()) {

            $arIds[$arElm['ID']] = ['ID' => $arElm['ID'], 'NAME' => $arElm['NAME']];
        }

    }


    $_SESSION['find_ids'] = $arIds;

    $_SESSION['tools']['alert'] = 'Получены ID: '.count($arIds).'<br/>';
    foreach ($arIds as $key => $value) {
        $_SESSION['tools']['alert'] .= $value['ID']. ' - '.$value['NAME'].'<br/>';
    }
    LocalRedirect("/local/tools/marks.php");
}
elseif ($_REQUEST['action'] == 'prepare')
{
    $arElements = explode("\r\n", trim($_REQUEST['goodIds']));
 
    if ($_REQUEST['mark'] && !empty($arElements[0]) && !empty($_REQUEST['form__add']))
    {
        $obElm = CIBlockElement::GetList(
            ["ID"=>"ASC"],
            ["IBLOCK_ID" => $IBLOCK_ID,  'ID'=>$arElements],

            false, //false,
            ['nTopCount'=>1000],
            ["ID", "IBLOCK_ID", "NAME", "PROPERTY_ACTION_SIGN_VALUE"]
        );
        $counter = 0;
        while ($arElm = $obElm->GetNext()) {

            $actions = [];
            $actions[] = $_REQUEST['form__add'];

            if (!empty($arElm['PROPERTY_ACTION_SIGN_VALUE_VALUE']))
            {
                foreach ($arElm['PROPERTY_ACTION_SIGN_VALUE_VALUE'] as $key => $value) {
                    if (!in_array($value, $actions) && $value != '')
                    {
                        $actions[] = $key;
                    }
                }
            }
            if (!empty($actions))
            {
                $counter++;
                $arUpdate = ['ACTION_SIGN' =>  $actions];
            }
            else {
                $arUpdate = ['ACTION_SIGN' =>  ''];
            }
            CIBlockElement::SetPropertyValuesEx($arElm['ID'], $IBLOCK_ID, $arUpdate);
        }
        $_SESSION['tools']['alert'] = 'Привязано товаров: '.$counter.'';

        LocalRedirect("/local/tools/marks.php");die;
    }
    elseif (isset($_REQUEST['unmark']))
    {
        if ($_REQUEST['unmark'] &&  !empty($_REQUEST['form__del']))
        {
            $new_act = $_REQUEST['form__del'];

            if (!empty($arElements[0]))
            {
                $obElm = CIBlockElement::GetList(
                    ["ID"=>"ASC"],
                    ["IBLOCK_ID" => $IBLOCK_ID,  'ID'=>$arElements ],

                    false, //false,
                    ['nTopCount'=>1000],
                    ["ID", "IBLOCK_ID", "NAME", "PROPERTY_ACTION_SIGN_VALUE"]
                );
                $counter = 0;

                while ($arElm = $obElm->GetNext()) {

                    $actions = [];

                    if (!empty($arElm['PROPERTY_ACTION_SIGN_VALUE_VALUE']))
                    {
                        foreach ($arElm['PROPERTY_ACTION_SIGN_VALUE_VALUE'] as $key => $value) {
                            if (!in_array($value, $actions) && $value != $new_act && $value != '')
                            {
                                $actions[] = $key;
                            }
                        }
                    }
                    if (!empty($actions))
                    {
                        $arUpdate = ['ACTION_SIGN' =>  $actions];
                    }
                    else {
                        $arUpdate = ['ACTION_SIGN' =>  ''];
                    }
                    $counter++;
                    CIBlockElement::SetPropertyValuesEx($arElm['ID'], $IBLOCK_ID, $arUpdate);
                }
                $_SESSION['tools']['alert'] = 'Отвязано товаров: '.$counter.'';
            }
            else
            {
                $actions = [$new_act];
                $obElm = CIBlockElement::GetList(
                    ["ID"=>"ASC"],
                    ["IBLOCK_ID" => $IBLOCK_ID,  "PROPERTY_ACTION_SIGN_VALUE"=>$new_act ],

                    false, false,
                    //['nTopCount'=>10],
                    ["ID", "IBLOCK_ID", "NAME", "PROPERTY_ACTION_SIGN_VALUE"]
                );
                $counter = 0;
                while ($arElm = $obElm->GetNext()) {

                    if (!empty($arElm['PROPERTY_ACTION_SIGN_VALUE_VALUE']))
                    {
                        $addact= [];
                        foreach ($arElm['PROPERTY_ACTION_SIGN_VALUE_VALUE'] as $key => $value) {

                            if (!in_array($value, $actions))
                            {
                                $addact[] = $value;
                            }
                        }
                        if (!empty($addact))
                            $arUpdate = ['ACTION_SIGN' =>  $addact];
                        else {
                            $arUpdate = ['ACTION_SIGN' =>  ''];
                        }
                        CIBlockElement::SetPropertyValuesEx($arElm['ID'], $IBLOCK_ID, $arUpdate);
                        $counter++;
                    }
                }

                $_SESSION['tools']['alert'] = 'Отвязано товаров: '.$counter.'';
            }

            LocalRedirect("/local/tools/marks.php");
        }
        else{


            $_SESSION['tools']['error'] = 'Невыбрана метка.';
            LocalRedirect("/local/tools/marks.php");
        }
    }
    elseif ($_REQUEST['showbylabel'] && (!empty($_REQUEST['form__addlabel']) || !empty($_REQUEST['form__add'])))
    {
        if (!empty($_REQUEST['form__addlabel'])) {
            $label = $_REQUEST['form__addlabel'];
            $prop = "OFFERS";
        }
        elseif (!empty($_REQUEST['form__add'])) {
            $label = $_REQUEST['form__add'];
            $prop = "ACTION_SIGN";
        }
        else {

            LocalRedirect("/local/tools/marks.php");
        }
        $arIds = [];
        $obOffElm = CIBlockElement::GetList(
            ["ID"=>"ASC"],
            ["IBLOCK_ID" => $IBLOCK_ID, "ACTIVE"=>"Y", "PROPERTY_".$prop=>$label],
            false,
            false,
            ["ID", "NAME"]
        );
        while ($arElm = $obOffElm->GetNext()) {

            $arIds[$arElm['ID']] = ['ID' => $arElm['ID'], 'NAME' => $arElm['NAME']];
        }

        $_SESSION['find_ids'] = $arIds;

        $_SESSION['tools']['alert'] = 'Получены ID: '.count($arIds).'<br/>';
        foreach ($arIds as $key => $value) {
            $_SESSION['tools']['alert'] .= $value['ID']. ' - '.$value['NAME'].'<br/>';
        }
        LocalRedirect("/local/tools/marks.php");


    }
    elseif ($_REQUEST['setlabel'] && !empty($_REQUEST['form__addlabel']))
    {
        $arElements = explode("\r\n", trim($_REQUEST['goodIds']));

        if (!empty($arElements[0]) && !empty($_REQUEST['form__addlabel'])){

            $obElm = CIBlockElement::GetList(
                ["ID"=>"ASC"],
                ["IBLOCK_ID" => $IBLOCK_ID,  'ID'=>$arElements ],

                false, //false,
                ['nTopCount'=>1000],
                ["ID", "IBLOCK_ID", "NAME", "PROPERTY_OFFERS_VALUE"]
            );
            $counter = 0;
            while ($arElm = $obElm->GetNext()) {

                $offers = [];
                $offers[] = $_REQUEST['form__addlabel'];

                if (!empty($arElm['PROPERTY_OFFERS_VALUE_VALUE']))
                {
                    foreach ($arElm['PROPERTY_OFFERS_VALUE_VALUE'] as $key => $value) {
                        if (!in_array($value, $offers) && $value != '')
                        {
                            $offers[] = $key;
                        }
                    }
                }
                if (!empty($offers))
                {
                    $counter++;
                    $arUpdate = ['OFFERS' =>  $offers];
                }
                else {
                    $arUpdate = ['OFFERS' =>  ''];
                }
                CIBlockElement::SetPropertyValuesEx($arElm['ID'], $IBLOCK_ID, $arUpdate);
            }
            $_SESSION['tools']['alert'] = 'Привязано товаров: '.$counter.'';

            LocalRedirect("/local/tools/marks.php");die;

        }

    }
    elseif ($_REQUEST['unsetlabel'] && !empty($_REQUEST['form__dellabel']))
    {

        $arElements = explode("\r\n", trim($_REQUEST['goodIds']));

        $new_act = $_REQUEST['form__dellabel'];

        if (!empty($arElements[0]))
        {
            $obElm = CIBlockElement::GetList(
                ["ID"=>"ASC"],
                ["IBLOCK_ID" => $IBLOCK_ID,  'ID'=>$arElements ],

                false, //false,
                ['nTopCount'=>1000],
                ["ID", "IBLOCK_ID", "NAME", "PROPERTY_OFFERS_VALUE"]
            );
            $counter = 0;

            while ($arElm = $obElm->GetNext()) {

                $actions = [];

                if (!empty($arElm['PROPERTY_OFFERS_VALUE_VALUE']))
                {
                    foreach ($arElm['PROPERTY_OFFERS_VALUE_VALUE'] as $key => $value) {
                        if (!in_array($value, $actions) && $key != $new_act && $key != '')
                        {
                            $actions[] = $key;
                        }
                    }
                }
                if (!empty($actions))
                {
                    $arUpdate = ['OFFERS' =>  $actions];
                }
                else {
                    $arUpdate = ['OFFERS' =>  ''];
                }
                $counter++;
                CIBlockElement::SetPropertyValuesEx($arElm['ID'], $IBLOCK_ID, $arUpdate);
            }
            $_SESSION['tools']['alert'] = 'Отвязано товаров: '.$counter.'';
        }
        else
        {
            $actions = [$new_act];
            $obElm = CIBlockElement::GetList(
                ["ID"=>"ASC"],
                ["IBLOCK_ID" => $IBLOCK_ID,  "PROPERTY_OFFERS"=>$new_act ],

                false, false,
                //['nTopCount'=>10],
                ["ID", "IBLOCK_ID", "NAME", "PROPERTY_OFFERS_VALUE"]
            );
            $counter = 0;
            while ($arElm = $obElm->GetNext()) {

                if (!empty($arElm['PROPERTY_OFFERS_VALUE_VALUE']))
                {
                    $addact= [];
                    foreach ($arElm['PROPERTY_OFFERS_VALUE_VALUE'] as $key => $value) {

                        if (!in_array($key, $actions))
                        {
                            $addact[] = $key;
                        }
                    }
                    if (!empty($addact))
                        $arUpdate = ['OFFERS' =>  $addact];
                    else {
                        $arUpdate = ['OFFERS' =>  ''];
                    }
                    CIBlockElement::SetPropertyValuesEx($arElm['ID'], $IBLOCK_ID, $arUpdate);
                    $counter++;
                }
            }

            $_SESSION['tools']['alert'] = 'Отвязано товаров: '.$counter.'';
        }

       LocalRedirect("/local/tools/marks.php");
    }

    die();
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
