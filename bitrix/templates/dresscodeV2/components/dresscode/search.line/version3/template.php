<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$this->setFrameMode(true);?>
<div id="topSearchMob">
    <div class="searchContainerInner">
        <div class="searchContainer" style="line-height:normal;">
            <div class="searchColumn">
                <input type="text" name="q" value="<?=!empty($arResult["q"]) ? $arResult["q"] : ""?>" autocomplete="off" placeholder="Поиск по каталогу" id="searchQueryMob">
            </div>
            <div class="searchColumn" style="padding-left:0;width:44px;">
                <input type="submit" name="send" value="Y" id="goSearchMob" style="width:44px;height:40px;background: url(/bitrix/templates/dresscodeV2/images/menu/telf.svg) 50% 50% no-repeat #868787;border-radius:0;border:0;background-size: 45%;">
                <input type="hidden" name="r" value="Y">
            </div>
        </div>
    </div>
    <div id="topSearchAdaptive">
        <form action="/search/" method="GET" id="topSearchFormMob">
            <div class="searchContainerInner">
                <div class="searchWrapper">
                    <button type="button" id="topSearchCloseFormAdaptive">
                        <img src="<?=SITE_TEMPLATE_PATH?>/images/arrow_left.jpg" alt="">
                    </button>
                    <div class="searchColumn">
                        <input type="text" name="q" value="<?=!empty($arResult["q"]) ? $arResult["q"] : ""?>" autocomplete="off" id="searchQueryAdaptive">
                        <div class="searchFinder">
                            <div class="tfl-popup__close"></div>
                            <button type="button" class="digi-search-form__submit">Найти</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="searchResultAdaptive"></div>
    <div id="searchOverlapAdaptive"></div>
</div>
<div class="topSearchDesktop">
    <input type="text" name="qf" autocomplete="false" class="b_head_search_input" placeholder="Поиск по каталогу"  id="openSearch"/><input type="submit" class="b_head_search_but" value=""/>
    <div id="topSearch3">
        <div class="limiter">
            <form action="/search/" method="GET" id="topSearchForm">
                <div class="searchContainerInner">
                    <div class="searchContainer">
                        <div class="searchColumn">
                            <input type="text" name="q" value="<?=!empty($arResult["q"]) ? $arResult["q"] : ""?>" autocomplete="off" placeholder="<?=GetMessage("SEARCH_TEXT")?>" id="searchQuery">
                            <a href="#" id="topSeachCloseForm"><?=GetMessage("SEARCH_CLOSE_BUTTON")?></a>
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
	var searchAjaxPath = "<?=$componentPath?>/ajax.php";
	var searchProductParams = '<?=\Bitrix\Main\Web\Json::encode($arParams);?>';
</script>
