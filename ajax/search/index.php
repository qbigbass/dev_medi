<?define("STOP_STATISTICS", true);?>
<?define("NO_AGENT_CHECK", true);?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/search/prolog.php");

if (!empty($_GET) && $_GET["search_popular_phrases"] == "Y") {
    $arFields = [
        "PHRASE",
        "COUNT"
    ];

    $arPopularPhrases = [];
    $rsData = CSearchStatistic::GetList(["COUNT" => "DESC"], [], $arFields, true);

    while ($arRows = $rsData->Fetch()) {
        if (count($arPopularPhrases) < 6) {
            $arPopularPhrases[] = $arRows["PHRASE"];
        } else {
            break;
        }
    }

    $html = '';
    if (!empty($arPopularPhrases)) {
        foreach ($arPopularPhrases as $phrase) {
            $html .= '<span>' . $phrase . '</span>';
        }
    }

    echo $html;
}



