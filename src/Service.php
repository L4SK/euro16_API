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
        $req = "SELECT 1 FROM Utilisateur WHERE ID_Facebook='$id_facebook'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows != 0) {
            error_log("Impossible de creer l'utilisateur : id deja present en base");
            return false;
        }
        $req = "INSERT INTO Utilisateur(NomUti, PrenomUti, PhotoUti, ID_Facebook) VALUES ('$nom', '$prenom', '$photo', '$id_facebook')";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }
        return true;
    }

    public function _creerGroupe($nom, $admin, $photo) {
        if (empty($nom) || empty($admin)) {
            return false;
        }
        $req = "SELECT 1 FROM Utilisateur WHERE ID_Facebook='$admin'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows != 1) {
            error_log("Impossible de creer le groupe : L'admin n'est pas un utilisateur existant");
            return false;
        }

        $req = "INSERT INTO Groupe(NomGrp, AdminGrp, PhotoGrp) VALUES ('$nom', '$admin', '$photo')";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }

        if (!$this->_ajouterUtilisateurGroupe($admin, $nom)) {
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
        if ($sql->num_rows != 1) {
            error_log("Impossible de creer la communaute : L'admin n'est pas un utilisateur existant");
            return false;
        }
        $req = "INSERT INTO Communaute(NomCom, AdminCom, TypeCom, PhotoCom) VALUES ('$nom', '$admin', '$type', '$photo')";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }

        if (!$this->_ajouterUtilisateurCommunaute($admin, $nom)) {
            return false;
        }
        return true;
    }

    public function _creerMatch($equipe1, $equipe2, $date_match) {
        if (empty($equipe1) || empty($equipe2) || empty($date_match)) {
            return false;
        }
        $req = "SELECT 1 FROM Match_Euro16 WHERE Equipe1='$equipe1' AND Equipe2='$equipe2' AND DateMatch=STR_TO_DATE('$date_match','%d-%m-%Y %H:%i:%s')";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows != 0) {
            error_log("Impossible de creer le match : deja present en base");
            return false;
        }
        $req = "INSERT INTO Match_Euro16(Equipe1, Equipe2, Score1, Score2, DateMatch) VALUES ('$equipe1', '$equipe2', '', '', STR_TO_DATE('$date_match','%d-%m-%Y %H:%i:%s'))";
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
        if (!in_array($resultat, ['1', 'n', 'N', '2'])) {
            error_log("Resultat incorrect");
            return false;
        }

        if (!empty($groupe)) {
            $req = "SELECT ID_Cla FROM Groupe WHERE NomGrp='$groupe'";
            if (!($sql = $this->mysqli->query($req))) {
                error_log($this->mysqli->error);
                return false;
            }
            $ID_Cla = $sql->fetch_object()->ID_Cla;
        } else if (!empty($communaute)) {
            $req = "SELECT ID_Cla FROM Communaute WHERE NomCom='$communaute'";
            if (!($sql = $this->mysqli->query($req))) {
                error_log($this->mysqli->error);
                return false;
            }
            $ID_Cla = $sql->fetch_object()->ID_Cla;
        } else if (empty($groupe) && empty($communaute)) {
            $ID_Cla = "ID_GLOBAL";
        }
        $req = "SELECT ID_Mch FROM Match_Euro16 WHERE Equipe1='$equipe1' AND Equipe2='$equipe2' AND DateMatch=STR_TO_DATE('$date_match','%d-%m-%Y %H:%i:%s')";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            error_log("Match inexistant");
            return false;
        } else {
            $ID_Mch = $sql->fetch_object()->ID_Mch;
        }
        $req = "SELECT 1 FROM Utilisateur WHERE ID_Facebook='$id_facebook'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            error_log("Utilisateur inexistant");
            return false;
        }

        $req = "INSERT INTO Pronostic(Utilisateur, Score1, Score2, ID_Mch, Resultat, ID_Cla) VALUES ('$id_facebook', '$score1', '$score2', '$ID_Mch', '$resultat', '$ID_Cla')";
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
        $req = "SELECT 1 FROM Utilisateur WHERE ID_Facebook='$id_facebook'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows != 1) {
            error_log("Impossible d'ajouter l'utilisateur : inexistant en base");
            return false;
        }
        $req = "SELECT 1 FROM Utilisateur_Groupe WHERE Utilisateur='$id_facebook' AND Groupe='$groupe'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows != 0) {
            error_log("Impossible d'ajouter l'utilisateur : deja associe au groupe");
            return false;
        }
        $req = "SELECT 1 FROM Groupe WHERE NomGrp='$groupe'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows != 1) {
            error_log("Impossible d'ajouter l'utilisateur au groupe : groupe inexistant en base");
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
        $req = "SELECT 1 FROM Utilisateur WHERE ID_Facebook='$id_facebook'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows != 1) {
            error_log("Impossible d'ajouter l'utilisateur : inexistant en base");
            return false;
        }
        $req = "SELECT 1 FROM Utilisateur_Communaute WHERE Utilisateur='$id_facebook' AND Communaute='$communaute'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows != 0) {
            error_log("Impossible d'ajouter l'utilisateur : deja associe a la communaute");
            return false;
        }
        $req = "SELECT 1 FROM Communaute WHERE NomCom='$communaute'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows != 1) {
            error_log("Impossible d'ajouter l'utilisateur a la communaute : communaute inexistante en base");
            return false;
        }
        $req = "INSERT INTO Utilisateur_Communaute(Utilisateur, Communaute) VALUES ('$id_facebook', '$communaute')";
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

    public function _deleteGroupe($groupe) {
        if (empty($groupe)) {
            return -1;
        }
        $req = "DELETE FROM Groupe WHERE NomGrp = '$groupe'";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return -1;
        }
        return $this->mysqli->affected_rows;
    }

    public function _deleteCommunaute($communaute) {
        if (empty($communaute)) {
            return -1;
        }
        $req = "DELETE FROM Communaute WHERE NomCom = '$communaute'";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return -1;
        }
        return $this->mysqli->affected_rows;
    }

    public function _deleteUtilisateurGroupe($id_facebook, $groupe) {
        if (empty($communaute)) {
            return -1;
        }
        // TODO Delete pronostics de l'utilisateur pour le groupe en question OU ajouter un trigger en BDD pour le faire (mieux)
        // TODO Tests unitaires associes
        $req = "DELETE FROM Utilisateur_Groupe WHERE Utilisateur = '$id_facebook' AND NomGrp = '$groupe'";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return -1;
        }
        return $this->mysqli->affected_rows;
    }

    public function _deleteUtilisateurCommunaute($id_facebook, $communaute) {
        if (empty($communaute)) {
            return -1;
        }
        // TODO Delete pronostics de l'utilisateur pour la communaute en question OU ajouter un trigger en BDD pour le faire (mieux)
        // TODO Tests unitaires associes
        $req = "DELETE FROM Utilisateur_Communaute WHERE Utilisateur = '$id_facebook' AND NomCom = '$communaute'";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return -1;
        }
        return $this->mysqli->affected_rows;
    }
}

?>