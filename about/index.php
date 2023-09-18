<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("О medi");
$APPLICATION->AddHeadString('<link rel="canonical" href="https://www.medi-salon.ru'.$APPLICATION->GetCurDir().'" />');
?><h1>О medi</h1>
<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"personal",
	array(
	"COMPONENT_TEMPLATE" => "personal",
		"ROOT_MENU_TYPE" => "company",	// Тип меню для первого уровня
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
<style>
	#main {
		font-size: 18px;
	}
	.galka>li {
		margin-bottom: 6px;
	}
	.h3 {
		font-size: 1.2em!important;
		line-height: 1.3em;
	}
	.theme-link-dashed {
		display: inline!important;
		font-size: 1em;
	}
	.sert {
		width: 100%;
		max-width: 280px;
		margin: auto;
		padding: 20px;
	}
	.flex>.flex-item {
		padding: 0!important;
		border: none!important;
	}
	.light-bg, .white-bg {
		padding-top: 25px;
		padding-bottom: 25px;
	}
	.white-bg>.white-bg {
		padding: 25px 5%;
	}
	.flex>.item:first-child {
		width: 150px;
		padding: 20px;
		margin-left: auto;
		margin-right: auto;
	}
	.flex>.item:last-child {
		width: calc(100% - 190px);
	}
	@media screen and (max-width:750px) {
		.flex>.item:last-child {
			width: 100%!important;
		}
		.reverse {
			flex-direction: column-reverse;
		}
		.no-padding {
			padding-left: 0!important;
			padding-right: 0!important;
		}
		.sert {
			padding: 20px 0;
		}
	}
	@media screen and (min-width:751px) and (max-width:1023px) {
		.flex>.flex-item{
			width: 100%!important;
		}
	}
</style>
<h2 class="h2 ff-medium" style="text-align: center;">Приветствуем вас в&nbsp;официальном интернет-магазине фирменных ортопедических салонов medi!</h2>
<div class="white-bg">
	<div class="flex">
		<div class="flex-item">
			<iframe style="width: 100%; height: 30vh;" src="https://www.youtube.com/embed/vomgs5ql09w" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		</div>
		<div class="flex-item">
			<p style="margin-top: 0;"><span class="medi-color ff-medium">Компания medi (Германия)</span>&nbsp;&ndash; один из&nbsp;мировых лидеров в&nbsp;области производства компрессионных и&nbsp;ортопедических изделий для&nbsp;лечения и&nbsp;профилактики заболеваний, а&nbsp;также реабилитации после травм и&nbsp;операций. Более 100&nbsp;лет инновационных разработок, направленных на&nbsp;улучшение здоровья и&nbsp;качества жизни людей по&nbsp;всему миру.</p>
			<p>Официальное представительство medi и&nbsp;сеть фирменных ортопедических салонов работают в&nbsp;России более 20&nbsp;лет.</p>
			<p>medi навсегда изменила представление миллионов людей о&nbsp;комфорте, качестве и&nbsp;эффективности ортопедических и&nbsp;компрессионных изделий.</p>
		</div>
	</div>
</div>
<div class="light-bg flex reverse">
	<div class="flex-item">
		<p><span class="medi-color ff-medium">Официальный интернет-магазин medi</span> предоставляет весь ассортимент изделий компании medi. Подлинность, медицинская эффективность и&nbsp;соответствие европейским стандартам качества подтверждены множеством сертификатов.</p>
		<p>В&nbsp;интернет-магазине medi вы найдете&nbsp;то, что&nbsp;искали&nbsp;&ndash; даже&nbsp;если в&nbsp;ассортименте нет&nbsp;подходящего изделия, у&nbsp;нас можно оформить его на&nbsp;заказ.</p>
	</div>
	<div class="flex-item">
		<img width="100%" src="/upload/content/about/1.jpg" alt="">
	</div>
