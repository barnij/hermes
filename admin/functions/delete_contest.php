<?php
  session_start();

  if ((!isset($_POST['TAKusuncontest'])) || (!isset($_SESSION['zalogowanyadmin'])))
	{
		header('Location: /admin');
		exit();
  }

  $lokalizacja = 'Location: /admin/konsola.php?tool=list_contest';
  header($lokalizacja);

  $id_contest = $_POST['id_contest'];
  
  require_once "../../functions/connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);

		try
		{
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
      
      if($polaczenie->connect_errno!=0) //połączenie z DB nienawiązane
			{
				throw new Exception(mysqli_connect_errno());
			}
			else //połączenie nawiązane!
			{
				mysqli_set_charset($polaczenie,"utf8");
        $polaczenie->query('SET NAMES utf8');			
        
        $zapytanie = $polaczenie->query("SELECT id_submit FROM submits WHERE id_contest='$id_contest'");

        while($row = mysqli_fetch_row($zapytanie))
        {
            $resultadres = '/var/www/html/results/'.$row[0].'.txt';

            if(file_exists($resultadres))
              unlink($resultadres);

            $submitadres = '/var/www/html/submits/'.$row[0].'.';

            if(file_exists($submitadres.'cpp'))
              unlink($submitadres.'cpp');
            elseif(file_exists($submitadres.'py'))
              unlink($submitadres.'py');
            elseif(file_exists($submitadres.'bap'))
              unlink($submitadres.'bap');
            elseif(file_exists($submitadres.'mrram'))
              unlink($submitadres.'mrram');
        }

 
        if(!$polaczenie->query("DELETE FROM submits WHERE id_contest='$id_contest'"))
          throw new Exception($polaczenie->error);

        if(!$polaczenie->query("DELETE FROM permissions WHERE id_contest='$id_contest'"))
          throw new Exception($polaczenie->error);

        if(!$polaczenie->query("DELETE FROM contest_list WHERE id_contest='$id_contest'"))
          throw new Exception($polaczenie->error);

        if(!$polaczenie->query("DELETE FROM contests WHERE id_contest='$id_contest'"))
          throw new Exception($polaczenie->error);

        $polaczenie->close();

			}
    }

     catch(Exception $e) //wyświetlanie błędu
		{
			echo '<span style="color: red;">Błąd serwera!</span>';
			echo '<br /> Informacja deweloperska: '.$e;
		}

?>