<?
set_time_limit(90);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

// Получаем список свойств для обновления
// 345  - Я.Маркет   114 - Бренд   154 - Похожие   152 - Сопутствующие

$arElements = [];

$obElm = CIBlockElement::GetList(
    ["ID"=>"ASC"],
    ["IBLOCK_ID" => "17", "ACTIVE"=>"Y", "ID"=>1239],
false,
false,
    ["ID", "IBLOCK_ID", "NAME", "PROPERTY_345",  "PROPERTY_114"]
);
while ($arElm = $obElm->GetNext()) {

    // Ставим категорию Я.Маркета
    if ($arElm['PROPERTY_345_VALUE'] != '') {
        // поиск  id категории маркета
        if (preg_match_all("/\[(\d+)\]/", $arElm['PROPERTY_345_VALUE'], $matches)) {
            if (intval($matches[1][0]) > 0) {
                $market_cat_id  = $matches[1][0];
                $arElements[$arElm['ID']][181] = $market_cat_id;
            }
        }
    }
    // Устанавливаем бренд
    if ($arElm['PROPERTY_114_VALUE'] != '') {

        $obElmBrand = CIBlockElement::GetList([], ["IBLOCK_ID"=> 1, "NAME" => $arElm['PROPERTY_114_VALUE']], false, false, ["ID"] );

        if ($arElmBrand = $obElmBrand->GetNext()) {
            $arElements[$arElm['ID']][134] = $arElmBrand['ID'];
        }

    }
}

  $obElm = CIBlockElement::GetList(
      ["ID"=>"ASC"],
      ["IBLOCK_ID" => "17", "ACTIVE"=>"Y"],
  false,
  false,
      ["ID", "IBLOCK_ID", "NAME",  "PROPERTY_154"]
  );
  while ($arElm = $obElm->GetNext()) {

      // Устанавливаем похожие товары
      if (intval($arElm['PROPERTY_154_VALUE']) > 0) {

          $obElmSim = CIBlockElement::GetList([], ["IBLOCK_ID"=> 17, "NAME" => $arElm['PROPERTY_154_VALUE']], false, false, ["ID"] );

          while ($arElmSim = $obElmSim->GetNext()) {
              $arElements[$arElm['ID']][153][] = $arElmSim['ID'];
          }
      }
      else {
          $arElements[$arElm['ID']][153][] = "";
      }

  }

  $obElm = CIBlockElement::GetList(
      ["ID"=>"ASC"],
      ["IBLOCK_ID" => "17", "ACTIVE"=>"Y"],
  false,
  false,
      ["ID", "IBLOCK_ID", "NAME",  "PROPERTY_152"]
  );
  while ($arElm = $obElm->GetNext()) {

      // Устанавливаем похожие товары
      if (intval($arElm['PROPERTY_152_VALUE']) > '0') {

          $obElmSim = CIBlockElement::GetList([], ["IBLOCK_ID"=> 17, "NAME" => $arElm['PROPERTY_152_VALUE']], false, false, ["ID"] );

          while ($arElmSim = $obElmSim->GetNext()) {
              $arElements[$arElm['ID']][151][] = $arElmSim['ID'];
          }
      }
      else {

              $arElements[$arElm['ID']][151][] = "";
         }

  }


if (!empty($arElements))
{

  foreach($arElements AS $k=>$arUpdate)
  {
    if (count($arUpdate[151]) > 20)
    {
      continue;
    }
    if (count($arUpdate[153]) > 20)
    {
        continue;
    }
    if (!empty($arUpdate)) {
        CIBlockElement::SetPropertyValuesEx($k, 17, $arUpdate);
    }
  }
}
