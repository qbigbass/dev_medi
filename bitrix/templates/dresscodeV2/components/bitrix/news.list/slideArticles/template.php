<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>
<? if (!empty($arResult["ITEMS"])): ?>

    <div class="related-flex">
        <? foreach ($arResult['ITEMS'] as $arItem): ?>
            <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="related-card">
                <img src="<?= $arItem['PHOTO']['src'] ?>" title="<?= $arItem['NAME'] ?>"
                     alt="<?= $arItem['NAME'] ?>">
                <div class="related-text ff-medium"><?= $arItem['NAME'] ?></div>
            </a>
        <? endforeach ?>
    </div>

<? endif; ?>
