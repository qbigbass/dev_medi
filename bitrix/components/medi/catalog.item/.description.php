<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$arComponentDescription = array(
   "NAME" => GetMessage("NAME"),
   "DESCRIPTION" => GetMessage("DESCRIPTION"),
   "ICON" => "/images/compare.gif",
   "PATH" => array(
      "ID" => "DRESSCODE",
      "CHILD" => array(
         "ID" => "catalogItem",
         "NAME" => GetMessage("NAME")
      )
   )
);
?>