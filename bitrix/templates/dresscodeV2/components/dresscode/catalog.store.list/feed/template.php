<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(false);
//    echo "<pre>";print_r($arResult['SALONS']);
header('Content-Type: application/xml; charset=utf-8');
?>
<?= '<?xml version="1.0" encoding="' . SITE_CHARSET . '"?>' ?>

<companies>
    <? if ($_REQUEST['ff']) {
        echo "<pre>";
        print_r($arResult['STORES']);
    } ?>
    <? foreach ($arResult["STORES"] as $arNextStore):
        $rubrics = unserialize($arNextStore['UF_YARUBRICS']);
        $yanames = unserialize($arNextStore['UF_YANAME']);
        ?>
        <company>
            <company-id><?= $arNextStore['ID'] ?></company-id>
            <name lang="ru">medi</name>
            <shortname lang="ru">medi</shortname>
            <? if (!empty($yanames)) {
                foreach ($yanames as $n) {
                    ?>
                    <name-other lang="ru"><?= $n ?></name-other>
                    <?
                }
            } ?>
            <address lang="ru"><?= preg_replace("/[0-9]{6}, /", "", $arNextStore["ADDRESS"]); ?></address>
            <country lang="ru">Россия</country>
            <? if (!empty($arNextStore["PHONE"])) { ?>
                <phone>
                    <ext/>
                    <type>phone</type>
                    <number><?= (!empty($arNextStore['UF_YMAP_PHONE']) ? $arNextStore['UF_YMAP_PHONE'] : $arNextStore['PHONE']) ?></number>
                    <info/>
                </phone>
            <? } ?>
            <? if (!empty($arNextStore["EMAIL"])) { ?>
                <email><?= $arNextStore['EMAIL'] ?></email>
            <? } ?>
            <url>https://www.medi-salon.ru/</url>
            <add-url>https://vk.com/medi.salon.russia</add-url>
            <add-url>https://ok.ru/medi.salon.russia</add-url>
            <add-url>https://www.youtube.com/channel/UC5oEj1qAP5GO078nOxKceBA</add-url>
            <info-page>https://www.medi-salon.ru<?= $arNextStore['DETAIL_PAGE_URL'] ?></info-page>
            <working-time lang="ru"><?= $arNextStore["SCHEDULE"] ?></working-time>
            <? if (!empty($rubrics)) {
                foreach ($rubrics as $r) {
                    ?>
                    <rubric-id><?= $r ?></rubric-id>
                    <?
                }
            } ?>
            <coordinates>
                <lon><?= $arNextStore['GPS_S'] ?></lon>
                <lat><?= $arNextStore['GPS_N'] ?></lat>
            </coordinates>
            <? if (!empty($arNextStore['DETAIL_IMG'])) {
                ?>
                <photos gallery-url="https://www.medi-salon.ru<?= $arNextStore['DETAIL_PAGE_URL'] ?>">
                    <photo url="https://www.medi-salon.ru<?= $arNextStore['DETAIL_IMG']['SRC'] ?>"/>
                    <? if (!empty($arNextStore['PHOTOS'])) {
                        foreach ($arNextStore['PHOTOS'] as $p) {
                            ?>
                            <photo url="https://www.medi-salon.ru<?= $p['SRC'] ?>"/>
                            <?
                        } ?>
                    <? } ?>
                </photos>
            <? } ?>
            <?
            $modify_time = MakeTimeStamp($arNextStore['DATE_MODIFY']);
            if ((time() - $modify_time) / 86400 > 20) {
                $modify_time = mktime(0, 0, 0, date("m"), 1);
            } ?>

            <actualization-date><?= FormatDate("d.m.Y", $modify_time) ?></actualization-date>
        </company>
    <? endforeach ?>
</companies>
