<?php
    session_start();

    if( (!isset($_POST['pack_true'])) || (!isset($_SESSION['zalogowanyadmin'])) )
    {
        header('Location: /admin');
		exit();
    }

    header('Location: /admin/konsola.php?tool=add_task');

    $DanePoprawne = true;

    //---------- Wypakowywanie paczki ---------------

    if(!is_uploaded_file($_FILES['pack']['tmp_name'][0])){
        $_SESSION['e_pack']="Błąd przesyłu plików!";
        exit();
    }

    $ilepaczek = count($_FILES['pack']['tmp_name']);
    echo $ilepaczek.'<br/>';

    for($i=0; $i<$ilepaczek; $i+=1){

        $packname = pathinfo($_FILES['pack']['name'][$i], PATHINFO_FILENAME);
        $errortemplate = 'Paczka: '.$packname.' - ';

        $zip = new ZipArchive;
        if ($zip->open($_FILES['pack']['tmp_name'][$i]) === TRUE) {
            $name = uniqid();
            $path = sys_get_temp_dir().'/';
            $folderpath = $path.$name.'/';
            $zip->extractTo($folderpath);
            $zip->close();

            $set = fopen($folderpath.'set.txt',"r");
            if(!$set){
                if(!isset($_SESSION['e_pack']))
                        $_SESSION['e_pack'] = "";
                echo $_SESSION['e_pack']=$_SESSION['e_pack'].$errortemplate."Błąd odczytu pliku set.txt!<br/>";
                continue;
            }
            $id_task = fgets($set);
            $title_task = fgets($set);
            $difficulty = fgets($set);
            $timelimit = fgets($set);
            $memorylimit = fgets($set);
            $startpoints = fgets($set);

            if($id_task===FALSE || $title_task===FALSE || $difficulty===FALSE || $timelimit===FALSE || $memorylimit===FALSE || $startpoints===FALSE){
                if(!isset($_SESSION['e_pack']))
                        $_SESSION['e_pack'] = "";
                echo $_SESSION['e_pack']=$_SESSION['e_pack'].$errortemplate."Błąd odczytu pliku set.txt!<br/>";
                continue;
            }
            fclose($set);

            $id_task = str_replace(array("\r\n", "\n", "\r"), '', $id_task);
            $title_task = str_replace(array("\r\n", "\n", "\r"), '', $title_task);
            $difficulty = str_replace(array("\r\n", "\n", "\r"), '', $difficulty);
            $timelimit = str_replace(array("\r\n", "\n", "\r"), '', $timelimit);
            $memorylimit = str_replace(array("\r\n", "\n", "\r"), '', $memorylimit);
            $startpoints = str_replace(array("\r\n", "\n", "\r"), '', $startpoints);

            $inpath = $folderpath.'in/';
            $outpath = $folderpath.'out/';

            //---------- Identyfikator zadania ---------------

            $id_task = strtoupper($id_task);

            if (!ctype_alnum($id_task)) //tylko znaki alfanumeryczne
            {
                $DanePoprawne=false;
                if(!isset($_SESSION['e_pack']))
                        $_SESSION['e_pack'] = "";
                echo $_SESSION['e_pack']=$_SESSION['e_pack'].$errortemplate."Dozwolone znaki: A-Z, 0-9<br/>";
                continue;
            }

            if(strlen($id_task)>5 || strlen($id_task)==0) //dlugosc skrotu od 1 do 5 znakow
            {
                $DanePoprawne=false;
                if(!isset($_SESSION['e_pack']))
                        $_SESSION['e_pack'] = "";
                echo $_SESSION['e_pack']=$_SESSION['e_pack'].$errortemplate."ID powinno mieć od 1 do 5 znaków.<br/>";
                continue;
            }


            //---------- Nazwa zadania ---------------

            $font = $_SERVER['DOCUMENT_ROOT']."/font/times.ttf";

            $words = explode(" ", $title_task);

            foreach ($words as $w)
            {
                list($left,, $right) = imagettfbbox( 16, 0, $font, $w);

                if(($right - $left)>160)
                {
                    $DanePoprawne=false;
                    if(!isset($_SESSION['e_pack']))
                        $_SESSION['e_pack'] = "";
                    echo $_SESSION['e_pack']=$_SESSION['e_pack'].$errortemplate.'Pojedyncze słowa są za długie!<br/>';
                    continue;
                }
            }

            //---------- Treść zadania ---------------

            $max_rozmiar = 20971520; //max 20MB
            $task_path = $folderpath.$id_task;
            $task_text_path_pdf = $task_path.'.pdf';
            $task_text_path_txt = $task_path.'.txt';

            if (file_exists($task_text_path_pdf)){

                if (filesize($task_text_path_pdf) > $max_rozmiar)
                {
                    $DanePoprawne = false;
                    if(!isset($_SESSION['e_pack']))
                        $_SESSION['e_pack'] = "";
                    echo $_SESSION['e_pack']=$_SESSION['e_pack'].$errortemplate.'Plik z treścią jest za duży!<br/>';
                    continue;
                }
                else
                {
                    $pdf = 1;
                    $ext = 'pdf';
                }

            }else if(file_exists($task_text_path_txt)){
                if (filesize($task_text_path_txt) > $max_rozmiar)
                {
                    $DanePoprawne = false;
                    if(!isset($_SESSION['e_pack']))
                        $_SESSION['e_pack'] = "";
                    echo $_SESSION['e_pack']=$_SESSION['e_pack'].$errortemplate.'Plik z treścią jest za duży!.<br/>';
                    continue;
                }
                else
                {
                    $pdf=0;
                    $ext = 'txt';
                }
            }
            else
            {
                $DanePoprawne=false;
                if(!isset($_SESSION['e_pack']))
                        $_SESSION['e_pack'] = "";
                echo $_SESSION['e_pack']=$_SESSION['e_pack'].$errortemplate.'Brak treści zadania!.<br/>';
                continue;
            }

            //----------- Wrzucanie testów -----------


            if (!file_exists($inpath) || !file_exists($outpath))
            {
                $DanePoprawne=false;
                if(!isset($_SESSION['e_pack']))
                        $_SESSION['e_pack'] = "";
                echo $_SESSION['e_pack']=$_SESSION['e_pack'].$errortemplate.'Błąd przetwarzania folderów z testami!<br/>';
                continue;
            }

            $infiles = glob( $inpath.'*' );
            $outfiles = glob( $outpath.'*' );
            $countin = count($infiles);
            $countout = count($outfiles);
            $iletestow = $countin;

            if(!$countin || !$countout || $countout!=$countin)
            {
                $DanePoprawne=false;
                if(!isset($_SESSION['e_pack']))
                        $_SESSION['e_pack'] = "";
                echo $_SESSION['e_pack']=$_SESSION['e_pack'].$errortemplate.'Nierówna liczba testów wejściowych i plików wynikowych!<br/>';
                continue;
            }

            // Limity

            $timelimit = doubleval($timelimit);
            $memorylimit = doubleval($memorylimit);

            if($timelimit <= 0 || $memorylimit <= 0)
            {
                $DanePoprawne = false;
                if(!isset($_SESSION['e_pack']))
                        $_SESSION['e_pack'] = "";
                echo $_SESSION['e_pack']=$_SESSION['e_pack'].$errortemplate.'Wartości limitów muszą być dodatnie!<br/>';
                continue;
            }

            //Startowa liczba punktów

            $startpoints = doubleval($startpoints);

            if($startpoints < 1)
            {
                $DanePoprawne = false;
                if(!isset($_SESSION['e_pack']))
                        $_SESSION['e_pack'] = "";
                echo $_SESSION['e_pack'] = $_SESSION['e_pack'].$errortemplate.'Minimalna wartość punktów za zadanie to 1!<br/>';
                continue;
            }

            require_once ('../../functions/connect.php');
            $polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

            //Czy jest juz takie zadanie?
            if($polaczenie->connect_errno!=0 || !$DanePoprawne)
            {
                if(!isset($_SESSION['e_pack']))
                        $_SESSION['e_pack'] = "";
                $_SESSION['e_pack']=$_SESSION['e_pack'].$errortemplate."Błąd bazy danych lub nieznany błąd.<br/>";
                echo "Error: ".$polaczenie->connect_errno;
                continue;
            }
            else
            {
                mysqli_set_charset($polaczenie,"utf8");
                $polaczenie->query('SET NAMES utf8');

                $zapytanie = $polaczenie->query("SELECT id_task from tasks WHERE id_task='$id_task'");

                if(mysqli_num_rows($zapytanie) != 0){
                    $DanePoprawne = false;
                    if(!isset($_SESSION['e_pack']))
                        $_SESSION['e_pack'] = "";
                    $_SESSION['e_pack']=$_SESSION['e_pack'].$errortemplate."Zadanie z takim ID już istnieje<br/>";
                    $polaczenie->close();
                    continue;
                }

            }

            //Wrzucanie zadania do bazy danych
            if($DanePoprawne)
            {

                $sciezka = $_SERVER['DOCUMENT_ROOT']."/tasks/".$id_task;
                mkdir($sciezka,0777); //glowny folder z zadaniem
                mkdir($sciezka."/in",0777);
                mkdir($sciezka."/out",0777);
                rename($folderpath.$id_task.'.'.$ext, $sciezka."/".$id_task.".".$ext); //tresc

                foreach($infiles as $in){
                    $filenamein = pathinfo($in, PATHINFO_FILENAME);
                    rename($in, $sciezka."/in/".$filenamein.".in");
                }

                foreach($outfiles as $out){
                    $filenameout = pathinfo($out, PATHINFO_FILENAME);
                    rename($out, $sciezka."/out/".$filenameout.".out");
                }

                $conf = fopen($sciezka.'/conf.txt',"w") or die("Nie można utworzyć pliku konfiguracyjnego!"); //pisanie pliku konfiguracyjnego
                fwrite($conf, $iletestow."\n");

                $punktynatest = $startpoints/$iletestow;

                for($j=0; $j<$iletestow; $j+=1)
                {
                    fwrite($conf, "#".$i."\n");
                    fwrite($conf, $punktynatest."\n");
                    fwrite($conf, $timelimit."\n");
                    fwrite($conf, $memorylimit."\n");
                }

                fclose($conf);

                if($polaczenie->query("INSERT INTO tasks(id_task,title_task,difficulty,pdf,sum) VALUES ('$id_task','$title_task','$difficulty','$pdf','$startpoints')"))
                {
                    if(!isset($_SESSION['success_pack']))
                        $_SESSION['success_pack'] = "";
                    $_SESSION['success_pack'] = $_SESSION['success_pack'].'<span style="color: green; padding-left: 10px;">Dodano paczkę z zadaniem: '.$id_task.'</span><br/>';
                }else
                {
                    if(!isset($_SESSION['e_pack']))
                        $_SESSION['e_pack'] = "";
                    $_SESSION['e_pack']=$_SESSION['e_pack'].$errortemplate."Błąd dodania zadania do bazy danych<br/>";
                    echo "Error: ".$polaczenie->connect_errno;
                    continue;
                }

                $polaczenie->close();
            }
        } else {
            if(!isset($_SESSION['e_pack']))
                        $_SESSION['e_pack'] = "";
            echo $_SESSION['e_pack']=$_SESSION['e_pack'].$errortemplate."Błąd w wypakowywaniu paczki!<br/>";
        }
    }

?>