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

	}else //szczegóły wybranego użytkownika
	{
        
    }

?>