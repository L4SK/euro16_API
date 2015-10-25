<?php

class SERVICE{

	// const DB_SERVER = "miscusifpesql.mysql.db";
	// const DB_USER = "miscusifpesql";
	// const DB_PASSWORD = "wW6125077250";
	// const DB = "miscusifpesql";

	const DB_SERVER = "localhost";
	const DB_USER = "root";
	const DB_PASSWORD = "";
	const DB = "euro16";

	private $db = NULL;

	public function __construct(){
		$this->dbConnect();					// Initiate Database connection
	}


	/*
	 *  Database connection
	*/
	private function dbConnect(){
		$this->db = mysqli_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
		if($this->db){
			mysqli_select_db($this->db,self::DB);
		}
	}
	
	public function _creerUtilisateur($nom, $prenom, $photo, $id_facebook){
		$req = "INSERT INTO Utilisateur(NomUti, PrenomUti, PhotoUti, ID_Facebook) VALUES ('$nom', '$prenom', '$photo', '$id_facebook')";
		$sql = mysqli_query($this->db, $req) or die(mysqli_error($this->db));
		return true;
	}
	
	public function _getUtilisateurs(){
		$req = "SELECT * FROM Utilisateur";
		$sql = mysqli_query($this->db, $req) or die(mysqli_error($this->db));

		if(mysqli_num_rows($sql) > 0){
			$result = array();
			while($rlt = mysqli_fetch_array($sql,MYSQL_ASSOC)){
				$result[] = $rlt;
			}
			return $result;
		}
		return -1;
	}
} 

?>