/*
 * @copyright Copyright &copy; Компания MEAsoft, 2014
 */

var picker = 0;

if (window.jQuery) {

    $(document).ready(function(){


        // dropdown календарь на странице оформления заказа клиентом
        $(document).on('focus', '#ms_date_putn', function() {
            // уничтодаем обхект, если создан, обязательно для случаев с несколькими профилями Measoft
            if (picker != 0)
            {
                picker.destroy()
            }

            var msDatePutn = document.getElementById('ms_date_putn');
            let profileId = msDatePutn.getAttribute("mid");
            let minDate = msDatePutn.dataset.date;

            if ( !measoftProfileConfig[profileId]["DISABLE_CALENDAR"] )
            {
                picker = new Lightpick({
                    field: document.getElementById('ms_date_putn'),
                    format: "DD.MM.YYYY",
                    minDate: minDate,
                });
            }

        });
    });
}

BX.ready(function(){
    var test;
    BX.addCustomEvent("onPullEvent", function(module_id,command,params) {
        let sessionId = BX.bitrix_sessid();
        if (module_id === "measoft.courier" && command === 'error' && sessionId === params.sessionid && sessionId !== undefined)
        {
            BX.setCookie('measoftnotif', params.message, {expires: 30});
            if(params.isajax === true) {
                BX.UI.Notification.Center.notify({
                    content: params.message,
                });
                BX.setCookie('measoftnotif', null, { expires: -1 });
            }

        }
    });
    message =  BX.getCookie('measoftnotif');
    if(message !== undefined) {
        BX.UI.Notification.Center.notify({
            content: message
        });
        BX.setCookie('measoftnotif', null, { expires: -1 });
    }
});