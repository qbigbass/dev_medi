$(function () {
    $('.order-form__submit').on('click', '.order-form__submit-button', function () {
        var link = $(this).attr('data-edit-url');
        if (link !== undefined && link !== '') {
            document.location.href = link;
        }
    });


    autosize($('textarea'));

    $('.order-form__submit').on('click', '.cta--print', function () {
        window.print();
    });
});
