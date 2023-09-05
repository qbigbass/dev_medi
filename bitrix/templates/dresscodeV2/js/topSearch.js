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

	var searchQueryAdaptive = $("#topSearch3 #topSearchMob #searchQueryAdaptive");
	var openSearchAdaptive = function (event) {
		$('body .overlay:first').show();
		$('#topSearchMob #topSearchAdaptive').css("display", "flex");
		var tmpSearchKeyword = searchQueryAdaptive.val();
		searchVisible = true;
		searchQueryAdaptive.val("");
		searchQueryAdaptive.val(tmpSearchKeyword);
		searchQueryAdaptive.focus();
		$("#topSearch3").css("z-index", 1010);
		$("#topSearch3 #topSearchMob #searchResultAdaptive").css("z-index", 1011);
		event.preventDefault();
	}

	var closeSearchAdaptive = function(event){
		$('body .overlay:first').hide();
		if (searchVisible == true) {
			if (event.which == 1) {
				$("#topSearchMob #searchProductsClose").trigger("click");
				$("#topSearchMob #topSearchAdaptive").hide();
				searchVisible = false;
				return event.preventDefault();
			}
		}
	}

	$(document).on("click", "#headerTools, #topSearchForm, #searchResult", function(event){event.stopImmediatePropagation();});
	$(document).on("click", ".topSearchDesktop #openSearch", openSearch);
	$(document).on("click", ".topSearchDesktop .openSearch", openSearch);
	$(document).on("click", "#topSeachCloseForm", closeSearch);
	$(document).on("click", "#topSearchCloseFormAdaptive", closeSearchAdaptive);
	$(document).on("click", ".overlay", closeSearch);
	$(document).on("click", "#topSearchMob #searchQueryMob", openSearchAdaptive);
});
