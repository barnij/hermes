<?php
    session_start();

    $max_rozmiar = 102400; //max 100kB
    $id_user = $_SESSION['id_user'];
    $shortcut_contest = $_POST['id_contest'];
    $id_task = $_POST['id_task'];
    $adres1 = 'Location: /'.$shortcut_contest.'/mysubmits'; //sukces
    $adres2 = 'Location: /'.$shortcut_contest.'/'.$id_task.'/submit'; //blad

    if (is_uploaded_file($_FILES['plik']['tmp_name'])) {

        require_once ('connect.php');

        $polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

        if($polaczenie->connect_errno!=0)
        {
            echo "Error: ".$polaczenie->connect_errno;
        }
        else
        {
            mysqli_set_charset($polaczenie,"utf8");
            $polaczenie->query('SET NAMES utf8');

            if ($_FILES['plik']['size'] > $max_rozmiar) {
                header($adres2);
                $_SESSION['e_file']='<span style="color: red; padding-left: 10px;">Błąd! Plik jest za duży!</span>';
            } else {
                header($adres1);

                $zapytanie = $polaczenie->query("SELECT id_submit FROM submits ORDER BY id_submit DESC LIMIT 1");

                if(mysqli_num_rows($zapytanie)==0)
                {
                    $nr=1;
                }else
                {
                    $rezultat = $zapytanie->fetch_assoc();
                    $nr = $rezultat['id_submit']+1;
                }

                if($_POST['lang']=="C++ (g++ 4.7)")
                    $rozszerzenie = ".cpp";
                else if($_POST['lang']=="Python 3.6")
                    $rozszerzenie = ".py";
                else if($_POST['lang']=="RAM Machine")
                    $rozszerzenie = ".mrram";
                else if($_POST['lang']=="BAP")
                    $rozszerzenie = ".bap";

                move_uploaded_file($_FILES['plik']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/submits/'.$nr.$rozszerzenie);

                $zapytanie = $polaczenie->query("SELECT id_contest FROM contests WHERE shortcut_contest='$shortcut_contest'");
                $rezultat = $zapytanie->fetch_assoc();
                $id_contest = $rezultat['id_contest'];

                if($polaczenie->query("INSERT INTO submits(id_user,id_task,id_contest) VALUES ('$id_user','$id_task','$id_contest')"))
                {
                    //sukces
                }
                else
                {
                    echo "Error: ".$polaczenie->connect_errno;
                }

                $polecenie = 'CD /D C:/xampp/htdocs/ && Start.exe '.$nr.$rozszerzenie.' '.$id_task;

                shell_exec($polecenie);
            }

            $polaczenie->close();
        }
    } else {
        header($adres2);
        $_SESSION['e_file']='<span style="color: red; padding-left: 10px;">Błąd przy przesyłaniu danych!</span>';
    }

?> 