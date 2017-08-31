<?php

class Zakoncz{

    public $db;
    private $class_db_file;
    
    private $do_pliku;
    private $nazwa_pliku;
    
    private $zapytanie_czysc_tmp;
    private $zapytanie_zeruj_tmp;
    
    private $zapytanie_czysc_znane;
    private $zapytanie_zeruj_znane;

    public function __construct(){
        $this->do_pliku = "";
        $this->nazwa_pliku = "input/";

        $this->zapytanie_czysc_tmp = "DELETE FROM tmp";
        $this->zapytanie_zeruj_tmp = "ALTER TABLE tmp AUTO_INCREMENT=0";
        
        $this->zapytanie_czysc_znane = "DELETE FROM znane_hosty";
        $this->zapytanie_zeruj_znane = "ALTER TABLE znane_hosty AUTO_INCREMENT=0";
        
        $this->class_db_file = 'db.php';

        if(file_exists($this->class_db_file)){
            require_once($this->class_db_file);
            $this->db = new db();
        }else{
            echo "brak pliku z klasą do łączenia z db";
        }
    }
    
    public function getVLAN(){
        $zapytanie_vlan = "SELECT VLAN FROM znane_hosty WHERE id_hosta=1";
        $rezultat_vlan = mysqli_query($this->db->connection, $zapytanie_vlan);
        $row = mysqli_fetch_array($rezultat_vlan);
        $nr_vlan = $row['VLAN'];
        return $nr_vlan;
    }
    
    public function generujPlik(){
        $zapytanie_generuj = "SELECT * FROM znane_hosty";
        
        $rezultat_generuj = mysqli_query($this->db->connection, $zapytanie_generuj);
        
        while($row = mysqli_fetch_array($rezultat_generuj)){
            $this->do_pliku .= "host ".$row['nazwa_hosta']." {fixed-address ".$row['ip_address'].
            ";hardware ethernet ".$row['mac_address'].";}"."\r\n";
	}
        
        mysqli_free_result($rezultat_generuj);
	$this->nazwa_pliku .= "dhcpd-vlan".$this->getVLAN().".conf";
        //mysqli_close($this->db->connection);
        file_put_contents($this->nazwa_pliku, $this->do_pliku);
        header('location: index.php');
    }
        
    public function czyscTabele(){
        $rezultat_czysc_znane = mysqli_query($this->db->connection, $this->zapytanie_czysc_znane);        
        $rezultat_zeruj_znane = mysqli_query($this->db->connection, $this->zapytanie_zeruj_znane);
        
	$rezultat_czysc_tmp = mysqli_query($this->db->connection, $this->zapytanie_czysc_tmp);        
        $rezultat_zeruj_tmp = mysqli_query($this->db->connection, $this->zapytanie_zeruj_tmp);
    }
    

    
    public function __destruct(){
        //mysqli_close($this->db->connection);
	header('Location: index.php');
    }
}
$koniec = new Zakoncz();
$koniec->generujPlik();
$koniec->czyscTabele();
//$koniec->czyscTabeleznane();
