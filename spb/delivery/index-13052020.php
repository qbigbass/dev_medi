<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Способы и условия доставки по Санкт-Петербургу и Ленинградской области");
?>

<h1>Способы и&nbsp;условия доставки по&nbsp;Санкт-Петербургу и&nbsp;Ленинградской области</h1>
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
<style>
	td{
		border: none!important;
	}
</style>
<div class="global-block-container">
  <div class="global-content-block">
    <div class="detail-text-wrap">
      <p class="ff-medium">Вы&nbsp;можете заказать доставку изделий курьером или&nbsp;забрать самостоятельно в&nbsp;пунктах выдачи заказов СДЭК.</p>
      <div class="tabs-wrap">
        <div class="tabs-links">
          <div class="tab-link tab-btn-link btn-small btn-border active">Доставка курьером</div>
          <div class="tab-link tab-btn-link btn-small btn-border">Самовывоз</div>
        </div>
        <div class="tabs-content">
		  <div class="tab-content active" style="margin: 0 10px;">
			<img width="100%" src="/upload/content/delivery/1.png" alt="">
			<div class="table-simple-wrap">
			<table align="center" class="table-simple" style="margin: 10px 0;">
			<tbody>
			  <tr style="box-shadow: 0 5px 5px 0 rgba(0,0,0,0.1);">
				<td style="font-size: 1.3em;"><span class="ff-medium"> По&nbsp;Санкт-Петербургу</span> (в&nbsp;пределах КАД)<br>заказов стоимостью <span class="medi-color ff-medium">до&nbsp;1000 рублей</span></td>
				<td style="font-size: 1.6em;background: #e9edf0; width: 50%;" class="ff-medium"> 290&nbsp;рублей </td>
			  </tr>
			</tbody>
			</table>
			<table align="center" class="table-simple" style="margin: 10px 0;">
			<tbody>
			  <tr style="box-shadow: 0 5px 5px rgba(0,0,0,0.1);">
				<td style="font-size: 1.3em;"><span class="ff-medium"> По&nbsp;Санкт-Петербургу</span> (в&nbsp;пределах КАД)<br>заказов стоимостью <span class="medi-color ff-medium">от&nbsp;1000 рублей</span></td>
				<td class="ff-medium" style="font-size: 1.6em;background: #e9edf0; width: 50%; text-transform: uppercase;"> Бесплатно</td>
			  </tr>
			</tbody>
			</table>
			<table align="center" class="table-simple" style="margin: 10px 0;">
			<tbody>
			  <tr style="box-shadow: 0 5px 5px rgba(0,0,0,0.1);">
				<td style="font-size: 1.3em;"><span class="ff-medium"> По&nbsp;Ленинградской области</span> (до&nbsp;10&nbsp;км от&nbsp;КАД), включая Пушкин, Колпино, Токсово, Всеволожск, Кузьмоловский, Вартемяги, Сертолово, Левашово, Песочный, Сестрорецк, Котлин, Большая Ижора, Красное Село.</td>
				<td class="ff-medium" style="font-size: 1.6em;background: #e9edf0; width: 50%;"> 290&nbsp;рублей </td>
			  </tr>
			</tbody>
			</table>
			</div>
            <div class="error-wrap">
              <ol>
                <li>Доставка осуществляется по&nbsp;адресу, указанному при&nbsp;оформлении заказа. Если заказ необходимо доставить по&nbsp;другому адресу, сообщите его нашему специалисту при подтверждении заказа.</li>
                <li>Доставка заказов осуществляется на&nbsp;следующий день после подтверждения заказа.</li>
                <li>При&nbsp;доставке вам будут переданы все необходимые документы, подтверждающие покупку изделий.</li>
                <li>Цена, указанная в&nbsp;переданных вам курьером документах, является окончательной. Курьер не&nbsp;обладает правом корректировки цены.</li>
                <li>В&nbsp;обязанности работников службы доставки не&nbsp;входит консультация относительно свойств изделий. Все потребительские качества и&nbsp;свойства приобретаемых вами изделий следует уточнять у&nbsp;специалистов до&nbsp;момента покупки изделий.</li>
              </ol>
            </div>
          </div>
          <div class="tab-content">
			<img width="100%" src="/upload/content/delivery/2.png" alt="">
			<div class="table-simple-wrap">
			<table align="center" class="table-simple" style="margin: 10px 0;">
			<tbody class="ff-medium">
			  <tr style="box-shadow: 0 5px 5px rgba(0,0,0,0.1);">
				<td style="font-size: 1.3em;">Самовывоз из&nbsp;пункта выдачи заказов <br><span class="medi-color">CDEK</span></td>
				<td style="font-size: 1.3em;background: #e9edf0; width: 50%;text-align: left;">Стоимость рассчитывается автоматически при&nbsp;оформлении заказа</td>
			  </tr>
			</tbody>
			</table>
			</div>
		  </div>
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
</div>
<br />
<br />
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>