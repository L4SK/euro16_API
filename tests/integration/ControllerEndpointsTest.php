<?php

require_once('../../vendor/autoload.php');
require_once('../../config.php');

$GLOBALS['api_url'] = 'http://localhost/euro16_API/index.php?rquest=';

class ControllerEndpointsTest extends PHPUnit_Framework_TestCase {

    private $client;

    /**
     * @before
     */
    public function setup() {
        $this->client = new GuzzleHttp\Client();
    }

    /**
     * Methodes utilisables pour executer les requetes disponibles ici (Rubrique : "Requests Methods") : https://guzzle.readthedocs.org/en/guzzle4/http-messages.html
     */

    public function testEndpointCreerUtilisateurSuccess() {
        $nom = "NomTest";
        $prenom = "PrenomTest";
        $photo = "PhotoTest";
        $id_facebook = "IdFacebookTest";
        $data = array(
            "nom" => $nom,
            "prenom" => $prenom,
            "photo" => $photo,
            "id_facebook" => $id_facebook
        );
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerUtilisateur&cle=' . $GLOBALS["cle"],
            array(
                "json" => $data
            )
        );
        $this->assertEquals(201, $requete->getStatusCode());
    }

    public function testEndpointGetUtilisateursSuccess() {
        $nom = "NomTest";
        $prenom = "PrenomTest";
        $photo = "PhotoTest";
        $id_facebook = "IdFacebookTest";
        $this->client->post($GLOBALS['api_url'] . 'creerUtilisateur&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "photo" => $photo,
                    "id_facebook" => $id_facebook
                )
            )
        );
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "ID_Facebook" => $id_facebook))), (string)$requete->getBody());
    }

    public function testEndpointUpdateUtilisateursSuccess() {
        $nom = "NomTest";
        $prenom = "PrenomTest";
        $photo = "PhotoTest";
        $id_facebook = "IdFacebookTest";
        $this->client->post($GLOBALS['api_url'] . 'creerUtilisateur&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "photo" => $photo,
                    "id_facebook" => $id_facebook
                )
            )
        );
        $nom = "NomModif";
        $prenom = "PrenomModif";
        $photo = "PhotoModif";
        $id_facebook = "IdFacebookTest";
        $this->client->put($GLOBALS['api_url'] . 'updateUtilisateur&cle=' . $GLOBALS["cle"],
            [ 'form_params' => [
                'nom' => $nom,
                'prenom' => $prenom,
                'photo' => $photo,
                "id_facebook" => $id_facebook
            ]]
        );
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "ID_Facebook" => $id_facebook))), (string)$requete->getBody());
    }

    public function testEndpointDeleteUtilisateursSuccess() {
        $nom = "NomTest";
        $prenom = "PrenomTest";
        $photo = "PhotoTest";
        $id_facebook = "IdFacebookTest";
        $this->client->post($GLOBALS['api_url'] . 'creerUtilisateur&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "photo" => $photo,
                    "id_facebook" => $id_facebook
                )
            )
        );
        $this->client->delete($GLOBALS['api_url'] . 'deleteUtilisateur&cle=' . $GLOBALS["cle"] . '&id_facebook=' . $id_facebook);
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(204, $requete->getStatusCode());
        $this->assertEquals('', (string)$requete->getBody());
    }
}

?>