<?php
/**
 * @copyright Copyright &copy; �������� MEAsoft, 2014
 */

class measoft_courier extends CModule
{
    public $MODULE_ID = "measoft.courier";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    public $MODULE_GROUP_RIGHTS = "N";

    function getMessage($name, $aReplace = false)
    {
        return GetMessage($name, $aReplace);
    }

    function __construct()
    {
        $arModuleVersion = array();
        include("version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        if ($this->MODULE_VERSION === "1.8.24") {

            $options = ["measoft_check_date_format" => 'N', "measoft_check_date_weekend" => 'N', "measoft_check_fill_deliverydate" => 'N', 'measoft_sync_disable' => 'N',
                "measoft_check_fill_deliverydate_hour" => '', 'measoft_sync_order_cnt' => 30, 'ADD_DELIVERTY_DAYES_COUNT' => ''];

            $mEvents = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/measoft.courier/MeasoftEvents.php';

            if (file_exists($mEvents)) {

                $lines = file($mEvents);

                $cnt = 0;
                foreach ($lines as $line) {
                    $cnt++;
                    if ($cnt >= 20) break;

                    foreach ($options as $optionId => $optionDefValue) {

                        if (strpos($line, $optionId . '"') !== false || strpos($line, $optionId . "'") !== false) {
                            if (strpos($line, '/') === false && strpos($line, '#') === false) {
                                if (in_array($optionId, ["measoft_check_date_format", "measoft_check_date_weekend", "measoft_check_fill_deliverydate", 'measoft_sync_disable'])) {
                                    $optionDefValue = 'Y';
                                } else {
                                    $line = preg_replace("/\s+/", "", $line);
                                    $start = strpos($line, ',') + 1;
                                    $end = strpos($line, ';') - 1;
                                    $optionDefValue = (int)substr($line, $start, $end - $start);
                                }
                            }
                            COption::SetOptionString("measoft_courier", $optionId, $optionDefValue);

                        }

                    }
                }
            }
        }

        IncludeModuleLangFile(__FILE__);

        $this->PARTNER_NAME = 'Measoft';
        $this->PARTNER_URI = 'http://courierexe.ru/';

        $this->MODULE_NAME = $this->GetMessage('MEASOFT_MODULE_NAME');
        $this->MODULE_DESCRIPTION = $this->GetMessage('MEASOFT_MODULE_DESCRIPTION');
    }

    function DoInstall()
    {
        //����������� �������
        RegisterModuleDependences('sale', 'OnSaleDeliveryHandlersBuildList', $this->MODULE_ID, 'MeasoftEvents', 'Init');
        RegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepDelivery', $this->MODULE_ID, 'MeasoftEvents', 'OnSaleComponentOrderOneStepDelivery');
        RegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepFinal', $this->MODULE_ID, 'MeasoftEvents', 'OnSaleComponentOrderOneStepComplete');
        RegisterModuleDependences('sale', 'OnSaleStatusOrder', $this->MODULE_ID, 'MeasoftEvents', 'OnSaleStatusOrder');
        RegisterModuleDependences('sale', 'OnBeforeOrderAdd', $this->MODULE_ID, 'MeasoftEvents', 'OnBeforeOrderAdd');
        RegisterModuleDependences('sale', 'OnSaleComponentOrderProperties', $this->MODULE_ID, 'MeasoftEvents', 'setSettingsForCalculte');

        //����������� ������
        RegisterModule($this->MODULE_ID);

        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/measoft.courier/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);

        CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/measoft.courier/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/measoft.courier", true, true);


