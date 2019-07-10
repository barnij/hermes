<?php

    session_start();

    if( (!isset($_POST['na_login'])) || (!isset($_SESSION['zalogowanyadmin'])) )
    {
        header('Location: /admin');
        exit();
    }

    header('Location: /admin/konsola.php?tool=manage_admin');

    $DanePoprawne = true;

    



?>