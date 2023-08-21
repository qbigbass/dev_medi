<? define("STOP_STATISTICS", true); ?>
<? define("NO_AGENT_CHECK", true); ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); ?>
<? error_reporting(0); ?>
<? use \DigitalWeb\Basket as DwBasket;
use Bitrix\Main\Context,
    Bitrix\Currency\CurrencyManager,
    Bitrix\Sale\Order,
    Bitrix\Sale\Basket,
    Bitrix\Sale\Delivery,
    Bitrix\Sale\PaySystem;

?>
<? global $USER;
$nUserID = 0;
$nUserID = $USER->GetID();
global $nUserEmail;
global $nUserName;
$nUserName = 'guest';
$nUserEmail = '';
if ($nUserID > 0):
    $nUserName = $USER->GetFullName();
    $nUserEmail = md5($USER->GetEmail());

endif; ?>
<? if (!empty($_GET["act"])) {
    
    
    $GLOBALS['price_code'] = 'CATALOG_PRICE_' . $GLOBALS['medi']['price_id'][SITE_ID];

//include modules
    if (!CModule::IncludeModule("catalog") || !CModule::IncludeModule("sale") || !CModule::IncludeModule("dw.deluxe")) {
        die;
    }
    if ($_GET["act"] == "getItemData") {
        
        
        if (!empty($_GET["product_id"])) {
            
            $arReturn['ID'] = intval($_GET["product_id"]);
            $rsIblockProduct = CIBlockElement::GetList([], ['ID' => $arReturn['ID']], false, false, ['IBLOCK_ID']);
            if ($productIblock = $rsIblockProduct->GetNext()) {
                $arReturn['IBLOCK_ID'] = $productIblock['IBLOCK_ID'];
            }
            
            $goodId = '';
            $goodName = '';
            $rsBaseProduct = CIBlockElement::GetList([], ['ID' => $arReturn['ID'], "IBLOCK_ID" => $arReturn['IBLOCK_ID']], false, false, ['IBLOCK_ID', 'PROPERTY_CML2_LINK.ID', 'PROPERTY_CML2_LINK.IBLOCK_ID', 'PROPERTY_CML2_ARTICLE']);
            if ($productInfo = $rsBaseProduct->GetNext()) {
                if ($productInfo['PROPERTY_CML2_LINK_ID'] > 0) {
                    $arFilter = array(
                        "ID" => $productInfo['PROPERTY_CML2_LINK_ID'],
                        "IBLOCK_ID" => $productInfo['PROPERTY_CML2_LINK_IBLOCK_ID'],
                        "ACTIVE_DATE" => "Y",
                        "ACTIVE" => "Y"
                    );
                    $rsBaseProduct2 = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'NAME', 'DETAIL_PAGE_URL', $GLOBALS['price_code'], 'PROPERTY_ATT_BRAND.NAME', 'PROPERTY_CML2_ARTICLE']);
                    if ($productBrand = $rsBaseProduct2->GetNext()) {
                        $arReturn['BRAND'] = $productBrand['PROPERTY_ATT_BRAND_NAME'];
                        $goodId = $productBrand['ID'];
                        $goodName = $productBrand['NAME'];
                        $goodPrice = $productBrand[$GLOBALS['price_code']];
                        $goodUrl = $productBrand['DETAIL_PAGE_URL'];
                        $goodArticle = $productInfo['PROPERTY_CML2_ARTICLE_VALUE'];
                    }
                } else {
                    $arFilter = array(
                        "ID" => $arReturn['ID'],
                        "IBLOCK_ID" => $arReturn['IBLOCK_ID'],
                        "ACTIVE_DATE" => "Y",
                        "ACTIVE" => "Y"
                    );
                    $rsBaseProduct2 = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'NAME', 'DETAIL_PAGE_URL', $GLOBALS['price_code'], 'PROPERTY_ATT_BRAND.NAME', 'PROPERTY_CML2_ARTICLE']);
                    if ($productBrand = $rsBaseProduct2->GetNext()) {
                        $arReturn['BRAND'] = $productBrand['PROPERTY_ATT_BRAND_NAME'];
                        $goodId = $productBrand['ID'];
                        $goodPrice = $productBrand[$GLOBALS['price_code']];
                        $goodUrl = $productBrand['DETAIL_PAGE_URL'];
                        $goodName = $productBrand['NAME'];
                        
                        $goodArticle = $productBrand['PROPERTY_CML2_ARTICLE_VALUE'];
                    }
                }
            }
            
            $arReturn['ID'] = $goodId;
            $arReturn['NAME'] = $goodName;
            $arReturn['QUANTITY'] = (intval($_GET["quantity"]) > 0 ? intval($_GET["quantity"]) : 1);
            $arReturn['PRICE'] = $goodPrice;
            
            $secturl = explode("/", $goodUrl);
            $sectcount = count($secturl) - 1;
            unset($secturl[$sectcount]);
            unset($secturl[0]);
            unset($secturl[1]);
            
            $arReturn['CATEGORY'] = implode("/", $secturl);
            
            $arReturn['CML2_ARTICLE'] = $goodArticle;
            
            //return component
            echo \Bitrix\Main\Web\Json::encode(
                array(
                    "SUCCESS" => "Y",
                    "DATA" => $arReturn
                )
            );
        } else {
            //return component
            echo \Bitrix\Main\Web\Json::encode(
                array(
                    "SUCCESS" => "N"
                )
            );
        }
        
    } elseif ($_GET["act"] == "getAvailableWindow") {
        if (!empty($_GET["product_id"])) {
            
            //buffer
            ob_start();
            
            $APPLICATION->IncludeComponent(
                "dresscode:catalog.store.amount",
                "fastView",
                array(
                    "COMPONENT_TEMPLATE" => "fastView",
                    "ELEMENT_ID" => intval($_GET["product_id"]),
                    "STORES" => array(),
                    "ELEMENT_CODE" => "",
                    "YANDEX_MAP_VERSION" => "2.0",
                    "STORE_PATH" => "/stores/#store_id#/",
                    "CACHE_TYPE" => "N",
                    "CACHE_TIME" => "36000000",
                    "MAIN_TITLE" => "",
                    "USER_FIELDS" => array(
                        0 => "",
                        1 => "",
                    ),
                    "FIELDS" => array(
                        0 => "TITLE",
                        1 => "ADDRESS",
                        2 => "DESCRIPTION",
                        3 => "PHONE",
                        4 => "EMAIL",
                        5 => "IMAGE_ID",
                        6 => "COORDINATES",
                        7 => "SCHEDULE",
                        8 => "",
                    ),
                    "SHOW_EMPTY_STORE" => "Y",
                    "USE_MIN_AMOUNT" => "Y",
                    "SHOW_GENERAL_STORE_INFORMATION" => "N",
                    "MIN_AMOUNT" => "0"
                ),
                false,
                array("HIDE_ICONS" => "Y")
            );
            
            //save buffer
            $componentData = ob_get_contents();
            
            //end buffer
            ob_end_clean();
            
            //return component
            echo \Bitrix\Main\Web\Json::encode(
                array(
                    "SUCCESS" => "Y",
                    "COMPONENT_DATA" => $componentData
                )
            );
            
        }
    } elseif ($_GET["act"] == "addSubscribe") {
        
        if (!empty($_GET["id"]) && !empty($_GET["site_id"])) {
            
            //inclde module
            if (CModule::IncludeModule("iblock")) {
                
                //global vars
                global $USER;
                
                //vars
                $userId = false;
                
                //get user id
                if ($USER && is_object($USER) && $USER->isAuthorized()) {
                    $userId = $USER->getId();
                }
                
                //get subscribe for current user
                $resultObject = \Bitrix\Catalog\SubscribeTable::getList(
                    array(
                        "select" => array(
                            "ID",
                            "ITEM_ID",
                            "TYPE" => "PRODUCT.TYPE",
                            "IBLOCK_ID" => "IBLOCK_ELEMENT.IBLOCK_ID",
                        ),
                        "filter" => array(
                            "USER_CONTACT" => $USER->getEmail(),
                            "ITEM_ID" => intval($_GET["id"]),
                            "SITE_ID" => htmlspecialcharsbx($_GET["site_id"]),
                            "USER_ID" => $userId,
                        ),
                    )
                );
                
                //if no exist subscribe
                if (!$subscribeItem = $resultObject->fetch()) {
                    
                    //buffer
                    ob_start();
                    
                    //include form
                    $APPLICATION->IncludeComponent(
                        "dresscode:catalog.product.subscribe",
                        ".default",
                        array(
                            "SITE_ID" => htmlspecialcharsbx($_GET["site_id"]),
                            "PRODUCT_ID" => intval($_GET["id"])
                        ),
                        false,
                        array(
                            "HIDE_ICONS" => "Y"
                        )
                    );
                    
                    //save buffer
                    $componentData = ob_get_contents();
                    
                    //end buffer
                    ob_end_clean();
                    
                    //return component
                    echo \Bitrix\Main\Web\Json::encode(
                        array(
                            "SUCCESS" => "Y",
                            "SUBSCRIBE_FORM" => $componentData
                        )
                    );
                    
                    
                } else {
                    
                    //return error
                    echo \Bitrix\Main\Web\Json::encode(
                        array(
                            "ERROR" => "Y",
                            "SUBSCRIBE" => "IS EXIST"
                        )
                    );
                    
                }
                
            }
        }
        
    } elseif ($_GET["act"] == "unSubscribe") {
        
        if (!empty($_GET["subscribeId"])) {
            
            //get subscribe by id
            $resultObject = \Bitrix\Catalog\SubscribeTable::getList(
                array(
                    "select" => array(
                        "ID",
                        "ITEM_ID",
                        "USER_CONTACT",
                        "TYPE" => "PRODUCT.TYPE",
                        "IBLOCK_ID" => "IBLOCK_ELEMENT.IBLOCK_ID",
                    ),
                    "filter" => array(
                        "ID" => intval($_GET["subscribeId"]),
                    ),
                )
            );
            
            //if exist subscribe
            if ($subscribeItem = $resultObject->fetch()) {
                
                $subscribeManager = new \Bitrix\Catalog\Product\SubscribeManager;
                $subscribeResult = $subscribeManager->unSubscribe(
                    array(
                        "unSubscribe" => "Y",
                        "subscribeId" => $subscribeItem["ID"],
                        "productId" => $subscribeItem["ITEM_ID"],
                        "userContact" => $subscribeItem["USER_CONTACT"]
                    )
                );
                
                if ($subscribeResult) {
                    echo \Bitrix\Main\Web\Json::encode(array("SUCCESS" => "Y"));
                } else {
                    
                    $errorObject = current($subscribeManager->getErrors());
                    if ($errorObject) {
                        echo \Bitrix\Main\Web\Json::encode(array("ERROR" => "Y", "SUBSCRIBE" => $errorObject->getMessage()));
                    }
                    
                }
                
            } else {
                echo \Bitrix\Main\Web\Json::encode(array("ERROR" => "Y", "SUBSCRIBE" => intval($_GET["subscribeId"]) . " not found"));
            }
            
        }
        
    } elseif ($_GET["act"] === "requestPrice") {
        
        if (!empty($_GET["telephone"]) && !empty($_GET["productID"])) {
            
            if (CModule::IncludeModule("iblock")) {
                $OPTION_CURRENCY = CCurrency::GetBaseCurrency();
                $arElement = CIBlockElement::GetByID(intval($_GET["productID"]))->GetNext();
                if (!empty($arElement)) {
                    
                    $postMess = CEventMessage::GetList($by = "site_id", $order = "desc", array("TYPE" => "SALE_DRESSCODE_REQUEST_SEND"))->GetNext();
                    
                    if (empty($postMess)) {
                        
                        $MESSAGE = "<h3>С сайта #SITE# поступил запрос цены на товар. </h3> <p> Товар: <b>#PRODUCT#</b>  <br /> Имя: <b>#NAME#</b> <br /> Телефон: <b>#PHONE#</b> <br /> Комментарий: #COMMENT#";
                        $FIELDS = "#SITE# \n #PRODUCT# \n #NAME# \n #PHONE# \n #COMMENT# \n";
                        
                        $et = new CEventType;
                        $et->Add(
                            array(
                                "LID" => "ru",
                                "EVENT_NAME" => "SALE_DRESSCODE_REQUEST_SEND",
                                "NAME" => "Запрос цены на товар",
                                "DESCRIPTION" => $FIELDS
                            )
                        );
                        
                        $arr["ACTIVE"] = "Y";
                        $arr["EVENT_NAME"] = "SALE_DRESSCODE_REQUEST_SEND";
                        $arr["LID"] = $_GET["SITE_ID"];
                        $arr["EMAIL_FROM"] = COption::GetOptionString('main', 'email_from', 'webmaster@webmaster.com');
                        $arr["EMAIL_TO"] = COption::GetOptionString("sale", "order_email");
                        $arr["BCC"] = COption::GetOptionString("main", 'email_from', 'webmaster@webmaster.com');
                        $arr["SUBJECT"] = "Запрос цены на товар";
                        $arr["BODY_TYPE"] = "html";
                        $arr["MESSAGE"] = $MESSAGE;
                        
                        $emess = new CEventMessage;
                        $emess->Add($arr);
                        
                    }
                    
                    $arMessage = array(
                        "SITE" => SITE_SERVER_NAME,
                        "PRODUCT" => $arElement["NAME"] . " (ID:" . $arElement["ID"] . " )",
                        "NAME" => BX_UTF != 1 ? iconv("UTF-8", "windows-1251//IGNORE", htmlspecialcharsbx($_GET["name"])) : htmlspecialcharsbx($_GET["name"]),
                        "PHONE" => BX_UTF != 1 ? iconv("UTF-8", "windows-1251//IGNORE", htmlspecialcharsbx($_GET["telephone"])) : htmlspecialcharsbx($_GET["telephone"]),
                        "COMMENT" => BX_UTF != 1 ? iconv("UTF-8", "windows-1251//IGNORE", htmlspecialcharsbx($_GET["message"])) : htmlspecialcharsbx($_GET["message"])
                    );
                    
                    CEvent::SendImmediate("SALE_DRESSCODE_REQUEST_SEND", htmlspecialcharsbx($_GET["SITE_ID"]), $arMessage, "Y", false);
                    
                    if (empty($result)) {
                        $result = array(
                            "heading" => "Ваш запрос успешно отправлен",
                            "message" => "В ближайшее время Вам перезвонит наш менеджер для уточнения цены.",
                            "success" => true
                        );
                    }
                    
                } else {
                    
                    $result = array(
                        "heading" => "Ошибка",
                        "message" => "Ошибка, товар не найден!",
                        "success" => false
                    );
                    
                }
                
            }
            
        } else {
            $result = array(
                "heading" => "Ошибка",
                "message" => "Ошибка, заполните обязательные поля!",
                "success" => false
            );
        }
        
        echo \Bitrix\Main\Web\Json::encode($result);
        
    } elseif ($_GET["act"] == "getRequestPrice") {
        
        if (!empty($_GET["id"])) {
            
            $OPTION_CURRENCY = CCurrency::GetBaseCurrency();
            $arResult = array();
            
            $res = CIBlockElement::GetList(array(), array("ID" => intval($_GET["id"])), false, false, array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "NAME", "CATALOG_QUANTITY"));
            while ($arRes = $res->GetNextElement()) {
                $arResult["PRODUCT"] = $arRes->GetFields();
                $arResult["PRODUCT"]["PROPERTIES"] = $arRes->GetProperties();
                
                $arResult["PRODUCT"]["PICTURE"] = CFile::ResizeImageGet($arResult["PRODUCT"]["DETAIL_PICTURE"], array("width" => 270, "height" => 230), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 80);
                $arResult["PRODUCT"]["PICTURE"]["src"] = !empty($arResult["PRODUCT"]["PICTURE"]["src"]) ? $arResult["PRODUCT"]["PICTURE"]["src"] : SITE_TEMPLATE_PATH . "/images/empty.png";
                
                if (empty($arResult["PRODUCT"]["DETAIL_PICTURE"])) {
                    $skuProductInfo = CCatalogSKU::getProductList($arResult["PRODUCT"]["ID"]);
                    if (!empty($skuProductInfo)) {
                        foreach ($skuProductInfo as $itx => $skuProductInfoElement) {
                            $productBySku = CIBlockElement::GetByID($skuProductInfoElement["ID"]);
                            if (!empty($productBySku)) {
                                if ($arResProductSku = $productBySku->GetNextElement()) {
                                    $arResProductSkuFields = $arResProductSku->GetFields();
                                    if (!empty($arResProductSkuFields["DETAIL_PICTURE"])) {
                                        $arResult["PRODUCT"]["PICTURE"] = CFile::ResizeImageGet($arResProductSkuFields["DETAIL_PICTURE"], array("width" => 270, "height" => 230), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 80);
                                    }
                                }
                            }
                        }
                    }
                }
                
                if (!empty($arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE"])) {
                    $mSt = '';
                    foreach ($arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE"] as $ifv => $marker) {
                        $background = strstr($arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv], "#") ? $arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv] : "#424242";
                        $mStr .= '<div class="marker" style="background-color: ' . $background . '">' . $marker . '</div>';
                    }
                    
                    $arResult["PRODUCT"]["MARKER"] = $mStr;
                }
                
            }
            
            if (!empty($arResult)) {
                echo \Bitrix\Main\Web\Json::encode($arResult);
            }
            
        }
        
    } elseif ($_GET["act"] == "getPricesWindow") {
        if (!empty($_GET["product_id"])) {
            $APPLICATION->IncludeComponent(
                "dresscode:catalog.prices.view",
                ".default",
                array(
                    "COMPONENT_TEMPLATE" => ".default",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "360000",
                    "PRODUCT_ID" => intval($_GET["product_id"]),
                    "PRODUCT_PRICE_CODE" => explode("||", $_GET["product_price_code"]),
                    "CURRENCY_ID" => $_GET["product_currency"]
                ),
                false
            );
        }
    } elseif ($_GET["act"] == "getFastView") {
        if (!empty($_GET["product_id"])) {
            $APPLICATION->IncludeComponent(
                "dresscode:catalog.item",
                "fast",
                array(
                    "COMPONENT_TEMPLATE" => ".default",
                    "CACHE_TIME" => "36000000",
                    "CACHE_TYPE" => "Y",
                    "DISPLAY_MORE_PICTURES" => "Y",
                    "DISPLAY_LAST_SECTION" => "N",
                    "DISPLAY_FILES_VIDEO" => "N",
                    "DISPLAY_RELATED" => "N",
                    "DISPLAY_SIMILAR" => "N",
                    "DISPLAY_BRAND" => "Y",
                    "PICTURE_HEIGHT" => "",
                    "PICTURE_WIDTH" => "",
                    "GET_MORE_PICTURES" => "Y", // more picture + detail picture
                    "IBLOCK_ID" => intval($_GET["product_iblock_id"]),
                    "PRODUCT_ID" => intval($_GET["product_id"]),
                    "CURRENCY_ID" => $_GET["product_currency_id"],
                    "HIDE_MEASURES" => $_GET["product_hide_measures"],
                    "CONVERT_CURRENCY" => $_GET["product_convert_currency"],
                    "HIDE_NOT_AVAILABLE" => $_GET["product_hide_not_available"],
                    "PRODUCT_PRICE_CODE" => !empty($_GET["product_price_code"]) ? explode("||", $_GET["product_price_code"]) : NUll,
                    "IN_CART" => $_GET["in_cart"]
                ),
                false
            );
        }
    } elseif ($_GET["act"] == "selectSku") {
        if (!empty($_GET["params"]) &&
            !empty($_GET["iblock_id"]) &&
            !empty($_GET["prop_id"]) &&
            !empty($_GET["product_id"]) &&
            !empty($_GET["level"]) &&
            !empty($_GET["props"])
        ) {
            
            $OPTION_ADD_CART = COption::GetOptionString("catalog", "default_can_buy_zero");
            $OPTION_CURRENCY = \Bitrix\Sale\Internals\SiteCurrencyTable::getSiteCurrency(SITE_ID);
            
            $arResult["PRODUCT_PRICE_ALLOW"] = array();
            $arResult["PRODUCT_PRICE_ALLOW_FILTER"] = array();
            $arPriceCode = array();
            
            //utf8 convert
            $_GET["price-code"] = !defined("BX_UTF") ? iconv("UTF-8", "windows-1251", $_GET["price-code"]) : $_GET["price-code"];
            
            if (!empty($_GET["price-code"]) && $_GET["price-code"] != "undefined") {
                $arPriceCode = explode("||", $_GET["price-code"]);
                $dbPriceType = CCatalogGroup::GetList(
                    array("SORT" => "ASC"),
                    array("NAME" => $arPriceCode)
                );
                while ($arPriceType = $dbPriceType->Fetch()) {
                    if ($arPriceType["CAN_BUY"] == "Y")
                        $arResult["PRODUCT_PRICE_ALLOW"][] = $arPriceType;
                    $arResult["PRODUCT_PRICE_ALLOW_FILTER"][] = $arPriceType["ID"];
                }
            }
            
            $arTmpFilter = array(
                "ACTIVE" => "Y",
                "IBLOCK_ID" => intval($_GET["iblock_id"]),
                "PROPERTY_" . intval($_GET["prop_id"]) => intval($_GET["product_id"])
            );
            
            // if($OPTION_ADD_CART == N){
            // 	$arTmpFilter[">CATALOG_QUANTITY"] = 0;
            // }
            
            $arProps = array();
            $arParams = array();
            $arTmpParams = array();
            $arCastFilter = array();
            $arProperties = array();
            $arPropActive = array();
            $arAllProperties = array();
            $arPropertyTypes = array();
            $arPropCombination = array();
            $arHighloadProperty = array();
            
            $PROPS = BX_UTF != 1 ? iconv("UTF-8", "windows-1251", $_GET["props"]) : $_GET["props"];
            $PARAMS = BX_UTF != 1 ? iconv("UTF-8", "windows-1251", $_GET["params"]) : $_GET["params"];
            $HIGHLOAD = BX_UTF != 1 ? iconv("UTF-8", "windows-1251", $_GET["highload"]) : $_GET["highload"];
            
            //normalize property
            $exProps = explode(";", trim($PROPS, ";"));
            $exParams = explode(";", trim($PARAMS, ";"));
            $exHighload = explode(";", trim($HIGHLOAD, ";"));
            
            if (empty($exProps) || empty($exParams))
                die("error #1 | Empty params or propList _no valid data");
            
            if (!empty($exHighload)) {
                foreach ($exHighload as $ihl => $nextHighLoad) {
                    $arHighloadProperty[$nextHighLoad] = "Y";
                }
            }
            
            foreach ($exProps as $ip => $sProp) {
                $msp = explode(":", $sProp);
                $arProps[$msp[0]][$msp[1]] = "D";
            }
            
            foreach ($exParams as $ip => $pProp) {
                $msr = explode(":", $pProp);
                $arParams[$msr[0]] = $msr[1];
                $resProp = CIBlockProperty::GetByID($msr[0]);
                if ($arNextPropGet = $resProp->GetNext()) {
                    $arPropertyTypes[$msr[0]] = $arNextPropGet["PROPERTY_TYPE"];
                    if (empty($arHighloadProperty[$msr[0]]) && $arNextPropGet["PROPERTY_TYPE"] != "E") {
                        $arTmpParams["PROPERTY_" . $msr[0] . "_VALUE"] = $msr[1];
                    } else {
                        $arTmpParams["PROPERTY_" . $msr[0]] = $msr[1];
                    }
                }
            }
            
            $arFilter = array_merge($arTmpFilter, array_slice($arTmpParams, 0, $_GET["level"]));
            
            $rsOffer = CIBlockElement::GetList(
                array(),
                $arFilter, false, false,
                array(
                    "ID",
                    "NAME",
                    "IBLOCK_ID",
                    "CATALOG_MEASURE",
                    "CATALOG_AVAILABLE",
                    "CATALOG_QUANTITY",
                    "CATALOG_QUANTITY_TRACE",
                    "CATALOG_CAN_BUY_ZERO"
                )
            );
            
            while ($obOffer = $rsOffer->GetNextElement()) {
                $arOfferParams = $obOffer->GetFields();
                $arFilterProp = $obOffer->GetProperties();
                foreach ($arFilterProp as $ifp => $arNextProp) {
                    if ($arNextProp["PROPERTY_TYPE"] == "L" || $arNextProp["PROPERTY_TYPE"] == "E" && !empty($arNextProp["VALUE"])
                        || $arNextProp["PROPERTY_TYPE"] == "S" && !empty($arNextProp["USER_TYPE_SETTINGS"]["TABLE_NAME"]) && !empty($arNextProp["VALUE"])
                    ) {
                        $arProps[$arNextProp["CODE"]][$arNextProp["VALUE"]] = "N";
                        $arProperties[$arNextProp["CODE"]] = $arNextProp["VALUE"];
                        $arPropCombination[$arOfferParams["ID"]][$arNextProp["CODE"]][$arNextProp["VALUE"]] = "Y";
                    }
                }
            }
            
            if (!empty($arParams)) {
                foreach ($arParams as $propCode => $arField) {
                    if ($arProps[$propCode][$arField] == "N") {
                        $arProps[$propCode][$arField] = "Y";
                    } else {
                        if (!empty($arProps[$propCode])) {
                            foreach ($arProps[$propCode] as $iCode => $upProp) {
                                if ($upProp == "N") {
                                    $arProps[$propCode][$iCode] = "Y";
                                    break(1);
                                }
                            }
                        }
                    }
                }
            }
            
            if (!empty($arProps)) {
                foreach ($arProps as $ip => $arNextProp) {
                    foreach ($arNextProp as $inv => $arNextPropValue) {
                        if ($arNextPropValue == "Y") {
                            $arPropActive[$ip] = $inv;
                            $arPropActiveIndex[$activeIntertion++] = $inv;
                        }
                    }
                }
            }
            
            if (!empty($arProps)) {
                $arPrevLevelProp = array();
                $levelIteraion = 0;
                foreach ($arProps as $inp => $arNextProp) { //level each
                    if ($levelIteraion > 0) {
                        foreach ($arNextProp as $inpp => $arNextPropEach) {
                            if ($arNextPropEach == "N" && !empty($arPrevLevelProp)) {
                                $seachSuccess = false;
                                foreach ($arPropCombination as $inc => $arNextCombination) {
                                    if ($arNextCombination[$inp][$inpp] == "Y" && $arNextCombination[$arPrevLevelProp["INDEX"]][$arPrevLevelProp["VALUE"]] == "Y") {
                                        $seachSuccess = true;
                                        break(1);
                                    }
                                }
                                if ($seachSuccess == false) {
                                    $arProps[$inp][$inpp] = "D";
                                }
                            }
                        }
                    }
                    $levelIteraion++;
                    $arPrevLevelProp = array("INDEX" => $inp, "VALUE" => $arPropActive[$inp]);
                }
            }
            
            $arLastFilter = array(
                "ACTIVE" => "Y",
                "IBLOCK_ID" => intval($_GET["iblock_id"]),
                "PROPERTY_" . intval($_GET["prop_id"]) => intval($_GET["product_id"])
            );
            
            foreach ($arPropActive as $icp => $arNextProp) {
                if (empty($arHighloadProperty[$icp]) && $arPropertyTypes[$icp] != "E") {
                    $arLastFilter["PROPERTY_" . $icp . "_VALUE"] = $arNextProp;
                } else {
                    $arLastFilter["PROPERTY_" . $icp] = $arNextProp;
                }
            }
            
            $arSkuPriceCodes = array();
            
            if (!empty($arResult["PRODUCT_PRICE_ALLOW"])) {
                $arSkuPriceCodes["PRODUCT_PRICE_ALLOW"] = $arResult["PRODUCT_PRICE_ALLOW"];
            }
            
            if (!empty($arPriceCode)) {
                $arSkuPriceCodes["PARAMS_PRICE_CODE"] = $arPriceCode;
            }
            
            $arLastOffer = getLastOffer($arLastFilter, $arProps, $_GET["product_id"], $OPTION_CURRENCY, !empty($_GET["product-more-pictures"]), $arSkuPriceCodes);
            
            if (!empty($arLastOffer["PRODUCT"]["CATALOG_MEASURE"])) {
                //коэффициент еденица измерения
                $rsMeasure = CCatalogMeasure::getList(
                    array(),
                    array(
                        "ID" => $arLastOffer["PRODUCT"]["CATALOG_MEASURE"]
                    ),
                    false,
                    false,
                    false
                );
                
                while ($arNextMeasure = $rsMeasure->Fetch()) {
                    $arLastOffer["PRODUCT"]["MEASURE"] = $arNextMeasure;
                }
            }
            
            if (!empty($_GET["product-change-prop"]) && $_GET["product-change-prop"] != "undefined") {
                ob_start();
                $APPLICATION->IncludeComponent(
                    "dresscode:catalog.properties.list",
                    htmlspecialchars($_GET["product-change-prop"]),
                    array(
                        "PRODUCT_ID" => $arLastOffer["PRODUCT"]["ID"],
                        "COUNT_PROPERTIES" => 10
                    ),
                    false
                );
                $arLastOffer["PRODUCT"]["RESULT_PROPERTIES"] = ob_get_contents();
                ob_end_clean();
            }
            
            //price count
            $arPriceFilter = array("PRODUCT_ID" => $arLastOffer["PRODUCT"]["ID"], "CAN_ACCESS" => "Y");
            if (!empty($arResult["PRODUCT_PRICE_ALLOW_FILTER"])) {
                $arPriceFilter["CATALOG_GROUP_ID"] = $arResult["PRODUCT_PRICE_ALLOW_FILTER"];
            }
            $dbPrice = CPrice::GetList(
                array(),
                $arPriceFilter,
                false,
                false,
                array("ID")
            );
            $arLastOffer["PRODUCT"]["COUNT_PRICES"] = $dbPrice->SelectedRowsCount();
            
            // max price отображение старой цены
            
            $maxprice_id = 2;
            $minprice_id = 1;
            if (SITE_ID == 's2') {
                $maxprice_id = 5;
                $minprice_id = 6;
                
            }
            $GLOBALS['medi']['price_id'][SITE_ID] = $minprice_id;
            $GLOBALS['medi']['max_price_id'][SITE_ID] = $maxprice_id;
            
            $obOfferPrice = CIBlockElement::GetList([], ["IBLOCK_ID" => $arLastOffer["PRODUCT"]['IBLOCK_ID'], "ID" => $arLastOffer["PRODUCT"]['ID']], false, false, ["CATALOG_PRICE_" . $maxprice_id, "CATALOG_PRICE_" . $minprice_id]);
            if ($arOffermaxPrice = $obOfferPrice->GetNext()) {
                $maxprice_diff = $arOffermaxPrice['CATALOG_PRICE_' . $minprice_id] + 100;
                if ($arOffermaxPrice['CATALOG_PRICE_' . $maxprice_id] > $maxprice_diff) {
                    $arLastOffer['PRODUCT']['PRICE']['RESULT_PRICE']['DISCOUNT_PRICE'] = CCurrencyLang::CurrencyFormat($arOffermaxPrice['CATALOG_PRICE_' . $maxprice_id] - $arOffermaxPrice['CATALOG_PRICE_' . $minprice_id], "RUB", true);
                    $arLastOffer['PRODUCT']['PRICE']['RESULT_PRICE']['DISCOUNT'] = ($arOffermaxPrice['CATALOG_PRICE_' . $maxprice_id] - $arOffermaxPrice['CATALOG_PRICE_' . $minprice_id]);
                    $arLastOffer['PRODUCT']['PRICE']['RESULT_PRICE']['DISCOUNT_PRINT'] = CCurrencyLang::CurrencyFormat(($arOffermaxPrice['CATALOG_PRICE_' . $maxprice_id] - $arOffermaxPrice['CATALOG_PRICE_' . $minprice_id]), "RUB", true);
                    $arLastOffer['PRODUCT']['PRICE']['PRICE']['BASE_PRICE'] = CCurrencyLang::CurrencyFormat($arOffermaxPrice['CATALOG_PRICE_' . $maxprice_id], "RUB", true);
                    $arLastOffer['PRODUCT']['PRICE']['RESULT_PRICE']['BASE_PRICE'] = CCurrencyLang::CurrencyFormat($arOffermaxPrice['CATALOG_PRICE_' . $maxprice_id], "RUB", true);
                }
            }
            
            
            //Информация о складах
            $rsStore = CCatalogStoreProduct::GetList(array(), array("PRODUCT_ID" => $arLastOffer["PRODUCT"]["ID"]), false, false, array("ID", "AMOUNT"));
            while ($arNextStore = $rsStore->GetNext()) {
                $arLastOffer["PRODUCT"]["STORES"][] = $arNextStore;
            }
            
            $arLastOffer["PRODUCT"]["STORES_COUNT"] = count($arLastOffer["PRODUCT"]["STORES"]);
            
            $filter = array(
                "ACTIVE" => "Y",
                "PRODUCT_ID" => $arLastOffer["PRODUCT"]["ID"],
                "+SITE_ID" => SITE_ID,
                "ISSUING_CENTER" => 'Y',
            );
            $rsProps = CCatalogStore::GetList(
                array('TITLE' => 'ASC', 'ID' => 'ASC'),
                $filter,
                false,
                false,
                ["ID", "ACTIVE", "PRODUCT_AMOUNT"]
            );
            $arLastOffer["PRODUCT"]['SALON_AVAILABLE'] = 0;
            $arLastOffer["PRODUCT"]['SALON_COUNT'] = 0;
            while ($sStore = $rsProps->GetNext()) {
                $arLastOffer["PRODUCT"]['SALON_AVAILABLE'] += $sStore['PRODUCT_AMOUNT'];
                
                if ($sStore['PRODUCT_AMOUNT'] > 0) {
                    $arLastOffer["PRODUCT"]['SALON_COUNT']++;
                }
            }
            
            $sumAmount = 0; // общее количество товара в салонах
            $mainStoreAmount = 0; // количество на складах
            
            
            if ($arLastOffer['PRODUCT']['PROPERTIES']['CML2_LINK']['VALUE'] > 0) {
                $mainElem = CIBlockElement::GetList([], ['ID' => $arLastOffer['PRODUCT']['PROPERTIES']['CML2_LINK']['VALUE'], false, false, ['IBLOCK_SECTION_ID']]);
                if ($arMainElem = $mainElem->GetNext()) {
                    // определяем корневую категорию
                    $arSects = [];
                    $nav = CIBlockSection::GetNavChain(false, $arMainElem['IBLOCK_SECTION_ID']);
                    while ($arSectionPath = $nav->GetNext()) {
                        $arSects[] = $arSectionPath;
                        
                    }
                }
            }
            
            
            // Для обуви  подключаем доп.склады с флагом UF_SHOES_STORE
            if ($arSects[0]['ID'] == 88) {
                $filter = array(
                    "ACTIVE" => "Y",
                    "PRODUCT_ID" => $arLastOffer["PRODUCT"]['ID'],
                    ["LOGIC" => "OR",
                        ["UF_STORE" => true],
                        ["UF_SHOES_STORE" => true]
                    ]
                );
            } else {
                $filter = array(
                    "ACTIVE" => "Y",
                    "PRODUCT_ID" => $arLastOffer["PRODUCT"]['ID'],
                    "UF_STORE" => true,
                );
            }
            if (SITE_ID == 's2') {
                $filter['SITE_ID'] = SITE_ID;
            } else {
                $filter['+SITE_ID'] = SITE_ID;
            }
            
            $rsProps = CCatalogStore::GetList(
                array('TITLE' => 'ASC', 'ID' => 'ASC'),
                $filter,
                false,
                false,
                ["ID", "ACTIVE", "PRODUCT_AMOUNT", "UF_STORE", "SITE_ID"]
            );
            while ($mStore = $rsProps->GetNext()) {
                $mainStoreAmount += $mStore['PRODUCT_AMOUNT'];
            }
            if ($mainStoreAmount <= 0) {
                $arLastOffer["PRODUCT"]["CATALOG_AVAILABLE"] = "N";
                $arLastOffer["PRODUCT"]["CATALOG_QUANTITY"] = "0";
                $arLastOffer["PRODUCT"]["CAN_BUY"] = "N";
                $arLastOffer["PRODUCT"]['SITE_ID'] = SITE_ID;
            }
            
            //NOTE Управление показом кнопок
            $obShowButtonsElm = CIBlockElement::GetList([], ["IBLOCK_ID" => $arLastOffer["PRODUCT"]['PROPERTIES']['CML2_LINK']['LINK_IBLOCK_ID'], "ID" => $arLastOffer["PRODUCT"]['PROPERTIES']['CML2_LINK']['VALUE']], false, false, ["PROPERTY_MTM", "PROPERTY_DONT_SHOW_REST", "PROPERTY_NO_CART_BUTTON", "PROPERTY_NO_RESERV_BUTTON", "PROPERTY_INSOLE_BUTTON"]);
            if ($arShowButtonsElm = $obShowButtonsElm->GetNext()) {
                // "Возможно изготовление на заказ"
                $arLastOffer["PRODUCT"]['DISPLAY_BUTTONS']['MTM_BUTTON'] = false;
                if (isset($arShowButtonsElm['PROPERTY_MTM_VALUE'])
                    && $arShowButtonsElm['PROPERTY_MTM_VALUE'] == 'Да'
                ) {
                    $arLastOffer["PRODUCT"]['DISPLAY_BUTTONS']['MTM_BUTTON'] = true;
                }
                
                // "Только под заказ"
                $arLastOffer["PRODUCT"]['DISPLAY_BUTTONS']['ONLY_ORDER_BUTTON'] = false;
                if (isset($arShowButtonsElm['PROPERTY_DONT_SHOW_REST_VALUE'])
                    && $arShowButtonsElm['PROPERTY_DONT_SHOW_REST_VALUE'] == 'Да'
                ) {
                    $arLastOffer["PRODUCT"]['DISPLAY_BUTTONS']['ONLY_ORDER_BUTTON'] = true;
                }
                global $USER;
                // Показывать или скрывать кнопку "В корзину"
                $arLastOffer["PRODUCT"]['DISPLAY_BUTTONS']['CART_BUTTON'] = true;
                if (isset($arShowButtonsElm['PROPERTY_NO_CART_BUTTON_VALUE'])
                    && $arShowButtonsElm['PROPERTY_NO_CART_BUTTON_VALUE'] == 'Да'
                ) {
                    if (($arSects[0]['ID'] == 354 || $arSects[0]['ID'] == 93) && $USER->IsAuthorized()
                        && !empty(array_intersect([20, 1], $USER->GetUserGroupArray()))) {
                        $arLastOffer["PRODUCT"]['DISPLAY_BUTTONS']['CART_BUTTON'] = true;
                    } else {
                        $arLastOffer["PRODUCT"]['DISPLAY_BUTTONS']['CART_BUTTON'] = false;
                    }
                }
                
                // Показывать или скрывать кнопку "Забронировать в салоне"
                $arLastOffer["PRODUCT"]['DISPLAY_BUTTONS']['RESERV_BUTTON'] = true;
                if (isset($arShowButtonsElm['PROPERTY_NO_RESERV_BUTTON_VALUE'])
                    && $arShowButtonsElm['PROPERTY_NO_RESERV_BUTTON_VALUE'] == 'Да'
                ) {
                    $arLastOffer["PRODUCT"]['DISPLAY_BUTTONS']['RESERV_BUTTON'] = false;
                }
                // Показывать или скрывать кнопку "Запись на изготовление стелек"
                $arLastOffer["PRODUCT"]['DISPLAY_BUTTONS']['INSOLE_BUTTON'] = false;
                if (isset($arShowButtonsElm["PROPERTY_INSOLE_BUTTON_VALUE"])
                    && $arShowButtonsElm["PRODUCT"]['PROPERTIES']['INSOLE_BUTTON']['VALUE'] == 'Да'
                ) {
                    $arLastOffer["PRODUCT"]['DISPLAY_BUTTONS']['INSOLE_BUTTON'] = true;
                }
            }
            
            if (!empty($arProps)) {
                echo \Bitrix\Main\Web\Json::encode(
                    array(
                        array("PRODUCT" => $arLastOffer["PRODUCT"]),
                        array("PROPERTIES" => $arLastOffer["PROPERTIES"])
                    )
                );
            }
            
        }
    } elseif ($_GET["act"] == "addCart") {
        
        if (!defined("SITE_ID")) {
            define("SITE_ID", htmlspecialcharsbx($_GET["site_id"]));
        }
        
        $GLOBALS['price_code'] = 'CATALOG_PRICE_1';
        if (SITE_ID == 's2') {
            $GLOBALS['price_code'] = 'CATALOG_PRICE_6';
        }
        
        
        //multi
        if (!empty($_GET["multi"]) && !empty($_GET['id'])) {
            
            $errors = array();
            $addElements = explode(";", $_GET["id"]);
            
            if (!empty($_GET["q"])) {
                $addQauntity = explode(";", $_GET["q"]);
            }
            
            if (!empty($addQauntity)) {
                foreach ($addQauntity as $inx => $nextQuanity) {
                    $exQuantity = explode(":", $nextQuanity);
                    if (!empty($exQuantity[0]) && !empty($exQuantity[1])) {
                        $elementsQauntity[$exQuantity[0]] = $exQuantity[1];
                    }
                }
            }
            
            foreach ($addElements as $x => $nextID) {
                if (empty($elementsQauntity[$nextID])) {
                    $addBasketQuantity = 1;
                    $rsMeasureRatio = CCatalogMeasureRatio::getList(
                        array(),
                        array("PRODUCT_ID" => $nextID),
                        false,
                        false,
                        array()
                    );
                    
                    if ($arProductMeasureRatio = $rsMeasureRatio->Fetch()) {
                        if (!empty($arProductMeasureRatio["RATIO"])) {
                            $addBasketQuantity = $arProductMeasureRatio["RATIO"];
                        }
                    }
                } else {
                    $addBasketQuantity = $elementsQauntity[$nextID];
                }
                
                //addProduct
                $basketResult = Bitrix\Catalog\Product\Basket::addProduct(array(
                    "PRODUCT_ID" => floatval($nextID),
                    "QUANTITY" => $addBasketQuantity,
                    "PROPS" => array(),
                
                ));
                
                //check result
                if (!$basketResult->isSuccess()) {
                    $errors[$nextID] = $basketResult->getErrorMessages();
                }
                
            }
            
            //check errors
            if (!empty($errors)) {
                //print json
                echo \Bitrix\Main\Web\Json::encode(array(
                    "errors" => $errors,
                    "status" => false
                ));
            } //success
            else {
                //print json
                echo \Bitrix\Main\Web\Json::encode(array(
                    "status" => true
                ));
            }
            
        } //single
        else {
            
            //globals
            global $APPLICATION;
            
            //measure ratio
            $addBasketQuantityRatio = $addBasketQuantity = 1;
            $rsMeasureRatio = CCatalogMeasureRatio::getList(
                array(),
                array("PRODUCT_ID" => intval($_GET["id"])),
                false,
                false,
                array()
            );
            
            if ($arProductMeasureRatio = $rsMeasureRatio->Fetch()) {
                if (!empty($arProductMeasureRatio["RATIO"])) {
                    $addBasketQuantityRatio = $addBasketQuantity = $arProductMeasureRatio["RATIO"];
                }
            }
            
            if (!empty($_GET["q"]) && $_GET["q"] != $addBasketQuantity) {
                $addBasketQuantity = floatval($_GET["q"]);
            }
            
            //addProduct
            $basketResult = Bitrix\Catalog\Product\Basket::addProduct(array(
                "PRODUCT_ID" => floatval($_GET["id"]),
                "QUANTITY" => $addBasketQuantity,
                "PROPS" => array(),
            
            ));
            
            //check result
            if (!$basketResult->isSuccess()) {
                $errors = $basketResult->getErrorMessages();
                //print json
                echo \Bitrix\Main\Web\Json::encode(array(
                    "errors" => $errors,
                    "status" => false
                ));
            } //push basket window component
            else {
                
                $brand_by_pid = intval($_GET["id"]);
                $skuParentProduct = CCatalogSku::GetProductInfo($brand_by_pid);
                //check exist offers for product
                $brContainOffers = CCatalogSKU::getExistOffers($brand_by_pid);
                
                //get parent product id
                if (!empty($skuParentProduct)) {
                    
                    $opProductId = $skuParentProduct["ID"];
                    
                    //parent element filter
                    $arFilter = array(
                        "ID" => $opProductId,
                        "ACTIVE_DATE" => "Y",
                        "ACTIVE" => "Y"
                    );
                    
                    $rsBaseProduct = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'NAME', 'PROPERTY_ATT_BRAND.NAME', 'IBLOCK_SECTION_ID', 'DETAIL_PAGE_URL', $GLOBALS['price_code']]);
                    if ($productBrand = $rsBaseProduct->GetNext()) {
                        $addedProduct['BRAND'] = $productBrand['PROPERTY_ATT_BRAND_NAME'];
                        $addedProduct['PRICE'] = intval($productBrand[$GLOBALS['price_code']]);
                        $addedProduct['PRICE_CODE'] = $GLOBALS['price_code'];
                        $addedProduct['NAME'] = $productBrand['NAME'];
                        $addedProduct['ID'] = $productBrand['ID'];
                        $addedProduct['SECTION_ID'] = $productBrand['IBLOCK_SECTION_ID'];
                        
                        $secturl = explode("/", $productBrand['DETAIL_PAGE_URL']);
                        $sectcount = count($secturl) - 1;
                        unset($secturl[$sectcount]);
                        unset($secturl[0]);
                        unset($secturl[1]);
                        
                        $addedProduct['CATEGORY'] = implode("/", $secturl);
                        $addedProduct['SITE_ID'] = SITE_ID;
                        
                        //offers  element filter
                        $arFilter2 = array(
                            "ID" => $brand_by_pid,
                            "ACTIVE_DATE" => "Y",
                            "ACTIVE" => "Y",
                            "IBLOCK_ID" => $skuParentProduct['OFFER_IBLOCK_ID']
                        );
                        $rsSkuProduct = CIBlockElement::GetList([], $arFilter2, false, false, ['ID', 'PROPERTY_CML2_ARTICLE', 'NAME']);
                        
                        if ($productOffer = $rsSkuProduct->GetNext()) {
                            $addedProduct['CML2_ARTICLE'] = $productOffer['PROPERTY_CML2_ARTICLE_VALUE'];
                        }
                    }
                } else {
                    // op = operation id
                    //set id, iblock for calc sku
                    $opProductId = $brand_by_pid;
                    
                    //simple  element filter
                    $arFilter = array(
                        "ID" => $opProductId,
                        "ACTIVE_DATE" => "Y",
                        "ACTIVE" => "Y"
                    );
                    
                    $rsBaseProduct = CIBlockElement::GetList([], $arFilter, false, false, ['ID', 'PROPERTY_ATT_BRAND.NAME', 'PROPERTY_CML2_ARTICLE', $GLOBALS['price_code'], 'DETAIL_PAGE_URL', 'NAME', 'IBLOCK_SECTION_ID']);
                    if ($productBrand = $rsBaseProduct->GetNext()) {
                        $addedProduct['BRAND'] = $productBrand['PROPERTY_ATT_BRAND_NAME'];
                        $addedProduct['CML2_ARTICLE'] = $productBrand['PROPERTY_CML2_ARTICLE_VALUE'];
                        $addedProduct['PRICE'] = intval($productBrand[$GLOBALS['price_code']]);
                        $addedProduct['PRICE_CODE'] = $GLOBALS['price_code'];
                        $addedProduct['NAME'] = $productBrand['NAME'];
                        $addedProduct['ID'] = $productBrand['ID'];
                        $addedProduct['SECTION_ID'] = $productBrand['IBLOCK_SECTION_ID'];
                        $addedProduct['SITE_ID'] = SITE_ID;
                        
                        $secturl = explode("/", $productBrand['DETAIL_PAGE_URL']);
                        $sectcount = count($secturl) - 1;
                        unset($secturl[$sectcount]);
                        unset($secturl[0]);
                        unset($secturl[1]);
                        
                        $addedProduct['CATEGORY'] = implode("/", $secturl);
                    }
                }
                
                
                //start buffering
                ob_start();
                
                //push component
                $APPLICATION->IncludeComponent(
                    "dresscode:sale.basket.window",
                    ".default",
                    array(
                        "HIDE_MEASURES" => $_GET["hide_measures"],
                        "PRODUCT_ID" => intval($_GET["id"]),
                        "SITE_ID" => htmlspecialcharsbx($_GET["site_id"]),
                    ),
                    false
                );
                
                //save buffer
                $componentHTML = ob_get_contents();
                
                //clean buffer
                ob_end_clean();
                
                //print json
                echo \Bitrix\Main\Web\Json::encode(array(
                    "window_component" => $componentHTML,
                    "status" => true,
                    "product" => $addedProduct
                ));
            }
            
        }
        
    } elseif ($_GET["act"] == "del") {
        echo CSaleBasket::Delete(intval($_GET["id"]));
    } elseif ($_GET["act"] == "upd") {
        
        if (!empty($_GET["id"])) {
            
            //globals
            global $USER;
            
            //vars
            $arReturn = array();
            
            $getList = CIBlockElement::GetList(
                array(),
                array(
                    "ID" => intval($_GET['id'])
                ),
                false,
                false,
                array(
                    "ID",
                    "NAME",
                    "DETAIL_PICTURE",
                    "DETAIL_PAGE_URL",
                    "CATALOG_MEASURE",
                    "CATALOG_AVAILABLE",
                    "CATALOG_QUANTITY",
                    "CATALOG_QUANTITY_TRACE",
                    "CATALOG_CAN_BUY_ZERO"
                )
            );
            
            $obj = $getList->GetNextElement();
            $arProduct = $obj->GetFields();
            
            $OPTION_QUANTITY_TRACE = $arProduct["CATALOG_QUANTITY_TRACE"];
            
            if (!empty($arProduct)) {
                $dbBasketItems = CSaleBasket::GetList(
                    false,
                    array(
                        "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                        "ORDER_ID" => "NULL",
                        "PRODUCT_ID" => intval($_GET["id"]),
                        "LID" => $_GET["site_id"],
                    ),
                    false,
                    false,
                    array("ID")
                );
                
                $basketRES = $dbBasketItems->Fetch();
                if (!empty($basketRES)) {
                    
                    if ($OPTION_QUANTITY_TRACE == "Y") {
                        if ($arProduct["CATALOG_QUANTITY"] < doubleval($_GET["q"])) {
                            $quantityError = true;
                        }
                    }
                    
                    if (!$quantityError) {
                        
                        if (CSaleBasket::Update($basketRES["ID"], array("QUANTITY" => doubleval($_GET["q"])))) {
                            
                            //extented prices and rules for working with basket
                            $dbBasketItems = CSaleBasket::GetList(
                                false,
                                array(
                                    "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                                    "PRODUCT_ID" => intval($_GET["id"]),
                                    "LID" => $_GET["site_id"],
                                    "ORDER_ID" => "NULL"
                                ),
                                false,
                                false,
                                array(
                                    "ID",
                                    "QUANTITY",
                                    "PRICE",
                                    "PRODUCT_ID",
                                    "CURRENCY",
                                    "DISCOUNT_PRICE",
                                    "MODULE"
                                )
                            );
                            
                            $basketQty = $dbBasketItems->Fetch();
                            
                            $allSum += ($basketQty["PRICE"] * $basketQty["QUANTITY"]);
                            $allWeight += ($basketQty["WEIGHT"] * $basketQty["QUANTITY"]);
                            $arItems[] = $basketQty;
                            
                            $arOrder = array(
                                "SITE_ID" => $_GET["site_id"],
                                "USER_ID" => $USER->GetID(),
                                "ORDER_PRICE" => $allSum,
                                "ORDER_WEIGHT" => $allWeight,
                                "BASKET_ITEMS" => $arItems
                            );
                            
                            $arOptions = array(
                                "COUNT_DISCOUNT_4_ALL_QUANTITY" => "Y",
                            );
                            
                            $arErrors = array();
                            
                            CSaleDiscount::DoProcessOrder($arOrder, $arOptions, $arErrors);
                            $basketQty = $arOrder["BASKET_ITEMS"][0];
                            
                            $basketQty["~DISCOUNT_PRICE"] = !empty($basketQty["DISCOUNT_PRICE"]) && $basketQty["DISCOUNT_PRICE"] > 0 ? CCurrencyLang::CurrencyFormat($basketQty["PRICE"] + $basketQty["DISCOUNT_PRICE"], $basketQty["CURRENCY"], true) : $basketQty["DISCOUNT_PRICE"];
                            $basketQty["DISCOUNT_SUM"] = !empty($basketQty["DISCOUNT_PRICE"]) && $basketQty["DISCOUNT_PRICE"] > 0 ? CCurrencyLang::CurrencyFormat(($basketQty["PRICE"] + $basketQty["DISCOUNT_PRICE"]) * round($basketQty["QUANTITY"]), $basketQty["CURRENCY"], true) : $basketQty["DISCOUNT_PRICE"];
                            $basketQty["OLD_PRICE"] = round($basketQty["~DISCOUNT_PRICE"]) > 0 ? $basketQty["PRICE"] + $basketQty["DISCOUNT_PRICE"] : 0;
                            $arProduct["CAN_BUY"] = $arProduct["CATALOG_AVAILABLE"];
                            $arProduct["MEASURE_SYMBOL_RUS"] = "";
                            
                            if (!empty($arProduct["CATALOG_MEASURE"])) {
                                //коэффициент еденица измерения
                                $rsMeasure = CCatalogMeasure::getList(
                                    array(),
                                    array(
                                        "ID" => $arProduct["CATALOG_MEASURE"]
                                    ),
                                    false,
                                    false,
                                    false
                                );
                                
                                while ($arNextMeasure = $rsMeasure->Fetch()) {
                                    $arProduct["MEASURE"] = $arNextMeasure;
                                }
                            }
                            
                            if (!empty($arProduct["MEASURE"])) {
                                $arProduct["MEASURE_SYMBOL_RUS"] = $arProduct["MEASURE"]["SYMBOL_RUS"];
                            }
                            
                            //write data
                            $arReturn = array(
                                "PRODUCT_ID" => intval($basketQty["PRODUCT_ID"]),
                                "~PRICE" => round($basketQty["PRICE"]),
                                "OLD_PRICE" => $basketQty["OLD_PRICE"],
                                "SUM" => addslashes(CCurrencyLang::CurrencyFormat(round($basketQty["PRICE"]) * doubleval($basketQty["QUANTITY"]), $basketQty["CURRENCY"], true)),
                                "PRICE" => addslashes(CCurrencyLang::CurrencyFormat($basketQty["PRICE"], $basketQty["CURRENCY"], true)),
                                "DISCOUNT_PRICE" => $basketQty["~DISCOUNT_PRICE"],
                                "DISCOUNT_SUM" => $basketQty["DISCOUNT_SUM"],
                                "CAN_BUY" => $arProduct["CAN_BUY"],
                                "MEASURE_SYMBOL_RUS" => $arProduct["MEASURE_SYMBOL_RUS"]
                            );
                            
                            //success flag
                            $arReturn["success"] = "Y";
                            
                            //return data
                            echo \Bitrix\Main\Web\Json::encode($arReturn);
                            
                        } else {
                            echo '{"error" : "basketUpdateError"}';
                        }
                        
                    } else {
                        CSaleBasket::Update($basketRES["ID"], array("QUANTITY" => $arProduct["CATALOG_QUANTITY"]));
                        echo '{"error" : "quantityError", "currentQuantityValue": "' . $arProduct["CATALOG_QUANTITY"] . '"}';
                    }
                    
                } else {
                    echo '{"error" : "productCartError"}';
                }
            } else {
                echo '{"error" : "productNotFoundError"}';
            }
        } else {
            echo '{"error" : "empty product id"}';
        }
    } elseif ($_GET["act"] == "skuADD") {
        if (!empty($_GET["id"]) && !empty($_GET["ibl"])) {
            
            $PRODUCT_ID = intval($_GET["id"]);
            $IBLOCK_ID = intval($_GET["ibl"]);
            $SKU_INFO = CCatalogSKU::GetInfoByProductIBlock($IBLOCK_ID);
            $PRODUCT_INFO = CIBlockElement::GetByID($PRODUCT_ID)->GetNext();
            $OPTION_ADD_CART = COption::GetOptionString("catalog", "default_can_buy_zero");
            $OPTION_CURRENCY = CCurrency::GetBaseCurrency();
            
            $dbPriceType = CCatalogGroup::GetList(
                array("SORT" => "ASC"),
                array("BASE" => "Y")
            );
            
            while ($arPriceType = $dbPriceType->Fetch()) {
                $OPTION_BASE_PRICE = $arPriceType["ID"];
            }
            
            if (is_array($SKU_INFO)) {
                
                $arResult = array();
                $rsOffers = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"], "PROPERTY_" . $SKU_INFO["SKU_PROPERTY_ID"] => $PRODUCT_ID), false, false, array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "NAME", "CATALOG_QUANTITY"));
                while ($ob = $rsOffers->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    $arProps = $ob->GetProperties();
                    $dbPrice = CPrice::GetList(
                        array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC", "SORT" => "ASC"),
                        array(
                            "PRODUCT_ID" => $arFields["ID"],
                            "CATALOG_GROUP_ID" => $OPTION_BASE_PRICE
                        ),
                        false,
                        false,
                        array("ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", "QUANTITY_FROM", "QUANTITY_TO")
                    );
                    
                    while ($arPrice = $dbPrice->Fetch()) {
                        $arDiscounts = CCatalogDiscount::GetDiscountByPrice(
                            $arPrice["ID"],
                            $USER->GetUserGroupArray(),
                            "N",
                            SITE_ID
                        );
                        $arFields["PRICE"] = CCatalogProduct::CountPriceWithDiscount(
                            $arPrice["PRICE"],
                            $arPrice["CURRENCY"],
                            $arDiscounts
                        );
                        
                        $arFields["DISCONT_PRICE"] = $arFields["PRICE"] != $arPrice["PRICE"] ? CurrencyFormat(CCurrencyRates::ConvertCurrency($arPrice["PRICE"], $arPrice["CURRENCY"], $OPTION_CURRENCY), $OPTION_CURRENCY) : 0;
                        $arFields["PRICE"] = CurrencyFormat(CCurrencyRates::ConvertCurrency($arFields["PRICE"], $arPrice["CURRENCY"], $OPTION_CURRENCY), $OPTION_CURRENCY);
                        
                    }
                    
                    $picture = CFile::ResizeImageGet($arFields['DETAIL_PICTURE'], array('width' => 220, 'height' => 200), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                    $arFields["DETAIL_PICTURE"] = !empty($picture["src"]) ? $picture["src"] : SITE_TEMPLATE_PATH . "/images/empty.png";
                    $arFields["ADDCART"] = $OPTION_ADD_CART === "Y" ? true : $arFields["CATALOG_QUANTITY"] > 0;
                    $arResult[] = array_merge($arFields, array("PROPERTIES" => $arProps));
                    
                }
                
                foreach ($arResult[0]["PROPERTIES"] as $i => $arProp) {
                    $propVisible = false;
                    if (empty($arProp["VALUE"])) {
                        if (empty($propDelete[$i])) {
                            foreach ($arResult as $x => $arElement) {
                                if (!empty($arElement["PROPERTIES"][$i]["VALUE"])) {
                                    $propVisible = true;
                                    break;
                                }
                            }
                            
                            if ($propVisible === false) {
                                $propDelete[$i] = true;
                            }
                        }
                    }
                }
                
                foreach ($arResult as $i => $arElement) {
                    foreach ($propDelete as $x => $val) {
                        unset($arResult[$i]["PROPERTIES"][$x]);
                    }
                }
                
                if (!empty($arResult)) {
                    echo \Bitrix\Main\Web\Json::encode($arResult);
                }
                
            }
            
        }
    } elseif ($_GET["act"] == "addWishlist") {
        if (!empty($_GET["id"])) {
            $_SESSION["WISHLIST_LIST"]["ITEMS"][$_GET["id"]] = $_GET["id"];
            echo intval($_SESSION["WISHLIST_LIST"]["ITEMS"][$_GET["id"]]);
        }
    } elseif ($_GET["act"] == "removeWishlist") {
        if (!empty($_GET["id"])) {
            unset($_SESSION["WISHLIST_LIST"]["ITEMS"][$_GET["id"]]);
            echo true;
        }
    } elseif ($_GET["act"] == "addCompare") {
        if (!empty($_GET["id"])) {
            $_SESSION["COMPARE_LIST"]["ITEMS"][$_GET["id"]] = $_GET["id"];
            echo intval($_SESSION["COMPARE_LIST"]["ITEMS"][$_GET["id"]]);
        }
    } elseif ($_GET["act"] == "compDEL") {
        if (!empty($_GET["id"])) {
            foreach ($_SESSION["COMPARE_LIST"]["ITEMS"] as $key => $arValue) {
                if ($arValue == $_GET["id"]) {
                    echo true;
                    unset($_SESSION["COMPARE_LIST"]["ITEMS"][$key]);
                    break;
                }
            }
        }
    } elseif ($_GET["act"] == "clearCompare") {
        unset($_SESSION["COMPARE_LIST"]["ITEMS"]);
        echo true;
    } elseif ($_GET["act"] == "search") {
        $_GET["name"] = BX_UTF !== 1 ? htmlspecialcharsbx(iconv("UTF-8", "CP1251//IGNORE", $_GET["name"])) : $_GET["name"];
        
        $OPTION_ADD_CART = COption::GetOptionString("catalog", "default_can_buy_zero");
        $OPTION_PRICE_TAB = COption::GetOptionString("catalog", "show_catalog_tab_with_offers");
        $OPTION_CURRENCY = CCurrency::GetBaseCurrency();
        
        $dbPriceType = CCatalogGroup::GetList(
            array("SORT" => "ASC"),
            array("BASE" => "Y")
        );
        
        while ($arPriceType = $dbPriceType->Fetch()) {
            $OPTION_BASE_PRICE = $arPriceType["ID"];
        }
        
        if (!empty($_GET["name"]) && !empty($_GET["iblock_id"])) {
            $section = !empty($_GET["section"]) ? intval($_GET["section"]) : 0;
            $arSelect = array("ID", "NAME", "DETAIL_PICTURE", "DETAIL_PAGE_URL", "CATALOG_QUANTITY");
            $arFilter = array("ACTIVE_DATE" => "Y", "ACTIVE" => "Y", "IBLOCK_ID" => intval($_GET["iblock_id"]), "PROPERTY_REGION_VALUE" => $GLOBALS['medi']['region_cities'][SITE_ID], "CATALOG_AVAILABLE" => "Y");
            $arFilter[] = array("LOGIC" => "OR", "?NAME" => $_GET["name"], "PROPERTY_ARTICLE" => $_GET["name"]);
            if ($section) {
                $arFilter["SECTION_ID"] = $section;
            }
            $res = CIBlockElement::GetList(array("shows" => "DESC"), $arFilter, false, array("nPageSize" => 4), $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $dbPrice = CPrice::GetList(
                    array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC", "SORT" => "ASC"),
                    array(
                        "PRODUCT_ID" => $arFields["ID"],
                        "CATALOG_GROUP_ID" => (SITE_ID == 's2' ? "6" : (SITE_ID == 's1' ? "1" : "8"))
                    ),
                    false,
                    false,
                    array("ID", "CATALOG_GROUP_ID", "PRICE", "CURRENCY", "QUANTITY_FROM", "QUANTITY_TO")
                );
                while ($arPrice = $dbPrice->Fetch()) {
                    $arDiscounts = CCatalogDiscount::GetDiscountByPrice(
                        $arPrice["ID"],
                        $USER->GetUserGroupArray(),
                        "N",
                        SITE_ID
                    );
                    $arFields["TMP_PRICE"] = $arFields["PRICE"] = CCatalogProduct::CountPriceWithDiscount(
                        $arPrice["PRICE"],
                        $arPrice["CURRENCY"],
                        $arDiscounts
                    );
                    $arFields["DISCONT_PRICE"] = $arFields["PRICE"] != $arPrice["PRICE"] ? CurrencyFormat(CCurrencyRates::ConvertCurrency($arPrice["PRICE"], $arPrice["CURRENCY"], $OPTION_CURRENCY), $OPTION_CURRENCY) : 0;
                    $arFields["PRICE"] = CurrencyFormat(CCurrencyRates::ConvertCurrency($arFields["PRICE"], $arPrice["CURRENCY"], $OPTION_CURRENCY), $OPTION_CURRENCY);
                }
                
                if (empty($arFields["TMP_PRICE"])) {
                    $arFields["SKU"] = CCatalogSKU::IsExistOffers($arFields["ID"]);
                    if ($arFields["SKU"]) {
                        $SKU_INFO = CCatalogSKU::GetInfoByProductIBlock($arFields["IBLOCK_ID"]);
                        if (is_array($SKU_INFO)) {
                            $rsOffers = CIBlockElement::GetList(array(), array("IBLOCK_ID" => $SKU_INFO["IBLOCK_ID"], "PROPERTY_" . $SKU_INFO["SKU_PROPERTY_ID"] => $arFields["ID"]), false, false, array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "NAME"));
                            while ($arSku = $rsOffers->GetNext()) {
                                $arSkuPrice = CCatalogProduct::GetOptimalPrice($arSku["ID"], 1, $USER->GetUserGroupArray());
                                if (!empty($arSkuPrice)) {
                                    $arFields["SKU_PRODUCT"][] = $arSku + $arSkuPrice;
                                }
                                $arFields["PRICE"] = ($arFields["PRICE"] > $arSkuPrice["DISCOUNT_PRICE"] || empty($arFields["PRICE"])) ? $arSkuPrice["DISCOUNT_PRICE"] : $arFields["PRICE"];
                            }
                            $arFields["DISCONT_PRICE"] = null;
                            $arFields["PRICE"] = "от " . CurrencyFormat($arFields["PRICE"], $OPTION_CURRENCY);
                        }
                    }
                }
                
                $arFields["ADDCART"] = $OPTION_ADD_CART === "Y" ? true : $arFields["CATALOG_QUANTITY"] > 0;
                $picture = CFile::ResizeImageGet($arFields['DETAIL_PICTURE'], array('width' => 50, 'height' => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                $arFields["DETAIL_PICTURE"] = !empty($picture["src"]) ? $picture["src"] : SITE_TEMPLATE_PATH . "/images/empty.png";
                foreach ($arFields as $key => $arProp) {
                    $arJsn[] = '"' . $key . '" : "' . addslashes(trim(str_replace("'", "", $arProp))) . '"';
                }
                $arReturn[] = '{' . implode($arJsn, ",") . '}';
            }
            
            echo "[" . implode($arReturn, ",") . "]";
        }
    } elseif ($_GET["act"] == "flushCart") {
        ?>
        <ul>
        <li class="dl">
            <? $APPLICATION->IncludeComponent(
                "bitrix:sale.basket.basket.line",
                addslashes($_GET["topCartTemplate"]),
                array(
                    "HIDE_ON_BASKET_PAGES" => "N",
                    "PATH_TO_BASKET" => SITE_DIR . "personal/cart/",
                    "PATH_TO_ORDER" => SITE_DIR . "personal/order/make/",
                    "PATH_TO_PERSONAL" => SITE_DIR . "personal/",
                    "PATH_TO_PROFILE" => SITE_DIR . "personal/",
                    "PATH_TO_REGISTER" => SITE_DIR . "login/",
                    "POSITION_FIXED" => "N",
                    "SHOW_AUTHOR" => "Y",
                    "SHOW_EMPTY_VALUES" => "Y",
                    "SHOW_NUM_PRODUCTS" => "Y",
                    "SHOW_PERSONAL_LINK" => "N",
                    "SHOW_PRODUCTS" => "Y",
                    "SHOW_TOTAL_PRICE" => "Y",
                    "COMPONENT_TEMPLATE" => "topCart"
                ),
                false
            ); ?>
        </li>
        <li class="dl">
            <? $APPLICATION->IncludeComponent(
                "bitrix:sale.basket.basket.line",
                "bottomCart",
                array(
                    "HIDE_ON_BASKET_PAGES" => "N",
                    "PATH_TO_BASKET" => SITE_DIR . "personal/cart/",
                    "PATH_TO_ORDER" => SITE_DIR . "personal/order/make/",
                    "PATH_TO_PERSONAL" => SITE_DIR . "personal/",
                    "PATH_TO_PROFILE" => SITE_DIR . "personal/",
                    "PATH_TO_REGISTER" => SITE_DIR . "login/",
                    "POSITION_FIXED" => "N",
                    "SHOW_AUTHOR" => "N",
                    "SHOW_EMPTY_VALUES" => "Y",
                    "SHOW_NUM_PRODUCTS" => "Y",
                    "SHOW_PERSONAL_LINK" => "N",
                    "SHOW_PRODUCTS" => "Y",
                    "SHOW_TOTAL_PRICE" => "Y",
                    "COMPONENT_TEMPLATE" => "topCart"
                ),
                false
            ); ?>
        </li>
        <li class="dl">
            <? $APPLICATION->IncludeComponent("dresscode:favorite.line", addslashes($_GET["wishListTemplate"]), array(),
                false
            ); ?>
        </li>
        <li class="dl">
            <? $APPLICATION->IncludeComponent("dresscode:compare.line", addslashes($_GET["compareTemplate"]), array(),
                false
            ); ?>
        </li>
        </ul><?
    } elseif ($_GET["act"] == "rating") {
        global $USER;
        if ($USER->IsAuthorized()) {
            if (!empty($_GET["id"])) {
                $arUsers[] = $USER->GetID();
                $res = CIBlockElement::GetList(array(), array("ID" => intval($_GET["id"]), "ACTIVE_DATE" => "Y", "ACTIVE" => "Y"), false, false, array("ID", "IBLOCK_ID", "PROPERTY_USER_ID", "PROPERTY_GOOD_REVIEW", "PROPERTY_BAD_REVIEW"));
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    if ($arFields["PROPERTY_USER_ID_VALUE"] == $arUsers[0]) {
                        $result = array(
                            "result" => false,
                            "error" => "Вы уже голосовали!",
                            "heading" => "Ошибка"
                        );
                        break;
                    }
                }
                if (!$result) {
                    $propCODE = $_GET["trig"] ? "GOOD_REVIEW" : "BAD_REVIEW";
                    $propVALUE = $_GET["trig"] ? $arFields["PROPERTY_GOOD_REVIEW_VALUE"] + 1 : $arFields["PROPERTY_BAD_REVIEW_VALUE"] + 1;
                    $db_props = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], array("sort" => "asc"), array("CODE" => "USER_ID"));
                    if ($arProps = $db_props->Fetch()) {
                        $arUsers[] = $arProps["VALUE"];
                    }
                    CIBlockElement::SetPropertyValuesEx($arFields["ID"], $arFields["IBLOCK_ID"], array($propCODE => $propVALUE, "USER_ID" => $arUsers));
                    $result = array(
                        "result" => true
                    );
                }
            } else {
                $result = array(
                    "result" => false,
                    "error" => "Элемент не найден",
                    "heading" => "Ошибка"
                );
            }
        } else {
            $result = array(
                "error" => "Для голосования вам необходимо авторизоваться",
                "result" => false,
                "heading" => "Ошибка"
            );
        }
        echo \Bitrix\Main\Web\Json::encode($result);
        
    } elseif ($_REQUEST["act"] == "newReview") {
        global $USER;
        if ($USER->IsAuthorized()) {
            if (!empty($_REQUEST["NAME"]) &&
                //!empty($_REQUEST["SHORTCOMINGS"]) &&
                !empty($_REQUEST["COMMENT"]) &&
                //!empty($_REQUEST["DIGNITY"])       &&
                //!empty($_REQUEST["USED"])         &&
                //!empty($_REQUEST["RATING"])       &&
                !empty($_REQUEST["PRODUCT_NAME"]) &&
                !empty($_REQUEST["PRODUCT_ID"])
            ) {
                $arUsers = array($USER->GetID());
                $res = CIBlockElement::GetList(
                    array(),
                    array(
                        "ID" => intval($_REQUEST["PRODUCT_ID"]),
                        "ACTIVE_DATE" => "Y",
                        "ACTIVE" => "Y"
                    ),
                    false,
                    false,
                    array(
                        "ID",
                        "IBLOCK_ID",
                        "PROPERTY_USER_ID",
                        "PROPERTY_VOTE_SUM",
                        "PROPERTY_VOTE_COUNT"
                    )
                );
                while ($ob = $res->GetNextElement()) {
                    $arFields = $ob->GetFields();
                    if ($arFields["PROPERTY_USER_ID_VALUE"] == $arUsers[0]) {
                        $result = array(
                            "heading" => "Ошибка",
                            "message" => "Вы уже оставляли отзыв к этому товару."
                        );
                        break;
                    }
                    $arUsers[] = $arFields["PROPERTY_USER_ID_VALUE"];
                }
                if (empty($result)) {
                    $newElement = new CIBlockElement;
                    
                    // DIGNITY - достоинства
                    // SHORTCOMINGS - недостатки
                    // RATING - рейтинг
                    // EXPERIENCE - опыт использования
                    // NAME - Имя
                    
                    $PROP = array(
                        "DIGNITY" => (BX_UTF == 1) ? htmlspecialchars($_REQUEST["DIGNITY"]) : iconv("UTF-8", "windows-1251//IGNORE", htmlspecialchars($_REQUEST["DIGNITY"])),
                        "SHORTCOMINGS" => (BX_UTF == 1) ? htmlspecialchars($_REQUEST["SHORTCOMINGS"]) : iconv("UTF-8", "windows-1251//IGNORE", htmlspecialchars($_REQUEST["SHORTCOMINGS"])),
                        "NAME" => (BX_UTF == 1) ? htmlspecialchars($_REQUEST["NAME"]) : iconv("UTF-8", "windows-1251//IGNORE", htmlspecialchars($_REQUEST["NAME"])),
                        "EXPERIENCE" => intval($_REQUEST["USED"]),
                        //"RATING" => intval($_REQUEST["RATING"])
                    );
                    
                    $arLoadProductArray = array(
                        "MODIFIED_BY" => $USER->GetID(),
                        "IBLOCK_SECTION_ID" => false,
                        "IBLOCK_ID" => intval($_REQUEST["iblock_id"]),
                        "PROPERTY_VALUES" => $PROP,
                        "NAME" => (BX_UTF == 1) ? htmlspecialchars($_REQUEST["PRODUCT_NAME"]) : iconv("UTF-8", "windows-1251//IGNORE", htmlspecialchars($_REQUEST["PRODUCT_NAME"])),
                        "ACTIVE" => "N",
                        "DETAIL_TEXT" => (BX_UTF == 1) ? htmlspecialchars($_REQUEST["COMMENT"]) : iconv("UTF-8", "windows-1251//IGNORE", htmlspecialchars($_REQUEST["COMMENT"])),
                        "CODE" => intval($_REQUEST["PRODUCT_ID"])
                    );
                    
                    if ($PRODUCT_ID = $newElement->Add($arLoadProductArray)) {
                        $result = array(
                            "heading" => "Отзыв добавлен",
                            "message" => "Ваш отзыв будет опубликован после модерации.",
                            "reload" => true
                        );
                        /*
                            $VOTE_SUM   = $arFields["PROPERTY_VOTE_SUM_VALUE"] + intval($_REQUEST["RATING"]);
                            $VOTE_COUNT = $arFields["PROPERTY_VOTE_COUNT_VALUE"] + 1;
                            $RATING = ($VOTE_SUM / $VOTE_COUNT);
    */
                        CIBlockElement::SetPropertyValuesEx(
                            intval($_REQUEST["PRODUCT_ID"]),
                            $arFields["IBLOCK_ID"],
                            array(
                                "VOTE_SUM" => $VOTE_SUM,
                                "VOTE_COUNT" => $VOTE_COUNT,
                                //"RATING" => $RATING,
                                "USER_ID" => $arUsers
                            )
                        );
                        
                    } else {
                        $result = array(
                            "heading" => "Ошибка",
                            "message" => "error(1)"
                        );
                    }
                }
            } else {
                $result = array(
                    "heading" => "Ошибка",
                    "message" => "Заполните обязательные поля!"
                );
            }
        } else {
            $result = array(
                "heading" => "Ошибка",
                "message" => "Ошибка авторизации"
            );
        }
        
        echo \Bitrix\Main\Web\Json::encode($result);
        
    } elseif ($_GET["act"] == "getFastBuy") {
        
        if (!empty($_GET["id"])) {
            
            //globals
            global $USER;
            
            $OPTION_CURRENCY = CCurrency::GetBaseCurrency();
            $arResult = array();
            
            $res = CIBlockElement::GetList(array(), array("ID" => intval($_GET["id"])), false, false, array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "NAME", "CATALOG_QUANTITY"));
            if ($arRes = $res->GetNextElement()) {
                
                $arResult["PRODUCT"] = $arRes->GetFields();
                $arResult["PRODUCT"]["PROPERTIES"] = $arRes->GetProperties();
                $arTmpPrice = CCatalogProduct::GetOptimalPrice($arResult["PRODUCT"]["ID"], 1, $USER->GetUserGroupArray());
                $arResult["PRODUCT"]["PICTURE"] = CFile::ResizeImageGet($arResult["PRODUCT"]["DETAIL_PICTURE"], array("width" => 270, "height" => 230), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 80);
                $arResult["PRODUCT"]["PICTURE"]["src"] = !empty($arResult["PRODUCT"]["PICTURE"]["src"]) ? $arResult["PRODUCT"]["PICTURE"]["src"] : SITE_TEMPLATE_PATH . "/images/empty.png";
                $arResult["PRODUCT"]["PRICE"]["PRICE_FORMATED"] = CurrencyFormat($arTmpPrice["DISCOUNT_PRICE"], $OPTION_CURRENCY);
                $arResult["PRODUCT"]["PRICE"]["PRICE"] = $arTmpPrice["DISCOUNT_PRICE"];
                
                if (empty($arResult["PRODUCT"]["DETAIL_PICTURE"])) {
                    $skuProductInfo = CCatalogSKU::getProductList($arResult["PRODUCT"]["ID"]);
                    if (!empty($skuProductInfo)) {
                        foreach ($skuProductInfo as $itx => $skuProductInfoElement) {
                            $productBySku = CIBlockElement::GetByID($skuProductInfoElement["ID"]);
                            if (!empty($productBySku)) {
                                if ($arResProductSku = $productBySku->GetNextElement()) {
                                    $arResProductSkuFields = $arResProductSku->GetFields();
                                    if (!empty($arResProductSkuFields["DETAIL_PICTURE"])) {
                                        $arResult["PRODUCT"]["PICTURE"] = CFile::ResizeImageGet($arResProductSkuFields["DETAIL_PICTURE"], array("width" => 270, "height" => 230), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 80);
                                    }
                                }
                            }
                        }
                    }
                }
                
                if ($arTmpPrice["RESULT_PRICE"]["BASE_PRICE"] != $arTmpPrice["RESULT_PRICE"]["DISCOUNT_PRICE"]) {
                    $arResult["PRODUCT"]["PRICE"]["PRICE_FORMATED"] .= ' <s class="discount">' . CurrencyFormat($arTmpPrice["RESULT_PRICE"]["BASE_PRICE"], $OPTION_CURRENCY) . '</s>';
                }
                
                if (!empty($arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE"])) {
                    $mSt = '';
                    foreach ($arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE"] as $ifv => $marker) {
                        $background = strstr($arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv], "#") ? $arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv] : "#424242";
                        $mStr .= '<div class="marker" style="background-color: ' . $background . '">' . $marker . '</div>';
                    }
                    
                    $arResult["PRODUCT"]["MARKER"] = $mStr;
                }
                
                if ($USER->IsAuthorized()) {
                    $rsUser = CUser::GetByID($USER->GetID());
                    $arUser = $rsUser->Fetch();
                    if (!empty($arUser)) {
                        $arResult["PRODUCT"]["USER_NAME"] = $USER->GetFullName();
                        $arResult["PRODUCT"]["USER_PHONE"] = $arUser["PERSONAL_MOBILE"];
                    }
                }
                
                $obElm = CIBlockElement::GetList([], ["ID" => $arResult["PRODUCT"]['ID']], false, false, ["IBLOCK_ID"]);
                if ($arElm = $obElm->GetNext()) {
                    $obElmProp = CIBlockElement::GetList([], ["ID" => $arResult["PRODUCT"]['ID'], "IBLOCK_ID" => $arElm['IBLOCK_ID']], false, false, ["ID", "NAME", "PROPERTY_CML2_LINK", "PROPERTY_CML2_LINK.IBLOCK_ID", "PROPERTY_ATT_BRAND.NAME", "PROPERTY_CML2_ARTICLE"]);
                    if ($arElmProp = $obElmProp->GetNext()) {
                        $brand = '';
                        $article = $arElmProp['PROPERTY_CML2_ARTICLE_VALUE'];
                        // sku
                        if ($arElmProp['PROPERTY_CML2_LINK_VALUE']) {
                            $obElmBrand = CIBlockElement::GetList([], ["IBLOCK_ID" => $arElmProp['PROPERTY_CML2_LINK_IBLOCK_ID'], "ID" => $arElmProp['PROPERTY_CML2_LINK_VALUE']], false, false, ["ID", "NAME", "PROPERTY_ATT_BRAND.NAME"]);
                            
                            if ($arElmBrand = $obElmBrand->GetNext()) {
                                $brand = $arElmBrand['PROPERTY_ATT_BRAND_NAME'];
                                $goodId = $arElmBrand['ID'];
                                $goodName = $arElmBrand['NAME'];
                            }
                            
                        } // simple
                        elseif ($arElmProp['PROPERTY_ATT_BRAND_NAME']) {
                            
                            $brand = $arElmProp['PROPERTY_ATT_BRAND_NAME'];
                            
                            $goodId = $arElmProp['ID'];
                            $goodName = $arElmProp['NAME'];
                            
                        }
                        
                    }
                }
                
                $secturl = explode("/", $arResult["PRODUCT"]['DETAIL_PAGE_URL']);
                $sectcount = count($secturl) - 1;
                unset($secturl[$sectcount]);
                unset($secturl[0]);
                unset($secturl[1]);
                
                $arResult["PRODUCT"]['ITEM'] = array(
                    'id' => $goodId,
                    'q' => 1,
                    'price' => $arResult["PRODUCT"]["PRICE"]["PRICE"],
                    'article' => $article,
                    'name' => $goodName,
                    'category' => implode("/", $secturl),
                    'brand' => $brand
                );
                
            }
            
            if (!empty($arResult)) {
                echo \Bitrix\Main\Web\Json::encode(array($arResult["PRODUCT"]));
            }
            
        }
        
    } elseif ($_GET["act"] === "fastBack") {
        
        if (!empty($_GET["phone"]) && !empty($_GET["id"])) {
            
            if (CModule::IncludeModule("iblock") && CModule::IncludeModule("sale") && CModule::IncludeModule("catalog") && CModule::IncludeModule("dw.deluxe") && CModule::IncludeModule("form")) {
                $OPTION_CURRENCY = CCurrency::GetBaseCurrency();
                $arElement = CIBlockElement::GetByID(intval($_GET["id"]))->GetNext();
                if (!empty($arElement)) {
                    
                    $arPrice = CCatalogProduct::GetOptimalPrice($arElement["ID"], 1, $USER->GetUserGroupArray(), "N");
                    
                    if (!empty($arPrice)) {
                        $arElement["~PRICE"] = $arElement["PRICE"] = $arPrice["RESULT_PRICE"]["DISCOUNT_PRICE"];
                        $arElement["PRICE"] = CurrencyFormat($arElement["PRICE"], $arPrice["RESULT_PRICE"]["CURRENCY"]);
                    }
                    
                    $postMess = CEventMessage::GetList($by = "site_id", $order = "desc", array("TYPE" => "SALE_DRESSCODE_FASTBACK_SEND"))->GetNext();
                    
                    if (empty($postMess)) {
                        
                        $MESSAGE = "<h3>С сайта #SITE# поступил новый заказ. </h3> <p> Товар: <b>#PRODUCT#</b>  <br /> Имя: <b>#NAME#</b> <br /> Телефон: <b>#PHONE#</b> <br /> Ссылка: #PRODUCT_URL# <br /> Комментарий: #COMMENT#";
                        $FIELDS = "#SITE# \n #PRODUCT# \n #NAME# \n #PHONE# \n #COMMENT# \n";
                        
                        $et = new CEventType;
                        $et->Add(
                            array(
                                "LID" => "ru",
                                "EVENT_NAME" => "SALE_DRESSCODE_FASTBACK_SEND",
                                "NAME" => "Купить в один клик",
                                "DESCRIPTION" => $FIELDS
                            )
                        );
                        
                        $arr["ACTIVE"] = "Y";
                        $arr["EVENT_NAME"] = "SALE_DRESSCODE_FASTBACK_SEND";
                        $arr["LID"] = SITE_ID;
                        $arr["EMAIL_FROM"] = COption::GetOptionString('main', 'email_from', 'webmaster@webmaster.com');
                        $arr["EMAIL_TO"] = COption::GetOptionString("sale", "order_email");
                        $arr["BCC"] = COption::GetOptionString("main", 'email_from', 'webmaster@webmaster.com');
                        $arr["SUBJECT"] = "Заказ товара";
                        $arr["BODY_TYPE"] = "html";
                        $arr["MESSAGE"] = $MESSAGE;
                        
                        $emess = new CEventMessage;
                        $emess->Add($arr);
                        
                    }
                    $phone = BX_UTF != 1 ? iconv("UTF-8", "windows-1251//IGNORE", htmlspecialcharsbx($_GET["phone"])) : htmlspecialcharsbx($_GET["phone"]);
                    
                    $phone = str_replace(['+7', '(', ')', ' ', '-'], '', $phone);
                    
                    $PRODUCT_URL = "<a href=";
                    $PRODUCT_URL .= (CMain::IsHTTPS()) ? "https://" : "http://";
                    $PRODUCT_URL .= SITE_SERVER_NAME . $arElement["DETAIL_PAGE_URL"];
                    $PRODUCT_URL .= "\">" . $arElement["NAME"] . "</a>";
                    
                    $arMessage = array(
                        "SITE" => SITE_SERVER_NAME,
                        "PRODUCT" => $arElement["NAME"] . " (ID:" . $arElement["ID"] . " )" . " - " . $arElement["PRICE"],
                        "PRODUCT_URL" => $PRODUCT_URL,
                        "NAME" => BX_UTF != 1 ? iconv("UTF-8", "windows-1251//IGNORE", htmlspecialcharsbx($_GET["name"])) : htmlspecialcharsbx($_GET["name"]),
                        "PHONE" => $phone,
                        "COMMENT" => BX_UTF != 1 ? iconv("UTF-8", "windows-1251//IGNORE", htmlspecialcharsbx($_GET["message"])) : htmlspecialcharsbx($_GET["message"])
                    );
                    
                    CEvent::SendImmediate("SALE_DRESSCODE_FASTBACK_SEND", htmlspecialcharsbx($_GET["SITE_ID"]), $arMessage, "Y", false);
                    
                    $FORM_ID = 9;
                    
                    $arValues = array(
                        "form_text_80" => $arMessage['NAME'],
                        "form_text_81" => $arMessage['PHONE'],
                        "form_textarea_82" => $arMessage['COMMENT'],
                        "form_text_83" => $arMessage['PRODUCT'],
                        "form_text_84" => $GLOBALS['medi']["region_cities"][SITE_ID]
                    );
                    
                    $obElm = CIBlockElement::GetList([], ["ID" => $arElement["ID"]], false, false, ["IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "CATALOG_PRICE_" . $GLOBALS['medi']['price_id'][SITE_ID]]);
                    if ($arElm = $obElm->GetNext()) {
                        $obElmProp = CIBlockElement::GetList([], ["ID" => $arElement["ID"], "IBLOCK_ID" => $arElm['IBLOCK_ID']], false, false, ["ID", "NAME", "PROPERTY_CML2_LINK", "PROPERTY_CML2_LINK.IBLOCK_ID", "PROPERTY_ATT_BRAND.NAME", "PROPERTY_CML2_ARTICLE"]);
                        if ($arElmProp = $obElmProp->GetNext()) {
                            $brand = '';
                            $article = $arElmProp['PROPERTY_CML2_ARTICLE_VALUE'];
                            // sku
                            if ($arElmProp['PROPERTY_CML2_LINK_VALUE']) {
                                $obElmBrand = CIBlockElement::GetList([], ["IBLOCK_ID" => $arElmProp['PROPERTY_CML2_LINK_IBLOCK_ID'], "ID" => $arElmProp['PROPERTY_CML2_LINK_VALUE']], false, false, ["ID", "NAME", "PROPERTY_ATT_BRAND.NAME"]);
                                
                                if ($arElmBrand = $obElmBrand->GetNext()) {
                                    $brand = $arElmBrand['PROPERTY_ATT_BRAND_NAME'];
                                    $goodId = $arElmProp['ID'];
                                    $goodName = $arElmBrand['NAME'];
                                }
                                
                            } // simple
                            elseif ($arElmProp['PROPERTY_ATT_BRAND_NAME']) {
                                
                                $brand = $arElmProp['PROPERTY_ATT_BRAND_NAME'];
                                
                                $goodId = $arElmProp['ID'];
                                $goodName = $arElmProp['NAME'];
                                
                            }
                            
                        }
                    }
                    
                    $secturl = explode("/", $arElm['DETAIL_PAGE_URL']);
                    $sectcount = count($secturl) - 1;
                    unset($secturl[$sectcount]);
                    unset($secturl[0]);
                    unset($secturl[1]);
                    
                    $arProduct = array(
                        'id' => $goodId,
                        'q' => 1,
                        'price' => $arElement["~PRICE"], //$arElm["CATALOG_PRICE_".$GLOBALS['medi']['price_id'][SITE_ID]],
                        'article' => $article,
                        'name' => $goodName,
                        'category' => implode("/", $secturl),
                        'brand' => $brand
                    );
                    
                    // создадим новый результат
                    if ($RESULT_ID = CFormResult::Add($FORM_ID, $arValues)) {
                        if (!isset($_COOKIE['medi_cfos'])) {
                            
                            setcookie('medi_cfos', $arElement["~PRICE"], time() + 365 * 86400, "/");
                            $arProduct['gdeslon']['user'] = '002';
                            $arProduct['gdeslon']['sum'] = $arElement["~PRICE"];
                        } else {
                            setcookie('medi_cfos', $arElement["~PRICE"] + intval($_COOKIE['medi_cfos']), time() + 365 * 86400, "/");
                            $arProduct['gdeslon']['user'] = '001';
                            $arProduct['gdeslon']['sum'] = $arElement["~PRICE"] + intval($_COOKIE['medi_cfos']);
                        }
                        global $USER;
                        $nUserID = "0";
                        $nUserID = $USER->GetID();
                        $arProduct['gdeslon']['user_id'] = $nUserID;
                        
                        
                        $result = array(
                            "heading" => "Ваш заказ успешно отправлен",
                            "message" => "В ближайшее время Вам перезвонит наш специалист для уточнения деталей заказа.",
                            "success" => true,
                            "result_id" => $RESULT_ID,
                            "product" => $arProduct
                        );
                    } else {
                        global $strError;
                        $result = array(
                            "heading" => "Ошибка",
                            "message" => "Ошибка, заказ не создан!",
                            "success" => false
                        );
                        //echo $strError;
                    }
                    
                    //new order
                    
                    //basket object
                    /*$basketAjax = DwBasket::getInstance();
                            $basket = $basketAjax->getBasket();
    
                            //clearBasket
                            $basketAjax->clearBasket();
    
                            //Добавление товара
                            $item = $basket->createItem("catalog", intval($arElement["ID"]));
                            $item->setFields([
                                "QUANTITY" => 1,
                                "CURRENCY" => $basketAjax->getCurrencyCode(),
                                "LID" => $_GET["SITE_ID"],
                                "PRODUCT_PROVIDER_CLASS" => "Bitrix\Catalog\Product\CatalogProvider",
                                "CATALOG_XML_ID" => $arElement["IBLOCK_EXTERNAL_ID"],
                                "PRODUCT_XML_ID" => $arElement["EXTERNAL_ID"],
                            ]);
    
                            //Сохранение изменений
                            $basket->save();
    
                            //set siteId
                            $basketAjax->setSiteId(htmlspecialcharsbx($_GET["SITE_ID"]));
    
                            //get items
                            $arBasketItems = $basketAjax->getBasketItems();
    
                            //compilation
                            $arOrder = $basketAjax->getOrderInfo();
    
                            //order
                            $order = $basketAjax->getOrder();
    
                            //get collection
                            $propertyCollection = $order->getPropertyCollection();
    
                            //set properties
                            //set phone
                            if(!empty($_GET["phone"])){
                                $propertyCollection = $order->getPropertyCollection();
                                if($phoneProp = $propertyCollection->getPhone()){
                                    $phoneProp->setValue(!defined("BX_UTF") ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["phone"])) : htmlspecialcharsbx($_GET["phone"]));
                                }
                            }
    
                            //set name
                            if(!empty($_GET["name"])){
                                if($nameProp = $propertyCollection->getPayerName()){
                                    $nameProp->setValue(!defined("BX_UTF") ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["name"])) : htmlspecialcharsbx($_GET["name"]));
                                }
                            }
    
                            //order comment
                            if(!empty($_GET["message"])){
                               $basketAjax->setOrderComment(!defined("BX_UTF") ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["message"])) : htmlspecialcharsbx($_GET["message"]));
                            }
    
                            //prepare
                            $order->doFinalAction(true);
    
                            //save order
                            $orderStatus = $order->save();
    
                            //check success
                            if(!$orderStatus->isSuccess()){
    
                                //get errors for debugging
                                $errors = $orderStatus->getErrors();
    
                                //push
                                $result = array(
                                    "heading" => "Ошибка",
                                    "message" => "Ошибка, заказ не создан!",
                                    "success" => false
                                );
    
                            }
    
                            if(empty($result)){
                                $result = array(
                                    "heading" => "Ваш заказ успешно отправлен",
                                    "message" => "В ближайшее время Вам перезвонит наш специалист для уточнения деталей заказа.",
                                    "success" => true
                                );
                            }*/
                    
                } else {
                    
                    $result = array(
                        "heading" => "Ошибка",
                        "message" => "Ошибка, товар не найден!",
                        "success" => false
                    );
                    
                }
                
            }
            
        } else {
            $result = array(
                "heading" => "Ошибка",
                "message" => "Ошибка, заполните обязательные поля!",
                "success" => false
            );
        }
        
        echo \Bitrix\Main\Web\Json::encode($result);
        
    } elseif ($_GET["act"] == "getFastOrder") {
        
        if (!empty($_GET["id"])) {
            
            //globals
            global $USER;
            
            $OPTION_CURRENCY = CCurrency::GetBaseCurrency();
            $arResult = array();
            
            $res = CIBlockElement::GetList(array(), array("ID" => intval($_GET["id"])), false, false, array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "NAME", "CATALOG_QUANTITY"));
            if ($arRes = $res->GetNextElement()) {
                
                $arResult["PRODUCT"] = $arRes->GetFields();
                $arResult["PRODUCT"]["PROPERTIES"] = $arRes->GetProperties();
                $arTmpPrice = CCatalogProduct::GetOptimalPrice($arResult["PRODUCT"]["ID"], 1, $USER->GetUserGroupArray()/*, 'N', ['ID' => $GLOBALS['price_id']]*/);
                $arResult["PRODUCT"]["PICTURE"] = CFile::ResizeImageGet($arResult["PRODUCT"]["DETAIL_PICTURE"], array("width" => 270, "height" => 230), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 80);
                $arResult["PRODUCT"]["PICTURE"]["src"] = !empty($arResult["PRODUCT"]["PICTURE"]["src"]) ? $arResult["PRODUCT"]["PICTURE"]["src"] : SITE_TEMPLATE_PATH . "/images/empty.png";
                $arResult["PRODUCT"]["PRICE"]["PRICE_FORMATED"] = CurrencyFormat($arTmpPrice["DISCOUNT_PRICE"], $OPTION_CURRENCY);
                $arResult["PRODUCT"]["PRICE"]["PRICE"] = $arTmpPrice["DISCOUNT_PRICE"];
                
                if (empty($arResult["PRODUCT"]["DETAIL_PICTURE"])) {
                    $skuProductInfo = CCatalogSKU::getProductList($arResult["PRODUCT"]["ID"]);
                    if (!empty($skuProductInfo)) {
                        foreach ($skuProductInfo as $itx => $skuProductInfoElement) {
                            $productBySku = CIBlockElement::GetByID($skuProductInfoElement["ID"]);
                            if (!empty($productBySku)) {
                                if ($arResProductSku = $productBySku->GetNextElement()) {
                                    $arResProductSkuFields = $arResProductSku->GetFields();
                                    if (!empty($arResProductSkuFields["DETAIL_PICTURE"])) {
                                        $arResult["PRODUCT"]["PICTURE"] = CFile::ResizeImageGet($arResProductSkuFields["DETAIL_PICTURE"], array("width" => 270, "height" => 230), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 80);
                                    }
                                }
                            }
                        }
                    }
                }
                
                if ($arTmpPrice["RESULT_PRICE"]["BASE_PRICE"] != $arTmpPrice["RESULT_PRICE"]["DISCOUNT_PRICE"]) {
                    $arResult["PRODUCT"]["PRICE"]["PRICE_FORMATED"] .= ' <s class="discount">' . CurrencyFormat($arTmpPrice["RESULT_PRICE"]["BASE_PRICE"], $OPTION_CURRENCY) . '</s>';
                }
                
                if (!empty($arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE"])) {
                    $mSt = '';
                    foreach ($arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE"] as $ifv => $marker) {
                        $background = strstr($arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv], "#") ? $arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv] : "#424242";
                        $mStr .= '<div class="marker" style="background-color: ' . $background . '">' . $marker . '</div>';
                    }
                    
                    $arResult["PRODUCT"]["MARKER"] = $mStr;
                }
                
                if ($USER->IsAuthorized()) {
                    $rsUser = CUser::GetByID($USER->GetID());
                    $arUser = $rsUser->Fetch();
                    if (!empty($arUser)) {
                        $arResult["PRODUCT"]["USER_NAME"] = $USER->GetFullName();
                        $arResult["PRODUCT"]["USER_PHONE"] = $arUser["PERSONAL_MOBILE"];
                    }
                }
                
                $obElm = CIBlockElement::GetList([], ["ID" => $arResult["PRODUCT"]['ID']], false, false, ["IBLOCK_ID"]);
                if ($arElm = $obElm->GetNext()) {
                    $obElmProp = CIBlockElement::GetList([], ["ID" => $arResult["PRODUCT"]['ID'], "IBLOCK_ID" => $arElm['IBLOCK_ID']], false, false, ["ID", "NAME", "PROPERTY_CML2_LINK", "PROPERTY_CML2_LINK.IBLOCK_ID", "PROPERTY_ATT_BRAND.NAME", "PROPERTY_CML2_ARTICLE"]);
                    if ($arElmProp = $obElmProp->GetNext()) {
                        $brand = '';
                        $article = $arElmProp['PROPERTY_CML2_ARTICLE_VALUE'];
                        // sku
                        if ($arElmProp['PROPERTY_CML2_LINK_VALUE']) {
                            $obElmBrand = CIBlockElement::GetList([], ["IBLOCK_ID" => $arElmProp['PROPERTY_CML2_LINK_IBLOCK_ID'], "ID" => $arElmProp['PROPERTY_CML2_LINK_VALUE']], false, false, ["ID", "NAME", "PROPERTY_ATT_BRAND.NAME"]);
                            
                            if ($arElmBrand = $obElmBrand->GetNext()) {
                                $brand = $arElmBrand['PROPERTY_ATT_BRAND_NAME'];
                                $goodId = $arElmProp['ID'];
                                $goodName = $arElmBrand['NAME'];
                            }
                            
                        } // simple
                        elseif ($arElmProp['PROPERTY_ATT_BRAND_NAME']) {
                            
                            $brand = $arElmProp['PROPERTY_ATT_BRAND_NAME'];
                            
                            $goodId = $arElmProp['ID'];
                            $goodName = $arElmProp['NAME'];
                            
                        }
                        
                    }
                }
                
                $secturl = explode("/", $arResult["PRODUCT"]['DETAIL_PAGE_URL']);
                $sectcount = count($secturl) - 1;
                unset($secturl[$sectcount]);
                unset($secturl[0]);
                unset($secturl[1]);
                
                $arResult["PRODUCT"]['ITEM'] = array(
                    'id' => $goodId,
                    'q' => 1,
                    'price' => $arResult["PRODUCT"]["PRICE"]["PRICE"],
                    'article' => $article,
                    'name' => $goodName,
                    'category' => implode("/", $secturl),
                    'brand' => $brand
                );
                
            }
            
            if (!empty($arResult)) {
                echo \Bitrix\Main\Web\Json::encode(array($arResult["PRODUCT"]));
            }
            
        }
        
    } elseif ($_GET["act"] === "fastOrder") {
        
        if (!empty($_GET["phone"]) && !empty($_GET["id"])) {
            
            if (CModule::IncludeModule("iblock") && CModule::IncludeModule("sale") && CModule::IncludeModule("catalog") && CModule::IncludeModule("dw.deluxe") && CModule::IncludeModule("form")) {
                $OPTION_CURRENCY = CCurrency::GetBaseCurrency();
                $arElement = CIBlockElement::GetByID(intval($_GET["id"]))->GetNext();
                if (!empty($arElement)) {
                    
                    $arPrice = CCatalogProduct::GetOptimalPrice($arElement["ID"], 1, $USER->GetUserGroupArray(), "N");
                    
                    if (!empty($arPrice)) {
                        $arElement["~PRICE"] = $arElement["PRICE"] = $arPrice["RESULT_PRICE"]["DISCOUNT_PRICE"];
                        $arElement["PRICE"] = CurrencyFormat($arElement["PRICE"], $arPrice["RESULT_PRICE"]["CURRENCY"]);
                    }
                    
                    $postMess = CEventMessage::GetList($by = "site_id", $order = "desc", array("TYPE" => "SALE_FASTORDER_SEND"))->GetNext();
                    
                    if (empty($postMess)) {
                        
                        $MESSAGE = "<h3>С сайта #SITE# поступил новый заказ. </h3> <p> Товар: <b>#PRODUCT#</b>  <br /> Имя: <b>#NAME#</b> <br /> Телефон: <b>#PHONE#</b> <br /> Ссылка: #PRODUCT_URL# <br /> Комментарий: #COMMENT#";
                        $FIELDS = "#SITE# \n #PRODUCT# \n #NAME# \n #PHONE# \n #COMMENT# \n";
                        
                        $et = new CEventType;
                        $et->Add(
                            array(
                                "LID" => "ru",
                                "EVENT_NAME" => "SALE_FASTORDER_SEND",
                                "NAME" => "Быстрый заказ",
                                "DESCRIPTION" => $FIELDS
                            )
                        );
                        
                        $arr["ACTIVE"] = "Y";
                        $arr["EVENT_NAME"] = "SALE_FASTORDER_SEND";
                        $arr["LID"] = SITE_ID;
                        $arr["EMAIL_FROM"] = COption::GetOptionString('main', 'email_from', 'webmaster@webmaster.com');
                        $arr["EMAIL_TO"] = COption::GetOptionString("sale", "order_email");
                        $arr["BCC"] = COption::GetOptionString("main", 'email_from', 'webmaster@webmaster.com');
                        $arr["SUBJECT"] = "Быстрый заказ";
                        $arr["BODY_TYPE"] = "html";
                        $arr["MESSAGE"] = $MESSAGE;
                        
                        $emess = new CEventMessage;
                        $emess->Add($arr);
                        
                    }
                    
                    
                    $PRODUCT_URL = "<a href=";
                    $PRODUCT_URL .= (CMain::IsHTTPS()) ? "https://" : "http://";
                    $PRODUCT_URL .= SITE_SERVER_NAME . $arElement["DETAIL_PAGE_URL"];
                    $PRODUCT_URL .= "\">" . $arElement["NAME"] . "</a>";
                    
                    $phone = BX_UTF != 1 ? iconv("UTF-8", "windows-1251//IGNORE", htmlspecialcharsbx($_GET["phone"])) : htmlspecialcharsbx($_GET["phone"]);
                    
                    $phone = str_replace(['+7', '(', ')', ' ', '-'], '', $phone);
                    
                    $arMessage = array(
                        "SITE" => SITE_SERVER_NAME,
                        "PRODUCT" => $arElement["NAME"] . " (ID:" . $arElement["ID"] . " )" . " - " . $arElement["PRICE"],
                        "PRODUCT_URL" => $PRODUCT_URL,
                        "NAME" => BX_UTF != 1 ? iconv("UTF-8", "windows-1251//IGNORE", htmlspecialcharsbx($_GET["name"])) : htmlspecialcharsbx($_GET["name"]),
                        "PHONE" => $phone,
                        "COMMENT" => BX_UTF != 1 ? iconv("UTF-8", "windows-1251//IGNORE", htmlspecialcharsbx($_GET["message"])) : htmlspecialcharsbx($_GET["message"])
                    );
                    
                    CEvent::SendImmediate("SALE_FASTORDER_SEND", htmlspecialcharsbx($_GET["SITE_ID"]), $arMessage, "Y", false);
                    
                    $FORM_ID = 13;
                    
                    $arValues = array(
                        "form_text_175" => $arMessage['NAME'],
                        "form_text_176" => $arMessage['PHONE'],
                        "form_textarea_177" => $arMessage['COMMENT'],
                        "form_text_178" => $arMessage['PRODUCT'],
                        "form_text_179" => $GLOBALS['medi']["region_cities"][SITE_ID]
                    );
                    
                    $obElm = CIBlockElement::GetList([], ["ID" => $arElement["ID"]], false, false, ["IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "CATALOG_PRICE_" . $GLOBALS['medi']['price_id'][SITE_ID]]);
                    if ($arElm = $obElm->GetNext()) {
                        $obElmProp = CIBlockElement::GetList([], ["ID" => $arElement["ID"], "IBLOCK_ID" => $arElm['IBLOCK_ID']], false, false, ["ID", "NAME", "PROPERTY_CML2_LINK", "PROPERTY_CML2_LINK.IBLOCK_ID", "PROPERTY_ATT_BRAND.NAME", "PROPERTY_CML2_ARTICLE"]);
                        if ($arElmProp = $obElmProp->GetNext()) {
                            $brand = '';
                            $article = $arElmProp['PROPERTY_CML2_ARTICLE_VALUE'];
                            // sku
                            if ($arElmProp['PROPERTY_CML2_LINK_VALUE']) {
                                $obElmBrand = CIBlockElement::GetList([], ["IBLOCK_ID" => $arElmProp['PROPERTY_CML2_LINK_IBLOCK_ID'], "ID" => $arElmProp['PROPERTY_CML2_LINK_VALUE']], false, false, ["ID", "NAME", "PROPERTY_ATT_BRAND.NAME"]);
                                
                                if ($arElmBrand = $obElmBrand->GetNext()) {
                                    $brand = $arElmBrand['PROPERTY_ATT_BRAND_NAME'];
                                    $goodId = $arElmProp['ID'];
                                    $goodName = $arElmBrand['NAME'];
                                }
                                
                            } // simple
                            elseif ($arElmProp['PROPERTY_ATT_BRAND_NAME']) {
                                
                                $brand = $arElmProp['PROPERTY_ATT_BRAND_NAME'];
                                
                                $goodId = $arElmProp['ID'];
                                $goodName = $arElmProp['NAME'];
                                
                            }
                            
                        }
                    }
                    
                    $secturl = explode("/", $arElm['DETAIL_PAGE_URL']);
                    $sectcount = count($secturl) - 1;
                    unset($secturl[$sectcount]);
                    unset($secturl[0]);
                    unset($secturl[1]);
                    
                    $arProduct = array(
                        'id' => $goodId,
                        'q' => 1,
                        'price' => $arElement["~PRICE"], //$arElm["CATALOG_PRICE_".$GLOBALS['medi']['price_id'][SITE_ID]],
                        'article' => $article,
                        'name' => $goodName,
                        'category' => implode("/", $secturl),
                        'brand' => $brand
                    );
                    
                    // создадим новый результат
                    if ($RESULT_ID = CFormResult::Add($FORM_ID, $arValues)) {
                        if (!isset($_COOKIE['medi_cfos'])) {
                            
                            setcookie('medi_cfos', $arElement["~PRICE"], time() + 365 * 86400, "/");
                            $arProduct['gdeslon']['user'] = '002';
                            $arProduct['gdeslon']['sum'] = $arElement["~PRICE"];
                        } else {
                            setcookie('medi_cfos', $arElement["~PRICE"] + intval($_COOKIE['medi_cfos']), time() + 365 * 86400, "/");
                            $arProduct['gdeslon']['user'] = '001';
                            $arProduct['gdeslon']['sum'] = $arElement["~PRICE"] + intval($_COOKIE['medi_cfos']);
                        }
                        
                        global $USER;
                        $nUserID = "0";
                        $nUserID = $USER->GetID();
                        $arProduct['gdeslon']['user_id'] = $nUserID;
                        
                        
                        $result = array(
                            "heading" => "Ваш заказ успешно отправлен",
                            "message" => "В ближайшее время Вам перезвонит наш специалист для уточнения деталей заказа.",
                            "success" => true,
                            "result_id" => $RESULT_ID,
                            "product" => $arProduct
                        );
                    } else {
                        global $strError;
                        $result = array(
                            "heading" => "Ошибка",
                            "message" => "Ошибка, заказ не создан!",
                            "success" => false
                        );
                        //echo $strError;
                    }
                    
                    //new order
                    
                    //basket object
                    /*$basketAjax = DwBasket::getInstance();
                        $basket = $basketAjax->getBasket();
    
                        //clearBasket
                        $basketAjax->clearBasket();
    
                        //Добавление товара
                        $item = $basket->createItem("catalog", intval($arElement["ID"]));
                        $item->setFields([
                            "QUANTITY" => 1,
                            "CURRENCY" => $basketAjax->getCurrencyCode(),
                            "LID" => $_GET["SITE_ID"],
                            "PRODUCT_PROVIDER_CLASS" => "Bitrix\Catalog\Product\CatalogProvider",
                            "CATALOG_XML_ID" => $arElement["IBLOCK_EXTERNAL_ID"],
                            "PRODUCT_XML_ID" => $arElement["EXTERNAL_ID"],
                        ]);
    
                        //Сохранение изменений
                        $basket->save();
    
                        //set siteId
                        $basketAjax->setSiteId(htmlspecialcharsbx($_GET["SITE_ID"]));
    
                        //get items
                        $arBasketItems = $basketAjax->getBasketItems();
    
                        //compilation
                        $arOrder = $basketAjax->getOrderInfo();
    
                        //order
                        $order = $basketAjax->getOrder();
    
                        //get collection
                        $propertyCollection = $order->getPropertyCollection();
    
                        //set properties
                        //set phone
                        if(!empty($_GET["phone"])){
                            $propertyCollection = $order->getPropertyCollection();
                            if($phoneProp = $propertyCollection->getPhone()){
                                $phoneProp->setValue(!defined("BX_UTF") ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["phone"])) : htmlspecialcharsbx($_GET["phone"]));
                            }
                        }
    
                        //set name
                        if(!empty($_GET["name"])){
                            if($nameProp = $propertyCollection->getPayerName()){
                                $nameProp->setValue(!defined("BX_UTF") ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["name"])) : htmlspecialcharsbx($_GET["name"]));
                            }
                        }
    
                        //order comment
                        if(!empty($_GET["message"])){
                           $basketAjax->setOrderComment(!defined("BX_UTF") ? iconv("UTF-8","windows-1251//IGNORE", htmlspecialcharsbx($_GET["message"])) : htmlspecialcharsbx($_GET["message"]));
                        }
    
                        //prepare
                        $order->doFinalAction(true);
    
                        //save order
                        $orderStatus = $order->save();
    
                        //check success
                        if(!$orderStatus->isSuccess()){
    
                            //get errors for debugging
                            $errors = $orderStatus->getErrors();
    
                            //push
                            $result = array(
                                "heading" => "Ошибка",
                                "message" => "Ошибка, заказ не создан!",
                                "success" => false
                            );
    
                        }
    
                        if(empty($result)){
                            $result = array(
                                "heading" => "Ваш заказ успешно отправлен",
                                "message" => "В ближайшее время Вам перезвонит наш специалист для уточнения деталей заказа.",
                                "success" => true
                            );
                        }*/
                    
                } else {
                    
                    $result = array(
                        "heading" => "Ошибка",
                        "message" => "Ошибка, товар не найден!",
                        "success" => false
                    );
                    
                }
                
            }
            
        } else {
            $result = array(
                "heading" => "Ошибка",
                "message" => "Ошибка, заполните обязательные поля!",
                "success" => false
            );
        }
        
        echo \Bitrix\Main\Web\Json::encode($result);
        
    } elseif ($_GET["act"] == "getSmpFastOrder") {
        
        if (empty($_GET["id"])) {
            echo "error";
            return;
        }
        
        //globals
        global $USER;
        
        $OPTION_CURRENCY = CCurrency::GetBaseCurrency();
        $arResult = array();
        
        $res = CIBlockElement::GetList(array(), array("ID" => intval($_GET["id"])), false, false,
            array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_PICTURE", "NAME", "CATALOG_QUANTITY"));
        if ($arRes = $res->GetNextElement()) {
            $arResult["PRODUCT"] = $arRes->GetFields();
            $arResult["PRODUCT"]["PROPERTIES"] = $arRes->GetProperties();
            $arTmpPrice = CCatalogProduct::GetOptimalPrice($arResult["PRODUCT"]["ID"], 1, $USER->GetUserGroupArray()
            /*, 'N', ['ID' => $GLOBALS['price_id']]*/);
            $arResult["PRODUCT"]["PICTURE"] = CFile::ResizeImageGet($arResult["PRODUCT"]["DETAIL_PICTURE"],
                array("width" => 270, "height" => 230), BX_RESIZE_IMAGE_PROPORTIONAL, false, false, false, 80);
            $arResult["PRODUCT"]["PICTURE"]["src"] = !empty($arResult["PRODUCT"]["PICTURE"]["src"]) ?
                $arResult["PRODUCT"]["PICTURE"]["src"] : SITE_TEMPLATE_PATH . "/images/empty.png";
            $arResult["PRODUCT"]["PRICE"]["PRICE_FORMATED"] = CurrencyFormat($arTmpPrice["DISCOUNT_PRICE"],
                $OPTION_CURRENCY);
            $arResult["PRODUCT"]["PRICE"]["PRICE"] = $arTmpPrice["DISCOUNT_PRICE"];
            
            if (empty($arResult["PRODUCT"]["DETAIL_PICTURE"])) {
                $skuProductInfo = CCatalogSKU::getProductList($arResult["PRODUCT"]["ID"]);
                if (!empty($skuProductInfo)) {
                    foreach ($skuProductInfo as $itx => $skuProductInfoElement) {
                        $productBySku = CIBlockElement::GetByID($skuProductInfoElement["ID"]);
                        if (!empty($productBySku)) {
                            $arResProductSku = $productBySku->GetNextElement();
                            
                            $arResProductSkuFields = $arResProductSku->GetFields();
                            if (!empty($arResProductSkuFields["DETAIL_PICTURE"])) {
                                $arResult["PRODUCT"]["PICTURE"] =
                                    CFile::ResizeImageGet($arResProductSkuFields["DETAIL_PICTURE"],
                                        array("width" => 270, "height" => 230), BX_RESIZE_IMAGE_PROPORTIONAL,
                                        false, false, false, 80);
                            }
                            
                        }
                    }
                }
            }
            
            if ($arTmpPrice["RESULT_PRICE"]["BASE_PRICE"] != $arTmpPrice["RESULT_PRICE"]["DISCOUNT_PRICE"]) {
                $arResult["PRODUCT"]["PRICE"]["PRICE_FORMATED"] .=
                    ' <s class="discount">' . CurrencyFormat($arTmpPrice["RESULT_PRICE"]["BASE_PRICE"], $OPTION_CURRENCY)
                    . '</s>';
            }
            
            if (!empty($arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE"])) {
                $mSt = '';
                foreach ($arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE"] as $ifv => $marker) {
                    $background = strstr($arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv], "#") ? $arResult["PRODUCT"]["PROPERTIES"]["OFFERS"]["VALUE_XML_ID"][$ifv] : "#424242";
                    $mStr .= '<div class="marker" style="background-color: ' . $background . '">' . $marker . '</div>';
                }
                
                $arResult["PRODUCT"]["MARKER"] = $mStr;
            }
            
            if ($USER->IsAuthorized()) {
                $rsUser = CUser::GetByID($USER->GetID());
                $arUser = $rsUser->Fetch();
                if (!empty($arUser)) {
                    $arResult["PRODUCT"]["USER_NAME"] = $USER->GetFullName();
                    $arResult["PRODUCT"]["USER_PHONE"] = $arUser["PERSONAL_MOBILE"];
                }
            }
            
            $obElm = CIBlockElement::GetList([], ["ID" => $arResult["PRODUCT"]['ID']], false, false, ["IBLOCK_ID"]);
            if ($arElm = $obElm->GetNext()) {
                $obElmProp = CIBlockElement::GetList([], ["ID" => $arResult["PRODUCT"]['ID'], "IBLOCK_ID" => $arElm['IBLOCK_ID']], false, false, ["ID", "NAME", "PROPERTY_CML2_LINK", "PROPERTY_CML2_LINK.IBLOCK_ID", "PROPERTY_ATT_BRAND.NAME", "PROPERTY_CML2_ARTICLE"]);
                if ($arElmProp = $obElmProp->GetNext()) {
                    $brand = '';
                    $article = $arElmProp['PROPERTY_CML2_ARTICLE_VALUE'];
                    // sku
                    if ($arElmProp['PROPERTY_CML2_LINK_VALUE']) {
                        $obElmBrand = CIBlockElement::GetList([], ["IBLOCK_ID" => $arElmProp['PROPERTY_CML2_LINK_IBLOCK_ID'], "ID" => $arElmProp['PROPERTY_CML2_LINK_VALUE']], false, false, ["ID", "NAME", "PROPERTY_ATT_BRAND.NAME"]);
                        
                        if ($arElmBrand = $obElmBrand->GetNext()) {
                            $brand = $arElmBrand['PROPERTY_ATT_BRAND_NAME'];
                            $goodId = $arElmProp['ID'];
                            $goodName = $arElmBrand['NAME'];
                        }
                        
                    } // simple
                    elseif ($arElmProp['PROPERTY_ATT_BRAND_NAME']) {
                        
                        $brand = $arElmProp['PROPERTY_ATT_BRAND_NAME'];
                        
                        $goodId = $arElmProp['ID'];
                        $goodName = $arElmProp['NAME'];
                        
                    }
                    
                }
            }
            
            $secturl = explode("/", $arResult["PRODUCT"]['DETAIL_PAGE_URL']);
            $sectcount = count($secturl) - 1;
            unset($secturl[$sectcount]);
            unset($secturl[0]);
            unset($secturl[1]);
            
            $arResult["PRODUCT"]['ITEM'] = array(
                'id' => $goodId,
                'q' => 1,
                'price' => $arResult["PRODUCT"]["PRICE"]["PRICE"],
                'article' => $article,
                'name' => $goodName,
                'category' => implode("/", $secturl),
                'brand' => $brand
            );
            
        }
        
        if (!empty($arResult)) {
            echo \Bitrix\Main\Web\Json::encode(array($arResult["PRODUCT"]));
        }
        
        
    } elseif ($_REQUEST["act"] === "SmpFastOrder") {
        
        if (!empty($_REQUEST["phone"]) && !empty($_REQUEST["id"])) {
            
            if (CModule::IncludeModule("iblock")
                && CModule::IncludeModule("sale")
                && CModule::IncludeModule("catalog")
                && CModule::IncludeModule("dw.deluxe")
                && CModule::IncludeModule("form")) {
                
                $OPTION_CURRENCY = CCurrency::GetBaseCurrency();
                $arElement = CIBlockElement::GetByID(intval($_REQUEST["id"]))->GetNext();
                if (!empty($arElement)) {
                    
                    $arPrice = CCatalogProduct::GetOptimalPrice($arElement["ID"], 1, $USER->GetUserGroupArray(), "N");
                    
                    if (!empty($arPrice)) {
                        $arElement["~PRICE"] = $arElement["PRICE"] = $arPrice["RESULT_PRICE"]["DISCOUNT_PRICE"];
                        $arElement["PRICE"] = CurrencyFormat($arElement["PRICE"], $arPrice["RESULT_PRICE"]["CURRENCY"]);
                    }
                    
                    $postMess = CEventMessage::GetList($by = "site_id", $order = "desc",
                        array("TYPE" => "SALE_SMPORDER_SEND"))->GetNext();
                    
                    $PRODUCT_URL = "<a href=";
                    $PRODUCT_URL .= (CMain::IsHTTPS()) ? "https://" : "http://";
                    $PRODUCT_URL .= SITE_SERVER_NAME . $arElement["DETAIL_PAGE_URL"];
                    $PRODUCT_URL .= "\">" . $arElement["NAME"] . "</a>";
                    
                    $phone = BX_UTF != 1 ? iconv("UTF-8", "windows-1251//IGNORE",
                        htmlspecialcharsbx($_REQUEST["phone"])) : htmlspecialcharsbx($_REQUEST["phone"]);
                    
                    $phone = str_replace(['+', '(', ')', ' ', '-'], '', $phone);
                    
                    $file_src = '';
                    if (intval($_REQUEST["file"]) > 0) {
                        $file_src = "https://www.medi-salon.ru" . CFile::GetFileArray(intval($_REQUEST["file"]))['SRC'];
                    }
                    
                    $arMessage = array(
                        "SITE" => SITE_SERVER_NAME,
                        "PRODUCT" => $arElement["NAME"] . " (ID:" . $arElement["ID"] . " )" .
                            " - " . $arElement["PRICE"],
                        "PRODUCT_URL" => $PRODUCT_URL,
                        "NAME" => BX_UTF != 1 ?
                            iconv("UTF-8", "windows-1251//IGNORE",
                                htmlspecialcharsbx($_REQUEST["name"])) : htmlspecialcharsbx($_REQUEST["name"]),
                        "PHONE" => $phone,
                        "GPO" => $_REQUEST['GPOSmpFastOrder'] == 'on' ? "259" : "",
                        "URGENT" => $_REQUEST['urgentOrder'] == 'on' ? ($_SERVER['SERVER_NAME'] == 'www.medi-salon.ru' ?
                            "261" : "260") : "",
                        "URGENT_SUBJ" => $_REQUEST['urgentOrder'] == 'on' ? "СРОЧНЫЙ" : "",
                        "FILE" => $file_src,
                        "FILE_SRC" => "",
                        "DOCTOR" => BX_UTF != 1 ?
                            iconv("UTF-8", "windows-1251//IGNORE",
                                htmlspecialcharsbx($_REQUEST["message"])) : htmlspecialcharsbx($_REQUEST["doctor"]),
                        "COMMENT" => BX_UTF != 1 ?
                            iconv("UTF-8", "windows-1251//IGNORE",
                                htmlspecialcharsbx($_REQUEST["message"])) : htmlspecialcharsbx($_REQUEST["message"])
                    );
                    if (!empty($file_src)) {
                        $arMessage['FILE_SRC'] .= "Прикреплен файл: <a href='" . $file_src . "'>" . $file_src . "</a><br>";
                    }
                    CEvent::SendImmediate("SALE_STATUS_CHANGED_NS", htmlspecialcharsbx($_REQUEST["SITE_ID"]),
                        $arMessage, "Y", false);
                    
                    $FORM_ID = 18;
                    
                    $arValues = array(
                        "form_text_254" => $arMessage['NAME'],
                        "form_text_255" => $arMessage['PHONE'],
                        "form_textarea_258" => $arMessage['COMMENT'],
                        "form_text_256" => $arMessage['PRODUCT'],
                        "form_text_257" => $arMessage['DOCTOR'],
                        "form_checkbox_GPO" => [$arMessage['GPO']],
                        "form_checkbox_URGENT" => [$arMessage['URGENT']],
                        "form_text_" .
                        ($_SERVER['SERVER_NAME'] == 'www.medi-salon.ru' ?
                            "262" : "261") => $file_src,
                        //"form_text_179"     => $GLOBALS['medi']["region_cities"][SITE_ID]
                    );
                    
                    $obElm = CIBlockElement::GetList([], ["ID" => $arElement["ID"]], false, false,
                        ["IBLOCK_ID", "NAME", "DETAIL_PAGE_URL",
                            "CATALOG_PRICE_" . $GLOBALS['medi']['price_id'][SITE_ID]
                        ]);
                    if ($arElm = $obElm->GetNext()) {
                        $obElmProp = CIBlockElement::GetList([], ["ID" => $arElement["ID"], "IBLOCK_ID" => $arElm['IBLOCK_ID']], false, false, ["ID", "NAME", "PROPERTY_CML2_LINK", "PROPERTY_CML2_LINK.IBLOCK_ID", "PROPERTY_ATT_BRAND.NAME", "PROPERTY_CML2_ARTICLE"]);
                        if ($arElmProp = $obElmProp->GetNext()) {
                            $brand = '';
                            $article = $arElmProp['PROPERTY_CML2_ARTICLE_VALUE'];
                            // sku
                            if ($arElmProp['PROPERTY_CML2_LINK_VALUE']) {
                                $obElmBrand = CIBlockElement::GetList([], ["IBLOCK_ID" => $arElmProp['PROPERTY_CML2_LINK_IBLOCK_ID'], "ID" => $arElmProp['PROPERTY_CML2_LINK_VALUE']], false, false, ["ID", "NAME", "PROPERTY_ATT_BRAND.NAME"]);
                                
                                if ($arElmBrand = $obElmBrand->GetNext()) {
                                    $brand = $arElmBrand['PROPERTY_ATT_BRAND_NAME'];
                                    $goodId = $arElmProp['ID'];
                                    $goodName = $arElmBrand['NAME'];
                                }
                                
                            } // simple
                            elseif ($arElmProp['PROPERTY_ATT_BRAND_NAME']) {
                                
                                $brand = $arElmProp['PROPERTY_ATT_BRAND_NAME'];
                                
                                $goodId = $arElmProp['ID'];
                                $goodName = $arElmProp['NAME'];
                                
                            }
                            
                        }
                    }
                    
                    $secturl = explode("/", $arElm['DETAIL_PAGE_URL']);
                    $sectcount = count($secturl) - 1;
                    unset($secturl[$sectcount]);
                    unset($secturl[0]);
                    unset($secturl[1]);
                    
                    $arProduct = array(
                        'id' => $goodId,
                        'q' => 1,
                        'price' => $arElement["~PRICE"],
                        'article' => $article,
                        'name' => $goodName,
                        'category' => implode("/", $secturl),
                        'brand' => $brand
                    );
                    
                    // создадим новый результат
                    if ($RESULT_ID = CFormResult::Add($FORM_ID, $arValues)) {
                        global $USER;
                        $nUserID = "0";
                        $nUserID = $USER->GetID();
                        
                        $arVALUE = array();
                        $FIELD_SID = "AUTHOR";
                        CFormResult::SetField($RESULT_ID, $FIELD_SID, $USER->GetID());
                        
                        $result = array(
                            "heading" => "Ваш заказ успешно отправлен",
                            "message" => "В ближайшее время Вам перезвонит наш специалист для уточнения деталей заказа.",
                            "success" => true,
                            "result_id" => $RESULT_ID,
                            "product" => $arProduct
                        );
                    } else {
                        global $strError;
                        w2l($strError, 0, 'smp.log');
                        $result = array(
                            "heading" => "Ошибка",
                            "message" => "Ошибка, заказ не создан!",
                            "success" => false
                        );
                    }
                    
                    //new order
                    
                    //basket object
                    $basketAjax = DwBasket::getInstance();
                    $basket = $basketAjax->getBasket();
                    
                    //clearBasket
                    $basketAjax->clearBasket();
                    
                    //Добавление товара
                    $item = $basket->createItem("catalog", intval($arElement["ID"]));
                    $item->setFields([
                        "QUANTITY" => 1,
                        "CURRENCY" => $basketAjax->getCurrencyCode(),
                        "LID" => $_GET["SITE_ID"],
                        "PRODUCT_PROVIDER_CLASS" => "Bitrix\Catalog\Product\CatalogProvider",
                        "CATALOG_XML_ID" => $arElement["IBLOCK_EXTERNAL_ID"],
                        "PRODUCT_XML_ID" => $arElement["EXTERNAL_ID"],
                    ]);
                    
                    //Сохранение изменений
                    $basket->save();
                    
                    //set siteId
                    $basketAjax->setSiteId(htmlspecialcharsbx($_GET["SITE_ID"]));
                    
                    //get items
                    $arBasketItems = $basketAjax->getBasketItems();
                    
                    //compilation
                    $arOrder = $basketAjax->getOrderInfo();
                    
                    //order
                    $order = $basketAjax->getOrder();
                    
                    //get collection
                    $propertyCollection = $order->getPropertyCollection();
                    $order->setField('STATUS_ID', 'NS');
                    
                    $arProps = $propertyCollection->getArray();
                    //set properties
                    //set phone
                    if (!empty($phone)) {
                        $propertyCollection = $order->getPropertyCollection();
                        if ($phoneProp = $propertyCollection->getPhone()) {
                            $phoneProp->setValue(!defined("BX_UTF") ?
                                iconv("UTF-8", "windows-1251//IGNORE",
                                    htmlspecialcharsbx($phone)) : htmlspecialcharsbx($phone));
                        }
                    }
                    
                    //set name
                    if (!empty($_GET["name"])) {
                        if ($nameProp = $propertyCollection->getPayerName()) {
                            $nameProp->setValue(!defined("BX_UTF") ?
                                iconv("UTF-8", "windows-1251//IGNORE",
                                    htmlspecialcharsbx($_GET["name"])) : htmlspecialcharsbx($_GET["name"]));
                        }
                    }
                    
                    //order comment
                    if (!empty($_GET["message"])) {
                        if ($USER->IsAuthorized()) {
                            $rsUser = CUser::GetByID($USER->GetID());
                            $arUser = $rsUser->Fetch();
                            if (!empty($arUser)) {
                                $USER_NAME = $USER->GetFullName();
                                $USER_PHONE = $arUser["PERSONAL_MOBILE"];
                            }
                        }
                        
                        $basketAjax->setOrderComment(!defined("BX_UTF") ? iconv("UTF-8", "windows-1251//IGNORE", htmlspecialcharsbx($_GET["message"])) : htmlspecialcharsbx($_GET["message"]) . " \r\n\r\n Представитель " . $USER_NAME . " \r\n Телефон:" . $USER_PHONE);
                    }
                    
                    // врач
                    if (!empty($_GET["doctor"])) {
                        if ($somePropValue = $propertyCollection->getItemByOrderPropertyId(17)) {
                            $somePropValue->setValue(!defined("BX_UTF") ? iconv("UTF-8", "windows-1251//IGNORE", htmlspecialcharsbx($_GET["doctor"])) : htmlspecialcharsbx($_GET["doctor"]));
                        }
                        
                        if ($somePropValue = $propertyCollection->getItemByOrderPropertyId(15)) {
                            $somePropValue->setValue("YES");
                        }
                        
                    }
                    
                    // Источник заказа
                    if ($somePropValue = $propertyCollection->getItemByOrderPropertyId(11)) {
                        $somePropValue->setValue("ORDER_REF_TP");
                    }
                    
                    // ГПО заказа
                    if (!empty($arMessage['GPO'])) {
                        if ($somePropValue = $propertyCollection->getItemByOrderPropertyId(12)) {
                            $somePropValue->setValue("YES_GPO");
                        }
                    }
                    
                    if ($somePropValue = $propertyCollection->getItemByOrderPropertyId(18)) {
                        $somePropValue->setValue("D_NODISCOUNT");
                    }
                    
                    
                    // Создаём одну отгрузку и устанавливаем способ доставки - "Без доставки" (он служебный)
                    $shipmentCollection = $order->getShipmentCollection();
                    
                    foreach ($shipmentCollection as $shipment) {
                        $shipment->delete();
                    }

// Создаём оплату со способом #1
                    /*$paymentCollection = $order->getPaymentCollection();
                    $payment = $paymentCollection->createItem();
                    $paySystemService = PaySystem\Manager::getObjectById(1);
                    $payment->setFields(array(
                        'PAY_SYSTEM_ID' => $paySystemService->getField("PAY_SYSTEM_ID"),
                        'PAY_SYSTEM_NAME' => $paySystemService->getField("NAME"),
                    ));*/
                    
                    
                    //prepare
                    $order->doFinalAction(true);
                    
                    //save order
                    $orderStatus = $order->save();
                    
                    //check success
                    if (!$orderStatus->isSuccess()) {
                        
                        //get errors for debugging
                        $errors = $orderStatus->getErrors();
                        w2l($errors, 1, 'smp.log');
                        //push
                        $result = array(
                            "heading" => "Ошибка",
                            "message" => "Ошибка, заказ не создан!",
                            "success" => false
                        );
                        
                    }
                    
                    if (empty($result)) {
                        $result = array(
                            "heading" => "Ваш заказ успешно отправлен",
                            "message" => "В ближайшее время Вам перезвонит наш специалист для уточнения деталей заказа.",
                            "success" => true
                        );
                    }
                    
                } else {
                    
                    $result = array(
                        "heading" => "Ошибка",
                        "message" => "Ошибка, товар не найден!",
                        "success" => false
                    );
                    
                }
                
            }
            
        } else {
            $result = array(
                "heading" => "Ошибка",
                "message" => "Ошибка, заполните обязательные поля!",
                "success" => false
            );
        }
        
        echo \Bitrix\Main\Web\Json::encode($result);
        
    } elseif ($_REQUEST['act'] == 'savefile') {
        $error = '';
        if (!isset($_FILES['file'])) {
            $error = 'Файл не загружен.';
        } else {
            $file = $_FILES['file'];
        }
        
        $arrFile = array_merge(
            $_FILES["file"],
            array("MODULE_ID" => "form"));
        
        // $res = CFile::CheckFile($arrFile, 0, "false", "xls,xlsx,doc,docx,jpeg,jpg,png,pdf,txt");
        //if (strlen($res) > 0) $error .= $res . "<br>";
        
        if (empty($error))
            $fid = CFile::SaveFile($_FILES["file"], array("MODULE_ID" => "form"));
        
        if ($fid) {
            $filepath = CFile::GetFileArray($fid);
            
            $result = array(
                
                "success" => true,
                "file" => $filepath
            );
        } else {
            
            $result = array(
                "heading" => "Ошибка",
                "message" => "Ошибка! " . $error,
                "success" => false
            );
        }
        
        echo \Bitrix\Main\Web\Json::encode($result);
    }
}

function priceFormat($data, $str = "")
{
    $price = explode(".", $data);
    $strLen = strlen($price[0]);
    for ($i = $strLen; $i > 0; $i--) {
        $str .= (!($i % 3) ? " " : "") . $price[0][$strLen - $i];
    }
    return $str . ($price[1] > 0 ? "." . $price[1] : "");
}

function jsonEn($data, $multi = false)
{
    if (!$multi) {
        foreach ($data as $index => $arValue) {
            $arJsn[] = '"' . $index . '" : "' . addslashes($arValue) . '"';
        }
        return "{" . implode($arJsn, ",") . "}";
    }
}

function jsonMultiEn($data)
{
    if (is_array($data)) {
        if (count($data) > 0) {
            $arJsn = "[" . implode(getJnLevel($data, 0), ",") . "]";
        } else {
            $arJsn = implode(getJnLevel($data), ",");
        }
    }
    return str_replace(array("\t", "\r", "\n", "'"), "", trim($arJsn));
}

function getJnLevel($data, $level = 1, $arJsn = array())
{
    foreach ($data as $i => $arNext) {
        if (!is_array($arNext)) {
            $arJsn[] = '"' . $i . '":"' . addslashes(trim(str_replace("'", "", $arNext))) . '"';
        } else {
            if ($level === 0) {
                $arJsn[] = "{" . implode(getJnLevel($arNext), ",") . "}";
            } else {
                $arJsn[] = '"' . $i . '":{' . implode(getJnLevel($arNext), ",") . '}';
            }
        }
    }
    return $arJsn;
}

function getLastOffer($arLastFilter, $arProps, $productID, $opCurrency, $enableMorePictures = false, $arPrices = array())
{
    
    if (!empty($_GET["product_width"]) && !empty($_GET["product_height"])) {
        $arProductImage = array("width" => $_GET["product_width"], "height" => $_GET["product_height"]);
    } else {
        $arProductImage = array("width" => 220, "height" => 200);
    }
    
    $rsLastOffer = CIBlockElement::GetList(
        array(),
        $arLastFilter, false, false,
        array(
            "ID",
            "NAME",
            "IBLOCK_ID",
            "DETAIL_PICTURE",
            "DETAIL_PAGE_URL",
            "CATALOG_QUANTITY",
            "CATALOG_AVAILABLE",
            "CATALOG_SUBSCRIBE",
            "PREVIEW_TEXT"
        )
    );
    if (!$rsLastOffer->SelectedRowsCount()) {
        $st = array_pop($arLastFilter);
        $mt = array_pop($arProps);
        return getLastOffer($arLastFilter, $arProps, $productID, $opCurrency, $enableMorePictures, $arPrices);
    } else {
        if ($obReturnOffer = $rsLastOffer->GetNextElement()) {
            
            $productFilelds = $obReturnOffer->GetFields();
            if ($enableMorePictures) {
                $productProperties = $obReturnOffer->GetProperties();
            }
            
            $productFilelds["IMAGES"] = array();
            $rsProductSelect = array("ID", "IBLOCK_ID", "DETAIL_PICTURE", "PREVIEW_TEXT");
            
            if (!empty($productFilelds["DETAIL_PICTURE"])) {
                
                $arImageResize = CFile::ResizeImageGet($productFilelds["DETAIL_PICTURE"], $arProductImage, BX_RESIZE_IMAGE_PROPORTIONAL, false);
                $productFilelds["PICTURE"] = $arImageResize["src"];
                
                $productFilelds["IMAGES"][] = array(
                    "SMALL_PICTURE" => CFile::ResizeImageGet($productFilelds["DETAIL_PICTURE"], array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false),
                    "LARGE_PICTURE" => CFile::ResizeImageGet($productFilelds["DETAIL_PICTURE"], array("width" => 300, "height" => 300), BX_RESIZE_IMAGE_PROPORTIONAL, false),
                    "SUPER_LARGE_PICTURE" => CFile::ResizeImageGet($productFilelds["DETAIL_PICTURE"], array("width" => 900, "height" => 900), BX_RESIZE_IMAGE_PROPORTIONAL, false)
                );
            }
            
            if (!empty($productProperties["MORE_PHOTO"]["VALUE"])) {
                foreach ($productProperties["MORE_PHOTO"]["VALUE"] as $irp => $nextPictureID) {
                    $productFilelds["IMAGES"][] = array(
                        "SMALL_PICTURE" => CFile::ResizeImageGet($nextPictureID, array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false),
                        "LARGE_PICTURE" => CFile::ResizeImageGet($nextPictureID, array("width" => 300, "height" => 300), BX_RESIZE_IMAGE_PROPORTIONAL, false),
                        "SUPER_LARGE_PICTURE" => CFile::ResizeImageGet($nextPictureID, array("width" => 900, "height" => 900), BX_RESIZE_IMAGE_PROPORTIONAL, false)
                    );
                }
            }
            
            if (empty($productFilelds["DETAIL_PICTURE"]) || empty($productProperties["MORE_PHOTO"]["VALUE"])) {
                if ($rsProduct = CIBlockElement::GetList(array(), array("ID" => $productID), false, false, $rsProductSelect)->GetNextElement()) {
                    
                    $rsProductFields = $rsProduct->GetFields();
                    if ($enableMorePictures) {
                        $rsProductProperties = $rsProduct->GetProperties(array("sort" => "asc", "name" => "asc"), array("EMPTY" => "N"));
                    }
                    
                    if (!empty($rsProductFields["DETAIL_PICTURE"]) || !empty($rsProductProperties["MORE_PHOTO"]["VALUE"])) {
                        if (!empty($rsProductFields["DETAIL_PICTURE"]) && empty($productFilelds["DETAIL_PICTURE"])) {
                            
                            $arImageResize = CFile::ResizeImageGet($rsProductFields["DETAIL_PICTURE"], $arProductImage, BX_RESIZE_IMAGE_PROPORTIONAL, false);
                            $productFilelds["PICTURE"] = $arImageResize["src"];
                            
                            array_unshift($productFilelds["IMAGES"], array(
                                "SMALL_PICTURE" => CFile::ResizeImageGet($rsProductFields["DETAIL_PICTURE"], array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false),
                                "LARGE_PICTURE" => CFile::ResizeImageGet($rsProductFields["DETAIL_PICTURE"], array("width" => 300, "height" => 300), BX_RESIZE_IMAGE_PROPORTIONAL, false),
                                "SUPER_LARGE_PICTURE" => CFile::ResizeImageGet($rsProductFields["DETAIL_PICTURE"], array("width" => 900, "height" => 900), BX_RESIZE_IMAGE_PROPORTIONAL, false)
                            ));
                            
                        }
                        if (!empty($rsProductProperties["MORE_PHOTO"]["VALUE"]) && empty($productProperties["MORE_PHOTO"]["VALUE"])) {
                            foreach ($rsProductProperties["MORE_PHOTO"]["VALUE"] as $irp => $nextPictureID) {
                                if (!empty($nextPictureID)) {
                                    $productFilelds["IMAGES"][] = array(
                                        "SMALL_PICTURE" => CFile::ResizeImageGet($nextPictureID, array("width" => 50, "height" => 50), BX_RESIZE_IMAGE_PROPORTIONAL, false),
                                        "LARGE_PICTURE" => CFile::ResizeImageGet($nextPictureID, array("width" => 300, "height" => 300), BX_RESIZE_IMAGE_PROPORTIONAL, false),
                                        "SUPER_LARGE_PICTURE" => CFile::ResizeImageGet($nextPictureID, array("width" => 900, "height" => 900), BX_RESIZE_IMAGE_PROPORTIONAL, false)
                                    );
                                }
                            }
                        }
                    } else {
                        if (empty($productFilelds["IMAGES"])) {
                            $productFilelds["IMAGES"][0]["SMALL_PICTURE"] = array("SRC" => SITE_TEMPLATE_PATH . "/images/empty.png");
                            $productFilelds["IMAGES"][0]["LARGE_PICTURE"] = array("SRC" => SITE_TEMPLATE_PATH . "/images/empty.png");
                            $productFilelds["IMAGES"][0]["SUPER_LARGE_PICTURE"] = array("SRC" => SITE_TEMPLATE_PATH . "/images/empty.png");
                        }
                    }
                }
            }
            
            if (empty($productFilelds["PICTURE"])) {
                $productFilelds["PICTURE"] = SITE_TEMPLATE_PATH . "/images/empty.png";
            }
            
            //get price info
            $productFilelds["EXTRA_SETTINGS"] = array();
            $productFilelds["EXTRA_SETTINGS"]["PRODUCT_PRICE_ALLOW"] = array();
            $productFilelds["EXTRA_SETTINGS"]["PRODUCT_PRICE_ALLOW_FILTER"] = array();
            
            if (!empty($arPrices["PARAMS_PRICE_CODE"])) {
                
                //get available prices code & id
                $arPricesInfo = DwPrices::getPriceInfo($arPrices["PARAMS_PRICE_CODE"], $productFilelds["IBLOCK_ID"]);
                if (!empty($arPricesInfo)) {
                    $productFilelds["EXTRA_SETTINGS"]["PRODUCT_PRICE_ALLOW"] = $arPricesInfo["ALLOW"];
                    $productFilelds["EXTRA_SETTINGS"]["PRODUCT_PRICE_ALLOW_FILTER"] = $arPriceType["ALLOW_FILTER"];
                }
                
            }
            
            $productFilelds["PRICE"] = DwPrices::getPricesByProductId($productFilelds["ID"], $productFilelds["EXTRA_SETTINGS"]["PRODUCT_PRICE_ALLOW"], $productFilelds["EXTRA_SETTINGS"]["PRODUCT_PRICE_ALLOW_FILTER"], $arPrices["PARAMS_PRICE_CODE"], $productFilelds["IBLOCK_ID"], $opCurrency);
            $productFilelds["PRICE"]["DISCOUNT_PRICE"] = CCurrencyLang::CurrencyFormat($productFilelds["PRICE"]["DISCOUNT_PRICE"], $opCurrency, true);
            $productFilelds["PRICE"]["RESULT_PRICE"]["BASE_PRICE"] = CCurrencyLang::CurrencyFormat($productFilelds["PRICE"]["RESULT_PRICE"]["BASE_PRICE"], $opCurrency, true);
            $productFilelds["PRICE"]["DISCOUNT_PRINT"] = CCurrencyLang::CurrencyFormat($productFilelds["PRICE"]["RESULT_PRICE"]["DISCOUNT"], $opCurrency, true);
            $productFilelds["CAN_BUY"] = $productFilelds["CATALOG_AVAILABLE"];
            
            if (!empty($productFilelds["PRICE"]["EXTENDED_PRICES"])) {
                $productFilelds["PRICE"]["EXTENDED_PRICES_JSON_DATA"] = \Bitrix\Main\Web\Json::encode($productFilelds["PRICE"]["EXTENDED_PRICES"]);
            }
            
            if (!empty($productFilelds["PRICE"]["DISCOUNT"])) {
                unset($productFilelds["PRICE"]["DISCOUNT"]);
            }
            
            if (!empty($productFilelds["PRICE"]["DISCOUNT_LIST"])) {
                unset($productFilelds["PRICE"]["DISCOUNT_LIST"]);
            }
            
            //коэффициент еденица измерения
            $productFilelds["BASKET_STEP"] = 1;
            $rsMeasureRatio = CCatalogMeasureRatio::getList(
                array(),
                array("PRODUCT_ID" => intval($productFilelds["ID"])),
                false,
                false,
                array()
            );
            
            if ($arProductMeasureRatio = $rsMeasureRatio->Fetch()) {
                if (!empty($arProductMeasureRatio["RATIO"])) {
                    $productFilelds["BASKET_STEP"] = $arProductMeasureRatio["RATIO"];
                }
            }
            
            if (empty($productFilelds["PREVIEW_TEXT"])) {
                if (!empty($rsProductFields)) {
                    $productFilelds["PREVIEW_TEXT"] = $rsProductFields["PREVIEW_TEXT"];
                } else {
                    if ($rsProduct = CIBlockElement::GetList(array(), array("ID" => $productID), false, false, $rsProductSelect)->GetNextElement()) {
                        $rsProductFields = $rsProduct->GetFields();
                        $productFilelds["PREVIEW_TEXT"] = $rsProductFields["PREVIEW_TEXT"];
                    }
                }
            }
            
            return array(
                "PRODUCT" => array_merge(
                    $productFilelds, array(
                        "PROPERTIES" => $obReturnOffer->GetProperties()
                    )
                ),
                "PROPERTIES" => $arProps
            );
        }
    }
}

?>
