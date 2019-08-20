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

		$tresc = "SELECT id_contest, shortcut_contest, title_contest FROM contests ORDER BY ".$ws;

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

	}elseif((!isset($_GET['submits'])) && (!isset($_GET['ranking']))) //edycja i szczegóły wybranego contestu
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
			<td style=\"width: 50%;\">
				Identyfikator wybranych zawodów: <b>".$rezultat["shortcut_contest"]."</b>
			</td>
			<td style=\"width: 15%; text-align: center\">
				[ <a href=\"/admin/konsola.php?tool=list_contest&edit_contest=".$id_contest."&submits\">Wysłania</a> ]
			</td>
			<td style=\"width: 15%; text-align: center\">
				[ <a href=\"/admin/konsola.php?tool=list_contest&edit_contest=".$id_contest."&ranking\">Ranking</a> ]
			</td>
			<td style=\"width:20%; text-align: right;\">
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
				<table style="width: 680px; margin-top: 0;">
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
							<input style="background-color: #ef6262;" type="submit" name="TAKusuncontest" value="Usuń Zawody" onclick="'."return confirm('Czy na pewno chcesz to zrobić? Zostaną usunięte nadesłane rozwiązania i cała historia zawodów!');\"".'>
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
		<form method="post" action="functions/edit_contest_list.php">

		<p style="margin: 0 0 8px 0;">
		<input type="submit" value="Zapisz zestaw zadań">';
		if(isset($_SESSION['success_edit_contest_list']))
			echo '<span style="padding-left: 10px; color: green;">'.$_SESSION['success_edit_contest_list'].'</span>';
		echo '</p>

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
			<p style="margin-top: 8px; margin-bottom: 5px;">Dodane zadania:</br></p>
			<table style="width: 700px; border-spacing:0 10px;">';

		$tresc = "SELECT contest_list.id_task AS id_task, tasks.title_task AS title_task, tasks.difficulty AS difficulty, tasks.pdf, tasks.sum FROM tasks, contest_list WHERE contest_list.id_contest='$id_contest' AND contest_list.id_task=tasks.id_task GROUP BY contest_list.id_task ORDER BY ".$ws;
		$zapytanie = $polaczenie->query($tresc);

		while($row = mysqli_fetch_row($zapytanie))
		{
			$sciezka = $_SERVER['DOCUMENT_ROOT'].'/tasks/'.$row[0].'/conf.txt';

			echo '<tr>';

			if(file_exists($sciezka))
			{
				echo '<td style="font-weight: bold; width: '.$sz1.'px;">
						<label for="'.$row[0].'">'.
							$row[0]
						.'</label></td>
						<td style="width: '.$sz2.'px;">
						<label for="'.$row[0].'">'.
							$row[1].' <span style="font-style: italic; padding-left: 5px;" title="Suma punktów">('.$row['4'].')</span>'
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
			}else
			{
				echo '<td style="width: 700px; color: red;">Brak pliku konfiguracyjnego zadania <b>'.$row[0].'</b>!</td>';
			}

			echo '</tr>';
		}
		echo '</table>
		<p style="margin-top: 5px; margin-bottom: 5px;">Pozostałe zadania:</br></p>
		<table style="width: 700px; border-spacing:0 10px;">';

		$tresc = "SELECT id_task, title_task, difficulty, pdf, sum FROM tasks WHERE NOT EXISTS (SELECT NULL FROM contest_list WHERE contest_list.id_task = tasks.id_task AND id_contest = '$id_contest') ORDER BY ".$ws;
		$zapytanie = $polaczenie->query($tresc);

		while($row = mysqli_fetch_row($zapytanie))
		{
			$sciezka = $_SERVER['DOCUMENT_ROOT'].'/tasks/'.$row[0].'/conf.txt';

			echo '<tr>';

			if(file_exists($sciezka))
			{
				echo '<td style="font-weight: bold; width: '.$sz1.'px;">
						<label for="'.$row[0].'">'.
							$row[0]
						.'</label></td>
						<td style="width: '.$sz2.'px;">
						<label for="'.$row[0].'">'.
							$row[1].' <span style="font-style: italic; padding-left: 5px;" title="Suma punktów">('.$row['4'].')</span>'
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

			}else
			{
				echo '<td style="width: 700px; color: red;">Brak pliku konfiguracyjnego zadania <b>'.$row[0].'</b>!</td>';
			}

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

	}elseif(isset($_GET['submits'])) //lista wysłań
	{
		require_once($_SERVER['DOCUMENT_ROOT'].'/functions/echomodal.php');

		$tresc="SELECT * from contests WHERE id_contest=".$_GET['edit_contest'];
		$zapytanie = $polaczenie -> query($tresc);
		$rezultat = $zapytanie->fetch_assoc();

		echo "<table style=\"width: 700px;\">
			<tr>
			<td style=\"width: 50%;\">
				Identyfikator wybranych zawodów: <b>".$rezultat["shortcut_contest"]."</b>
			</td>
			<td style=\"width: 15%; text-align: center\">
				[ [<a href=\"/admin/konsola.php?tool=list_contest&edit_contest=".$id_contest."&submits\">Wysłania</a>] ]
			</td>
			<td style=\"width: 15%; text-align: center\">
				[ <a href=\"/admin/konsola.php?tool=list_contest&edit_contest=".$id_contest."&ranking\">Ranking</a> ]
			</td>
			<td style=\"width:20%; text-align: right;\">
				[ <a href=\"/admin/konsola.php?tool=list_contest&edit_contest=".$id_contest."\">Wróć do edycji</a> ]
			</td>
			</tr>
		</table>";

		if(!isset($_GET['nr']))
			$nr_strony=0;
		else
			$nr_strony=$_GET['nr'];

		$rekordownastronie = 30;
		$pominieterekordy = $nr_strony*$rekordownastronie;


		$zapytanie=$polaczenie->query("SELECT tasks.id_task,tasks.title_task,submits.time, submits.status,submits.id_submit, submits.id_user FROM tasks, submits WHERE tasks.id_task=submits.id_task AND submits.id_contest='$id_contest' ORDER BY submits.id_submit DESC");


		$wszystkierekordy = mysqli_num_rows($zapytanie);

		$zapytanie=$polaczenie->query("SELECT tasks.id_task,tasks.title_task,submits.time, submits.status,submits.id_submit, submits.id_user, users.name, tasks.pdf FROM tasks, submits, users WHERE tasks.id_task=submits.id_task AND submits.id_contest='$id_contest' AND submits.id_user=users.id_user ORDER BY submits.id_submit DESC LIMIT $pominieterekordy, $rekordownastronie");

		$maxstron = floor($wszystkierekordy/$rekordownastronie)-1;

		if($wszystkierekordy%$rekordownastronie!=0)
			$maxstron=$maxstron+1;

		//-------------- wybor strony ------------------

		echo '<table width="720"">
		<tr>
		<th width="40" style="padding-bottom: 10px; text-align: left;">';

		if($nr_strony>0)
			echo '<a href="/admin/konsola.php?tool=list_contest&edit_contest='.$id_contest.'&submits&nr='.($nr_strony-1).'" style="text-decoration: none; color: black; font-weight: bold;">←</a>';

		echo '</th>
		<th width="640" style="text-align: center;"></th>
		<th width="40" style="padding-bottom: 10px; text-align: right;">';

		if($nr_strony<$maxstron)
			echo '<a href="/admin/konsola.php?tool=list_contest&edit_contest='.$id_contest.'&submits&nr='.($nr_strony+1).'" style="text-decoration: none; color: black; font-weight: bold;">→</a>';

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
			$if_pdf = $row[7];

			echo '	<td width="'.$sz1.'" align="center" style="line-height: 32px;">
				<a class="nolink" href="/admin/konsola.php?tool=list_task&edit_task='.$id_task.'" >'.$id_task.'</a>
			</td>
			<td width="'.$sz2.'" align="center" >';
			if($if_pdf==1)
				echo '<a class="nolink" href="/tasks/'.$id_task.'/'.$id_task.'.pdf" target="_blank">'.$name_task.'</a>';
			else
				echo '<a class="nolink" href="/admin/functions/view_task.php?task='.$id_task.'" target="_blank">'.$name_task.'</a>';
			echo '</td>
			<td width="'.$sz3.'" align="center" >
				<a class="nolink" href="/admin/functions/showusercode.php?submit='.$id_submit.'" target="_blank">'.$time.'</a>
			</td>
			<td width="'.$sz4.'" align="center" >
				<a class="nolink" href="/admin/konsola.php?tool=list_users&user='.$id_user_submit.'">'.$name_user.'</td>
			<td width="'.$sz5.'" align="center" >';
			//sprawdzenie statusu:
			if($status==0)
				echo '<img src="/images/loading.gif" width="20px" height="20px" style="padding-top: 5px;">';
			else if($status==1)
				echo '<a href="/results/'.$id_submit.'" onclick="openmodal(1);" style="color: green; font-weight: bold; text-decoration: none;" target="iframemodal">OK</a>';
			elseif($status==2)
				echo '<a href="/results/'.$id_submit.'" onclick="openmodal(2);" style="color: red; font-weight: bold; text-decoration: none;" target="iframemodal">ERR</a>';
			elseif($status==3)
				echo '<a href="/results/'.$id_submit.'" onclick="openmodal(3);" style="color: #7c0b0b; font-weight: bold; text-decoration: none;" target="iframemodal">CPE</a>';
			elseif($status==4)
				echo '<a href="/results/'.$id_submit.'" onclick="openmodal(4);" style="color: #ffe900; font-weight: bold; text-decoration: none; text-shadow: 1px 1px black;" target="iframemodal">TLE</a>';
			elseif($status==5)
				echo '<a href="/results/'.$id_submit.'" onclick="openmodal(5);" style="color: #2800ad; font-weight: bold; text-decoration: none;" target="iframemodal">SEG</a>';

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
			echo '<a href="/admin/konsola.php?tool=list_contest&edit_contest='.$id_contest.'&submits&nr='.($nr_strony-1).'" style="text-decoration: none; color: black; font-weight: bold;">←</a>';

		echo '</th>
		<th width="640" style="text-align: center;"></th>
		<th width="40" style="padding-top: 5px; text-align: right;">';

		if($nr_strony<$maxstron)
			echo '<a href="/admin/konsola.php?tool=list_contest&edit_contest='.$id_contest.'&submits&nr='.($nr_strony+1).'" style="text-decoration: none; color: black; font-weight: bold;">→</a>';

		echo '</th>
		</tr>
		</table>';

		//----------------------------------------------

	}elseif(isset($_GET['ranking'])) //ranking
	{
		$tresc="SELECT * from contests WHERE id_contest=".$_GET['edit_contest'];
		$zapytanie = $polaczenie -> query($tresc);
		$rezultat = $zapytanie->fetch_assoc();
		$start_time_contest = $rezultat['time_from'];
		$end_time_contest = $rezultat['time_to'];

		echo "<table style=\"width: 700px;\">
			<tr>
			<td style=\"width: 50%;\">
				Identyfikator wybranych zawodów: <b>".$rezultat["shortcut_contest"]."</b>
			</td>
			<td style=\"width: 15%; text-align: center\">
				[ <a href=\"/admin/konsola.php?tool=list_contest&edit_contest=".$id_contest."&submits\">Wysłania</a> ]
			</td>
			<td style=\"width: 15%; text-align: center\">
				[ [<a href=\"/admin/konsola.php?tool=list_contest&edit_contest=".$id_contest."&ranking\">Ranking</a>] ]
			</td>
			<td style=\"width:20%; text-align: right;\">
				[ <a href=\"/admin/konsola.php?tool=list_contest&edit_contest=".$id_contest."\">Wróć do edycji</a> ]
			</td>
			</tr>
		</table>";

		$zapytanie = $polaczenie->query("SELECT users.name, SUM(sub.points) AS sumapunktow, SUM(CASE sub.status WHEN 1 THEN 1 ELSE 0 END) AS sumaok, SUM(CASE sub.points WHEN 0 THEN 0 ELSE HOUR(TIMEDIFF(sub.time, '$start_time_contest'))*60+MINUTE(TIMEDIFF(sub.time, '$start_time_contest')) END) AS czas FROM (SELECT submits.* FROM (SELECT id_user, id_contest, id_task, MAX(points) AS points FROM submits GROUP BY id_user, id_contest, id_task) tt INNER JOIN submits ON tt.id_user=submits.id_user AND tt.id_contest=submits.id_contest AND tt.id_task=submits.id_task AND tt.points=submits.points) sub, users WHERE users.id_user=sub.id_user AND sub.id_contest='$id_contest' AND TIMEDIFF('$end_time_contest',sub.time)>0 GROUP BY sub.id_user ORDER BY sumapunktow DESC, sumaok ASC, czas ASC");

		$atrybutynaglowka = 'align="center" bgcolor="e5e5e5"';
		$sz1 = 50;
		$sz2 = 360;
		$sz3 = 90;
		$sz4 = 70;
		$sz5 = 150;

		echo '
		<table width="720" align="left" border="1" bordercolor="#d5d5d5" cellpadding="0" cellspacing="0" style="margin-top: 20px;">
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
	}

?>