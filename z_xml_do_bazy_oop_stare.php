<?php
require_once "connection.php";


class XML_do_bazy{
    private $file_path;
    private $data;
    private $obiektxml;
    private $vlan;
    
    public function __construct(string $plik){
        $this->file_path = '../../mysql/data/nowe_hosty/dane_xml.txt';
        $this->obiektxml = simplexml_load_file($plik);
        $this->data = $this->pobierzDate();
        $this->vlan = 10;
}

    public function tworzStringTxt(){
        $attrdate = $this->obiektxml->runstats->finished->attributes();
        $tablica = array();
        $string = "";
        $licznik_wierszy=0;
        foreach($this->obiektxml as $host){
            if(isset($host->address[1]) && isset($host->address[0])){
            $attrmac = $host->address[1]->attributes();
            $ajpi = $host->address[0]->attributes();
            $mac = (string)$attrmac['addr'];
            $ip = (string)$ajpi['addr'];
            $licznik_wierszy++;
            $string .= $licznik_wierszy.",".$mac.",".$ip.",".$this->data.",".$this->vlan."\r\n";
            }
        }
        return $string;
    }
    
    public function tworzPlikTxt(){
        file_put_contents($this->file_path, $this->tworzStringTxt());
    }
    
    public function wypelnijTabliceTmp(){
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "nowe_hosty";
        $conn = @mysqli_connect($servername, $username, $password, $dbname);
        if(!$conn){
                die("B³¹d po³¹czenia z baz¹: ".mysqli_connect_error());
        }
        $this->tworzPlikTxt();
        $sciezka = $this->file_path;
        $sql = "LOAD DATA INFILE '../../mysql/data/nowe_hosty/dane_xml.txt' INTO TABLE tmp 
	FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n'";
        if($result = mysqli_query($conn, $sql)){
        $result = mysqli_query($conn, $sql);
        }else{
            echo "coœ Ÿle";
        }
        var_dump($result);
        mysqli_close($conn);
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

}

$nowyxml = new XML_do_bazy('xml/out.xml');
$nowyxml->wypelnijTabliceTmp();
header('Location: roznice_oop.php');
