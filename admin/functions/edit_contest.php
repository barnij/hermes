<?php
	session_start();

	if ((!isset($_POST['title_contest'])) || (!isset($_SESSION['zalogowanyadmin'])))
	{
		header('Location: /admin');
		exit();
    }
    
    $id_contest = $_POST["id_contest"];

    $header = 'Location: /admin/konsola.php?tool=list_contest&edit_contest='.$id_contest;
	header($header);

	$DanePoprawne = true;

	$title = $_POST['title_contest'];

	$font = "C:/xampp/htdocs/font/times.ttf"; //czy pojedyncze słowa w nazwie nie są za długie

	list($left,, $right) = imagettfbbox( 16, 0, $font, $title);

	if(($right - $left)>720)
	{
		$DanePoprawne=false;
		$_SESSION['e_title']='Nazwa jest za długa!';
	}

	$start = $_POST['start_contest'];

	$end = $_POST['end_contest'];
	
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

	$password=$_POST['password_contest'];

	if(isset($_POST['visibility_contest']))
	{
		$visibility = 1;
	}else
	{
		$visibility = 0;
	}

	if(isset($_POST['timer_contest']))
	{
		$timer = 1;
	}else
	{
		$timer = 0;
    }
    
    echo $title."<br/>".$password."<br/>".$start."<br/>".$end."<br/>".$visibility."<br/>".$timer."<br/>".$id_contest;

	require_once "../../functions/connect.php";
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
			}

			if ($DanePoprawne) // Wszystkie dane poprawne HURRA!
			{
				if ($polaczenie->query("UPDATE contests SET title_contest = '$title', password='$password', time_from='$start', time_to='$end', timer='$timer', visibility='$visibility'  WHERE id_contest='$id_contest'")) //dodawanie rekordu do users
				{
					$_SESSION['edit_contest_success'] = 'Zapisano.';
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
			echo '<span style="color: red;">Błąd serwera! Skontaktuj się z administratorem!</span>';
			echo '<br /> Informacja deweloperska: '.$e;
		}

?>