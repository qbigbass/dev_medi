<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Карьера");
?><style>
	.text {
		padding: 15px 40px;
	}
	.light-bg {
		padding: 0!important;
	}
	.flex {
		align-items: center;
	}
	[class*=col-] {
		align-self: stretch;
	}
	@media screen and (max-width: 1023px) {
		.reverse {
			flex-direction: column-reverse;
		}
		.text {
			padding: 15px 10px;
		}
	}
	@media screen and (max-width: 750px) {
		.btn-wrap * {
			text-align: center!important;
		}
	}

</style>
<h2 class="h2 ff-medium" style="text-align: center;">В&nbsp;России компания <span class="medi-color">medi</span> развивается уже&nbsp;более&nbsp;20&nbsp;лет и&nbsp;обладает отличной репутацией как&nbsp;работодатель.</h2>
<br>
<div class="row flex no-gutters">
	<div class="col-12 col-lg-6 flex no-gutters">
		<img class="col-12" width="100%" src="/upload/content/career/1.jpg" alt="">
	</div>
	<div class="col-12 col-lg-6 flex light-bg">
		<div class="text">
			<div class="ff-medium">Сотрудники&nbsp;&ndash; наша основная ценность.</div>
			Они&nbsp;обогащают компанию своими разнообразными талантами, знаниями и&nbsp;опытом. Мы&nbsp;хотим, чтобы наши сотрудники получали удовольствие от&nbsp;работы и&nbsp;верим в&nbsp;то, что&nbsp;единственный способ добиваться высоких результатов и&nbsp;продолжать активное развитие&nbsp;&ndash; это&nbsp;работать вместе, строить отношения на&nbsp;доверии и&nbsp;оставаться надежными партнерами на&nbsp;протяжении многих лет.
		</div>
	</div>
</div>
<div class="row flex reverse no-gutters">
	<div class="col-12 col-lg-6 flex">
		<div class="text">
			<span class="ff-medium">В&nbsp;нашей компании работают</span> высококвалифицированные специалисты&nbsp;&ndash; мы&nbsp;заботимся о&nbsp;том, чтобы уровень знаний и&nbsp;профессиональных навыков персонала постоянно повышался. В&nbsp;составе компании действует <span class="ff-medium">Корпоративный учебный центр</span>, в&nbsp;котором проходят обучение все&nbsp;сотрудники. Мы&nbsp;реализуем программы индивидуального и&nbsp;корпоративного обучения, личностного роста, а&nbsp;также развития управленческих навыков.
		</div>
	</div>
	<div class="col-12 col-lg-6 flex no-gutters">
		<img class="col-12" width="100%" src="/upload/content/career/2.jpg" alt="">
	</div>
</div>
<div class="row flex no-gutters">
	<div class="col-12 col-lg-6 flex no-gutters">
		<img class="col-12" width="100%" src="/upload/content/career/3.jpg" alt="">
	</div>
	<div class="col-12 col-lg-6 light-bg flex">
		<div class="text">
			<p style="margin-top: 0;"><span class="ff-medium">Мы&nbsp;проводим ответственную политику</span> в&nbsp;отношении своих работников, которая заключается в&nbsp;следующем:</p>
			<ul style="margin: 0; padding-left: 20px;">
				<li>строгое соблюдение всех норм трудового законодательства</li>
				<li>постоянное улучшений условий труда</li>
				<li>поддержание достойного уровня оплаты труда</li>
				<li>повышение уровня квалификации</li>
				<li>создание позитивной, доброжелательной атмосферы</li>
			</ul>
		</div>
	</div>
</div>
<div class="row flex reverse no-gutters">
	<div class="col-12 col-lg-6 flex">
		<div class="text">
			<p><span class="ff-medium">В&nbsp;свою команду мы&nbsp;ждем</span> людей, которые, прежде всего, ориентированы на&nbsp;достижение высоких результатов, получают удовольствие от&nbsp;своей работы, обладают сильным командным духом и&nbsp;хотят помогать людям улучшать их&nbsp;жизнь.</p>
			Увидели себя в&nbsp;этом описании? Тогда мы&nbsp;<span class="ff-medium">ждем ваше резюме</span> и&nbsp;будем рады, если вы&nbsp;присоединитесь к&nbsp;большой семье medi.
		</div>
	</div>
	<div class="col-12 col-lg-6 flex no-gutters">
		<img class="col-12" width="100%" src="/upload/content/career/4.jpg" alt="">
	</div>
</div>
<div class="row flex btn-wrap">
	<div class="col-12 col-md-6" style="text-align: right;">
		<a target="_blank" href="https://hh.ru/employer/191961" class="btn-simple btn-small">Отправить резюме</a>
	</div>
	<div class="col-12 col-md-6">
		<div class="btn-simple btn-small showCareerForm">Заполнить анкету соискателя</div>
	</div>
</div>
<br><br>
<div id="webForm" class="form-wrap" data-load="/bitrix/templates/dresscodeV2/images/picLoad.gif">
	<div id="career-container">
		<div class="heading ff-medium">Анкета соискателя <a href="#" class="close closeWindow"></a></div>
		<?$APPLICATION->IncludeComponent(
			"bitrix:form.result.new",
			"career",
			Array(
				"CACHE_TIME" => "360000",
				"CACHE_TYPE" => "Y",
				"CHAIN_ITEM_LINK" => "",
				"CHAIN_ITEM_TEXT" => "",
				"COMPONENT_TEMPLATE" => ".default",
				"EDIT_URL" => "",
				"IGNORE_CUSTOM_TEMPLATE" => "N",
				"LIST_URL" => "",
				"SEF_MODE" => "N",
				"SUCCESS_URL" => "",
				"USE_EXTENDED_ERRORS" => "Y",
				"VARIABLE_ALIASES" => array("WEB_FORM_ID"=>"WEB_FORM_ID","RESULT_ID"=>"RESULT_ID",),
				"WEB_FORM_ID" => "16"
			)
		);?>
	</div>
</div>
<script>
    var _gcTracker=_gcTracker||[];
    _gcTracker.push(['view_page', { name: 'view_career' }]);
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
