<?php
	session_start();

	if((!(isset($_GET['submit']))) || ($_GET['submit']==0) || (!(isset($_SESSION['zalogowany'])))  || (isset($_SESSION['zalogowany'])==false))
	{
		header('Location: /');

		exit();
	}

	require_once('connect.php');

	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

	if($polaczenie->connect_errno!=0)
	{
		echo "Error: ".$polaczenie->connect_errno;
	}
	else
	{
		mysqli_set_charset($polaczenie,"utf8");
		$polaczenie->query('SET NAMES utf8');

		$id_user = $_SESSION['id_user'];
		$id_submit = $_GET['submit'];

		echo '<!DOCTYPE html>
		<html>
			<head>
				<title>'.$id_submit.' - HERMES</title>
				<link rel="shortcut icon" type="image/png" href="/favicon.png"/>
			</head>
			<body>';

		$zapytanie=$polaczenie->query("SELECT id_submit FROM submits WHERE id_submit='$id_submit' AND id_user='$id_user'");

		if(mysqli_num_rows($zapytanie)!=0)
		{
			$adres = 'C:\xampp\htdocs\submits\\'.$id_submit;

			$czyistnieje = true;

			if(file_exists($adres.'.cpp'))
				$adres=$adres.'.cpp';
			else if(file_exists($adres.'.py'))
				$adres=$adres.'.py';
			else if(file_exists($adres.'.mrram'))
				$adres=$adres.'.mrram';
			else if(file_exists($adres.'.bap'))
				$adres=$adres.'.bap';
			else
			{
				echo "Błąd otwarcia pliku. Skontaktuj się z administratorem.";
				$czyistnieje = false;
			}

			if($czyistnieje)
			{
				$plik = file($adres);
				$ile = count($plik);
				
				for($i=0;$i<$ile;$i++)
				{
					$plik[$i] = htmlentities($plik[$i], ENT_QUOTES, "UTF-8");
					$plik[$i] = preg_replace('#\r\n?#', "\n", $plik[$i]);
					$plik[$i] = str_replace(' ', '&nbsp;', $plik[$i]);
					echo $plik[$i];
					echo '<br>';
				}
			}

		}
		else
		{
			header('Location: /');
		}

		echo '</body>
		</html>';
	}

	$polaczenie->close();
?>