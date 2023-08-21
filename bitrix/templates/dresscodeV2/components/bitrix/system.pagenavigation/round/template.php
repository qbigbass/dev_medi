<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @var CBitrixComponentTemplate $this */

$this->setFrameMode(true);

if (!$arResult["NavShowAlways"]) {
    if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
        return;
}
$delParam = array_merge(["calltouch_tm", "utm_source", "utm_campaign", "utm_term", "utm_medium", "backurl", "PAGEN_1", "PAGEN_2", "PAGEN_3", "PAGEN_4", "PAGEN_5", "PAGEN_6", "PAGEN_7", "PAGEN_8", "PAGEN_9", "PAGEN_10", "send", "r", "back_url", "PHPSESSID", "ajax", "yandex-source", "advert_id", "bxajaxid", "yadclid", "yadordid", "set_source"], \Bitrix\Main\HttpRequest::getSystemParameters());
$arResult['NavQueryString'] = htmlspecialcharsbx(DeleteParam($delParam));

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"] . "&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?" . $arResult["NavQueryString"] : "");
?>

<div class="bx-pagination">
    <div class="bx-pagination-container">
        <ul>
            <? if ($arResult["bDescPageNumbering"] === true): ?>
                
                <? if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]): ?>
                    <? if ($arResult["bSavePage"]): ?>
                        <li class="bx-pag-prev"><a
                                    href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] + 1) ?>"><span
                                        class="arrow_left active"
                                        title="<? echo GetMessage("round_nav_back") ?>"></span></a></li>
                        <li class=""><a
                                    href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] + 1) ?>"><span
                                        class="num">1</span></a></li>
                    <? else: ?>
                        <? if (($arResult["NavPageNomer"] + 1) == $arResult["NavPageCount"]): ?>
                            <li class="bx-pag-prev"><a href="<?= $arResult["sUrlPath"] ?><?= $strNavQueryStringFull ?>"><span
                                            class="arrow_left active"
                                            title="<? echo GetMessage("round_nav_back") ?>"></span></a></li>
                        <? else: ?>
                            <li class="bx-pag-prev"><a
                                        href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] + 1) ?>"><span
                                            class="arrow_left active"
                                            title="<? echo GetMessage("round_nav_back") ?>"></span></a></li>
                        <? endif ?>
                        <li class=""><a href="<?= $arResult["sUrlPath"] ?><?= $strNavQueryStringFull ?>"><span
                                        class="num">1</span></a></li>
                    <? endif ?>
                <? else: ?>
                    <li class="bx-pag-prev"><span class="arrow_left"
                                                  title="<? echo GetMessage("round_nav_back") ?>"></span></li>
                    <li class="bx-active"><span>1</span></li>
                <? endif ?>
                
                <?
                $arResult["nStartPage"]--;
                while ($arResult["nStartPage"] >= $arResult["nEndPage"] + 1):
                    ?>
                    <? $NavRecordGroupPrint = $arResult["NavPageCount"] - $arResult["nStartPage"] + 1; ?>
                    
                    <? if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
                    <li class="bx-active"><span class="num"><?= $NavRecordGroupPrint ?></span></li>
                <? else:?>
                    <li class=""><a
                                href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["nStartPage"] ?>"><span
                                    class="num"><?= $NavRecordGroupPrint ?></span></a></li>
                <? endif ?>
                    
                    <? $arResult["nStartPage"]-- ?>
                <? endwhile ?>
                
                <? if ($arResult["NavPageNomer"] > 1): ?>
                    <? if ($arResult["NavPageCount"] > 1): ?>
                        <li class=""><a
                                    href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=1"><span
                                        class="num"><?= $arResult["NavPageCount"] ?></span></a></li>
                    <? endif ?>
                    <li class="bx-pag-next"><a
                                href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] - 1) ?>"><span
                                    class="arrow_right active"
                                    title="<? echo GetMessage("round_nav_forward") ?>"></span></a></li>
                <? else: ?>
                    <? if ($arResult["NavPageCount"] > 1): ?>
                        <li class="bx-active"><span class="num"><?= $arResult["NavPageCount"] ?></span></li>
                    <? endif ?>
                    <li class="bx-pag-next"><span class="arrow_right"
                                                  title="<? echo GetMessage("round_nav_forward") ?>"></span></li>
                <? endif ?>
            
            <? else: ?>
                
                <? if ($arResult["NavPageNomer"] > 1): ?>
                    <? if ($arResult["bSavePage"]): ?>
                        <li class="bx-pag-prev"><a
                                    href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] - 1) ?>"><span
                                        class="arrow_left" title="<? echo GetMessage("round_nav_back") ?>"></span></a>
                        </li>
                        <li class=""><a
                                    href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=1"><span
                                        class="num">1</span></a></li>
                    <? else: ?>
                        <? if ($arResult["NavPageNomer"] > 2): ?>
                            <li class="bx-pag-prev"><a
                                        href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] - 1) ?>"><span
                                            class="arrow_left active"
                                            title="<? echo GetMessage("round_nav_back") ?>"></span></a></li>
                        <? else: ?>
                            <li class="bx-pag-prev"><a href="<?= $arResult["sUrlPath"] ?><?= $strNavQueryStringFull ?>"><span
                                            class="arrow_left active"
                                            title="<? echo GetMessage("round_nav_back") ?>"></span></a></li>
                        <? endif ?>
                        <li class=""><a href="<?= $arResult["sUrlPath"] ?><?= $strNavQueryStringFull ?>"><span
                                        class="num">1</span></a></li>
                    <? endif ?>
                <? else: ?>
                    <li class="bx-pag-prev"><span class="arrow_left"
                                                  title="<? echo GetMessage("round_nav_back") ?>"></span></li>
                    <li class="bx-active"><span class="num">1</span></li>
                <? endif ?>
                
                <?
                $arResult["nStartPage"]++;
                while ($arResult["nStartPage"] <= $arResult["nEndPage"] - 1):
                    ?>
                    <? if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
                    <li class="bx-active"><span class="num"><?= $arResult["nStartPage"] ?></span></li>
                <? else:?>
                    <li class=""><a
                                href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["nStartPage"] ?>"><span
                                    class="num"><?= $arResult["nStartPage"] ?></span></a></li>
                <? endif ?>
                    <? $arResult["nStartPage"]++ ?>
                <? endwhile ?>
                
                <? if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]): ?>
                    <? if ($arResult["NavPageCount"] > 1): ?>
                        <li class=""><a
                                    href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["NavPageCount"] ?>"><span
                                        class="num"><?= $arResult["NavPageCount"] ?></span></a></li>
                    <? endif ?>
                    <li class="bx-pag-next"><a
                                href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] + 1) ?>"><span
                                    class="arrow_right active"
                                    title="<? echo GetMessage("round_nav_forward") ?>"></span></a></li>
                <? else: ?>
                    <? if ($arResult["NavPageCount"] > 1): ?>
                        <li class="bx-active"><span class="num"><?= $arResult["NavPageCount"] ?></span></li>
                    <? endif ?>
                    <li class="bx-pag-next"><span class="arrow_right"
                                                  title="<? echo GetMessage("round_nav_forward") ?>"></span></li>
                <? endif ?>
            <? endif ?>
            
            <? if ($arResult["bShowAll"]): ?>
                <? if ($arResult["NavShowAll"]): ?>
                    <li class="bx-pag-all"><a
                                href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>SHOWALL_<?= $arResult["NavNum"] ?>=0"
                                rel="nofollow"><span><? echo GetMessage("round_nav_pages") ?></span></a></li>
                <? else: ?>
                    <li class="bx-pag-all"><a
                                href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>SHOWALL_<?= $arResult["NavNum"] ?>=1"
                                rel="nofollow"><span><? echo GetMessage("round_nav_all") ?></span></a></li>
                <? endif ?>
            <? endif ?>
        </ul>
        <div style="clear:both"></div>
    </div>
</div>
