<?php

class db{
	private $db_host;
	private $db_user;
	private $db_pass;
	private $db_name;
	
	public $connection;
	public $error;
	
	public function __construct(){
		$this->db_host = "localhost";
		$this->db_user = "root";
		$this->db_pass = "";
		$this->db_name = "zmiany1";
		$this->error = "";
		if(!$polaczenie = new MySQLi($this->db_host, $this->db_user, $this->db_pass, $this->db_name)){
			die("Błąd połączenia z bazą: ".mysqli_connect_error());
			$this->error = mysqli_error();
			$this->connection = false;
		}else{
                        $polaczenie->set_charset("utf8");
			$this->connection = $polaczenie;
		}
	}
	
	
/*	public function connect(){
		if(!$this->connection){
			die("Błąd połączenia z bazą: ".mysqli_connect_error());
			$this->error = mysqli_error();
			return false;
		}else{
			return $this->connection;
		}
	}
*/
	
	public function pobierz_rezultat($sql){
		if($this->connection){
			if(isset($sql) && !empty($sql)){
				if($result = mysqli_query($this->connection, $sql)){
					return $result;
				}else{
					$this->error = mysqli_error($this->connection);
					return false;
				}
			}else{
				$this->error = 'Błąd zapytania sql';
				return false;
			}
		}else{
			$this->error = 'Brak połączenia z bazą danych';
			return false;
		}
	}
	
	public function insertuj($sql){
		$rezultat = $this->pobierz_rezultat($sql);		
	}
	
	public function selektuj($sql){
		$rezultat = $this->pobierz_rezultat($sql);
		$ilosc_wierszy = $rezultat->num_rows;
		$row = mysqli_fetch_array($rezultat);
		return $row;
	}
	
	/*public function query($sql){
		if(isset($sql) && !empty($sql)){
			if($this->connection){
				if($result = mysqli_query($this->connection, $sql)){
					return true;
				}else{
					$this->error = mysqli_error();
					return false;
				}
			}else{
				$this->error = 'Brak połączenia z bazą danych';
				return false;
			}
		}else{
			$this->error = 'Błąd zapytania SQL';
			return false;
		}
	}
	*/
	
	
	public function close(){
		if($this->connection){
			mysqli_close($this->connection);
		}else{
			$this->error = 'Brak aktywnego połączenia';
		}
	}
	
	public function __destruct(){
		$this->close();
	}
	
}