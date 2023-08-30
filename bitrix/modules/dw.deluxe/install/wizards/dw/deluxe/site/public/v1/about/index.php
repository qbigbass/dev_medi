<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("О магазине");
?><h1>О магазине</h1>
<?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"personal", 
	array(
		"COMPONENT_TEMPLATE" => "personal",
		"ROOT_MENU_TYPE" => "about",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_TIME" => "3600000",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => "1",
		"CHILD_MENU_TYPE" => "",
		"USE_EXT" => "N",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N"
	),
	false
);?>
<div class="global-block-container">
	<div class="global-content-block">
		<div class="bx_page">
			Мы рады приветствовать вас на сайте нашей компании.
			<p>Наша компания была основана в 1993 году, а наш интернет-магазин стал одним из первых магазинов, осуществляющих on-line продажу мебели в регионе. Компания специализируется на оптовой и розничной продаже мебели как для дома, так и для офиса и производственных помещений, а также различной мебельной фурнитуры.</p>
			<p>На данный момент мы представляем собой крупную компанию, владеющую интернет–магазином и имеющую в своей сети единый call-центр, который регулирует всю деятельность магазина, отдел продаж, службу доставки, широкий штат квалифицированных сборщиков, собственный склад c постоянным наличием необходимого запаса товаров.</p>

			<p>За это время у нас сложились партнерские отношения с ведущими производителями, позволяющие предлагать высококачественную продукцию по конкурентоспособным ценам.</p>

			<p>Мы можем гордиться тем, что у нас один из самых широких ассортиментов мебели в городе и области. </p>

			<p><b>НА НАШЕМ САЙТЕ К ВАШИМ УСЛУГАМ:</b></p>

			<ul>
			  <li>реальные и конкурентоспособные цены;</li>

			  <li>широчайший ассортимент товаров;</li>

			  <li>качественные описания и изображения товаров;</li>

			  <li>поиск товаров в магазине;</li>

			  <li>система обратной связи;</li>

			  <li>продажа только сертифицированных и имеющих легальное происхождение товаров;</li>

			  <li>гарантийное обслуживание купленных у нас товаров;</li>

			  <li>покупка товара, не выходя из дома или офиса;</li>

			  <li>быстрое согласование товара с клиентом для подтверждения заказа;</li>

			  <li>обмен товаров ненадлежащего качества и многое другое.</li>
			</ul>

			<p>Мы всегда рады общению с нашими клиентами. Если у вас есть какие-либо пожелания, предложения, замечания, касающиеся работы нашего Интернет-магазина - пишите нам, и мы с благодарностью примем ваше мнение во внимание:</p>

			<p><b>ЭЛЕКТРОННАЯ ПОЧТА</b>: <a href="mailto:sale@magazine.ru">sale@magazine.ru</a></p>
		</div>
	</div>
	<div class="global-information-block">
		<?$APPLICATION->IncludeComponent(
			"bitrix:main.include", 
			".default", 
			array(
				"COMPONENT_TEMPLATE" => ".default",
				"AREA_FILE_SHOW" => "sect",
				"AREA_FILE_SUFFIX" => "information_block",
				"AREA_FILE_RECURSIVE" => "Y",
				"EDIT_TEMPLATE" => ""
			),
			false
		);?>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>