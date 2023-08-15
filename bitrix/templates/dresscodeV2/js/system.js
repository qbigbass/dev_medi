var timeOutID;
var intervalID;
var flushTimeout;
var lastAddCartText;
var skuLoading = false;
var fastBuyOpen = false;
var fastOrderOpen = false;
var SmpFastOrderOpen = false;
var fastViewOpen = false;
var fastViewStoresOpen = false;
var priceVariantOpen = false;
var requestPriceOpen = false;
var specialBlockMoved = false;
var basketProductsNow = false;
var oSkuDropdownOpened = false;
var medi_popup_Open = false;

var loadingPictureControl = function (imagePath, callBack) {

    if (typeof imagePath != "undefined" && imagePath != "") {

        var newImage = new Image();
        newImage.src = imagePath;

        $(newImage).one("load", callBack).each(function () {
            if (this.complete) {
                $(this).load();
            }
        });

    }

};

var checkLazyItems = function () {
    var $lazyItems = $(".lazy");
    $.each($lazyItems, function () {
        var $item = $(this);
        var lazyPath = $item.data("lazy");
        if (typeof lazyPath != "undefined" && lazyPath != "") {
            loadingPictureControl(lazyPath, function () {
                $item.attr("src", lazyPath);
            });
        }
    });
}

var slickItems = function() {
    var items = $('.single-item');
    setTimeout(function () {
        $.each(items, function () {
            if ($(this).hasClass('more-images')) {
                $(this).slick({
                    dots: true,
                    arrows:false,
                    slidesToShow: 1,
                    adaptiveHeight: true
                });
            }
        })
    }, 500);
}

var changeAddCartButton = function (jsonData) {

    //search addCart buttons
    if (typeof jsonData["CATEGORIES"] != "undefined") {
        if (typeof jsonData["CATEGORIES"]["READY"] != "undefined") {

            //each basket elements
            $.each(jsonData["CATEGORIES"]["READY"], function (index, nextElement) {
                if (typeof nextElement["PRODUCT_ID"] != "undefined") {
                    var $currentButton = $('.addCart[data-id="' + nextElement["PRODUCT_ID"] + '"]');
                    if (typeof $currentButton != "undefined") {
                        $currentButton.each(function (ii, nextButton) {
                            updateAddCartButton($(nextButton));
                        });
                    }
                }
            });

            //save current values
            basketProductsNow = jsonData;

        }
    }

};

var updateAddCartButton = function ($currentElement) {
    var $gtm = 'GTM_go_cart_catalog';
    if ($currentElement.parents("#appFastView").length) {
        $gtm = "GTM_go_cart_fast";
    } else if ($currentElement.parents("#catalogElement").length) {
        $gtm = "GTM_go_cart_card";
    }
    var $imageAfterLoad = $currentElement.find("img");
    $currentElement.text(LANG["ADDED_CART_SMALL"])
        .attr("href", SITE_DIR + "personal/cart/")
        .prepend($imageAfterLoad.attr("src", TEMPLATE_PATH + "/images/added.png"))
        .addClass("added").attr("id", $gtm);
};

var cartReload = function () {

    if (typeof (window.topCartTemplate) == "undefined") {
        window.topCartTemplate = "topCart";
    }

    if (typeof (window.wishListTemplate) == "undefined") {
        window.wishListTemplate = ".default";
    }

    if (typeof (window.compareTemplate) == "undefined") {
        window.compareTemplate = ".default";
    }

    $.get(ajaxPath + "?act=flushCart&topCartTemplate=" + window.topCartTemplate + "&wishListTemplate=" + window.wishListTemplate + "&compareTemplate=" + window.compareTemplate, function (data) {

        var $items = $(data).find(".dl");

        $("#flushTopCart").html($items.eq(0).html());
        $("#flushFooterCart").html($items.eq(1).html());
        $("#flushTopwishlist").html($items.eq(2).html());
        $("#flushTopCompare").html($items.eq(3).html());

    });
}


