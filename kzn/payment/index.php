<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Способы оплаты");
?><h1>Оплата при покупке в Казани</h1>
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
		<ul>
		  <li>Курьеру при&nbsp;получении  заказа</li>
		  <li>При самовывозе в&nbsp;ортопедических салонах&nbsp;medi</li>
		  <li>При самовывозе в&nbsp;пунктах выдачи заказов СДЭК и&nbsp;Boxberry</li>
		</ul>
		</div>
	<div class="flex-item">
		<table>
			<tr>
				<td><img src="/upload/content/payment/icon-card.png"></td><td><h2 class="h2 ff-medium">Оплата банковскими картами</h2></td>
			</tr>
		</table>
		<ul>
		  <li>Онлайн при&nbsp;оформлении заказа</li>
		  <li>Курьеру при&nbsp;получении заказа</li>
		  <li>При самовывозе в&nbsp;ортопедических салонах&nbsp;medi</li>
		  <li>При самовывозе в&nbsp;пунктах выдачи заказов СДЭК и&nbsp;Boxberry</li>
		</ul>
	</div>
</div>
</div>
	</div>
	<div class="global-information-block">
		<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	".default",
	array(
	"COMPONENT_TEMPLATE" => ".default",
		"AREA_FILE_SHOW" => "sect",	// Показывать включаемую область
		"AREA_FILE_SUFFIX" => "information_block",	// Суффикс имени файла включаемой области
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_TEMPLATE" => "",	// Шаблон области по умолчанию
	)
);?>
	</div>
</div><br /><br />
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>