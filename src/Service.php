<?php

class Service {

    public $db = NULL;
    public $mysqli;

    public function __construct($environnement) {
        switch ($environnement) {
            case "dev":
                $this->mysqli = new mysqli("localhost", "root", "", "euro16_dev");
                break;
            case "test":
                $this->mysqli = new mysqli("localhost", "root", "", "euro16_test");
                break;
            case "prod":
                // TODO stocker les identifiants de la base de prod proprement pour qu'ils ne soient en clair pas sur le repo
                $this->mysqli = new mysqli("", "", "", "");
                break;
            default:
                error_log("Merci de specifier un environnement pour lier la bonne BDD");
                break;
        }
        if ($this->mysqli->connect_error) {
            die('Erreur de connexion (' . $this->mysqli->connect_errno . ') '
                . $this->mysqli->connect_error);
        }
    }

    public function _creerUtilisateur($nom, $prenom, $photo, $id_facebook) {
        if (empty($id_facebook)) {
            return false;
        }
        $req = "INSERT INTO Utilisateur(NomUti, PrenomUti, PhotoUti, ID_Facebook) VALUES ('$nom', '$prenom', '$photo', '$id_facebook')";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }
        return true;
    }

    public function _getUtilisateurs() {
        $req = "SELECT * FROM Utilisateur";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        $result = array();
        if ($sql->num_rows > 0) {
            while ($rlt = $sql->fetch_assoc()) {
                $result[] = $rlt;
            }
        }
        return $result;
    }

    public function _deleteUtilisateur($id_facebook) {
        if (empty($id_facebook)) {
            return -1;
        }
        $req = "DELETE FROM Utilisateur WHERE ID_Facebook = '$id_facebook'";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return -1;
        }
        return $this->mysqli->affected_rows;
    }
}

?>