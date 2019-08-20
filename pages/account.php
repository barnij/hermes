<?php

	if((!isset($_SESSION['zalogowany'])) || ($_SESSION['zalogowany']==false))
	{
		header('Location: /');
		exit();
	}

	if(isset($_POST['nazwakonta']) || isset($_POST['email']))
	{
		$DanePoprawne1 = true;

		$nazwakonta = $_POST['nazwakonta'];

		if ((strlen($nazwakonta)<1) || (strlen($nazwakonta)>40)) //długość nazwy konta od 1 do 40 znaków
		{
			$DanePoprawne1=false;
			$_SESSION['e_nazwakonta']='<span class="error">Nazwa musi mieć od 1 do 40 znaków!</span><br/><br/>';
		}

		$font = $_SERVER['DOCUMENT_ROOT']."/font/times.ttf";

		$words = explode(" ", $nazwakonta);

		foreach ($words as $w)
		{
			list($left,, $right) = imagettfbbox( 16, 0, $font, $w);

			if(($right - $left)>160)
	  		{
	  			$DanePoprawne1=false;
				$_SESSION['e_nazwakonta']='<span class="error">Pojedyncze słowa są za długie!</span><br/><br/>';
	  		}
		}

		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		if (!(filter_var($emailB, FILTER_VALIDATE_EMAIL)) || ($emailB!=$email)) //czy email poprawny
		{
			$DanePoprawne1=false;
			$_SESSION['e_email']='<span class="error">Podaj poprawny adres email!</span><br/><br/>';
		}

		if($DanePoprawne1==true)
		{

			if($polaczenie->query("UPDATE users SET name='$nazwakonta', email='$email' WHERE id_user='$id_user'"))
			{
				if(isset($_SESSION['e_nazwakonta'])) unset($_SESSION['e_nazwakonta']);
				if(isset($_SESSION['e_email'])) unset($_SESSION['e_email']);
				$_SESSION['user_email'] = $email;
				$_SESSION['name'] = $nazwakonta;
				$_SESSION['savedalert'] = '<span style="color: green; padding-left: 10px;">Zapisano.</span>';
			}else
			{
				echo "Error: ".$polaczenie->connect_errno;
			}
		}
	}

	if(isset($_POST['newpassword']))
	{
		$DanePoprawne2 = true;

		$zapytanie = $polaczenie->query("SELECT password FROM users WHERE id_user='$id_user'");
		$rezultat = $zapytanie->fetch_assoc();
		$passfrombase = $rezultat['password'];

		$password = $_POST['oldpassword'];

		if(!(password_verify($password,$passfrombase)))
		{
			$DanePoprawne2 = false;
			$_SESSION['e_haslo']='<span class="error">Niepoprawne hasło!</span><br/><br/>';
		}

		$newpassword = $_POST['newpassword'];
		$newpasswordrepeat = $_POST['newpasswordrepeat'];

		if ($newpassword!=$newpasswordrepeat)
		{
			$DanePoprawne2=false;
			$_SESSION['e_haslo2']='<span class="error">Hasła nie są identyczne!</span><br/><br/>';
		}

		$haslo_hash = password_hash($newpassword, PASSWORD_DEFAULT);

		if($DanePoprawne2==true)
		{
			if($polaczenie->query("UPDATE users SET password='$haslo_hash' WHERE id_user='$id_user'"))
			{
				if(isset($_SESSION['e_haslo'])) unset($_SESSION['e_haslo']);
				if(isset($_SESSION['e_haslo2'])) unset($_SESSION['e_haslo2']);
				$_SESSION['newpasswordalert'] = '<span style="color: green; padding-left: 10px;">Hasło zostało zmienione.</span>';
			}else
			{
				echo "Error: ".$polaczenie->connect_errno;
			}
		}

	}


	echo '
	<div style="float: left; width: 300px; margin-right: 20px; min-height: 200px;">
		Tu możesz edytować dane swojego konta:<br/><br/>
		<form method="post" style="padding-left: 20px;">
			<label for="nazwakonta">Edytuj nazwę:</label><br/>
			<input type="text" name="nazwakonta" value="'.$_SESSION['name'].'"  style="width: 250px;"><br/><br/>';

			if (isset($_SESSION['e_nazwakonta']))
			{
				echo $_SESSION['e_nazwakonta'];
				unset($_SESSION['e_nazwakonta']);
			}

	echo	'<label for="email">Edytuj email:</label><br/>
			<input type="text" name="email" value="'.$_SESSION['user_email'].'" style="width: 250px;"><br/><br/>';

			if (isset($_SESSION['e_email']))
			{
				echo $_SESSION['e_email'];
				unset($_SESSION['e_email']);
			}

	echo	'<input type="submit" value="Zapisz">';

			if(isset($_SESSION['savedalert']))
			{
				echo $_SESSION['savedalert'];
				unset($_SESSION['savedalert']);
			}

	echo '</form>
	</div>

	<div style="float: left; width: 300px; margin-left: 40px; min-height: 200px;">
		Zmień hasło:<br/><br/>
		<form method="post" style="padding-left: 20px;">
			<label for="oldpassword">Aktualne hasło:</label><br/>
			<input type="password" name="oldpassword" style="width: 250px;" required><br/><br/>';

			if (isset($_SESSION['e_haslo']))
			{
				echo $_SESSION['e_haslo'];
				unset($_SESSION['e_haslo']);
			}

	echo	'<label for="newpassword">Nowe hasło:</label><br/>
			<input type="password" name="newpassword" style="width: 250px;" required><br/><br/>
			<label for="newpasswordrepeat">Powtórz nowe hasło:</label><br/>
			<input type="password" name="newpasswordrepeat" style="width: 250px;" required><br/><br/>';

			if (isset($_SESSION['e_haslo2']))
			{
				echo $_SESSION['e_haslo2'];
				unset($_SESSION['e_haslo2']);
			}

	echo	'<input type="submit" value="Zapisz">
		';

			if(isset($_SESSION['newpasswordalert']))
			{
				echo $_SESSION['newpasswordalert'];
				unset($_SESSION['newpasswordalert']);
			}

	echo '</form>
	</div>
	<div style="clear: both;"></div>';

?>

