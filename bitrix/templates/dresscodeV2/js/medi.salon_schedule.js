$(document).ready(function() {

    if (!$(".medi-new-select__item:first-child").length){
        $.ajax({
            url: '/ajax/salon/',
            data: 'action=get_schedule_salons',

            dataType: 'json',
            success:  function(data) {
                if (data.status == 'ok'){
                    var first_item = 0;
                    $.each(data.data,function(index,value) {
                        if(index == 0) first_item = value['SALON']['ID'];
                        if (value['SALON']) {
                            $("select.medi-select").append("<option value='" + value['SALON']['ID'] + "' data-link='" + value['SALON']['CODE'] + "' title='" + value['SALON']['ADDRESS'] + "'>м. " + value['SALON']['METRO']['NAME'] + "</option>");
                        }
                    });

                    folder = '/';
                    $("#salon_link").attr("href", "//www.medi-salon.ru/salons"+folder+data.data[0].SALON.CODE+"/");

                    if (first_item > 0)
                    {
                        $store_id = first_item;
                        showLoader();
                        console.log("load");
                        $.ajax({
                            url: '/ajax/salon/',
                            data: 'id='+$store_id+'&action=get_schedule',

                            dataType: 'json',
                            success:  function(data) {
                                if (data.status == 'ok'){
                                    $(".shedule-time").html("");
                                    $.each(data.days,function(index,value) {
                                        $(".shedule-time.sh"+index).html(value);
                                    });
                                    folder = '/';
                                    if (data.data.SITE_ID == 's2')
                                        folder = '/spb/';
                                    $("#salon_link").attr("href", "//www.medi-salon.ru/salons"+folder+data.data.SALON.CODE+"/");

                                }

                                else if (data.status == 'error')
                                {
                                    console.log('error');
                                    console.log(data);
                                }

                            },
                            complete: function(data) {
                                hideLoader();
                            }
                        });
                    }
                }
                else if (data.status == 'error')
                {
                    console.log('error');
                    console.log(data);
                }

            },
            complete: function(data) {
                hideLoader();

                mediSelect();

                $("#selectMediParams .medi-new-select__item").on("click", changeMediSelect);
            }
        });
    }

    var $this = $("#selectMediParams");
    var changeMediSelect = function(){

        if ($(this).data("value") == undefined)
        {
            var $this = $(".medi-new-select__item:first-child");
        }
        else {
            $this = $(this);
        }
        if ($this.data("value") > 0)
        {
            $store_id = $this.data("value");
            showLoader();
             $.ajax({
            url: '/ajax/salon/',
            data: 'id='+$store_id+'&action=get_schedule',

            dataType: 'json',
            success:  function(data) {
                if (data.status == 'ok'){

                    $(".shedule-time").html("");
                    $.each(data.days,function(index,value) {
                        $(".shedule-time.sh"+index).html(value);
                    });
                    folder = '/';
                    if (data.data.SITE_ID == 's2')
                        folder = '/spb/';
                    $("#salon_link").attr("href", "//www.medi-salon.ru/salons"+folder+data.data.SALON.CODE+"/");

                }

                else if (data.status == 'error')
                {
                    console.log('error');
                    console.log(data);
                }
            },
            complete: function(data) {
                hideLoader();
            }
        });
        }
    };
    changeMediSelect();
});
