<?php
    session_start();

    if ((!isset($_GET['task'])) || (!isset($_SESSION['zalogowanyadmin'])))
	{
		header('Location: /admin');
		exit();
    }

    $adres = $_SERVER['DOCUMENT_ROOT'].'/tasks/'.$_GET['task'].'/'.$_GET['task'].'.txt';


    if(!file_exists($adres)) //czy istnieje plik
    {
        echo "Błąd otwarcia treści zadania!";
    }else
    {

        echo '
        <!DOCTYPE HTML>
        <html lang="pl">
        <head>
            <meta charset="utf-8">
	        <title>Zadanie '.$_GET['task'].' - HERMES ADMIN</title>
        </head>
        <body>
        <div style="margin-left: 100px; margin-top: 80px;">
            <p style="font-style: italic; font-weight: bold;">Treść zadania '.$_GET['task'].':</p>';
        
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

        echo '</div></body>';
    }

?>