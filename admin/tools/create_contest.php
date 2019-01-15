<?php
	if(!isset($id_admin))
	{
		header('Location: /');
		exit();
	}
?>

<form method="post" action="functions/create_contest.php">
	<label for="shortcut_new_contest">Podaj identyfikator zawodów:</label>
	<input type="text" name="shortcut_new_contest" style="width: 60px; margin-left: 10px;" autocomplete="off" required>

	<?php
		if(isset($_SESSION['e_shortcut']))
		{
			echo '<span class="error" style="padding-left: 10px;">'.$_SESSION['e_shortcut'].'</span>';
			unset($_SESSION['e_shortcut']);
		}
	?>
	<br/><br/>

	<label for="title_new_contest">Podaj nazwę zawodów:</label><br/>
	<input type="text" name="title_new_contest" style="width: 450px;" required>

	<?php
		if(isset($_SESSION['e_title']))
		{
			echo '<span class="error" style="padding-left: 10px;">'.$_SESSION['e_title'].'</span>';
			unset($_SESSION['e_title']);
		}
	?>
	<br/><br/>

	<label for="start_new_contest">Podaj czas rozpoczęcia: </label>
	<input type="text" name="start_new_contest" placeholder="rrrr-mm-dd hh:ii:ss" <?php echo 'value="'.date('Y-m-d H:i:s').'"'; ?> required>

	<?php
		if(isset($_SESSION['e_date1']))
		{
			echo '<span class="error" style="padding-left: 10px;">'.$_SESSION['e_date1'].'</span>';
			unset($_SESSION['e_date1']);
		}
	?>
	<br/><br/>

	<label for="end_new_contest">Podaj czas zakończenia: </label>
	<input type="text" name="end_new_contest" placeholder="rrrr-mm-dd hh:ii:ss" required>

	<?php
		if(isset($_SESSION['e_date']))
		{
			echo '<span class="error" style="padding-left: 10px;">'.$_SESSION['e_date'].'</span>';
			unset($_SESSION['e_date']);
		}
	?>
	<br/><br/>
	
	<label for="password_new_contest">Wpisz hasło by zabezpieczyć nim zawody: </label>
	<input type="password" name="password_new_contest" style="width: 200px; margin-left: 10px;"><br/><br/>
	<label for="visibility_new_contest">Czy zawody mają być widoczne na stronie głównej?</label>
	<input type="checkbox" name="visibility_new_contest" id="visibility_new_contest"><br/><br/>
	<label for="timer_new_contest">Czy pokazywać licznik czasu?</label>
	<input type="checkbox" name="timer_new_contest" id="timer_new_contest"><br/><br/><br/>

	<input type="submit" value="Utwórz zawody">

	<?php
		if(isset($_SESSION['create_contest_success']))
		{
			echo '<span style="padding-left: 10px; color: green;">'.$_SESSION['create_contest_success'].'</span>';
			unset($_SESSION['create_contest_success']);
		}
	?>

</form>