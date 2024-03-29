<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;
use Bitrix\Catalog;

Loc::loadMessages( __FILE__ );

class CAcritExportproCatalog extends CAcritCatalogCondCtrlIBlockFields{
    public static function GetClassName(){
        return __CLASS__;
    }

    public static function GetControls( $strControlID = false ){
        $arControlList = array(
            "CondExportproSetContains" => array(
                "ID" => "CondExportproSetContains",
                "FIELD" => "SET_CONTAINS",
                "FIELD_TYPE" => "int",
                "MULTIPLE" => "N",
                "GROUP" => "N",
                "LABEL" => GetMessage( "BT_MOD_CATALOG_COND_CMP_CATALOG_SET_CONTAINS" ),
                "PREFIX" => GetMessage( "BT_MOD_CATALOG_COND_CMP_CATALOG_SET_CONTAINS" ),
                "LOGIC" => static::GetLogic(
                    array(
                        BT_COND_LOGIC_EQ,
                        BT_COND_LOGIC_NOT_EQ,
                        BT_COND_LOGIC_GR,
                        BT_COND_LOGIC_LS,
                        BT_COND_LOGIC_EGR,
                        BT_COND_LOGIC_ELS
                    )
                ),
                "JS_VALUE" => array(
                    "type" => "popup",
                    "popup_url" =>  "/bitrix/admin/iblock_element_search.php",
                    "popup_params" => array(
                        "lang" => LANGUAGE_ID,
                        "IBLOCK_ID" => CAcritExportproProps::$arIBlockFilter[0],
                        "discount" => "Y"
                    ),
                    "param_id" => "n"
                ),
            ),
            "CondExportproAvailability" => array(
                "ID" => "CondExportproAvailability",
                "FIELD" => "CATALOG_AVAILABLE",
                "FIELD_TYPE" => "char",
                "MULTIPLE" => "N",
                "GROUP" => "N",
                "LABEL" => GetMessage( "BT_MOD_CATALOG_COND_CMP_CATALOG_AVAILABLE" ),
                "PREFIX" => GetMessage( "BT_MOD_CATALOG_COND_CMP_CATALOG_AVAILABLE" ),
                "LOGIC" => static::GetLogic(
                    array(
                        BT_COND_LOGIC_EQ,
                        BT_COND_LOGIC_NOT_EQ,
                    )
                ),
                "JS_VALUE" => array(
                    "type" => "select",
                    "values" => array(
                        "Y" => GetMessage( "BT_MOD_CATALOG_COND_CMP_CATALOG_AVAILABLE_YES" ),
                        "N" => GetMessage( "BT_MOD_CATALOG_COND_CMP_CATALOG_AVAILABLE_NO" ),
                    )
                ),
            ),
        );

        $dbStore = CCatalogStore::GetList(
            array(),
            array()
        );

        while( $arStore = $dbStore->GetNext() ){
            $arControlList["CondExportproStore:".$arStore["ID"]] = array(
                "ID" => "CondExportproStore:".$arStore["ID"],
                "FIELD" => "CATALOG_STORE_AMOUNT_".$arStore["ID"],
                "FIELD_TYPE" => "double",
                "MULTIPLE" => "N",
                "GROUP" => "N",
                "LABEL" => GetMessage( "BT_MOD_CATALOG_COND_CMP_CATALOG_SET_CATALOG_STORE" )." \"".$arStore["TITLE"].( !empty( $arStore["ADDRESS"] ) ? "\": ".$arStore["ADDRESS"] : "" ),
                "PREFIX" => GetMessage( "BT_MOD_CATALOG_COND_CMP_CATALOG_SET_CATALOG_STORE" )." \"".$arStore["TITLE"].( !empty( $arStore["ADDRESS"] ) ? "\": ".$arStore["ADDRESS"] : "" ),
                "LOGIC" => static::GetLogic(
                    array(
                        BT_COND_LOGIC_EQ,
                        BT_COND_LOGIC_NOT_EQ,
                        BT_COND_LOGIC_GR,
                        BT_COND_LOGIC_LS,
                        BT_COND_LOGIC_EGR,
                        BT_COND_LOGIC_ELS
                    )
                ),
                "JS_VALUE" => array(
                    "type" => "input"
                ),
            );
        }

        if( $strControlID === false ){
            return $arControlList;
        }
        elseif( isset( $arControlList[$strControlID] ) ){
            return $arControlList[$strControlID];
        }
        else{
            return false;
        }
    }

