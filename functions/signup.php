<?php
	session_start();

	if (isset($_SESSION['blad'])) {
		unset($_SESSION['blad']);
	}

	if (isset($_POST['login'])) //czy formularz już wysłany
	{
		$DanePoprawne = true;
		$login = $_POST['login'];

		if(strlen($login)>20) // długość loginu do 16 znaków
		{
			$DanePoprawne=false;
			$_SESSION['e_login']="Login musi zawierać do 16 znaków!<br/>";
		}

		if (!ctype_alnum(str_replace('_', '', $login))) //tylko znaki alfanumeryczne
		{
			$DanePoprawne=false;
			$_SESSION['e_login']="Dozwolone znaki a-z, A-Z, 0-9, _<br/>";
		}

		$nazwa = $_POST['nazwa'];

		if ((strlen($nazwa)<1) || (strlen($nazwa)>40)) //długość nazwy konta od 1 do 40 znaków
		{
			$DanePoprawne=false;
			$_SESSION['e_nazwa']='Nazwa musi mieć od 1 do 40 znaków!<br/>';
		}

		$font = "C:/xampp/htdocs/font/times.ttf"; //czy pojedyncze słowa w nazwie nie są za długie

		$words = explode(" ", $nazwa);

		foreach ($words as $w) 
		{
			list($left,, $right) = imagettfbbox( 16, 0, $font, $w);

			if(($right - $left)>160)
	  		{
	  			$DanePoprawne=false;
				$_SESSION['e_nazwa']='Pojedyńcze słowa są za długie!<br/>';
	  		}
		}

		$haslo1 = $_POST['haslo1'];
		$haslo2 = $_POST['haslo2'];

		if ($haslo1!=$haslo2)
		{
			$DanePoprawne=false;
			$_SESSION['e_haslo2']="Hasła nie są identyczne!<br/>";
		}

		$haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT); //hashowanie hasla

		if (!(isset($_POST['regulamin']))) //nie potwierdzono regulaminu
		{
			$DanePoprawne=false;
			$_SESSION['e_regulamin']="Potwierdź akceptację regulaminu!<br/>";
		}

		require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);

		try
		{
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			if($polaczenie->connect_errno!=0) //połączenie z DB nienawiązane
			{
				throw new Exception(mysqli_connect_errno());
			}
			else //połączenie nawiązane!
			{
				mysqli_set_charset($polaczenie,"utf8");
				$polaczenie->query('SET NAMES utf8');

				$rezultat = $polaczenie->query("SELECT id_user FROM users WHERE login='$login'");  //wybierz z DB rekordy z podanym loginem

				if (!$rezultat) throw new Exception($polaczenie->error);
					
				if($rezultat->num_rows>0) //BŁĄD - konto z takim loginem już istnieje w bazie!
				{
					$DanePoprawne=false;
					$_SESSION['e_login']="Konto z takim loginem już istnieje!<br/>";
				}										
			}

			if ($DanePoprawne) // Wszystkie dane poprawne HURRA!
			{
				
				if ($polaczenie->query("INSERT INTO users VALUES (NULL, '$login', '$nazwa', '$haslo_hash', '')")) //dodawanie rekordu do users
				{

					$_SESSION['udanarejestracja']=true;
					header('Location: /welcome');
					
				}
				else
				{
					throw new Exception($polaczenie->error);
				}

				$polaczenie->close();
			}
		}

		catch(Exception $e) //wyświetlanie błędu
		{
			echo '<span style="color: red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
			//echo '<br /> Informacja deweloperska: '.$e;
		}
	}
?>

<!DOCTYPE HTML>
<html lang="pl">
<?php
	include_once ('../templates/head.php');
?>
<body>
	<div id="container">
		<?php
			include_once ('../templates/top.php');
		?>
		<div id="center">
			<div style="height: 100px;"></div>
			<div style="width: 50%; text-align: left; margin: auto;">
				<form method="post">
				
					Podaj login: <br /><input type="text" name="login" required><br/>
					<?php //błąd nazwy
						if (isset($_SESSION['e_login'])) 
							{echo '  <span class="error">'.$_SESSION['e_login'].'</span>';
							unset($_SESSION['e_login']);}
					?><br/>
					
					Podaj nazwę: <span style="font-style: italic;">(Zalecamy własne imię i nazwisko)</span><br />
					<input type="text" name="nazwa" required><br/>
					<?php //błąd nazwy
						if (isset($_SESSION['e_nazwa'])) 
							{echo '  <span class="error">'.$_SESSION['e_nazwa'].'</span>';
							unset($_SESSION['e_nazwa']);}
					?><br/>
					
					Podaj hasło: <br />
					<input type="password" name="haslo1" required><br/>
					<?php //błąd nazwy
						if (isset($_SESSION['e_haslo'])) 
							{echo '  <span class="error">'.$_SESSION['e_haslo'].'</span>';
							unset($_SESSION['e_haslo']);}
					?><br/>

					Powtórz hasło: <br />
					<input type="password" name="haslo2" required><br/>
					<?php //błąd nazwy
						if (isset($_SESSION['e_haslo2'])) 
							{echo '  <span class="error">'.$_SESSION['e_haslo2'].'</span>';
							unset($_SESSION['e_haslo2']);}
					?><br/>

					<label>
						<input type="checkbox" name="regulamin"> Akceptuję regulamin<br/>
					</label>
					<?php //błąd nazwy
						if (isset($_SESSION['e_regulamin'])) 
							{echo '  <span class="error">'.$_SESSION['e_regulamin'].'</span>';
							unset($_SESSION['e_regulamin']);}
					?><br/>
				
				
					<input type="submit" value="Zarejestruj się">
				</form>
			</div>
		</div>
		<?php
			include_once ('../templates/footer.php');
		?>
	</div>
</body>
</html>