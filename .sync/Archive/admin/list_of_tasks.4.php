<?php
	
	$maxid = $polaczenie->query('SELECT MAX(id_user) FROM users');
	$zapytanie = $polaczenie->query('SELECT login FROM users where id_user = maxid');
	$rezultat = $zapytanie->fetch_assoc();
	echo $rezultat['login'];

?>

<p>No halo</p>