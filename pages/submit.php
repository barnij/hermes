<?php

	if(!$AccessToContest)
	{
		header('Location: /'.$shortcut_contest);
	}else
	{
		if((!(isset($_GET['id']))) || (!(isset($_GET['task']))))
		{
			header('Location: /');
		}

		$id_task = $_GET['task'];
		$zapytanie = $polaczenie->query("SELECT id_task FROM contest_list WHERE id_contest='$id_contest' AND id_task='$id_task'");

    	if(mysqli_num_rows($zapytanie)==0) //brak tego zadania w danych zawodach
    	{
     		header('Location: /');
      	  	exit();
    	}

		$dzis = date("U");
		$expired = date("U", strtotime($end_time_contest));

		if($expired-$dzis <= 0 && !$submitafterend)
		{
			echo 'Wysyłanie zadań zostało zakończone.';
		}else
		{
			echo 'Wyślij swoje rozwiązanie zadania: <span style="font-weight: bold;">'.$id_task.'</span><br/><br/>';

			echo '
			<form action="/functions/submit.php" method="POST" ENCTYPE="multipart/form-data">
			<input type="hidden" name="id_contest" value="'.$_GET['id'].'"/>
			<input type="hidden" name="id_task" value="'.$id_task.'"/>
			Wybierz język: <select name="lang">
					<option>C++ (g++ 4.7)</option>
					<option disabled>Python 3.6</option>
					<option disabled>RAM Machine</option>
					<option disabled>BAP</option>
			</select><br/><br/>
			Wybierz plik: <span style="font-style: italic;">(Pierwszeństwo przesyłu)</span><br/>
			<input type="file" name="plik"/><br/><br/>
			lub wklej kod poniżej:<br/>
			<textarea name="code" rows="10" cols="70" wrap="off"/></textarea><br/><br/>
			<input type="submit" value="Wyślij rozwiązanie"/>';

			if(isset($_SESSION['e_file']))
			{
				echo $_SESSION['e_file'];
				unset($_SESSION['e_file']);
			}

			echo '</form>';
		}
	}
 ?>
