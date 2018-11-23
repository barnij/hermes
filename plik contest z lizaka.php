<?php
	session_start();

	if(!isset($_SESSION['zalogowany']))
	{
		header('Location: /index.php');
		exit();
	}

	require_once "functions/connect.php";

	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

	if($polaczenie->connect_errno!=0)
	{
		echo "Error: ".$polaczenie->connect_errno;
	}
	else
	{
		mysqli_set_charset($polaczenie,"utf8");
		$polaczenie->query('SET NAMES utf8');

		$zapytanie1 = $polaczenie->query("SELECT id_task FROM tasks");

		$id_user = $_SESSION['id_user'];
	}

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
			HERMES v0,5
			<div style="font-size: 13px; line-height: 10px;">Sprawdzaczka Twoich rozwiązań</div>
		</div>
		<div id="center">
			<div style="height: 50px;"></div>
			<div id="menu">
				Zalogowany użytkownik:<br/>
				<p style="margin-top: 2px; margin-bottom: 5px; padding-left: 10px; font-weight: bold;"> > <?php echo $_SESSION['login']; ?></p>
				<br/>
				Zadania:
				<ul>
					<?php
						while($wiersz = mysqli_fetch_row($zapytanie1))
						{
							$czyrozwiazane = "";
							$zapytanie2 = $polaczenie->query("SELECT if_correct FROM answers WHERE id_user = '$id_user' AND id_task = '$wiersz[0]' AND if_correct = 2");

							if(mysqli_num_rows($zapytanie2) > 0)
							{
								$czyrozwiazane = " &#9786; ";
							}
							

							echo " <li> <a href=\"?task=$wiersz[0]\">Zadanie $wiersz[0]</a>".$czyrozwiazane."</li>";
						}
					?>
				</ul>
				<p style="margin-top: 25px;"><a href="?answers">Wszystkie zgłoszenia</a></p>

				[ <a href="/functions/logout.php">WYLOGUJ</a> ]
			</div>
			<div id="content">
				<?php
					
					if(isset($_GET["task"]))
					{
						$id_task = $_GET["task"];
						$zapytanie1 = $polaczenie->query("SELECT * FROM tasks WHERE id_task = '$id_task'");
						$zapytanie2 = $polaczenie->query("SELECT if_correct FROM answers WHERE id_task = '$id_task' AND id_user = '$id_user' AND if_correct = 2");

						if(mysqli_num_rows($zapytanie1) > 0)
						{
							$rezultat = $zapytanie1->fetch_assoc();
							$name_task = $rezultat['name_task'];
							$text = $rezultat['text'];

							echo " 	<h1>Zadanie $id_task";



							echo   "</h1>
								   	<p id=\"TrescZadania\">$text</p>";

							if(mysqli_num_rows($zapytanie2) > 0)
								echo " <span style=\"font-size: 20px; color: green; font-style: italic;\">Brawo! Udało Ci się rozwiązać to zadanie.</span>";
							else
							{
								echo " 	Prześlij rozwiązanie:<br/>
									   	<textarea rows=\"4\" cols=\"50\" name=\"TrescKodu\" form=\"SendAnswer\"></textarea>

									    <form method=\"post\" id=\"SendAnswer\">
									    	<input type=\"submit\" value=\"Wyślij\">
									    </form>";
								
								if(isset($_POST['TrescKodu']))
								{
									$TrescKodu = str_replace(" ", "", $_POST['TrescKodu']);
									$TrescKoduDoBazy = htmlentities($TrescKodu, ENT_QUOTES, "UTF-8");
									$polaczenie->query("INSERT INTO answers VALUES (NULL,'$id_user','$id_task',NULL,0,1,'$TrescKoduDoBazy')");
									$zapytanie1 = $polaczenie->query("SELECT id_ans FROM answers WHERE id_user = '$id_user' ORDER BY id_ans DESC LIMIT 1");
									$rezultat = $zapytanie1->fetch_assoc();
									$id_ans = $rezultat['id_ans'];
									
									$polecenie1 = 'C:\xampp\htdocs\lizak\tester.exe '.$id_task.' '.$id_user.' '.$id_ans.' "'.$TrescKodu.'"';
									$polecenie2 = 'C:\xampp\htdocs\lizak\ileznakow.exe "'.$TrescKodu.'"';
									$poprawnosc = shell_exec($polecenie1); //uruchamianie testerki
									$ileznakow = shell_exec($polecenie2); //sprawdzenie ilosci znakow

									$polaczenie->query("UPDATE answers SET if_correct = '$poprawnosc', words = '$ileznakow' WHERE id_ans = '$id_ans'");

									if($poprawnosc == 2)
									{
										$polaczenie->query("UPDATE ranking SET all_ans = all_ans + 1, ok_ans = ok_ans + 1, words = words + '$ileznakow' WHERE id_user = '$id_user'");
									}else
									{
										$polaczenie->query("UPDATE ranking SET all_ans = all_ans + 1 WHERE id_user = '$id_user'");
									}

									header("Refresh:0");

								}
							}
						}
						else
						{
							echo "Nie ma takiego zadania.";
						}
						
					}elseif(isset($_GET["answers"]))
					{
						include('all_answers.php');

					}elseif(isset($_GET["kod"]))
					{
						include('showcode.php');
					}
					else
					{
						echo "Wybierz zadanie z listy po lewej stronie";
					}

				?>
			</div>
			
		</div>
		<div style="clear: both;"></div>
		<div id="footer">
			<table>
				<th style="text-align: left; padding-left: 20px;">
					Autor: Bartosz Jaśkiewicz
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