<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Способы и условия доставки");
?><h1>Способы и&nbsp;условия доставки по&nbsp;Москве и&nbsp;Московской области</h1>
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
		"ROOT_MENU_TYPE" => "service",	// Тип меню для первого уровня
		"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
	)
);?>
<div class="global-block-container">
	<div class="global-content-block">
		 <style>
			.tabs-links {
				margin-bottom: 40px;
			}
			.flex> .tab-dostavka {
				width: 16%;
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
			.flex .flex-item {
    border: none;
}
			#mechanic .flex-item {
				width: 50%;
				padding: 0;
				
				overflow: hidden;
				flex-grow: 3;
			}
			#mechanic .flex-item table {
				width: 100%;
				border-collapse: collapse;
			}
#mechanic .flex-item:first-child {
    border-right: 2px solid #cdcdcd;
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
				/*height: 200px;*/
				border-right: 2px solid #cdcdcd;
				content: "";
				width: calc(100% - 2px);
			}
			@media screen and (max-width: 1023px) {
#mechanic .flex-item:first-child {
    border-right: none;
 border-bottom: 2px solid #cdcdcd!important;
}
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
					<p class="h3">
						 Доставка курьером
					</p>
				</div>
				<div class="tab-link tab-dostavka">
 <img src="/upload/content/delivery/2.svg" alt="">
					<p class="h3">
						 Самовывоз из ортопедических салонов medi
					</p>
				</div>
				<div class="tab-link tab-dostavka">
 <img src="/upload/content/delivery/3.svg" alt="">
					<p class="h3">
						 Пункты выдачи заказов СДЭК и Boxberry или Курьером до двери СДЭК/Boxberry
					</p>
				</div>
        <div class="tab-link tab-dostavka">
 <img src="/upload/content/delivery/5.svg" alt="">
					<p class="h3">
						 Почта России
					</p>
				</div>
				<div class="tab-link tab-dostavka">
 <img src="/upload/content/delivery/4.svg" alt="">
					<p class="h3">
						 Выезд специалиста по ортезированию
					</p>
				</div>
			</div>
			 <?if (defined("delivery_attention")){?>
			<div class="attention">
 <span class="ff-medium medi-color"><?=delivery_attention?></span>
			</div>
			 <?}?>
			<div class="tabs-content">
				<div class="tab-content active">
					<div class="light-bg">
						<div id="mechanic" class="flex">
							<div class="flex-item">
								<table>
								<tbody>
								<tr>
									<td>
										<p class="h2 ff-medium">
											 Сколько стоит?
										</p>
									</td>
								</tr>
								<tr>
									<td>
										<p class="ff-medium">
											 Доставка по&nbsp;Москве и МО (до 20км от МКАД):
										</p>
										<p>
											 –&nbsp;<span class="medi-color">Бесплатная доставка</span>&nbsp;– при&nbsp;сумме покупки свыше 1&nbsp;000&nbsp;рублей;<br>
											 –&nbsp;<span class="medi-color">250&nbsp;рублей</span>&nbsp;– если сумма вашей покупки менее 1&nbsp;000&nbsp;рублей.
										</p>
										
										
										
										<p class="ff-medium">
											 Максимальное количество обуви для доставки курьером — 3 пары.
										</p>
<p class="ff-medium">
											 В случае отказа от выкупа обуви, покупателем оплачивается возмещение расходов по доставке в размере 250 рублей.
										</p>
									</td>
								</tr>
								</tbody>
								</table>
							</div>
							<div class="flex-item">
								<table>
								<tbody>
								<tr>
									<td>
										<p class="h2 ff-medium">
											 Когда?
										</p>
									</td>
								</tr>
								<tr>
									<td>
										<p>
											 Бесплатная доставка по Москве и МО (до 20км от МКАД) осуществляется течение 1-3 дней.<br><br>
 <span class="medi-color">Ежедневно и&nbsp;без&nbsp;выходных.</span>
										</p>
									</td>
								</tr>
								</tbody>
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
								<tbody>
								<tr>
									<td>
										<p class="h2 ff-medium">
											 Сколько стоит?
										</p>
									</td>
								</tr>
								<tr>
									<td>
										<p class="medi-color ff-medium">
											 Бесплатно
										</p>
									</td>
								</tr>
								</tbody>
								</table>
							</div>
							<div class="flex-item">
								<table>
								<tbody>
								<tr>
									<td>
										<p class="h2 ff-medium">
											 Когда?
										</p>
									</td>
								</tr>
								<tr>
									<td>
										<p>
											 Изделие поступает в&nbsp;салон в&nbsp;течение <nobr class="ff-medium medi-color">1–3&nbsp;дней</nobr>. При&nbsp;поступлении приходит смс–уведомление.
										</p>
										<p>
											 Изделие необходимо забрать в&nbsp;течение 3&nbsp;дней с&nbsp;момента поступления в&nbsp;салон.
										</p>
									</td>
								</tr>
								</tbody>
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
								<tbody>
								<tr>
									<td>
										<p class="h2 ff-medium">
											 Сколько стоит?
										</p>
									</td>
								</tr>
								<tr>
									<td>
