<?	global $APPLICATION;
CJSCore::Init(array("jquery"));
$widget_url = COption::GetOptionString('up.boxberrydelivery_spb', 'WIDGET_URL');
$APPLICATION->AddHeadScript($widget_url);
IncludeModuleLangFile(__FILE__);

if (!function_exists('findParentBXB_spb')) {
    function findParentBXB_spb($profiles){
        if ($profiles['CODE']=='boxberry_spb'){
            return $profiles['ID'];
        }
    }
}

$allDeliverys_spb = \Bitrix\Sale\Delivery\Services\Manager::getActiveList();
$parent = array_filter ($allDeliverys, 'findParentBXB_spb');
$boxberry_profiles_spb=array();

foreach ($allDeliverys_spb as $profile){
    foreach ($parent as $key=>$value){
        if($profile["PARENT_ID"]==$key && (strpos($profile['CODE'],'PVZ_COD')!==false)){
			$boxberry_profiles_cod_spb[] = $profile["ID"];
		}elseif($profile["PARENT_ID"]==$key && (strpos($profile['CODE'],'PVZ')!==false)){
            $boxberry_profiles_spb[] = $profile["ID"];
        }
	}
}

$bxbOptions_spb['address'] = COption::GetOptionString('up.boxberrydelivery_spb', 'BB_ADDRESS');
$bxbOptions_spb['bb_custom_link'] = COption::GetOptionString('up.boxberrydelivery_spb', 'BB_CUSTOM_LINK');
$bxbOptions_spb['bb_paid_person_ph'] = COption::GetOptionString('up.boxberrydelivery_spb', 'BB_PAID_PERSON_PH');
$bxbOptions_spb['bb_paid_person_jur'] = COption::GetOptionString('up.boxberrydelivery_spb', 'BB_PAID_PERSON_JUR');


$arOrderProps_spb = array();
$arOrderPropsCode_spb = array();
$dbProps_spb = CSaleOrderProps::GetList(
    array("PERSON_TYPE_ID" => "ASC", "SORT" => "ASC"),
    array(),
    false,
    false,
    array()
);

