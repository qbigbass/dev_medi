<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $nUserID;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Basket;
use Bitrix\Main\Grid\Declension;
/**
 * @var array $arParams
 * @var array $arResult
 * @var $APPLICATION CMain
 */

if ($arParams["SET_TITLE"] == "Y")
{
	$APPLICATION->SetTitle(Loc::getMessage("SOA_ORDER_COMPLETE"));
	$APPLICATION->SetPageProperty("page_title", Loc::getMessage("SOA_ORDER_COMPLETE"));
}
?>

<? if (!empty($arResult["ORDER"])):

	$obBasket = \Bitrix\Sale\Basket::getList(array('filter' => array('ORDER_ID' => $arResult['ORDER']['ID'])));
	$bItems = [];
	$feed_products = [];
	while($bItem = $obBasket->Fetch()){
	     $bItems[] = $bItem;
	}
	if (!empty($bItems)) {
		foreach ($bItems as $key => $arItem) {
			$obElm = CIBlockElement::GetList([], ["ID" => $arItem['PRODUCT_ID']], false, false, ["IBLOCK_ID"] );
			$picture = '';
			if ($arElm = $obElm->GetNext())
			{
				$obElmProp = CIBlockElement::GetList([], ["ID" => $arItem['PRODUCT_ID'], "IBLOCK_ID"=>$arElm['IBLOCK_ID']], false, false, ["ID", "NAME", "PROPERTY_CML2_LINK", "PROPERTY_CML2_LINK.IBLOCK_ID", "PROPERTY_ATT_BRAND.NAME", "PROPERTY_CML2_ARTICLE", 'DETAIL_PAGE_URL', 'DETAIL_PICTURE', 'PROPERTY_CML2_LINK.DETAIL_PICTURE'] );
				if ($arElmProp = $obElmProp->GetNext())
				{
					if ($arElmProp['DETAIL_PICTURE'] > 0)
					{
							$picture = CFile::ResizeImageGet($arElmProp['DETAIL_PICTURE'], array('width'=>90, 'height'=>90), BX_RESIZE_IMAGE_PROPORTIONAL, true);
					}
					elseif ($arElmProp['PROPERTY_CML2_LINK_DETAIL_PICTURE'] > 0)
					{
							$picture = CFile::ResizeImageGet($arElmProp['PROPERTY_CML2_LINK_DETAIL_PICTURE'], array('width'=>90, 'height'=>90), BX_RESIZE_IMAGE_PROPORTIONAL, true);
					}
					$brand = '';
					$article = $arElmProp['PROPERTY_CML2_ARTICLE_VALUE'];
					// sku
					if($arElmProp['PROPERTY_CML2_LINK_VALUE'])
					{
						$obElmBrand = CIBlockElement::GetList([], ["IBLOCK_ID"=>  $arElmProp['PROPERTY_CML2_LINK_IBLOCK_ID'], "ID" => $arElmProp['PROPERTY_CML2_LINK_VALUE']], false, false, ["ID", "NAME", "PROPERTY_ATT_BRAND.NAME"] );

						if ($arElmBrand = $obElmBrand->GetNext()) {
							$brand = $arElmBrand['PROPERTY_ATT_BRAND_NAME'];
							$goodId =  $arItem['PRODUCT_ID'];
			                $goodName = $arElmBrand['NAME'];
						}

					}
					// simple
					elseif ($arElmProp['PROPERTY_ATT_BRAND_NAME']) {

						$brand = $arElmProp['PROPERTY_ATT_BRAND_NAME'];
						$goodId =  $arItem['PRODUCT_ID'];
						$goodName = $arElmProp['NAME'];
					}

				}
			}

			$secturl = explode("/", $arItem['DETAIL_PAGE_URL']);
			$sectcount = count($secturl) - 1;




			switch ($secturl[2]){
				case "kompressionnyy-trikotazh":
					$feed_id = '2';
				break;
				case "ortopedicheskaya-obuv":
					$feed_id = '3';
				break;
				case "odezhda-dlya-sporta":
					$feed_id = '4';
				break;
				default:
				$feed_id = MYTARGET_FEED_ID;
			}
			//reset feed
		    $feed_id = '1';

			$feed_products[$arItem['ID']] = ['id' => $arItem['ID'], 'price'=>$arItem['PRICE'], 'list'=> $feed_id];
            $list_products[$arItem['ID']] = ['id' => $arItem['ID'], 'price'=>$arItem['PRICE'], 'quantity'=> $arItem['q']];


			unset($secturl[$sectcount]); unset($secturl[0]);unset($secturl[1]);

			$arResult['ITEMS'][] = array(
			'id' => $goodId,
			'q' => $arItem["QUANTITY"],
			'price' => $arItem["PRICE"],
			'article' => $article,
			'name' => $goodName,
			'category' => implode("/",$secturl),
			'brand' => $brand,
			'url' => $arItem['DETAIL_PAGE_URL'],
			'picture' => $picture
			);
		}

		if (!empty($arResult['ITEMS']) && (!isset($_COOKIE['CONFIRM_ORDER_'.$arResult['ORDER']['ACCOUNT_NUMBER']]) || $_COOKIE['CONFIRM_ORDER_'.$arResult['ORDER']['ACCOUNT_NUMBER']] != $arResult['ORDER']['ACCOUNT_NUMBER']))
		{

			$val = 0;?>
			<script>
		    window.dataLayer = window.dataLayer || [];
		    dataLayer.push({
		    'ecommerce': {
		      'currencyCode': 'RUB',
		      'purchase': {
		        'actionField': {'id': <?=$arResult['ORDER']['ACCOUNT_NUMBER']?>, 'affiliation': 'Основной заказ', 'coupon': '',
				'revenue': <?=$arResult['ORDER']['PRICE']-$arResult['ORDER']['PRICE_DELIVERY']?>,
				'shipping':<?=$arResult['ORDER']['PRICE_DELIVERY']?>},
		        'products': [
		<?foreach ($arResult['ITEMS'] as  $item) {


			$product_ids[] = $item['id'];
			$val = $val + $item['price']*$item['q'];?>
		        {'name': '<?=$item['name']?>', 'id': <?=$item['id']?>,'price': <?=$item['price']?>,'brand': '<?=$item['brand']?>','category': '<?=$item['category']?>','variant': '<?=$item['article']?>', 'quantity': <?=$item['q']?>},
		<?}?>
				],

		      }
		    },
		    'event': 'gtm-ee-event',
		    'gtm-ee-event-category': 'Enhanced Ecommerce',
		    'gtm-ee-event-action': 'Purchase',
		    'gtm-ee-event-non-interaction': 'False',
		    });

			var _rutarget = window._rutarget || [];
			_rutarget.push({'event': 'thankYou', 'products': [
				<?foreach ($arResult['ITEMS'] as  $item) {
				  echo '{ qty: '.intval($item['q']).', sku: "'.$item['id'].'", price: '.round($item['price']).' },';
			  }?>
		  ], 'order_id': '<?=$arResult['ORDER']['ACCOUNT_NUMBER']?>', 'total_cost': <?=$arResult['ORDER']['PRICE']?>});


			waitForYm(function(){
				ym(30121774,'reachGoal','FULL_ORDER');
			});
			waitForFbq(function(){
				fbq('track', 'Purchase', {content_type: 'product', contents:[<?
   foreach ($arResult['ITEMS'] as  $item) {
	   echo '{id: "'.$item['id'].'" , quantity: '.intval($item['q']).'},';
   }
			   ?>], currency: 'RUB', value:  parseInt(<?=$val?>)});
			});

			 dataLayer.push({ 'event': 'purchase', 'value':<?=$val?>, 'items': [
			 	<?foreach ($arResult['ITEMS'] as  $item) {?>
				 { 'id': <?=$item['id']?>, 'google_business_vertical': 'retail' },
				 <?}?>
			  ] });

			  waitForGtag(function () {
		          gtag('event', 'purchase', {
		            'send_to': 'AW-434927646',
		            'value': parseInt(<?=$val?>),
		            'items': [
		                <?foreach ($arResult['ITEMS'] as  $item) {?>{
		              'id': <?=$item['id']?>,
		              'google_business_vertical': 'retail'
		          },
		              <?}?>]
		          });
		      });

			  waitForVk(function(){
				 VK.Goal('purchase');

				 const eventParams = {
             	"products" : [{
   			 	<?foreach ($arResult['ITEMS'] as  $item) {?>
   				 'id':<?=$item['id']?>,
   				 <?}?>
			 }] ,
                 //"category_ids" : $section_id,
             	//"business_value" : 88,
             	"total_price" : parseInt(<?=$val?>)
             	};
                  VK.Retargeting.ProductEvent(PRICE_LIST_ID, 'purchase', eventParams);

			  });
			  var _gcTracker = window._gcTracker || [];
			  _gcTracker.push(['order_complete', { order_id: '<?=$arResult['ORDER']['ACCOUNT_NUMBER']?>' }]);

            var _tmr = window._tmr || (window._tmr = []);
            _tmr.push({"type":"reachGoal","id":3206755,"goal":"purchase"});

			  <?if (!empty($feed_products)):
				  $pid_ar  = '['.implode(",", $product_ids).']';?>
			  var _tmr = _tmr || [];
			  _tmr.push({
			  type: "itemView",
			  productid: <?=$pid_ar?>,
			  pagetype: "purchase",
			  totalvalue:  <?=$val?>,
			  list: "102" });

            _tmr.push({
                type: "itemView",
                productid: <?=$pid_ar?>,
                pagetype: "purchase",
                totalvalue:  <?=$val?>,
                list: "1" });

			  <?endif;?>
		    </script>

            <script>
                <?
                $oldUser = 0;
                $order_costs = 0;
                if (isset($_COOKIE['medi_cos']) || isset($_COOKIE['medi_cfos'])){
                    $order_costs = intval($_COOKIE['medi_cos']) + intval($_COOKIE['medi_cfos']);
                    if ($order_costs > 0) $oldUser = "1";
                }
                if ($USER->GetID() > 0) {
                    if ($obUser = $USER->GetByID($USER->GetID())){
                        $arUser = $obUser->Fetch();
                        $userRegTime = (time() - strtotime($arUser['DATE_REGISTER'])) / 86400;
                        if ($userRegTime >= 1){
                            $oldUser = "2";
                        }
                    }
                }
                //DATE_REGISTER?>
                window.gdeslon_q = window.gdeslon_q || [];
                window.gdeslon_q.push({
                    page_type: "thanks", //тип страницы: main, list, card, basket, thanks, other
                    merchant_id: "104092", //id оффера в нашей системе
                    order_id: "<?=$arResult['ORDER']['ACCOUNT_NUMBER']?>", //id заказа
                    category_id: "", //id текущей категории
                    products: [<?foreach ($arResult['ITEMS'] as  $item) {
                        echo '{ quantity: '.intval($item['q']).', id: "'.$item['id'].'", price: '.round($item['price']).' },';
                    }?>
                    <?if ($oldUser == "0"){echo '{ quantity: 1, id: "002", price: '.round($val).' }';}else {echo '{ quantity: 1, id: "001", price: '.round($val).' }';}?>],
                    deduplication: "<?=DEDUPLICATION?>", //параметр дедупликации заказов (по умолчанию - заказ для Gdeslon)
                    user_id: "<?=$nUserID?>" //идентификатор пользователя
                });
            </script>
				<script type='text/javascript'>
				var dataLayer = dataLayer || [];
				dataLayer.push({
					'event': 'crto_transactionpage',
					crto: {
						'email': '<?=$nUserEmail?>',
						'transactionid':'<?=$arResult['ORDER']['ACCOUNT_NUMBER']?>',
						'products': [
				<?foreach ($arResult['ITEMS'] as  $item) {

					$product_ids[] = $item['id'];
					$val = $val + $item['price'];?>
						{'id': <?=$item['id']?>,'price': <?=$item['price']?>,'quantity': <?=$item['q']?>},
				<?}?>
					   ]
					}
				});
				</script>
		<?}
		setcookie('CONFIRM_ORDER_'.$arResult['ORDER']['ACCOUNT_NUMBER'],  $arResult['ORDER']['ACCOUNT_NUMBER'], time()+365*86400 );

	}
	?>
