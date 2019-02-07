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
    $zapytanie = $polaczenie->query("SELECT id FROM contest_list WHERE id_contest='$id_contest' AND id_task='$id_task'");

    if(mysqli_num_rows($zapytanie)==0) //brak tego zadania w danych zawodach
    {
        header('Location: /');
        exit();
    }

    $adres = 'C:\xampp\htdocs\tasks\\'.$_GET['task'].'\\'.$_GET['task'].'.txt';


    if(!file_exists($adres)) //czy istnieje plik
    {
        echo "Błąd otwarcia treści zadania!";
    }else
    {

        echo '
        <table style="width: 700px; border-bottom: 1px solid black; padding-bottom: 15px; margin-bottom: 15px;">
        <tr>
            <td style="width: 30%; text-align: left;">
                Identyfikator zadania: <b>'.$_GET['task'].'</b>
            </td>
            <td style="width: 40%; text-align: center;">
                [ <a href="/'.$_GET['id'].'/'.$_GET['task'].'/submits">Zobacz wysłania</a> ]
            </td>
            <td style="width: 30%; text-align: right;">
                [ <a href="/'.$_GET['id'].'/'.$_GET['task'].'/submit">Wyślij rozwiązanie</a> ]
            </td>
        </tr>
        </table>
        ';
    
        $plik = file($adres);
        $ile = count($plik);
        
        for($i=0;$i<$ile;$i++)
        {
            //$plik[$i] = htmlentities($plik[$i], ENT_QUOTES, "UTF-8");
            //$plik[$i] = utf8_encode($plik[$i]);
            $plik[$i] = preg_replace('#\r\n?#', "\n", $plik[$i]);
            $plik[$i] = str_replace(' ', '&nbsp;', $plik[$i]);
            echo $plik[$i];
            echo '<br>';
        }
    }

?>