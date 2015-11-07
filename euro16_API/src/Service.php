<?php

class Service {

    const DB_SERVER = "localhost";
    const DB_USER = "root";
    const DB_PASSWORD = "";
    const DB = "miscusifpesql";

    public $db = NULL;

    public function __construct() {
        $this->dbConnect();                    // Initiate Database connection
    }

    /*
     *  Database connection
    */
    private function dbConnect() {
        $this->db = mysqli_connect(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD);
        if ($this->db) {
            mysqli_select_db($this->db, self::DB);
        }
    }

    public function _creerUtilisateur($nom, $prenom, $photo, $id_facebook) {
        $req = "INSERT INTO Utilisateur(NomUti, PrenomUti, PhotoUti, ID_Facebook) VALUES ('$nom', '$prenom', '$photo', '$id_facebook')";
        if(empty($id_facebook)){
            return false;
        }
        if (!($sql = mysqli_query($this->db, $req))) {
            error_log(mysqli_error($this->db));
            return false;
        }
        return true;
    }

    public function _getUtilisateurs() {
        $req = "SELECT * FROM Utilisateur";
        if (!($sql = mysqli_query($this->db, $req))) {
            error_log(mysqli_error($this->db));
            return false;
        }

        $result = array();
        if (mysqli_num_rows($sql) > 0) {
            while ($rlt = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
                $result[] = $rlt;
            }
        }
        return $result;
    }
}

?>