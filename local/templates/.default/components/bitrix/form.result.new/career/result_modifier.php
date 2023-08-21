<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
 
$arResult['HIDE_FIELDS'] = [];
if (!empty($arParams['HIDDEN_FIELDS']))
{
	foreach ($arParams['HIDDEN_FIELDS'] as $key) {
		$arResult['HIDE_FIELDS'][] = $arResult['arAnswers'][$key][0]['ID'];
	}
}
