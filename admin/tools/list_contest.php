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

		echo '<script type="text/javascript">
					function pokazedit() 
					{
						var x = document.getElementById("edytujszczegoly");
						
						if(x.style.display == "none")
							x.style.display = "block";
						else
							x.style.display = "none";
					}
				</script>';

		echo "<table style=\"width: 700px;\">
			<tr>
			<td>
				Identyfikator wybranych zawodów: <b>".$rezultat["shortcut_contest"]."</b>
			</td>
			<td style=\"text-align: right;\">
				<span class=\"sztucznylink\" onclick=\"pokazedit()\">Edytuj szczegóły</span>
			</td>
			</tr>
		</table>";
		echo '<div class="borderinedit" ';
			if(isset($_SESSION['edit_contest']))
				unset($_SESSION['edit_contest']);
			else
				echo 'style="display: none;" ';
		echo 'id="edytujszczegoly">
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
						<label for="showpassword">Pokaż hasło: </label><input type="checkbox" onclick="pokazhaslo()" id="showpassword">
					</td>
				</table><br/>
				
				<table style="width: 680px;">
					<td style="text-align: left;">
						<label for="visibility_contest">Widoczność na stronie głównej: </label>
						<input type="checkbox" name="visibility_contest" id="visibility_contest" ';  
							if($rezultat["visibility"]) echo 'checked';
						echo '>
						
					</td>
					<td style="text-align: left;">
						<label for="timer_contest">Czy pokazywać licznik czasu?</label>
						<input type="checkbox" name="timer_contest" id="timer_contest" ';
							if($rezultat["timer"]) echo 'checked';
						echo '>
					</td>
				</table><br/>

				<table style="width: 680px;">
					<td style="text-align: left;">
						<label for="showresults">Widoczność wyników:</label>
						<input type="checkbox" name="showresults" id="showresults" ';
							if($rezultat['showresults']) echo 'checked';
						echo '>	
					</td>
					<td style="text-align: left;">
						<label for="submitafterend">Wysyłanie rozwiązań po zakończeniu:</label>
						<input type="checkbox" name="submitafterend" id="submitafterend" ';
							if($rezultat["submitafterend"]) echo 'checked';
						echo '>
					</td>
				</table><br/>
				
				<input type="hidden" name="id_contest" value="'.$rezultat["id_contest"].'">
				<table style="width: 670px; margin-top: 0;">
					<tr>
					<td style="text-align: left; width: 50%;">
						<input style="margin-left:5px;" type="submit" value="Zapisz zmiany">';
						if(isset($_SESSION['edit_contest_success']))
						{
							echo '<span style="padding-left: 10px; color: green;">'.$_SESSION['edit_contest_success'].'</span>';
							unset($_SESSION['edit_contest_success']);
						}
						echo '</form>
					</td>
					<td style="text-align: right; width: 50%;">
						<form method="post" action="functions/delete_contest.php">
							<input type="hidden" name="id_contest" value="'.$id_contest.'">
							<input style="background-color: red;" type="submit" name="TAKusuncontest" value="Usuń Zawody" onclick="'."return confirm('Czy na pewno chcesz to zrobić? Zostaną usunięte nadesłane rozwiązania i cała historia zawodów!');\"".'>
						</form>
					</td>
					</tr>
				</table>	
		</div>';

		$sz1 = 100;
		$sz2 = 300;
		$sz3 = 100;
		$sz4 = 100;
		$sz5 = 100;
		$tal = 'text-align: left';
		$tac = 'text-align: center';

		if(!isset($_GET['sort']))
		{
			$sort = 0; //sortuj od najnowszych do najstarszych (domyślnie)
		}else
		{
			$sort = $_GET['sort'];
		}

		if($sort==0 || $sort>5 || $sort<0)
		{
			$ws = 'id_task ASC';
		}else if($sort==1)
		{
			$ws = 'id_task DESC';
		}else if($sort==2)
		{
			$ws = 'title_task ASC';
		}else if($sort==3)
		{
			$ws = 'title_task DESC';
		}else if($sort==4)
		{
			$ws = 'difficulty ASC';
		}else //$sort==5
		{
			$ws = 'difficulty DESC';
		}

		echo '<div class="borderinedit">
			<table style="width: 700px;">
				<tr>
					<th style="width: '.$sz1.'px; '.$tal.'">';
						if($sort==0)
							echo '<a class="nolink" href="?tool=list_contest&edit_contest='.$id_contest.'&sort=1">ID ↑';
						elseif($sort==1)
							echo '<a class="nolink" href="?tool=list_contest&edit_contest='.$id_contest.'&sort=0">ID ↓';
						else
							echo '<a class="nolink" href="?tool=list_contest&edit_contest='.$id_contest.'&sort=0">ID';
				echo '</a>
					</th>
					<th style="width: '.$sz2.'px; '.$tal.'">';
					if($sort==2)
						echo '<a class="nolink" href="?tool=list_contest&edit_contest='.$id_contest.'&sort=3">Nazwa zadania ↑';
					elseif($sort==3)
						echo '<a class="nolink" href="?tool=list_contest&edit_contest='.$id_contest.'&sort=2">Nazwa zadania ↓';
					else
						echo '<a class="nolink" href="?tool=list_contest&edit_contest='.$id_contest.'&sort=2">Nazwa zadania';
				echo'</a>	
					</th>
					<th style="width: '.$sz3.'px; '.$tac.'">';
					if($sort==4)
						echo '<a class="nolink" href="?tool=list_contest&edit_contest='.$id_contest.'&sort=5">Trudność ↑';
					elseif($sort==5)
						echo '<a class="nolink" href="?tool=list_contest&edit_contest='.$id_contest.'&sort=4">Trudność ↓';
					else
						echo '<a class="nolink" href="?tool=list_contest&edit_contest='.$id_contest.'&sort=4">Trudność';
				echo '</a>
					</th>
					<th style="width: '.$sz4.'px; '.$tac.'">
						Zobacz treść
					</th>
					<th style="width: '.$sz5.'px; '.$tac.'">
						Zaznacz
					</th>
				</tr>
			</table>
			<form method="post" action="functions/edit_contest_list.php">
			<p style="margin-top: 8px; margin-bottom: 5px;">Dodane zadania:</br></p>
			<table style="width: 700px; border-spacing:0 10px;">';

		$tresc = "SELECT contest_list.id_task AS id_task, tasks.title_task AS title_task, tasks.difficulty AS difficulty, tasks.pdf FROM tasks, contest_list WHERE contest_list.id_contest='$id_contest' AND contest_list.id_task=tasks.id_task GROUP BY contest_list.id_task ORDER BY ".$ws;
		$zapytanie = $polaczenie->query($tresc);

		while($row = mysqli_fetch_row($zapytanie))
		{
			echo '<tr>';
			echo '<td style="font-weight: bold; width: '.$sz1.'px;">
					<label for="'.$row[0].'">'.
						$row[0]
				  	.'</label></td>
					<td style="width: '.$sz2.'px;">
					<label for="'.$row[0].'">'.
						$row[1]
					.'</label></td>
					<td style="width: '.$sz3.'px; '.$tac.'">
					<label for="'.$row[0].'">'.
						$row[2]
					.'</label></td>
					<td style="width: '.$sz4.'px; '.$tac.'">
						[ <a href="'; 
						if($row[3]==1) echo '/tasks/'.$row[0].'/'.$row[0].'.pdf';
						else echo '/admin/functions/view_task.php?task='.$row[0];
						echo'" target="_blank">Otwórz</a> ]
					</td>
					<td style="width: '.$sz5.'px; '.$tac.'">
						<input type="checkbox" name="listoftasks[]" id="'.$row[0].'" value="'.$row[0].'" checked>
					</td>';

			echo '</tr>';
		}
		echo '</table>
		<p style="margin-top: 5px; margin-bottom: 5px;">Pozostałe zadania:</br></p>
		<table style="width: 700px; border-spacing:0 10px;">';

		$tresc = "SELECT id_task, title_task, difficulty, pdf FROM tasks WHERE NOT EXISTS (SELECT NULL FROM contest_list WHERE contest_list.id_task = tasks.id_task AND id_contest = '$id_contest') ORDER BY ".$ws;
		$zapytanie = $polaczenie->query($tresc);

		while($row = mysqli_fetch_row($zapytanie))
		{
			echo '<tr>';
			echo '<td style="font-weight: bold; width: '.$sz1.'px;">
					<label for="'.$row[0].'">'.
						$row[0]
				  	.'</label></td>
					<td style="width: '.$sz2.'px;">
					<label for="'.$row[0].'">'.
						$row[1]
					.'</label></td>
					<td style="width: '.$sz3.'px; '.$tac.'">
					<label for="'.$row[0].'">'.
						$row[2]
					.'</label></td>
					<td style="width: '.$sz4.'px; '.$tac.'">
						[ <a href="'; 
						if($row[3]==1) echo '/tasks/'.$row[0].'/'.$row[0].'.pdf';
						else echo '/admin/functions/view_task.php?task='.$row[0];
						echo'" target="_blank">Otwórz</a> ]
					</td>
					<td style="width: '.$sz5.'px; '.$tac.'">
						<input type="checkbox" name="listoftasks[]" id="'.$row[0].'" value="'.$row[0].'">
					</td>';

			echo '</tr>';
		}
		echo '</table>
		<input type="hidden" name="id_contest" value="'.$id_contest.'">
		<input type="submit" value="Zapisz zestaw zadań">';
		if(isset($_SESSION['success_edit_contest_list']))
		{
			echo '<span style="padding-left: 10px; color: green;">'.$_SESSION['success_edit_contest_list'].'</span>';
			unset($_SESSION['success_edit_contest_list']);
		}
		echo '</form></div>';
	}

?>