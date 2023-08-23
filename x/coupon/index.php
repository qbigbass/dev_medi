<?
define("NO_HEAD_BREADCRUMB", "Y");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Получить промокод | Официальный интернет-магазин medi"); ?>
<? global $USER, $APPLICATION;
$APPLICATION->AddHeadScript("https://www.google.com/recaptcha/api.js?render=6LfbK6IlAAAAACWPKHjNecUsBIO_XHWWjUtls77I");
$APPLICATION->AddHeadScript("/x/coupon/script.min.js");
$APPLICATION->SetAdditionalCSS("/x/coupon/style.min.css");
?>
<?php
$iblockId = 38; // ID инфоблока купонных акций

$arFilter = ["IBLOCK_ID" => $iblockId, "ACTIVE" => "Y"];
if ($actionId > 0) {
    $arFilter['ID'] = $actionId;
}
$dbRes = CIBlockSection::GetList(['sort' => 'asc'], $arFilter, false, ["ID", "NAME", "DESCRIPTION", "UF_*"]);

if ($arAction = $dbRes->Fetch()) {
    if ($arAction['UF_HEAD_IMG']) {
        $arAction['HEAD_IMG'] = CFile::GetFileArray($arAction['UF_HEAD_IMG']);
    }
    ?>

    <div class="coupon_page">
        <? /*<div class="coupon_head">       </div>
        <div class="coupon_title">Купоны акции &laquo;<?= $arAction['NAME'] ?>&raquo;</div>
        <span class="coupons_count">oсталось&nbsp;<span id="coupons_count">-</span></span>
*/ ?>
        <? if (!empty($arAction['HEAD_IMG'])) { ?>
            <div class="head_img">
                <img src="<?= $arAction['HEAD_IMG']['SRC'] ?>" title="<?= $arAction['NAME'] ?>"/>
            </div>
        
        <? } else { ?>
            <div class="coupon_title"><?= $arAction['NAME'] ?></div>
        <? } ?>
        <span class="coupons_count">Осталось&nbsp;<span id="coupons_count">-</span></span>
        
        <? if (!empty($arAction['DESCRIPTION'])) { ?>
            <div class="coupon_desc"><?= $arAction['DESCRIPTION'] ?></div>
        <? } ?>
        <div class="coupon_form">
            <div class="text-danger tr alert " id="requestResult"></div>
            <form id="couponForm">
                <input type="hidden" name="action" id="action_id" value="<?= $arAction['ID'] ?>"/>
                <input type="hidden" name="goto" value="<?= $arAction['UF_REDIRECT'] ?>"/>
                <input type="hidden" name="sessid" id="sessid" value="<?= bitrix_sessid(); ?>"/>
                <div class="tr">
                    <label for="name">Как к вам обращаться:<span class="starrequired">*</span></label><br/>
                    <input type="text" id="name" name="name" class=""
                           value="<?= ($USER->IsAuthorized() ? $USER->GetFirstName() : '') ?>" required>
                </div>
                <div class="tr">
                    <label for="phone">Телефон:<span class="starrequired">*</span></label><br/>
                    <input type="tel" id="phone" name="phone" class="phonemask" value="" required>
                </div>
                <div class="tr agree">
                    <input type="checkbox" class="personalInfoField" id="agree" checked name="personalInfo" value="Y">
                    <label class="label-for" for="agree">
                        Я соглашаюсь с&nbsp;<a href="/legality/policy/" style="color: #3e3e3e;font-size: 14px;"
                                               target="_blank">Политикой
                            в отношении обработки персональных данных.</a>
                        <span class="starrequired">*</span>
                    </label>
                </div>
                <br>
                <div class="tr submit">
                    <button type="submit" class="submit_button" <?= ($arAction['UF_BUT_COLOR'] != '' ?
                        'style="background:' . $arAction['UF_BUT_COLOR'] : '') ?>">Получить купон</button>
                </div>
                
                <? if (!empty($arAction['UF_REDIRECT'])) { ?>
                    <div class="tr conditions_link">
                        <a href="<?= $arAction['UF_REDIRECT'] ?>" style="color: #3e3e3e;font-size: 14px;"
                           target="_blank">Посмотреть условия акции</a>
                    </div>
                <? } ?>
            </form>
        </div>
        <? if (!empty($arAction['UF_COUPON_ACTION_DESC'])) { ?>
            <div class="attention"><?= (htmlspecialchars_decode($arAction['UF_COUPON_ACTION_DESC'])); ?></div><br>
            <br>
        <? } ?>
    </div>
    
    <?
} else {
    ?>
    <div class="coupon_page">
        <div class="coupon_head">
            <div class="coupon_title">Произошла ошибка</div>
        </div>
        <div class="text-danger tr" style="display: block">
            <p>К сожалению, в данный момент, нет активных предложений.</p>
            <p><a href="/lk"/>Присоединяйтесь к Клубу лояльности medi</a> и&nbsp;первыми узнавайте о&nbsp;начале
                наших новых специальных предложениий.</p>
        </div>
    </div>
    <br><br>
    <?
} ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
