<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$SALE_RIGHT = $APPLICATION->GetGroupRight("sale");
if ($SALE_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));


// Получаем код шаблона
function MediPrintTemplate($rep_file_name, $arOrder, $ORDER_ID, $arOrderProps, $arBasketIDs, $arQuantities, $arUser, $report, $serCount) {

	ob_start();

	include($rep_file_name);

	$content = ob_get_contents();

	ob_end_clean();

	return $content;
}


if (CModule::IncludeModule("sale")) {

	$arOrdersID = explode("|", $ORDER_ID);
	unset($ORDER_ID);

	if(is_array($arOrdersID) && count($arOrdersID) > 0) {

		$allContent = '';

		foreach ($arOrdersID as $key => $ORDER_ID) {
			if ($arOrder = CSaleOrder::GetByID($ORDER_ID)) {

                // Бланк для вывода на печать
				$rep_file_name = $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/reports/user_form.php";

                // Получаем все данные по заказу

				$arOrderProps = array();
				$dbOrderPropVals = CSaleOrderPropsValue::GetList(
					array(),
					array("ORDER_ID" => $ORDER_ID),
					false,
					false,
					array("ID", "CODE", "VALUE", "ORDER_PROPS_ID", "PROP_TYPE")
				);
				while ($arOrderPropVals = $dbOrderPropVals->Fetch()) {
					$arCurOrderPropsTmp = CSaleOrderProps::GetRealValue(
						$arOrderPropVals["ORDER_PROPS_ID"],
						$arOrderPropVals["CODE"],
						$arOrderPropVals["PROP_TYPE"],
						$arOrderPropVals["VALUE"],
						LANGUAGE_ID
					);
					foreach ($arCurOrderPropsTmp as $key => $value) {
						$arOrderProps[$key] = $value;
					}
				}

				$arBasketIDs = array();
				$arQuantities = array();

				if (!isset($SHOW_ALL) || $SHOW_ALL == "N") {
					$arBasketIDs_tmp = explode(",", $BASKET_IDS);
					$arQuantities_tmp = explode(",", $QUANTITIES);

					if (count($arBasketIDs_tmp)!=count($arQuantities_tmp)) die("INVALID PARAMS");
					for ($i = 0; $i < count($arBasketIDs_tmp); $i++) {
						if (IntVal($arBasketIDs_tmp[$i])>0 && doubleVal($arQuantities_tmp[$i])>0) {
							$arBasketIDs[] = IntVal($arBasketIDs_tmp[$i]);
							$arQuantities[] = doubleVal($arQuantities_tmp[$i]);
						}
					}
				}
				else {
					$db_basket = CSaleBasket::GetList(array("NAME" => "ASC"), array("ORDER_ID"=>$ORDER_ID), false, false, array("ID", "QUANTITY"));
					while ($arBasket = $db_basket->GetNext()) {
						$arBasketIDs[] = $arBasket["ID"];
						$arQuantities[] = $arBasket["QUANTITY"];
					}
				}

				$dbUser = CUser::GetByID($arOrder["USER_ID"]);
				$arUser = $dbUser->Fetch();

				// Получаем шаблон с данными
				$allContent .= MediPrintTemplate($rep_file_name, $arOrder, $ORDER_ID, $arOrderProps, $arBasketIDs, $arQuantities, $arUser, $report, $serCount);
			}
		}

        // Получаем тело шаблона и разбиваем постранично
		if(strlen($allContent) > 0) {
			preg_match_all("|<head>(.*?)</head>|s", $allContent, $matches, PREG_PATTERN_ORDER);
			$sHead = $matches[1][0];

			preg_match_all("|(<body[^>]*>)|s", $allContent, $matches, PREG_PATTERN_ORDER);
			$sBodyOpen = $matches[0][0];


			preg_match_all("|<body[^>]*>(.*)</body>|Us", $allContent, $matches, PREG_PATTERN_ORDER);


			$arBody = array();
			$arBody_ = array();
			for ($i=0; $i< count($matches[0]); $i++) {
				$arBody[] = $matches[1][$i];
			}

			$sPrintBreak = '<div style="border-bottom:2px dashed #888;margin-top:2em"class="no-print"></div><div style="page-break-after: always;clear: both; " ></div>';

			$sResult = '';
			$sResult .= '<!DOCTYPE html><html><head>';

			$sResult .= $sHead;

			$sResult .= '</head>';

			$sResult .= $sBodyOpen;

			$bFirstBreak = false;
			foreach($arBody as $value) {
				if($bFirstBreak)
					$sResult .= $sPrintBreak;

				$sResult .= $value;

				$bFirstBreak = true;
			}

			$sResult .= '</body></html>';

			echo $sResult;
		}
	}
}
