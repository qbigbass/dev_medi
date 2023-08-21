<style>
.down {
  transform: rotate(90deg);
}
.up {
  transform: rotate(270deg);
}
.barrow {
  display: inline-block;
  margin-left: 5px;
  font-size: 24px;
  vertical-align: middle;
}
.medi-openning-shadow-block .medi-shadow-block-content {
  transition: max-height 0.5s cubic-bezier(0.39, 0.575, 0.565, 1);
}
.medi-shadow-block-text-content {
  background: linear-gradient(
    to top,
    rgba(255, 255, 255, 1),
    rgba(255, 255, 255, 0.1),
    rgba(255, 0, 0, 0)
  );
}
.medi-position-more-button {
  color: #000;
  height: auto;
  padding-top: 0;
  display: inline-block;
  width: auto;
  vertical-align: text-bottom;
}
</style>

<div class="limiter">
<div class="medi-openning-shadow-block">
	<div class="medi-shadow-block-content">
		<div class="medi-shadow-block-text-content">
		</div>

		<h2 class="ff-medium">Чем мы занимаемся</h2>
<p>Уже более 100 лет немецкая компания medi — один из признанных лидеров в области производства компрессионных и ортопедических изделий для лечения и профилактики заболеваний, а также реабилитации после травм и операций. В России официальное представительство существует более 20 лет.</p>
		<p>Инновационные разработки, высокотехнологичные и качественные материалы, медицинская эффективность и соответствие европейским стандартам качества, которые подтверждены множеством сертификатов, — всё это бренд medi.</p>
<p>В интернет-магазине каждый найдет то, что ищет, и даже если в ассортименте нет подходящего изделия, его можно оформить на заказ из Германии. Вы можете вы можете получить консультацию по номеру +7 495 225-06-00, сделать заказ с помощью оператора call-центра, а также заказать обратный звонок через форму на сайте — бесплатно для всех операторов сотовой связи.</p>
		<p>Некоторые изделия сложно купить без примерки, поэтому для жителей Москвы и Московской области есть бесплатная услуга подбора изделий на дому или в медицинском учреждении. Для наших постоянных клиентов предусмотрена <a href="/about/loyalty/" class="theme-link-dashed" target="_blank">система лояльности</a> с прогрессивной шкалой скидок.</p>
		<p>На нашем сайте публикуем <a href="/encyclopedia/" class="theme-link-dashed" target="_blank">познавательные статьи</a> от специалистов, чтобы вы получали достоверную медицинскую информацию о различных заболеваниях и состояниях и следили за новинками мира ортопедических товаров.</p>
<p>Мы следим за трендами, регулярно выпускаем новые коллекции обуви и трикотажа в соответствии с актуальными тенденциями в индустрии. В салонах представлена большой выбор размеров, цветов и моделей, которые хорошо сочетаются с офисной, повседневной и спортивной одеждой.</p>
		<h2 class="ff-medium">Преимущества medi</h2>
<ul class="galka">
	<li>     Широкий ассортимент с понятными размерными сетками и множеством расцветок.</li>
	<li>       Качество подтверждается международными сертификатами качества, все товары имеют <a href="/service/guaranty/" class="theme-link-dashed" target="_blank">гарантию от производителя</a> на 6-12 месяцев.</li>
	<li>         Возможно оформление компрессионного трикотажа <a href="/services/zakaz-po-individualnym-merkam/" class="theme-link-dashed" target="_blank">по индивидуальным меркам</a>.</li>
	<li>        Квалифицированные <a href="/services/konsultatsiya-spetsialista-po-ortezirovaniyu/" class="theme-link-dashed" target="_blank">консультанты-ортезисты</a> помогают подобрать ортопедические товары.</li>
	<li>        Выезд специалиста <a href="/services/individualnyy-podbor-ortopedicheskikh-izdeliy/" class="theme-link-dashed" target="_blank">для индивидуального подбора</a> ортопедического изделия.</li>
	<li>         Удобная <a href="/payment/" class="theme-link-dashed" target="_blank">система оплаты<a/>.</li>
		<li>        <a href="/delivery/" class="theme-link-dashed" target="_blank">Доставка</a> по всей стране службами CDEK, Boxberry, «Почта России».</li>
		</ul>
	</div>

	</div>

<div id="medi-openning-more-button" class="medi-position-more-button">
	Подробнее <span class="barrow down">&#8250;</span>
	</div>	
</div>

<script>

            if ($('#medi-openning-more-button').length) {
                $('#medi-openning-more-button').on("click", function () {
                    console.log("click");
                    if (!$(this).data('status')) {
                        $('.medi-openning-shadow-block').addClass("is-active");
                        $(this).html('Скрыть<span class="barrow up">&#8250;</span>');
                        $(this).data('status', true);
                    } else {
                        $('.medi-openning-shadow-block').removeClass("is-active");
                        $(this).html('Подробнее <span class="barrow down">&#8250;</span>');
                        $(this).data('status', false);
                    }
                });
            }
        </script>
