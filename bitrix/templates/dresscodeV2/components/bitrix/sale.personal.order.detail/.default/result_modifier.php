<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
$res=array();
foreach ($arResult["BASKET"] as $k => $arItem)
{
	foreach ($arItem["PROPS"] as $key=>$propValue)
	{
		foreach ($arItem["SKU_DATA"] as $propId => $arProp)
		{
			if($propValue["NAME"]==$arProp["NAME"]){$res[$propId]=$arProp;unset($res[$propId]["VALUES"]);}
			foreach ($arProp["VALUES"] as $id => $arVal)
			{
				if($propValue["VALUE"]==$arVal["NAME"]){$res[$propId]["VALUES"][]=$arVal;}
			}
		}
	}
	$arResult["BASKET"][$k]["SKU_DATA"]=$res;
}