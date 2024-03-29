<?php
    if(!isset($AccessToContest)) //prawdopodobnie niezalogowany użytkownik
	{
		header('Location: /');
		exit();
    }

    if(!$AccessToContest) //brak dostępu do zawodów
    {
        $adres = 'Location: /'.$_GET['id'];
        header($adres);
        exit();
    }

    $id_task = $_GET['task'];
    $zapytanie = $polaczenie->query("SELECT id_task FROM contest_list WHERE id_contest='$id_contest' AND id_task='$id_task'");

    if(mysqli_num_rows($zapytanie)==0) //brak tego zadania w danych zawodach
    {
        header('Location: /');
        exit();
    }

    $adres = $_SERVER['DOCUMENT_ROOT'].'/tasks/'.$id_task.'/'.$id_task.'.txt';

    if(!file_exists($adres)) //czy istnieje plik
    {
        echo "Błąd otwarcia treści zadania!";
    }else
    {
        echo '
        <table style="width: 700px; border-bottom: 1px solid black; padding-bottom: 15px; margin-bottom: 15px;">
        <tr>
            <td style="width: 30%; text-align: left;">
                Identyfikator zadania: <b>'.$id_task.'</b>
            </td>
            <td style="width: 40%; text-align: center;">
                [ <a href="/'.$_GET['id'].'/'.$id_task.'/submits">Zobacz wysłania</a> ]
            </td>
            <td style="width: 30%; text-align: right;">
                [ <a href="/'.$_GET['id'].'/'.$id_task.'/submit">Wyślij rozwiązanie</a> ]
            </td>
        </tr>
        </table>';
        
        $zapytanie = $polaczenie->query("SELECT title_task FROM tasks WHERE id_task='$id_task'");
        $rezultat = $zapytanie->fetch_assoc();
        $title = $rezultat['title_task'];
        
        echo '<div style="font-weight: bold;">'.$title.'</div><br/> ';
    
        $plik = implode('<br/>', file($adres));
        echo '<div style="width: 95%;">'.$plik.'</div>';
    }

?>