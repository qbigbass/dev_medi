<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Как купить");
?><h1>Как купить</h1>
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
		"MENU_CACHE_TIME" => "36000",	// Время кеширования (сек.)
		"MENU_CACHE_TYPE" => "A",	// Тип кеширования
		"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
		"ROOT_MENU_TYPE" => "service",	// Тип меню для первого уровня
		"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
	)
);?>
<style>
	.answer {
		color: #000000!important;
		font-size: 18px;
	}
	.h3 {
		font-size: 18px!important;
	}
	img.big-pic-medi, img.small-pic-medi {
		margin-bottom: 1em;
	}
	.big-pic-medi {
		max-width: 1100px;
	}
	.tab-link {
		display: block!important;
	}
	a.theme-link-dashed {
		display: inline;
	}
/*  ---- Стилизация select подмененного через JS ----   */
	.select {
		display: block;
		max-width: 100%;
		width: 400px;
		position: relative;
	}

	.new-select {
		position: relative;
		border: 2px solid #e20074;
		padding: 10px 35px 10px 15px;
		cursor: pointer;
		user-select: none;
		font-size: 16px;
		color: #e20074;
	}

	.new-select__list {
		position: absolute;
		top: 44px;
		left: 0;
		border: 1px solid #e20074;
		cursor: pointer;
		width: calc(100% - 2px);
		z-index: 2;
		background: #fff;
		user-select: none;
	}

	.new-select__list.on {
		display: block;
	}

	.new-select__item span {
		display: block;
		padding: 10px 15px;
	}

	.new-select__item span:hover {
		color: #ffffff;
		background-color: #e20074;
		font-family: "robotomedium";
	}

	.new-select:after {
		content: '';
		display: block;
		width: 25px;
		height: 25px;
		position: absolute;
		right: 9px;
		top: 9px;
		background: url('/bitrix/templates/dresscodeV2/images/question-arrow.png') no-repeat right center / cover;
/*		opacity: 0.6;*/

		-webkit-transition: all .27s ease-in-out;
			-o-transition: all .27s ease-in-out;
				transition: all .27s ease-in-out;

		-webkit-transform: rotate(0deg);
			-ms-transform: rotate(0deg);
			 -o-transform: rotate(0deg);
				transform: rotate(0deg);
	}

	.new-select.on:after {
		-webkit-transform: rotate(180deg);
			-ms-transform: rotate(180deg);
			 -o-transform: rotate(180deg);
				transform: rotate(180deg);
	}

/*     -------------------     */
	@media screen and (max-width: 959px) {
		.answer {
			font-size: 15px!important;
		}
	}