<h1 class="ff-medium">Спасибо за заказ!</h1>
<h2 class="ff-medium">Заказ №<?=$arResult["ORDER"]["ACCOUNT_NUMBER"]?> создан</h2>

<?if (defined("order_attention")){?>
<div class="attention"><span class="ff-medium medi-color"><?=order_attention?></span></div>
<?}?>

<div class="order_desc">
	<?if(!empty($arResult["ORDER_PROPS"]['PHONE']) || !empty($arResult["ORDER_PROPS"]['EMAIL'])):?>
	<?=($arResult["ORDER_INFO"]['NAME'] ? $arResult["ORDER_INFO"]['NAME'].', у' : 'У')?>ведомления о статусе заказа вы будете получать <?if(!empty($arResult["ORDER_PROPS"]['PHONE'])):?>по SMS на номер  <b><?=$arResult["ORDER_PROPS"]['PHONE']?></b>
<?endif;?>
	<?if(!empty($arResult["ORDER_PROPS"]['PHONE']) && !empty($arResult["ORDER_PROPS"]['EMAIL'])):?> и <?endif;?>
	<?if(!empty($arResult["ORDER_PROPS"]['EMAIL'])):?> по электронной почте <b><?=$arResult["ORDER_PROPS"]['EMAIL']?></b>
		<?endif;?>
	<?endif;?>
