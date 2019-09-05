<?php

function updatetaskstatus($polaczenie, $id_submit)
{
    $lockfile = $_SERVER['DOCUMENT_ROOT'].'/playground/'.$id_submit.'.lock';
    $fileinresults = $_SERVER['DOCUMENT_ROOT'].'/results/'.$id_submit.'.txt';

    if(!file_exists($lockfile) && file_exists($fileinresults))
    {
        $plik = file($fileinresults);
        $ilewierszy = count($plik)-1;

        if(intval($plik[$ilewierszy])==1) //zaktualizuj na poprawna
        {
            if(!($polaczenie->query("UPDATE submits SET status = 1 WHERE id_submit='$id_submit'")))
                echo "Error: ".$polaczenie->connect_errno;
        }
        elseif(intval($plik[$ilewierszy])==2) //zaktualizuj na bledna
        {
            if(!($polaczenie->query("UPDATE submits SET status = 2 WHERE id_submit='$id_submit'")))
                echo "Error: ".$polaczenie->connect_errno;
        }
        elseif(intval($plik[$ilewierszy])==3) //zaktualizuj na błąd kompilacji
        {
            if(!($polaczenie->query("UPDATE submits SET status = 3 WHERE id_submit='$id_submit'")))
                echo "Error: ".$polaczenie->connect_errno;
        }
        elseif(intval($plik[$ilewierszy])==4) //zaktualizuj na przekroczenie czasu
        {
            if(!($polaczenie->query("UPDATE submits SET status = 4 WHERE id_submit='$id_submit'")))
                echo "Error: ".$polaczenie->connect_errno;
        }
        elseif(intval($plik[$ilewierszy])==5) //zaktualizuj na naruszenie pamięci
        {
            if(!($polaczenie->query("UPDATE submits SET status = 5 WHERE id_submit='$id_submit'")))
                echo "Error: ".$polaczenie->connect_errno;
        }

        $points = 0;

        for($i=0;$i<$ilewierszy;$i+=1)
        {
            if(substr($plik[$i],0,1)=='#')
                $points += doubleval($plik[$i+1]);
        }

        if($points<1) $points=0;
        else
        $points = round($points, 8);

        if(!($polaczenie->query("UPDATE submits SET points = '$points' WHERE id_submit='$id_submit'")))
            echo "Error: ".$polaczenie->connect_errno;
    }
}

?>