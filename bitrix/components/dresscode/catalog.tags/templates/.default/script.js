$(function(){

	var showMore = function(event){

		//jquery vars
		var $this = $(this);
		var $tagItemsContainer = $this.parents(".catalogTagItems");
		var $tagItems = $tagItemsContainer.find('.catalogTagItem:not(".moreButton")');

		//other
		var lastLabel = $this.data("last-label");
		var currentLabel = $this.html();

		//check state
		if($this.hasClass("opened")){
			//hide
			$tagItems.removeClass("showAll");
		}

		//display all
		else{
			$tagItems.addClass("showAll");
		}

		//change label
		$this.html(lastLabel).data("last-label", currentLabel);
		$this.toggleClass("opened");

		//block actions
		return event.preventDefault();

	}

	//bind functions
	$(document).on("click", ".catalogTagItems .moreButtonLink", showMore);

});