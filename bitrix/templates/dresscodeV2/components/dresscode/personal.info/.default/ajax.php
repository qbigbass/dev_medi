<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
	//langs
	\Bitrix\Main\Localization\Loc::loadMessages(dirname(__FILE__)."/ajax.php");
?>
<?
	if(isset($_GET["USER_PASSWORD"]) &&
	   isset($_GET["USER_PASSWORD_CONFIRM"]) &&
	   isset($_GET["USER_STREET"]) &&
	   isset($_GET["USER_MOBILE"]) &&
	   isset($_GET["USER_CITY"]) &&
	   isset($_GET["USER_ZIP"]) &&
	   isset($_GET["EMAIL"]) &&
	   isset($_GET["FIO"])
	){
		global $USER;
		$userID = $USER->GetID();
		if($userID){
			
			$NAME = str_replace("  ", " ",trim($_GET["FIO"]));
			$NAME            = explode(" ", $NAME);
			$EMAIL           = htmlspecialchars($_GET["EMAIL"]);
			$PASSWORD        = addslashes($_GET["USER_PASSWORD"]);
			$REPASSWORD      = addslashes($_GET["USER_PASSWORD_CONFIRM"]);
			$PERSONAL_STREET = htmlspecialchars($_GET["USER_STREET"]);
			$PERSONAL_MOBILE = htmlspecialchars($_GET["USER_MOBILE"]);
			$PERSONAL_CITY   = htmlspecialchars($_GET["USER_CITY"]);
			$PERSONAL_ZIP    = htmlspecialchars($_GET["USER_ZIP"]);

			$user = new CUser;
			$fields = Array(
			  "NAME"              => defined("BX_UTF") ? $NAME[1] : iconv("UTF-8","windows-1251//IGNORE", $NAME[1]),
			  "SECOND_NAME"       => defined("BX_UTF") ? $NAME[2] : iconv("UTF-8","windows-1251//IGNORE", $NAME[2]),
			  "LAST_NAME"         => defined("BX_UTF") ? $NAME[0] : iconv("UTF-8","windows-1251//IGNORE", $NAME[0]),
			  "PERSONAL_STREET"   => defined("BX_UTF") ? $PERSONAL_STREET : iconv("UTF-8","windows-1251//IGNORE", $PERSONAL_STREET),
			  "PERSONAL_CITY"	  => defined("BX_UTF") ? $PERSONAL_CITY : iconv("UTF-8","windows-1251//IGNORE", $PERSONAL_CITY),
			  "PERSONAL_ZIP"      => defined("BX_UTF") ? $PERSONAL_ZIP : iconv("UTF-8","windows-1251//IGNORE", $PERSONAL_ZIP),
			  "PERSONAL_MOBILE"   => defined("BX_UTF") ? $PERSONAL_MOBILE : iconv("UTF-8","windows-1251//IGNORE", $PERSONAL_MOBILE),
			  "EMAIL"             => $EMAIL,
			  "PASSWORD"          => $PASSWORD,
			  "CONFIRM_PASSWORD"  => $REPASSWORD
			);

			if(empty($PASSWORD)){
				unset($fields["PASSWORD"]);
				unset($fields["REPASSWORD"]);
			}

			if(!$user->Update($userID, $fields)){
				$result = array(
					"message" => strip_tags($user->LAST_ERROR),
					"heading" => GetMessage("PERSONAL_ERROR"),
					"reload" => false
				);
			}else{
				$result = array(
					"message" => GetMessage("PERSONAL_SUCCESS_SAVED"),
					"heading" => GetMessage("PERSONAL_SAVED"),
					"reload" => true
				);
			}
		}else{
			$result = array(
				"message" => GetMessage("PERSONAL_NEED_AUTH"),
				"heading" => GetMessage("PERSONAL_ERROR"),
				"reload" => false
			);
		}

	}else{
		$result = array(
			"message" => GetMessage("PERSONAL_SEND_ERROR"),
			"heading" => GetMessage("PERSONAL_ERROR"),
			"reload" => false
		);
	}

	echo \Bitrix\Main\Web\Json::encode($result);

?>