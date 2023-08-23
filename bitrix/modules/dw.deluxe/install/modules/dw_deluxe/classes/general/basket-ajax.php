<?
//namespace
namespace DigitalWeb;

//bitrix uses
use Bitrix\Main,
    Bitrix\Main\Localization\Loc as Loc,
    Bitrix\Main\Loader,
    Bitrix\Main\Config\Option,
    Bitrix\Sale\Delivery,
    Bitrix\Sale\PaySystem,
    Bitrix\Sale\PersonType,
    Bitrix\Sale,
    Bitrix\Sale\Order,
    Bitrix\Sale\DiscountCouponsManager,
    Bitrix\Main\Context;

//get langs
IncludeModuleLangFile(__FILE__);

//
class BasketAjax extends Basket{

    //static vars

    //private
    private static $instance = false;

    //constuct
    function __construct(){}

    //singleton
    public static function getInstance(){

        if (!self::$instance){
            self::$instance = new BasketAjax();
        }

        return self::$instance;
    }

    //functions
    public static function orderMake($orderData = array()){

        //check transmitted data
        if(!empty($orderData)){

            //default
            $orderData["properties"] = !empty($orderData["properties"]) ? $orderData["properties"] : array();

            //basket
            $arBasketItems = parent::getBasketItems();

            //get params
            $arParams = parent::getParams();

            //check basket items
            if(!empty($arBasketItems)){

                //compilation
                $arOrder = parent::getOrderInfo();

                //order
                $order = parent::getOrder();

                //check instance
                if(!$order instanceof \Bitrix\Sale\order){

                    //set error
                    parent::setError("order instance error");
                    return false;

                }

                //innerPayment
                if(!empty($orderData["innerPayment"]) && $orderData["innerPayment"] == "Y"){

                    if(!parent::setInnerPayment()){
                        return false;
                    }

                    //check full payment by inner
                    if($order->isPaid()){
                        parent::clearPayments();
                    }

                }

                //append files to properties
                if(!empty($orderData["files"])){

                    //processing
                    $arFiles = parent::processingFiles($orderData["files"]);

                    //concat array (save indexes)
                    if(!empty($arFiles)){
                        $orderData["properties"] = ($orderData["properties"] + $arFiles);
                    }

                }

                //set order properties
                if(!empty($orderData["properties"])){
                    if(!parent::setProperties($orderData["properties"])){
                        return false;
                    }
                }

                //order comment
                if(!empty($orderData["comment"])){
                    if(!parent::setOrderComment($orderData["comment"])){
                        return false;
                    }
                }

                //auto user register
                if(!empty($arParams["REGISTER_USER"]) && $arParams["REGISTER_USER"] == "Y"){
                    if(!parent::autoRegisterUser()){
                        return false;
                    }
                }

                //update user info
                if(!parent::updateUserInfo($arOrder, $orderData["properties"])){
                    return false;
                }

                //user profile
                if(!parent::createUserProfile($arOrder, $orderData["properties"])){
                    return false;
                }

                //prepare
                $order->doFinalAction(true);

                //save order
                $orderStatus = $order->save();

                //check success
                if($orderStatus->isSuccess()){
                    return ["orderId" => $order->getId()];
                }

                //errors
                else{

                    //get errors
                    $errors = $orderStatus->getErrors();

                    //write
                    if(!empty($errors)){
                        parent::setError($errors);
                    }

                }

            }

            //set error
            else{
                //C2_BASKET_EMPTY_ERROR
                parent::setError(\Bitrix\Main\Localization\Loc::GetMessage("C2_BASKET_EMPTY_ERROR"));
            }

        }

        //set error
        else{
            //C2_BASKET_DATA_EMPTY_ERROR
            parent::setError(\Bitrix\Main\Localization\Loc::GetMessage("C2_BASKET_DATA_EMPTY_ERROR"));
        }

        return false;

    }

   public static function compilation(){

        //vars
        $arReturn = array();

        //basket
        $arBasketItems = parent::getBasketItems();

        //append product fields to basket items
        $arProducts = parent::addProductsInfo($arBasketItems);

        //check basket products
        if(!empty($arProducts)){

            //get vars
            $arOrder = parent::getOrderInfo();
            $arProducts = parent::addProductPrices($arProducts);
            $discountListFull = parent::getDiscountListFull();
            $appliedDiscounts = parent::getAppliedDiscounts();
            $arStores = parent::getStores($arProducts);

            //check minimum order amount
            $isMinOrderAmount = parent::checkMinOrderAmount();

            //push to result array
            $arReturn = array(
                "applied_discount_list" => $appliedDiscounts,
                "full_discount_list" => $discountListFull,
                "min_order_amount" => $isMinOrderAmount,
                "stores" => $arStores,
                "items" => $arProducts,
                "order" => $arOrder
            );

        }

        return $arReturn;

   }

}