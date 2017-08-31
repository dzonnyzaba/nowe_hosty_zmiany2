<?php
if(isset($_GET['network'])){
    if($_GET['network']=="10" || $_GET['network']=="64"){
        

class XML_do_bazy{
    private $plik_input_conf;
    private $plik_input_conf_to_txt;
    //private $lista_xmli;
    private $plik_xml;
    private $plik_xml_to_txt;
    private $dataczas;
    private $vlan;
    public $db;
    private $class_db_file;
	
    
    public function __construct(){
        if($_GET['network']=="10"){
            $this->plik_input_conf = 'dhcpd-vlan10.conf';
            $this->plik_input_conf_to_txt = 'dhcpd-vlan10.txt';
            $this->plik_xml = '10_10_0_0-18.xml';
            $this->plik_xml_to_txt = '10_10_0_0-18.txt';
            $this->vlan = 10;
        }else{
            $this->plik_input_conf = 'dhcpd-vlan64.conf';
            $this->plik_input_conf_to_txt = 'dhcpd-vlan64.txt';
            $this->plik_xml = '10_10_64_0-18.xml';
            $this->plik_xml_to_txt = '10_10_64_0-18.txt';
            $this->vlan = 64;
        }
        $this->dataczas = date("d-m-y");
        $this->class_db_file = 'db.php';
        if(file_exists($this->class_db_file)){
            require_once($this->class_db_file);
            $this->db = new db();
        }else{
            echo "brak pliku z klasą do łączenia z db";
        }
    }
    
    public function wypelnijTabliceZnane(){
        $sql = "LOAD DATA LOCAL INFILE 'C:/xampp/htdocs/nowe_hosty/nowe_hosty_zmiany1/txt/$this->plik_input_conf_to_txt' IGNORE INTO TABLE tmp 
                FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' (@klucz, nowy_mac, nowy_ip, data, VLAN)";
    }

//    private function pobierzPlikiXML(){
//        $dir = "xml";
//        $tabtmp = array();
//        $files = scandir($dir);
//        foreach($files as $f){
//            if($f!="." && $f!=".."){
//                $tabtmp[]=$f;		
//            }
//        }
//
//        if(count($tabtmp)>=1){
//                return $tabtmp;
//        }else{
//                echo "nieeeeeeeeeeeeeeee";
//                header('location: index.php?error=1');
//                exit();
//        }
//        var_dump($tabtmp);
//    }
	
	
    public function tworzStringXMLnaTxt(){
        $obiekt = simplexml_load_file("input/".$this->plik_xml);
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
        $tab_z_conf = file("input/".$this->plik_input_conf);
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
            file_put_contents("txt/".$this->plik_input_conf_to_txt, $this->tworzStringConfNaTxt());
            file_put_contents("txt/". $this->plik_xml_to_txt, $this->tworzStringXMLnaTxt());
    }
    
    public function wypelnijTabliceTmpiZnane(){
	//var_dump($this->lista_xmli)."<br>";
        //foreach($this->lista_xmli as $plik_xml){
        $sql_tmp = "LOAD DATA LOCAL INFILE 'C:/xampp/htdocs/nowe_hosty/nowe_hosty_zmiany1/txt/$this->plik_xml_to_txt' IGNORE INTO TABLE tmp 
			FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' (@klucz, nowy_mac, nowy_ip, data, VLAN)";
        $sql_znane = "LOAD DATA LOCAL INFILE 'C:/xampp/htdocs/nowe_hosty/nowe_hosty_zmiany1/txt/$this->plik_input_conf_to_txt' IGNORE INTO TABLE  znane_hosty
			FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' (@klucz, nazwa_hosta, mac_address, ip_address, VLAN)";
        
        if($result = mysqli_query($this->db->connection, $sql_tmp)){
        $result = mysqli_query($this->db->connection, $sql_tmp);
        }
        if($result = mysqli_query($this->db->connection, $sql_znane)){
        $result = mysqli_query($this->db->connection, $sql_znane);
        }
	if(!file_exists('C:/xampp/htdocs/nowe_hosty/nowe_hosty_baza/stare_pliki_xml/'.$this->dataczas)){
            mkdir('C:/xampp/htdocs/nowe_hosty/nowe_hosty_baza/stare_pliki_xml/'.$this->dataczas);
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
$nowyxml->tworzPlikiTxt();
$nowyxml->wypelnijTabliceTmpiZnane();
//$nowyxml->pobierzPlikiXML();
header('Location: roznice_oop.php');
    }else{
        header('location: index.php?error=2');
    }
}else{
    header('location: index.php?error=3');
}