$(function () {

    // ecommerce analytics
    var clickEventPush = function ($this) {
        var $this = $this.parents(".item");
        var productID = $this.data("product-id");
        var productName = $this.data("product-name");
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

    $(".itemCard a.name, .itemCard a.picture").click(function () {
        clickEventPush($(this));
    });


    $(".questions-answers-list .question").click(function () {
        var par = $(this).parents(".question-answer-wrap");
        par.toggleClass("active").find(".answer").slideToggle();
    });

    $(".banner-animated").addClass("banner-image-load");

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
    $(".salons-menu-tab").click(function () {
        if (!$(this).hasClass("active")) {
            var par = $(this).parents(".salons-tabs-wrap");
            var index = $(this).index();

            $(this).addClass('active').siblings().removeClass("active");
            $('.salons-tab-content:eq(' + index + ')', par).addClass("active").siblings().removeClass("active");
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

    // call the modal window on the feedback block
    $(".consultation-btn").click(function () {
        $(".callBack").click();
    });

    if ($("#footerTabs .tab").length == 0) {
        $("#footerTabs, #footerTabsCaption").remove();
    } else {
        $("#footerTabsCaption .item").eq(0).find("a").addClass("selected");
        $("#footerTabs .tab").eq(0).addClass("selected");
    }

    if ($("#infoTabs .tab").length == 0) {
        $("#infoTabs, #infoTabsCaption").remove();
    } else {
        $("#infoTabsCaption .item").eq(0).find("a").addClass("selected");
        $("#infoTabs .tab").eq(0).addClass("selected");
    }

    var $upButton = $("#upButton");

    $(window).on("ready scroll", function (event) {
        var curScrollValueY = (event.currentTarget.scrollY) ? event.currentTarget.scrollY : $(window).scrollTop()
        if (curScrollValueY > 0) {
            $upButton.addClass("enb");
        } else {
            $upButton.removeClass("enb");
        }

    });

    $upButton.on("click", function (event) {

        $("html,body").animate({
            scrollTop: 0
        }, 250);

        return event.preventDefault();

    });

});

$(window).on("ready", function (event) {

        //vars
        var $topMenuContainer = $("#mainMenuContainer");
        var $mainMenuStaticContainer = $("#mainMenuStaticContainer");
        var $body = $("body").removeClass("loading"); // cache body
        var $window = $(window);

        if ($("div").is(".global-block-container") && $("div").is(".global-information-block") && $("div").is(".global-information-block-cn")) {

            //disable fixed menu
            _topMenuNoFixed = true;

            //vars
            var $globalBlockContainer = $body.find(".global-block-container");
            var $globalInformationBlock = $globalBlockContainer.find(".global-information-block");
            var $globalInformationBlockCntr = $globalInformationBlock.find(".global-information-block-cn");

            //set height
            $globalBlockContainer.css("min-height", $globalInformationBlock.height());

            if (!$globalInformationBlock.hasClass("no-fixed")) {
                var informBlockOffset = $globalInformationBlock.offset();
                var maxScrollHeight = $globalBlockContainer.height() + informBlockOffset.top - ($globalInformationBlockCntr.height() + 24); //24 padding top
            }

            var gbScrollCtr = function (event) {

                var $this = $(this);
                var currentScrollValue = $this.scrollTop();

                if (currentScrollValue >= informBlockOffset.top) {
                    if (currentScrollValue >= maxScrollHeight) {
                        $globalInformationBlock.addClass("max-scroll");
                    } else {
                        $globalInformationBlock.removeClass("max-scroll");
                    }
                    $globalInformationBlock.addClass("fixed");
                } else {
                    $globalInformationBlock.removeClass("fixed");
                }

            };

            var reCalcGbParams = function () {
                informBlockOffset = $globalInformationBlock.offset();
                maxScrollHeight = $globalBlockContainer.height() + informBlockOffset.top - ($globalInformationBlockCntr.height() + 24); //24 padding top
            }

            $(window).on("scroll", gbScrollCtr);
            $(window).on("resize", reCalcGbParams);

        }

        var moveBlockToContainer = function (blockID, moveBlockID) {

            //set j vars
            var $blockID = $(blockID);
            var $moveBlockID = $(moveBlockID);

            //move
            $moveBlockID.append($blockID);

            //set global flag var
            return specialBlockMoved = true;

        };

        var setSpecialBlockPosition = function () {

            if ($("div").is("#specialBlock")) {
                if ($(window).width() <= 1850) {
                    moveBlockToContainer("#specialBlock", "#specialBlockMoveContainer");
                } else if (specialBlockMoved === true && $(window).width() > 1600) {
                    moveBlockToContainer("#specialBlock", "#promoBlock");
                }
            }
        };

        //start form load
        setSpecialBlockPosition();

        //resize events
        $(window).on("resize", setSpecialBlockPosition);

        var getRequestPrice = function (event) {

            var $this = $(this);
            var $requestPrice = $("#requestPrice");
            var $foundation = $("#foundation").addClass("blurred");

            $("#requestPrice, #requestPrice .requstProductContainer").show();
            $("#requestPriceResult").hide();

            //clear form
            $("#requestPriceForm").find('input[type="text"], textarea').val("");
            $requestPrice.find(".requestPricePicture").attr("src", $requestPrice.data("load"));

            var productID = $this.data("id");

            $this.addClass("loading");

            var gObj = {
                id: productID,
                act: "getRequestPrice"
            };

            $.getJSON(ajaxPath, gObj).done(function (jData) {

                $this.removeClass("loading");
                $requestPrice.find(".requestPriceUrl").attr("href", jData["PRODUCT"]["DETAIL_PAGE_URL"]);
                $requestPrice.find(".productNameBlock .middle").html(jData["PRODUCT"]["NAME"]);
                $requestPrice.find("#requestPriceProductID").val(jData["PRODUCT"]["ID"]);
                $requestPrice.find(".markerContainer").remove();

                if (jData["PRODUCT"]["MARKER"] != undefined) {

                    $requestPrice.find("#fastBuyPicture").prepend(
                        $("<div>").addClass("markerContainer")
                            .append(
                                jData["PRODUCT"]["MARKER"]
                            )
                    );
                }

                $requestPrice.show();

                loadingPictureControl(jData["PRODUCT"]["PICTURE"]["src"], function () {
                    $requestPrice.find(".requestPricePicture").attr("src", jData["PRODUCT"]["PICTURE"]["src"]);
                });

                requestPriceOpen = true;

            }).fail(function (jqxhr, textStatus, error) {

                $.get(ajaxPath, gObj).done(function (Data) {
                    console.log(Data)
                });

                $this.removeClass("loading")
                    .addClass("error");

                console.error(
                    "Request Failed: " + textStatus + ", " + error
                );

            });

            return event.preventDefault();
        };

        var sendRequestPrice = function (event) {

            var $this = $(this).addClass("loading");
            var $requestPriceForm = $("#requestPriceForm");
            var $requestPriceFormTelephone = $requestPriceForm.find("#requestPriceFormTelephone").removeClass("error");

            if ($requestPriceFormTelephone.val() == "") {
                $requestPriceFormTelephone.addClass("error");
            }

            var $personalInfo = $requestPriceForm.find("#personalInfoRequest");
            if (!$personalInfo.prop("checked")) {
                $personalInfo.addClass("error");
            }

            if ($requestPriceFormTelephone.val() != "" && $personalInfo.prop("checked")) {

                $.getJSON(ajaxPath + "?" + $requestPriceForm.serialize()).done(function (jData) {

                    $("#requestPriceResultTitle").html(jData["heading"]);
                    $("#requestPriceResultMessage").html(jData["message"]);

                    $("#requestPrice .requstProductContainer").hide();
                    $("#requestPriceResult").show();

                    $this.removeClass("loading");

                }).fail(function (jqxhr, textStatus, error) {

                    $this.removeClass("loading").addClass("error");

                    console.error(
                        "Request Failed: " + textStatus + ", " + error
                    );

                });

            } else {
                $this.removeClass("loading");
            }

            return event.preventDefault();
        };


        var closeRequestPrice = function (event) {
            var $appFastBuy = $("#requestPrice").hide();
            var $foundation = $("#foundation").removeClass("blurred");
            requestPriceOpen = false;
            return event.preventDefault();
        };

        var getFastView = function (event) {

            var $this = $(this).addClass("loading");
            var $in_cart = 0;
            if ($this.hasClass("fast")) {
                console.log("in_cart");
                var $in_cart = 1;
            }
            var $productContainer = $this.parents(".item");
            var productID = $this.data("id");
            var productIblockID = $productContainer.data("product-iblock-id");

            var productListName = $productContainer.parents(".productsListName").data("list-name");
            var productListPos = $productContainer.data("position");

            if (productID) {
                $.ajax({
                    url: ajaxPath + "?act=getFastView&product_id=" + productID + "&product_iblock_id=" + productIblockID + "&product_currency_id=" + $productContainer.data("currency-id") + "&product_convert_currency=" + $productContainer.data("convert-currency") + "&product_price_code=" + $productContainer.data("price-code") + "&product_hide_measures=" + $productContainer.data("hide-measure") + "&product_hide_not_available=" + $productContainer.data("hide-not-available") + "&product_pos=" + productListPos + "&in_cart=" + $in_cart,
                    success: function (http) {

                        //clear carousel cache vars
                        delete fastViewInitPictureCarousel;
                        delete fastViewInitPictureSlider;
                        delete initFastViewApp;
                        delete createFastView;

                        //remove fastview window
                        $("#appFastView").remove();

                        //append to body
                        $body.append(http);
                        $this.removeClass("loading");

                        //unbind last events
                        $(document).off("click", "#appFastView .appFastViewExit");
                        $(document).off("click", "#appFastView .appFastViewPictureCarouselItem");
                        $(document).off("click", "#appFastView .appFastViewPictureCarouselLeftButton");
                        $(document).off("click", "#appFastView .appFastViewPictureCarouselRightButton");
                        $(document).off("mousemove", "#appFastView .appFastViewPictureSliderItemLink");
                        $(document).off("mouseover", "#appFastView .appFastViewPictureSliderItemLink");
                        $(document).off("mouseleave", "#appFastView .appFastViewPictureSliderItemLink");

                        //start fastView scrips
                        initFastViewApp();
                        //reload addCart button
                        cartReload();
                        //subscribe button reload
                        //subscribeOnline();
                        /*
                                            window.dataLayer = window.dataLayer || [];

                                            dataLayer.push({
                                                'ecommerce': {
                                                  'currencyCode': 'RUB',
                                                  'click': {
                                                    'actionField': {
                                                    'list': productListName
                                                    },
                                                    'products': [{
                                                     'name': $("#appFastView .appFastViewProductHeadingLinkLayout").html(),
                                                      'id': productID,
                                                      'price': $("#appFastView .priceVal").html().replace(/\D+/, "").replace(/\D+/, ""),
                                                      'brand': $("#appFastView .propertyValue.brand_prop_value a").html(),
                                                      'category': $("#appFastView .appFastViewProductHeadingLinkLayout").attr("data-section-path"),
                                                      'variant': $("#appFastView .changeArticle").html(),
                                                      'position': productListPos
                                                    }]
                                                  }
                                                },
                                                'event': 'gtm-ee-event',
                                                'gtm-ee-event-category': 'Enhanced Ecommerce',
                                                'gtm-ee-event-action': 'Product Clicks',
                                                'gtm-ee-event-non-interaction': 'False',
                                            });*/

                    },
                    cache: false,
                    async: false
                });
                fastViewOpen = true;
            }

            return event.preventDefault();
        }

        var getStoresWindow = function (event) {

            var $this = $(this).addClass("loading");
            var productID = $this.data("id");

            if (productID) {
                $.getJSON(ajaxPath + "?act=getAvailableWindow&product_id=" + productID, function (json) {
                    if (typeof json["COMPONENT_DATA"] != "undefined") {
                        $("#fastViewStores").remove();
                        $body.append(json["COMPONENT_DATA"]);
                        $this.removeClass("loading");
                        fastViewStoresOpen = true;
                    }
                });
            }

            return event.preventDefault();

        };

        var closeStoresWindow = function (event) {
            $("#fastViewStores").remove();
            fastViewStoresOpen = false;
            return event.preventDefault();
        };
        //  показ окна с размерной сеткой товара
        var getSizechartWindow = function (event) {

            var $this = $(this).addClass("loading");
            var chartID = $this.data("id");
            var chartImg = $this.data("img");
            //console.log(chartImg);
            if (chartImg) {

                $chartHtml = '<div id="fastViewSizechart"><div id="fastViewSizechart-wrap"><div class="fastViewSizechartContainer"><div class ="fastViewSizechartContentBox"><div class="fastViewSizechartHeading">Подбор размера <a href="#" class="fastViewSizechartExit"></a></div>' +
                    '<div class="fastViewSizechartContent"><img class="sizechart" src="' + chartImg + '"/></div>' +
                    '</div></div></div></div>';


                $("#fastViewSizechart").remove();
                $body.append($chartHtml);
                $this.removeClass("loading");
                fastViewSizechartOpen = true;
            }
            /**  TODO Load html by prop id
             *
             else  if (chartID) {
            $.getJSON(ajaxPath + "?act=getSizechartWindow&product_id=" + productID, function(json) {
                if (typeof json["COMPONENT_DATA"] != "undefined") {
                    $("#fastViewSizechart").remove();
                    $body.append(json["COMPONENT_DATA"]);
                    $this.removeClass("loading");
                    fastViewSizechartOpen = true;
                }
            });
        }
             *
             */

            return event.preventDefault();

        };

        var closeSizechartWindow = function (event) {
            $("#fastViewSizechart").remove();
            fastViewSizechartOpen = false;
            return event.preventDefault();
        };

        //  показ окна
        var get_medi_popup_Window = function (event) {

            var $this = $(this).addClass("loading");
            var popupID = $this.data("id");
            var popupImg = $this.data("img");
            var popupSvg = $this.data("svg");
            var popupVideo = $this.data("video");
            var popupTitle = $this.data("title");
            var popupSrc = $this.data("src");
            var popupAction = $this.data("action");

            if (popupSvg) {

                $popupHtml = '<div class="medi_popup"><div class="medi_popup_wrap"><div class="medi_popup_Container"><div class ="medi_popup_ContentBox"><div class="medi_popup_Heading">' + popupTitle + ' <a href="#" class="medi_popup_Exit"></a></div>' +
                    '<div class="medi_popup_Content"><object  type="image/svg+xml" data="' + popupSvg + '" class="medi_popup_Img" >Your browser does not support SVGs</object></div>' +
                    '</div></div></div></div>';


                $(".medi_popup").remove();
                $body.append($popupHtml);
                $this.removeClass("loading");
                medi_popup_Open = true;
            } else if (popupVideo) {

                $popupHtml = '<div class="medi_popup"><div class="medi_popup_wrap"><div class="medi_popup_Container"><div class ="medi_popup_ContentBox"><div class="medi_popup_Heading">' + popupTitle + ' <a href="#" class="medi_popup_Exit"></a></div>' +
                    '<div class="medi_popup_Content"><iframe class="medi_popup_Video" frameborder=0 allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen src="' + popupVideo + '"/></div>' +
                    '</div></div></div></div>';


                $(".medi_popup").remove();
                $body.append($popupHtml);
                $this.removeClass("loading");
                medi_popup_Open = true;
            } else if (popupImg) {

                $popupHtml = '<div class="medi_popup"><div class="medi_popup_wrap"><div class="medi_popup_Container"><div class ="medi_popup_ContentBox"><div class="medi_popup_Heading">' + popupTitle + ' <a href="#" class="medi_popup_Exit"></a></div>' +
                    '<div class="medi_popup_Content"><img class="medi_popup_Img" src="' + popupImg + '"/></div>' +
                    '</div></div></div></div>';


                $(".medi_popup").remove();
                $body.append($popupHtml);
                $this.removeClass("loading");
                medi_popup_Open = true;
            } else if (popupSrc) {
                if (popupID > 0) popupSrc = popupSrc + '&p=' + popupID;
                $.ajax({
                    url: popupSrc,
                    success: function (data) {

                        $popupHtml = '<div class="medi_popup"><div class="medi_popup_wrap"><div class="medi_popup_Container"><div class ="medi_popup_ContentBox"><div class="medi_popup_Heading">' + popupTitle + ' <a href="#" class="medi_popup_Exit"></a></div>' +
                            '<div class="medi_popup_Content">' + data + '</div>' +
                            '</div></div></div></div>';


                        $(".medi_popup").remove();
                        $body.append($popupHtml);


                        $(".tab-link").on("click", function () {
                            if (!$(this).hasClass("active")) {
                                var par = $(this).parents(".tabs-wrap");
                                var index = $(this).index();

                                $(this).addClass('active').siblings().removeClass("active");
                                $('.tab-content:eq(' + index + ')', par).addClass("active").siblings().removeClass("active");
                            }
                        });

                        $this.removeClass("loading");
                        medi_popup_Open = true;
                    }
                });
            }

            if (medi_popup_Open) {
                $(".medi_popup_wrap").click(function (e) {
                    if ($(e.target).closest('.medi_popup_Container').length) {
                        // клик внутри элемента
                        return;
                    }
                    close_medi_popup_Window();
                });
                $(document).keyup(function (e) {
                    if (e.key === "Escape" || e.keyCode === 27) {
                        close_medi_popup_Window();
                    }
                });

            }
            /**  TODO Load html by prop id
             *
             else  if (chartID) {
            $.getJSON(ajaxPath + "?act=getSizechartWindow&product_id=" + productID, function(json) {
                if (typeof json["COMPONENT_DATA"] != "undefined") {
                    $("#fastViewSizechart").remove();
                    $body.append(json["COMPONENT_DATA"]);
                    $this.removeClass("loading");
                    fastViewSizechartOpen = true;
                }
            });
        }
             *
             */

            return event.preventDefault();

        };

        var close_medi_popup_Window = function (event) {
            $(".medi_popup").remove();
            medi_popup_Open = false;
            if (event !== undefined)
                return event.preventDefault();
        };

        var getPricesWindow = function (event) {

            var $this = $(this).addClass("loading");
            var $thisContainer = $this.parents(".item");
            var productID = $this.data("id");

            if (productID) {
                $.get(ajaxPath + "?act=getPricesWindow&product_id=" + productID + "&product_price_code=" + encodeURIComponent($thisContainer.data("price-code")) + "&product_currency=" + encodeURIComponent($thisContainer.data("currency")), function (http) {

                    $("#appProductPriceVariant").remove();
                    $this.removeClass("loading");
                    $body.append(http);

                    var thisOffsetLeft = $this.offset().left;
                    var thisOffsetTop = $this.offset().top;

                    if (thisOffsetLeft + 320 > $(window).width()) {
                        thisOffsetLeft = $(window).width() - 334;
                    }

                    if ($this.data("fixed") == "Y") {
                        $("#appProductPriceVariant").css({
                            left: thisOffsetLeft,
                            top: thisOffsetTop - $(window).scrollTop(),
                            position: "fixed"
                        });
                    } else {
                        $("#appProductPriceVariant").css({
                            left: thisOffsetLeft,
                            top: thisOffsetTop
                        });
                    }
                    priceVariantOpen = true;
                });
            }

            return event.preventDefault();

        };

        var closePricesWindow = function (event) {
            $("#appProductPriceVariant, .priceVariantStyles").remove();
            priceVariantOpen = false;
            return event.preventDefault();
        };

        var fastViewSku = function ($product, http) {
            var $namer = $product.find(".appFastViewProductHeadingLink");
            var $elPicture = $product.find(".picture");
            var $changeFastBack = $product.find(".fastBack").removeClass("disabled");
            var $changeFastOrder = $product.find(".fastOrder").removeClass("disabled").show();

            $namer.attr("href", http[0]["PRODUCT"]["DETAIL_PAGE_URL"]).html(http[0]["PRODUCT"]["NAME"]);

            $elPicture.attr("href", http[0]["PRODUCT"]["DETAIL_PAGE_URL"]);
            $elPicture.html($("<img/>").attr("src", http[0]["PRODUCT"]["PICTURE"]));

            $product.find(".addCart, .fastBack, .fastOrder, .addCompare").data("id", http[0]["PRODUCT"]["ID"]).attr("data-id", http[0]["PRODUCT"]["ID"]);
            if (http[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"]) {
                $product.find(".price").html($("<span />", {class: "priceVal"}).html(http[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"] + " ")).removeClass("getPricesWindow");
            } else {
                $product.find(".price").html($("<span />", {class: "priceVal"}).html(LANG["REQUEST_PRICE_LABEL"])).removeClass("getPricesWindow").removeAttr("href");
                http[0]["PRODUCT"]["CAN_BUY"] = "N";
            }
            if (http[0]["PRODUCT"]["RESULT_PROPERTIES"]) {
                $product.find(".changeProperties").html(http[0]["PRODUCT"]["RESULT_PROPERTIES"]);
            }

            var $changeCart = $product.find(".addCart").removeClass("subscribe unSubscribe");

            $changeCart.find("img").remove();
            if (http[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"]) {


                //fast display
                // $changeFastOrder.removeClass("disabled").show();
                $changeCart.removeClass("added").removeClass("disabled").removeClass("requestPrice")
                    .html(LANG["ADD_BASKET_DEFAULT_LABEL"])
                    .prepend($("<img />").attr({src: TEMPLATE_PATH + "/images/incart.png", class: "icon"}))
                    .attr("href", "#");
            } else {
                $changeFastBack.addClass("disabled");
                $changeFastOrder.addClass("disabled").hide();
                $changeCart.removeClass("added").addClass("disabled").addClass("requestPrice")
                    .html(LANG["REQUEST_PRICE_BUTTON_LABEL"])
                    .prepend($("<img />").attr({src: TEMPLATE_PATH + "/images/request.png", class: "icon"}))
                    .attr("href", "#");
            }

            if (http[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"]) {
                if ($product.data("hide-measure") != "Y" && http[0]["PRODUCT"]["MEASURE"] != undefined && http[0]["PRODUCT"]["MEASURE"]["SYMBOL_RUS"] != "") {
                    $product.find(".price").append(
                        $("<span/>").addClass("measure").html(
                            " / " + http[0]["PRODUCT"]["MEASURE"]["SYMBOL_RUS"] + " "
                        )
                    );
                }
            }

            if (http[0]["PRODUCT"]["PRICE"]["RESULT_PRICE"]["DISCOUNT"] > 0) {
                $product.find(".price").append(
                    $("<span/>").addClass("oldPriceLabel").html(CATALOG_LANG["FAST_VIEW_OLD_PRICE_LABEL"]).append(
                        $("<s/>").addClass("discount").html(
                            http[0]["PRODUCT"]["PRICE"]["RESULT_PRICE"]["BASE_PRICE"]
                        )
                    )
                );
            }

            if (http[0]["PRODUCT"]["COUNT_PRICES"] > 1) {
                $product.find(".price").addClass("getPricesWindow").data("id", http[0]["PRODUCT"]["ID"]).prepend($("<span/>", {class: "priceIcon"})).attr("href", "#");
            } else {
                $product.find(".price").removeAttr("href").find(".priceIcon").remove();
            }

            var $changeAvailable = $product.find(".changeAvailable");

            $changeAvailable.removeClass("getStoresWindow");
            $changeAvailable.removeClass("outOfStock");
            $changeAvailable.removeClass("onOrder");
            $changeAvailable.removeClass("inStock");
            $changeAvailable.removeAttr("href");


            if (http[0]["PRODUCT"]["CATALOG_QUANTITY"] > 0) {
                if (http[0]["PRODUCT"]["STORES_COUNT"] > 1) {
                    $changeAvailable.html($("<span/>").html(LANG["CATALOG_AVAILABLE"])).addClass("inStock").attr("href", "#").addClass("getStoresWindow").data("id", http[0]["PRODUCT"]["ID"]);
                    $changeAvailable.prepend(
                        $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/inStock.png")
                    );
                } else {
                    $changeAvailable.html(LANG["CATALOG_AVAILABLE"]).addClass("inStock");
                    $changeAvailable.prepend(
                        $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/inStock.png")
                    );
                }
                $changeCart.removeClass("disabled label fastBack").addClass("addCart").show();
                $(".qtyBlock").show();
            } else {
                if (http[0]["PRODUCT"]["CAN_BUY"] != "Y") {
                    $changeAvailable.html(LANG["CATALOG_NO_AVAILABLE"]).addClass("outOfStock");
                    //$changeFastBack.addClass("disabled");
                    $changeFastOrder.addClass("disabled").hide();

                    if (http[0]["PRODUCT"]["CATALOG_SUBSCRIBE"] == "Y" && http[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"]) {
                        $changeCart.html(LANG["ADD_SUBSCRIBE_LABEL"])
                            .prepend($("<img />").attr({src: TEMPLATE_PATH + "/images/subscribe.png", class: "icon"}))
                            .attr("href", "#").addClass("subscribe");
                    } else {
                        $changeFastOrder.addClass("disabled").hide();
                        $(".qtyBlock").hide();

                        $changeCart.html("Под заказ").prepend(
                            $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/incart.png"))
                            .attr("href", "#").addClass("fastBack ");


                        //fast display
                        $changeCart.addClass("disabled").hide();
                    }

                    $changeAvailable.prepend(
                        $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/outOfStock.png")
                    );
                } else {
                    $changeAvailable.html(LANG["CATALOG_ON_ORDER"]).addClass("onOrder");
                    $changeAvailable.prepend(
                        $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/onOrder.png")
                    );
                }
            }

            //article

            if (typeof (http[0]["PRODUCT"]["PROPERTIES"]["CML2_ARTICLE"]) != "undefined") {
                if (typeof (http[0]["PRODUCT"]["PROPERTIES"]["CML2_ARTICLE"]["VALUE"]) != "undefined" && http[0]["PRODUCT"]["PROPERTIES"]["CML2_ARTICLE"]["VALUE"] != "") {
                    $product.find(".changeArticle").html(http[0]["PRODUCT"]["PROPERTIES"]["CML2_ARTICLE"]["VALUE"]).parents(".article").removeClass("hidden");
                } else {
                    if ($product.find(".changeArticle").data("first-value")) {
                        $product.find(".changeArticle").html($product.find(".changeArticle").data("first-value"));
                    } else {
                        $product.find(".changeArticle").parents(".article").addClass("hidden");
                    }
                }
            }

            //desc
            var $productDescription = $product.find(".appFastViewDescription");
            var $productDescriptionText = $productDescription.find(".appFastViewDescriptionText");
            if (http[0]["PRODUCT"]["PREVIEW_TEXT"]) {
                $productDescription.addClass("visible");
                $productDescriptionText.html(http[0]["PRODUCT"]["PREVIEW_TEXT"]);
            } else {
                $productDescription.removeClass("visible");
            }


            //QTY BOX

            //get qty box ()
            var $qtyBox = $product.find(".catalogQtyBlock .catalogQty");
            $qtyBox.removeAttr("data-extended-price").removeData("extended-price");

            //write values
            $qtyBox.val(http[0]["PRODUCT"]["BASKET_STEP"]).data("max-quantity", http[0]["PRODUCT"]["CATALOG_QUANTITY"]).data("step", http[0]["PRODUCT"]["BASKET_STEP"]).removeClass("error");
            $changeCart.data("quantity", http[0]["PRODUCT"]["BASKET_STEP"]);

            if (typeof http[0]["PRODUCT"]["PRICE"]["EXTENDED_PRICES_JSON_DATA"] != "undefined") {
                if (http[0]["PRODUCT"]["PRICE"]["EXTENDED_PRICES_JSON_DATA"] != "") {
                    $qtyBox.data("extended-price", http[0]["PRODUCT"]["PRICE"]["EXTENDED_PRICES_JSON_DATA"]);
                }
            }

            if (http[0]["PRODUCT"]["CATALOG_QUANTITY_TRACE"] == "Y" && http[0]["PRODUCT"]["CATALOG_CAN_BUY_ZERO"] == "N") {
                $qtyBox.data("enable-trace", "Y");
            } else {
                $qtyBox.data("enable-trace", "N");
            }

            if (http[0]["PRODUCT"]["IMAGES"]) {

                var $appFastViewPictureSliderItems = $product.find(".appFastViewPictureSliderItems").css({left: 0});
                var $appFastViewPictureCarouselItems = $product.find(".appFastViewPictureCarouselItems").css({left: 0});

                $appFastViewPictureSliderItems.empty();
                $appFastViewPictureCarouselItems.empty();

                $.each(http[0]["PRODUCT"]["IMAGES"], function (i, nextElement) {
                    $appFastViewPictureSliderItems.append(
                        $("<div />", {class: "appFastViewPictureSliderItem"}).append(
                            $("<div />", {class: "appFastViewPictureSliderItemLayout"}).append(
                                $("<a />", {
                                    class: "appFastViewPictureSliderItemLink",
                                    href: http[0]["PRODUCT"]["DETAIL_PAGE_URL"]
                                }).data("loupe-picture", nextElement["SUPER_LARGE_PICTURE"]["src"]).append(
                                    $("<img />", {
                                        class: "appFastViewPictureSliderItemPicture",
                                        src: nextElement["LARGE_PICTURE"]["src"]
                                    })
                                )
                            )
                        )
                    );

                    $appFastViewPictureCarouselItems.append(
                        $("<div />", {class: "appFastViewPictureCarouselItem"}).append(
                            $("<a />", {class: "appFastViewPictureCarouselItemLink", href: "#"}).append(
                                $("<img />", {
                                    class: "appFastViewPictureCarouselItemPicture",
                                    src: nextElement["SMALL_PICTURE"]["src"]
                                })
                            )
                        )
                    );

                });

                // //addCart button reload
                // changeAddCartButton(basketProductsNow);
                // //subscribe button reload
                // subscribeOnline();

                //sliders
                fastViewInitPictureSlider();
                fastViewInitPictureCarousel();

            }
        };

        var selectSku = function (event) {

            if (skuLoading == true) {
                return false;
            }

            var _params = "";
            var _props = "";
            var _highload = "";
            var _product_width = 200;
            var _product_height = 180;

            var $_this = $(this);
            var $_mProductContainer = $_this.parents(".item");
            var $_mProduct = $_this.parents(".sku");
            var $_parentProp = $_this.parents(".skuProperty");
            var $_propList = $_mProduct.find(".skuProperty");
            var $_clickedProp = $_this.parents(".skuPropertyValue");

            var _level = $_parentProp.data("level");

            $_this.parents(".skuPropertyList").find("li").removeClass("selected");
            $_clickedProp.addClass("selected loading");

            skuLoading = true; //block

            // set product image paramets
            if ($_mProduct.data("product-width") != undefined) {
                _product_width = $_mProduct.data("product-width");
            }

            if ($_mProduct.data("product-height") != undefined) {
                _product_height = $_mProduct.data("product-height");
            }

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
            $.getJSON(ajaxPath + "?act=selectSku&props=" + encodeURIComponent(_props) + "&params=" + encodeURIComponent(_params) + "&level=" + _level + "&iblock_id=" + $_mProduct.data("iblock-id") + "&prop_id=" + $_mProduct.data("prop-id") + "&product_id=" + $_mProduct.data("product-id") + "&highload=" + encodeURIComponent(_highload) + "&product_width=" + _product_width + "&product_height=" + _product_height + "&product-change-prop=" + $_mProduct.data("change-prop") + "&product-more-pictures=" + $_mProduct.data("more-pictures") + "&price-code=" + encodeURIComponent($_mProductContainer.data("price-code")))
                .done(function (http) {
                    $_propList.each(function (pI, pV) {
                        var $_sf = $(pV);
                        $_sf.data("level") > _level && $_sf.find(".skuPropertyValue").removeClass("selected").addClass("disabled");
                    });
                    $.each(http[1]["PROPERTIES"], function (name, val) {
                        var $_gPropList = $_propList.filter(function () {
                            return ($(this).data("name") == name);
                        });
                        var $_gPropListValues = $_gPropList.find(".skuPropertyValue");
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

                    if ($_mProduct.data("cast-func")) {
                        eval($_mProduct.data("castFunc"))($_mProduct, http); // callback function for cast sku change.
                    } else {

                        var $namer = $_mProduct.find(".name");
                        var $nameMiddler = $namer.find(".middle");
                        var $elPicture = $_mProduct.find(".picture");
                        var $changeFastBack = $_mProduct.find(".fastBack").removeClass("disabled");
                        var $changeFastOrder = $_mProduct.find(".fastOrder").removeClass("disabled").show();
                        var $changeReserve = $_mProduct.find(".reserve");

                        if ($nameMiddler) {
                            $namer.attr("href", http[0]["PRODUCT"]["DETAIL_PAGE_URL"]);
                            $nameMiddler.html(http[0]["PRODUCT"]["NAME"]);
                        } else {
                            $namer.attr("href", http[0]["PRODUCT"]["DETAIL_PAGE_URL"]).html(http[0]["PRODUCT"]["NAME"]);
                        }

                        $elPicture.attr("href", http[0]["PRODUCT"]["DETAIL_PAGE_URL"]);
                        $elPicture.html($("<img/>").attr("src", http[0]["PRODUCT"]["PICTURE"]));
                        //$elPicture.append($("<span />", {class: "getFastView"}).data("id", http[0]["PRODUCT"]["ID"]).html(LANG["FAST_VIEW_PRODUCT_LABEL"]));


                        $_mProduct.find(".addCart, .fastBack, .fastOrder, .addCompare, .changeCart, .reserve").data("id", http[0]["PRODUCT"]["ID"]).attr("data-id", http[0]["PRODUCT"]["ID"]);

                        if (http[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"]) {
                            $_mProduct.find(".price").html(http[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"] + " ").removeClass("getPricesWindow");
                        } else {
                            $_mProduct.find(".price").html(LANG["REQUEST_PRICE_LABEL"]).removeClass("getPricesWindow");
                        }

                        var $changeCart = $_mProduct.find(".addCart").removeClass("subscribe unSubscribe");
                        var $changeFast = $_mProduct.find(".fastBut");

                        $changeCart.find("img").remove();
                        if (http[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"]) {

                            $changeCart.removeClass("added").removeClass("disabled").removeClass("requestPrice")
                                .html(LANG["ADD_BASKET_DEFAULT_LABEL"])
                                .prepend($("<img />").attr({src: TEMPLATE_PATH + "/images/incart.png", class: "icon"}))
                                .attr("href", "#");
                        } else {
                            $changeFastBack.addClass("disabled");
                            $changeFastOrder.addClass("disabled").hide();
                            $changeCart.removeClass("added").addClass("disabled").addClass("requestPrice")
                                .html(LANG["REQUEST_PRICE_BUTTON_LABEL"])
                                .prepend($("<img />").attr({src: TEMPLATE_PATH + "/images/request.png", class: "icon"}))
                                .attr("href", "#");
                            http[0]["PRODUCT"]["CAN_BUY"] = "N";
                        }

                        if (http[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"]) {
                            if ($_mProduct.data("hide-measure") != "Y" && http[0]["PRODUCT"]["MEASURE"] != undefined && http[0]["PRODUCT"]["MEASURE"]["SYMBOL_RUS"] != "") {
                                $_mProduct.find(".price").append(
                                    $("<span/>").addClass("measure").html(
                                        " / " + http[0]["PRODUCT"]["MEASURE"]["SYMBOL_RUS"] + " "
                                    )
                                );
                            }
                        }
                        var discountPrice = http[0]["PRODUCT"]["PRICE"]["RESULT_PRICE"]["DISCOUNT"] > 0 ? http[0]["PRODUCT"]["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] : "";

                        $_mProduct.find(".price").append(
                            $("<s/>").addClass("discount").html(
                                discountPrice
                            )
                        );

                        if (http[0]["PRODUCT"]["COUNT_PRICES"] > 1) {
                            $_mProduct.find(".price").addClass("getPricesWindow").data("id", http[0]["PRODUCT"]["ID"]).prepend($("<span/>", {class: "priceIcon"})).attr("href", "#");
                        } else {
                            $_mProduct.find(".price").find(".priceIcon").remove();
                        }

                        var $changeAvailable = $_mProduct.find(".changeAvailable");

                        $changeAvailable.removeClass("getStoresWindow");
                        $changeAvailable.removeClass("outOfStock");
                        $changeAvailable.removeClass("onOrder");
                        $changeAvailable.removeClass("inStock");
                        $changeAvailable.removeAttr("href");


                        if (http[0]["PRODUCT"]["CATALOG_QUANTITY"] > 0) {
                            if (http[0]["PRODUCT"]["STORES_COUNT"] > 1) {
                                $changeAvailable.html($("<span/>").html(LANG["CATALOG_AVAILABLE"])).addClass("inStock").attr("href", "#").addClass("getStoresWindow").data("id", http[0]["PRODUCT"]["ID"]);
                                $changeAvailable.prepend(
                                    $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/inStock.png")
                                );

                            } else {
                                $changeAvailable.html(LANG["CATALOG_AVAILABLE"]).addClass("inStock");
                                $changeAvailable.prepend(
                                    $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/inStock.png")
                                );
                            }

                            if (http[0]["PRODUCT"]['DISPLAY_BUTTONS']['CART_BUTTON'] == true) {
                                $changeFastOrder.removeClass("disabled").show().data("id", http[0]["PRODUCT"]["ID"]);

                                // возврат кнопки "В корзину"
                                $_mProduct.find(".addCart").html("В корзину").prepend(
                                    $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/incart.png"))
                                    .attr("href", "#").removeClass("disabled").removeClass("label").removeClass("fastBack").addClass("addCart").addClass("addCart1").show();

                                $changeReserve.hide();
                                $changeFast.html("В корзину").prepend(
                                    $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/incart.png"))
                                    .attr("href", "#").removeClass("disabled").removeClass("label").removeClass("fastBack").addClass("addCart").addClass("addCart2").show();
                                /*$elPicture.append($("<span />", {class: "getFastOrder", id: "GTM_fastorder_catalog_get", 'data-id': http[0]["PRODUCT"]["ID"] }).data("id", http[0]["PRODUCT"]["ID"]).html('Быстрый заказ')); */
                            } else {
                                $(".qtyBlock").hide();
                                $changeFastOrder.addClass("disabled").hide();

                                if (http[0]["PRODUCT"]['SALON_COUNT'] > 0 && (http[0]["PRODUCT"]['DISPLAY_BUTTONS']['CART_BUTTON'] == true || http[0]["PRODUCT"]['DISPLAY_BUTTONS']['RESERV_BUTTON'] == true)) {
                                    $changeFast.hide();
                                    $changeCart.hide();
                                    $changeReserve.show();
                                } else {
                                    $changeCart.html("Под заказ").prepend(
                                        $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/incart.png"))
                                        .attr("href", "#").removeClass("addCart").addClass("fastBack label changeCart").show();
                                    $changeFast.html("Под заказ").attr("href", "#").removeClass("disabled").removeClass("label").removeClass("fastOrder").addClass("fastBack").show();


                                    //fast display
                                    $changeCart.addClass("disabled").hide();

                                }
                            }

                        } else {
                            if (http[0]["PRODUCT"]["CAN_BUY"] != "Y") {

                                $changeAvailable.html(LANG["CATALOG_NO_AVAILABLE"]).addClass("outOfStock");
                                //$changeFastBack.addClass("disabled");
                                //$changeFastOrder.addClass("disabled").hide();

                                if (http[0]["PRODUCT"]["CATALOG_SUBSCRIBE"] == "Y" && http[0]["PRODUCT"]["PRICE"]["DISCOUNT_PRICE"]) {
                                    $changeCart.html(LANG["ADD_SUBSCRIBE_LABEL"])
                                        .prepend($("<img />").attr({
                                            src: TEMPLATE_PATH + "/images/subscribe.png",
                                            class: "icon"
                                        }))
                                        .attr("href", "#").addClass("subscribe");
                                } else {
                                    //$changeCart.addClass("disabled");
                                    $(".qtyBlock").hide();
                                    //$changeFastOrder.addClass("disabled").hide();
                                    if (http[0]["PRODUCT"]['SALON_COUNT'] > 0 && http[0]["PRODUCT"]['DISPLAY_BUTTONS']['CART_BUTTON'] == true) {
                                        $changeReserve.show();
                                        $changeFast.hide();
                                        $changeCart.hide();
                                    } else {

                                        $changeReserve.hide();
                                        $changeFast.show();

                                        $changeFast.html("Под заказ").prepend(
                                            $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/incart.png"))
                                            .attr("href", "#").removeClass("fastOrder").addClass("fastBack label");
                                        $changeCart.html("Под заказ").prepend(
                                            $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/incart.png"))
                                            .attr("href", "#").removeClass("addCart").removeClass("fastOrder").addClass("fastBack label changeCart").show();


                                        //fast display
                                        $changeCart.addClass("disabled").hide();
                                        $changeFast.addClass("disabled").hide();
                                    }
                                }

                                $changeAvailable.prepend(
                                    $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/outOfStock.png")
                                );

                            } else {
                                $changeAvailable.html(LANG["CATALOG_ON_ORDER"]).addClass("onOrder");
                                $changeAvailable.prepend(
                                    $("<img/>").addClass("icon").attr("src", TEMPLATE_PATH + "/images/onOrder.png")
                                );
                            }
                        }
                    }

                    //addCart button reload
                    changeAddCartButton(basketProductsNow);
                    //subscribe button reload
                    //subscribeOnline();

                    $_clickedProp.removeClass("loading");
                    skuLoading = false;

                }).fail(function (jqxhr, textStatus, error) {
                $_clickedProp.removeClass("loading");
                skuLoading = false;
                alert("Request Failed: " + textStatus + ", " + error);
            });

            event.preventDefault();

        }

        var addSubscribe = function (event) {

            //j vars
            $body = $("body");
            $this = $(this);

            //vars
            productId = $this.data("id");

            //check id
            if (productId != "") {

                //loader
                $this.addClass("loading");

                //get subscribe window
                $.getJSON(ajaxPath + "?act=addSubscribe&id=" + productId + "&site_id=" + SITE_ID, function (jsonData) {

                    if (jsonData["SUCCESS"] == "Y") {

                        //metrica
                        if (typeof globalSettings != "undefined" && typeof globalSettings["TEMPLATE_METRICA_SUBSCRIBE"] != "undefined" && typeof globalSettings["TEMPLATE_METRICA_ID"] != "undefined" && typeof window["yaCounter" + globalSettings["TEMPLATE_METRICA_ID"]] != "undefined") {
                            window["yaCounter" + globalSettings["TEMPLATE_METRICA_ID"]].reachGoal(globalSettings["TEMPLATE_METRICA_SUBSCRIBE"]);
                        }

                        //show form
                        if (jsonData["SUBSCRIBE_FORM"] != "") {
                            $body.append(jsonData["SUBSCRIBE_FORM"]);
                            $this.removeClass("loading");
                        }

                    } else {
                        console.error(jsonData);
                    }

                });

            } else {
                //show error
                console.error("product id not found");

            }

            //block action
            return event.preventDefault();

        };

        var unSubscribe = function (event) {

            //j vars
            $this = $(this);
            $thisImage = $this.find("img");

            //vars
            subscribeId = $this.data("subscribe-id");

            //check id
            if (subscribeId != "") {

                //loader
                $this.addClass("loading");

                //get subscribe window
                $.getJSON(ajaxPath + "?act=unSubscribe&subscribeId=" + subscribeId + "&site_id=" + SITE_ID, function (jsonData) {

                    if (jsonData["SUCCESS"] == "Y") {
                        $this.data("subscribe-id", "").text(LANG["ADD_SUBSCRIBE_LABEL"]).prepend($thisImage.attr({
                            src: TEMPLATE_PATH + "/images/subscribe.png",
                        })).removeClass("unSubscribe");
                    } else {
                        console.error(jsonData);
                    }

                });

            } else {
                //show error
                console.error("product id not found");

            }
            return event.preventDefault();
        };

        var addCart = function (event) {

            var $this = $(this);

            $this.html("").prepend($("<img />").attr({src: TEMPLATE_PATH + "/images/loadcart.svg", class: "icon"}));

            var $is_fastview = $(this).hasClass('fast');
            var productID = $this.data("id");
            var quantity = $this.data("quantity");
            var windowDisplay = $this.data("display-window");
            var refreshPage = $this.data("refresh-page");
            var addedLabel = $this.data("cart-label");


            var _arID = [];

            if (!$this.hasClass("disabled") && !$this.hasClass("subscribe")) {

                if ($this.attr("href") === "#") {

                    //metrica
                    if (typeof globalSettings != "undefined" && typeof globalSettings["TEMPLATE_METRICA_ADD_CART"] != "undefined" && typeof globalSettings["TEMPLATE_METRICA_ID"] != "undefined" && typeof window["yaCounter" + globalSettings["TEMPLATE_METRICA_ID"]] != "undefined") {
                        window["yaCounter" + globalSettings["TEMPLATE_METRICA_ID"]].reachGoal(globalSettings["TEMPLATE_METRICA_ADD_CART"]);
                    }

                    if ($this.hasClass("multi")) {
                        if ($this.data("selector") != "" && $this.attr("href") === "#") {
                            $this.addClass("loading").text(LANG["ADD_CART_LOADING"]);
                            var $addElements = $($this.data("selector")).filter(":not(.disabled)");
                            var elementsQuantity = "";
                            if ($addElements.length > 0) {
                                $addElements.each(function (x, elx) {
                                    var $elx = $(elx);
                                    if ($elx.data("id") != "") {
                                        _arID[x] = $elx.data("id");
                                        if (parseFloat($elx.data("quantity")) != "") {
                                            elementsQuantity += $elx.data("id") + ":" + parseFloat($elx.data("quantity")) + ";";
                                        }
                                    }
                                });

                                if (_arID != "") {
                                    $.getJSON(ajaxPath + "?act=addCart&id=" + _arID.join(";") + "&q=" + elementsQuantity + "&multi=1&site_id=" + SITE_ID, function (data) {
                                        var $imageAfterLoad = $this.find("img");
                                        $this.text(LANG["ADDED_CART_SMALL"])
                                            .attr("href", SITE_DIR + "personal/cart/")
                                            .prepend($imageAfterLoad.attr("src", TEMPLATE_PATH + "/images/added.png"))
                                            .removeClass("loading")
                                            .addClass("added").attr("id", "GTM_go_cart_catalog");
                                        cartReload();
                                    });
                                } else {
                                    alert("error (5)");
                                }
                            } else {
                                alert("error(6)");
                            }
                            event.preventDefault();
                        }
                    } else {

                        if (parseInt(productID, 10) > 0) {

                            $this.addClass("loading");

                            var gObj = {
                                act: "addCart",
                                id: productID,
                                site_id: SITE_ID
                            };

                            if (quantity > 0) {
                                gObj["q"] = quantity;
                            } else {
                                quantity = 1;
                            }

                            $.getJSON(ajaxPath, gObj).done(function (jData) {

                                var reloadCart = cartReload();

                                //push window component
                                if (typeof (windowDisplay) == "undefined" || typeof (windowDisplay) != "undefined" && windowDisplay == "Y") {
                                    if (typeof jData["status"] != "undefined" && jData["status"] == true && typeof jData["window_component"] != "undefined" && jData["window_component"] != "") {
                                        if (!$is_fastview) {
                                            $body.append(jData["window_component"]);
                                        } else {
                                            refreshPage = 'Y';
                                            //remove
                                            $("#appFastView").remove();

                                        }
                                    }
                                }

                                //change add cart label
                                LANG["BASKET_ADDED"] = typeof (addedLabel) == "undefined" ? LANG["BASKET_ADDED"] : addedLabel;

                                var $imageAfterLoad = $this.find("img");

                                $(".bwOpened").removeClass("bwOpened");
                                lastAddCartText = $this.html();

                                $where_add = $this.attr("id");
                                $this.removeClass("loading")
                                    .addClass("added")
                                    .addClass("bwOpened")
                                    .html(LANG["BASKET_ADDED"])
                                    .prepend($imageAfterLoad.attr("src", TEMPLATE_PATH + "/images/added.png"))
                                    .attr("href", SITE_DIR + "personal/cart/").attr("id", "GTM_go_cart_catalog");


                                $addfromlist = '';
                                if ($where_add !== undefined) {
                                    $addfromlist = 'Каталог';
                                }
                                window.dataLayer = window.dataLayer || [];
                                console.log(jData["product"]);
                                dataLayer.push({
                                    'ecommerce': {
                                        'currencyCode': 'RUB',
                                        'add': {
                                            'actionField': {'list': $addfromlist},
                                            'products': [{
                                                'name': jData["product"]['NAME'],
                                                'id': productID,
                                                'price': parseInt(jData["product"]['PRICE']),
                                                'brand': jData["product"]['BRAND'],
                                                'category': jData["product"]['CATEGORY'],
                                                'variant': jData["product"]['CML2_ARTICLE'],
                                                'quantity': parseInt(quantity)
                                            }]
                                        }
                                    },
                                    'event': 'gtm-ee-event',
                                    'gtm-ee-event-category': 'Enhanced Ecommerce',
                                    'gtm-ee-event-action': 'Adding a Product to a Shopping Cart',
                                    'gtm-ee-event-non-interaction': 'False',
                                });

                                var prod_list = jData["product"]['CATEGORY'].split("/");


                                var _tmr = _tmr || [];
                                _tmr.push({
                                    type: "itemView",
                                    productid: productID,
                                    pagetype: "cart",
                                    totalvalue: parseInt(jData["product"]['PRICE']),
                                    list: "1"
                                });
                                _tmr.push({
                                    type: "itemView",
                                    productid: productID,
                                    pagetype: "cart",
                                    totalvalue: parseInt(jData["product"]['PRICE']),
                                    list: "102"
                                });
                                waitForFbq(function () {
                                    fbq('track', 'AddToCart', {
                                        content_type: 'product',
                                        contents: [{id: productID, quantity: 1}],
                                        value: jData["product"]['PRICE'],
                                        currency: 'RUB'
                                    });
                                });
                                var _tmr = window._tmr || (window._tmr = []);
                                _tmr.push({"type": "reachGoal", "id": 3206755, "goal": "addtocart"});
                                waitForVk(function () {
                                    VK.Goal('add_to_cart', {value: jData["product"]['PRICE']});
                                    const eventParams = {
                                        "products": [{'id': productID}],
                                        //"category_ids" : $section_id,
                                        //"business_value" : 88,
                                        "total_price": jData["product"]['PRICE']
                                    };
                                    VK.Retargeting.ProductEvent(PRICE_LIST_ID, 'add_to_cart', eventParams);
                                });
                                var _gcTracker = window._gcTracker || [];
                                _gcTracker.push(['add_to_card', {
                                    category_id: jData["product"]['SECTION_ID'],
                                    product_id: productID
                                }]);

                                var _rutarget = window._rutarget || [];
                                _rutarget.push({
                                    'event': 'addToCart',
                                    'sku': productID,
                                    'qty': 1,
                                    'price': jData["product"]['PRICE']
                                });


                                //reload page after add cart
                                if (typeof (refreshPage) != "undefined" && refreshPage == "Y") {
                                    document.location.reload();
                                    window.scrollTo(0, 0);

                                    showLoader();
                                }

                            }).fail(function (jqxhr, textStatus, error) {

                                $.get(ajaxPath, gObj).done(function (Data) {
                                    console.log(Data);
                                });

                                $this.html(LANG["ADD_BASKET_DEFAULT_LABEL"])
                                    .prepend($("<img />").attr({src: TEMPLATE_PATH + "/images/incart.png", class: "icon"}));

                                $this.removeClass("loading")
                                    .addClass("error");

                                console.error(
                                    "Request Failed: " + textStatus + ", " + error
                                );

                            });

                        }
                    }
                } else {
                    return true;
                }
            }

            return event.preventDefault();

        }

        var addCompare = function (event) {

            var $this = $(event.currentTarget);
            var $icon = $this.find("img");
            var productID = $this.data("id");

            if ($this.attr("href") == "#") {

                if (parseInt(productID, 10) > 0 && !$this.hasClass("added")) {

                    $this.addClass("loading");

                    var gObj = {
                        id: productID,
                        act: "addCompare"
                    };

                    $.get(ajaxPath, gObj).done(function (hData) {
                        if (hData != "") {
                            var reloadCart = cartReload();
                            if ($this.data("no-label") == "Y") {
                                $this.removeClass("loading")
                                    .addClass("added")
                                    .attr("href", SITE_DIR + "compare/");
                            } else {
                                $this.removeClass("loading")
                                    .addClass("added")
                                    .html(LANG["ADD_COMPARE_ADDED"])
                                    .prepend($icon)
                                    .attr("href", SITE_DIR + "compare/");
                            }
                        } else {
                            $this.removeClass("loading")
                                .addClass("error");
                        }
                    }).fail(function (jqxhr, textStatus, error) {

                        $this.removeClass("loading")
                            .addClass("error");

                        console.error(
                            "Request Failed: " + textStatus + ", " + error
                        );

                    });
                }

                return event.preventDefault();
            }
        };

        var addWishlist = function (event) {

            var $this = $(event.currentTarget);
            var $icon = $this.find("img");
            var productID = $this.data("id");

            if ($this.attr("href") == "#") {
                if (parseInt(productID, 10) > 0 && !$this.hasClass("added")) {

                    $this.addClass("loading");

                    var gObj = {
                        id: productID,
                        act: "addWishlist"
                    };

                    $.get(ajaxPath, gObj).done(function (hData) {
                        if (hData != "") {
                            var reloadCart = cartReload();
                            if ($this.data("no-label") == "Y") {
                                $this.removeClass("loading")
                                    .addClass("added")
                                    .attr("href", SITE_DIR + "wishlist/");
                            } else {
                                $this.removeClass("loading")
                                    .addClass("added")
                                    .html(LANG["WISHLIST_ADDED"])
                                    .prepend($icon)
                                    .attr("href", SITE_DIR + "wishlist/");
                            }
                        } else {
                            $this.removeClass("loading")
                                .addClass("error");
                        }
                    }).fail(function (jqxhr, textStatus, error) {

                        $this.removeClass("loading")
                            .addClass("error");

                        console.error(
                            "Request Failed: " + textStatus + ", " + error
                        );

                    });
                }

                return event.preventDefault();
            }
        };

        var openFastBack = function (event) {

            var $this = $(this);

            if (!$this.hasClass("disabled")) {

                var $appFastBuy = $("#appFastBuy");
                var $foundation = $("#foundation").addClass("blurred");

                $("#fastBuyOpenContainer").show();
                $("#fastBuyResult").hide();

                $("#fastBuyForm").find('input[type="text"], textarea').val("");

                var productID = $this.data("id");

                $this.addClass("loading");

                var gObj = {
                    id: productID,
                    act: "getFastBuy"
                };

                $.getJSON(ajaxPath, gObj).done(function (jData) {
                    $this.removeClass("loading");
                    $appFastBuy.find("#fastBuyPicture .url, #fastBuyName .url").attr("href", jData[0]["DETAIL_PAGE_URL"]);
                    $appFastBuy.find("#fastBuyPicture .picture").attr("src", $appFastBuy.data("load"));
                    $appFastBuy.find("#fastBuyPrice").html(jData[0]["PRICE"]["PRICE_FORMATED"]);
                    $appFastBuy.find("#fastBuyFormName").val(jData[0]["USER_NAME"]);
                    $appFastBuy.find("#fastBuyFormTelephone").val(jData[0]["USER_PHONE"]);
                    $appFastBuy.find("#fastBuyName .middle").html(jData[0]["NAME"]);
                    $appFastBuy.find("#fastBuyFormId").val(jData[0]["ID"]);
                    $appFastBuy.find(".markerContainer").remove();
                    if (jData[0]["MARKER"] != undefined) {

                        $appFastBuy.find("#fastBuyPicture").prepend(
                            $("<div>").addClass("markerContainer")
                                .append(
                                    jData[0]["MARKER"]
                                )
                        );
                    }

                    window.dataLayer = window.dataLayer || [];
                    dataLayer.push({
                        'ecommerce': {
                            'currencyCode': 'RUB',
                            'checkout': {
                                'actionField': {'step': 2},
                                'products': [{
                                    'name': jData[0]['ITEM']['name'],
                                    'id': jData[0]['ITEM']['id'],
                                    'price': jData[0]['ITEM']['price'],
                                    'brand': jData[0]['ITEM']['brand'],
                                    'category': jData[0]['ITEM']['category'],
                                    'variant': jData[0]['ITEM']['article'],
                                    'quantity': jData[0]['ITEM']['q']
                                }]
                            }
                        },
                        'event': 'gtm-ee-event',
                        'gtm-ee-event-category': 'Enhanced Ecommerce',
                        'gtm-ee-event-action': 'Checkout Step 2',
                        'gtm-ee-event-non-interaction': 'False',
                    });

                    var prod_list = jData[0]['ITEM']['category'].split("/");


                    var _tmr = _tmr || [];
                    _tmr.push({
                        type: "itemView",
                        productid: jData[0]['ID'],
                        pagetype: "cart",
                        totalvalue: parseInt(jData[0]['ITEM']['price']),
                        list: "1"
                    });
                    _tmr.push({
                        type: "itemView",
                        productid: jData[0]['ID'],
                        pagetype: "cart",
                        totalvalue: parseInt(jData[0]['ITEM']['price']),
                        list: "102"
                    });
                    fbq('track', 'InitiateCheckout', {
                        content_type: 'product',
                        content_ids: [jData[0]['ID']],
                        currency: 'RUB',
                        value: parseInt(jData[0]['ITEM']['price'])
                    });

                    dataLayer.push({
                        'event': 'crto_basketpage',
                        crto: {
                            'email': $nUserEmail,
                            'products': [
                                {
                                    'id': jData[0]['ITEM']['id'],
                                    'price': parseInt(jData[0]['ITEM']['price']),
                                    'quantity': 1
                                },
                            ]
                        }
                    });

                    $appFastBuy.show();

                    loadingPictureControl(jData[0]["PICTURE"]["src"], function () {
                        $appFastBuy.find("#fastBuyPicture .picture").attr("src", jData[0]["PICTURE"]["src"]);
                    });


                }).fail(function (jqxhr, textStatus, error) {

                    $.get(ajaxPath, gObj).done(function (Data) {
                        console.log(Data)
                    });

                    $this.removeClass("loading")
                        .addClass("error");

                    console.error(
                        "Request Failed: " + textStatus + ", " + error
                    );

                });

                fastBuyOpen = true;
            }

            return event.preventDefault();
        };

        var sendFastBack = function (event) {

            var $this = $(this).addClass("loading");
            var $fastBuyForm = $("#fastBuyForm");
            var $fastBuyFormName = $fastBuyForm.find("#fastBuyFormName").removeClass("error");
            var $fastBuyFormTelephone = $fastBuyForm.find("#fastBuyFormTelephone").removeClass("error");

            if ($fastBuyFormName.val() == "") {
                $fastBuyFormName.addClass("error");
            }

            if ($fastBuyFormTelephone.val() == "") {
                $fastBuyFormTelephone.addClass("error");
            }

            var $personalInfo = $fastBuyForm.find("#personalInfoFastBuy");
            if (!$personalInfo.prop("checked")) {
                $personalInfo.addClass("error");
            }

            if ($fastBuyFormName.val() != "" && $fastBuyFormTelephone.val() != "" && $personalInfo.prop("checked")) {

                $.getJSON(ajaxPath + "?" + $fastBuyForm.serialize()).done(function (jData) {

                    //metrica
                    if (typeof globalSettings != "undefined" && typeof globalSettings["TEMPLATE_METRICA_FAST_BUY"] != "undefined" && typeof globalSettings["TEMPLATE_METRICA_ID"] != "undefined" && typeof window["yaCounter" + globalSettings["TEMPLATE_METRICA_ID"]] != "undefined") {
                        window["yaCounter" + globalSettings["TEMPLATE_METRICA_ID"]].reachGoal(globalSettings["TEMPLATE_METRICA_FAST_BUY"]);
                    }

                    $("#fastBuyResultTitle").html(jData["heading"]);
                    $("#fastBuyResultMessage").html(jData["message"]);

                    window.dataLayer = window.dataLayer || [];
                    dataLayer.push({
                        'ecommerce': {
                            'currencyCode': 'RUB',
                            'purchase': {
                                'actionField': {
                                    'id': 'order' + jData['result_id'],
                                    'coupon': '',
                                    'affiliation': 'Заказное изделие',
                                    'revenue': jData['product']['price']
                                },
                                'products': [{
                                    'name': jData['product']['name'],
                                    'id': jData['product']['id'],
                                    'price': jData['product']['price'],
                                    'brand': jData['product']['brand'],
                                    'category': jData['product']['category'],
                                    'variant': jData['product']['article'],
                                    'quantity': jData['product']['q']
                                }]
                            }
                        },
                        'event': 'gtm-ee-event',
                        'gtm-ee-event-category': 'Enhanced Ecommerce',
                        'gtm-ee-event-action': 'Purchase',
                        'gtm-ee-event-non-interaction': 'False',
                    });

                    $("#fastBuyOpenContainer").hide();
                    $(".FastBuy-desc ").hide();
                    $("#fastBuyResult").show();

                    var prod_list = jData["product"]['category'].split("/");
                    $list = '';
                    if (prod_list[0] == 'ortopedicheskaya-obuv') {
                        $list = '3';
                    } else if (prod_list[0] == 'odezhda-dlya-sporta') {
                        $list = '4';
                    } else if (prod_list[0] == 'kompressionnyy-trikotazh') {
                        $list = '2';
                    }
                    var _tmr = _tmr || [];
                    _tmr.push({
                        type: "itemView",
                        productid: jData["product"]['id'],
                        pagetype: "purchase",
                        totalvalue: parseInt(jData["product"]['price']),
                        list: "1"
                    });
                    _tmr.push({
                        type: "itemView",
                        productid: jData["product"]['id'],
                        pagetype: "purchase",
                        totalvalue: parseInt(jData["product"]['price']),
                        list: "102"
                    });
                    var _tmr = window._tmr || (window._tmr = []);
                    _tmr.push({"type": "reachGoal", "id": 3206755, "goal": "purchase"});

                    ym(30121774, 'reachGoal', 'SEND_ORDER');


                    dataLayer.push({
                        'event': 'crto_transactionpage',
                        crto: {
                            'email': $nUserEmail,
                            'transactionid': jData['result_id'],
                            'products': [
                                {'id': jData['product']['id'], 'price': jData['product']['price'], 'quantity': 1},

                            ]
                        }
                    });

                    window.gdeslon_q = window.gdeslon_q || [];
                    window.gdeslon_q.push({
                        page_type: "thanks",
                        merchant_id: "104092",
                        order_id: jData['result_id'],
                        category_id: "",
                        products: [
                            {'id': jData['product']['id'], 'price': jData['product']['price'], 'quantity': 1},
                            {'id': jData['product']['gdeslon']['user'], 'price': jData['product']['price'], 'quantity': 1}
                        ],
                        deduplication: $.cookie('client_utm_source'),
                        user_id: jData['product']['gdeslon']['user_id']
                    });


                    fbq('track', 'Purchase', {
                        content_type: 'product',
                        content_ids: [jData['product']['id']],
                        currency: 'RUB',
                        value: jData['product']['price'],
                        num_items: 1
                    });

                    $this.removeClass("loading");

                }).fail(function (jqxhr, textStatus, error) {

                    $this.removeClass("loading").addClass("error");

                    console.error(
                        "Request Failed: " + textStatus + ", " + error
                    );

                });

            } else {
                $this.removeClass("loading");
            }

            return event.preventDefault();
        };

        var closeFastBack = function (event) {
            var $appFastBuy = $("#appFastBuy").hide();
            var $foundation = $("#foundation").removeClass("blurred");
            return event.preventDefault();
        };


        var openFastOrder = function (event) {

            var $this = $(this);

            if (!$this.hasClass("disabled")) {

                var $appFastOrder = $("#appFastOrder");
                var $foundation = $("#foundation").addClass("blurred");

                $("#FastOrderOpenContainer").show();
                $("#FastOrderResult").hide();

                $("#FastOrderForm").find('input[type="text"], textarea').val("");

                var productID = $this.data("id");

                $this.addClass("loading");

                var gObj = {
                    id: productID,
                    act: "getFastOrder"
                };

                $.getJSON(ajaxPath, gObj).done(function (jData) {
                    $this.removeClass("loading");
                    $appFastOrder.find("#FastOrderPicture .url, #FastOrderName .url").attr("href", jData[0]["DETAIL_PAGE_URL"]);
                    $appFastOrder.find("#FastOrderPicture .picture").attr("src", $appFastOrder.data("load"));
                    $appFastOrder.find("#FastOrderPrice").html(jData[0]["PRICE"]["PRICE_FORMATED"]);
                    $appFastOrder.find("#FastOrderFormName").val(jData[0]["USER_NAME"]);
                    $appFastOrder.find("#FastOrderFormTelephone").val(jData[0]["USER_PHONE"]);
                    $appFastOrder.find("#FastOrderName .middle").html(jData[0]["NAME"]);
                    $appFastOrder.find("#FastOrderFormId").val(jData[0]["ID"]);
                    $appFastOrder.find(".markerContainer").remove();
                    if (jData[0]["MARKER"] != undefined) {

                        $appFastOrder.find("#FastOrderPicture").prepend(
                            $("<div>").addClass("markerContainer")
                                .append(
                                    jData[0]["MARKER"]
                                )
                        );
                    }

                    window.dataLayer = window.dataLayer || [];
                    dataLayer.push({
                        'ecommerce': {
                            'currencyCode': 'RUB',
                            'checkout': {
                                'actionField': {'step': 2},
                                'products': [{
                                    'name': jData[0]['ITEM']['name'],
                                    'id': jData[0]['ID'],
                                    'price': jData[0]['ITEM']['price'],
                                    'brand': jData[0]['ITEM']['brand'],
                                    'category': jData[0]['ITEM']['category'],
                                    'variant': jData[0]['ITEM']['article'],
                                    'quantity': jData[0]['ITEM']['q']
                                }]
                            }
                        },
                        'event': 'gtm-ee-event',
                        'gtm-ee-event-category': 'Enhanced Ecommerce',
                        'gtm-ee-event-action': 'Checkout Step 2',
                        'gtm-ee-event-non-interaction': 'False',
                    });


                    var prod_list = jData[0]['ITEM']['category'].split("/");
                    $list = '';
                    if (prod_list[0] == 'ortopedicheskaya-obuv') {
                        $list = '3';
                    } else if (prod_list[0] == 'odezhda-dlya-sporta') {
                        $list = '4';
                    } else if (prod_list[0] == 'kompressionnyy-trikotazh') {
                        $list = '2';
                    }

                    var _tmr = _tmr || [];
                    _tmr.push({
                        type: "itemView",
                        productid: jData[0]['ID'],
                        pagetype: "cart",
                        totalvalue: parseInt(jData[0]['ITEM']['price']),
                        list: "1"
                    });
                    _tmr.push({
                        type: "itemView",
                        productid: jData[0]['ID'],
                        pagetype: "cart",
                        totalvalue: parseInt(jData[0]['ITEM']['price']),
                        list: "102"
                    });
                    var _tmr = window._tmr || (window._tmr = []);
                    _tmr.push({"type": "reachGoal", "id": 3206755, "goal": "addtocart"});

                    dataLayer.push({
                        'event': 'crto_basketpage',
                        crto: {
                            'email': $nUserEmail,
                            'products': [
                                {
                                    'id': jData[0]['ID'],
                                    'price': parseInt(jData[0]['ITEM']['price']),
                                    'quantity': 1
                                },
                            ]
                        }
                    });
                    //fbq('track', 'InitiateCheckout', {content_type: 'product', content_ids:[jData[0]['ID']], currency: 'RUB', value:  jData[0]['ITEM']['price'], num_items: 1});

                    $appFastOrder.show();

                    loadingPictureControl(jData[0]["PICTURE"]["src"], function () {
                        $appFastOrder.find("#FastOrderPicture .picture").attr("src", jData[0]["PICTURE"]["src"]);
                    });


                }).fail(function (jqxhr, textStatus, error) {

                    $.get(ajaxPath, gObj).done(function (Data) {
                        console.log(Data)
                    });

                    $this.removeClass("loading")
                        .addClass("error");

                    console.error(
                        "Request Failed: " + textStatus + ", " + error
                    );

                });

                fastOrderOpen = true;
            }

            return event.preventDefault();
        };

        var sendFastOrder = function (event) {

            var $this = $(this).addClass("loading");
            var $fastOrderForm = $("#FastOrderForm");
            var $fastOrderFormName = $fastOrderForm.find("#FastOrderFormName").removeClass("error");
            var $fastOrderFormTelephone = $fastOrderForm.find("#FastOrderFormTelephone").removeClass("error");
            var $error = 0;

            if ($fastOrderFormName.val() == "") {
                $fastOrderFormName.addClass("error");
                $error = 1;
            }

            $phoneNums = $fastOrderFormTelephone.val().replace(/(\W+)/g, "");
            if ($fastOrderFormTelephone.val() == "" || $phoneNums.length < 11) {
                $fastOrderFormTelephone.addClass("error");
                $error = 1;
            }

            var $personalInfo = $fastOrderForm.find("#personalInfoFastOrder");
            if (!$personalInfo.prop("checked")) {
                $personalInfo.addClass("error");
                $error = 1;
            }

            if ($error == 0) {

                $.getJSON(ajaxPath + "?" + $fastOrderForm.serialize()).done(function (jData) {

                    //metrica
                    if (typeof globalSettings != "undefined" && typeof globalSettings["TEMPLATE_METRICA_FAST_BUY"] != "undefined" && typeof globalSettings["TEMPLATE_METRICA_ID"] != "undefined" && typeof window["yaCounter" + globalSettings["TEMPLATE_METRICA_ID"]] != "undefined") {
                        window["yaCounter" + globalSettings["TEMPLATE_METRICA_ID"]].reachGoal(globalSettings["TEMPLATE_METRICA_FAST_BUY"]);
                    }

                    $("#FastOrderResultTitle").html(jData["heading"]);
                    $("#FastOrderResultMessage").html(jData["message"]);

                    if (jData['product']) {
                        window.dataLayer = window.dataLayer || [];
                        dataLayer.push({
                            'ecommerce': {
                                'currencyCode': 'RUB',
                                'purchase': {
                                    'actionField': {
                                        'id': 'order' + jData['result_id'],
                                        'coupon': '',
                                        'affiliation': 'Быстрый заказ',
                                        'revenue': jData['product']['price']
                                    },
                                    'products': [{
                                        'name': jData['product']['name'],
                                        'id': jData['product']['id'],
                                        'price': jData['product']['price'],
                                        'brand': jData['product']['brand'],
                                        'category': jData['product']['category'],
                                        'variant': jData['product']['article'],
                                        'quantity': jData['product']['q']
                                    }]
                                }
                            },
                            'event': 'gtm-ee-event',
                            'gtm-ee-event-category': 'Enhanced Ecommerce',
                            'gtm-ee-event-action': 'Purchase',
                            'gtm-ee-event-non-interaction': 'False',
                        });


                        dataLayer.push({
                            'event': 'crto_transactionpage',
                            crto: {
                                'email': $nUserEmail,
                                'transactionid': jData['result_id'],
                                'products': [
                                    {'id': jData['product']['id'], 'price': jData['product']['price'], 'quantity': 1},

                                ]
                            }
                        });

                        var _rutarget = window._rutarget || [];
                        _rutarget.push({
                            'event': 'buyNow',
                            'sku': jData['product']['id'],
                            'qty': 1,
                            'price': parseInt(jData["product"]['price'])
                        });


                        var prod_list = jData["product"]['category'].split("/");
                        $list = '';
                        if (prod_list[0] == 'ortopedicheskaya-obuv') {
                            $list = '3';
                        } else if (prod_list[0] == 'odezhda-dlya-sporta') {
                            $list = '4';
                        } else if (prod_list[0] == 'kompressionnyy-trikotazh') {
                            $list = '2';
                        }

                        var _tmr = _tmr || [];
                        _tmr.push({
                            type: "itemView",
                            productid: jData["product"]['id'],
                            pagetype: "purchase",
                            totalvalue: parseInt(jData["product"]['price']),
                            list: "1"
                        });
                        _tmr.push({
                            type: "itemView",
                            productid: jData["product"]['id'],
                            pagetype: "purchase",
                            totalvalue: parseInt(jData["product"]['price']),
                            list: "102"
                        });
                        VK.Goal('purchase');
                        var _tmr = window._tmr || (window._tmr = []);
                        _tmr.push({"type": "reachGoal", "id": 3206755, "goal": "purchase"});
                        const eventParams = {
                            "products": [{'id': jData["product"]['id']}],
                            //"category_ids" : $section_id,
                            //"business_value" : 88,
                            "total_price": parseInt(jData["product"]['price'])
                        };
                        VK.Retargeting.ProductEvent(PRICE_LIST_ID, 'purchase', eventParams);


                        window.gdeslon_q = window.gdeslon_q || [];
                        window.gdeslon_q.push({
                            page_type: "thanks",
                            merchant_id: "104092",
                            order_id: jData['result_id'],
                            category_id: "",
                            products: [
                                {'id': jData['product']['id'], 'price': jData['product']['price'], 'quantity': 1},
                                {
                                    'id': jData['product']['gdeslon']['user'],
                                    'price': jData['product']['price'],
                                    'quantity': 1
                                }
                            ],
                            deduplication: $.cookie('client_utm_source'),
                            user_id: jData['product']['gdeslon']['user_id']
                        });


                        ym(30121774, 'reachGoal', 'FAST_ORDER');

                        var _tmr = window._tmr || (window._tmr = []);
                        _tmr.push({"type": "reachGoal", "id": 3206755, "goal": "purchase"});
                    }
                    //fbq('track', 'Purchase', {content_type: 'product', content_ids:[jData["product"]['id']], currency: 'RUB', value:  parseInt(jData["product"]['price']), num_items: 1});

                    $("#FastOrderOpenContainer").hide();
                    $(".FastOrder-desc ").hide();
                    $("#FastOrderResult").show();

                    $this.removeClass("loading");

                }).fail(function (jqxhr, textStatus, error) {

                    $this.removeClass("loading").addClass("error");

                    console.error(
                        "Request Failed: " + textStatus + ", " + error
                    );

                });

            } else {
                $this.removeClass("loading");
            }

            return event.preventDefault();
        };

        var closeFastOrder = function (event) {
            var $appFastOrder = $("#appFastOrder").hide();
            var $foundation = $("#foundation").removeClass("blurred");
            return event.preventDefault();
        };


        var openSmpFastOrder = function (event) {

            var $this = $(this);

            if (!$this.hasClass("disabled")) {

                var $appSmpFastOrder = $("#appSmpFastOrder");
                var $foundation = $("#foundation").addClass("blurred");

                $("#SmpFastOrderOpenContainer").show();
                $("#SmpFastOrderResult").hide();

                $("#SmpFastOrderForm").find('input[type="text"], textarea').val("");

                var productID = $this.data("id");

                $this.addClass("loading");

                var gObj = {
                    id: productID,
                    act: "getSmpFastOrder"
                };

                $.getJSON(ajaxPath, gObj).done(function (jData) {
                    $this.removeClass("loading");
                    $appSmpFastOrder.find("#SmpFastOrderPicture .url, #SmpFastOrderName .url").attr("href", jData[0]["DETAIL_PAGE_URL"]);
                    $appSmpFastOrder.find("#SmpFastOrderPicture .picture").attr("src", $appSmpFastOrder.data("load"));
                    $appSmpFastOrder.find("#SmpFastOrderPrice").html(jData[0]["PRICE"]["PRICE_FORMATED"]);
                    $appSmpFastOrder.find(".Order_Author").html("Создатель заказа: " + jData[0]["USER_NAME"] + " " + jData[0]["USER_PHONE"]);
                    $appSmpFastOrder.find("#SmpFastOrderFormName").val("");
                    $appSmpFastOrder.find("#SmpFastOrderFormTelephone").val("");
                    $appSmpFastOrder.find("#SmpFastOrderName .middle").html(jData[0]["NAME"]);
                    $appSmpFastOrder.find("#SmpFastOrderFormId").val(jData[0]["ID"]);
                    $appSmpFastOrder.find(".markerContainer").remove();
                    if (jData[0]["MARKER"] != undefined) {

                        $appSmpFastOrder.find("#SmpFastOrderPicture").prepend(
                            $("<div>").addClass("markerContainer")
                                .append(
                                    jData[0]["MARKER"]
                                )
                        );
                    }


                    $appSmpFastOrder.show();

                    loadingPictureControl(jData[0]["PICTURE"]["src"], function () {
                        $appSmpFastOrder.find("#SmpFastOrderPicture .picture").attr("src", jData[0]["PICTURE"]["src"]);
                    });


                }).fail(function (jqxhr, textStatus, error) {

                    $.get(ajaxPath, gObj).done(function (Data) {
                        console.log(Data)
                    });

                    $this.removeClass("loading")
                        .addClass("error");

                    console.error(
                        "Request Failed: " + textStatus + ", " + error
                    );

                });

                SmpFastOrderOpen = true;
            }

            return event.preventDefault();
        };

        var sendSmpFastOrder = function (event) {

            var $this = $(this).addClass("loading");
            var $SmpFastOrderForm = $("#SmpFastOrderForm");
            var $SmpFastOrderFormName = $SmpFastOrderForm.find("#SmpFastOrderFormName").removeClass("error");
            var $SmpFastOrderFormTelephone = $SmpFastOrderForm.find("#SmpFastOrderFormTelephone").removeClass("error");
            var $error = 0;

            if ($SmpFastOrderFormName.val() == "") {
                $SmpFastOrderFormName.addClass("error");
                $error = 1;
            }

            $phoneNums = $SmpFastOrderFormTelephone.val().replace(/(\W+)/g, "");
            if ($SmpFastOrderFormTelephone.val() == "" || $phoneNums.length < 11) {
                $SmpFastOrderFormTelephone.addClass("error");
                $error = 1;
            }

            var $GPO = $SmpFastOrderForm.find("#GPOSmpFastOrder");


            if ($error == 0) {
                var formData = new FormData();
                formData.append('file', $("#SmpFastOrderFormFile")[0].files[0]);

                if ($("#SmpFastOrderFormFile")[0].files[0]) {
                    $.ajax({
                        type: "POST",
                        url: '/ajax.php?act=savefile',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        dataType: 'json',
                        success: function (msg) {
                            data = $SmpFastOrderForm.serialize();
                            data += '&file=' + msg.file.ID;


                            $.ajax({
                                method: "get",
                                dataType: "json",
                                url: ajaxPath + "?" + data
                            }).done(function (jData) {

                                //metrica
                                if (typeof globalSettings != "undefined" && typeof globalSettings["TEMPLATE_METRICA_FAST_BUY"] != "undefined" && typeof globalSettings["TEMPLATE_METRICA_ID"] != "undefined" && typeof window["yaCounter" + globalSettings["TEMPLATE_METRICA_ID"]] != "undefined") {
                                    window["yaCounter" + globalSettings["TEMPLATE_METRICA_ID"]].reachGoal(globalSettings["TEMPLATE_METRICA_FAST_BUY"]);
                                }

                                $("#SmpFastOrderResultTitle").html(jData["heading"]);
                                $("#SmpFastOrderResultMessage").html(jData["message"]);

                                //fbq('track', 'Purchase', {content_type: 'product', content_ids:[jData["product"]['id']], currency: 'RUB', value:  parseInt(jData["product"]['price']), num_items: 1});

                                $("#SmpFastOrderOpenContainer").hide();
                                $(".SmpFastOrder-desc ").hide();
                                $("#SmpFastOrderResult").show();

                                $this.removeClass("loading");

                            }).fail(function (jqxhr, textStatus, error) {

                                $this.removeClass("loading").addClass("error");

                                console.error(
                                    "Request Failed: " + textStatus + ", " + error
                                );
                            });
                        }
                    });
                } else {

                    $.getJSON(ajaxPath + "?" + $SmpFastOrderForm.serialize()).done(function (jData) {

                        //metrica
                        if (typeof globalSettings != "undefined" && typeof globalSettings["TEMPLATE_METRICA_FAST_BUY"] != "undefined" && typeof globalSettings["TEMPLATE_METRICA_ID"] != "undefined" && typeof window["yaCounter" + globalSettings["TEMPLATE_METRICA_ID"]] != "undefined") {
                            window["yaCounter" + globalSettings["TEMPLATE_METRICA_ID"]].reachGoal(globalSettings["TEMPLATE_METRICA_FAST_BUY"]);
                        }

                        $("#SmpFastOrderResultTitle").html(jData["heading"]);
                        $("#SmpFastOrderResultMessage").html(jData["message"]);

                        //fbq('track', 'Purchase', {content_type: 'product', content_ids:[jData["product"]['id']], currency: 'RUB', value:  parseInt(jData["product"]['price']), num_items: 1});

                        $("#SmpFastOrderOpenContainer").hide();
                        $(".SmpFastOrder-desc ").hide();
                        $("#SmpFastOrderResult").show();

                        $this.removeClass("loading");

                    }).fail(function (jqxhr, textStatus, error) {

                        $this.removeClass("loading").addClass("error");

                        console.error(
                            "Request Failed: " + textStatus + ", " + error
                        );

                    });
                }

            } else {
                $this.removeClass("loading");
            }

            return event.preventDefault();
        };

        var closeSmpFastOrder = function (event) {
            var $appSmpFastOrder = $("#appSmpFastOrder").hide();
            var $foundation = $("#foundation").removeClass("blurred");
            return event.preventDefault();
        };


        var removeFromWishlist = function (event) {

            var $this = $(this);
            var $wishlist = $("#wishlist");
            var $parentThis = $(this).parents(".item");
            var productID = $this.data("id");
            $this.addClass("loading");

            var gObj = {
                id: productID,
                act: "removeWishlist"
            };

            $.get(ajaxPath, gObj).done(function (hData) {
                if (hData != "") {
                    if ($wishlist.find(".product, .itemRow").length == 1) {
                        window.location.reload();
                    } else {
                        reloadCart = cartReload();
                        $parentThis.remove();
                    }
                } else {
                    $this.removeClass("loading")
                        .addClass("error");
                }
            }).fail(function (jqxhr, textStatus, error) {

                $this.removeClass("loading")
                    .addClass("error");

                console.error(
                    "Request Failed: " + textStatus + ", " + error
                );

            });

            return event.preventDefault();

        };

        var slideCollapsedBlock = function (event) {
            var $collapsed = $("#left").children(".collapsed");
            if (!$collapsed.is(":visible") || $collapsed.hasClass("toggled")) {
                $collapsed.stop().slideToggle().addClass("toggled");
                return event.preventDefault();
            }
        };

        var openSmartFiler = function (event) {
            $smartFilterForm = $("#smartFilterForm");
            if ($(window).width() > 1024) {
                if ($smartFilterForm.is(":visible")) {
                    $smartFilterForm.stop().slideUp("fast");
                } else {
                    $smartFilterForm.stop().slideDown("fast");
                }
            }
        };

        var openSmartSections = function (event) {
            $smartSections = $("#nextSection ul");
            if ($smartSections.is(":visible")) {
                $smartSections.stop().slideUp("fast");
            } else {
                $smartSections.stop().slideDown("fast");
            }
        };

        var formatPrice = function (data) {
            var price = String(data).split('.');
            var strLen = price[0].length;
            var str = "";

            for (var i = strLen; i > 0; i--) {
                str = str + ((!(i % 3) ? " " : "") + price[0][strLen - i]);
            }

            return str + (price[1] != undefined ? "." + price[1] : "");
        }

//extented prices
        var catalogReCalcPrice = function ($qtyBox, currentQuantity) {
            if (currentQuantity > 0) {

                //price
                var $priceContainer = $qtyBox.parents(".item").find(".price");
                var $priceValContainer = $priceContainer.find(".priceVal");

                //check for empty
                if ($priceValContainer.length > 0) {
                    var $priceContainerStr = $priceValContainer.html().replace(/\d\.\d/g, '').replace(/[0-9]/g, '');
                }

                //discount
                var $discountContainer = $priceContainer.find(".discount");
                if ($discountContainer.length > 0) {
                    var $discountContainerStr = $discountContainer.html().replace(/\d\.\d/g, '').replace(/[0-9]/g, '');
                }

                //get price object
                var obExtentedPrices = $qtyBox.data("extended-price");

                if (typeof obExtentedPrices != "undefined") {
                    if (typeof obExtentedPrices == "string") {
                        obExtentedPrices = $.parseJSON(obExtentedPrices);
                    }
                }

                //check for empty object
                if (typeof obExtentedPrices == "object") {

                    //each prices
                    $.each(obExtentedPrices, function (index, nextValue) {

                        //check for empty quantity
                        if (nextValue["QUANTITY_FROM"] != null || nextValue["QUANTITY_TO"] != null) {

                            //check for current quantity
                            if ((nextValue["QUANTITY_FROM"] == null || nextValue["QUANTITY_FROM"] != "" && currentQuantity >= nextValue["QUANTITY_FROM"]) && (nextValue["QUANTITY_TO"] == null || nextValue["QUANTITY_TO"] != "" && currentQuantity <= nextValue["QUANTITY_TO"])) {

                                //write price
                                if (typeof nextValue["DISCOUNT_PRICE"] != "undefined") {
                                    $priceValContainer.html(formatPrice(Number(nextValue["DISCOUNT_PRICE"]).toFixed(0)) + $priceContainerStr);
                                }

                                //write discount
                                if (typeof nextValue["OLD_PRICE"] != "undefined") {
                                    $discountContainer.html(formatPrice(Number(nextValue["OLD_PRICE"]).toFixed(0)) + $discountContainerStr);
                                }

                            }
                        }
                    });
                }

            }
            return;
        };

        var catalogAddCartPlus = function (event) {

            var $this = $(this);
            var $qtyBox = $this.siblings(".catalogQtyBlock .catalogQty");
            var $addCartBtn = $this.parent().siblings(".addCart");

            var xCurrentQtyValue = Number($qtyBox.val());
            var xQtyStep = Number($qtyBox.data("step"));
            var xQtyExpression = Number((xCurrentQtyValue * 10 + xQtyStep * 10) / 10); //js magic .9999999

            var _enableTrace = $qtyBox.data("enable-trace");
            var _maxQuantity = Number($qtyBox.data("max-quantity"));

            var __qtyError = false;
            var xTmpExpression = 0;

            if (_enableTrace == "Y") {

                xTmpExpression = xQtyExpression;
                xQtyExpression = (xQtyExpression > _maxQuantity) ? _maxQuantity : xQtyExpression;

                if (xTmpExpression != xQtyExpression) {
                    __qtyError = true;
                }

            }

            $qtyBox.val(xQtyExpression);
            $addCartBtn.data("quantity", xQtyExpression);

            //extented prices
            catalogReCalcPrice($qtyBox, xQtyExpression);

            //set or remove error
            __qtyError === true ? $qtyBox.addClass("error") : $qtyBox.removeClass("error");

            return event.preventDefault();

        };

        var catalogAddCartMinus = function (event) {

            var $this = $(this);
            var $qtyBox = $this.siblings(".catalogQtyBlock .catalogQty");
            var $addCartBtn = $this.parent().siblings(".addCart");

            var xCurrentQtyValue = Number($qtyBox.val());
            var xQtyStep = Number($qtyBox.data("step"));
            var xQtyExpression = Number((xCurrentQtyValue * 10 - xQtyStep * 10) / 10); //js magic .9999999

            var _enableTrace = $qtyBox.data("enable-trace");
            var _maxQuantity = Number($qtyBox.data("max-quantity"));

            var __qtyError = false;
            var xTmpExpression = 0;

            xQtyExpression = xQtyExpression > xQtyStep ? xQtyExpression : xQtyStep;

            if (_enableTrace == "Y") {

                xTmpExpression = xQtyExpression;
                xQtyExpression = (xQtyExpression > _maxQuantity) ? _maxQuantity : xQtyExpression;

                if (xTmpExpression != xQtyExpression) {
                    __qtyError = true;
                }

            }

            $qtyBox.val(xQtyExpression);
            $addCartBtn.data("quantity", xQtyExpression);

            //extented prices
            catalogReCalcPrice($qtyBox, xQtyExpression);

            //set or remove error
            __qtyError === true ? $qtyBox.addClass("error") : $qtyBox.removeClass("error");

            return event.preventDefault();

        };

        var catalogAddCartChange = function (event) {

            var $this = $(this);
            var $addCartBtn = $this.parent().siblings(".addCart");

            var xCurrentQtyValue = $this.val();
            var xQtyStep = Number($this.data("step"));

            var _enableTrace = $this.data("enable-trace");
            var _maxQuantity = Number($this.data("max-quantity"));

            var __qtyError = false;
            var xTmpExpression = 0;

            if (xCurrentQtyValue.replace(/[^\d.]/gi, '') != xCurrentQtyValue) {
                xCurrentQtyValue = xQtyStep;
            } else {
                xCurrentQtyValue = Number(xCurrentQtyValue);
            }

            xQtyExpression = Math.ceil(xCurrentQtyValue / xQtyStep) * xQtyStep;

            if (_enableTrace == "Y") {

                xTmpExpression = xQtyExpression;
                xQtyExpression = (xQtyExpression > _maxQuantity) ? _maxQuantity : xQtyExpression;

                if (xTmpExpression != xQtyExpression) {
                    __qtyError = true;
                }

            }

            $this.val(xQtyExpression);
            $addCartBtn.data("quantity", xQtyExpression);

            //extented prices
            catalogReCalcPrice($qtyBox, xQtyExpression);

            //set or remove error
            __qtyError === true ? $this.addClass("error") : $this.removeClass("error");

        };

        var closeElementsAfterClick = function (event) {

            if (fastBuyOpen === true) {
                $("#appFastBuy").hide();
                $("#foundation").removeClass("blurred");
                fastBuyOpen = false;
            }
            if (fastOrderOpen === true) {
                $("#appFastOrder").hide();
                $("#foundation").removeClass("blurred");
                fastOrderOpen = false;
            }
            if (SmpFastOrderOpen === true) {
                $("#appSmpFastOrder").hide();
                $("#foundation").removeClass("blurred");
                SmpFastOrderOpen = false;
            }

            if (fastViewOpen === true) {
                $("#appFastView").remove();
                fastViewOpen = false;
            }

            if (fastViewStoresOpen === true) {
                $("#fastViewStores").remove();
                fastViewStoresOpen = false;
            }

            if (priceVariantOpen === true) {
                $("#appProductPriceVariant").remove();
                priceVariantOpen = false;
            }

            if (requestPriceOpen === true) {
                $("#foundation").removeClass("blurred");
                $("#requestPrice").hide();
                requestPriceOpen = false;
            }

        };

        $(document).on("click", "#footerTabsCaption .item", function (event) {
            $(this).find("a").addClass("selected");
            $(this).siblings(".item").find("a").removeClass("selected");
            $("#footerTabs").find(".tab").hide().eq($(this).index()).show();
            event.stopImmediatePropagation();
            return event.preventDefault();
        });

        $(document).on("click", "#infoTabsCaption .item", function (event) {
            $(this).find("a").addClass("selected");
            $(this).siblings(".item").find("a").removeClass("selected");
            $("#infoTabs").find(".tab").hide().eq($(this).index()).show();
            return event.preventDefault();
        });

//check checkbox by class name on label
        $(".label-class").on("click", function () {

            var $this = $(this);
            var $cTarget = $this.attr("for");
            var $parentForm = $this.parents("form");
            var $cCheckBox = $parentForm.find("." + $cTarget);

            if ($cCheckBox.prop("checked")) {
                $cCheckBox.prop("checked", false).focus();
            } else {
                $cCheckBox.prop("checked", "checked").focus();
            }

            return event.preventDefault();

        });

//check checkbox by data-for label
        $(".label-for").on("click", function () {

            var $this = $(this);
            var $cTarget = $this.data("for");
            var $parentForm = $this.parents("form");
            var $cCheckBox = $parentForm.find("." + $cTarget);

            if ($cCheckBox.prop("checked")) {
                $cCheckBox.prop("checked", false).focus();
            } else {
                $cCheckBox.prop("checked", "checked").focus();
            }

            return event.preventDefault();

        });

        var openSkuDropDown = function (event) {

            //vars
            var $this = $(this);
            var $dropList = $this.siblings(".oSkuDropdownList");

            //show list
            $dropList.toggleClass("opened");

            //opened flag
            oSkuDropdownOpened = $dropList.hasClass("opened");

            return event.preventDefault();

        };

        var selectSkuDropDownValue = function (event) {

            //vars
            var $this = $(this);
            var $dropList = $this.parents(".oSkuDropdownList");
            var $dropListItems = $dropList.find(".oSkuDropdownListItem").removeClass("selected");
            var $checkedItem = $dropList.siblings(".oSkuCheckedItem");

            if (!$checkedItem.hasClass("noHideChecked")) {

                //hide list
                $dropList.removeClass("opened");

                //opened flag
                oSkuDropdownOpened = false;

            }

            //active
            $this.addClass("selected");

            //write value
            $checkedItem.html($this.text());

            //
            return event.preventDefault();

        };

        var closeSkuDropDown = function (event) {

            //if opened
            if (oSkuDropdownOpened) {
                //block trigger events
                if (typeof event.isTrigger == "undefined") {
                    //close
                    $(".oSkuDropdownList").removeClass("opened");

                    //opened flag
                    oSkuDropdownOpened = false;
                }
            }

        };

//fix top menu
        if ($topMenuContainer.length > 0 && $topMenuContainer.hasClass("auto-fixed")) {

            //vars
            var scrollUnbind = false;
            var topMenuFixed = false;
            var menuStartOffset = 0;
            var menuStartHeight = 0;
            var menuStartbackground = 0;

            //check disable flag
            if (typeof _topMenuNoFixed == "undefined") {

                //functions
                var calcPosFixTopMenu = function (event) {

                    //to default
                    $topMenuContainer.removeClass("fixed");
                    $mainMenuStaticContainer.css("height", "auto");

                    //get offset & css value
                    menuStartOffset = $topMenuContainer.offset().top;
                    menuStartHeight = $topMenuContainer.height();
                    menuStartbackground = $topMenuContainer.css("background-color");

                    //set flag
                    topMenuFixed = false;

                    //unbind
                    if ($window.width() <= 1024) {

                        //bind off
                        $window.off("scroll", scrollFixTopMenu);

                        //set flag
                        scrollUnbind = true;

                    } else {

                        //check flag
                        if (scrollUnbind) {

                            //bind on
                            $window.on("scroll", scrollFixTopMenu);

                            //set flag
                            scrollUnbind = false;

                        }

                        //check
                        scrollFixTopMenu();

                    }

                };

                var scrollFixTopMenu = function (event) {

                    //vars
                    var currentScrollPosition = typeof event != "undefined" ? event.currentTarget.scrollY : $window.scrollTop();

                    if (currentScrollPosition >= menuStartOffset) {
                        if (!topMenuFixed) {

                            //set clases and css values
                            $topMenuContainer.addClass("fixed");
                            $mainMenuStaticContainer.css("height", $topMenuContainer.height() + "px");
                            $mainMenuStaticContainer.css("background-color", menuStartbackground);

                            //set flag
                            topMenuFixed = true;
                        }
                    } else {
                        //set clases and css values
                        $topMenuContainer.removeClass("fixed");
                        //set flag
                        topMenuFixed = false;
                    }

                };

                //binds
                $window.on("resize", calcPosFixTopMenu);
                $window.on("scroll", scrollFixTopMenu);

                //
                calcPosFixTopMenu();

            }

        }

//lazy load pictures
        checkLazyItems();

//other binds
        $(document).on("click", ".oSkuDropDownProperty .oSkuCheckedItem", openSkuDropDown);
        $(document).on("click", ".oSkuDropDownProperty .oSkuDropdownListItem", selectSkuDropDownValue);
        $(document).on("click", ".oSkuDropdown", function (event) {
            event.stopImmediatePropagation()
        });
        $(document).on("click", closeSkuDropDown);


        $(document).on("click", ".catalogQtyBlock .catalogPlus", catalogAddCartPlus);
        $(document).on("click", ".catalogQtyBlock .catalogMinus", catalogAddCartMinus);
        $(document).on("change", ".catalogQtyBlock .catalogQty", catalogAddCartChange);

        $(document).on("click", ".skuPropertyLink", selectSku);
        $(document).on("click", ".subscribe:not(.unSubscribe)", addSubscribe);
        $(document).on("click", ".unSubscribe", unSubscribe);
        $(document).on("click", ".addCart, .add-cart", addCart);

        $(document).on("click", ".addWishlist", addWishlist);
        $(document).on("click", ".addCompare", addCompare);
        $(document).on("click", ".fastBack", openFastBack);

        $(document).on("click", ".SmpFastOrder", openSmpFastOrder);

        $(document).on("click", ".fastOrder", openFastOrder);
        $(document).on("click", ".fast-order", openFastOrder);
        $(document).on("click", ".requestPrice", getRequestPrice);
        $(document).on("click", "#requestPriceSubmit", sendRequestPrice);
        $(document).on("click", "#GTM_ordering_order", sendFastBack);
        $(document).on("click", ".send_fastorder", sendFastOrder);
        $(document).on("click", ".send_SmpFastOrder", sendSmpFastOrder);

        $(document).on("click", "#appFastBuy .closeWindow", closeFastBack);
        $(document).on("click", "#appFastOrder .closeWindow", closeFastOrder);
        $(document).on("click", "#appSmpFastOrder .closeWindow", closeSmpFastOrder);

        $(document).on("click", "#requestPrice .closeWindow", closeRequestPrice);
        $(document).on("click", ".removeFromWishlist", removeFromWishlist);

        $(document).on("click", "#fastViewStores .fastViewStoresExit", closeStoresWindow);
        $(document).on("click", ".getStoresWindow", getStoresWindow);

        $(document).on("click", "#fastViewSizechart .fastViewSizechartExit", closeSizechartWindow);
        $(document).on("click", ".getSizechartWindow", getSizechartWindow);

// NOTE medi_popup bind
        $(document).on("click", ".medi_popup .medi_popup_Exit", close_medi_popup_Window);
        $(document).on("click", ".get_medi_popup_Window", get_medi_popup_Window);

        $(document).on("click", "#appProductPriceVariant .appPriceVariantExit", closePricesWindow);
        $(document).on("click", ".getPricesWindow", getPricesWindow);

        $(document).on("click", ".getFastOrder", openFastOrder);
        $(document).on("click", ".getSmpFastOrder", openSmpFastOrder);
        $(document).on("click", ".getFastView", getFastView);

        $(document).on("click", "#catalogMenuHeading", slideCollapsedBlock);
        $(document).on("click", "#smartFilter .heading", openSmartFiler);
        $(document).on("click", "#nextSection .title", openSmartSections);

        $(document).on("click", "#appFastView .appFastViewContainer, #fastViewStores .fastViewStoresContainer, #appProductPriceVariant, #appFastBuyContainer,#appFastOrderContainer, .getFastView, .getPricesWindow, .fastBack, .fastOrder, .addCart, #requestPriceContainer, .requestPrice", function (event) {
            return event.stopImmediatePropagation();
        });

//close elements after document click
        $(document).on("click", closeElementsAfterClick);

// ajax all error;

        $(document).ajaxError(function (event, request, settings) {
            console.error("Error requesting page " + settings.url);
        });

    }
)
;

var formatPrice = function (data) {
    var price = String(data).split('.');
    var strLen = price[0].length;
    var str = "";

    for (var i = strLen; i > 0; i--) {
        str = str + ((!(i % 3) ? " " : "") + price[0][strLen - i]);
    }

    return str + (price[1] != undefined ? "." + price[1] : "");
}

function validateEmail(sEmail) {

    //vars
    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;

    //check
    if (filter.test(sEmail)) {
        return true;
    } else {
        return false;
    }

}
