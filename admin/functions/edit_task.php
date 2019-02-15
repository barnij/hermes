<?php
    session_start();

    if( ((!isset($_POST['l'])) && (!isset($_POST['title_task'])) ) || (!isset($_POST['id_task'])) || (!isset($_SESSION['zalogowanyadmin'])) )
    {
        header('Location: /admin');
		exit();
    }

    $id_task = $_POST['id_task'];
    $lokalizacja = 'Location: /admin/konsola.php?tool=list_task&edit_task='.$id_task;
    header($lokalizacja);
    require_once ('../../functions/connect.php');

    if(isset($_POST['title_task']))
    {
        $DanePoprawne = true;

        //---------- Nazwa zadania ---------------

        $title_task = $_POST['title_task'];

        $font = "/var/www/html/font/times.ttf";

        $words = explode(" ", $title_task);

        foreach ($words as $w) 
        {
            list($left,, $right) = imagettfbbox( 16, 0, $font, $w);

            if(($right - $left)>160)
            {
                $DanePoprawne=false;
                $_SESSION['e_title_task']='Pojedyńcze słowa są za długie!';
            }
        }

        //----------------------------------------
        //------------ Nowa treść zadania --------

        $max_rozmiar = 102400; //max 100kB
        $nowatresc = false;

        if (is_uploaded_file($_FILES['tresc']['tmp_name'])){

            if ($_FILES['tresc']['size'] > $max_rozmiar) 
            {
                $DanePoprawne = false;
                $_SESSION['e_text_task']='Plik jest za duży!';
            }else
            {
                $filename = $_FILES['tresc']['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);

                if($ext=='txt' || $ext =='pdf')
                    $nowatresc = true;

                if($ext == 'txt')
                        $pdf = 0;
                else if($ext == 'pdf')
                        $pdf = 1;
                else
                {
                    $DanePoprawne=false;
                    $_SESSION['e_text_task']='Niedozwolone rozszerzenie pliku!';
                }
            }

        }

        //--------------------------------------------
        //--------------- Trudnosc -------------------

        $difficulty = $_POST['trudnosc'];

        //--------------------------------------------

        if($DanePoprawne)
        {
            if($nowatresc)
            {
                $sciezka = $_SERVER['DOCUMENT_ROOT']."/tasks/".$id_task;
                $staratresc = $sciezka.'/'.$id_task;
                if(file_exists($staratresc.'.txt'))
                    unlink($staratresc.'.txt');
                elseif(file_exists($staratresc.'.pdf'))
                    unlink($staratresc.'.pdf');

                    
                move_uploaded_file($_FILES['tresc']['tmp_name'], $sciezka."/".$id_task.".".$ext); //tresc
            }
            
            
            $polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

            if($polaczenie->connect_errno!=0)
            {
                echo "Error: ".$polaczenie->connect_errno;
            }
            else
            {
                mysqli_set_charset($polaczenie,"utf8");
                $polaczenie->query('SET NAMES utf8');

                if($nowatresc){
                    if($polaczenie->query("UPDATE tasks SET title_task='$title_task', difficulty='$difficulty', pdf='$pdf' WHERE id_task='$id_task'"))
                    $_SESSION['success_edit_task_info'] = '<span style="color: green; padding-left: 10px;">Zapisano.</span>';
                    else
                    {
                        echo "Error: ".$polaczenie->connect_errno;
                    }
                }else{
                    if($polaczenie->query("UPDATE tasks SET title_task='$title_task', difficulty='$difficulty' WHERE id_task='$id_task'"))
                    $_SESSION['success_edit_task_info'] = '<span style="color: green; padding-left: 10px;">Zapisano.</span>';
                    else
                    {
                        echo "Error: ".$polaczenie->connect_errno;
                    }
                }

                $polaczenie->close();

            }

        }
        

    }elseif(isset($_POST['l']))
    {

    }

?>