    public static function GetControlShow( $arParams ){
        $arControls = static::GetControls();
        $arResult = array(
            "controlgroup" => true,
            "group" =>  false,
            "label" => Loc::getMessage( "BT_MOD_CATALOG_COND_CMP_IBLOCK_CONTROLGROUP_CATALOG_LABEL" ),
            "showIn" => static::GetShowIn( $arParams["SHOW_IN_GROUPS"] ),
            "children" => array()
        );
        foreach( $arControls as &$arOneControl ){
            $arResult["children"][] = array(
                "controlId" => $arOneControl["ID"],
                "group" => false,
                "label" => $arOneControl["LABEL"],
                "showIn" => static::GetShowIn( $arParams["SHOW_IN_GROUPS"] ),
                "control" => array(
                    array(
                        "id" => "prefix",
                        "type" => "prefix",
                        "text" => $arOneControl["PREFIX"]
                    ),
                    static::GetLogicAtom( $arOneControl["LOGIC"] ),
                    static::GetValueAtom( $arOneControl["JS_VALUE"] )
                )
            );
        }
        if( isset( $arOneControl ) )
            unset( $arOneControl );

        return $arResult;
    }
}

class CAcritExportproPrices extends CAcritCatalogCondCtrlIBlockFields{
    public static function GetClassName(){
        return __CLASS__;
    }

    public static function GetControls( $strControlID = false ){
        $dbPriceType = CCatalogGroup::GetList(
            array(),
            array()
        );
        while( $arPriceType = $dbPriceType->GetNext() ){
            $priceName = $arPriceType["NAME_LANG"] ? "{$arPriceType["NAME_LANG"]} ({$arPriceType["NAME"]})" : GetMessage( "BT_MOD_CATALOG_COND_CMP_CATALOG_PRICE_NAME" )." ({$arPriceType["NAME"]})";
            $priceNameWD = $arPriceType["NAME_LANG"] ? "{$arPriceType["NAME_LANG"]} ({$arPriceType["NAME"]} - ".GetMessage( "BT_MOD_CATALOG_COND_CMP_CATALOG_PRICE_NAME_WD" ).")" : GetMessage( "BT_MOD_CATALOG_COND_CMP_CATALOG_PRICE_NAME" )." ({$arPriceType["NAME"]} - ".GetMessage( "BT_MOD_CATALOG_COND_CMP_CATALOG_PRICE_NAME_WD" ).")";
            $priceNameD = $arPriceType["NAME_LANG"] ? "{$arPriceType["NAME_LANG"]} ({$arPriceType["NAME"]} - ".GetMessage( "BT_MOD_CATALOG_COND_CMP_CATALOG_PRICE_NAME_D" ).")" : GetMessage( "BT_MOD_CATALOG_COND_CMP_CATALOG_PRICE_NAME" )." ({$arPriceType["NAME"]} - ".GetMessage( "BT_MOD_CATALOG_COND_CMP_CATALOG_PRICE_NAME_D" ).")";

            $arControlList["CondCatPrice_".$arPriceType["ID"]] = array(
                "ID" => "CondCatPrice_".$arPriceType["ID"],
                "FIELD" => "CATALOG_PRICE_".$arPriceType["ID"],
                "FIELD_TYPE" => "double",
                "MULTIPLE" => "N",
                "GROUP" => "N",
                "LABEL" => $priceName,
                "PREFIX" => $priceName,
                "LOGIC" => static::GetLogic( array( BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ, BT_COND_LOGIC_GR, BT_COND_LOGIC_LS, BT_COND_LOGIC_EGR, BT_COND_LOGIC_ELS ) ),
                "JS_VALUE" => array(
                    "type" => "input"
                ),
            );

            $arControlList["CondCatPrice_".$arPriceType["ID"]."_WD"] = array(
                "ID" => "CondCatPrice_".$arPriceType["ID"]."_WD",
                "FIELD" => "CATALOG_PRICE_".$arPriceType["ID"]."_WD",
                "FIELD_TYPE" => "double",
                "MULTIPLE" => "N",
                "GROUP" => "N",
                "LABEL" => $priceNameWD,
                "PREFIX" => $priceNameWD,
                "LOGIC" => static::GetLogic( array( BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ, BT_COND_LOGIC_GR, BT_COND_LOGIC_LS, BT_COND_LOGIC_EGR, BT_COND_LOGIC_ELS ) ),
                "JS_VALUE" => array(
                    "type" => "input"
                ),
            );

            $arControlList["CondCatPrice_".$arPriceType["ID"]."_D"] = array(
                "ID" => "CondCatPrice_".$arPriceType["ID"]."_D",
                "FIELD" => "CATALOG_PRICE_".$arPriceType["ID"]."_D",
                "FIELD_TYPE" => "double",
                "MULTIPLE" => "N",
                "GROUP" => "N",
                "LABEL" => $priceNameD,
                "PREFIX" => $priceNameD,
                "LOGIC" => static::GetLogic( array( BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ, BT_COND_LOGIC_GR, BT_COND_LOGIC_LS, BT_COND_LOGIC_EGR, BT_COND_LOGIC_ELS ) ),
                "JS_VALUE" => array(
                    "type" => "input"
                ),
            );
        }
        if( $strControlID === false ){
            return $arControlList;
        }
        elseif( isset( $arControlList[$strControlID] ) ){
            return $arControlList[$strControlID];
        }
        else{
            return false;
        }
    }

