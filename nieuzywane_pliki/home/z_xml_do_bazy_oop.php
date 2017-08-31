<?php
require_once "connection.php";


class XML_do_bazy{
    private $file_path;
    private $data;
    private $obiektxml;
    private $vlan;
	private $lista_xmli;
	
    
    public function __construct(){
        $this->file_path = 'E:/programy/Xampp/htdocs/standard/load_data_oop/dane_xml.txt';
        //$this->obiektxml = simplexml_load_file('');
        //$this->data = $this->pobierzDate();
        $this->vlan = 10;
		$this->lista_xmli = array();
}

	/*public function sklejPlikiXml(){
		$dir = "xml/";
		$files = scandir($dir);
		$duzy_xml = "";
		foreach($files as $f){
			$duzy_xml .= file
		}
	}
*/

	private function pobierzPlikiXML(){
		$dir = "xml";
		$tabtmp = array();
				
		$files = scandir($dir);
				
		foreach($files as $f){
					
		if($f!="." && $f!=".."){
						
		$tabtmp[]=$f;
					
		}
				
		}
		return $tabtmp;
	}
	
	
    public function tworzStringTxt($plik_xml){
		//echo "próbuje wczytaæ plik xml $plik_xml";
		$obiekt = simplexml_load_file("xml/".$plik_xml);
        $attrdate = $obiekt->runstats->finished->attributes();
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
    
    public function tworzPlikTxt(){
		$this->lista_xmli = $this->pobierzPlikiXML();
		foreach($this->lista_xmli as $plik_xml){
        file_put_contents("txt/".$plik_xml.".txt", $this->tworzStringTxt($plik_xml));
		} 
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
        //$this->tworzPlikTxt();
        //$sciezka = $this->file_path;
		var_dump($this->lista_xmli);
		foreach($this->lista_xmli as $plik_xml){
        $sql = "LOAD DATA INFILE 'E:/programy/Xampp/htdocs/standard/load_data_oop/txt/$plik_xml.txt' IGNORE INTO TABLE tmp 
			FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' (@klucz, nowy_mac, nowy_ip, data, VLAN)";
			echo $sql;
        if($result = mysqli_query($conn, $sql)){
        $result = mysqli_query($conn, $sql);
        }else{
            echo "coœ Ÿle";
			echo $sql;
        }
		}
        //var_dump($result);
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

$nowyxml = new XML_do_bazy();
$nowyxml->tworzPlikTxt();
$nowyxml->wypelnijTabliceTmp();
//$nowyxml->pobierzPlikiXML();
header('Location: roznice_oop.php');
