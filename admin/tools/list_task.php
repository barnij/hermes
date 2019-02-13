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
        echo '<p style="margin-top: 0px; margin-bottom: 5px;">Edytuj podstawowe informacje:</br></p>
        <div class="borderinedit">
        <form method="POST" action="functions/edit_task.php">
            <label for="title_task">Nazwa zadania:</label><br/>
            <input style="width: 500px;" type="text" name="title_task" value="'.$title_task.'" required/><br/><br/>
            <label for="tresc">Treść zadania: </label>
            <input type="file" name="tresc"><br/>
            <span style="font-style: italic;">Nie wybierając pliku, treść nie zostanie zastąpiona.</span><br/><br/>
            <label for="trudnosc">Trudność:</label>
	        <input type="range" id="RangeTrudnosc" name="trudnosc" min="0" max="10" step="1" oninput="document.getElementById(\'rangeValLabel\').innerHTML = this.value;" value="'.$difficulty.'"> <em id="rangeValLabel" style="font-style: normal; font-weight: bold">'.$difficulty.'</em>
            <br/><br/>
            <input type="submit" value="Zapisz">
        </form>
        </div><br/><br/>
        <p style="margin-top: 0px; margin-bottom: 5px;">Edytuj podstawowe informacje:</br></p>';
    }

?>