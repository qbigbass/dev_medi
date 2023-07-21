$(function () {
    $('.datepicker').pickadate({
        format: 'dd.mm.yyyy'
    });
    $('.timepicker').pickatime({
        format: 'HH:i',
        min: [9, 0],
        max: [21, 0]
    });
    autosize($("textarea"));
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