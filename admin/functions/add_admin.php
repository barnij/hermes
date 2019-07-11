<?php

    session_start();

    if( (!isset($_POST['na_login'])) || (!isset($_SESSION['zalogowanyadmin'])) )
    {
        header('Location: /admin');
        exit();
    }

    header('Location: /admin/konsola.php?tool=manage_admin');

    $DanePoprawne = true;

    $login = $_POST['na_login'];

    if(strlen($login)>20) // długość loginu do 16 znaków
    {
        $DanePoprawne=false;
        $_SESSION['e_na_login']="Login musi zawierać do 16 znaków!<br/>";
    }

    if (!ctype_alnum(str_replace('_', '', $login))) //tylko znaki alfanumeryczne
    {
        $DanePoprawne=false;
        $_SESSION['e_na_login']="Dozwolone znaki a-z, A-Z, 0-9, _<br/>";
    }

    $font = $_SERVER['DOCUMENT_ROOT']."/font/times.ttf";
    $name = $_POST['na_name'];

    list($left,, $right) = imagettfbbox( 16, 0, $font, $name);

    if(($right - $left)>235)
    {
        $DanePoprawne1=false;
        $_SESSION['e_na_name']='<span class="error">Nazwa jest za długa!</span>';
    }

    $haslo1 = $_POST['na_pass'];
    $haslo2 = $_POST['na_pass_repeat'];

    if ($haslo1!=$haslo2)
    {
        $DanePoprawne=false;
        $_SESSION['e_na_pass']="Hasła nie są identyczne!";
    }

    $haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT); //hashowanie hasla

    $parent = $_POST['parent'];

    if(!$DanePoprawne)
        exit();

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

            $rezultat = $polaczenie->query("SELECT id_admin FROM admins WHERE login='$login'");  //wybierz z DB rekordy z podanym loginem

            if (!$rezultat) throw new Exception($polaczenie->error);
                
            if($rezultat->num_rows>0) //BŁĄD - konto z takim loginem już istnieje w bazie!
            {
                $DanePoprawne=false;
                $_SESSION['e_na_login']="Konto z takim loginem już istnieje!";
            }										
        }

        if ($DanePoprawne) // Wszystkie dane poprawne HURRA!
        {
            
            if ($polaczenie->query("INSERT INTO admins VALUES (NULL, '$login', '$haslo_hash', '$parent', '$name')")) //dodawanie rekordu do admins
            {

                $_SESSION['success_add_admin']='<span style="color: green; padding-left: 10px;">Pomyślnie dodano administratora.</span>';
                
            }
            else
            {
                throw new Exception($polaczenie->error);
            }

        }

        $polaczenie->close();
    }

    catch(Exception $e) //wyświetlanie błędu
    {
        echo '<br /> Błąd: '.$e;
    }
	



?>