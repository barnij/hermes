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

		echo 'Wyślij swoje rozwiązanie zadania: <span style="font-weight: bold;">'.$id_task.'</span><br/><br/>';

		echo '
		<form action="/functions/submit.php" method="POST" ENCTYPE="multipart/form-data">
		   <input type="hidden" name="id_contest" value="'.$_GET['id'].'"/>
		   <input type="hidden" name="id_task" value="'.$id_task.'"/>
		   Wybierz język: <select name="lang">
		   		<option>C++ (g++ 4.7)</option>
		   		<option>Python 3.6</option>
		   		<option>RAM Machine</option>
		   		<option>BAP</option>
		   </select><br/><br/>
		   Wybierz plik: <span style="font-style: italic;">(Pierwszeństwo przesyłu)</span><br/>
		   <input type="file" name="plik"/><br/><br/>
		   lub wklej kod poniżej:<br/>
		   <textarea name="code" rows="10" cols="48" placeholder="Sprawdzanie kodu poprzez wklejenie jest chwilowo niedostępne."/></textarea><br/><br/>
		   <input type="submit" value="Wyślij rozwiązanie"/>';
		
		if(isset($_SESSION['e_file']))
		{
			echo $_SESSION['e_file'];
			unset($_SESSION['e_file']);
		}

		echo '</form>';
	}
 ?>