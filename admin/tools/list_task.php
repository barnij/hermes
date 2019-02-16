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
    
    }else //Edycja zadania
    {
        echo '<p style="margin-top: 0px; margin-bottom: 3px;">Edytuj podstawowe informacje:</p>
        <div class="borderinedit">
        <form method="POST" action="functions/edit_task.php" enctype="multipart/form-data">
            <label for="title_task">Nazwa zadania:</label><br/>
            <input style="width: 500px;" type="text" name="title_task" value="'.$title_task.'" required/><br/>';
            if(isset($_SESSION['e_title_task']))
            {
                echo '<span class="error">'.$_SESSION['e_title_task'].'</span><br/>';
                unset($_SESSION['e_title_task']);
            }
            echo '<br/>
            <label for="tresc">Treść zadania: </label>
            <input type="file" name="tresc" accept=".txt,.pdf">';
            if(isset($_SESSION['e_text_task']))
            {
                echo '<span class="error">'.$_SESSION['e_text_task'].'</span>';
                unset($_SESSION['e_text_task']);
            }
            echo '<br/>
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

            <table style="width: 680px;">
            <tr>
                <td style="width: 50%; text-align: left;">
                    <input type="hidden" name="id_task" value="'.$id_task.'">
                    <input type="submit" value="Zapisz">';
                    if(isset($_SESSION['success_edit_task_info']))
                    {
                        echo $_SESSION['success_edit_task_info'];
                        unset($_SESSION['success_edit_task_info']);
                    }
                    echo '</form>
                </td>
                <td style="width: 50%; text-align: right;">
                    <form method="POST" action="functions/delete_task.php">
                        <input type="hidden" name="id_task" value="'.$id_task.'">
                        <input type="hidden" name="TAKusuntask" value="1">
                        <input style="background-color: #ef6262;" type="submit" value="Usuń zadanie" onclick="'."return confirm('Czy na pewno chcesz to zrobić? Zostaną usunięte wszystkie pliki związane z tym zadaniem!');\"".'>
                    </form>
                </td>
            </tr>
            </table>
        </div>

        <p style="margin-top: 20px; margin-bottom: 3px;">Wybierz nowe pliki testów:</p>
        <div class="borderinedit">
        <form method="POST" action="functions/edit_task.php" enctype="multipart/form-data">
            <label for="newiny[]">Wybierz nowe testy wejściowe:</label>
            <input type="file" name="newiny[]" multiple="multiple" required><br/>';
            if(isset($_SESSION['e_iny']))
            {
                echo '<span class="error">'.$_SESSION['e_iny'].'</span><br/>';
                unset($_SESSION['e_iny']);
            }
            echo '<br/>
            
            <label for="newouty[]">Wybierz nowe pliki wynikowe:</label>
            <input type="file" name="newouty[]" multiple="multiple" required><br/>';
            if(isset($_SESSION['e_outy']))
            {
                echo '<span class="error">'.$_SESSION['e_outy'].'</span><br/>';
                unset($_SESSION['e_outy']);
            }
            echo '<br/>

            <span>Limity:</span>
            <input type="text" name="timelimit" style="width: 50px; text-align: right; margin-left: 15px;" required> s
            <input type="text" name="memorylimit" style="width: 50px; text-align: right; margin-left: 15px;" required> MB
            <label for="startpoints" style="margin-left: 35px; padding-right: 10px;">Suma punktów:</label>
            <input type="text" name="startpoints" style="width: 50px; margin-right: 50px;" required>
            <input type="hidden" name="id_task" value="'.$id_task.'">
            <input type="hidden" name="newtests" value="1">
            <input type="submit" value="Zapisz">';
            if(isset($_SESSION['e_limit']) || isset($_SESSION['e_startpoints']))
                echo '<br/><br/>';
            if(isset($_SESSION['e_limit']))
            {
                echo '<span class="error" style="margin-right: 40px;">'.$_SESSION['e_limit'].'</span>';
                unset($_SESSION['e_limit']);
            }
            if(isset($_SESSION['e_startpoints']))
            {
                echo '<span class="error">'.$_SESSION['e_startpoints'].'</span>';
                unset($_SESSION['e_startpoints']);
            }
            if(isset($_SESSION['success_edit_task_tests']))
            {
                echo $_SESSION['success_edit_task_tests'];
                unset($_SESSION['success_edit_task_tests']);
            }
        echo '</form>
        </div>

        
        <p style="margin-top: 20px; margin-bottom: 3px;">Edytuj testy:</p>
        <div class="borderinedit">';

        $conffile = $_SERVER['DOCUMENT_ROOT'].'/tasks/'.$id_task.'/conf.txt';

        if(file_exists($conffile))
        {
            $plik = file($conffile);
            $lp=0;
            $ilewierszy = count($plik)-1;

            echo '<div style="width: 100%; border-bottom: 3px black dotted; margin-bottom: 10px; padding-bottom: 10px;">
                <form method="POST" action="functions/edit_task.php">
                    <p style="margin: 0 0 8px 0;"><span style="font-weight: bold;">Edytuj dla wszystkich testów:</span>
                        <label style="margin-left: 40px; padding-right: 10px;" for="timelimitFORALL">Limit czasu:</label>
                        <input type="text" name="timelimitFORALL" style="width: 100px; text-align: right;"> s
                    </p>
                    <p style="margin: 0 0 8px 0;">
                        <label style="padding-right: 10px;" for="memorylimitFORALL">Limit pamięci:</label>
                        <input type="text" name="memorylimitFORALL" style="width: 100px; text-align: right;"> MB
                        <label style="margin-left: 40px; padding-right: 10px;" for="sumofpoints">Suma punktów:</label>
                        <input type="text" name="sumofpoints" style="width: 160px; text-align: center;" placeholder="'.$sum_of_points.'">
                    </p>
                    <input type="hidden" name="id_task" value="'.$id_task.'">
                    <input type="hidden" name="setFORALL" value="1">
                    <input type="submit" value="Zatwierdź">';
                    if(isset($_SESSION['success_edit_task_setFORALL']))
                    {
                        echo $_SESSION['success_edit_task_setFORALL'];
                        unset($_SESSION['success_edit_task_setFORALL']);
                    }
                    if(isset($_SESSION['e_limitFORALL']) || isset($_SESSION['e_pointsFORALL']))
                        echo '<br/><br/>';
                    if(isset($_SESSION['e_limitFORALL']))
                    {
                        echo '<span class="error" style="margin-right: 40px;">'.$_SESSION['e_limitFORALL'].'</span>';
                        unset($_SESSION['e_limitFORALL']);
                    }
                    if(isset($_SESSION['e_pointsFORALL']))
                    {
                        echo '<span class="error">'.$_SESSION['e_pointsFORALL'].'</span>';
                        unset($_SESSION['e_pointsFORALL']);
                    }
                echo '</form>
            </div>';

            $sz1 = 60;
            $sz2 = 210;
            $sz3 = 210;
            $sz4 = 230;
            $tal = 'text-align: left;';
            $tac = 'text-align: center;';

            echo '<table style="width: 700px;">
                  <tr>
                  <th style="width: '.$sz1.'px;">
                    Nr<br/>testu
                  </th>
                  <th style="width: '.$sz2.'px;">
                    Limit czasu
                  </th>
                  <th style="width: '.$sz3.'px;">
                    Limit pamięci
                  </th>
                  <th style="width: '.$sz4.'px;">
                    Liczba punktów<br/>za test
                  </th>
                  </tr>
                  </table>
                  
                  <form method="POST" action="functions/edit_task.php">
                  <table style="width: 700px; border-spacing: 0 15px;">';

            for($i=0;$i<$ilewierszy;$i+=1)
            {
                if(substr($plik[$i],0,1)=='#')
                {
                    echo '<tr>
                    <td style="width: '.$sz1.'px; '.$tac.'">
                        '.$lp.'
                    </td>
                    <td style="width: '.$sz2.'px; '.$tac.'">
                        <input type="text" style="width: 100px; text-align: right;" name="timelimit'.$lp.'" value="'.doubleval($plik[$i+2]).'" required> s
                    </td>
                    <td style="width: '.$sz3.'px; '.$tac.'">
                        <input type="text" style="width: 100px; text-align: right;" name="memorylimit'.$lp.'" value="'.doubleval($plik[$i+3]).'" required> MB
                    </td>
                    <td style="width: '.$sz4.'px; '.$tac.'">
                    <input type="text" style="width: 160px; text-align: center;" name="points'.$lp.'" value="'.doubleval($plik[$i+1]).'" required>
                    </td>';
                    echo '</tr>';
                    $lp+=1;
                }
            }
            echo '</table>
            <input type="hidden" name="id_task" value="'.$id_task.'">
            <input type="hidden" name="seteverytest" value="1">
            <input type="submit" value="Zapisz">';
            if(isset($_SESSION['success_edit_task_everytest']))
            {
                echo $_SESSION['success_edit_task_everytest'];
                unset($_SESSION['success_edit_task_everytest']);
            }
            if(isset($_SESSION['e_seteverytest']))
            {
                echo '<span class="error" style="margin-left: 20px;">'.$_SESSION['e_seteverytest'].'</span>';
                unset($_SESSION['e_seteverytest']);
            }
            echo '</form>';
        }else
        {
            echo "Błąd otwarcia pliku konfiguracyjnego!";
        }

        echo '</form>
        </div>';
    }

?>