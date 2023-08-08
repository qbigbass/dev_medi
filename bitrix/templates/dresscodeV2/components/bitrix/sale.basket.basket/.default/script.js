$(function(){

	//modern mode
	"use strict";

	//constants for fast customization
	const classNameCluster = {

		//main
		order: "DwBasketOrder",
		basket: "DwBasket",

		//select fields
		personType: "personSelect",

		//stores
		storesSwitch: "storeSelect",
		storesContainer: "storeSelectContainer",

		//checkbox fields
		budgetContainer: "payFromBudget",
		budget: "budgetSwitch",

		//input fields
		quantityField: "qty",

		//extra services
		extraService: "extraServiceSwitch",
		extraServiceItem: "extraServicesItem",
		extraServiceItems: "extraServiceItems",
		extraServiceItemSum: "extraServiceItemSum",
		extraServiceItemContainer: "extraServicesItemContainer",

		//delivery
		delivery: "deliverySelect",
		deliverySum: "deliverySum",
		deliveryProps: "deliveryProps",

		//paysystem
		paysystem: "paySelect",
		paysystemProps: "payProps",

		//location
		locationField: "location",
		locationSwitchLink: "locationSwitchLink",
		locationSwitchContainer: "locationSwitchContainer",

		//coupon
		couponButton: "couponActivate",
		couponButtonCancel: "couponDeActivate",
		couponField: "couponField",

		//link
		remove: "delete",
		minus: "minus",
		plus: "plus",

		//basket items
		productItemList: "productList",
		productItem: "parent",

		//product fields
		productPrice: "priceContainer",
		productDiscount: "discountContainer",

		//properties
		propertyItem: "propItem",

		//special
		clearAll: "clearAllBasketItems",
		giftContainer: "giftContainer",
		minPayment: "minimumPayment",
		orderComment: "orderComment",
		fastBuy: "fastBayContainer",
		countItems: "countItems",
		orderAreas: "orderAreas",
		orderMove: "orderMove",
		basketSum: "basketSum",
		discountSum: "discountSum",
		basketAllSum: "basketAllSum",
		goToOrder: "goToOrder",
		orderSum: "orderSum",
		propActive: "active",
		propHidden: "hidden",

		//erros
		errorWindowClose: "basketErrorClose",
		errorWindowMessage: "errorMessage",
		errorWindow: "basketError",
		error: "error",

		//new order
		orderMake: "orderMake"

	};

	//error form ids
	const errorIds = {
		maxQuantity: "error1",
		orderErrors: "error2",
		couponError: "error3",
	};

	//other constants
	const timeoutDelay = 100;

	//vars
	var timeoutId = 0;
	var deliveryId = 0;
	var compilation = {};

	//application object map
	var application = {
		calculate: {},
		binding: {
			keypress: {},
			mouseup: {},
			change: {},
			click: {},
			focus: {},
			keyup: {},
			blur: {},
		},
		tools: {},
		commit:{
			basket:{},
			location:{},
		},
		flags: {
			location: {
				open: false
			}
		},
		check: {},
		price: {},
		ajax: {},
		push: {},
		pull: {},
	};

	//binding functions

	application.showLoader =  function(event){
		$('body').css({'overflow' : 'hidden'});
		$('body .loader:first').show();
		$('body .overlay:first').show();
	}
	application.hideLoader = function(event){
		$('body').css({'overflow' : ''});
		$('body .loader:first').hide();
		$('body .overlay:first').hide();
	}
	//click events
	application.binding.click.stop = function(event){
		return event.stopImmediatePropagation();
	};

	application.binding.click.closeout = function(event){
		//location
		if(application.flags.location.open === true){
			application.tools.resetLocation(event);
		}
	};

	//create order
	application.binding.click.orderMake = function(event){

		//jquery vars
		var $this = $(this);

		//other
		var sendObject = new FormData();

		//push request params
		sendObject.append("actionType", "orderMake");
		sendObject.append("siteId", siteId);

		//get orderFields
		var $orderFields = application.pull.getOrderFields();

		//check required fields
		if(application.check.required($orderFields)){

			//push order fields values
			sendObject = application.push.serializeFields($orderFields, sendObject);

			//push order comment
			sendObject = application.push.orderComment(sendObject);

			//push basket fields values
			sendObject = application.push.fields(sendObject);

			//push arParams
			sendObject = application.push.params(sendObject);

			//start loader
			application.tools.launchLoader($this);

			//send data
			application.ajax.sendData(ajaxDir + "/ajax.php", sendObject, "post", "json", dataProcessing, false);

			//proccesing data after request
			function dataProcessing(jsonData){

				//check state
				if(jsonData["status"] === true && typeof jsonData["orderResult"]["orderId"] != undefined){
					self.location.replace(application.tools.addParamToUrl({orderId: jsonData["orderResult"]["orderId"]}));
				}

				//check errors
				else{
					if(jsonData["error"] === true){
						application.tools.addError($this);
						application.tools.pushAjaxErrors(jsonData);
						console.error(jsonData);
					}
				}

				//remove loader
				application.tools.stopLoader($this);

			};


		}

		return event.preventDefault();

	};

	application.commit.basket.refreshCart = function(event){
		application.showLoader();
		window.location.reload();
	};


	application.binding.click.setCoupon = function(event){

		//jquery vars
		var $couponField = $("." + classNameCluster.couponField);
		var $this = $(this);

		//other
		var couponValue = $couponField.val();

		//check empty
		if(couponValue != "undefined" && couponValue != ""){

			//create formData
			var sendObject = new FormData();

			//push request params
			sendObject.append("actionType", "setCoupon");
			sendObject.append("coupon", couponValue);
			sendObject.append("siteId", siteId);

			//start loader
			application.tools.launchLoader($this);

			//send data
			application.ajax.sendData(ajaxDir + "/ajax.php", sendObject, "post", "json", dataProcessing, false);

			//proccesing data after request
			function dataProcessing(jsonData){

				//check state
				if(jsonData["status"] === true && jsonData["success"] === true){
					//page reload
					application.showLoader();
					if (jsonData['coupon_ok'] === true)
					{
						$("#coupon .couponField").addClass("coupon_ok");
						$(".couponActivate").addClass("coupon_ok").val("✔ принят");
						setTimeout("self.location.reload();", 3000);
					}
					else{
						$("#coupon .couponField").removeClass("coupon_ok").addClass("coupon_fail");
						$(".couponActivate").addClass("coupon_fail").val("× не найден");
						/*application.tools.addError($couponField);
						application.tools.pushAjaxErrors(jsonData, "couponError");*/
						console.error(jsonData);
						setTimeout("self.location.reload();", 3000);
					}
					application.tools.stopLoader($this);
					//self.location.reload();
				}

				//check errors
				else{
					if(jsonData["error"] === true){
						//set errors
						/*application.tools.addError($couponField);
						application.tools.pushAjaxErrors(jsonData, "couponError");*/
						console.error(jsonData);
					}
				}

				//remove loader
				application.tools.stopLoader($this);

			};

		}

		//set error
		else{
			application.tools.addError($couponField);
		}

		//block actions
		return event.preventDefault();

	};

	application.binding.click.unsetCoupon = function(event){

		//jquery vars
		var $couponField = $("." + classNameCluster.couponField);
		var $this = $(this);

		//other
		var couponValue = $couponField.val();

		//check empty
		if(couponValue != "undefined" && couponValue != ""){


			//create formData
			var sendObject = new FormData();

			//push request params
			sendObject.append("actionType", "unsetCoupon");
			sendObject.append("coupon", couponValue);
			sendObject.append("siteId", siteId);

			//start loader
			application.tools.launchLoader($this);

			//send data
			application.ajax.sendData(ajaxDir + "/ajax.php", sendObject, "post", "json", dataProcessing, false);

			//proccesing data after request
			function dataProcessing(jsonData){

				//check state
				if(jsonData["status"] === true && jsonData["success"] === true){
					//page reload
					application.showLoader();

					self.location.reload();
				}

				//check errors
				else{
					if(jsonData["error"] === true){
						//set errors
						application.tools.addError($couponField);
						application.tools.pushAjaxErrors(jsonData, "couponError");
						console.error(jsonData);
					}
				}

				//remove loader
				application.tools.stopLoader($this);

			};

		}

		//set error
		else{
			application.tools.addError($couponField);
		}

		//block actions
		return event.preventDefault();

	};

	application.binding.click.clearAll = function(event){

		//jquery vars
		var $this = $(this);

		//create formData
		var sendObject = new FormData();

		//push request params
		sendObject.append("actionType", "clearAll");
		sendObject.append("siteId", siteId);

		//start loader
		application.tools.launchLoader($this);

		//send data
		application.ajax.sendData(ajaxDir + "/ajax.php", sendObject, "post", "json", dataProcessing, false);

		//proccesing data after request
		function dataProcessing(jsonData){

			//check state
			if(jsonData["status"] === true){
				//page reload
				application.showLoader();
				self.location.reload();
			}

			//check errors
			else{
				if(jsonData["error"] === true){
					application.tools.pushAjaxErrors(jsonData);
					console.error(jsonData);
				}
			}

			//remove loader
			//application.tools.stopLoader($this);

		};

		//block actions
		return event.preventDefault();

	};

	application.binding.click.deleteItem = function(event){

		application.showLoader();

		//jquery vars
		var $this = $(this);

		//other
		var basketItemId = $this.data("id");
		var sendObject = new FormData();

		//push request params
		sendObject.append("actionType", "removeItem");
		sendObject.append("basketId", basketItemId);
		sendObject.append("siteId", siteId);

		//push arParams
		sendObject = application.push.params(sendObject);

		//push basket fields values
		sendObject = application.push.fields(sendObject);

		//start loader
		application.tools.launchLoader($this);

		//send data
		application.ajax.sendData(ajaxDir + "/ajax.php", sendObject, "post", "json", dataProcessing, false);

		//proccesing data after request
		function dataProcessing(jsonData){

			//check state
			if(jsonData["status"] === true){

				window.dataLayer = window.dataLayer || [];

				dataLayer.push({
				'ecommerce': {
				  'currencyCode': 'RUB',
				  'remove': {
				    'products': [{
				     'name': jsonData['product']['NAME'],
				      'id': jsonData['product']['ID'],
				      'price': jsonData['product']['PRICE'],
				      'brand': jsonData['product']['BRAND'],
				      'category': jsonData['product']['CATEGORY'],
				      'variant': jsonData['product']['CML2_ARTICLE'],
				       'quantity':jsonData['product']['QUANTITY']
				    }]
				  }
				},
				'event': 'gtm-ee-event',
				'gtm-ee-event-category': 'Enhanced Ecommerce',
				'gtm-ee-event-action': 'Removing a Product from a Shopping Cart',
				'gtm-ee-event-non-interaction': 'False',
				});

				//reloading page if not find items
				if($this.parents("." + classNameCluster.productItemList).find("." + classNameCluster.productItem).length <= 1){
					//page reload
					application.showLoader();
					self.location.reload();
				}
				else{
					//delete dom item
					$this.parents("." + classNameCluster.productItem).remove();
					//commit result data
					//application.commit.basket.proccesing(jsonData);

					application.showLoader();
					application.commit.basket.realtime();
					application.commit.basket.refreshCart();
				}

				// JS-трекер Loymax (Событие: удаления товара из корзины)
				var _gcTracker = window._gcTracker || [];
				_gcTracker.push(['remove_from_card', { product_id: jsonData['product']['ID'] }]);
			}

			//check errors
			else{
				if(jsonData["error"] === true){
					application.tools.pushAjaxErrors(jsonData);
					console.error(jsonData);
					application.hideLoader();
				}
			}

		};

		//block actions
		return event.preventDefault();

	};

	application.binding.click.quantityMinus = function(event){

		//application.showLoader();
		//jquery vars
		var $this = $(this);
		var $basketItem = $this.parents("." + classNameCluster.productItem);
		var $quantityField = $basketItem.find("." + classNameCluster.quantityField);

		//other
		var measureRatio = parseFloat($quantityField.data("ratio"));
		var currentQuantity = parseFloat($quantityField.val());

		//need changing
		if(currentQuantity > measureRatio){

			//pre calculate quantity
			var preQuantity = currentQuantity - measureRatio;

			//set value
			$quantityField.val(+preQuantity.toFixed(5));
			$quantityField.trigger("change");

			var dl_product = $this.parents('.item');

			window.dataLayer = window.dataLayer || [];

			dataLayer.push({
			'ecommerce': {
			  'currencyCode': 'RUB',
			  'remove': {
				'products': [{
				 'name': dl_product.data('product-name'),
				  'id': dl_product.data('product-id'),
				  'price': dl_product.data('product-price'),
				  'brand': dl_product.data('product-brand'),
				  'category': dl_product.data('product-category'),
				  'variant': dl_product.data('product-articul'),
				   'quantity':1
				}]
			  }
			},
			'event': 'gtm-ee-event',
			'gtm-ee-event-category': 'Enhanced Ecommerce',
			'gtm-ee-event-action': 'Removing a Product from a Shopping Cart',
			'gtm-ee-event-non-interaction': 'False',
			});

		}
		else {
			application.hideLoader();
		}

		//block actions
		return event.preventDefault();

	};

	application.binding.click.quantityPlus = function(event){

		application.showLoader();
		//jquery vars
		var $this = $(this);
		var $basketItem = $this.parents("." + classNameCluster.productItem);
		var $quantityField = $basketItem.find("." + classNameCluster.quantityField);

		//other
		var measureRatio = parseFloat($quantityField.data("ratio"));
		var currentQuantity = parseFloat($quantityField.val());

		//pre calculate quantity
		var preQuantity = currentQuantity + measureRatio;

		//set value
		$quantityField.val(+(preQuantity).toFixed(5));
		$quantityField.trigger("change");

		var dl_product = $this.parents('.item');

		window.dataLayer = window.dataLayer || [];

		dataLayer.push({
		'ecommerce': {
		  'currencyCode': 'RUB',
		  'add': {
			'products': [{
			 'name': dl_product.data('product-name'),
			  'id': dl_product.data('product-id'),
			  'price': dl_product.data('product-price'),
			  'brand': dl_product.data('product-brand'),
			  'category': dl_product.data('product-category'),
			  'variant': dl_product.data('product-articul'),
			   'quantity':1
			}]
		  }
		},
		'event': 'gtm-ee-event',
		'gtm-ee-event-category': 'Enhanced Ecommerce',
		'gtm-ee-event-action': 'Adding a Product to a Shopping Cart',
		'gtm-ee-event-non-interaction': 'False',
		});


		//application.hideLoader();
		//block actions
		return event.preventDefault();

	};

	application.binding.click.locationLink = function(event){

		//jquery vars
		var $this = $(this);
		var $locationContainer = $this.parents("." + classNameCluster.locationSwitchContainer);
		var $locationField = $locationContainer.siblings("." + classNameCluster.locationField);

		//other
		var locationName = $this.data("path");
		var locationId = $this.data("id");

		//set values
		$locationField.val(locationName);
		$locationField.data("location", locationId);

		//close
		application.tools.closeLocationDropdown($locationContainer);

		//flush basket
		application.commit.basket.flush();

		//block actions
		return event.preventDefault();

	};

	application.binding.click.orderMove = function(){

		//vars
		var $order = $("." + classNameCluster.order);
		var $minPayment = $("." + classNameCluster.minPayment);

		//other
		var blockActions = true;

		//check object
		if(typeof $order === "object" && $order.is(":visible")){
			application.tools.scrollTo($order);
		}

		//check min payment to scroll
		else{

			//check object
			if(typeof $minPayment === "object" && !$.isEmptyObject($minPayment)){

				//check for available
				if($minPayment.is(":visible")){
					application.tools.scrollTo($minPayment);
				}

				else{
					blockActions = false;
				}

			}

		}

		//block actions
		if(blockActions === true){
			return event.preventDefault();
		}

	}

	application.binding.click.closeErrorWindow = function(){

		//jquery vars
		var $this = $(this);
		var $errorWindow = $this.parents("." + classNameCluster.errorWindow);

		//hide
		application.tools.closeWindow($errorWindow);

		//block actions
		return event.preventDefault();

	}

	//keyup events
	application.binding.keyup.location = function(event){

		//remove previous
		clearTimeout(timeoutId);

		//realtime calc
		timeoutId = setTimeout(application.commit.location.pushComponent, timeoutDelay);

	};

	//keypress events
	application.binding.keypress.quantity = function(event){

		//available characters
		var allowedChars = [44, 46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57];

		//check which
		if(allowedChars.indexOf(event.which) === -1){

			//block actions
			return event.preventDefault();

		}

	};

	//focus events
	application.binding.focus.location = function(event){

		//jquery vars
		var $this = $(this);

		//other
		var currentValue = $this.val().toString();
		var currentId = $this.data("location");

		//save last value
		$this.data("last-value", currentValue);
		$this.data("last-id", currentId);

		//reset
		$this.val("");
		$this.data("location", "");

	};

	application.binding.focus.coupon = function(event){

		//jquery vars
		var $this = $(this);

		//clear errors
		application.tools.removeError($this);

	};

	//blur events
	application.binding.blur.location = function(event){

		if(application.flags.location.open === false){
			application.tools.resetLocation(event);
		}

		return false;

	};

	//change events
	application.binding.change.quantity = function(event){

		application.showLoader();
		//jquery vars
		var $this = $(this);

		//other
		var basketItemId = $this.data("id");
		var maxAmount = parseFloat($this.data("max-quantity"));
		var measureRatio = parseFloat($this.data("ratio"));
		var currentQuantity = parseFloat($this.val());
		var multiplicity = +(currentQuantity / measureRatio).toFixed(5);
		var defQuantity = currentQuantity;

		//reset errors
		application.tools.removeError($this);

		//need changing
		//check max amount
		if(typeof maxAmount !== undefined && currentQuantity > maxAmount){

			//set max value
			currentQuantity = maxAmount;

			//commit
			$this.val(currentQuantity);

			//set error
			application.tools.addError($this);

			//show error window flag 1
			application.tools.openWindow(errorIds.maxQuantity);

		}

		//check nan & min value
		else if(isNaN(currentQuantity) || currentQuantity < measureRatio){

			//set min value
			currentQuantity = measureRatio;

			//commit
			$this.val(currentQuantity);

		}

		//check multiple of number
		else if((multiplicity ^ 0) !== multiplicity){

			//set last value
			currentQuantity = $this.data("last-value");

			//commit
			$this.val(currentQuantity);

		}

		//
		else{
			//save value
			$this.data("last-value", currentQuantity);
			$this.val(currentQuantity);
		}

		//remove previous
		clearTimeout(timeoutId);

		application.commit.basket.realtime();

		//realtime calc
		timeoutId = setTimeout(function(){
			application.commit.basket.updateProduct(basketItemId, currentQuantity);

		}, timeoutDelay);
	};

	application.binding.change.person = function(event){

		//jquery vars
		var $this = $(this);
		var $orderAreas = $("." + classNameCluster.orderAreas);

		//other
		var personTypeId = parseInt($this.val());

		//reset delivery
		deliveryId = 0;

		//hide all areas
		application.tools.hideByFlag($orderAreas, false, "active");

		//show actual areas
		application.tools.hideByFlag($orderAreas.filter('[data-person-id="' + personTypeId + '"]'), true, "active");

		//flush
		application.commit.basket.flush();

	};

	application.binding.change.delivery = function(event){
		application.commit.basket.flush();
	};

	application.binding.change.paysystem = function(event){
		application.commit.basket.flush();
	};

	application.binding.change.budget = function(event){
		application.commit.basket.realtime();
	};

	application.binding.change.extraService = function(event){

		//jquery vars
		var $this = $(this);
		var $basket = $("." + classNameCluster.basket);
		var $deliveryField = $basket.find("." + classNameCluster.delivery).find("option:selected");

		//get input type
		var type = $this.attr("type");
		var tagName = $this.prop("tagName").toLowerCase();

		//get values
		var values = {
			current: parseFloat($this.val()),
			default: parseFloat($this.data("default")),
			price: parseFloat($this.data("price")),
			last: parseFloat($this.data("last")),
		};

		//delivery info
		var delivery = {
			price: parseFloat($deliveryField.data("price")),
			name: $deliveryField.data("name")
		};

		//special
		var final = {},
			last = {};

		//check type
		if(type == "text"){

			//jquery vars
			var $sumContainer = $this.parents("." + classNameCluster.extraServiceItem).find("." + classNameCluster.extraServiceItemSum);

			//check nan & min value
			if(isNaN(values.current) || values.current < 0){
				values.current = values.default;
			}

			//calculate final sum
			final.sum = values.current * values.price;

			//calculate last sum
			last.sum = values.last * values.price;

			//calculate final delivery sum
			delivery.sum = delivery.price - last.sum + final.sum;

			//update sum container
			$sumContainer.html(application.price.format(final.sum, false, false, false, true));

			//update quantity
			$this.val(values.current).data("last", values.current);

		}

		else if(type == "checkbox"){
			delivery.sum = $this.is(":checked") ? delivery.price + values.price : delivery.price - values.price;
		}

		else if(tagName == "select"){

			//get price
			values.current = parseFloat($this.find("option:selected").data("price"));

			//calculate total delivery sum
			delivery.sum = delivery.price - values.last + values.current;

			//update last
			$this.data("last", values.current);

		}

		//update delivery container
		// $deliveryField.html(delivery.name + " " + application.price.format(delivery.sum, false, false, false, true));
		// $deliveryField.data("price", delivery.sum);

		//flush basket
		application.commit.basket.flush();

	};

	//commit functions

	//basket

	application.commit.basket.updateProduct = function(basketId, quantity){

		application.showLoader();
		if(typeof basketId === "number" && typeof quantity === "number"){

			//jquery vars
			var $self = $("." + classNameCluster.basket);

			//other
			var sendObject = new FormData();

			//push request params
			sendObject.append("actionType", "updateItem");
			sendObject.append("basketId", basketId);
			sendObject.append("quantity", quantity);
			sendObject.append("siteId", siteId);

			//push arParams
			sendObject = application.push.params(sendObject);

			//push basket fields values
			sendObject = application.push.fields(sendObject);

			//start loader
			//application.tools.launchLoader($self, "wait");
			//application.showLoader();

			//send data
			application.ajax.sendData(ajaxDir + "/ajax.php", sendObject, "post", "json", dataProcessing, false);

			//proccesing data after request
			function dataProcessing(jsonData){

				//check state
				if(jsonData["status"] === true){
					//commit result data
					application.commit.basket.proccesing(jsonData);
/*
					if (jsonData.compilation.items){
						var a = jsonData.compilation.items;
						var index = 0;
						var products = [];
						for (var key in a) {
						    if (a.hasOwnProperty(key) &&
						        /^0$|^[1-9]\d*$/.test(key) &&
						        key <= 4294967294) {


							    products[index] = {
									'id' : a[key]['PRODUCT_ID'],
									'price' : a[key]['PRICE'],
									'quantity' : a[key]['QUANTITY']
								};
								++index;

							}
						}
						var dataLayer = dataLayer || [];
				        dataLayer.push({
				            'event': 'crto_basketpage',
				            crto: {
				                'email': $("#nuseremail").val(),
				                'products': [products]
				            }
				        });
					}*/
					application.showLoader();
					application.commit.basket.refreshCart();

				}

				//check errors
				else{
					if(jsonData["error"] === true){
						application.tools.pushAjaxErrors(jsonData);
						console.error(jsonData);
					}
				}
				//remove loader
				//application.tools.stopLoader($self, "wait");
				//application.hideLoader();
			};

		}

		else{
			console.error("check transmitted data");application.hideLoader();
		}

	}

	application.commit.basket.flush = function(){

		//jquery vars
		var $self = $("." + classNameCluster.basket);

		//other
		var sendObject = new FormData();

		//push request params
		sendObject.append("actionType", "getCompilation");
		sendObject.append("siteId", siteId);

		//push basket fields values
		sendObject = application.push.fields(sendObject);

		//push arParams
		sendObject = application.push.params(sendObject);

		//start loader
		//application.tools.launchLoader($self, "wait");
		application.showLoader();

		//send data
		application.ajax.sendData(ajaxDir + "/ajax.php", sendObject, "post", "json", dataProcessing, false);

		//proccesing data after request
		function dataProcessing(jsonData){

			//check state
			if(jsonData["status"] === true){
				//commit result data
				application.commit.basket.proccesing(jsonData);
			}

			//check errors
			else{
				if(jsonData["error"] === true){
					application.tools.pushAjaxErrors(jsonData);
					console.error(jsonData);
				}
			}

			//remove loader
			//application.tools.stopLoader($self, "wait");
			//application.hideLoader();
		};

	};

	application.commit.basket.proccesing = function(jsonData){
		application.showLoader();
		//set global compilation for fast access
		//application.commit.basket.setCompilation(jsonData);

		//commit other
		application.commit.basket.items();
		//application.commit.basket.deliveries();
		//application.commit.basket.paysystems();
		application.commit.basket.minOrderPrice();

		//gifts
		application.commit.basket.gifts(jsonData);

		//update basket fields
		application.commit.basket.realtime();
		//application.hideLoader();
	};

	application.commit.basket.items = function(){

		//check empty
		if(typeof compilation["items"] === "object" && !$.isEmptyObject(compilation["items"])){

			//get items data from last compilation data
			var basketItems = compilation["items"];

			//get basket items DOM
			var $basketItems = $("." + classNameCluster.productItem);

			//each data
			$.each(basketItems, function(currentIndex, basketItem){

				//find current product
				var $basketItem = $basketItems.filter('[data-id="'+ basketItem["PRODUCT_ID"] +'"]')
				//find price container
				var $basketItemPrice = $basketItem.find("." + classNameCluster.productPrice);
				//find discount container
				var $basketItemDiscount = $basketItem.find("." + classNameCluster.productDiscount).addClass("hidden");

				//commit price
				$basketItemPrice.html(basketItem["PRICE_FORMATED"]);
				$basketItemPrice.data("price", basketItem["PRICE"]);

				//commit discount price
				if(basketItem["DISCOUNT"] > 0){
					$basketItemDiscount.html(basketItem["BASE_PRICE_FORMATED"]).removeClass("hidden");
				}

			});


		}

	};

	application.commit.basket.deliveries = function(){

		//check empty
		if(typeof compilation["order"]["DELIVERIES"] === "object" && !$.isEmptyObject(compilation["order"]["DELIVERIES"])){

			//jquery vars
			var $this = $("." + classNameCluster.propActive + " ." + classNameCluster.delivery);

			//other
			var currentId = parseInt($this.val());

			//get deliveries from last compilation data
			var deliveries = compilation["order"]["DELIVERIES"];

			//get last delivery id
			deliveryId = application.pull.getLastDeliveryId();

			//clear
			$this.html("");

			//each data
			$.each(deliveries, function(currentIndex, nextDelivery){

				//create next delivery item
				var $deliveryItem = $("<option/>").val(nextDelivery["ID"]);

				//set data attrs (price, name)
				$deliveryItem.data({
					price: nextDelivery["PRICE"],
					name: nextDelivery["NAME"]
				});

				//set title
				$deliveryItem.html(nextDelivery["NAME"] + " " + nextDelivery["PRICE_FORMATED"]);

				//set selected
				if(currentId == nextDelivery["ID"]){
					$deliveryItem.attr("selected", "selected");
				}

				//push
				$this.append($deliveryItem);

			});

			//refresh var
			currentId = parseInt($this.val());

			//processing
			application.commit.basket.deliveryStores(currentId);
			application.commit.basket.deliveryProperties(currentId);

			//need refresh extra services
			if(deliveryId != currentId){
				application.commit.basket.deliveryServices(currentId);
			}

			return deliveryId = currentId;

		}

	};

	application.commit.basket.deliveryStores = function(deliveryId){

		//check types
		if(typeof deliveryId !== "number" || isNaN(deliveryId) || typeof compilation["order"]["DELIVERIES"] !== "object" || typeof compilation["stores"] !== "object"){
			return false;
		}

		//jquery vars
		var $storesContainer = $("." + classNameCluster.propActive + " ." + classNameCluster.storesContainer);
		var $storesSwitch = $storesContainer.find("." + classNameCluster.storesSwitch);

		//other
		var hideContainer = true;
		var currentId = parseInt($storesSwitch.val());

		//clear
		$storesSwitch.html("");

		//get deliveries from last compilation data
		var deliveriesData = compilation["order"]["DELIVERIES"];
		var storesData = compilation["stores"];

		//check stores
		if(typeof deliveriesData[deliveryId]["STORES"] === "object" && !$.isEmptyObject(deliveriesData[deliveryId]["STORES"])){

			//each stores
			$.each(storesData, function(index, nextStore){

				//check exist
				if($.inArray(nextStore["ID"], deliveriesData[deliveryId]["STORES"]) != -1){

					//create next store item
					var $storeItem = $("<option/>").val(nextStore["ID"]);

					//set title
					$storeItem.html(nextStore["TITLE"] + " - " + nextStore["ADDRESS"] + " - " + nextStore["PRODUCTS_STATUS"]);

					//set selected
					if(currentId == nextStore["ID"]){
						$storeItem.attr("selected", "selected");
					}

					//push
					$storesSwitch.append($storeItem);

					//set flag (no hide)
					hideContainer = false;

				}

			});

		}

		//hide if not exist stores
		application.tools.hideByFlag($storesContainer, hideContainer);

	};

	application.commit.basket.deliveryServices = function(deliveryId){

		//check types
		if(typeof deliveryId !== "number"){
			return false;
		}

		//jquery vars
		var $serviceItemsContainers = $("." + classNameCluster.propActive + " ." + classNameCluster.extraServiceItemContainer);
		var $serviceItems = $("." + classNameCluster.propActive + " ." + classNameCluster.extraServiceItems);
		var $serviceItem = $("." + classNameCluster.propActive + " ." + classNameCluster.extraService);

		//filter active delivery items
		var $itemsByDelivery = $serviceItemsContainers.filter('[data-delivery-id="' + deliveryId + '"]');

		//check main containers
		application.tools.hideByFlag($serviceItems, $itemsByDelivery.length <= 0);

		//hide all service items
		application.tools.hideByFlag($serviceItemsContainers, true);

		//show service from current delivery
		application.tools.hideByFlag($itemsByDelivery, false);

		//set default values
		$.each($serviceItem, function(){

			//jquery vars
			var $this = $(this);

			//other
			var item = {
				id: $this.attr("id"),
				type: $this.attr("type"),
				value: $this.val(),
				default: $this.data("default"),
				price: parseFloat($this.data("price")),
			}

			//check type
			if(item.type == "text" || item.type == "number"){

				//jquery vars
				var $sumContainer = $this.parents("." + classNameCluster.extraServiceItem).find("." + classNameCluster.extraServiceItemSum);

				//update sum container
				$sumContainer.html(application.price.format((item.value * item.price), false, false, false, true));

				//push values
				$this.val(item.default).data("last", item.default);

			}

			else if(item.type == "checkbox"){

				if(item.default == "Y" || item.default == item.id){
					$this.prop("checked", true);
				}

				else if(item.default == "N" || item.default != item.id){
					$this.removeAttr("checked");
				}

			}

		});

	};

	application.commit.basket.deliveryProperties = function(deliveryId){

		//check types
		if(typeof deliveryId !== "number" || typeof compilation["order"]["PROPERTIES"] !== "object"){
			return false;
		}

		//jquery vars
		var $deliveryProperties = $("." + classNameCluster.propActive + " ." + classNameCluster.deliveryProps);

		//get properties from last compilation data
		var orderProperties = compilation["order"]["PROPERTIES"]["PROPERTIES"];

		//hide all delivery properties
		application.tools.hideByFlag($deliveryProperties, true);

		//show properties from current delivery
		$.each(orderProperties, function(){
			if(typeof this["RELATION"] === "object" && !$.isEmptyObject(this["RELATION"])){
				if(typeof this["DELIVERY_RELATION"] !== undefined && this["DELIVERY_RELATION"] === "Y"){
					application.tools.hideByFlag($deliveryProperties.filter('[data-property-id="' + this["ID"] + '"]'));
				}
			}
		});

	};

	application.commit.basket.gifts = function(jdata){

		//jquery vars
		var $giftContainer = $("." + classNameCluster.giftContainer);

		//get & check gift component
		var giftComponent = typeof jdata["gifts"] !== "undefined" ? jdata["gifts"] : "";

		//push gifts component
		$giftContainer.html(giftComponent);

	};

	application.commit.basket.paysystems = function(){

		//check empty
		if(typeof compilation["order"]["PAYSYSTEMS"] !== "object" || $.isEmptyObject(compilation["order"]["PAYSYSTEMS"])){

			//for display error message
			compilation["order"]["PAYSYSTEMS"] = {
				0: {
					ID: 0,
					NAME: basketLang["empty-paysystems"]
				}
			}

		}

		//jquery vars
		var $this = $("." + classNameCluster.propActive + " ." + classNameCluster.paysystem);

		//other
		var currentId = parseInt($this.val());

		//get deliveries from last compilation data
		var paysystems = compilation["order"]["PAYSYSTEMS"];

		//clear
		$this.html("");

		//each data
		$.each(paysystems, function(currentIndex, nextPaysystem){

			//create next delivery item
			var $paysystemItem = $("<option/>").val(nextPaysystem["ID"]);

			//set title
			$paysystemItem.html(nextPaysystem["NAME"]);

			//set selected
			if(currentId == nextPaysystem["ID"]){
				$paysystemItem.attr("selected", "selected");
			}

			//push
			$this.append($paysystemItem);

		});

		//processing
		application.commit.basket.payProperties();
		application.commit.basket.payInner();

	};

	application.commit.basket.payProperties = function(){

		//check types
		if(typeof compilation["order"]["PROPERTIES"] !== "object"){
			return false;
		}

		//jquery vars
		var $paysystemProperties = $("." + classNameCluster.propActive + " ." + classNameCluster.paysystemProps);

		//get properties from last compilation data
		var orderProperties = compilation["order"]["PROPERTIES"]["PROPERTIES"];

		//hide all delivery properties
		application.tools.hideByFlag($paysystemProperties, true);

		//show properties from current delivery
		$.each(orderProperties, function(){
			if(typeof this["RELATION"] === "object" && !$.isEmptyObject(this["RELATION"])){
				if(typeof this["PAYSYSTEM_RELATION"] !== undefined && this["PAYSYSTEM_RELATION"] === "Y"){
					application.tools.hideByFlag($paysystemProperties.filter('[data-property-id="' + this["ID"] + '"]'));
				}
			}
		});

	};

	application.commit.basket.payInner = function(){

		//jquery vars
		var $innerPaymentContainer = $("." + classNameCluster.budgetContainer);
		var $innerPayment = $("." + classNameCluster.budget);

		//other
		var flags = {
			container: true
		}

		//check empty
		if(!$.isEmptyObject(compilation["order"]["INNER_PAYMENT"])){

			//set flag
			flags.container = false;

			//max amount to write off
			if($.isEmptyObject(compilation["order"]["INNER_PAYMENT"]["RANGE"]["MAX"])){
				$innerPayment.data("max", compilation["order"]["INNER_PAYMENT"]["RANGE"]["MAX"]);
			}

		}

		//reset
		if(flags.container === true){
			$innerPayment.removeAttr("checked");
		}

		//hide / show inner payment container
		application.tools.hideByFlag($innerPaymentContainer, flags.container);

	};

	application.commit.basket.minOrderPrice = function(){

		//check empty
		if(typeof compilation["min_order_amount"] !== "undefined"){

			//jquery vars
			var $order = $("." + classNameCluster.order);
			var $fastBuy = $("." + classNameCluster.fastBuy);
			var $goToOrder = $("." + classNameCluster.goToOrder);
			var $minPayment = $("." + classNameCluster.minPayment);

			//hide / show blocks
			application.tools.hideByFlag($order, !compilation["min_order_amount"]);
			application.tools.hideByFlag($fastBuy, !compilation["min_order_amount"]);
			application.tools.hideByFlag($goToOrder, !compilation["min_order_amount"]);
			application.tools.hideByFlag($minPayment, compilation["min_order_amount"]);

		}

	}

	//realtime calculate sum & tools fields
	application.commit.basket.realtime = function(){

		//object map
		var self = {
			fields: {},
			calculate: {}
		}

		//get fields
		self.fields.basket = $("." + classNameCluster.basket);
		self.fields.budget = self.fields.basket.find("." + classNameCluster.propActive + " ." + classNameCluster.budget);
		self.fields.delivery = self.fields.basket.find("." + classNameCluster.propActive + " ." + classNameCluster.delivery);
		self.fields.basketSum = self.fields.basket.find("." + classNameCluster.basketSum);
		self.fields.basketAllSum = self.fields.basket.find("." + classNameCluster.basketAllSum);
		self.fields.discountSum = self.fields.basket.find("." + classNameCluster.discountSum);

		self.fields.deliverySum = self.fields.basket.find("." + classNameCluster.deliverySum);
		self.fields.basketItems = self.fields.basket.find("." + classNameCluster.productItem);
		self.fields.orderSumContainer = self.fields.basket.find("." + classNameCluster.orderSum);
		self.fields.countItemsContainer = self.fields.basket.find("." + classNameCluster.countItems);

		//calculate
		self.calculate.amount = application.calculate.getAmountProducts(self.fields.basketItems);

		self.calculate.basketSum = application.calculate.getBasketSum(self.fields.basketItems);
		self.calculate.basketAllSum = application.calculate.getBasketAllSum(self.fields.basketItems);
		self.calculate.discountSum = application.calculate.getdiscountSum(self.fields.basketItems);

		self.calculate.orderSum = application.calculate.getOrderSum(self.fields.basketItems, self.fields.delivery, self.fields.budget, self.calculate.basketSum);
		self.calculate.deliverySum = application.calculate.getDeliverySum(self.fields.delivery);

		//push values
		self.fields.countItemsContainer.html(self.calculate.amount);
		self.fields.orderSumContainer.html(application.price.format(self.calculate.orderSum, false, false, false, true));
		self.fields.deliverySum.html(application.price.format(self.calculate.deliverySum, false, false, false, true));
		self.fields.basketSum.html(application.price.format(self.calculate.basketSum, false, false, false, true));
		self.fields.basketAllSum.html(application.price.format(self.calculate.basketAllSum, false, false, false, true));
		self.fields.discountSum.html(application.price.format(self.calculate.discountSum, false, false, false, true));

	};

	application.commit.basket.setCompilation = function(jsonData){

		if(typeof jsonData === "object" && !$.isEmptyObject(jsonData)){
			if(typeof jsonData["compilation"] === "object" && !$.isEmptyObject(jsonData)){
				return compilation = jsonData["compilation"];
			}
		}

		return compilation = {};

	};

	//location
	application.commit.location.pushComponent = function(){

		//jquery vars
		var $this = $("." + classNameCluster.propActive + " ." + classNameCluster.locationField);
		var $swichContainer = $this.siblings("." + classNameCluster.locationSwitchContainer);

		//other
		var currentValue = $this.val().toString();
		var sendObject = new FormData();

		//check filling
		if(currentValue == ""){
			return false;
		}

		//push request params
		sendObject.append("actionType", "pushLocation");
		sendObject.append("value", currentValue);
		sendObject.append("siteId", siteId);

		//start loader
		application.tools.launchLoader($this);

		//send data
		application.ajax.sendData(ajaxDir + "/ajax.php", sendObject, "post", "json", dataProcessing, false);

		//proccesing data after request
		function dataProcessing(jsonData){

			//check state
			if(typeof jsonData["component"] == "string" && jsonData["component"] != ""){

				//set flag
				application.flags.location.open = true;

				//push component data
				$swichContainer.html(jsonData["component"]);

			}

			//check errors
			else{
				if(jsonData["error"] === true){
					console.error(jsonData);
				}
			}

			//remove loader
			application.tools.stopLoader($this);

		};

	};

	//calculate all order sum
	application.calculate.getOrderSum = function($basketItems, $deliveryField, $budgetSwitch, totalSum = 0){

		//check delivery
		if(typeof $deliveryField === "object"){
			totalSum += application.calculate.getDeliverySum($deliveryField);
		}

		//check budget
		if(typeof $budgetSwitch === "object"){
			totalSum -= application.calculate.getBudgetMaxSum($budgetSwitch);
		}

		return totalSum >= 0 ? totalSum : 0;

	};

	//calculate products sum
	application.calculate.getBasketSum = function($basketItems){

		//var
		var totalSum = 0;

		//check empty
		if(typeof $basketItems === "object"){

			//get price containers
			var $priceFields = $basketItems.find("." + classNameCluster.productPrice);

			//get quantity containers
			var $quantityFields = $basketItems.find("." + classNameCluster.quantityField);

			//calculate
			$priceFields.each(function(currentIndex){

				//get values
				var currentPrice = parseFloat($(this).data("price"));
				var currentQuantity = parseFloat($quantityFields.eq(currentIndex).val());

				//calculate
				totalSum += (currentPrice * currentQuantity);

			});

		}

		return totalSum;

	};
	//calculate products sum
	application.calculate.getBasketAllSum = function($basketItems){

		//var
		var totalSum = 0;

		//check empty
		if(typeof $basketItems === "object"){

			//get price containers
			var $priceFields = $basketItems.find("." + classNameCluster.productDiscount);

			//get quantity containers
			var $quantityFields = $basketItems.find("." + classNameCluster.quantityField);

			//calculate
			$priceFields.each(function(currentIndex){

				//get values
				var currentPrice = parseFloat($(this).data("price"));
				var currentQuantity = parseFloat($quantityFields.eq(currentIndex).val());

				//calculate
				totalSum += (currentPrice * currentQuantity);

			});

		}

		return totalSum;

	};//calculate products sum

	application.calculate.getdiscountSum = function($basketItems){

		//var
		var totalDSum = 0;

		//check empty
		if(typeof $basketItems === "object"){

			//get price containers
			//var $priceFields = $basketItems.find("." + classNameCluster.productPrcie);
			var $priceFieldsFull = $basketItems.find("." + classNameCluster.productDiscount);

			//get quantity containers
			var $quantityFields = $basketItems.find("." + classNameCluster.quantityField);

			//calculate
			$priceFieldsFull.each(function(currentIndex){
				//get values
				var currentPrice = parseFloat($(this).data("discount"));

				var currentQuantity = parseFloat($quantityFields.eq(currentIndex).val());

				//calculate
				totalDSum -= (currentPrice * currentQuantity);

			});

		}

		return totalDSum;

	};

	//calculate number of units products
	application.calculate.getAmountProducts = function($basketItems, countItems = 0){

		//check empty
		if(typeof $basketItems === "object" && !$.isEmptyObject($basketItems)){

			//get quantity containers
			var $quantityFields = $basketItems.find("." + classNameCluster.quantityField);

			//calculate
			$quantityFields.each(function(){
				countItems += parseFloat($(this).val());
			});

		}

		return countItems;

	};

	//delivery sum
	application.calculate.getDeliverySum = function($deliveryField){

		//check empty
		if(typeof $deliveryField === "object" && !$.isEmptyObject($deliveryField)){
			var getDeliverySum = parseFloat($deliveryField.find("option:selected").data("price"));
			if(!isNaN(getDeliverySum) && getDeliverySum > 0){
				return getDeliverySum;
			}
		}

		return 0;

	};

	application.calculate.getBudgetMaxSum = function($budgetSwitch){

		//check object
		if(typeof $budgetSwitch === "object" && !$.isEmptyObject($budgetSwitch)){

			//check state
			if($budgetSwitch.is(":checked")){

				//get balance
				var accountBalance = parseFloat($budgetSwitch.data("account-balance"));
				var maxSpend = parseFloat($budgetSwitch.data("max"));

				//check max spend
				if(!isNaN(maxSpend) && maxSpend !== undefined){
					if(accountBalance > maxSpend){
						accountBalance = maxSpend;
					}
				}

				//check balance
				if(accountBalance > 0){
					return accountBalance;
				}

			}

		}

		return 0;

	};

	//push functions

	application.push.serializeFields = function($orderFields, sendObject = {}){

		//check transmitted
		if(typeof $orderFields !== "undefined" && !$.isEmptyObject($orderFields)){

			//collect
			$.each($orderFields, function(){

				//get field
				var $this = $(this);

				//field data
				var field = {
					multiple: $this.prop("multiple"),
					tagName: $this.prop("tagName").toLowerCase(),
					checked: $this.prop("checked"),
					type: $this.attr("type"),
					name: $this.attr("name").toLowerCase(),
					id: $this.data("id"),
					val: $this.val()
				};

				if(field.id === "undefined"){
					console.error("undefined id");
				}

				//processing
				if(field.tagName == "input"){
					//text & checkbox (radio)
					if((field.type == "text" || field.type == "date" || field.type == "number" && field.val != "")
						|| ((field.type == "checkbox") || field.type == "radio" && field.checked == true)){
						sendObject.append("properties[" + field.id + "]", field.val);
					}
					//file
					else if(field.type == "file" && field.val != ""){
						//check multi file
						if($this[0].files.length > 1){
							$.each($this[0].files, function(){
								sendObject.append("properties[" + field.id + "][]", this);
							});
						}//one
						else{
							sendObject.append("properties[" + field.id + "]", $this[0].files[0]);
						}
					}
				}

				//textarea
				else if(field.tagName == "textarea" && field.val != ""){
					sendObject.append("properties[" + field.id + "]", field.val);
				}

				//select
				else if(field.tagName == "select"){

					//multiselect
					if(field.multiple == true){

						//get selected options
						var $options = $this.find("option:selected");

						//check count
						if($options.length > 0){

							//push values
							$.each($options, function(){
								sendObject.append("properties[" + field.id + "][]", $(this).val());
							});

						}

					}

					//one
					else{
						sendObject.append("properties[" + field.id + "]", field.val);
					}

				}

			});

		}

		return sendObject;
	};

	application.push.fields = function(sendObject){

		//push request params
		sendObject = application.push.formData(sendObject, application.pull.getExtraServices(), "extraServices");
		sendObject = application.push.formData(sendObject, application.pull.getPersonTypeId(), "personTypeId");
		sendObject = application.push.formData(sendObject, application.pull.getInnerPayment(), "innerPayment");
		sendObject = application.push.formData(sendObject, application.pull.getPaysystemId(), "paysystemId");
		sendObject = application.push.formData(sendObject, application.pull.getDeliveryId(), "deliveryId");
		sendObject = application.push.formData(sendObject, application.pull.getLocationId(), "locationId");
		sendObject = application.push.formData(sendObject, application.pull.getStoreId(), "store");

		return sendObject;

	};

	application.push.params = function(sendObject){

		//append component params
		if(typeof basketParams === "object"){
			sendObject = application.push.formData(sendObject, basketParams, "params");
		}

		return sendObject;

	};

	application.push.orderComment = function(sendObject){

		//get comment field
		var $comment = $("." + classNameCluster.propActive + " ." + classNameCluster.orderComment);

		//check object
		if(typeof $comment === "object" && !$.isEmptyObject($comment)){

			var commentValue = $comment.val();

			//check empty
			if(commentValue != undefined && commentValue != ""){
				sendObject = application.push.formData(sendObject, commentValue.toString(), "comment");
			}

		}

		return sendObject;

	};

	application.push.formData =	function(sendObject, values, name){

		//sendObject(formData)
		//multi
        if(typeof values == "object"){

        	//each values
            for(var index in values){

            	//recursion
                if(typeof values[index] == "object"){
                    application.push.formData(sendObject, values[index], name + "[" + index + "]");
                }

                //one
                else{
                    sendObject.append(name + "[" + index + "]", values[index]);
                }
            }

        }

        //one
        else{
            sendObject.append(name, values);
        }

	    return sendObject;

	}

	//pull functions

	application.pull.getOrderFields = function(){

		//main data
		var $orderFields = $([]);

		//jquery vars
		var $self = $("." + classNameCluster.basket);

		//write
		$orderFields.push.apply($orderFields, $self.find("." + classNameCluster.propActive + ' .' + classNameCluster.propertyItem + ':not(".' + classNameCluster.propHidden + '") input'));
		$orderFields.push.apply($orderFields, $self.find("." + classNameCluster.propActive + ' .' + classNameCluster.propertyItem + ':not(".' + classNameCluster.propHidden + '") select'));
		$orderFields.push.apply($orderFields, $self.find("." + classNameCluster.propActive + ' .' + classNameCluster.propertyItem + ':not(".' + classNameCluster.propHidden + '") textarea'));

		return $orderFields;

	};

	application.pull.getLocationId = function(){

		//jquery vars
		var $locationField = $("." + classNameCluster.propActive + " ." + classNameCluster.locationField);

		//other
		var locationId = parseFloat($locationField.data("location"));

		//check empty
		if(!isNaN(locationId)){
			return locationId;
		}

		return 0;

	};

	application.pull.getStoreId = function(){
		return application.tools.getFieldValue("." + classNameCluster.propActive + " ." + classNameCluster.storesContainer + ':not(".' + classNameCluster.propHidden + '") .' + classNameCluster.storesSwitch);
	};

	application.pull.getInnerPayment = function(){
		return application.tools.getFieldValue("." + classNameCluster.propActive + " ." + classNameCluster.budgetContainer + ':not(".' + classNameCluster.propHidden + '") .' + classNameCluster.budget);
	};

	application.pull.getPaysystemId = function(){
		return application.tools.getFieldValue("." + classNameCluster.propActive + " ." + classNameCluster.paysystem);
	};

	application.pull.getDeliveryId = function(){
		return application.tools.getFieldValue("." + classNameCluster.propActive + " ." + classNameCluster.delivery);
	};

	application.pull.getPersonTypeId = function(){
		return application.tools.getFieldValue("." + classNameCluster.personType);
	};

	application.pull.getExtraServices = function(){

		//jquery vars
		var $serviceFieldsContainers = $("." + classNameCluster.propActive + " ." + classNameCluster.extraServiceItemContainer + ':not(".' + classNameCluster.propHidden + '")');
		var $serviceFields = $serviceFieldsContainers.find("." + classNameCluster.extraService);
		var $deliveryField = $("." + classNameCluster.propActive + " ." + classNameCluster.delivery);

		//other
		var extraServices = [];
		var currentDeliveryId = application.pull.getDeliveryId();
		var deliverylastId = application.pull.getLastDeliveryId();

		//check empty
		if(typeof $serviceFields === "object" && $serviceFields.length > 0){

			//get values
			$.each($serviceFields, function(){

				//get jquery object
				var $item = $(this);

				//get field data
				var field = {
					value: application.tools.getFieldValue($item, false),
					id: $item.data("id")
				};

				//push
				if(field.value != ""){
					extraServices.push(field);
				}

			});

		}

		return extraServices;

	};

	application.pull.getLastDeliveryId = function(){

		//check filling
		if(deliveryId == 0){

			//jquery vars
			var $deliveryField = $("." + classNameCluster.propActive + " ." + classNameCluster.delivery);

			//get default delivery id
			if(typeof $deliveryField === "object" && !$.isEmptyObject($deliveryField)){
				deliveryId = parseInt($deliveryField.data("default"));
			}

		}

		return deliveryId;

	};

	//check functions
	application.check.required = function($orderFields){

		//vars
		var control = true;

		//check transmitted
		if(typeof $orderFields === "undefined" && !$.isEmptyObject($orderFields)){
			var $orderFields = application.pull.getOrderFields();
		}

		//check required
		$.each($orderFields, function(){

			//get jquery object
			var $this = $(this);

			//check data
			if($this.data("required") == "Y"){

				//check filling
				if(!application.check.filling($this)){

					//add error marker
					application.tools.addError($this);

					//check control / scroll
					if(control === true){
						control = application.tools.scrollTo($this);
					}

				}

			}

		});

		return control;

	};

	//check functions
	application.check.filling = function($field){

		//check transmitted
		if(typeof $field !== "undefined" && !$.isEmptyObject($field)){

			//field data
			var field = {
				tagName: $field.prop("tagName").toLowerCase(),
				checked: $field.prop("checked"),
				mail: $field.data("mail"),
				type: $field.attr("type"),
				name: $field.attr("name"),
				val: $field.val(),
			};

			//check email
			if(field.mail != "undefined" && field.mail == "Y"){
				if(!application.tools.validateEmail(field.val)){
					return false;
				}
			}

			//check filling
			if(field.type == "checkbox" && field.checked == true ||
				field.tagName == "textarea" && field.val != "" ||
				field.type == "number" && field.val != "" ||
				field.type == "text" && field.val != "" ||
				field.type == "date" && field.val != "" ||
				field.type == "file" && field.val != ""){
				return true;
			}

		}

		return false;

	}

	//price functions
	application.price.format = function(price, decimals = 2, decPoint = ".", thousands = " ", langFormat = false){

		//check empty
		if(typeof price !== "undefined" && price !== null && price !== ""){

			//check global vars
			if(decPoint === false && siteCurrency["DEC_POINT"] !== undefined){
				decPoint = siteCurrency["DEC_POINT"];
			}

			if(decimals === false && siteCurrency["DECIMALS"] !== undefined){
				decimals = siteCurrency["DECIMALS"];
			}

			if(thousands === false && siteCurrency["THOUSANDS_VARIANT"] !== undefined){
				thousands = application.price.thousandsVariant(siteCurrency["THOUSANDS_VARIANT"]);
			}

			//vars
			var fsplit = price.toString().split(".");
			var whole = fsplit[0].replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1' + thousands);
			var rest = "";

			//check rest
			if(fsplit[1] !== undefined && decimals > 0){
				rest = decPoint + fsplit[1].substr(0, decimals);
			}

			if(langFormat === true){
				return application.price.langFormat(whole + rest);
			}

			else{
				return whole + rest;
			}

		}

		return false

	};

	application.price.langFormat = function(price){
		return siteCurrency["FORMAT_STRING"].toString().replace("#", price);
	}

	application.price.thousandsVariant = function(thousands = "N"){
		return siteCurrency["SEPARATORS"][thousands];
	};


	//ajax functions
	application.ajax.sendData = function(url, data, type, dataType, success, forEngageloader = false){

		$.ajax({
			dataType: dataType,
			processData: false,
			contentType: false,
			cache: false,
			url: url,
			type: type,
			data: data,
			success: success,
			beforeSend: function(jqXHR, settings){
				application.tools.launchLoader(forEngageloader);
			},
			complete: function(jqXHR, textStatus){
				application.tools.stopLoader(forEngageloader);
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.error({httpResponse: jqXHR.responseText, status: jqXHR.statusText});
				console.error(jqXHR, textStatus, errorThrown);
			}
		});

	};

	//tools functions
	application.tools.resetLocation = function(event){

		//jquery vars
		var $locationField = $("." + classNameCluster.propActive + " ." + classNameCluster.locationField);
		var $locationContainer = $locationField.siblings("." + classNameCluster.locationSwitchContainer);

		//other
		var currentValue = $locationField.val().toString();
		var currentId = $locationField.data("location").toString();

		//close dropdown
		application.tools.closeLocationDropdown($locationContainer);

		//check filling
		if(currentValue == "" || currentId == ""){

			//get last values
			var lastId = $locationField.data("last-id");
			var lastValue = $locationField.data("last-value");

			//white last values
			$locationField.data("location", lastId);
			$locationField.val(lastValue);

		}

	};

	application.tools.closeLocationDropdown = function($container = null){

		//check object
		if(typeof $container === "object"){

			//check flag
			if(application.flags.location.open === true){

				//set flag
				application.flags.location.open = false;

				//clear
				$container.html("");

			}

		}

	};

	application.tools.getFieldValue = function(item, onlyChecked = true){

		//check empty
		if(typeof item === "string" && item !== "" || typeof item === "object" && item.length > 0){

			//jquery vars
			var $field = (typeof item === "object" ? item : $(item));

			//check
			if(typeof $field === "object" && $field.length > 0){

				//other
				var field = {
					tagName: $field.prop("tagName").toLowerCase(),
					checked: $field.prop("checked"),
					type: $field.attr("type"),
					val: $field.val()
				}

				//select
				if(field.tagName == "select" && field.val !== undefined && field.val !== null && field.val !== ""){
					return field.val.toString();
				}

				//checkbox / radio
				else if((field.tagName == "input" && typeof field.type !== undefined && (field.type == "checkbox" || field.type == "radio"))){

					if(field.checked == true){
						return field.val.toString();
					}

					else if(onlyChecked === false){
						return "N";
					}

				}

				//text / number
				else if((field.tagName == "input" && typeof field.type !== undefined && (field.type == "text" || field.type == "number"))){
					if(field.val != ""){
						return field.val.toString();
					}
				}

			}

		}

		return 0;

	};

	application.tools.hideByFlag = function($item, flag = false, className = "hidden"){
		if(typeof $item === "object"){
			return (flag === true) ? $item.addClass(className) : $item.removeClass(className);
		}
	};

	application.tools.launchLoader = function($item, loaderClassName = "loading"){
		if(typeof $item === "object"){
			return $item.addClass(loaderClassName);
		}
	};

	application.tools.stopLoader = function($item, loaderClassName = "loading"){
		if(typeof $item === "object"){
			return $item.removeClass(loaderClassName);
		}
	};

	//errors
	application.tools.addError = function($item, errorClassName = classNameCluster.error){
		if(typeof $item === "object"){
			return $item.addClass(errorClassName);
		}
	};

	application.tools.removeError = function($item, errorClassName = classNameCluster.error){
		if(typeof $item === "object"){
			return $item.removeClass(errorClassName);
		}
	};

	application.tools.addParamToUrl = function(params = {}){

		//get current url
		var url = window.location.href;
		var separator = url.indexOf("?") === -1 ? "?" : "&";

		//check empty
		if(typeof params === "object" && !$.isEmptyObject(params)){
			//append params
			$.each(params, function(param, value){
				url = url + separator + param + "=" + value;
				separator = url.indexOf("?") === -1 ? "?" : "&";
			});
		}

		return url;

	};

	application.tools.validateEmail = function(email){

		//vars
		var regularEx = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;

		//check empty
		if(typeof email === "string" && email != ""){
		    return regularEx.test(String(email).toLowerCase());
		}

		return false;
	}

	application.tools.scrollTo = function($item){

		if(typeof $item === "object"){
		    $([document.documentElement, document.body]).animate({
		        scrollTop: ($item.offset().top - 300)
		    }, 600);
		}

		return false;
	};

	application.tools.pushAjaxErrors = function(errorData = {}, errorId = "orderErrors"){

		//check empty
		if(typeof errorData === "object" && !$.isEmptyObject(errorData)){

			//jquery vars
			var $errorWindow = $("." + errorIds[errorId]);
			var $windowMessage = $errorWindow.find("." + classNameCluster.errorWindowMessage);

			//confirm
			if(!$.isEmptyObject($errorWindow) && !$.isEmptyObject($windowMessage)){

				//push errors
				$windowMessage.html(readError(errorData));

				//show
				application.tools.openWindow(errorIds[errorId]);

			}

		}

		//recursive
		function readError(data, concated = ""){

			//vars
			var delimiter = " &bull; ";

			//one
			if(typeof data == "string" && data != ""){
				concated += ((concated != "" ? delimiter : "") + data);
			}

			//multi
			else if(typeof data == "object"){
				$.each(data, function(){
					concated = readError(this, concated);
				});
			}

			return concated;
		}

		return false;
	};

	//openWindow
	application.tools.openWindow = function(errorClass){
		if(typeof errorClass != "undefined" && errorClass != ""){
			var $errorWindow = $("." + errorClass);
			if(typeof $errorWindow === "object" && !$.isEmptyObject($errorWindow)){
				$errorWindow.css({display: "block"});
			}
		}
	};

	application.tools.closeWindow = function($errorWindow){
		if(typeof $errorWindow === "object" && !$.isEmptyObject($errorWindow)){
			$errorWindow.css({display: "none"});
		}
	};

	// ecommerce analytics
	var clickEventPush = function($this){
		var $this = $this.parents(".item");
		var productID = $this.data("product-id");
		var productPrice = $this.data("product-price");
		var productName = $this.data("product-name");
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
				 'name': productName,
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

	$(".item a.name, .item a.picture, .item a.pic").click(function(){ clickEventPush($(this));});

	//jquery binding functions
	//you must edit constants for fast customization

	//click
	$(document).on("click", "." + classNameCluster.errorWindowClose, application.binding.click.closeErrorWindow);
	$(document).on("click", "." + classNameCluster.locationSwitchLink, application.binding.click.locationLink);
	$(document).on("click", "." + classNameCluster.locationSwitchContainer, application.binding.click.stop);
	$(document).on("click", "." + classNameCluster.couponButton, application.binding.click.setCoupon);
	$(document).on("click", "." + classNameCluster.couponButtonCancel, application.binding.click.unsetCoupon);
	$(document).on("click", "." + classNameCluster.remove, application.binding.click.deleteItem);
	$(document).on("click", "." + classNameCluster.minus, application.binding.click.quantityMinus);
	$(document).on("click", "." + classNameCluster.plus, application.binding.click.quantityPlus);
	$(document).on("click", "." + classNameCluster.orderMake, application.binding.click.orderMake);
	$(document).on("click", "." + classNameCluster.orderMove, application.binding.click.orderMove);
	$(document).on("click", "." + classNameCluster.clearAll, application.binding.click.clearAll);

	$(document).on("click", "", application.binding.click.closeout);

	//keyup
	$(document).on("keyup", "." + classNameCluster.locationField, application.binding.keyup.location);

	//keypress
	$(document).on("keypress", "." + classNameCluster.quantityField, application.binding.keypress.quantity);
	$(document).on("keypress", "." + classNameCluster.extraService, application.binding.keypress.quantity);

	//focus
	$(document).on("focus", "." + classNameCluster.locationField, application.binding.focus.location);
	$(document).on("focus", "." + classNameCluster.couponField, application.binding.focus.coupon);

	//focus
	$(document).on("blur", "." + classNameCluster.locationField, application.binding.blur.location);

	//change
	$(document).on("change", "." + classNameCluster.extraService, application.binding.change.extraService);
	$(document).on("change", "." + classNameCluster.quantityField, application.binding.change.quantity);
	$(document).on("change", "." + classNameCluster.paysystem, application.binding.change.paysystem);
	$(document).on("change", "." + classNameCluster.personType, application.binding.change.person);
	$(document).on("change", "." + classNameCluster.delivery, application.binding.change.delivery);
	$(document).on("change", "." + classNameCluster.budget, application.binding.change.budget);

	
		/*application.commit.basket.updateProduct(parseInt($("#basketProductList .item").data('cart-id')), parseInt($("#basketProductList .item .basketQty input").val()));

		 */


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

$(function(){
	if ($("#client_input").length)
	{
		let str_start = '';
		let str_end = '';
		var delFirstEight = {
			onKeyPress: function(val, e, field, options) {
				str_start = val.slice(0, 2);
				str_end = val.slice(2);
				if (str_start == '89' || str_start == '79' || str_start == '+7') {
					val = val.replace(str_start,'+7') + str_end;
					field.val(val);
				}
				/*if (val.replace(/\D/g, '').length===2)
				{
					val = val.replace('8','');
					field.val(val);
				}*/
				field.mask("+7 (999) 999-99-99", options);
			},
			placeholder: "+7 (___) ___-__-__"
		};
		if ($("#client_input").val().length == '11'){
			$("#client_input").mask("+7 (999) 999-99-99", delFirstEight);
		}
		$("#client_input").on("change keyup", function(e){
			if($(this).val().length == 0) {
				$("#client_input").unmask().val("").attr( "placeholder","Телефон или ДК клиента");
			}
			if ($(this).val().length >= 11 && $(this).val().length <= 18) {

				str_start = $(this).val().slice(0, 2);
				str_end = $(this).val().slice(2);

				if (str_start == '89' || str_start == '79' || str_start == '+7')
				{
					if (str_start == '89' || str_start == '79' ) {
						val =  $(this).val();
						val = val.replace(str_start,'+79') + str_end;
						$(this).val(val) ;
					}
					$("#client_input").mask("+7 (999) 999-99-99", delFirstEight);
				}
				else
				{
					$("#client_input").unmask().attr( "placeholder","Телефон или ДК клиента");
				}
			}
			else
			{
				$("#client_input").unmask().attr( "placeholder","Телефон или ДК клиента");
			}
		});

		$("#clientDeActivate").on("click", function(){
			showLoader();
			client = $("#client_input").val("");
			$("#client_input").unmask().attr( "placeholder","Телефон или ДК клиента");

			$.ajax({
				url: '/ajax/lmx/user/',
				data: 'action=salon_client_exit',
				method: 'POST',
				dataType: 'json',
				success:  function(data) {
					if (data.status == 'ok'){
						document.location.reload();
					}

					else if (data.status == 'error')
					{

						console.log(data);
					}

				},
				complete: function(data) {


					//document.location.reload();
				}
			});
			return false;
		});
		$("#client_form").on("submit", function(){
			client = $("#client_input");

			$error = 0;
			// phone
			if (client.val().length == 18)
			{
				showLoader();

				$client_num = client.val().replace(/\D/g, '');

				if ($client_num.length != 11)
				{
					$error = 1;
					hideLoader();
				}
			}
			// card
			else if (client.val().length >= 5 && client.val().length <= 13 )
			{
				$client_num = client.val().replace(/![0-9a-zA-Z]/g, '');

				/*if ($client_num.length != 13)
				{
					$error = 1;
					hideLoader();
				}*/
			}
			if ($error == 0)
			{
				$.ajax({
					url: '/ajax/lmx/user/',
					data: 'action=salon_client&code='+$client_num,
					method: 'POST',
					dataType: 'json',
					success:  function(data) {
						if (data.status == 'ok'){

							$("#client_auth_status").html("успешно");
							document.location.reload();
						}

						else if (data.status == 'error')
						{
							$("#client_auth_status").html("не найден");hideLoader();
						}

					},
					complete: function(data) {
						//hideLoader();

						//document.location.reload();
					}
				});
			}
			else {

			}
			return false;
		});


	}
});