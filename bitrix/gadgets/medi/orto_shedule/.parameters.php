<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("sale"))
    return false;

$arParameters = Array(
    "PARAMETERS"=> Array(),
    "USER_PARAMETERS"=> Array(
    ),
);