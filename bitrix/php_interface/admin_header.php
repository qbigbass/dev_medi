<?

$userID = $USER->GetID();
$userName = $USER->GetFullName();

?>
<script type="text/javascript" src="//yastatic.net/jquery/2.1.3/jquery.min.js"></script>
<script src="/bitrix/templates/dresscodeV2/js/jquery.mask.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@19.7.1/dist/css/suggestions.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/suggestions-jquery@19.7.1/dist/js/jquery.suggestions.min.js"></script>

<script>
    $.fn.setCursorPosition = function (pos) {
        if ($(this).get(0).setSelectionRange) {
            $(this).get(0).setSelectionRange(pos, pos);
        } else if ($(this).get(0).createTextRange) {
            var range = $(this).get(0).createTextRange();
            range.collapse(true);
            range.moveEnd('character', pos);
            range.moveStart('character', pos);
            range.select();
        }
    };


    $(document).ready(function () {

        $("input[name='PROPERTIES[6]']").on("change", function () {
            $status = $("#STATUS_ID").val();
            $zip = this.value;
            if (this.value.length == 10) {
                $.ajax({
                    url: '/local/ajax_cp.php',
                    data: {
                        action: "getZip",
                        lid: $zip,
                    },
                    method: 'POST',
                    success: function (data) {
                        var x = JSON.parse(data);
                        if (x.length == 6) {
                            if ($("input[name='PROPERTIES[4]']").val() != x && ($status == "N" || $status == "MN" || $status == "W" || $status == "H" || $status == "Q")) {
                                $("input[name='PROPERTIES[4]']").val(x);
                            }
                        }
                    },
                    failure: function () {
                    }
                });
            }
        });

        if ($("#sale_order_create_form #STATUS_ID").length) {
            $("#sale_order_create_form #STATUS_ID").on("change", function () {
                alert("Создавать заказ можно только в статусе Новый.");
                $(this).val("N");
                return;
            });
        }
        if ($("#tab_order_edit_table #STATUS_ID").length) {
            $("select[name='PROPERTIES[43]']").parentsUntil("table").hide();
            $("textarea[name='PROPERTIES[44]']").parentsUntil("table").hide();

            if ($("#tab_order_edit_table #STATUS_ID").val() == 'Y' || $("#tab_order_edit_table #STATUS_ID").val() == 'K') {
                $("select[name='PROPERTIES[43]']").parentsUntil("table").show();
                $("textarea[name='PROPERTIES[44]']").parentsUntil("table").show();
                //$("select[name='PROPERTIES[43]']").prop('required','true');
            } else {
                //$("select[name='PROPERTIES[43]']").prop('required','');
            }

            $("#tab_order_edit_table #STATUS_ID").on("change", function () {
                if ($("#tab_order_edit_table #STATUS_ID").val() == 'Y' || $("#tab_order_edit_table #STATUS_ID").val() == 'K') {
                    $("textarea[name='PROPERTIES[44]']").focus();
                    //$("select[name='PROPERTIES[43]']").prop('required','true');
                    $("select[name='PROPERTIES[43]']").parentsUntil("table").show();
                    $("textarea[name='PROPERTIES[44]']").parentsUntil("table").show();
                } else {
                    //$("select[name='PROPERTIES[43]']").prop('required','');
                    $("select[name='PROPERTIES[43]']").parentsUntil("table").hide();
                    $("textarea[name='PROPERTIES[44]']").parentsUntil("table").hide();
                }
            });

            $("select[name='PROPERTIES[43]']").on("change", function () {
                if ($(this).val() == '3' ||
                    $(this).val() == '6' ||
                    $(this).val() == '13' ||
                    $(this).val() == '15' ||
                    $(this).val() == '18') {
                    $("textarea[name='PROPERTIES[44]']").focus();
                } else {
                    //$("textarea[name='PROPERTIES[44]']").prop('required', '');
                }
            });
        }

        if ($("input[name='PROPERTIES[3]']").length) {
            $("input[name='PROPERTIES[3]']").mask("~0000000000", {
                'translation': {"~": {pattern: /[7|8]/}},
                placeholder: "7__________"
            });
            $("input[name='PROPERTIES[3]']").on("keyup", function () {
                $val = $(this).val();
                if ($val.length == 11) {
                    $fix = $val.replace(/\^8/, "7");
                    $(this).val($fix);
                }
            });
        }


        if ($("input[name='PROPERTIES[1]']").length) {

            $suggestions = $("input[name='PROPERTIES[1]']").suggestions({
                token: "3bf699bebe24b4c22576fce52726c0adec68917b",
                type: "NAME",
                minChars: 3,
            });
        }

        if ($("input[name='PROPERTIES[2]']").length) {

            $suggestions = $("input[name='PROPERTIES[2]']").suggestions({
                token: "3bf699bebe24b4c22576fce52726c0adec68917b",
                type: "EMAIL",
                minChars: 2,
            });
            if ($("input[name='PROPERTIES[2]']").val() == '') {
                $("input[name='PROPERTIES[2]']").parent().append("<b style='color:red'>Чеки только в электронном виде, фиксируем почту, для автоматической отправки чека при курьерской доставке!</b>");
            }
        }

        if ($("input[name='PROPERTIES[6]']").length) {

            $suggestions = $("input[name='PROPERTIES[7]']").suggestions({
                token: "3bf699bebe24b4c22576fce52726c0adec68917b",
                type: "ADDRESS",
                geoLocation: false,
                minChars: 3,
                restrict_value: true,
                onSelect: function (sugg) {
                    if (sugg.data.street_with_type !== null) {
                        $("input[name='PROPERTIES[20]']").val(sugg.data.street_with_type);
                    }
                    /*if (sugg.data.house !== null)
                    {
                        $block = '';
                        if (sugg.data.block !== null)
                        {
                            $block = ' ' + sugg.data.block_type + ' ' + sugg.data.block;
                        }
                        $("input[name='PROPERTIES[21]']").val(sugg.data.house + $block);
                    }
                    if (sugg.data.flat !== null)
                    {
                        $("input[name='PROPERTIES[24]']").val(sugg.data.flat);
                    } */
                    if (sugg.data.postal_code !== null) {
                        $("input[name='PROPERTIES[4]']").val(sugg.data.postal_code);
                    }
                }
            });

            $restrict_val = '';

            $("input[name='PROPERTIES[6]']").on("change", function () {
                $loc = $("input[name='PROPERTIES[6]']").val();

                $.ajax({
                    url: '/local/ajax_cp.php',
                    data: {
                        action: "getLocation",
                        lid: $loc,
                    },
                    method: 'POST',
                    success: function (data) {
                        $suggestions.suggestions().setOptions({
                            constraints: {
                                locations: {city: data},
                                deletable: true
                            }
                        });
                    }
                });


            });

        }

        if ($("input[name='PROPERTIES[23]']").length) {
            $inp = $("input[name='PROPERTIES[23]']");
            $inp.attr("readonly", "true");

            $("input[name='PROPERTIES[23]'] + input[type='button']").after("<input type='button' value='Очистить дату' id='clear_date_but' style='margin: 0px 10px;'>");
            $("#clear_date_but").bind("click", function () {
                $inp.val("");
            });
        }


        if ($("input[name='PROPERTIES[15]']").length) {

            $("select[name='PROPERTIES[48]']").parent("td").parent("tr").hide(); // Тип Не Рц
            $("input[name='PROPERTIES[49]']").parent("td").parent("tr").hide();  // Коммент к Не Рц
            $("input[name='PROPERTIES[16]']").parent("td").parent("tr").hide();
            $("textarea[name='PROPERTIES[17]']").parent("td").parent("tr").hide();

            if ($("input[name='PROPERTIES[15]']:checked").val() != "NO") {
                $("select[name='PROPERTIES[48]']").parent("td").parent("tr").hide(); // Тип Не Рц
                $("input[name='PROPERTIES[49]']").parent("td").parent("tr").hide();  // Коммент к Не Рц
            } else {
                $("input[name='PROPERTIES[16]']").parent("td").parent("tr").hide();
                $("textarea[name='PROPERTIES[17]']").parent("td").parent("tr").hide();
            }

            if ($("input[name='PROPERTIES[15]']:checked").val() == "YES") {
                $("select[name='PROPERTIES[48]']").parent("td").parent("tr").hide();
                $("input[name='PROPERTIES[49]']").parent("td").parent("tr").hide();

                $("input[name='PROPERTIES[16]']").parent("td").parent("tr").show();
                $("textarea[name='PROPERTIES[17]']").parent("td").parent("tr").show();
            }

            if ($("input[name='PROPERTIES[15]']:checked").val() == "NO") {
                $("select[name='PROPERTIES[48]']").parent("td").parent("tr").show();
                $("input[name='PROPERTIES[49]']").parent("td").parent("tr").show();

                $("input[name='PROPERTIES[16]']").parent("td").parent("tr").hide();
                $("textarea[name='PROPERTIES[17]']").parent("td").parent("tr").hide();
            }

            if ($("input[name='PROPERTIES[15]']:checked").val() == "NO_ANSWER") {
                $("select[name='PROPERTIES[48]']").parent("td").parent("tr").hide();
                $("input[name='PROPERTIES[49]']").parent("td").parent("tr").hide();

                $("input[name='PROPERTIES[16]']").parent("td").parent("tr").hide();
                $("textarea[name='PROPERTIES[17]']").parent("td").parent("tr").hide();
            }

            $("input[name='PROPERTIES[15]']").on("change", function () {
                if ($(this).val() == 'NO') {
                    $("select[name='PROPERTIES[48]']").parent("td").parent("tr").show();

                    if ($("select[name='PROPERTIES[48]']").val() == 'OTHER') {
                        $("input[name='PROPERTIES[49]']").parent("td").parent("tr").show();
                    }

                    $("input[name='PROPERTIES[16]']").parent("td").parent("tr").hide();
                    $("textarea[name='PROPERTIES[17]']").parent("td").parent("tr").hide();
                } else if ($(this).val() == 'NO_ANSWER') {

                    $("select[name='PROPERTIES[48]']").parent("td").parent("tr").hide();
                    $("input[name='PROPERTIES[49]']").parent("td").parent("tr").hide();

                    $("input[name='PROPERTIES[16]']").parent("td").parent("tr").hide();
                    $("textarea[name='PROPERTIES[17]']").parent("td").parent("tr").hide();
                } else {
                    $("select[name='PROPERTIES[48]']").parent("td").parent("tr").hide(); // Тип Не Рц
                    $("input[name='PROPERTIES[49]']").parent("td").parent("tr").hide();  // Коммент к Не Рц

                    $("input[name='PROPERTIES[16]']").parent("td").parent("tr").show();
                    $("textarea[name='PROPERTIES[17]']").parent("td").parent("tr").show();

                }
            });
            $("select[name='PROPERTIES[48]']").on("change", function () {
                if ($(this).val() == 'OTHER') {
                    $("input[name='PROPERTIES[49]']").parent("td").parent("tr").show();
                } else {

                    $("input[name='PROPERTIES[49]']").parent("td").parent("tr").hide();
                }
            });

        }


        if ($("#tab_order_edit_table").length) {
            setTimeout(function () {
                if ($("#store_name1")) {
                    $("#SHIPMENT_SECTION_SHORT_1 .adm-shipment-block-short-info  > .adm-detail-content-cell-l:nth-child(2)").append("<br>" + $("#store_name1").html());
                }
            }, 2000);

        }

        if ($("#tbl_sale_order_footer").length) {
            $("#tbl_sale_order_footer .adm-selectall-wrap").html("").hide();
        }

        if ($(".bx-adm-promocode-container").length) {

            $(".bx-adm-promocode-container").append("<select name='choseDiscount' id='choseDiscount'><option value=''>выбрать из списка</option><optgroup label='Москва'><option value='DISCOUNT4'>DISCOUNT4</option><option value='DISCOUNT5'>DISCOUNT5</option><option value='DISCOUNT7'>DISCOUNT7</option><option value='DISCOUNT8'>DISCOUNT8</option><option value='DISCOUNT10'>DISCOUNT10</option><option value='DISCOUNT15'>DISCOUNT15</option><option value='DISCOUNT20'>DISCOUNT20</option><option value='DISCOUNT25'>DISCOUNT25</option><option value='DISCOUNT30'>DISCOUNT30</option><option value='DISCOUNT40'>DISCOUNT40</option></optgroup><optgroup label='Россия'><option value='DISCOUNT_4RU'>DISCOUNT_4RU</option><option value='DISCOUNT_5RU'>DISCOUNT_5RU</option><option value='DISCOUNT_7RU'>DISCOUNT_7RU</option><option value='DISCOUNT_8RU'>DISCOUNT_8RU</option><option value='DISCOUNT_10RU'>DISCOUNT_10RU</option><option value='DISCOUNT_15RU'>DISCOUNT_15RU</option><option value='DISCOUNT_20RU'>DISCOUNT_20RU</option><option value='DISCOUNT_25RU'>DISCOUNT_25RU</option><option value='DISCOUNT_30RU'>DISCOUNT_30RU</option><option value='DISCOUNT_40RU'>DISCOUNT_40RU</option></optgroup></select>");
            $("#choseDiscount").bind("change", function () {
                $val = $(this).val();
                $("#sale-admin-order-coupons").val($val);
            });
        }
    });

    function mediCourierist($ORDERID) {
        var mediCourieristDialog = new BX.CDialog({
            title: 'Отправка заявки на забор и доставку заказа',
            content_url: '/local/ajax_cp.php',
            content_post: 'action=courieristOrderForm&window=openWindow&orderID=' + $ORDERID,
            width: 750,
            min_width: 400,
            height: 400,
            min_height: 200,
            buttons: [
                //BX.CDialog.prototype.btnSave,
                BX.CDialog.prototype.btnClose
            ]
        });
        mediCourieristDialog.Show();
    }

</script>
<style>
    #sale_order_basketsale_order_edit_product_table tbody tr td table tr td span,
    #sale_order_basketsale_order_edit_product_table tbody tr td table tr td div,
    #sale_order_basketsale_order_view_product_table tbody tr td table tr td span,
    #sale_order_basketsale_order_view_product_table tbody tr td table tr td div {
        font-size: 12px !important;
        font-weight: bold;
    }
</style>
<? /*
<!-- Yandex.Metrika counter --> <script type="text/javascript" > (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)}; m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)}) (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym"); ym(86910735, "init", { clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true, trackHash:true }); </script> <noscript><div><img src="https://mc.yandex.ru/watch/86910735" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->
<?global $USER;
 $nUserID = $USER->GetID();

    $nUserName = $USER->GetFullName();
    $nUserEmail = $USER->GetEmail();
?>

<script>
function waitForYm(callback){
    if(typeof ym !== 'undefined'){
        callback()
    } else {
        setTimeout(function () {
            waitForYm(callback)
        }, 100)
    }
}

waitForYm(function(){
ym(86910735, 'userParams', {
    UserID: <?=$nUserID?>,
    UserEmail: '<?=$nUserEmail?>'
});
});
</script>
*/ ?>
