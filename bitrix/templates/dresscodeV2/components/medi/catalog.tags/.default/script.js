/* $( document ).ready(function() {
	$("#catalogTags").dwCarouselForTags({
		leftButton: ".mainSalesBtnLeft",
		rightButton: ".mainSalesBtnRight",
		countElement: 4,
		resizeElement: true,
		resizeAutoParams: {
			1920: 4,
			1024: 3,
			550: 2
		}
	});
});


(function($) {
    jQuery.fn.dwCarouselForTags = function(options) {

        var options = $.extend({
            resizeAutoParams: false,
            resizeElement: false,
            resizeWidth: false,
            countElement: 6,
            severity: 8,
            speed: 400,
        }, options);

        options.tmpCountElement = options.countElement; //save original value

        var $_this = $(this);
        var $main = new Main($(this));

        function Main(obj) {

            this.ths = obj;
            this.ul = $_this.find(".slideBox");
            this.li = this.ul.children("li");
            this.a =  this.ul.find("a");
            this.qtyLI = this.li.length;
            this.curPos = 0;
            this.startTouch = false;
            this.startTouchPos = false;
            this.clicking = false;
            this.active = false;
			this.visible = $("#catalogTags").width();
        }
		console.log($main.curPos);
        // main functions

        var onLoadUp = function(){
        
            $_this.css({
                "overflow" : "hidden",
                "position" : "relative"
            });
            
            $main.ul.css({
                "position": "relative",
                "overflow" : "hidden",
                "clear" : "both",
                "left" : "0px",
            });
        
        };

        var bindEvents = function(e) {
            if (e) {

                $(document).on("click", options.leftButton, {direction: "left"}, moveUL);
                $(document).on("click", options.rightButton, {direction: "right"}, moveUL);
                $(document).on("mouseup touchend", touchEnd);


                $main.ul.on("mousedown touchstart", touchStart);
                $main.ul.on("mousemove touchmove", touchMove);
                $main.active = true;

            }
        }

        var resizeElements = function(count) {
            $main.ul.css({
                width: "auto"
            });

 
        };

        var calculateParams = function(windowSize) {
            var currentCount = options.tmpCountElement;
            var tmpCount = 15360;
            options.countElement = options.tmpCountElement;
            if (options.resizeElement === true) {
                if(options.resizeWidth !== false){
                    options.countElement = Math.floor($_this.outerWidth() / options.resizeWidth);
                }else if(options.resizeAutoParams !== false){
                    $.each(options.resizeAutoParams, function(screenWidth, countElements) {
                        if(parseInt(windowSize) <= parseInt(screenWidth)){
                            if(parseInt(tmpCount) > parseInt(screenWidth)){
                                options.countElement = countElements;
                                tmpCount = screenWidth;
                            }

                        }
                    });
                }
            }

            if (options.countElement < $main.qtyLI) {

                $(options.leftButton).show();
                $(options.rightButton).show();

                if (!$main.active) {
                    $main.active = true;
                    bindEvents(true);
                }

            } else {

                $(options.leftButton).hide();
                $(options.rightButton).hide();
                $main.ul.unbind();
                $main.active = false;

            }

            $main.ul.css("left", 0);

        };

        var moveUL = function(event) {
            var direction = event.data.direction == "left" ? "left" : "right",
                maxPos = ($("#catalogTags").find(".slideBox").width() - $main.visible),
                animateValue = null
				console.log(direction);
            if (direction == "left") {
                if ($main.curPos <= 0) {
                    $main.curPos = 0;
					animateValue = 0 + "px";
                } else {
                    animateValue = "-" + ($main.curPos - 200) + "px";
					$main.curPos = $main.curPos - 200;
                }
            } else {
                if ($main.curPos >= maxPos) {
                    $main.curPos = 0;
                    animateValue = 0 + "px";
                } else {
                    animateValue = "-" + ($main.curPos + 200) + "px";
					$main.curPos = $main.curPos + 200;
                }
            }

            $main.ul.finish().animate({
                "left": animateValue
            }, options.speed);

            event.preventDefault();
			console.log("Длина списка" + $("#catalogTags").find(".slideBox").width());
			console.log("Длина визуальной части" + $("#catalogTags").width());
			console.log("Текущая позиция" + $main.curPos);
			console.log("Максимальная позиция" + maxPos);
        };

        var touchStart = function(event) {
            $main.startTouch = event.type == "touchstart" ? event.originalEvent.touches[0].pageX : event.pageX;
            $main.startTouchPos = Math.abs(parseInt($main.ul.css("left"), 10));
            if(event.type !== "touchstart"){
                event.preventDefault();
            }
        };

        var touchMove = function(event) {
            if ($main.startTouch) {
                event.pageX = event.type == "touchmove" ? event.originalEvent.touches[0].pageX : event.pageX;
                var animateValue = (-$main.startTouchPos - ($main.startTouch - event.pageX));
                var maxPos = ($("#catalogTags").find(".slideBox").width() - $main.visible);

                if (animateValue > 0) {
                    animateValue /= 8;
                } else if (maxPos < Math.abs(parseInt($main.ul.css("left")))) {
                    animateValue = -(maxPos + ((Math.abs(animateValue) - maxPos) / 8));
                }

                $main.ul.stop().css({
                    "left" : animateValue + "px"
                });

                $main.clicking = true;
            }
        };

        var touchEnd = function(event) {
            
            if ($main.startTouch) {
                var maxPos = ($("#catalogTags").find(".slideBox").width() - $main.visible),
                    posNow = parseInt($main.ul.css("left")),
                    animateValue = null;
                if (posNow > 0) {
                    animateValue = 0;
                } else if (Math.abs($main.startTouchPos - Math.abs(posNow)) < 30) {
                    animateValue = "-" + $main.startTouchPos;
                    $main.clicking = false;
                } else if (maxPos < Math.abs(posNow)) {
                    animateValue = "-" + maxPos;
                } else {
                    animateValue = "-" + (Math.abs(posNow) > $main.startTouchPos ? Math.ceil(Math.abs(posNow) / $main.li.outerWidth()) : Math.floor(Math.abs(posNow) / $main.li.outerWidth())) * $main.li.outerWidth();
                }

                $main.ul.finish().animate({
                    "left": animateValue
                }, options.speed);

                $main.startTouch = false;

                if($main.clicking){
                    $main.a.each(function(){
                        
                        var $ths = $(this);

                        if($ths.is(":hover")){
                            $ths.one("click", function(event){
                                event.preventDefault();
                            });
                            
                            return false;
                        }
                    
                    });
                
                    $main.clicking = false;

                }
            
            }
        };

        $(window).resize(function(e) {
            calculateParams($(window).width());
            resizeElements(options.countElement);
        });

        calculateParams($(window).width());
        resizeElements(options.countElement);
        bindEvents();
        onLoadUp();

    };

})($);

*/

//////////////////////
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