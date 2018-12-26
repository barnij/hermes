<?php
    if(!isset($AccessToContest))
	{
		header('Location: /');
		exit();
    }

    if(!$AccessToContest)
    {
        $adres = 'Location: /'.$_GET['id'];
        header($adres);
        exit();
    }

    echo '
    <table style="width: 700px; border-bottom: 1px solid black; padding-bottom: 15px; margin-bottom: 15px;">
    <tr>
        <td style="width: 50%; text-align: left;">
            Identyfikator zadania: <b>'.$_GET['task'].'</b>
        </td>
        <td style="width: 50%; text-align: right;">
            [ <a href="/'.$_GET['id'].'/'.$_GET['task'].'/submit">Wyślij rozwiązanie</a> ]
        </td>
    </tr>
    </table>
    ';

    $adres = 'C:\xampp\htdocs\tasks\\'.$_GET['task'].'\\'.$_GET['task'].'.txt';

	$czyistnieje = true;

    if(!file_exists($adres))
    {
        $czyistnieje=false;
        echo "Błąd otwarcia treści zadania!";
    }

    if($czyistnieje)
    {
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