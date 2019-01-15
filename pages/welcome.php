<?php
	session_start();

	if(!(isset($_SESSION['udanarejestracja'])))
	{
		header('Location: /');
		exit();
	}
	else
	{
		//unset($_SESSION['udanarejestracja']);
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
	<?php
		include_once ('../templates/head.php');
	?>
</head>
<body>
	<div id="container">
		<?php
			include_once ('../templates/top.php');
		?>
		<div id="center">
			<div style="height: 100px;"></div>
			<div style="width: 50%; text-align: left; margin: auto;">
				<b>Dziękujemy za rejestrację!</b><br/>
				Możesz już się zalogować.<br/><br/>
				
				<a href="/">Zaloguj się</a>
			</div>
		</div>
		<?php
			include_once ('../templates/footer.php');
		?>
	</div>
</body>
</html>