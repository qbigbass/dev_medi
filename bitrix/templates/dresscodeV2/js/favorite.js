$(document).ready(function() {
    $('#catalogElement').on('click', '.b-card-favorite', function (){
        let productId = $(this).attr('data-product-id');
        let doAction = '';

        if ($(this).hasClass('active')) {
            doAction = 'delete';
        } else {
            doAction = 'add';
        }

        addFavorite(productId, doAction);
    });

    function addFavorite(productId, action) {
        let param = 'id='+productId+"&action="+action;

        $.ajax({
            url: '/ajax/favorite/',
            type: 'GET',
            dataType: 'html',
            data: param,
            success: function (response) {
                let result = $.parseJSON(response);
                let wishCount = 1;

                if (result == 1) {
                    $('.b-card-favorite[data-product-id="'+productId+'"]').addClass('active');
                    let currentCount = parseInt($('.favorites_link .count').html());

                    if (currentCount >= 0) {
                        wishCount = currentCount + 1;
                    }

                    $('.favorites_link .count').html(wishCount);
                    if (!$('.favorites_link').hasClass('has_items')) {
                        $('.favorites_link').addClass('has_items');
                    }
                }

                if (result == 2) {
                    $('.b-card-favorite[data-product-id="'+productId+'"]').removeClass('active');
                    wishCount = parseInt($('.favorites_link .count').html()) - 1;

                    if (wishCount == 0) {
                        $('.favorites_link .count').html('');
                        $('.favorites_link').removeClass('has_items');
                    } else {
                        $('.favorites_link .count').html(wishCount);
                    }
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('Error: '+ errorThrown);
            }
        });
    }
});
