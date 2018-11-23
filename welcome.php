<?php
	session_start();

	if(!(isset($_SESSION['udanarejestracja'])))
	{
		header('Location: /');
		exit();
	}
	else
	{
		unset($_SESSION['udanarejestracja']);
	}

	//Usuwanie błędów rejestracji
	if (isset($_SESSION['e_login'])) unset($_SESSION['e_login']);
	if (isset($_SESSION['e_nazwa'])) unset($_SESSION['e_nazwa']);
	if (isset($_SESSION['e_haslo'])) unset($_SESSION['e_haslo']);
	if (isset($_SESSION['e_haslo2'])) unset($_SESSION['e_haslo2']);
	if (isset($_SESSION['e_regulamin'])) unset($_SESSION['e_regulamin']);

?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<title>HERMES</title>
	<meta name="author" content="Bartosz Jaśkiewicz">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<link rel="stylesheet" type="text/css" href="main.css">
	<link rel="shortcut icon" type="image/png" href="/favicon.png"/>
</head>
<body>
	<div id="container">
		<div id="top">
			HERMES v0,5<br/>
			<div style="font-size: 13px; line-height: 10px;">Sprawdzaczka Twoich rozwiązań</div>
		</div>
		<div id="center">
			<div style="height: 100px;"></div>
			<div style="width: 50%; text-align: left; margin: auto;">
				<b>Dziękujemy za rejestrację!</b><br/>
				Możesz już się zalogować.<br/><br/>
				
				<a href="/">Zaloguj się</a>
			</div>
		</div>
		<div id="footer">
			<table>
				<th style="text-align: left; padding-left: 20px;">
					I Liceum Ogólnokształcące w Legnicy
				</th>
				<th style="text-align: right; padding-right: 30px;">
					&copy; Wszelkie prawa zastrzeżone
				</th>
			</table>
		</div>
	</div>
</body>
</html>