<?
global $APPLICATION;
if (!empty(intval($_GET['PAGEN_1'])) && intval($_GET['PAGEN_1']) > 1){

    $ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arParams['IBLOCK_ID'],$arParams['SECTION_ID']);
   $IPROPERTY  = $ipropValues->getValues();

   $IPROPERTY['SECTION_META_DESCRIPTION'] =  $IPROPERTY['SECTION_META_DESCRIPTION'].' | Страница '.intval($_GET['PAGEN_1']);

   $APPLICATION->SetPageProperty("description",  $IPROPERTY['SECTION_META_DESCRIPTION']);

}

//$APPLICATION->AddHeadString('<link href="https://www.medi-salon.ru'.$APPLICATION->GetCurDir().'" rel="canonical">');
?>
<? if($arParams['SECTION_ID'] > 0):?>
    <script>
        var _gcTracker=_gcTracker||[];
        _gcTracker.push(['view_category', { category_id: '<?=$arResult['ID']?>'}]);
    </script>
<?endif;?>


