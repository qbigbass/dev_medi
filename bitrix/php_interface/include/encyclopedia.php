<?php
// Устанавливаем рандомное кол-во лайков для новой статьи в ИБ "Энциклопедия"
AddEventHandler("iblock", "OnAfterIBlockElementAdd", "SetEncPostLikes");

function SetEncPostLikes($arFields) {
    if ($arFields['IBLOCK_ID'] === 3 ) {
        $cntLikes = random_int(50, 150);
        CIBlockElement::SetPropertyValuesEx($arFields["ID"], "3", array("LIKES_CNT" => $cntLikes));
    }
}
