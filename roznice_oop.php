<?php
    class Roznice{
          public $db;
          private $class_db_file;
        
        public function __construct(){
        $this->class_db_file = 'db.php';

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
        
        public function wyswietlRoznice(){
            $res = $this->znajdzRoznice();
            $licznik=1;
            $tabelka = "";
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
        public function __destruct(){
            
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
$roznica = new Roznice();
echo $roznica->wyswietlRoznice();
?>
    </table>
 <a href="zakoncz_oop.php">Zakończ</a>
</body>
</html>