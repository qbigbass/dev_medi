<!-- Авторский блок Начало -->
<div class="light-bg">
    <div class="flex">
        <div class="author-brick">
            <?if (!empty($arResult["EXPERT_POST"])):?>
                <img src="<?= $arResult["EXPERT_POST"]["IMG"] ?>">
                <div class="copy-url">
                    <div class="ff-medium h3 marg"><?= $arResult["EXPERT_POST"]["NAME"] ?></div>
                    <div><?= $arResult["EXPERT_POST"]["SPECIALTY"] ?><br>
                        Стаж - <?= $arResult["EXPERT_POST"]["EXPERIENCE"] ?></div>
                </div>
            <?endif;?>
        </div>

        <div class="like-brick">
            <div class="ff-medium h3 marg">Понравилась статья?</div>
            <div class="like">
                <img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/like.svg">Мне нравится 152
            </div>
        </div>

        <div class="share-brick">
            <div class="ff-medium h3 marg">Поделиться статьей:</div>
            <div class="share">
                <a href="/">
                    <img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/wa.svg">
                </a>
                <a href="/">
                    <img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/ok.svg">
                </a>
                <a href="/">
                    <img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/vk.svg">
                </a>
                <a href="/">
                    <img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/tg.svg">
                </a>
                <a href="/" class="copy-url">
                    <div class="text-url">
                        <img src="/bitrix/templates/dresscodeV2/components/bitrix/news/encyclopedia/bitrix/news.detail/.default/include/url.svg">
                        <div class="ff-medium">Скопировать ссылку</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Авторский блок Конец -->