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
        switch(true){
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