<?php
//require_once "funkcje.php";
//require_once "connection.php";

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

public function pobierzXml(){
    $xml = simplexml_load_file($this->nazwa_pliku);
    return $xml;
}

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

public function tworzTabeleAdresow(SimpleXMLElement $xml){
    $licznik_wierszy = 0;
    foreach($xml->host as $host){
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

public function porownanie(array $tablica) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "standard";
	 $conn = @mysqli_connect($servername, $username, $password, $dbname);
	 if(!$conn){
		 die("Błąd połączenia z bazą: ".mysqli_connect_error());
	 }
    $zapytanie = "SELECT mac_address FROM znane_hosty";
	$rezultat_porownanie = mysqli_query($conn, $zapytanie);
	//var_dump($rezultat_porownanie);
    if($rezultat_porownanie = mysqli_query($conn, $zapytanie)){
        //var_dump($rezultat_porownanie);
        while($row = mysqli_fetch_array($rezultat_porownanie)){
            for($i=0; $i<count($tablica); $i++){
                if($tablica[$i]['mac']==$row['mac_address']/* && $tablica[$i]['ip']==$row['ip_address']*/){
                    echo "<br>wywalam $i---".$tablica[$i]['mac'];
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

public function wyswietlTabeleZPorownania(array $tab){
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
                //. "<td><a href=\"dodaj_host_fast.php?ip=".$t['ip']
                //. "&mac=".$t['mac']."&data=".$this->data."\""
                //. ">dodaj host do bazy</a></td>\n"
                . "<td>"
                . "<form method=\"post\">\n"
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
    $str.= "</table>";
    return $str;
}

public function dodajeWyrzuca(){
	if(isset($_POST['indeks']) && isset($_POST['ip']) && isset($_POST['mac']) && isset($_POST['data']) && isset($_POST['nazwa']) && isset($_POST['lokalizacja']) && isset($_POST['uwagi'])){
		$indeks = $_POST['indeks'];
		$ip = $_POST['ip'];
		$mac = $_POST['mac'];
		$data = $_POST['data'];
		$nazwa = $_POST['nazwa'];
		$lokalizacja = $_POST['lokalizacja'];
		$uwagi = $_POST['uwagi'];
		unset($this->tablica[$indeks]);
        array_splice($tablica, 1, 1);
		echo $indeks." ".$ip." ".$mac." ".$data." ".$nazwa. " " .$lokalizacja. " " .$uwagi;
	}else{
		//return false;
		echo "nic do wyświetlenia";
	}
}
}

$nowyxml = new XML_do_bazy('out.xml');
$obiekt = $nowyxml->pobierzXml();
$obiektadresy = $nowyxml->tworzTabeleAdresow($obiekt);
echo $nowyxml->pobierzDate($obiekt);
$poporownaniu = $nowyxml->porownanie($nowyxml->tworzTabeleAdresow($obiekt));
echo "<pre>";
//var_dump($poporownaniu);
echo "</pre>";
echo $nowyxml->wyswietlTabeleZPorownania($nowyxml->porownanie($obiektadresy));
$nowyxml->doDodania();