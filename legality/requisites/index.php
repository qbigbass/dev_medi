<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Реквизиты организации");
?><?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"personal",
	array(
	"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
		"CHILD_MENU_TYPE" => "",	// Тип меню для остальных уровней
		"COMPONENT_TEMPLATE" => "personal",
		"DELAY" => "N",	// Откладывать выполнение шаблона меню
		"MAX_LEVEL" => "1",	// Уровень вложенности меню
		"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
		"MENU_CACHE_TIME" => "3600000",	// Время кеширования (сек.)
		"MENU_CACHE_TYPE" => "A",	// Тип кеширования
		"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
		"ROOT_MENU_TYPE" => "legality",	// Тип меню для первого уровня
		"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
	)
);?>

<style>
.ContentTable {
	border-collapse: collapse;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	width: 100%;
	overflow-x: auto;
	min-width: 320px;
}
.ContentTable th {
	font-family: "robotomedium";
	background-color: #f7f7f7;
	border: 1px solid #e4e4e4;
	font-weight: normal;
	padding: 12px 24px;
}
.ContentTable td {
	border: 1px solid #e4e4e4;
	vertical-align: middle;
	padding: 12px 24px;
	text-align: left;
	color: #333333;
}
.ContentTable span {
	color:#777777;
	font-size:12px;
}
</style>

<div class="global-block-container">
  <div class="global-content-block">

<table class="ContentTable">
<thead>
<tr>
	<th>
		 Название организации
	</th>
	<th>
		Общество с&nbsp;ограниченной ответственностью<br>
		«МЕДИ&nbsp;РУС»
	</th>
</tr>
</thead>
<tbody>
<tr>
	<td>
 <b>Страна юридического и&nbsp;фактического местонахождения</b>
	</td>
	<td>
		 Российская Федерация
	</td>
</tr>
<tr>
	<td>
 <b>Юридический адрес</b>
	</td>
	<td>
	121609, г.&nbsp;Москва, ул.&nbsp;Осенняя, дом&nbsp;4, корп.&nbsp;1, помещение&nbsp;П&nbsp;42
	</td>
</tr>
<tr>
	<td>
 <b>Адрес для&nbsp;корреспонденции</b>
	</td>
	<td>
	108811, ОПС&nbsp;Московский, а/я&nbsp;1607 
	</td>
</tr>
<tr>
	<td>
 <b>ИНН</b>
	</td>
	<td>
	7731536713
	</td>
</tr>
<tr>
	<td>
 <b>ОГРН</b>
	</td>
	<td>
	1057749667975
	</td>
</tr>
<tr>
	<td>
 <b>ОКВЭД</b>
	</td>
	<td>46.46</td>
</tr>
</tbody>
</table>
<br>
</div>
  <div class="global-information-block">
    <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	".default",
	array(
	"AREA_FILE_RECURSIVE" => "Y",
		"AREA_FILE_SHOW" => "sect",	// Показывать включаемую область
		"AREA_FILE_SUFFIX" => "information_block",	// Суффикс имени файла включаемой области
		"COMPONENT_TEMPLATE" => ".default",
		"EDIT_TEMPLATE" => "",	// Шаблон области по умолчанию
	)
);?>
  </div>
</div>
<br><br>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>