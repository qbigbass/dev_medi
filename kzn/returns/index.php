<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Возврат и обмен");
?><h1>Правила возврата и обмена при покупке в Казани</h1>
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
	<p>Интернет-магазин medi-salon.ru предлагает к&nbsp;покупке товары, имеющие сертификаты качества, официальные разрешения на&nbsp;реализацию в&nbsp;Российской Федерации. Товары medi и&nbsp;других мировых производителей ортопедической продукции проходят качественный контроль при&nbsp;производстве.</p>
	<p>Мы&nbsp;всегда открыты к&nbsp;пожеланиям покупателей, и&nbsp;в&nbsp;случае нареканий к&nbsp;приобретенным товарам, проводим проверку на&nbsp;предмет производственного брака. При подозрении на&nbsp;ненадлежащее качество ознакомьтесь с&nbsp;правилами возврата товаров&nbsp;ниже.</p>
	<p>В&nbsp;случае надлежащего качества ортопедические товары не&nbsp;могут быть возвращены и&nbsp;обменяны в&nbsp;соответствии с&nbsp;их&nbsp;особым статусом, закрепленным в&nbsp;Законе. Регламентирующие документы: Федеральный закон «О&nbsp;защите прав потребителей» и&nbsp;Перечень товаров надлежащего качества, не&nbsp;подлежащих возврату или&nbsp;обмену.</p>
	<h2 class="h2 ff-medium">Правила возврата товаров</h2>
	<p>При возврате товара необходимо соблюсти требования:</p>
	<ul>
	 <li>Сохранена упаковка, в&nbsp;которой товар был отпущен. Упаковка не&nbsp;должна быть поврежденной.</li>
	 <li>При возврате необходимо предоставить документы:</li>
	 <ul>
	 <li>Документ, удостоверяющий личность.</li>
	 <li>Заполненное <a class="theme-link-dashed" href="/upload/content/returns/zayvlenie-na-vozvrat.pdf">заявление установленного образца</a> с&nbsp;указанием причины возврата и&nbsp;дефектов приобретенного товара.</li>
	 <li>Документ, подтверждающий факт приобретения товара (кассовый чек, накладная, почтовая квитанция).</li>
	 </ul>
	</ul>
	<p><span class="ff-medium">Документы и&nbsp;товар к&nbsp;возврату принимаются в&nbsp;том салоне, где&nbsp;вы&nbsp;получали свою покупку. </span>Для регионов, где&nbsp;есть салоны</p>
	<p><span class="ff-medium">Если товар был получен у&nbsp;курьера или в&nbsp;пункте выдачи заказов, возвращаемый товар и&nbsp;комплект документов необходимо отправить почтой по&nbsp;адресу:</span><span class="ff-medium">117342, г.&nbsp;Москва, ул.&nbsp;Бутлерова,&nbsp;д.17, БЦ&nbsp;НЕО-ГЕО, оф.&nbsp;272. ИП&nbsp;Баженов&nbsp;Д.Г.</span></p>
	<p>После оформления возврата мы&nbsp;проведем экспертизу о&nbsp;ненадлежащем качестве товара. По&nbsp;ее&nbsp;результатам с&nbsp;вами свяжется специалист Контактного центра и&nbsp;проинформирует о дальнейших действиях.</p>
	<h2 class="h2 ff-medium">Регламентирующие документы</h2>
	<ul>
	 <li>Федеральный закон «О&nbsp;защите прав потребителей»</li>
	 <li>Согласно п.1&nbsp;Перечню, утверждённому постановлением Правительства Российской Федерации от&nbsp;19&nbsp;января 1998&nbsp;г., №&nbsp;55, товары из&nbsp;текстиля, металла и&nbsp;резины для&nbsp;профилактики и&nbsp;лечения заболеваний в&nbsp;домашних условиях не&nbsp;подлежат возврату или&nbsp;обмену.</li>
	</ul>
	<h2>N&nbsp;55 ПЕРЕЧЕНЬ НЕПРОДОВОЛЬСТВЕННЫХ ТОВАРОВ НАДЛЕЖАЩЕГО КАЧЕСТВА, НЕ&nbsp;ПОДЛЕЖАЩИХ ВОЗВРАТУ ИЛИ&nbsp; ОБМЕНУ НА&nbsp;АНАЛОГИЧНЫЙ ТОВАР ДРУГИХ РАЗМЕРА, ФОРМЫ, ГАБАРИТА, ФАСОНА, РАСЦВЕТКИ ИЛИ&nbsp;КОМПЛЕКТАЦИИ</h2>
	<ol>
		<li>Товары для&nbsp;профилактики и&nbsp;лечения заболеваний в&nbsp;домашних условиях (предметы санитарии и&nbsp;гигиены из&nbsp;металла, резины, текстиля и&nbsp;других материалов, инструменты, приборы и&nbsp;аппаратура медицинские, средства гигиены полости рта, линзы очковые, предметы по&nbsp;уходу за&nbsp;детьми).</li>
		<li>Предметы личной гигиены.</li>
		<li>Парфюмерно - косметические товары.</li>
		<li>Текстильные товары (хлопчатобумажные, льняные, шелковые, шерстяные и&nbsp;синтетические ткани, нетканые материалы типа тканей, метражные товары из&nbsp;тканей и&nbsp;нетканых материалов - ленты, тесьма, кружева и&nbsp;другие аналогичные товары).</li>
		<li>Швейные и&nbsp;трикотажные изделия (изделия швейные и&nbsp;трикотажные бельевые, изделия чулочно - носочные).</li>
		<li>Изделия и&nbsp;материалы, контактирующие с&nbsp;пищевыми продуктами, из&nbsp;полимерных материалов, в&nbsp;том&nbsp;числе для&nbsp;разового использования (посуда и&nbsp;принадлежности столовые и&nbsp;кухонные, емкости и&nbsp;упаковочные материалы для&nbsp;хранения и&nbsp;транспортирования пищевых продуктов).</li>
		<li>Товары бытовой химии.</li>
		<li>Мебель бытовая (мебельные гарнитуры и&nbsp;комплекты).</li>
		<li>Изделия из&nbsp;драгоценных металлов, с&nbsp;драгоценными камнями, из&nbsp;драгоценных металлов со&nbsp;вставками из&nbsp;полудрагоценных и&nbsp;синтетических камней, ограненные драгоценные камни.</li>
		<li>Автомобили и&nbsp;мотовелотовары, прицепы и&nbsp;номерные агрегаты к&nbsp;ним; мобильные средства малой механизации сельскохозяйственных работ; прогулочные суда и&nbsp;иные плавсредства бытового назначения.</li>
		<li>Технически сложные товары бытового назначения, на&nbsp;которые установлены гарантийные сроки (станки металлорежущие и&nbsp;деревообрабатывающие бытовые; кухонное оборудование; электробытовые машины и&nbsp;приборы; бытовая радиоэлектронная аппаратура; бытовая вычислительная и&nbsp;множительная техника; фото- и&nbsp;киноаппаратура; телефонные аппараты и&nbsp;факсимильная аппаратура; электромузыкальные инструменты; игрушки электронные).</li>
	</ol>
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