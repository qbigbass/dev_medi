var sliceMenuOpened = false;

$(function(){

	var openDropDownAuth = function(event){

		if(sliceMenuOpened == false){
			$("#topAuth").show();
			sliceMenuOpened = true;
		}else{
			$("#topAuth").hide();
			sliceMenuOpened = false;
		}

		return event.preventDefault();
	}

	var closeDropDownAuth = function(event){
		if(sliceMenuOpened == true){
			$("#topAuth").hide();
			sliceMenuOpened = false;
		}
	};

	$(document).on("click", ".topAuthMenu", openDropDownAuth);
    $(document).on("click", ".topAuthMenu, #topAuth", function(event){
    	return event.stopImmediatePropagation();
    });

	$(document).on("click", closeDropDownAuth);

});
