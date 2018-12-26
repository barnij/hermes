<?php
    if(!isset($id_admin))
	{
		header('Location: /');
		exit();
    }
?>

<form method="post" action="functions/add_task.php">
	<label for="id_task">Podaj identyfikator zadania: </label>
	<input type="text" name="id_task" style="width: 60px; margin-left: 10px;" required><br/><br/>
	<label for="title_task">Podaj nazwę zadania:</label><br/>
	<input type="text" name="title_task" style="width: 450px;" required><br/><br/>
	<label for="tresc">Wybierz treść zadania (pdf/txt): </label>
	<input type="file" name="tresc" required><br/>
	<a style="font-style: italic; text-decorate: none;" href="files/szablon.tex">Pobierz szablon</a>
	<br/><br/>
	<table style="width: 700px; height: 60px;">
	<tr style="vertical-align: top;">
		<td style="border: 1px solid black; width:50%; padding: 15px;">
			<input id="recznie" type="radio" name="typeofouts" value="recznie" required><label for="recznie"> Wrzuć testy ręcznie:</label><br/><br/>
			<p style="font-style: italic; text-align: center; margin: 0; padding: 0;">Wszystkie pliki muszą być ponumerowane kolejnymi liczbami naturalnymi.</p><br/>
			<label for="iny[]">Wybierz "iny":</label><br/>
			<input type="file" name="iny[]" required/><br/><br/>
			<label for="outy[]">Wybierz "outy":</label><br/>
			<input type="file" name="outy[]" required/><br/>
		</td>
		<td style="border: 1px solid black; width: 50%; padding: 15px;">
			<input id="automatycznie" type="radio" name="typeofouts" value="automatycznie"><label for="automatycznie"> Wygeneruj "outy" automatycznie:</label><br/>
		</td>
	</tr>
	</table>
</form>