</style>
<div class="global-block-container">
	<div class="global-content-block">
		<div class="detail-text-wrap">
			<div class="questions-answers-list">
				<div class="question-answer-wrap">
					<div class="question h3"><span class="ff-medium">Как&nbsp;найти изделие в&nbsp;поиске по&nbsp;каталогу</span>
						<div class="open-answer"><span class="hide-answer-text">Скрыть</span><span class="open-answer-text">Подробнее</span><div class="open-answer-btn"></div></div>
					</div>
					<div class="answer" style="display: none;">
						<div class="big-pic-medi">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 1-й</span></p>
							<p>Кликните по&nbsp;полю "Поиск по&nbsp;каталогу", которое находится на&nbsp;любой странице сайта в&nbsp;верхней части экрана.</p>
							<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/11.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 2-й</span></p>
							<p>Введите в&nbsp;открывшуюся поисковую строку один из&nbsp;искомых параметров: артикул, вид изделия (например, «чулки» или&nbsp;«бандаж»), класс компрессии (например, «1&nbsp;класс» или&nbsp;«2&nbsp;класс»), модельный ряд (например, «mediven elegance» или&nbsp;«mediven thrombexin»). Поисковая система подберет для&nbsp;вас изделия, максимально релевантные вашему запросу.</p>
							<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/12.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 3-й</span></p>
							<p>Кликните по&nbsp;подходящему варианту в&nbsp;окне <span class="medi-color">Результаты поиска</span>. Кликнув по&nbsp;фотографии или&nbsp;наименованию изделия, вы сразу попадете на&nbsp;страницу с&nbsp;его подробным описанием. Вы&nbsp;можете посмотреть все&nbsp;результаты поиска, кликнув по&nbsp;ссылке <span class="medi-color">Смотреть все&nbsp;результаты</span>. Перейдя по&nbsp;этой ссылке, вы&nbsp;попадете в&nbsp;<span class="medi-color">Каталог</span>, отфильтрованный с&nbsp;учетом вашего поискового запроса.</p>
						</div>
						<div class="small-pic-medi">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 1-й</span></p>
							<p>Находясь на&nbsp;любой странице сайта, нажмите на&nbsp;иконку лупы <span><img style="height: 18px; width: auto; display: inline; transform: translateY(3px);" src="/upload/content/service/howtobuy/lupa.png" alt=""></span> в&nbsp;меню.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m11.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 2-й</span></p>
							<p>Введите в&nbsp;открывшуюся поисковую строку один из&nbsp;искомых параметров: артикул, вид изделия (например, «чулки» или&nbsp;«бандаж»), класс компрессии (например, «1&nbsp;класс» или&nbsp;«2&nbsp;класс»), модельный ряд (например, «mediven elegance» или&nbsp;«mediven thrombexin»). Поисковая система подберет для&nbsp;вас изделия, максимально релевантные вашему запросу.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m12.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 3-й</span></p>
							<p>Выберите подходящий вариант в&nbsp;окне <span class="medi-color">Результаты поиска</span>. Нажав на&nbsp;фотографию или&nbsp;наименование изделия, вы&nbsp;сразу попадете на&nbsp;страницу с&nbsp;его подробным описанием.<br>Вы&nbsp;также можете посмотреть все&nbsp;результаты поиска, нажав на&nbsp;выделенный текст <span class="medi-color">Смотреть все&nbsp;результаты</span>. Перейдя по&nbsp;этой ссылке, вы&nbsp;попадете в&nbsp;<span class="medi-color">Каталог</span>, отфильтрованный с&nbsp;учетом вашего поискового запроса.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m13.png" alt="">
						</div>
					</div>
				</div>
				<div class="question-answer-wrap">
					<div class="question h3"><span class="ff-medium">Как&nbsp;подобрать изделие по&nbsp;каталогу</span>
						<div class="open-answer"><span class="hide-answer-text">Скрыть</span><span class="open-answer-text">Подробнее</span><div class="open-answer-btn"></div></div>
					</div>
					<div class="answer" style="display: none;">
						<div class="big-pic-medi">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 1-й</span></p>
							<p>Наведите курсор на&nbsp;наименование одного из&nbsp;<span class="medi-color">разделов каталога</span>. В&nbsp;выпавшем списке выберите основной параметр, по&nbsp;которому хотите отсортировать каталог (вид&nbsp;изделия, класс компрессии, назначение и&nbsp;т.д.).</p>
							<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/21.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 2-й</span></p>
							<p><span class="medi-color">Отфильтруйте</span> открывшийся каталог по&nbsp;параметрам, которые для&nbsp;вас важны.</p>
							<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/22.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 3-й</span></p>
							<p>Перейдите на&nbsp;страницу с&nbsp;подробным описанием изделия, кликнув на&nbsp;его&nbsp;<span class="medi-color">фотографию</span> или&nbsp;<span class="medi-color">наименование</span>.</p>
							<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/22.png" alt="">
						</div>
						<div class="small-pic-medi">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 1-й</span></p>
							<p>Нажмите на&nbsp;иконку списка <span><img style="height: 18px; width: auto; display: inline; transform: translateY(3px);" src="/upload/content/service/howtobuy/menu.png" alt=""></span> в&nbsp;основном меню и&nbsp;выберите нужный раздел каталога.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m21.png" alt="">
							<p>В&nbsp;выпавшем списке выберите <span class="meedi-color">основной параметр</span>, по&nbsp;которому хотите отсортировать этот раздел каталога (по&nbsp;виду изделия, классу компрессии, назначению&nbsp;и&nbsp;т.д.).</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m22.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 2-й</span></p>
							<p><span class="medi-color">Отфильтруйте</span> открывшийся каталог по&nbsp;параметрам, которые для&nbsp;вас важны.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m23.png" alt="">
							<p>Когда выберите нужны параметры, опуститесь в&nbsp;конец списка и&nbsp;нажмите на&nbsp;кнопку <span class="medi-color">Показать</span>.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m24.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 3-й</span></p>
							<p>Перейдите на&nbsp;страницу с&nbsp;подробным описанием изделия, нажав на&nbsp;его&nbsp;<span class="medi-color">фотографию</span>, <span class="medi-color">наименование</span> или&nbsp;кнопку <span class="medi-color">Подробнее</span>.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m25.png" alt="">
						</div>
					</div>
				</div>
				<div class="question-answer-wrap">
					<div class="question h3"><span class="ff-medium">Выбор параметров изделия на&nbsp;странице изделия</span>
						<div class="open-answer"><span class="hide-answer-text">Скрыть</span><span class="open-answer-text">Подробнее</span><div class="open-answer-btn"></div></div>
					</div>
					<div class="answer" style="display: none;">
						<div class="big-pic-medi">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 1-й</span></p>
							<p>Вы&nbsp;находитесь на&nbsp;странице с&nbsp;подробным описание изделия. Для&nbsp;того, чтобы определить, какие параметры изделия нужны именно вам, кликните по&nbsp;тексту <span class="medi-color">Подобрать&nbsp;размер</span>.</p>
							<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/31.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 2-й</span></p>
							<p>В&nbsp;открывшемся окне будут указаны мерки, которые нужно снять для&nbsp;определения требуемого размера и&nbsp;других параметров. Снимите мерки, следуя инструкции и&nbsp;запишите полученные результаты.</p>
							<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/32.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 3-й</span></p>
							<p>После снятия мерок закройте окно с&nbsp;инструкцией и,&nbsp;на&nbsp;основании полученных результатов, выберите все&nbsp;необходимые параметры изделия.</p>
							<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/33.png" alt="">
						</div>
						<div class="small-pic-medi">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 1-й</span></p>
							<p>Вы&nbsp;попали на&nbsp;страницу с&nbsp;подробным описанием изделия. Для&nbsp;того, чтобы определить, какие параметры изделия нужны именно вам, пролистайте страницу вниз и&nbsp;нажмите по&nbsp;выделенному тексту <span class="medi-color">подобрать&nbsp;размер</span>.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m31.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 2-й</span></p>
							<p>В&nbsp;открывшемся окне будут указаны мерки, которые нужно снять для&nbsp;определения требуемого размера и&nbsp;других параметров&nbsp;&ndash; это&nbsp;<span class="medi-color">Размерная сетка</span>. Снимите мерки, следуя инструкции и&nbsp;запишите полученные результаты. <span class="medi-clor">Размерную сетку</span> вы&nbsp;можете пролистывать влево-вправо чтобы открыть те&nbsp;ее&nbsp;части, которые не&nbsp;помещаются на&nbsp;одном экране.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m32.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 3-й</span></p>
							<p>После снятия мерок закройте окно с&nbsp;инструкцией и&nbsp;выберите все&nbsp;необходимые параметры изделия.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m33.png" alt="">
						</div>
					</div>
				</div>
				<div class="question-answer-wrap">
					<div class="question h3"><span class="ff-medium">Как добавить изделие в корзину</span>
						<div class="open-answer"><span class="hide-answer-text">Скрыть</span><span class="open-answer-text">Подробнее</span><div class="open-answer-btn"></div></div>
					</div>
					<div class="answer" style="display: none;">
						<div class="big-pic-medi">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 1-й</span></p>
							<p>Выбрав параметры изделия, добавьте его в&nbsp;корзину, кликнув по&nbsp;соответствующей кнопке в&nbsp;правой части экрана.</p>
							<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/41.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 2-й</span></p>
							<p>После добавления товара в&nbsp;корзину откроется окно с&nbsp;основной информацией о&nbsp;заказываемом изделии. Выберите нужное количество и&nbsp;кликните по&nbsp;кнопке <span class="span medi-color">Перейти в&nbsp;корзину</span>.</p>
							<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/42.png" alt="">
							<p>Перейти в&nbsp;корзину вы&nbsp;можете с&nbsp;любой страницы, нажав на&nbsp;иконку <span class="medi-color">корзины</span>&nbsp;<span><img style="height: 34px; width: auto; display: inline; transform: translateY(7px);" src="/upload/content/service/howtobuy/basket.png" alt=""></span> в&nbsp;шапке сайта.</p>
						</div>
						<div class="small-pic-medi">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 1-й</span></p>
							<p>Выбрав параметры для&nbsp;заказа на&nbsp;странице описания изделия, добавьте его в&nbsp;корзину, нажав по&nbsp;соответствующей кнопке в&nbsp;нижней части страницы.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m41.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 2-й</span></p>
							<p>После добавления товара в&nbsp;корзину кнопка поменяет цвет. Нажмите на&nbsp;нее еще&nbsp;раз, чтобы <span class="medi-color">перейти в&nbsp;корзину</span>.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m42.png" alt="">
							<p>Перейти в&nbsp;корзину вы&nbsp;можете с&nbsp;любой страницы, нажав на&nbsp;иконку <span class="medi-color">корзины</span> в&nbsp;шапке сайта.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m43.png" alt="">
						</div>
					</div>
				</div>
				<div class="question-answer-wrap">
					<div class="question h3"><span class="ff-medium">Как оформить заказ</span>
						<div class="open-answer"><span class="hide-answer-text">Скрыть</span><span class="open-answer-text">Подробнее</span><div class="open-answer-btn"></div></div>
					</div>
					<div class="answer" style="display: none;">
						<div class="big-pic-medi">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 1-й</span></p>
							<p>Находясь в&nbsp;корзине, кликните по&nbsp;кнопке <span class="ff-medium">Оформить заказ</span>. В&nbsp;заказ автоматически попадут те&nbsp;изделия, которые ранее вы&nbsp;добавили в&nbsp;корзину.</p>
							<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/51.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 2-й</span></p>
							<p>Вам нужно заполнить форму заказа. Сначала выберите <span class="medi-color">Город получения заказа</span> и&nbsp;кликните по&nbsp;кнопке <span class="medi-color">Далее</span></p>
							<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/52.png" alt="">
							<p id="select" class="h2 ff-medium"><span class="medi-color">Шаг 3-й</span></p>
							<p>Выберите способ доставки.<br>В&nbsp;зависимости от&nbsp;региона, в&nbsp;который доставляется заказ, вам могут быть доступны:</p>
							<div class="tabs-wrap">
								<select class="select tabs-links">
									<option disabled>Самовывоз из&nbsp;салонов medi</option>
									<option class="tab-link" value="Самовывоз из салонов medi">Самовывоз из&nbsp;салонов medi</option>
									<option class="tab-link" value="Самовывоз из салонов medi">Самовывоз из&nbsp;пунктов выдачи Boxberry</option>
									<option class="tab-link" value="Самовывоз из салонов medi">Самовывоз из&nbsp;пунктов выдачи CDEK</option>
									<option class="tab-link" value="Самовывоз из салонов medi">Доставка Почтой России</option>
									<option class="tab-link" value="Самовывоз из салонов medi">Курьерская доставка по&nbsp;городу или&nbsp;области</option>
								</select>
								<div class="tabs-content">
									<div class="tab-content active">
										<h2 class="h2 ff-medium" style="text-align: center;">Самовывоз из&nbsp;салонов medi</h2>
										<p>Поставьте галочку в&nbsp;соответствующем окошке и&nbsp;кликните по&nbsp;кнопке <span class="medi-color">Далее</span>.</p>
										<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/tabs/sam1.png" alt="">
										<p>Перед вами открылась карта с&nbsp;салонами, в&nbsp;которые может&nbsp;быть доставлен ваш&nbsp;заказ. Выберите подходящий вам пункт самовывоза кликнув на&nbsp;маркер, установленный на&nbsp;карте или&nbsp;из&nbsp;списка адресов, представленного ниже. После выбора салона переходите к&nbsp;следующему этапу оформления заказа, кликнув по&nbsp;кнопке <span class="medi-color">Далее</span>.</p>
										<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/tabs/sam2.png" alt="">
										<div class="btn-wrap" style="text-align: right;">
											<a href="#select" class="theme-link-dashed">Вернуться к&nbsp;списку Способов получения заказа</a>
										</div>
									</div>
									<div class="tab-content">
										<h2 class="h2 ff-medium" style="text-align: center;">Самовывоз из&nbsp;пунктов выдачи Boxberry</h2>
										<p>Поставьте галочку в&nbsp;соответствующем окошке и&nbsp;кликните по&nbsp;выделенному тексту <span class="medi-color">Выбрать пункт выдачи на&nbsp;карте</span>.</p>
										<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/tabs/bb1.png" alt="">
										<p>Перед вами откроется интерактивная карта с&nbsp;точками выдачи заказов, отмеченными маркерами. Выберите точку на&nbsp;интерактивной карте и&nbsp;нажмите на&nbsp;кнопку <span class="medi-color">Выбрать</span>, чтобы перейти к&nbsp;следующему шагу.</p>
										<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/tabs/bb2.png" alt="">
										<div class="btn-wrap" style="text-align: right;">
											<a href="#select" class="theme-link-dashed">Вернуться к&nbsp;списку Способов получения заказа</a>
										</div>
									</div>
									<div class="tab-content">
										<h2 class="h2 ff-medium" style="text-align: center;">Самовывоз из&nbsp;пунктов выдачи заказов CDEK</h2>
										<p>Поставьте галочку в&nbsp;соответствующем окошке кликните по&nbsp;кнопке <span class="medi-color">Выбрать пункт самовывоза</span>.</p>
										<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/tabs/cdek1.png" alt="">
										<p>Поставьте галочку в&nbsp;соответствующем окошке кликните по&nbsp;кнопке <span class="medi-color">Выбрать пункт самовывоза</span>.</p>
										<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/tabs/cdek1.png" alt="">
										<div class="btn-wrap" style="text-align: right;">
											<a href="#select" class="theme-link-dashed">Вернуться к&nbsp;списку Способов получения заказа</a>
										</div>
									</div>
									<div class="tab-content">
										<h2 class="h2 ff-medium" style="text-align: center;">Доставка Почтой России</h2>
										<p>Поставьте галочку в&nbsp;соответствующем окошке кликните по&nbsp;кнопке <span class="medi-color">Далее</span>.</p>
										<p>Сроки и&nbsp;стоимость доставки рассчитываются при&nbsp;оформлении заказа и&nbsp;напрямую зависят от&nbsp;географии, суммы и&nbsp;веса покупки.</p>
										<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/tabs/pr1.png" alt="">
										<div class="btn-wrap" style="text-align: right;">
											<a href="#select" class="theme-link-dashed">Вернуться к&nbsp;списку Способов получения заказа</a>
										</div>
									</div>
									<div class="tab-content">
										<h2 class="h2 ff-medium" style="text-align: center;">Доставка курьером</h2>
										<p>Поставьте галочку в&nbsp;соответствующем окошке и&nbsp;кликните по&nbsp;кнопке <span class="medi-color">Далее</span>. Курьерская доставка осуществляется специалистами medi или&nbsp;курьерской службой CDEK. Адрес доставки вам нужно будет вписать в&nbsp;соответствующее поле при&nbsp;указании <span class="medi-color">Данных получателя</span>.</p>
										<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/tabs/kur1.png" alt="">
										<div class="btn-wrap" style="text-align: right;">
											<a href="#select" class="theme-link-dashed">Вернуться к&nbsp;списку Способов получения заказа</a>
										</div>
									</div>
								</div>
							</div>
							<p class="h2 ff-medium"><span class="medi-color">Шаг 4-й</span></p>
							<p>Выберите способ оплаты заказа и&nbsp;кликните по&nbsp;кнопке <span class="medi-color">Далее</span>.</p>
							<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/54.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 5-й</span></p>
							<p>Введите свои контактные данные в&nbsp;поля формы. Обязательными полями являются ФИО и&nbsp;Телефон, остальную информацию можно будет уточнить устно специалисту контактного центра при&nbsp;подтверждении заказа по&nbsp;телефону. Тем&nbsp;не&nbsp;менее, для&nbsp;уверенности в&nbsp;правильности предоставленной информации, мы&nbsp;рекомендуем заполнять все&nbsp;поля самостоятельно.</p>
							<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/55.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 6-й</span></p>
							<p>Вы&nbsp;корректно заполнили все&nbsp;поля формы. Теперь поставьте галочку в&nbsp;поле согласия с&nbsp;«Политикой обработки персональных данных» и&nbsp;нажмите на&nbsp;кнопку <span class="medi-color">Оформить заказ</span>.</p>
							<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/56.png" alt="">
							<p>Ваш заказ сформирован. Ожидайте звонка от&nbsp;нашего специалиста для&nbsp;уточнения деталей и&nbsp;подтверждения заказа.</p>
						</div>
						<div class="small-pic-medi">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 1-й</span></p>
							<p>Находясь в корзине, пролистайте страницу вниз и нажмите на кнопку <span class="medi-color">Оформить заказ</span>. В заказ автоматически попадут все изделия, добавленные в корзину.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m51.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 2-й</span></p>
							<p>Вам нужно заполнить форму заказа.<br>Выберите <span class="medi-color">Город получения заказа</span> и нажмите на кнопку <span class="medi-color">Далее</span>.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m52.png" alt="">
							<p id="mselect" class="h2 ff-medium"><span class="medi-color">Шаг 3-й</span></p>
							<p>Выберите способ доставки. В зависимости от региона, в который доставляется заказ, вам могут быть доступны:</p>
							<div class="tabs-wrap">
								<select class="select tabs-links">
									<option disabled>Самовывоз из&nbsp;салонов medi</option>
									<option class="tab-link" value="Самовывоз из салонов medi">Самовывоз из&nbsp;салонов medi</option>
									<option class="tab-link" value="Самовывоз из салонов medi">Самовывоз из&nbsp;пунктов выдачи Boxberry</option>
									<option class="tab-link" value="Самовывоз из салонов medi">Самовывоз из&nbsp;пунктов выдачи CDEK</option>
									<option class="tab-link" value="Самовывоз из салонов medi">Доставка Почтой России</option>
									<option class="tab-link" value="Самовывоз из салонов medi">Курьерская доставка по&nbsp;городу или&nbsp;области</option>
								</select>
								<div class="tabs-content">
									<div class="tab-content active">
										<h2 class="h2 ff-medium" style="text-align: center;">Самовывоз из&nbsp;салонов medi</h2>
										<p>Поставьте галочку в&nbsp;соответствующем окошке и&nbsp;кликните по&nbsp;кнопке <span class="medi-color">Далее</span>.</p>
										<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/tabs/msam1.png" alt="">
										<p>Перед вами открылась карта с&nbsp;салонами, в&nbsp;которые может&nbsp;быть доставлен ваш&nbsp;заказ. Выберите подходящий вам пункт самовывоза кликнув на&nbsp;маркер, установленный на&nbsp;карте или&nbsp;из&nbsp;списка адресов, представленного ниже. После выбора салона переходите к&nbsp;следующему этапу оформления заказа, кликнув по&nbsp;кнопке <span class="medi-color">Далее</span>.</p>
										<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/tabs/msam2.png" alt="">
										<div class="btn-wrap" style="text-align: right;">
											<a href="#mselect" class="theme-link-dashed">Вернуться к&nbsp;списку Способов получения заказа</a>
										</div>
									</div>
									<div class="tab-content">
										<h2 class="h2 ff-medium" style="text-align: center;">Самовывоз из&nbsp;пунктов выдачи Boxberry</h2>
										<p>Поставьте галочку в&nbsp;соответствующем окошке и&nbsp;кликните по&nbsp;выделенному тексту <span class="medi-color">Выбрать пункт выдачи на&nbsp;карте</span>.</p>
										<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/tabs/mbb1.png" alt="">
										<p>Перед вами откроется интерактивная карта с&nbsp;точками выдачи заказов, отмеченными маркерами. Выберите точку на&nbsp;интерактивной карте и&nbsp;нажмите на&nbsp;кнопку <span class="medi-color">Выбрать</span>, чтобы перейти к&nbsp;следующему шагу.</p>
										<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/tabs/mbb2.png" alt="">
										<div class="btn-wrap" style="text-align: right;">
											<a href="#mselect" class="theme-link-dashed">Вернуться к&nbsp;списку Способов получения заказа</a>
										</div>
									</div>
									<div class="tab-content">
										<h2 class="h2 ff-medium" style="text-align: center;">Самовывоз из&nbsp;пунктов выдачи заказов CDEK</h2>
										<p>Поставьте галочку в&nbsp;соответствующем окошке кликните по&nbsp;кнопке <span class="medi-color">Выбрать пункт самовывоза</span>.</p>
										<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/tabs/mcdek1.png" alt="">
										<p>Поставьте галочку в&nbsp;соответствующем окошке кликните по&nbsp;кнопке <span class="medi-color">Выбрать пункт самовывоза</span>.</p>
										<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/tabs/mcdek1.png" alt="">
										<div class="btn-wrap" style="text-align: right;">
											<a href="#mselect" class="theme-link-dashed">Вернуться к&nbsp;списку Способов получения заказа</a>
										</div>
									</div>
									<div class="tab-content">
										<h2 class="h2 ff-medium" style="text-align: center;">Доставка Почтой России</h2>
										<p>Поставьте галочку в&nbsp;соответствующем окошке кликните по&nbsp;кнопке <span class="medi-color">Далее</span>.</p>
										<p>Сроки и&nbsp;стоимость доставки рассчитываются при&nbsp;оформлении заказа и&nbsp;напрямую зависят от&nbsp;географии, суммы и&nbsp;веса покупки.</p>
										<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/tabs/mpr1.png" alt="">
										<div class="btn-wrap" style="text-align: right;">
											<a href="#mselect" class="theme-link-dashed">Вернуться к&nbsp;списку Способов получения заказа</a>
										</div>
									</div>
									<div class="tab-content">
										<h2 class="h2 ff-medium" style="text-align: center;">Доставка курьером</h2>
										<p>Поставьте галочку в&nbsp;соответствующем окошке и&nbsp;кликните по&nbsp;кнопке <span class="medi-color">Далее</span>. Курьерская доставка осуществляется специалистами medi или&nbsp;курьерской службой CDEK. Адрес доставки вам нужно будет вписать в&nbsp;соответствующее поле при&nbsp;указании <span class="medi-color">Данных получателя</span>.</p>
										<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/tabs/mkur1.png" alt="">
										<div class="btn-wrap" style="text-align: right;">
											<a href="#mselect" class="theme-link-dashed">Вернуться к&nbsp;списку Способов получения заказа</a>
										</div>
									</div>
								</div>
							</div>
							<p class="h2 ff-medium"><span class="medi-color">Шаг 4-й</span></p>
							<p>Выберите способ оплаты заказа и&nbsp;кликните по&nbsp;кнопке <span class="medi-color">Далее</span>.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m54.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 5-й</span></p>
							<p>Введите свои контактные данные в&nbsp;поля формы. Обязательными полями являются ФИО и&nbsp;Телефон, остальную информацию можно будет уточнить устно специалисту контактного центра при&nbsp;подтверждении заказа по&nbsp;телефону. Тем&nbsp;не&nbsp;менее, для&nbsp;уверенности в&nbsp;правильности предоставленной информации, мы&nbsp;рекомендуем заполнять все&nbsp;поля самостоятельно.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m55.png" alt="">
							<p class="h2 ff-medium"><span class="medi-color">Шаг 6-й</span></p>
							<p>Вы&nbsp;корректно заполнили все&nbsp;поля формы. Теперь поставьте галочку в&nbsp;поле согласия с&nbsp;«Политикой обработки персональных данных» и&nbsp;нажмите на&nbsp;кнопку <span class="medi-color">Оформить заказ</span>.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m56.png" alt="">
							<p>Ваш заказ сформирован. Ожидайте звонка от&nbsp;нашего специалиста для&nbsp;уточнения деталей и&nbsp;подтверждения заказа.</p>

						</div>
					</div>
				</div>
				<div class="question-answer-wrap">
					<div class="question h3"><span class="ff-medium">Как сделать Быстрый заказ</span>
						<div class="open-answer"><span class="hide-answer-text">Скрыть</span><span class="open-answer-text">Подробнее</span><div class="open-answer-btn"></div></div>
					</div>
					<div class="answer" style="display: none;">
						<div class="big-pic-medi">
							<p>Вы&nbsp;также можете воспользоваться функцией <span class="medi-color">Быстрый заказ</span>, кликнув по&nbsp;соответствующей кнопке на&nbsp;странице с&nbsp;подробным описанием изделия.</p>
							<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/61.png" alt="">
							<p>В&nbsp;открывшемся окне заполните короткую форму, поставьте галочку в&nbsp;поле согласия с&nbsp;Политикой обработки персональных данных и&nbsp;нажмите на&nbsp;кнопку <span class="medi-color">Заказать</span>. Наш специалист свяжется с&nbsp;вами и&nbsp;уточнит все&nbsp;параметры заказа по&nbsp;телефону.</p>
							<img width="100%" class="big-pic-medi" src="/upload/content/service/howtobuy/62.png" alt="">
						</div>
						<div class="small-pic-medi">
							<p>Вы&nbsp;также можете воспользоваться функцией <span class="medi-color">Быстрый заказ</span>, нажав по&nbsp;соответствующей кнопке на&nbsp;странице с&nbsp;подробным описанием изделия.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m61.png" alt="">
							<p>В&nbsp;открывшемся окне заполните короткую форму, поставьте галочку в&nbsp;поле согласия с&nbsp;«Политикой в&nbsp;отношении обработки персональных данных» и&nbsp;нажмите на&nbsp;кнопку <span class="medi-color">Заказать</span>. Наш&nbsp;специалист свяжется с&nbsp;вами и&nbsp;уточнит все&nbsp;параметры заказа по&nbsp;телефону.</p>
							<img width="100%" class="small-pic-medi" src="/upload/content/service/howtobuy/m62.png" alt="">
						</div>
					</div>
				</div>
			</div>
			<h2 class="h2 ff-medium" style="text-align: center;">Желаем вам <span class="medi-color">приятных</span> покупок!</h2>
			<div class="btn-wrap" style="text-align: center;">
				<a href="/catalog/" class="btn-simple btn-medium">Перейти в каталог</a>
			</div>
		</div>
	</div>
