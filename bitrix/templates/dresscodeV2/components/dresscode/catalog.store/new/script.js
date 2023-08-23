function changeSalonsCity(){
    $val = $(this).data('link');
    if ($val == "-1")
        return;
    else if ($val == "")
        window.location = "/salons/";
    else
        window.location = "/"+$val+"/salons/";

    return false;
}
function showFilterSalons()
{
    if ($(".salons-menu-filtr-but").hasClass("active")){

        $(".salons-menu-filtr-but").removeClass("active");
        $(".salons-menu-filtr").hide();
    }
    else {

        $(".salons-menu-filtr-but").addClass("active");
        $(".salons-menu-filtr").show();
    }
}

function showSalonsCities(){
    $("#salons_city_popup").show();
    return false;
}
function closeSalonsCities(){
    $("#salons_city_popup").hide();
    return false;

}

function clearSalonsFilter () {
    var $active_tab = $(".salons-tabs-content .salons-tab-content.active").attr("data-id");
    var $active_subtab = $(".tabs-links .tab-link.active").attr("data-id");

    var $form = $("#salonFilterForm");
    var $data = {action:'show', cur_city: $("#cur_city").val()};

    if ($active_tab == undefined)
        $active_tab = 'map';
    if ($active_subtab == undefined)
        $active_subtab = 'service';

    $.ajax({
        type: 'POST',
        url: '/ajax/salon/',
        data: $data,
        beforeSend: function() {
            showLoader();
        },
        success: function(data) {

            $("#filterSalonsResult").html(data);
            setTimeout(hideLoader, 500);

            $(".salons-menu-tabs .salons-menu-tab").removeClass("active");
            $(".salons-menu-tabs .tab-"+$active_tab).addClass("active");
            $(".salons-tabs-content .salons-tab-content").removeClass("active");
            $(".salons-tabs-content .tab-"+$active_tab).addClass("active");


             $(".tabs-links .tab-link").removeClass("active");
             $(".tabs-links .tab-"+$active_subtab).addClass("active");
             $(".tabs-content .tab-content").removeClass("active");
             $(".tabs-content .tab-"+$active_subtab).addClass("active");
               // open tabs
           $(".salons-menu-tab").click(function() {
               if ( !$(this).hasClass("active") ) {
       			var par = $(this).parents(".salons-tabs-wrap");
       			var index = $(this).index();

       			$(this).addClass('active').siblings().removeClass("active");
       			$('.salons-tab-content:eq(' + index + ')', par).addClass("active").siblings().removeClass("active");
       		}
           });

          $(".tab-link").click(function() {
              if ( !$(this).hasClass("active") ) {
      			var par = $(this).parents(".tabs-wrap");
      			var index = $(this).index();

      			$(this).addClass('active').siblings().removeClass("active");
      			$('.tab-content:eq(' + index + ')', par).addClass("active").siblings().removeClass("active");
      		}
          });


            $(".salons-menu-filtr-but").on("click", showFilterSalons);
            $("#salon_city_select").on("change", changeSalonsCity);
            $(".salon_filter_clear").on("click", clearSalonsFilter);
            $(".salon_filter_submit").on("click", submitSalonsFilter);
            $(".city_link").on("click", showSalonsCities);
            $(".salons_city_popup__close").on("click", closeSalonsCities);
            $(".salons_select_city").on("click", changeSalonsCity);
        },
        failure: function(){

            hideLoader();
        }
    });


}


function submitSalonsFilter(){
    var $form = $("#salonFilterForm");
    var $data = $form.serialize();
    var $active_tab = $(".salons-tabs-content .salons-tab-content.active").attr("data-id");
    var $active_subtab = $(".tabs-links .tab-link.active").attr("data-id");

    if ($active_tab == undefined)
        $active_tab = 'map';
    if ($active_subtab == undefined)
        $active_subtab = 'service';

    $.ajax({
        type: 'POST',
        url: '/ajax/salon/',
        data: $data,
        beforeSend: function() {
            showLoader();
        },
        success: function(data) {

           $("#filterSalonsResult").html(data);
           setTimeout(hideLoader, 500);

           $(".salons-menu-tabs .salons-menu-tab").removeClass("active");
           $(".salons-menu-tabs .tab-"+$active_tab).addClass("active");
           $(".salons-tabs-content .salons-tab-content").removeClass("active");
           $(".salons-tabs-content .tab-"+$active_tab).addClass("active");

          $(".tabs-links .tab-link").removeClass("active");
          $(".tabs-links .tab-"+$active_subtab).addClass("active");
          $(".tabs-content .tab-content").removeClass("active");
          $(".tabs-content .tab-"+$active_subtab).addClass("active");
               // open tabs
           $(".salons-menu-tab").click(function() {
               if ( !$(this).hasClass("active") ) {
       			var par = $(this).parents(".salons-tabs-wrap");
       			var index = $(this).index();

       			$(this).addClass('active').siblings().removeClass("active");
       			$('.salons-tab-content:eq(' + index + ')', par).addClass("active").siblings().removeClass("active");
       		}
           });

           $(".tab-link").click(function() {
               if ( !$(this).hasClass("active") ) {
       			var par = $(this).parents(".tabs-wrap");
       			var index = $(this).index();

       			$(this).addClass('active').siblings().removeClass("active");
       			$('.tab-content:eq(' + index + ')', par).addClass("active").siblings().removeClass("active");
       		}
           });



            $(".salons-menu-filtr-but").on("click", showFilterSalons);
            $("#salon_city_select").on("change", changeSalonsCity);
            $(".salon_filter_clear").on("click", clearSalonsFilter);
            $(".salon_filter_submit").on("click", submitSalonsFilter);
            $(".city_link").on("click", showSalonsCities);
            $(".salons_city_popup__close").on("click", closeSalonsCities);
            $(".salons_select_city").on("click", changeSalonsCity);
        },
        failure: function(){

            hideLoader();
        }
    });

}

$(document).ready(function() {


    $("#salon_city_select").on("change", changeSalonsCity);
    $(".salons-menu-filtr-but").on("click", showFilterSalons);
    $(".city_link").on("click", showSalonsCities);
    $(".salons_city_popup__close").on("click", closeSalonsCities);
    $(".salons_select_city").on("click", changeSalonsCity);

    $(".salon_filter_clear").on("click", clearSalonsFilter);
    $(".salon_filter_submit").on("click", submitSalonsFilter);
});
