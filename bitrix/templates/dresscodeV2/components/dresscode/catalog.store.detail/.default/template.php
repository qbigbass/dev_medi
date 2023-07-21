<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div id="storeDetail" >
<div class="flex storeHeadInfo">
  <div class="flex-item">
    <?if (!empty($arResult["ADDRESS"])): ?>
    <div class="storeInformationName">
      <?=$arResult["ADDRESS"]?>
    </div>
    <?endif; ?>
  </div>
  <div class="flex-item"> <span class="salon-metro">
    <?if ($arResult['METRO']) {
                    foreach ($arResult['METRO'] AS $metro) {
                    if($metro['SECTION']['ICON']['SRC']){?>
    <img src="<?=$metro['SECTION']['ICON']['SRC']?>" alt="<?=$metro['SECTION']['NAME']?>" title="<?=$metro['SECTION']['NAME'] ?>"/>
    <?}?>
    &nbsp;м.&nbsp;
    <?=$metro['NAME'] ?>
    <?}
                } ?>
    </span> <span class="salon-print text-right"><a href="#" class="active-link" onClick="window.print();return false;">Распечатать</a></span> </div>
</div>
<div class="storeDetailContainer flex">
  <div class="storeIfno flex-item">
    <div class="storeInformation ">
      <?if(!empty($arResult["DESCRIPTION"])):?>
      <div class="storeInformationDescription">
        <?=$arResult["DESCRIPTION"]?>
      </div>
      <?endif;?>
      <?if(!empty($arResult["UF_VECTOR"])):?>
      <div class="storeInformationDescription"><b class="storeToolsItemLabel">Как проехать:</b>
        <?=$arResult["UF_VECTOR"] ?>
      </div>
      <?endif;?>
    </div>
    <?if(!empty($arResult["SCHEDULE"])):?>
    <div class="storeToolsItem"> <span class="storeToolsItemLabel">
      <?=GetMessage("S_SCHEDULE")?>
      </span>
      <?=$arResult["SCHEDULE"]?>
    </div>
    <?endif;?>
    <?if(!empty($arResult["HOLIDAY_SHEDULE"])):?>
    <div class="storeToolsItem"> <span class="storeToolsItemLabel"><span id="holiday_shedule">
      <?=$arResult["HOLIDAY_SHEDULE"]["NAME"]?>
      </span></span>
      <div id="holiday_shedule_text">
        <?=$arResult["HOLIDAY_SHEDULE"]["PREVIEW_TEXT"]?>
      </div>
    </div>
    <?endif;?>
    <?if(!empty($arResult["PHONE"])):?>
    <div class="storeToolsItem"> <span class="storeToolsItemLabel">
      <?=GetMessage("S_PHONE")?>
  </span> <a href="tel:<?=$arResult["PHONE"]?>" id="GTM_salon_phone" class="salon_phone">
      <?=$arResult["PHONE"]?>
      </a> </div>
    <?endif;?>
    <?if(!empty($arResult["EMAIL"])):?>
    		<div class="storeToolsItem">
    			<span class="storeToolsItemLabel"><?=GetMessage("S_EMAIL")?></span>
    			<a href="mailto:<?=$arResult["EMAIL"]?>"><?=$arResult["EMAIL"]?></a>
  	     </div>
    		<?endif;?>
    <?
        if (($arResult["GPS_N"]) != 0 && ($arResult["GPS_S"]) != 0): ?>
    <div class="storeDetailMap ">
      <?
        $gpsN = substr($arResult["GPS_N"], 0, 15);
        $gpsS = substr($arResult["GPS_S"], 0, 15);
        $gpsText = $arResult["ADDRESS"];
        $gpsTextLen = strlen($arResult["ADDRESS"]);
        ?>
      <div id="map_salon"></div>
      <script>
          var map;
          ymaps.ready(initMap);

          function initMap()
          {

              features = [{
                'type':'Feature',
                'id':'<?=$arResult['ID']?>',
                'geometry':{
                  'type':'Point',
                  'coordinates':[<?=$gpsN?>, <?=$gpsS?>]},
                  'properties':{
                    'hintContent':'<?=$arResult['NAME']?>',
                    'balloonContentHeader':'<div class=\"bheader\"><?=$arResult['NAME']?><\/div>',
                    'balloonContentBody':'<p class=\"city_value address\" ><?=$arResult['ADDRESS']?><\/p>\n<p class=\"city_value worktime\" ><?=$arResult['SCHEDULE']?><\/p>',
                    'balloonContentFooter':'<div class=\"bfooter\"><\/div>'
                  }
                }];

              map = new ymaps.Map("map_salon", {
                  center: [<?=$gpsN?>, <?=$gpsS?>],
                  zoom: 16,
                  controls: ['geolocationControl', 'fullscreenControl', 'zoomControl']
              });
              map.options.set( {
                  suppressMapOpenBlock: true,
              });

              mediObjectManager = new ymaps.ObjectManager({
                  clusterize: false,
                  geoObjectIconLayout: 'default#image',
                  geoObjectIconImageHref:  '//<?=SITE_SERVER_NAME?><?=SITE_TEMPLATE_PATH;?>/images/placemarker.png',
                  geoObjectIconImageSize: [28, 37],
                  geoObjectIconImageOffset: [-14, -37]
              });


              map.geoObjects.add(mediObjectManager);
              if (features !== undefined) {
                  mediObjectManager.add({
                      type: 'FeatureCollection',
                      features: features
                  });
              }
          }
      </script>
    </div>
    <?endif; ?>
    <div class="clear"></div>
  </div>
  <div class="storePictures flex-item">
    <div class=" storePictureContainer">
      <?if(!empty($arResult["IMAGES"])):?>
      <div id="pictureContainer">
        <div class="pictureSlider">
          <?foreach ($arResult["IMAGES"] as $ipr => $arNextPicture):?>
          <div class="item"> <a href="<?=$arNextPicture["BIG"]["SRC"]?>" title=""  class="zoom" data-small-picture="<?=$arNextPicture["SMALL"]["src"]?>" data-large-picture="<?=$arNextPicture["BIG"]["SRC"]?>"><img src="<?=$arNextPicture["MED"]["src"]?>" alt="<?=$arResult['TITLE'];?>" title="<?=$arResult['TITLE']; ?>"></a> </div>
          <?endforeach;?>
        </div>
      </div>
      <div id="moreImagesCarousel"<?if(empty($arResult["IMAGES"]) || count($arResult["IMAGES"]) <= 1):?> class="hide"<?endif;?>>
        <div class="carouselWrapper">
          <div class="slideBox">
            <?if(empty($arResult["IMAGES"]) || count($arResult["IMAGES"]) > 1):?>
            <?foreach ($arResult["IMAGES"] as $ipr => $arNextPicture):?>
            <div class="item"> <a href="<?=$arNextPicture["BIG"]["SRC"]?>" data-large-picture="<?=$arNextPicture["BIG"]["SRC"]?>" data-small-picture="<?=$arNextPicture["SMALL"]["src"]?>"> <img src="<?=$arNextPicture["SMALL"]["src"]?>" alt=""> </a> </div>
            <?endforeach;?>
            <?endif;?>
          </div>
        </div>
        <div class="controls"> <a href="#" id="moreImagesLeftButton"></a> <a href="#" id="moreImagesRightButton"></a> </div>
      </div>
      <?endif;?>
      <div class="clear"></div>
    </div>
  </div>
