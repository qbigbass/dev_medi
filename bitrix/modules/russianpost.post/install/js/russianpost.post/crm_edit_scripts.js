$(document).ready(function ()
{
	BX.addCustomEvent('onAjaxSuccess', setReadonly);
	var target = $('[data-cid="DELIVERY_ID"]')[0];
	console.log(target);
	if(typeof target != "undefined")
	{
		var observer = new MutationObserver(function(mutations) {
			mutations.forEach(function(mutation) {
				console.log(mutation.type);
				if(mutation.type == 'childList')
				{
					setReadonly();
				}
			});
		});
		var config = { attributes: true, childList: true, characterData: true };
		observer.observe(target,  config);
	}
	//BX.addCustomEvent('onAjaxSuccess', consoleMessage);
	setReadonly();
});

function setReadonly()
{
	console.log('WORK EDIT CRM');
	var order_id = $('input[name="ORDER_ID"]').val();
	var inpDeliv = $('input[name="DELIVERY_ID"]');
	if(typeof inpDeliv !== 'undefined')
	{
		var delivery_id = $(inpDeliv).val();
		if(typeof delivery_id !== 'undefined' && delivery_id > 0)
		{
			$.ajax({
				type: "POST",
				url: "/bitrix/js/russianpost.post/crm_edit_ajax.php",
				//dataType: "json",
				data: {
					"order_id": order_id,
					"delivery_id": delivery_id,
				},
				//data: "DO=MAKE_ORDER&sessid=<?=bitrix_sessid();?>&sessions="+strSelectSessions+"&dops="+strSelectDops+"&pay_system="+paySystemId,
				success: function(msg){
					if (msg != null && msg != undefined){
						if(msg.status == 'success')
						{
							var link_set = $('#link_set').val();
							console.log(link_set);
							if(link_set == undefined)
							{
								$('[data-cid="DELIVERY_ID"]').append(msg.message);
							}
							else if(msg.message == '')
							{
								$('#russianpost_info_block').remove();
								//$('#section_map_'+index_shipment).html(msg.message);
							}
						}
					}
				}
			});
		}
	}
}

var contentRussianpost = '<div id="ecom-widget" style="height: 100%"></div>';

var popupRussianpost = '';
function openMap(guidId, price, weight, zip, location)
{
	let width = 600;
	let heigth = 600;

	if (document.body.clientWidth < 600)
	{
		width = document.body.clientWidth;
	}
	if (document.body.clientHeight < 600)
	{
		width = document.body.clientHeight - 30;
	}

	popupRussianpost = BX.PopupWindowManager.create("popup-message", BX('element'), {
		content: contentRussianpost,

		width: width, // ������ ����
		height: heigth, // ������ ����
		zIndex: 1300, // z-index
		closeIcon: {
			// ������ �� ������� ��� ������ ��������, ��� null - ������ �� �����
			opacity: 1
		},
		titleBar: '����� ������ ������',
		closeByEsc: true, // �������� ���� �� esc
		darkMode: false, // ���� ����� ������� ��� ������
		autoHide: false, // �������� ��� ����� ��� ����
		draggable: true, // ����� ������� ��� ���
		resizable: true, // ����� ���������
		min_height: 100, // ����������� ������ ����
		min_width: 100, // ����������� ������ ����
		lightShadow: true, // ������������ ������� ���� � ����
		angle: true, // �������� ������
		overlay: {
			// ������ �� ������� ����
			// backgroundColor: 'black',
			// opacity: 500
		},
		buttons: [],
		events: {
			onPopupShow: function ()
			{
				// ������� ��� ������ ����
				console.log('POPUP SHOW');
			},
			onPopupClose: function ()
			{
				// ������� ��� �������� ����
			}
		}
	});

	$('#ecom-widget').html('');
	//if(weight == 0) weight = 10;
	var post = {};
	post['guidId'] = guidId;
	post['price'] = price;
	post['weight'] = weight;
	post['location'] = location;


	BX.ajax.post(
		'/bitrix/js/russianpost.post/ajax_crm_map.php',
		post,
		function (data)
		{
			//console.log(data);
		}
	);


	popupRussianpost.show();
};

function callbackCrmFunction(data)
{
	$('#russianpost_result_type').val(data.mailType);
	$('#russianpost_result_price').val(data.cashOfDelivery);
	$('#russianpost_result_zip').val(data.indexTo);



	var fullAddress = '';
	if (data.regionTo != null)
		fullAddress = fullAddress + data.regionTo + ' ';
	if (data.areaTo != null)
		fullAddress = fullAddress + data.areaTo + ' ';
	if (data.cityTo != null)
		fullAddress = fullAddress + data.cityTo + ' ';
	if (data.addressTo != null)
		fullAddress = fullAddress + data.addressTo;
	$('#russianpost_result_address').val(fullAddress);
	var splitAddress = $('#russianpost_split_address').val();
	if(splitAddress == 'true')
	{
		$('#russianpost_street_address').val(data.addressTo);
	}

	popupRussianpost.close();
}