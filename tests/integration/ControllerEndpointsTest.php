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

    /**
     * 1er test :
     * - post utilisateur
     * - get utilisateur
     * - put utilisateur
     * - get utilisateur
     * - delete utilisateur
     * - get utilisateur
     */

    public function testEndpointGestionUtilisateur() {
        $nom = "NomTest";
        $prenom = "PrenomTest";
        $photo = "PhotoTest";
        $id_facebook = "IdFacebookTest";
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerUtilisateur&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "photo" => $photo,
                    "id_facebook" => $id_facebook
                )
            )
        );
        $this->assertEquals(201, $requete->getStatusCode());
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "ID_Facebook" => $id_facebook))), (string)$requete->getBody());
        $nom = "NomModif";
        $prenom = "PrenomModif";
        $photo = "PhotoModif";
        $requete = $this->client->put($GLOBALS['api_url'] . 'updateUtilisateur&cle=' . $GLOBALS["cle"],
            [ 'form_params' => [
                'nom' => $nom,
                'prenom' => $prenom,
                'photo' => $photo,
                "id_facebook" => $id_facebook
            ]]
        );
        $this->assertEquals(200, $requete->getStatusCode());
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "ID_Facebook" => $id_facebook))), (string)$requete->getBody());
        $this->client->delete($GLOBALS['api_url'] . 'deleteUtilisateur&cle=' . $GLOBALS["cle"] . '&id_facebook=' . $id_facebook);
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(204, $requete->getStatusCode());
        $this->assertEquals('', (string)$requete->getBody());
    }

    /**
     * 2e test :
     * - post util
     * - get util
     * - post match
     * - get match
     * - put match
     * - get match
     * - post groupe
     * - get groupe
     * - put groupe
     * - get groupe
     * - post utilisteur dans groupe
     * - get utilisateur - groupe
     * - post pronostic
     * - get pronostic
     * - put pronostic
     * - get pronostic
     * - delete pronostic
     * - get pronostic
     * - delete groupe
     * - get groupe
     * - delete match
     * - get match
     */

    public function testEndpointGestionUtilisateurGroupePronostic() {
        $nom = "NomTest";
        $prenom = "PrenomTest";
        $photo = "PhotoTest";
        $id_facebook = "IdFacebookTest";
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerUtilisateur&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "nom" => $nom,
                    "prenom" => $prenom,
                    "photo" => $photo,
                    "id_facebook" => $id_facebook
                )
            )
        );
        $this->assertEquals(201, $requete->getStatusCode());
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("NomUti" => $nom, "PrenomUti" => $prenom, "PhotoUti" => $photo, "ID_Facebook" => $id_facebook))), (string)$requete->getBody());
        $equipe1 = "France" ;
        $equipe2 = "Roumanie";
        $date_match = "03-07-2016 20:00:00";
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerMatch&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "equipe1" => $equipe1,
                    "equipe2" => $equipe2,
                    "date_match" => $date_match
                )
            )
        );
        $this->assertEquals(201, $requete->getStatusCode());
        $requete = $this->client->get($GLOBALS['api_url'] . 'getMatchs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("Equipe1" => $equipe1, "Equipe2" => $equipe2, "Score1" => "", "Score2" => "", "DateMatch" => $date_match))), (string)$requete->getBody());

        $equipe1_new = "Espagne";
        $equipe2_new = "Angleterre";
        $date_match_new = "05-07-2016 20:00:00";
        $requete = $this->client->put($GLOBALS['api_url'] . 'updateMatch&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "equipe1_old" => $equipe1,
                    "equipe1_new" => $equipe1_new,
                    "equipe2_old" => $equipe2,
                    "equipe2_new" => $equipe2_new,
                    "score1" => "",
                    "score2" => "",
                    "dateMatch_old" => $date_match,
                    "dateMatch_new" => $date_match_new
                )
            )
        );
        $this->assertEquals(200, $requete->getStatusCode());
        $requete = $this->client->get($GLOBALS['api_url'] . 'getMatchs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("Equipe1" => $equipe1_new, "Equipe2" => $equipe2_new, "Score1" => "", "Score2" => "", "DateMatch" => $date_match_new))), (string)$requete->getBody());
        $nomGroupe = "NomGroupe";
        $photoGroupe = "PhotoGroupe";
        $requete = $this->client->post($GLOBALS['api_url'] . 'creerGroupe&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "nom" => $nomGroupe,
                    "admin" => $id_facebook,
                    "photo" => $photoGroupe
                )
            )
        );
        $this->assertEquals(201, $requete->getStatusCode());
        $requete = $this->client->get($GLOBALS['api_url'] . 'getGroupes&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("nom" => $nomGroupe, "admin" => $id_facebook, "photo" => $photoGroupe))), (string)$requete->getBody());
        $nomGroupe = "NomGroupeModif";
        $photoGroupe = "PhotoGroupeModif";
        $requete = $this->client->put($GLOBALS['api_url'] . 'creerGroupe&cle=' . $GLOBALS["cle"],
            array(
                "json" => array(
                    "nom" => $nomGroupe,
                    "admin" => $id_facebook,
                    "photo" => $photoGroupe
                )
            )
        );
        $this->assertEquals(201, $requete->getStatusCode());
        $requete = $this->client->get($GLOBALS['api_url'] . 'getGroupes&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(200, $requete->getStatusCode());
        $this->assertEquals(json_encode(array(array("nom" => $nomGroupe, "admin" => $id_facebook, "photo" => $photoGroupe))), (string)$requete->getBody());


        $this->client->delete($GLOBALS['api_url'] . 'deleteUtilisateur&cle=' . $GLOBALS["cle"] . '&id_facebook=' . $id_facebook);
        $requete = $this->client->get($GLOBALS['api_url'] . 'getUtilisateurs&cle=' . $GLOBALS["cle"]);
        $this->assertEquals(204, $requete->getStatusCode());
        $this->assertEquals('', (string)$requete->getBody());
    }

    /**
     * 3e test :
     * - post util
     * - get util
     * - post match
     * - get match
     * - put match
     * - get match
     * - post dans communaute
     * - get communaute
     * - put communaute
     * - get communaute
     * - post pronostic
     * - get pronostic
     * - put pronostic
     * - get pronostic
     * - delete pronostic
     * - get pronostic
     * - delete communaute
     * - get communaute
     * - delete match
     * - get match
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