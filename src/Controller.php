<?php

require_once dirname(__FILE__) . "/../lib/Rest.inc.php";
require_once dirname(__FILE__) . "/Service.php";

class Controller extends REST {

    public $data = "";
    public $service;

    public function __construct($environnement) {
        parent::__construct();
        $this->service = new Service($environnement);
    }

    public function processApi() {
        $func = strtolower(trim(str_replace("/", "", $_REQUEST['rquest'])));
        if ((int)method_exists($this, $func) > 0)
            $this->$func();
        else
            $this->response('', 404);                // If the method not exist with in this class, response would be "Page not found".
    }

    private function creerUtilisateur() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $nom = $this->_request['nom'];
        $prenom = $this->_request['prenom'];
        $photo = $this->_request['photo'];
        $id_facebook = $this->_request['id_facebook'];
        if (!empty($nom) && !empty($prenom) && !empty($photo) && !empty($id_facebook)) {
            switch ($this->service->_creerUtilisateur($nom, $prenom, $photo, $id_facebook)) {
                case true:
                    $this->response('', 201);
                    break;
                case false:
                    $this->response('', 400);
                    break;
            }
        } else {
            $error = array('status' => "Failed", "msg" => "Invalid json");
            $this->response($this->json($error), 400);
        }
    }
    private function creerGroupe() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $nom = $this->_request['nom'];
        $admin = $this->_request['admin'];
        $photo = $this->_request['photo'];
        if (!empty($nom) && !empty($admin)) {
            switch ($this->service->_creerGroupe($nom, $admin, $photo)) {
                case true:
                    $this->response('', 201);
                    break;
                case false:
                    $this->response('', 400);
                    break;
                case -1:
                    $this->response('Nom de groupe déjà utilisé', 409);
                    break;
            }
        } else {
            $error = array('status' => "Failed", "msg" => "Invalid json");
            $this->response($this->json($error), 400);
        }
    }
    private function creerCommunaute() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $nom = $this->_request['nom'];
        $admin = $this->_request['admin'];
        $photo = $this->_request['photo'];
        $type = $this->_request['type'];
        if (!empty($nom) && !empty($admin) && !empty($type)) {
            switch ($this->service->_creerCommunaute($nom, $admin, $photo, $type)) {
                case true:
                    $this->response('', 201);
                    break;
                case false:
                    $this->response('', 400);
                    break;
                case -1:
                    $this->response('Nom de communauté déjà utiliseé', 409);
                    break;
            }
        } else {
            $error = array('status' => "Failed", "msg" => "Invalid json");
            $this->response($this->json($error), 400);
        }
    }
    private function creerMatch() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $equipe1 = $this->_request['equipe1'];
        $equipe2 = $this->_request['equipe2'];
        $date_match = $this->_request['date_match'];
        if (!empty($equipe1) && !empty($equipe2) && !empty($date_match)) {
            switch ($this->service->_creerMatch($equipe1, $equipe2, $date_match)) {
                case true:
                    $this->response('', 201);
                    break;
                case false:
                    $this->response('', 400);
                    break;
            }
        } else {
            $error = array('status' => "Failed", "msg" => "Invalid json");
            $this->response($this->json($error), 400);
        }
    }
    private function creerPronostic() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $id_facebook = $this->_request['id_facebook'];
        $equipe1 = $this->_request['equipe1'];
        $equipe2 = $this->_request['equipe2'];
        $date_match = $this->_request['date_match'];
        $score1 = $this->_request['score1'];
        $score2 = $this->_request['score2'];
        $resultat = $this->_request['resultat'];
        $groupe = $this->_request['groupe'];
        $communaute = $this->_request['communaute'];
        if (!empty($id_facebook) && !empty($equipe1) && !empty($equipe2) && !empty($date_match) && !empty($resultat) && (!empty($groupe) || !empty($communaute))) {
            switch ($this->service->_creerPronostic($id_facebook, $equipe1, $equipe2, $date_match, $score1, $score2, $resultat, $groupe, $communaute)) {
                case true:
                    $this->response('', 201);
                    break;
                case false:
                    $this->response('', 400);
                    break;
            }
        } else {
            $error = array('status' => "Failed", "msg" => "Invalid json");
            $this->response($this->json($error), 400);
        }
    }
    private function ajouterUtilisateurGroupe() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $id_facebook = $this->_request['id_facebook'];
        $groupe = $this->_request['groupe'];
        if (!empty($id_facebook) && !empty($groupe)) {
            switch ($this->service->_ajouterUtilisateurGroupe($id_facebook, $groupe)) {
                case true:
                    $this->response('', 201);
                    break;
                case false:
                    $this->response('', 400);
                    break;
            }
        } else {
            $error = array('status' => "Failed", "msg" => "Invalid json");
            $this->response($this->json($error), 400);
        }
    }
    private function ajouterUtilisateurCommunaute() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        $id_facebook = $this->_request['id_facebook'];
        $communaute = $this->_request['communaute'];
        if (!empty($id_facebook) && !empty($communaute)) {
            switch ($this->service->_ajouterUtilisateurCommunaute($id_facebook, $communaute)) {
                case true:
                    $this->response('', 201);
                    break;
                case false:
                    $this->response('', 400);
                    break;
            }
        } else {
            $error = array('status' => "Failed", "msg" => "Invalid json");
            $this->response($this->json($error), 400);
        }
    }

    private function getUtilisateurs() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $utilisateurs = $this->service->_getUtilisateurs();
        switch (true) {
            case $utilisateurs == false:
                $this->response('', 400);
                break;
            case sizeof($utilisateurs) > 0:
                $this->response($this->json($utilisateurs), 200);
                break;
            default:
                $this->response('', 204);
                break;
        }
    }
    private function getGroupes() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $groupe = $this->service->_getGroupes();
        switch (true) {
            case $groupe == false:
                $this->response('', 400);
                break;
            case sizeof($groupe) > 0:
                $this->response($this->json($groupe), 200);
                break;
            default:
                $this->response('', 204);
                break;
        }
    }
    private function getCommunautes() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $communaute = $this->service->_getCommunautes();
        switch (true) {
            case $communaute == false:
                $this->response('', 400);
                break;
            case sizeof($communaute) > 0:
                $this->response($this->json($communaute), 200);
                break;
            default:
                $this->response('', 204);
                break;
        }
    }
    private function getUtilisateur() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $id_facebook = $this->_request['id_facebook'];
        $utilisateur = $this->service->_getUtilisateur($id_facebook);
        switch (true) {
            case $utilisateur == false:
                $this->response('', 400);
                break;
            case sizeof($utilisateur) > 0:
                $this->response($this->json($utilisateur), 200);
                break;
            default:
                $this->response('', 204);
                break;
        }
    }
    private function getGroupe() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $nom_groupe = $this->_request['groupe'];
        $groupe = $this->service->_getGroupe($nom_groupe);
        switch (true) {
            case $groupe == false:
                $this->response('', 400);
                break;
            case sizeof($groupe) > 0:
                $this->response($this->json($groupe), 200);
                break;
            default:
                $this->response('', 204);
                break;
        }
    }
    private function getCommunaute() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $nom_communaute = $this->_request['communaute'];
        $communaute = $this->service->_getCommunaute($nom_communaute);
        switch (true) {
            case $communaute == false:
                $this->response('', 400);
                break;
            case sizeof($communaute) > 0:
                $this->response($this->json($communaute), 200);
                break;
            default:
                $this->response('', 204);
                break;
        }
    }

    private function deleteUtilisateur() {
        // Cross validation if the request method is DELETE else it will return "Not Acceptable" status
        if ($this->get_request_method() != "DELETE") {
            $this->response('', 406);
        }
        $suppression = $this->service->_deleteUtilisateur($this->_request['id_facebook']);
        switch (true) {
            case $suppression > 0:
                $this->response('', 200);
                break;
            case $suppression == 0:
                $this->response('', 304);
                break;
            default:
                $this->response('', 500);
                break;
        }
    }
    private function deleteGroupe() {
        // Cross validation if the request method is DELETE else it will return "Not Acceptable" status
        if ($this->get_request_method() != "DELETE") {
            $this->response('', 406);
        }
        $suppression = $this->service->_deleteGroupe($this->_request['groupe']);
        switch (true) {
            case $suppression > 0:
                $this->response('', 200);
                break;
            case $suppression == 0:
                $this->response('', 304);
                break;
            default:
                $this->response('', 500);
                break;
        }
    }
    private function deleteCommunaute() {
        // Cross validation if the request method is DELETE else it will return "Not Acceptable" status
        if ($this->get_request_method() != "DELETE") {
            $this->response('', 406);
        }
        $suppression = $this->service->_deleteCommunaute($this->_request['communaute']);
        switch (true) {
            case $suppression > 0:
                $this->response('', 200);
                break;
            case $suppression == 0:
                $this->response('', 304);
                break;
            default:
                $this->response('', 500);
                break;
        }
    }
    private function deleteUtilisateurGroupe() {
        // Cross validation if the request method is DELETE else it will return "Not Acceptable" status
        if ($this->get_request_method() != "DELETE") {
            $this->response('', 406);
        }
        $suppression = $this->service->_deleteUtilisateurGroupe($this->_request['id_facebook'], $this->_request['groupe']);
        switch (true) {
            case $suppression > 0:
                $this->response('', 200);
                break;
            case $suppression == 0:
                $this->response('', 304);
                break;
            default:
                $this->response('', 500);
                break;
        }
    }
    private function deleteUtilisateurCommunaute() {
        // Cross validation if the request method is DELETE else it will return "Not Acceptable" status
        if ($this->get_request_method() != "DELETE") {
            $this->response('', 406);
        }
        $suppression = $this->service->_deleteUtilisateurCommunaute($this->_request['id_facebook'], $this->_request['communaute']);
        switch (true) {
            case $suppression > 0:
                $this->response('', 200);
                break;
            case $suppression == 0:
                $this->response('', 304);
                break;
            default:
                $this->response('', 500);
                break;
        }
    }

    private function json($data) {
        if (is_array($data)) {
            return json_encode($data);
        }
    }
}

?>