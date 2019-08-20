<?php

    session_start();

    if( (!isset($_POST['id_user'])) || (!isset($_SESSION['zalogowanyadmin'])) )
    {
        header('Location: /admin');
        exit();
    }

    $id_user = $_POST['id_user'];

    header('Location: /admin/konsola.php?tool=list_users&user='.$id_user);


    if(isset($_POST['username'])) //zmiana nazwy użytkownika
    {
        $DanePoprawne = true;
        $nazwakonta = $_POST['username'];

		if ((strlen($nazwakonta)<1) || (strlen($nazwakonta)>40)) //długość nazwy konta od 1 do 40 znaków
		{
			$DanePoprawne=false;
			$_SESSION['e_username']='<span class="error" style="margin-left: 20px;">Nazwa musi mieć od 1 do 40 znaków!</span>';
		}

		$font = $_SERVER['DOCUMENT_ROOT']."/font/times.ttf";

		$words = explode(" ", $nazwakonta);

		foreach ($words as $w)
		{
			list($left,, $right) = imagettfbbox( 16, 0, $font, $w);

			if(($right - $left)>160)
	  		{
	  			$DanePoprawne=false;
				$_SESSION['e_username']='<span class="error" style="margin-left: 20px;">Pojedyncze słowa są za długie!</span>';
	  		}
        }

        if($DanePoprawne)
        {
            require_once($_SERVER['DOCUMENT_ROOT']."/functions/connect.php");
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

                    if(!$polaczenie->query("UPDATE users SET name='$nazwakonta' WHERE id_user='$id_user'"))
                        throw new Exception($polaczenie->error);


                    if(!$polaczenie->query("DELETE FROM admins WHERE id_admin='$toremove'"))
                        throw new Exception($polaczenie->error);

                    $_SESSION['success_change_username'] = 'Zapisano.';


                    $polaczenie->close();

                }
            }

            catch(Exception $e) //wyświetlanie błędu
            {
                echo '<span style="color: red;">Błąd serwera!</span>';
                echo '<br /> Informacja deweloperska: '.$e;
            }

        }

    }elseif(isset($_POST['reset']))
    {
        $haslo = strval(rand(1001,99999));

        $haslo_hash = password_hash($haslo, PASSWORD_DEFAULT); //hashowanie hasla

        require_once($_SERVER['DOCUMENT_ROOT']."/functions/connect.php");
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

                if ($polaczenie->query("UPDATE users SET password='$haslo_hash' WHERE id_user='$id_user'"))
                {

                    $_SESSION['success_change_pass']='Pomyślnie zresetowano hasło.';
                    $_SESSION['reset_pass'] = $haslo;

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
            echo '<br /> Błąd: '.$e;
        }

    }



?>