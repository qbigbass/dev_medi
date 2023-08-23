<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Доставка");
?><h1>Доставка покупок в Казани</h1>
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
	<p>Интернет-магазин medi-salon.ru предоставляет широкие возможности по доставке покупки. При этом действуют все возможные <a class="theme-link-dashed" href="/payment/">способы оплаты</a>.</p>
	<div class="tabs-wrap">
		<div class="tabs-links">
			<div class="tab-link tab-btn-link active">Доставка курьером</div>
			<div class="tab-link tab-btn-link">Самовывоз покупок</div>
		</div>
		<div class="tabs-content">
			<div class="tab-content active">
			  <p>Курьерскую доставку до двери обеспечивают службы CDEK и&nbsp;Boxberry. Точная стоимость рассчитывается автоматически при&nbsp;оформлении заказа на&nbsp;сайте магазина или&nbsp;ее&nbsp;сообщает специалист контактного центра при&nbsp;подтверждении заказа.</p>
			  <p>При&nbsp;получении заказа необходимо предъявить паспорт.</p>
		    </div>
			<div class="tab-content">
			  <p>Мы предоставляем доставку покупок в&nbsp;пункты самовывоза CDEK и&nbsp;Boxberry. Точная стоимость доставки рассчитывается автоматически при&nbsp;оформлении заказа на&nbsp;сайте.<br>
			  При&nbsp;получении заказа необходимо предъявить паспорт.</p>
			  <div class="table-simple-wrap">
			    <table class="table-simple">
					<thead>
					  <tr>
						<th><span class="ff-medium">Условия самовывоза</span></th>
						<th><span class="ff-medium">Стоимость самовывоза</span></th>
					  </tr>
					</thead>
					<tbody>
					  <tr>
						<td >
						  <p>Самовывоз из&nbsp;отделения Почты России</p>
						  <p>Выберите доставку Почтой России при&nbsp;оформлении заказа. Стоимость рассчитается автоматически.</p>
						</td>
						<td><span class="ff-medium">точный расчет при&nbsp;оформлении заказа</span></td>
					  </tr>
					  <tr>
						<td>
						  <p>Самовывоз в&nbsp;пунктах выдачи заказов CDEK и&nbsp;Boxberry</p>
						  <p>Выберите подходящую службу доставки при&nbsp;оформлении заказа. Стоимость рассчитается автоматически.</p>
						</td>
						<td><span class="ff-medium">точный расчет при&nbsp;оформлении заказа</span></td>
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
</div><br /><br />
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
