<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Клуб лояльности medi");
$APPLICATION->AddHeadString('<link rel="canonical" href="https://www.medi-salon.ru'.$APPLICATION->GetCurDir().'" />');
?><style>
	.theme-link-dashed.h3 {
		color: #e20074;
		font-size: 17px;
		margin-top: 1em;
	}
	.m-lgray {
		color: #9B9B9B;
		margin-bottom: 1em;
	}
	.m-icon {
		width: 68px;
		margin-right: 10px;
	}
	.no-wrap {
		flex-wrap: nowrap;
		align-items: center;
		justify-content: flex-start;
	}
	.m-big {display: block;}
	.m-small {display: none;}
	.card-section {
		background: linear-gradient(to top, #f7f7f7 70%, #ffffff 30%);
		padding: 20px 5% 2em;
	}
	.page-title {
		color: #e20074;
		font-size: 30px;
		line-height: 1.2em;
	}
	.page-title.m-big {
		margin-bottom: 50px;
		margin-top: 1em;
	}
	.page-title.m-small {
		text-align: center;
		font-size: 21px;
		margin-bottom: 1em;
	}
	@media (max-width: 992px) {
		.m-big {display: none;}
		.m-small {display: block;}
		.card-section .col-12 {
			padding: 1em!important;
			margin: 0 auto!important;
		}
		.card-section .btn-wrap {
			text-align: center;
		}
		.card-section {
			background: linear-gradient(to top, #f7f7f7 75%, #ffffff 25%);
		}
	}
	@media (max-width: 767px) {
		.card-section {
			background: linear-gradient(to top, #f7f7f7 83%, #ffffff 17%);
		}
	}
</style>
<div class="card-section flex" style="justify-content: center;">
	<div class="col-lg-5 col-md-6 col-12" style="margin-bottom: 1em; padding: 2em;">
		<div class="ff-medium page-title m-small">
			 Клуб лояльности medi
		</div>
 <img width="100%" src="/upload/content/about/loyalty/k1.webp" alt="">
	</div>
	<div class="col-lg-7 col-12" style="margin-bottom: 1em; padding: 2em">
		<div class="ff-medium page-title m-big">
			 Клуб лояльности medi
		</div>
		<div class="h3">
			 Присоединяйтесь к&nbsp;Клубу лояльности medi и&nbsp;покупайте товары для&nbsp;здоровья немецкого качества по&nbsp;специальным предложениям.
		</div>
		<div class="btn-wrap">
 <a target="_blank" href="/auth/?register=yes" class="btn-simple btn-medium">Вступить в&nbsp;клуб</a>
		</div>
	</div>
	<div class="col-lg-4 col-md-6 col-sm-6 col-12 flex no-wrap">
 <img src="/upload/content/about/loyalty/2.svg" alt="" class="m-icon">
		<div>
			 накопительная система скидок
		</div>
	</div>
	 <?/*<div class="col-lg-3 col-md-5 col-sm-6 col-12 flex no-wrap">
		<img src="/upload/content/about/loyalty/3.svg" alt="" class="m-icon">
		<div>специальная скидка ко дню рождения</div>
	</div>*/?>
	<div class="col-lg-4 col-md-6 col-sm-6 col-12 flex no-wrap">
 <img src="/upload/content/about/loyalty/4.svg" alt="" class="m-icon">
		<div>
			 персональные предложения
		</div>
	</div>
</div>
 <br>
<div class="h2 ff-medium" style="text-align: center;">
	 Как это работает?
</div>
 <br>
<div class="flex" style="justify-content: center;">
	<div class="col-lg-5 col-md-6 col-12">
		<div class="h3 ff-medium m-lgray" style="text-align: center;">
			 Шаг 1
		</div>
		<div class="ff-medium" style="max-width: 400px;">
 <span class="medi-color">Оформите виртуальную карту</span> удобным способом:
		</div>
		<ul>
			<li>Пройдя <a target="_blank" href="/personal/register/" class="theme-link-dashed">регистрацию</a> на&nbsp;сайте</li>
			<li>В&nbsp;одном из&nbsp;розничных <a target="_blank" href="/salons/" class="theme-link-dashed">салонов</a></li>
			<li>Через контактный центр <a class="theme-link-dashed" href="tel:<?=$GLOBALS['medi']['phones'][SITE_ID];?>">по&nbsp;телефону</a></li>
			<li>В&nbsp;мобильном приложении <a target="_blank" href="https://cardsmobile.onelink.me/4238130056/88e2556f" class="theme-link-dashed">Кошелек</a></li>
		</ul>
	</div>
	<div class="col-lg-5 col-md-6 col-12">
		<div class="h3 ff-medium m-lgray" style="text-align: center;">
			 Шаг 2
		</div>
		<div class="ff-medium">
			 Совершайте покупки и&nbsp;<span class="medi-color">увеличивайте размер скидки:</span>
		</div>
		<ul>
			<li>5% скидка при&nbsp;накопленной сумме покупок от&nbsp;7&nbsp;000&nbsp;р.</li>
			<li>7% скидка при&nbsp;накопленной сумме покупок от&nbsp;30&nbsp;000&nbsp;р.</li>
			<li>10% скидка при&nbsp;накопленной сумме покупок от&nbsp;50&nbsp;000&nbsp;р.</li>
			<li>Свыше&nbsp;10% скидка по&nbsp;специальным предложениям<br>
			 только для&nbsp;членов Клуба лояльности medi.</li>
		</ul>
	</div>
</div>
 <img width="100%" src="/upload/content/about/loyalty/f.png" alt="" class="m-big"> <img width="100%" src="/upload/content/about/loyalty/f1.png" alt="" class="m-small">
<div class="light-gray-bg">
	<div class="h1 ff-medium" style="text-align: center;">
		 Правила Клуба лояльности <span class="medi-color">medi</span>
	</div>
 <br>
	<div style="max-width: 1000px; margin-left: auto; margin-right: auto;">
		<p class="h3">
			 Скидка по&nbsp;карте не&nbsp;суммируется с&nbsp;другими акциями и&nbsp;предложениями (если иное не&nbsp;указано в&nbsp;условиях акции)
		</p>
		 <?/*<p class="h3">Скидка в&nbsp;размере&nbsp;20% предоставляется за&nbsp;6&nbsp;дней&nbsp;до и&nbsp;7&nbsp;дней после дня&nbsp;рождения, кроме <a target="_blank" href="/upload/content/about/loyalty/1.pdf" class="theme-link-dashed" style="font-size: 1em;">товаров-исключений</a></p>*/?>
		<p class="h3">
			 Скидка 15% людям с инвалидностью, участникам ВОВ и&nbsp;боевых действий, многодетным семьям с&nbsp;первой покупки <span class="medi-color">при&nbsp;предъявлении соответствующего удостоверения</span>. 
		</p>
 <a target="_blank" href="/about/loyalty/pl/" class="theme-link-dashed h3">Подробнее&nbsp;&gt;&gt;</a>
	</div>
</div>
<br>
    <script>
        var _gcTracker=_gcTracker||[];
        _gcTracker.push(['view_page', { name: 'view_loyalty' }]);
    </script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php")?>