</div>
<div class="white-bg no-padding">
	<h2 class="h2 ff-medium" style="text-align: center;">Продукция medi дарит свободу движения и&nbsp;радость активной жизни</h2><br>
	<div class="white-bg flex" style="border: 1px solid #e9e9e9;">
		<div class="item">
			<img width="100%" src="/upload/content/about/medi.svg" alt="">
		</div>
		<div class="item">
			<h3 class="h3 ff-medium">medi</h3>
			<p>Бренд medi представлен множеством продуктов из&nbsp;разных категорий, которые направлены на&nbsp;повышение качества жизни людей, столкнувшихся с&nbsp;различными заболеваниями венозной и&nbsp;лимфатической системы, а&nbsp;также с&nbsp;заболеваниями опорно-двигательного аппарата. <a target="_blank" href="/service/guaranty/" class="theme-link-dashed">Качество компрессионной продукции medi подтверждено международными стандартами.</a></p>
			<p><a target="_blank" href="/catalog/kompressionnyy-trikotazh/filter/series-is-824921e4-9f45-11e9-8113-e03f49499b1d-or-7b7075ee-9f45-11e9-8113-e03f49499b1d-or-8f582868-9f45-11e9-8113-e03f49499b1d-or-88c283c1-9f45-11e9-8113-e03f49499b1d-or-72d3c0fa-9f45-11e9-8113-e03f49499b1d-or-f8f742d1-b91e-11e9-8113-e03f49499b1d-or-5c6c67aa-ad37-11e9-8113-e03f49499b1d-or-96080d7b-9f45-11e9-8113-e03f49499b1d-or-58162910-9f45-11e9-8113-e03f49499b1d/apply/" class="theme-link-dashed">Компрессионный трикотаж для&nbsp;профилактики и&nbsp;лечения заболеваний венозной и&nbsp;лимфатической системы</a></p>
			<p><a target="_blank" href="/catalog/kompressionnyy-trikotazh/gospitalnyy/" class="theme-link-dashed">Госпитальный компрессионный трикотаж</a></p>
			<p><a target="_blank" href="/catalog/kompressionnyy-trikotazh/kosmetologicheskiy/" class="theme-link-dashed">Косметологический компрессионный трикотаж</a></p>
			<p><a target="_blank" href="/catalog/ortopediya/" class="theme-link-dashed">Бандажи и&nbsp;ортезы</a></p>
			<p><a target="_blank" href="/catalog/kompressionnyy-trikotazh/bandazhi-circaid/" class="theme-link-dashed">Регулируемые нерастяжимые бандажи circaid для&nbsp;лечения лимфатических отеков и&nbsp;венозных язв</a></p>
		</div>
	</div>
	<div class="light-bg" style="text-align: center;border: 1px solid #e9e9e9;">
		Помимо основного бренда компрессионных и&nbsp;ортопедических изделий, компания medi активно развивается в&nbsp;двух областях: разработке индивидуальных ортопедических стелек на&nbsp;карбоновой основе и&nbsp;интеллектуальной компрессионной одежды для&nbsp;спорта. Разработки в&nbsp;этих областях собраны в&nbsp;самостоятельные бренды: <span class="medi-color ff-medium">igli</span> и&nbsp;<span class="medi-color ff-medium">CEP</span>.
	</div>
	<div class="white-bg flex" style="border: 1px solid #e9e9e9;">
		<div class="flex-item" style="text-align: center;">
			<img style="height: 150px; margin: auto;" src="/upload/content/about/igli.svg" alt="">
			<h3 class="h3 ff-medium">igli</h3>
			<p>Бренд индивидуальных ортопедических стелек с&nbsp;динамической карбоновой основой для&nbsp;профилактики и&nbsp;лечения деформаций стоп</p>
		</div>
		<div class="flex-item" style="text-align: center;">
			<img style="height: 150px; margin: auto;" src="/upload/content/about/cep.svg" alt="">
			<h3 class="h3 ff-medium">CEP</h3>
			<p>Интеллектуальная одежда для&nbsp;спорта CEP для&nbsp;повышения комфорта и&nbsp;эффективности тренировок</p>
		</div>
	</div>

</div>
<h2 class="h2 ff-medium" style="text-align: center;">Приобретайте продукцию компании medi в&nbsp;фирменных ортопедических салонах</h2>
<div class="white-bg flex">
	<div class="flex-item">
		<img width="100%" src="/upload/content/about/2.jpg" alt="">
	</div>
	<div class="flex-item">
		<h3 class="h3 ff-medium"><span class="medi-color">medi</span>&nbsp;&ndash; немецкий стандарт обслуживания в&nbsp;России</h3>
		<ul class="galka">
			<li>Премиальный сервис.</li>
			<li>Обученные и&nbsp;квалифицированные специалисты салонов и&nbsp;интернет-магазина.</li>
			<li>Подробные и&nbsp;понятные консультации от&nbsp;ведущих специалистов по&nbsp;ортезированию и&nbsp;компрессионному трикотажу.</li>
			<li>Снятие мерок для&nbsp;подбора изделия в&nbsp;салоне, на&nbsp;дому или&nbsp;в&nbsp;лечебном учреждении.</li>
			<li>Изготовление изделий на&nbsp;заказ по&nbsp;индивидуальным меркам.</li>
			<li>Гарантии качества продукции и&nbsp;постпродажное обслуживание.</li>
		</ul>
	</div>
