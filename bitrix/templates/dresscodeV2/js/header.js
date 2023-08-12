$(function() {
    let headerHeight = $('header').height();
    let windowInnerWidth = window.innerWidth;
    
    if (windowInnerWidth >= 1024) {
        $(window).scroll(function() {
            if ($(this).scrollTop() > headerHeight) {
                if (!$('body').hasClass('fixed-header')) {
                    $('body').addClass('fixed-header');

                    $('header #subHeader8').animate({
                        height: "90px",
                    }, 100, function() {
                        $(this).css('background', '#fff');
                        $(this).css('borderBottom', '0');
                    });

                    $('header .b_logo img').animate({
                        height: "90px"
                    }, 500);

                    $('header .b_head_main').animate({
                        marginTop: "-100px"
                    }, 500);
                }
            } else {
                if ($('body').hasClass('fixed-header')) {
                    $('header .b_head_main').animate({
                        marginTop: "0"
                    }, 500);

                    $('header .b_logo img').animate({
                        height: "130px"
                    }, 500);

                    $('header #subHeader8').animate({
                        height: "140px",
                    }, 500, function() {
                        $(this).css('background', '#fff url(/bitrix/templates/dresscodeV2/headers/header8/css/images/top_bg.png)top center repeat-x');
                        $(this).css('borderBottom', '1px solid #f3f3f3;');
                    });
                    $('body').removeClass('fixed-header');
                }
            }
        });
    }
});