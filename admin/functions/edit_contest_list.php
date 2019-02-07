<?php
session_start();

	if ((!isset($_POST['id_contest'])) || (!isset($_SESSION['zalogowanyadmin'])))
	{
		header('Location: /admin');
		exit();
    }
    
    $lokalizacja = 'Location: /admin/konsola.php?tool=list_contest&edit_contest='.$_POST['id_contest'];
    header($lokalizacja);

    $id_contest = $_POST['id_contest'];

    require_once "../../functions/connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);


    $polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);
    if($polaczenie->connect_errno!=0)
    {
            echo "Error: ".$polaczenie->connect_errno;
    }
    else //połączenie nawiązane!
    {
        mysqli_set_charset($polaczenie,"utf8");
        $polaczenie->query('SET NAMES utf8');

        if(!$polaczenie->query("DELETE FROM contest_list WHERE id_contest='$id_contest'"))
            echo "Error: ".$polaczenie->connect_errno;
        
        if(!empty($_POST['listoftasks'])) {
            foreach($_POST['listoftasks'] as $id_task) {
                if(!$polaczenie->query("INSERT INTO contest_list (id_task, id_contest) VALUES ('$id_task','$id_contest')"))
                    echo "Error: ".$polaczenie->connect_errno;
            }
        }

        $_SESSION['success_edit_contest_list']='Zapisano.';
    }
        
?>