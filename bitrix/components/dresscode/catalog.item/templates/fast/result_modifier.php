<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true){
	die();
}

if(!empty($arResult)){
    // Управление показом кнопок

	// "Возможно изготовление на заказ"
	$arResult['DISPLAY_BUTTONS']['MTM_BUTTON'] = false;
	if(isset($arResult['PROPERTIES']['MTM']['VALUE'])
		&& $arResult['PROPERTIES']['MTM']['VALUE'] == 'Да'
	)
	{
		$arResult['DISPLAY_BUTTONS']['MTM_BUTTON'] = true;
	}

	// "Только под заказ"
	$arResult['DISPLAY_BUTTONS']['ONLY_ORDER_BUTTON'] = false;
	if(isset($arResult['PROPERTIES']['ORDER_ONLY']['VALUE'])
		&& $arResult['PROPERTIES']['ORDER_ONLY']['VALUE'] == 'Да'
	)
	{
		$arResult['DISPLAY_BUTTONS']['ONLY_ORDER_BUTTON'] = true;
	}

	// Показывать или скрывать кнопку "В корзину"
	$arResult['DISPLAY_BUTTONS']['CART_BUTTON'] = true;
	if(isset($arResult['PROPERTIES']['NO_CART_BUTTON']['VALUE'])
		&& $arResult['PROPERTIES']['NO_CART_BUTTON']['VALUE'] == 'Да'
	)
	{
		$arResult['DISPLAY_BUTTONS']['CART_BUTTON'] = false;
	}

	// Показывать или скрывать кнопку "Забронировать в салоне"
	$arResult['DISPLAY_BUTTONS']['RESERV_BUTTON'] = true;
	if(isset($arResult['PROPERTIES']['NO_RESERV_BUTTON']['VALUE'])
		&& $arResult['PROPERTIES']['NO_RESERV_BUTTON']['VALUE'] == 'Да'
	)
	{
		$arResult['DISPLAY_BUTTONS']['RESERV_BUTTON'] = false;
	}
}
