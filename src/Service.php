<?php

require_once dirname(__FILE__) . "/../config.php";

class Service {

    public $db = NULL;
    public $mysqli;

    public function __construct($environnement) {
        switch ($environnement) {
            case "dev":
                $this->mysqli = new mysqli($GLOBALS['db_host_dev'], $GLOBALS['db_user_dev'], $GLOBALS['db_password_dev'], $GLOBALS['database_dev']);
                break;
            case "test":
                $this->mysqli = new mysqli($GLOBALS['db_host_test'], $GLOBALS['db_user_test'], $GLOBALS['db_password_test'], $GLOBALS['database_test']);
                break;
            case "prod":
                $this->mysqli = new mysqli($GLOBALS['db_host_prod'], $GLOBALS['db_user_prod'], $GLOBALS['db_password_prod'], $GLOBALS['database_prod']);
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

    /**
     * Uniquement utilisee pour les tests d'integration
     */
    public function _deleteAll() {
        $req = "DELETE FROM bareme;";
        $req .= "DELETE FROM classement;";
        $req .= "DELETE FROM communaute;";
        $req .= "DELETE FROM groupe;";
        $req .= "DELETE FROM match_euro16;";
        $req .= "DELETE FROM participe;";
        $req .= "DELETE FROM pronostic;";
        $req .= "DELETE FROM utilisateur;";
        $req .= "DELETE FROM bareme;";
        $req .= "DELETE FROM utilisateur_communaute;";
        $req .= "DELETE FROM utilisateur_groupe;";
        if (!$this->mysqli->multi_query($req)) {
            error_log($this->mysqli->error);
            return -1;
        }
        return $this->mysqli->more_results();
    }

    public function _creerUtilisateur($nom, $prenom, $photo, $email, $id_facebook) {
        if (empty($nom) || empty($prenom) || empty($id_facebook)) {
            return false;
        }
        if (empty($email)){
            $email = "NO EMAIL";
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
        $req = "INSERT INTO Utilisateur(NomUti, PrenomUti, PhotoUti, Email, ID_Facebook) VALUES ('$nom', '$prenom', '$photo', '$email', '$id_facebook')";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }
        $req = "INSERT INTO Participe(Utilisateur, Classement, Points) VALUES ('$id_facebook', '1', '0')";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }
        if($this->mysqli->affected_rows != 1) {
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
        $req = "SELECT 1 FROM Groupe WHERE NomGrp='$nom'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows != 0) {
            error_log("Impossible de creer le groupe : Le nom du groupe existe deja");
            return -1;
        }
        $req = "SELECT COUNT(*) FROM Groupe WHERE AdminGrp='$admin'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows > 5) {
            error_log("Impossible de creer le groupe : Le nombre de groupe possible est d�pass�");
            return -2;
        }
        $req = "INSERT INTO Classement(ID_Bar) VALUES (10001)";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }
        $last_id = $this->mysqli->insert_id;
        $req = "INSERT INTO Groupe(NomGrp, AdminGrp, PhotoGrp, ID_Cla) VALUES ('$nom', '$admin', '$photo', '$last_id')";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }

        if (!$this->_ajouterUtilisateurGroupe($admin, $nom, 1)) {
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
        $req = "SELECT COUNT(*) FROM Communaute WHERE NomCom='$admin'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows > 5) {
            error_log("Impossible de creer le groupe : Le nombre de groupe possible est d�pass�");
            return -2;
        }
        $req = "INSERT INTO Classement(ID_Bar) VALUES (10001)";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }
        $last_id = $this->mysqli->insert_id;
        $req = "INSERT INTO Communaute(NomCom, AdminCom, TypeCom, PhotoCom, ID_Cla) VALUES ('$nom', '$admin', '$type', '$photo', '$last_id')";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }
        if (!$this->_ajouterUtilisateurCommunaute($admin, $nom, 1)) {
            return false;
        }
        return true;
    }
    public function _creerMatch($equipe1, $equipe2, $date_match, $groupe) {
        if (empty($equipe1) || empty($equipe2) || empty($date_match) || empty($groupe)) {
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
        $req = "INSERT INTO Match_Euro16(Equipe1, Equipe2, DateMatch, Groupe) VALUES ('$equipe1', '$equipe2', STR_TO_DATE('$date_match','%d-%m-%Y %H:%i:%s'), '$groupe')";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }
        return true;
    }
    public function _creerPronostic($id_facebook, $equipe1, $equipe2, $date_match, $score1, $score2, $resultat) {
        if (empty($id_facebook) || empty($equipe1) || empty($equipe2) || empty($date_match) || empty($resultat)) {
            return false;
        }
        if (!in_array($resultat, ['1', 'n', 'N', '2'])) {
            error_log("Resultat incorrect");
            return false;
        }
        if(strtotime($date_match) < time()) {
            return false;
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

        if(empty($score1) || empty($score2)){
            $req = "INSERT INTO Pronostic(Utilisateur, ID_Mch, Resultat) VALUES ('$id_facebook', '$ID_Mch', '$resultat')";
        } else {
            $req = "INSERT INTO Pronostic(Utilisateur, Score1, Score2, ID_Mch, Resultat) VALUES ('$id_facebook', '$score1', '$score2', '$ID_Mch', '$resultat')";
        }
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }
        return true;
    }
    public function _ajouterUtilisateurGroupe($id_facebook, $groupe, $statut) {
        if (empty($id_facebook) || empty($groupe) || empty($statut)) {
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

        $req = "SELECT * FROM Groupe WHERE NomGrp='$groupe'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows != 1) {
            error_log("Impossible d'ajouter l'utilisateur au groupe : groupe inexistant en base");
            return false;
        }
        if (!($result = $sql->fetch_assoc())){
            error_log($this->mysqli->error);
            return false;
        }
        $id = $result['ID_Grp'];
        $idCla = $result['ID_Cla'];
        $req = "INSERT INTO Utilisateur_Groupe(Utilisateur, Groupe, Statut) VALUES ('$id_facebook', '$id', '$statut')";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }
        $req = "INSERT INTO Participe(Utilisateur, Classement, Points) VALUES ('$id_facebook', '$idCla', '0')";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }
        return true;
    }
    public function _ajouterUtilisateurCommunaute($id_facebook, $communaute, $statut) {
        if (empty($id_facebook) || empty($communaute) || empty($statut)) {
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
        $req = "SELECT * FROM Communaute WHERE NomCom='$communaute'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows != 1) {
            error_log("Impossible d'ajouter l'utilisateur a la communaute : communaute inexistante en base");
            return false;
        }
        if (!($result = $sql->fetch_assoc())){
            error_log($this->mysqli->error);
            return false;
        }
        $id = $result['ID_Com'];
        $idCla = $result['ID_Cla'];
        $req = "INSERT INTO Utilisateur_Communaute(Utilisateur, Communaute, Statut) VALUES ('$id_facebook', '$id', '$statut')";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return false;
        }
        $req = "INSERT INTO Participe(Utilisateur, Classement, Points) VALUES ('$id_facebook', '$idCla', '0')";
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
    public function _getGroupes($utilisateur) {
        $req = "SELECT Groupe FROM Utilisateur_Groupe WHERE Utilisateur = '$utilisateur'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        $ids = array();
        if ($sql->num_rows > 0) {
            $cpt = 0;
            while ($rlt = $sql->fetch_assoc()) {
                $ids[$cpt] = $rlt["Groupe"];
                $cpt = $cpt + 1;
            }
        }
        $ids = implode(",", $ids);
        $req = "SELECT NomGrp, AdminGrp, PhotoGrp FROM Groupe
                WHERE Groupe.ID_Grp NOT IN ($ids)";
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
    public function _getCommunautes($utilisateur) {
        $req = "SELECT Communaute FROM Utilisateur_Communaute WHERE Utilisateur = '$utilisateur'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        $ids = array();
        if ($sql->num_rows > 0) {
            $cpt = 0;
            while ($rlt = $sql->fetch_assoc()) {
                $ids[$cpt] = $rlt["Communaute"];
                $cpt = $cpt + 1;
            }
            $ids = implode(",", $ids);
        }
        $req = "SELECT Communaute.NomCom, Communaute.AdminCom, Communaute.PhotoCom, Communaute.TypeCom
                FROM Communaute
                WHERE Communaute.ID_Com NOT IN ($ids)";
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
    public function _getGroupesUtilisateur($id_facebook) {
        $req = "SELECT NomGrp, AdminGrp, PhotoGrp, Statut FROM Groupe INNER JOIN Utilisateur_Groupe ON Groupe.ID_Grp = Utilisateur_Groupe.Groupe
              WHERE Utilisateur_Groupe.Utilisateur = '$id_facebook'";
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
    public function _getCommunautesUtilisateur($id_facebook) {
        $req = "SELECT NomCom, AdminCom, PhotoCom, TypeCom, Statut FROM Communaute INNER JOIN Utilisateur_Communaute ON Communaute.ID_Com = Utilisateur_Communaute.Communaute
              WHERE Utilisateur_Communaute.Utilisateur = '$id_facebook'";
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
    public function _getUtilisateur($id_facebook) {
        if(empty($id_facebook)){
            return [];
        }
        $req = "SELECT * FROM Utilisateur WHERE ID_Facebook='$id_facebook'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return [];
        }
        if (!($result = $sql->fetch_assoc())){
            error_log($this->mysqli->error);
            return false;
        }
        return $result;
    }
    public function _getGroupe($groupe) {
        if(empty($groupe)){
            return [];
        }
        $req = "SELECT NomGrp, AdminGrp, PhotoGrp FROM Groupe WHERE NomGrp='$groupe'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return [];
        }
        if (!($result = $sql->fetch_assoc())){
            error_log($this->mysqli->error);
            return false;
        }
        return $result;
    }
    public function _getCommunaute($communaute) {
        if(empty($communaute)){
            return [];
        }
        $req = "SELECT NomCom, AdminCom, PhotoCom, TypeCom FROM Communaute WHERE NomCom='$communaute'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return [];
        }
        if (!($result = $sql->fetch_assoc())){
            error_log($this->mysqli->error);
            return false;
        }
        return $result;
    }
    public function _getUtilisateursGroupe($groupe) {
        if(empty($groupe)){
            return [];
        }
        $req = "SELECT * FROM Groupe WHERE NomGrp='$groupe'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return false;
        }
        $ID_Grp = $sql->fetch_object()->ID_Grp;
        $req = "SELECT Utilisateur.*, Utilisateur_Groupe.Statut FROM Utilisateur JOIN Utilisateur_Groupe ON Utilisateur.ID_Facebook = Utilisateur_Groupe.Utilisateur WHERE Utilisateur_Groupe.Groupe = '$ID_Grp'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows > 0) {
            while ($rlt = $sql->fetch_assoc()) {
                $result[] = $rlt;
            }
        }
        return $result;
    }
    public function _getUtilisateursCommunaute($communaute) {
        if(empty($communaute)){
            return [];
        }
        $req = "SELECT * FROM Communaute WHERE NomCom='$communaute'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return false;
        }
        $ID_Com = $sql->fetch_object()->ID_Com;
        $req = "SELECT Utilisateur.*, Utilisateur_Communaute.Statut FROM Utilisateur JOIN Utilisateur_Communaute ON Utilisateur.ID_Facebook = Utilisateur_Communaute.Utilisateur WHERE Utilisateur_Communaute.Communaute = '$ID_Com'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows > 0) {
            while ($rlt = $sql->fetch_assoc()) {
                $result[] = $rlt;
            }
        }
        return $result;
    }
    public function _getMatchs() {
        $req = "SELECT Equipe1, Equipe2, Score1, Score2, DateMatch, Groupe FROM Match_Euro16";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        $result = array();
        if ($sql->num_rows > 0) {
            while ($rlt = $sql->fetch_assoc()) {
                $rlt["DateMatch"] = strtotime($rlt["DateMatch"]);
                $result[] = $rlt;
            }
        }
        return $result;
    }
    public function _getMatch($equipe1, $equipe2, $dateMatch) {
        if(empty($equipe1) || empty($equipe2) || empty($dateMatch)) {
            return false;
        }
        $req = "SELECT Equipe1, Equipe2, Score1, Score2, DateMatch, Groupe FROM Match_Euro16 WHERE Equipe1='$equipe1' AND Equipe2='$equipe2' AND DateMatch=STR_TO_DATE('$dateMatch','%d-%m-%Y %H:%i:%s')";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return [];
        }
        if (!($result = $sql->fetch_assoc())){
            error_log($this->mysqli->error);
            return false;
        }
        $result["DateMatch"] = strtotime($result["DateMatch"]);
        return $result;
    }
    public function _getPronostic($utilisateur, $equipe1, $equipe2, $date_match) {
        if(empty($utilisateur) || empty($equipe1) || empty($equipe2) || empty($date_match)) {
            return false;
        }
        $req = "SELECT ID_Mch FROM Match_Euro16 WHERE Equipe1='$equipe1' AND Equipe2='$equipe2' AND DateMatch=STR_TO_DATE('$date_match','%d-%m-%Y %H:%i:%s')";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return false;
        }
        $ID_Mch = $sql->fetch_object()->ID_Mch;
        $req = "SELECT Utilisateur, Score1, Score2, Resultat FROM Pronostic WHERE Utilisateur='$utilisateur' AND ID_Mch='$ID_Mch'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return [];
        }
        if (!($result = $sql->fetch_assoc())){
            error_log($this->mysqli->error);
            return false;
        }
        return $result;
    }
    public function _getClassementCommunaute($communaute) {
        $req = "SELECT ID_Cla FROM Communaute WHERE NomCom = '$communaute'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        $ID_Cla = $sql->fetch_object()->ID_Cla;
        $req = "SELECT Participe.Points, Utilisateur.NomUti, Utilisateur.PrenomUti, Utilisateur.PhotoUti, Utilisateur.ID_Facebook
                FROM Participe
                JOIN Utilisateur
                ON Participe.Utilisateur = Utilisateur.ID_Facebook
                WHERE Classement = '$ID_Cla' ORDER BY Points";
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
    public function _getClassementGroupe($groupe) {
        $req = "SELECT ID_Cla FROM Groupe WHERE NomGrp = '$groupe'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        $ID_Cla = $sql->fetch_object()->ID_Cla;
        $req = "SELECT Participe.Points, Utilisateur.NomUti, Utilisateur.PrenomUti, Utilisateur.PhotoUti, Utilisateur.ID_Facebook
                FROM Participe
                JOIN Utilisateur
                ON Participe.Utilisateur = Utilisateur.ID_Facebook
                WHERE Classement = '$ID_Cla' ORDER BY Points";
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
    public function _getClassementGlobal() {
        $req = "SELECT Participe.Points, Utilisateur.NomUti, Utilisateur.PrenomUti, Utilisateur.PhotoUti, Utilisateur.ID_Facebook
                FROM Participe
                JOIN Utilisateur
                ON Participe.Utilisateur = Utilisateur.ID_Facebook
                WHERE Classement = '1' ORDER BY Points";
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
    public function _getPronosticsUtilisateur($utilisateur) {
        if(empty($utilisateur)) {
            return false;
        }
        $req = "SELECT Pronostic.Utilisateur, Pronostic.Score1, Pronostic.Score2, Pronostic.Resultat, Match_Euro16.Equipe1, Match_Euro16.Equipe2, Match_Euro16.DateMatch
                FROM Pronostic JOIN Match_Euro16
                ON Pronostic.ID_Mch = Match_Euro16.ID_Mch
                WHERE Utilisateur='$utilisateur'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return [];
        }
        $result = array();
        if ($sql->num_rows > 0) {
            while ($rlt = $sql->fetch_assoc()) {
                $rlt["DateMatch"] = strtotime($rlt["DateMatch"]);
                $result[] = $rlt;
            }
        }
        return $result;
    }
    public function _getNonPronosticsUtilisateur($utilisateur) {
        if(empty($utilisateur)) {
            return false;
        }
        $req = "SELECT Match_Euro16.ID_Mch
                FROM Pronostic JOIN Match_Euro16
                ON Pronostic.ID_Mch = Match_Euro16.ID_Mch
                WHERE Utilisateur='$utilisateur'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        $idsMatch = array();
        if ($sql->num_rows > 0) {
            $cpt = 0;
            while ($rlt = $sql->fetch_assoc()) {
                $idsMatch[$cpt] = $rlt["ID_Mch"];
                $cpt = $cpt + 1;
            }
            $idsMatch = implode(",", $idsMatch);
        }
        $req = "SELECT DISTINCT Match_Euro16.Equipe1, Match_Euro16.Equipe2, Match_Euro16.DateMatch
                FROM Match_Euro16
                WHERE Match_Euro16.ID_Mch NOT IN ('$idsMatch')
                AND Match_Euro16.DateMatch > CURRENT_TIMESTAMP
                ORDER BY Match_Euro16.DateMatch ASC";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return [];
        }
        $result = array();
        if ($sql->num_rows > 0) {
            while ($rlt = $sql->fetch_assoc()) {
                $rlt["DateMatch"] = strtotime($rlt["DateMatch"]);
                $result[] = $rlt;
            }
        }
        return $result;
    }
    public function _getPronosticsGroupe($groupe, $equipe1, $equipe2, $date_match) {
        if(empty($groupe) || empty($equipe1) || empty($equipe2) || empty($date_match)) {
            return false;
        }
        $req = "SELECT ID_Mch FROM Match_Euro16 WHERE Equipe1='$equipe1' AND Equipe2='$equipe2' AND DateMatch=STR_TO_DATE('$date_match','%d-%m-%Y %H:%i:%s')";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return false;
        }
        $ID_Mch = $sql->fetch_object()->ID_Mch;
        $req = "SELECT Utilisateur_Groupe.Utilisateur
                FROM Utilisateur_Groupe JOIN Groupe
                ON Utilisateur_Groupe.Groupe = Groupe.ID_Grp
                WHERE Groupe.NomGrp='$groupe'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        $idsUtilisateur = array();
        if ($sql->num_rows > 0) {
            $cpt = 0;
            while ($rlt = $sql->fetch_assoc()) {
                $idsUtilisateur[$cpt] = "'" . $rlt["Utilisateur"] . "'";
                $cpt = $cpt + 1;
            }
            $idsUtilisateur = implode(",", $idsUtilisateur);
        } else {
            $idsUtilisateur = implode("", $idsUtilisateur);
        }
        if(!empty($idsUtilisateur)) {
            $req = "SELECT Resultat
                FROM Pronostic
                WHERE Utilisateur IN ($idsUtilisateur)
                AND ID_Mch='$ID_Mch'";
        } else {
            $req = "SELECT Resultat
                FROM Pronostic
                WHERE ID_Mch='$ID_Mch'";
        }
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return [];
        }
        $result = array();
        if ($sql->num_rows > 0) {
            while ($rlt = $sql->fetch_assoc()) {
                $result[] = $rlt;
            }
        }
        return $result;
    }
    public function _getPronosticsCommunaute($communaute, $equipe1, $equipe2, $date_match) {
        if(empty($communaute) || empty($equipe1) || empty($equipe2) || empty($date_match)) {
            return false;
        }
        $req = "SELECT ID_Mch FROM Match_Euro16 WHERE Equipe1='$equipe1' AND Equipe2='$equipe2' AND DateMatch=STR_TO_DATE('$date_match','%d-%m-%Y %H:%i:%s')";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return false;
        }
        $ID_Mch = $sql->fetch_object()->ID_Mch;
        $req = "SELECT Utilisateur_Communaute.Utilisateur
                FROM Utilisateur_Communaute JOIN Communaute
                ON Utilisateur_Communaute.Communaute = Communaute.ID_Com
                WHERE Communaute.NomCom='$communaute'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        $idsUtilisateur = array();
        if ($sql->num_rows > 0) {
            $cpt = 0;
            while ($rlt = $sql->fetch_assoc()) {
                $idsUtilisateur[$cpt] = "'" . $rlt["Utilisateur"] . "'";
                $cpt = $cpt + 1;
            }
            $idsUtilisateur = implode(",", $idsUtilisateur);
        } else {
            $idsUtilisateur = implode("", $idsUtilisateur);
        }
        if(!empty($idsUtilisateur)) {
            $req = "SELECT Resultat
                FROM Pronostic
                WHERE Utilisateur IN ($idsUtilisateur)
                AND ID_Mch='$ID_Mch'";
        } else {
            $req = "SELECT Resultat
                FROM Pronostic
                WHERE ID_Mch='$ID_Mch'";
        }
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return [];
        }
        $result = array();
        if ($sql->num_rows > 0) {
            while ($rlt = $sql->fetch_assoc()) {
                $result[] = $rlt;
            }
        }
        return $result;
    }
    public function _getPronosticsGlobal($equipe1, $equipe2, $date_match) {
        if(empty($equipe1) || empty($equipe2) || empty($date_match)) {
            return false;
        }
        $req = "SELECT ID_Mch FROM Match_Euro16 WHERE Equipe1='$equipe1' AND Equipe2='$equipe2' AND DateMatch=STR_TO_DATE('$date_match','%d-%m-%Y %H:%i:%s')";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return false;
        }
        $ID_Mch = $sql->fetch_object()->ID_Mch;
        $req = "SELECT Resultat
                FROM Pronostic
                WHERE ID_Mch='$ID_Mch'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return [];
        }
        $result = array();
        if ($sql->num_rows > 0) {
            while ($rlt = $sql->fetch_assoc()) {
                $result[] = $rlt;
            }
        }
        return $result;
    }

    public function _updateUtilisateur($nom, $prenom, $photo, $email, $id_facebook) {
        if(empty($id_facebook)){
            return false;
        }
        $req = "SELECT * FROM Utilisateur WHERE ID_Facebook='$id_facebook'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return false;
        }

        $req = "UPDATE Utilisateur SET NomUti='$nom', PrenomUti='$prenom', PhotoUti='$photo', Email='$email' WHERE ID_Facebook='$id_facebook'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        return true;

    }
    public function _updateStatutUtilisateurGroupe($groupe, $utilisateur, $new_statut) {
        if(empty($groupe) || empty($utilisateur) || empty($new_statut)){
            return false;
        }
        $req = "SELECT ID_Grp FROM Groupe WHERE NomGrp='$groupe'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows != 1) {
            return false;
        }
        if (!($result = $sql->fetch_assoc())){
            error_log($this->mysqli->error);
            return false;
        }
        $idGrp = $result['ID_Grp'];
        $req = "SELECT 1 FROM Utilisateur_Groupe WHERE Groupe='$idGrp' AND Utilisateur='$utilisateur'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows != 1) {
            return false;
        }
        $req = "UPDATE Utilisateur_Groupe SET Statut='$new_statut' WHERE Groupe='$idGrp' AND Utilisateur='$utilisateur'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($this->mysqli->affected_rows == 0) {
            return false;
        }
        return true;
    }
    public function _updateStatutUtilisateurCommunaute($communaute, $utilisateur, $new_statut) {
        if(empty($communaute) || empty($utilisateur) || empty($new_statut)){
            return false;
        }
        $req = "SELECT ID_Com FROM Communaute WHERE NomCom='$communaute'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return false;
        }
        if (!($result = $sql->fetch_assoc())){
            error_log($this->mysqli->error);
            return false;
        }
        $idCom = $result['ID_Com'];
        $req = "SELECT 1 FROM Utilisateur_Communaute WHERE Communaute='$idCom' AND Utilisateur='$utilisateur'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows != 1) {
            return false;
        }
        $req = "UPDATE Utilisateur_Communaute SET Statut='$new_statut' WHERE Communaute='$idCom' AND Utilisateur='$utilisateur'  ";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($this->mysqli->affected_rows == 0) {
            return false;
        }
        return true;
    }
    public function _updateGroupe($old_nom, $new_nom, $admin, $photo) {
        if(empty($old_nom)){
            return false;
        }
        $req = "SELECT ID_Grp FROM Groupe WHERE NomGrp='$old_nom'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return false;
        }

        $idGrp = $rlt = $sql->fetch_assoc()["ID_Grp"];
        if(!empty($new_nom)) {
            $req = "SELECT 1 FROM Groupe WHERE NomGrp='$new_nom'";
            if (!($sql = $this->mysqli->query($req))) {
                error_log($this->mysqli->error);
                return false;
            }
            if ($sql->num_rows != 0) {
                return false;
            }
            $req = "UPDATE Groupe SET NomGrp='$new_nom' WHERE ID_Grp='$idGrp'";
            if (!($sql = $this->mysqli->query($req))) {
                error_log($this->mysqli->error);
                return false;
            }
        }

        if(!empty($admin)) {
            $req = "SELECT 1 FROM Utilisateur WHERE ID_Facebook='$admin'";
            if (!($sql = $this->mysqli->query($req))) {
                error_log($this->mysqli->error);
                return false;
            }
            if ($sql->num_rows != 1) {
                error_log("Impossible de creer la communaute : L'admin n'est pas un utilisateur existant");
                return false;
            }
            $req = "UPDATE Groupe SET AdminGrp='$admin' WHERE ID_Grp='$idGrp'";
            if (!($sql = $this->mysqli->query($req))) {
                error_log($this->mysqli->error);
                return false;
            }
        }

        if(!empty($photo)) {
            $req = "UPDATE Groupe SET PhotoGrp='$photo' WHERE ID_Grp='$idGrp'";
            if (!($sql = $this->mysqli->query($req))) {
                error_log($this->mysqli->error);
                return false;
            }
        }

        return true;
    }
    public function _updateCommunaute($old_nom, $new_nom, $admin, $type, $photo) {
        if(empty($old_nom)){
            return false;
        }
        $req = "SELECT ID_Com FROM Communaute WHERE NomCom='$old_nom'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return false;
        }
        if (!($result = $sql->fetch_assoc())){
            error_log($this->mysqli->error);
            return false;
        }
        $idCom = $result['ID_Com'];
        if(!empty($new_nom)) {
            $req = "SELECT 1 FROM Communaute WHERE NomCom='$new_nom'";
            if (!($sql = $this->mysqli->query($req))) {
                error_log($this->mysqli->error);
                return false;
            }
            if ($sql->num_rows != 0) {
                return false;
            }
            $req = "UPDATE Communaute SET NomCom='$new_nom' WHERE ID_Com='$idCom'";
            if (!($sql = $this->mysqli->query($req))) {
                error_log($this->mysqli->error);
                return false;
            }
        }

        if(!empty($admin)) {
            $req = "SELECT 1 FROM Utilisateur WHERE ID_Facebook='$admin'";
            if (!($sql = $this->mysqli->query($req))) {
                error_log($this->mysqli->error);
                return false;
            }
            if ($sql->num_rows != 1) {
                error_log("Impossible de creer la communaute : L'admin n'est pas un utilisateur existant");
                return false;
            }
            $req = "UPDATE Communaute SET AdminCom='$admin' WHERE ID_Com='$idCom'";
            if (!($sql = $this->mysqli->query($req))) {
                error_log($this->mysqli->error);
                return false;
            }
        }

        if(!empty($type)) {
            $req = "UPDATE Communaute SET TypeCom='$type' WHERE ID_Com='$idCom'";
            if (!($sql = $this->mysqli->query($req))) {
                error_log($this->mysqli->error);
                return false;
            }
        }

        if(!empty($photo)) {
            $req = "UPDATE Communaute SET PhotoCom='$photo' WHERE ID_Com='$idCom'";
            if (!($sql = $this->mysqli->query($req))) {
                error_log($this->mysqli->error);
                return false;
            }
        }

        return true;
    }
    public function _updateMatch($equipe1_old, $equipe1_new, $equipe2_old, $equipe2_new, $score1, $score2, $dateMatch_old, $dateMatch_new, $groupe) {
        $req = "SELECT 1 FROM Match_Euro16 WHERE Equipe1='$equipe1_old' AND Equipe2='$equipe2_old' AND DateMatch=STR_TO_DATE('$dateMatch_old','%d-%m-%Y %H:%i:%s')";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return false;
        }
        if(empty($equipe1_new) || empty($equipe2_new) || empty($dateMatch_new)) {
            $req = "UPDATE Match_Euro16 SET Score1='$score1', Score2='$score2', Groupe='$groupe' WHERE Equipe1='$equipe1_old' AND Equipe2='$equipe2_old' AND DateMatch=STR_TO_DATE('$dateMatch_old','%d-%m-%Y %H:%i:%s')";
            if (!($sql = $this->mysqli->query($req))) {
                error_log($this->mysqli->error);
                return false;
            }
        } else {
            if(!empty($score1) && !empty($score2)) {
                $req = "UPDATE Match_Euro16 SET Equipe1='$equipe1_new', Equipe2='$equipe2_new', Score1='$score1', Score2='$score2', DateMatch=STR_TO_DATE('$dateMatch_new','%d-%m-%Y %H:%i:%s'), Groupe='$groupe' WHERE Equipe1='$equipe1_old' AND Equipe2='$equipe2_old' AND DateMatch=STR_TO_DATE('$dateMatch_old','%d-%m-%Y %H:%i:%s')";
                if (!($sql = $this->mysqli->query($req))) {
                    error_log($this->mysqli->error);
                    return false;
                }
            } else {
                $req = "UPDATE Match_Euro16 SET Equipe1='$equipe1_new', Equipe2='$equipe2_new', DateMatch=STR_TO_DATE('$dateMatch_new','%d-%m-%Y %H:%i:%s'), Groupe='$groupe' WHERE Equipe1='$equipe1_old' AND Equipe2='$equipe2_old' AND DateMatch=STR_TO_DATE('$dateMatch_old','%d-%m-%Y %H:%i:%s')";
                if (!($sql = $this->mysqli->query($req))) {
                    error_log($this->mysqli->error);
                    return false;
                }
            }
        }
        return true;
    }
    public function _updatePronostic($utilisateur, $equipe1, $equipe2, $date_match, $resultat, $score1, $score2) {
        if(empty($utilisateur) || empty($equipe1) || empty($equipe2) || empty($date_match)) {
            return false;
        }
        if(strtotime($date_match) < time()) {
            return false;
        }
        $req = "SELECT * FROM Utilisateur WHERE ID_Facebook='$utilisateur'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return false;
        }
        $req = "SELECT ID_Mch FROM Match_Euro16 WHERE Equipe1='$equipe1' AND Equipe2='$equipe2' AND DateMatch=STR_TO_DATE('$date_match','%d-%m-%Y %H:%i:%s')";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 0) {
            return false;
        }
        $ID_Mch = $sql->fetch_object()->ID_Mch;
        if(empty($score1) || empty($score2)) {
            $req = "UPDATE Pronostic SET Resultat='$resultat' WHERE Utilisateur='$utilisateur' AND ID_Mch='$ID_Mch'";
        } else {
            $req = "UPDATE Pronostic SET Resultat='$resultat', Score1='$score1', Score2='$score2' WHERE Utilisateur='$utilisateur' AND ID_Mch='$ID_Mch'";
        }
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        return true;
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
        if (empty($id_facebook) || empty($groupe)) {
            return -1;
        }
        $req = "SELECT 1 FROM Groupe WHERE AdminGrp='$id_facebook' AND NomGrp='$groupe'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return -1;
        }
        if ($sql->num_rows == 1) {
            error_log("Impossible de supprimer l'utilisateur : Il est admin du groupe");
            return -1;
        }
        $req = "SELECT ID_Grp FROM Groupe WHERE NomGrp='$groupe'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return -1;
        }
        $ID_Grp = $sql->fetch_object()->ID_Grp;
        $req = "DELETE FROM Utilisateur_Groupe WHERE Utilisateur = '$id_facebook' AND Groupe = '$ID_Grp'";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return -1;
        }
        return $this->mysqli->affected_rows;
    }
    public function _deleteUtilisateurCommunaute($id_facebook, $communaute) {
        if (empty($id_facebook) || empty($communaute)) {
            return -1;
        }
        $req = "SELECT 1 FROM Communaute WHERE AdminCom='$id_facebook' AND NomCom='$communaute'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return false;
        }
        if ($sql->num_rows == 1) {
            error_log("Impossible de supprimer l'utilisateur : Il est admin de la communaute");
            return -1;
        }
        $req = "SELECT ID_Com FROM Communaute WHERE NomCom='$communaute'";
        if (!($sql = $this->mysqli->query($req))) {
            error_log($this->mysqli->error);
            return -1;
        }
        $ID_Com = $sql->fetch_object()->ID_Com;
        $req = "DELETE FROM Utilisateur_Communaute WHERE Utilisateur = '$id_facebook' AND Communaute = '$ID_Com'";
        if (!$this->mysqli->query($req)) {
            error_log($this->mysqli->error);
            return -1;
        }
        return $this->mysqli->affected_rows;
    }
}

?>