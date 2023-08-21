<?
use Bitrix\Main,
    Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Sale,
    Bitrix\Catalog,
    Bitrix\Iblock;

use Bitrix\Main\Entity;
use Bitrix\Main\SystemException;
use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\Parser;


    if (SITE_ID == 's2')
    {
        $GLOBALS['price_id'] = '6';
    }
    else {
        $GLOBALS['price_id'] = '1';
    }


    if (!defined( "ADMIN_SECTION" )) {

    $eventManager->addEventHandler('catalog', 'OnGetOptimalPrice', function(
      $productId,
      $quantity = 1,
      $arUserGroups = [],
      $renewal = "N",
      $arPrices = [],
      $siteID = false,
      $arDiscountCoupons = false)
    {
        static $isLoop = false;

        if ($isLoop)
        {
         return true;
        }



        $priceIterator = Bitrix\Catalog\PriceTable::getList(array(
         'select' => array('ID', 'CATALOG_GROUP_ID', 'PRICE', 'CURRENCY'),
         'filter' => array(
            '=PRODUCT_ID' => $productId,
            '@CATALOG_GROUP_ID' => $GLOBALS['price_id'],
            array(
               'LOGIC' => 'OR',
               '<=QUANTITY_FROM' => $quantity,
               '=QUANTITY_FROM' => null
            ),
            array(
               'LOGIC' => 'OR',
               '>=QUANTITY_TO' => $quantity,
               '=QUANTITY_TO' => null
            )
         ),
         'order' => array('CATALOG_GROUP_ID' => 'ASC')
        ));

        $isLoop = true;
        $prices = CCatalogProduct::GetOptimalPrice($productId, $quantity, $arUserGroups, $renewal, $priceIterator->fetchAll(), $siteID, $arDiscountCoupons);

        /*if (!$lmxapp)
        {
            $lmxapp = new appLmx();
        }
        $getLoymaxPrice = $lmxapp->getLoymaxPrice($productId, $prices['PRICE']['ELEMENT_IBLOCK_ID']);

        if (!empty($getLoymaxPrice) && $getLoymaxPrice['PRICE'] != $prices['RESULT_PRICE']['DISCOUNT_PRICE']){

            $prices['RESULT_PRICE']['BASE_PRICE'] = $prices['PRICE']['BASE_PRICE'];
            $prices['PRICE']['PRICE'] = $getLoymaxPrice['PRICE'];

            $prices['RESULT_PRICE']['DISCOUNT_PRICE'] = $getLoymaxPrice['PRICE'];

            $prices['RESULT_PRICE']['DISCOUNT'] = $getLoymaxPrice['DISCOUNT'];

            $prices['DISCOUNT_PRICE'] = $getLoymaxPrice['PRICE'];
            $prices['MAX_PRICE'][$prices['PRICE']['PRODUCT_ID']] = $getLoymaxPrice['PRICE']['BASE_PRICE'];

        }*/

        $isLoop = false;

        return $prices;
    });
}


/*AddEventHandler("sale", "OnBeforeBasketAdd", "checkMTMTax");
AddEventHandler("sale", "OnBeforeBasketUpdate", "checkMTMTaxUpdate");
AddEventHandler("sale", "OnBasketAdd", "checkMTMTaxUpdate");
*/
function checkMTMTax(&$arFields)
{
    if ($arFields['MODULE'] == '' && !$arFields['VAT_RATE'] && !$arFields['PRODUCT_XML_ID'])
    {
        $arFields['VAT_RATE'] = '0.1';
        $arFields['VAT_INCLUDED'] = 'Y';
    }
}
function checkMTMTaxUpdate($ID, &$arFields)
{
    $prod = \CCatalogProduct::GetVATInfo($ID);

    if ($arFields['MODULE'] == '' && !$arFields['VAT_RATE']  && !$arFields['PRODUCT_XML_ID'])
    {
        $arFields['VAT_RATE'] = '0.1';
        $arFields['VAT_INCLUDED'] = 'Y';
    }
}


if (!defined( "ADMIN_SECTION" )  )
{
    Main\EventManager::getInstance()->addEventHandler(
        'sale',
        'OnSaleBasketBeforeSaved',
        'saleBasketLmxCalculate'
    );
}

