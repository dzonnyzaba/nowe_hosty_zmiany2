<?php
//require_once "roznice_oop.php";
class test{
    public function pobierzPlikiXML(){
		$this->zapiszDoLoga("szukam plików xml");
        $dir = "xmle";
        $tabtmp = array();
        $files = scandir($dir);
        $ile = count($files);
        foreach($files as $f){
            if($f!="." && $f!=".."){
                if($f=='10_10_0_0-18.xml'){
					$this->zapiszDoLoga("znalazłem plik xml ".$f);
                    $tap = array();
                    $tap[0] = basename($f, ".xml");
                    $tap[1] = 'dhcpd-vlan10';
                    $tap[2] = 10;
                    $tabtmp[] = $tap;
                }else{
					$this->zapiszDoLoga("znalazłem plik xml ".$f);
                    $tap = array();
                    $tap[0] = basename($f, ".xml");
                    $tap[1] = 'dhcpd-vlan64';
                    $tap[2] = 64;
                    $tabtmp[] = $tap;
                }
            }
		}
        if(count($tabtmp)>=1){
            return $tabtmp;
        }else{
            echo "nieeeeeeeeeeeeeeee";
            exit();//tu wstawić return 0
        }
        //var_dump($tabtmp);
    }
	
	private function zapiszDoLoga($komunikat){
		file_put_contents('roznice_oop_log.txt', $komunikat."\r\n",  FILE_APPEND);
	}

    public function powysylaj(){
		$this->zapiszDoLoga("powysylaj()");
        $r = $this->pobierzPlikiXML();
        if($r){
            foreach ($r as $element){
				$this->zapiszDoLoga("zaczytuje plik ".$element[0]);
                $xml_do_bazy_obyekt = new XML_do_bazy($element[0], $element[1], $element[2]);
				$xml_do_bazy_obyekt->przetwarzaj();
            }
			$roznica = new Roznice();
			//header('Location: roznice_oop.php');
			$this->wyswietlNaglowek();
			echo $roznica->wyswietlTabeleRoznice();
			$this->wyswietlStopke();
        }
    }
	
	private function wyswietlNaglowek(){
		echo "<!doctype html>
				<html>
					<head>
						<meta charset=\"UTF-8\" />
					</head>
					<body>
						<table border=1>
							<tr>
								<th></th><th>id</th><th>adres IP</th><th>adres MAC</th><th></th>
							</tr>";
	}
	
	private function wyswietlStopke(){
		echo "</table>
			 <a href=\"zakoncz_oop.php\">Zakończ</a>
			</body>
			</html>";
	}
}

class XML_do_bazy{
    private $plik_input_conf;
    private $plik_input_conf_to_txt;
    private $plik_xml;
    private $plik_xml_to_txt;
    private $dataczas;
    private $vlan;
    public $db;
    private $class_db_file;
	
    
    public function __construct($plikxml, $plikconf, $jakasiec){
        $this->plik_input_conf = $plikconf.".conf";
        $this->plik_input_conf_to_txt = $plikconf.'.txt';
        $this->plik_xml = $plikxml.'.xml';
        $this->plik_xml_to_txt = $plikxml.'.txt';
        $this->vlan = $jakasiec;
        
        $this->dataczas = date("d-m-y");
        $this->class_db_file = 'db.php';
        if(file_exists($this->class_db_file)){
            require_once($this->class_db_file);
            $this->db = new db();
        }else{
            echo "brak pliku z klasą do łączenia z db";
        }
    }
	
	public function przetwarzaj(){
		$this->tworzPlikiTxt();
		$this->wypelnijTabliceTmpiZnane();
	}
	
