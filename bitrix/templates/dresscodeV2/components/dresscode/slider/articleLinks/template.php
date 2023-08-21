<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>
<? if (!empty($arResult["ITEMS"])): ?>
    <div id="relatedArticlesSlider">
        <ul class="slideBox">
            <? foreach ($arResult["ITEMS"] as $i => $arElement): ?>
                <li>
                    <? if (!empty($arElement["DETAIL_PAGE_URL"])): ?>
                    <a href="<?= str_replace("#SITE_DIR#", SITE_DIR, $arElement["DETAIL_PAGE_URL"]) ?>"
                       class="related-card">
                        <? endif; ?>
                        <? if ($arParams["LAZY_LOAD_PICTURES"] == "Y"): ?>
                            <img src="<?= $templateFolder ?>/images/lazy.jpg"
                                 data-lazy="<?= $arElement["PHOTO"]["src"] ?>"
                                 class="lazy "
                                 alt="">
                        <? else: ?>
                            <img src="<?= $arElement["PHOTO"]["src"] ?>" class="lazy" alt="">
                        <? endif; ?>
                        <? if ($arElement['NAME']) {
                            ?>
                            <div class="related-text ff-medium"><?= $arElement['NAME'] ?></div>
                        <? } ?>
                        <? if (!empty($arElement["DETAIL_PAGE_URL"])): ?>
                    </a>
                <? endif; ?>

                </li>
            <? endforeach; ?>
        </ul>
        <a href="#" class="realtedArticlesBtnLeft"></a>
        <a href="#" class="realtedArticlesBtnRight"></a>
    </div>
    <? php/*
    <script>
        $("#relatedArticlesSlider").dwCarousel({
            leftButton: ".realtedArticlesBtnLeft",
            rightButton: ".realtedArticlesBtnRight",
            countElement: 5,
            autoMove: true,
            resizeElement: true,
            resizeAutoParams: {

                768: 2,
                480: 1
            }
        });
    </script>*/ ?>
<? endif; ?>
