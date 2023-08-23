if (!window.jQuery) {
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
            let passwordInput = $("input[name=CONFIG\\[general\\]\\[PASSWORD\\]]");
            let loginInput = $("input[name=CONFIG\\[general\\]\\[LOGIN\\]]");
            let codeInput = $("input[name=CONFIG\\[general\\]\\[CODE\\]]");

            let id = $("input[name=ID]").val();
            codeInput.parent().append("<br>").append('<input type="button" id="checkAuth" value="'+MEASOFT_DELIVERY_CHECK_AUTH+'"> <span id="checkAuthText" style="margin-left:30px;"></span>');
            if(!!$("#checkAuth" )){
                $("#checkAuth").click(function(){
                    let loginVal = loginInput.val();
                    let passwordVal =  passwordInput.val();
                    let codeVal = codeInput.val();
                    BX.showWait();

                    let request = BX.ajax.runAction('measoft:courier.api.ajax.checkAuth', {
                        data: {
                            login: loginVal,
                            password: passwordVal,
                            code: codeVal
                        }
                    });
                    request.then(function (response) {
                        let data = response.data;
                        let checkAuthText = $("#checkAuthText");
                        let mapClientCodeInput = $("input[name=CONFIG\\[general\\]\\[MAP_CLIENT_CODE\\]]");
                        if(data.success == "Y"){
                            checkAuthText.html('<span style="color:green">'+MEASOFT_DELIVERY_CHECK_AUTH_TRUE+'</span>');
                            if(data.MAP_CLIENT_CODE !== undefined && data.MAP_CLIENT_CODE !== null){
                                mapClientCodeInput.parent().find('span').html(json.data.MAP_CLIENT_CODE);
                                mapClientCodeInput.val(json.data.MAP_CLIENT_CODE);
                            }else{
                                mapClientCodeInput.parent().find('span').html("");
                                mapClientCodeInput.val("");
                            }
                        }else{
                            checkAuthText.html('<span style="color:red">'+MEASOFT_DELIVERY_CHECK_AUTH_FALSE+'</span>');
                            mapClientCodeInput.parent().find('span').html("");
                            mapClientCodeInput.val("");
                        }
                        BX.closeWait();

                    });

                });
            }
        });
    }, false);
}
