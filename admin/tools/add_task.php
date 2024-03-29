<?php
    if(!isset($id_admin))
	{
		header('Location: /');
		exit();
    }

?>

<form method="post" action="functions/add_task_pack.php" enctype="multipart/form-data">
	<table style="width: 680px; padding: 5px; border: 1px solid black;">
	<tr>
	<td>
		<span>Możesz także dodać zadania przez wysłanie paczek</span><br/>
		Tu możesz pobrać przykładową paczkę z wyjaśnieniem pliku set.txt: <a style="font-style: italic; text-decoration: none;" href="files/ID.zip">Pobierz</a><br/><br/>
		<label for="pack[]">Wybierz paczki do dodania: </label>
		<input type="file" name="pack[]" multiple="multiple" accept=".zip" required/><br/>
		<?php
			if(isset($_SESSION['e_pack']))
			{
				echo ' <span class="error">'.$_SESSION['e_pack'].'</span><br/>';
				unset($_SESSION['e_pack']);
			}
		?>
		<br/>
		<input type="hidden" name="pack_true" value="1">
		<input type="submit" value="Dodaj zadanie/a">
		<?php
		if(isset($_SESSION['success_pack']))
		{
			echo $_SESSION['success_pack'];
			unset($_SESSION['success_pack']);
		}
	?>
	</td>
	</tr>
	</table><br/>
</form>

<form method="post" action="functions/add_task.php" enctype="multipart/form-data">
	<label for="id_task">Podaj identyfikator zadania: </label>
	<input type="text" name="id_task" style="width: 60px; margin-left: 10px;" required>
	<?php
		if(isset($_SESSION['e_id_task']))
		{
			echo ' <span class="error" style="padding-left: 15px;">'.$_SESSION['e_id_task'].'</span>';
			unset($_SESSION['e_id_task']);
		}
	?><br/><br/>
	<label for="title_task">Podaj nazwę zadania:</label><br/>
	<input type="text" name="title_task" style="width: 450px;" required>
	<?php
		if(isset($_SESSION['e_title_task']))
		{
			echo ' <span class="error" style="padding-left: 15px;">'.$_SESSION['e_title_task'].'</span>';
			unset($_SESSION['e_title_task']);
		}
	?><br/><br/>
	<label for="tresc">Wybierz treść zadania (pdf/txt): </label>
	<input type="file" name="tresc" accept=".txt,.pdf" required><br/>
	Pobierz szablon: <a style="padding-left: 10px; font-style: italic; text-decoration: none;" href="files/szablon.tex">TEX</a>
	<a style="padding-left: 10px; font-style: italic; text-decoration: none;" href="files/szablon.txt" download>TXT</a>
	<?php
		if(isset($_SESSION['e_text_task']))
		{
			echo ' <span class="error" style="padding-left: 15px;">'.$_SESSION['e_text_task'].'</span>';
			unset($_SESSION['e_text_task']);
		}
	?>
	<br/><br/>
	<table style="width: 680px; padding: 5px; border: 1px solid black;">
	<tr>
	<td>
		<span style="font-style: italic; ">Wszystkie pliki muszą być ponumerowane od 0 kolejnymi liczbami naturalnymi.<br/>
		Rozszerzenie plików nie ma znaczenia.</span><br/><br/>
		<label for="iny[]">Wybierz testy wejściowe: </label>
		<?php
			if(isset($_SESSION['e_iny']))
			{
				echo ' <span class="error" style="padding-left: 15px;">'.$_SESSION['e_iny'].'</span>';
				unset($_SESSION['e_iny']);
			}
		?>
		<input type="file" name="iny[]" multiple="multiple" required/><br/><br/>
		<label for="outy[]">Wybierz pliki wynikowe:</label>
		<input type="file" name="outy[]" multiple="multiple" required/><br/>
		<?php
			if(isset($_SESSION['e_outy']))
			{
				echo ' <span class="error" style="padding-left: 15px;">'.$_SESSION['e_outy'].'</span>';
				unset($_SESSION['e_outy']);
			}
		?>
	</td>
	</tr>
	</table><br/>
	<label for="trudnosc">Wybierz trudność zadania:</label>
	<input type="range" id="RangeTrudnosc" name="trudnosc" min="0" max="10" step="1" oninput="document.getElementById('rangeValLabel').innerHTML = this.value;"> <em id="rangeValLabel" style="font-style: normal; font-weight: bold">5</em>
	<br/><br/>

	<label for="timelimit">Określ limity: </label><input type="text" name="timelimit" style="margin-left: 10px; width: 40px; text-align: right;" required> s
	<input type="text" name="memorylimit" style="margin-left: 20px; width: 40px; text-align: right;" required> MB
	<?php
		if(isset($_SESSION['e_limit']))
		{
			echo ' <span class="error" style="padding-left: 15px;">'.$_SESSION['e_limit'].'</span>';
			unset($_SESSION['e_limit']);
		}
	?>
	<br/><br/>
	<label for="startpoints">Wpisz ilość punktów za zadanie:</label>
	<input type="text" name="startpoints" style="margin-left: 5px; width: 40px;" required>
	<?php
		if(isset($_SESSION['e_startpoints']))
		{
			echo ' <span class="error" style="padding-left: 15px;">'.$_SESSION['e_startpoints'].'</span>';
			unset($_SESSION['e_startpoints']);
		}
	?>
	<br/>
	<span style="font-style: italic;">Zalecana wartość 100. 1 oznacza zadanie oceniane binarnie.</span>
	<br/><br/>

	<input type="submit" value="Wyślij zadanie">
	<?php
		if(isset($_SESSION['success_add_task']))
		{
			echo $_SESSION['success_add_task'];
			unset($_SESSION['success_add_task']);
		}
	?>

</form>
