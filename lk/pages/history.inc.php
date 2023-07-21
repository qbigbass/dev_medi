<?
$date_year  = date("Y-m-d\T00:00:00\Z", time()-86400*365);
$today = date("Y-m-4\T00:00:00\Z");
$hfilter['filter'] = [/*'fromDate' => $date_year, 'toDate' => $today  'from'=>8,*/ 'count' => '20'];
$history = $api-> userHistory($hfilter);
if ($_REQUEST['ff']) {echo"<pre>";print_r($history);echo "</pre>";}
if ($history['status'] == 'ok')
{
    $orderCount = $history['data']['data']['allCount'];
    if (!empty($history['data']['data']['rows'])){
        foreach($history['data']['data']['rows'] AS $k=>$row){
            if($row['type'] == 'PurchaseData'){
                $history_rows[] = $row;
                //__($row);
            }
        }
    }
}
//__($history);
?>
<div class="h2 ff-medium title">История покупок</div>
<br>

<!--	          Для экранов 1240px и крупнее		      -->
<div class="lg-dev">

    <!--		Для каждого заказа			-->
    <?if (!empty($history_rows)){?>
        <div class="flex title ff-medium">
            <div class="col-lg-2">Дата</div>
            <div class="col-lg-6">Наименование</div>
            <div class="col-lg-2">Скидка</div>
            <?/*<div class="col-lg-1">Списано бонусов</div>*/?>
            <div class="col-lg-2">Вы оплатили</div>
            <?/*<div class="col-lg-1">Начислено бонусов</div>*/?>
        </div>
        <?

        foreach($history_rows AS $k=>$row){
//__($row);
            ?>
            <div class="row flex order">

                <div class="col-lg-12 order_list flex">
                    <!--		Для каждого товара в заказе			-->
                    <?
                    $discount = 0;
                    $amount = 0;
                    $rewards = 0;
                    $bonus = 0;
                    $row_count = 0;
                    ?>
                    <?if ($row['data']['chequeItems']){


                        $countItems = count($row['data']['chequeItems']);
                        foreach($row['data']['chequeItems'] as $i => $arItem){

                            if ($arItem['description'])
                            {
                                $good = [];
                                $obElm = CIBlockElement::GetList([], ['IBLOCK_ID'=>19, 'PROPERTY_CML2_ARTICLE'=>$arItem['description']], false, false, ['ID', 'IBLOCK_ID', 'NAME', 'ACTIVE', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL', 'PROPERTY_CML2_LINK.DETAIL_PICTURE']);
                                if ($arElm = $obElm->GetNext())
                                {
                                    $picture  =  [];
                                    if ($arElm['DETAIL_PICTURE']){
                                        $picture = CFile::ResizeImageGet($arElm["DETAIL_PICTURE"], array("width" => 90, "height" => 90), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 100);
                                    }
                                    elseif ($arElm['PROPERTY_CML2_LINK_DETAIL_PICTURE'])
                                    {
                                        $picture = CFile::ResizeImageGet($arElm["PROPERTY_CML2_LINK_DETAIL_PICTURE"], array("width" => 90, "height" => 90), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 100);
                                    }
                                    $arElm['PICT'] = $picture;
                                    $good = $arElm;
                                }
                                else{

                                    $obMainElm = CIBlockElement::GetList([], ['IBLOCK_ID'=>17, 'PROPERTY_CML2_ARTICLE'=>$arItem['description']], false, false, ['ID', 'IBLOCK_ID', 'NAME', 'ACTIVE', 'DETAIL_PICTURE', 'PREVIEW_PICTURE', 'DETAIL_PAGE_URL']);
                                    if ($arMainElm = $obMainElm->GetNExt())
                                    {
                                        $picture  =  [];

                                        if ($arMainElm['DETAIL_PICTURE']){
                                            $picture = CFile::ResizeImageGet($arMainElm["DETAIL_PICTURE"], array("width" => 90, "height" => 90), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 100);
                                        }
                                        elseif ($arMainElm['PREVIEW_PICTURE']){
                                            $picture = CFile::ResizeImageGet($arMainElm["PREVIEW_PICTURE"], array("width" => 90, "height" => 90), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 100);
                                        }
                                        $arMainElm['PICT'] = $picture;
                                        $good = $arMainElm;
                                    }

                                    else {

                                        // Поиск по всем наименованиям
                                        $query2 = 'SELECT * FROM `b_mrnomenklatura` WHERE UF_NAME = "'.$arItem['description'].'" ';
                                        $obRes = $DB->Query($query2);
                                        if ($arHLgood = $obRes->Fetch()) {
                                            $good = [
                                                'NAME' => $arHLgood['UF_DESCRIPTION'],
                                                'DETAIL_PAGE_URL' => '',
                                                'ACTIVE' => 'N'
                                            ];
                                        }
                                        else{

                                            // Товар не найден в бд сайта
                                            $good = [
                                                'NAME' => $arItem['description'],
                                                'DETAIL_PAGE_URL' => '',
                                                'ACTIVE' => 'N'
                                            ];
                                        }
                                    }
                                }
                            }
                            ?>
                            <div class="col-lg-2 row"><?=($i == 0 ? date("d.m.Y H:i", strtotime($row['dateTime'])-10800) : '&nbsp;')?></div>

                            <div class="col-lg-10 item">
                                <div class="flex offer <?=($countItems > 1 ? 'bb' : '')?>">
                                    <div class="col-2">
                                        <?=($row['data']['isRefund'] ? '<b>Возврат</b>' : '');?>
                                        <?if ($good['PICT']['src']){?>
                                            <img class="offer_pict" src="<?=$good['PICT']['src']?>" title ="<?=$good['NAME']?>">
                                        <?}
                                        else{?>
                                            <img class="offer_pict_empty" src="/bitrix/templates/dresscodeV2/images/empty.png" title ="<?=$good['NAME']?>">
                                        <?}?>
                                    </div>
                                    <div class="col-5 offer_title">
                                        <?if ($good['DETAIL_PAGE_URL'] != '' && $good['ACTIVE'] == 'Y'){?>
                                            <a target="_blank" class="theme-link-dashed" href="<?=$good['DETAIL_PAGE_URL']?>"><?=$good['NAME']?></a>
                                        <?} else {?>
                                            <span><?=$good['NAME']?></span>
                                        <?}?>
                                    </div>
                                    <?//Скидка ?>
                                    <div class="col-3 medi-color">
                                        <?if (intval($row['data']['rewards'][0]['positionInfo'][$i]['value']) > 0){
                                            if ($row['data']['rewards'][0]['rewardType'] == 'Discount')
                                            {
                                                $discount += $row['data']['rewards'][0]['positionInfo'][$i]['value'];
                                                ?>-&nbsp;<?=number_format($row['data']['rewards'][0]['positionInfo'][$i]['value'], 0, '.', ' ').'&nbsp;'.$row['data']['rewards'][0]['amount']['currency']?><br/>
                                                <?
                                            }
                                        }
                                        else{
                                            ?>-<?
                                        }?>
                                    </div>

                                    <div class="col-2">
                                        <?if ($row['data']['chequeItems'][$i]['amount'] != 0){
                                            $amount =+ $row['data']['chequeItems'][$i]['amount'];
                                            ?><?=number_format($row['data']['chequeItems'][$i]['amount'], 0, '.', ' ')?>&nbsp;руб.<br/>
                                            <?
                                        }
                                        else{
                                            ?>-<?
                                        }?>
                                    </div>
                                </div>
                            </div>
                            <?
                            $row_count++;
                        }  // END foreach($row['data']['chequeItems']
                    }  // END IF $row['data']['chequeItems']
                    ?>
                </div>
                <?if ($row_count > 1){?>
                    <div class="col-lg-12 ff-medium total">
                        <div class="flex summary ff-medium">
                            <div class="col-lg-7"></div>
                            <div class="col-lg-1 text-right">Итого:</div>
                            <div class="col-lg-2 medi-color ">-&nbsp;<?=number_format($discount, 0, '.', ' ')?>&nbsp;руб.</div>
                            <div class="col-lg-2"><?=number_format($row['data']['amount']['amount'], 0, '.', ' ')?>&nbsp;руб.</div>
                        </div>
                    </div>
                <?}?>
            </div>
            <?//} // endif type PurchaseData
        }  // END foreach $history_rows?>
    <?}
    else{?>
        <div class="row  order text-center ff-medium"><Br><br>Информации о покупках пока нет. Посмотрите  <a href="/catalog/" class="medi-color">каталог товаров</a> и сделайте ваш первый заказ.<br><br><br></div>
    <?}?>
</div>

<!--		Для экранов менее 1240px		-->
<div class="small-dev">

    <?
    if (!empty($history_rows)){
        ?>
        <?

        foreach($history_rows AS $k=>$row){
            if($row['type'] == 'PurchaseData'){?>
                <div class="order flex row">
                    <!--		Для каждого заказа			-->
                    <div class="col-md-2 col-3 date"><?=date("d.m.Y H:i", strtotime($row['dateTime']))?></div>
                    <div class="col-md-10 col-9 order_list">
                        <!--		Для каждого товара в заказе			-->
                        <?if ($row['data']['chequeItems']){
                            foreach($row['data']['chequeItems'] as $arItem){
                                if ($arItem['description'])
                                {
                                    $good = [];
                                    $obElm = CIBlockElement::GetList([], ['IBLOCK_ID'=>19, 'PROPERTY_CML2_ARTICLE'=>$arItem['description']], false, false, ['ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL', 'PROPERTY_CML2_LINK.DETAIL_PICTURE']);

                                    if ($arElm = $obElm->GetNext())
                                    {
                                        /*$obMainElm = CIBlockElement::GetList([], ['IBLOCK_ID'=>17, 'ID'=>$arElm['PROPERTY_CML2_LINK_VALUE'], false, false, ['ID', 'NAME', 'DETAIL_PICTURE',   'CATALOG_GROUP_1']]);
                                        if ($arMainElm = $obMainElm->GetNExt())
                                        {
                                            $mgood = $arMainElm;
                                        }*/

                                        $picture  =  [];

                                        if ($arElm['DETAIL_PICTURE']){
                                            $picture = CFile::ResizeImageGet($arElm["DETAIL_PICTURE"], array("width" => 90, "height" => 90), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 100);

                                        }
                                        elseif ($arElm['PROPERTY_CML2_LINK_DETAIL_PICTURE'])
                                        {
                                            $picture = CFile::ResizeImageGet($arElm["PROPERTY_CML2_LINK_DETAIL_PICTURE"], array("width" => 90, "height" => 90), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 100);
                                        }
                                        $arElm['PICT'] = $picture;
                                        $good = $arElm;


//                                __($good);

                                    }
                                    else{
                                        $good = [
                                            'NAME' => $arItem['description'],
                                            'DETAIL_PAGE_URL' => ''
                                        ];
                                    }
                                }
                                ?>
                                <div class="item row">
                                    <div class="row flex">
                                        <div class="col-8 col-md-10 ff-medium title">
                                            <?if ($good['DETAIL_PAGE_URL'] != ''){?><a target="_blank" class="theme-link-dashed" href="<?=$good['DETAIL_PAGE_URL']?>"><?=$good['NAME']?></a><?} else {
                                                ?><span><?=$good['NAME']?></span><?
                                            }?>
                                        </div>
                                        <div class="col-4 col-md-2 icon">
                                            <?if ($good['PICT']['src']){?>
                                                <img  style="width:auto;max-height:180px;max-width:150px;" src="<?=$good['PICT']['src']?>" alt="">
                                            <?}?>
                                        </div>
                                    </div>
                                    <div class="row flex price no-gutters">
                                        <div class="col-auto col-lg-4">Стоимость:</div>
                                        <div class="col-auto ff-medium"><?=number_format($arItem['amount'], 0, '.', ' ');?>&nbsp;руб.</div>
                                    </div>
                                </div>

                                <?
                            }
                        }
                        ?>
                        <?/*<div class="row flex price m-bor no-gutters">
                    <div class="col-auto col-lg-4">Бонусов списано:</div>
                    <div class="col-auto"><?if ($row['data']['withdraws']){
						foreach($row['data']['withdraws'] AS $w => $withdraws){
							if (intval($withdraws['amount']['amount']) != 0){
							?><?=number_format($withdraws['amount']['amount'], 0, '.', ' ').' '.$withdraws['amount']['currency']?><br/>
							<?
							}
							else {
								?>0<?
							}
						}
					}?></div>
                </div>*/?>
                        <div class="row flex price no-gutters">
                            <div class="col-auto col-lg-4">Вы оплатили:</div>
                            <div class="col-auto ff-medium"><?=number_format($row['data']['amount']['amount'])?> <?=$row['data']['amount']['currency']?></div>
                        </div>
                        <div class="row flex price no-gutters">
                            <div class="col-auto col-lg-4">Скидка:</div>
                            <div class="col-auto ff-medium medi-color"><?if ($row['data']['rewards']){
                                    foreach($row['data']['rewards'] AS $r => $rewards){
                                        if ($rewards['rewardType'] == 'Discount')
                                        {


                                            if ($rewards['amount']['amount'] != 0){
                                                ?>-&nbsp;<?=number_format($rewards['amount']['amount'], 0, '.', ' ').'&nbsp;'.$rewards['amount']['currency']?><br/>
                                                <?
                                            }
                                        }
                                    }
                                }?></div>
                        </div>

                    </div>
                </div>

            <?} // endif type PurchaseData
        }?>
        <?
    }else{?>
        <div class="row  order text-center ff-medium"><Br><br>Информации о покупках пока нет. Посмотрите  <a href="/catalog/" class="medi-color">каталог товаров</a> и сделайте ваш первый заказ.<br><br><br></div>
    <?}?>
</div>