    public static function GetControlShow( $arParams ){
        $arControls = static::GetControls();
        $arResult = array(
            "controlgroup" => true,
            "group" =>  false,
            "label" => Loc::getMessage( "BT_MOD_CATALOG_COND_CMP_IBLOCK_CONTROLGROUP_PRICE_LABEL" ),
            "showIn" => static::GetShowIn( $arParams["SHOW_IN_GROUPS"] ),
            "children" => array()
        );

        foreach( $arControls as &$arOneControl ){
            $arResult["children"][] = array(
                "controlId" => $arOneControl["ID"],
                "group" => false,
                "label" => $arOneControl["LABEL"],
                "showIn" => static::GetShowIn( $arParams["SHOW_IN_GROUPS"] ),
                "control" => array(
                    array(
                        "id" => "prefix",
                        "type" => "prefix",
                        "text" => $arOneControl["PREFIX"]
                    ),
                    static::GetLogicAtom( $arOneControl["LOGIC"] ),
                    static::GetValueAtom( $arOneControl["JS_VALUE"] )
                )
            );
        }

        if( isset( $arOneControl ) )
            unset( $arOneControl );

        return $arResult;
    }
}

class CAcritExportproProps extends CAcritCatalogCondCtrlIBlockProps{
    public static $arIBlockFilter = array();
    public static $arEnums = array();
    public static function GetClassName(){
        return __CLASS__;
    }

