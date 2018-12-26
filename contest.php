<?php
	session_start();

	if((!isset($_SESSION['zalogowany'])) || ($_SESSION['zalogowany']==false))
	{
		header('Location: /');
		exit();
	}

	require_once "functions/connect.php";

	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

	if($polaczenie->connect_errno!=0)
	{
		echo "Error: ".$polaczenie->connect_errno;
	}
	else
	{
		mysqli_set_charset($polaczenie,"utf8");
		$polaczenie->query('SET NAMES utf8');

		$id_user = $_SESSION['id_user'];
	}

?>

<!DOCTYPE HTML>
<html lang="pl">
<?php
	include_once ('templates/head.php');
	echo '<script type="text/javascript" src="/scripts/timer.js"></script>';
?>
<body>
	<div id="container">
		<?php
			include_once ('templates/top.php');
		?>
		<div id="center">
			<div id="topmenu">
				<div id="logged">
					Zalogowany użytkownik:<br/>
					<p class="grubiejwmenu"> > <?php echo $_SESSION['login']; ?></p>					
				</div>
				<div id="whatcontest">
					<?php
						if(isset($_GET['id']))
						{
							$shortcut_contest = $_GET['id'];
							$zapytanie = $polaczenie->query("SELECT id_contest,title_contest,password, time_from, time_to,timer FROM contests WHERE shortcut_contest='$shortcut_contest'");
							
							if(mysqli_num_rows($zapytanie)==0)
							{
								header('Location: /');
							}

							$rezultat = $zapytanie->fetch_assoc();
							$id_contest = $rezultat['id_contest'];
							$password_contest=$rezultat['password'];
							$timer = $rezultat['timer'];

							echo "Wybrane zawody: <br/>";
							echo "<p class=\"grubiejwmenu\"> > ".$rezultat['title_contest']."</p>";
						}else
					?>
				</div>
				<div id="icons_user">
					<a href="/account"><i class="icon-user"></i></a></i><a href="/logout"><i class="icon-logout"></i></a>
				</div>
				<div style="clear: both;"></div>
			</div>
			<div id="menu">
				<?php
					if(isset($_GET['id']) || isset($_GET['account']))
					{
						if(isset($_GET['submit']))
							echo '[ <a href="/'.$_GET['id'].'">POWRÓT</a> ]<br/><br/>';
						else if(isset($_GET['contestsubmits']))
							echo '[ <a href="/'.$_GET['id'].'">POWRÓT DO ZADAŃ</a> ]<br/><br/>';
						else
							echo '[ <a href="/contest">WYBÓR CONTESTU</a> ]<br/><br/>';
					}

					if(isset($_GET['id']))
					{
						echo "Czas rozpoczęcia: <br/>";
						echo "<p class=\"grubiejwmenu\"> > ".$rezultat['time_from']." </p>";
						echo "Czas zakończenia: <br/>";
						echo "<p class=\"grubiejwmenu\"> > ".$rezultat['time_to']." </p><br/>";
						if($timer==1)
						{
							echo '
							<script type="text/javascript">
								var data = "'.$rezultat['time_to'].'";
								timer(data);
							</script>
							<span id="timer"></span><br/><br/>';
						}
						echo '[ <a href="/'.$shortcut_contest.'/mysubmits">MOJE WYSŁANIA</a> ]<br/><br/>';
						echo '[ <a href="/'.$shortcut_contest.'/submits">WYSŁANIA</a> ]<br/><br/>';
					}

				?>
			</div>
			<div id="content">

				<script type="text/javascript">
					function myFunction() 
					{
				    	var x = document.getElementById("archiwum");
				    	x.style.display = "block";
					}
				</script>
				
				<?php

				if(empty($_GET))
				{
					echo "<b>Wybierz zawody z listy: </b><br/><br/>";

					$zapytanie = $polaczenie->query("SELECT shortcut_contest, title_contest, time_to FROM contests WHERE time_to >= CURRENT_TIMESTAMP AND visibility=1 ORDER BY id_contest DESC");

					while($wiersz = mysqli_fetch_row($zapytanie))
					{ 
						echo "&bull; <a style=\"font-weight: bold;\" href=\"/$wiersz[0]\"> $wiersz[0] - $wiersz[1]</a><br/><br/>";
					}

					$zapytanie = $polaczenie->query("SELECT shortcut_contest, title_contest, time_to FROM contests WHERE time_to < CURRENT_TIMESTAMP AND visibility=1 ORDER BY time_to ASC");

					echo '<span class="sztucznylink" onclick="myFunction()">Archiwum:</span><br/><br/>';

					echo '<div id="archiwum">';
					while($wiersz = mysqli_fetch_row($zapytanie))
					{ 
						echo "&bull; <a href=\"/$wiersz[0]\"> $wiersz[0] - $wiersz[1]</a><br/><br/>";
					}
					echo '</div>';
				}

				if(isset($_GET['id']))
				{
					include_once ('functions/checkaccesstocontest.php'); // $AccessToContest true lub false
				}

				if(isset($_GET['id']) && (!(isset($_GET['submit']))) && (!(isset($_GET['contestsubmits']))))
				{
					include_once ('pages/view_contest.php');
				}

				if(isset($_GET['account']))
				{
					include_once ('pages/account.php');
				}

				if(isset($_GET['submit']))
				{
					include_once ('pages/submit.php');
				}
				if(isset($_GET['contestsubmits']))
				{
					include_once ('pages/submits_contest.php');
				}


				?>
			</div>
			
		</div>
		<div style="clear: both;"></div>
		<?php
			include_once ('templates/footer.php');
		?>
	</div>
</body>
</html>

<?php $polaczenie->close(); ?>