<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контактная информация");
?><h1>Контактная информация</h1>
 <?$APPLICATION->IncludeComponent(
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
		"ROOT_MENU_TYPE" => "company",	// Тип меню для первого уровня
		"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
	)
);?>
<?
if ($_SESSION["USER_GEO_POSITION"]['city'] != "Москва" && SITE_ID == 's1')
{
	$phone = $GLOBALS['medi']['phones'][0];
}
else {
	$phone = $GLOBALS['medi']['phones'][SITE_ID];
}
?>
	<ul class="contactList">
		<li>
		<table>
		<tbody>
		<tr>
			<td>
 <img alt="cont1.png" src="/bitrix/templates/dresscodeV2/images/cont1.png" title="cont1.png">
			</td>
			<td>
				 <a href="tel:<?=$phone?>" id="contacts_phone" onclick="ym(30121774, 'reachGoal', 'CLICK_PHONE'); return true;"><?=$phone?></a><br>
			</td>
		</tr>
		</tbody>
		</table>
 </li>
		<li>
		<table>
		<tbody>
		<tr>
			<td>
 <img alt="cont2.png" src="/bitrix/templates/dresscodeV2/images/cont2.png" title="cont2.png">
			</td>
			<td>
 <a href="mailto:info@medi-salon.ru">info@mediexp.ru</a><br>
<a href="mailto:marketing@mediexp.ru">marketing@mediexp.ru</a>
			</td>
		</tr>
		</tbody>
		</table>
 </li>
		<!-- <li>
		<table>
		<tbody>
		<tr>
			<td>
 <img alt="cont3.png" src="/bitrix/templates/dresscodeV2/images/cont3.png" title="cont3.png">
			</td>
			<td>

			</td>
		</tr>
		</tbody>
		</table>
 </li> -->
		<li>
		<table>
		<tbody>
		<tr>
			<td>
 <img alt="cont4.png" src="/bitrix/templates/dresscodeV2/images/cont4.png" title="cont4.png">
			</td>
			<td>
				 Ежедневно   с 8:00 до 21:00<br>
Без перерывов и&nbsp;выходных
			</td>
		</tr>
		</tbody>
		</table>
 </li>
	</ul>

<br><br>
		<?$APPLICATION->IncludeComponent(
	"bitrix:form.result.new",
	"twoColumns",
	array(
	"CACHE_TIME" => "360000",	// Время кеширования (сек.)
		"CACHE_TYPE" => "A",	// Тип кеширования
		"CHAIN_ITEM_LINK" => "",	// Ссылка на дополнительном пункте в навигационной цепочке
		"CHAIN_ITEM_TEXT" => "",	// Название дополнительного пункта в навигационной цепочке
		"COMPONENT_TEMPLATE" => "twoColumns",
		"EDIT_URL" => "",	// Страница редактирования результата
		"IGNORE_CUSTOM_TEMPLATE" => "N",	// Игнорировать свой шаблон
		"LIST_URL" => "",	// Страница со списком результатов
		"SEF_MODE" => "N",	// Включить поддержку ЧПУ
		"SUCCESS_URL" => "",	// Страница с сообщением об успешной отправке
		"USE_EXTENDED_ERRORS" => "Y",	// Использовать расширенный вывод сообщений об ошибках
		"VARIABLE_ALIASES" => array(
			"WEB_FORM_ID" => "WEB_FORM_ID",
			"RESULT_ID" => "RESULT_ID",
		),
		"WEB_FORM_ID" => "3",	// ID веб-формы
	)
);?>
<br><br>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "medi",
  "alternateName": "Меди",
  "url": "https://www.medi-salon.ru/contacts/",
  "logo": "https://www.medi-salon.ru/bitrix/templates/dresscodeV2/headers/header8/images/logo.png?v=1580286314?v=1580286314",
  "sameAs": [
    "https://vk.com/medi.salon.russia",
    "https://ok.ru/medi.salon.russia",
    "https://www.youtube.com/channel/UC5oEj1qAP5GO078nOxKceBA"
  ]
}
</script>
<script>
    var _gcTracker=_gcTracker||[];
    _gcTracker.push(['view_page', { name: 'view_contacts' }]);
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php")?>