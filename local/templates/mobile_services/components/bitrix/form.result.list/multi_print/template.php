<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

//__($arResult);
?>

<div class="order-list">
    <?
    if (!empty($arResult['arrResults'])): ?>
        <? foreach ($arResult['arrResults'] as $arOrder): ?>
            <?$APPLICATION->IncludeComponent("bitrix:form.result.view", "orto_short", Array(
            "SEF_MODE" => "Y",
            "RESULT_ID" => $arOrder['ID'],
            "SHOW_ADDITIONAL" => "Y",
            "SHOW_ANSWER_VALUE" => "Y",
            "SHOW_STATUS" => "Y",
            "EDIT_URL" => "/edit.php?id=#RESULT_ID#",
            "CHAIN_ITEM_TEXT" => "",
            "CHAIN_ITEM_LINK" => "",
            )
            );?>

        <? endforeach; ?>
    <? endif; ?>
    <script >
        window.print();
    </script>
</div>