$adminBoxberry_spb = true;
while ($arProps_spb = $dbProps_spb->GetNext())
{
    if(strlen($arProps_spb["CODE"]) > 0)    {
        $arOrderPropsCode_spb[$arProps_spb["CODE"]][$arProps_spb["PERSON_TYPE_ID"]] = $arProps_spb;
    }
}
$bxbOptions_spb['bb_paid_person_ph'] = (!empty($bxbOptions_spb['bb_paid_person_ph']) ? $bxbOptions_spb['bb_paid_person_ph'] : 1);
if ($arOrder = CSaleOrder::GetByID($_REQUEST['orderId']))
{
   if (strpos($arOrder['DELIVERY_ID'], 'boxberry_spb') === false ){ $adminBoxberry_spb=false; }

}
?>
<? if ($adminBoxberry_spb){?>
<script>
    var bxb_errors_spb = [];
    var bx_soa_delivery_spb = false;
    var bb_custom_link_spb = false;
	var selected_cod_profile_spb = true;
    var boxberry_delivery_profiles_spb = {};
		boxberry_delivery_profiles_spb.widget=<?=CUtil::PhpToJSObject($boxberry_profiles_spb)?>;
		boxberry_delivery_profiles_spb.widget_cod=<?=CUtil::PhpToJSObject($boxberry_profiles_cod_spb)?>;
		boxberry_delivery_profiles_spb.module_addr_options=<?=CUtil::PhpToJSObject($arOrderPropsCode_spb[$bxbOptions_spb['address']])?>;



    function admin_delivery_spb(result){
		 $.ajax({
                url: '/bitrix/js/up.boxberrydelivery_spb/ajax.php',
                type: 'POST',
                dataType: 'JSON',
                data: {save_admin_pvz_id:result.id,order_id:selected_bxb_id,address:'Boxberry: '+ result.address + " #" + result.id},
                success:function(data){$('.js-bxb-select-'+selected_bxb_id).html(result.id);}
            });
	}
    function delivery_spb(result){
		if (typeof(selected_bxb_id) !== 'undefined'){
			if ($('.js-bxb-select-'+selected_bxb_id).length > 0){
				$.ajax({
					url: '/bitrix/js/up.boxberrydelivery_spb/ajax.php',
					type: 'POST',
					dataType: 'JSON',
					data: {change_pvz_id:result.id,order_id:selected_bxb_id,address:'Boxberry: '+ result.address + " #" + result.id, change_location:result.name},
					success:function(data){$('.js-bxb-select-'+selected_bxb_id).html(result.id);}
				});
			}
		}
        if (boxberry_delivery_profiles_spb.widget_element != undefined){
            element = document.getElementById(boxberry_delivery_profiles_spb.widget_element);
        }else{
            person_type = $('input[name="PERSON_TYPE"]:checked').val();
            if (person_type != undefined){
                prop_id = boxberry_delivery_profiles_spb.module_addr_options[person_type].ID
            }else{
                prop_id = boxberry_delivery_profiles_spb.module_addr_options[<?=$bxbOptions_spb['bb_paid_person_ph'];?>].ID
            }
            element = document.getElementById('ORDER_PROP_'+ prop_id);
        }
        if (element != undefined){
            element.value = 'Boxberry: '+ result.address + " #" + result.id ;
            if (boxberry_delivery_profiles_spb.widget_element != undefined){
                bxb_errors_spb=[];
            }
            $.ajax({
                url: '/bitrix/js/up.boxberrydelivery_spb/ajax.php',
                type: 'POST',
                dataType: 'JSON',
                data: {save_pvz_id:result.id, change_location:result.name},
                success:function(data){checkSelectPvz_spb();BX.Sale.OrderAjaxComponent.sendRequest();}
            });
        }
        return false;
    }
    function checkSelectPvz_spb(){
        $.ajax({
            url: '/bitrix/js/up.boxberrydelivery_spb/ajax.php',
            type: 'POST',
            dataType: 'JSON',
            data: {check_pvz:1},
            success: function(not_selected){
                $('#bx-soa-orderSave a').show();
                bxb_errors_spb=[];
                if (not_selected==true){
                    $('#bx-soa-orderSave a').hide();
                    bxb_errors_spb[0]=('<?=GetMessage("PVZ_REQUIRED");?>');
                }
                if (typeof (BX.Sale.OrderAjaxComponent.showBlockErrors) === 'function'){
                    BX.Sale.OrderAjaxComponent.result.ERROR.DELIVERY = bxb_errors_spb;
                    BX.Sale.OrderAjaxComponent.showBlockErrors(BX.Sale.OrderAjaxComponent.deliveryBlockNode);
                }else if (typeof (BX.Sale.OrderAjaxComponent.showError)  === 'function' && bxb_errors_spb.length >0){
                    BX.Sale.OrderAjaxComponent.showError(BX.Sale.OrderAjaxComponent.deliveryBlockNode, bxb_errors_spb[0]);
                }

            }
        });
    }

	function makeWidgetString_spb( params )
	{
		WidgetString = '';
		for(var index in params) {
		  if (selected_cod_profile && index == 'paysum'){
			  params[index] = params['ordersum'];
		  }
		   WidgetString = WidgetString + "'" + params[index]+ "',";
		}
		return WidgetString;
	}

	function getLink_spb()
	{
		$.ajax({
			url: '/bitrix/js/up.boxberrydelivery_spb/ajax.php',
			type: 'POST',
			dataType: 'JSON',
			data: {get_link:'1'},
			success: function(return_data){
				$('#'+bb_custom_link).html("");
				$('#'+bb_custom_link).append('<a href="#" onclick="boxberry_spb.checkLocation(1);boxberry_spb.open('+  makeWidgetString_spb(return_data) +');return false;" ><?=GetMessage("BB_CUSTOM_LINK");?></a>');
			}
		});
	}
    function checkPVZ_spb(code)
	{

		if (boxberry_delivery_profiles_spb.widget.indexOf(code)!=-1){
			selected_cod_profile = true;
			if (bb_custom_link) getLink_spb();
			checkSelectPvz_spb();
		} else if (boxberry_delivery_profiles_spb.widget_cod.indexOf(code)!=-1){
			selected_cod_profile = false;
			if (bb_custom_link) getLink_spb();
			checkSelectPvz_spb();
		} else {
			if ($('#'+bb_custom_link).html() && bb_custom_link) $('#'+bb_custom_link).html("");

            $('#bx-soa-orderSave a').show();
        }
    }

    function afterFormReload_spb(e)
	{

		if (e!=undefined){
            if (e.order!=undefined && boxberry_delivery_profiles_spb.module_options != false){
                for (var key in e.order.PERSON_TYPE) {

                    if (e.order.PERSON_TYPE[key].CHECKED != undefined){
                        if (key < 1){
                            key = <?=$bxbOptions_spb['bb_paid_person_ph'];?>;
                        }else{
							key = e.order.PERSON_TYPE[key].ID;
						}
                        boxberry_delivery_profiles_spb.type_person = key;
                        boxberry_delivery_profiles_spb.widget_element = 'soa-property-'+boxberry_delivery_profiles_spb.module_addr_options[key].ID;
                    }
                }
                e.order.DELIVERY.forEach(function(item, i, arr) {
                    if (item.CHECKED != undefined){
                        checkPVZ_spb(item.ID)
                    }
                });
            }
        }else{
            return false;
        }
    }
	if (window.jQuery || window.$){
		$(document).ready(function() {
			$('#bx-soa-region').on ('focusout', '#zipProperty', function(){
				BX.Sale.OrderAjaxComponent.sendRequest();
			});
			bx_soa_delivery_spb = document.querySelector('#bx-soa-delivery');
			<?=(!empty($bxbOptions_spb['bb_custom_link']) ? 'bb_custom_link="'.$bxbOptions_spb['bb_custom_link'].'";' : '');?>
			$.ajax({
				url: '/bitrix/js/up.boxberrydelivery_spb/ajax.php',
				type: 'POST',
				dataType: 'JSON',
				data: {remove_pvz:1}
			});

			if (bx_soa_delivery_spb){
				BX.addCustomEvent('onAjaxSuccess', afterFormReload_spb);
				BX.Sale.OrderAjaxComponent.sendRequest();
			}else{
				$("input[name='DELIVERY_ID']" ).each(function(i,el) {
					if ($(el).prop('checked')){
						checkPVZ_spb($(el).val());
					}
				})
			}
		});
	}

</script>
<?}?>
