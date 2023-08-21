<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Bitrix\Currency\CurrencyManager,
    Bitrix\Iblock,
    Bitrix\Sale\Order,
    Bitrix\Sale\Fuser,
    Bitrix\Sale\Basket;

use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\Parser;

global $USER;

Bitrix\Main\Loader::includeModule("sale");
Bitrix\Main\Loader::includeModule("catalog");
Bitrix\Main\Loader::includeModule("iblock");
Bitrix\Main\Loader::includeModule("form");

$siteId = Context::getCurrent()->getSite();

$action = '';
if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], ['precheck', 'save'])) {
    $action = strval($_REQUEST['action']);
} else
    die();

if ($action == 'precheck') {
    
    if (!empty($_REQUEST['precheck'])) {
        $precheck = $_REQUEST['precheck'];
        
        
        $parsedPhone = Parser::getInstance()->parse($precheck['phone']);
        $phone = preg_replace("/(\D)*/", "", $parsedPhone->format(Format::E164));
        if (!$phone) {
            $result = ['status' => 'error', 'message' => "check phone"];
        } else {
            $basket = Basket::loadItemsForFUser(Fuser::getId(), $siteId);
            
            $arBasket = [];
            $arProducts = [];
            foreach ($basket as $basketItem) {
                $arItem = [];
                $arProducts[] = $arItem['PRODUCT_ID'] = $basketItem->getProductId();
                
                $arItem['BASKET_ID'] = $basketItem->getId();
                
                $element = [];
                $offer = [];
                
                //$basketPropertyCollection = $basketItem->getPropertyCollection();
                //$arItem['PROPS'] = $basketPropertyCollection->getPropertyValues();
                
                $offer = Bitrix\Iblock\Elements\ElementOffersTable::getByPrimary($arItem['PRODUCT_ID'], [
                    'select' => ['ID', 'CML2_ARTICLE', 'LMX_GOODID', 'CML2_LINK', "XML_ID"],
                ])->fetch();
                if (empty($offer)) {
                    $element = Bitrix\Iblock\Elements\ElementCatalogTable::getByPrimary($arItem['PRODUCT_ID'], [
                        'select' => ['ID', 'CML2_ARTICLE', 'LMX_GOODID', 'XML_ID', 'NAME'],
                    ])->fetch();
                } else {
                    $parent_id = $offer['IBLOCK_ELEMENTS_ELEMENT_OFFERS_CML2_LINK_VALUE'];
                    $element = Bitrix\Iblock\Elements\ElementCatalogTable::getByPrimary($parent_id, [
                        'select' => ['ID', 'CML2_ARTICLE', 'LMX_GOODID', 'XML_ID', 'NAME'],
                    ])->fetch();
                }
                
                
                if (empty($element)) {
                    $basketItem->delete();
                    continue;
                }
                
                $arItem['NomenclatureData'] = [
                    'UID' => $element['XML_ID'],
                    'Name' => $element['NAME'],
                    'ItemNumber' => $element['IBLOCK_ELEMENTS_ELEMENT_CATALOG_CML2_ARTICLE_VALUE'],
                ];
                if (empty($offer)) {
                    $arItem['CharacteristicData'] = ['PresentOnSite' => false];
                } else {
                    $arItem['CharacteristicData'] = [
                        'PresentOnSite' => true,
                        'UID' => explode("#", $offer['XML_ID'])[1],
                        'Name' => $offer['IBLOCK_ELEMENTS_ELEMENT_OFFERS_CML2_ARTICLE_VALUE']
                    ];
                }
                $arItem['QUANTITY'] = $basketItem->getQuantity();
                $arItem['PRICE'] = $basketItem->getPrice();
                $arItem['SUM'] = $basketItem->getFinalPrice();
                
                //w2l($arItem, 1, 'element.log');
                $arBasket[] = $arItem;
            }
            if (empty($arBasket)) {
                $result = ['status' => 'error', 'message' => "empty basket"];
            } else {
                
                $referer = Context::getCurrent()->getServer()->get('HTTP_REFERER');
                $is_dev = false;
                if (strpos($referer, 'dev3')) {
                    $is_dev = true;
                }
                // сохранем запись
                $FORM_ID = 19;
                
                $arValues = array(
                    "form_text_" . ($is_dev ? 262 : 263) => $phone,   // phone   263
                    "form_text_" . ($is_dev ? 263 : 264) => $precheck['reciept'],     // reciept_id 264
                    "form_textarea_" . ($is_dev ? 264 : 265) => $precheck['comment'],      //  265
                    "form_text_" . ($is_dev ? 265 : 266) => $precheck['coupon'],              // купон 266
                    "form_text_" . ($is_dev ? 266 : 267) => $precheck['mtz'],   // mtz   267
                    "form_textarea_" . ($is_dev ? 267 : 268) => implode("\r\n", $basket->getListOfFormatText())   // basket   268
                );
                
                $salon = "" . $USER->GetLogin() . " " . $USER->GetFullName();
                
                
                if ($RESULT_ID = CFormResult::Add($FORM_ID, $arValues)) {
                    CFormResult::SetField($RESULT_ID, 'SALON', $salon);
                    CFormResult::SetField($RESULT_ID, 'BASKET_ARRAY', serialize($arBasket));
                    
                    CFormResult::SetField($RESULT_ID, 'SALON_ID', $USER->GetID());
                    $arValues['result'] = $RESULT_ID;
                    
                    // данные по салону
                    $rsSalon = CUser::GetByID($USER->GetID());
                    $arSalon = $rsSalon->Fetch();
                    if (!empty($arSalon['UF_SKLAD'])) {
                        $rsStore = CCatalogStore::GetList(
                            [],
                            array('ACTIVE' => 'Y', 'XML_ID' => $arSalon['UF_SKLAD']),
                            false,
                            false,
                            array("ID", "TITLE")
                        );
                        if ($arStore = $rsStore->Fetch()) {
                            $arRecieptStore = ['UID' => $arSalon['UF_SKLAD'], 'NAME' => $arStore['TITLE']];
                            
                            // проверка остатков
                            $rsStoreAmount = CCatalogStore::GetList(
                                [],
                                array('ACTIVE' => 'Y', 'XML_ID' => $arSalon['UF_SKLAD'], 'PRODUCT_ID' => $arProducts),
                                false,
                                false,
                                array("ID", "TITLE", "PRODUCT_AMOUNT")
                            );
                            $arStoreProducts = [];
                            if ($arStoreAmount = $rsStoreAmount->Fetch()) {
                                $arStoreProducts[] = $arStoreAmount;
                            }
                        } else {
                            // mail
                            $arFields = [
                                'SUBJ' => 'Не найден склад для предчека',
                                'MSG' => 'SALON ID <a href="https://www.medi-salon.ru/bitrix/admin/user_edit.php?lang=ru&ID=' .
                                    $arSalon['ID'] . '"/>' . $arSalon['ID'] . '</a> ' . $arSalon['LOGIN'] . '<br><br>
                                    <a href="https://www.medi-salon.ru/bitrix/admin/form_result_edit.php?lang=ru&WEB_FORM_ID=' . $FORM_ID . '&RESULT_ID=' . $RESULT_ID . '&WEB_FORM_NAME=PRERECEIPT">Заявка</a>',
                                //'PHONE' => $site == 's2' ? '79019971161' : '79038246596'
                                'EMAIL' => 'denis@makoviychuk.ru'
                            
                            ];
                            \CEvent::SendImmediate('MSG_LOG', 's1', $arFields, 'N');
                        }
                    } else {
                        //mail
                        $arFields = [
                            'SUBJ' => 'Нет привязки склада для предчека',
                            'MSG' => 'SALON ID <a href="https://www.medi-salon.ru/bitrix/admin/user_edit.php?lang=ru&ID=' . $arSalon['ID'] . '"/>' . $arSalon['ID'] . '</a> ' . $arSalon['LOGIN'] . '<br><br>
                                    <a href="https://www.medi-salon.ru/bitrix/admin/form_result_edit.php?lang=ru&WEB_FORM_ID=' . $FORM_ID . '&RESULT_ID=' . $RESULT_ID . '&WEB_FORM_NAME=PRERECEIPT">Заявка</a>',
                            //'PHONE' => $site == 's2' ? '79019971161' : '79038246596'
                            'EMAIL' => 'denis@makoviychuk.ru'
                        
                        ];
                        \CEvent::SendImmediate('MSG_LOG', 's1', $arFields, 'N');
                    }
                    
                    // отправляем в 1С
                    
                    $preRecieptData = [
                        'ShoppingCartID' => $RESULT_ID,
                        'ClientName' => $_SESSION['lmx']['lastName'] . ' ' .
                            $_SESSION['lmx']['firstName'] . ' ' .
                            $_SESSION['lmx']['patronymicName'],
                        'PhoneNumber' => $phone,
                        'Recipe' => !(empty($precheck['reciept']) ? true : false),
                        'RecipeID' => $precheck['reciept'],
                        'RecipeComment' => $precheck['comment'],
                        'GiftCoupon' => $precheck['coupon'],
                        'SalesFloorManagerCode' => intval($precheck['mtz']),
                        'Salon' => $arRecieptStore
                    ];
                    $ProductLines = [];
                    $line = 1;
                    foreach ($arBasket as $arBasketItem) {
                        $ProductLines[] = [
                            'LineNumber' => $line,
                            'SalesFloorManagerCode' => intval(($precheck['itemcons'][$arBasketItem['BASKET_ID']] ?
                                $precheck['itemcons'][$arBasketItem['BASKET_ID']] : $precheck['mtz'])),
                            'Product' => [
                                'Nomenclature' => $arBasketItem['NomenclatureData'],
                                'Characteristic' => $arBasketItem['CharacteristicData']
                            ],
                            'Quantity' => $arBasketItem['QUANTITY'],
                            'Price' => $arBasketItem['PRICE'],
                            'Sum' => $arBasketItem['SUM']
                        ];
                        $line++;
                    }
                    $preRecieptData['ProductLines'] = $ProductLines;
                    
                    
                    $res = sendPreRecieptData($preRecieptData);
                    
                    if ($res) {
                        
                        $result = ['status' => 'success', 'client' => $precheck, 'result' => $res, $arStoreProducts];
                        w2l(['success', $res], 1, 'prereciept.log');
                        w2l($preRecieptData, 1, 'prereciept.log');
                        
                    } else {
                        $result = ['status' => 'error', 'message' => "error send to 1C " . $res['message'], $arBasket];
                        w2l(['error', $res], 1, 'prereciept.log');
                        header("Content-type: application/json; charset=utf-8");
                        echo json_encode($result);
                        die;
                    }
                    // очистка корзины
                    CSaleBasket::DeleteAll(Fuser::getId());
                    unset($_SESSION['precheck_data']);
                    
                } else {
                    global $strError;
                    $result = ['status' => 'error', 'message' => "add form result fail " . $strError];
                    header("Content-type: application/json; charset=utf-8");
                    echo json_encode($result);
                    die;
                }
                
            }
        }
        
    } else {
        
        $result = ['status' => 'error', 'message' => "empty query"];
    }
    
    
    header("Content-type: application/json; charset=utf-8");
    echo json_encode($result);
} elseif ($action == 'save') {
    if (!empty($_REQUEST['precheck'])) {
        $precheck = $_REQUEST['precheck'];
        
        $_SESSION['precheck_data'] = $_REQUEST['precheck'];
        $result = ['status' => 'success', 'message' => "saved"];
    } else {
        $result = ['status' => 'error', 'message' => "empty query"];
    }
    
    header("Content-type: application/json; charset=utf-8");
    echo json_encode($result);
}