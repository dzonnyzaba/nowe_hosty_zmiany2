<?php
	
	//ob_start();
	$suma = 0;

	require_once "funkcje.php";
	require_once "connection.php";
	 
	$tabela_z_xmla = array();
    $xml = simplexml_load_file('out.xml');
	$attrdate = $xml->runstats->finished->attributes();
	$datatab = $attrdate['timestr'];
	$data = utworzDate($datatab);
	$licznik_wierszy = 0;
    foreach($xml->host as $host){
        if(isset($host->address[1]) && isset($host->address[0])){
			$attrmac = $host->address[1]->attributes();
			$ajpi = $host->address[0]->attributes();
			$tabela_z_xmla[$licznik_wierszy]['mac'] = (string)$attrmac['addr'];
			$tabela_z_xmla[$licznik_wierszy]['ip'] = (string)$ajpi['addr'];
			$tabela_z_xmla[$licznik_wierszy]['data'] = $data;
			$licznik_wierszy++;	
		}
	}
	
	
	$conn = @mysqli_connect($servername, $username, $password, $dbname);
	if(!$conn){
		die("Błąd połączenia z bazą: ".mysqli_connect_error());
	}
	$zapytanie = "SELECT mac_address FROM znane_hosty";
	$rezultat_porownanie = mysqli_query($conn, $zapytanie);
	//drukuje to co pobiera z bazy
	/*
	$nr=1;
	while($row=mysqli_fetch_array($rezultat_porownanie)){
		echo $nr." -> ".$row['mac_address']."<br>";
		$nr++;
	}
	*/
	
	//porównuje i wywala z tablicy indeks który występuje w bazie
	while($row = mysqli_fetch_array($rezultat_porownanie)){
		for($i=0; $i<count($tabela_z_xmla); $i++){
			if($tabela_z_xmla[$i]['mac']==$row['mac_address']){
				echo "<br>wywalam $i---".$tabela_z_xmla[$i]['mac'];
				unset($tabela_z_xmla[$i]);
				array_splice($tabela_z_xmla, 1, 1);
				
			}
		}
	}
	
	// $nr=1;//drukuje to co pozostaje w tablicy
	// foreach($tabela_z_xmla as $t){
		// echo "$nr ".$t['ip']." ||||| ".$t['mac']."<br>";
		// $nr++;
	// }
	
	$licznik=1;
	$indeks=0;
    $str = "<table border=1>\n";
    $str .= "<tr>\n"
            . "<th>lp.</th>\n"
            . "<th>adres ip</th>\n"
            . "<th>adres mac</th>\n"
            . "<th></th>\n"
            . "</tr>\n";
    foreach ($tabela_z_xmla as $t) {
        $str.= "<tr>\n"
                ."<td>$licznik</td>\n"
                . "<td>".$t['ip']."</td>\n"
                . "<td>".$t['mac']."</td>\n"
                . "<td>"
                . "<form method=\"post\" action=\"dodaj_host_proc.php\">\n"
                . "nazwa: <input type=\"text\" name=\"nazwa\">\n"
                . "lokalizacja: <input type=\"text\" name=\"lokalizacja\">\n"
                . "uwagi: <input type=\"text\" name=\"uwagi\">\n"
                . "<input type=\"hidden\" name=\"indeks\" value=".$indeks.">\n"
                . "<input type=\"hidden\" name=\"ip\" value=".$t['ip'].">\n"
                . "<input type=\"hidden\" name=\"mac\" value=".$t['mac'].">\n"
                . "<input type=\"hidden\" name=\"data\" value=".$data.">\n"
                . "<input type=\"submit\" value=\"dodaj do bazy\">\n"
                . "</form>\n"
                . "<td></tr>\n";
        $licznik++;
		$indeks++;
    }
    $str.= "</table>";
    echo $str;
	

	
/*	if(isset($_POST['indeks']) && isset($_POST['ip']) && isset($_POST['mac']) && isset($_POST['data']) && isset($_POST['nazwa']) && isset($_POST['lokalizacja']) && isset($_POST['uwagi'])){
		$indeks = $_POST['indeks'];
		$ip = $_POST['ip'];
		$mac = $_POST['mac'];
		$data = $_POST['data'];
		$nazwa = $_POST['nazwa'];
		$lokalizacja = $_POST['lokalizacja'];
		$uwagi = $_POST['uwagi'];
		//unset($tabela_z_xmla[$indeks]);
        //array_splice($tablica, 1, 1);
		echo $indeks." ".$ip." ".$mac." ".$data." ".$nazwa. " " .$lokalizacja. " " .$uwagi;
                //header('location: dodaj_host_fast.php?');
	}else{
		//return false;
		echo "nic do wyświetlenia";
	}
*/

?>
<!doctype html>
<html>
     <head>
          <meta charset="UTF-8" />
     </head>
     <body>
	 <table border=1>
		<tr>
			<th>id</th><th>adres IP</th><th>adres MAC</th><th></th>
		</tr>
		<?php
			// while($row = mysqli_fetch_array($rezultat)){
				// echo "<tr>";
				// echo "<td>".$row['id_nowego_hosta']."</td><td>".
				// $row['nowy_ip']."</td><td>".$row['nowy_mac'].'</td><td><a href="dodaj_host.php?id='.
				// $row['id_nowego_hosta'].'">Dodaj hosta do bazy</a></td>';
				// echo "</tr>";
			// }
			
			//mysqli_free_result($rezultat);
	
			mysqli_close($conn);
		?>
	 </table>
	 <a href="zakoncz.php">Zakończ</a>
     </body>
</html>
?>