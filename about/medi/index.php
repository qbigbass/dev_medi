<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("О компании medi");
$APPLICATION->AddHeadString('<link rel="canonical" href="https://www.medi-salon.ru'.$APPLICATION->GetCurDir().'" />');
?><h1>О medi</h1>
<?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"personal",
	array(
	"COMPONENT_TEMPLATE" => "personal",
		"ROOT_MENU_TYPE" => "company",	// Тип меню для первого уровня
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
      <div>
         <div>
            <div class="pic-text-flex white-bg">
               <div class="flex-item-picture minimal-width">
                  <img class="img-responsive" src="/upload/content/about/medi/img1.jpeg" alt="">
               </div>
               <div class="flex-item-text position-item-text">
                  <div class="h2 ff-medium">Компания medi</div>
                  <div class="line"></div>
                  <p>Компания medi, штаб-квартира которой находится в&nbsp;Байройте, Германия, является одним из&nbsp;ведущих мировых производителей медицинских изделий. Следуя девизу “medi.&nbsp;Почувствуйте себя лучше”, около 2,600&nbsp;сотрудников по&nbsp;всему миру помогают людям повысить качество их&nbsp;жизни: будь то&nbsp;боль в&nbsp;спине, разрыв связок во&nbsp;время занятий спортом, варикозное расширение вен, плоскостопие или хронические трофические язвы&nbsp;– изделия&nbsp;medi и&nbsp;терапевтические концепции medi способствуют успеху лечения многих заболеваний и&nbsp;недугов.</p>
               </div>
            </div>
         </div>
         <div>
            <div class="pic-text-flex medi-color-bg">
               <div class="flex-item-text">
                  <div style="color: #fff;" class="h2 ff-medium">Миссия компании medi</div>
                  <p>Мы убеждены,&nbsp;что здоровье человека является важнейшим фактором жизни, который влияет на&nbsp;мировоззрение, формирует мышление и&nbsp;дает возможности. Для сохранения и&nbsp;поддержания здоровья и&nbsp;в&nbsp;случае лечения заболеваний каждый человек должен обладать правом выбора лучшего, будь то&nbsp;врач, терапия или&nbsp;специализированные изделия.</p>
                  <p>При&nbsp;разных методиках и&nbsp;подходах к&nbsp;лечению целью является выздоровление человека и&nbsp;улучшение качества жизни. Являясь международным экспертом в&nbsp;области производства компрессионной и&nbsp;ортопедической продукции, мы становимся элементом комплексной терапии и&nbsp;помогаем врачам и&nbsp;пациентам максимально эффективно достичь общей цели.</p>
                  <p>Мы также уверены, что&nbsp;технический прогресс и&nbsp;научные достижения дают новые и&nbsp;неограниченные возможности, поэтому максимально сосредоточили усилия на&nbsp;непрерывном совершенствовании производства и&nbsp;развитии наших уникальных технологий, чтобы воплотить эти знания в&nbsp;компрессионных и&nbsp;ортопедических изделиях.</p>
                  <p>Мы создаем «историю здоровья» вместе с&nbsp;миллионами клиентов и&nbsp;тысячами специалистов по&nbsp;всему миру.</p>
               </div>
            </div>
         </div>
         <div>
            <div class="pic-text-flex white-bg">
               <div class="flex-item-text position-item-text">
                  <div class="h2 ff-medium">С&nbsp;medi чувствуют себя лучше</div>
                  <p>Мы разрабатываем инновации для&nbsp;улучшения качества жизни. Для&nbsp;нас это самый большой стимул&nbsp;- научно обоснованный и&nbsp;высокотехнологичный. В&nbsp;результате мы получаем устойчивый успех лечения и&nbsp;уникальные физические ощущения.</p>
               </div>
               <div class="flex-item-picture minimal-width">
                  <img class="img-responsive" src="/upload/content/about/medi/img2.jpeg" alt="">
               </div>
            </div>
         </div>
         <div>
            <div style="flex-wrap: wrap;" class="pic-text-flex light-gray-bg">
               <div class="flex-item-text">
                  <div class="h2 ff-medium">The&nbsp;World of&nbsp;Compression</div>
                  <p>medi World of Compression прокладывает путь в&nbsp;будущее компрессионных технологий. В&nbsp;его основе лежит более 65-летний опыт.  Этот знак определяет компрессионные изделия такие, как медицинский компрессионный трикотаж для&nbsp;флебологического и&nbsp;лимфологического применения, а&nbsp;также бандажи для&nbsp;терапии суставов.</p>
                  <p>Кроме того, medi является специалистом в&nbsp;области нерастяжимой, адаптивной компрессионной терапии для&nbsp;лечения ран и&nbsp;отеков. Польза для&nbsp;наших клиентов заключается в&nbsp;возможности различных вариантов комбинации изделий, первоклассном мастерстве создания изделий и&nbsp;высоком уровне комфорта&nbsp;- в&nbsp;том, чтобы на&nbsp;себе ощутить значение нашего девиза "Почувствуйте&nbsp;себя&nbsp;лучше".</p>
               </div>
               <a rel="nofollow" class="main-new-button" href="https://www.medirus.ru/kompanija/world-of-compression/" title="More about the medi World of Compression"><noindex>Узнать больше о medi World of Compression</noindex></a>
            </div>
         </div>
         <div>
            <div class="white-bg">
               <div class="flex-item-text">
                  <div class="h2 ff-medium">На&nbsp;пользу пациенту</div>
                  <p>Выступая в&nbsp;качестве катализатора между медицинской промышленностью и&nbsp;специализированными продавцами, medi разрабатывает соответствующие показаниям средства лечения для&nbsp;пользы пациента. Спектр медицинских услуг включает медицинский компрессионный трикотаж, бандажи, ортезы, чулки для&nbsp;профилактики тромбоза, косметологический компрессионный трикотаж и&nbsp;ортопедические стельки для&nbsp;обуви. Этот ассортимент продукции можно найти практически в&nbsp;каждом специализированном медицинском магазине (особенно в&nbsp;ортопедических салонах) в&nbsp;Германии и&nbsp;более чем в&nbsp;90&nbsp;странах мира.</p>
                  <p>Кроме того, более 65-летний опыт в&nbsp;области компрессионных технологий перетек в&nbsp;модный спортивный продукт под&nbsp;брендом medi&nbsp;CEP.</p>
               </div>
               <div class="flip-flex">
				<a target="_blank" href="/catalog/kompressionnyy-trikotazh/">
				  <div class="flex-item-flip-picture">
                     <div class="flip-box">
                        <div class="flip-box-inner">
                           <div class="flip-box-front">
                              <img class="img-responsive" src="/upload/content/about/medi/img3.jpeg" alt="">
                              <div>Компрессионный трикотаж</div>
                           </div>
                        </div>
                     </div>
                  </div>
				</a>
				<a target="_blank" href="/catalog/ortopedicheskie-stelki/individualnye-stelki/">
                  <div class="flex-item-flip-picture">
                     <div class="flip-box">
                        <div class="flip-box-inner">
                           <div class="flip-box-front">
                              <img class="img-responsive" src="/upload/content/about/medi/img4.jpg" alt="">
                              <div>Ортопедические стельки&nbsp;igli</div>
                           </div>
                        </div>
                     </div>
                  </div>
				</a>
				<a target="_blank" href="/catalog/odezhda-dlya-sporta/">
                  <div class="flex-item-flip-picture">
                     <div class="flip-box">
                        <div class="flip-box-inner">
                           <div class="flip-box-front">
                              <img class="img-responsive" src="/upload/content/about/medi/img5.jpeg" alt="">
                              <div>Интеллектуальная одежда для спорта</div>
                           </div>
                        </div>
                     </div>
                  </div>
				</a>
               </div>
            </div>
         </div>
         <div>
            <div class="pic-text-flex dark-blue-bg iframe-container">

			   <iframe src="https://player.vimeo.com/video/132697529?color=ff0179&title=0&byline=0&portrait=0"  frameborder="0" allow="autoplay; fullscreen; accelerometer" allowfullscreen></iframe>

            </div>
         </div>
         <div>
            <div class="pic-text-flex white-bg">
               <div class="flex-item-picture minimal-width">
                  <img class="img-responsive" src="/upload/content/about/medi/map.svg" alt="">
               </div>
               <div class="flex-item-text position-item-text">
                  <div class="h2 ff-medium">medi в&nbsp;мире</div>
                  <p>Являясь глобальным игроком на&nbsp;рынке здравоохранения, мы непрерывно расширяем наше международное сотрудничество: в&nbsp;настоящее время у&nbsp;medi около 2,600&nbsp;сотрудников и 21&nbsp;международный филиал по&nbsp;всему миру. Медицинские изделия medi производятся на&nbsp;наших производственных участках в&nbsp;Германии и&nbsp;США и&nbsp;экспортируются в&nbsp;более чем 90&nbsp;стран.</p>
               </div>
            </div>
         </div>
      </div><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
