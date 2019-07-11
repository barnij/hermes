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
		require_once($_SERVER['DOCUMENT_ROOT'].'/functions/echomodal.php');

		require_once($_SERVER['DOCUMENT_ROOT'].'/functions/updatetaskstatus.php');

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

			$zapytanie=$polaczenie->query("SELECT tasks.id_task,tasks.title_task,submits.time, submits.status,submits.id_submit,tasks.pdf FROM tasks, submits WHERE tasks.id_task=submits.id_task AND submits.id_contest='$id_contest' AND submits.id_user = '$id_user' ORDER BY submits.id_submit DESC LIMIT $pominieterekordy, $rekordownastronie");

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
				$if_pdf=$row[5];

				$linkIDzapytanie = $polaczenie->query("SELECT id_task FROM contest_list WHERE id_contest='$id_contest' AND id_task='$id_task'");
				if(mysqli_num_rows($linkIDzapytanie)==0)
					$linkID = 0;
				else
					$linkID = 1;

				if($if_pdf==1)
					$placetask = "task";
				else
					$placetask = $_GET['id'];

				echo '	<td width="'.$sz1.'" align="center" style="line-height: 32px;">';
				if($linkID==0)
					echo $id_task;
				else
					echo '<a class="nolink" href="/'.$placetask.'/'.$id_task.'">'.$id_task.'</a>';
				echo '</td>
				<td width="'.$sz2.'" align="center" >'.$name_task.'</td>
				<td width="'.$sz3.'" align="center" >'.$time.'</td>
				<td width="'.$sz4.'" align="center" >'.'[ <a href="/submit/'.$id_submit.'" target="_blank">Otwórz</a> ]'.'</td>
				<td width="'.$sz5.'" align="center" >';
				//sprawdzenie statusu:
				if($status==0)
				{
					echo '<img src="/images/loading.gif" width="20px" height="20px" style="padding-top: 5px;">';

					updatetaskstatus($polaczenie, $id_submit);
				}
				elseif(!$showresults)
					echo '<span style="color: grey; font-weight: bold; text-decoration: none;">?</span>';
				elseif($status==1)
					echo '<a href="/results/'.$id_submit.'" style="color: green; font-weight: bold; text-decoration: none;" 
					target="iframemodal" onclick="openmodal(1)">OK</a>';
				elseif($status==2)
					echo '<a href="/results/'.$id_submit.'" style="color: red; font-weight: bold; text-decoration: none;" 
					target="iframemodal" onclick="openmodal(2)">ERR</a>';
				elseif($status==3)
					echo '<a href="/results/'.$id_submit.'" style="color: #7c0b0b; font-weight: bold; text-decoration: none;" 
					target="iframemodal" onclick="openmodal(3)">CPE</a>';
				elseif($status==4)
					echo '<a href="/results/'.$id_submit.'" style="color: #ffe900; font-weight: bold; text-decoration: none; text-shadow: 1px 1px black;" 
					target="iframemodal" onclick="openmodal(4)">TLE</a>';
				elseif($status==5)
					echo '<a href="/results/'.$id_submit.'" style="color: #2800ad; font-weight: bold; text-decoration: none;" 
					target="iframemodal" onclick="openmodal(5)">SEG</a>';

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

		}elseif($showresults) //submity wszystkich
		{
			if(isset($_GET['task']))
			{
				$task = $_GET['task'];
				$zapytanie=$polaczenie->query("SELECT tasks.id_task,tasks.title_task,submits.time, submits.status,submits.id_submit, submits.id_user FROM tasks, submits WHERE tasks.id_task=submits.id_task AND submits.id_contest='$id_contest' AND submits.id_task='$task' ORDER BY submits.id_submit DESC");
			}else
			{
				$zapytanie=$polaczenie->query("SELECT tasks.id_task,tasks.title_task,submits.time, submits.status,submits.id_submit, submits.id_user FROM tasks, submits WHERE tasks.id_task=submits.id_task AND submits.id_contest='$id_contest' ORDER BY submits.id_submit DESC");
			}

			$wszystkierekordy = mysqli_num_rows($zapytanie);

			if(isset($_GET['task']))
			{
				$zapytanie=$polaczenie->query("SELECT tasks.id_task,tasks.title_task,submits.time, submits.status,submits.id_submit, submits.id_user, users.name, tasks.pdf FROM tasks, submits, users WHERE tasks.id_task=submits.id_task AND submits.id_contest='$id_contest' AND 	submits.id_user=users.id_user AND submits.id_task='$task' ORDER BY submits.id_submit DESC LIMIT $pominieterekordy, $rekordownastronie");
			}else
			{
				$zapytanie=$polaczenie->query("SELECT tasks.id_task,tasks.title_task,submits.time, submits.status,submits.id_submit, submits.id_user, users.name, tasks.pdf FROM tasks, submits, users WHERE tasks.id_task=submits.id_task AND submits.id_contest='$id_contest' AND 	submits.id_user=users.id_user ORDER BY submits.id_submit  DESC LIMIT $pominieterekordy, $rekordownastronie");
			}
			$maxstron = floor($wszystkierekordy/$rekordownastronie)-1;

			if($wszystkierekordy%$rekordownastronie!=0)
				$maxstron=$maxstron+1;

			if(isset($_GET['task']))
				$submitstask = '/'.$_GET['task'];
			else
				$submitstask = '';

			//-------------- wybor strony ------------------

			echo '<table width="720"">
			<tr>
			<th width="40" style="padding-bottom: 10px; text-align: left;">';

			if($nr_strony>0)
				echo '<a href="/'.$shortcut_contest.$submitstask.'/submits/'.($nr_strony-1).'" style="text-decoration: none; color: black; font-weight: bold;">←</a>';

			echo '</th>
			<th width="640" style="text-align: center;"></th>
			<th width="40" style="padding-bottom: 10px; text-align: right;">';
			
			if($nr_strony<$maxstron)
				echo '<a href="/'.$shortcut_contest.$submitstask.'/submits/'.($nr_strony+1).'" style="text-decoration: none; color: black; font-weight: bold;">→</a>';

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

				$linkIDzapytanie = $polaczenie->query("SELECT id_task FROM contest_list WHERE id_contest='$id_contest' AND id_task='$id_task'");
				if(mysqli_num_rows($linkIDzapytanie)==0)
					$linkID = 0;
				else
					$linkID = 1;

				if($if_pdf==1)
					$placetask = "task";
				else
					$placetask = $_GET['id'];

				echo '	<td width="'.$sz1.'" align="center" style="line-height: 32px;">';
				if($linkID==0)
					echo $id_task;
				else
					echo '<a class="nolink" href="/'.$placetask.'/'.$id_task.'">'.$id_task.'</a>';
				echo '</td>
				<td width="'.$sz2.'" align="center" >'.$name_task.'</td>
				<td width="'.$sz3.'" align="center" >'; 
				if($id_user==$id_user_submit)
				{
					echo '<a class="nolink" href="/submit/'.$id_submit.'" target="_blank">'.$time.'</a>';
				}else
					echo $time;
				echo '</td>
				<td width="'.$sz4.'" align="center" >'.$name_user.'</td>';
				echo '<td width="'.$sz5.'" align="center" >';
				//sprawdzenie statusu:
				if($status==0)
				{
					echo '<img src="/images/loading.gif" width="20px" height="20px" style="padding-top: 5px;">';

					if($id_user==$id_user_submit) //czy rozpoczynać sprawdzanie zawartosci pliku rezultowego
					{
						updatetaskstatus($polaczenie, $id_submit);
					}
				}
				else if($status==1)
					echo '<a href="/results/'.$id_submit.'" style="color: green; font-weight: bold; text-decoration: none;" 
					target="iframemodal" onclick="openmodal(1)">OK</a>';
				elseif($status==2)
					echo '<a href="/results/'.$id_submit.'" style="color: red; font-weight: bold; text-decoration: none;" 
					target="iframemodal" onclick="openmodal(2)">ERR</a>';
				elseif($status==3)
					echo '<a href="/results/'.$id_submit.'" style="color: #7c0b0b; font-weight: bold; text-decoration: none;" 
					target="iframemodal" onclick="openmodal(3)">CPE</a>';
				elseif($status==4)
					echo '<a href="/results/'.$id_submit.'" style="color: #ffe900; font-weight: bold; text-decoration: none; text-shadow: 1px 1px black;" 
					target="iframemodal" onclick="openmodal(4)">TLE</a>';
				elseif($status==5)
					echo '<a href="/results/'.$id_submit.'" style="color: #2800ad; font-weight: bold; text-decoration: none;" 
					target="iframemodal" onclick="openmodal(5)">SEG</a>';


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
				echo '<a href="/'.$shortcut_contest.$submitstask.'/submits/'.($nr_strony-1).'" style="text-decoration: none; color: black; font-weight: bold;">←</a>';

			echo '</th>
			<th width="640" style="text-align: center;"></th>
			<th width="40" style="padding-top: 5px; text-align: right;">';
			
			if($nr_strony<$maxstron)
				echo '<a href="/'.$shortcut_contest.$submitstask.'/submits/'.($nr_strony+1).'" style="text-decoration: none; color: black; font-weight: bold;">→</a>';

			echo '</th>
			</tr>
			</table>';

			//----------------------------------------------

		}else
		{
			echo 'Wyniki są ukryte.';
		}
	}
?>