<p>
											 –&nbsp;<span class="medi-color">Бесплатная доставка</span>&nbsp;– при&nbsp;сумме покупки свыше 4&nbsp;490&nbsp;рублей;<br>
											 –&nbsp;<span class="medi-color">Стоимость доставки рассчитывается <span class="medi-color">при&nbsp;оформлении заказа.</span></span>&nbsp;– если сумма вашей покупки менее 4&nbsp;490&nbsp;рублей.
										</p>
										
									</td>
								</tr>
								</tbody>
								</table>
							</div>
							<div class="flex-item">
								<table>
								<tbody>
								<tr>
									<td>
										<p class="h2 ff-medium">
											 Когда?
										</p>
									</td>
								</tr>
								<tr>
									<td>
										<p>
											 Время доставки зависит от удаленности адреса доставки от Москвы.
										</p>
<p>
											Примерный срок доставки рассчитается при оформлении заказа на сайте, после внесения адреса доставки и выбора способа доставки. В дальнейшем, клиент может самостоятельно отследить движение посылки на сайте транспортной компании.
										</p>
									</td>
								</tr>
								</tbody>
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
								<tbody>
								<tr>
									<td>
										<p class="h2 ff-medium">
											 Сколько стоит?
										</p>
									</td>
								</tr>
								<tr>
									<td>
<p>
										
											 –&nbsp;<span class="medi-color">Стоимость доставки рассчитывается при&nbsp;оформлении заказа.</span>
										</p>
										
									</td>
								</tr>
								</tbody>
								</table>
							</div>
							<div class="flex-item">
								<table>
								<tbody>
								<tr>
									<td>
										<p class="h2 ff-medium">
											 Когда?
										</p>
									</td>
								</tr>
								<tr>
									<td>
										<p>
											 Время доставки зависит от удаленности адреса доставки от Москвы.
										</p>
<p>
											Примерный срок доставки рассчитается при оформлении заказа на сайте, после внесения адреса доставки и выбора способа доставки. В дальнейшем, клиент может самостоятельно отследить движение посылки на сайте транспортной компании.
										</p>
									</td>
								</tr>
								</tbody>
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
								<tbody>
								<tr>
									<td>
										<p class="h2 ff-medium">
											 Сколько стоит?
										</p>
									</td>
								</tr>
								<tr>
									<td>
										<p>
											 Стоимость услуги&nbsp;– <span class="medi-color">700&nbsp;рублей.</span>
										</p>
										<p>
											 При&nbsp;приобретении ортопедических изделий&nbsp;– <span class="medi-color">бесплатно.</span>
										</p>
										<p>
											 Покупатели из&nbsp;Подмосковья дополнительно оплачивают выезд за&nbsp;МКАД:<br>
											 –&nbsp;до&nbsp;20&nbsp;км от&nbsp;МКАД&nbsp;– <span class="medi-color">300&nbsp;рублей</span><br>
											 –&nbsp;до&nbsp;40&nbsp;км от&nbsp;МКАД&nbsp;– <span class="medi-color">400&nbsp;рублей</span><br>
											 –&nbsp;до&nbsp;60&nbsp;км от&nbsp;МКАД&nbsp;– <span class="medi-color">500&nbsp;рублей</span>
										</p>
									</td>
								</tr>
								</tbody>
								</table>
							</div>
							<div class="flex-item">
								<table>
								<tbody>
								<tr>
									<td>
										<p class="h2 ff-medium">
											 Когда?
										</p>
									</td>
								</tr>
								<tr>
									<td>
										<p>
В рабочие дни с 11:00-18:00.
										</p>
<p>
- при подтверждении заказа со специалистом КЦ до 9:30 утра, возможно исполнение день-в-день;
										</p>
</p>
<p>
- при подтверждении заказа после 9:30 текущего дня, на следующий будний день;
										</p>
									</td>
								</tr>
								</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>