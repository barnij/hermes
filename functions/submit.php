<?php
    session_start();

    $max_rozmiar = 102400; //max 100kB
    $id_user = $_SESSION['id_user'];
    $shortcut_contest = $_POST['id_contest'];
    $id_task = $_POST['id_task'];
    $adres1 = 'Location: /'.$shortcut_contest.'/mysubmits'; //sukces
    $adres2 = 'Location: /'.$shortcut_contest.'/'.$id_task.'/submit'; //blad

    if (is_uploaded_file($_FILES['plik']['tmp_name']) || isset($_POST['code'])) {

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

            $checkfile = true;
            if(!is_uploaded_file($_FILES['plik']['tmp_name']))
                $checkfile = false;


            if ($checkfile && $_FILES['plik']['size'] > $max_rozmiar) {
                header($adres2);
                $_SESSION['e_file']='<span style="color: red; padding-left: 10px;">Błąd! Plik jest za duży!</span>';
                $polaczenie->close();
                exit();
            }


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

            $lang = $_POST['lang'];
            if($lang=="C++ (g++ 4.7)")
                $rozszerzenie = ".cpp";
            else if($lang=="Python 3.5")
                $rozszerzenie = ".py";
            else if($lang=="RAM Machine")
                $rozszerzenie = ".mrram";
            else if($lang=="BAP")
                $rozszerzenie = ".bap";


            if($checkfile)
            {
                move_uploaded_file($_FILES['plik']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].'/submits/'.$nr.$rozszerzenie);
            }else
            {
                $code = $_POST['code'];
                $fp = fopen($_SERVER['DOCUMENT_ROOT'].'/submits/'.$nr.$rozszerzenie, 'w');
                fwrite($fp, $code);
                fclose($fp);
            }

            $zapytanie = $polaczenie->query("SELECT id_contest FROM contests WHERE shortcut_contest='$shortcut_contest'");
            $rezultat = $zapytanie->fetch_assoc();
            $id_contest = $rezultat['id_contest'];

            if($polaczenie->query("INSERT INTO submits(id_user,id_task,id_contest,lang) VALUES ('$id_user','$id_task','$id_contest','$lang')"))
            {
                //sukces
            }
            else
            {
                echo "Error: ".$polaczenie->connect_errno;
            }

            // Sciezka do programu sprawdzajacego
            $polecenie = '/var/www/hermes/public_html/start '.$nr.' '.$rozszerzenie.' '.$id_task;

            shell_exec($polecenie);

            $polaczenie->close();
            sleep(2);
        }
    }else {
        header($adres2);
        $_SESSION['e_file']='<span style="color: red; padding-left: 10px;">Błąd przy przesyłaniu danych!</span>';
    }

?>