$(function () {
    $('.datepicker').pickadate({
        format: 'dd.mm.yyyy'
    });
    $('.timepicker').pickatime({
        format: 'HH:i',
        min: [9, 0],
        max: [21, 0]
    });

    $('.order-list').on('click', '.order-list__item', function () {
        var link = $(this).attr('data-detail-url');
        if(link !== undefined && link !== '') {
            document.location.href = link;
        }
    });

    $("#order-form__type").on("change", function(){
        if($(this).val() == 102){  // Доставка
            $("#filter_delivery_date").show();
            $("#filter_visit_date").hide();
            $("#filter_doctor_name").hide();
            $("#order-form__visit-date").val("");
            $("input [name='find_MEDI_ORTO_visit_date_USER_text_submit']").val("");
        }
        else if ($(this).val() == 103) // ЛПУ
        {
            $("#filter_visit_date").show();
            $("#filter_doctor_name").show();
            $("#filter_delivery_date").hide();
            $("#order-form__delivery-date").val("");
            $("input [name='find_MEDI_ORTO_delivery_date_USER_text_submit']").val("");
        }

        else if ($(this).val() == 104) // CG
        {
            $("#filter_visit_date").hide();
            $("#filter_doctor_name").show();
            $("#filter_delivery_date").show();
            $("#order-form__visit-date").val("");
            $("input [name='find_MEDI_ORTO_visit_date_USER_text_submit']").val("");
        }
        else if ($(this).val() == '') // ЛПУ
        {
            $("#filter_visit_date").hide();
            $("#filter_delivery_date").hide();
            $("#order-form__delivery-date").val("");
            $("#order-form__visit-date").val("");
            $("input [name='find_MEDI_ORTO_visit_date_USER_text_submit']").val("");
            $("input [name='find_MEDI_ORTO_delivery_date_USER_text_submit']").val("");
        }
    });

    if ($("#order-form__type").val() == 102) // Доставка
    {
        $("#filter_delivery_date").show();
        $("#filter_visit_date").hide();
        $("#order-form__visit-date").val("");
        $("input [name='find_MEDI_ORTO_visit_date_USER_text_submit']").val("");
    }
    else if ($("#order-form__type").val() == 103) // ЛПУ
    {
        $("#filter_visit_date").show();
        $("#filter_delivery_date").hide();
        $("#order-form__delivery-date").val("");
        $("input [name='find_MEDI_ORTO_delivery_date_USER_text_submit']").val("");
    }

    else if ($("#order-form__type").val() == 104) // СП
    {
        $("#filter_visit_date").hide();
        $("#filter_delivery_date").show();
        $("#order-form__visit-date").val("");
        $("input [name='find_MEDI_ORTO_delivery_date_USER_text_submit']").val("");
    }
});
