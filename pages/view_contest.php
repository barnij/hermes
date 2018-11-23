<?php

	if(!isset($AccessToContest))
	{
		header('Location: /');
		exit();
	}

	if($AccessToContest)
	{
		echo '<table width="720" align="left">
				<tr>
					<td>Masz dostęp do tego konkursu.</td>
					<td style="text-align: right;">';

					$zapytanie = $polaczenie->query("SELECT id_task FROM contest_list WHERE id_contest='$id_contest'");
					$numoftasks = mysqli_num_rows($zapytanie);
					$zapytanie = $polaczenie->query("SELECT id_task FROM submits WHERE id_contest='$id_contest' AND id_user='$id_user' AND status=1 GROUP BY id_task");
					$numofcorrect = mysqli_num_rows($zapytanie);

					echo '<b><span style="color: green;">'.$numofcorrect.'</span>/'.$numoftasks.'</b>';

		echo '</td>
				</tr>
			</table><br/><br/>';
		$zapytanie = $polaczenie->query("SELECT tasks.id_task, tasks.title_task FROM tasks INNER JOIN contest_list ON tasks.id_task=contest_list.id_task WHERE contest_list.id_contest='$id_contest'");


		$atrybutynaglowka = 'align="center" bgcolor="e5e5e5"';
		$sz1 = 80;
		$sz2 = 380;
		$sz3 = 100;
		$sz4 = 100;
		$sz5 = 60;

		echo '
		<table width="720" align="left" border="1" bordercolor="#d5d5d5" cellpadding="0" cellspacing="0">
		<tr>
		<td width="'.$sz1.'" '.$atrybutynaglowka.' style="padding-top: 15px; padding-bottom: 15px;">ID</td>
		<td width="'.$sz2.'" '.$atrybutynaglowka.'>Nazwa zadania</td>
		<td width="'.$sz3.'" '.$atrybutynaglowka.'>Zobacz<br/>zadanie</td>
		<td width="'.$sz4.'" '.$atrybutynaglowka.'>Wyślij<br/>rozwiązanie</td>
		<td width="'.$sz5.'" '.$atrybutynaglowka.'>Status</td>
		<tr></tr>';

		while($row = mysqli_fetch_row($zapytanie))
		{
			$id_task = $row[0];
			$name_task = $row[1];

			echo '	<td width="'.$sz1.'" align="center" style="line-height: 32px;">'.$id_task.'</td>
			<td width="'.$sz2.'" align="center" >'.$name_task.'</td>
			<td width="'.$sz3.'" align="center" >[ <a href="/task/'.$id_task.'" target="_blank">Otwórz</a> ]</td>
			<td width="'.$sz4.'" align="center" >[ <a href="/'.$_GET['id'].'/'.$id_task.'/submit">Otwórz</a> ]</td>
			<td width="'.$sz5.'" align="center" >';
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
					$rezultat = $zapytanie1->fetch_assoc();
					echo '<a href="/results/'.$rezultat['id_submit'].'" style="color: green; font-weight: bold; text-decoration: none;" target="_blank">OK</a>';
				}else
				{
					$zapytanie1 = $polaczenie->query("SELECT status,id_submit FROM submits WHERE id_task='$id_task' AND id_user='$id_user' AND id_contest='$id_contest' ORDER BY id_submit DESC LIMIT 1");
					$rezultat = $zapytanie1->fetch_assoc();

					if($rezultat['status']==0) //sprawdzanie zadania trwa
					{
						echo '<img src="/loading.gif" width="20px" height="20px" style="padding-top: 5px;">';

						$fileinresults = 'C:/xampp/htdocs/results/'.$rezultat['id_submit'].'.txt';
						if(file_exists($fileinresults))
						{
							$plik = file($fileinresults);
							$ilewierszy = count($plik)-1;
							$id_submit = $rezultat['id_submit'];
							
							if($plik[$ilewierszy]=="1") //zaktualizuj na poprawna
							{
								if($polaczenie->query("UPDATE submits SET status = 1 WHERE id_submit='$id_submit'"));
							}else //zaktualizuj na bledna
							{
								if($polaczenie->query("UPDATE submits SET status = -1 WHERE id_submit='$id_submit'"));
							}
						}

					}else //jedyne wyslania byly zle, a to ostatnie z nich
					{
						echo '<a href="/results/'.$rezultat['id_submit'].'" style="color: red; font-weight: bold; text-decoration: none;" target="_blank">ERR</a>';
					}
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