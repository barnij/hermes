<?php

	if(!isset($AccessToContest))
	{
		header('Location: /');
		exit();
	}

	if($AccessToContest)
	{
		function updatetaskstatus($polaczenie, $id_submit)
		{
			$fileinresults = '/var/www/html/results/'.$id_submit.'.txt';
			
			if(file_exists($fileinresults))
			{
				$plik = file($fileinresults);
				$ilewierszy = count($plik)-1;
					
				if($plik[$ilewierszy]=="1") //zaktualizuj na poprawna
				{
					if(!($polaczenie->query("UPDATE submits SET status = 1 WHERE id_submit='$id_submit'")))
						echo "Error: ".$polaczenie->connect_errno;
				}
				elseif($plik[$ilewierszy]=="2") //zaktualizuj na bledna
				{	
					if(!($polaczenie->query("UPDATE submits SET status = 2 WHERE id_submit='$id_submit'")))
						echo "Error: ".$polaczenie->connect_errno;
				}
				elseif($plik[$ilewierszy]=="3") //zaktualizuj na błąd kompilacji
				{	
					if(!($polaczenie->query("UPDATE submits SET status = 3 WHERE id_submit='$id_submit'")))
						echo "Error: ".$polaczenie->connect_errno;
				}
				elseif($plik[$ilewierszy]=="4") //zaktualizuj na przekroczenie czasu
				{	
					if(!($polaczenie->query("UPDATE submits SET status = 4 WHERE id_submit='$id_submit'")))
						echo "Error: ".$polaczenie->connect_errno;
				}
				elseif($plik[$ilewierszy]=="5") //zaktualizuj na naruszenie pamięci
				{
					if(!($polaczenie->query("UPDATE submits SET status = 5 WHERE id_submit='$id_submit'")))
						echo "Error: ".$polaczenie->connect_errno;
				}

				$points = 0;

				for($i=0;$i<$ilewierszy;$i+=1)
				{
					if(substr($plik[$i],0,1)=='#')
						$points += intval($plik[$i+1]);
				}

				if(!($polaczenie->query("UPDATE submits SET points = '$points' WHERE id_submit='$id_submit'")))
					echo "Error: ".$polaczenie->connect_errno;

			}
		}

		echo '<table width="720" align="left">
				<tr>
					<td>Masz dostęp do tego konkursu.</td>
					<td style="text-align: right;">';

					$zapytanie = $polaczenie->query("SELECT id_task FROM contest_list WHERE id_contest='$id_contest'");
					$numoftasks = mysqli_num_rows($zapytanie);
					
					if($showresults)
					{
						$zapytanie = $polaczenie->query("SELECT id_task FROM submits WHERE id_contest='$id_contest' AND id_user='$id_user' AND status=1 GROUP BY id_task");
						$numofcorrect = mysqli_num_rows($zapytanie);
						echo '<b><span style="color: green;">'.$numofcorrect.'</span>/'.$numoftasks.'</b>';
					}else
						echo '<b><span style="color: grey;">?</span>/'.$numoftasks.'</b>';
		echo '</td>
				</tr>
			</table><br/><br/>';
		
		$zapytanie = $polaczenie->query("SELECT tasks.id_task, tasks.title_task, tasks.pdf FROM tasks INNER JOIN contest_list ON tasks.id_task=contest_list.id_task WHERE contest_list.id_contest='$id_contest' GROUP BY tasks.id_task");


		$atrybutynaglowka = 'align="center" bgcolor="e5e5e5"';
		$sz1 = 80;
		$sz2 = 315;
		$sz3 = 85;
		$sz4 = 85;
		$sz5 = 95;
		$sz6 = 60;

		echo '
		<table width="720" align="left" border="1" bordercolor="#d5d5d5" cellpadding="0" cellspacing="0">
		<tr>
		<td width="'.$sz1.'" '.$atrybutynaglowka.' style="padding-top: 15px; padding-bottom: 15px;">ID</td>
		<td width="'.$sz2.'" '.$atrybutynaglowka.'>Nazwa zadania</td>
		<td width="'.$sz3.'" '.$atrybutynaglowka.'>Zobacz<br/>wysłania</td>
		<td width="'.$sz4.'" '.$atrybutynaglowka.'>Zobacz<br/>zadanie</td>
		<td width="'.$sz5.'" '.$atrybutynaglowka.'>Wyślij<br/>rozwiązanie</td>
		<td width="'.$sz6.'" '.$atrybutynaglowka.'>Status</td>
		<tr></tr>';

		while($row = mysqli_fetch_row($zapytanie))
		{
			$id_task = $row[0];
			$name_task = $row[1];
			$if_pdf = $row[2];

			echo '	<td width="'.$sz1.'" align="center" style="line-height: 32px;">'.$id_task.'</td>
			<td width="'.$sz2.'" align="center" >'.$name_task.'</td>';
			if($showresults)
				echo '<td width="'.$sz3.'" align="center">[ <a href="/'.$_GET['id'].'/'.$id_task.'/submits">Otwórz</a> ]</td>';
			else
				echo '<td width="'.$sz3.'" align="center">•</td>';
			echo '<td width="'.$sz4.'" align="center" >[ <a href="';
			if($if_pdf==1)
				echo '/task/'.$id_task.'" target="_blank"';
			else echo $_GET['id'].'/'.$id_task.'"';
			echo '>Otwórz</a> ]</td>
			<td width="'.$sz5.'" align="center" >[ <a href="/'.$_GET['id'].'/'.$id_task.'/submit">Otwórz</a> ]</td>
			<td width="'.$sz6.'" align="center" >';
			//sprawdzenie statusu:
			$zapytanie1 = $polaczenie->query("SELECT status FROM submits WHERE id_task='$id_task' AND id_user='$id_user' AND id_contest='$id_contest' ORDER BY id_submit DESC LIMIT 1");
			
			if(mysqli_num_rows($zapytanie1)==0) //brak wysłań zadania w danym contescie
			{
				echo "•";
			}else
			{	
				$zapytanie1 = $polaczenie->query("SELECT status,id_submit FROM submits WHERE id_task='$id_task' AND id_user='$id_user' AND id_contest='$id_contest' AND status=1 ORDER BY id_submit DESC LIMIT 1");
				if(mysqli_num_rows($zapytanie1)!=0) //przynajmniej raz rozwiazano dobrze zadanie
				{
					if($showresults)
					{
						$rezultat = $zapytanie1->fetch_assoc();
						echo '<a href="/results/'.$rezultat['id_submit'].'" style="color: green; font-weight: bold; text-decoration: none;" target="_blank">OK</a>';
					}else
						echo '<span style="color: grey; font-weight: bold; text-decoration: none;">?</span>';
				}else
				{
					$zapytanie1 = $polaczenie->query("SELECT status,id_submit FROM submits WHERE id_task='$id_task' AND id_user='$id_user' AND id_contest='$id_contest' ORDER BY id_submit DESC LIMIT 1");
					$rezultat = $zapytanie1->fetch_assoc();
					$status=$rezultat['status'];
					$id_submit= $rezultat['id_submit'];

					if($status==0)
					{
						echo '<img src="/images/loading.gif" width="20px" height="20px" style="padding-top: 5px;">';

						updatetaskstatus($polaczenie, $id_submit);
					}
					elseif(!$showresults)
						echo '<span style="color: grey; font-weight: bold; text-decoration: none;">?</span>';
					elseif($status==1)
						echo '<a href="/results/'.$id_submit.'" style="color: green; font-weight: bold; text-decoration: none;" target="_blank">OK</a>';
					elseif($status==2)
						echo '<a href="/results/'.$id_submit.'" style="color: red; font-weight: bold; text-decoration: none;" target="_blank">ERR</a>';
					elseif($status==3)
						echo '<a href="/results/'.$id_submit.'" style="color: #7c0b0b; font-weight: bold; text-decoration: none;" target="_blank">CPE</a>';
					elseif($status==4)
						echo '<a href="/results/'.$id_submit.'" style="color: #ffe900; font-weight: bold; text-decoration: none; text-shadow: 1px 1px black;" target="_blank">TLE</a>';
					elseif($status==5)
						echo '<a href="/results/'.$id_submit.'" style="color: #2800ad; font-weight: bold; text-decoration: none;" target="_blank">SEG</a>';
				}
			}

			echo '</td>
			<tr></tr>';
		}

		echo '</tr>
		</table>';

	}
	else
	{
		echo "Nie masz dostępu do tego konkursu. <br/><br/>";

		echo '
		<form action="/functions/accesstocontest.php" method="post">
			<input type="hidden" name="shortcut" value="'.$shortcut_contest.'" />
			<input type="hidden" name="id_contest" value="'.$id_contest.'" />
			Wpisz hasło: <input type="password" name="password"></input><br/><br/>';
		if(isset($_SESSION['e_contest'])){ echo $_SESSION['e_contest']; unset($_SESSION['e_contest']);}
		echo '<input type="submit" value="Uzyskaj dostęp">
		</form>
		';
	}
?>