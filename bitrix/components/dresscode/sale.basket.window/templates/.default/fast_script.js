$(function(){

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
		var productListPos = 1;
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

	$("#appBasketContainer a.moreLink").click(function(){ clickEventPush($(this));});


	//blur
	var $foundation = $("#foundation");

	//vars
	var timeoutId = 0;

	//document
	var $document = $(document);

	//other constants
	const timeoutDelay = 400;

	//application object map
	var application = {
		binding: {
			keypress: {},
			change: {},
			click: {},
			keyup: {},
		},
		tools: {},
		commit:{},
		ajax: {},
	};

	//binding functions

	//click events
	application.binding.click.deleteItem = function(event){

		//jquery vars
		var $this = $(this);

		//other
		var basketItemId = $this.data("id");

		//launch ajax request
		application.commit.deleteProduct(basketItemId);

		//block actions
		return event.preventDefault();

	};

	application.binding.click.closeWindow = function(event){

		//jquery vars
		$self = $("#appBasket");

		//jquery binding functions
		$.each(events, function(eventId, eventData){
			$.each(eventData, function(eventElement, eventFunction){
				$document.off(eventId, eventElement, eventFunction);
			});
		});

		//remove
		$self.remove();

		//remove blur
		$foundation.removeClass("blurred");

		//block actions
		return event.preventDefault();

	}

	application.binding.click.quantityMinus = function(event){

		//jquery vars
		var $this = $(this);
		var $quantityContainer = $this.parents(".qtyBlock");
		var $quantityField = $quantityContainer.find(".qty");

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

			$product_id = $this.data('product-id');
			application.ajax.sendData( "/ajax.php?act=getItemData&product_id="+$product_id, '', "get", "json", dataProcessing, false);

			//proccesing data after request
			function dataProcessing(jsonData){
				window.dataLayer = window.dataLayer || [];
				dataLayer.push({
				'ecommerce': {
				  'currencyCode': 'RUB',
				  'remove': {
					'products': [{
					 'name': jsonData["DATA"]['NAME'],
					  'id': jsonData["DATA"]['ID'],
					  'price': parseInt(jsonData["DATA"]['PRICE']),
					  'brand': jsonData["DATA"]['BRAND'],
					  'category': jsonData["DATA"]['CATEGORY'],
					  'variant': jsonData["DATA"]['CML2_ARTICLE'],
					   'quantity': 1
					 }]
				  }
				},
				'event': 'gtm-ee-event',
				'gtm-ee-event-category': 'Enhanced Ecommerce',
				'gtm-ee-event-action': 'Removing a Product from a Shopping Cart',
				'gtm-ee-event-non-interaction': 'False',
				});
			}

		}

		//block actions
		return event.preventDefault();

	};

	application.binding.click.quantityPlus = function(event){

		//jquery vars
		var $this = $(this);
		var $quantityContainer = $this.parents(".qtyBlock");
		var $quantityField = $quantityContainer.find(".qty");

		//other
		var measureRatio = parseFloat($quantityField.data("ratio"));
		var currentQuantity = parseFloat($quantityField.val());

		//pre calculate quantity
		var preQuantity = currentQuantity + measureRatio;

		//set value
		$quantityField.val(+(preQuantity).toFixed(5));
		$quantityField.trigger("change");

		$product_id = $this.data('product-id');
		application.ajax.sendData( "/ajax.php?act=getItemData&product_id="+$product_id, '', "get", "json", dataProcessing, false);

		//proccesing data after request
		function dataProcessing(jsonData){
			window.dataLayer = window.dataLayer || [];
			dataLayer.push({
			'ecommerce': {
			  'currencyCode': 'RUB',
			  'add': {
				'products': [{
				 'name': jsonData["DATA"]['NAME'],
				  'id': jsonData["DATA"]['ID'],
				  'price': parseInt(jsonData["DATA"]['PRICE']),
				  'brand': jsonData["DATA"]['BRAND'],
				  'category': jsonData["DATA"]['CATEGORY'],
				  'variant': jsonData["DATA"]['CML2_ARTICLE'],
				   'quantity': 1
				 }]
			  }
			},
			'event': 'gtm-ee-event',
			'gtm-ee-event-category': 'Enhanced Ecommerce',
			'gtm-ee-event-action': 'Adding a Product to a Shopping Cart',
			'gtm-ee-event-non-interaction': 'False',
			});
		}

		//block actions
		return event.preventDefault();

	};

	//change events
	application.binding.change.quantity = function(event){

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

			//get near quantity
			var currentQuantity = Math.ceil(currentQuantity / measureRatio) * measureRatio;

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

		//realtime calc
		timeoutId = setTimeout(function(){
			application.commit.updateProduct(basketItemId, currentQuantity);
		}, timeoutDelay);

	};

	//commit functions
	application.commit.updateProduct = function(basketId, quantity){

		if(typeof basketId === "number" && typeof quantity === "number"){

			//jquery vars
			var $self = $("#appBasket");

			//other
			var sendObject = new FormData();

			//push request params
			sendObject.append("actionType", "updateQuantity");
			sendObject.append("basketId", basketId);
			sendObject.append("quantity", quantity);

			//hide measures
			sendObject.append("hide-measures", $self.data("hide-measures"));

			//push siteId (set in template)
			sendObject.append("siteId", appBasketSiteId);

			//start loader
			application.tools.launchLoader($self);

			//send data (appBasketAjaxDir set in template)
			application.ajax.sendData(appBasketAjaxDir + "/ajax.php", sendObject, "post", "json", dataProcessing, false);

			//proccesing data after request
			function dataProcessing(jsonData){

				//check state
				if(jsonData["status"] === true){
					//commit result data
					application.commit.proccesing(jsonData);
				}

				//check errors
				else{
					if(jsonData["error"] === true){
						console.error(jsonData);
					}
				}

				//remove loader
				application.tools.stopLoader($self);

			};

		}

		else{
			console.error("check transmitted data");
		}

	}

	//commit functions
	application.commit.deleteProduct = function(basketId){

		if(typeof basketId === "number"){

			//jquery vars
			var $self = $("#appBasket");

			//other
			var sendObject = new FormData();

			//push request params
			sendObject.append("actionType", "removeItem");
			sendObject.append("basketId", basketId);

			//push siteId (set in template)
			sendObject.append("siteId", appBasketSiteId);

			//start loader
			application.tools.launchLoader($self);

			//send data (appBasketAjaxDir set in template)
			application.ajax.sendData(appBasketAjaxDir + "/ajax.php", sendObject, "post", "json", dataProcessing, false);

			//proccesing data after request
			function dataProcessing(jsonData){

				//check state
				if(jsonData["status"] === true){

					//update statate
					//get addCart button for current item
					var $savedItems = $(".bwOpened").removeClass("added").attr("href", "#");

					//close window
					$("#appBasket .closeWindow").trigger("click");

					//default button label
					if(typeof lastAddCartText != "undefined" && lastAddCartText != ""){
						$savedItems.html(lastAddCartText);
					}

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

					//reload other baskets
					cartReload();

				}

				//check errors
				else{
					if(jsonData["error"] === true){
						console.error(jsonData);
					}
				}

				//remove loader
				application.tools.stopLoader($self);

			};

		}

		else{
			console.error("check transmitted data");
		}

	}

	application.commit.proccesing = function(jsonData){

		//check transsmited data
		if(typeof jsonData["compilation"] != "undefined" && typeof jsonData["compilation"]["item"] != "undefined"){

			//jquery vars
			var $self = $("#appBasket");
			var $sumContainer = $self.find(".allSum");
			var $priceContainer = $self.find(".price");

			//other
			var itemData = jsonData["compilation"]["item"];

			//clear
			$sumContainer.html("");
			$priceContainer.html("");

			//push price
			$priceContainer.append($("<span/>", {class: "priceContainer"}).html(itemData["PRICE_FORMATED"]));

			//push measure
			if(typeof jsonData["compilation"]["measures"] != "undefined" && !$.isEmptyObject(jsonData["compilation"]["measures"])){
				if(typeof itemData["CATALOG_MEASURE"] != "undefined" && itemData["CATALOG_MEASURE"] != "" && itemData["CATALOG_MEASURE"] != null){
					$priceContainer.append($("<span/>", {class: "measure"}).html(" / " + jsonData["compilation"]["measures"][itemData["CATALOG_MEASURE"]]["SYMBOL_RUS"]));
				}
			}

			//push sum
			$sumContainer.append(itemData["SUM_FORMATED"]);

			//push discount
			if(parseFloat(itemData["DISCOUNT"]) > 0){

				//discount price
				$priceContainer.append(" ").append($("<s/>", {class: "discount"}).html(itemData["BASE_PRICE_FORMATED"]));

				//discount total sum
				$sumContainer.append($("<s/>", {class: "discount"}).html(itemData["BASE_SUM_FORMATED"]));

			}

			//flush other baskets
			cartReload();

		}

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
	application.tools.addError = function($item, errorClassName = "error"){
		if(typeof $item === "object"){
			return $item.addClass(errorClassName);
		}
	};

	application.tools.removeError = function($item, errorClassName = "error"){
		if(typeof $item === "object"){
			return $item.removeClass(errorClassName);
		}
	};

	//event object
	var events = {
		click: {
			"#appBasket .closeWindow": application.binding.click.closeWindow,
			"#appBasket .delete": application.binding.click.deleteItem,
			"#appBasket .minus": application.binding.click.quantityMinus,
			"#appBasket .plus": application.binding.click.quantityPlus
		},
		keypress: {
			"#appBasket .qty": application.binding.keypress.quantity
		},
		change: {
			"#appBasket .qty": application.binding.change.quantity
		}
	};

	//jquery binding functions
	$.each(events, function(eventId, eventData){
		$.each(eventData, function(eventElement, eventFunction){
			$document.on(eventId, eventElement, eventFunction);
		});
	});

});
