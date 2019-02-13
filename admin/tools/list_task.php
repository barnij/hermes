<?php

    if(!isset($id_admin))
    {
        header('Location: /');
        exit();
    }

    if(!isset($_GET['edit_task']))
    {
        echo '<p style="margin-top: 0px; margin-bottom: 15px;">Wybierz zadanie, by je edytować:</br></p>';

        $sz1 = 100;
        $sz2 = 400;
        $sz3 = 100;
        $sz4 = 100;
        $tal = 'text-align: left';
        $tac = 'text-align: center';

        if(!isset($_GET['sort']))
        {
            $sort = 0; //sortuj od najnowszych do najstarszych (domyślnie)
        }else
        {
            $sort = $_GET['sort'];
        }

        if($sort==0 || $sort>5 || $sort<0)
        {
            $ws = 'id_task ASC';
        }else if($sort==1)
        {
            $ws = 'id_task DESC';
        }else if($sort==2)
        {
            $ws = 'title_task ASC';
        }else if($sort==3)
        {
            $ws = 'title_task DESC';
        }else if($sort==4)
        {
            $ws = 'difficulty ASC';
        }else //$sort==5
        {
            $ws = 'difficulty DESC';
        }

        
        echo '<table style="width: 700px;">
                <tr>
                    <th style="width: '.$sz1.'px; '.$tal.'">';
                        if($sort==0)
                            echo '<a class="nolink" href="?tool=list_task&sort=1">ID ↑';
                        elseif($sort==1)
                            echo '<a class="nolink" href="?tool=list_task&sort=0">ID ↓';
                        else
                            echo '<a class="nolink" href="?tool=list_task&sort=0">ID';
                echo '</a>
                    </th>
                    <th style="width: '.$sz2.'px; '.$tal.'">';
                    if($sort==2)
                        echo '<a class="nolink" href="?tool=list_task&sort=3">Nazwa zadania ↑';
                    elseif($sort==3)
                        echo '<a class="nolink" href="?tool=list_task&sort=2">Nazwa zadania ↓';
                    else
                        echo '<a class="nolink" href="?tool=list_task&sort=2">Nazwa zadania';
                echo'</a>	
                    </th>
                    <th style="width: '.$sz3.'px; '.$tac.'">';
                    if($sort==4)
                        echo '<a class="nolink" href="?tool=list_task&sort=5">Trudność ↑';
                    elseif($sort==5)
                        echo '<a class="nolink" href="?tool=list_task&sort=4">Trudność ↓';
                    else
                        echo '<a class="nolink" href="?tool=list_task&sort=4">Trudność';
                echo '</a>
                    </th>
                    <th style="width: '.$sz4.'px; '.$tac.'">
                        Zobacz treść
                    </th>
                </tr>
            </table>
            <table style="width: 700px; border-spacing:0 10px;">';

        $tresc = "SELECT id_task, title_task, difficulty, pdf FROM tasks ORDER BY ".$ws;
        $zapytanie = $polaczenie->query($tresc);

        while($row = mysqli_fetch_row($zapytanie))
        {
            echo '<tr>';
            echo '<td style="font-weight: bold; width: '.$sz1.'px;">
                    <label for="'.$row[0].'">
                    <a class="nolink" href="?tool=list_task&edit_task='.$row[0].'">'.$row[0].'</a>   
                    </label></td>
                    <td style="width: '.$sz2.'px;">
                    <label for="'.$row[0].'">
                    <a class="nolink" href="?tool=list_task&edit_task='.$row[0].'">'.$row[1].'</a>
                    </label></td>
                    <td style="width: '.$sz3.'px; '.$tac.'">
                    <label for="'.$row[0].'">
                    <a class="nolink" href="?tool=list_task&edit_task='.$row[0].'">'.$row[2].'</a>
                    </label></td>
                    <td style="width: '.$sz4.'px; '.$tac.'">
                        [ <a href="'; 
                        if($row[3]==1) echo '/tasks/'.$row[0].'/'.$row[0].'.pdf';
                        else echo '/admin/functions/view_task.php?task='.$row[0];
                        echo'" target="_blank">Otwórz</a> ]
                    </td>
                </tr>';
        }
        echo '</table>';
    
    }else
    {
        echo '<p style="margin-top: 0px; margin-bottom: 3px;">Edytuj podstawowe informacje:</p>
        <div class="borderinedit">
        <form method="POST" action="functions/edit_task.php">
            <label for="title_task">Nazwa zadania:</label><br/>
            <input style="width: 500px;" type="text" name="title_task" value="'.$title_task.'" required/><br/><br/>
            <label for="tresc">Treść zadania: </label>
            <input type="file" name="tresc"><br/>
            <span style="font-style: italic;">Nie wybierając pliku, treść nie zostanie zastąpiona.</span>
            <span style="padding-left: 20px;">[ <a href="';
            if($if_pdf)
                echo '/tasks/'.$id_task.'/'.$id_task.'.pdf';
            else
                echo '/admin/functions/view_task.php?task='.$id_task;
            echo '" target="_blank">Otwórz aktualną treść</a> ]</span><br/><br/>
            <label for="trudnosc">Trudność:</label>
	        <input type="range" id="RangeTrudnosc" name="trudnosc" min="0" max="10" step="1" oninput="document.getElementById(\'rangeValLabel\').innerHTML = this.value;" value="'.$difficulty.'"> <em id="rangeValLabel" style="font-style: normal; font-weight: bold">'.$difficulty.'</em>
            <br/><br/>
            <input type="submit" value="Zapisz">
        </form>
        </div>
        <p style="margin-top: 20px; margin-bottom: 3px;">Edytuj testy:</p>
        <div class="borderinedit">
        <form>';
        
        $conffile = '/var/www/html/tasks/'.$id_task.'/conf.txt';

        if(file_exists($conffile))
        {
            $plik = file($conffile);
            $iletestow = intval($plik[0]);
            $lp=0;
            $ilewierszy = count($plik)-1;
            $sumapunkow = 0;
            for($i=0;$i<$ilewierszy;$i+=1)
            {
                if(substr($plik[$i],0,1)=='#')
                {
                    echo '<span style="font-weight: bold;">Test '.$lp.'</span>';
                    echo '
                    <label style="margin-left: 30px; padding-right: 10px;">Liczba punktów za test:</label>
                    <input type="text" style="width: 140px;" value="'.doubleval($plik[$i+1]).'"><br/>
                    <label style="margin-left: 74px; padding-right: 10px;">Limit czasu:</label>
                    <input type="text" style="width: 100px; text-align: right;" value="'.doubleval($plik[$i+2]).'"> s
                    <label style="margin-left: 30px; padding-right: 10px;">Limit pamięci:</label>
                    <input type="text" style="width: 100px; text-align: right;" value="'.doubleval($plik[$i+3]).'"> MB<br/>';
                    $lp+=1;

                    if($lp!=$iletestow)
                        echo '<br/>';
                }
            }
        }else
        {
            echo "Błąd otwarcia pliku konfiguracyjnego!";
        }

        echo '</form>
        </div>';
    }

?>