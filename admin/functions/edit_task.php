<?php
    session_start();

    if( ((!isset($_POST['setFORALL'])) && (!isset($_POST['newtests'])) && (!isset($_POST['title_task'])) ) || (!isset($_POST['id_task'])) || (!isset($_SESSION['zalogowanyadmin'])) )
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

        $font = $_SERVER['DOCUMENT_ROOT']."/font/times.ttf";

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
        

    }elseif(isset($_POST['newtests']))
    {
        $DanePoprawne = true;

        if (!is_uploaded_file($_FILES['newiny']['tmp_name'][0])) //iny
        {
            $DanePoprawne=false;
            $_SESSION['e_iny']='Błąd przesyłu plików!';       
        }

        if (is_uploaded_file($_FILES['newouty']['tmp_name'][0])) //outy
        {
            if(count($_FILES['newiny']['tmp_name']) != count($_FILES['newouty']['tmp_name']))
            {
                $DanePoprawne=false;
                $_SESSION['e_outy']='Nierówna liczba testów wejściowych i plików wynikowych!';
            }
        }else
        {
            $DanePoprawne=false;
            $_SESSION['e_outy']='Błąd przesyłu plików!';
        }

        // poprzednie testy
        $sciezka = $_SERVER['DOCUMENT_ROOT']."/tasks/".$id_task;

        if(file_exists($sciezka.'/conf.txt'))
        {
            $plik = file($sciezka.'/conf.txt');
            $nrofoldtests = intval($plik[0]);
        }else
        {
            $DanePoprawne = false;
            $_SESSION['e_outy'] = 'Błąd w usuwaniu poprzednich plików! Brak pliku conf.txt<br/>
            Zapisz ręcznie poprawny plik conf.txt z liczbą testów w pierwszym wierszu!';
        }

        // Limity
        $timelimit = $_POST['timelimit'];
        $memorylimit = $_POST['memorylimit'];

        if($timelimit <= 0 || $memorylimit <= 0)
        {
            $DanePoprawne = false;
            $_SESSION['e_limit']='Obie wartości limitów muszą być dodatnie!';
        }

        //Startowa liczba punktów

        $startpoints = $_POST['startpoints'];

        if($startpoints < 1)
        {
            $DanePoprawne = false;
            $_SESSION['e_startpoints'] = 'Minimalna wartość sumy punktów to 1!';
        }

        if($DanePoprawne)
        {
            $polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

            if($polaczenie->connect_errno!=0)
            {
                echo "Error: ".$polaczenie->connect_errno;
            }
            else
            {
                //usuwanie starych testow:
                for($i=0; $i<$nrofoldtests; $i+=1)
                {
                    if(file_exists($sciezka.'/in/'.$i.'.in'))
                        unlink($sciezka.'/in/'.$i.'.in');
                    
                    if(file_exists($sciezka.'/out/'.$i.'.out'))
                        unlink($sciezka.'/out/'.$i.'.out');
                }
                
                //wrzucanie nowych
                $iletestow = count($_FILES['newiny']['tmp_name']);

                for($i=0; $i < $iletestow; $i+=1)
                {
                    $filenamein = pathinfo($_FILES['newiny']['name'][$i], PATHINFO_FILENAME);
                    $filenameout = pathinfo($_FILES['newouty']['name'][$i], PATHINFO_FILENAME);
                    move_uploaded_file($_FILES['newiny']['tmp_name'][$i], $sciezka."/in/".$filenamein.".in");
                    move_uploaded_file($_FILES['newouty']['tmp_name'][$i], $sciezka."/out/".$filenameout.".out");
                }
                

                $conf = fopen($sciezka.'/conf.txt',"w") or die("Nie można utworzyć pliku konfiguracyjnego!"); //pisanie pliku konfiguracyjnego
                fwrite($conf, $iletestow."\n");

                $punktynatest = $startpoints/$iletestow;

                for($i=0; $i<$iletestow; $i+=1)
                {
                    fwrite($conf, "#".$i."\n");
                    fwrite($conf, $punktynatest."\n");
                    fwrite($conf, $timelimit."\n");
                    fwrite($conf, $memorylimit."\n");
                }

                fclose($conf);

                if($polaczenie->query("UPDATE tasks SET sum='$startpoints' WHERE id_task='$id_task'"))
                {
                    $_SESSION['success_edit_task_tests'] = '<span style="color: green; padding-left: 10px;">Zapisano.</span>';
                }else
                {
                    echo "Error: ".$polaczenie->connect_errno;
                }

                $polaczenie->close();

            }
        }

    }elseif(isset($_POST['setFORALL']))
    {
        $DanePoprawne = true;
        $iftime = false;
        $ifmemory = false;
        $ifpoints = false;

        if(isset($_POST['timelimitFORALL']) && ($_POST['timelimitFORALL']!=''))
        {
            $timelimit = $_POST['timelimitFORALL'];
            $iftime = true;

            if($timelimit <= 0)
            {
                $DanePoprawne = false;
                $_SESSION['e_limitFORALL']='Limity muszą być liczbami dodatnimi dodatnimi!';
            }
        }

        if(isset($_POST['memorylimitFORALL']) && ($_POST['memorylimitFORALL']!=''))
        {
            $memorylimit = $_POST['memorylimitFORALL'];
            $ifmemory = true;

            if($memorylimit <= 0)
            {
                $DanePoprawne = false;
                $_SESSION['e_limitFORALL']='Limity muszą być liczbami dodatnimi dodatnimi!';
            }
        }

        if(isset($_POST['sumofpoints']) && ($_POST['sumofpoints']!=''))
        {
            $startpoints = $_POST['sumofpoints'];
            $ifpoints = true;

            if($startpoints < 1)
            {
                $DanePoprawne = false;
                $_SESSION['e_pointsFORALL'] = 'Minimalna wartość sumy punktów to 1!';
            }
        }

        if($DanePoprawne)
        {
            $sciezka = $_SERVER['DOCUMENT_ROOT'].'/tasks/'.$id_task.'/conf.txt';
            $sciezka1 = $_SERVER['DOCUMENT_ROOT'].'/tasks/'.$id_task.'/conf1.txt';

            if(file_exists($sciezka))
            {   
                
                $polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

                if($polaczenie->connect_errno!=0)
                {
                    echo "Error: ".$polaczenie->connect_errno;
                }
                else
                {
                    rename($sciezka,$sciezka1);
                    $oldconf = file($sciezka1);

                    $punktynatest = ($startpoints/intval($oldconf[0]));

                    $ilewierszy = count($oldconf)-1;
                    $conf = fopen($sciezka,"w") or die("Nie można utworzyć pliku konfiguracyjnego!");

                    fwrite($conf, $oldconf[0]); //pierwsza linia
                    

                    for($i=1; $i<$ilewierszy; $i+=1)
                    {
                        if(substr($oldconf[$i],0,1)=='#')
                        {
                            fwrite($conf, $oldconf[$i]);

                            if($ifpoints)
                                fwrite($conf, $punktynatest."\n");
                            else
                                fwrite($conf, $oldconf[$i+1]);

                            if($iftime)
                                fwrite($conf, $timelimit."\n");
                            else
                                fwrite($conf, $oldconf[$i+2]);

                            if($ifmemory)
                                fwrite($conf, $memorylimit."\n");
                            else
                                fwrite($conf, $oldconf[$i+3]);
                        }
                    }

                    fclose($conf);
                    unlink($sciezka1);

                    if($polaczenie->query("UPDATE tasks SET sum='$startpoints' WHERE id_task='$id_task'"))
                    {
                        $_SESSION['success_edit_task_setFORALL'] = '<span style="color: green; padding-left: 10px;">Zapisano.</span>';
                    }else
                    {
                        echo "Error: ".$polaczenie->connect_errno;
                    }

                    $polaczenie->close();

                }

            }else
            {
                $_SESSION['e_confFORALL'] = 'BRAK PLIKU KONFIGURACYJNEGO!';
            }
        }

    }

?>