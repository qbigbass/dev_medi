<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Доставка");
?><h1>Доставка покупок по Санкт-Петербургу и Ленинградской области</h1>
<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"personal",
	array(
	"COMPONENT_TEMPLATE" => "personal",
		"ROOT_MENU_TYPE" => "service",	// Тип меню для первого уровня
		"MENU_CACHE_TYPE" => "A",	// Тип кеширования
		"MENU_CACHE_TIME" => "3600000",	// Время кеширования (сек.)
		"MENU_CACHE_USE_GROUPS" => "N",	// Учитывать права доступа
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
 <p>Интернет-магазин medi-salon.ru доставляет заказы курьерской службой в&nbsp;будние дни с&nbsp;11:00 до&nbsp;18:00.</p>
 <h2 class="h2 ff-medium">Стоимость доставки</h2>
 <div class="table-simple-wrap">
	 <table class="table-simple">
		 <tr>
		 <th><span class="ff-medium">Условия доставки</span></td>
		 <th><span class="ff-medium">Стоимость доставки</span></p></td>
		 </tr>
		 <tr>
		 <td><p>Доставка заказа в&nbsp;пределах КАД при&nbsp;покупке на&nbsp;сумму <span class="ff-medium">более 5000&nbsp;рублей</span></p></td>
		 <td>БЕСПЛАТНО</p></td>
		 </tr>
		 <tr>
		 <td><p>Доставка заказа в&nbsp;пределах&nbsp;КАД при&nbsp;покупке на&nbsp;сумму<span class="ff-medium"> менее 5000&nbsp;рублей</span></p></td>
		 <td><p>300&nbsp;РУБЛЕЙ</p></td>
		 </tr>
		 <tr>
		 <td><p>Доставка заказа за&nbsp;КАД и&nbsp;по&nbsp;Ленинградской&nbsp;области<br>
		 Мурино, Девяткино, Парголово, Сертолово, Токсово, Агалатово, п.&nbsp;Песочный, Кронштадт&nbsp;и&nbsp;др.</p></td>
		 <td><p>400&nbsp;РУБЛЕЙ</p></td>
		 </tr>
		 <tr>
		 <td><p>Доставка заказа за&nbsp;КАД и&nbsp;по&nbsp;Ленинградской&nbsp;области<br>
		 Металлострой, Шушары, Всеволожск, Белоостров, Сестрорецк, Петергоф, Стрельна, Ломоносов&nbsp;и&nbsp;др.</p></td>
		 <td><p>500&nbsp;РУБЛЕЙ</p></td>
		 </tr>
		 <tr>
		 <td><p>Доставка заказа за&nbsp;КАД и&nbsp;по&nbsp;Ленинградской&nbsp;области<br>
		 Тайцы, Павловск, Красное&nbsp;село, Малое Карлино, Колпино, Пушкин, Славянка, Шлиссельбург, Отрадное, Дубровка, Кировск, пос.&nbsp;им.&nbsp;Морозова, Сосновый&nbsp;бор, Репино, Зеленогорск, Коммунар, Гатчина&nbsp;и&nbsp;др.</p></td>
		 <td><p>600 РУБЛЕЙ</p></td>
		 </tr>
	 </table>
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
