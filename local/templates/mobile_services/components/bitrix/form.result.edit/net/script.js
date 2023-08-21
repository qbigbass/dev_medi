$(function () {
    $('.datepicker').pickadate({
        format: 'dd.mm.yyyy'
    });
    $('.timepicker').pickatime({
        format: 'HH:i',
        min: [9, 0],
        max: [21, 0]
    });

    autosize($('textarea'));


    	$(".shipment_str").on("change", function(){
    		$shipment = '';
    		for (var $sh = 0; $sh<12; $sh++)
    		{
    			$shipment += $("#shimpent_articul_"+$sh).val() + '|' + $("#shimpent_size_"+$sh).val() + '|' +$("#shimpent_color_"+$sh).val() + '|' +$("#shimpent_quantity_"+$sh).val() + '|' + $("#shimpent_netprice_"+$sh).val() + '|' + $("#shimpent_price_"+$sh).val() + "\r\n";
    		}

    		$("textarea[name='form_textarea_105']").html($shipment);
    	});
    	$(".getting_str").on("change", function(){
    		$getting = '';
    		for (var $sh = 0; $sh<12; $sh++)
    		{
    			$getting += $("#getting_articul_"+$sh).val() + '|' + $("#getting_size_"+$sh).val() + '|' +$("#getting_color_"+$sh).val() + '|' +$("#getting_quantity_"+$sh).val() + '|' + $("#getting_netprice_"+$sh).val() + '|' + $("#getting_price_"+$sh).val() + "\r\n";
    		}

    		$("textarea[name='form_textarea_106']").html($getting);
    	});


//    $('.order-form').on('click', '.bind__google-speech', function () {
//
//        var resultField = $('#' + $(this).attr('data-speech-element-id'));
//        console.log(resultField.html());
//
//        if (window.hasOwnProperty('webkitSpeechRecognition')) {
//            var recognition = new webkitSpeechRecognition();
//            recognition.continuous = false;
//            recognition.interimResults = false;
//            recognition.lang = "ru";
//            recognition.start();
//
//            recognition.onresult = function (e) {
//                resultField.val(e.results[0][0].transcript);
//                recognition.stop();
//            };
//
//            recognition.onerror = function (e) {
//                recognition.stop();
//            }
//
//        }
//    });

    // Дополнительные поля
    $('body').on('change', 'select[data-action=enableElementOnSelect]', function () {
        var dataTarget = $(this).attr('data-target');
        if (dataTarget !== undefined) {
            var isEnable = $('option:selected', this).attr('data-enable');
            if (isEnable !== undefined) {
                if (isEnable == 'true') {
                    $(dataTarget).prop('disabled', false);
                } else if (isEnable == 'false') {
                    $(dataTarget).prop('disabled', true);
                    $(dataTarget).val('');
                }
            }
        }
    });
});
