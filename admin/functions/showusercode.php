<?php
	session_start();

	if((!(isset($_GET['submit']))) || ($_GET['submit']==0) || (!(isset($_SESSION['zalogowanyadmin']))) )
	{
		header('Location: /admin');

		exit();
	}

	require_once ('../../functions/connect.php');

	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

	if($polaczenie->connect_errno!=0)
	{
		echo "Error: ".$polaczenie->connect_errno;
	}
	else
	{
		mysqli_set_charset($polaczenie,"utf8");
		$polaczenie->query('SET NAMES utf8');

		$id_submit = $_GET['submit'];

		echo '<!DOCTYPE html>
		<html>
			<head>
				<title>'.$id_submit.' - HERMES</title>
				<link rel="shortcut icon" type="image/png" href="/favicon.png"/>
				<link rel="stylesheet" href="/css/hightlight/default.css">
				<script src="/scripts/highlight.pack.js"></script>
				<script>hljs.initHighlightingOnLoad();</script>
			</head>
			<body>';
			echo '<pre><code class="auto">';

			$adres = $_SERVER['DOCUMENT_ROOT'].'/submits/'.$id_submit;

			$czyistnieje = true;

			if(file_exists($adres.'.cpp'))
				$adres=$adres.'.cpp';
			else if(file_exists($adres.'.py'))
				$adres=$adres.'.py';
			else if(file_exists($adres.'.mrram'))
				$adres=$adres.'.mrram';
			else if(file_exists($adres.'.bap'))
				$adres=$adres.'.bap';
			else
			{
				echo "Błąd otwarcia pliku. Skontaktuj się z administratorem.";
				$czyistnieje = false;
			}

			if($czyistnieje)
			{
				$plik = file($adres);
				$ile = count($plik);
				
				for($i=0;$i<$ile;$i++)
				{
					$plik[$i] = htmlentities($plik[$i], ENT_QUOTES, "UTF-8");
					//$plik[$i] = preg_replace('#\r\n?#', "\n", $plik[$i]);
					//$plik[$i] = str_replace(' ', '&nbsp;', $plik[$i]);
					echo $plik[$i];
					//echo '<br>';
				}
			}


		echo '</code></pre></body>
		</html>';
	}

	$polaczenie->close();
?>