<?php

	if(!isset($AccessToContest))
	{
		header('Location: /');
		exit();
	}
	
	if(!$AccessToContest)
	{
		header('Location: /'.$shortcut_contest);
	}else
	{
		if(!isset($_GET['nr']))
			$nr_strony=0;
		else
			$nr_strony=$_GET['nr'];

		$rekordownastronie = 13;
		$pominieterekordy = $nr_strony*$rekordownastronie;

		if(isset($_GET['my'])) //moje submity
		{
			$zapytanie=$polaczenie->query("SELECT tasks.id_task,tasks.title_task,submits.time, submits.status,submits.id_submit FROM tasks, submits WHERE tasks.id_task=submits.id_task AND submits.id_contest='$id_contest' AND submits.id_user = '$id_user' ORDER BY submits.id_submit DESC");

			$wszystkierekordy = mysqli_num_rows($zapytanie);

			$zapytanie=$polaczenie->query("SELECT tasks.id_task,tasks.title_task,submits.time, submits.status,submits.id_submit FROM tasks, submits WHERE tasks.id_task=submits.id_task AND submits.id_contest='$id_contest' AND submits.id_user = '$id_user' ORDER BY submits.id_submit DESC LIMIT $pominieterekordy, $rekordownastronie");

			$maxstron = floor($wszystkierekordy/$rekordownastronie)-1;

			if($wszystkierekordy%$rekordownastronie!=0)
				$maxstron=$maxstron+1;

			//-------------- wybor strony ------------------

			echo '<table width="720"">
			<tr>
			<th width="40" style="padding-bottom: 10px; text-align: left;">';

			if($nr_strony>0)
				echo '<a href="/'.$shortcut_contest.'/mysubmits/'.($nr_strony-1).'" style="text-decoration: none; color: black; font-weight: bold;">←</a>';

			echo '</th>
			<th width="640" style="text-align: center;"></th>
			<th width="40" style="padding-bottom: 10px; text-align: right;">';
			
			if($nr_strony<$maxstron)
				echo '<a href="/'.$shortcut_contest.'/mysubmits/'.($nr_strony+1).'" style="text-decoration: none; color: black; font-weight: bold;">→</a>';

			echo '</th>
			</tr>
			</table>';

			//----------------------------------------------

			$atrybutynaglowka = 'align="center" bgcolor="e5e5e5"';
			$sz1 = 80;
			$sz2 = 310;
			$sz3 = 170;
			$sz4 = 100;
			$sz5 = 60;

			echo '
			<table width="720" align="left" border="1" bordercolor="#d5d5d5" cellpadding="0" cellspacing="0">
			<tr>
			<td width="'.$sz1.'" '.$atrybutynaglowka.' style="padding-top: 5px; padding-bottom: 5px;">ID</td>
			<td width="'.$sz2.'" '.$atrybutynaglowka.'>Nazwa zadania</td>
			<td width="'.$sz3.'" '.$atrybutynaglowka.'>Czas wysłania</td>
			<td width="'.$sz4.'" '.$atrybutynaglowka.'>Zobacz kod</td>
			<td width="'.$sz5.'" '.$atrybutynaglowka.'>Status</td>
			<tr></tr>';

			while($row = mysqli_fetch_row($zapytanie))
			{
				$id_task = $row[0];
				$name_task = $row[1];
				$time=$row[2];
				$status=$row[3];
				$id_submit=$row[4];

				echo '	<td width="'.$sz1.'" align="center" style="line-height: 32px;">'.$id_task.'</td>
				<td width="'.$sz2.'" align="center" >'.$name_task.'</td>
				<td width="'.$sz3.'" align="center" >'.$time.'</td>
				<td width="'.$sz4.'" align="center" >'.'[ <a href="/submit/'.$id_submit.'" target="_blank">Otwórz</a> ]'.'</td>
				<td width="'.$sz5.'" align="center" >';
				//sprawdzenie statusu:
				if($status==1)
				{
					echo '<a href="/results/'.$id_submit.'" style="color: green; font-weight: bold; text-decoration: none;" target="_blank">OK</a>';
				}else if($status==-1)
				{
					echo '<a href="/results/'.$id_submit.'" style="color: red; font-weight: bold; text-decoration: none;" target="_blank">ERR</a>';
				}else
				{
					echo '<img src="/loading.gif" width="20px" height="20px" style="padding-top: 5px;">';

					$fileinresults = 'C:/xampp/htdocs/results/'.$id_submit.'.txt';
					if(file_exists($fileinresults))
					{
						$plik = file($fileinresults);
						$ilewierszy = count($plik)-1;
						
						if($plik[$ilewierszy]=="1") //zaktualizuj na poprawna
						{
							if($polaczenie->query("UPDATE submits SET status = 1 WHERE id_submit='$id_submit'"));
						}else //zaktualizuj na bledna
						{
							if($polaczenie->query("UPDATE submits SET status = -1 WHERE id_submit='$id_submit'"));
						}
					}
				}

				echo '</td>
				<tr></tr>';
			}

			echo '</tr>
			</table>';

			//-------------- wybor strony ------------------
			
			echo '<table width="720"">
			<tr>
			<th width="40" style="padding-top: 5px; text-align: left;">';

			if($nr_strony>0)
				echo '<a href="/'.$shortcut_contest.'/mysubmits/'.($nr_strony-1).'" style="text-decoration: none; color: black; font-weight: bold;">←</a>';

			echo '</th>
			<th width="640"></th>
			<th width="40" style="padding-top: 5px; text-align: right;">';
			
			if($nr_strony<$maxstron)
				echo '<a href="/'.$shortcut_contest.'/mysubmits/'.($nr_strony+1).'" style="text-decoration: none; color: black; font-weight: bold;">→</a>';

			echo '</th>
			</tr>
			</table>';

			//-------------------------------------------------

		}else //submity wszystkich
		{
			$zapytanie=$polaczenie->query("SELECT tasks.id_task,tasks.title_task,submits.time, submits.status,submits.id_submit, submits.id_user FROM tasks, submits WHERE tasks.id_task=submits.id_task AND submits.id_contest='$id_contest' ORDER BY submits.id_submit DESC");

			$wszystkierekordy = mysqli_num_rows($zapytanie);

			$zapytanie=$polaczenie->query("SELECT tasks.id_task,tasks.title_task,submits.time, submits.status,submits.id_submit, submits.id_user, users.name FROM tasks, submits, users WHERE tasks.id_task=submits.id_task AND submits.id_contest='$id_contest' AND submits.id_user=users.id_user ORDER BY submits.id_submit  DESC LIMIT $pominieterekordy, $rekordownastronie");

			$maxstron = floor($wszystkierekordy/$rekordownastronie)-1;

			if($wszystkierekordy%$rekordownastronie!=0)
				$maxstron=$maxstron+1;

			//-------------- wybor strony ------------------

			echo '<table width="720"">
			<tr>
			<th width="40" style="padding-bottom: 10px; text-align: left;">';

			if($nr_strony>0)
				echo '<a href="/'.$shortcut_contest.'/submits/'.($nr_strony-1).'" style="text-decoration: none; color: black; font-weight: bold;">←</a>';

			echo '</th>
			<th width="640" style="text-align: center;"></th>
			<th width="40" style="padding-bottom: 10px; text-align: right;">';
			
			if($nr_strony<$maxstron)
				echo '<a href="/'.$shortcut_contest.'/submits/'.($nr_strony+1).'" style="text-decoration: none; color: black; font-weight: bold;">→</a>';

			echo '</th>
			</tr>
			</table>';

			//----------------------------------------------

			$atrybutynaglowka = 'align="center" bgcolor="e5e5e5"';
			$sz1 = 80;
			$sz2 = 250;
			$sz3 = 170;
			$sz4 = 160;
			$sz5 = 60;

			echo '
			<table width="720" align="left" border="1" bordercolor="#d5d5d5" cellpadding="0" cellspacing="0">
			<tr>
			<td width="'.$sz1.'" '.$atrybutynaglowka.' style="padding-top: 5px; padding-bottom: 5px;">ID</td>
			<td width="'.$sz2.'" '.$atrybutynaglowka.'>Nazwa zadania</td>
			<td width="'.$sz3.'" '.$atrybutynaglowka.'>Czas wysłania</td>
			<td width="'.$sz4.'" '.$atrybutynaglowka.'>Użytkownik</td>
			<td width="'.$sz5.'" '.$atrybutynaglowka.'>Status</td>
			<tr></tr>';

			while($row = mysqli_fetch_row($zapytanie))
			{
				$id_task = $row[0];
				$name_task = $row[1];
				$time=$row[2];
				$status=$row[3];
				$id_submit=$row[4];
				$id_user_submit=$row[5];
				$name_user=$row[6];

				echo '	<td width="'.$sz1.'" align="center" style="line-height: 32px;">'.$id_task.'</td>
				<td width="'.$sz2.'" align="center" >'.$name_task.'</td>
				<td width="'.$sz3.'" align="center" >'.$time.'</td>
				<td width="'.$sz4.'" align="center" >'.$name_user.'</td>';
				
				echo '<td width="'.$sz5.'" align="center" >';
				//sprawdzenie statusu:
				if($status==1)
				{
					echo '<a href="/results/'.$id_submit.'" style="color: green; font-weight: bold; text-decoration: none;" target="_blank">OK</a>';
				}else if($status==-1)
				{
					echo '<a href="/results/'.$id_submit.'" style="color: red; font-weight: bold; text-decoration: none;" target="_blank">ERR</a>';
				}else
				{
					echo '<img src="/loading.gif" width="20px" height="20px" style="padding-top: 5px;">';

					if($id_user==$id_user_submit) //czy rozpoczynać sprawdzanie zawartosci pliku rezultowego
					{
						$fileinresults = 'C:/xampp/htdocs/results/'.$id_submit.'.txt';
						if(file_exists($fileinresults))
						{
							$plik = file($fileinresults);
							$ilewierszy = count($plik)-1;
							
							if($plik[$ilewierszy]=="1") //zaktualizuj na poprawna
							{
								if($polaczenie->query("UPDATE submits SET status = 1 WHERE id_submit='$id_submit'"));
							}else //zaktualizuj na bledna
							{
								if($polaczenie->query("UPDATE submits SET status = -1 WHERE id_submit='$id_submit'"));
							}
						}
					}
				}

				echo '</td>
				<tr></tr>';
			}

			echo '</tr>
			</table>';

			//-------------- wybor strony ------------------

			echo '<table width="720"">
			<tr>
			<th width="40" style="padding-top: 5px; text-align: left;">';

			if($nr_strony>0)
				echo '<a href="/'.$shortcut_contest.'/submits/'.($nr_strony-1).'" style="text-decoration: none; color: black; font-weight: bold;">←</a>';

			echo '</th>
			<th width="640" style="text-align: center;"></th>
			<th width="40" style="padding-top: 5px; text-align: right;">';
			
			if($nr_strony<$maxstron)
				echo '<a href="/'.$shortcut_contest.'/submits/'.($nr_strony+1).'" style="text-decoration: none; color: black; font-weight: bold;">→</a>';

			echo '</th>
			</tr>
			</table>';

			//----------------------------------------------

		}
	}
?>