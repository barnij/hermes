<?php
	
	$zapytanie = $polaczenie->query('SELECT name FROM users where id_user = 3');
	$rezultat = $zapytanie->fetch_assoc();
	echo $rezultat['name'];

?>

<p>No halo</p>