<?php
	session_start();

	if((!isset($_SESSION['zalogowanyadmin'])) || $_SESSION['zalogowanyadmin']==false)
	{
		header('Location: /admin');
		exit();
	}

	require_once "../functions/connect.php";

	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

	if($polaczenie->connect_errno!=0)
	{
		echo "Error: ".$polaczenie->connect_errno;
	}
	else
	{
		mysqli_set_charset($polaczenie,"utf8");
		$polaczenie->query('SET NAMES utf8');

		$id_admin = $_SESSION['id_admin'];
	}

?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<title>HERMES</title>
	<meta name="author" content="Bartosz Jaśkiewicz">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<link rel="stylesheet" type="text/css" href="/css/main.css">
	<link rel="shortcut icon" type="image/png" href="/favicon.png">
	<link rel="stylesheet" type="text/css" href="/css/fontello.css">
	<link rel="stylesheet" type="text/css" href="/css/modal.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
</head>
<body>
	<div id="container">
		<div id="top">
			HERMES
			<div style="font-size: 13px; line-height: 10px;">Panel Administratora</div>
		</div>
		<div id="center">
			<div id="topmenu">
				<div id="logged">
					Zalogowany administrator:<br/>
					<p class="grubiejwmenu"> > <?php echo $_SESSION['admin_name']; ?> </p>
				</div>
				<div id="whatcontest">
					<?php
						if(isset($_GET['edit_contest']))
						{
							$id_contest = $_GET['edit_contest'];
							$zapytanie = $polaczenie->query("SELECT shortcut_contest,title_contest,password, time_from, time_to, timer, visibility FROM contests WHERE id_contest='$id_contest'");

							if(mysqli_num_rows($zapytanie)==0)
							{
								header('Location: /admin');
							}

							$rezultat = $zapytanie->fetch_assoc();
							$shortcut_contest = $rezultat['shortcut_contest'];
							$password_contest = $rezultat['password'];
							$time_to = $rezultat['time_to'];
							$time_from = $rezultat['time_from'];
							$timer = $rezultat['timer'];
							$visibility = $rezultat['visibility'];
							echo "Wybrane zawody: <br/>";
							echo "<p class=\"grubiejwmenu\"> > ".$rezultat['title_contest']."</p>";
						}elseif(isset($_GET['edit_task']))
						{
							$id_task = $_GET['edit_task'];

							$zapytanie = $polaczenie->query("SELECT * FROM tasks WHERE id_task='$id_task'");

							if(mysqli_num_rows($zapytanie)==0)
							{
								header('Location: /admin');
							}

							$rezultat = $zapytanie->fetch_assoc();
							$title_task = $rezultat['title_task'];
							$difficulty = $rezultat['difficulty'];
							$if_pdf = $rezultat['pdf'];
							$sum_of_points = $rezultat['sum'];
							echo "ID wybranego zadania: <br/>";
							echo "<p class=\"grubiejwmenu\"> > ".$id_task."</p>";
						}elseif(isset($_GET['user']))
						{
							$id_user = $_GET['user'];

							$zapytanie = $polaczenie->query("SELECT login, name, email FROM users WHERE id_user='$id_user'");

							if(mysqli_num_rows($zapytanie)==0)
							{
								header('Location: /admin');
							}

							$rezultat = $zapytanie->fetch_assoc();
							$login_user = $rezultat['login'];
							$name_user = $rezultat['name'];
							$email_user = $rezultat['email'];
							echo "Login wybranego użytkownika: <br/>";
							echo "<p class=\"grubiejwmenu\"> > ".$login_user."</p>";
						}
					?>
				</div>
				<div style="float: right; width: 40px; height: 45px; margin-top: 5px; margin-right: 20px; color: black; text-decoration: none;">
					<a href="functions/logout.php"><i class="icon-logout"></i></a>
				</div>
				<div style="clear: both;"></div>
			</div>
			<div style="height: 50px;"></div>
			<div id="menu">

				<p style="margin-top: 0;"><a href="?tool=create_contest">Utwórz Zawody</a>
					<?php if(isset($_GET['tool']) && $_GET['tool']=="create_contest") echo ' &bull;';?></p>
				<p><a href="?tool=list_contest">Zawody</a>
					<?php if(isset($_GET['tool']) && $_GET['tool']=="list_contest") echo ' &bull;';?></p>
				<p><a href="?tool=add_task">Dodaj zadanie</a>
					<?php if(isset($_GET['tool']) && $_GET['tool']=="add_task") echo ' &bull;';?></p>
				<p><a href="?tool=list_task">Zadania</a>
					<?php if(isset($_GET['tool']) && $_GET['tool']=="list_task") echo ' &bull;';?></p>
				<p><a href="?tool=list_users">Użytkownicy</a>
					<?php if(isset($_GET['tool']) && $_GET['tool']=="list_users") echo ' &bull;';?></p>
				<p><a href="?tool=manage_admin">Administatorzy</a>
					<?php if(isset($_GET['tool']) && $_GET['tool']=="manage_admin") echo ' &bull;';?></p>

			</div>
			<div id="content">
				<?php

					if(isset($_GET['tool']))
					{
						$tool = $_SERVER['DOCUMENT_ROOT'].'/admin/tools/'.$_GET['tool'].".php";
						include($tool);

					}elseif(isset($_GET["brak"]))
					{
						echo "Ta funkcja jest aktualnie wdrażana.";

					}else
					{
						echo    "Wybierz narzędzie z panelu po lewej. <br/>";
					}

				?>
			</div>

		</div>
		<div style="clear: both;"></div>
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

<?php $polaczenie->close(); ?>
