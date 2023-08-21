$(function(){
	var searchVisible;

	var $searchQuery = $(".topSearchDesktop #searchQuery");
	var openSearch = function(event){


		$('body .overlay:first').show();
		$("#topSearch, #topSearch3").slideDown(150, function(){
			var tmpSearchKeyword = $searchQuery.val();
			searchVisible = true;
			$searchQuery.val("");
			$searchQuery.val(tmpSearchKeyword);
			$searchQuery.focus();
			$("#topSearch3").css("z-index", 1010);
			$("#searchResult").css("z-index", 1011);
		});
		event.preventDefault();
	}

	var closeSearch = function(event){

		$('body .overlay:first').hide();
		if(searchVisible == true){
			if(event.which == 1){
				$("#searchProductsClose").trigger("click");
				$("#topSearch, #topSearch3").slideUp(150);
				searchVisible = false;
				return event.preventDefault();
			}
		}
	}

	$(document).keydown(function(event) {
	    if(searchVisible == true && event.keyCode === 27 ) {
			$("#searchProductsClose").trigger("click");
			$("#topSearch, #topSearch3").slideUp(150);
			searchVisible = false;
	        return false;
	    }
	});


	$(document).on("click", "#headerTools, #topSearchForm, #searchResult", function(event){event.stopImmediatePropagation();});
	$(document).on("click", ".topSearchDesktop #openSearch", openSearch);
	$(document).on("click", ".topSearchDesktop .openSearch", openSearch);
	$(document).on("click", "#topSeachCloseForm", closeSearch);
	$(document).on("click", ".overlay", closeSearch);
});
