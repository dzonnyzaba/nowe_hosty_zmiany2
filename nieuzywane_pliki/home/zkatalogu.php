<?php
/*
	$dir = "xml/";
	$files = scandir($dir);
	var_dump($files);
	foreach($files as $f){
		if($f!="." || $f!=".."){
			echo "<br>".$f;
		}
	}
?>


LOAD DATA INFILE 'E:/programy/Xampp/htdocs/standard/load_data_oop/dane_xml.txt' IGNORE INTO TABLE tmp 
	FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' (@klucz, nowy_mac, nowy_ip, data, VLAN)
*/

		
$dir = "xml";
		
$tabtmp = array();
		
$files = scandir($dir);
		
foreach($files as $f){
			
if($f!="." && $f!=".."){
				
$tabtmp[]=$f;
			
}
		
}
		
var_dump($tabtmp);