    public function tworzStringXMLnaTxt(){
        $obiekt = simplexml_load_file("xmle/".$this->plik_xml);
        $attrdate = $obiekt->runstats->finished->attributes();
        $datatab = $attrdate['timestr'];
        $data = $this->utworzDate($datatab);
        $tablica = array();
        $string = "";
        $licznik_wierszy=0;
        foreach($obiekt as $host){
            if(isset($host->address[1]) && isset($host->address[0])){
                $attrmac = $host->address[1]->attributes();
                $ajpi = $host->address[0]->attributes();
                $mac = (string)$attrmac['addr'];
                $ip = (string)$ajpi['addr'];
                $licznik_wierszy++;
                $string .= $licznik_wierszy.",".$mac.",".$ip.",".$data.",".$this->vlan."\r\n";
            }
        }
        return $string;
    }
    
    public function tworzStringConfNaTxt(){
        $wzor = '@^host ([0-9a-zA-Z._-]+) +{fixed-address (\d+\.\d+\.\d+\.\d+) ?;hardware ethernet +([a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2}:[a-fA-F0-9]{2});}@';
        echo $this->plik_input_conf."<br>";
        $tab_z_conf = file("confy/".$this->plik_input_conf);
        $string = "";
        $licznik=0;
        foreach($tab_z_conf as $linijka){
            if(strlen($linijka)>1){
                if($reg = preg_match($wzor, $linijka, $match)){
                    $licznik++;
                    $string .= $licznik.",".$match[1].",".$match[3].",".$match[2].",".$this->vlan."\r\n";
                }
            }
        }
        return $string;
    }
    
    public function tworzPlikiTxt(){
		$this->zapiszDoLoga("tworze pliki txt dla pary plików txt/".$this->plik_input_conf_to_txt." txt/". $this->plik_xml_to_txt);
            file_put_contents("txt/".$this->plik_input_conf_to_txt, $this->tworzStringConfNaTxt());
            file_put_contents("txt/". $this->plik_xml_to_txt, $this->tworzStringXMLnaTxt()); 
    }
    
    public function wypelnijTabliceTmpiZnane(){
		$this->zapiszDoLoga("wypełniam obie tablice tmp i znane_hosty");
		//var_dump($this->lista_xmli)."<br>";
        //foreach($this->lista_xmli as $plik_xml){
        $sql_tmp = "LOAD DATA LOCAL INFILE 'e:/programy/Xampp/htdocs/standard/nowe_hosty_zmiany2/txt/$this->plik_xml_to_txt' IGNORE INTO TABLE tmp 
			FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' (@klucz, nowy_mac, nowy_ip, data, VLAN)";
        $sql_znane = "LOAD DATA LOCAL INFILE 'e:/programy/Xampp/htdocs/standard/nowe_hosty_zmiany2/txt/$this->plik_input_conf_to_txt' IGNORE INTO TABLE  znane_hosty
			FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' (@klucz, nazwa_hosta, mac_address, ip_address, VLAN)";
        
        if($result = mysqli_query($this->db->connection, $sql_tmp)){
			$this->zapiszDoLoga("wypełniam tablice tmp");
        $result = mysqli_query($this->db->connection, $sql_tmp);
		$this->zapiszDoLoga("wypełniam tablice tmp ".$result);
        }
        if($result = mysqli_query($this->db->connection, $sql_znane)){
			$this->zapiszDoLoga("wypełniam tablice znane");
        $result = mysqli_query($this->db->connection, $sql_znane);
		$this->zapiszDoLoga("wypełniam tablice znane ".$result);
        }
	if(!file_exists('e:/programy/Xampp/htdocs/standard/nowe_hosty_zmiany2/stare_pliki_xml/'.$this->dataczas)){
            mkdir('e:/programy/Xampp/htdocs/standard/nowe_hosty_zmiany2/stare_pliki_xml/'.$this->dataczas);
	}
//	rename('C:/xampp/htdocs/nowe_hosty/nowe_hosty_baza/xml/'.$plik_xml, 
//	'C:/xampp/htdocs/nowe_hosty/nowe_hosty_baza/stare_pliki_xml/'.$this->dataczas.'/'.$plik_xml);
//        }else{
//            echo "coś źle";
//            echo $sql;
//        }
	//}
        //var_dump($result);
        //mysqli_close($this->db->connection);
		$this->zapiszDoLoga("wywołuje skrypt roznice_oop.php");
		$roznica = new Roznice();
		$roznica->TworzTabeleRoznice();
		//$roznica->TworzTabeleRoznice();
        //header('Location: roznice_oop.php');
    }
	
