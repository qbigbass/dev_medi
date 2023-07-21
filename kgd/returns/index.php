<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Возврат и обмен");
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
						<p class="ff-medium h3">
							 Для&nbsp;возврата товаров, приобретенных &nbsp;в&nbsp;интернет-магазине
						</p>
						<p>
 <a href="/upload/content/returns/zayavlenie.doc" target="_blank" class="theme-link-dashed">Заявление установленного образца</a><br>
 <a href="/upload/content/returns/zayavlenie-na-vozvrat-rekvizity.pdf" target="_blank" class="theme-link-dashed">Заявление на возврат денежных средств</a><br>
 <a href="/upload/content/returns/obrazec-vozvrat-proverka.pdf" target="_blank" class="theme-link-dashed">Образец заявления</a><br>
 <a href="/upload/content/returns/obrazec-vozvrat-rekvizity.pdf" target="_blank" class="theme-link-dashed">Образец на возврат денежных средств</a>
						</p>
						<p>
 <span class="ff-medium">Возврат покупки клиентом лично:</span> г.&nbsp;Москва, Петроверигский переулок, дом 3, строение 1
						</p>
 <a href="/salons/salon-medi-kitai-gorod/" target="_blank" class="theme-link-dashed">График работы и&nbsp;схема проезда</a>
<p>&nbsp;</p>
						<p>
							 <span class="ff-medium">Для клиентов из регионов</span>, просьба, разборчиво заполнить заявления, выслать копию нам на почту <a href="mailto:mks@mediexp.ru">mks@mediexp.ru</a> для проверки правильности заполнения. <span class="ff-medium">Дождаться подтверждения</span> проверки, затем оригинал заявлений отправить вместе с изделием.
						</p>
						<ul>
							<li><span class="ff-medium">Для отправки Почтой России</span> адрес отправления: 119435 Новодевичий пр-д дом 10, Получатель: ООО «МедиРУС»;</li>
							<li> <span class="ff-medium">Для отправки СДЭКом/Боксберри</span>, необходимо указать отправку <span class="ff-medium">КУРЬЕРОМ до двери получателя</span>, адрес отправления: г. Москва, НАО, поселение Сосенское, деревня Николо-Хованское, дом 1006, строение 1, Получатель: ООО «МЕДИ РУС»;</li>
						</ul>
					</div>
					<div class="flex-item">
						<p class="ff-medium h3">Для&nbsp;возврата товаров, приобретенных в&nbsp;одном из салонов medi</p>
						<p>Возврат оформляется в&nbsp;салоне, где&nbsp;был получен товар</p>
						<a href="/kgd/salons/" target="_blank" class="theme-link-dashed">Адреса и&nbsp;график работы салонов medi.</a>
					</div>
				</div>
				<br>
				<p class="h3 ff-medium"><span class="medi-color ff-bold" style="font-size: 1.3em;">2.</span>&nbsp;&nbsp;Вместе с&nbsp;заявлением передайте специалисту салона или перешлите почтой товар, сохраненный в&nbsp;надлежащем виде:</p>
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