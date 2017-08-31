<?php



if(isset($_POST['indeks']) && isset($_POST['ip']) 
	&& isset($_POST['mac']) && isset($_POST['data']) 
&& isset($_POST['nazwa']) && isset($_POST['lokalizacja']) 
&& isset($_POST['uwagi'])){
		$indeks = $_POST['indeks'];
		$ip = $_POST['ip'];
		$mac = $_POST['mac'];
		$data = $_POST['data'];
		$nazwa = $_POST['nazwa'];
		$lokalizacja = $_POST['lokalizacja'];
		$uwagi = $_POST['uwagi'];
		
	$servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "nowe_hosty";
    $conn = @mysqli_connect($servername, $username, $password, $dbname);
    if(!$conn){
            die("Błąd połączenia z bazą: ".mysqli_connect_error());
	}
	
	echo $indeks."<br>";
	echo $ip."<br>";
	echo $mac."<br>";
	echo $data."<br>";
	echo $nazwa."<br>";
	echo $lokalizacja."<br>";
	echo $uwagi."<br>";
	
	echo '<a href="z_xml_do_bazy_proc.php">powrót</a>';
	
    $zapytanie_znane = "INSERT INTO znane_hosty(nazwa_hosta, mac_address, 
    data_dodania, lokalizacja, uwagi, VLAN, ip_address) 
    VALUES('".$nazwa."', '".$mac."', '".$data."', '".$lokalizacja."', '". $uwagi."', '--', '".$ip."')";
			
			
	// $zapytanie_znane = "INSERT INTO znane_hosty(nazwa_hosta, mac_address, 
    // data_dodania, lokalizacja, uwagi, VLAN, ip_address) 
    // VALUES('nazwa', 'jakis mak', 'data dzisiejsza', 'kuchnia', 'uwaga!', '--', 'jakiś ip')";
    
    $rezultat_znane = mysqli_query($conn, $zapytanie_znane);
	var_dump($rezultat_znane);
	
	mysqli_close($conn);
	
	//header('location: z_xml_do_bazy_proc.php');
}else{
	header('location: mur.php');
}