<?php
    session_start();

    if( ((!isset($_POST['l'])) && (!isset($_POST['title_task'])) ) || (!isset($_SESSION['zalogowanyadmin'])) )
    {
        header('Location: /admin');
		exit();
    }
?>