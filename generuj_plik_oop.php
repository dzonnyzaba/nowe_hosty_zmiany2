<?php

class Generuj{
    public $db;
    private $class_db_file;
    private $do_pliku;
    private $nazwa_pliku;
    
    public function __construct() {
        $this->do_pliku = "";
        $this->nazwa_pliku = "kat/hosty_z_bazy.conf";
        $this->class_db_file = 'db.php';

        if(file_exists($this->class_db_file)){
            require_once($this->class_db_file);
            $this->db = new db();
        }else{
            echo "brak pliku z klasą do łączenia z db";
        }
    }
    
    public function generujPlik(){
        $zapytanie_generuj = "SELECT * FROM znane_hosty";
        
        $rezultat_generuj = mysqli_query($this->db->connection, $zapytanie_generuj);
        
        while($row = mysqli_fetch_array($rezultat_generuj)){
            $this->do_pliku .= "host ".$row['nazwa_hosta']." {fixed address ".$row['ip_address'].
            ";hardware ethernet ".$row['mac_address'].";}"."\r\n";
	}
        
        mysqli_free_result($rezultat_generuj);
	
        //mysqli_close($this->db->connection);
        file_put_contents($this->nazwa_pliku, $this->do_pliku);
        header('location: index.php');
    }
}
$generuj = new Generuj();
$generuj->generujPlik();
