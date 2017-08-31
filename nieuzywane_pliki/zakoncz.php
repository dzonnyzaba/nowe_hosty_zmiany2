<?php

	require_once "connection.php";

	$conn = @mysqli_connect($servername, $username, $password, $dbname);
	if(!$conn){
		die("Błąd połączenia z bazą danych". mysqli_connect_error());
	}
	
	$zapytanie_zakoncz = "DROP TABLE tmp";
	
	$rezultat_zakoncz = mysqli_query($conn, $zapytanie_zakoncz);
	
	mysqli_close($conn);
	header('Location: index.php');