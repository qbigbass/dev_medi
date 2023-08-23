/*
 * @copyright Copyright &copy; Компания MEAsoft, 2014
 */

var statusContainer = 0;
var measoft_s_h = 0;


function measoftCourier() {
    var orderId = parseInt(getURLVar('ID'));
    var picker = 0;

    // dropdown календарь на странице оформления заказа клиентом
    $(document).on('focus', '#ms_date_putn', function() {
        // уничтодаем обхект, если создан, обязательно для случаев с несколькими профилями Measoft
        if (picker != 0)
        {
            picker.destroy()
        }

        var profileId = document.getElementById('ms_date_putn').getAttribute("mid");

        if ( !measoftProfileConfig[profileId]["DISABLE_CALENDAR"] )
        {
            picker = new Lightpick({
                field: document.getElementById('ms_date_putn'),
                format: "DD.MM.YYYY"
            });
        }
    });

    // отображение статуса доставки на странице заказа

    if (statusContainer.length) {
        if (checkStatus) {
            if (checkStatus) {
                let message, data;
                let request = BX.ajax.runAction('measoft:courier.api.ajax.checkStatus', {
                    data: {
                        orderId: orderId
                    }
                });
                request.then(
                    function (response) {
                        data = response.data;
                        if (data.message && data.success === true) {
                            message = data.message;
                        } else {
                            message = MEASOFT_ERROR_ORDER_STATUS;
                        }
                        message = data.message;

                        if (data.cancelError) {
                            if (showMeasoftError) {
                                if (data.IsCanceled) {
                                    BX.UI.Notification.Center.notify({
                                        content: MEASOFT_ERROR_CALCEL_MESS + ": " + "\n" + data.cancelError + "\n" + MEASOFT_ERROR_CALCEL_MESS_AFTER
                                    });
                                } else {
                                    BX.UI.Notification.Center.notify({
                                        content: data.cancelError
                                    });
                                }
                            }

                            showMeasoftError = false;
                        }

                        $("#meafott-order-status-h").html(MEASOFT_DELIVERY_STATUS + ' <span id="meafott-order-status">'+message+'</span>');
                    },
                    function (response) {
                        message = MEASOFT_ERROR_ORDER_STATUS
                        $("#meafott-order-status-h").html(MEASOFT_DELIVERY_STATUS + ' <span id="meafott-order-status">'+message+'</span>');
                    }
                );
            }
        }
    }

}

var showMeasoftError = true;
var checkStatus = true;

function toggleForm(){
    var msForm = $('#ms_courier');
    var msButton = $('#ID_DELIVERY_courier_simple');
    if (msButton) {
        if(msButton.is(':checked')) {
            msForm.show();
            return false;
        }
        else msForm.hide();
    }
    return false;
}

// возвращает значение переменной строки запроса
function getURLVar(param) {
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for (var i = 0; i < hashes.length; i++) {
        var hash = hashes[i].split('=');
        if (param == hash[0]) {
            return hash[1];
        }
    }
    return undefined;
}

// динамическая подгрузка jQuery и jQuery UI
if (!window.jQuery) { console.log("measoft loading jquery");
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = 'https://code.jquery.com/jquery-latest.min.js';
    document.getElementsByTagName('head')[0].appendChild(script);
    script.addEventListener('load', function(){
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js';
        document.getElementsByTagName('head')[0].appendChild(script);
        $(document).ready(function(){
            statusContainer = $('#sale-order-edit-block-order-info .adm-bus-orderinfoblock-content .adm-bus-orderinfoblock-content-block-last');
            measoft_s_h = statusContainer.append('<span id="meafott-order-status-h"></span>');
            measoftCourier();
        });
    }, false);
} else
{
    $(document).ready(function(){
        statusContainer = $('#sale-order-edit-block-order-info .adm-bus-orderinfoblock-content .adm-bus-orderinfoblock-content-block-last');
        measoft_s_h = statusContainer.append('<span id="meafott-order-status-h"></span>');
        measoftCourier();
    });
}