</div>

<?$itemDeclension = new Declension('товар', 'товара', 'товаров');?>
<div class="order_cols">
	<div class="order_info">
		<?if ($arResult['STORE']):?>
		<h2 class="ff-medium">Cамовывоз из салона medi</h2>

		<div class="order_salon">
			<div class="order_salon_info">
				<span class="order_salon_label address">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 729.33 729.33"><defs><style>.order_salon_label.address .a{fill:none;stroke:#b4b4b4;stroke-miterlimit:10;stroke-width:25px;}.order_salon_label.address .b{fill:#b4b4b4;}</style></defs><title>Адрес</title><circle class="a" cx="364.66" cy="364.66" r="352.16"></circle><path class="b" d="M740.19,227.73l-.38-.1c-100.78,0-182.49,81.7-182.49,182.48a182.1,182.1,0,0,0,53.39,129L739.89,668.27l.22.1L869.29,539.2a182.14,182.14,0,0,0,53.39-129C922.68,309.44,841,227.73,740.19,227.73Zm-2.82,288.83a105,105,0,1,1,105-105A105,105,0,0,1,737.37,516.56Z" transform="translate(-375.34 -83.34)"></path></svg><?=$arResult['STORE']['ADDRESS']?></span>

				<span class="order_salon_label metro"><?if ($arResult['STORE']['METRO']['SECTION']['ICON']){?><img src="<?=$arResult['STORE']['METRO']['SECTION']['ICON']['SRC']?>" title="<?=$arResult['STORE']['METRO']['SECTION']['NAME']?>"/><?=$arResult['STORE']['METRO']['NAME']?><?}?></span>

				<span class="order_salon_label phone"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 39 39"><defs><style>.order_salon_label.phone .a{fill:none;stroke:#b4b4b4;stroke-miterlimit:10;stroke-width:2px;}.order_salon_label.phone .b{fill:#b4b4b4;}</style></defs><title>Номер телефона</title><circle class="a" cx="19.5" cy="19.5" r="18.5"></circle><path class="b" d="M651.47,414.17a3.31,3.31,0,0,0-1.13,3.41,10.58,10.58,0,0,0,3.65,6.6,3.38,3.38,0,0,0,3.67.73l3.74,6.45a4.29,4.29,0,0,1-5.5.38,20.18,20.18,0,0,1-5.45-5.41,31,31,0,0,1-5.55-11.84c-.09-.47-.16-.94-.22-1.42-.36-2.7.45-4.17,3-5.42Z" transform="translate(-635.42 -399.33)"></path><path class="b" d="M652.69,414.29a4.08,4.08,0,0,1-.82-.73c-1.17-2-2.31-4-3.47-6a.88.88,0,0,1,.39-1.35l1.62-1a.87.87,0,0,1,1.35.38c1.16,2,2.32,4,3.44,6,.15.26.19.84,0,1C654.46,413.22,653.61,413.7,652.69,414.29Z" transform="translate(-635.42 -399.33)"></path><path class="b" d="M662.57,431.4a3.89,3.89,0,0,1-.8-.69c-1.18-2-2.33-4-3.5-6a.85.85,0,0,1,.31-1.3c.59-.35,1.18-.7,1.78-1a.83.83,0,0,1,1.23.34c1.19,2.06,2.39,4.12,3.55,6.21.12.21.13.71,0,.81C664.33,430.3,663.47,430.81,662.57,431.4Z" transform="translate(-635.42 -399.33)"></path></svg><?=$arResult['STORE']['PHONE']?></span>

				<span class="order_salon_label shedule"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 39 39"><defs><style>.order_salon_label.shedule .a{fill:none;stroke:#b4b4b4;stroke-miterlimit:10;stroke-width:2px;}.order_salon_label.shedule .b{fill:#b4b4b4;}</style></defs><title>Режим работы</title><circle class="a" cx="19.5" cy="19.5" r="18.5"></circle><rect class="b" x="17.5" y="7.67" width="4" height="12"></rect><rect class="b" x="657.42" y="412.33" width="4" height="13" transform="translate(442.83 -639.92) rotate(90)"></rect></svg><?=$arResult['STORE']['SCHEDULE']?>  <a href="/salons/<?=$arResult['STORE']['CODE']?>/" class="order_salon_more" target="_blank">Подробнее о салоне</a></span>

			</div>
			<div class="order_salon_map" id="salon_map">

			</div>
			<div class="order_salon_vector">
				<div class="sp__header"><h2 class="ff-medium">Как добраться</h2></div>
				<div class="sp_content"><p><?=htmlspecialchars_decode($arResult['STORE']["UF_VECTOR"]) ?></p></div>
			</div>
		</div>

		<?
		  $gpsN = substr($arResult['STORE']["GPS_N"], 0, 15);
		  $gpsS = substr($arResult['STORE']["GPS_S"], 0, 15);
		  $gpsText = $arResult['STORE']["ADDRESS"];
		  $gpsTextLen = strlen($arResult['STORE']["ADDRESS"]);
		  ?>
		<script>
			var map;
			ymaps.ready(initMap);
			function initMap()
			{
				features = [{
				  'type':'Feature',
				  'id':'<?=$arResult['STORE']['ID']?>',
				  'geometry':{
					'type':'Point',
					'coordinates':[<?=$gpsN?>, <?=$gpsS?>]},
					'properties':{
					  'hintContent':'<?=$arResult['STORE']['NAME']?>',

					}
				  }];
				map = new ymaps.Map("salon_map", {
					center: [<?=$gpsN?>, <?=$gpsS?>],
					zoom: 16,
					controls: ['geolocationControl', 'fullscreenControl', 'zoomControl']
				});
				map.options.set( {
					suppressMapOpenBlock: true,
				});
				mediObjectManager = new ymaps.ObjectManager({
					clusterize: false,
					geoObjectIconLayout: 'default#image',
					geoObjectIconImageHref:  '//<?=SITE_SERVER_NAME?><?=SITE_TEMPLATE_PATH;?>/images/placemarker.png',
					geoObjectIconImageSize: [28, 37],
					geoObjectIconImageOffset: [-14, -37]
				});
				map.geoObjects.add(mediObjectManager);
				if (features !== undefined) {
					mediObjectManager.add({
						type: 'FeatureCollection',
						features: features
					});
				}
			}
		</script>

		 <?else:?>
		 <div class="order_delivery">
 			<h2 class="ff-medium">Доставка по адресу</h2>
			 <span class="order_address">
			 	<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 729.33 729.33"><defs><style>.order_address .a{fill:none;stroke:#b4b4b4;stroke-miterlimit:10;stroke-width:25px;}.order_address .b{fill:#b4b4b4;}</style></defs><title>Адрес</title><circle class="a" cx="364.66" cy="364.66" r="352.16"></circle><path class="b" d="M740.19,227.73l-.38-.1c-100.78,0-182.49,81.7-182.49,182.48a182.1,182.1,0,0,0,53.39,129L739.89,668.27l.22.1L869.29,539.2a182.14,182.14,0,0,0,53.39-129C922.68,309.44,841,227.73,740.19,227.73Zm-2.82,288.83a105,105,0,1,1,105-105A105,105,0,0,1,737.37,516.56Z" transform="translate(-375.34 -83.34)"></path></svg>
			 	 <?=$arResult['ORDER_PROPS']['LOCATION']?>,
			 	 <?=$arResult['ORDER_PROPS']['ADDRESS']?>
			 	 <?=$arResult['ORDER_PROPS']['ADDRESS_INFO']?></span>
		 </div>
		 <?endif;?>
		<h2 class="ff-medium">Оплата</h2>
		<b><?=$arResult['PAYMENT_NAME']?></b>
        <?if ($arResult['PAY_SYSTEM']['ID'] == '12'){?>
            <?/*<p>Вы можете дождаться подтверждения заказа нашим оператором, либо оплатить заказ прямо сейчас:</p>*/?>
                <?if ($arResult["ORDER"]["IS_ALLOW_PAY"] === 'Y'
                && !empty($arResult["PAYMENT"])) {

                    foreach ($arResult["PAYMENT"] as $payment){


                        if ($payment["PAID"] != 'Y'
                            && !empty($arResult['PAY_SYSTEM_LIST'])
                            && array_key_exists($payment["PAY_SYSTEM_ID"], $arResult['PAY_SYSTEM_LIST'])){

                            $arPaySystem = $arResult['PAY_SYSTEM_LIST_BY_PAYMENT_ID'][$payment["ID"]];

                if (empty($arPaySystem["ERROR"]))
                {
                    preg_match_all('/<a.*?href=["\'](.*?)["\'].*?>/i', $arPaySystem["BUFFERED_OUTPUT"], $matches);
                    if ($matches[1][0] && !isset($_REQUEST['return'])){?>
                        <script>window.location.href = '<?=$matches[1][0]?>';</script>
                    <?}
                    else{?>
                        <?=$arPaySystem["BUFFERED_OUTPUT"];
                    }?>

                <br><br>
                <?
                }
                else
                {
                ?>
                <span style="color:red;"><?=Loc::getMessage("SOA_ORDER_PS_ERROR")?></span>
                <?
                            }
                        }
                        else
                        {
                            ?>
            <span style="color:red;"><?=Loc::getMessage("SOA_ORDER_PS_ERROR")?></span>
            <?
                        }
                    }
                }
            }
        ?>
	</div>
	<div class="order_items">
		<div class="order_items_info">
		<p class="order_label">В заказе <?=count($arResult['ITEMS']).'&nbsp;'.($itemDeclension->get(count($arResult['ITEMS'])));?> на сумму <?=CurrencyFormat(($arResult['ORDER']['PRICE']-$arResult['ORDER']['PRICE_DELIVERY']), 'RUB')?></p>
			<?foreach ($arResult['ITEMS'] as $key => $value) {
				?>
				<div class="order_item">
					<a href="<?=$value['url']?>" target="_blank" class="order_item_pic">
						<?if (!empty($value['picture'])):?><img src="<?=$value['picture']['src']?>"/><?endif;?>
					</a>
					<div class="order_item_link">
						<a href="<?=$value['url']?>" target="_blank"  class="order_item_name"><?=$value['name']?></a><br>

						<span class="order_item_count">
							<?=round($value['q'])?> шт. x <?=CurrencyFormat($value['price'], 'RUB')?>
						</span>
					</div>
				</div>
				<?
			}?>
		</div>

		<div class="order_summary">
			<table class="order_summary_row">
				<tr>
					<td>
						Товары
					</td>
			 		<td>
						<span class="order_value "><?=CurrencyFormat(($arResult['ORDER']['PRICE']-$arResult['ORDER']['PRICE_DELIVERY']), 'RUB')?></span>
					</td>
				</tr>
				<tr>
					<td>
						Доставка
					</td>
			 		<td>
						<?=($arResult['ORDER']['PRICE_DELIVERY'] > 0 ? CurrencyFormat($arResult['ORDER']['PRICE_DELIVERY'], 'RUB') : 'Бесплатно')?>
					</td>
				</tr>
				<tr>
					<td>
						Итого к оплате
					</td>
			 		<td class="big">
						<?=CurrencyFormat($arResult['ORDER']['PRICE'], 'RUB')?>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
	<?/*table class="sale_order_full_table">
		<tr>
			<td>
				<?=Loc::getMessage("SOA_ORDER_SUC", array(
					"#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"]->toUserTime()->format('d.m.Y H:i'),
					"#ORDER_ID#" => $arResult["ORDER"]["ACCOUNT_NUMBER"]
				))?>
				<? if (!empty($arResult['ORDER']["PAYMENT_ID"])): ?>
					<?=Loc::getMessage("SOA_PAYMENT_SUC", array(
						"#PAYMENT_ID#" => $arResult['PAYMENT'][$arResult['ORDER']["PAYMENT_ID"]]['ACCOUNT_NUMBER']
					))?>
				<? endif ?>
				<? if ($arParams['NO_PERSONAL'] !== 'Y'): ?>
					<br /><br />
					<?=Loc::getMessage('SOA_ORDER_SUC1', ['#LINK#' => $arParams['PATH_TO_PERSONAL']])?>
				<? endif; ?>
			</td>
		</tr>
	</table>*/?>

	<?

	?>

<? else: ?>

	<b><?=Loc::getMessage("SOA_ERROR_ORDER")?></b>
	<br /><br />

	<table class="sale_order_full_table">
		<tr>
			<td>
				<?=Loc::getMessage("SOA_ERROR_ORDER_LOST", ["#ORDER_ID#" => htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"])])?>
				<?=Loc::getMessage("SOA_ERROR_ORDER_LOST1")?>
			</td>
		</tr>
	</table>

<? endif;