        $orderPropList = [
            [
                'CODE'=>                 'MEASOFT_DATE_PUTN',
                'NAME'=>                 $this->getMessage('MEASOFT_FIELDS_DATE_PUTN_ADMIN'),
                'TYPE'=>                 'TEXT',
                'SORT'=>                 '1000',
            ],

            [
                'CODE'=>                 'MEASOFT_TIME_MIN',
                'NAME'=>                 $this->getMessage('MEASOFT_FIELDS_TIME_MIN_ADMIN'),
                'TYPE'=>                 'TEXT',
                'SORT'=>                 '1100',
            ],

            [
                'CODE'=>                 'MEASOFT_TIME_MAX',
                'NAME'=>                 $this->getMessage('MEASOFT_FIELDS_TIME_MAX_ADMIN'),
                'TYPE'=>                 'TEXT',
                'SORT'=>                 '1200',
            ],
            [
                'CODE'=>                 'PVZ_CODE',
                'NAME'=>                 $this->getMessage('PVZ_CODE'),
                'TYPE'=>                 'TEXT',
                'SORT'=>                 '1300',
            ],
            [
                'CODE'=>                 'PVZ_ADDRESS',
                'NAME'=>                 $this->getMessage('PVZ_ADDRESS'),
                'TYPE'=>                 'TEXT',
                'SORT'=>                 '1400',
            ],
            [
                'CODE'=>                 'PVZ_PHONE',
                'NAME'=>                 $this->getMessage('PVZ_PHONE'),
                'TYPE'=>                 'TEXT',
                'SORT'=>                 '1500',
            ],
            [
                'CODE'=>                 'PVZ_WORKTIME',
                'NAME'=>                 $this->getMessage('PVZ_WORKTIME'),
                'TYPE'=>                 'TEXT',
                'SORT'=>                 '1600',
            ],
            [
                'CODE'=>                 'MEASOFT_ORDER_ERROR',
                'NAME'=>                 $this->getMessage('MEASOFT_ORDER_ERROR'),
                'TYPE'=>                 'TEXT',
                'SORT'=>                 '1700',
            ],

        ];

        // ��������� �������� � ��
        CModule::IncludeModule("sale");

        $typesRes = CSalePersonType::GetList(Array("SORT" => "ASC"), Array(/*"LID"=>SITE_ID*/));
        while ($typeArr = $typesRes->Fetch())
        {
            $PERSON_TYPE_ID = $typeArr["ID"];

            foreach($orderPropList as $propArr)
            {
                if (!CSaleOrderProps::GetList(array(), array('CODE' => $propArr['CODE'], 'PERSON_TYPE_ID' => $PERSON_TYPE_ID ))->Fetch()) {
                     CSaleOrderProps::Add(array(
                        'CODE'=>                 $propArr['CODE'],
                        'NAME'=>                 $propArr['NAME'],
                        'TYPE'=>                 'TEXT',
                        'REQUIED'=>              'N',
                        'PROPS_GROUP_ID'=>       '2',
                        'DEFAULT_VALUE'=>        '',
                        'PERSON_TYPE_ID'=>       $PERSON_TYPE_ID,
                        'SORT'=>                 $propArr['SORT'],
                        'USER_PROPS'=>           'N',
                        'IS_LOCATION'=>          'N',
                        'IS_EMAIL'=>             'N',
                        'IS_PROFILE_NAME'=>      'N',
                        'IS_PAYER'=>             'N',
                        'IS_LOCATION4TAX'=>      'N',
                        'IS_ZIP'=>               'N',
                        'IS_FILTERED'=>          'N',
                        'ACTIVE'=>               'Y',
                        'UTIL'=>                 'Y',
                        'INPUT_FIELD_LOCATION'=> '0',
                    ));
                }
            }
        }

        $sql = 'create table if not exists measoft_cities
(
	ID int(5) NOT NULL auto_increment,
	BITRIX_ID varchar(7),
	MEASOFT_ID int(5),
	NAME varchar(50),
	PRIMARY KEY(ID)
);';


        global $DB;
        $results = $DB->Query($sql);

        $sql = 'create table if not exists measoft_pay_system
(
	ID int(5) NOT NULL auto_increment,
	BITRIX_ID varchar(7),
	PAYSYSTEM_ID int(5),
	CASH int(1),
	CARD int(1),
	PRIMARY KEY(ID)
);';

