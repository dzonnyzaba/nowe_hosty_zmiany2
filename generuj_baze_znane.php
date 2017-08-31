<?php

class Generuj_baze_znane{
    public $db;
    private $class_db_file;
    private $nazwa_pliku;
    
    public function __construct() {
        $this->nazwa_pliku = 'dhcpd-vlan64.conf';
        if(file_exists($this->class_db_file)){
            require_once($this->class_db_file);
            $this->db = new db();
        }else{
            echo "brak pliku z klasą do łączenia z db";
        }
    }
}