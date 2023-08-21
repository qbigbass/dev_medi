<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Доставка");
?><h1>Доставка покупок в Ростове-на-Дону</h1>
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
		<style>
			.tabs-links {
				margin-bottom: 40px;
			}
			.flex> .tab-dostavka {
				width: 20%;
				border: 1px solid #e9edf0;
				text-align: center;
				padding: 20px;
			}
			.tab-dostavka.active {
				border: 1px solid #e20074;
			}
			.tab-dostavka:hover {
				border: 1px solid #e20074;
				cursor: pointer;
				transition: 0.3s;
			}
			.tab-dostavka img {
				height: 100px;
			}
			.active .h3 {
				font-family: "robotomedium";
			}
			.flex-item {
				border: none!important;
			}
			#mechanic .flex-item {
				width: 50%;
				padding: 0;
				position: relative;
				overflow: hidden;
				flex-grow: 3;
			}
			#mechanic .flex-item table {
				width: 100%;
				border-collapse: collapse;
			}
			#mechanic .flex-item tr:first-child td {
				border-bottom: 2px solid #cdcdcd;
			}
			#mechanic .flex-item tr td {
				padding: 10px 5%;
			}
			#mechanic .flex-item:nth-child(odd) tr:first-child td::before {
				position: absolute;
				top: 0;
				left: 0;
				height: 200px;
				border-right: 2px solid #cdcdcd;
				content: "";
				width: calc(100% - 2px);
			}
			@media screen and (max-width: 1023px) {
				#mechanic .flex-item {
					width: 100%;
				}
				#mechanic .flex-item:nth-child(odd) tr:first-child td::before {
					display: none;
				}
				#mechanic .flex-item tr:first-child td {
					border: none;
				}
				#mechanic .flex-item:first-child {
					border-bottom: 2px solid #cdcdcd!important;
				}
				.flex> .tab-dostavka {
					width: 40%!important;
					margin-bottom: 20px;
				}
				.tab-dostavka img {
					height: 80px;
				}
			}
			@media screen and (max-width: 750px) {
				.flex> .tab-dostavka {
					padding: 20px 10px 10px 10px;
				}
				.tab-dostavka img {
					height: 50px;
				}
				.tab-dostavka .h3 {
					font-size: 15px;
				}
				.tabs-links {
					margin-bottom: 20px;
				}
			}
		</style>
		<div class="tabs-wrap">
			<div class="tabs-links flex">
				<div class="tab-link tab-dostavka active">
					<img src="/upload/content/delivery/1.svg" alt="">
					<p class="h3">Доставка курьером СДЭК и&nbsp;Boxberry</p>
				</div>
				<!-- <div class="tab-link tab-dostavka">
					<img src="/upload/content/delivery/2.svg" alt="">
					<p class="h3">Самовывоз из ортопедических салонов medi</p>
				</div> -->
				<div class="tab-link tab-dostavka">
					<img src="/upload/content/delivery/3.svg" alt="">
					<p class="h3">Пункты выдачи заказов СДЭК и&nbsp;Boxberry</p>
				</div>
				<div class="tab-link tab-dostavka">
					<img src="/upload/content/delivery/5.svg" alt="">
					<p class="h3">Почта России</p>
				</div>
			</div>

			<p class="ff-medium medi-color" style="text-align:center">В связи с повышенным спросом в Новогодние праздники и высокой загрузкой транспортных компаний и курьеров срок доставки увеличен</p>
			<div class="tabs-content">
				<div class="tab-content active">
					<div class="light-bg">
						<div id="mechanic" class="flex">
							<div class="flex-item">
								<table>
									<tr>
										<td>
											<p class="h2 ff-medium">Сколько стоит?</p>
										</td>
									</tr>
									<tr>
										<td>
											<p>Вы&nbsp;сможете выбрать курьерскую службу в&nbsp;процессе оформления заказа. Курьер доставит ваш заказ по&nbsp;указанному адресу в&nbsp;удобное для вас время.</p> <p>Стоимость доставки зависит от&nbsp;веса заказанных товаров и&nbsp;от&nbsp;региона, и&nbsp;будет рассчитана автоматически в&nbsp;процессе оформления заказа.</p>
										</td>
									</tr>
								</table>
							</div>
							<div class="flex-item">
								<table>
									<tr>
										<td>
											<p class="h2 ff-medium">Когда?</p>
										</td>
									</tr>
									<tr>
										<td>
											<p>Срок доставки будет рассчитан автоматически в&nbsp;процессе оформления заказа.</p>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
				<!-- <div class="tab-content">
					<div class="light-bg">
						<div id="mechanic" class="flex">
							<div class="flex-item">
								<table>
									<tr>
										<td>
											<p class="h2 ff-medium">Сколько стоит?</p>

										</td>
									</tr>
									<tr>
										<td>
											<p class="medi-color ff-medium">Бесплатно</p>
										</td>
									</tr>
								</table>
							</div>
							<div class="flex-item">
								<table>
									<tr>
										<td>
											<p class="h2 ff-medium">Когда?</p>
										</td>
									</tr>
									<tr>
										<td>
											<p>Изделие поступает в&nbsp;салон в&nbsp;течение <nobr class="ff-medium medi-color">1&ndash;3&nbsp;дней</nobr>. При&nbsp;поступлении приходит смс&ndash;уведомление.</p>
											<p>Изделие необходимо забрать в&nbsp;течение 3&nbsp;дней с&nbsp;момента поступления в&nbsp;салон.</p>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div> -->
				<div class="tab-content">
					<div class="light-bg">
						<div id="mechanic" class="flex">
							<div class="flex-item">
								<table>
									<tr>
										<td>
											<p class="h2 ff-medium">Сколько стоит?</p>
										</td>
									</tr>
									<tr>
										<td>
											<p>Стоимость доставки зависит от&nbsp;веса заказанных товаров и&nbsp;от&nbsp;региона, и&nbsp;будет рассчитана автоматически в&nbsp;процессе <span class="medi-color">оформления заказа</span>.</p>
										</td>
									</tr>
								</table>
							</div>
							<div class="flex-item">
								<table>
									<tr>
										<td>
											<p class="h2 ff-medium">Когда?</p>
										</td>
									</tr>
									<tr>
										<td>
											<p>Срок доставки будет рассчитан автоматически в&nbsp;процессе оформления заказа.</p>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-content">
					<div class="light-bg">
						<div id="mechanic" class="flex">
							<div class="flex-item">
								<table>
									<tr>
										<td>
											<p class="h2 ff-medium">Сколько стоит?</p>
										</td>
									</tr>
									<tr>
										<td>
											<p>Вы&nbsp;сможете получить заказ в&nbsp;отделении Почты России. Выбрать удобное отделение можно будет в&nbsp;процессе оформления заказа.</p>
											<p>Стоимость доставки зависит от&nbsp;веса заказанных товаров и&nbsp;от&nbsp;региона, и&nbsp;будет рассчитана автоматически в&nbsp;процессе оформления заказа.</p>
										</td>
									</tr>
								</table>
							</div>
							<div class="flex-item">
								<table>
									<tr>
										<td>
											<p class="h2 ff-medium">Когда?</p>
										</td>
									</tr>
									<tr>
										<td>
											<p>Срок доставки будет рассчитан автоматически в&nbsp;процессе оформления заказа.</p>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
