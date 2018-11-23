<table width="700" align="left" border="1" bordercolor="#d5d5d5" cellpadding="0" cellspacing="0" id="wyslania">
	<tr style="line-height: 40px;">
		<td width="100" align="center" bgcolor="e5e5e5">Miejsce</td>
		<td width="240" align="center" bgcolor="e5e5e5">Nazwa drużyny</td>
		<td width="120" align="center" bgcolor="e5e5e5">Ilość wysłań</td>
		<td width="120" align="center" bgcolor="e5e5e5">Ilość OK</td>
		<td width="120" align="center" bgcolor="e5e5e5">Ilość znaków</td>
	<tr></tr>
	<?php
		$zapytanie1 = $polaczenie->query("SELECT * FROM ranking ORDER BY ok_ans DESC,words");
		$miejsce = 1;

		while($row = mysqli_fetch_row($zapytanie1))
		{
			$id_user = $row[0];
			$all_ans = $row[1];
			$ok_ans = $row[2];
			$words = $row[3];

			$zapytanie2 = $polaczenie->query("SELECT name FROM users WHERE id_user = '$id_user' LIMIT 1");
			$wiersz = $zapytanie2->fetch_assoc();
			$name = $wiersz['name'];

			echo "	<td width=\"100\" align=\"center\" >$miejsce</td>
					<td width=\"240\" align=\"center\" >$name</td>
					<td width=\"120\" align=\"center\" >$all_ans</td>
					<td width=\"120\" align=\"center\" >$ok_ans</td>
					<td width=\"120\" align=\"center\" >$words</td>
					<tr></tr>";
			$miejsce = $miejsce + 1;
		}
	?>
	</tr>
</table>