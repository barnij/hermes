<?php

	$host = "localhost";
	$db_user = "root";
	$db_password = "";
	$db_name = "smiglo";

	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);

	if($polaczenie->connect_errno!=0)
	{
		echo "Error: ".$polaczenie->connect_errno;
	}
	else
	{
		$zapytanie=$polaczenie->query("SELECT data FROM smiglo WHERE id=0");
		$rezultat = $zapytanie->fetch_assoc();
		$data = $rezultat['data'];

		$datetimefrombase = date_create($data);
		$datetimenow = date_create(date('Y-m-d H:i:s'));

		$interval = date_diff($datetimefrombase, $datetimenow);
    
    	$ile = $interval->format('%d');

		$polaczenie->close();
	}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<title>ŚMIGŁO ŚMIGA!</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>
	<div id="container">
		<div class="center">
			<p id="tekst">Ile dni nie pił Śmigło?</p>
			<p id="licznik"><?php echo $ile; ?></p>
		</div>
	</div>
</body>
</html>