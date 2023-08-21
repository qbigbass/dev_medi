<?php
try{
	$module = CModule::CreateModuleObject('measoft.courier');
	$version = $module->MODULE_VERSION;
}catch(\Exception $e){
	$version = time();
}

?>
<script src="https://home.courierexe.ru/js/measoft_map.js?v=<?php print $version; ?>" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="/bitrix/components/measoft.courier/js/lightpick/lightpick.css">

<script src="/bitrix/components/measoft.courier/js/lightpick/moment.min.js" type="text/javascript"></script>
<script src="/bitrix/components/measoft.courier/js/lightpick/lightpick.js" type="text/javascript"></script>


<script>
    $(document).on('focus', '#ms_date_putn', function() {
        if (picker != 0)
        {
            picker.destroy()
        }

        console.log("measoft calendar init");

        var profileId = document.getElementById('ms_date_putn').getAttribute("mid");

        if ( !measoftProfileConfig[profileId]["DISABLE_CALENDAR"] )
        {
            picker = new Lightpick({
                field: document.getElementById('ms_date_putn'),
                format: "DD.MM.YYYY"
            });
        }

    });
</script>

<div id="measoftMapBlock" class="<?= ($arResult["HIDE_MAP_EDITS"]=="Y") ? "hide-filter" : "" ?> <?= ($arResult["HIDE_MAP_SEARCH"]=="Y") ? "hide-search" : "" ?>">

</div>

<input type="hidden" name="city_code" value="153361" id="sel-city-code" >

<style>
    #measoftMapBlock #open_map_button { display: none !important; }
    #measoftMapBlock { z-index: 99999; position: fixed; left: 50%; top: 50%; margin-left: -325px; margin-top: -225px;}
    .pvz-select-holder, #pvz-info-holder {margin-top: 5px;}
    .hide-filter #map_filter_block { display: none !important; }
    .hide-search #map_filter_address_block  { display: none !important; }
    .hide-filter.hide-search #map_content_block { height: 415px !important; }
    .hide-search #map_content_block { height: 410px !important; }
    .hide-filter #map_content_block { height: 400px !important; }
</style>

<script defer type="text/javascript">
    var measoftObject = 0;
    var cityCoords = 0;
    var orderWeight = <?= isset($arResult["orderWeight"]) ? $arResult["orderWeight"] : 50 ?>;
    var pvzInputs = [<?=substr($arResult['PROP_ADDRESS'],0,-1)?>];
    var measoftProfileConfigStr = '<?= $arResult["courierArrSettings"] ?>';
    var measoftProfileConfig = JSON.parse( measoftProfileConfigStr );
    console.log(measoftProfileConfig);


    console.log("pvzInputs=" + pvzInputs);

    var currentProfileId = 0;
    var chznPnkt = 0;

    for(var i in pvzInputs){
        if(typeof(pvzInputs[i]) == 'function') continue;
        chznPnkt = $('[name="ORDER_PROP_'+ pvzInputs[i]+'"]');
        if(chznPnkt.length>0)
            break;
    }

    console.log("chznPnkt::");
    console.log(chznPnkt);

    function  measoftObjectInit(profileId) {
        currentProfileId = profileId;

        var request = BX.ajax.runComponentAction('measoft.courier:pickup', 'getCoords', {
            mode:'class',
        });

        request.then(function(resp){
            if(resp.status === 'success') {

                cityCoordsA = JSON.parse(resp.data);
                cityCoords = [ cityCoordsA.lat, cityCoordsA.lon ];

                p1 = cityCoordsA.lat; // '55.755814';
                p2 = cityCoordsA.lon;// '37

                BX("sel-city-code").value = cityCoordsA.code;

                setTimeout( function(){

                    console.log("orderWeight=" + orderWeight);

                    console.log("MAP_CLIENT_CODE=" + measoftProfileConfig[currentProfileId].MAP_CLIENT_CODE);


                    measoftObject = measoftMap.config({
                        'windowFixedPosition' : '1',
                        'townBlock' : 'sel-city-code',
                        'mapBlock': 'measoftMapBlock',
                        'client_id': measoftProfileConfig[currentProfileId].USER_CODE,
                        'client_code': measoftProfileConfig[currentProfileId].MAP_CLIENT_CODE,
                        'mapSize': {
                            'width': '650',
                            'height': '450'
                        },

                        'centerCoords': [p1, p2],
                        'showMapButton': '1',
                        'showMapButtonCaption': '<?= GetMessage("MEASOFT_MAP_BUTTON_CAPTION"); ?>',
                        'filter': {
                            //'acceptcard': 'YES',
                            'maxweight' : orderWeight,
                        },
                        'allowedFilterParams': ['acceptcash', 'acceptcard', 'acceptfitting','store'], //
                        'choicePvzCallback' : function (selCode) { console.log("selCode=" + selCode);
                            getSelectedPvzData = measoftMap.getSelectedPvzData();
                            console.log(getSelectedPvzData);

                            BX("pvz-info-holder").innerHTML = '<ul class="bx-soa-pp-list"><li><div class="bx-soa-pp-list-termin"><?= GetMessage("MEASOFT_PVZ_SELECT_ON_MAP"); ?>:</div><div class="bx-soa-pp-list-description">'+ getSelectedPvzData.address +'</div></li></ul>';

                            var requestPvz = BX.ajax.runComponentAction('measoft.courier:pickup', 'pvzSelected', {
                                mode:'class',
                                data: {
                                    pickupArr: getSelectedPvzData
                                }
                            });

                            requestPvz.then(function(resp) {
                                if (resp.status === 'success') {
                                    console.log(resp);
                                    submitFormProxy();
                                }
                            });

                            for(var i in pvzInputs){
                                if(typeof(pvzInputs[i]) == 'function') continue;

                                chznPnkt = $('#ORDER_PROP_'+pvzInputs[i]);
                                if(chznPnkt.length<=0 || chznPnkt.get(0).tagName != 'INPUT')
                                    chznPnkt = $('[name="ORDER_PROP_'+pvzInputs[i]+'"]');
                                if(chznPnkt.length>0){
                                    chznPnkt.val(getSelectedPvzData.address);
                                    chznPnkt.css('background-color', '#eee').attr('readonly','readonly');
                                    break;
                                }
                            }

                            $("#MEASOFT_PVZ_CODE").val( getSelectedPvzData.code );
                            $("#MEASOFT_PVZ_ADDRESS").val( getSelectedPvzData.address );
                            $("#MEASOFT_PVZ_PHONE").val( getSelectedPvzData.phone );
                            $("#MEASOFT_PVZ_WORKTIME").val( getSelectedPvzData.worktime );
                        }
                    }).init();

                    $("#measoftMapBlock").attr("class", measoftProfileConfig[currentProfileId].MAP_CSS);

                    measoftMap.open('start');
                }, 100 );
            }
        });
    }
</script>