    public static function GetControls( $strControlID = false ){
        $arControlList = array();

        $arIBlockList = self::$arIBlockFilter;
        if( empty( $arIBlockList ) ){
            $rsIBlocks = CCatalog::GetList(
                array(),
                array(),
                false,
                false,
                array(
                    "IBLOCK_ID",
                    "PRODUCT_IBLOCK_ID"
                )
            );

            while( $arIBlock = $rsIBlocks->Fetch() ){
                $arIBlock["IBLOCK_ID"] = (int)$arIBlock["IBLOCK_ID"];
                $arIBlock["PRODUCT_IBLOCK_ID"] = (int)$arIBlock["PRODUCT_IBLOCK_ID"];
                if( $arIBlock["IBLOCK_ID"] > 0 )
                    $arIBlockList[$arIBlock["IBLOCK_ID"]] = true;
                if( $arIBlock["PRODUCT_IBLOCK_ID"] > 0 )
                    $arIBlockList[$arIBlock["PRODUCT_IBLOCK_ID"]] = true;
            }
            unset( $arIBlock, $rsIBlocks );
            $arIBlockList = array_keys( $arIBlockList );
        }
        if( !empty( $arIBlockList ) && is_array( $arIBlockList ) ){
            sort( $arIBlockList );
            if( !count( self::$arEnums ) ){
                $enumCodes = array();

                foreach( $arIBlockList as $intIBlockID ){
                    $rsProps = CIBlockProperty::GetList(
                        array(),
                        array(
                            "IBLOCK_ID" => $intIBlockID
                        )
                    );

                    while( $arProp = $rsProps->Fetch() ){
                        if( "L" == $arProp["PROPERTY_TYPE"] ){
                            $enumCodes[] = $arProp["CODE"];
                        }
                    }
                }

                if( count( $enumCodes ) ){
                    $time = time();
                    $rsPropEnums = CIBlockPropertyEnum::GetList(
                        array(
                            "DEF" => "DESC",
                            "SORT" => "ASC"
                        ),
                        array(
                            "CODE" => $enumCodes
                        )
                    );
                    unset( $enumCodes );

                    while( $arPropEnum = $rsPropEnums->Fetch() ){
                        self::$arEnums[$arPropEnum["PROPERTY_ID"]][] = $arPropEnum;
                    }
                }
            }

            foreach( $arIBlockList as &$intIBlockID ){
                $strName = CIBlock::GetArrayByID( $intIBlockID, "NAME" );
                if( false !== $strName ){
                    $boolSep = true;
                    $rsProps = CIBlockProperty::GetList(
                        array(
                            "SORT" => "ASC",
                            "NAME" => "ASC"
                        ),
                        array(
                            "IBLOCK_ID" => $intIBlockID
                        )
                    );

                    while( $arProp = $rsProps->Fetch() ){
                        if( "CML2_LINK" == $arProp["XML_ID"] || ( "F" == $arProp["PROPERTY_TYPE"] ) )
                            continue;

                        if( "L" == $arProp["PROPERTY_TYPE"] ){
                            $arProp["VALUES"] = array();

                            if( isset( self::$arEnums[$arProp["ID"]] ) && count( self::$arEnums[$arProp["ID"]] ) ){
                                $arProp["VALUES"] = self::$arEnums[$arProp["ID"]];
                            }
                            elseif( !isset( self::$arEnums[$arProp["ID"]] ) || !count( self::$arEnums[$arProp["ID"]] ) ){
                                continue;
                            }
                        }

                        $strFieldType = "";
                        $arLogic = array();
                        $arValue = array();
                        $arPhpValue = "";

                        $boolUserType = false;
                        if( isset( $arProp["USER_TYPE"] ) && !empty( $arProp["USER_TYPE"] ) ){
                            switch( $arProp["USER_TYPE"] ){
                                case "DateTime":
                                    $strFieldType = "datetime";
                                    $arLogic = static::GetLogic( array( BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ, BT_COND_LOGIC_GR, BT_COND_LOGIC_LS, BT_COND_LOGIC_EGR, BT_COND_LOGIC_ELS ) );
                                    $arValue = array(
                                        "type" => "datetime",
                                        "format" => "datetime"
                                    );
                                    $boolUserType = true;
                                    break;
                                default:
                                    $boolUserType = false;
                                    break;
                            }
                        }

                        if( !$boolUserType ){
                            switch( $arProp["PROPERTY_TYPE"] ){
                                case "N":
                                    $strFieldType = "double";
                                    $arLogic = static::GetLogic( array( BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ, BT_COND_LOGIC_GR, BT_COND_LOGIC_LS, BT_COND_LOGIC_EGR, BT_COND_LOGIC_ELS ) );
                                    $arValue = array( "type" => "input" );
                                    break;
                                case "S":
                                    $strFieldType = "text";
                                    $arLogic = static::GetLogic( array( BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ, BT_COND_LOGIC_CONT, BT_COND_LOGIC_NOT_CONT ) );
                                    $arValue = array( "type" => "input" );
                                    break;
                                case "L":
                                    $strFieldType = "int";
                                    $arLogic = static::GetLogic( array( BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ ) );
                                    $arValue = array(
                                        "type" => "select",
                                        "values" => array()
                                    );
                                    foreach( $arProp["VALUES"] as &$arOnePropValue ){
                                        $arValue["values"][$arOnePropValue["ID"]] = $arOnePropValue["VALUE"];
                                    }
                                    if( isset( $arOnePropValue ) )
                                        unset( $arOnePropValue );
                                    $arPhpValue = array( "VALIDATE" => "list" );
                                    break;
                                case "E":
                                    $strFieldType = "int";
                                    $arLogic = static::GetLogic( array( BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ ) );
                                    $arValue = array(
                                        "type" => "popup",
                                        "popup_url" =>  "/bitrix/admin/iblock_element_search.php",
                                        "popup_params" => array(
                                            "lang" => LANGUAGE_ID,
                                            "IBLOCK_ID" => $arProp["LINK_IBLOCK_ID"],
                                            "discount" => "Y"
                                        ),
                                        "param_id" => "n"
                                    );
                                    $arPhpValue = array( "VALIDATE" => "element" );
                                    break;
                                case "G":
                                    $strFieldType = "int";
                                    $arLogic = static::GetLogic( array( BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ ) );
                                    $arValue = array(
                                        "type" => "popup",
                                        "popup_url" =>  "/bitrix/admin/cat_section_search.php",
                                        "popup_params" => array(
                                            "lang" => LANGUAGE_ID,
                                            "IBLOCK_ID" => $arProp["LINK_IBLOCK_ID"],
                                            "discount" => "Y"
                                        ),
                                        "param_id" => "n"
                                    );
                                    $arPhpValue = array( "VALIDATE" => "section" );
                                    break;
                            }
                        }
                        $arControlList["CondIBProp:".$intIBlockID.":".$arProp["ID"]] = array(
                            "ID" => "CondIBProp:".$intIBlockID.":".$arProp["ID"],
                            "PARENT" => false,
                            "EXIST_HANDLER" => "Y",
                            "MODULE_ID" => "catalog",
                            "MODULE_ENTITY" => "iblock",
                            "ENTITY" => "ELEMENT_PROPERTY",
                            "IBLOCK_ID" => $intIBlockID,
                            "FIELD" => "PROPERTY_".$arProp["ID"]."_VALUE",
                            "FIELD_TABLE" => $intIBlockID.":".$arProp["ID"],
                            "FIELD_TYPE" => $strFieldType,
                            "MULTIPLE" => "Y",
                            "GROUP" => "N",
                            "SEP" => ( $boolSep ? "Y" : "N" ),
                            "SEP_LABEL" => ( $boolSep ? str_replace( array( "#ID#", "#NAME#" ), array( $intIBlockID, $strName ), Loc::getMessage( "BT_MOD_CATALOG_COND_CMP_CATALOG_PROP_LABEL" ) ) : "" ),
                            "LABEL" => $arProp["NAME"],
                            "PREFIX" => str_replace( array( "#NAME#", "#IBLOCK_ID#", "#IBLOCK_NAME#" ), array( $arProp["NAME"], $intIBlockID, $strName ), Loc::getMessage( "BT_MOD_CATALOG_COND_CMP_CATALOG_ONE_PROP_PREFIX" ) ),
                            "LOGIC" => $arLogic,
                            "JS_VALUE" => $arValue,
                            "PHP_VALUE" => $arPhpValue
                        );

                        $boolSep = false;
                    }
                }
            }
            if( isset( $intIBlockID ) )
                unset( $intIBlockID );

            unset( $arIBlockList );
        }

        if( $strControlID === false ){
            return $arControlList;
        }
        elseif( isset( $arControlList[$strControlID] ) ){
            return $arControlList[$strControlID];
        }
        else{
            return false;
        }
    }
}


