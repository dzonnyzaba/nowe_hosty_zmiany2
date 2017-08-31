<?php
require_once "connection.php";


	$conn = @mysqli_connect($servername, $username, $password, $dbname);
	if(!$conn){
		die("Błąd połączenia z bazą danych". mysqli_connect_error());
	}
	$do_pliku = "";
	$zapytanie_generuj = "SELECT * FROM znane_hosty";
	
	$rezultat_generuj = mysqli_query($conn, $zapytanie_generuj);
	
	while($row = mysqli_fetch_array($rezultat_generuj)){
		$do_pliku .= "host ".$row['nazwa_hosta']." {fixed address ".$row['ip_address'].
		";hardware ethernet ".$row['mac_address'].";}"."\n\r";
	}
	
	//var_dump($rezultat_generuj);
	//echo $do_pliku;
	mysqli_free_result($rezultat_generuj);
	
	mysqli_close($conn);
	$nazwa_pliku = "kat/hosty_z_bazy.conf";
	file_put_contents($nazwa_pliku, $do_pliku);
	header('Location: index.php');