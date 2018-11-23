<?php
	
	$zapytanie = $polaczenie->query('SELECT id_user, login FROM users ORDER BY id_user DESC LIMIT 1');
	$rezultat = $zapytanie->fetch_assoc();
	echo $rezultat['login'];

?>

<p>No halo</p>