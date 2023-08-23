$(function() {
    // добавление товара в корзину
    $('.add_cart').on('submit', function() {

        var data = $(this).serialize();

        $.ajax({
            url: '/local/ajax/product.php',
            type: 'POST',
            data: data,
            success: function(res) {
                BX.onCustomEvent('OnBasketChange');

                $('.add_product_wrap').fadeIn();
            },
            error: function(res) {
                alert('alert');
            },
        });

        return false;
    });


// показываем блок добавления товара
    $(document).mouseup(function (e) {
        var container = $(".add_product_wrap");
        if (container.has(e.target).length === 0){
            container.fadeOut();
        }
    });
});







$(document).ready(function() {
    /* Favorites */
    $('.like-icon').on('click', function(e) {
        var favorID = $(this).attr('data-item');

        if($(this).hasClass('check'))
            var doAction = 'delete';
        else
            var doAction = 'add';

        addFavorite(favorID, doAction);
    });
    /* Favorites */
});
/* Избранное */
function addFavorite(id, action)
{
    var param = 'id='+id+"&action="+action;
    $.ajax({
        url:     '/local/ajax/favorites.php', // URL отправки запроса
        type:     "GET",
        dataType: "html",
        data: param,
        success: function(response) { // Если Данные отправлены успешно
            var result = $.parseJSON(response);
            if(result == 1){ // Если всё чётко, то выполняем действия, которые показывают, что данные отправлены :)
                $('.like-icon[data-item="'+id+'"]').addClass('check');
                $('.like-black-icon[data-item="'+id+'"]').addClass('check');
            }
            if(result == 2){
                $('.like-icon[data-item="'+id+'"]').removeClass('check');
                $('.like-black-icon[data-item="'+id+'"]').removeClass('check');
            }

            // console.log(response);
        },
        error: function(jqXHR, textStatus, errorThrown){ // Если ошибка, то выкладываем печаль в консоль
            console.log('Error: '+ errorThrown);
        }
    });
}
/* Избранное */


$(document).ready(function() {
    $('.popup-youtube').magnificPopup({
        type: 'iframe'
    });
});



$(function() {
    $('#one_click_feedback .btn-prim').click(function() {

        function validName(name) {
            if(name.val() === '') {
                name.addClass('error');
                $('.error-name').show();
                return false;
            } else {
                name.removeClass('error');
                $('.error-name').hide();
                return true;
            }
        }


        function validPhone(phone) {
            let result = false;

            if (phone.val() != '') {
                let phoneMatch = phone.val().match(/\d/g).length;

                if(phoneMatch === 11){
                    phone.removeClass('error');
                    $('.error-phone').hide();
                    result = true;
                } else {
                    phone.addClass('error');
                    $('.error-phone').show().text('Неверный формат.');
                }
            } else {
                phone.addClass('error');
                $('.error-phone').show('Поле телефона не заполнено.');
            }

            return result;
        }



        validName($('input#name'));
        validPhone($('#one_click_feedback input#phone'));


        if (validName($('input#name')) && validPhone($('#one_click_feedback input#phone'))) {

            $.ajax({
                type: "POST",
                url: "/local/ajax/order_phone.php",
                data: {
                    name : $('input#name').val(),
                    phone : $('#one_click_feedback input#phone').val(),
                    product_name : $('input#product_name').val(),
                    product_price : $('input#product_price').val(),
                }
            }).done(function(data) {
                if (data == 'ok') {
                    $('#one_click_feedback .btn-prim').text('Ваш заказ оформлен');
                    $('#one_click_feedback .text').text('Менеджер скоро позвонит вам.');

                    setTimeout(function() {
                        // Done Functions
                        location.href=location.href;
                    }, 3500);
                }
            });
        }

    });
});
$(document).ready(function(){
    $('form[name="SIMPLE_FORM_1"] .btn-prim').click(function(){
        function validName(name) {
            if(name.val() === '') {
                name.addClass('error');
                console.log(name.next());
                name.next().show();
                return false;
            } else {
                name.removeClass('error');
                name.next().hide();
                return true;
            }
        }
        $('form[name="PREORDER"] .btn-prim').click(function(){
            function validName(name) {
                if(name.val() === '') {
                    name.addClass('error');
                    console.log(name.next());
                    name.next().show();
                    return false;
                } else {
                    name.removeClass('error');
                    name.next().hide();
                    return true;
                }
            }
            function validPhone(phone) {
                let result = false;

                if (phone.val() != '') {
                    let phoneMatch = phone.val().match(/\d/g).length;

                    if(phoneMatch === 11){
                        phone.removeClass('error');
                        phone.next().hide();
                        result = true;
                    } else {
                        phone.addClass('error');
                        phone.next().show().text('Неверный формат.');
                    }
                } else {
                    phone.addClass('error');
                    phone.next().show('Поле телефона не заполнено.');
                }

                return result;
            }
            function validEmail(email) {
                if(email.val() === '') {
                    email.addClass('error');
                    email.next().show();
                    return false;
                } else {
                    email.removeClass('error');
                    email.next().hide();
                    return true;
                }
            }
            validName($('input[name="form_text_2"]'));
            validPhone($('input[name="form_text_4"]'));
            validEmail($('input[name="form_email_6"]'));

            if (validName($('input[name="form_text_2"]')) && validPhone($('input[name="form_text_4"]')) && validEmail($('input[name="form_email_6"]'))){
            }else{
                return false;
            }
        });
    });


    $(document).ready(function() {
        document.querySelector('.view-all').addEventListener('click', function (e) {
            e.preventDefault()
            if (window.matchMedia("(max-width: 991px)").matches)
            {
                $('html, body').animate({
                    scrollTop: $('#two').offset().top - 60,
                }, function() {
                    $('#cTwo').addClass('show');
                    $('#two button').removeClass('collapsed');
                });
            } else {
                $('html, body').animate({
                    scrollTop: $('#productTab').offset().top - 60,
                }, function() {
                    $('#two-tab').click();
                });
                // document.querySelector('#two-tab').scrollIntoView(true);
                // document.querySelector('#two-tab').click();
            }
        });

    });
});