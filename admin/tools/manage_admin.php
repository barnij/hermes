<?php
    if(!isset($_SESSION['id_admin']))
	{
		header('Location: /');
		exit();
	}

	function foo(&$polaczenie, $i){
		$zapytanie = $polaczenie->query("SELECT id_admin, login, name, parent FROM admins WHERE parent='$i'");

		while($row = mysqli_fetch_row($zapytanie))
		{
			echo '<li style="padding-bottom: 10px;">'.$row[0].' - <b>'.$row[1].'</b> - '.$row[2].' - '.$row[3];
			echo '<button style="margin-left: 20px;" name="delete_admin" type="submit" value="'.$row[0].'"
					onclick="'."return confirm('Czy na pewno chcesz usunąć tego administratora?');".'">Usuń</button></li>';
			foo($polaczenie, $row[0]);
		}
	}

	if(isset($_POST['a_name']))
	{
		header('Location: /admin/konsola.php?tool=manage_admin');
		$DanePoprawne1 = true;
		$name = $_POST['a_name'];
		$font = $_SERVER['DOCUMENT_ROOT']."/font/times.ttf";

		list($left,, $right) = imagettfbbox( 16, 0, $font, $name);

		if(($right - $left)>235)
		{
			$DanePoprawne1=false;
			$_SESSION['e_a_name']='<span class="error">Nazwa jest za długa!</span>';
		}

		if($DanePoprawne1)
		{
			if($polaczenie->query("UPDATE admins SET name='$name' WHERE id_admin='$id_admin'"))
			{
				$_SESSION['admin_name'] = $name;
				$_SESSION['a_name_success']='<span style="color: green; padding-left: 10px;">Zapisano.</span>';
			}else{
				echo "Error: ".$polaczenie->connect_errno;
			}
		}
		exit();
	}


?>

Zmień swoją nazwę:<br/>
<div style="border: 1px solid black; width:600px; min-height: 30px; padding: 15px;">
	<form method="post">
		<label for="a_name">Nazwa administratora: </label>
		<input type="text" name="a_name" style="width: 150px; margin-left: 10px;" value="<?php echo $_SESSION['admin_name']; ?>" required>
		<?php
			if(isset($_SESSION['e_a_name']))
			{
				echo $_SESSION['e_a_name'];
				unset($_SESSION['e_a_name']);
			}
		?>
		<br/><br/>
		<input type="submit" value="Zapisz">
		<?php
			if(isset($_SESSION['a_name_success']))
			{
				echo $_SESSION['a_name_success'];
				unset($_SESSION['a_name_success']);
			}
		?>
	</form>
</div><br/>

Zmień swoje hasło:<br/>
<div style="border: 1px solid black; width:600px; min-height: 30px; padding: 15px;">
	<form method="post" action="functions/change_pass_admin.php">
		<label for="old_pass">Aktualne hasło: </label>
		<input type="password" name="old_pass" style="width: 150px; margin-left: 10px;" autocomplete="old-password" required>
		<?php
			if(isset($_SESSION['e_old_pass']))
			{
				echo ' <span class="error" style="padding-left: 15px;">'.$_SESSION['e_old_pass'].'</span>';
				unset($_SESSION['e_old_pass']);
			}
		?><br/><br/>
		<label for="new-pass">Nowe hasło: </label>
		<input type="password" name="new_pass" style="width: 150px; margin-left: 10px;" autocomplete="new-password" required><br/><br/>
		<label for="new-pass-repeat">Powtórz nowe hasło: </label>
		<input type="password" name="new_pass_repeat" style="width: 150px; margin-left: 10px;" autocomplete="new-password" required><br/><br/>
		<input type="submit" value="Zapisz">
		<?php
			if(isset($_SESSION['new_pass_success']))
			{
				echo $_SESSION['new_pass_success'];
				unset($_SESSION['new_pass_success']);
			}
		?>
	</form>
</div><br/>

Dodaj administratora:<br/>
<div style="border: 1px solid black; width:600px; min-height: 130px; padding: 15px;">
	<form method="post" action="functions/add_admin.php">
		<label for="na_login">Podaj nazwę nowego administratora: </label>
		<input type="text" name="na_name" style="width: 150px; margin-left: 10px;" required>
		<?php
			if(isset($_SESSION['e_na_name']))
			{
				echo ' <span class="error" style="padding-left: 15px;">'.$_SESSION['e_na_name'].'</span>';
				unset($_SESSION['e_na_name']);
			}
		?><br/><br/>
		<label for="na_login">Podaj login nowego administratora: </label>
		<input type="text" name="na_login" style="width: 150px; margin-left: 10px;"  autocomplete="username"  required>
		<?php
			if(isset($_SESSION['e_na_login']))
			{
				echo ' <br/><span class="error">'.$_SESSION['e_na_login'].'</span>';
				unset($_SESSION['e_na_login']);
			}
		?><br/><br/>
		<label for="na_pass">Podaj nowe hasło: </label>
		<input type="password" name="na_pass" style="width: 150px; margin-left: 10px;" autocomplete="new-password" required>
		<?php
			if(isset($_SESSION['e_na_pass']))
			{
				echo ' <span class="error" style="padding-left: 15px;">'.$_SESSION['e_na_pass'].'</span>';
				unset($_SESSION['e_na_pass']);
			}
		?><br/><br/>
		<label for="na_pass_repeat">Powtórz hasło: </label>
		<input type="password" name="na_pass_repeat" style="width: 150px; margin-left: 10px;" autocomplete="new-password" required>
		<br/><br/>
		<input type="hidden" name="parent" value="<?php echo $id_admin; ?>" required>

		<input type="submit" value="Zapisz">
		<?php
			if(isset($_SESSION['success_add_admin']))
			{
				echo $_SESSION['success_add_admin'];
				unset($_SESSION['success_add_admin']);
			}
		?>
	</form>
</div><br/>

Usuń administatora:<br/>
<div style="border: 1px solid black; width:630px; min-height: 50px;">
	<?php
		if(isset($_SESSION['success_remove_admin']))
		{
			echo '<br/><span style="color: green; margin-left: 30px;">'.$_SESSION['success_remove_admin'].'</span>';
			unset($_SESSION['success_remove_admin']);
		}
	?>
	<form method="post" action="functions/remove_admin.php">
	<ul>
	<li style="padding-bottom: 10px; font-weight: bold;">Id - login - nazwa - Id rodzica</li>
	<?php
		foo($polaczenie, $_SESSION['id_admin']);
	?>
	</ul>
	</form>
</div>
