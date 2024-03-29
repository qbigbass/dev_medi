<?
IncludeModuleLangFile( __FILE__ );

class acrit_exportpro extends CModule{
    const MODULE_ID = "acrit.exportpro";
    var $MODULE_ID = "acrit.exportpro";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    private $siteArray = array();

    private $siteEncoding = array(
        "utf-8" => "utf8",
        "UTF-8" => "utf8",
        "WINDOWS-1251" => "cp1251",
        "windows-1251" => "cp1251",
    );

    function __construct(){
        require( __DIR__."/version.php" );

        $path = str_replace( "\\", "/", __FILE__ );
        $path = substr( $path, 0, strlen( $path ) - strlen( "/index.php" ) );
        include( $path."/version.php" );
        if( is_array( $arModuleVersion ) && array_key_exists( "VERSION", $arModuleVersion ) ){
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this->MODULE_NAME = GetMessage( "ACRIT_EXPORTPRO_MODULE_NAME" );
        $this->MODULE_DESCRIPTION = GetMessage( "ACRIT_EXPORTPRO_MODULE_DESC" );
        $this->PARTNER_NAME = GetMessage("ACRIT_EXPORTPRO_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("ACRIT_EXPORTPRO_PARTNER_URI");

        $app = \Bitrix\Main\Application::getInstance();
        $dbSite = \Bitrix\Main\SiteTable::getList();
        while( $arSite = $dbSite->Fetch() ){
            if( !$arSite["DOC_ROOT"] )
                $this->siteArray[$arSite["LID"]] = $app->getDocumentRoot().$arSite["DIR"];
            else
                $this->siteArray[$arSite["LID"]] = $arSite["DOC_ROOT"];

            $this->siteArray[$arSite["LID"]] = \Bitrix\Main\IO\Path::normalize($this->siteArray[$arSite["LID"]]);
        }

    }

    function InstallEvents(){
        RegisterModuleDependences( "main", "OnBuildGlobalMenu", self::MODULE_ID , "CAcritExportproMenu", "OnBuildGlobalMenu" );

        RegisterModuleDependences( self::MODULE_ID, "OnCondCatControlBuildList", self::MODULE_ID, "CAcritCatalogCondCtrlGroup", "GetControlDescr", 100 );
        RegisterModuleDependences( self::MODULE_ID, "OnCondCatControlBuildList", self::MODULE_ID, "CAcritCatalogCondCtrlIBlockFields", "GetControlDescr", 200 );
        RegisterModuleDependences( self::MODULE_ID, "OnCondCatControlBuildList", self::MODULE_ID, "CAcritCatalogCondCtrlIBlockProps", "GetControlDescr", 300 );

        return true;
    }

    function UnInstallEvents(){
        UnRegisterModuleDependences( "main", "OnBuildGlobalMenu", self::MODULE_ID, "CAcritExportproMenu", "OnBuildGlobalMenu" );

        UnRegisterModuleDependences( self::MODULE_ID, "OnCondCatControlBuildList", self::MODULE_ID, "CAcritCatalogCondCtrlGroup", "GetControlDescr" );
        UnRegisterModuleDependences( self::MODULE_ID, "OnCondCatControlBuildList", self::MODULE_ID, "CAcritCatalogCondCtrlIBlockFields", "GetControlDescr" );
        UnRegisterModuleDependences( self::MODULE_ID, "OnCondCatControlBuildList", self::MODULE_ID, "CAcritCatalogCondCtrlIBlockProps", "GetControlDescr" );

        return true;
    }

    function InstallDB( $arParams = array() ){
        global $DB, $DBType, $APPLICATION;

        $this->errors = false;
        if( CModule::IncludeModule( "security" ) ){
            $dbSecurityFilter = CSecurityFilterMask::GetList();
            $acritMask = "/bitrix/admin/acrit_exportpro_edit.php";

            $bMaskSet = false;
            $arFilterMask = array();
            while( $arSecurityFilter = $dbSecurityFilter->Fetch() ){
                if( $arSecurityFilter["FILTER_MASK"] == $acritMask )
                    $bMaskSet = true;

                $arFilterMask[] = array(
                    "MASK" => $arSecurityFilter["FILTER_MASK"],
                    "SITE_ID" => $arSecurityFilter["SITE_ID"]
                );
            }

            if( !$bMaskSet ){
                $arFilterMask[] = array(
                    "MASK" => $acritMask,
                    "SITE_ID" => ""
                );
                CSecurityFilterMask::Update( $arFilterMask );
            }
        }

        $this->errors = $DB->RunSQLBatch( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/db/install.sql" );
        if( $this->errors !== false ){
            $APPLICATION->ThrowException( implode( "<br/>", $this->errors ) );
            return false;
        }

        return true;
    }

    function UninstallDB( $arParams = array() ){
        global $DB, $DBType, $APPLICATION;

        $this->errors = false;

        if( CModule::IncludeModule( "security" ) ){
            $dbSecurityFilter = CSecurityFilterMask::GetList();
            $acritMask = "/bitrix/admin/acrit_export_edit.php";

            $bMaskSet = false;
            $arFilterMask = array();
            while( $arSecurityFilter = $dbSecurityFilter->Fetch() ){
                if( $arSecurityFilter["FILTER_MASK"] == $acritMask )
                    $bMaskSet = true;
                else
                    $arFilterMask[] = array(
                        "MASK" => $arSecurityFilter["FILTER_MASK"],
                        "SITE_ID" => $arSecurityFilter["SITE_ID"]
                    );
            }

            if( $bMaskSet ){
                CSecurityFilterMask::Update( $arFilterMask );
            }
        }

        if( !array_key_exists( "save_tables", $arParams ) || ( $arParams["save_tables"] != "Y" ) ){
            $this->errors = $DB->RunSQLBatch( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/db/uninstall.sql" );
        }

        return true;
    }

    function DoInstall(){
        global $DOCUMENT_ROOT, $APPLICATION, $DB, $DBType, $step, $install;
        if( $APPLICATION->GetGroupRight( "main" ) < "W" ){
            return;
        }

        CJSCore::Init( array( "jquery" ) );
        $arJqueryExt = CJSCore::getExtInfo( "jquery" );

        if( !is_array( $arJqueryExt ) || !isset( $arJqueryExt["js"] ) || !file_exists( $DOCUMENT_ROOT.$arJqueryExt["js"] ) ){
            $APPLICATION->ThrowException( GetMessage( "ACRIT_EXPORTPRO_JQUERY_REQUIRE" ) );
            return false;
        }
        unset( $arJqueryExt );

        $phpVersion = explode( ".", phpversion() );
        if( ( intval( $phpVersion[0] ) < 5 ) || ( ( intval( $phpVersion[0] ) == 5 ) && ( intval( $phpVersion[1] ) < 4 ) ) ){
            $APPLICATION->ThrowException( GetMessage( "ACRIT_EXPORTPRO_PHP_REQUIRE" ) );
            return false;
        }
        unset( $phpVersion );

        $licenceDB = $DB->Query( "SELECT * FROM b_option WHERE `MODULE_ID`='{$this->MODULE_ID}' AND `NAME`='~bsm_stop_date'" );
        if( $licenceDB->Fetch() ){
            $DB->Query( "DELETE FROM b_option WHERE `MODULE_ID`='{$this->MODULE_ID}' AND `NAME`='~bsm_stop_date'" );
        }

        if( !isset( $step ) || ( $step < 1 ) ){
            $APPLICATION->IncludeAdminFile( GetMessage( "ACRIT_EXPORTPRO_RECOMMENDED" ), $DOCUMENT_ROOT."/bitrix/modules/{$this->MODULE_ID}/install/step.php" );
        }
        elseif( ( $step == 3 ) && ( $install == "Y" ) ){
            $this->InstallFiles();
            $this->InstallDB();
            RegisterModule( self::MODULE_ID );
            $this->InstallEvents();
            $this->RegisterGadget();

            if( CModule::IncludeModule( self::MODULE_ID ) ){
                $bBitrixCloudMonitoring = CExportproInformer::UpdateBitrixCloudMonitoring( GetMessage( "ACRIT_EXPORTPRO_BITRIX_CLOUD_MONITORING_EMAIL" ) );
            }

            $urlRewriter = new CUrlRewriter();
            foreach( $this->siteArray as $siteID => $siteDir ){
                $urlRewriter->Add(array(
                    "SITE_ID" => $siteID,
                    "CONDITION" => "#^/acrit.exportpro/(.*)#",
                    "PATH" => "/acrit.exportpro/index.php",
                    "RULE" => "path=$1",
                ));
            }
            $APPLICATION->IncludeAdminFile( GetMessage( "MOD_INST_OK" ), $DOCUMENT_ROOT."/bitrix/modules/{$this->MODULE_ID}/install/step3.php" );
        }
        elseif( $step == 2 ){
            CheckDirPath( __DIR__."/db/category" );
            CopyDirFiles( __DIR__."/db/", __DIR__."/db/category" );
            $APPLICATION->IncludeAdminFile( GetMessage( "MOD_INST_OK" ), $DOCUMENT_ROOT."/bitrix/modules/{$this->MODULE_ID}/install/step2.php" );
        }
    }

    function DoUninstall(){
        global $DOCUMENT_ROOT, $APPLICATION, $DB, $DBType;
        if( $APPLICATION->GetGroupRight( "main" ) < "W" ){
            return;
        }

        if( $_REQUEST["step"] < 2 ){
            global $moduleID;
            $moduleID = $this->MODULE_ID;
            $APPLICATION->IncludeAdminFile( GetMessage( "MOD_UNINST_OK" ), $DOCUMENT_ROOT."/bitrix/modules/{$this->MODULE_ID}/install/unstep.php" );
        }
        elseif( $_REQUEST["step"] == 2 ){
            global $APPLICATION;
            $this->UnRegisterGadget();
            $this->UnInstallEvents();
            UnRegisterModule( self::MODULE_ID );
            $this->UnInstallFiles();
            if( $_REQUEST["savedata"] != "Y" ){
                $this->UnInstallDB();
            }
            $DB->Query( "DELETE FROM b_option WHERE `MODULE_ID`='{$this->MODULE_ID}' AND `NAME`='~bsm_stop_date'" );
            $APPLICATION->IncludeAdminFile( GetMessage( "MOD_UNINST_OK" ), $DOCUMENT_ROOT."/bitrix/modules/{$this->MODULE_ID}/install/unstep2.php" );
        }
    }

    function InstallFiles( $arParams = array() ){
        global $DB, $DBType, $APPLICATION;
        if( is_dir( $p = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/admin" ) ){
            if( $dir = opendir( $p ) ){
                while( false !== ( $item = readdir( $dir ) ) ){
                    if( ( $item == ".." ) || ( $item == "." ) || ( $item == "menu.php" ) ){
                        continue;
                    }
                    file_put_contents( $file = $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/acrit_exportpro_".$item,
                        "<".'? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/'.self::MODULE_ID."/admin/".$item.'" );?'.">" );
                }
                closedir( $dir );
            }
        }
        if( $_ENV["COMPUTERNAME"] != "BX" ){
            CopyDirFiles( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/", true, true );
            CopyDirFiles( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js", true, true );
            CopyDirFiles( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true );
            CopyDirFiles( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/gadgets", $_SERVER["DOCUMENT_ROOT"]."/bitrix/gadgets", true, true );
            CopyDirFiles( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/tools", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools", true, true );
            foreach( $this->siteArray as $sireDir ){
                CopyDirFiles( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/public", $sireDir, true, true );
            }
        }
        return true;
    }

    function UnInstallFiles(){
        global $DB, $DBType, $APPLICATION;
        if( is_dir( $p = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/admin" ) ){
            if( $dir = opendir( $p ) ){
                while( false !== ( $item = readdir( $dir ) ) ){
                    if( ( $item == ".." ) || ( $item == "." ) ){
                        continue;
                    }
                    if( file_exists( $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/".self::MODULE_ID."_".$item ) ){
                        unlink( $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/".self::MODULE_ID."_".$item );
                    }
                }
                closedir( $dir );
            }
        }
        if( is_dir( $p = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::MODULE_ID."/install/components" ) ){
            if( $dir = opendir( $p ) ){
                while( false !== ( $item = readdir( $dir ) ) ){
                    if( ( $item == ".." ) || ( $item == "." ) || !is_dir( $p0 = $p."/".$item ) ){
                        continue;
                    }

                    $dir0 = opendir( $p0 );
                    while( false !== ( $item0 = readdir( $dir0 ) ) ){
                        if( ( $item0 == ".." ) || ( $item0 == "." ) ){
                            continue;
                        }

                        DeleteDirFilesEx( "/bitrix/components/".$item."/".$item0 );
                    }
                    closedir( $dir0 );
                }
                closedir( $dir );
            }
        }

        if( $_ENV["COMPUTERNAME"] != "BX" ){
            DeleteDirFilesEx( "/bitrix/js/acrit.exportpro/" );
            DeleteDirFilesEx( "/bitrix/gadgets/acrit/exportpro/" );
            DeleteDirFilesEx( "/bitrix/tools/acrit.exportpro/" );
            DeleteDirFilesEx( "/bitrix/themes/.default/acrit/exportpro/" );
            DeleteDirFilesEx( "/bitrix/themes/.default/acrit/exportpro/" );
            DeleteDirFilesEx( "/bitrix/themes/.default/acrit.exportpro.css" );
            DeleteDirFilesEx( "/upload/acrit_exportpro/" );

            DeleteDirFiles( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/" );
            foreach( $this->siteArray as $sireDir ){
                DeleteDirFiles( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/public", $sireDir );
            }
        }
        DeleteDirFilesEx( "/upload/exportpro_log" );
        DeleteDirFilesEx( "/".$this->MODULE_ID );
        return true;
    }

    function RegisterGadget(){
        $arModuleNameParts = explode( ".", self::MODULE_ID );

        $arAcritGadget = array(
            ToUpper( $arModuleNameParts[1] )."@".time() => array(
                "COLUMN" => 0,
                "ROW" => 0,
                "HIDE" => "N"
            )
        );

        $arOptions = CUserOptions::GetOption( "intranet", "~gadgets_admin_index" );
        if( is_array( $arOptions[0]["GADGETS"] ) && !empty( $arOptions[0]["GADGETS"] ) ){
            $arOptions[0]["GADGETS"] = array_merge( $arAcritGadget, $arOptions[0]["GADGETS"] );
        }
        else{
            $arOptions[0]["GADGETS"] = $arAcritGadget;
        }
        CUserOptions::SetOption( "intranet", "~gadgets_admin_index", $arOptions, false, false );
    }


    function UnRegisterGadget(){
        $arModuleNameParts = explode( ".", self::MODULE_ID );
        $moduleUpperId = ToUpper( $arModuleNameParts[1] );
        $arOptions = CUserOptions::GetOption( "intranet", "~gadgets_admin_index" );

        foreach( $arOptions[0]["GADGETS"] as $gadgetIndex => $arGadgetData ){
            if( stripos( $gadgetIndex, $moduleUpperId."@" ) !== false ){
                unset( $arOptions[0]["GADGETS"][$gadgetIndex] );
            }
        }

        CUserOptions::SetOption( "intranet", "~gadgets_admin_index", $arOptions, false, false );
    }
}