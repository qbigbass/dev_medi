<?
$property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>26, "CODE"=>"SITE"));
while($enum_fields = $property_enums->GetNext())
{
   if ($enum_fields["XML_ID"] == SITE_ID) {
       $site_id = $enum_fields['ID'];
    }
}

$arFilter = ["IBLOCK_ID"=>26, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y"];
if ($site_id)
{
    $arFilter["PROPERTY_SITE"] = $site_id;
}
if (!empty($_SESSION['top_alert_hide']))
{
    $arFilter['!ID'] = $_SESSION['top_alert_hide'];
}
$obElement = CIBlockElement::GetList(["SORT"=>"ASC"], $arFilter, false, false, ["ID", "NAME", "PREVIEW_TEXT", "PREVIEW_PICTURE", "PROPERTY_LINK", "PROPERTY_HEIGHT","PROPERTY_BG","PROPERTY_COLOR", "PROPERTY_IMG_POS"]);
if ($arElement = $obElement->GetNext() ){

     if (!$_SESSION['top_alert_hide'][$arElement['ID']]){
         $height = ($arElement['PROPERTY_HEIGHT_VALUE'] > 0 ? intval($arElement['PROPERTY_HEIGHT_VALUE']) : 90);
         $bg = ($arElement['PROPERTY_BG_VALUE'] != "" ? $arElement['PROPERTY_BG_VALUE'] : "none");
         $color = ($arElement['PROPERTY_COLOR_VALUE'] != "" ? $arElement['PROPERTY_COLOR_VALUE'] : "#fff");
         ?>
         <div class="top_alert" style="height:<?=$height;?>px;background-color:<?=$bg;?>;color:<?=$color;?>;">
         	<span class="close" data-id="<?=$arElement['ID']?>"></span>
         	<div class="top_alert_content">

         		<?if (!empty($arElement['PREVIEW_PICTURE'])){?>
         		<div class="ta_picture <?=($arElement['PROPERTY_IMG_POS_VALUE'] == 'Слева' ? 'left' : ($arElement['PROPERTY_IMG_POS_VALUE'] == 'Справа' ? 'right' : ''))?>" style="background:url(<?=$arElement['PREVIEW_PICTURE']['SRC'];?>) no-repeat 50% 50%; height:<?=$height;?>px;"></div>
         		<?}?>
         		<?if ($arElement['PROPERTY_LINK_VALUE'] != "" ){?><a href="<?=$arElement['PROPERTY_LINK_VALUE']?>"><?}?>
         		<?=$arElement["PREVIEW_TEXT"];?>
         		<?if ($arElement['PROPERTY_LINK_VALUE'] != "" ){?></a><?}?>

         	</div>
         </div>
    <?
    }
}
