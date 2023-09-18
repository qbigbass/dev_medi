$(function(){
    jQuery.fn.dwTimer = function(options) {
        var options = $.extend({
        	timerLoop: false,
        	startDate: false,
        	endDate: false
        }, options);

        //vars
        var timeoutID;

        //j vars
        var $this = this;
        var $dayContainer = $(".timerDayValue");
        var $hourContainer = $(".timerHourValue");
        var $minuteContainer = $(".timerMinuteValue");
        var $secondContainer = $(".timerSecondValue");

        //functions
        var calculateValues = function(){

        	//set vars
        	curDate = new Date();

		    //calculate
	        var remainingDate = new Date(endDate.getTime() - curDate.getTime());
			var remainingSecond = Math.floor((remainingDate / 1000) % 60);
			var remainingMinute = Math.floor((remainingDate / 1000 / 60) % 60);
			var remainingHour = Math.floor((remainingDate / (1000 * 60 * 60)) % 24);
			var remainingDay = Math.floor(remainingDate / (1000 * 60 * 60 * 24));

			//write DOM
			$dayContainer.each(function () {
				$(this).text(remainingDay);
			});

			$hourContainer.each(function () {
				$(this).text(remainingHour);
			});

			$minuteContainer.each(function () {
				$(this).text(remainingMinute);
			});

			$secondContainer.each(function () {
				$(this).text(remainingSecond);
			});
	    };

        // set end time value
        if(options["timerLoop"] > 0 && options["startDate"] > 0){ //loop mode

	        var endDate = new Date(options["startDate"] * 1000); // timestamp msec  (php time() * 1000)
	        var curDate = new Date();

	        //set end date form option
			endDate.setDate(endDate.getDate() + parseInt(options["timerLoop"])); // set tmp endDate

			//loop mode
			while(endDate.getTime() <= curDate.getTime()){
				endDate.setDate(endDate.getDate() + parseInt(options["timerLoop"]));
			}

	    }else if(options["endDate"] != ""){

			var endDate = new Date(options["endDate"] * 1000); // timestamp msec  (php time() * 1000)
			var curDate = new Date();

	    }

    	timeoutID = setInterval(function(){
			calculateValues();
		}, 1000);

		//start timer
		calculateValues();

    };
});