$(function () {

	$("#web_form_submit_but").on("click", function(){
		//$("#web_form_submit_but").attr("disabled", "disabled");
		return true;

	});
	$("form[name='MEDI_ORTO'] input").on("change", function(){
		$("#web_form_submit_but").removeAttr("disabled");
	});
    $('.datepicker').pickadate({
        format: 'dd.mm.yyyy'
    });
    $('.timepicker').pickatime({
        format: 'HH:i',
        min: [9, 0],
        max: [21, 0]
    });

    autosize($('textarea'));


	$("#order-form__contact-address").suggestions({
        token: "3bf699bebe24b4c22576fce52726c0adec68917b",
        type: "ADDRESS",
		geoLocation: [{"kladr_id": "50"}, {"kladr_id": "77"}]
    });
	$("#order-form__client-sif").suggestions({
        token: "3bf699bebe24b4c22576fce52726c0adec68917b",
        type: "NAME",
    });
	$("#order-form__doctor-sif").suggestions({
        token: "3bf699bebe24b4c22576fce52726c0adec68917b",
        type: "NAME",
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

	$("input[name='form_text_84']").on("change", function(){
		$sum = parseFloat($("input[name='form_text_84']").val());
		$reason_id = $("select[name='form_dropdown_discount_reason']").val();


		if (parseInt($("input[name='form_text_84']").val()) == $("input[name='form_text_84']").val()){
			switch ($reason_id) {
			  case "90":  // нет скидки
					$discount_sum = $sum;
				break;
			  case "91":  // диск. карта ?

				break;
			  case "92": // рецепт 4%
					$discount_sum =  parseFloat($sum - $sum*0.04);

				break;
			  case "93": // 10%
					$discount_sum =  parseFloat($sum - $sum*0.1);
				break;
			  case "94": // 10%
					$discount_sum =  parseFloat($sum - $sum*0.1);
				break;
			  case "95": // 10%
					$discount_sum =  parseFloat($sum - $sum*0.1);
				break;

			  default:
				$discount_sum = $sum;
			}

			$("input[name='form_text_89']").val($discount_sum);
		}
	});

	$("select[name='form_dropdown_discount_reason']").on("change", function(){
		$sum = parseFloat($("input[name='form_text_84']").val());
		$reason_id = $("select[name='form_dropdown_discount_reason']").val();

		if (parseInt($("input[name='form_text_84']").val()) == $("input[name='form_text_84']").val()){
			switch ($reason_id) {
			  case "90":  // нет скидки
					$discount_sum = $sum;
				break;
			  case "91":  // диск. карта ?

				break;
			  case "92": // рецепт 4%
					$discount_sum =  parseFloat($sum - $sum*0.04);

				break;
			  case "93": // 10%
					$discount_sum =  parseFloat($sum - $sum*0.1);
				break;
			  case "94": // 10%
					$discount_sum =  parseFloat($sum - $sum*0.1);
				break;
			  case "95": // 10%
					$discount_sum =  parseFloat($sum - $sum*0.1);
				break;

			  default:
				$discount_sum = $sum;
			}

			$("input[name='form_text_89']").val($discount_sum);
		}
	});

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
