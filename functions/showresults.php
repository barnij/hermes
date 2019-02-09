<?php
	session_start();

	if((!(isset($_GET['result']))) || ($_GET['result']==0) || ((!(isset($_SESSION['zalogowany'])))  || (isset($_SESSION['zalogowany'])==false)) && (!isset($_SESSION['zalogowanyadmin'])) )
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

		$id_submit = $_GET['result'];

		$zapytanie = $polaczenie->query("SELECT contests.showresults AS showresults FROM contests,submits WHERE submits.id_contest=contests.id_contest AND submits.id_submit='$id_submit'");
		$rezultat = $zapytanie->fetch_assoc();

		echo '<!DOCTYPE html>
		<html>
			<head>
				<title>'.$id_submit.' - HERMES</title>
				<link rel="shortcut icon" type="image/png" href="/favicon.png"/>
			</head>
			<body>';

		if($rezultat['showresults']=='1')
		{
			$adres = 'C:\xampp\htdocs\results\\'.$id_submit.'.txt';
			$plik = file($adres);
			$ile = count($plik);
			$plik = preg_replace('#\r\n?#', "\n", $plik);
			for($i=0;$i<$ile;$i++)
			{
				$plik[$i] = str_replace(' ', '&nbsp;', $plik[$i]);
				echo $plik[$i];
				echo '<br>';
			}
		}else
		{
			echo "Wyniki są ukryte.";
		}

		echo '</body>
		</html>';
	}

	$polaczenie->close();
?>

