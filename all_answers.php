<table width="700" align="left" border="1" bordercolor="#d5d5d5" cellpadding="0" cellspacing="0" id="wyslania">
	<tr style="line-height: 40px;">
		<td width="100" align="center" bgcolor="e5e5e5">Nr zadania</td>
		<td width="150" align="center" bgcolor="e5e5e5">Czas przesłania</td>
		<td width="120" align="center" bgcolor="e5e5e5">Liczba znaków</td>
		<td width="120" align="center" bgcolor="e5e5e5">Pokaż kod</td>
		<td width="210" align="center" bgcolor="e5e5e5">Status</td>
	<tr></tr>
	<?php
		$zapytanie2 = $polaczenie->query("SELECT id_task,time,words,if_correct,id_ans FROM answers WHERE id_user='$id_user' GROUP BY id_ans DESC");
		$ile = mysqli_num_rows($zapytanie2);

		for ($i = 1; $i <= $ile; $i++)
		{
			$row = $zapytanie2->fetch_assoc();
			$id_task = $row['id_task'];
			$czas_przeslania = $row['time'];
			$words = $row['words'];
			$status = $row['if_correct'];
			$id_ans = $row['id_ans'];

			echo "	<td width=\"100\" align=\"center\" >$id_task</td>
					<td width=\"150\" align=\"center\" >$czas_przeslania</td>
					<td width=\"120\" align=\"center\" >$words</td>
					<td width=\"120\" align=\"center\" ><a href=\"?kod=".$id_ans."\">Pokaż</a></td>
					<td width=\"210\" align=\"center\" >";
			
			if($status==0)
			{
				echo "<span style=\"color: red;\">Odpowiedź błędna</span>";
			}elseif ($status==1)
			{
				echo "Błąd testerki.<br/> Spróbuj ponownie.";
			}else
			{
				echo "<span style=\"color: green; font-weight: bold;\">Odpowiedź prawidłowa</span>";
			}

			echo 	"</td>
					<tr></tr>";
		}
	?>
	</tr>
</table>