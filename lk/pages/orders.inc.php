<div class="h2 ff-medium title">Статус заказа</div>
<br>

    <div class="">

<?$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order",
	"lk_new",
	array(
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/lk/orders/",
		"ORDERS_PER_PAGE" => "10",
		"PATH_TO_PAYMENT" => "/lk/orders/payment/",
		"PATH_TO_BASKET" => "/personal/cart/",
		"SET_TITLE" => "N",
		"SAVE_IN_SESSION" => "N",
		"NAV_TEMPLATE" => "round",
		"SHOW_ACCOUNT_NUMBER" => "Y",
		"COMPONENT_TEMPLATE" => ".default",
		"PROP_1" => array(
		),
		"PROP_2" => "",
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_GROUPS" => "Y",
		"CUSTOM_SELECT_PROPS" => array(
		),
		"HISTORIC_STATUSES" => array(
			0 => "",
		),
		"DETAIL_HIDE_USER_INFO" => array(
			0 => "0",
		),
		"PATH_TO_CATALOG" => "/catalog/",
		"DISALLOW_CANCEL" => "N",
		"RESTRICT_CHANGE_PAYSYSTEM" => array(
			0 => "0",
		),
		"REFRESH_PRICES" => "N",
		"ORDER_DEFAULT_SORT" => "DATE_INSERT",
		"ALLOW_INNER" => "N",
		"ONLY_INNER_FULL" => "N",
		"SEF_URL_TEMPLATES" => array(
			"list" => "index.php",
			"detail" => "detail/#ID#/",
			"cancel" => "cancel/#ID#/",
		)
	),
	false
);?>
</div>
