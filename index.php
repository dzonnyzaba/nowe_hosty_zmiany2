<?php
    require_once 'glowna.php';
    
    $tresc = "<a href=\"z_xml_do_bazy_oop.php?network=10\">Sprawdź sieć 10</a><br>
        <a href=\"z_xml_do_bazy_oop.php?network=64\">Sprawdź sieć 64</a><br>
	 "/*<a href=\"generuj_plik_oop.php\">Generuj plik z bazy</a>"*/;
	 if(isset($_GET['error'])){
		 $tresc .= "<p style=\"color:red\">Brak plików do sprawdzenia</p>";
	 }
    
    $glowna = new Glowna($tresc);
    
    $glowna->wyswietlCalosc();


