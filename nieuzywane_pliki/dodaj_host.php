<?php
require_once "connection.php";

if(isset($_GET['id'])){
	$id = $_GET['id'];
	?>
	
	<form method="post">
		nazwa: <input type="text" name="nazwa"/>
		lokalizacja: <input type="text" name="lokalizacja"/>
		uwagi: <input type="text" name="uwagi"/>
		<input type="submit" value="dodaj"/>
	</form>
<?php
	if((isset($_POST['nazwa']) && !empty($_POST['nazwa'])) && 
	(isset($_POST['lokalizacja']) && !empty($_POST['lokalizacja'])) && 
	(isset($_POST['uwagi']) && !empty($_POST['uwagi']))){
		$nowa_nazwa = $_POST['nazwa'];
		$nowa_lokalizacja = $_POST['lokalizacja'];
		$nowe_uwagi = $_POST['uwagi'];
	$conn = @mysqli_connect($servername, $username, $password, $dbname);
	if(!$conn){
		die("Błąd połączenia z bazą danych". mysqli_connect_error());
	}
	
	$zapytanie_nowe = "SELECT * FROM tmp WHERE id_nowego_hosta=$id";
	$rezultat_nowe = mysqli_query($conn, $zapytanie_nowe);
	$row = mysqli_fetch_array($rezultat_nowe);
	//var_dump($row);
	
	
	$zapytanie_znane = "INSERT INTO znane_hosty(nazwa_hosta, mac_address, 
	data_dodania, lokalizacja, uwagi, VLAN, ip_address) 
	VALUES('".$nowa_nazwa."', '".$row['nowy_mac']."', '".$row['data']."', '".
	$nowa_lokalizacja."', '".$nowe_uwagi."', '".$row['VLAN']."', '".$row['nowy_ip']."')";
	$rezultat_znane = mysqli_query($conn, $zapytanie_znane);
	
	$zapytanie_nowe_kasuj = "DELETE FROM tmp WHERE id_nowego_hosta=$id";
	$rezultat_nowe_kasuj = mysqli_query($conn, $zapytanie_nowe_kasuj);
	
	if($rezultat_znane){
		echo "dołączono host do bazy";
	}else{
		echo "nie udało się dołączyc hosta do bazy";
	}
	
	mysqli_free_result($rezultat_nowe);
	mysqli_close($conn);
	
	header('location: roznice.php');
	exit();
	}
}else{
	header('location: roznice.php');
	exit();
}


