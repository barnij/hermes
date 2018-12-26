<?php
	if(!isset($id_admin))
	{
		header('Location: /');
		exit();
	}

	if(!isset($_GET['edit_contest'])) //wyświetlanie listy contestów
	{
		echo '<p style="margin-top: 0px; margin-bottom: 5px;">Wybierz zawody, by zobaczyć szczegóły i je edytować:</br></p>';

		if(!isset($_GET['sort']))
		{
			$sort = 0; //sortuj od najnowszych do najstarszych (domyślnie)
		}else
		{
			$sort = $_GET['sort'];
		}

		if($sort==0 || $sort>5 || $sort<0)
		{
			$ws = 'id_contest DESC';
		}else if($sort==1)
		{
			$ws = 'id_contest ASC';
		}else if($sort==2)
		{
			$ws = 'shortcut_contest ASC';
		}else if($sort==3)
		{
			$ws = 'shortcut_contest DESC';
		}else if($sort==4)
		{
			$ws = 'title_contest ASC';
		}else //$sort==5
		{
			$ws = 'title_contest DESC';
		}

		$tresc = "SELECT id_contest, shortcut_contest, title_contest FROM contests GROUP BY ".$ws;

		$zapytanie = $polaczenie->query($tresc);

		$atrybuty = 'align="left"';
		$sz1 = '50px';
		$sz2 = '80px';
		$sz3 = '590px';

		echo '<table style="width: 720px;">
				<tr style="line-height: 30px;">
					<th style="width: '.$sz1.'; text-align: left;">';

					if($sort==0)
					{
						echo '<a class="nolink" href="?tool=list_contest&sort=1">sql ↓';
					}else if($sort==1)
					{
						echo '<a class="nolink" href="?tool=list_contest&sort=0">sql ↑';
					}else
					{
						echo '<a class="nolink" href="?tool=list_contest&sort=0">sql';
					}

					echo '</a></th>
					<th style="width: '.$sz2.'; text-align: left;">';

					if($sort==2)
					{
						echo '<a class="nolink" href="?tool=list_contest&sort=3">ID ↑';
					}else if($sort==3)
					{
						echo '<a class="nolink" href="?tool=list_contest&sort=2">ID ↓';
					}else
					{
						echo '<a class="nolink" href="?tool=list_contest&sort=2">ID';
					}


					echo '</a></th>
					<th style="width: '.$sz3.'; text-align: left;">';

					if($sort==4)
					{
						echo '<a class="nolink" href="?tool=list_contest&sort=5">Tytuł zawodów ↑';
					}else if($sort==5)
					{
						echo '<a class="nolink" href="?tool=list_contest&sort=4">Tytuł zawodów ↓';
					}else
					{
						echo '<a class="nolink" href="?tool=list_contest&sort=4">Tytuł zawodów';
					}


					echo '</a></th>
				</tr>';

		while($row = mysqli_fetch_row($zapytanie))
		{
			$id = $row[0];
			$shortcut = $row[1];
			$title = $row[2];

			echo 	'<tr style="line-height: 22px;">
					<td style="width: '.$sz1.'; text-align: left;"><a class="nolink" href="?tool=list_contest&edit_contest='.$id.'">'.$id.'</a></td>
					<td style="width: '.$sz2.'; text-align: left;"><a class="nolink" href="?tool=list_contest&edit_contest='.$id.'">'.$shortcut.'</a></td>
					<td style="width: '.$sz3.'; text-align: left;"><a class="nolink" href="?tool=list_contest&edit_contest='.$id.'">'.$title.'</a></td>
				</tr>';

		}

		echo '</table>';

	}else //edycja i szczegóły wybranego contestu
	{
		$tresc="SELECT * from contests WHERE id_contest=".$_GET['edit_contest'];
		$zapytanie = $polaczenie -> query($tresc);
		$rezultat = $zapytanie->fetch_assoc();

		echo "Identyfikator wybranych zawodów: <b>".$rezultat["shortcut_contest"]."</b>"; 
		echo '<div class="borderinedit">
			<form method="post" action="functions/edit_contest.php">
				<label for="title_contest">Edytuj nazwę zawodów: </label><br/>
				<input type="text" name="title_contest" style="width: 450px;" value="'.$rezultat["title_contest"].'" required>';
				if(isset($_SESSION['e_title']))
				{
					echo '<span class="error" style="padding-left: 10px;">'.$_SESSION['e_title'].'</span>';
					unset($_SESSION['e_title']);
				}
				echo '<br/><br/>

				<label for="start_contest">Podaj czas rozpoczęcia: </label>
				<input type="text" name="start_contest" placeholder="rrrr-mm-dd hh:ii:ss" value="'.$rezultat["time_from"].'" required>';
				if(isset($_SESSION['e_date1']))
				{
					echo '<span class="error" style="padding-left: 10px;">'.$_SESSION['e_date1'].'</span>';
					unset($_SESSION['e_date1']);
				}
				echo '<br/><br/>

				<label for="end_contest">Podaj czas zakończenia: </label>
				<input type="text" name="end_contest" placeholder="rrrr-mm-dd hh:ii:ss" value="'.$rezultat["time_to"].'" required>';
				if(isset($_SESSION['e_date']))
				{
					echo '<span class="error" style="padding-left: 10px;">'.$_SESSION['e_date'].'</span>';
					unset($_SESSION['e_date']);
				}
				echo '<br/><br/>

				<script>
					function pokazhaslo() {
						var x = document.getElementById("edit_password_contest");
						if (x.type === "password") {
							x.type = "text";
						} else {
							x.type = "password";
						}
					} 
				</script>
				
				<table style="width: 680px;">
					<td style="text-align: left;">
						<label for="password_contest">Hasło zawodów: </label>
						<input type="password" name="password_contest" style="width: 200px; margin-left: 10px;" id="edit_password_contest"';
						if($rezultat["password"]=="") echo 'placeholder="zawody otwarte"';
						else echo 'value="'.$rezultat["password"].'"';
						echo '>
					</td>
					<td style="text-align: left; width: 300px;">
						Pokaż hasło: <input type="checkbox" onclick="pokazhaslo()">
					</td>
				</table><br/>
				
				<table style="width: 680px;">
					<td style="text-align: left;">
						<label for="visibility_contest">Widoczność na stronie głównej: </label>
						<input type="checkbox" name="visibility_contest" ';  
							if($rezultat["visibility"]) echo 'checked';
						echo '>
						
					</td>
					<td style="text-align: left;">
						<label for="timer_contest">Czy pokazywać licznik czasu?</label>
						<input type="checkbox" name="timer_contest" ';
							if($rezultat["timer"]) echo 'checked';
						echo '>
					</td>
				</table>
				<br/>
				<input type="hidden" name="id_contest" value="'.$rezultat["id_contest"].'">
				<input type="submit" value="Zapisz zmiany">';
				if(isset($_SESSION['edit_contest_success']))
				{
					echo '<span style="padding-left: 10px; color: green;">'.$_SESSION['edit_contest_success'].'</span>';
					unset($_SESSION['edit_contest_success']);
				}
			echo '</form>
		';
		echo '</div>';

	}

?>