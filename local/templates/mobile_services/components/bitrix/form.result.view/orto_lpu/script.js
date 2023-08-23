$(function () {
    $('.order-form').on('click', '.order-form__submit-button', function () {
        var link = $(this).attr('data-edit-url');
        if (link !== undefined && link !== '') {
            document.location.href = link;
        }
    });

    $('.order-form').on('click', '.cta--print', function () {
        window.print();
    });
});