<?php
if(isset($_GET['id'])){
	$id = $_GET['id'];
?>
<form method="post">
    nazwa: <input type="text" name="nazwa"/>
<!--    lokalizacja: <input type="text" name="lokalizacja"/>
    uwagi: <input type="text" name="uwagi"/>-->
    lokalizacja: <textarea name="lokalizacja"></textarea>
    uwagi: <textarea name="uwagi"></textarea>
    <input type="submit" value="dodaj"/>
</form>
<a href="roznice_oop.php">Powrót do tabelki</a>
<?php        
if((isset($_POST['nazwa']) && !empty($_POST['nazwa'])) && 
(isset($_POST['lokalizacja']) && !empty($_POST['lokalizacja'])) && 
(isset($_POST['uwagi']) && !empty($_POST['uwagi']))){
    
class Dodaj{
    private $nowa_nazwa;
    private $nowa_lokalizacja;
    private $nowe_uwagi;
    private $id;
    public $db;
    private $class_db_file;
    
    public function __construct(){
        $this->nowa_nazwa = $_POST['nazwa'];
        $this->nowa_lokalizacja = $_POST['lokalizacja'];
        $this->nowe_uwagi = $_POST['uwagi'];        
        $this->id = $_GET['id'];
        $this->class_db_file = 'db.php';

        if(file_exists($this->class_db_file)){
            require_once($this->class_db_file);
            $this->db = new db();
        }else{
            echo "brak pliku z klasą do łączenia z db";
        }
    }
    
    public function pobierzPozostaleDane(){
        $zapytanie_nowe = "SELECT * FROM tmp WHERE id_nowego_hosta=$this->id";
	$rezultat_nowe = mysqli_query($this->db->connection, $zapytanie_nowe);
	$row = mysqli_fetch_array($rezultat_nowe);
        return $row;
    }
    
    public function dodajRekord(){
        $row_sel = $this->pobierzPozostaleDane();
        $zapytanie_znane = "INSERT INTO znane_hosty(nazwa_hosta, mac_address, 
	data_dodania, lokalizacja, uwagi, VLAN, ip_address) 
	VALUES('".$this->nowa_nazwa."', '".$row_sel['nowy_mac']."', '".$row_sel['data']."', '".
        $this->nowa_lokalizacja."', '".$this->nowe_uwagi."', '".$row_sel['VLAN']."', '"
        .$row_sel['nowy_ip']."')";
        $rezultat_znane = mysqli_query($this->db->connection, $zapytanie_znane);
        if($rezultat_znane){
		echo "dołączono host do bazy";
	}else{
		echo "nie udało się dołączyc hosta do bazy";
	}
        
        return 0;
    }
    
    public function kasujRekord(){
	$zapytanie_nowe_kasuj = "DELETE FROM tmp WHERE id_nowego_hosta=$this->id";
	mysqli_query($this->db->connection, $zapytanie_nowe_kasuj);
	
	//mysqli_close($this->db->connection);
        header('location: roznice_oop.php');        
    }
}
$dodaj = new Dodaj();
$dodaj->dodajRekord();
$dodaj->kasujRekord();
//header('location: roznice_oop.php');
}else{
    echo "<p>wypełnij wszystkie pola</p>";
}
}else{
	header('location: roznice_oop.php');
	exit();
}

