<?php
    if(!isset($id_admin))
	{
		header('Location: /');
		exit();
    }
?>

<form method="post" action="functions/add_task.php" enctype="multipart/form-data">
	<label for="id_task">Podaj identyfikator zadania: </label>
	<input type="text" name="id_task" style="width: 60px; margin-left: 10px;" required><br/><br/>
	<label for="title_task">Podaj nazwę zadania:</label><br/>
	<input type="text" name="title_task" style="width: 450px;" required><br/><br/>
	<label for="tresc">Wybierz treść zadania (pdf/txt): </label>
	<input type="file" name="tresc" required><br/>
	<a style="font-style: italic; text-decoration: none;" href="files/szablon.tex">Pobierz szablon</a>
	<br/><br/>
	<table style="width: 700px; height: 60px;">
	<tr style="vertical-align: top;">
		<td style="border: 1px solid black; width:50%; padding: 15px;">
			<input id="recznie" type="radio" name="typeofouts" value="recznie" required checked><label for="recznie"> Wrzuć testy ręcznie:</label><br/><br/>
			<p style="font-style: italic; text-align: center; margin: 0; padding: 0;">Wszystkie pliki muszą być ponumerowane kolejnymi liczbami naturalnymi.</p><br/>
			<label for="iny[]">Wybierz "iny":</label><br/>
			<input type="file" name="iny[]" multiple="multiple"/><br/><br/>
			<label for="outy[]">Wybierz "outy":</label><br/>
			<input type="file" name="outy[]" multiple="multiple"/><br/>
		</td>
		<td style="border: 1px solid black; width: 50%; padding: 15px;">
			<!--<input id="automatycznie" type="radio" name="typeofouts" value="automatycznie">--><label for="automatycznie"> Wygeneruj "outy" automatycznie:</label><br/><br/>
			<label for="iny1[]">Wybierz "iny":</label><br/>
			<!--<input type="file" name="iny1[]" multiple="multiple">--><br/><br/>
			<label for="wzorcowka">Wybierz plik wykonywalny<br/>programu wzorcowego:</label><br/>
			<!--<input type="file" name="wzorcowka">--><br/>
		</td>
	</tr>
	</table><br/>
	<label for="trudnosc">Wybierz trudność zadania:</label>

	<script>
		//$('#rangeText').text($('#RangeTrudnosc').val());
		document.onload = trudnosc();

		function trudnosc(){
			document.getElementById("rangeText").innerHTML = "75";
		}
	</script>

	<input type="range" id="RangeTrudnosc" name="trudnosc" min="0" max="10" step="1" oninput="document.getElementById('rangeValLabel').innerHTML = this.value;"> <em id="rangeValLabel" style="font-style: normal; font-weight: bold">5</em>
	<br/><br/>

	<label for="timelimit">Określ limity: </label><input type="text" name="timelimit" style="margin-left: 10px; width: 40px; text-align: right;"> s
	<input type="text" name="memorylimit" style="margin-left: 20px; width: 40px; text-align: right;"> MB
	<br/><br/>

	<input type="submit" value="Wyślij zadanie">

</form>
