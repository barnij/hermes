<?php
	
	session_start();

	if ((!isset($_POST['login'])) || (!isset($_POST['password']))) 
	{
		header('Location: /');
		exit();
	}
	
	require_once "connect.php";

	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);


	if($polaczenie->connect_errno!=0)
	{
		echo "Error: ".$polaczenie->connect_errno;
	}
	else
	{
		mysqli_set_charset($polaczenie,"utf8");
		$polaczenie->query('SET NAMES utf8');
		$login = htmlentities($_POST['login'], ENT_QUOTES, "UTF-8");
		$haslo = $_POST['password'];

		
		if($rezultat = @$polaczenie->query(
		sprintf("SELECT * FROM users WHERE login='%s'",
		mysqli_real_escape_string($polaczenie, $login))))
		{
			if($rezultat->num_rows>0) //czy znaleziono w bazie
			{
				$wiersz = $rezultat->fetch_assoc();

				if(password_verify($haslo,$wiersz['password']))
				{
					$_SESSION['zalogowany'] = true;
					$_SESSION['id_user'] = $wiersz['id_user'];
					$_SESSION['login'] = $wiersz['login'];
					$_SESSION['name'] = $wiersz['name'];
					$_SESSION['user_email'] = $wiersz['email'];

					unset($_SESSION['blad']);
					$rezultat->free();
					header('Location: /contest');
				}
				else
				{
					$_SESSION['blad'] = '<span class="error" title="Jeśli nie pamiętasz swoich danych logowania,
skontaktuj się z administratorem." >Nieprawidłowy login lub hasło! &#9432;</span>';
					header('Location: /');
				}
			}
			else
			{
				$_SESSION['blad'] = '<span style="color:red" title="Jeśli nie pamiętasz swoich danych logowania,
skontaktuj się z administratorem." >Nieprawidłowy login lub hasło! &#9432;</span>';
				header('Location: /');
			}
		}

		$polaczenie->close();
	}
?>