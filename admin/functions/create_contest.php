<?php
	session_start();

	if ((!isset($_POST['shortcut_new_contest'])) || (!isset($_SESSION['zalogowanyadmin'])))
	{
		header('Location: /admin');
		exit();
	}

	header('Location: /admin/konsola.php?tool=create_contest');

	$DanePoprawne = true;

	$shortcut = $_POST['shortcut_new_contest'];

	$shortcut = strtoupper($shortcut);

	if (!ctype_alnum($shortcut)) //tylko znaki alfanumeryczne
	{
		$DanePoprawne=false;
		$_SESSION['e_shortcut']="Dozwolone znaki: A-Z, 0-9";
	}

	if(strlen($shortcut)>5 || strlen($shortcut)==0) //dlugosc skrotu od 1 do 5 znakow
	{
		$DanePoprawne=false;
		$_SESSION['e_shortcut']="ID powinno mieć od 1 do 5 znaków.";
	}

	$title = $_POST['title_new_contest'];

	$font = "C:/xampp/htdocs/font/times.ttf"; //czy nazwa nie jest za długa

	list($left,, $right) = imagettfbbox( 16, 0, $font, $title);

	if(($right - $left)>720)
	{
		$DanePoprawne=false;
		$_SESSION['e_title']='Nazwa jest za długa!';
	}

	$start = $_POST['start_new_contest'];

	$end = $_POST['end_new_contest'];
	
	if(strtotime($end)<strtotime($start))
	{
		$DanePoprawne=false;
		$_SESSION['e_date']='Data zakończenia musi być po dacie rozpoczęcia!';
	}

	function verifyDate($date)
	{
    	return (DateTime::createFromFormat('Y-m-d H:i:s', $date) !== false);
	}

	if(!verifyDate($start))
	{
		$DanePoprawne=false;
		$_SESSION['e_date1']='Nieprawidłowy format!';
	}

	if(!verifyDate($end))
	{
		$DanePoprawne=false;
		$_SESSION['e_date']='Nieprawidłowy format!';
	}

	$password=$_POST['password_new_contest'];

	if(isset($_POST['visibility_new_contest']))
	{
		$visibility = 1;
	}else
	{
		$visibility = 0;
	}

	if(isset($_POST['timer_new_contest']))
	{
		$timer = 1;
	}else
	{
		$timer = 0;
	}

	if(isset($_POST['showresults']))
	{
		$showresults = 1;
	}else
	{
		$showresults = 0;
	}

	if(isset($_POST['submitafterend']))
	{
		$submitafterend = 1;
	}else
	{
		$submitafterend = 0;
	}

	require_once "../../functions/connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);

		try
		{
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			if($polaczenie->connect_errno!=0) //połączenie z DB nienawiązane
			{
				$DanePoprawne=false;
				throw new Exception(mysqli_connect_errno());
			}
			else //połączenie nawiązane!
			{
				mysqli_set_charset($polaczenie,"utf8");
				$polaczenie->query('SET NAMES utf8');

				$rezultat = $polaczenie->query("SELECT shortcut_contest FROM contests WHERE shortcut_contest='$shortcut'");  //wybierz z DB rekordy z podanym ID zawodów

				if (!$rezultat) throw new Exception($polaczenie->error);
					
				if($rezultat->num_rows>0) //BŁĄD - zawody z takim ID już istnieją!
				{
					$DanePoprawne=false;
					$_SESSION['e_shortcut']="Zawody z takim ID już istnieją!";
				}										
			}

			if ($DanePoprawne) // Wszystkie dane poprawne HURRA!
			{
				
				if ($polaczenie->query("INSERT INTO contests VALUES (NULL, '$shortcut', '$title', '$password', '$start', '$end', '$timer', '$visibility', '$showresults', '$submitafterend')")) //dodawanie rekordu do contests
				{
					if(isset($_POST['editafteradd']))
					{
						$zapytanie = $polaczenie->query("SELECT id_contest FROM contests ORDER BY id_contest DESC LIMIT 1");
						$rezultat = $zapytanie->fetch_assoc();
						$_SESSION['create_contest_success_edit'] = $rezultat['id_contest'];
					}else
						$_SESSION['create_contest_success'] = 'Utworzono zawody.';
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
			echo '<br /> Informacja deweloperska: '.$e;
		}

?>