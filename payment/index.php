<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Способы оплаты");
?><h1>Оплата при покупке в Москве и Московской области</h1>
<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"personal",
	array(
	"COMPONENT_TEMPLATE" => "personal",
		"ROOT_MENU_TYPE" => "service",	// Тип меню для первого уровня
		"MENU_CACHE_TYPE" => "A",	// Тип кеширования
		"MENU_CACHE_TIME" => "3600000",	// Время кеширования (сек.)
		"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
		"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
		"MAX_LEVEL" => "1",	// Уровень вложенности меню
		"CHILD_MENU_TYPE" => "",	// Тип меню для остальных уровней
		"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
		"DELAY" => "N",	// Откладывать выполнение шаблона меню
		"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
	)
);?>
<div class="global-block-container">
	<div class="global-content-block">
<div class="detail-text-wrap">
<div class="flex">
	<div class="flex-item">
		<table>
			<tr>
				<td><img src="/upload/content/payment/icon-money.png"></td><td><h2 class="h2 ff-medium">Оплата наличными доступна везде и&nbsp;всегда</h2></td>
			</tr>
		</table>
		<ul class="galka">
		  <li>Курьеру при&nbsp;получении  заказа</li>
		  <li>В&nbsp;ортопедических салонах&nbsp;medi</li>
		  <li>В&nbsp;пунктах выдачи заказов СДЭК и&nbsp;Boxberry</li>
		</ul>
		</div>
	<div class="flex-item">
		<table>
			<tr>
				<td><img src="/upload/content/payment/icon-card.png"></td><td><h2 class="h2 ff-medium">Оплата банковскими картами</h2></td>
			</tr>
		</table>
		<ul class="galka">
			<li>Наложенным платежом</li>
		  <?/*<li>Онлайн при&nbsp;оформлении заказа</li>*/?>
		  <li>Курьеру при&nbsp;получении заказа</li>
		  <li>В&nbsp;ортопедических салонах&nbsp;medi</li>
		  <li>В&nbsp;пунктах выдачи заказов СДЭК и&nbsp;Boxberry</li>
		</ul>
	</div>
</div>
</div>
	</div>

</div><br /><br />
<script>
var _gcTracker=_gcTracker||[];
_gcTracker.push(['view_page', { name: 'view_payment'}]);
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
