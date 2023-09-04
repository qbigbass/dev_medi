$(function () {

    var skuLoading = false;

    var elementSelectSku = function (event, ignoreLoading) {

        if (skuLoading == true && typeof ignoreLoading == "undefined") {
            return false;
        }

        var _params = "";
        var _props = "";
        var _highload = "";
        var _requestStoresParams = "";

        var $_this = $(this);

        if (!$_this.parent().hasClass("selected")) {

            var $_mProduct = $_this.parents(".elementSku");
            var $_mProductContainer = $_this.parents(".item");
            var $_parentProp = $_this.parents(".elementSkuProperty");
            var $_propList = $_mProduct.find(".elementSkuProperty");
            var $_clickedProp = $_this.parents(".elementSkuPropertyValue");
            var $changeFastBack = $_mProduct.find(".fastBack").removeClass("disabled");
            var $changeFastOrder = $_mProduct.find(".fastOrder");

            var _level = $_parentProp.data("level");

            $_this.parents(".elementSkuPropertyList").find("li").removeClass("selected");
            $_clickedProp.addClass("selected loading");
            //$("#catalogElement").addClass("loading");
            showLoader();

            skuLoading = true; //block

            $_propList.each(function (i, prop) {

                var $_nextProp = $(prop);
                var $_nextPropList = $_nextProp.find("li");

                var propName = $_nextProp.data("name");
                var _used = false;

                if ($_nextProp.data("highload") == "Y") {
                    _highload = _highload + propName + ";"
                }

                $_nextPropList.each(function (io, obj) {
                    var $_currentObj = $(obj);
                    _props = _props + propName + ":" + $_currentObj.data("value") + ";";
                    if ($_currentObj.hasClass("selected")) {
                        _params = _params + propName + ":" + $_currentObj.data("value") + ";";
                        return _used = true;
                    }
                });

                if (!_used) {
                    _params = _params + propName + ":-forse;";
                }

            });

            if (typeof elementStoresComponentParams != "undefined") {
                _requestStoresParams = JSON.stringify(elementStoresComponentParams);
            }

            var sendObject = {
                "act": "selectSku",
                "props": _props,
                "params": _params,
                "level": _level,
                "iblock_id": $_mProduct.data("iblock-id"),
                "prop_id": $_mProduct.data("prop-id"),
                "product_id": $_mProduct.data("product-id"),
                "highload": _highload,
                "price-code": $_mProductContainer.data("price-code"),
                "stores_params": _requestStoresParams,
                "deactivated": $_mProduct.data("deactivated"),
                "msite_id": msite_id,
            }

            $.ajax({
                url: elementAjaxPath,
                type: "POST",
                async: true,
                cache: false,
                data: sendObject,
                dataType: "json",
                success: skuProcessingResult,
                complete: function () {
                    $_clickedProp.removeClass("loading");
                    $_mProduct.removeClass("loading");
                    hideLoader();
                    skuLoading = false;
                    // open tabs
                    $(".tab-link").click(function () {
                        if (!$(this).hasClass("active")) {
                            var par = $(this).parents(".tabs-wrap");
                            var index = $(this).index();

                            $(this).addClass('active').siblings().removeClass("active");
                            $('.tab-content:eq(' + index + ')', par).addClass("active").siblings().removeClass("active");
                        }
                    });
                    // open tabs
                    $(".subtab-link").click(function () {
                        if (!$(this).hasClass("active")) {
                            var par = $(this).parents(".subtabs-wrap");
                            var index = $(this).index();

                            $(this).addClass('active').siblings().removeClass("active");
                            $('.subtab-content:eq(' + index + ')', par).addClass("active").siblings().removeClass("active");
                        }
                    });

                    $(".available_link").on("click", function () {
                        $(".tab-link").removeClass("active");
                        $(".tab-content").removeClass("active");
                        $(".tab-link.salon").addClass("active");
                        $(".tab-content.stores").addClass("active");

                        $(".salon").addClass("active");
                        $(".tab_content.stores ").addClass("active").removeClass("hide");

                        $("#acor").attr('style', '');

                        $("#chacor5").click();
                        $("#chacor5").attr("checked", "checked");
                        //$("body").scrollTo(".tabs-links", 500);
                        $("body").scrollTo('#acor');
                        $(".acor-container.active #acor").css("height", "auto");
                        return false;

                    });

                }
            });

            function skuProcessingResult(jsonData) {

                $_propList.each(function (pI, pV) {
                    var $_sf = $(pV);
                    $_sf.data("level") > _level && $_sf.find(".elementSkuPropertyValue").removeClass("selected").addClass("disabled");
                });

                $.each(jsonData[1]["PROPERTIES"], function (name, val) {
                    var $_gPropList = $_propList.filter(function () {
                        return ($(this).data("name") == name);
                    });
                    var $_gPropListValues = $_gPropList.find(".elementSkuPropertyValue");
                    $_gPropListValues.each(function (il, element) {
                        var $nextElement = $(element);
                        $.each(val, function (pVal, _selected) {
                            if (pVal == $nextElement.data("value") && _selected != "D") {
                                (_selected == "Y") ? $nextElement.addClass("selected").removeClass("disabled").trigger("click") : $nextElement.removeClass("disabled");
                                return false;
                            }
                        });
                    });
                });

                // salon AVAILABLE show reserve button
                if (jsonData[0]["PRODUCT"]['SALON_AVAILABLE'] > 0) {
                    /*if (jsonData[0]["PRODUCT"]['CATALOG_AVAILABLE'] != 'Y') {*/
                    $(".greyBigButton.reserve").show();
                    /*}*/
                    //$(".tab[data-id='stores']").show();
                    //$(".tab-content.stores").show();
                } else {
                    $(".greyBigButton.reserve").hide();
                    //$(".tab[data-id='stores']").hide();
                    //$(".tab-content.stores").hide();
                }

                // salon AVAILABLE show reserve button
                if (jsonData[0]["PRODUCT"]['SALON_COUNT'] > 0 && jsonData[0]["PRODUCT"]['DISPLAY_BUTTONS']['RESERV_BUTTON']) {
                    $(".salons_avail_later").removeClass("hidden");
                    $(".availability_block").show();
                    $(".availability_block").html(jsonData[0]["PRODUCT"]['SALON_COUNT_STR']);

                    $(".salons_avail_now").html(jsonData[0]["PRODUCT"]['SALONS_AVAIL_NOW']);
                    $(".salons_avail_later").show();
                    $(".available_link ").removeClass("hidden");
                    $(".tab-link.salon").show().html("Наличие в салонах (" + jsonData[0]["PRODUCT"]['SALON_COUNT'] + ")");

                    $(".tab-content.stores ").removeClass('hide');
                    //$("#acor").show();
                } else {
                    $(".salons_avail_later").removeClass("hidden");
                    $(".availability_block").hide();
                    $(".availability_block").html(jsonData[0]["PRODUCT"]['SALON_COUNT_STR']);
                    $(".salons_avail_now").html("");
                    $(".salons_avail_later").hide();

                    if ($(".tab-link.salon").hasClass("active")) {
                        $(".tab-link.desc").addClass("active");
                        $(".tab-content.desc ").addClass("active");
                    }
                    $(".tab-link.salon").removeClass("active").hide();
                    $(".tab-content.stores ").removeClass("active").addClass('hide');

                    $(".available_link ").addClass("hidden");
                }
                var _tmr = _tmr || [];
                _tmr.push({
                    type: "itemView",
                    productid: jsonData[0]["PRODUCT"]["ID"],
                    pagetype: "product",
                    totalvalue: jsonData[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"],
                    list: "1"
                });
                _tmr.push({
                    type: "itemView",
                    productid: jsonData[0]["PRODUCT"]["ID"],
                    pagetype: "product",
                    totalvalue: jsonData[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"],
                    list: "102"
                });


                // pictures
                var countImages = 0;

                if (jsonData[0]["PRODUCT"]["IMAGES"]) {

                    for (var i in jsonData[0]["PRODUCT"]["IMAGES"]) {
                        countImages = i;
                    }

                    // big slider vars
                    var $pictureSlider = $("#pictureContainer .pictureSlider.detail_product").empty();
                    $pictureSlider.removeClass();
                    $pictureSlider.addClass('pictureSlider');
                    $pictureSlider.addClass('detail_product');
                    if (countImages > 0) {
                        $pictureSlider.addClass('more-images');
                    } else {
                        $pictureSlider.addClass('one-image');
                    }

                    // small pictures slider
                    var $moreImagesCarousel = $("#moreImagesCarousel").removeClass("hide");
                    var $moreImagesCarouselSlideBox = $moreImagesCarousel.find(".slideBox");
                    $moreImagesCarouselSlideBox.find(".item").remove();

                    $.each(jsonData[0]["PRODUCT"]["IMAGES"], function (i, nextElement) {

                        var $sliderImage = $("<img />", {src: nextElement["MEDIUM_IMAGE"]["SRC"]});

                        //big slider
                        $pictureSlider.append(
                            $("<div />", {class: "item"}).append(
                                $("<a/>", {
                                    class: "zoom",
                                    href: nextElement["LARGE_IMAGE"]["SRC"]
                                }).data("large-picture", nextElement["LARGE_IMAGE"]["SRC"]).data("small-picture", nextElement["SMALL_IMAGE"]["SRC"]).append(
                                    $sliderImage
                                )
                            )
                        )

                        if (countImages > 0) {
                            //small slider
                            $moreImagesCarouselSlideBox.append(
                                $("<div />", {class: "item", "data-pic-index": i}).append(
                                    $("<a/>", {
                                        class: "zoom",
                                        href: nextElement["LARGE_IMAGE"]["SRC"]
                                    }).data("large-picture", nextElement["LARGE_IMAGE"]["SRC"]).append(
                                        $("<img />", {src: nextElement["SMALL_IMAGE"]["SRC"]})
                                    )
                                )
                            );
                        } else {
                            $moreImagesCarousel.addClass("hide");
                        }
                    });

                    if ($pictureSlider.hasClass('detail_product')) {
                        var defaultOptions = {
                            dots: false,
                            arrows:false,
                            slidesToShow:1,
                            adaptiveHeight:true,
                            autoplay:true,
                            autoplaySpeed:5000
                        }
                        $pictureSlider.slick(
                            defaultOptions
                        );
                    }
                    $pictureSlider.on('afterChange', function(event, slick, currentSlide, nextSlide){
                        let currentIndexBigImg = currentSlide;
                        $('#moreImagesCarousel .slideBox .item').each(function(){
                            $(this).removeClass('selected');
                        });
                        $('#moreImagesCarousel .slideBox .item[data-pic-index='+currentIndexBigImg+']').addClass('selected');
                    });


                    //addCart button reload
                    changeAddCartButton(basketProductsNow);
                    //subscribe button reload
                    //subscribeOnline();

                    //apps
                    //startPictureElementSlider();
                    startMorePicturesElementCarousel();
                    createZoomer();

                }

                $_mProduct.find(".changeID").data("id", jsonData[0]["PRODUCT"]["ID"]).attr("data-id", jsonData[0]["PRODUCT"]["ID"]);
                $_mProduct.find(".changePicture").html($("<img/>").attr("src", jsonData[0]["PRODUCT"]["IMAGES"][0]["MEDIUM_IMAGE"]["SRC"]));
                $_mProduct.find(".changePropertiesNoGroup").html(jsonData[0]["PRODUCT"]["RESULT_PROPERTIES_NO_GROUP"]);
                $_mProduct.find(".changePropertiesGroup").html(jsonData[0]["PRODUCT"]["RESULT_PROPERTIES_GROUP"]);

                var $changeCart = $_mProduct.find(".changeCart").removeClass("subscribe unSubscribe");

                var _tmr = _tmr || [];
                _tmr.push({
                    type: "itemView",
                    productid: jsonData[0]["PRODUCT"]["ID"],
                    pagetype: "product",
                    totalvalue: jsonData[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"],
                    list: "<?=$feed_id?>"
                });


                $changeCart.find("img").remove();
                if (jsonData[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"]) {

                    $changeFastOrder.removeClass("disabled").show();
                    $changeCart.removeClass("added").removeClass("disabled").removeClass("requestPrice")
                        .html(LANG["ADD_BASKET_DEFAULT_LABEL"])
                        .prepend($("<img />").attr({src: TEMPLATE_PATH + "/images/incart.png", class: "icon"}))
                        .attr("href", "#").attr("id", "GTM_add_cart_card");
                } else {
                    //$changeFastBack.addClass("disabled");
                    $changeFastOrder.addClass("disabled").hide();
                    $changeCart.removeClass("added").addClass("disabled").addClass("requestPrice")
                        .html(LANG["REQUEST_PRICE_BUTTON_LABEL"])
                        .prepend($("<img />").attr({src: TEMPLATE_PATH + "/images/request.png", class: "icon"}))
                        .attr("href", "#");
                }

                //AVAILABLE
                var $changeAvailable = $_mProduct.find(".eChangeAvailable");

                $changeAvailable.removeClass("getStoresWindow");
                $changeAvailable.removeClass("outOfStock");
                $changeAvailable.removeClass("onOrder");
                $changeAvailable.removeClass("inStock");
                $changeAvailable.removeAttr("href");
                if (jsonData[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"]) {
                    var $productChangePrice = $_mProduct.find(".changePrice").empty().removeClass("getPricesWindow").removeClass("requestPrice").removeAttr("href").data("id", jsonData[0]["PRODUCT"]["ID"]);
                    var $productPriceVal = $("<span/>", {class: "priceVal"}).html(jsonData[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"]);
                    var $productPriceContainer = $("<span/>", {class: "priceContainer"}).html($productPriceVal);
                } else {
                    var $productChangePrice = $_mProduct.find(".changePrice").empty().removeClass("getPricesWindow").removeAttr("href").addClass("requestPrice").addClass("disabled").data("id", jsonData[0]["PRODUCT"]["ID"]);
                    var $productPriceVal = $("<span/>", {class: "priceVal"}).html(LANG["REQUEST_PRICE_LABEL"]);
                    var $productPriceContainer = $("<span/>", {class: "priceContainer"}).html($productPriceVal);

                    jsonData[0]["PRODUCT"]["CAN_BUY"] = "N";
                }

                //remove bonus container
                $productChangePrice.find(".purchaseBonus").remove();

                if (jsonData[0]["PRODUCT"]["RESULT_PROPERTIES"]) {
                    $product.find(".changeProperties").html(jsonData[0]["PRODUCT"]["RESULT_PROPERTIES"]);
                }

                if (jsonData[0]["PRODUCT"]["COUNT_PRICES"] > 1) {
                    $productPriceContainer.prepend($("<span/>", {class: "priceIcon"}));
                    $productChangePrice.addClass("getPricesWindow").attr("href", "#");
                }

                //write price container with price value
                $productChangePrice.html($productPriceContainer);
                if (jsonData[0]["PRODUCT"]["CATALOG_QUANTITY"] > 0) {
                    if (jsonData[0]["PRODUCT"]["STORES_COUNT"] > 0) {
                        $changeAvailable.html($("<span/>").html(LANG["CATALOG_AVAILABLE"])).addClass("inStock").attr("href", "#").addClass("getStoresWindow").data("id", jsonData[0]["PRODUCT"]["ID"]);
                        $changeAvailable.prepend(
                            $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/inStock.png")
                        );
                    } else {
                        $changeAvailable.html(LANG["CATALOG_AVAILABLE"]).addClass("inStock");
                        $changeAvailable.prepend(
                            $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/inStock.png")
                        );
                    }

                    if (jsonData[0]["PRODUCT"]['DISPLAY_BUTTONS']['CART_BUTTON'] == true) {
                        $changeCart.removeClass("fastBack").removeClass("disabled").removeClass("label").addClass("addCart").show();
                        $changeFastOrder.removeClass("disabled").show();
                        $(".qtyBlock").show();
                        $changeCart.html("В корзину").prepend(
                            $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/basket.svg"))
                            .attr("href", "#").addClass("addCart");

                        $(".greyBigButton.reserve").hide();

                    } else {
                        $(".qtyBlock").hide();
                        $changeFastOrder.addClass("disabled").hide();


                        if (jsonData[0]["PRODUCT"]["SALON_COUNT"] == "0" && jsonData[0]["PRODUCT"]["SHOES_CAT"] != "Y" && jsonData[0]["PRODUCT"]["MKT_CAT"] != "Y") {
                            $changeCart.html("Под заказ").prepend(
                                $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/incart.png"))
                                .attr("href", "#").removeClass("addCart").addClass("fastBack label").removeClass("disabled");
                            $changeCart.addClass("disabled").hide();
                        } else {
                            //$changeCart.hide();
                            $changeCart.html("нет в наличии").attr("href", "#").removeClass("addCart").addClass("label disabled").removeClass("fastBack");
                        }
                    }

                } else {
                    if (jsonData[0]["PRODUCT"]["CAN_BUY"] != "Y") {

                        $changeAvailable.html(LANG["CATALOG_NO_AVAILABLE"]).addClass("outOfStock");
                        $changeFastBack.addClass("disabled").hide();
                        $changeFastOrder.addClass("disabled").hide();

                        if (jsonData[0]["PRODUCT"]["CATALOG_SUBSCRIBE"] == "Y" && jsonData[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"]) {
                            $changeCart.html(LANG["ADD_SUBSCRIBE_LABEL"])
                                .prepend($("<img />").attr({
                                    src: TEMPLATE_PATH + "/images/subscribe.png",
                                    class: "icon"
                                }))
                                .attr("href", "#").addClass("subscribe");


                            $(".qtyBlock").hide();
                        } else {
                            //$changeCart.addClass("disabled").hide();
                            $(".qtyBlock").hide();
                            $changeFastOrder.addClass("disabled").hide();
                            $changeFastBack.addClass("disabled").hide();

                            if (jsonData[0]["PRODUCT"]["SALON_COUNT"] == "0" && jsonData[0]["PRODUCT"]["SHOES_CAT"] != "Y" && jsonData[0]["PRODUCT"]["MKT_CAT"] != "Y") {

                                $changeCart.html("Под заказ").prepend(
                                    $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/incart.png"))
                                    .attr("href", "#").removeClass("addCart").addClass("fastBack label").removeClass("disabled").hide();
                                // disable fast
                                $changeCart.html("нет в наличии").attr("href", "#").removeClass("addCart").addClass("label disabled").removeClass("fastBack");
                            } else {
                                if (jsonData[0]["PRODUCT"]["SALON_COUNT"] == "0") {
                                    $changeCart.html("нет в наличии").attr("href", "#").removeClass("addCart").addClass("label disabled").removeClass("fastBack");
                                } else {

                                    $changeCart.html("").attr("href", "#").removeClass("addCart").addClass("label disabled");
                                }
                            }

                        }

                        $changeAvailable.prepend(
                            $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/outOfStock.png")
                        );

                    } else {
                        $changeAvailable.html(LANG["CATALOG_ON_ORDER"]).addClass("onOrder");

                        $changeFastOrder.addClass("disabled").hide();
                        $changeAvailable.prepend(
                            $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/onOrder.png")
                        );
                    }
                }

                //QTY BOX

                //get qty box ()
                var $qtyBox = $_mProduct.find(".qtyBlock .qty");

                //write values
                $qtyBox.val(jsonData[0]["PRODUCT"]["BASKET_STEP"]).data("max-quantity", jsonData[0]["PRODUCT"]["CATALOG_QUANTITY"]).data("step", jsonData[0]["PRODUCT"]["BASKET_STEP"]).removeClass("error");
                $qtyBox.removeAttr("data-extended-price").removeData("extended-price");

                $changeCart.data("quantity", jsonData[0]["PRODUCT"]["BASKET_STEP"]);

                if (typeof jsonData[0]["PRODUCT"]["PRICE"]["EXTENDED_PRICES_JSON_DATA"] != "undefined") {
                    if (jsonData[0]["PRODUCT"]["PRICE"]["EXTENDED_PRICES_JSON_DATA"] != "") {
                        $qtyBox.data("extended-price", jsonData[0]["PRODUCT"]["PRICE"]["EXTENDED_PRICES_JSON_DATA"]);
                    }
                }

                if (jsonData[0]["PRODUCT"]["CATALOG_QUANTITY_TRACE"] == "Y" && jsonData[0]["PRODUCT"]["CATALOG_CAN_BUY_ZERO"] == "N") {
                    $qtyBox.data("enable-trace", "Y");
                } else {
                    $qtyBox.data("enable-trace", "N");
                }

                //stores component

                //storesTab
                var $storesTab = $_mProduct.find('.changeTabs .tab[data-id="stores"]').removeClass("active disabled");

                if (typeof jsonData[0]["PRODUCT"]["STORES_COMPONENT"] != "undefined" && jsonData[0]["PRODUCT"]["STORES_COMPONENT"] != "") {
                    if ($("div").is("#storesContainer")) {
                        //vars
                        var $storesContainer = $("#storesContainer");
                        //insert component html
                        $storesContainer.html(jsonData[0]["PRODUCT"]["STORES_COMPONENT"]);
                        //calc element tabs
                        if (typeof startElementTabs === "function") {
                            startElementTabs();
                        }

                    }
                } else {

                    //clear
                    $("#storesContainer").html("");
                    $storesTab.addClass("disabled");

                    //calc element tabs
                    if (typeof startElementTabs === "function") {
                        startElementTabs();
                    }

                }

                //short description

                if (jsonData[0]["PRODUCT"]["PREVIEW_TEXT"]) {
                    $_mProduct.find(".changeShortDescription").html(jsonData[0]["PRODUCT"]["PREVIEW_TEXT"]);
                } else {
                    if ($_mProduct.find(".changeShortDescription").data("first-value")) {
                        $_mProduct.find(".changeShortDescription").html($_mProduct.find(".changeShortDescription").data("first-value"));
                    }
                }

                // full description

                if (jsonData[0]["PRODUCT"]["DETAIL_TEXT"]) {
                    $_mProduct.find(".changeDescription").html(jsonData[0]["PRODUCT"]["DETAIL_TEXT"]);
                } else {
                    if ($_mProduct.find(".changeDescription").data("first-value")) {
                        $_mProduct.find(".changeDescription").html($_mProduct.find(".changeDescription").data("first-value"));
                    }
                }

                //article

                if (typeof (jsonData[0]["PRODUCT"]["PROPERTIES"]["CML2_ARTICLE"]) != "undefined") {
                    if (typeof (jsonData[0]["PRODUCT"]["PROPERTIES"]["CML2_ARTICLE"]["VALUE"]) != "undefined" && jsonData[0]["PRODUCT"]["PROPERTIES"]["CML2_ARTICLE"]["VALUE"] != "") {
                        $_mProduct.find(".changeArticle").html(jsonData[0]["PRODUCT"]["PROPERTIES"]["CML2_ARTICLE"]["VALUE"]).parents(".article").removeClass("hidden");
                    } else {
                        if ($_mProduct.find(".changeArticle").data("first-value")) {
                            $_mProduct.find(".changeArticle").html($_mProduct.find(".changeArticle").data("first-value"));
                        } else {
                            $_mProduct.find(".changeArticle").parents(".article").addClass("hidden");
                        }
                    }
                }


                if (jsonData[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"]) {
                    if ($_mProduct.data("hide-measure") != "Y" && jsonData[0]["PRODUCT"]["MEASURE"] != undefined && jsonData[0]["PRODUCT"]["MEASURE"]["SYMBOL_RUS"] != "") {
                        $productChangePrice.append(
                            $("<span/>").addClass("measure").html(
                                " / " + jsonData[0]["PRODUCT"]["MEASURE"]["SYMBOL_RUS"] + " "
                            )
                        );
                    }
                }

                if (jsonData[0]["PRODUCT"]["PRICE"]["RESULT_PRICE"]["DISCOUNT"] > 0) {

                    $productPriceBlock = $productChangePrice.find(".priceContainer")
                    //$("<span/>", {class: "priceBlock"});
                    $productPriceBlock.append(
                        $("<span/>").addClass("oldPriceLabel").html(
                            $("<s/>").addClass("discount").html(
                                jsonData[0]["PRODUCT"]["PRICE"]["RESULT_PRICE"]["BASE_PRICE"]
                            )
                        )
                    );

                    $productPriceBlock.append(
                        $("<span/>").addClass("oldPriceLabel").html(" &nbsp; " + LANG["CATALOG_ECONOMY"]).append(
                            $("<span/>").addClass("economy").html(
                                jsonData[0]["PRODUCT"]["PRICE"]['RESULT_PRICE']["DISCOUNT_PRINT"]
                            )
                        )
                    );

                    //write discount
                    //$productChangePrice.append($productPriceBlock);

                }

                //bonus
                if (typeof jsonData[0]["PRODUCT"]["PROPERTIES"]["BONUS"] != "undefined" && jsonData[0]["PRODUCT"]["PROPERTIES"]["BONUS"]["VALUE"] != "") {
                    var $purchaseBonus = $("<span />", {class: "purchaseBonus"}).html(jsonData[0]["PRODUCT"]["PROPERTIES"]["BONUS"]["NAME"]);
                    var $purchaseBonusValue = $("<span />", {class: "theme-color"}).html("+ " + jsonData[0]["PRODUCT"]["PROPERTIES"]["BONUS"]["VALUE"]);
                    $purchaseBonus.prepend($purchaseBonusValue);
                    $productChangePrice.append($purchaseBonus);
                }

                //catalog set (complect) block

                if ($("div").is("#set")) {

                    var $changePriceSet = $(".changePriceSet").html(jsonData[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"]);
                    if (jsonData[0]["PRODUCT"]["PRICE"]["RESULT_PRICE"]["DISCOUNT"] > 0) {
                        $changePriceSet.append(
                            $("<s/>").addClass("discount").html(
                                jsonData[0]["PRODUCT"]["PRICE"]["RESULT_PRICE"]["BASE_PRICE"]
                            )
                        );
                    }

                    //j vars
                    var $setProductContainer = $("#set");
                    var $setMainElements = $setProductContainer.find('.setMainElement:not(".disabled")');
                    var $setPrice = $("#setPrice");
                    var $setDisnt = $("#setDisnt");

                    //n vars
                    var setPriceValue = 0;
                    var setPriceDiscountValue = 0;

                    //str vars
                    var tmpPriceLabel = $setPrice.html().replace(/[0-9]/g, '');
                    var tmpDisntLabel = $setDisnt.html().replace(/[0-9]/g, '');

                    var changePriceValue = parseInt(jsonData[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"].replace(/[^0-9]/g, ''));

                    $setProductContainer.find(".general.setElement").data("price", changePriceValue).data("discount", jsonData[0]["PRODUCT"]["PRICE"]["RESULT_PRICE"]["DISCOUNT"]);

                    $setMainElements.each(function (i, nextElement) {
                        var $nextElement = $(nextElement);
                        setPriceValue += parseInt($nextElement.data("price"));
                        setPriceDiscountValue += parseInt($nextElement.data("price")) + parseInt($nextElement.data("discount"));
                    });

                    $setPrice.html(
                        formatPrice(setPriceValue) + tmpPriceLabel
                    );

                    $setDisnt.html(
                        formatPrice(setPriceDiscountValue) + tmpDisntLabel
                    );

                    if (setPriceDiscountValue == 0) {
                        $setDisnt.hide();
                    } else {
                        $setDisnt.show();
                    }

                }

                if ($("div").is("#setWindow")) {

                    // catalog set window
                    var $setWindowContainer = $("#setWindow");
                    var $setWindowElements = $setWindowContainer.find('.setWindowElement:not(".disabled")');
                    var $setWindowPrice = $("#setWPrice");
                    var $setWindowDisnt = $("#setWDisnt");

                    //n vars
                    var setWindowPriceValue = 0;
                    var setWindowPriceDiscountValue = 0;

                    //str vars
                    var tmpPriceWindowLabel = $setWindowPrice.html().replace(/[0-9]/g, '');
                    var tmpDisntWindowLabel = $setWindowDisnt.html().replace(/[0-9]/g, '');

                    var changePriceValue = parseInt(jsonData[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"].replace(/[^0-9]/g, ''));

                    $setWindowContainer.find("#wProduct").data("price", changePriceValue).data("discount", jsonData[0]["PRODUCT"]["PRICE"]["RESULT_PRICE"]["DISCOUNT"]);

                    $setWindowElements.each(function (i, nextElement) {
                        var $nextElement = $(nextElement);
                        setWindowPriceValue += parseInt($nextElement.data("price"));
                        setWindowPriceDiscountValue += parseInt($nextElement.data("price")) + parseInt($nextElement.data("discount"));
                    });

                    $setWindowPrice.html(
                        formatPrice(setWindowPriceValue) + tmpPriceLabel
                    );

                    $setWindowDisnt.html(
                        formatPrice(setWindowPriceDiscountValue) + tmpDisntLabel
                    );

                    if (setWindowPriceDiscountValue == 0) {
                        $setWindowDisnt.hide();
                    } else {
                        $setWindowDisnt.show();
                    }
                }

                $(".changeName").html(jsonData[0]["PRODUCT"]["NAME"]);
                $(".cheaper-product-name").html(jsonData[0]["PRODUCT"]["NAME"]);

            }

        }

        event.preventDefault();

    }

    var openSkuDropDown = function (event) {

        //vars
        var $this = $(this);
        var $dropList = $this.siblings(".skuDropdownList");

        //show list
        $dropList.toggleClass("opened");

        //opened flag
        skuDropdownOpened = $dropList.hasClass("opened");

        return event.preventDefault();

    };

    var selectSkuDropDownValue = function (event) {

        //vars
        var $this = $(this);
        var $dropList = $this.parents(".skuDropdownList");
        var $dropListItems = $dropList.find(".skuDropdownListItem").removeClass("selected");
        var $checkedItem = $dropList.siblings(".skuCheckedItem");

        //hide list
        $dropList.removeClass("opened");

        //opened flag
        skuDropdownOpened = false;

        //active
        $this.addClass("selected");

        //write value
        $checkedItem.html($this.text());

        //
        return event.preventDefault();

    };

    var closeSkuDropDown = function (event) {

        //if opened
        if (skuDropdownOpened) {

            //close
            $(".skuDropdownList").removeClass("opened");

            //opened flag
            skuDropdownOpened = false;
        }

    };

    //skuDropDown
    $(document).on("click", ".elementSkuDropDownProperty .skuCheckedItem", openSkuDropDown);
    $(document).on("click", ".elementSkuDropDownProperty .skuDropdownListItem", selectSkuDropDownValue);
    $(document).on("click", ".skuDropdown", function (event) {
        event.stopImmediatePropagation()
    });
    $(document).on("click", closeSkuDropDown);

    //sku select
    $(document).on("click", ".elementSkuPropertyLink", elementSelectSku);


    $(".available_link").on("click", function () {
        $(".tab-link").removeClass("active");
        $(".tab-content").removeClass("active");
        $(".tab-link.salon").addClass("active");
        $(".tab-content.stores").addClass("active");
    });

});
