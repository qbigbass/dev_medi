<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


$arResult['DETAIL_TEXT'] =  str_replace("#PHONE#", $GLOBALS['medi']['phones'][SITE_ID], $arResult['DETAIL_TEXT']);
$arResult['PREVIEW_TEXT'] =  str_replace("#PHONE#", $GLOBALS['medi']['phones'][SITE_ID], $arResult['PREVIEW_TEXT']);



if (strpos($arResult['DETAIL_TEXT'], "#WEBFORM") > 0)
{

    $form_match = preg_match_all("/#WEBFORM(\d+)?(\[[0-9,]+\])?#/iU", $arResult['DETAIL_TEXT'], $fmatches);
 
    $arResult['FORM_ID'] = '';
    if ($fmatches[1][0] > 0 ):
        if (CModule::IncludeModule("form")):
            $arFilter = Array(
                "ID"       => $fmatches[1][0],
                "SITE"      => SITE_ID
            );

            // получим список всех форм, для которых у текущего пользователя есть право на заполнение
            $rsForms = CForm::GetList($by="s_id", $order="desc", $arFilter, $is_filtered);
            if   ($arForm = $rsForms->Fetch())
            {

                $arResult['FORM_ID'] = $arForm['ID'];
                $arResult['FORM']  = $arForm;
            }
    endif;
    endif;

    $arResult['FORM_IN_TEXT'] = 1;
    if (!empty($fmatches[1][0])){
            $salons = [];
            if (!empty($fmatches[2][0]))
            {
                $salons = explode(",", str_replace(['[',']'], '', $fmatches[2][0]));
            }
            //buffer
            ob_start();

			if ($arResult['FORM'] && $arResult['FORM_ID'] > 0):?>
				<div class="serviceWebForm">
					<?
					// Форма бронирования товара в салоне
					$APPLICATION->IncludeComponent(
						"bitrix:form.result.new",
						strtolower($arResult['FORM']['SID']),
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
							"COMPOSITE_FRAME_MODE" => "N",
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
							"HIDDEN_FIELDS" => array(
								0 => "AGREE",
							),
                            "SALONS" => $salons,
							"USE_EXTENDED_ERRORS" => "Y",
							"WEB_FORM_ID" => $arResult['FORM']['ID'],
							"COMPONENT_TEMPLATE" => strtolower($arResult['FORM']['SID']),
							"LIST_URL" => "",
							"EDIT_URL" => "",
							"VARIABLE_ALIASES" => array(
								"WEB_FORM_ID" => "WEB_FORM_ID",
								"RESULT_ID" => "RESULT_ID",
							)
						),
						false
					); ?>
				</div>
			<?endif;

            $componentData = ob_get_contents();
            ob_end_clean();

            $arResult['DETAIL_TEXT'] = str_replace($fmatches[0][0], $componentData, $arResult['DETAIL_TEXT']);


    }
}
else {

}

$this->__component->SetResultCacheKeys(array(
    "NAME",
    "FORM_ID",
    "FORM",
    "PREVIEW_TEXT",
    "PREVIEW_PICTURE",
    "DETAIL_PICTURE",
    "DETAIL_PAGE_URL"
));
