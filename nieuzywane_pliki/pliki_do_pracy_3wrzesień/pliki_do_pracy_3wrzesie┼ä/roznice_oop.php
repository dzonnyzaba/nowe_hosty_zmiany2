<?php
    class RozniceA{
          public $db;
          private $class_db_file;
          
          private $zapytanie_czysc_tmp;
          private $zapytanie_zeruj_tmp;
    
          private $zapytanie_czysc_znane;
          private $zapytanie_zeruj_znane;
        
        public function __construct(){
            $this->class_db_file = 'db.php';
        
            $this->zapytanie_czysc_tmp = "DELETE FROM tmp";
            $this->zapytanie_zeruj_tmp = "ALTER TABLE tmp AUTO_INCREMENT=0";
        
            $this->zapytanie_czysc_znane = "DELETE FROM znane_hosty";
            $this->zapytanie_zeruj_znane = "ALTER TABLE znane_hosty AUTO_INCREMENT=0";

            if(file_exists($this->class_db_file)){
                require_once($this->class_db_file);
                $this->db = new db();
            }else{
                echo "brak pliku z klasą do łączenia z db";
            }
        }

        
        public function znajdzRoznice(){
	$zapytanie = "SELECT * FROM tmp WHERE nowy_mac NOT IN (SELECT mac_address FROM znane_hosty)";
        //SELECT * FROM tmp WHERE nowy_mac NOT IN (SELECT mac_address FROM znane_hosty) AND nowy_ip NOT IN(SELECT ip_address FROM znane_hosty)
	$rezultat = mysqli_query($this->db->connection, $zapytanie);

        return $rezultat;
        }
        
        public function TworzTabeleRoznice(){
            $res = $this->znajdzRoznice();
//            $licznik=1;
//            $tabelka = "";
            while($row = mysqli_fetch_array($res)){
                $sql_wypelnij_roznice = "INSERT INTO roznice(nowy_ip, nowy_mac, data, VLAN) values('".$row['nowy_ip']."', '"
                    .$row['nowy_mac']."', '".$row['data']."', '".$row['VLAN']."')";
                $rezultat_wstawiaj = mysqli_query($this->db->connection, $sql_wypelnij_roznice);
//                    $tabelka .= "<tr><td>$licznik</td>";
//                    $tabelka .= "<td>".$row['id_nowego_hosta']."</td><td>".
//                    $row['nowy_ip']."</td><td>".$row['nowy_mac'].'</td><td><a href="dodaj_host_oop.php?id='.
//                    $row['id_nowego_hosta'].'">Dodaj hosta do bazy</a></td>';
//                    $tabelka .= "</tr>";
//                    $licznik++;
            }
            mysqli_free_result($res);
            $this->czyscTabeleZnaneItmp();
            //echo $tabelka;
        }
        
        public function wyswietlTabeleRoznice(){
            $licznik=1;
            $tabelka = "";
            $sql_wyswietl_tab_roznice = "SELECT * FROM roznice";
            $res = mysqli_query($this->db->connection, $sql_wyswietl_tab_roznice);
            while($row = mysqli_fetch_array($res)){
                    $tabelka .= "<tr><td>$licznik</td>";
                    $tabelka .= "<td>".$row['id_nowego_hosta']."</td><td>".
                    $row['nowy_ip']."</td><td>".$row['nowy_mac'].'</td><td><a href="dodaj_host_oop.php?id='.
                    $row['id_nowego_hosta'].'">Dodaj hosta do bazy</a></td>';
                    $tabelka .= "</tr>";
                    $licznik++;
            }
            mysqli_free_result($res);
            return $tabelka;
        }
        
        
        public function czyscTabeleZnaneItmp(){
			$this->zapiszDoLoga("czyszcze tablice");
            $rezultat_czysc_znane = mysqli_query($this->db->connection, $this->zapytanie_czysc_znane);        
            $rezultat_zeruj_znane = mysqli_query($this->db->connection, $this->zapytanie_zeruj_znane);
        
            $rezultat_czysc_tmp = mysqli_query($this->db->connection, $this->zapytanie_czysc_tmp);        
            $rezultat_zeruj_tmp = mysqli_query($this->db->connection, $this->zapytanie_zeruj_tmp);
			$this->zapiszDoLoga($rezultat_czysc_znane." ".$rezultat_zeruj_znane." ".$rezultat_czysc_tmp
			." ".$rezultat_zeruj_tmp);
        }
        
        public function __destruct(){
            
        }
		
		private function zapiszDoLoga($komunikat){
			file_put_contents('roznice_oop_log.txt', $komunikat."\r\n",  FILE_APPEND);
		}
    }
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
$roznica = new RozniceA();
//$roznica->TworzTabeleRoznice();
echo $roznica->wyswietlTabeleRoznice();
?>
    </table>
 <a href="zakoncz_oop.php">Zakończ</a>
</body>
</html>