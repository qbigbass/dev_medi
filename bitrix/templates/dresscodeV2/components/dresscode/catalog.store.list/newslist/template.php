<?if (!empty($arResult["STORES"])):?>
<ul class="store_list_in_text">
<?foreach ($arResult["STORES"] as $ins => $arNextStore):?>
<li><a href="<?=$arNextStore["DETAIL_PAGE_URL"] ?>" class="storesListTableLink theme-link-dashed"><?=$arNextStore["TITLE"] ?></a></li>
 <?endforeach;?>
</ul>
 <?endif;?>
