<?php

	$zapytanie = $polaczenie->query("SELECT id FROM permissions WHERE id_user='$id_user' AND id_contest='$id_contest'");

	if(($password_contest=="") || (mysqli_num_rows($zapytanie) > 0))
	{
		$AccessToContest=true;
	}else
	{
		$AccessToContest=false;
	}
?>