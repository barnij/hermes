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
        $zapytanie = $polaczenie->query("SELECT users.name, SUM(submits.points) AS sumapunktow, SUM(CASE submits.status WHEN 1 THEN 1 ELSE 0 END) AS sumaok, SUM(HOUR(TIMEDIFF(submits.time, '$start_time_contest'))*60+MINUTE(TIMEDIFF(submits.time, '$start_time_contest'))) AS czas FROM submits, users WHERE users.id_user=submits.id_user AND submits.id_contest='$id_contest' GROUP BY submits.id_user ORDER BY sumapunktow DESC, sumaok ASC, czas ASC");

		$atrybutynaglowka = 'align="center" bgcolor="e5e5e5"';
		$sz1 = 50;
		$sz2 = 400;
		$sz3 = 80;
		$sz4 = 70;
		$sz5 = 120;

		echo '
		<table width="720" align="left" border="1" bordercolor="#d5d5d5" cellpadding="0" cellspacing="0">
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
			echo '	<td width="'.$sz1.'" align="center" style="line-height: 32px;">'.$lp.'</td>
			<td width="'.$sz2.'" align="center" >'.$row[0].'</td>
			<td width="'.$sz3.'" align="center">'.$row[1].'</td>
			<td width="'.$sz4.'" align="center" >'.$row[2].'</td>
			<td width="'.$sz5.'" align="center" >'.$row[3].'</td>
            <tr></tr>';
            $lp+=1;
		}

		echo '</tr>
		</table>';

    }
    
?>


