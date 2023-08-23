<?
header('HTTP/1.1 503 Service Temporarily Unavailable');
header('Status: 503 Service Temporarily Unavailable');
header('Retry-After: 300');//300 seconds
?>
<html>
<head>
	<title>Сайт временно закрыт на тех.обслуживание</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <link href="/bitrix/templates/dresscodeV2/template_styles.css" type="text/css" rel="stylesheet">
    <link href="/bitrix/templates/dresscodeV2/fonts/roboto/roboto.css" type="text/css" rel="stylesheet">

</head>
<body style="text-align: center;">
    <br><br>
    <img src="/bitrix/templates/dresscodeV2/headers/header8/images/logo.png?v=1580286314?v=1580286314" alt="">
<br><br>
	<h1 style="text-align: center;">Сайт временно закрыт на тех.обслуживание</h1>
<p>	В настоящий момент на сайте ведутся технические работы.
</p>
<p>Скоро все заработает, обязательно возвращайтесь!
</p>
</body>
</html>
