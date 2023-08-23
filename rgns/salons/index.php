<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Адреса ортопедических салонов medi");
$APPLICATION->SetPageProperty("title", "Адреса ортопедических салонов medi");
$APPLICATION->SetPageProperty("description", "Здесь представлены адреса и контактные данные ортопедических салонов medi. &#9989 Выберите свой город и получите полный список салонов medi рядом с вами.");?>

<h1><?$APPLICATION->ShowTitle(true)?></h1>

<?$GLOBALS['arrFilter'] = array("UF_SALON" => "1"); ?>

<?$APPLICATION->IncludeComponent(
"dresscode:catalog.store",
".default",
array(
"CACHE_TIME" => "3600",
"CACHE_TYPE" => "A",
"COMPONENT_TEMPLATE" => ".default",
"MAP_TYPE" => "0",
"PHONE" => "Y",
"SCHEDULE" => "Y",
"EMAIL" => "Y",
"SEF_FOLDER" => "/salons/",
"SEF_MODE" => "Y",
"SET_TITLE" => "N",
"FILTER_NAME" => "arrFilter",
"TITLE" => "Список салонов с подробной информацией",
"SEF_URL_TEMPLATES" => array(
"liststores" => "",
"element" => "#store_code#/",
),
"NOW_HOUR" => date("H")
),
false
); ?><br /><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>
