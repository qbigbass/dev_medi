<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Гарантийные обязательства");
?><h1>Гарантийные обязательства</h1>
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
<style>
	.flex-item-picture {
		width: 200px;
		margin: 20px 0!important;
		padding: 0!important;
	}
	.flex-item-picture img {
		width: 100%;
	}
	@media screen and (min-width: 960px) {
		.pic-text-flex {
			align-items: flex-start;
		}
		.flex-item-text {
			width: calc(100% - 220px);
			margin-left: 20px;
		}
		.light-bg, .white-bg {
			padding: 20px!important;
		}
	}
</style>
<div class="global-block-container">
	<div class="global-content-block">
		<div class="detail-text-wrap">
			<div class="light-bg">
				<h2 class="h2 ff-medium" style="text-align: center;">Гарантийный срок продукции <span class="medi-color">medi</span></h2>
				<p><span class="ff-medium">Гарантийный срок начинается в&nbsp;день покупки и&nbsp;требует соблюдения покупателем правил,</span> изложенных в&nbsp;инструкции по&nbsp;использованию и&nbsp;уходу, прилагаемой к&nbsp;каждому изделию.</p>
				<p><span class="ff-medium">Гарантийный срок равен:</span><br>
				&ndash;&nbsp;<span class="medi-color ff-medium">6&nbsp;месяцам</span> для&nbsp;лечебного трикотажа и&nbsp;компрессионных бандажей (с&nbsp;сохранением компрессионных свойств);<br>
				&ndash;&nbsp;<span class="medi-color ff-medium">12&nbsp;месяцам</span> для&nbsp;регулируемых ортезов с&nbsp;шарнирами на&nbsp;металлические части и&nbsp;6&nbsp;месяцам на&nbsp;тканевые и&nbsp;полимерные элементы.</p>
				<p>В&nbsp;случае утраты изделиями заявленных свойств до&nbsp;истечения этого срока из-за производственных дефектов (при&nbsp;условии подтверждения экспертом компании-продавца или&nbsp;независимой экспертизой), компания medi гарантирует возврат денежных средств.</p>
			</div>
			<div class="white-bg">
				<h2 class="h2 ff-medium" style="text-align: center;">Сертификаты и&nbsp;стандарты, подтверждающие качество продукции <span class="medi-color">medi</span></h2>
				<div class="pic-text-flex">
					<div class="flex-item-picture">
						<img src="/upload/content/service/guaranty/1.svg" alt="">
					</div>
					<div class="flex-item-text">
						<p><span class="ff-medium">RAL&ndash;GZ&nbsp;387</span>&nbsp;&ndash; самый строгий европейский стандарт для&nbsp;медицинских компрессионных изделий, регламентирующий не&nbsp;только компрессионные свойства, эластичность и&nbsp;прочность трикотажа, но&nbsp;также состав и&nbsp;безопасность используемых материалов, требования к&nbsp;упаковке и&nbsp;маркировке и&nbsp;сохранение компрессионных свойств на&nbsp;протяжении всего гарантийного срока использования. Соответствие компрессионного трикотажа mediven требованиям стандарта RAL&ndash;GZ&nbsp;387 гарантирует его отличное качество и&nbsp;высокую медицинскую эффективность.</p>
					</div>
				</div>
				<div class="pic-text-flex">
					<div class="flex-item-picture">
						<img src="/upload/content/service/guaranty/2.svg" alt="">
					</div>
					<div class="flex-item-text">
						<p><span class="ff-medium">DIN&nbsp;EN&nbsp;ISO&nbsp;9001:2000/DIN&nbsp;EN&nbsp;13485</span>&nbsp;&ndash; международный стандарт, определяющий требования к&nbsp;системе управления качеством. Соответствие производства компании medi стандарту DIN&nbsp;EN&nbsp;ISO&nbsp;9001:2000/DIN&nbsp;EN&nbsp;13485 гарантирует, что&nbsp;качество продукции контролируется на&nbsp;всех этапах производственного процесса.<br>
						<a href="/upload/content/service/guaranty/1.pdf" target="_blank" class="theme-link-dashed">EN&nbsp;ISO&nbsp;13485</a></p>
					</div>
				</div>
				<div class="pic-text-flex">
					<div class="flex-item-picture">
						<img src="/upload/content/service/guaranty/3.svg" alt="">
					</div>
					<div class="flex-item-text">
						<p><span class="ff-medium">Oeko&ndash;Tex&nbsp;Standard&nbsp;100</span>&nbsp;&ndash; это&nbsp;независимая международная система проверки и&nbsp;сертификации материалов, промежуточной и&nbsp;конечной продукции на&nbsp;всех этапах производственного процесса. Соответствие компрессионного трикотажа mediven требованиям Oeko&ndash;Tex&nbsp;Standard&nbsp;100 подтверждает его гипоаллергенность и&nbsp;безопасность даже для&nbsp;чувствительной кожи при&nbsp;длительном использовании.</p>
						<p>
							<a target="_blank" href="/upload/content/service/guaranty/2.pdf" class="theme-link-dashed">Бандажи</a><br>
							<a target="_blank" href="/upload/content/service/guaranty/3.pdf" class="theme-link-dashed">Компрессионный трикотаж</a><br>
							<a target="_blank" href="/upload/content/service/guaranty/4.pdf" class="theme-link-dashed">Изделия плоской вязки</a><br>
							<a target="_blank" href="/upload/content/service/guaranty/5.pdf" class="theme-link-dashed">Госпитальный трикотаж</a>
						</p>
					</div>
				</div>
				<div class="pic-text-flex">
					<div class="flex-item-picture">
						<img src="/upload/content/service/guaranty/4.svg" alt="">
					</div>
					<div class="flex-item-text">
						<p>Продукция medi соответствует основным требованиям директив&nbsp;ЕС и&nbsp;стандартам Европейского союза: изделия не&nbsp;являются вредными (опасными) для&nbsp;здоровья его потребителей, а&nbsp;также безвредны для&nbsp;окружающей среды.</p>
						<a target="_blank" href="/upload/content/service/guaranty/6.pdf" class="theme-link-dashed">Декларация о&nbsp;соответствии&nbsp;EC</a>
					</div>
				</div>
			</div>
	  </div>
	</div>
</div><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>