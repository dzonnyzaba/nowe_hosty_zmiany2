<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "nowe_hosty";
	 $conn = @mysqli_connect($servername, $username, $password, $dbname);
	 if(!$conn){
		 die("Błąd połączenia z bazą: ".mysqli_connect_error());
	 }
/*	 for($i=1; $i<20; $i++){
	 $zapins = "INSERT INTO `tmp`(`id_nowego_hosta`, `nowy_ip`, `nowy_mac`, `data`, `VLAN`) VALUES ('".$i."', '10.10.10.".$i."', '192.168.0.".$i."', '2017-08-23', '10')";
	 $res = mysqli_query($conn, $zapins);
	 }
*/

	$tablica = array("192.168.0.3", "192.168.0.0", "192.168.1.20", "192.168.0.11", 
	"192.168.0.5", "172.168.0.5", "192.168.0.1");
	$tablica_nowa = array();
	$zapsel = "SELECT nowy_mac FROM tmp";
	
	//$res = mysqli_query($conn, $zapsel);
/*		while($row = mysqli_fetch_array($res)){
			foreach($tablica as $t){
				if($row['nowy_mac']!=$t){
					$tablica_nowa[]=$t;
			}
		}
	}*///dla każdego rekordu z bd przebiega tablica
	
	
	//dla każdego inedksu tablicy przelatuje baza danych
	$dl = count($tablica);
	echo $dl;
	for($i=0; $i<$dl; $i++){
		$res = mysqli_query($conn, $zapsel);
		echo "element tablicy: ".$tablica[$i]."<br>";
		while($row=mysqli_fetch_array($res)){
			//echo $row['nowy_mac']."<br>";
 			if($row['nowy_mac']!=$tablica[$i]){
 			//$tablica_nowa[$i]=$tablica[$i];
			$tablica_nowa[$i] = $tablica[$i];
			$tablica[$i]=0;
			//unset($tablica[$i]); 
			//echo $tablica[$i]."-->".$row['nowy_mac']."<br>";
		} 
	}
	
	}
	
	
	echo "<pre>";
	var_dump($tablica_nowa);
	echo "<pre>";