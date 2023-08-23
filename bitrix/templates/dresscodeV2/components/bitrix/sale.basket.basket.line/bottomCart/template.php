<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$frame = $this->createFrame()->begin();

if (!empty($arResult['CATEGORIES']['READY']))
{
    $num_products = 0;
    foreach ($arResult['CATEGORIES']['READY'] as $cat)
    {
        $num_products += $cat['QUANTITY'];
    }
    $arResult["NUM_PRODUCTS"] = $num_products;
}
?>

<?//$compareCount = !empty($_SESSION["COMPARE_LIST"]["ITEMS"]) ? count($_SESSION["COMPARE_LIST"]["ITEMS"]) : 0?>
<?//$wishlistCount = !empty($_SESSION["WISHLIST_LIST"]["ITEMS"]) ? count($_SESSION["WISHLIST_LIST"]["ITEMS"]) : 0?>
<?/*
<div class="item">
	<a <?if($compareCount > 0):?>href="<?=SITE_DIR?>compare/"<?endif;?> class="compare<?if($compareCount > 0):?> active<?endif;?>"><span class="icon"></span><span class="textLabel"><?=GetMessage("CART_COMPARE_LABEL")?></span><span class="mark"><?=$compareCount?></span></a>
</div>
<div class="item">
	<a <?if($wishlistCount > 0):?>href="<?=SITE_DIR?>wishlist/"<?endif;?> class="wishlist<?if($wishlistCount > 0):?> active<?endif;?>"><span class="icon"></span><span class="textLabel"><?=GetMessage("CART_WISHLIST_LABEL")?></span><span class="mark"><?=$wishlistCount?></span></a>
</div>*/?>
<a href="<?=SITE_DIR?>personal/cart/" class="large-link footer_icon_cart  <?=(strpos($APPLICATION->GetCurDir(), 'cart/') ? 'active' : '')?> <?if(!empty($arResult["NUM_PRODUCTS"])):?> full<?endif;?>"><br><span >Корзина</span><?if(!empty($arResult["NUM_PRODUCTS"])):?><span class="mark"><?=$arResult["NUM_PRODUCTS"]?></span><?endif;?></a>

<?$frame->end();?>
