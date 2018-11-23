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
		if($polaczenie->query("UPDATE smiglo SET data = current_timestamp WHERE id=0"))
		{
			
		}else
		{
			echo "Error: ".$polaczenie->connect_errno;
		}

		$polaczenie->close();
	}

?>