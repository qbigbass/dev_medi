<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?

	global $USER;
    unset($_SESSION['lmx']);
    unset($_SESSION['lmxapp']);

    setcookie('lmx[token]', '', time(), '/');
    setcookie('lmx[phone]', '', time(), '/');
 


	$USER->Logout();
	header("location:/");
?>