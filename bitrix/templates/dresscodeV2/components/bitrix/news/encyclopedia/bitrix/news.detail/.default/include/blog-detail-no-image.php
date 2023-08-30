<style>
.marg {
  margin-bottom: 15px;
}
.flex-info {
  display: flex;
  flex-wrap: wrap;
  margin-top: -32px;
  color: #a6a6a6;
  justify-content: right;
  align-items: center;
  font-size: 12px;
}
.flex-info .inf:not(:last-child) {
  margin-right: 20px;
}
.flex-info .inf {
  display: flex;
  align-items: center;
}
.flex-info img {
  margin-right: 5px;
  height: 18px;
}
.share,
.like {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
}
.share a:hover {
  opacity: 0.8;
}
.share a {
  margin-right: 8px;
}
.text-url {
  display: flex;
  align-items: center;
}
	.text-url img {
		margin-right:5px;
	}
.copy-url {
  color: #5f5f5f;
  text-decoration: none;
}
.like {
  color: #888888;
  align-items: center;
}

.like img {
  filter: invert(12%) sepia(98%) saturate(5991%) hue-rotate(322deg)
    brightness(88%) contrast(101%);
  margin-right: 5px;
}
.author-brick {
  display: flex;
  align-items: start;
  justify-content: center;
  flex-wrap: wrap;
}
.author-brick img {
  margin: 0 15px;
}
@media (max-width: 1100px) {
  .flex-info {
    margin-top: auto;
    justify-content: left;
  }
  .marg {
    width: 100%;
    text-align: center;
  }
  .author-brick img {
    margin: 15px;
  }
  .share-brick,
  .like-brick,
  .author-brick {
    margin-bottom: 30px;
    text-align: center;
    width: 100%;
  }
  .like img {
    width: 40px;
  }
  .text-url {
    margin-top: 10px;
    width: 100%;
    justify-content: center;
  }
  .copy-url {
    width: 100%;
  }
}
</style>
<!-- Инфо справа в шапке Начало-->
<div class="flex-info">
<div class="inf">05.07.2023</div>
	<div class="inf"><img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/time.svg"><div>Время чтения 5 мин</div></div>
<div class="inf"><img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/view.svg"><div>284</div></div>
<div class="inf"><img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/like.svg"><div>152</div></div>
</div>
<!-- Инфо справа в шапке Конец-->

<div class="global-block-container">
	<div class="global-content-block">
		<div class="blog-banner banner-no-image">
			<div class="banner-elem">
				<div class="tb">
					<div class="text-wrap tc">
						<div class="tb">
							<div class="tr">
								<div class="tc">
									<?if(!empty($arResult["NAME"])):?>
										<h1 class="ff-medium"><?=$arResult["NAME"]?></h1>
									<?endif;?>
									<?if(!empty($arResult["PREVIEW_TEXT"])):?>
										<div class="descr"><?=$arResult["PREVIEW_TEXT"]?></div>
									<?endif;?>
								</div>
							</div>
							<div class="social">
								<div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,moimir,twitter,telegram"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="detail-text-wrap">
			<?=$arResult["DETAIL_TEXT"]?>
			<div class="btn-simple-wrap">
				<a href="<?=$arResult["LIST_PAGE_URL"]?>" class="btn-simple btn-micro btn-border"><?=GetMessage("NEWS_BACK")?></a>
			</div>
		</div>
	</div>
	<?global $arrFilter; $arrFilter["!ID"] = $arResult["ID"];?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:news.list",
		"blogDetail",
		array_merge($arParams, array("NEWS_COUNT" => 3, "FILTER_NAME" => "arrFilter", "INCLUDE_IBLOCK_INTO_CHAIN" => "N", "ADD_SECTIONS_CHAIN" => "N", "ADD_ELEMENT_CHAIN" => "N", "SET_TITLE" => "N", "DISPLAY_TOP_PAGER" => "N", "DISPLAY_BOTTOM_PAGER" => "N")),
		$component
	);?>
</div>

<!-- Авторский блок Начало -->
<div class="light-bg">
  <div class="flex">
    <div class="author-brick">
      <img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/photo.png">
      <div class="copy-url">
        <div class="ff-medium h3 marg">Иван Иванов</div>

        <div>Ведущий специалист компании medi.<br>
          Стаж - 12лет</div>
      </div>
    </div>

    <div class="like-brick">
      <div class="ff-medium h3 marg">Понравилась статья?</div>
      <div class="like"><img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/like.svg">Мне нравится 152</div>
    </div>

    <div class="share-brick">
      <div class="ff-medium h3 marg">Поделиться статьей:</div>
<div class="share">
      <a href="/"><img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/wa.svg"></a>
      <a href="/"><img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/ok.svg"></a>
      <a href="/"><img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/vk.svg"></a>
      <a href="/"><img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/tg.svg"></a>
	<a href="/" class="copy-url"><div class="text-url"><img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/url.svg"><div class="ff-medium">Скопировать ссылку</div></div></a>
		</div></div>

  </div>
</div>
<!-- Авторский блок Конец -->

<script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js" charset="utf-8"></script>
<script src="//yastatic.net/share2/share.js" charset="utf-8"></script>