</div>
<div class="white-bg flex reverse">
	<div class="flex-item">
		<h3 class="h3 ff-medium">Уникальный набор услуг для&nbsp;здоровья в&nbsp;салонах и&nbsp;<span class="medi-color"><nobr>интернет-магазине</nobr> medi</span></h3>
		<ul class="galka">
			<li><a target="_blank" href="/services/zakaz-po-individualnym-merkam/" class="theme-link-dashed">Срочное изготовление компрессионного трикотажа medi по&nbsp;индивидуальным меркам</a></li>
			<li><a target="_blank" href="/services/izgotovlenie-ortopedicheskikh-stelek/" class="theme-link-dashed">Изготовление ортопедических стелек igli по&nbsp;индивидуальным параметрам</a></li>
			<li><a target="_blank" href="/services/besplatnoe-skanirovanie-stop/" class="theme-link-dashed">Бесплатное сканирование стоп</a></li>
			<li><a target="_blank" href="/services/individualnyy-podbor-ortopedicheskikh-izdeliy/" class="theme-link-dashed">Индивидуальный подбор ортопедических изделий на&nbsp;дому или&nbsp;в&nbsp;лечебном учреждении</a></li>
			<li><a target="_blank" href="/services/konsultatsiya-spetsialista-po-ortezirovaniyu/" class="theme-link-dashed">Бесплатная консультация специалиста по&nbsp;ортезированию</a></li>
			<li><a target="_blank" href="/services/shoes/" class="theme-link-dashed">Умная система подбора обуви</a></li>
		</ul>
	</div>
	<div class="flex-item">
		<img width="100%" src="/upload/content/about/3.jpg" alt="">
	</div>
</div>
<h2 class="h2 ff-medium" style="text-align: center;">Мы всегда рядом!</h2>
<div class="white-bg flex">
	<div class="flex-item">
		<img width="100%" src="/upload/content/about/4.jpg" alt="">
	</div>
	<div class="flex-item">
		<p>Весь ассортимент фирменных салонов medi доступен в&nbsp;официальном интернет-магазине medi&nbsp;&ndash; <a style="font-size: 1em;" target="_blank" href="/" class="theme-link-dashed">medi-salon.ru</a>.</p>
		<p>В&nbsp;интернет-магазине medi вы&nbsp;можете:</p>
		<ul class="galka">
			<li>Быстро, удобно и&nbsp;безопасно заказать любой товар, представленный в&nbsp;<a style="font-size: 1em;" target="_blank" href="/catalog/" class="theme-link-dashed">Каталоге</a> с&nbsp;доставкой по&nbsp;адресу, в&nbsp;салон, пункт выдачи СДЭК или&nbsp;Boxberry;</li>
			<li>Получить всю&nbsp;необходимую информацию о&nbsp;любом продукте, представленном на&nbsp;сайте;</li>
			<li>Узнать адрес нужного салона, график его работы и&nbsp;схему проезда в&nbsp;разделе <a style="font-size: 1em;" target="_blank" href="/salons/" class="theme-link-dashed">Салоны</a>;</li>
			<li>Записаться онлайн или&nbsp;по&nbsp;телефону, указанному на&nbsp;сайте, на&nbsp;любую из&nbsp;услуг, доступных в&nbsp;вашем регионе;</li>
		</ul>
	</div>
</div>
<h2 class="h2 ff-medium" style="text-align: center;">Желаем вам приятных покупок!</h2>
<div class="btn-wrap" style="text-align: center;">
	<a href="/catalog/" class="btn-simple btn-medium">Перейти в&nbsp;каталог</a>
</div>
<br>
<script>
    var _gcTracker=_gcTracker||[];
    _gcTracker.push(['view_page', { name: 'view_about' }]);
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