</div>
<br>
<script>
	$('.new-select').click( function() {
		$(this).siblings(".new-select__list").slideToggle("slow");
		return false;
	});
	$('.select').each(function() {
    const _this = $(this),
        selectOption = _this.find('option'),
        selectOptionLength = selectOption.length,
        selectedOption = selectOption.filter(':selected'),
        duration = 450; // длительность анимации

    _this.hide();
    _this.wrap('<div class="select"></div>');
    $('<div>', {
        class: 'new-select',
        text: _this.children('option:disabled').text()
    }).insertAfter(_this);

    const selectHead = _this.next('.new-select');
    $('<div>', {
        class: 'new-select__list tabs-links'
    }).insertAfter(selectHead);

    const selectList = selectHead.next('.new-select__list');
    for (let i = 1; i < selectOptionLength; i++) {
        $('<div>', {
            class: 'new-select__item tab-link',
            html: $('<span>', {
                text: selectOption.eq(i).text()
            })
        })
        .attr('data-value', selectOption.eq(i).val())
        .appendTo(selectList);
    }

    const selectItem = selectList.find('.new-select__item');
    selectList.slideUp(0);
    selectHead.on('click', function() {
        if ( !$(this).hasClass('on') ) {
            $(this).addClass('on');
            selectList.slideDown(duration);

            selectItem.on('click', function() {
                let chooseItem = $(this).data('value');

                $('select').val(chooseItem).attr('selected', 'selected');
                selectHead.text( $(this).find('span').text() );

                selectList.slideUp(duration);
                selectHead.removeClass('on');
            });

        } else {
            $(this).removeClass('on');
            selectList.slideUp(duration);
        }
    });
	$(document).click( function(e){
		if ( $(e.target).closest('.new-select').length ) {
			// клик внутри элемента
			return;
		}
		// клик снаружи элемента
		selectList.slideUp(duration);
		selectHead.removeClass('on');
	});
});

