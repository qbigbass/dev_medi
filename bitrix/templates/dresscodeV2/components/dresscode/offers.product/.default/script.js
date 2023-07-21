$(function(){

	// ecommerce analytics
	var clickEventPush = function($this){
		var $this = $this.parents(".item");
		var productID = $this.data("product-id");
		var productPrice = $this.data("product-price");
		var productCategory = $this.data("product-category");
		var productBrand = $this.data("product-brand");
		var productArticul = $this.data("product-articul");
		var productIblockID = $this.data("product-iblock-id");
		var productListPos = $this.data("position");
		var productListName = $this.parents(".productsListName").data("list-name");

		window.dataLayer = window.dataLayer || [];

		dataLayer.push({
			'ecommerce': {
			  'currencyCode': 'RUB',
			  'click': {
				'actionField': {
				'list': productListName
				},
				'products': [{
				 'name': $this.find(".name span").html(),
				  'id': productID,
				  'price': productPrice,
				  'brand': productBrand,
				  'category': productCategory,
				  'variant': productArticul,
				  'position': productListPos
				}]
			  }
			},
			'event': 'gtm-ee-event',
			'gtm-ee-event-category': 'Enhanced Ecommerce',
			'gtm-ee-event-action': 'Product Clicks',
			'gtm-ee-event-non-interaction': 'False',
		});
		return true;
	};

	var mainElementID = "#homeCatalog"; //--\\
	var $self = $(mainElementID);
	var httpLock = false;

	var getProductByGroup = function(event){
		if(httpLock == false){
			if(offersProductParams != ""){

				var $this = $(this);
				var $parentThis = $this.parent();

				var page = $this.data("page");
				var groupID = $this.data("group");
				var changeSheet = $this.data("sheet");


				if($parentThis.hasClass("selected") && changeSheet != "Y"){
					return false;
				}

				if(changeSheet != "Y"){
					var $captionEL = $self.find(".caption")
										.removeClass("selected");

				}

				var $ajaxContainer = $self.find(".ajaxContainer")
												.addClass("loading");

				$parentThis
					.addClass("loading");

				$this.data("sheet", "N");	// clear status

				if(parseInt(groupID, 10) > 0 || groupID == "all"){

					httpLock = true;

					var sendDataObj = {
						params: offersProductParams,
						groupID: groupID,
						page: page
					}

					var jqxhr = $.get(ajaxDir + "/ajax.php", sendDataObj, function(http) {
						if(http){

							$ajaxContainer.html(http)
								.removeClass("loading");

							$parentThis
								.removeClass("loading");

							if(changeSheet != "Y"){
								$this.parents(".caption")
									.addClass("selected");
							}
							httpLock = false;
							//addCart button reload
							changeAddCartButton(basketProductsNow);
							//subscribe button reload
							//subscribeOnline();
							checkLazyItems();
							$(".itemCard a.name, .itemCard a.picture").bind("click", function(){ clickEventPush($(this));});

						}
					});

				}else{
					console.error("check data group (data.group not found)");
				}

			}else{
				console.error("var type (json) not found (name offersProductParams)");
			}
		}
		return event.preventDefault();

	};

	var getProductNextPage = function(event){

		var $activeGroup = $self.find(".caption.selected a");
		var currentPage = parseInt($activeGroup.data("page"), 10);

		$activeGroup.data({
			"page": currentPage + 1,
			"sheet": "Y"
		});

		$activeGroup.trigger("click");


		$(".itemCard a.name, .itemCard a.picture").bind("click", function(){ clickEventPush($(this));});

		return event.preventDefault();

	}

	$(document).on("click", ".getProductByGroup", getProductByGroup);
	$(document).on("click", ".product .showMore", getProductNextPage);



});


/*---- Корусель карточек товара ---*/

(function($) {
    jQuery.fn.mediCarousel = function(options) {

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
            this.li = this.ul.children(".item");
            this.a =  this.ul.find("a");
            this.qtyLI = this.li.length;
            this.curPos = null;
            this.startTouch = false;
            this.startTouchPos = false;
            this.clicking = false;
            this.active = false;
        }
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
                width: ($main.qtyLI * 100) + "%"
            });

            $main.li.css({
                width: (100 / $main.qtyLI / count) + "%"
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
                maxPos = $main.qtyLI - options.countElement,
                animateValue = null

            if (direction == "left") {
                if (!$main.curPos) {
                    animateValue = "-" + (100 / options.countElement * maxPos) + "%";
                    $main.curPos = maxPos;
                } else {
                    animateValue = "-" + (100 / options.countElement * --$main.curPos) + "%";
                }
            } else {
                if ($main.curPos == maxPos) {
                    $main.curPos = maxPos;
                    $main.curPos = animateValue = 0;
                } else {
                    animateValue = "-" + (100 / options.countElement * ++$main.curPos) + "%";
                }
            }

            $main.ul.finish().animate({
                "left": animateValue
            }, options.speed);

            event.preventDefault();
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
                var maxPos = ($main.li.outerWidth() * $main.qtyLI) - (options.countElement * $main.li.outerWidth());

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
                var maxPos = ($main.li.outerWidth() * $main.qtyLI) - (options.countElement * $main.li.outerWidth()),
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