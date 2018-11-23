<?php
	if(!isset($id_admin))
	{
		header('Location: /');
		exit();
	}

	if(!isset($_GET['edit_contest']))
	{
		echo '<p style="margin-top: 0px; margin-bottom: 5px;">Wybierz zawody, by je edytować:</br></p>';

		if(!isset($_GET['sort']))
		{
			$sort = 0;
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
						echo '<a class="nolink" href="?tool=edit_contest&sort=1">sql ↓';
					}else if($sort==1)
					{
						echo '<a class="nolink" href="?tool=edit_contest&sort=0">sql ↑';
					}else
					{
						echo '<a class="nolink" href="?tool=edit_contest&sort=0">sql';
					}

																		echo '</a></th>
					<th style="width: '.$sz2.'; text-align: left;">';

					if($sort==2)
					{
						echo '<a class="nolink" href="?tool=edit_contest&sort=3">ID ↑';
					}else if($sort==3)
					{
						echo '<a class="nolink" href="?tool=edit_contest&sort=2">ID ↓';
					}else
					{
						echo '<a class="nolink" href="?tool=edit_contest&sort=2">ID';
					}


																		echo '</a></th>
					<th style="width: '.$sz3.'; text-align: left;">';

					if($sort==4)
					{
						echo '<a class="nolink" href="?tool=edit_contest&sort=5">Tytuł zawodów ↑';
					}else if($sort==5)
					{
						echo '<a class="nolink" href="?tool=edit_contest&sort=4">Tytuł zawodów ↓';
					}else
					{
						echo '<a class="nolink" href="?tool=edit_contest&sort=4">Tytuł zawodów';
					}


																		echo '</a></th>
				</tr>';

		while($row = mysqli_fetch_row($zapytanie))
		{
			$id = $row[0];
			$shortcut = $row[1];
			$title = $row[2];

			echo 	'<tr style="line-height: 22px;">
					<td style="width: '.$sz1.'; text-align: left;"><a class="nolink" href="?tool=edit_contest&edit_contest='.$id.'">'.$id.'</a></td>
					<td style="width: '.$sz2.'; text-align: left;"><a class="nolink" href="?tool=edit_contest&edit_contest='.$id.'">'.$shortcut.'</a></td>
					<td style="width: '.$sz3.'; text-align: left;"><a class="nolink" href="?tool=edit_contest&edit_contest='.$id.'">'.$title.'</a></td>
				</tr>';

		}

		echo '</table>';

	}else //edycja wybranego contesu
	{
		
		

	}

?>