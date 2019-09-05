<?php
    session_start();

    if ((!isset($_GET['task'])) || (!isset($_SESSION['zalogowanyadmin'])))
	{
		header('Location: /admin');
		exit();
    }

    $id_task = $_GET['task'];
    $title = '';

    $adres = $_SERVER['DOCUMENT_ROOT'].'/tasks/'.$id_task.'/'.$id_task.'.txt';


    if(!file_exists($adres)) //czy istnieje plik
    {
        echo "Błąd otwarcia treści zadania!";
    }else
    {
        require_once($_SERVER['DOCUMENT_ROOT']."/functions/connect.php");

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

                $zapytanie = $polaczenie->query("SELECT title_task FROM tasks WHERE id_task='$id_task'");
                $rezulatat = $zapytanie->fetch_assoc();
                $title = $rezulatat['title_task'];

                $polaczenie->close();

            }
        }

        catch(Exception $e) //wyświetlanie błędu
        {
            echo '<span style="color: red;">Błąd serwera!</span>';
            echo '<br /> Informacja deweloperska: '.$e;
        }


        echo '
        <!DOCTYPE HTML>
        <html lang="pl">
        <head>
            <meta charset="utf-8">
	        <title>Zadanie '.$id_task.' - HERMES ADMIN</title>
        </head>
        <body>
        <div style="margin-left: 100px; margin-top: 80px;">
            <p style="font-style: italic; font-weight: bold;">Treść zadania '.$id_task.':</p>
            <div style="padding-left: 20px;">
            <div style="font-weight: bold">'.$title.'</div><br/>';
        
            $plik = file($adres);
            $ile = count($plik);
            
            for($i=0;$i<$ile;$i++)
            {
                //$plik[$i] = htmlentities($plik[$i], ENT_QUOTES, "UTF-8");
                //$plik[$i] = utf8_encode($plik[$i]);
                $plik[$i] = preg_replace('#\r\n?#', "\n", $plik[$i]);
                $plik[$i] = str_replace(' ', '&nbsp;', $plik[$i]);
                echo $plik[$i];
                echo '<br>';
            }

        echo '</div></div></body>';
    }

?>