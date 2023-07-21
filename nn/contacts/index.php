<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контактная информация");
?><h1>Контактная информация</h1>
 <?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"personal",
	array(
		"ALLOW_MULTI_SELECT" => "N",
		"CHILD_MENU_TYPE" => "",
		"COMPONENT_TEMPLATE" => "personal",
		"DELAY" => "N",
		"MAX_LEVEL" => "1",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MENU_CACHE_TIME" => "3600000",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"ROOT_MENU_TYPE" => "company",
		"USE_EXT" => "N"
	),
	false
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
				 <a href="tel:<?=$phone?>"><?=$phone?></a><br>
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
 <img alt="cont3.png" src="/bitrix/templates/dresscodeV2/images/cont3.png" title="cont3.png">
			</td>
			<td>

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
		"CACHE_TIME" => "360000",
		"CACHE_TYPE" => "A",
		"CHAIN_ITEM_LINK" => "",
		"CHAIN_ITEM_TEXT" => "",
		"COMPONENT_TEMPLATE" => "twoColumns",
		"EDIT_URL" => "",
		"IGNORE_CUSTOM_TEMPLATE" => "N",
		"LIST_URL" => "",
		"SEF_MODE" => "N",
		"SUCCESS_URL" => "",
		"USE_EXTENDED_ERRORS" => "Y",
		"WEB_FORM_ID" => "3",
		"VARIABLE_ALIASES" => array(
			"WEB_FORM_ID" => "WEB_FORM_ID",
			"RESULT_ID" => "RESULT_ID",
		)
	),
	false
);?>
<br><br>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php")?>
