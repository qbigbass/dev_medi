<?define("STOP_STATISTICS", true);?>
<?define("NO_AGENT_CHECK", true);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->SetTitle("RSS");
$GLOBALS['cityFilter'] = ['UF_YAEXPORT'=>true];
?><?$APPLICATION->IncludeComponent(
    "dresscode:catalog.store.list",
    "feed",
    Array(
        "CACHE_TIME" => "36000000",
        "CACHE_TYPE" => "A",
        "COMPOSITE_FRAME_MODE" => "A",
        "COMPOSITE_FRAME_TYPE" => "AUTO",
        "MAP_TYPE" => "0",
        "PATH_TO_ELEMENT" => "store/#store_code#",
        "PHONE" => "Y",
        "SCHEDULE" => "Y",
        "SET_TITLE" => "N",
       "FILTER_NAME" => "cityFilter"


    )
);?>
