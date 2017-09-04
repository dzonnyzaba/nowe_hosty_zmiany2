<?php

class Zakoncz{

    public $db;
    private $class_db_file;
    
    private $do_pliku;
    private $nazwa_pliku;
    
    private $zapytanie_czysc_roznice;
    private $zapytanie_zeruj_roznice;

    public function __construct(){

        $this->zapytanie_czysc_roznice = "DELETE FROM roznice";
        $this->zapytanie_zeruj_roznice = "ALTER TABLE roznice AUTO_INCREMENT=0";
        
        $this->class_db_file = 'db.php';

        if(file_exists($this->class_db_file)){
            require_once($this->class_db_file);
            $this->db = new db();
        }else{
            echo "brak pliku z klasą do łączenia z db";
        }
    }

        
    public function czyscTabele(){
        $rezultat_czysc_roznice = mysqli_query($this->db->connection, $this->zapytanie_czysc_roznice);        
        $rezultat_zeruj_roznice = mysqli_query($this->db->connection, $this->zapytanie_zeruj_roznice);

    }
    

    
    public function __destruct(){
	header('Location: index.php');
    }
}
$koniec = new Zakoncz();
$koniec->czyscTabele();
//$koniec->czyscTabeleznane();
