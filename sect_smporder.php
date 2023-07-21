<?
//include module
\Bitrix\Main\Loader::includeModule("dw.deluxe");

//get template settings
$arTemplateSettings = DwSettings::getInstance()->getCurrentSettings();

//check settings
if (!empty($arTemplateSettings)) {
    
    //set params
    $arParams["USE_MASKED_INPUT"] = !empty($arTemplateSettings["TEMPLATE_USE_MASKED_INPUT"]) ? $arTemplateSettings["TEMPLATE_USE_MASKED_INPUT"] : "N";
    
    //get masked input format
    if ($arParams["USE_MASKED_INPUT"] == "Y") {
        $arParams["MASKED_INPUT_FORMAT"] = !empty($arTemplateSettings["TEMPLATE_MASKED_INPUT_CUSTOM_FORMAT"]) ? $arTemplateSettings["TEMPLATE_MASKED_INPUT_CUSTOM_FORMAT"] : $arTemplateSettings["TEMPLATE_MASKED_INPUT_FORMAT"];
    }
    
} ?>
<div id="appSmpFastOrder" data-load="<?= SITE_TEMPLATE_PATH ?>/images/picLoad.gif">
    <div id="appSmpFastOrderContainer">
        <div class="heading">Заказ <a href="#" class="close closeWindow"></a></div>

        <div class="container" id="SmpFastOrderOpenContainer">
            <div class="column">
                <div id="SmpFastOrderPicture">
                    <a href="#" class="url"><img src="<?= SITE_TEMPLATE_PATH ?>/images/picLoad.gif"
                                                 alt="" class="picture"></a>
                </div>
                <div id="SmpFastOrderName"><a href="" class="name url"><span class="middle"></span></a></div>
                <div id="SmpFastOrderPrice" class="price"></div>
            </div>
            <div class="column" style="    height: 450px;overflow: auto">
                <form action="<?= SITE_DIR ?>callback/" id="SmpFastOrderForm" method="GET"
                      enctype="multipart/form-data">
                    <input name="id" type="hidden" id="SmpFastOrderFormId" value="">
                    <input name="act" type="hidden" id="SmpFastOrderFormAct" value="SmpFastOrder">
                    <input name="author" type="hidden" class="Order_Author" value=""/>
                    <input name="SITE_ID" type="hidden" id="SmpFastOrderFormSiteId" value="<?= SITE_ID ?>">
                    <div class="formLine">Укажите контактную информацию клиента:</div>
                    <div class="formLine">
                        <input name="name" type="text" placeholder="ФИО Клиента*" value="" id="SmpFastOrderFormName">
                    </div>
                    <div class="formLine">
                        <input name="phone" type="tel" placeholder="Телефон клиента*" value=""
                               id="SmpFastOrderFormTelephone"/>
                    </div>
                    <div class="formLine">
                        <input name="doctor" type="text" placeholder="Врач" value="" id="SmpFastOrderFormDoctor">
                    </div>
                    <div class="formLine">
                        <input name="file" type="file" placeholder="Документ к заказу" id="SmpFastOrderFormFile"
                               style="width: 95%;padding: 9px 0 9px;"/>
                    </div>
                    <div class="formLine">
                        <input type="checkbox" name="GPOSmpFastOrder" id="GPOSmpFastOrder">
                        <label for="GPOSmpFastOrder">Выезд специалиста ГПО</label>
                    </div>
                    <div class="formLine">
                        <textarea name="message" cols="30" rows="10" placeholder="Комментарий к заказу"
                                  id="SmpFastOrderFormMessage"></textarea>
                    </div>
                    <div class="formLine">
                        <input type="checkbox" name="urgentOrder" id="smpUrgentOrderOrder">
                        <label for="smpUrgentOrderOrder">Срочный заказ</a></label>
                    </div>
                    <div class="formLine"><a href="#" class="send_SmpFastOrder" id="GTM_SmpFastOrder_card_send">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/incart.png" alt="Заказать">Заказать</a>
                    </div>
                </form>
            </div>
        </div>
        <div id="SmpFastOrderResult">
            <div id="SmpFastOrderResultTitle"></div>
            <div id="SmpFastOrderResultMessage"></div>
            <a href="" id="SmpFastOrderResultClose" class="closeWindow">Закрыть окно</a>
        </div>
        <? if (!empty($arParams["USE_MASKED_INPUT"])): ?>
            <script>
                $(function () {
                    var delFirstEight = {
                        onKeyPress: function (val, e, field, options) {

                            if (val.replace(/\D/g, '').length === 2) {
                                val = val.replace('8', '');
                                field.val(val);
                            }
                            field.mask("+7 (999) 999-99-99", options);
                        },
                        placeholder: "+7 (___) ___-__-__"
                    };

                    // phone mask
                    $("#SmpFastOrderFormTelephone").mask("<?=$arParams["MASKED_INPUT_FORMAT"]?>", delFirstEight);
                });
            </script>
        <? endif; ?>
    </div>
</div>
