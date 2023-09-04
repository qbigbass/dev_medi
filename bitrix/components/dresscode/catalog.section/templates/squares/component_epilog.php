<?
global $FAVORITE_ITEMS;

if (!empty($FAVORITE_ITEMS)) {
    foreach ($FAVORITE_ITEMS as $favoriteProductItem) {?>
        <script>
            if ($('.b-card-favorite[data-product-id="<?=$favoriteProductItem?>"]')) {
                $('.b-card-favorite[data-product-id="<?=$favoriteProductItem?>"]').addClass('active');
            }
        </script>
    <?}
}
