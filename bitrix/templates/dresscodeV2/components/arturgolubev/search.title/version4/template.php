<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>
<? $INPUT_ID = trim($arParams["~INPUT_ID"]);
if (strlen($INPUT_ID) <= 0)
    $INPUT_ID = "smart-title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if (strlen($CONTAINER_ID) <= 0)
    $CONTAINER_ID = "smart-title-search";

$PRELOADER_ID = CUtil::JSEscape($CONTAINER_ID . "_preloader_item");
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID); ?>
<div id="topSearchMob">
    <form action="/search/" method="GET" id="topSearchFormMob">
        <div class="searchContainerInner">
            <div class="searchContainer" style="line-height:normal;">
                <div class="searchColumn">
                    <input type="text" name="q" value="<?= !empty($arResult["q"]) ? $arResult["q"] : "" ?>"
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
</div>
<div class="topSearchDesktop">
    <input type="text" name="qf" autocomplete="false" class="b_head_search_input" placeholder="Поиск по каталогу"
           id="openSearch"/><input type="submit" class="b_head_search_but" value=""/>
    <div id="topSearch3">
        <div class="limiter">
            <form action="/search/" method="GET" id="topSearchForm">
                <div class="searchContainerInner">
                    <div class="searchContainer">
                        <div class="searchColumn">
                            <input type="text" name="q" value="<?= !empty($arResult["q"]) ? $arResult["q"] : "" ?>"
                                   autocomplete="off" placeholder="<?= GetMessage("SEARCH_TEXT") ?>" id="searchQuery">
                            <a href="#" id="topSeachCloseForm"><?= GetMessage("SEARCH_CLOSE_BUTTON") ?></a>
                        </div>
                        <div class="searchColumn">
                            <input type="submit" name="send" value="Y" id="goSearch">
                            <input type="hidden" name="r" value="Y">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="searchResult"></div>
<div id="searchOverlap"></div>
<script>
    var searchAjaxPath = "<?=$templateFolder ?>/ajax.php";
    var searchProductParams = '<?=\Bitrix\Main\Web\Json::encode($arParams);?>';
</script>
