<?php
	session_start();

	if((!(isset($_GET['result']))) || ($_GET['result']==0) || ((!(isset($_SESSION['zalogowany'])))  || (isset($_SESSION['zalogowany'])==false)) && (!isset($_SESSION['zalogowanyadmin'])) )
	{
		echo "Błąd w uwierzytelnianiu.<br/>Zaloguj się i spróbuj ponownie.";

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

		$zapytanie = $polaczenie->query("SELECT contests.showresults AS showresults, contests.shortcut_contest AS contest, submits.id_task AS id_task, submits.lang AS lang, submits.time AS time, submits.points AS points, users.name AS user FROM contests,submits,users WHERE submits.id_contest=contests.id_contest AND submits.id_submit='$id_submit' AND users.id_user=submits.id_user");
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
			$contest = $rezultat['contest'];
			$points = $rezultat['points'];
			$user = $rezultat['user'];
			$lang = $rezultat['lang'];
			$time = $rezultat['time'];
			$resultadres = $_SERVER['DOCUMENT_ROOT'].'/results/'.$id_submit.'.txt';
			$result = file($resultadres);
			$confadres = $_SERVER['DOCUMENT_ROOT'].'/tasks/'.$id_task.'/conf.txt';
			$conf = file($confadres);
			$ile = count($result);
			$iletestow = intval($conf[0]);
			$procent = 100/$iletestow;

			echo '
			<span style="margin-left: 10px;">
				Rozwiązanie użytkownika: <b>'.$user.'</b>
			</span><br/>
			<span style="margin-left: 10px;">
				Rozwiązanie zadania: <b>'.$id_task.'</b>
			</span>
			<span style="margin-left: 20px;">
				Język rozwiązania: <b>'.$lang.'</b>
			</span>
			<span style="margin-left: 20px;">
				Czas zgłoszenia: <b>'.$time.'</b>
			</span></br>
			<span style="margin-left: 10px;">
				Rozwiązanie do zawodów: <b>'.$contest.'</b>
			</span>
			<span style="margin-left: 20px;">
				Łączna liczba uzyskanych punktów: <b>'.$points.'</b>
			</span>

			<table style="width: 700px; margin-top: 10px;">
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
			</table><br/>';

			echo
			'<table style="width: 700px; border-collapse: collapse;">
				<tr style="text-align: left; border-bottom: black 1px solid;">
					<th style="width: 40px;">
						Nr
					</th>
					<th style="width: 150px;">
						Punkty
					</th>
					<th style="width: 200px;">
						Czas działania (s)
					</th>
					<th style="width: 200px;">
						Zużyta pamięć (MB)
					</th>
					<th style="width: 110px;">
						Status
					</th>
				</tr>';

				for($i=0; $i<$iletestow; $i+=1)
				{
					$i_r = ($i*5)+1; //liczba pkt
					$i_c = ($i*4)+2; //max liczba pkt
					echo '<tr  style="border-bottom: gray 1px solid;">
						<td style="width: 40px;">'.
							$i.
						'</td>
						<td style="width: 150px;">'.
							$result[$i_r].' / '.$conf[$i_c].
						'</td>
						<td style="width: 200px;">'.
							$result[$i_r+1].' / '.$conf[$i_c+1].
						'</td>
						<td style="width: 200px;">'.
							$result[$i_r+2].' / '.$conf[$i_c+2].
						'</td>
						<td style="width: 110px;">';
							switch(intval($result[$i_r+3]))
							{
								case 1:
									echo '<span>Accepted</span>';
								break;
								case 2:
									echo '<span">Wrong Answer</span>';
								break;
								case 3:
									echo '<span>Compilation Error</span>';
								break;
								case 4:
									echo '<span>Time Limit Exceeded</span>';
								break;
								case 5:
									echo '<span>Segmentation Fault</span>';
								break;
								default:
								break;
							}
						echo
						'</td>';

					echo '</tr>';
				}

			echo
			'</table>';

		}else
		{
			echo "Wyniki są ukryte.";
		}

		echo '</body>
		</html>';
	}

	$polaczenie->close();
?>

