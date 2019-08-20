<?php

	session_start();

	if (!isset($_POST['password'])) 
	{
		header('Location: /');
		exit();
	}

	$shortcut = $_POST['shortcut'];
	header('Location: /'.$shortcut);

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

		$id_contest = $_POST['id_contest'];

		$zapytanie1 = $polaczenie->query("SELECT password FROM contests WHERE id_contest = '$id_contest'");
		$rezultat = $zapytanie1->fetch_assoc();
		$good_password = $rezultat['password'];

		if($_POST['password'] == $good_password)
		{
			$id_user = $_SESSION['id_user'];

			if ($polaczenie->query("INSERT INTO permissions VALUES (NULL, '$id_user', '$id_contest')")) //dodawanie rekordu do premissions
				{
					if(isset($_SESSION[$_SESSION['e_contest']]))
						unset($_SESSION['e_contest']);
				}
				else
				{
					throw new Exception($polaczenie->error);
				}
		}else
		{
			$_SESSION['e_contest'] = '<span class="error" >Nieprawidłowe hasło!</span><br/><br/>';
		}

	}

	$polaczenie->close();

?>