class CAcritExportproCatalogCond extends CAcritGlobalCondTree{
    public static function GetClassName(){
        return __CLASS__;
    }

    public function __construct(){
        parent::__construct();
    }

    public function __destruct(){
        parent::__destruct();
    }

    public function Parse( $arData = "", $arParams = false ){
        global $APPLICATION;
        $this->arMsg = array();

        $this->usedModules = array();
        $this->usedExtFiles = array();

        $arResult = array();
        if( !$this->boolError ){
            if( empty( $arData ) || !is_array( $arData ) ){
                if( isset( $_POST[$this->strPrefix] ) && !empty( $_POST[$this->strPrefix] ) && is_array( $_POST[$this->strPrefix] ) ){
                    $arData = $_POST[$this->strPrefix];
                }
                else{
                    $this->boolError = true;
                    $this->arMsg[] = array(
                        "id" => "CONDITIONS",
                        "text" => Loc::getMessage( "BT_MOD_COND_ERR_PARSE_DATA_EMPTY" )
                    );
                }
            }
        }

        if( !$this->boolError ){
            foreach( $arData as $strKey => $value ){
                $arKeys = $this->__ConvertKey( $strKey );
                if( empty( $arKeys ) ){
                    $this->boolError = true;
                    $this->arMsg[] = array(
                        "id" => "CONDITIONS",
                        "text" => Loc::getMessage( "BT_MOD_COND_ERR_PARSE_DATA_BAD_KEY" )
                    );
                    break;
                }

                if( !isset( $value["controlId"] ) || empty( $value["controlId"] ) ){
                    $this->boolError = true;
                    $this->arMsg[] = array(
                        "id" => "CONDITIONS",
                        "text" => Loc::getMessage( "BT_MOD_COND_ERR_PARSE_DATA_EMPTY_CONTROLID" )
                    );
                    break;
                }

                if( !isset( $this->arControlList[$value["controlId"]] ) ){
                    $this->boolError = true;
                    $this->arMsg[] = array(
                        "id" => "CONDITIONS",
                        "text" => Loc::getMessage( "BT_MOD_COND_ERR_PARSE_DATA_BAD_CONTROLID" )
                    );
                    break;
                }

                $arOneCondition = call_user_func_array(
                    $this->arControlList[$value["controlId"]]["Parse"],
                    array( $value )
                );

                if( false === $arOneCondition ){
                    $this->arMsg[] = array(
                        "id" => "CONDITIONS",
                        "text" => Loc::getMessage( "BT_MOD_COND_ERR_PARSE_DATA_CONTROL_BAD_VALUE" )
                    );

                    unset( $arData[$strKey] );
                    continue;
                }

                $arItem = array(
                    "CLASS_ID" => $value["controlId"],
                    "DATA" => $arOneCondition
                );

                if( "Y" == $this->arControlList[$value["controlId"]]["GROUP"] ){
                    $arItem["CHILDREN"] = array();
                }

                if( !$this->__SetCondition( $arResult, $arKeys, 0, $arItem ) ){
                    $this->boolError = true;
                    $this->arMsg[] = array(
                        "id" => "CONDITIONS",
                        "text" => Loc::getMessage( "BT_MOD_COND_ERR_PARSE_DATA_DOUBLE_KEY" )
                    );
                    break;
                }
            }
        }

        if( $this->boolError ){
            $obError = new CAdminException( $this->arMsg );
            $APPLICATION->ThrowException( $obError );
        }

        return ( !$this->boolError ? $arResult : "" );
    }

