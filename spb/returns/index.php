<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Обмен и возврат изделий");
?><?$APPLICATION->IncludeComponent(
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
<h1>Обмен и возврат изделий</h1>
<div class="global-block-container">
	<div class="global-content-block">
		<div class="detail-text-wrap">
			<style>
				@media screen and (max-width: 700px){
					.detail-text-wrap blockquote {
						margin-left: 10px;
						margin-right: 0;
					}
				}
			</style>
			<p class="ff-medium">В&nbsp;соответствие с&nbsp;Законом&nbsp;РФ «О&nbsp;защите прав потребителей» и&nbsp;Постановления Правительства&nbsp;РФ №&nbsp;55 ортопедические и&nbsp;компрессионные изделия подлежат обмену и&nbsp;возврату только в&nbsp;случае наличия заводского брака или&nbsp;дефекта.</p>
			<p>Чтобы вернуть или&nbsp;обменять товар, приобретенный в&nbsp;салоне или&nbsp;интернет-магазине medi нужно выполнить следующие шаги:</p>
			<div class="white-bg" style="padding-top: 20px;padding-bottom: 20px;">
				<p class="h3 ff-medium"><span class="medi-color ff-bold" style="font-size: 1.3em;">1.</span>&nbsp;&nbsp;Заполните <a href="/upload/content/returns/zayvlenie-na-vozvrat.pdf" target="_blank" class="theme-link-dashed">заявление установленного образца</a> с&nbsp;указанием причины возврата и&nbsp;дефектов приобретенного товара, и&nbsp;передайте по&nbsp;одному из&nbsp;адресов:</p>
				<div class="flex">
					<div class="flex-item">
						<p class="ff-medium h3">Для&nbsp;возврата товаров, приобретенных у&nbsp;специалиста по&nbsp;ортезированию или&nbsp;в&nbsp;интернет-магазине</p>
						<p>г.&nbsp;Санкт-Петербург, ул.&nbsp;Есенина,&nbsp;д.&nbsp;19</p>
						<a href="/spb/salons/salon-medi-esenina/" target="_blank" class="theme-link-dashed">График работы и&nbsp;схема проезда</a>
					</div>
					<div class="flex-item">
						<p class="ff-medium h3">Для&nbsp;возврата товаров, приобретенных в&nbsp;одном из салонов medi</p>
						<p>Возврат оформляется в&nbsp;салоне, где&nbsp;был получен товар</p>
						<a href="/spb/salons/" target="_blank" class="theme-link-dashed">Адреса и&nbsp;график работы салонов medi.</a>
					</div>
				</div>
				<br>
				<p class="h3 ff-medium"><span class="medi-color ff-bold" style="font-size: 1.3em;">2.</span>&nbsp;&nbsp;Вместе с&nbsp;заявлением передайте товар, сохраненный в&nbsp;надлежащем виде:</p>
				<blockquote>
					<span class="medi-color ff-medium" style="font-size: 2em;">&#33;</span>&nbsp;&nbsp;Товар принимается к&nbsp;возврату только в&nbsp;неповрежденной упаковке, в&nbsp;которой товар был получен при&nbsp;покупке.
				</blockquote>
				<p class="h3 ff-medium"><span class="medi-color ff-bold" style="font-size: 1.3em;">3.</span>&nbsp;&nbsp;При&nbsp;возврате необходимо также предоставить следующие документы:</p>
				<blockquote>
					<ul>
						<li>Документ, удостоверяющий личность.</li>
						<li>Документ, подтверждающий факт приобретения товара (кассовый чек, накладная, почтовая квитанция).</li>
					</ul>
				</blockquote>
				<p class="h3 ff-medium"><span class="medi-color ff-bold" style="font-size: 1.3em;">4.</span>&nbsp;&nbsp;Дождитесь результатов экспертизы</p>
				<blockquote>
					<p>После оформления заявления на&nbsp;возврат или&nbsp;обмен, специалисты компании проведут проверку качества изделия и&nbsp;сообщат вам о&nbsp;ее результатах: в&nbsp;случае выявления заводского брака или&nbsp;дефекта, вам будет предложена замена товара или&nbsp;будет возвращена стоимость, по&nbsp;которой вы&nbsp;его приобрели</p>
				</blockquote>
			</div>
		</div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>