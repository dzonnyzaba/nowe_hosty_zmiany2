<?php
//require_once "funkcje.php";
require_once "connection.php";

//var_dump($conn);

class XML_do_bazy{
    private $tabela_z_xmla;
    private $nazwa_pliku;//docelowo będzie podawana w konstruktorze
    private $data;
    private $obiektxml;
    
    public function __construct($plik){
        $this->tabela_z_xmla = array();
        $this->nazwa_pliku = $plik;
        $this->obiektxml = simplexml_load_file($plik);
        $this->data = $this->pobierzDate();
		}

/* public function pobierzXml(){ */
//    $xml = simplexml_load_file($this->nazwa_pliku);
//    return $xml;
//	}

public function pobierzDate(){
    $attrdate = $this->obiektxml->runstats->finished->attributes();
    $datatab = $attrdate['timestr'];
    $data = $this->utworzDate($datatab);
    return $data;
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

public function tworzTabeleAdresow(){
    $licznik_wierszy = 0;
    foreach($this->obiektxml->host as $host){
        if(isset($host->address[1]) && isset($host->address[0])){
            $attrmac = $host->address[1]->attributes();
            $ajpi = $host->address[0]->attributes();
            $tabela_z_xmla[$licznik_wierszy]['mac'] = (string)$attrmac['addr'];
            $tabela_z_xmla[$licznik_wierszy]['ip'] = (string)$ajpi['addr'];
            $tabela_z_xmla[$licznik_wierszy]['data'] = $this->data;
            $licznik_wierszy++;	
        }
    }
    return $tabela_z_xmla;
}

public function porownanie() {
	$tablica = $this->tworzTabeleAdresow();
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "standard";
	 $conn = @mysqli_connect($servername, $username, $password, $dbname);
	 if(!$conn){
		 die("Błąd połączenia z bazą: ".mysqli_connect_error());
	 }
    $zapytanie = "SELECT mac_address, ip_address FROM znane_hosty";
    if($rezultat_porownanie = mysqli_query($conn, $zapytanie)){
        //var_dump($rezultat_porownanie);
        while($row = mysqli_fetch_array($rezultat_porownanie)){
            for($i=0; $i<count($tablica); $i++){
                if($tablica[$i]['mac']==$row['mac_address'] && $tablica[$i]['ip']==$row['ip_address']){
                    //echo "<br>wywalam $i---".$tablica[$i]['mac'];
                    unset($tablica[$i]);
                    array_splice($tablica, 1, 1);
                }
            }
        }
    }else{
        echo "coś poszło nie tak";
    }
    mysqli_close($conn);
    return $tablica;
}

public function wyswietlTabeleZPorownania(){
	$tab = $this->porownanie();
    $licznik=1;
	$indeks=0;
    $str = "<table border=1>\n";
    $str .= "<tr>\n"
            . "<th>lp.</th>\n"
            . "<th>adres ip</th>\n"
            . "<th>adres mac</th>\n"
            . "<th></th>\n"
            . "</tr>\n";
    foreach ($tab as $t) {
        $str.= "<tr>\n"
                ."<td>$licznik</td>\n"
                . "<td>".$t['ip']."</td>\n"
                . "<td>".$t['mac']."</td>\n"
                . "<td>"
                . "<form method=\"post\" action=\"dodaj_host_fast.php\">\n"
                . "nazwa: <input type=\"text\" name=\"nazwa\">\n"
                . "lokalizacja: <input type=\"text\" name=\"lokalizacja\">\n"
                . "uwagi: <input type=\"text\" name=\"uwagi\">\n"
                . "<input type=\"hidden\" name=\"indeks\" value=".$indeks.">\n"
                . "<input type=\"hidden\" name=\"ip\" value=".$t['ip'].">\n"
                . "<input type=\"hidden\" name=\"mac\" value=".$t['mac'].">\n"
                . "<input type=\"hidden\" name=\"data\" value=".$this->data.">\n"
                . "<input type=\"submit\" value=\"dodaj do bazy\">\n"
                . "</form>\n"
                . "<td></tr>\n";
        $licznik++;
		$indeks++;
    }
    $str.= '</table><br><a href="index.php" >Powrót do strony głównej</a>';
    return $str;
}

}

$nowyxml = new XML_do_bazy('out.xml');
//$obiekt = $nowyxml->pobierzXml();
//$obiektadresy = $nowyxml->tworzTabeleAdresow();
//$poporownaniu = $nowyxml->porownanie();

echo $nowyxml->wyswietlTabeleZPorownania();
