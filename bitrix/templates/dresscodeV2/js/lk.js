$(function(){

    var lk_edit_info = 0;
    var lk_edit_contacts = 0;
    var lk_info_edited =  0;
    function edit_contacts_lk(event){
        if (lk_edit_info == 0 ){
            lk_edit_info = 1;
            $(".lk_contacts").removeAttr("disabled");
            $(".ligal .edit").show();
            $("input[name='"+event.target.name+"']").focus();
            $("#lk_edit_contacts").hide();
        }
        else if (lk_edit_info == 0  && event.data.variant == 1) {
            lk_edit_info = 0;
            $(".lk_contacts").attr("disabled", "disabled");
            $(".ligal .edit").hide();

            $("#lk_edit_contacts").show();
        }
        $(".lk_contacts").on("change", function(){
            lk_info_edited = 1;
        });
    }

    $("#lk_edit_contacts_form").on("click", {"variant":"0"}, edit_contacts_lk);
    $("#lk_edit_contacts").on("click", edit_contacts_lk, {"variant":"1"}, edit_contacts_lk);



    $(".question").on("click", showHelp);
    $attention = 0;
    function showHelp(){
        $qid = $(this).data('id');
        $(".answer").hide();
        if ($qid != ''){
            if ($attention == 0)
            {
                $(".answer."+$qid).css("display", "inline");
                $attention = 1;
            }
            else {
                $(".answer."+$qid).hide();
                $attention = 0;
            }
        }
    }


    $("#lk_edit_contact_cancel").on("click", function(){
        if (lk_edit_info == 1 && lk_info_edited == 1){

            document.location.reload();
        }
        else if  (lk_edit_info == 1 && lk_info_edited == 0) {
            $(".lk_contacts").attr("disabled", "disabled");
            $(".ligal .edit").hide();
            lk_edit_info = 0;
            $("#lk_edit_contacts").show();
        }
    });
    $("#lk_edit_contact_cancel_m").on("click", function(){
        if (lk_edit_info == 1 && lk_info_edited == 1){

            document.location.reload();
        }
        else if  (lk_edit_info == 1 && lk_info_edited == 0) {
            $(".lk_contacts").attr("disabled", "disabled");
            $(".ligal .edit").hide();
            lk_edit_info = 0;
            $("#lk_edit_contacts").show();
        }
    });

    $("#sex label ").on("click", function(){
        if (lk_edit_info == 1){
            $("#sex label ").removeClass("checked");
            $("input[name='gender']").removeAttr("checked");
            $(this).siblings("input[type=radio]").attr("checked", "checked");

            $(this).addClass("checked");
        }
    });

    $("#lk_edit_contact_save").on("click", function(){
        $form = $("#lk_edit_contacts_form").serialize();

		showLoader();

        $.ajax({
            url: '/ajax/lmx/client/',
            data: $form,
            method: 'POST',
            dataType: 'json',
            success:  function(data) {
                if (data.status == 'ok'){

                    lk_edit_contacts = 0;

                    document.location.reload();

                }

                else if (data.status == 'error')
                {
                    console.log(data);

                }

            },
            complete: function(data) {
                hideLoader();
                lk_edit_contacts = 0;
                lk_edit_info = 0;
                $("#lk_edit_contacts").show();
                $(".lk_contacts").attr("disabled", "disabled");
                $(".ligal .edit").hide();
                                    document.location.reload();
            }
        });
    });

    $("#change_contacts").on("click", function(){

        lk_edit_contacts = 1;
        lk_edit_info = 0;
        $(".lk_contacts").attr("disabled", "disabled");
        $(".ligal .edit").hide();
        $("#user-data").hide();
        $("#edit-contact").show();

        $("#change_phone").on("blur", function(){
            $phone = $("#change_phone");
            if ($phone.val().replace(/\D/g, '').length != 11)
    		{
    			$error = 1;
    			$phone.addClass("error");
    			hideLoader();
    			$("#change_phone_start").off('click').attr("disabled", "disabled");
    		}
            else{

                $phone.removeClass("error");
                $("#change_phone_start").on("click", change_phone_start).removeAttr("disabled");
            }
        });

        $("#change_email").on("blur", function(){
            $email = $("#change_email");
            if ($email.val().replace(/\D/g, '').length > 6)
    		{
    			$error = 1;
    			$email.addClass("error");
    			hideLoader();
    			$("#change_email_start").off('click').attr("disabled", "disabled");
    		}
            else{

                $email.removeClass("error");
                $("#change_email_start").on("click", change_email_start).removeAttr("disabled");
            }
        });

    });

    $("#change_contact_back").on("click", function(){
        lk_edit_contacts = 0;
        lk_edit_info = 0;
        $(".lk_contacts").attr("disabled", "disabled");
        $(".ligal .edit").hide();
        $("#change_email_start").off('click').attr("disabled", "disabled");
        $("#change_phone_start").off('click').attr("disabled", "disabled");
        $("#user-data").show();
        $("#edit-contact").hide();
    });


    function change_phone_start() {
        showLoader();

		var $phone = $("#change_phone");

		$phone_num = $phone.val().replace(/\D/g, '');


		$error = 0;
		if ($phone_num.length != 11)
		{
			$error = 1;
			$phone.addClass("error");
			hideLoader();
            $("#change_phone_start").off('click').attr("disabled", "disabled");
		}

		else {
			$error = 0;
			$phone.removeClass("error");

		}
		if ($error == 0)
		{

            $.ajax({
                url: '/ajax/lmx/client/',
                data: 'action=change_phone_start&phone='+$phone_num,

                method: 'POST',
                dataType: 'json',
                success:  function(data) {

                    if (data.data.result.state == 'Success')
                    {

                        $("#confirm_phone").show();
                        $("#change_phone").attr("disabled", "disabled");
                        $("#check_phone_code").on("keyup change", change_code_check);

                        $("#change_phone_start").val("Изменить номер").off("click").on("click", function(){
                            $("#confirm_phone").hide();
                            $("#change_phone").removeAttr("disabled").val("");
                            $("#change_phone_start").off("click").attr("disabled", "disabled").val("Отправить код");
                        });

                    }

                    else if (data.status == 'fail')
                    {

                            console.log('error');
                    }

                        console.log(data.data);
                },
                complete: function(data) {
                    hideLoader();
                    lk_edit_contacts = 0;
                    lk_edit_info = 0;

                }
            });
        }
    };

    function change_code_check(){
        $code = $("#check_phone_code");

        if ($code.val().length != 6)
		{
			$error = 1;
			$code.addClass("error");
            $("#change_code_check").off('click').attr("disabled", "disabled");
		}
        else{
            $code.removeClass("error");
            $("#change_code_check").removeAttr("disabled").on('click', check_phone_code);
        }

        hideLoader();
    }

    function check_phone_code(){
        showLoader();
        $code = $("#check_phone_code");
        $error = 0;


        $.ajax({
            url: '/ajax/lmx/client/',
            data: 'action=change_phone_confirm&code='+$code.val(),

            method: 'POST',
            dataType: 'json',
            success:  function(data) {
                console.log(data);
                if (data.data.result.state == 'Success')
                {

                    $("#status_message_edit").removeClass("fail success").addClass("success").html("Телефон успешно обновлен.  Сейчас вы будете перенаправлены в <a href='/lk/'>личный кабинет</a>.").show();

                    setTimeout(function () {

                        window.location.href = '/lk/';
                    },  2000);

                }

                else if (data.data.result.State  == 'Error')
                {

                        console.log('error');
                                                    console.log(data);
                }

                    console.log(data.data);
            },
            complete: function(data) {
                hideLoader();
                lk_edit_contacts = 0;
                lk_edit_info = 0;

            }
        });



    }

    function change_email_start() {
        showLoader();
        console.log('change_email_start');
		var $email = $("#change_email");

		$email_num = $email.val();


		$error = 0;


            $.ajax({
                url: '/ajax/lmx/client/',
                data: 'action=change_email_start&email='+$email_num,

                method: 'POST',
                dataType: 'json',
                success:  function(data) {

                    if (data.data.result.state == 'Success')
                    {

                        $("#confirm_email").show();
                        $("#change_email").attr("disabled", "disabled");
                        $("#change_email_code").on("keyup change", change_emailcode_check);

                        $("#change_email_start").val("Изменить email").off("click").on("click", function(){
                            $("#confirm_email").hide();
                            $("#change_email").removeAttr("disabled").val("");
                            $("#change_email_start").off("click").attr("disabled", "disabled").val("Отправить код");
                        });

                    }

                    else if (data.status == 'fail')
                    {

                            console.log('error');
                    }

                        console.log(data.data);
                },
                complete: function(data) {
                    hideLoader();
                    lk_edit_contacts = 0;
                    lk_edit_info = 0;

                }
            });

    };

    function change_emailcode_check(){
        $code = $("#change_email_code");

        if ($code.val().length != 6)
		{
			$error = 1;
			$code.addClass("error");
            $("#change_emailcode_check").off('click').attr("disabled", "disabled");
		}
        else{
            $code.removeClass("error");
            $("#change_emailcode_check").removeAttr("disabled").on('click', check_email_code);
        }

        hideLoader();
    }

    function check_email_code(){
        showLoader();
        $code = $("#change_email_code");
        $error = 0;


        $.ajax({
            url: '/ajax/lmx/client/',
            data: 'action=change_email_confirm&code='+$code.val(),

            method: 'POST',
            dataType: 'json',
            success:  function(data) {
                console.log("check_email_code");
                console.log(data);
                if (data.data.result.state == 'Success')
                {

                    $("#status_message_edit").removeClass("fail success").addClass("success").html("Email успешно обновлен.  Сейчас вы будете перенаправлены в <a href='/lk/'>личный кабинет</a>.").show();

                    setTimeout(function () {

                        window.location.href = '/lk/';
                    },  2000);

                }
                else if (data.status  == 'error')
                {
                    $code.addClass("error");
                    $("#change_emailcode_check").off('click').attr("disabled", "disabled");
                        console.log('error');
                                                    console.log(data);
                }

                else if (data.data.result.State  == 'Error')
                {

                        console.log('error');
                                                    console.log(data);
                }

                    console.log(data.data);
                        hideLoader();
            },
            complete: function(data) {
                hideLoader();
                lk_edit_contacts = 0;
                lk_edit_info = 0;

            }
        });

            hideLoader();
    }

});
