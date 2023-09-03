<?
if (defined("SITE_ID")){
    /*if (SITE_ID == 's1')
    {
        $GLOBALS['price_id'] = '1';
        $GLOBALS['medi']['price_id'][SITE_ID] = '1';
        $GLOBALS['medi']['max_price_id'][SITE_ID] = '2';
    }
    else*/
    if (SITE_ID == 's2')
    {
        $GLOBALS['price_id'] = '6';
        $GLOBALS['medi']['price_id'][SITE_ID] = '6';
        $GLOBALS['medi']['max_price_id'][SITE_ID] = '5';
    }
    else {
        $GLOBALS['price_id'] = '1';
        $GLOBALS['medi']['price_id'][SITE_ID] = '1';
        $GLOBALS['medi']['max_price_id'][SITE_ID] = '2';
    }
}
// Значения сортировки для меток
const SORT_SIGN = [
    509 => 40500, //Новинка
    510 => 30500, //Хит продаж
    13477 => 20500, //Акция
    511 => 10500 //Распродажа
];