<?php
	
	session_start();

	if ((!isset($_POST['adminlogin'])) || (!isset($_POST['adminpassword'])))
	{
		header('Location: /admin');
		exit();
	}
	
	require_once "../../functions/connect.php";

	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

	if($polaczenie->connect_errno!=0)
	{
		echo "Error: ".$polaczenie->connect_errno;
	}
	else
	{
		mysqli_set_charset($polaczenie,"utf8");
		$polaczenie->query('SET NAMES utf8');
		$login = htmlentities($_POST['adminlogin'], ENT_QUOTES, "UTF-8");
		$haslo = $_POST['adminpassword'];
		
		if($rezultat = @$polaczenie->query(
		sprintf("SELECT * FROM admins WHERE login='%s'",
		mysqli_real_escape_string($polaczenie, $login),mysqli_real_escape_string($polaczenie, $haslo))))
		{
			if($rezultat->num_rows>0) //czy znaleziono w bazie
			{
				$wiersz = $rezultat->fetch_assoc();

				if(password_verify($haslo,$wiersz['password']))
				{
					$_SESSION['zalogowanyadmin'] = true;
					$_SESSION['admin_name'] = $wiersz['name'];
					$_SESSION['id_admin'] = $wiersz['id_admin'];

					unset($_SESSION['blad']);
					$rezultat->free();
					header('Location: /admin/konsola.php');
				}
				else
				{
					$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
					header('Location: /admin');
				}				
				
			}
			else
			{
				$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
				header('Location: /admin');
			}
		}

		$polaczenie->close();
	}
?>