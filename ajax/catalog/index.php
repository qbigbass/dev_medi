<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


$action = '';
if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], array('reserve', 'reserve_send', 'order_delivery2salon', 'ESHOP_ORDER2SALON_send', 'time'))) {
    $action = strval($_REQUEST['action']);
} else
    die();

if ($action == 'time') {


    echo time();
}
elseif ($action == 'order_delivery2salon') {

    // Форма бронирования товара в салоне
    $APPLICATION->IncludeComponent(
        "bitrix:form.result.new",
        "order2salon",
        array(
            "AJAX_MODE" => "N",
            "AJAX_OPTION_ADDITIONAL" => "",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "CACHE_TIME" => "3600",
            "CACHE_TYPE" => "N",
            "CHAIN_ITEM_LINK" => "",
            "CHAIN_ITEM_TEXT" => "",
            "COMPOSITE_FRAME_MODE" => "A",
            "COMPOSITE_FRAME_TYPE" => "AUTO",
            "EDIT_ADDITIONAL" => "N",
            "EDIT_STATUS" => "N",
            "IGNORE_CUSTOM_TEMPLATE" => "N",
            "NOT_SHOW_FILTER" => array(
                0 => "",
                1 => "",
            ),
            "NOT_SHOW_TABLE" => array(
                0 => "",
                1 => "",
            ),
            "RESULT_ID" => $_REQUEST[RESULT_ID],
            "SEF_MODE" => "N",
            "SHOW_ADDITIONAL" => "N",
            "SHOW_ANSWER_VALUE" => "Y",
            "SHOW_EDIT_PAGE" => "N",
            "SHOW_LIST_PAGE" => "N",
            "SHOW_STATUS" => "N",
            "SHOW_VIEW_PAGE" => "N",
            "START_PAGE" => "new",
            "SUCCESS_URL" => "",
            "HIDDEN_FIELDS" => ['SKU', 'AGREE'],
            "OFFER_ID" => $_REQUEST['p'],
            "USE_EXTENDED_ERRORS" => "Y",
            "WEB_FORM_ID" => "5",
            "COMPONENT_TEMPLATE" => "",
            "VARIABLE_ALIASES" => array(
                "action" => "action",
            )
        ),
        false
    );

}
elseif ($action == 'ESHOP_ORDER2SALON_send')
{
	CModule::IncludeModule("form");

	$arValues = array(
		"form_text_23"       => $_REQUEST['sname'],
		"form_text_24"       => $_REQUEST['sphone'],
		"form_text_26"       => $_REQUEST['sprod'],
		"form_text_25"       => $_REQUEST['sstore'],
		"form_text_28"       => $_REQUEST['scard'],
		"form_text_27"       => $_REQUEST['saddress'],
		"form_checkbox_AGREE"       => array(29),
	);

    if ($RESULT_ID = CFormResult::Add(5, $arValues)) {

        // Привязка заявки к городу по статусу
        if (SITE_ID != 's1')
        {
            $status_id = 12;
            switch (SITE_ID) {
                case "s2":
                    $status_id = 13;
                break;
                case "s3":
                    $status_id = 14;
                break;
                case "s4":
                    $status_id = 15;
                break;
                case "s5":
                    $status_id = 16;
                break;
                case "s6":
                    $status_id = 17;
                break;
                case "s7":
                    $status_id = 18;
                break;
                case "s8":
                    $status_id = 19;
                break;
            }
            CFormResult::SetStatus($RESULT_ID, $status_id);
        }

    	$arEventFields = array(
    		"FIO" => $arValues['form_text_23'],
    		"PHONE" => $arValues['form_text_24'],
    		"ADDRESS" => $arValues['form_text_27'],
    		"SKU" => $arValues['form_text_26'],
    		"CARD" => $arValues['form_text_28'],
    		"SALON" => $arValues['form_text_25'],
    	);

	   CEvent::Send("FORM_FILLING_ESHOP_ORDER2SALON",SITE_ID, $arEventFields, "Y");


    	$result = "<div class='reserve_response'><p>Ваш заказ на доставку товара ".$arValues['form_text_26']."<br/> в салон ".$arValues['form_text_25']." (".$arValues['form_text_27'].") успешно отправлен.</p><p>В ближайшее время с Вами свяжется наш специалист для подтверждения заказа.</p></div>";
        //.'<script type="text/javascript">  yaCounter30121774.reachGoal("ESHOP_BOOKING");ga("send", "event", "Booking", "Click", "SendEshopBooking");  </script>';

    } else {
       	global $strError;
      	$result = $strError;
    }
    echo $result;
}
elseif ($action == 'reserve') {

    // Форма бронирования товара в салоне
    $APPLICATION->IncludeComponent(
        "bitrix:form.result.new",
        "reservation",
        array(
            "AJAX_MODE" => "N",
            "AJAX_OPTION_ADDITIONAL" => "",
            "AJAX_OPTION_HISTORY" => "N",
            "AJAX_OPTION_JUMP" => "N",
            "AJAX_OPTION_STYLE" => "Y",
            "CACHE_TIME" => "3600",
            "CACHE_TYPE" => "N",
            "CHAIN_ITEM_LINK" => "",
            "CHAIN_ITEM_TEXT" => "",
            "COMPOSITE_FRAME_MODE" => "A",
            "COMPOSITE_FRAME_TYPE" => "AUTO",
            "EDIT_ADDITIONAL" => "N",
            "EDIT_STATUS" => "N",
            "IGNORE_CUSTOM_TEMPLATE" => "N",
            "NOT_SHOW_FILTER" => array(
                0 => "",
                1 => "",
            ),
            "NOT_SHOW_TABLE" => array(
                0 => "",
                1 => "",
            ),
            "RESULT_ID" => $_REQUEST[RESULT_ID],
            "SEF_MODE" => "N",
            "SHOW_ADDITIONAL" => "N",
            "SHOW_ANSWER_VALUE" => "Y",
            "SHOW_EDIT_PAGE" => "N",
            "SHOW_LIST_PAGE" => "N",
            "SHOW_STATUS" => "N",
            "SHOW_VIEW_PAGE" => "N",
            "START_PAGE" => "new",
            "SUCCESS_URL" => "",
            "USE_EXTENDED_ERRORS" => "Y",
            "WEB_FORM_ID" => "4",
            "COMPONENT_TEMPLATE" => "reservation",
            "VARIABLE_ALIASES" => array(
                "action" => "action",
            )
        ),
        false
    );

}
elseif ($action == 'reserve_send')
{
	CModule::IncludeModule("form");

	$arValues = array(
		"form_text_16"       => $_REQUEST['sname'],
		"form_text_17" 	=> $_REQUEST['sphone'],
		"form_text_19"        => $_REQUEST['sprod'],
		"form_text_20"       => $_REQUEST['sstore'],
		"form_text_18"       => $_REQUEST['scard'],
		"form_text_21"       => $_REQUEST['saddress'],
		"form_checkbox_AGREE"       => array(22),
	);

    if ($RESULT_ID = CFormResult::Add(4, $arValues)) {

        // Привязка заявки к городу по статусу
        if (SITE_ID != 's1')
        {
            $status_id = 4;
            switch (SITE_ID) {
                case "s2":
                    $status_id = 5;
                break;
                case "s3":
                    $status_id = 6;
                break;
                case "s4":
                    $status_id = 7;
                break;
                case "s5":
                    $status_id = 8;
                break;
                case "s6":
                    $status_id = 9;
                break;
                case "s7":
                    $status_id = 10;
                break;
                case "s8":
                    $status_id = 11;
                break;
            }
            CFormResult::SetStatus($RESULT_ID, $status_id);
        }

    	$arEventFields = array(
    		"FIO" => $arValues['form_text_16'],
    		"PHONE" => $arValues['form_text_17'],
    		"ADDRESS" => $arValues['form_text_21'],
    		"SKU" => $arValues['form_text_19'],
    		"CARD" => $arValues['form_text_18'],
    		"SALON" => $arValues['form_text_20'],
    	);

	   CEvent::Send("FORM_FILLING_RESERVATION",SITE_ID, $arEventFields, "Y");


    	$result = "<div class='reserve_response' data-resid='".$RESULT_ID."'>Ваш запрос на бронирование успешно отправлен.<br/> В ближайшее время с Вами свяжется наш специалист<p class='ff-medium'>Без подтверждения специалиста, изделие не бронируется, пожалуйста, дождитесь звонка.</p></div>";
        //.'<script type="text/javascript">  yaCounter30121774.reachGoal("BOOKING");ga("send", "event", "Booking", "Click", "SendBooking");  </script>';

    } else {
       	global $strError;
      	$result = $strError;
    }

    echo $result;
}