    public function Show( $arConditions = "" ){
        if( empty( $arConditions ) && !isset( $_REQUEST["ID"] ) ){
            $arConditions = array(
                "CLASS_ID" => "CondGroup",
                "DATA" => array(
                    "All" => "AND",
                    "True" => "True"
                ),
                "CHILDREN" => array(
                    array(
                        "CLASS_ID" => "CondIBActive",
                        "DATA" => array(
                                "logic" => "Equal",
                                "value" => "Y"
                        )
                    )
                )
            );
        }

        parent::Show( $arConditions );
    }

    public function OnConditionControlBuildList(){
        if( !$this->boolError && !isset( $this->arControlList ) ){
            $this->arControlList = array();
            $this->arShowInGroups = array();
            $this->forcedShowInGroup = array();
            $this->arShowControlList = array();
            $this->arInitControlList = array();

            $basicEvents = GetModuleEvents( $this->arEvents["CONTROLS"]["MODULE_ID"], $this->arEvents["CONTROLS"]["EVENT_ID"], true );
            $basicEvents = array_merge( $basicEvents, array(
                    array(
                        "TO_CLASS" => "CAcritExportproPrices",
                        "TO_METHOD" => "GetControlDescr"
                    ),
                    array(
                        "TO_CLASS" => "CAcritExportproCatalog",
                        "TO_METHOD" => "GetControlDescr"
                    ),
                )
            );
            foreach( $basicEvents as $arEvent ){
                if( $arEvent["TO_CLASS"] == "CAcritCatalogCondCtrlIBlockProps" ){
                    $arEvent["TO_CLASS"] = "CAcritExportproProps";
                    $arEvent["TO_MODULE_ID"] = "acrit.exportpro";
                    $arEvent["TO_NAME"] = "CAcritExportproProps::GetControlDescr (acrit.exportpro)";
                }
                $arRes = ExecuteModuleEventEx( $arEvent );
                if( !is_array( $arRes ) )
                    continue;

                if( isset( $arRes["ID"] ) ){
                    if( isset( $arRes["EXIST_HANDLER"] ) && ( $arRes["EXIST_HANDLER"] === "Y" ) ){
                        if( !isset( $arRes["MODULE_ID"] ) && !isset( $arRes["EXT_FILE"] ) )
                            continue;
                    }
                    else{
                        $arRes["MODULE_ID"] = "";
                        $arRes["EXT_FILE"] = "";
                    }

                    if( array_key_exists( "EXIST_HANDLER", $arRes ) )
                        unset( $arRes["EXIST_HANDLER"] );

                    $arRes["GROUP"] = ( ( isset( $arRes["GROUP"] ) && ( $arRes["GROUP"] == "Y" ) ) ? "Y" : "N" );
                    if( isset( $this->arControlList[$arRes["ID"]] ) ){
                        $this->arMsg[] = array( "id" => "CONTROLS", "text" => str_replace( "#CONTROL#", $arRes["ID"], Loc::getMessage( "BT_MOD_COND_ERR_CONTROL_DOUBLE" ) ) );
                        $this->boolError = true;
                    }
                    else{
                        if( !$this->CheckControl( $arRes ) )
                            continue;

                        $this->arControlList[$arRes["ID"]] = $arRes;
                        if( $arRes["GROUP"] == "Y" ){
                            $this->arShowInGroups[] = $arRes["ID"];
                        }
                        if( isset( $arRes["GetControlShow"] ) && !empty( $arRes["GetControlShow"] ) ){
                            if( !in_array( $arRes["GetControlShow"], $this->arShowControlList ) )
                                $this->arShowControlList[] = $arRes["GetControlShow"];
                        }
                        if( isset( $arRes["InitParams"] ) && !empty( $arRes["InitParams"] ) ){
                            if( !in_array( $arRes["InitParams"], $this->arInitControlList ) )
                                $this->arInitControlList[] = $arRes["InitParams"];
                        }
                    }
                }
                elseif( isset( $arRes["COMPLEX"] ) && ( "Y" == $arRes["COMPLEX"] ) ){
                    $complexModuleID = "";
                    $complexExtFiles = "";
                    if( isset( $arRes["EXIST_HANDLER"] ) && $arRes["EXIST_HANDLER"] === "Y" ){
                        if( isset( $arRes["MODULE_ID"] ) )
                            $complexModuleID = $arRes["MODULE_ID"];
                        if( isset( $arRes["EXT_FILE"] ) )
                            $complexExtFiles = $arRes["EXT_FILE"];
                    }
                    if( isset( $arRes["CONTROLS"] ) && !empty( $arRes["CONTROLS"] ) && is_array( $arRes["CONTROLS"] ) ){
                        if( array_key_exists( "EXIST_HANDLER", $arRes ) )
                            unset( $arRes["EXIST_HANDLER"] );

                        $arInfo = $arRes;
                        unset( $arInfo["COMPLEX"], $arInfo["CONTROLS"] );
                        foreach( $arRes["CONTROLS"] as &$arOneControl ){
                            if( isset( $arOneControl["ID"] ) ){
                                if( isset( $arOneControl["EXIST_HANDLER"] ) && ( $arOneControl["EXIST_HANDLER"] === "Y" ) ){
                                    if( !isset( $arOneControl["MODULE_ID"] ) && !isset( $arOneControl["EXT_FILE"] ) )
                                        continue;
                                }
                                $arInfo["GROUP"] = "N";
                                $arInfo["MODULE_ID"] = isset( $arOneControl["MODULE_ID"] ) ? $arOneControl["MODULE_ID"] : $complexModuleID;
                                $arInfo["EXT_FILE"] = isset( $arOneControl["EXT_FILE"] ) ? $arOneControl["EXT_FILE"] : $complexExtFiles;
                                $control = array_merge( $arOneControl, $arInfo );
                                if( isset( $this->arControlList[$control["ID"]] ) ){
                                    $this->arMsg[] = array( "id" => "CONTROLS", "text" => str_replace( "#CONTROL#", $control["ID"], Loc::getMessage( "BT_MOD_COND_ERR_CONTROL_DOUBLE" ) ) );
                                    $this->boolError = true;
                                }
                                else{
                                    if( !$this->CheckControl( $control ) )
                                        continue;

                                    $this->arControlList[$control["ID"]] = $control;
                                }
                                unset( $control );
                            }
                        }
                        if( isset( $arOneControl ) )
                            unset( $arOneControl );

                        if( isset( $arRes["GetControlShow"] ) && !empty( $arRes["GetControlShow"] ) ){
                            if( !in_array($arRes["GetControlShow"], $this->arShowControlList ) )
                                $this->arShowControlList[] = $arRes["GetControlShow"];
                        }

                        if( isset( $arRes["InitParams"] ) && !empty( $arRes["InitParams"] ) ){
                            if( !in_array( $arRes["InitParams"], $this->arInitControlList ) )
                                $this->arInitControlList[] = $arRes["InitParams"];
                        }
                    }
                }
                else{
                    foreach( $arRes as &$arOneRes ){
                        if( is_array( $arOneRes ) && isset( $arOneRes["ID"] ) ){
                            if( isset( $arOneRes["EXIST_HANDLER"] ) && ( $arOneRes["EXIST_HANDLER"] === "Y" ) ){
                                if( !isset( $arOneRes["MODULE_ID"] ) && !isset( $arOneRes["EXT_FILE"] ) )
                                    continue;
                            }
                            else{
                                $arOneRes["MODULE_ID"] = "";
                                $arOneRes["EXT_FILE"] = "";
                            }

                            if( array_key_exists( "EXIST_HANDLER", $arOneRes ) )
                                unset( $arOneRes["EXIST_HANDLER"] );

                            $arOneRes["GROUP"] = ( isset( $arOneRes["GROUP"] ) && ( ( $arOneRes["GROUP"] == "Y" ) ? "Y" : "N" ) );
                            if( isset( $this->arControlList[$arOneRes["ID"]] ) ){
                                $this->arMsg[] = array( "id" => "CONTROLS", "text" => str_replace( "#CONTROL#", $arOneRes["ID"], Loc::getMessage( "BT_MOD_COND_ERR_CONTROL_DOUBLE" ) ) );
                                $this->boolError = true;
                            }
                            else{
                                if( !$this->CheckControl( $arOneRes ) )
                                    continue;

                                $this->arControlList[$arOneRes["ID"]] = $arOneRes;

                                if( $arOneRes["GROUP"] == "Y" ){
                                    $this->arShowInGroups[] = $arOneRes["ID"];
                                }

                                if( isset( $arOneRes["GetControlShow"] ) && !empty( $arOneRes["GetControlShow"] ) ){
                                    if( !in_array( $arOneRes["GetControlShow"], $this->arShowControlList ) )
                                        $this->arShowControlList[] = $arOneRes["GetControlShow"];
                                }

                                if( isset( $arOneRes["InitParams"] ) && !empty( $arOneRes["InitParams"] ) ){
                                    if( !in_array( $arOneRes["InitParams"], $this->arInitControlList ) )
                                        $this->arInitControlList[] = $arOneRes["InitParams"];
                                }
                            }
                        }
                    }

                    if( isset( $arOneRes ) )
                        unset( $arOneRes );
                }
            }

            if( empty( $this->arControlList ) ){
                $this->arMsg[] = array( "id" => "CONTROLS", "text" => Loc::getMessage( "BT_MOD_COND_ERR_CONTROLS_EMPTY" ) );
                $this->boolError = true;
            }
        }
    }
}