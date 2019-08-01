<?php
	if(!isset($id_admin))
	{
		header('Location: /');
		exit();
	}

	if(!isset($_GET['user'])) //wyświetlanie wszystkich użytkowników
	{
		echo '<p style="margin-top: 0px; margin-bottom: 5px;">Wybierz użytkownika, by zobaczyć szczegóły:</br></p>';

		if(!isset($_GET['sort']))
		{
			$sort = 0; //sortuj od najnowszych do najstarszych (domyślnie)
		}else
		{
			$sort = $_GET['sort'];
		}

		if($sort==0 || $sort>5 || $sort<0)
		{
			$ws = 'id_user DESC';
		}else if($sort==1)
		{
			$ws = 'id_user ASC';
		}else if($sort==2)
		{
			$ws = 'login ASC';
		}else if($sort==3)
		{
			$ws = 'login DESC';
		}else if($sort==4)
		{
			$ws = 'name ASC';
		}else //$sort==5
		{
			$ws = 'name DESC';
		}

		$tresc = "SELECT id_user, login, name FROM users ORDER BY ".$ws;

		$zapytanie = $polaczenie->query($tresc);

		$atrybuty = 'align="left"';
		$sz1 = '50px';
		$sz2 = '200px';
		$sz3 = '470px';

		echo '<table style="width: 720px;">
				<tr style="line-height: 30px;">
					<th style="width: '.$sz1.'; text-align: left;">';

					if($sort==0)
					{
						echo '<a class="nolink" href="?tool=list_users&sort=1">id ↓';
					}else if($sort==1)
					{
						echo '<a class="nolink" href="?tool=list_users&sort=0">id ↑';
					}else
					{
						echo '<a class="nolink" href="?tool=list_users&sort=0">id';
					}

					echo '</a></th>
					<th style="width: '.$sz2.'; text-align: left;">';

					if($sort==2)
					{
						echo '<a class="nolink" href="?tool=list_users&sort=3">login ↑';
					}else if($sort==3)
					{
						echo '<a class="nolink" href="?tool=list_users&sort=2">login ↓';
					}else
					{
						echo '<a class="nolink" href="?tool=list_users&sort=2">login';
					}


					echo '</a></th>
					<th style="width: '.$sz3.'; text-align: left;">';

					if($sort==4)
					{
						echo '<a class="nolink" href="?tool=list_users&sort=5">Nazwa użytkownika ↑';
					}else if($sort==5)
					{
						echo '<a class="nolink" href="?tool=list_users&sort=4">Nazwa użytkownika ↓';
					}else
					{
						echo '<a class="nolink" href="?tool=list_users&sort=4">Nazwa użytkownika';
					}


					echo '</a></th>
				</tr>';

		while($row = mysqli_fetch_row($zapytanie))
		{
			$id = $row[0];
			$login = $row[1];
			$name = $row[2];

			echo 	'<tr style="line-height: 22px;">
					<td style="width: '.$sz1.'; text-align: left;"><a class="nolink" href="?tool=list_users&user='.$id.'">'.$id.'</a></td>
					<td style="width: '.$sz2.'; text-align: left;"><a class="nolink" href="?tool=list_users&user='.$id.'">'.$login.'</a></td>
					<td style="width: '.$sz3.'; text-align: left;"><a class="nolink" href="?tool=list_users&user='.$id.'">'.$name.'</a></td>
				</tr>';

		}

		echo '</table>';

	}elseif(!isset($_GET['submits'])) //szczegóły wybranego użytkownika
	{
        echo "<table style=\"width: 700px;\">
			<tr>
			<td style=\"width: 40%;\"></td>
			<td style=\"width: 30%; text-align: center\">
				[ [<a href=\"/admin/konsola.php?tool=list_users&user=".$id_user."\">Informacje o użytkowniku</a>] ]
			</td>
			<td style=\"width: 30%; text-align: center\">
				[ <a href=\"/admin/konsola.php?tool=list_users&user=".$id_user."&submits\">Wysłania użytkownika</a> ]
			</td>
			</tr>
		</table>";

		echo '<br/>
		<div style="border: 1px solid black; width:650px; min-height: 70px; padding: 15px;">
			<form method="post" action="functions/manage_user.php">
				<label>Nazwa użytkownika:</label>
				<input type="text" style="width: 300px; margin-left: 10px;" name="username" value="'.$name_user.'">
				<input type="hidden" name="id_user" value="'.$id_user.'">
				<br/><br/>
				<input type="submit" value="Zapisz">';
				if(isset($_SESSION['e_username']))
				{
					echo $_SESSION['e_username'];
					unset($_SESSION['e_username']);
				}elseif(isset($_SESSION['success_change_username']))
				{
					echo '<span style="color: green; margin-left: 30px;">'.$_SESSION['success_change_username'].'</span>';
					unset($_SESSION['success_change_username']);
				}
			echo '
			</form>
		</div><br/>
		<div style="border: 1px solid black; width:650px; min-height: 70px; padding: 15px;">
			<form method="post" action="functions/manage_user.php">
				<input type="hidden" name="id_user" value="'.$id_user.'">
				<input type="submit" name="reset" value="Zresetuj hasło">';
				if(isset($_SESSION['success_change_pass']))
				{
					echo '<span style="color: green; margin-left: 20px;">'.$_SESSION['success_change_pass'].'</span>';
					unset($_SESSION['success_change_pass']);
				}
			echo '
			</form><br/>
			<input type="text" readonly placeholder="Tutaj pojawi się wygenerowane hasło. Skopiuj je i przekaż użytkownikowi." style="width: 500px;" ';
				if(isset($_SESSION['reset_pass']))
				{
					echo 'value="'.$_SESSION['reset_pass'].'"';
					unset($_SESSION['reset_pass']);
				}
			echo '>
		</div><br/>
		<span style="margin-left: 15px; padding-right: 10px; font-weight: bold;">E-mail użytkownika:</span>
		<input type="text" readonly ';
		if($email_user!='')
			echo 'value="'.$email_user.'"';
		else
			echo 'placeholder="Nie podano."';
		echo '>';


	}else //wysłania wybranego użytkownika
	{
		require_once($_SERVER['DOCUMENT_ROOT'].'/functions/echomodal.php');

		echo "<table style=\"width: 700px;\">
			<tr>
			<td style=\"width: 40%;\"></td>
			<td style=\"width: 30%; text-align: center\">
				[ <a href=\"/admin/konsola.php?tool=list_users&user=".$id_user."\">Informacje o użytkowniku</a> ]
			</td>
			<td style=\"width: 30%; text-align: center\">
				[ [<a href=\"/admin/konsola.php?tool=list_users&user=".$id_user."&submits\">Wysłania użytkownika</a>] ]
			</td>
			</tr>
		</table>";

		if(!isset($_GET['nr']))
			$nr_strony=0;
		else
			$nr_strony=$_GET['nr'];

		$rekordownastronie = 30;
		$pominieterekordy = $nr_strony*$rekordownastronie;

		$zapytanie = $polaczenie->query("SELECT tasks.id_task, tasks.title_task, submits.time, submits.status, submits.id_submit, submits.id_user FROM tasks, submits WHERE tasks.id_task=submits.id_task AND submits.id_user='$id_user' ORDER BY submits.id_submit");

		$wszystkierekordy = mysqli_num_rows($zapytanie);

		$zapytanie = $polaczenie->query("SELECT tasks.id_task, tasks.title_task, submits.time, submits.status, submits.id_submit, submits.id_user, users.name, tasks.pdf FROM tasks, submits, users WHERE tasks.id_task=submits.id_task AND submits.id_user='$id_user' AND submits.id_user=users.id_user ORDER BY submits.id_submit DESC LIMIT $pominieterekordy, $rekordownastronie");

		$maxstron = floor($wszystkierekordy/$rekordownastronie)-1;

		if($wszystkierekordy%$rekordownastronie!=0)
			$maxstron=$maxstron+1;

		//-------------- wybor strony ------------------

		echo '<table width="720"">
		<tr>
		<th width="40" style="padding-bottom: 10px; text-align: left;">';

		if($nr_strony>0)
			echo '<a href="/admin/konsola.php?tool=list_users&user='.$id_user.'&submits&nr='.($nr_strony-1).'" style="text-decoration: none; color: black; font-weight: bold;">←</a>';

		echo '</th>
		<th width="640" style="text-align: center;"></th>
		<th width="40" style="padding-bottom: 10px; text-align: right;">';

		if($nr_strony<$maxstron)
			echo '<a href="/admin/konsola.php?tool=list_users&user='.$id_user.'&submits&nr='.($nr_strony+1).'" style="text-decoration: none; color: black; font-weight: bold;">→</a>';

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
			<td width="'.$sz4.'" align="center" >'.$name_user.'</td>
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
			echo '<a href="/admin/konsola.php?tool=list_users&user='.$id_user.'&submits&nr='.($nr_strony-1).'" style="text-decoration: none; color: black; font-weight: bold;">←</a>';

		echo '</th>
		<th width="640" style="text-align: center;"></th>
		<th width="40" style="padding-top: 5px; text-align: right;">';

		if($nr_strony<$maxstron)
			echo '<a href="/admin/konsola.php?tool=list_users&user='.$id_user.'&submits&nr='.($nr_strony+1).'" style="text-decoration: none; color: black; font-weight: bold;">→</a>';

		echo '</th>
		</tr>
		</table>';

		//----------------------------------------------
	}

?>