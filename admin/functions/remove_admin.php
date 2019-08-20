<?php

    session_start();

    if( (!isset($_POST['delete_admin'])) || (!isset($_SESSION['zalogowanyadmin'])) )
    {
        header('Location: /admin');
        exit();
    }

    header('Location: /admin/konsola.php?tool=manage_admin');

    $toremove = $_POST['delete_admin'];

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

            $zapytanie = $polaczenie->query("SELECT parent FROM admins WHERE id_admin='$toremove'");
            $rezulatat = $zapytanie->fetch_assoc();
            $parent = $rezulatat['parent'];

            if(!$polaczenie->query("UPDATE admins SET parent='$parent' WHERE parent='$toremove'"))
                throw new Exception($polaczenie->error);


            if(!$polaczenie->query("DELETE FROM admins WHERE id_admin='$toremove'"))
                throw new Exception($polaczenie->error);

            $_SESSION['success_remove_admin'] = 'Pomyślnie usunięto administratora.';


            $polaczenie->close();

        }
}

    catch(Exception $e) //wyświetlanie błędu
    {
        echo '<span style="color: red;">Błąd serwera!</span>';
        echo '<br /> Informacja deweloperska: '.$e;
    }






?>