</div>
<?if (!empty($arResult['ASSORTMENT'])) {    ?>
<div class="flex storeFootInfo" >
  <div class="flex-item">
    <div class="storeAssortment"> <span class="storeToolsItemLabel">Ассортимент салона:</span>
      <? foreach ($arResult['ASSORTMENT'] AS $ks=>$arAssort) {
                foreach ($arAssort AS $k=>$assort) {
            ?>
      <span class="assortmentItem">
      <?if ($assort['LINK']){?>
      <a href="<?=$assort['LINK']?>" class="link-dashed">
      <?=$assort['NAME'] ?>
      </a>
      <?}else{?>
      <?=$assort['NAME']?>
      <?}?>
      </span>
      <?}
            }?>
    </div>
  </div>
  <?} ?>
  <?if (!empty($arResult['SERVICES'])) { ?>
  <div class="flex-item">
    <div class="storeServices"> <span class="storeToolsItemLabel">Услуги в салоне:</span>
      <?foreach ($arResult['SERVICES'] AS $sort=>$arService) {
                foreach ($arService AS $k=>$service) {          ?>
      <span class="servicetItem <?if ($service['LINK']) { ?>question link-dashed" data-href="<?=$service['LINK']?>"  data-description="<?=$service['DESC']?>"<?}?>">
      <?=$service['NAME'] ?>
      </span>
      <?}
                } ?>
    </div>
  </div>
  <?} ?>
</div>

<div itemscope itemtype="http://schema.org/LocalBusiness" class="microdata">
    <span itemprop="name"><?=$arResult['UF_STORE_PUBLIC_NAME'];?></span>
    <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
        <span itemprop="streetAddress"><?=str_replace($arResult['CITY'].", ", "", $arResult["ADDRESS"])?></span>
        <span itemprop="postalCode"><?=substr($arResult["~ADDRESS"], 0,  6)?></span>
        <span itemprop="addressLocality"><?=$arResult['CITY']?></span>,
    </div>
    <span itemprop="telephone"><?=$arResult['PHONE']?></span>
    <span itemprop="email"><?=$arResult['EMAIL']?></span>
    <span itemprop="openingHours"><?=$arResult["SCHEDULE"]?></span>
    <div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
        <span itemprop="latitude" ><?=$gpsN?></span>
        <span itemprop="longitude" ><?=$gpsS?></span>
    </div>
</div>
