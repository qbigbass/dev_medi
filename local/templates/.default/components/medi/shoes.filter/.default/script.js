$(document).ready(function(){

    $(".shoesWrap .btn-simple").on("click", function(){
        if ($(this).hasClass("checked")){
            $(this).removeClass("checked");
        }
        else {
            $(this).addClass("checked");
        }
    });



	$("#length").on("change keyup", function(e){
        if (e.keyCode != 188 && e.keyCode != 190) {

            $("#length").removeClass('error');
            $p = $("#length").val().replace(/\,/g, ".");
            //$("#length").val($p);
            if ($p < 10 || $p > 32) {
                $("#length").addClass('error');
                $("#results_count").html("Укажите параметры поиска");
                $("#show_but").addClass("disabled");
                $(".shoes_results").html("");

            } else {

                calcShoesResult();
            }
        }
	});

	$(".person .for-who").on("click", function(){
        if ($(this).hasClass("checked")) {

            calcShoesResult();
        }
	});

    $("#fullness").on("change keyup", function(){
        $("#fullness").removeClass('error');
        $p = $("#fullness").val().replace(/\,/g, ".");
        $("#fullness").val($p);
        if ($("#length").val() <10 || $("#length").val()>32){
            $("#length").addClass('error');
            $("#results_count").html("Укажите параметры поиска");
            $("#show_but").addClass("disabled");
            $(".shoes_results").html("");

        }
        else {

            calcShoesResult();
        }
    });

    $(".person .for-who").on("click", calcShoesResult);
    $(".seasons .season").on("click", calcShoesResult);
    $(".answer input[type='checkbox']").on("change click", calcShoesResult);


    $("#show_but").on("click", showShoesResult);

    function calcShoesResult(){

        $("#length").removeClass('error');
		$p = $("#length").val().replace(/\,/g, ".");
		$("#length").val($p);


        $("#fullness").removeClass('error');
        $p = $("#fullness").val().replace(/\,/g, ".");
        $("#fullness").val($p);

        if ($("#length").val() <10 || $("#length").val()>35){
            $("#length").addClass('error');
        }
        if (!$(".person .for-who").hasClass("checked")){
            $(".person .for-who").addClass('error');
        }


        var $makeFilter = compileShoesFilter();
        var $filter = $makeFilter[0];
        var $can_search = $makeFilter[1];


            if ($can_search == "1")
            {
                $("#show_but").removeClass("disabled");
                $(".shoes_results").html("");
                $.ajax({
                    url: '/ajax/shoes/?action=search',
                    type: 'POST',
                    dataType: 'json',
                    data: $filter,
                    before: function()
                    {
                        showLoader();
                    },
                    success:  function(data) {
                        if (data.status == 'empty'){
                            $("#results_count").html("Укажите параметры поиска");
                            $("#show_but").addClass("disabled");
                            window.location.hash="filter";
                        }
                        else {
                            if (data.find == 0)
                            {
                                $("#results_count").html("По запросу ничего не найдено, измените параметры поиска.");
                                $("#show_but").addClass("disabled");
                            }
                            else if (data.find > 200) {
                                $("#results_count").html("Найдено: "+data.find+" "+data.word+". Уточните параметры поиска.");
                                $("#show_but").addClass("disabled");
                            }
                            else {
                                $("#results_count").html("Найдено: "+data.find+" "+data.word+".");
                                $("#show_but").removeClass("disabled");
                            }
                        }
                        hideLoader();
                    },
                    fail: function(){
                    hideLoader();
                    window.location.hash="filter";

                    }
                });
            }
            else {
                $("#results_count").html("Укажите обязательные параметры поиска");
                $("#show_but").addClass("disabled");
                $(".shoes_results").html("");
                window.location.hash="filter";
            }

    }

    function showShoesResult(){

        if ($("#show_but").hasClass("disabled"))
        {
            return false;
        }

        var $makeFilter = compileShoesFilter();
        var $filter = $makeFilter[0];
        var $can_search = $makeFilter[1];

        if ($can_search == "1")
        {
            $(".shoes_results").html("");
                showLoader();
            $.ajax({
                url: '/ajax/shoes/?action=show_result',
                type: 'POST',
                dataType: 'html',
                data: $filter,

                before: function()
                {
                    showLoader();
                    window.location.hash="filter";
                },
                success:  function(data) {
                    $(".shoes_results").html(data);
                    window.location.hash="sresult";
                    hideLoader();
                },
                fail: function(){
                    hideLoader();

                    window.location.hash="filter";
                }
            });
        }
    }

    function compileShoesFilter()
    {
        var $filter = {};
        $can_search = "0";
        $can_search_length = "0";

        if ($("#fullness").val() >= 10 && $("#fullness").val() < 35 ){
            $filter.fullness = $("#fullness").val();
        }
        if ( $("#length").val() >= 10 &&  $("#length").val() < 35 ){
            $can_search_length = "1";
            $filter.length =  $("#length").val();
        }
        if ($(".person .for-who").hasClass("checked"))
        {
            var $for_who = [];
            $(".person .for-who.checked").each(function(){
                $for_who.push($(this).data("value"));
            });
            if ($for_who.length >= 1)
            {
                $filter.for_who = $for_who;
                $can_search = "1";
            }
            else{
                $can_search = "0";
            }
        }
        else {
            $can_search = "0";
        }

        if ($can_search == "0" || $can_search_length == "0") {

            return [$filter, 0];
        }

        var $season = [];
        $(".seasons .season.checked").each(function(){
            $season.push($(this).data("value"));
        });

        if ($season.length >= 1)
        {
            $filter.season =  $season;
            $can_search = 1;
        }

        $offerType = [];
        $(".offerType .answer input:checked").each(function(){
            $offerType.push($(this).val());
        });
        if ($offerType.length >= 1)
        {
            $filter.offerType = $offerType;
            $can_search = 1;
        }

        $medical = [];
        $(".medical .answer input:checked").each(function(){
            $medical.push($(this).val());
        });
        if ($medical.length >= 1)
        {
            $filter.medical = $medical;
            $can_search = 1;
        }
        $brandOf = [];
        $(".brandOf .answer input:checked").each(function(){
            $brandOf.push($(this).val());
        });
        if ($brandOf.length >= 1)
        {
            $filter.brandOf = $brandOf;
            $can_search = 1;
        }
        return [$filter, $can_search];
    }

});
