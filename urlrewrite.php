<?php
$arUrlRewrite=array (
  55 => 
  array (
    'CONDITION' => '#^/brands/([a-zA-Z0-9-_\\-]+)/about/(\\?.*)*$#',
    'RULE' => 'ELEMENT_CODE=$1',
    'ID' => '',
    'PATH' => '/brands/about.php',
    'SORT' => 90,
  ),
  80 => 
  array (
    'CONDITION' => '#^={$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["smart_filter"]}\\??(.*)#',
    'RULE' => '&$1',
    'ID' => 'bitrix:catalog.smart.filter',
    'PATH' => '/bitrix/templates/dresscodeV2/components/dresscode/catalog/.default/section.php',
    'SORT' => 100,
  ),
  77 => 
  array (
    'CONDITION' => '#^/bitrix/services/yandex.market/trading/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/yandex.market/trading/index.php',
    'SORT' => 100,
  ),
  78 => 
  array (
    'CONDITION' => '#^/video/([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#',
    'RULE' => 'alias=$1&videoconf',
    'ID' => 'bitrix:im.router',
    'PATH' => '/desktop_app/router.php',
    'SORT' => 100,
  ),
  0 => 
  array (
    'CONDITION' => '#^/bitrix/services/ymarket/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/ymarket/index.php',
    'SORT' => 100,
  ),
  74 => 
  array (
    'CONDITION' => '#^/acrit.exportpro/(.*)#',
    'RULE' => 'path=$1',
    'ID' => NULL,
    'PATH' => '/acrit.exportpro/index.php',
    'SORT' => 100,
  ),
  73 => 
  array (
    'CONDITION' => '#^/personal/order/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.order',
    'PATH' => '/personal/order/index.php',
    'SORT' => 100,
  ),
  82 => 
  array (
    'CONDITION' => '#^/salons/orders/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.order',
    'PATH' => '/salons/orders/index.php',
    'SORT' => 100,
  ),
  45 => 
  array (
    'CONDITION' => '#^/tmn/services/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/tmn/services/index.php',
    'SORT' => 100,
  ),
  47 => 
  array (
    'CONDITION' => '#^/ekb/services/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/ekb/services/index.php',
    'SORT' => 100,
  ),
  43 => 
  array (
    'CONDITION' => '#^/kzn/services/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/kzn/services/index.php',
    'SORT' => 100,
  ),
  44 => 
  array (
    'CONDITION' => '#^/kgd/services/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/kgd/services/index.php',
    'SORT' => 100,
  ),
  59 => 
  array (
    'CONDITION' => '#^/encyclopedia/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/encyclopedia/index.php',
    'SORT' => 100,
  ),
  48 => 
  array (
    'CONDITION' => '#^/rnd/services/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/rnd/services/index.php',
    'SORT' => 100,
  ),
  42 => 
  array (
    'CONDITION' => '#^/spb/services/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/spb/services/index.php',
    'SORT' => 100,
  ),
  46 => 
  array (
    'CONDITION' => '#^/nn/services/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/nn/services/index.php',
    'SORT' => 100,
  ),
  70 => 
  array (
    'CONDITION' => '#^/tmn/salons/#',
    'RULE' => '',
    'ID' => 'dresscode:catalog.store',
    'PATH' => '/tmn/salons/index.php',
    'SORT' => 100,
  ),
  76 => 
  array (
    'CONDITION' => '#^/rnd/salons/#',
    'RULE' => '',
    'ID' => 'dresscode:catalog.store',
    'PATH' => '/rnd/salons/index.php',
    'SORT' => 100,
  ),
  69 => 
  array (
    'CONDITION' => '#^/kgd/salons/#',
    'RULE' => '',
    'ID' => 'dresscode:catalog.store',
    'PATH' => '/kgd/salons/index.php',
    'SORT' => 100,
  ),
  34 => 
  array (
    'CONDITION' => '#^/collection/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/collection/index.php',
    'SORT' => 100,
  ),
  68 => 
  array (
    'CONDITION' => '#^/spb/salons/#',
    'RULE' => '',
    'ID' => 'dresscode:catalog.store',
    'PATH' => '/spb/salons/index.php',
    'SORT' => 100,
  ),
  66 => 
  array (
    'CONDITION' => '#^/ekb/salons/#',
    'RULE' => '',
    'ID' => 'dresscode:catalog.store',
    'PATH' => '/ekb/salons/index.php',
    'SORT' => 100,
  ),
  27 => 
  array (
    'CONDITION' => '#^/kzn/salons/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog.store',
    'PATH' => '/kzn/salons/index.php',
    'SORT' => 100,
  ),
  64 => 
  array (
    'CONDITION' => '#^/nn/salons/#',
    'RULE' => '',
    'ID' => 'dresscode:catalog.store',
    'PATH' => '/nn/salons/index.php',
    'SORT' => 100,
  ),
  2 => 
  array (
    'CONDITION' => '#^/smp/order/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.order',
    'PATH' => '/smp/order/index.php',
    'SORT' => 100,
  ),
  81 => 
  array (
    'CONDITION' => '#^/lk/orders/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.order',
    'PATH' => '/lk/orders/index.php',
    'SORT' => 100,
  ),
  58 => 
  array (
    'CONDITION' => '#^/services/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/services/index.php',
    'SORT' => 100,
  ),
  84 => 
  array (
    'CONDITION' => '#^/catalog/#',
    'RULE' => '',
    'ID' => 'medi:catalog',
    'PATH' => '/catalog/index.php',
    'SORT' => 100,
  ),
  61 => 
  array (
    'CONDITION' => '#^/salons/#',
    'RULE' => '',
    'ID' => 'dresscode:catalog.store',
    'PATH' => '/salons/index.php',
    'SORT' => 100,
  ),
  56 => 
  array (
    'CONDITION' => '#^/brands/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/brands/index.php',
    'SORT' => 100,
  ),
  7 => 
  array (
    'CONDITION' => '#^/survey/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/survey/index.php',
    'SORT' => 100,
  ),
  6 => 
  array (
    'CONDITION' => '#^/stores/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog.store',
    'PATH' => '/stores/index.php',
    'SORT' => 100,
  ),
  8 => 
  array (
    'CONDITION' => '#^/store/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog.store',
    'PATH' => '/store/index.php',
    'SORT' => 100,
  ),
  39 => 
  array (
    'CONDITION' => '#^\\??(.*)#',
    'RULE' => '&$1',
    'ID' => 'dresscode:cast.smart.filter',
    'PATH' => '/bitrix/templates/dresscodeV2/components/bitrix/news/brands/detail.php',
    'SORT' => 100,
  ),
  71 => 
  array (
    'CONDITION' => '#^/stock/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/stock/index.php',
    'SORT' => 100,
  ),
  12 => 
  array (
    'CONDITION' => '#^/rest/#',
    'RULE' => '',
    'ID' => NULL,
    'PATH' => '/bitrix/services/rest/index.php',
    'SORT' => 100,
  ),
  60 => 
  array (
    'CONDITION' => '#^/news/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/news/index.php',
    'SORT' => 100,
  ),
  75 => 
  array (
    'CONDITION' => '#^/x/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/x/index.php',
    'SORT' => 100,
  ),
  83 => 
  array (
    'CONDITION' => '#^/p/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/p/index.php',
    'SORT' => 100,
  ),
);
