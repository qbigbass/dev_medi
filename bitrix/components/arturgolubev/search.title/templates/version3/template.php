<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

$INPUT_ID = trim($arParams["~INPUT_ID"]);
if(strlen($INPUT_ID) <= 0)
	$INPUT_ID = "smart-title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if(strlen($CONTAINER_ID) <= 0)
	$CONTAINER_ID = "smart-title-search";

$PRELOADER_ID = CUtil::JSEscape($CONTAINER_ID."_preloader_item");
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

// echo '<pre>'; print_r($arResult["VISUAL_PARAMS"]); echo '</pre>';

if($arParams["SHOW_INPUT"] !== "N"):?>
	<a href="#" class="openTopSearch" id="openSearch"></a>
	<div id="<?echo $CONTAINER_ID?>">
		<div class="limiter">
			<form action="/search/" id="topSearchForm">
				<div class="searchContainerInner">
					<div class="searchContainer">
						<div class="searchColumn">
							<input id="<?echo $INPUT_ID?>" placeholder="<?=$arParams["INPUT_PLACEHOLDER"]?>" type="text" name="q" value="<?=htmlspecialcharsbx($_REQUEST["q"])?>" autocomplete="off"/>
							<a href="#" id="topSeachCloseForm">Закрыть окно</a>
						</div>
						<div class="searchColumn">
							<span class="bx-searchtitle-preloader <?if($arResult["MODULE_SETTING"]["SHOW_PRELOADER"] == 'Y') echo 'view';?>" id="<?echo $PRELOADER_ID?>"></span>
							<input type="submit" name="send" value="Y" id="goSearch">
							<input type="hidden" name="r" value="Y">
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div id="searchResult"></div>
	<div id="searchOverlap"></div>
<?endif?>


<script>
	BX.ready(function(){
		new JCTitleSearchAG({
			// 'AJAX_PAGE' : '/your-path/fast_search.php',
			'AJAX_PAGE' : '<?echo CUtil::JSEscape(POST_FORM_ACTION_URI)?>',
			'CONTAINER_ID': '<?echo $CONTAINER_ID?>',
			'INPUT_ID': '<?echo $INPUT_ID?>',
			'PRELODER_ID': '<?echo $PRELOADER_ID?>',
			'MIN_QUERY_LEN': 2
		});
	});
</script>