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

		$zapytanie = $polaczenie->query("SELECT contests.showresults AS showresults, submits.id_task AS id_task FROM contests,submits WHERE submits.id_contest=contests.id_contest AND submits.id_submit='$id_submit'");
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
			$id_task = $rezultat['id_task'];
			$resultadres = $_SERVER['DOCUMENT_ROOT'].'/results/'.$id_submit.'.txt';
			$result = file($resultadres);
			$confadres = $_SERVER['DOCUMENT_ROOT'].'/tasks/'.$id_task.'/conf.txt';
			$conf = file($confadres);
			$ile = count($result);
			$iletestow = intval($conf[0]);
			$procent = 100/$iletestow;

			echo '<table style="width: 600px;">
			<tr style="height: 40px;">';
			for($i=1;$i<=$iletestow;$i+=1)
			{
				echo '<td style="width: '.$procent.'%; background-color: '; 
				$status = intval($result[4*$i+$i-1]);
				if($status == 1)
					echo 'green';
				elseif($status == 2)
					echo 'red';
				elseif($status == 3)
					echo '#7c0b0b';
				elseif($status == 4)
					echo '#ffe900';
				else
					echo '#2800ad';
				echo '"></td>';
			}
			echo '</tr>
			</table><br/><br/>';
			for($i=0;$i<20;$i+=1)
			{
				echo $i.'<br/>';
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

