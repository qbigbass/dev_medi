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
	var searchResultAdaptive = $("#topSearch3 #topSearchMob #searchResultAdaptive");
	var searchHistoryResultAdaptive = $("#topSearch3 #topSearchMob #searchHistoryResultAdaptive");

	var openSearchAdaptive = function (event) {
		$('body .overlay:first').show();
		$('body').css('overflow', 'hidden');
		$('#topSearchMob #topSearchAdaptive').css("display", "flex");
		var tmpSearchKeyword = searchQueryAdaptive.val();
		searchVisible = true;
		searchQueryAdaptive.val("");
		searchQueryAdaptive.val(tmpSearchKeyword);
		searchQueryAdaptive.focus();
		$("#topSearch3").css("z-index", 1010);
		$("#topSearch3 #topSearchMob #searchResultAdaptive").css("z-index", 1011);

		// Получим популярные поисковые фразы из статистики
		getPopularPhrasesAdaptive();
		event.preventDefault();
	}

	var getPopularPhrasesAdaptive = function () {
		let params = {
			"search_popular_phrases": "Y"
		}
		var jqxhr = $.get('/ajax/search/', params, afterFindPopularPhrasesAdaptive);
	}

	var afterFindPopularPhrasesAdaptive = function (http) {
		searchHistoryResultAdaptive.html(http);
	}

	var closeSearchAdaptive = function(event){
		$('body .overlay:first').hide();
		$('body').css('overflow', 'unset');
		if (searchVisible == true) {
			if (event.which == 1) {
				$("#topSearchMob #searchProductsClose").trigger("click");
				$("#topSearchMob #topSearchAdaptive").hide();
				searchVisible = false;
				return event.preventDefault();
			}
		}
	}

	var clearSearchLineAdaptive = function (event) {
		searchQueryAdaptive.val("");
		searchResultAdaptive.empty();
		return event.preventDefault();
	}

	$(document).on("click", "#headerTools, #topSearchForm, #searchResult", function(event){event.stopImmediatePropagation();});
	$(document).on("click", ".topSearchDesktop #openSearch", openSearch);
	$(document).on("click", ".topSearchDesktop .openSearch", openSearch);
	$(document).on("click", "#topSeachCloseForm", closeSearch);
	$(document).on("click", ".overlay", closeSearch);

	// События для работы со строкой поиска на адаптиве
	$(document).on("click", "#topSearchMob #searchQueryMob", openSearchAdaptive); //Клик по строке поиска в шапке
	$(document).on("click", "#topSearchCloseFormAdaptive", closeSearchAdaptive);
	$(document).on("click", "#topSearchMob #topSearchAdaptive .searchFinder .tfl-popup__close", clearSearchLineAdaptive);
});