	private function zapiszDoLoga($komunikat){
		file_put_contents('roznice_oop_log.txt', $komunikat."\r\n",  FILE_APPEND);
	}

	public function utworzDate($s){
        $podziel = explode(" ", $s);
        $year = $podziel[4];
        $day = $podziel[2];

        switch($podziel[1]){
                case "Jan":
                        $month = "01";
                        break;
                case "Feb":
                        $month = "02";
                        break;
                case "Mar":
                        $month = "03";
                        break;
                case "Apr":
                        $month = "04";
                        break;
                case "May":
                        $month = "05";
                        break;
                case "Jun":
                        $month = "06";
                        break;
                case "Jul":
                        $month = "07";
                        break;
                case "Aug":
                        $month = "08";
                        break;
                case "Sep":
                        $month = "09";
                        break;
                case "Oct":
                        $month = "10";
                        break;
                case "Nov":
                        $month = "11";
                        break;
                case "Dec":
                        $month = "12";
                        break;
                default:
                        $month = "01";
        }	
        return $year."-".$month."-".$day;
	}

}

class Roznice{
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
		$string = "";
		$licznik_wierszy = 0;
        while($row = mysqli_fetch_array($res)){
			$licznik_wierszy++;
			$string.=$licznik_wierszy.",".$row['nowy_mac'].",".$row['nowy_ip'].",".$row['data'].",".$row['VLAN']."\r\n";
			
			//$licznik_wierszy.",".$mac.",".$ip.",".$data.",".$this->vlan."\r\n";
            /*$sql_wypelnij_roznice = "INSERT INTO roznice(nowy_ip, nowy_mac, data, VLAN) values('".$row['nowy_ip']."', '"
                    .$row['nowy_mac']."', '".$row['data']."', '".$row['VLAN']."')";
            $rezultat_wstawiaj = mysqli_query($this->db->connection, $sql_wypelnij_roznice);*/
//                    $tabelka .= "<tr><td>$licznik</td>";
//                    $tabelka .= "<td>".$row['id_nowego_hosta']."</td><td>".
//                    $row['nowy_ip']."</td><td>".$row['nowy_mac'].'</td><td><a href="dodaj_host_oop.php?id='.
//                    $row['id_nowego_hosta'].'">Dodaj hosta do bazy</a></td>';
//                    $tabelka .= "</tr>";
//                    $licznik++;
        }
        mysqli_free_result($res);
		file_put_contents("txt/roznice.txt", $string); 
        $this->czyscTabeleZnaneItmp();
        //echo $tabelka;
		$sql_roznice = "LOAD DATA LOCAL INFILE 'e:/programy/Xampp/htdocs/standard/nowe_hosty_zmiany2/txt/roznice.txt' IGNORE INTO TABLE  roznice
			FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' (@klucz, nowy_mac, nowy_ip, data, VLAN)";
		//if($result = mysqli_query($this->db->connection, $sql_roznice)){
		//	$this->zapiszDoLoga("wypełniam tablice tmp");
        $result = mysqli_query($this->db->connection, $sql_roznice);
		//$this->zapiszDoLoga("wypełniam tablice tmp ".$result);
        //}
		
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

$test = new test();
$test->powysylaj();
//$nowyxml->tworzPlikiTxt();
//$nowyxml->wypelnijTabliceTmpiZnane();
//$nowyxml->pobierzPlikiXML();
//header('Location: roznice_oop.php');

