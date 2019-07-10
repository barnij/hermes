<?php

    session_start();

    if( (!isset($_POST['old_pass'])) || (!isset($_SESSION['zalogowanyadmin'])) )
    {
        header('Location: /admin');
        exit();
    }

    header('Location: /admin/konsola.php?tool=manage_admin');
    
    $id_admin = $_SESSION['id_admin'];

    $DanePoprawne = true;

    require_once($_SERVER['DOCUMENT_ROOT'].'/functions/connect.php');

    $polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

    if($polaczenie->connect_errno!=0)
    {
        $_SESSION['e_old_pass']="Error: ".$polaczenie->connect_errno;
        exit();
    }

    $zapytanie = $polaczenie->query("SELECT password FROM admins WHERE id_admin='$id_admin'");
    $rezultat = $zapytanie->fetch_assoc();
    $passfrombase = $rezultat['password'];

    $password = $_POST['old_pass'];

    if(!(password_verify($password,$passfrombase)))
    {
        $DanePoprawne = false;
        $_SESSION['e_old_pass']='Niepoprawne hasło!';
        exit();
    }

    $newpassword = $_POST['new_pass'];
    $newpasswordrepeat = $_POST['new_pass_repeat'];

    if ($newpassword!=$newpasswordrepeat)
    {
        $DanePoprawne=false;
        $_SESSION['e_old_pass']='Hasła nie są identyczne!';
    }

    $haslo_hash = password_hash($newpassword, PASSWORD_DEFAULT);

    if($DanePoprawne==true)
    {
        if($polaczenie->query("UPDATE admins SET password='$haslo_hash' WHERE id_admin='$id_admin'"))
        {
            if(isset($_SESSION['e_haslo'])) unset($_SESSION['e_haslo']);
            if(isset($_SESSION['e_haslo2'])) unset($_SESSION['e_haslo2']);
            $_SESSION['new_pass_success'] = '<span style="color: green; padding-left: 10px;">Hasło zostało zmienione.</span>';
        }else
        {
            echo "Error: ".$polaczenie->connect_errno;
        }
    }

    $polaczenie->close();


?>