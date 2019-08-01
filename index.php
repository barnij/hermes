<?php
	session_start();

	if(isset($_SESSION['zalogowany']) && $_SESSION['zalogowany']==true)
	{
		header('Location: /contest');
		exit();
	}
?>

<!DOCTYPE HTML>
<html lang="pl">
<?php
	include_once ('templates/head.php');
?>
<body>
	<div id="container">
		<?php
			include_once ('templates/top.php');
		?>
		<div id="center">
			<div style="height: 100px;"></div>
			<div id="logowanie">
				<p><a href="/signup">Zarejestruj się już teraz</a></p>

				<?php
					if(isset($_SESSION['blad']))
					{
						echo $_SESSION['blad'].'<br/><br/>';
						unset($_SESSION['blad']);
					}
				?>

				<form action="/functions/login.php" method="post">
					Login: <input type="text" name="login"><br/><br/>
					Hasło: <input type="password" name="password"><br/><br/>
					<input type="submit" value="Zaloguj się">
				</form>
			</div>
		</div>
		<div style="clear: both;"></div>
		<?php
			include_once ('templates/footer.php');
		?>
	</div>
</body>
</html>