<?php
	$servername = 'localhost';
	$dbusername = 'TBD';
	$dbpassword = 'TBD';
	$dbname = 'candyland';
	$conn = mysqli_connect($servername, $dbusername, $dbpassword, $dbname);

	if (!$conn) {
		die("Kontakt med databasen misslyckades: " . mysqli_connect_error());
	}
?>