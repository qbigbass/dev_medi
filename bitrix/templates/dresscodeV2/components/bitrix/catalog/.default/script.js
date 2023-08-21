$(function(){

	var showAllParams = function(e) {
		var $this = $(this);
		var $elements = $this.parents("#nextSection li").siblings("li");

		if ($elements.filter(".off").length > 0) {
			$elements.removeClass("off");
			$this.html(SMART_FILTER_LANG["HIDE_ALL"] + " " + ($elements.length - 5));
		} else {
			$elements.slice(5).addClass("off");
			$this.html(SMART_FILTER_LANG["SHOW_ALL"] + " " + ($elements.length - 5));
		}

		e.preventDefault();
	}

	$(document).on("click", "#nextSection .showALL", showAllParams);
	
	//global vars
	var openSmartFilterFlag = false;
	var changeSortParams = function(){
		window.location.href = $(this).val();
	};

	$("#selectSortParams, #selectCountElements").on("change", changeSortParams);


	var openSmartFilter = function(event){

		// smartFilter block adaptive toggle
		if(!openSmartFilterFlag){
			$("#smartFilter").addClass("opened").css('marginTop', ($('.oSmartFilter').offset().top - $('#nextSection').offset().top - $('#nextSection').height() +25));
			openSmartFilterFlag = true;
		}

		else{
			$("#smartFilter").removeClass("opened").removeAttr("style");
			openSmartFilterFlag = false;
		}

		return event.preventDefault();
	};

	var closeSmartFilter = function(event){
		if(openSmartFilterFlag){
			$("#smartFilter").removeClass("opened");
			openSmartFilterFlag = false;
		}
	};

	$(document).on("click", ".oSmartFilter", openSmartFilter);
    $(document).on("click", "#smartFilter, .oSmartFilter, .rangeSlider", function(event){
    	return event.stopImmediatePropagation();
    });
	$(document).on("click", closeSmartFilter);

});