</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [{
    "@type": "Question",
    "name": "Как найти изделие в поиске по каталогу",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Шаг 1-й
Кликните по полю \"Поиск по каталогу\", которое находится на любой странице сайта в верхней части экрана.
Шаг 2-й
Введите в открывшуюся поисковую строку один из искомых параметров: артикул, вид изделия (например, «чулки» или «бандаж»), класс компрессии (например, «1 класс» или «2 класс»), модельный ряд (например, «mediven elegance» или «mediven thrombexin»). Поисковая система подберет для вас изделия, максимально релевантные вашему запросу.
Шаг 3-й
Кликните по подходящему варианту в окне Результаты поиска. Кликнув по фотографии или наименованию изделия, вы сразу попадете на страницу с его подробным описанием. Вы можете посмотреть все результаты поиска, кликнув по ссылке Смотреть все результаты. Перейдя по этой ссылке, вы попадете в Каталог, отфильтрованный с учетом вашего поискового запроса."
    }
  },{
    "@type": "Question",
    "name": "Как подобрать изделие по каталогу",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Шаг 1-й
Наведите курсор на наименование одного из разделов каталога. В выпавшем списке выберите основной параметр, по которому хотите отсортировать каталог (вид изделия, класс компрессии, назначение и т.д.)
Шаг 2-й
Отфильтруйте открывшийся каталог по параметрам, которые для вас важны.
Шаг 3-й
Перейдите на страницу с подробным описанием изделия, кликнув на его фотографию или наименование."
    }
  },{
    "@type": "Question",
    "name": "Выбор параметров изделия на странице изделия",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Шаг 1-й
Вы находитесь на странице с подробным описание изделия. Для того, чтобы определить, какие параметры изделия нужны именно вам, кликните по тексту Подобрать размер.
Шаг 2-й
В открывшемся окне будут указаны мерки, которые нужно снять для определения требуемого размера и других параметров. Снимите мерки, следуя инструкции и запишите полученные результаты.
Шаг 3-й
После снятия мерок закройте окно с инструкцией и, на основании полученных результатов, выберите все необходимые параметры изделия."
    }
  },{
    "@type": "Question",
    "name": "Как добавить изделие в корзину",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Шаг 1-й
Выбрав параметры изделия, добавьте его в корзину, кликнув по соответствующей кнопке в правой части экрана.
Шаг 2-й
После добавления товара в корзину откроется окно с основной информацией о заказываемом изделии. Выберите нужное количество и кликните по кнопке Перейти в корзину.
Перейти в корзину вы можете с любой страницы, нажав на иконку корзины  в шапке сайта."
    }
  },{
    "@type": "Question",
    "name": "Как оформить заказ",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Шаг 1-й
Находясь в корзине, кликните по кнопке Оформить заказ. В заказ автоматически попадут те изделия, которые ранее вы добавили в корзину.
Шаг 2-й
Вам нужно заполнить форму заказа. Сначала выберите Город получения заказа и кликните по кнопке Далее
Шаг 3-й
Выберите способ доставки.
В зависимости от региона, в который доставляется заказ, вам могут быть доступны:
Самовывоз из салонов medi
Самовывоз из пунктов выдачи Boxberry
Самовывоз из пунктов выдачи заказов CDEK
Доставка Почтой России
Доставка курьером
Шаг 4-й
Выберите способ оплаты заказа и кликните по кнопке Далее.
Шаг 5-й
Введите свои контактные данные в поля формы. Обязательными полями являются ФИО и Телефон, остальную информацию можно будет уточнить устно специалисту контактного центра при подтверждении заказа по телефону. Тем не менее, для уверенности в правильности предоставленной информации, мы рекомендуем заполнять все поля самостоятельно.
Шаг 6-й
Вы корректно заполнили все поля формы. Теперь поставьте галочку в поле согласия с «Политикой обработки персональных данных» и нажмите на кнопку Оформить заказ.
Ваш заказ сформирован. Ожидайте звонка от нашего специалиста для уточнения деталей и подтверждения заказа."
    }
  },{
    "@type": "Question",
    "name": "Как сделать Быстрый заказ",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Вы также можете воспользоваться функцией Быстрый заказ, кликнув по соответствующей кнопке на странице с подробным описанием изделия.
	  В открывшемся окне заполните короткую форму, поставьте галочку в поле согласия с Политикой обработки персональных данных и нажмите на кнопку Заказать. Наш специалист свяжется с вами и уточнит все параметры заказа по телефону."
    }
  }]
}
</script>
<script>
    var _gcTracker=_gcTracker||[];
    _gcTracker.push(['view_page', { name: 'view_howtobuy'}]);
</script>








<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>