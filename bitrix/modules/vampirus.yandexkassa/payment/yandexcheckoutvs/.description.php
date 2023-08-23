<?php
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$description = array(
	'RETURN'      => Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_RETURN'),
	'RESTRICTION' => Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_RESTRICTION'),
	'COMMISSION'  => Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_COMMISSION'),
	'MAIN'        => Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_DESCRIPTION'),
);

if (IsModuleInstalled('bitrix24')) {
	$description['REFERRER'] = Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT_REFERRER');
}

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$host    = $request->isHttps() ? 'https' : 'http';

$data = array(
	'NAME'  => Loc::getMessage('VAMPIRUS.YANDEXKASSA_CHECKOUT'),
	'SORT'  => 500,
	'CODES' => array(
		'ORDER_NUMBER'                             => array(
			'NAME'    => Loc::getMessage('VAMPIRUS.YANDEXKASSA_OPTIONS_ORDER_NUMBER'),
			'SORT'    => 750,
			//'GROUP' => Loc::getMessage('VAMPIRUS.YANDEXKASSA_PAYMENT_SETTINGS'),
			'DEFAULT' => array(
				'PROVIDER_VALUE' => 'ACCOUNT_NUMBER',
				'PROVIDER_KEY'   => 'ORDER',
			),
		),
		"YANDEX_CHECKOUT_DESCRIPTION"              => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_PAYMENT_DESCRIPTION"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_PAYMENT_DESCRIPTION_DESC"),
			'SORT'        => 250,
			'GROUP'       => 'CONNECT_SETTINGS_YANDEX',
			'DEFAULT'     => array(
				'PROVIDER_KEY'   => 'VALUE',
				'PROVIDER_VALUE' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_PAYMENT_DESCRIPTION_TEMPLATE"),
			),
		),
		'BUYER_PERSON_EMAIL'                       => array(
			'NAME'    => Loc::getMessage('VAMPIRUS.YANDEXKASSA_OPTIONS_EMAIL_USER'),
			'SORT'    => 1100,
			//'GROUP' => 'BUYER_PERSON',
			'DEFAULT' => array(
				'PROVIDER_VALUE' => 'EMAIL',
				'PROVIDER_KEY'   => 'USER',
			),
		),
		'BUYER_PERSON_INN'                         => array(
			'NAME'        => Loc::getMessage('VAMPIRUS.YANDEXKASSA_OPTIONS_INN_USER'),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTIONS_INN_USER_DESCR"),
			'SORT'        => 1101,
			//'GROUP' => 'BUYER_PERSON',
		),
		'BUYER_PERSON_NAME'                        => array(
			'NAME'    => Loc::getMessage('VAMPIRUS.YANDEXKASSA_OPTIONS_NAME_USER'),
			'SORT'    => 1102,
			//'GROUP' => 'BUYER_PERSON',
			'DEFAULT' => array(
				'PROVIDER_VALUE' => 'WORK_COMPANY',
				'PROVIDER_KEY'   => 'USER',
			),
		),
		"YANDEX_CHECKOUT_SHOP_ID"                  => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_SHOP_ID"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_SHOP_ID_DESC"),
			'SORT'        => 100,
			'GROUP'       => 'CONNECT_SETTINGS_YANDEX',
		),
		"YANDEX_CHECKOUT_SECRET_KEY"               => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_SECRET_KEY"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_SECRET_KEY_DESC"),
			'SORT'        => 200,
			'GROUP'       => 'CONNECT_SETTINGS_YANDEX',
		),
		"YANDEX_CHECKOUT_SHOP_ARTICLE_ID"          => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_SHOP_ARTICLE_ID"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_SHOP_ARTICLE_ID_DESC"),
			'SORT'        => 250,
			'GROUP'       => 'CONNECT_SETTINGS_YANDEX',
		),
		"YANDEX_CHECKOUT_RETURN_URL"               => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_RETURN_URL"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_RETURN_URL_DESC"),
			'SORT'        => 300,
			'GROUP'       => 'CONNECT_SETTINGS_YANDEX',
			'DEFAULT'     => array(
				'PROVIDER_KEY'   => 'VALUE',
				'PROVIDER_VALUE' => $host . '://' . $request->getHttpHost() . '/bitrix/tools/yandexcheckoutvs_return.php?id=#ID#',
			),
		),
		"CAN_CANCEL_PAYMENT"                       => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CAN_CANCEL_PAYMENT"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CAN_CANCEL_PAYMENT_DESC"),
			'SORT'        => 1005,
			'GROUP'       => "CONNECT_SETTINGS_YANDEX",
			"INPUT"       => array(
				'TYPE'    => 'ENUM',
				'OPTIONS' => array(
					'1' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_CAN_CANCEL_PAYMENT_NO"),
					'2' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_CAN_CANCEL_PAYMENT_YES"),
				),
			),
		),
		"SAVE_CARD"                                => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_SAVE_CARD"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_SAVE_CARD_DESC"),
			'SORT'        => 1006,
			'GROUP'       => "CONNECT_SETTINGS_YANDEX",
			"INPUT"       => array(
				'TYPE'    => 'ENUM',
				'OPTIONS' => array(
					'1' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_OPTION_SAVE_CARD_NO"),
					'2' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_OPTION_SAVE_CARD_YES"),
				),
			),
		),
		"YANDEX_CHECKOUT_FFD120"             => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_FFD120"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_FFD120_DESC"),
			'SORT'        => 390,
			'GROUP'       => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_GROUP_FISCALISATION"),
			"INPUT"       => array(
				'TYPE'    => 'ENUM',
				'OPTIONS' => array(
					'1' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_FFD120_NO"),
					'2' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_FFD120_YES"),
				),
			),
		),
		"YANDEX_CHECKOUT_FFD120_CODE_FORMAT"             => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_FFD120_CODE_FORMAT"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_FFD120_CODE_FORMAT_DESC"),
			'SORT'        => 390,
			'GROUP'       => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_GROUP_FISCALISATION"),
			"INPUT"       => array(
				'TYPE'    => 'ENUM',
				'OPTIONS' => array(
					'mark_code_raw' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_FFD120_MARK_CODE_RAW"),
					'unknown' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_FFD120_UNKNOWN"),
					'ean_8' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_FFD120_EAN_8"),
					'ean_13' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_FFD120_EAN_13"),
					'itf_14' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_FFD120_ITF_14"),
					'gs_10' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_FFD120_GS_10"),
					'gs_1m' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_FFD120_GS_1M"),
					'short' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_FFD120_SHORT"),
					'fur' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_FFD120_FUR"),
					'egais_20' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_FFD120_EGAIS_20"),
					'egais_30' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_FFD120_EGAIS_30"),
				),
			),
		),
		"YANDEX_CHECKOUT_SNO"                      => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_SNO"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_SNO_DESC"),
			'SORT'        => 400,
			'GROUP'       => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_GROUP_FISCALISATION"),
			"INPUT"       => array(
				'TYPE'    => 'ENUM',
				'OPTIONS' => array(
					'1' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SNO_OSN"),
					'2' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SNO_USN_INCOME"),
					'3' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SNO_USN_INCOME_OUTCOME"),
					'4' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SNO_ENVD"),
					'5' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SNO_ESN"),
					'6' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SNO_PATENT"),
				),
			),
		),
		"YANDEX_CHECKOUT_PRODUCT_NDS"              => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_PRODUCT_NDS"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_PRODUCT_NDS_DESC"),
			'SORT'        => 500,
			'GROUP'       => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_GROUP_FISCALISATION"),
			"INPUT"       => array(
				'TYPE'    => 'ENUM',
				'OPTIONS' => array(
					'0' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_NDS_CATALOG"),
					'1' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_NDS_NONE"),
					'2' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_NDS_VAT0"),
					'3' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_NDS_VAT10"),
					'4' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_NDS_VAT20"),
					'5' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_NDS_VAT110"),
					'6' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_NDS_VAT120"),
				),
			),
		),
		"YANDEX_CHECKOUT_DELIVERY_NDS"             => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_DELIVERY_NDS"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_DELIVERY_NDS_DESC"),
			'SORT'        => 600,
			'GROUP'       => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_GROUP_FISCALISATION"),
			"INPUT"       => array(
				'TYPE'    => 'ENUM',
				'OPTIONS' => array(
					'0' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_NDS_SETTINGS"),
					'1' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_NDS_NONE"),
					'2' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_NDS_VAT0"),
					'3' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_NDS_VAT10"),
					'4' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_NDS_VAT20"),
					'5' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_NDS_VAT110"),
					'6' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_NDS_VAT120"),
				),
			),
		),
		"YANDEX_CHECKOUT_PAYMENT_MODE"             => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_PAYMENT_MODE"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_PAYMENT_MODE_DESC"),
			'SORT'        => 700,
			'GROUP'       => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_GROUP_FISCALISATION"),
			"INPUT"       => array(
				'TYPE'    => 'ENUM',
				'OPTIONS' => array(
					'full_prepayment'    => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_MODE_FULL_PREPAYMENT"),
					'partial_prepayment' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_MODE_PREPAYMENT"),
					'advance'            => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_MODE_ADVANCE"),
					'full_payment'       => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_MODE_FULL_PAYMENT"),
					'partial_payment'    => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_MODE_PARTIAL_PAYMENT"),
					'credit'             => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_MODE_CREDIT"),
					'credit_payment'     => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_MODE_CREDIT_PAYMENT"),
				),
			),
		),
		"YANDEX_CHECKOUT_PAYMENT_SUBJECT"          => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_PAYMENT_SUBJECT"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_PAYMENT_SUBJECT_DESC"),
			'SORT'        => 800,
			'GROUP'       => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_GROUP_FISCALISATION"),
			"INPUT"       => array(
				'TYPE'    => 'ENUM',
				'OPTIONS' => array(
					'commodity'             => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_COMMODITY"),
					'excise'                => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_USN_EXCISE"),
					'job'                   => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_USN_INCOME_JOB"),
					'service'               => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_SERVICE"),
					'gambling_bet'          => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_GAMBLING_BET"),
					'gambling_prize'        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_GAMBLING_PRIZE"),
					'lottery'               => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_LOTTERY"),
					'lottery_prize'         => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_LOTTERY_PRIZE"),
					'intellectual_activity' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_INTELLECTUAL_ACTIVITY"),
					'payment'               => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_PAYMENT"),
					'agent_commission'      => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_AGENT_COMMISSION"),
					'composite'             => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_COMPOSITE"),
					'another'               => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_ANOTHER"),
				),
			),
		),
		"YANDEX_CHECKOUT_PAYMENT_SUBJECT_DELIVERY" => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_PAYMENT_SUBJECT_DELIVERY"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_PAYMENT_SUBJECT_DELIVERY_DESC"),
			'SORT'        => 900,
			'GROUP'       => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_GROUP_FISCALISATION"),
			"INPUT"       => array(
				'TYPE'    => 'ENUM',
				'OPTIONS' => array(
					'commodity'             => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_COMMODITY"),
					'excise'                => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_USN_EXCISE"),
					'job'                   => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_USN_INCOME_JOB"),
					'service'               => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_SERVICE"),
					'gambling_bet'          => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_GAMBLING_BET"),
					'gambling_prize'        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_GAMBLING_PRIZE"),
					'lottery'               => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_LOTTERY"),
					'lottery_prize'         => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_LOTTERY_PRIZE"),
					'intellectual_activity' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_INTELLECTUAL_ACTIVITY"),
					'payment'               => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_PAYMENT"),
					'agent_commission'      => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_AGENT_COMMISSION"),
					'composite'             => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_COMPOSITE"),
					'another'               => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_SUBJECT_ANOTHER"),
				),
			),
		),

		"YANDEX_CHECKOUT_PAYMENT_AGENT_TYPE"       => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_PAYMENT_AGENT_TYPE"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_PAYMENT_AGENT_TYPE_DESC"),
			'SORT'        => 900,
			'GROUP'       => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_GROUP_FISCALISATION"),
			"INPUT"       => array(
				'TYPE'    => 'ENUM',
				'OPTIONS' => array(
					'banking_payment_agent'    => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_AGENT_TYPE_BANKING_PAYMENT_AGENT"),
					'banking_payment_subagent' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_AGENT_TYPE_BANKING_PAYMENT_SUBAGENT"),
					'payment_agent'            => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_AGENT_TYPE_PAYMENT_AGENT"),
					'payment_subagent'         => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_AGENT_TYPE_PAYMENT_SUBAGENT"),
					'attorney'                 => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_AGENT_TYPE_ATTORNEY"),
					'commissioner'             => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_AGENT_TYPE_COMMISSIONER"),
					'agent'                    => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_AGENT_TYPE_AGENT")),
			),
		),

		"YANDEX_CHECKOUT_PRINT_SECOND"             => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_PRINT_SECOND"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_PRINT_SECOND_DESC"),
			'SORT'        => 960,
			'GROUP'       => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_GROUP_FISCALISATION"),
			"INPUT"       => array(
				'TYPE'    => 'ENUM',
				'OPTIONS' => array(
					'1' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_PRINT_SECOND_NO"),
					'2' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_PRINT_SECOND_YES"),
				),
			),
		),
		"YANDEX_CHECKOUT_PRINT_SECOND_ON_STATUS"   => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_PRINT_SECOND_ON_STATUS"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_PRINT_SECOND_ON_STATUS_DESC"),
			'SORT'        => 970,
			'GROUP'       => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_GROUP_FISCALISATION"),
		),
		"YANDEX_CHECKOUT_HOLD"                     => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_HOLD"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_HOLD_DESC"),
			'SORT'        => 1000,
			'GROUP'       => 'CONNECT_SETTINGS_YANDEX',
			"INPUT"       => array(
				'TYPE'    => 'ENUM',
				'OPTIONS' => array(
					'2' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_HOLD_YES"),
					'1' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_HOLD_NO"),
				),
			),
		),

		"YANDEX_CHECKOUT_NO_DELIVERY"              => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_NO_DELIVERY"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_NO_DELIVERY_DESC"),
			'SORT'        => 1100,
			'GROUP'       => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_GROUP_DELIVERY"),
			"INPUT"       => array(
				'TYPE'    => 'ENUM',
				'OPTIONS' => array(
					'2' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_NO_DELIVERY_YES"),
					'1' => Loc::getMessage("VAMPIRUS.YANDEXKASSA_OPTION_NO_DELIVERY_NO"),
				),
			),
		),

		"YANDEX_CHECKOUT_SINGLE_DELIVERY_NAME"     => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_SINGLE_DELIVERY_NAME"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_SINGLE_DELIVERY_NAME_DESC"),
			'SORT'        => 1200,
			'GROUP'       => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_GROUP_DELIVERY"),
		),
		"YANDEX_CHECKOUT_WIDGET"     => array(
			"NAME"        => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_WIDGET"),
			"DESCRIPTION" => Loc::getMessage("VAMPIRUS.YANDEXKASSA_CHECKOUT_WIDGET_DESC"),
			'SORT'        => 1300,
			'INPUT'       => [
				'TYPE' => 'Y/N',
			],
			'DEFAULT'     => [
				'PROVIDER_KEY'   => 'INPUT',
				'PROVIDER_VALUE' => 'N',
			],
		),
	),
);
