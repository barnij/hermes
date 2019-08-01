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
		if($showresults)
		{
			$zapytanie = $polaczenie->query("SELECT users.name, SUM(sub.points) AS sumapunktow, SUM(CASE sub.status WHEN 1 THEN 1 ELSE 0 END) AS sumaok, SUM(CASE sub.points WHEN 0 THEN 0 ELSE HOUR(TIMEDIFF(sub.time, '$start_time_contest'))*60+MINUTE(TIMEDIFF(sub.time, '$start_time_contest')) END) AS czas FROM (SELECT submits.* FROM (SELECT id_user, id_contest, id_task, MAX(points) AS points FROM submits GROUP BY id_user, id_contest, id_task) tt INNER JOIN submits ON tt.id_user=submits.id_user AND tt.id_contest=submits.id_contest AND tt.id_task=submits.id_task AND tt.points=submits.points) sub, users WHERE users.id_user=sub.id_user AND sub.id_contest='$id_contest' AND TIMEDIFF('$end_time_contest',sub.time)>0 GROUP BY sub.id_user ORDER BY sumapunktow DESC, sumaok ASC, czas ASC");

			$atrybutynaglowka = 'align="center" bgcolor="e5e5e5"';
			$sz1 = 50;
			$sz2 = 360;
			$sz3 = 90;
			$sz4 = 70;
			$sz5 = 150;

			echo '
			<table width="720" align="left" border="1" bordercolor="#d5d5d5" cellpadding="0" cellspacing="0" style="margin-top: 10px;">
			<tr>
			<td width="'.$sz1.'" '.$atrybutynaglowka.' style="padding-top: 15px; padding-bottom: 15px;">lp.</td>
			<td width="'.$sz2.'" '.$atrybutynaglowka.'>Nazwa użytkownika</td>
			<td width="'.$sz3.'" '.$atrybutynaglowka.'>Suma<br/>punktów</td>
			<td width="'.$sz4.'" '.$atrybutynaglowka.'>Suma<br/>OK</td>
			<td width="'.$sz5.'" '.$atrybutynaglowka.'>Łączny<br/>Czas (m)</td>
			<tr></tr>';

			$lp = 1;

			while($row = mysqli_fetch_row($zapytanie))
			{
				echo '<tr ';
				if($lp==1) echo 'style="background-color: #52ea54;"';
				else if($lp==2) echo 'style="background-color: #93ed94"';
				else if($lp==3) echo 'style="background-color: #c1f4c1"';
				echo'>
				<td width="'.$sz1.'" align="center" style="line-height: 32px;">'.$lp.'</td>
				<td width="'.$sz2.'" align="center" >'.$row[0].'</td>
				<td width="'.$sz3.'" align="center" style="font-weight: bold;"><span title="'.$row[1].'">'.intval($row[1]).'</span></td>
				<td width="'.$sz4.'" align="center" >'.$row[2].'</td>
				<td width="'.$sz5.'" align="center" >'.$row[3].'</td>
				</tr>';
				$lp+=1;
			}
			echo '</tr>
			</table><div style="clear: both;"></div><br/>';

			$zapytanie = $polaczenie->query("SELECT users.name, SUM(sub.points) AS sumapunktow, SUM(CASE sub.status WHEN 1 THEN 1 ELSE 0 END) AS sumaok, SUM(CASE sub.points WHEN 0 THEN 0 ELSE HOUR(TIMEDIFF(sub.time, '$start_time_contest'))*60+MINUTE(TIMEDIFF(sub.time, '$start_time_contest')) END) AS czas FROM (SELECT submits.* FROM (SELECT id_user, id_contest, id_task, MAX(points) AS points FROM submits GROUP BY id_user, id_contest, id_task) tt INNER JOIN submits ON tt.id_user=submits.id_user AND tt.id_contest=submits.id_contest AND tt.id_task=submits.id_task AND tt.points=submits.points) sub, users WHERE users.id_user=sub.id_user AND sub.id_contest='$id_contest' AND TIMEDIFF('$end_time_contest',sub.time)<=0 GROUP BY sub.id_user ORDER BY sumapunktow DESC, sumaok ASC, czas ASC");

			if(mysqli_num_rows($zapytanie)>0)
			{
			echo 'Po zakończeniu zawodów:

				<table width="720" align="left" border="1" bordercolor="#d5d5d5" cellpadding="0" cellspacing="0" style="margin-top: 10px; color: gray;">
				<tr>';

				$lp = 1;

				while($row = mysqli_fetch_row($zapytanie))
				{
					echo '	<td width="'.$sz1.'" align="center" style="line-height: 32px;">'.$lp.'</td>
					<td width="'.$sz2.'" align="center" >'.$row[0].'</td>
					<td width="'.$sz3.'" align="center"><span title="'.$row[1].'">'.intval($row[1]).'</span></td>
					<td width="'.$sz4.'" align="center" >'.$row[2].'</td>
					<td width="'.$sz5.'" align="center" >'.$row[3].'</td>
					<tr></tr>';
					$lp+=1;
				}

				echo '</tr>
				</table>';
			}
		}else
		{
			echo 'Wyniki są ukryte.';
		}

    }

?>


