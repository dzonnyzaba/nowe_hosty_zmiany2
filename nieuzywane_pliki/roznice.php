<?php
 require_once "connection.php";
 
	
	$conn = @mysqli_connect($servername, $username, $password, $dbname);
	if(!$conn){
		die("Błąd połączenia z bazą: ".mysqli_connect_error());
	}
	$zapytanie = "SELECT * FROM tmp WHERE nowy_mac NOT IN (SELECT mac_address FROM znane_hosty)";
	$rezultat = mysqli_query($conn, $zapytanie);
?>
<!doctype html>
<html>
     <head>
          <meta charset="UTF-8" />
     </head>
     <body>
	 <table border=1>
		<tr>
			<th></th><th>id</th><th>adres IP</th><th>adres MAC</th><th></th>
		</tr>
		<?php
			$licznik=1;
			while($row = mysqli_fetch_array($rezultat)){
				echo "<tr><td>$licznik</td>";
				echo "<td>".$row['id_nowego_hosta']."</td><td>".
				$row['nowy_ip']."</td><td>".$row['nowy_mac'].'</td><td><a href="dodaj_host.php?id='.
				$row['id_nowego_hosta'].'">Dodaj hosta do bazy</a></td>';
				echo "</tr>";
				$licznik++;
			}
			
			mysqli_free_result($rezultat);
	
			mysqli_close($conn);
		?>
	 </table>
	 <a href="zakoncz.php">Zakończ</a>
     </body>
</html>