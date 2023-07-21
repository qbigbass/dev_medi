;
$(document).ready(function() {
    $('.medi-new-select').click( function() {
        $(this).siblings(".medi-new-select__list").slideToggle("slow");
        return false;
    });
    $('.medi-select').each(function() {
        const _this = $(this),
            selectOption = _this.find('option'),
            selectOptionLength = selectOption.length,
            selectedOption = selectOption.filter(':selected'),
            duration = 450; // длительность анимации

        _this.hide();
        _this.wrap('<div class="medi-select"></div>');
        $('<div>', {
            class: 'medi-new-select',
            text: _this.children('option:selected').text(),
        }).insertAfter(_this);

        const selectHead = _this.next('.medi-new-select');
        $('<div>', {
            class: 'medi-new-select__list',
            name: 'sortFields',
            id: 'selectSortParams',
        }).insertAfter(selectHead);

        const selectList = selectHead.next('.medi-new-select__list');
        for (let i = 0; i < selectOptionLength; i++) {
            $('<div>', {
                class: 'medi-new-select__item',
                html: $('<span>', {
                    text: selectOption.eq(i).text()
                })
            })
                .attr('data-value', selectOption.eq(i).val())
                .appendTo(selectList);
        }

        const selectItem = selectList.find('.medi-new-select__item');
        selectList.slideUp(0);
        selectHead.on('click', function() {
            if ( !$(this).hasClass('on') ) {
                $(this).addClass('on');
                selectList.slideDown(duration);

                selectItem.on('click', function() {
                    let chooseItem = $(this).data('value');

                    $('medi-select').val(chooseItem).attr('selected', 'selected');
                    selectHead.text( $(this).find('span').text() );

                    selectList.slideUp(duration);
                    selectHead.removeClass('on');
                });

            } else {
                $(this).removeClass('on');
                selectList.slideUp(duration);
            }
        });

        var changeSortParams = function(){
            window.location.href = $(this).data("value");
        };

        $("#selectSortParams .medi-new-select__item").on("click", changeSortParams);

        $(document).click( function(e){
            if ( $(e.target).closest('.medi-new-select').length ) {
                // клик внутри элемента
                return;
            }
            // клик снаружи элемента
            selectList.slideUp(duration);
            selectHead.removeClass('on');
        });
    });
});