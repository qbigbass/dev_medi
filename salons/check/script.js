$(document).ready(function () {
    if ($("#has_recipe:checked").length) {
        $(".has_recipe").show();
    } else {
        $(".has_recipe").hide();
    }
    if ($("#aviability").val() != 1) {
        $("#precheck_send").attr("disabled", "disabled").off("submit").on("click", function () {
            $("span.not_available").addClass("btn-bordered").slideUp(100).slideDown(100);
        });
    }
    $(".mtz_item").on('click', function () {
        itemid = $(this).data('cons-id');
        if (itemid) {
            $(".itemcons" + itemid).show().focus();
        }
    });

    $("#has_recipe").on("change", function () {
        if ($("#has_recipe:checked").length) {
            $(".has_recipe").show();
        } else {
            $(".has_recipe").hide();
        }
    });

    $("#client_mtz").on("change", function () {
        if (parseInt($(this).val()) >= 10000) {
            $(this).addClass("error").focus();
            hideLoader();
            return false;
        } else {
            $(this).removeClass("error");
        }
    });

    $(".save_field").on("blur change", function () {
        $form = document.querySelector('#precheck_form');

        var formData = new FormData($form);
        $.ajax({
            url: "/ajax/salon/precheck/?action=save",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            type: "POST",
            dataType: "json",
            complete: function () {
            }
        });
    });
    $("#precheck_form").on("submit", function () {

        showLoader();

        if ($("#has_recipe:checked").length) {
            $comment = $("#client_comment");
            if ($comment.val().length < 5) {
                $comment.addClass("error").focus();
                hideLoader();
                return false;
            } else {
                $comment.removeClass("error");
            }
        }
        if (parseInt($("#client_mtz").val()) >= 10000) {
            $("#client_mtz").addClass("error").focus();
            hideLoader();
            return false;
        } else {
            $(this).removeClass("error");
        }

        var formData = new FormData(this);
        $.ajax({
            url: "/ajax/salon/precheck/?action=precheck",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            enctype: "multipart/form-data",
            type: "POST",
            dataType: "json",
            success: function (response) {
                if (response.status == 'success') {
                    $(".precheck_page").html("");
                    $(".precheck_page").html("<div class='result_block'><h2>Запрос успешно отправлен.</h2><br/>" +
                        "Номер вашего предчека: " + response['result']['ShoppingCartNumber'] + "<br/><br/>" +
                        "<a href='/catalog/'>перейти в каталог</a><br/><br/></div>");
                } else {

                    $(".precheck_page").html("");
                    $(".precheck_page").html("<div class='result_block'><h2>Предчек не отправлен. Попробуйте еще раз.</h2><br/><br/><a href=''>обновить страницу</a></div>");
                }
                hideLoader();
            },
            complete: function () {

                hideLoader();
            }
        });
        return false;
    });
});