        $results = $DB->Query($sql);

        $sql = 'create table if not exists measoft_order_status
(
	ID int(5) NOT NULL auto_increment,
	BITRIX_ID varchar(7),
	MEASOFT_STATUS_CODE varchar(20),
	BITRIX_STATUS_ID  varchar(7),
	PRIMARY KEY(ID)
);';

        $results = $DB->Query($sql);

        CAgent::AddAgent("MeasoftEvents::ordersSyncAgent();","measoft.courier","N",600);//обновление статусов заказов

    }

    function DoUninstall()
    {
        // �������������� �������
        UnRegisterModuleDependences('sale', 'OnSaleDeliveryHandlersBuildList', $this->MODULE_ID, 'MeasoftEvents', 'Init');
        UnRegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepDelivery', $this->MODULE_ID, 'MeasoftEvents', 'OnSaleComponentOrderOneStepDelivery');
        UnRegisterModuleDependences('sale', 'OnSaleComponentOrderOneStepFinal', $this->MODULE_ID, 'MeasoftEvents', 'OnSaleComponentOrderOneStepComplete');
        UnRegisterModuleDependences('sale', 'OnSaleStatusOrder', $this->MODULE_ID, 'MeasoftEvents', 'OnSaleStatusOrder');
        UnRegisterModuleDependences('sale', 'OnBeforeOrderAdd', $this->MODULE_ID, 'MeasoftEvents', 'OnBeforeOrderAdd');
        UnRegisterModuleDependences('sale', 'OnSaleComponentOrderProperties', $this->MODULE_ID, 'MeasoftEvents', 'setSettingsForCalculte');

        // �������� ������� ������ �� ��
        CModule::IncludeModule("sale");
        if ($prop = CSaleOrderProps::GetList(array(), array('CODE' => 'MEASOFT_DATE_PUTN'))->Fetch()) {
            CSaleOrderProps::Delete($prop['ID']);
        }
        if ($prop = CSaleOrderProps::GetList(array(), array('CODE' => 'MEASOFT_TIME_MIN'))->Fetch()) {
            CSaleOrderProps::Delete($prop['ID']);
        }
        if ($prop = CSaleOrderProps::GetList(array(), array('CODE' => 'MEASOFT_TIME_MAX'))->Fetch()) {
            CSaleOrderProps::Delete($prop['ID']);
        }
        if ($prop = CSaleOrderProps::GetList(array(), array('CODE' => 'PVZ_CODE'))->Fetch()) {
            CSaleOrderProps::Delete($prop['ID']);
        }
        if ($prop = CSaleOrderProps::GetList(array(), array('CODE' => 'PVZ_ADDRESS'))->Fetch()) {
            CSaleOrderProps::Delete($prop['ID']);
        }
        if ($prop = CSaleOrderProps::GetList(array(), array('CODE' => 'PVZ_PHONE'))->Fetch()) {
            CSaleOrderProps::Delete($prop['ID']);
        }
        if ($prop = CSaleOrderProps::GetList(array(), array('CODE' => 'PVZ_WORKTIME'))->Fetch()) {
            CSaleOrderProps::Delete($prop['ID']);
        }
        if ($prop = CSaleOrderProps::GetList(array(), array('CODE' => 'MEASOFT_ORDER_ERROR'))->Fetch()) {
            CSaleOrderProps::Delete($prop['ID']);
        }
        // �������������� ������
        UnRegisterModule($this->MODULE_ID);

        // �������� ������������� � ���������� ������ ��� ���������
        if (is_dir($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/measoft.courier/install/components/measoft.courier")) {
            DeleteDirFilesEx("/bitrix/components/measoft.courier");
        }
        if (is_dir($_SERVER["DOCUMENT_ROOT"]."/bitrix/js/measoft.courier")) {
            DeleteDirFilesEx("/bitrix/js/measoft.courier");
        }
    }
}
?>