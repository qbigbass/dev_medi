<?
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
/** @global CCacheManager $CACHE_MANAGER */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
    die();

$arParams['STORE_CODE'] = (isset($arParams['STORE']) ? (string)$arParams['STORE'] : "");
if (strlen($arParams['STORE_CODE']) <= 0)
{
    $arParams['STORE'] = (isset($arParams['STORE']) ? (int)$arParams['STORE'] : 0);
    if ($arParams['STORE'] <= 0) {
        ShowError(GetMessage("STORE_NOT_EXIST"));
        return;
    }
}


$arParams['MAP_TYPE'] = (int)(isset($arParams['MAP_TYPE']) ? $arParams['MAP_TYPE'] : 0);

$arParams['SET_TITLE'] = (isset($arParams['SET_TITLE']) && $arParams['SET_TITLE'] == 'Y' ? 'Y' : 'N');

if (!isset($arParams['CACHE_TIME']))
	$arParams['CACHE_TIME'] = 3600;

if ($this->startResultCache())
{
	if (!\Bitrix\Main\Loader::includeModule("catalog"))
	{
		$this->abortResultCache();
		ShowError(GetMessage("CATALOG_MODULE_NOT_INSTALL"));
		return;
	}
	$arResult['STORE'] = $arParams['STORE'];
	$arSelect = array(
		"ID",
		"TITLE",
		"ADDRESS",
		"DESCRIPTION",
		"GPS_N",
		"GPS_S",
		"IMAGE_ID",
		"PHONE",
		"SCHEDULE",
		"EMAIL",
		"SITE_ID",
        "UF_SERVICES",
        "UF_ASSORTMENT",
        "UF_CITY",
        "UF_METRO",
        "UF_*"
	);

    $arFilter = array("ACTIVE" => "Y", "UF_SALON" => true);
    if (strlen($arParams['STORE_CODE'])>0 ) {
        $arFilter['CODE'] = $arParams['STORE_CODE'];
    }
    else
    {
        $arFilter['ID'] = $arParams['STORE'];
    }


    $storeIterator = CCatalogStore::GetList(array('ID' => 'ASC'), $arFilter,false,false,$arSelect);
	$arResult = $storeIterator->GetNext();
	unset($storeIterator);
	if (!$arResult)
	{
		$this->abortResultCache();

        include($_SERVER['DOCUMENT_ROOT']."/404.php");
		return;
	}
    /*
	$storeSite = (string)$arResult['SITE_ID'];
	if ($storeSite != '' && $storeSite != SITE_ID)
	{
		$this->abortResultCache();
		ShowError(GetMessage("STORE_NOT_EXIST"));
		return;
	}
	unset($storeSite);*/
    $arResult['TITLE'] = $arResult['UF_STORE_PUBLIC_NAME'];
	if($arResult["GPS_N"] != '' && $arResult["GPS_S"] != '')
		$this->abortResultCache();
	$arResult["MAP"] = $arParams["MAP_TYPE"];
	if(isset($arParams["PATH_TO_LISTSTORES"]))
		$arResult["LIST_URL"] = CComponentEngine::makePathFromTemplate($arParams["PATH_TO_LISTSTORES"]);
	$this->includeComponentTemplate();
}

if ($arParams["SET_TITLE"] == "Y")
{
	$title = (isset($arResult["TITLE"]) && $arResult["TITLE"] != '' ? $arResult["TITLE"]." (".$arResult["ADDRESS"].")" : $arResult["ADDRESS"]);
	$APPLICATION->SetTitle($title);
}