function saleBasketLmxCalculate(Main\Event $event)
{
    global $USER;

    $log = [];
    $basket = $event->getParameter("ENTITY");
    $basketItems = $basket->getBasketItems();


    $start_time = microtime(true);

    $OUserID = CUser::GetID();//Sale\Fuser::getUserIdById ($basket->getFUserId());
    $arGroups = CUser::GetUserGroup($OUserID);

    // для медпредов не считаем.
    if (in_array(29, $arGroups)) return;
    $order = $basket->getOrder();
    $sid = Bitrix\Main\Context::getCurrent()->getSite();


    $price_id = 1;
    $max_price_id = 2;

    if ($sid == 's2'){
        $price_id = 6;
        $max_price_id = 5;
    }

    $user_not_found = '1';

    $lmxapp = new appLmx();
    $lmxapp->authMerchantToken();

    $log['USER_ID'] = ($OUserID ? $OUserID : 0);
    if ($OUserID > 0 && $user_not_found == '1')
    {
        $obUser = $USER->GetByID($OUserID);
        if ($arUser = $obUser->Fetch()){

            $parsedPhone = Parser::getInstance()->parse($arUser['LOGIN']);
            $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));
            if (!$phone){
                $parsedPhone = Parser::getInstance()->parse($arUser['PERSONAL_PHONE']);
                $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));
            }

            if ($phone != '' && strlen($phone) == 11)
            {
                $checkuser = $lmxapp->checkUser($phone, ['account', 'profile']);
                if ($checkuser['status'] == 'found')
                {
                    $authclientresult = $lmxapp->authClientToken($checkuser['code']);
                    $user_not_found = 0;


                }
                else{
                    $user_not_found = 1;
                }
            }
        }
        else{
            $user_not_found = 1;
        }
    }
    $log['USER_AUTH_TIME'] = microtime(true) - $start_time;

    $start_time2 = microtime(true);
    $objDateTime = new DateTime('NOW');
    $purchaseDate = $objDateTime->format("Y-m-d\TH:i:s.v\Z");

    if (isset($_SESSION['lmxapp']['purchaseId']) && $_SESSION['lmxapp']['purchaseTime'] > time()-600)
    {
        $purchaseId = str_replace([" ", "."], "",  $_SESSION['lmxapp']['purchaseId']);
        $purchaseDate = $_SESSION['lmxapp']['purchaseDate'];
        $_SESSION['lmxapp']['purchaseTime'] = time();
    }
    else
    {
        $purchaseId = str_replace([" ", "."], "",  rand(1, 10).microtime().$OUserID);
        $_SESSION['lmxapp']['purchaseId'] = $purchaseId;
        $_SESSION['lmxapp']['purchaseTime'] = time();
        $_SESSION['lmxapp']['purchaseDate'] = $purchaseDate;
    }

    $log['purchaseId'] = $purchaseId;

    $lines = [];
    $i = 1; // items
    $cc = 0; // lmx lines

    CModule::IncludeModule("iblock");

    foreach ($basketItems as $basketItem) {

        $iblock_id = CIBlockElement::GetIBlockByID($basketItem->getProductId());
        $obItem = CIBlockElement::GetList([], ['IBLOCK_ID'=>$iblock_id, 'ID'=>$basketItem->getProductId(),'ACTIVE'=>'Y'],
            false,false, ['ID', 'CATALOG_PRICE_'.$price_id,  'CATALOG_PRICE_'.$max_price_id, "PROPERTY_GTIN", "PROPERTY_LMX_GOODID", "NAME", "PROPERTY_CML2_ARTICLE" ] );
        if ($exItem = $obItem->GetNext()) {

            $lines[$cc] = [
                "position" => $i,
                "amount" => $exItem['CATALOG_PRICE_'.$price_id] * $basketItem->getQuantity(),
                "quantity" => $basketItem->getQuantity(),
                "cashback" => 0,
                "discount" => 0,
                "name" => $exItem['PROPERTY_CML2_ARTICLE_VALUE'],
                "price" => $exItem['CATALOG_PRICE_'.$price_id]
            ];
            if ($exItem['PROPERTY_LMX_GOODID_VALUE'] != '')
            {
                $lines[$cc] = array_merge($lines[$cc], ['goodsId'=>$exItem['PROPERTY_LMX_GOODID_VALUE']]);
            }elseif ($exItem['PROPERTY_GTIN_VALUE'] != '')
            {
                $lines[$cc] = array_merge($lines[$cc], ['barcode'=>substr($exItem['PROPERTY_GTIN_VALUE'],1)]);
            }
            if ($exItem['CATALOG_PRICE_'.$max_price_id] > $exItem['CATALOG_PRICE_'.$price_id])
            {
                $lines[$cc]['discount'] = $exItem['CATALOG_PRICE_'.$max_price_id] - $exItem['CATALOG_PRICE_'.$price_id];
            }
            $i++;
            $cc++;

        }
    }

    $log['products'] = count($lines);

    if (!empty($lines) )
    {
            $coupon = '';//'225404000001';
        if (!empty($_SESSION['lmxapp']['coupon']) && !empty($_SESSION['lmxapp']['coupon_ok'])){
            $coupon = $_SESSION['lmxapp']['coupon'];
        }
        $qResult = $lmxapp->calculate($_SESSION['lmxapp']['purchaseId'], $_SESSION['lmxapp']['purchaseDate'], $lines, $coupon);
        if (is_array($qResult['result']) && $qResult['result']['state'] == 'Success')
        {

            //$log['qResult'] = $qResult;
            foreach ($qResult['data'][0]['cheque']['lines'] as $k=>$line) {
                if ($basket[$k] !== null && !$lines[$k]['exclude']){
                    $basket[$k]->setFields(array(
                        'CUSTOM_PRICE' => "Y",
                        'PRICE' => ($line['amount']/$line['quantity']),
                        'BASE_PRICE' => $lines[$k]['price'],
                        'DISCOUNT_PRICE' => ($line['discount']/$line['quantity']),
                        'DISCOUNT_NAME' => $line['appliedOffers'][0]['name'],
                        'NOTES' => $line['appliedOffers'][0]['name'],
						'DISCOUNT_VALUE' => ($line['discount']/$line['quantity'])
                    )); // Изменение полей

                    $basketPropertyCollection = $basket[$k]->getPropertyCollection();

                    $fullprice = ($line['amount']+$line['discount'])/$line['quantity'];
                    $discountprice = $line['discount']/$line['quantity'];

                    $basketPropertyCollection->setProperty(array(
                        array(
                           'NAME' => 'Скидка '.round($discountprice/$fullprice *100, 0).'%',
                           'CODE' => 'DISCOUNT_NAME',
                           'VALUE' => $line['appliedOffers'][0]['name'].($coupon ? ' купон:'.$coupon: ''),

                        ),
                    ));

                }
            }
        }
    }
    $log['EXEC_TIME'] = microtime(true) - $start_time2;
    //w2t($log, 1, 'lmxbasket.csv');
            // END запрос в лоймакс
}
function deleteOldBaskets(){
    if ( CModule::IncludeModule("sale") && CModule::IncludeModule("catalog") ){
        global $DB;
        $nDays = 3; // сроком старше одного дня
        $nDays = IntVal($nDays);
        $strSql =
            "SELECT f.ID ".
            "FROM b_sale_fuser f ".
            "LEFT JOIN b_sale_order o ON (o.USER_ID = f.USER_ID) ".
            "WHERE ".
            "   TO_DAYS(f.DATE_UPDATE)<(TO_DAYS(NOW())-".$nDays.") ".
            "   AND o.ID is null ".
            "   AND f.USER_ID is null ".
            "LIMIT 3000";
        $db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
        while ($ar_res = $db_res->Fetch()){
            CSaleBasket::DeleteAll($ar_res["ID"], false);
            CSaleUser::Delete($ar_res["ID"]);
        }
    }
    return "deleteOldBaskets();";
}


function clearOldUserBaskets($nDays = 200)
{
    if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog")) {

        global $DB;

        $nDays = IntVal($nDays);
        $strSql =
            "SELECT f.ID, f.DATE_UPDATE " .
            "FROM b_sale_fuser f " .
            "LEFT JOIN b_sale_order o ON (o.USER_ID = f.USER_ID) " .
            "WHERE " .
            "   TO_DAYS(f.DATE_UPDATE)<(TO_DAYS(NOW())-" . $nDays . ") " .
            "   AND o.ID is null " .
//"  AND f.USER_ID is null ".
            "LIMIT 1000";
        $c = 0;
        $db_res = $DB->Query($strSql, false, "File: " . __FILE__ . "<br>Line: " . __LINE__);
        while ($ar_res = $db_res->Fetch()) {

            CSaleBasket::DeleteAll($ar_res["ID"], false);
            CSaleUser::Delete($ar_res["ID"]);
            $c++;
        }
    }
    return 'clearOldUserBaskets('.$nDays.');';
}
