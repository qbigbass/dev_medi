<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult
 */
$this->setFrameMode(true);
?>
<?
if ($arResult['ID'] && $arResult['HIDE'] != 'Y'):
?>
 <div class="top_alert desktop" >
    <span class="close" data-id="<?=$arResult['ID']?>"></span>
    <div class="top_alert_content">
        <div class="warn-wrap">
            <?if ($arResult['PROPERTY_LINK_VALUE'] != "" ){?><a href="<?=$arResult['PROPERTY_LINK_VALUE']?>"><?}?>
                <img width="100%" src="<?=$arResult['DETAIL_PICTURE']['SRC'];?>" height="100%" class="big-pic-medi" alt="">
            <?if ($arResult['PROPERTY_LINK_VALUE'] != "" ){?></a><?}?>
        </div>
    </div>
 </div>
<?endif;