<?php
    session_start();

    if( (!isset($_POST['id_task'])) || (!isset($_SESSION['zalogowanyadmin'])) )
    {
        header('Location: /admin');
		exit();
    }

    header('Location: /admin/konsola.php?tool=add_task');

    $DanePoprawne = true;
    require_once ('../../functions/connect.php');


    //---------- Identyfikator zadania ---------------

    $id_task = $_POST['id_task'];
    $id_task = strtoupper($id_task);

    if (!ctype_alnum($id_task)) //tylko znaki alfanumeryczne
	{
		$DanePoprawne=false;
		$_SESSION['e_id_task']="Dozwolone znaki: A-Z, 0-9";
    }

    if(strlen($id_task)>5 || strlen($id_task)==0) //dlugosc skrotu od 1 do 5 znakow
	{
		$DanePoprawne=false;
		$_SESSION['e_id_task']="ID powinno mieć od 1 do 5 znaków.";
    }

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
            $_SESSION['e_title_task']='Pojedyncze słowa są za długie!';
        }
    }

    //---------- Treść zadania ---------------

    $max_rozmiar = 20971520; //max 20MB

    if (is_uploaded_file($_FILES['tresc']['tmp_name'])){

        if ($_FILES['tresc']['size'] > $max_rozmiar)
        {
            $DanePoprawne = false;
            $_SESSION['e_text_task']='Plik jest za duży!';
        }
        else
        {

            $filename = $_FILES['tresc']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);

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

    }else
    {
        $DanePoprawne=false;
        $_SESSION['e_text_task']='Błąd przesyłu pliku!';
    }

    //----------- Wrzucanie testów -----------

    if (!is_uploaded_file($_FILES['iny']['tmp_name'][0])) //iny
    {
            $DanePoprawne=false;
            $_SESSION['e_iny']='Błąd przesyłu plików!';
    }


    if (is_uploaded_file($_FILES['outy']['tmp_name'][0])) //outy
    {
        if(count($_FILES['iny']['tmp_name']) != count($_FILES['outy']['tmp_name']))
        {
            $DanePoprawne=false;
            $_SESSION['e_outy']='Nierówna liczba testów wejściowych i plików wynikowych!';
        }
    }else
    {
        $DanePoprawne=false;
        $_SESSION['e_outy']='Błąd przesyłu plików!';
    }



    // Trudnosc

    $difficulty = $_POST['trudnosc'];

    // Limity
    $timelimit = $_POST['timelimit'];
    $memorylimit = $_POST['memorylimit'];

    if($timelimit <= 0 || $memorylimit <= 0)
    {
        $DanePoprawne = false;
        $_SESSION['e_limit']='Obie wartości muszą być dodatnie!';
    }

    //Startowa liczba punktów

    $startpoints = $_POST['startpoints'];

    if($startpoints < 1)
    {
        $DanePoprawne = false;
        $_SESSION['e_startpoints'] = 'Minimalna wartość to 1!';
    }

    $polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

    //Czy jest juz takie zadanie?
    if($polaczenie->connect_errno!=0 || !$DanePoprawne)
    {
        echo "Error: ".$polaczenie->connect_errno;
    }
    else
    {
        mysqli_set_charset($polaczenie,"utf8");
        $polaczenie->query('SET NAMES utf8');

        $zapytanie = $polaczenie->query("SELECT id_task from tasks WHERE id_task='$id_task'");

        if(mysqli_num_rows($zapytanie) != 0){
            $DanePoprawne = false;
            $_SESSION['e_id_task']="Zadanie z takim ID już istnieje";
            $polaczenie->close();
        }

    }


    if($DanePoprawne)
    {


        $sciezka = $_SERVER['DOCUMENT_ROOT']."/tasks/".$id_task;
        mkdir($sciezka,0777); //glowny folder z zadaniem
        mkdir($sciezka."/in",0777);
        mkdir($sciezka."/out",0777);

        move_uploaded_file($_FILES['tresc']['tmp_name'], $sciezka."/".$id_task.".".$ext); //tresc

        $iletestow = count($_FILES['iny']['tmp_name']);

        for($i=0; $i < $iletestow; $i+=1)
        {
            $filenamein = pathinfo($_FILES['iny']['name'][$i], PATHINFO_FILENAME);
            $filenameout = pathinfo($_FILES['outy']['name'][$i], PATHINFO_FILENAME);
            move_uploaded_file($_FILES['iny']['tmp_name'][$i], $sciezka."/in/".$filenamein.".in");
            move_uploaded_file($_FILES['outy']['tmp_name'][$i], $sciezka."/out/".$filenameout.".out");
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


        if($polaczenie->query("INSERT INTO tasks(id_task,title_task,difficulty,pdf,sum) VALUES ('$id_task','$title_task','$difficulty','$pdf','$startpoints')"))
        {
            $_SESSION['success_add_task'] = '<span style="color: green; padding-left: 10px;">Dodano zadanie.</span>';
        }else
        {
            echo "Error: ".$polaczenie->connect_errno;
        }

        $polaczenie->close();

    }

?>