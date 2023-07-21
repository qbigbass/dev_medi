<div class="lk-sidebar flex col-12 col-lg-3">
    <?
    if (!empty($cards['data'])):

        foreach ($cards['data'] as $ci => $card):

            if(strlen($card['number']) == 13){

                $card_num = substr($card['number'], 0, 2);
                $card_status = '';
                $status_name = '';
                if ($card_num == '20'){
                    $status_discount = "5%";
                    $status_name = '';
                }elseif ($card_num == '99'){
                    $status_discount = "10%";
                    $status_name = '';
                }elseif ($card_num == '90'){
                    $status_discount = "10%";
                    $status_class = "stat3";
                    $status_name = 'Золото';
                }elseif ($card_num == '80'){
                    $status_discount = "5%";
                    $status_class = "stat1";
                }?>
    <div class="user-card col-12 col-md-6 col-lg-12">

        <div class="user-card-title ff-medium">МОЯ <?if($card['cardCategory']['logicalName'] == 'VirtualCard'){?>ВИРТУАЛЬНАЯ<?}?> КАРТА
            <?if ($status[$ci]['currentStatus'] != ''){
                switch($status[$ci]['currentStatus']['name']){
                    case "Новый":
                        $status_class = "stat0";
                        $next = intval($status[$ci]['currentStatus']['threshold'] -$card[$ci]['currentValue']);
                        $status_discount = "0%";
                        $status_name = 'Новый';
                        break;
                    case "Бронза":
                        $status_class = "stat1";
                        $next = intval($status[$ci]['currentStatus']['threshold'] -$card[$ci]['currentValue']);
                        $status_discount = "5%";

                        $status_name = 'Бронза';
                        break;
                    case "Серебро":
                        $status_class = "stat2";
                        $status_discount = "7%";
                        $status_name = 'Серебро';
                        $next = intval($status[$ci]['currentStatus']['threshold'] -$status[$ci]['currentValue']);
                    break;
                    case "Золото";
                        $status_discount = "10%";
                        $status_class = "stat3";
                        $status_name = 'Золото';
                    break;

                    case "VIP-серебро":
                        $status_class = "stat2v";
                    break;
                    case "VIP-золото";
                        $status_class = "stat3v";
                    break;
                    default:
                        $status_class = "stat1";

                }
                ?>

            <?}?>
        </div>
<?if (($card_num == '80' || $card_num == '90') ){?>
    <span class="card_status"><?if ($status_name != ''){?>Статус: <span class="<?=$status_class?>" title="<?=($next > 0 ? 'До следующего статуса надо накопить - '.$next : '');?>">
    <?=($status[$ci]['currentStatus']['name'] == '' ? $status_name : $status[$ci]['currentStatus']['name'])?></span>
    <?}}?>Скидка: <span class="discount"><?=$status_discount?></span></span>
        <?/*<div class="balance flex row">
            <div class="title">БАЛАНС БОНУСОВ</div>
            <div class="content ff-bold medi-color"><?=floor($balance['data'][0]['balance'])?></div>
        </div>
        <div class="bonus flex row">
            <div class="title">ОЖИДАЮТ&nbsp;АКТИВАЦИИ</div>
            <div class="content ff-bold"><?=floor($balance['data'][0]['notActivated'])?></div>
        </div>
        <?if (!empty($detail_balance['data']['items'][0]['lifeTimesByTime'])){
            foreach($detail_balance['data']['items'][0]['lifeTimesByTime'] AS $lifePeriod){
                if ($lifePeriod['amount'] != 0){?>
                <div class="bonus flex row">
                    <div class="title">СГОРЯТ <?= FormatDate("X", strtotime($lifePeriod['date']));?></div>
                        <div class="content ff-bold"><?=floor($lifePeriod['amount'])?>

                        </div>
                </div>
                <?}?>
        <?}
    }?>*/?>
        <div class="barcode flex row">
            <?$_SESSION['barcode']=$card['number'];?>
            <div class="title"><img src="/local/barcode/barcode.php" alt="<?= $card['barCode']?>" title="<?= $card['barCode']?>" class="barcode_img"></div>
            <div class="content ff-medium"><nobr><?=$card['number']?></nobr></div>
        </div>

        <?/*<p>Бонусами можно оплатить до&nbsp;30% от&nbsp;суммы чека. Вы&nbsp;можете воспользоваться бонусами, сообщив о&nbsp;вашем намерении оператору при&nbsp;подтверждении заказа.</p>*/?>

    </div>
    <?}?>
        <?endforeach;
    endif;?>
    <div class="tabs-wrap col-12 col-md-6 col-lg-12">
        <div class="tabs-links">
            <div class="tab-link flex <?if ($active_tab == 'main' || !$active_tab){?>active<?}?>">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 79.18 114.1">
                  <path class="a" d="M39.8,59.7c19.5,0,35.3,22.7,35.3,49.9H4.5c0-16.9,6.1-31.9,15.5-40.7" style="fill: none;stroke-miterlimit: 10;stroke-width: 9px"/>
                  <circle class="a" cx="39.8" cy="32.1" r="27.6" style="fill: none;stroke-miterlimit: 10;stroke-width: 9px"/>
                </svg>
                <div class="title col-9">ЛИЧНЫЙ КАБИНЕТ</div>
                <svg class="search" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 81.67 142.13"><defs><style>.a{fill:none;stroke-miterlimit:10;stroke-width:15px;}</style></defs><polyline class="a" points="5.3 5.3 71.06 71.06 5.3 136.82"/></svg>
            </div>

            <div class="tab-link flex <?if ($active_tab == 'orders'){?>active<?}?>">
                <svg version="1.1" class="icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                	 viewBox="0 0 128.6 115.2" style="enable-background:new 0 0 128.6 115.2;" xml:space="preserve">
                <path class="d" d="M115.3,76l-1.9,7.7H22.6c-2.1-25.3-4.2-50.6-6.3-76.1H0V0h23.6c2.1,25.2,4.2,50.5,6.3,76L115.3,76z"/>
                <path class="d" d="M92.5,44.8H63.7V27.1h28.8V44.8z"/>
                <path class="d" d="M101.7,91.3c6.6,0,12,5.3,12.1,11.9c-0.1,6.6-5.4,11.9-12,12c-6.6,0-12-5.3-12.1-11.9
                	C89.7,96.7,95.1,91.3,101.7,91.3z"/>
                <path class="d" d="M49.8,103.2c0,6.6-5.2,11.9-11.8,12c0,0,0,0-0.1,0c-6.5,0.1-11.9-5.2-12-11.7c0-0.1,0-0.1,0-0.2
                	c-0.1-6.5,5-11.9,11.5-12c0.1,0,0.2,0,0.3,0c6.5-0.1,11.9,5,12,11.6C49.8,103,49.8,103.1,49.8,103.2z"/>
                <path class="d" d="M92.5,18.9H63.6V3.8h28.8L92.5,18.9z"/>
                <path class="d" d="M92.6,53v15H63.7V53H92.6z"/>
                <path class="d" d="M100.4,19V3.8h28.2c-1,5.1-2,10.1-3,15.1H100.4z"/>
                <path class="d" d="M32,27.1h23.5v17.8H33.8C33.2,38.9,32.6,33.1,32,27.1z"/>
                <path class="d" d="M124.1,27.1h-23.6v17.9h19.1c0.9-0.2,1.7-1,1.8-1.9C121.8,41.3,124.1,27.1,124.1,27.1z"/>
                <path class="d" d="M31.3,18.9c-0.5-5.1-1-10-1.5-15.1h25.8v15.1H31.3z"/>
                <path class="d" d="M36.1,68c-0.5-5-1-9.9-1.5-15h21v15H36.1z"/>
                <path class="d" d="M119,53h-18.6v15.3h15c0.8-0.2,1.4-0.9,1.4-1.6C117.2,65.2,119,53,119,53z"/>
                </svg>
                <div class="title col-9"><?/*<a href="/lk/orders/">*/?>СТАТУС ЗАКАЗА<?/*</a><?*/?></div>
                <svg class="search" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 81.67 142.13"><defs><style>.a{fill:none;stroke-miterlimit:10;stroke-width:15px;}</style></defs><polyline class="a" points="5.3 5.3 71.06 71.06 5.3 136.82"/></svg>
            </div>
            <div class="tab-link flex <?if ($active_tab == 'history'){?>active<?}?>">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 408.97 405.08">
                    <defs><style>.c{fill:none;stroke-miterlimit:10;stroke-width:40px;}</style></defs>
                    <path class="c" d="M497,45.84A182.59,182.59,0,0,1,317.19,196.75c-100.81,0-182.54-81.73-182.54-182.54s81.73-182.54,182.54-182.54a182.16,182.16,0,0,1,139.49,64.79" transform="translate(-114.65 188.33)"/><polygon class="b" points="408.97 33.46 261.25 117.76 377.24 149.45 408.97 33.46"/>
                    <rect class="b" x="153.67" y="125.83" width="49" height="125"/><rect class="b" x="306.33" y="-24.5" width="49" height="125" transform="translate(254.17 -104.49) rotate(90)"/>
                </svg>
                <div class="title col-9">ИСТОРИЯ ПОКУПОК</div>
                <svg class="search" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 81.67 142.13"><defs><style>.a{fill:none;stroke-miterlimit:10;stroke-width:15px;}</style></defs><polyline class="a" points="5.3 5.3 71.06 71.06 5.3 136.82"/></svg>
            </div>

            <div class="tab-link flex  <?if ($active_tab == 'letter'){?>active<?}?>">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 608.29 481.52">
                    <path class="b" d="M538.08,258.47c-55.89-3.8-107.22-16.19-150.48-50.89-13.34-10.71-27.06-12-42.92-10.28Q254,207.27,174.57,162c-30.4-17.35-58.3-37.51-78.86-66.4C72.25,62.67,61.06,25.94,64.45-14.46,68.43-62,89.73-101.28,124.89-133c37.8-34.09,82.61-54.24,132.15-64.41a310.6,310.6,0,0,1,92.95-5c67.79,6.59,127.9,30.65,176.19,80.05C553.1-94.78,570.51-62,575.58-23.51,581.73,23.12,569,64.85,540.64,102c-11.92,15.61-26.13,28.87-41.85,40.6-5.47,4.08-10.84,8.31-16,12.74-6.21,5.29-8.1,12-5.42,19.8,10.38,30.38,24.49,58,53.49,75.23C533.47,252,535.16,255.13,538.08,258.47ZM317.83,42.84c24-.07,44.34-20.46,44.3-44.37,0-22.9-20.91-44.25-43.12-44.14-24,.13-44.26,20.77-44.23,45.1C274.81,22.18,295.35,42.9,317.83,42.84ZM496.23-1.23c.1-23.44-20.44-44.29-43.72-44.39-22-.1-43.06,21.25-43.41,44-.36,23.87,19.61,44.18,43.5,44.23A43.62,43.62,0,0,0,496.23-1.23Zm-312,43.79c23.71,0,43.43-19.81,43.46-43.72,0-24.15-19.79-44.22-43.62-44.18-23.6,0-43.26,20.07-43.19,44S160.56,42.53,184.21,42.56Z" transform="translate(31.19 203.83)"/>
                    <path class="b" d="M37-67.88c-1.57,9.19-2.67,17.54-4.45,25.75C17.18,28.32,38.18,88.24,85.39,140.51c32,35.46,71.66,59.69,115.68,77.27,5.07,2,10.16,4,16.68,6.54-2.76.19-4.1.35-5.44.36q-29.73.27-59.46.5A27.63,27.63,0,0,0,136.22,231c-28.15,20.61-58.88,35.66-93.5,41.71-12.06,2.11-24.26,3.38-36.4,5l-1-1.26c2.33-2.78,4.2-6.23,7.09-8.22,22.4-15.4,33.8-38,41.33-63,2.7-9-.24-15.19-9.07-21.24A190.77,190.77,0,0,1,8,151.76C-13.92,126.55-27.9,97.68-30.69,64.19c-2.93-35.12,7-66.81,27.63-95.25C6.5-44.26,17.66-55.84,31.46-64.69,33-65.65,34.55-66.49,37-67.88Z" transform="translate(31.19 203.83)"/>
                </svg>
                <div class="title col-9">НАПИСАТЬ ДИРЕКТОРУ</div>
                <svg class="search" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 81.67 142.13"><defs><style>.a{fill:none;stroke-miterlimit:10;stroke-width:15px;}</style></defs><polyline class="a" points="5.3 5.3 71.06 71.06 5.3 136.82"/></svg>
            </div>
        </div>
    </div>
</div>
