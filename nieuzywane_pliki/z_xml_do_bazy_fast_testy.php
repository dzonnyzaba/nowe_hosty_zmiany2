<?php
    class testy{
        private $tab;
        
        public function __construct() {
            $this->tab=array();
        }
        
        public function tworzTablice(){
            for($i=0; $i<20; $i++){
                $this->tab[] = $i;
            }
            return $this->tab;
        }
        
        public function wyswietlTablice(){
            foreach($this->tab as $tablica){
                echo $tablica."<br>";
            }
        }
        
        public function wyswietlTabliceret(array $tab){
            foreach($tab as $tablica){
                echo $tablica."<br>";
            }
        }
        
        public function usunIndeks(array &$tab, $indeks){
            unset($tab[$indeks]);
            foreach($tab as $t){
                echo "-->".$t."<br>";
            }
            return $tab;
        }
        
        
    }
    
    $test = new testy();
    $nowatab = $test->tworzTablice();
    $test->wyswietlTablice();
    $pousunieciu = $test->usunIndeks($nowatab, 5);
    $test->wyswietlTabliceret($pousunieciu);

//$tablica = array();
//for($i=0; $i<20; $i++){
//    $tablica[]=$i;
//}
//
//foreach($tablica as $t){
//    echo $t."<br>";
//}
//
//function usun(&$tab, $indeks){
//    unset($tab[$indeks]);
//}
//usun($tablica, 15);
//foreach($tablica as $t){
//    echo $t."<br>";
//}


?>