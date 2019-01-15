<?php
    session_start();

    if( (!isset($_POST['id_task'])) || (!isset($_SESSION['zalogowanyadmin'])) )
    {
        header('Location: /admin');
		exit();
    }

    //header('Location: /admin/konsola.php?tool=add_task');

    $DanePoprawne = true;

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

    $font = "C:/xampp/htdocs/font/times.ttf";

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

    //---------- Treść zadania ---------------

    $max_rozmiar = 102400; //max 100kB
    $id_user = $_SESSION['id_user'];

    if (is_uploaded_file($_FILES['tresc']['tmp_name'])){

       $filename = $_FILES['tresc']['name'];
       $ext = pathinfo($filename, PATHINFO_EXTENSION);

       if($ext!='txt' && $ext!='pdf')
       {
           $DanePoprawne=false;
           $_SESSION['e_title_task']='Niedozwolone rozszerzenie pliku!';
       }

    }else
    {
        $DanePoprawne=false;
        $_SESSION['e_title_task']='Błąd przesyłu pliku!';
    }

    //----------- Wrzucanie testów -----------
    
    if($_POST['typeofouts']=='recznie') //Wrzucanie testów ręcznie
    {
        if (is_uploaded_file($_FILES['iny']['tmp_name'][0]) && is_uploaded_file($_FILES['outy']['tmp_name'][0]))
        {
            if(count($_FILES['iny']['tmp_name']) != count($_FILES['iny']['tmp_name']))
            {
                $DanePoprawne=false;
                $_SESSION['e_recznie']='Nierówna liczba testów wejściowych i wyjściowych.';
            }
        }else
        {
            $DanePoprawne=false;
            $_SESSION['e_recznie']='Błąd przesyłu plików!';
        }

    }else //Wrzucanie testów automatycznie
    {
        
    }

?>