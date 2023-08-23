<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

$INPUT_ID = trim($arParams["~INPUT_ID"]);
if (strlen($INPUT_ID) <= 0)
    $INPUT_ID = "smart-title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if (strlen($CONTAINER_ID) <= 0)
    $CONTAINER_ID = "smart-title-search";

$PRELOADER_ID = CUtil::JSEscape($CONTAINER_ID . "_preloader_item");
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);
?>
<form action="/search/" method="GET" id="topSearchFormMob">
    <div class="searchContainerInner">
        <div class="searchContainer" style="line-height:normal;">
            <div class="searchColumn">
                <input type="search" name="q" value="<?= htmlspecialcharsbx($_REQUEST["q"]) ?>"
                       autocomplete="off" placeholder="Поиск по каталогу" id="searchQueryMob">
            </div>
            <div class="searchColumn" style="padding-left:0;width:44px;">
                <input type="submit" name="send" value="Y" id="goSearchMob"
                       style="width:44px;height:40px;background: url(/bitrix/templates/dresscodeV2/images/menu/telf.svg) 50% 50% no-repeat #868787;border-radius:0;border:0;background-size: 45%;">
                <input type="hidden" name="r" value="Y">
            </div>
        </div>
    </div>
</form>


<?php
/* hints */
$arResult["HINTS"] = array();
if (is_array($arParams["ANIMATE_HINTS"])) {
    foreach ($arParams["ANIMATE_HINTS"] as $k => $v) {
        $v = trim($v);
        if ($v) {
            $arResult["HINTS"][] = $v;
        }
    }
}

if (count($arResult["HINTS"])) {
    CJSCore::Init(array("ag_smartsearch_type"));
    $arParams["INPUT_PLACEHOLDER"] = '';
    $arParams["ANIMATE_HINTS_SPEED"] = (intval($arParams["ANIMATE_HINTS_SPEED"]) ? intval($arParams["ANIMATE_HINTS_SPEED"]) : 1);
}
/* end hints */

?>

<script>
    BX.ready(function () {
        new JCTitleSearchAG({
            // 'AJAX_PAGE' : '/your-path/fast_search.php',
            'AJAX_PAGE': '<?echo CUtil::JSEscape(POST_FORM_ACTION_URI)?>',
            'CONTAINER_ID': '<?echo $CONTAINER_ID?>',
            'INPUT_ID': '<?echo $INPUT_ID?>',
            'PRELODER_ID': '<?echo $PRELOADER_ID?>',
            'MIN_QUERY_LEN': 2
        });
        
        <?if(count($arResult["HINTS"])):?>
        new Typed('#<?echo $INPUT_ID?>', {
            strings: <?=CUtil::PhpToJSObject($arResult["HINTS"]);?>,
            typeSpeed: <?=$arParams["ANIMATE_HINTS_SPEED"] * 20?>,
            backSpeed: <?=$arParams["ANIMATE_HINTS_SPEED"] * 10?>,
            backDelay: 500,
            startDelay: 1000,
            // smartBackspace: true,
            bindInputFocusEvents: true,
            attr: 'placeholder',
            loop: true
        });
        <?endif;?>
    });
</script>