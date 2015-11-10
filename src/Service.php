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
        if (empty($nom) || empty($prenom) || empty($id_facebook)) {
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

    public function _creerGroupe($nom, $admin, $photo) {
        if (empty($nom) || empty($admin) || empty($id_groupe)) {
            return false;
        }
        $req = "SELECT 1 FROM Utilisateur WHERE ID_Facebook='$admin'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if($sql->fetch_assoc()[1] != 1){
            error_log("Impossible de creer le groupe : L'admin n'est pas un utilisateur existant");
            return false;
        }
        $req = "SELECT 1 FROM Groupe WHERE NomGrp='$nom'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if($sql->fetch_assoc()[1] != 1){
            error_log("Impossible de creer le groupe : Le nom a deja ete pris");
            return -1;
        }

        $req = "INSERT INTO Groupe(NomGrp, AdminGrp, PhotoGrp, ID_Cla) VALUES ('$nom', '$admin', '$photo', '')";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }

        if(!$this->_ajouterUtilisateurGroupe($admin, $nom)){
            return false;
        }
        return true;
    }

    public function _creerCommunaute($nom, $admin, $photo, $type) {
        if (empty($nom) || empty($admin) || empty($type)) {
            return false;
        }
        $req = "SELECT 1 FROM Utilisateur WHERE ID_Facebook='$admin'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if($sql->fetch_assoc()[1] != 1){
            error_log("Impossible de creer le groupe : L'admin n'est pas un utilisateur existant");
            return false;
        }
        $req = "SELECT 1 FROM Communaute WHERE NomCom='$nom'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if($sql->fetch_assoc()[1] != 1){
            error_log("Impossible de creer la communaute : Le nom a deja ete pris");
            return -1;
        }
        $req = "INSERT INTO Communaute(NomCom, AdminCom, TypeCom, PhotoCom, ID_Cla) VALUES ('$nom', '$admin', '$type', '$photo', '')";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }

        if(!$this->_ajouterUtilisateurCommunaute($admin, $nom)){
            return false;
        }
        return true;
    }

    public function _creerMatch($equipe1, $equipe2, $date_match) {
        if (empty($equipe1) || empty($equipe2) || empty($date_match)) {
            return false;
        }
        $req = "SELECT 1 FROM Match_Euro16 WHERE Equipe1='$equipe1' AND Equipe2='$equipe2' AND DateMatch=STR_TO_DATE('$date_match','%d-%m-%Y %h:%i:%s')";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if($sql->fetch_assoc()[1] == 1){
            error_log("Impossible de creer le match : Le match est deja present en base");
            return false;
        }
        $req = "INSERT INTO Match_Euro16(Equipe1, Equipe2, Score1, Score2, DateMatch, ID_Mch) VALUES ('$equipe1', '$equipe2', '', '', STR_TO_DATE('$date_match','%d-%m-%Y %h:%i:%s'), '')";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }
        return true;
    }

    public function _creerPronostic($id_facebook, $equipe1, $equipe2, $date_match, $score1, $score2, $resultat, $groupe, $communaute) {
        if (empty($id_facebook) || empty($equipe1) || empty($equipe2) || empty($date_match) || empty($resultat)) {
            return false;
        }
        if (!empty($groupe)) {
            $req = "SELECT ID_Cla FROM Groupe WHERE NomGrp='$groupe'";
            if (!($sql = $this->mysqli->query($req))) {
                error_log($this->mysqli->error);
                return false;
            }
            $ID_Cla = $sql->fetch_assoc()[1];
        } else if (!empty($communaute)) {
            $req = "SELECT ID_Cla FROM Communaute WHERE NomCom='$communaute'";
            if (!($sql = $this->mysqli->query($req))) {
                error_log($this->mysqli->error);
                return false;
            }
            $ID_Cla = $sql->fetch_assoc()[1];
        } else if (empty($groupe) && empty($communaute)){
            $ID_Cla = "ID_GLOBAL";
        }
        $req = "SELECT ID_Mch FROM Match_Euro16 WHERE Equipe1='$equipe1' AND Equipe2='$equipe2' AND DateMatch='$date_match'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        $ID_Mch = $sql->fetch_assoc()[1];

        $req = "INSERT INTO Pronostic(Score1, Score2, ID_Mch, Resultat, ID_Cla) VALUES ('$score1', '$score2', '$ID_Mch', '$resultat', '$ID_Cla')";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }
        return true;
    }

    public function _ajouterUtilisateurGroupe($id_facebook, $groupe) {
        if (empty($id_facebook) || empty($groupe)) {
            return false;
        }
        $req = "INSERT INTO Utilisateur_Groupe(Utilisateur, Groupe) VALUES ('$id_facebook', '$groupe')";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }
        return true;
    }

    public function _ajouterUtilisateurCommunaute($id_facebook, $communaute) {
        if (empty($id_facebook) || empty($communaute)) {
            return false;
        }
        $req = "INSERT INTO Utilisateur_Communaute(Utilisateur, Communaute) VALUES ('$id_facebook', '$communaute')";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }
        return true;
    }
}

?>