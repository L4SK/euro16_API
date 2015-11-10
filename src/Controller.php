<?php

require_once dirname(__FILE__) . "/../lib/Rest.inc.php";
require_once dirname(__FILE__) . "/Service.php";

class Controller extends REST {

    public $data = "";
    public $service;

    public function __construct($environnement) {
        parent::__construct();                // Init parent contructor
        $this->service = new Service($environnement);
    }

    /*
     * Public method for access api.
     * This method dynmically call the method based on the query string
     *
     */
    public function processApi() {
        $func = strtolower(trim(str_replace("/", "", $_REQUEST['rquest'])));
        if ((int)method_exists($this, $func) > 0)
            $this->$func();
        else
            $this->response('', 404);                // If the method not exist with in this class, response would be "Page not found".
    }

    // /utilisateur
    // json{
    // 	nom
    // 	prenom
    // 	photo
    // 	id_facebook (récupéré de l'api fb)
    // }
    // succes = 201
    // bad request = 400
    // unauthorized = 401

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

    private function getUtilisateurs() {
        if ($this->get_request_method() != "GET") {
            $this->response('', 406);
        }
        $utilisateurs = $this->service->_getUtilisateurs();
        switch (true) {
            case $utilisateurs == -1:
                $this->response('', 400);
                break;
            case $utilisateurs > 0:
                $this->response($this->json($utilisateurs), 200);
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
        $suppression = (int)$this->_request['id'];
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

    /*
     *	Encode array into JSON
    */
    private function json($data) {
        if (is_array($data)) {
            return json_encode($data);
        }
    }
}

?>