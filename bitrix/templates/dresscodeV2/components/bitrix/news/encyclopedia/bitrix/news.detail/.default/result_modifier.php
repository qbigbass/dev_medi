<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$preview_text = $arResult['IPROPERTY_VALUES']['ELEMENT_META_DESCRIPTION'] != '' ?
    $arResult['IPROPERTY_VALUES']['ELEMENT_META_DESCRIPTION'] : $arResult['NAME'];

?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "<?= $arResult["CANONICAL_PAGE_URL"] ?>"
        },
        "headline": "<?= $arResult["NAME"] ?>",
        "description": "<?= strtok(wordwrap(strip_tags(substr($preview_text, 0, 250)), 150, "...n"), "n"); ?>",
        "image": "<?= (CMain::IsHTTPS() ? "https://" : "http://") . SITE_SERVER_NAME . $arResult["PREVIEW_PICTURE"]["SRC"] ?>",
        "author": {
            "@type": "Organization",
            "name": "medi",
            "url": "https://www.medi-salon.ru/"
        },
        "publisher": {
            "@type": "Organization",
            "name": "medi",
            "logo": {
                "@type": "ImageObject",
                "url": "https://www.medi-salon.ru/bitrix/templates/dresscodeV2/headers/header8/images/logo.png"
            }
        },
        "datePublished": "<?= date("Y-m-d", strtotime($arResult['DATE_CREATE'])) ?>",
        "dateModified": "<?= date("Y-m-d", strtotime($arResult['TIMESTAMP_X'])) ?>"
    }
    
    </script>
<?
$this->__component->SetResultCacheKeys(array(
    "NAME",
    "PREVIEW_TEXT",
    "PREVIEW_PICTURE",
    "DETAIL_PICTURE",
    "DETAIL_PAGE_URL",
    "RELATED"
)); ?>