<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

Loader::includeModule("highloadblock");

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

$preview_text = $arResult['IPROPERTY_VALUES']['ELEMENT_META_DESCRIPTION'] != '' ?
    $arResult['IPROPERTY_VALUES']['ELEMENT_META_DESCRIPTION'] : $arResult['NAME'];

/* Автор статьи */
if ($arResult["PROPERTIES"]["AUTHOR_POST"]["VALUE"] !== "") {
    $value = $arResult["PROPERTIES"]["AUTHOR_POST"]["VALUE"];

    $hlBlock = HL\HighloadBlockTable::getList([
        'filter' => ['=NAME' => 'ExpertsEncPosts']
    ])->fetch();

    if (!empty($hlBlock)) {
        $hlBlockId = $hlBlock["ID"];
        $entity = HL\HighloadBlockTable::compileEntity($hlBlock);
        $entityDataClass = $entity->getDataClass();

        $rsData = $entityDataClass::getList(array(
            "select" => array("*"),
            "order" => array("ID" => "ASC"),
            "filter" => array("UF_XML_ID" => $value),
            "limit" => 1
        ));

        while ($arData = $rsData->Fetch()){
            $arResult["EXPERT_POST"]["NAME"] = $arData["UF_ENC_EXPERT_NAME"];
            $arResult["EXPERT_POST"]["EXPERIENCE"] = $arData["UF_ENC_EXPERT_EXP"];
            $arResult["EXPERT_POST"]["SPECIALTY"] = $arData["UF_ENC_EXPERT_PROF"];

            if ($arData["UF_ENC_EXPERT_IMG"] !== "") {
                $file = CFile::ResizeImageGet(
                    $arData["UF_ENC_EXPERT_IMG"],
                    [
                        "width" => 83, "height" => 83
                    ],
                    BX_RESIZE_IMAGE_PROPORTIONAL
                );

                if (!empty($file)) {
                    $arResult["EXPERT_POST"]["IMG"] = $file["src"];
                }
            }
        }
    }
}

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
$this->__component->arResult['LIKES_CNT'] = $arResult["PROPERTIES"]["LIKES_CNT"]["VALUE"];
$this->__component->SetResultCacheKeys(array(
    "NAME",
    "PREVIEW_TEXT",
    "PREVIEW_PICTURE",
    "DETAIL_PICTURE",
    "DETAIL_PAGE_URL",
    "RELATED",
    "LIKES_CNT"
)); ?>